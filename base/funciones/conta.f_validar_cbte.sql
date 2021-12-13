CREATE OR REPLACE FUNCTION conta.f_validar_cbte (
  p_id_usuario integer,
  p_id_usuario_ai integer,
  p_usuario_ai varchar,
  p_id_int_comprobante integer,
  p_igualar varchar = 'no'::character varying,
  p_origen varchar = 'pxp'::character varying,
  p_fecha_ejecucion date = NULL::date,
  p_validar_doc boolean = true
)
RETURNS varchar AS
$body$
/*
	Autor: RCM
    Fecha: 05-09-2013
    Descripción: Función que se encarga de verificar la integridad del comprobante para posteriormente validarlo.

  ***************************************************************************************************


    HISTORIAL DE MODIFICACIONES:

 ISSUE            FECHA:		      AUTOR                 DESCRIPCION

 #0        		05-09-2013        RCM KPLIAN        Función que se encarga de verificar la integridad del comprobante para posteriormente validarlo
 #0       		2015              RAC KPLIAN        Ejecucion presupeustaria, Integracion con ENDESIS -> PXP
 #0       		20/11/2016        RAC KPLIAN        Se invirte la migraicon de cbte PXP -> ENDESIS
 #0       		22/12/2016        RAC KPLIAN        validacion de numeracion en cbtes
 #0       		13/06/2017        RAC KPLIAN        validacion de documentos asocidos de compra o de venta
 #13			    18/10/2017		    RAC KPLIAN		    Al validar comprobantes vamos actualizar e nro de tramite en doc_compra_venta si estan relacionados


*/
DECLARE

	v_debe			numeric;
    v_haber			numeric;
    v_errores 		varchar;
    v_rec_cbte 		record;
    v_registros		record;
    v_doc			varchar;
    v_codigo_clase_cbte			varchar;
    v_nro_cbte		varchar;
    v_id_periodo 	integer;
    v_filas			bigint;
    v_resp			varchar;
    v_nombre_funcion   				varchar;
    v_funcion_comprobante_validado  varchar;
    v_funcion_comprobante_prevalidado  varchar;
    v_funcion_comprobante_editado   varchar;
    v_variacion        				numeric;
    v_nombre_conexion				varchar;
    v_conexion_int_act				varchar;
    v_sincronizar					varchar;
    v_pre_integrar_presupuestos		varchar;
    v_conta_integrar_libro_bancos	varchar;
    v_resp_int_endesis 				varchar;
    v_conta_codigo_estacion			varchar;
    v_sincornizar_central			varchar;


    v_debe_mb						numeric;
    v_haber_mb						numeric;
    v_debe_mt						numeric;
    v_haber_mt						numeric;
    v_variacion_mb					numeric;
    v_variacion_mt					numeric;
    v_sw_rel						boolean;
    v_id_tipo_estado				integer;
    v_id_estado_actual 				integer;
    v_tiene_apertura				varchar;

    v_gasto 						numeric;
    v_recurso 						numeric;
    v_gasto_mb 						numeric;
    v_recurso_mb 					numeric;
    v_gasto_mt						numeric;
    v_recurso_mt 					numeric;
    v_resp_val_doc					varchar[];
    v_tes_integrar_lb_pagado		varchar;

    v_fecha_fin_periodo				date;

    --21/10/2020 franklin.espinoza
	v_prioridad_depto				integer;

BEGIN



    v_nombre_funcion:='conta.f_validar_cbte';
    v_pre_integrar_presupuestos = pxp.f_get_variable_global('pre_integrar_presupuestos');
    v_conta_codigo_estacion = pxp.f_get_variable_global('conta_codigo_estacion');
    v_sincornizar_central = pxp.f_get_variable_global('sincronizar_central');
    v_sincronizar = pxp.f_get_variable_global('sincronizar');
    --raise exception 'Error al Validar Comprobante: comprobante no está en Borrador o en Edición';
	v_errores = '';


    -- Obtención de datos del comprobante
    select
        c.*,
        c.temporal,
        c.vbregional,
        c.tipo_cambio,
        c.id_moneda,
        p.id_gestion ,
        c.id_int_comprobante_origen_central,
        c.origen,
        c.sw_editable,
        c.nro_cbte,
        c.codigo_estacion_origen,
        c.localidad,
        c.id_ajuste,
        c.cbte_reversion,
        c.id_proceso_wf,
        c.nro_tramite,
        c.id_estado_wf,
        c.estado_reg,
        c.id_depto,
        c.id_clase_comprobante,
        c.fecha,
        c.id_periodo,
        cc.id_documento,   --documento con que se genera la numeracion
        cc.codigo as codigo_cbte,
        sis.codigo as codigo_sistema,
        coalesce(c.id_service_request,0) as tipo_cbte --29/10/20 franklin.espinoza
	  into
        v_rec_cbte
    from conta.tint_comprobante c
    inner join conta.tclase_comprobante cc on cc.id_clase_comprobante = c.id_clase_comprobante
    inner join param.tperiodo p on p.id_periodo = c.id_periodo
    inner join segu.tsubsistema sis on sis.id_subsistema = c.id_subsistema
    where id_int_comprobante = p_id_int_comprobante;



    ------------------------------------------------------------------------------------------
    --  Verifica si los cbte de diario cuadran con los dosc/fact/recibos/invoices registrados
    -------------------------------------------------------------------------------------------
     --14-05-2020(may) condicion para los cbtes q no sea obligatorio los documentos
     IF (v_rec_cbte.localidad != 'internacional' and v_rec_cbte.tipo_cbte::varchar = '0'::varchar ) THEN --29/10/20 franklin.espinoza se agrega la regla para comprobantes integracion sigep and v_rec_cbte.tipo_cbte = 0
        v_resp_val_doc =  conta.f_validar_cbte_docs(p_id_int_comprobante, p_validar_doc);
     END IF;

     IF v_resp_val_doc[1] = 'FALSE' THEN
       return v_resp_val_doc[2];
     END IF;


     --Obtiene el documento para la numeración
    select
       doc.codigo,
       ccbte.codigo,
       ccbte.tiene_apertura
    into
      v_doc,
      v_codigo_clase_cbte,
      v_tiene_apertura
    from conta.tclase_comprobante ccbte
    inner join param.tdocumento doc
    on doc.id_documento = ccbte.id_documento
    where ccbte.id_clase_comprobante = v_rec_cbte.id_clase_comprobante;


    --  si  es un comprobante que se migrara a la central, abrimos conexion  (
    --  esto quiere decir que estamos en una regional internacional y se migrara al pxp central
    if v_sincornizar_central = 'true' then

          --si el comprobante viene de endesis y tenemso fecha de ejecucion actualizamos la fecha del comprobante intermedio
         IF p_fecha_ejecucion is NOT NULL THEN

              update conta.tint_comprobante set
                fecha = p_fecha_ejecucion
              where id_int_comprobante = p_id_int_comprobante;

         END IF;
    end if;


    -- TODO revisar cuando abrir conexion ....
    --abrimo conexion dblink
    IF v_conta_codigo_estacion != 'CENTRAL'  or v_sincronizar = 'true' THEN
        select * into v_nombre_conexion from migra.f_crear_conexion();
    END IF;



    -- si es un comprobante editado internacionales , abrimos una segunda conexion
    -- TODO,...para que ???

    IF v_rec_cbte.sw_editable = 'si' and  v_rec_cbte.vbregional = 'si' and  v_conta_codigo_estacion = 'CENTRAL' and v_rec_cbte.localidad != 'nacional'  THEN
         v_conexion_int_act = migra.f_crear_conexion(NULL,'tes.testacion', v_rec_cbte.codigo_estacion_origen);
    END IF;



    --validar que el periodo al que se agregara este abierto
    IF not param.f_periodo_subsistema_abierto(v_rec_cbte.fecha::date, 'CONTA') THEN
        raise exception 'El periodo se encuentra cerrado en contabilidad para la fecha:  %',v_rec_cbte.fecha;
    END IF;


    --Verificación de existencia de al menos 2 transacciones
    select coalesce(count(id_int_transaccion),0)
    into v_filas
    from conta.tint_transaccion
    where id_int_comprobante = p_id_int_comprobante;

    if v_filas < 2 then
    	raise exception 'Validación no realizada: el comprobante debe tener al menos dos transacciones';
    end if;


    --se ejecuta funcion de prevalidacion si existe
    IF v_rec_cbte.id_plantilla_comprobante is not null THEN

                select
                 pc.funcion_comprobante_prevalidado
                into v_funcion_comprobante_prevalidado
                from conta.tplantilla_comprobante pc
                where pc.id_plantilla_comprobante = v_rec_cbte.id_plantilla_comprobante;

                IF  v_funcion_comprobante_prevalidado is not null and v_funcion_comprobante_prevalidado != '' THEN
                   EXECUTE ( 'select ' || v_funcion_comprobante_prevalidado  ||'('||p_id_usuario::varchar||','||COALESCE(p_id_usuario_ai::varchar,'NULL')||','||COALESCE(''''||p_usuario_ai::varchar||'''','NULL')||','|| p_id_int_comprobante::varchar||', '||COALESCE('''' || v_nombre_conexion || '''','NULL')||')');
                END IF;
    END IF;


    --------------------------------------------------------------------------
    --  Verificar igualdad entre todas las monedas
    --  transaccional, base y triangulacion
    --  detectar diferencia por redondedo o por diferencia de cambio
    --------------------------------------------------------------------------

    --3. Verifica igualdad del debe y del haber
    select
         sum(tra.importe_debe),
         sum(tra.importe_haber),
         sum(tra.importe_debe_mb),
         sum(tra.importe_haber_mb),
         sum(tra.importe_debe_mt),
         sum(tra.importe_haber_mt),
         sum(tra.importe_gasto),
         sum(tra.importe_recurso),
         sum(tra.importe_gasto_mb),
         sum(tra.importe_recurso_mb),
         sum(tra.importe_gasto_mt),
         sum(tra.importe_recurso_mt)
    into
       v_debe,
       v_haber,
       v_debe_mb,
       v_haber_mb,
       v_debe_mt,
       v_haber_mt,
       v_gasto,
       v_recurso,
       v_gasto_mb,
       v_recurso_mb,
       v_gasto_mt,
       v_recurso_mt
    from conta.tint_transaccion tra
    where tra.id_int_comprobante = p_id_int_comprobante;


    if v_debe < v_haber then
       v_variacion = v_haber - v_debe;
    elsif v_debe > v_haber then
       v_variacion = v_debe - v_haber;
    end if;

    if v_debe_mb < v_haber_mb then
       v_variacion_mb = v_haber_mb - v_debe_mb;
    elsif v_debe_mb > v_haber_mb then
       v_variacion_mb =  v_debe_mb - v_haber_mb;
    end if;

     if v_debe_mt < v_haber_mt then
       v_variacion_mt = v_haber_mt - v_debe_mt;
    elsif v_debe_mt > v_haber_mt then
       v_variacion_mt = v_debe_mt -  v_haber_mt;
    end if;


    if  v_variacion != 0  then
         v_errores = 'El comprobante no iguala: Diferencia '||v_variacion::varchar;
    end if;

    if  v_variacion_mb != 0  then
         v_errores = 'El comprobante no iguala en moneda base: Diferencia '||v_variacion_mb::varchar;
    end if;

    if  v_variacion_mt != 0  then
        v_errores = 'El comprobante no iguala en moneda de triangulación: Diferencia  '||v_variacion_mt::varchar;
    end if;

    --verifica los monstos presupuestarios

    if v_gasto < v_recurso then
       v_variacion = v_recurso - v_gasto;
    elsif v_gasto > v_recurso then
       v_variacion = v_gasto - v_recurso;
    end if;

    if v_gasto_mb < v_recurso_mb then
       v_variacion_mb = v_recurso_mb - v_gasto_mb;
    elsif v_gasto_mb > v_recurso_mb then
       v_variacion_mb =  v_gasto_mb - v_recurso_mb;
    end if;

     if v_gasto_mt < v_recurso_mt then
       v_variacion_mt = v_recurso_mt - v_gasto_mt;
    elsif v_gasto_mt > v_recurso_mt then
       v_variacion_mt = v_gasto_mt -  v_recurso_mt;
    end if;


    if  v_variacion != 0  then
         v_errores = 'El comprobante (ID: '||p_id_int_comprobante||') no iguala presupuestariamente: Diferencia '||v_variacion::varchar;
    end if;

    if  v_variacion_mb != 0  then
         v_errores = 'El comprobante (ID: '||p_id_int_comprobante||') no iguala presupuestariamente en moneda base: Diferencia '||v_variacion_mb::varchar;
    end if;

    if  v_variacion_mt != 0  then
        v_errores = 'El comprobante (ID: '||p_id_int_comprobante||') no iguala presupuestariamente en moneda de triangulación: Diferencia  '||v_variacion_mt::varchar;
    end if;


    ---------------------------------------------------------------------------------------------------------
    --  Llamar a funcion de comprobante editado,
    --  es la pirmera vez que se valida solo si no tenemos numero de cbte
    --  (por ejm esta llamada se usa en tesorera para revertir el presupuesto comprometido originamente)
    ---------------------------------------------------------------------------------------------------------


    IF   v_rec_cbte.sw_editable = 'si' and  (v_rec_cbte.nro_cbte is null or v_rec_cbte.nro_cbte = '' )   THEN

          IF v_rec_cbte.id_plantilla_comprobante is not null THEN

                --obtener configuracion de la plantillasi existe
                select
                  pc.funcion_comprobante_editado
                into
                  v_funcion_comprobante_editado
                from conta.tplantilla_comprobante pc
                where pc.id_plantilla_comprobante = v_rec_cbte.id_plantilla_comprobante;

              IF v_funcion_comprobante_editado is not null and v_funcion_comprobante_editado != '' THEN
                  EXECUTE ( 'select ' ||v_funcion_comprobante_editado  ||'('||p_id_usuario::varchar||','||COALESCE(p_id_usuario_ai::varchar,'NULL')||','||COALESCE(''''||p_usuario_ai::varchar||'''','NULL')||','|| p_id_int_comprobante::varchar||', '||COALESCE('''' || v_nombre_conexion || '''','NULL')||')');
              END IF;

         END IF;

    END IF;


    --6. Numeración del comprobante
    if v_errores = '' then



            --Se obtiene el periodo
            select po_id_periodo
            into v_id_periodo
            from param.f_get_periodo_gestion(v_rec_cbte.fecha);

           -----------------------------------------
           --  Validaciones de cbte de apertura
           -----------------------------------------
           IF  v_rec_cbte.cbte_apertura = 'si'  THEN
               --si es comprobnate de apertura , validamos que no  exista otro ya validado para  la gestion y departamento

               IF  EXISTS (select 1 from conta.tint_comprobante c
                           inner join param.tperiodo p on p.id_periodo = c.id_periodo
                           where     c.id_depto = v_rec_cbte.id_depto
                                 and c.cbte_apertura = 'si'
                                 and c.estado_reg = 'validado' and p.id_gestion = v_rec_cbte.id_gestion)  THEN

                    raise exception 'ya existe un comprobante de apertura validado para este departamento en esta gestion';
               END IF;

               --valida que sea el primer dia del mes
               IF to_char(v_rec_cbte.fecha::date, 'DD-MM')::varchar != '01-01' THEN
                  raise exception 'El comprobante de apertura debe ser del primero de enero';
               END IF;

               --el comprobante de apertura solo puede ser un comprobante de diaraio
               IF v_codigo_clase_cbte != 'DIARIO' THEN
                 raise exception 'El comprobante de paertura solo puede ser del tipo DIARIO (CDIR) no %', v_codigo_clase_cbte;
               END IF;


            END IF;


           --valida cbte de apertura

           IF v_tiene_apertura = 'no' and  v_rec_cbte.cbte_apertura = 'si' THEN
               raise exception 'Esta clase de cbte no permite registros de apertura';
           END IF;


           ---------------------------------------------------
           --  OBTENCION DE LA NUMERACION DEL CBTE
           --    considera que cada clase de comprobante puede
           --    terner diferentes o los mismo documentos
           --    encargados de generar la numeracion
           -----------------------------------------------------

           --  Obtención del número de comprobante, si no tiene un numero asignado
           IF  v_rec_cbte.nro_cbte is null or v_rec_cbte.nro_cbte  = '' THEN


                --  validamos que la numeracion sea coherente con la fecha y correlativo
                 IF  v_rec_cbte.cbte_apertura = 'no' then
                      --(may)10-12-2019 condicion momentania para que no controle los cbtes y se habilita para Lobaton 22 y Shirley torrez 38
                      /*IF exists (select
                                        1
                                  from conta.tint_comprobante c
                                  inner join conta.tclase_comprobante cc on cc.id_clase_comprobante = c.id_clase_comprobante
                                  where c.id_depto = v_rec_cbte.id_depto
                                        --and c.id_clase_comprobante = v_rec_cbte.id_clase_comprobante
                                        and  cc.id_documento = v_rec_cbte.id_documento
                                        and c.id_periodo = v_rec_cbte.id_periodo
                                        and c.fecha > v_rec_cbte.fecha
                                        and (c.nro_cbte is not null or v_rec_cbte.nro_cbte  != '') ) THEN

                                raise exception 'Existen comprobantes validados con fecha superior al % para este periodo, cambie la fecha', v_rec_cbte.fecha;
                       END IF;*/

                       --(may)09-03-2020 modificacion para el control de fechas que tienen que estar en el mismo periodo y ser el mismo seguimiento de tramite
                       SELECT  per.fecha_fin
                       INTO v_fecha_fin_periodo
                       FROM param.tperiodo per
                       WHERE per.id_periodo = v_id_periodo;

                        --franklin.espinoza 20/10/2020 consulta para internacionales
                     select de.prioridad
                     into v_prioridad_depto
                     from conta.tint_comprobante tic
                     inner join param.tdepto de on de.id_depto = tic.id_depto
                     where tic.id_int_comprobante = p_id_int_comprobante;

                 	 IF (p_id_usuario not in  (22,38) and v_prioridad_depto != 3 and p_id_int_comprobante not in (118556, 113189,111900,111906,111870,111850,111907,113938,115044,111909,113937,113942,111911,111922,113945,113199,111903,111800,
113280,111924,111925,113186,111926,114035,111857,113974,111868,113147,111819,115043,111836,111845,111841,111867,111834,
111831,115337,113956,114007,113996,113957,114002,114000,113986,113993,113989,113948,113951,113953,113787,113274,111981,
113790,116295,116294,114238,115038,115036,111392,112264,114068,115440,110490,111187,111873,111846,111862,111820,113027,
110549,111824,111854,113965,111983,112094,113967,112092,111828,113969,110552,111825,111973,111914,111901,113228,111802,
113041,111927,111875,111877,110546,111863,113340,111811,115228,111842,111808,111814,111916,111928,115231,111908,115224,
113144,111856,113142,113028,115317,115324,113337,113150,111979,113154,113339,113970,113338,113244,113227,113029,113292,
114061,113226,113175,114058,113169,113982,114054,113225,113972,110550,113192,111851,113973,111840,113224,111977,111975,
113246,113223,111806,113030,111804,111816,115226,113220,111792,113176,113218,111920,113217,111918,113215,113975,114843,
113173,113174,113210,113977,113979,113170,115326,113981,113167,113033,115309,114052,116049,116046,116053,116051,116055,
111835,113209,113032,110212,111912,112675,112347,113983,114681,114680,114685,110252,116059,114240,116288,114673,112635,
113377,113376,112309,114460,114671,114027,114403,112693,113325,114318,114319,115548,114748,110082,110076,111699,113831,
115684,116305,116308,116300,116298,116303,109076,109079,109137,109280,114414,115234,110409,113984,110411,110578,110415,
110580,110417,110419,110421,113206,109608,109592,109590,109586,109581,109576,109574,109572,109570,110554,110556,109513,
109518,109527,111826,109536,115333,109543,109553,109557,109559,109563,115308,110345,110340,109758,109756,110341,110342,
109744,114048,109746,109748,109754,109751,110343,110344,109753,114046,110243,110245,109762,109764,109766,109640,109641,
109642,110305,110307,110333,110309,110311,110313,110315,110317,110320,110323,110241,110273,110262,110326,110330,111929,
113959,113248,111930,111864,113201,113250,113203,111931,111923,110247,110128,110130,110134,114587,114260,110008,110017,
113121,113123,110019,110021,114582,114579,109999,116040,116044,109997,109671,110005,110010,110587,112283,115078,110606,
114863,109626,111759,112674,109901,110589,111233,112294,111243,111238,111236,112286,112684,111241,115427,114232,110159,
113095,114321,114316,113085,113098,113100,113102,113093,115546,115089,114415,114689,115556,115555,115558,115550)) THEN
                     --raise exception 'llleagh % > %',v_rec_cbte.fecha, v_fecha_fin_periodo;
                    	 IF (v_rec_cbte.fecha > v_fecha_fin_periodo and v_rec_cbte.fecha != v_fecha_fin_periodo) THEN

                              IF exists (select
                                                1
                                          from conta.tint_comprobante c
                                          inner join conta.tclase_comprobante cc on cc.id_clase_comprobante = c.id_clase_comprobante
                                          where c.id_depto = v_rec_cbte.id_depto
                                                and c.id_clase_comprobante = v_rec_cbte.id_clase_comprobante
                                                and  cc.id_documento = v_rec_cbte.id_documento
                                                and c.id_periodo = v_rec_cbte.id_periodo
                                                --and c.fecha > v_rec_cbte.fecha
                                                and (c.nro_cbte is not null or v_rec_cbte.nro_cbte  != '') ) THEN

                                        raise exception 'Existen comprobantes validados con fecha superior al % para este periodo, cambie la fecha. ', to_char(v_rec_cbte.fecha, 'DD/MM/YYYY');
                               END IF;
                          ELSIF (v_rec_cbte.fecha != v_fecha_fin_periodo) THEN

                          		IF exists (select
                                                1
                                          from conta.tint_comprobante c
                                          inner join conta.tclase_comprobante cc on cc.id_clase_comprobante = c.id_clase_comprobante
                                          where c.id_depto = v_rec_cbte.id_depto
                                                and c.id_clase_comprobante = v_rec_cbte.id_clase_comprobante
                                                and  cc.id_documento = v_rec_cbte.id_documento
                                                and c.id_periodo = v_rec_cbte.id_periodo
                                                and c.fecha > v_rec_cbte.fecha
                                                and (c.nro_cbte is not null or v_rec_cbte.nro_cbte  != '') ) THEN

                                        raise exception 'Existen comprobantes validados con fecha superior al % para este periodo, cambie la fecha. ', to_char(v_rec_cbte.fecha, 'DD/MM/YYYY');
                               END IF;
                          END IF;


               		END IF;

                 else
                   -- si un comprobante de apertura
                   if   to_char(v_rec_cbte.fecha::date, 'MM')::varchar != '01'  then
                        raise exception 'los cbte de apertura debe ser de enero';
                   end if;

                    if   to_char(v_rec_cbte.fecha::date, 'DD')::integer  > 5  then
                        raise exception 'los cbte de apertura deben estar en los primeros 5 dias del año ';
                   end if;

                   --
                 end if;


                -- Si no es un cbte de apertura (pero su clase de cbte admite cbte de apertura)
                -- y estamos en enero fuerza el saltar inicio (dejar el primer numero para el cbte de apertura)

                IF  v_tiene_apertura = 'si' and v_rec_cbte.cbte_apertura = 'no' and   to_char(v_rec_cbte.fecha::date, 'MM')::varchar = '01'  THEN


                       v_nro_cbte =  param.f_obtener_correlativo(
                                 v_doc,
                                 v_id_periodo,-- par_id,
                                 NULL, --id_uo
                                 v_rec_cbte.id_depto,    -- id_depto
                                 p_id_usuario,
                                 'CONTA',
                                 NULL,
                                 0,
                                 0,
                                 'no_aplica',
                                 0,
                                 'no_aplica',
                                 1,
                                 'si',  --par_saltar_inicio
                                 'no');

                ELSEIF v_rec_cbte.cbte_apertura = 'no' THEN
                    --si no es un comprobante de apertura y no es enero genera la nmeracion normalmente
                   v_nro_cbte =  param.f_obtener_correlativo(
                                 v_doc,
                                 v_id_periodo,-- par_id,
                                 NULL, --id_uo
                                 v_rec_cbte.id_depto,    -- id_depto
                                 p_id_usuario,
                                 'CONTA',
                                 NULL);


                 ELSEIF v_rec_cbte.cbte_apertura = 'si' THEN
                   --si es un comprobante de inicio fuerza a optener el primer numero
                    v_nro_cbte =  param.f_obtener_correlativo(
                               v_doc,
                               v_id_periodo,-- par_id,
                               NULL, --id_uo
                               v_rec_cbte.id_depto,    -- id_depto
                               p_id_usuario,
                               'CONTA',
                               NULL,
                               0,
                               0,
                               'no_aplica',
                               0,
                               'no_aplica',
                               1,
                               'no',  --par_saltar_inicio
                               'si'); --par_forzar_inicio

                ELSE
                  raise exception 'tipo de cbte no previsto';
                END IF;

           ELSE
              v_nro_cbte = v_rec_cbte.nro_cbte;
           END IF;

          -----------------------------------------------------
          -- Llevar e estado de WF del cbte a validado
          --
          -------------------------------------------------------

           -- llevar a cbte al estado validado ...
            PERFORM conta.f_cambia_estado_wf_cbte(p_id_usuario, p_id_usuario_ai, p_usuario_ai,
                                                  p_id_int_comprobante,
                                                  'validado',
                                                  'Cbte validado');


           --Se guarda el número del comprobante
            update conta.tint_comprobante set
              nro_cbte = v_nro_cbte
            where id_int_comprobante = p_id_int_comprobante;


          ----------------------------------------------------------------------
          --  Si es solo un cbte de pago  validar la relacion con el devengado
          ----------------------------------------------------------------------
          --TODO analizar el caso de cbte de pago que se revierten
          --TODO analizar, cosnidera todas las transcciones que afecten bancos

          IF v_rec_cbte.sw_editable = 'si' and v_rec_cbte.momento_comprometido = 'no'  and  v_rec_cbte.momento_ejecutado = 'no'  and    v_rec_cbte.momento_pagado = 'si'  THEN
             v_sw_rel = TRUE;
             FOR v_registros in  (
                                       select
                                            itp.id_int_transaccion,
                                            itp.importe_gasto as importe_gasto_pag,
                                            itp.importe_recurso as importe_recurso_pag,
                                            sum(itd.importe_gasto) as importe_gasto_dev,
                                            sum(itd.importe_recurso) as importe_recurso_dev,
                                            sum(rd.monto_pago ) as total
                                       from conta.tint_rel_devengado rd
                                       inner join conta.tint_transaccion itp on rd.id_int_transaccion_pag = itp.id_int_transaccion
                                       inner join conta.tint_transaccion itd on rd.id_int_transaccion_dev = itd.id_int_transaccion
                                       where itp.id_int_comprobante = v_rec_cbte.id_int_comprobante
                                       group by
                                            itp.id_int_transaccion,
                                            itp.importe_gasto ,
                                            itp.importe_recurso ) LOOP


                                  --validacion de  pago o  reversión de pago segun el signo

                                  IF v_registros.total > 0 THEN

                                      IF v_registros.total < v_registros.importe_gasto_pag and v_registros.importe_recurso_pag = 0   THEN
                                        raise exception 'a) El monto devengado (%) no es suficiente para realizar el pago (%), verifique la relación devengado pago',v_registros.total,v_registros.importe_gasto_pag;
                                      END IF;

                                      IF v_registros.total < v_registros.importe_recurso_pag and v_registros.importe_gasto_pag = 0   THEN
                                        raise exception 'b) El monto devengado (%) no es suficiente para realizar el pago (%), verifique la relación devengado pago',v_registros.total,v_registros.importe_recurso_pag;
                                      END IF;

             					  ELSEIF v_registros.total < 0 THEN

                                   --Si es una transaccion de reversion ...

                                      IF (v_registros.total*-1) < v_registros.importe_gasto_pag and v_registros.importe_recurso_pag = 0   THEN
                                        raise exception 'a) El monto relacionado/devengado (%) no es suficiente para realizar la reversion (%), verifique la relación devengado -  pago',(v_registros.total*-1),v_registros.importe_gasto_pag;
                                      END IF;

                                      IF (v_registros.total*-1) < v_registros.importe_gasto_pag and v_registros.importe_recurso_pag = 0   THEN
                                        raise exception 'b) El monto relacioando/devengado (%) no es suficiente para realizar la reversion (%), verifique la relación devengado - pago',(v_registros.total*-1),v_registros.importe_recurso_pag;
                                      END IF;
                                  END IF;
                                  v_sw_rel = FALSE;


                END LOOP;

                IF v_sw_rel   THEN
                   raise exception 'El Cbte es de pago presupuestario pero tiene relación con un devengado';
                END IF;

          END IF;



         ----------------------------------------------------------------------------------------
         -- si viene de una plantilla de comprobante busca la funcion de validacion configurada
         ----------------------------------------------------------------------------------------

         IF v_rec_cbte.id_plantilla_comprobante is not null  THEN

                select
                 pc.funcion_comprobante_validado
                into v_funcion_comprobante_validado
                from conta.tplantilla_comprobante pc
                where pc.id_plantilla_comprobante = v_rec_cbte.id_plantilla_comprobante;


                -- raise exception 'llega % ---', v_funcion_comprobante_validado;

                -- raise exception 'validar comprobante pxp %',v_funcion_comprobante_validado ;
              	 IF  v_funcion_comprobante_validado is not null and v_funcion_comprobante_validado != '' THEN
                 	if p_id_int_comprobante not in (118556, 113189,111900,111906,111870,111850,111907,113938,115044,111909,113937,113942,111911,111922,113945,113199,111903,111800,
113280,111924,111925,113186,111926,114035,111857,113974,111868,113147,111819,115043,111836,111845,111841,111867,111834,
111831,115337,113956,114007,113996,113957,114002,114000,113986,113993,113989,113948,113951,113953,113787,113274,111981,
113790,116295,116294,114238,115038,115036,111392,112264,114068,115440,110490,111187,111873,111846,111862,111820,113027,
110549,111824,111854,113965,111983,112094,113967,112092,111828,113969,110552,111825,111973,111914,111901,113228,111802,
113041,111927,111875,111877,110546,111863,113340,111811,115228,111842,111808,111814,111916,111928,115231,111908,115224,
113144,111856,113142,113028,115317,115324,113337,113150,111979,113154,113339,113970,113338,113244,113227,113029,113292,
114061,113226,113175,114058,113169,113982,114054,113225,113972,110550,113192,111851,113973,111840,113224,111977,111975,
113246,113223,111806,113030,111804,111816,115226,113220,111792,113176,113218,111920,113217,111918,113215,113975,114843,
113173,113174,113210,113977,113979,113170,115326,113981,113167,113033,115309,114052,116049,116046,116053,116051,116055,
111835,113209,113032,110212,111912,112675,112347,113983,114681,114680,114685,110252,116059,114240,116288,114673,112635,
113377,113376,112309,114460,114671,114027,114403,112693,113325,114318,114319,115548,114748,110082,110076,111699,113831,
115684,116305,116308,116300,116298,116303,109076,109079,109137,109280,114414,115234,110409,113984,110411,110578,110415,
110580,110417,110419,110421,113206,109608,109592,109590,109586,109581,109576,109574,109572,109570,110554,110556,109513,
109518,109527,111826,109536,115333,109543,109553,109557,109559,109563,115308,110345,110340,109758,109756,110341,110342,
109744,114048,109746,109748,109754,109751,110343,110344,109753,114046,110243,110245,109762,109764,109766,109640,109641,
109642,110305,110307,110333,110309,110311,110313,110315,110317,110320,110323,110241,110273,110262,110326,110330,111929,
113959,113248,111930,111864,113201,113250,113203,111931,111923,110247,110128,110130,110134,114587,114260,110008,110017,
113121,113123,110019,110021,114582,114579,109999,116040,116044,109997,109671,110005,110010,110587,112283,115078,110606,
114863,109626,111759,112674,109901,110589,111233,112294,111243,111238,111236,112286,112684,111241,115427,114232,110159,
113095,114321,114316,113085,113098,113100,113102,113093,115546,115089,114415,114689,115556,115555,115558,115550) then
                    	EXECUTE ( 'select ' || v_funcion_comprobante_validado  ||'('||p_id_usuario::varchar||','||COALESCE(p_id_usuario_ai::varchar,'NULL')||','||COALESCE(''''||p_usuario_ai::varchar||'''','NULL')||','|| p_id_int_comprobante::varchar||', '||COALESCE('''' || v_nombre_conexion || '''','NULL')||')');
                	end if;
                 end IF;
          ELSE
                -- si no tenemos plantilla de comprobante revisamos la funcin directamente
                IF v_rec_cbte.funcion_comprobante_validado is not NULL and v_rec_cbte.funcion_comprobante_validado != '' THEN
                   EXECUTE ( 'select ' || v_rec_cbte.funcion_comprobante_validado  ||'('||p_id_usuario::varchar||','||COALESCE(p_id_usuario_ai::varchar,'NULL')||','||COALESCE(''''||p_usuario_ai::varchar||'''','NULL')||','|| p_id_int_comprobante::varchar||', '||COALESCE('''' || v_nombre_conexion || '''','NULL')||')');
                END IF;

         END IF;



         --------------------------------------------------
         -- Validaciones sobre el cbte y sus transacciones
         --  INTEGRAR CON libro de Bancos comprobantes de pago
         ----------------------------------------------------

         if p_id_int_comprobante not in (113189,111900,111906,111870,111850,111907,113938,115044,111909,113937,113942,111911,111922,113945,113199,111903,111800,
113280,111924,111925,113186,111926,114035,111857,113974,111868,113147,111819,115043,111836,111845,111841,111867,111834,
111831,115337,113956,114007,113996,113957,114002,114000,113986,113993,113989,113948,113951,113953,113787,113274,111981,
113790,116295,116294,114238,115038,115036,111392,112264,114068,115440,110490,111187,111873,111846,111862,111820,113027,
110549,111824,111854,113965,111983,112094,113967,112092,111828,113969,110552,111825,111973,111914,111901,113228,111802,
113041,111927,111875,111877,110546,111863,113340,111811,115228,111842,111808,111814,111916,111928,115231,111908,115224,
113144,111856,113142,113028,115317,115324,113337,113150,111979,113154,113339,113970,113338,113244,113227,113029,113292,
114061,113226,113175,114058,113169,113982,114054,113225,113972,110550,113192,111851,113973,111840,113224,111977,111975,
113246,113223,111806,113030,111804,111816,115226,113220,111792,113176,113218,111920,113217,111918,113215,113975,114843,
113173,113174,113210,113977,113979,113170,115326,113981,113167,113033,115309,114052,116049,116046,116053,116051,116055,
111835,113209,113032,110212,111912,112675,112347,113983,114681,114680,114685,110252,116059,114240,116288,114673,112635,
113377,113376,112309,114460,114671,114027,114403,112693,113325,114318,114319,115548,114748,110082,110076,111699,113831,
115684,116305,116308,116300,116298,116303,109076,109079,109137,109280,114414,115234,110409,113984,110411,110578,110415,
110580,110417,110419,110421,113206,109608,109592,109590,109586,109581,109576,109574,109572,109570,110554,110556,109513,
109518,109527,111826,109536,115333,109543,109553,109557,109559,109563,115308,110345,110340,109758,109756,110341,110342,
109744,114048,109746,109748,109754,109751,110343,110344,109753,114046,110243,110245,109762,109764,109766,109640,109641,
109642,110305,110307,110333,110309,110311,110313,110315,110317,110320,110323,110241,110273,110262,110326,110330,111929,
113959,113248,111930,111864,113201,113250,113203,111931,111923,110247,110128,110130,110134,114587,114260,110008,110017,
113121,113123,110019,110021,114582,114579,109999,116040,116044,109997,109671,110005,110010,110587,112283,115078,110606,
114863,109626,111759,112674,109901,110589,111233,112294,111243,111238,111236,112286,112684,111241,115427,114232,110159,
113095,114321,114316,113085,113098,113100,113102,113093,115546,115089,114415,114689,115556,115555,115558,115550) then
             IF not conta.f_int_trans_validar(p_id_usuario,p_id_int_comprobante) THEN
                  raise exception 'error al realizar validaciones en el combrobante';
             END IF;
         end if;


         ---------------------------------------------------------------------------------------
         -- SI estamos en una regional internacional y  el comprobante es propio de la estacion
         -- migramos a contabilidad central
         ---------------------------------------------------------------------------------------
         --(franklin.espinoza)despues de cambiar a estado validadodo se migra el comprobante al ERP BOLIVIA.
         --if pxp.f_get_variable_global('ESTACION_inicio') = 'BUE' then}
         --(maylee.perez)modificacion para todas las estaciones internacionales BUE, MIA, MAD,SAO y no ingrese a estacion central BOL
         if pxp.f_get_variable_global('ESTACION_inicio') != 'BOL' then

          select *
          into v_nombre_conexion
          from migra.f_crear_conexion();

          v_resp =  migra.f_migrar_cbte_a_central(p_id_int_comprobante, v_nombre_conexion);

         end if;
		 --19-11-2019 (maylee.perez) se comenta porque sale doble registro de cbte
         /*IF v_conta_codigo_estacion != 'CENTRAL' and v_rec_cbte.origen is NULL THEN
             v_resp =  migra.f_migrar_cbte_a_central(p_id_int_comprobante, v_nombre_conexion);
         END IF;*/



         ---------------------------------------------------------------------------
         --  Si estamos en la CENTRAL y el comprobante es  internacional  debemos  actualizar las modificaciones
         --  que pudieran haber sido realizadas al cbte en la estación regioanl
         ----------------------------------------------------------------------------

          IF  v_rec_cbte.sw_editable = 'si' and  v_rec_cbte.vbregional = 'si' and  v_conta_codigo_estacion = 'CENTRAL' and v_rec_cbte.localidad != 'nacional'  THEN
             v_resp =  migra.f_migrar_act_cbte_a_regional(p_id_int_comprobante, p_id_usuario ,v_conexion_int_act);
          END IF;


         ---------------------------------------------------------------------------------------------------
         --  SI el comprobante se valida en central (v_sincronizar = 'true' solo la central debe tener esta variable)
         --  y  la sincronizacion esta habilitada  migramos el cbte a ENDESIS
         --  si la moneda  no es dolares debemos convertir a Bolivianos
         --  TODO ...para que revisa si no es ajuste ..???? ...no migre lso cbtes de ajuste?
         ----------------------------------------------------------------------------------------------------


         IF (v_sincronizar = 'true'   and  v_rec_cbte.id_ajuste is null and  v_rec_cbte.fecha <= '31/12/2016'::Date)THEN
             -- si sincroniza locamente con endesis,
             -- marcando la bandera que proviene de regional internacional  (TODO ver para que es esta bandera ????)

             v_resp_int_endesis =  migra.f_migrar_cbte_endesis(p_id_int_comprobante, v_nombre_conexion, 'si');

         END IF;


         ------------------------------------------------------------------------
         --  #13  actualiza nro de tramite en documentos de compra venta relacionados
         -------------------------------------------------------------------------

           update conta.tdoc_compra_venta d set
                nro_tramite =  v_rec_cbte.nro_tramite
           where d.id_int_comprobante = p_id_int_comprobante;



         -----------------------------------------------------------------------------------------------
         --  Valifacion presupuestaria del comprobante  (ejecutamos el devengado o ejecutamos el pago)
         --  si es de una regional internacional y es moneda diferente de dolares convertimos a Bolivianos
         ------------------------------------------------------------------------------------------------
       --raise exception 'lelga %',v_rec_cbte.fecha;
         IF v_pre_integrar_presupuestos = 'true' THEN  --en las regionales internacionales la sincro de presupeustos esta deshabilitada

             IF v_sincronizar = 'true' and  v_rec_cbte.fecha <= '31/12/2016'::Date THEN

                v_resp =  conta.f_gestionar_presupuesto_cbte(p_id_usuario,p_id_int_comprobante,'no',p_fecha_ejecucion, v_nombre_conexion);

             ELSE

                v_resp =  conta.f_gestionar_presupuesto_cbte_pxp(p_id_usuario, p_id_int_comprobante, 'no', p_fecha_ejecucion, v_nombre_conexion);
             END IF;
         END IF;



         --------------------------------------------------
         --10.cerrar conexiones dblink si es que existe
         -------------------------------------------------

         --cerrar la conexion de actulizacion (que puede ser paralela a la de jecucion de presupesutos)  central -> regional
         if  v_conexion_int_act is not null then
              select * into v_resp from migra.f_cerrar_conexion(v_conexion_int_act,'exito');
         end if;

         --cerrar la conexion comun (central-> endesis,  regional -> central)
         if  v_nombre_conexion is not null then
              select * into v_resp from migra.f_cerrar_conexion(v_nombre_conexion,'exito');
         end if;

    else
    	raise exception 'Validación no realizada: %', v_errores;
    end if;



    --8. Respuesta
    return 'Comprobante validado';

EXCEPTION
WHEN OTHERS THEN
	if (current_user like '%dblink_%') then
    	v_resp = pxp.f_obtiene_clave_valor(SQLERRM,'mensaje','','','valor');
        if v_resp = '' then
        	v_resp = SQLERRM;
        end if;
    	return 'error' || '#@@@#' || v_resp;
    else
			v_resp='';
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje',SQLERRM);
			v_resp = pxp.f_agrega_clave(v_resp,'codigo_error',SQLSTATE);
			v_resp = pxp.f_agrega_clave(v_resp,'procedimientos',v_nombre_funcion);
			raise exception '%',v_resp;
    end if;
END;
$body$
LANGUAGE 'plpgsql'
VOLATILE
CALLED ON NULL INPUT
SECURITY INVOKER
COST 100;