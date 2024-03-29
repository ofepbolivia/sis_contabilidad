CREATE OR REPLACE FUNCTION conta.f_gestionar_presupuesto_cbte_pxp (
  p_id_usuario integer,
  p_id_int_comprobante integer,
  p_igualar varchar = 'no'::character varying,
  p_fecha_ejecucion date = NULL::date,
  p_conexion varchar = NULL::character varying
)
RETURNS varchar AS
$body$
/*
	Autor: RAC (KPLIAN)
    Fecha: 06-04-2016
    Descripción:
     Nueva funcion para gestion de presupuesto simplicada, toma ventaja de que el presupuesto se ejecuta
     directamente en pxp y no depende del deblink
*/
DECLARE

  v_registros_comprobante 			record;
  v_nombre_funcion					varchar;
  v_resp							varchar;
  v_retorno 						varchar;
  v_id_moneda_base					integer;
  v_id_moneda_tri					integer;
  v_id_moneda						integer;
  v_sw_moneda_base					varchar;
  v_momento_presupeustario			varchar;
  v_momento_aux						varchar;
  v_error_presupuesto				numeric;
  v_registros						record;
  v_importe_debe 					numeric;
  v_importe_haber 					numeric;
  v_monto_cmp 						numeric;
  v_respuesta_verificar				record;
  v_resp_ges						numeric[];
  v_importe_debe_mb 				numeric;
  v_importe_haber_mb 				numeric;
  v_monto_cmp_mb					numeric;
  v_sw_error						boolean;
  v_mensaje_error					varchar;
  v_tmp								varchar;
  v_codico_cc						varchar;
  v_registros_dev					record;
  v_monto_x_pagar					numeric;
  v_monto_x_pagar_mb				numeric;
  v_ano_1  							integer;
  v_ano_2  							integer;
  v_monto_rev						numeric;
  v_monto_rev_mb					numeric;

  v_importe_gasto 					numeric;
  v_importe_recurso 					numeric;
  v_importe_gasto_mb 				numeric;
  v_importe_recurso_mb 				numeric;
  v_reg_par_eje						record;
  --03-12-2021 (may)
  v_cuenta_doc						record;
  v_registros_cuenta_doc			record;
  v_importe_depositos				numeric;
  v_monto_cmp_dep					numeric;
  v_importe_cuenta_doc				numeric;
  v_importe_porcentaje				numeric;
  v_id_partida_eje_transaccion		integer;
  v_id_partida_ejecucion			integer;
  v_importe_fa						numeric;
  v_importe_total_rendiciones		numeric;
  v_importe_solicitado				numeric;
  v_importe_rendicion				numeric;
  v_importe_revertir_comprometido	numeric;
  v_resp_ges_com					numeric[];
  v_detalle_cuenta_doc				record;
  v_suma_partida_eje				numeric;
  v_suma_partida_eje_fk 			numeric;

  --07-02-2022 (may)
  v_monto_comprometido_eje			numeric;
  v_monto_comprometido_pag			numeric;

BEGIN


    v_nombre_funcion:='conta.f_gestionar_presupuesto_cbte_pxp';
    v_retorno = 'exito';
    v_sw_error = false; --iniciamos sin errores
    v_mensaje_error = '';


    -- recupera datos del comprobante
    select
      ic.momento,
      ic.id_clase_comprobante,
      cl.codigo as codigo_clase_cbte,
      ic.momento,
      ic.momento_comprometido,
      ic.momento_ejecutado,
      ic.momento_pagado,
      ic.estado_reg,
      ic.id_moneda,
      ic.fecha,
      ic.vbregional,
      ic.temporal,
      ic.nro_tramite,
      ic.cbte_reversion,
      per.id_gestion,
      ic.id_int_comprobante_fks
    into v_registros_comprobante
    from conta.tint_comprobante ic
    inner join conta.tclase_comprobante cl  on ic.id_clase_comprobante =  cl.id_clase_comprobante
    inner join param.tperiodo per on per.id_periodo = ic.id_periodo
    where ic.id_int_comprobante  =  p_id_int_comprobante;


     ---------------------------------------------------
     -- Determinar moneda de ejecucion presupuestaria
     -- Si viene de una regional y la moneda no  es la moenda de triangulación
     -- ejecutar moneda base
     ------------------------------------------------

     -- determinar moneda base
     v_id_moneda_base = param.f_get_moneda_base();
     v_id_moneda_tri = param.f_get_moneda_triangulacion();
     v_id_moneda = v_registros_comprobante.id_moneda;
     v_sw_moneda_base = 'no';

     IF v_registros_comprobante.vbregional = 'si' and v_registros_comprobante.id_moneda != v_id_moneda_tri THEN
       v_id_moneda = v_id_moneda_base;
       v_sw_moneda_base = 'si';
     END IF;

     -- determina la fecha de ejecucion presupuestaria
     IF p_fecha_ejecucion is NULL THEN
       p_fecha_ejecucion = v_registros_comprobante.fecha::date;  --, fecha del comprobante
     END IF;


     IF v_registros_comprobante.momento = 'presupuestario' THEN

             --recuepra el error maximo por  redondeo
             v_error_presupuesto =  pxp.f_get_variable_global('error_presupuesto')::numeric;

             IF v_error_presupuesto is NULL THEN
                raise exception 'No se encontro el valor de la variable global : error_presupuesto';
             END IF;

             -- definir lso momentos presupuestarios
             IF v_registros_comprobante.momento_ejecutado = 'si'  and    v_registros_comprobante.momento_pagado = 'si' then
                    v_momento_presupeustario = 'pagado'; --pagado
                    v_momento_aux='todo';
             ELSIF  v_registros_comprobante.momento_ejecutado = 'si'  and    v_registros_comprobante.momento_pagado = 'no'  THEN
                    v_momento_presupeustario = 'ejecutado';  --ejecutado
                    v_momento_aux='solo ejecutar';
             ELSIF v_registros_comprobante.momento_ejecutado = 'no'  and    v_registros_comprobante.momento_pagado = 'si'  THEN
                    v_momento_presupeustario = 'pagado';  --pagado
                    v_momento_aux='solo pagar';
             ELSIF v_registros_comprobante.momento_comprometido = 'si'  and  v_registros_comprobante.momento_ejecutado = 'no'  and    v_registros_comprobante.momento_pagado = 'no' then
                    raise exception 'Solo comprometer no esta implmentado';
             ELSE
                    raise exception 'Combinacion de momentos no contemplada';
             END IF;


            --listado de las transacciones con partidas presupuestaria
            FOR v_registros in (
                                  select
                                     it.id_int_transaccion,
                                     it.id_partida,
                                     it.id_partida_ejecucion,
                                     it.id_partida_ejecucion_dev,
                                     it.importe_debe,
                                     it.importe_haber,
                                     it.importe_gasto,
                                     it.importe_recurso,
                                     it.importe_debe_mb,
                                     it.importe_haber_mb,
                                     it.importe_gasto_mb,
                                     it.importe_recurso_mb,
                                     it.id_centro_costo,
                                     par.sw_movimiento,  --  presupuestaria o  flujo
                                     par.sw_transaccional,  --titular o movimiento
                                     par.tipo,                -- recurso o gasto
                                     pr.id_presupuesto,
                                     it.importe_reversion,
                                     it.factor_reversion,
                                     par.codigo as codigo_partida,
                                     it.actualizacion
                                  from conta.tint_transaccion it
                                  inner join pre.tpartida par on par.id_partida = it.id_partida
                                  inner join pre.tpresupuesto pr on pr.id_centro_costo =
                                  it.id_centro_costo
                                  where it.id_int_comprobante = p_id_int_comprobante
                                        and it.estado_reg = 'activo' )  LOOP


                           --selecciona la moneda de trabajo
                         IF v_sw_moneda_base = 'si' THEN
                              v_importe_gasto = v_registros.importe_gasto_mb;
                              v_importe_recurso =  v_registros.importe_recurso_mb;
                         ELSE
                              v_importe_gasto = v_registros.importe_gasto;
                              v_importe_recurso =  v_registros.importe_recurso;
                         END IF;

                         v_importe_gasto_mb = v_registros.importe_gasto_mb;
                         v_importe_recurso_mb =  v_registros.importe_recurso_mb;




                          IF    v_momento_aux = 'todo' or   v_momento_aux='solo ejecutar'  THEN

                                -- si solo ejecutamos el presupuesto
                                --  o (compromentemos y ejecutamos)
                                --  o (compromentemos, ejecutamos y pagamos)

                                -- si tiene partida ejecucion de comprometido y nose corresponde con la gestion
                                --  lo ponemos en null para que comprometa

                                --  es para devegar planes de pago que quedaron pendientes de una  gestion anterior

                                IF v_registros.id_partida_ejecucion is not NULL THEN



                                   select
                                       par.id_gestion
                                   into
                                      v_reg_par_eje
                                   from pre.tpartida_ejecucion pe
                                   inner join pre.tpartida par on par.id_partida = pe.id_partida
                                   where pe.id_partida_ejecucion = v_registros.id_partida_ejecucion;

                                    if v_reg_par_eje.id_gestion != v_registros_comprobante.id_gestion  then
                                        v_registros.id_partida_ejecucion = NULL;
                                        update conta.tint_transaccion set
                                           id_partida_ejecucion = NULL
                                        where id_int_transaccion = v_registros.id_int_transaccion;
                                    end if;

                                END IF;


                                -- si  el comprobante tiene que comprometer
                                IF v_registros_comprobante.momento_comprometido = 'si' and v_registros_comprobante.cbte_reversion = 'no'  then
                                      -- validamos que si tiene que comprometer la id_partida_ejecucion tiene que ser nulo
                                       IF v_registros.id_partida_ejecucion is not NULL THEN
                                           raise exception 'El comprobante no puede estar marcado para comprometer, si ya existe un comprometido';
                                       END IF;

                                END IF; --IF comprometido





                                -- solo procesamos si es una partida presupuestaria y no de flujo
                                IF v_registros.sw_movimiento = 'presupuestaria' THEN

                                         v_monto_cmp = 0;

                                         ---  revisar si esto esta bien
                                         IF v_registros_comprobante.momento_comprometido = 'no' THEN
                                                -- solo permite comprometer partidas de actulizacion (transaccion que igualan el comprobante)
                                               IF v_registros.id_partida_ejecucion is null  and v_registros.actualizacion = 'no'  THEN
                                                   raise exception 'El comprobante  no esta marcado para comprometer, y no tiene un origen comprometido';
                                                END IF;
                                         END IF;



                                         IF v_registros.tipo = 'gasto'  THEN
                                             -- importe debe ejecucion
                                             IF v_importe_gasto > 0  or v_importe_gasto_mb > 0 THEN
                                                 v_monto_cmp  = v_importe_gasto;
                                                 v_monto_cmp_mb = v_importe_gasto_mb;
                                             END IF;
                                             --importe haber es reversion, multiplicar por -1
                                             IF v_importe_recurso > 0 or v_importe_recurso_mb > 0 THEN
                                                 v_monto_cmp  = v_importe_recurso * (-1);
                                                 v_monto_cmp_mb = v_importe_recurso_mb * (-1);
                                             END IF;

                                         ELSE
                                             IF v_importe_recurso > 0 or v_importe_recurso_mb > 0 THEN
                                               v_monto_cmp  = v_importe_recurso;
                                               v_monto_cmp_mb = v_importe_recurso_mb;
                                             END IF;

                                             --importe debe es reversion, multiplicar por -1

                                             IF v_importe_gasto > 0 or v_importe_gasto_mb > 0 THEN
                                                 v_monto_cmp  = v_importe_gasto * (-1);
                                                 v_monto_cmp_mb = v_importe_gasto_mb * (-1);
                                             END IF;
                                         END IF;

                                       -- raise exception 'entra.. % --  %',v_monto_cmp, v_monto_cmp_mb;



                                        -- llamamos a la funcion de ejecucion
                                        v_resp_ges = pre.f_gestionar_presupuesto_v2(
                                                                                    p_id_usuario,
                                                                                    NULL,  --tipo de cambio,  ya mandamos la moneda convertida
                                                                                    v_registros.id_presupuesto,
                                                                                    v_registros.id_partida,
                                                                                    v_id_moneda,
                                                                                    v_monto_cmp,
                                                                                    v_monto_cmp_mb,
                                                                                    p_fecha_ejecucion,
                                                                                    v_momento_presupeustario,
                                                                                    v_registros.id_partida_ejecucion,
                                                                                    'id_int_transaccion',
                                                                                    v_registros.id_int_transaccion,--p_fk_llave,
                                                                                    v_registros_comprobante.nro_tramite,
                                                                                    p_id_int_comprobante,
                                                                                    v_registros_comprobante.momento_comprometido,
                                                                                    v_registros_comprobante.momento_ejecutado,
                                                                                    v_registros_comprobante.momento_pagado);

                                         ------------------------------------
                                         --  ACUMULAR ERRORES
                                         -----------------------------------

                                         --  analizamos respuesta y retornamos error
                                         IF v_resp_ges[1] = 0 THEN

                                                 --  recuperamos datos del presupuesto
                                                 v_mensaje_error = v_mensaje_error|| conta.f_armar_error_presupuesto(v_resp_ges,
                                                                                               v_registros.id_presupuesto,
                                                                                               v_registros.codigo_partida,
                                                                                               v_id_moneda,
                                                                                               v_id_moneda_base,
                                                                                               v_momento_presupeustario,
                                                                                               v_monto_cmp_mb);
                                                 v_sw_error = true;

                                          ELSE
                                                   -- sino se tiene error almacenamos el id de la aprtida ejecucion
                                                   IF v_registros.id_partida_ejecucion is  NULL THEN
                                                        update conta.tint_transaccion it set
                                                           id_partida_ejecucion = v_resp_ges[2],
                                                           id_partida_ejecucion_dev = v_resp_ges[2],
                                                           fecha_mod = now(),
                                                           id_usuario_mod = p_id_usuario
                                                        where it.id_int_transaccion  =  v_registros.id_int_transaccion;
                                                   ELSE
                                                       update conta.tint_transaccion it set
                                                           id_partida_ejecucion_dev = v_resp_ges[2],
                                                           fecha_mod = now(),
                                                           id_usuario_mod = p_id_usuario
                                                        where it.id_int_transaccion  =  v_registros.id_int_transaccion;
                                                   END IF;

                                          END IF; --fin id de error


                                         -------------------------------------------------------------------------------------
                                         --   si existe un factor a revertir y tenememos el id_partida_ejecucion, revertimos
                                         -------------------------------------------------------------------------------------
                                         /*------------
                                         --04-03-2021 (may) modificacion QUITANDO EL CALCULO DE FACTOR REVERSION a solicitud de Grover segun reunion
                                         desde la fecha
                                         --------------*/

                                       /*
                                         IF  v_registros.factor_reversion > 0 and v_registros.id_partida_ejecucion is not null THEN

                                                   /*  regla de 3 para calcular  el monto a revertir en moneda base
                                                    *      200 -> 0.87
                                                    *      X   -> 0.13
                                                    */

                                                  IF v_sw_moneda_base = 'si' THEN
                                                      --si forzamos el calculo en moenda base
                                                      -- calcular el monto a revertir segun factor por regla de tres en moneda base

                                                      v_monto_cmp_mb = (v_monto_cmp * v_registros.factor_reversion)/(1 - v_registros.factor_reversion);
                                                      v_monto_cmp = v_monto_cmp_mb;
                                                  ELSEIF  v_registros_comprobante.id_moneda = v_id_moneda_base THEN
                                                       -- si la transaccion ya fue calculada en moneda
                                                       v_monto_cmp = v_registros.importe_reversion;
                                                       v_monto_cmp_mb  = v_registros.importe_reversion;
                                                  ELSE
                                                        --calculo en loa moenda original de la transaccion
                                                        v_monto_cmp = v_registros.importe_reversion;

                                                        v_monto_cmp_mb = (v_monto_cmp_mb * v_registros.factor_reversion)/(1 - v_registros.factor_reversion);
                                                  END IF;

                                                   -- llamar a la funcion para revertir el comprometido

                                                   v_resp_ges = pre.f_gestionar_presupuesto_v2(
                                                                                            p_id_usuario,
                                                                                            NULL,  --tipo de cambio,  ya mandamos la moneda convertida
                                                                                            v_registros.id_presupuesto,
                                                                                            v_registros.id_partida,
                                                                                            v_id_moneda,
                                                                                            v_monto_cmp*(-1),
                                                                                            v_monto_cmp_mb*(-1),
                                                                                            p_fecha_ejecucion,
                                                                                            'comprometido',
                                                                                            v_registros.id_partida_ejecucion,
                                                                                            'id_int_transaccion',
                                                                                            v_registros.id_int_transaccion,--p_fk_llave,
                                                                                            v_registros_comprobante.nro_tramite,
                                                                                            p_id_int_comprobante,
                                                                                            v_registros_comprobante.momento_comprometido,
                                                                                            v_registros_comprobante.momento_ejecutado,
                                                                                            v_registros_comprobante.momento_pagado);


                                                     --  analizamos respuesta y retornamos error
                                                   IF v_resp_ges[1] = 0 THEN

                                                         --  recuperamos datos del presupuesto
                                                         v_mensaje_error = v_mensaje_error || conta.f_armar_error_presupuesto(v_resp_ges,
                                                                                               v_registros.id_presupuesto,
                                                                                               v_registros.codigo_partida,
                                                                                               v_id_moneda,
                                                                                               v_id_moneda_base,
                                                                                               v_momento_presupeustario,
                                                                                               v_monto_cmp_mb);
                                                         v_sw_error = true;

                                                  ELSE

                                                      update conta.tint_transaccion it set
                                                         id_partida_ejecucion_rev = v_resp_ges[2],   --partida de reversion
                                                         fecha_mod = now(),
                                                         id_usuario_mod = p_id_usuario
                                                      where it.id_int_transaccion  =   v_registros.id_int_transaccion;


                                                  END IF; --fin id de error

                                       END IF; --if la transacion tiene reversion

                                */


                                /*------------
                                 --06-12-2021 (may) se realiza para FA con detalle para realizar la reversion del 13% comprometido
                                 --------------*/

                                          select cdoc.detalle_cuenta_doc
                                          into v_cuenta_doc
                                          from cd.tcuenta_doc cdoc
                                          where cdoc.nro_tramite= v_registros_comprobante.nro_tramite
                                          and cdoc.id_tipo_cuenta_doc = 1
                                          and cdoc.detalle_cuenta_doc = 'si';

                                         raise notice 'llega v_cuenta_doc %', v_cuenta_doc;

                                         --el importe de los depositos no se realiza el descuento del 13%, por eso separamos el importe de deposito.

                                         IF  (v_registros.id_partida_ejecucion is not null and v_cuenta_doc.detalle_cuenta_doc = 'si') THEN

                                             --si tiene un factor comun se realiza la reversion del comprometido del 13%
                                             IF (v_registros.factor_reversion > 0) THEN

                                                   IF v_sw_moneda_base = 'si' THEN
                                                        --si forzamos el calculo en moenda base
                                                        -- calcular el monto a revertir segun factor por regla de tres en moneda base

                                                        v_monto_cmp_mb = (v_monto_cmp * v_registros.factor_reversion)/(1 - v_registros.factor_reversion);
                                                        v_monto_cmp = v_monto_cmp_mb;
                                                   ELSEIF  v_registros_comprobante.id_moneda = v_id_moneda_base THEN
                                                         -- si la transaccion ya fue calculada en moneda
                                                         v_monto_cmp = v_registros.importe_reversion;
                                                         v_monto_cmp_mb  = v_registros.importe_reversion;
                                                   ELSE
                                                          --calculo en loa moenda original de la transaccion
                                                          v_monto_cmp = v_registros.importe_reversion;
                                                          v_monto_cmp_mb = (v_monto_cmp_mb * v_registros.factor_reversion)/(1 - v_registros.factor_reversion);
                                                   END IF;

                                                   -- llamar a la funcion para revertir el comprometido
                                                   --raise notice 'llegaFAC-PAR % - % - % - %',v_registros.factor_reversion,  v_registros.id_partida_ejecucion,v_monto_cmp*(-1), v_monto_cmp_mb*(-1) ;
                                                         v_resp_ges = pre.f_gestionar_presupuesto_v2(
                                                                                                  p_id_usuario,
                                                                                                  NULL,  --tipo de cambio,  ya mandamos la moneda convertida
                                                                                                  v_registros.id_presupuesto,
                                                                                                  v_registros.id_partida,
                                                                                                  v_id_moneda,
                                                                                                  v_monto_cmp*(-1),
                                                                                                  v_monto_cmp_mb*(-1),
                                                                                                  p_fecha_ejecucion,
                                                                                                  'comprometido',
                                                                                                  v_registros.id_partida_ejecucion,
                                                                                                  'id_int_transaccion',
                                                                                                  v_registros.id_int_transaccion,--p_fk_llave,
                                                                                                  v_registros_comprobante.nro_tramite,
                                                                                                  p_id_int_comprobante,
                                                                                                  v_registros_comprobante.momento_comprometido,
                                                                                                  v_registros_comprobante.momento_ejecutado,
                                                                                                  v_registros_comprobante.momento_pagado);

                                                          --  analizamos respuesta y retornamos error
                                                          IF v_resp_ges[1] = 0 THEN
raise notice 'llega0 % ',v_registros.id_int_transaccion;
                                                               --  recuperamos datos del presupuesto
                                                               v_mensaje_error = v_mensaje_error || conta.f_armar_error_presupuesto(v_resp_ges,
                                                                                                       v_registros.id_presupuesto,
                                                                                                       v_registros.codigo_partida,
                                                                                                       v_id_moneda,
                                                                                                       v_id_moneda_base,
                                                                                                       v_momento_presupeustario,
                                                                                                       v_monto_cmp_mb);
                                                                 v_sw_error = true;

                                                          ELSE

                                                              update conta.tint_transaccion it set
                                                                 id_partida_ejecucion_rev = v_resp_ges[2],   --partida de reversion
                                                                 fecha_mod = now(),
                                                                 id_usuario_mod = p_id_usuario
                                                              where it.id_int_transaccion  =   v_registros.id_int_transaccion;


                                                          END IF; --fin id de error


                                                    	  /*--------------------------------
                                                          PARA el AJUSTE FINAL COMPROMETIDO
                                                          ----------------------------------*/

                                                          select cdoc.id_cuenta_doc, cdoc.id_cuenta_doc_fk
                                                          into v_registros_cuenta_doc
                                                          from cd.tcuenta_doc cdoc
                                                          where cdoc.id_int_comprobante = p_id_int_comprobante;

                                                          --importe inicial comprometido
                                                          select cdoc.importe
                                                          into v_importe_fa
                                                          from cd.tcuenta_doc cdoc
                                                          where cdoc.id_cuenta_doc = v_registros_cuenta_doc.id_cuenta_doc_fk;

                                                          --sumar importe de las rendiciones del FA
                                                          select sum(cdoc.importe)
                                                          into v_importe_total_rendiciones
                                                          from cd.tcuenta_doc cdoc
                                                          where cdoc.id_cuenta_doc_fk = v_registros_cuenta_doc.id_cuenta_doc_fk;
--raise exception 'llegaConfactor % - %',v_importe_fa,v_importe_total_rendiciones;
                                                          IF (v_importe_fa = v_importe_total_rendiciones) THEN


                                                                  --bucando importe del comprometido del detalle del FA
                                                                  select sum(cdet.importe)
                                                                  into v_importe_solicitado
                                                                  from cd.tcuenta_doc cdoc
                                                                  inner join cd.tcuenta_doc_det cdet on cdet.id_cuenta_doc = cdoc.id_cuenta_doc
                                                                  where cdoc.id_cuenta_doc = v_registros_cuenta_doc.id_cuenta_doc_fk
                                                                  and cdet.id_partida =v_registros.id_partida
                                                                  and cdet.id_cc= v_registros.id_presupuesto;

                                                                  --
                                                                  SELECT sum(pej.monto)
                                                                  INTO v_monto_comprometido_eje
                                                                  FROM pre.tpartida_ejecucion pej
                                                                  WHERE pej.id_partida = v_registros.id_partida
                                                                  and pej.id_presupuesto = v_registros.id_presupuesto
                                                                  and pej.nro_tramite =  v_registros_comprobante.nro_tramite
                                                                  and pej.tipo_movimiento = 'comprometido';

                                                                  SELECT sum(pej.monto)
                                                                  INTO v_monto_comprometido_pag
                                                                  FROM pre.tpartida_ejecucion pej
                                                                  WHERE pej.id_partida = v_registros.id_partida
                                                                  and pej.id_presupuesto = v_registros.id_presupuesto
                                                                  and pej.nro_tramite =  v_registros_comprobante.nro_tramite
                                                                  and pej.tipo_movimiento = 'pagado';

                                                                  --sumar importe de las rendiciones del FA
                                                                  select sum(cdoc.importe)
                                                                  into v_importe_total_rendiciones
                                                                  from cd.tcuenta_doc cdoc
                                                                  where cdoc.id_cuenta_doc_fk = v_registros_cuenta_doc.id_cuenta_doc_fk;
                                                                  ---

                                                                  IF (COALESCE(v_monto_comprometido_eje,0) > 0 and COALESCE(v_monto_comprometido_pag,0) = 0 ) THEN
                                                                  		v_importe_revertir_comprometido = coalesce(v_monto_comprometido_eje, 0) - coalesce(v_importe_total_rendiciones, 0) ;

                                                                  --

                                                                  --v_importe_rendicion = v_registros.importe_debe + v_registros.importe_reversion ;
                                                                  --v_importe_revertir_comprometido = coalesce(v_importe_solicitado, 0) - coalesce(v_importe_rendicion, 0) ;

                                                                  --realizando la reversion del comprometido del importe sobrante del FA

                                                                  v_resp_ges_com = pre.f_gestionar_presupuesto_v2(
                                                                                                p_id_usuario,
                                                                                                NULL,  --tipo de cambio,  ya mandamos la moneda convertida
                                                                                                v_registros.id_presupuesto,
                                                                                                v_registros.id_partida,
                                                                                                v_id_moneda,
                                                                                                v_importe_revertir_comprometido*(-1), --v_monto_cmp*(-1),
                                                                                                v_importe_revertir_comprometido*(-1), --v_monto_cmp_mb*(-1),
                                                                                                p_fecha_ejecucion,
                                                                                                'comprometido',
                                                                                                v_registros.id_partida_ejecucion,
                                                                                                'id_int_transaccion',
                                                                                                v_registros.id_int_transaccion,--p_fk_llave,
                                                                                                v_registros_comprobante.nro_tramite,
                                                                                                p_id_int_comprobante,
                                                                                                v_registros_comprobante.momento_comprometido,
                                                                                                v_registros_comprobante.momento_ejecutado,
                                                                                                v_registros_comprobante.momento_pagado);

                                                                   --  analizamos respuesta y retornamos error
                                                                    IF v_resp_ges_com[1] = 0 THEN
raise notice 'llega2 %',v_registros.id_int_transaccion;
																		 --  recuperamos datos del presupuesto
                                                                         v_mensaje_error = v_mensaje_error || conta.f_armar_error_presupuesto(v_resp_ges_com,
                                                                                                                 v_registros.id_presupuesto,
                                                                                                                 v_registros.codigo_partida,
                                                                                                                 v_id_moneda,
                                                                                                                 v_id_moneda_base,
                                                                                                                 v_momento_presupeustario,
                                                                                                                 v_monto_cmp_mb);
                                                                           v_sw_error = true;

                                                                    ELSE

                                                                        update pre.tpartida_ejecucion pj set
                                                                           id_partida_ejecucion_fk = v_registros.id_partida_ejecucion --v_resp_ges[2]
                                                                        where pj.id_partida_ejecucion = v_resp_ges_com[2];


                                                                    END IF; --fin id de error

                                                                 END IF;

                                                           END IF;

                                             ELSE --si v_registros.factor_reversion < 0 si no tiene un factor de reversion porque son de recibos el cual se debe realizar su reversion

                                                          /*--------------------------------
                                                          PARA el AJUSTE FINAL COMPROMETIDO
                                                          ----------------------------------*/

                                                          select cdoc.id_cuenta_doc, cdoc.id_cuenta_doc_fk
                                                          into v_registros_cuenta_doc
                                                          from cd.tcuenta_doc cdoc
                                                          where cdoc.id_int_comprobante = p_id_int_comprobante;

                                                          --importe inicial comprometido
                                                          select cdoc.importe
                                                          into v_importe_fa
                                                          from cd.tcuenta_doc cdoc
                                                          where cdoc.id_cuenta_doc = v_registros_cuenta_doc.id_cuenta_doc_fk;

                                                          --sumar importe de las rendiciones del FA
                                                          select sum(cdoc.importe)
                                                          into v_importe_total_rendiciones
                                                          from cd.tcuenta_doc cdoc
                                                          where cdoc.id_cuenta_doc_fk = v_registros_cuenta_doc.id_cuenta_doc_fk;
															--raise exception 'llegasinfactor % - %',v_importe_fa,v_importe_total_rendiciones;
                                                          IF (v_importe_fa = v_importe_total_rendiciones) THEN

                                                          			--para las rendiciones que no realizan su ejecutado y no esta en las transacciones
                                                                    FOR v_detalle_cuenta_doc IN ( select cdet.id_partida_ejecucion, cdet.importe,cdet.id_partida, cdet.id_cc, cdoc.id_moneda, cdoc.nro_tramite
                                                                                                   from cd.tcuenta_doc cdoc
                                                                                                   inner join cd.tcuenta_doc_det cdet on cdet.id_cuenta_doc = cdoc.id_cuenta_doc
                                                                                                   where cdoc.id_cuenta_doc = v_registros_cuenta_doc.id_cuenta_doc_fk
                                                                                                  )LOOP

                                                                                IF NOT EXISTS ( select 1
                                                                                			from conta.tint_transaccion tra
                                                                                            where tra.id_int_comprobante = p_id_int_comprobante
                                                                                            and tra.id_partida_ejecucion = v_detalle_cuenta_doc.id_partida_ejecucion
                                                                                			) THEN

                                                                                            --importe del comprometido FK
                                                                                            SELECT sum(pej.monto)
                                                                                            INTO v_suma_partida_eje_fk
                                                                                            FROM  pre.tpartida_ejecucion pej
                                                                                            WHERE pej.id_partida_ejecucion_fk = v_detalle_cuenta_doc.id_partida_ejecucion
                                                                                            and pej.nro_tramite = v_detalle_cuenta_doc.nro_tramite
                                                                                            and pej.tipo_movimiento in ('comprometido');

                                                                                            --importe del comprometido solicitado
                                                                                            SELECT sum(pej.monto)
                                                                                            INTO v_suma_partida_eje
                                                                                            FROM  pre.tpartida_ejecucion pej
                                                                                            WHERE pej.id_partida_ejecucion = v_detalle_cuenta_doc.id_partida_ejecucion
                                                                                            and pej.nro_tramite = v_detalle_cuenta_doc.nro_tramite
                                                                                            and pej.tipo_movimiento in ('comprometido');


                                                                          				  IF (coalesce(v_suma_partida_eje, 0) != coalesce(v_suma_partida_eje_fk, 0)) THEN

                                                                                                v_resp_ges_com = pre.f_gestionar_presupuesto_v2(
                                                                                                                              p_id_usuario,
                                                                                                                              NULL,  --tipo de cambio,  ya mandamos la moneda convertida
                                                                                                                              v_detalle_cuenta_doc.id_cc,
                                                                                                                              v_detalle_cuenta_doc.id_partida,
                                                                                                                              v_detalle_cuenta_doc.id_moneda,
                                                                                                                              v_detalle_cuenta_doc.importe*(-1), --v_monto_cmp*(-1),
                                                                                                                              v_detalle_cuenta_doc.importe*(-1), --v_monto_cmp_mb*(-1),
                                                                                                                              p_fecha_ejecucion,
                                                                                                                              'comprometido',
                                                                                                                              v_detalle_cuenta_doc.id_partida_ejecucion,
                                                                                                                              'id_cuenta_doc',
                                                                                                                              v_registros_cuenta_doc.id_cuenta_doc_fk,--p_fk_llave,
                                                                                                                              v_detalle_cuenta_doc.nro_tramite,
                                                                                                                              p_id_int_comprobante,
                                                                                                                              'no',
                                                                                                                              'no',
                                                                                                                              'no');

                                                                                                 update pre.tpartida_ejecucion pj set
                                                                                                   id_partida_ejecucion_fk = v_detalle_cuenta_doc.id_partida_ejecucion --v_resp_ges[2]
                                                                                                 where pj.id_partida_ejecucion = v_resp_ges_com[2];

                                                                                           END IF;

                                                                                END IF;



                                                                    END LOOP;
                                                                    ---

                                                                  --buscando importe del comprometido del detalle del FA
                                                                  select sum(cdet.importe)
                                                                  into v_importe_solicitado
                                                                  from cd.tcuenta_doc cdoc
                                                                  inner join cd.tcuenta_doc_det cdet on cdet.id_cuenta_doc = cdoc.id_cuenta_doc
                                                                  where cdoc.id_cuenta_doc = v_registros_cuenta_doc.id_cuenta_doc_fk
                                                                  and cdet.id_partida =v_registros.id_partida
                                                                  and cdet.id_cc= v_registros.id_presupuesto;

                                                                  --
                                                                  SELECT sum(pej.monto)
                                                                  INTO v_monto_comprometido_eje
                                                                  FROM pre.tpartida_ejecucion pej
                                                                  WHERE pej.id_partida = v_registros.id_partida
                                                                  and pej.id_presupuesto = v_registros.id_presupuesto
                                                                  and pej.nro_tramite =  v_registros_comprobante.nro_tramite
                                                                  and pej.tipo_movimiento = 'comprometido';

                                                                  SELECT sum(pej.monto)
                                                                  INTO v_monto_comprometido_pag
                                                                  FROM pre.tpartida_ejecucion pej
                                                                  WHERE pej.id_partida = v_registros.id_partida
                                                                  and pej.id_presupuesto = v_registros.id_presupuesto
                                                                  and pej.nro_tramite =  v_registros_comprobante.nro_tramite
                                                                  and pej.tipo_movimiento = 'pagado';

                                                                  --sumar importe de las rendiciones del FA
                                                                  select sum(cdoc.importe)
                                                                  into v_importe_total_rendiciones
                                                                  from cd.tcuenta_doc cdoc
                                                                  where cdoc.id_cuenta_doc_fk = v_registros_cuenta_doc.id_cuenta_doc_fk;
                                                                  ---

                                                                  IF (COALESCE(v_monto_comprometido_eje,0) > 0 and COALESCE(v_monto_comprometido_pag,0) = 0 ) THEN
                                                                  		v_importe_revertir_comprometido = coalesce(v_monto_comprometido_eje, 0) - coalesce(v_importe_total_rendiciones, 0) ;
                                                                  --


                                                                  --v_importe_rendicion = v_registros.importe_debe + v_registros.importe_reversion ;
                                                                  --v_importe_revertir_comprometido = coalesce(v_importe_solicitado, 0) - coalesce(v_importe_rendicion, 0) ;

                                                                  /*--para las rendiciones que no realizan su ejecutado y no esta en las transacciones
                                                                  IF not EXISTS ( select 1
                                                                                from cd.tcuenta_doc cdoc
                                                                                inner join cd.tcuenta_doc_det cdet on cdet.id_cuenta_doc = cdoc.id_cuenta_doc
                                                                                where cdoc.id_cuenta_doc = v_registros_cuenta_doc.id_cuenta_doc_fk
                                                                                and cdet.id_partida =v_registros.id_partida
                                                                                and cdet.id_cc= v_registros.id_presupuesto
                                                                  				) THEN

                                                                  END IF;*/

                                                                  --

                                                                  --realizando la reversion del comprometido del importe sobrante del FA

                                                                  v_resp_ges_com = pre.f_gestionar_presupuesto_v2(
                                                                                                p_id_usuario,
                                                                                                NULL,  --tipo de cambio,  ya mandamos la moneda convertida
                                                                                                v_registros.id_presupuesto,
                                                                                                v_registros.id_partida,
                                                                                                v_id_moneda,
                                                                                                v_importe_revertir_comprometido*(-1), --v_monto_cmp*(-1),
                                                                                                v_importe_revertir_comprometido*(-1), --v_monto_cmp_mb*(-1),
                                                                                                p_fecha_ejecucion,
                                                                                                'comprometido',
                                                                                                v_registros.id_partida_ejecucion,
                                                                                                'id_int_transaccion',
                                                                                                v_registros.id_int_transaccion,--p_fk_llave,
                                                                                                v_registros_comprobante.nro_tramite,
                                                                                                p_id_int_comprobante,
                                                                                                v_registros_comprobante.momento_comprometido,
                                                                                                v_registros_comprobante.momento_ejecutado,
                                                                                                v_registros_comprobante.momento_pagado);

                                                                   --  analizamos respuesta y retornamos error
                                                                    IF v_resp_ges_com[1] = 0 THEN
raise notice 'llega4 %',v_registros.id_int_transaccion;
                                                                         --  recuperamos datos del presupuesto
                                                                         v_mensaje_error = v_mensaje_error || conta.f_armar_error_presupuesto(v_resp_ges_com,
                                                                                                                 v_registros.id_presupuesto,
                                                                                                                 v_registros.codigo_partida,
                                                                                                                 v_id_moneda,
                                                                                                                 v_id_moneda_base,
                                                                                                                 v_momento_presupeustario,
                                                                                                                 v_monto_cmp_mb);
                                                                           v_sw_error = true;

                                                                    ELSE

                                                                        update pre.tpartida_ejecucion pj set
                                                                           id_partida_ejecucion_fk = v_registros.id_partida_ejecucion --v_resp_ges[2]
                                                                        where pj.id_partida_ejecucion = v_resp_ges_com[2];


                                                                    END IF; --fin id de error

                                                                 END IF;

                                                          END IF;

                                             END IF;



                                                    --



                                         END IF;

								   /*ELSIF (v_registros.sw_movimiento = 'flujo') THEN --06-12-2021 (may)
                                          --
                                          select cdoc.id_cuenta_doc, cdoc.id_cuenta_doc_fk
                                          into v_registros_cuenta_doc
                                          from cd.tcuenta_doc cdoc
                                          where cdoc.id_int_comprobante = p_id_int_comprobante;

                                          --importe inicial comprometido
                                          select cdoc.importe
                                          into v_importe_fa
                                          from cd.tcuenta_doc cdoc
                                          where cdoc.id_cuenta_doc = v_registros_cuenta_doc.id_cuenta_doc_fk;

                                          --sumar importe de las rendiciones del FAplantar jardin
                                          select sum(cdoc.importe)
                                          into v_importe_total_rendiciones
                                          from cd.tcuenta_doc cdoc
                                          where cdoc.id_cuenta_doc_fk = v_registros_cuenta_doc.id_cuenta_doc_fk;
                                            --raise exception 'llegasinfactor % - %',v_importe_fa,v_importe_total_rendiciones;
                                          IF (v_importe_fa = v_importe_total_rendiciones) THEN

                                          END IF;*/

                                 /*ELSIF (v_registros.sw_movimiento = 'flujo') THEN --06-12-2021 (may) se realiza para FA con detalle para realizar la reversion del importe total comprometido del deposito

                                 		 select cdoc.detalle_cuenta_doc
                                          into v_cuenta_doc
                                          from cd.tcuenta_doc cdoc
                                          where cdoc.nro_tramite= v_registros_comprobante.nro_tramite
                                          and cdoc.id_tipo_cuenta_doc = 1
                                          and cdoc.detalle_cuenta_doc = 'si';

                                 		 --id_partida =12703 INCREMENTO DE CAJA Y BANCOS

                                  		 IF  (v_registros.factor_reversion = 0 and v_cuenta_doc.detalle_cuenta_doc = 'si' and v_registros.id_partida= 12703) THEN
                                         		--SI existe un deposito separar el importe de la rendicion (que inserta desde un inicio en el registro)

                                         		   select cdoc.id_cuenta_doc
                                                   into v_registros_cuenta_doc
                                                   from cd.tcuenta_doc cdoc
                                                   where cdoc.id_int_comprobante = p_id_int_comprobante;

                                                   /*select COALESCE(sum(COALESCE(dpcd.importe_contable_deposito,lb.importe_deposito,0)),0)::numeric
                                                   into v_importe_depositos
                                                   from tes.tts_libro_bancos lb
                                                   left join cd.tdeposito_cd dpcd ON dpcd.id_libro_bancos = lb.id_libro_bancos
                                                   inner join cd.tcuenta_doc c on c.id_cuenta_doc = lb.columna_pk_valor and  lb.columna_pk = 'id_cuenta_doc' and lb.tabla = 'cd.tcuenta_doc'
                                                   where c.estado_reg = 'activo' and c.id_cuenta_doc =  v_registros_cuenta_doc.id_cuenta_doc;

                                                   --verificar la partida ejecucion
                                                   select it.id_partida_ejecucion
                                                   into v_id_partida_eje_transaccion
                                                   from conta.tint_transaccion it
                                                   --where it.id_int_comprobante = p_id_int_comprobante
                                                   where it.id_int_transaccion= v_registros.id_int_transaccion
                                                   and it.id_partida_ejecucion is not null;*/
                                                   --

                                                   ------------------------------
                                                    --insertar partida ejecucion
                                                    ------------------------------

                                                    INSERT INTO  pre.tpartida_ejecucion
                                                                                    (
                                                                                      id_usuario_reg,
                                                                                      fecha_reg,
                                                                                      estado_reg,
                                                                                      --id_partida_ejecucion,
                                                                                      nro_tramite,
                                                                                      monto,
                                                                                      monto_mb,
                                                                                      id_moneda,
                                                                                      id_presupuesto,
                                                                                      id_partida,
                                                                                      tipo_movimiento,
                                                                                      tipo_cambio,
                                                                                      fecha,
                                                                                      id_int_comprobante,
                                                                                      columna_origen,
                                                                                      valor_id_origen
                                                                                    )
                                                                                    VALUES (
                                                                                      p_id_usuario,
                                                                                      now(),
                                                                                      'activo',
                                                                                      --v_array_resp[v_cont],--:id_partida_ejecucion,
                                                                                      v_registros_comprobante.nro_tramite,
                                                                                      (v_registros.importe_debe) *(-1), --p_monto_total[v_cont],
                                                                                      (v_registros.importe_debe) *(-1), --v_monto_mb,
                                                                                      v_id_moneda,
                                                                                      v_registros.id_presupuesto,
                                                                                      v_registros.id_partida,
                                                                                      'comprometido', --tipo_movimiento
                                                                                      NULL,
                                                                                      now(),
                                                                                      p_id_int_comprobante,
                                                                                      'id_int_transaccion',
                                                                                      v_registros.id_int_transaccion

                                                                                    )RETURNING id_partida_ejecucion into v_id_partida_ejecucion;


                                                        /*-- llamar a la funcion para revertir el comprometido
                                                         v_resp_ges = pre.f_gestionar_presupuesto_v2(
                                                                                                      p_id_usuario,
                                                                                                      NULL,  --tipo de cambio,  ya mandamos la moneda convertida
                                                                                                      v_registros.id_presupuesto,
                                                                                                      v_registros.id_partida,
                                                                                                      v_id_moneda,
                                                                                                      (v_registros.importe_debe) *(-1),  --v_monto_cmp*(-1),
                                                                                                      (v_registros.importe_debe) *(-1),  --v_monto_cmp_mb*(-1),
                                                                                                      p_fecha_ejecucion,
                                                                                                      'comprometido',
                                                                                                      v_id_partida_eje_transaccion, --v_registros.id_partida_ejecucion,
                                                                                                      'id_int_transaccion',
                                                                                                      v_registros.id_int_transaccion,--p_fk_llave,
                                                                                                      v_registros_comprobante.nro_tramite,
                                                                                                      p_id_int_comprobante,
                                                                                                      v_registros_comprobante.momento_comprometido,
                                                                                                      v_registros_comprobante.momento_ejecutado,
                                                                                                      v_registros_comprobante.momento_pagado);*/

                                                          --  analizamos respuesta y retornamos error
                                                          IF v_resp_ges[1] = 0 THEN

                                                               --  recuperamos datos del presupuesto
                                                               v_mensaje_error = v_mensaje_error || conta.f_armar_error_presupuesto(v_resp_ges,
                                                                                                       v_registros.id_presupuesto,
                                                                                                       v_registros.codigo_partida,
                                                                                                       v_id_moneda,
                                                                                                       v_id_moneda_base,
                                                                                                       v_momento_presupeustario,
                                                                                                       v_monto_cmp_mb);
                                                                 v_sw_error = true;

                                                          ELSE

                                                              update conta.tint_transaccion it set
                                                                 id_partida_ejecucion_rev = v_id_partida_ejecucion, --v_resp_ges[2],  --partida de reversion
                                                                 fecha_mod = now(),
                                                                 id_usuario_mod = p_id_usuario
                                                              where it.id_int_transaccion  =   v_registros.id_int_transaccion;


                                                          END IF; --fin id de error

                                                    --END IF;

                                         END IF;*/



                                 END IF;  --fin if es partida presupuestaria


                          ELSIF  v_momento_aux='solo pagar'  THEN

                                 --  RAC 29/12/2016
                                 --  para los comprobantes de pago verificar que el devenga tenga gestion
                                 --  menor o igual a la gestion del pago

                                 IF exists ( select 1
                                             from conta.tint_comprobante ic
                                             inner join param.tperiodo per on per.id_periodo = ic.id_periodo
                                             where ic.id_int_comprobante = ANY(v_registros_comprobante.id_int_comprobante_fks)
                                                   and per.id_gestion > v_registros_comprobante.id_gestion) THEN
                                       raise exception 'No puede pagar, por que la fecha de pago no es coherente con la fecha del devengado';
                                 END IF;


                                 -- si es solo pagar debemos identificar las transacciones del devengado
                                 FOR  v_registros_dev in (
                                                                  select
                                                                    ird.id_int_rel_devengado,
                                                                    ird.monto_pago,
                                                                    ird.monto_pago_mb,
                                                                    ird.id_int_transaccion_dev,
                                                                    it.id_partida_ejecucion_dev,
                                                                    it.importe_reversion,
                                                                    it.factor_reversion,
                                                                    it.monto_pagado_revertido,
                                                                    ic.fecha,
                                                                    it.id_partida_ejecucion_rev,
                                                                    p.codigo as codigo_partida,
                                                                    it.id_centro_costo as id_presupuesto,
                                                                    p.id_partida,
                                                                    ic.nro_tramite

                                                                  from  conta.tint_rel_devengado ird
                                                                  inner join conta.tint_transaccion it  on it.id_int_transaccion = ird.id_int_transaccion_dev
                                                                  inner join pre.tpartida p on p.id_partida = it.id_partida

                                                                  inner join conta.tint_comprobante ic on ic.id_int_comprobante = it.id_int_comprobante
                                                                  where  ird.id_int_transaccion_pag = v_registros.id_int_transaccion
                                                                         and ird.estado_reg = 'activo'
                                                                         and p.sw_movimiento = 'presupuestaria'
                                                                 ) LOOP


                                               IF v_sw_moneda_base = 'si' THEN
                                                 v_monto_x_pagar = v_registros_dev.monto_pago_mb;
                                                 v_monto_x_pagar_mb = v_registros_dev.monto_pago_mb;
                                               ELSE
                                                 v_monto_x_pagar = v_registros_dev.monto_pago;
                                                 v_monto_x_pagar_mb = v_registros_dev.monto_pago_mb;
                                               END IF;


                                               -----------------------------------------------------------------------------
                                               --   Obtener el factor de reversion de la transaccion de devengado        ---
                                               --   Ejemplo fue comprometido 100  se devego 87 por el IVA se revirtio 13 ---
                                               --   presupeustariamente solo pagamos el 87                               ---
                                               -----------------------------------------------------------------------------
                                              IF  v_registros_dev.factor_reversion > 0 and v_registros_dev.id_partida_ejecucion_rev is not null   THEN

                                                    v_monto_rev =  COALESCE(round(v_monto_x_pagar * v_registros_dev.factor_reversion,2), 0);
                                                    v_monto_rev_mb =  COALESCE(round(v_monto_x_pagar_mb * v_registros_dev.factor_reversion,2), 0);
                                                    v_monto_x_pagar = v_monto_x_pagar -v_monto_rev;
                                                    v_monto_x_pagar_mb = v_monto_x_pagar_mb - v_monto_rev_mb;

                                                    --  actualizamos el monto no pagado
                                                    UPDATE conta.tint_transaccion it SET
                                                      monto_pagado_revertido = monto_pagado_revertido + v_monto_rev
                                                    WHERE it.id_int_transaccion = v_registros_dev.id_int_transaccion_dev;

                                              END IF; -- fin if factor de reversion

                                               --si la el año de pago es mayor que el año del devengado , el pago va con fecha de 31 de diciembre del año del devengado
                                               v_ano_1 =  EXTRACT(YEAR FROM  p_fecha_ejecucion::date);
                                               v_ano_2 =  EXTRACT(YEAR FROM  v_registros_dev.fecha::date);

                                               IF  v_ano_1  >  v_ano_2 THEN
                                                  p_fecha_ejecucion = ('31-12-'|| v_ano_2::varchar)::date;
                                               END IF;



                                               -- llamamos a la funcion de ejecucion
                                               v_resp_ges = pre.f_gestionar_presupuesto_v2(
                                                                                    p_id_usuario,
                                                                                    NULL,  --tipo de cambio,  ya mandamos la moneda convertida
                                                                                    v_registros_dev.id_presupuesto,
                                                                                    v_registros_dev.id_partida,
                                                                                    v_id_moneda,
                                                                                    v_monto_x_pagar,
                                                                                    v_monto_x_pagar_mb,
                                                                                    p_fecha_ejecucion,
                                                                                    v_momento_presupeustario,
                                                                                    v_registros_dev.id_partida_ejecucion_dev,
                                                                                    'id_int_rel_devengado',
                                                                                    v_registros_dev.id_int_rel_devengado,--p_fk_llave,
                                                                                    v_registros_dev.nro_tramite,   --nro de tramite del devengado
                                                                                    p_id_int_comprobante,
                                                                                    v_registros_comprobante.momento_comprometido,
                                                                                    v_registros_comprobante.momento_ejecutado,
                                                                                    v_registros_comprobante.momento_pagado);


                                               --  analizamos respuesta y retornamos error
                                               IF v_resp_ges[1] = 0 THEN

                                                         --  recuperamos datos del presupuesto
                                                         v_mensaje_error = v_mensaje_error || conta.f_armar_error_presupuesto(v_resp_ges,
                                                                                               v_registros_dev.id_presupuesto,
                                                                                               v_registros_dev.codigo_partida,
                                                                                               v_id_moneda,
                                                                                               v_id_moneda_base,
                                                                                               v_momento_presupeustario,
                                                                                               v_monto_x_pagar_mb);
                                                         v_sw_error = true;

                                                ELSE


                                                      update conta.tint_rel_devengado rd set
                                                         id_partida_ejecucion_pag = v_resp_ges[2],  --partida ejecucion del pagado
                                                         fecha_mod = now(),
                                                         id_usuario_mod = p_id_usuario
                                                      where rd.id_int_rel_devengado  =  v_registros_dev.id_int_rel_devengado;


                                                END IF; --fin id de error



                                 END LOOP;



                         END IF; -- fin if todo o solo ejecutar, solo pagar


            END LOOP;


            ---------------------------------------------
            --  CONTROL DE ERRORES
            --  si un atransaccion no se pudo ejecutar
            --  se realiza rollback  y reterona el mensaje
            -------------------------------------------

            IF v_sw_error THEN
               raise exception 'Error al procesar presupuesto: %', v_mensaje_error;
            END IF;



     END IF; -- fin del IF , si es cbte presupuestario
   return v_retorno;


EXCEPTION
WHEN OTHERS THEN
			v_resp='';
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje',SQLERRM);
			v_resp = pxp.f_agrega_clave(v_resp,'codigo_error',SQLSTATE);
			v_resp = pxp.f_agrega_clave(v_resp,'procedimientos',v_nombre_funcion);
			raise exception '%',v_resp;
END;
$body$
LANGUAGE 'plpgsql'
VOLATILE
CALLED ON NULL INPUT
SECURITY INVOKER
COST 100;

ALTER FUNCTION conta.f_gestionar_presupuesto_cbte_pxp (p_id_usuario integer, p_id_int_comprobante integer, p_igualar varchar, p_fecha_ejecucion date, p_conexion varchar)
  OWNER TO postgres;