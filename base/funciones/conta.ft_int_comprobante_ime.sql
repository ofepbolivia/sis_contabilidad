CREATE OR REPLACE FUNCTION conta.ft_int_comprobante_ime (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Sistema de Contabilidad
 FUNCION: 		conta.ft_int_comprobante_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'conta.tint_comprobante'
 AUTOR: 		 (admin)
 FECHA:	        29-08-2013 00:28:30
 COMENTARIOS:
***************************************************************************
 HISTORIAL DE MODIFICACIONES:

 DESCRIPCION:
 AUTOR:
 FECHA:
***************************************************************************/

DECLARE

	v_nro_requerimiento    	integer;
	v_parametros           	record;
    v_rec_cbte_fk          	record;

	v_id_requerimiento     	integer;
	v_resp		            varchar;
	v_nombre_funcion        text;
	v_mensaje_error         text;
	v_id_int_comprobante	integer;
	v_id_subsistema			integer;
	v_rec					record;
	v_result				varchar;
    v_rec_cbte record;
    v_funcion_comprobante_eliminado varchar;
    v_id_subsistema_conta			integer;
    v_resp2							varchar;
    v_reg_cbte						record;
    v_momento_comprometido			varchar;
    v_momento_ejecutado 			varchar;
    v_momento_pagado 				varchar;
    v_tipo_comprobante				varchar;
    v_id_moneda_tri					integer;
    v_tc_1							numeric;
    v_tc_2							numeric;
    v_tc_3 							numeric;
    v_id_int_comprobante_bk			integer;
    v_ges_1							record;
    v_ges_2							record;
    v_registros						record;

    v_id_tipo_relacion_comprobante	integer;
    v_id_clase_comprobante			integer;
    v_id_int_transaccion			integer;
    v_registros_dev					record;
    v_num_tramite					varchar;
    va_id_int_cbte_fk				integer[];
    v_id_proceso_macro				integer;
    v_codigo_proceso_macro 			varchar;
    v_codigo_tipo_proceso 			varchar;
    v_id_proceso_wf					integer;
    v_id_estado_wf					integer;
    v_codigo_estado					varchar;
    v_id_tipo_estado				integer;
    v_codigo_estado_siguiente		varchar;
    v_id_depto 						integer;
    v_obs							varchar;
    v_acceso_directo 				varchar;
    v_clase 						varchar;
    v_parametros_ad 				varchar;
    v_tipo_noti 					varchar;
    v_titulo  						varchar;
    v_id_estado_actual 				integer;
    v_registros_proc 				record;
    v_codigo_tipo_pro   			varchar;
    v_id_cuenta_bancaria 			integer;
    v_id_depto_lb 					integer;
    v_id_depto_conta 				integer;
    v_id_cuenta_bancaria_mov 		integer;
    v_operacion 					varchar;
    v_id_funcionario				integer;
    v_id_usuario_reg				integer;
    v_id_estado_wf_ant				integer;
    v_clcbt_desc					varchar;
    v_id_partida_ejecucion			integer;
    va_id_int_comprobante_fks		integer[];
    v_id_moneda_act					integer;
    v_id_gestion_cos				integer;
    v_id_gestion_cbte				integer;
    v_anio_gestion			 	    integer;
 	v_id_gestion_cosfin				integer;

    v_anio_com						integer;

    v_conta_codigo_estacion			varchar;
    v_sincronizar					varchar;
    v_nombre_conexion				varchar;


  v_nro_tramite varchar;
   v_registros_cd record;
   estado_cbte	varchar;

   v_periodo_fecha_cbte				integer;
   v_periodo_anio_cbte				integer;
   v_fecha_ini						date;
   v_fecha_fin						date;
   v_fecha							date;
   v_gestion_cbte					integer;
   v_periodo_mes_now				integer;
   v_periodo_anio_now				integer;

   v_reg_cbte_sol					record;
   v_importe_debe					numeric;
   v_importe_haber					numeric;
   v_importe_total_debe				numeric;
   v_importe_debe_sol				numeric;
   v_importe_haber_sol				numeric;
   v_mon_tri						numeric;
   v_mon_act						numeric;
   v_ofi_tri						numeric;
   v_ofi_act						numeric;

    --begin franklin.espinoza 27/09/2020
    v_id_dl							  integer;
	  v_id_cb							  integer;

    v_localidad						varchar;
    v_reversion						varchar;


	v_registros_int_cbte			record;
    v_id_tipo_proceso				integer;

    v_id_depto_libro				integer;

    v_libro_bancos               record;
    v_convertido				varchar;

BEGIN

    v_nombre_funcion = 'conta.ft_int_comprobante_ime';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'CONTA_INCBTE_INS'
 	#DESCRIPCION:	Insercion de registros
 	#AUTOR:		admin
 	#FECHA:		29-08-2013 00:28:30
	***********************************/

	if(p_transaccion='CONTA_INCBTE_INS')then

        begin

        	------------------
        	-- VALIDACIONES
        	------------------
        	select
              id_subsistema
              into
             v_id_subsistema_conta
            from segu.tsubsistema
            where codigo = 'CONTA';

            --SUBSISTEMA: Obtiene el id_subsistema del Sistema de Contabilidad si es que no llega como parámetro
        	IF  pxp.f_existe_parametro(p_tabla,'id_subsistema') THEN

                 IF v_parametros.id_subsistema is not NULL THEN
               	     v_id_subsistema = v_parametros.id_subsistema;
        	     else
                    v_id_subsistema = v_id_subsistema_conta;
                 end if;
        	ELSE
                v_id_subsistema = v_id_subsistema_conta;
            END IF;


            v_id_moneda_tri  = param.f_get_moneda_triangulacion();
            v_id_moneda_act  = param.f_get_moneda_actualizacion();

            --validacion de tipos de cambios
            IF v_parametros.tipo_cambio is NULL or  v_parametros.tipo_cambio_2 is NULL or  v_parametros.tipo_cambio_3 is NULL THEN
              raise exception 'no se definieron los tipos de cambio';
            END IF;

            IF  v_parametros.id_config_cambiaria is NULL  THEN
              raise exception 'la configuracion cambiara no puede ser nula';
            END IF;

            v_momento_comprometido = 'no';
            v_momento_ejecutado = 'no';
            v_momento_pagado = 'no';

            --momentos comprometido
            IF v_parametros.momento_comprometido = 'true' THEN
             v_momento_comprometido = 'si';
            END IF;



            --momentos presupeustarios
            IF v_parametros.momento_ejecutado = 'true' THEN
             v_momento_ejecutado = 'si';
            END IF;

            IF v_parametros.momento_pagado = 'true' THEN
             v_momento_pagado = 'si';
            END IF;


            --segun la clase  del comprobante definir si es presupeustario o contable
            select
              cc.tipo_comprobante,
              cc.descripcion
            into
              v_tipo_comprobante,
              v_clcbt_desc
            from conta.tclase_comprobante cc
            where cc.id_clase_comprobante = v_parametros.id_clase_comprobante;


            --PERIODO  Obtiene el periodo a partir de la fecha
        	v_rec = param.f_get_periodo_gestion(v_parametros.fecha);
            va_id_int_cbte_fk = (string_to_array(v_parametros.id_int_comprobante_fks,','))::INTEGER[];

            --raise exception '%', va_id_int_cbte_fk;

            -------------------------------
            --   GENERAR PROCESO DEL WF
            -------------------------------

           -- raise exception 'sss ---  %  ',va_id_int_cbte_fk;
        	IF va_id_int_cbte_fk is not null and va_id_int_cbte_fk [1] is not null THEN

                --  dispara proceso
                --  si tiene  un cbte relacion recuperar el nro de tramite

                 select
                    cbte.nro_tramite,
                    cbte.id_proceso_wf,
                    cbte.id_estado_wf
                 into
                   v_rec_cbte_fk
                 from conta.tint_comprobante cbte
                 where cbte.id_int_comprobante = va_id_int_cbte_fk[1];

                 -----------------------------------
                 -- dispara el comprobante
                 ----------------------------------
                   SELECT
                             ps_id_proceso_wf,ps_id_estado_wf, ps_codigo_estado, ps_nro_tramite
                       into
                             v_id_proceso_wf,v_id_estado_wf,v_codigo_estado, v_num_tramite
                   FROM wf.f_registra_proceso_disparado_wf(
                                p_id_usuario,
                                v_parametros._id_usuario_ai,
                                v_parametros._nombre_usuario_ai,
                                v_rec_cbte_fk.id_estado_wf,
                                NULL,  --id_funcionario wf
                                v_parametros.id_depto,
                                'Registro Manual de Cbte Relacionado',
                                'CBTE', --dispara proceso del comprobante
                                '');

            ELSE
                    --  inicia tramite nuevo
                    v_codigo_proceso_macro = pxp.f_get_variable_global('conta_codigo_macro_wf_cbte');
                    --obtener id del proceso macro
                    select
                     pm.id_proceso_macro
                    into
                     v_id_proceso_macro
                    from wf.tproceso_macro pm
                    where pm.codigo = v_codigo_proceso_macro;

                    If v_id_proceso_macro is NULL THEN
                      raise exception 'El proceso macro  de codigo % no esta configurado en el sistema WF',v_codigo_proceso_macro;
                    END IF;

                   --   obtener el codigo del tipo_proceso
                    select   tp.codigo
                     into v_codigo_tipo_proceso
                    from  wf.ttipo_proceso tp
                    where   tp.id_proceso_macro = v_id_proceso_macro
                          and tp.estado_reg = 'activo' and tp.inicio = 'si';

                    IF v_codigo_tipo_proceso is NULL THEN
                     raise exception 'No existe un proceso inicial para el proceso macro indicado % (Revise la configuración)',v_codigo_proceso_macro;
                    END IF;

                  -- inciar el tramite en el sistema de WF
                    SELECT
                       ps_num_tramite ,
                       ps_id_proceso_wf ,
                       ps_id_estado_wf ,
                       ps_codigo_estado
                      into
                       v_num_tramite,
                       v_id_proceso_wf,
                       v_id_estado_wf,
                       v_codigo_estado

                    FROM wf.f_inicia_tramite(
                       p_id_usuario,
                       v_parametros._id_usuario_ai,
                       v_parametros._nombre_usuario_ai,
                       v_rec.po_id_gestion,
                       v_codigo_tipo_proceso,
                       null,--v_parametros.id_funcionario,
                       v_parametros.id_depto,
                       'Registro de Cbte manual',
                       '' );


                    IF  v_codigo_estado != 'borrador' THEN
                      raise exception 'el estado inicial para cbtes debe ser borrador, revise la configuración del WF';
                    END IF;

            END IF;

             -------------------------------------------------
            --  validar fechas de costos de inicio o fin
            --  a solicitud del area de cosots la fecha incial de costo no puede ser de una gestion menor a la gestion
            --  de la fecha de comprobante
            --  RAC 29/08/2017
            -----------------------------------------------

 			--valida fechas de costos

            IF v_parametros.fecha_costo_fin <  v_parametros.fecha_costo_ini THEN
               raise exception 'LA FECHA FINAL NO PUEDE SER MENOR A LA FECHA INICIAL';
            END IF;

           /*--validador de gestion
			v_anio_gestion = ( select date_part('year',now()))::INTEGER;

			IF NOT ((date_part('year',v_parametros.fecha_costo_ini) = v_anio_gestion) and (date_part('year',v_parametros.fecha_costo_fin)=v_anio_gestion)) THEN
               raise exception 'LAS FECHAS NO CORRESPONDEN A LA GESTION ACTUAL';
            END IF;
			*/

             --control de fechas inicio y fin
            select date_part('year',com.fecha)
            into v_anio_com
            from conta.tint_comprobante com
            where com.id_int_comprobante = v_id_int_comprobante;
           --raise exception '%, %, %',v_parametros.fecha, v_parametros.fecha_costo_ini, v_parametros.fecha_costo_fin;

            IF NOT ((date_part('year',v_parametros.fecha_costo_ini) = v_anio_com) and (date_part('year',v_parametros.fecha_costo_fin)=v_anio_com)) THEN
               raise exception 'LAS FECHAS NO CORRESPONDEN A LA GESTIÓN, TIENE COMO FECHA %',v_parametros.fecha;
            END IF;

            select
              per.id_gestion
            into
              v_id_gestion_cbte
            from param.tperiodo per
            where  v_parametros.fecha BETWEEN per.fecha_ini and per.fecha_fin;

            select
              per.id_gestion
            into
              v_id_gestion_cos
            from param.tperiodo per
            where  v_parametros.fecha_costo_ini BETWEEN per.fecha_ini and per.fecha_fin;

            IF v_id_gestion_cos is not null THEN
               IF v_id_gestion_cos  <  v_id_gestion_cbte  THEN
                 raise exception 'La fecha del costo inicial debe ser de la misma gestión que el Cbte ';
               END IF;
            END IF;


        	-----------------------------
        	--REGISTRO DEL COMPROBANTE
        	-----------------------------
			if (v_parametros.cbte_cierre <> 'no' and v_parametros.cbte_cierre <> 'si') then
            	raise exception 'Error de datos en el campo Cierre, los datos ingresados deben ser si o no';
            elseif (v_parametros.cbte_apertura <> 'no' and v_parametros.cbte_apertura <> 'si' ) then
            	raise exception 'Error de datos en el campo Apertura, los datos ingresados deben ser si o no';
            elseif (v_parametros.cbte_aitb <> 'no' and v_parametros.cbte_aitb <> 'si' ) then
            	raise exception 'Error de datos en el campo AITBs, los datos ingresados deben ser si o no';
            else

          --begin (franklin.espinoza) 08/10/2020
          if v_parametros.id_depto IN (49,50, 79, 80, 81) then
            v_localidad = 'internacional';
          else
            v_localidad = 'nacional';
          end if;
          --end (franklin.espinoza) 08/10/2020

          --begin franklin.espinoza 09/10/2020
            IF  pxp.f_existe_parametro(p_tabla , 'id_depto_libro')  THEN
               v_id_dl = v_parametros.id_depto_libro;
            END IF;

            IF  pxp.f_existe_parametro(p_tabla , 'id_cuenta_bancaria')  THEN
               v_id_cb = v_parametros.id_cuenta_bancaria;
            END IF;
            --end franklin.espinoza 09/10/2020
          --if v_id_dl is null and v_id_cb is null then
          if v_parametros.id_depto NOT IN (49, 79, 80, 81) then
            insert into conta.tint_comprobante(
                  id_clase_comprobante,
                  id_subsistema,
                  id_depto,
                  id_moneda,
                  id_periodo,
                  id_funcionario_firma1,
                  id_funcionario_firma2,
                  id_funcionario_firma3,
                  tipo_cambio,
                  beneficiario,
                  estado_reg,
                  glosa1,
                  fecha,
                  glosa2,
                  --momento,
                  id_usuario_reg,
                  fecha_reg,
                  id_usuario_mod,
                  fecha_mod,
                  id_usuario_ai,
                  usuario_ai,
                  id_int_comprobante_fks,
                  cbte_cierre,
                  cbte_apertura,
                  cbte_aitb,
                  manual,
                  momento_comprometido,
                  momento_ejecutado,
                  momento_pagado,
                  momento,
                  id_tipo_relacion_comprobante,
                  fecha_costo_ini,
                  fecha_costo_fin,
                  id_config_cambiaria,
                  tipo_cambio_2,
                  localidad,
                  id_moneda_tri,
                  nro_tramite,
                  id_proceso_wf,
                  id_estado_wf,
                  forma_cambio,
                  id_moneda_act,
                  tipo_cambio_3,
                  tipo_cbte
              ) values(
                  v_parametros.id_clase_comprobante,
                  v_id_subsistema,
                  v_parametros.id_depto,
                  v_parametros.id_moneda,
                  v_rec.po_id_periodo,
                  v_parametros.id_funcionario_firma1,
                  v_parametros.id_funcionario_firma2,
                  v_parametros.id_funcionario_firma3,
                  v_parametros.tipo_cambio,
                  v_parametros.beneficiario,
                  'borrador',  --  v_codigo_estado
                  v_parametros.glosa1,
                  v_parametros.fecha,
                  v_parametros.glosa2,
                  --v_parametros.momento,
                  p_id_usuario,
                  now(),
                  null,
                  null,
                  v_parametros._id_usuario_ai,
                  v_parametros._nombre_usuario_ai,
                  va_id_int_cbte_fk,
                  v_parametros.cbte_cierre,
                  v_parametros.cbte_apertura,
                  v_parametros.cbte_aitb,
                  'si',
                  v_momento_comprometido,
                  v_momento_ejecutado,
                  v_momento_pagado,
                  v_tipo_comprobante,
                  v_parametros.id_tipo_relacion_comprobante,
                  v_parametros.fecha_costo_ini,
                  v_parametros.fecha_costo_fin,
                  v_parametros.id_config_cambiaria,
                  v_parametros.tipo_cambio_2,
                  'nacional', --(franklin.espinoza) 08/10/2020
                  v_id_moneda_tri,
                  v_num_tramite,
                  v_id_proceso_wf,
                  v_id_estado_wf,
                  v_parametros.forma_cambio,
                  v_id_moneda_act,
                  v_parametros.tipo_cambio_3,
                  v_localidad

        )RETURNING id_int_comprobante into v_id_int_comprobante;
      else
      		v_reversion = 'no';

      		--franklin.espinoza 06/11/2020 bandera que indica si un comprobante es de reversion
            IF  pxp.f_existe_parametro(p_tabla , 'reversion')  THEN
              IF v_parametros.reversion = 'true' THEN
               v_reversion = 'si';
              END IF;
            END IF;

            IF  pxp.f_existe_parametro(p_tabla , 'id_depto_libro')  THEN
            	v_id_depto_libro = v_parametros.id_depto_libro;
            END IF;
            IF  pxp.f_existe_parametro(p_tabla , 'id_cuenta_bancaria')  THEN
    			v_id_cuenta_bancaria = v_parametros.id_cuenta_bancaria;
            END IF;

            	insert into conta.tint_comprobante(
                  id_clase_comprobante,
                  id_subsistema,
                  id_depto,
                  id_moneda,
                  id_periodo,
                  id_funcionario_firma1,
                  id_funcionario_firma2,
                  id_funcionario_firma3,
                  tipo_cambio,
                  beneficiario,
                  estado_reg,
                  glosa1,
                  fecha,
                  glosa2,
                  --momento,
                  id_usuario_reg,
                  fecha_reg,
                  id_usuario_mod,
                  fecha_mod,
                  id_usuario_ai,
                  usuario_ai,
                  id_int_comprobante_fks,
                  cbte_cierre,
                  cbte_apertura,
                  cbte_aitb,
                  manual,
                  momento_comprometido,
                  momento_ejecutado,
                  momento_pagado,
                  momento,
                  id_tipo_relacion_comprobante,
                  fecha_costo_ini,
                  fecha_costo_fin,
                  id_config_cambiaria,
                  tipo_cambio_2,
                  localidad,
                  id_moneda_tri,
                  nro_tramite,
                  id_proceso_wf,
                  id_estado_wf,
                  forma_cambio,
                  id_moneda_act,
                  tipo_cambio_3,
                  id_cuenta_bancaria,
                  id_depto_libro,
                  tipo_cbte,
                  reversion
          		) values(
                  v_parametros.id_clase_comprobante,
                  v_id_subsistema,
                  v_parametros.id_depto,
                  v_parametros.id_moneda,
                  v_rec.po_id_periodo,
                  v_parametros.id_funcionario_firma1,
                  v_parametros.id_funcionario_firma2,
                  v_parametros.id_funcionario_firma3,
                  v_parametros.tipo_cambio,
                  v_parametros.beneficiario,
                  'borrador',  --  v_codigo_estado
                  v_parametros.glosa1,
                  v_parametros.fecha,
                  v_parametros.glosa2,
                  --v_parametros.momento,
                  p_id_usuario,
                  now(),
                  null,
                  null,
                  v_parametros._id_usuario_ai,
                  v_parametros._nombre_usuario_ai,
                  va_id_int_cbte_fk,
                  v_parametros.cbte_cierre,
                  v_parametros.cbte_apertura,
                  v_parametros.cbte_aitb,
                  'si',
                  v_momento_comprometido,
                  v_momento_ejecutado,
                  v_momento_pagado,
                  v_tipo_comprobante,
                  v_parametros.id_tipo_relacion_comprobante,
                  v_parametros.fecha_costo_ini,
                  v_parametros.fecha_costo_fin,
                  v_parametros.id_config_cambiaria,
                  v_parametros.tipo_cambio_2,
                  'nacional', --(franklin.espinoza) 08/10/2020
                  v_id_moneda_tri,
                  v_num_tramite,
                  v_id_proceso_wf,
                  v_id_estado_wf,
                  v_parametros.forma_cambio,
                  v_id_moneda_act,
                  v_parametros.tipo_cambio_3,
                  v_id_cuenta_bancaria,
                  v_id_depto_libro,
                  v_localidad,
                  v_reversion

				)RETURNING id_int_comprobante into v_id_int_comprobante;
      end if;

		end if;
            update wf.tproceso_wf p set
              descripcion = descripcion||' ('||v_clcbt_desc||'id:'||v_id_int_comprobante::varchar||')'
            where p.id_proceso_wf = v_id_proceso_wf;


			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Comprobante almacenado(a) con exito (id_int_comprobante'||v_id_int_comprobante||')');
            v_resp = pxp.f_agrega_clave(v_resp,'id_int_comprobante',v_id_int_comprobante::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'CONTA_INCBTE_MOD'
 	#DESCRIPCION:	Modificacion de registros
 	#AUTOR:		admin
 	#FECHA:		29-08-2013 00:28:30
	***********************************/

	elsif(p_transaccion='CONTA_INCBTE_MOD')then

		begin

			------------------
        	-- VALIDACIONES
        	------------------
            select
              id_subsistema
              into
             v_id_subsistema_conta
            from segu.tsubsistema
            where codigo = 'CONTA';


            select
              *
            into
             v_reg_cbte
            from conta.tint_comprobante ic where ic.id_int_comprobante = v_parametros.id_int_comprobante;

            IF v_reg_cbte.estado_reg not in ('borrador','elaborado') THEN
               raise exception 'solo puede editar comprobantes en borrador';
            END IF;

            IF v_reg_cbte.volcado = 'si' THEN
               raise exception 'no puede editar comprobantes volcados';
            END IF;

            --SUBSISTEMA: Obtiene el id_subsistema del Sistema de Contabilidad si es que no llega como parámetro


            --RAC, 15/08/2017
            --  este cambio trabjo problema al editar comprobante de planillas (integrascion con libro de bancos, lo cbte de planillas no generan cheques,  OJO ANALIZAR)
            --  parece logico no cambiar el sistema donde se origina el cbte
            -- sin embargo no me acerudo por que se hizo, probablemente es por alguna validacion que solo se aplica al sistema contable

             IF v_reg_cbte.id_subsistema is not NULL THEN
               	     v_id_subsistema = v_reg_cbte.id_subsistema;
             else
                    v_id_subsistema = v_id_subsistema_conta;
             end if;


        	--PERIODO
        	--Obtiene el periodo a partir de la fecha
        	v_rec = param.f_get_periodo_gestion(v_parametros.fecha);

            --segun la clase del comprobante definir si es presupeustario o contable
            select
              cc.tipo_comprobante
            into
              v_tipo_comprobante
            from conta.tclase_comprobante cc
            where cc.id_clase_comprobante = v_parametros.id_clase_comprobante;
            /*
            --revisa momentos presupeustario
            v_momento_comprometido = v_reg_cbte.momento_comprometido;
            v_momento_ejecutado = v_reg_cbte.momento_ejecutado;
            v_momento_pagado =  v_reg_cbte.momento_pagado;*/

            --momentos presupeustarios

              IF v_parametros.momento_comprometido = 'true' THEN
                 v_momento_comprometido = 'si';
              ELSE
                 v_momento_comprometido = 'no';
              END IF;


              IF v_parametros.momento_ejecutado = 'true' THEN
                 v_momento_ejecutado = 'si';
              ELSE
                 v_momento_ejecutado = 'no';
              END IF;

              IF v_parametros.momento_pagado = 'true' THEN
                 v_momento_pagado = 'si';
              ELSE
                 v_momento_pagado = 'no';
              END IF;

            --RAC 29/12/2016 ...  dejamos modificar momentos si el cbte es editable
            IF  v_reg_cbte.manual != 'si'   THEN


                 IF v_reg_cbte.sw_editable != 'si' THEN
                   -- RAC 29/12/2016
                   -- si es un cbte editable solo puede modificar el momento comprometido
                   -- para los casos devengados de fin de gestion
                   -- dond esea necesario comprometer en el presupeusto de la sigueinte gestion

                 		IF v_momento_comprometido != v_reg_cbte.momento_comprometido THEN
                  			 raise exception 'No puede cambiar el momento comprometido en cbtes automaticos';
               			END IF;
                 END IF;

               IF v_momento_ejecutado != v_reg_cbte.momento_ejecutado  or  v_momento_pagado != v_reg_cbte.momento_pagado THEN
                   raise exception 'No puede cambiar los momentos en cbte automaticos';
               END IF;

                IF v_parametros.id_clase_comprobante != v_reg_cbte.id_clase_comprobante   THEN
                   raise exception 'No puede cambiar el tipo de cbte automaticos';
                END IF;

            END IF;

            --  el tipo de cambio puede variar solo si sw_tipo_cambio = 'no' ...
            IF  v_reg_cbte.sw_tipo_cambio = 'si' THEN

              v_tc_1 = v_reg_cbte.tipo_cambio;
              v_tc_2 = v_reg_cbte.tipo_cambio_2;
              v_tc_3 = v_reg_cbte.tipo_cambio_3;



            ELSE

              IF v_parametros.tipo_cambio is  NULL or v_parametros.tipo_cambio_2 is  NULL  or v_parametros.tipo_cambio_3 is  NULL THEN
                raise exception 'No se definieron los tipos de cambio para cbte';
              END IF;

              v_tc_1 = v_parametros.tipo_cambio;
              v_tc_2 = v_parametros.tipo_cambio_2;
              v_tc_3 = v_parametros.tipo_cambio_3;


            END IF;

            --valida fechas de costos

            IF v_parametros.fecha_costo_fin <  v_parametros.fecha_costo_ini THEN
               raise exception 'LA FECHA FINAL NO PUEDE SER MENOR A LA FECHA INICIAL';
            END IF;

		/*	--validador de gestion
			v_anio_gestion = ( select date_part('year',now()))::INTEGER;

			IF NOT ((date_part('year',v_parametros.fecha_costo_ini) = v_anio_gestion) and (date_part('year',v_parametros.fecha_costo_fin)=v_anio_gestion)) THEN
               raise exception 'LAS FECHAS NO CORRESPONDEN A LA GESTION ACTUAL';
            END IF;
           */


            -------------------------------------------------
            --  validar fechas de costos de inicio o fin
            --  a solicitud del area de cosots la fecha incial de costo no puede ser de una gestion menor a la gestion
            --  de la fecha de comprobante
            --  RAC 29/08/2017
            -----------------------------------------------

            select
              per.id_gestion
            into
              v_id_gestion_cbte
            from param.tperiodo per
            where  v_parametros.fecha BETWEEN per.fecha_ini and per.fecha_fin;

            select
              per.id_gestion
            into
              v_id_gestion_cos
            from param.tperiodo per
            where  v_parametros.fecha_costo_ini BETWEEN per.fecha_ini and per.fecha_fin;

			select
              per.id_gestion
            into
              v_id_gestion_cosfin
            from param.tperiodo per
            where  v_parametros.fecha_costo_fin BETWEEN per.fecha_ini and per.fecha_fin;

         /*   IF v_id_gestion_cos is not null THEN
               IF v_id_gestion_cos  <  v_id_gestion_cbte  THEN
                 raise exception 'La fecha del costo inicial debe ser de la misma gestión que el Cbte ';
               END IF;
            END IF;
        */
        IF v_id_gestion_cos is not null THEN
               IF (v_id_gestion_cos  <  v_id_gestion_cbte) OR (v_id_gestion_cosfin  >  v_id_gestion_cbte) THEN
                 raise exception 'LAS FECHAS NO CORRESPONDEN A LA GESTION DEL CBTE';
               END IF;
            END IF;

			------------------------------
			--Sentencia de la modificacion
			------------------------------
			--begin franklin.espinoza 27/09/2020
        IF  pxp.f_existe_parametro(p_tabla , 'id_depto_libro')  THEN
          v_id_dl = v_parametros.id_depto_libro;
        END IF;

        IF  pxp.f_existe_parametro(p_tabla , 'id_cuenta_bancaria')  THEN
          v_id_cb = v_parametros.id_cuenta_bancaria;
        END IF;
      --end franklin.espinoza 27/09/2020
      if v_id_dl is null and v_id_cb is null then
        update conta.tint_comprobante set
                  id_clase_comprobante = v_parametros.id_clase_comprobante,
                  id_tipo_relacion_comprobante = v_parametros.id_tipo_relacion_comprobante,
                  momento = v_tipo_comprobante,
                  id_int_comprobante_fks =  (string_to_array(v_parametros.id_int_comprobante_fks,','))::INTEGER[],
                  id_subsistema = v_id_subsistema,
                  id_depto = v_parametros.id_depto,
                  id_moneda = v_parametros.id_moneda,
                  id_periodo = v_rec.po_id_periodo,
                  id_funcionario_firma1 = v_parametros.id_funcionario_firma1,
                  id_funcionario_firma2 = v_parametros.id_funcionario_firma2,
                  id_funcionario_firma3 = v_parametros.id_funcionario_firma3,
                  tipo_cambio = v_tc_1,
                  beneficiario = v_parametros.beneficiario,

                  glosa1 = v_parametros.glosa1,
                  fecha = v_parametros.fecha,
                  glosa2 = v_parametros.glosa2,

                  -- momento = v_parametros.momento,
                  id_usuario_mod = p_id_usuario,
                  fecha_mod = now(),
                  id_usuario_ai = v_parametros._id_usuario_ai,
                  usuario_ai = v_parametros._nombre_usuario_ai,
                  cbte_cierre = v_parametros.cbte_cierre,
                  cbte_apertura = v_parametros.cbte_apertura,
                  momento_comprometido = v_momento_comprometido,
                  momento_ejecutado = v_momento_ejecutado,
                  momento_pagado =  v_momento_pagado,
                  fecha_costo_ini = v_parametros.fecha_costo_ini,
                  fecha_costo_fin = v_parametros.fecha_costo_fin,
                  tipo_cambio_2 = v_tc_2,
                  tipo_cambio_3 = v_tc_3,
                  forma_cambio = v_parametros.forma_cambio
        where id_int_comprobante = v_parametros.id_int_comprobante;

      else
      	IF v_parametros.reversion = 'true' THEN
           v_reversion = 'si';
        ELSE
           v_reversion = 'no';
        END IF;
            	update conta.tint_comprobante set
                  id_clase_comprobante = v_parametros.id_clase_comprobante,
                  id_tipo_relacion_comprobante = v_parametros.id_tipo_relacion_comprobante,
                  momento = v_tipo_comprobante,
                  id_int_comprobante_fks =  (string_to_array(v_parametros.id_int_comprobante_fks,','))::INTEGER[],
                  id_subsistema = v_id_subsistema,
                  id_depto = v_parametros.id_depto,
                  id_moneda = v_parametros.id_moneda,
                  id_periodo = v_rec.po_id_periodo,
                  id_funcionario_firma1 = v_parametros.id_funcionario_firma1,
                  id_funcionario_firma2 = v_parametros.id_funcionario_firma2,
                  id_funcionario_firma3 = v_parametros.id_funcionario_firma3,
                  tipo_cambio = v_tc_1,
                  beneficiario = v_parametros.beneficiario,
                  glosa1 = v_parametros.glosa1,
                  fecha = v_parametros.fecha,
                  glosa2 = v_parametros.glosa2,

                  -- momento = v_parametros.momento,
                  id_usuario_mod = p_id_usuario,
                  fecha_mod = now(),
                  id_usuario_ai = v_parametros._id_usuario_ai,
                  usuario_ai = v_parametros._nombre_usuario_ai,
                  cbte_cierre = v_parametros.cbte_cierre,
                  cbte_apertura = v_parametros.cbte_apertura,
                  momento_comprometido = v_momento_comprometido,
                  momento_ejecutado = v_momento_ejecutado,
                  momento_pagado =  v_momento_pagado,
                  fecha_costo_ini = v_parametros.fecha_costo_ini,
                  fecha_costo_fin = v_parametros.fecha_costo_fin,
                  tipo_cambio_2 = v_tc_2,
                  tipo_cambio_3 = v_tc_3,
                  forma_cambio = v_parametros.forma_cambio,
                  --franklin.espinoza 27/09/2020
                  id_cuenta_bancaria = v_parametros.id_cuenta_bancaria,
                  id_depto_libro = v_parametros.id_depto_libro,
                  reversion = v_reversion

              where id_int_comprobante = v_parametros.id_int_comprobante;
      end if;



            -- si el tipo de cambio varia es encesario recalcular las equivalenscias en todas las transacciones
            IF    v_parametros.tipo_cambio != v_reg_cbte.tipo_cambio
               or v_parametros.tipo_cambio_2 != v_reg_cbte.tipo_cambio_2
               or v_parametros.tipo_cambio_3 != v_reg_cbte.tipo_cambio_3 THEN

              IF  not conta.f_int_trans_recalcular_tc(v_parametros.id_int_comprobante) THEN
                raise exception 'Error al reprocesar el tipo de cambio';
              END IF;

            END IF;

            -- procesar las trasaaciones (con diversos propositos, ejm validar  cuentas bancarias)


            IF not conta.f_int_trans_procesar(v_parametros.id_int_comprobante) THEN
              raise exception 'Error al procesar transacciones';
            END IF;

            -- si la fecha varia revisar si es necesario cambiar de gestion

            IF v_parametros.fecha != v_reg_cbte.fecha THEN

               --revisamos si son de diferente gestión
               SELECT * into v_ges_1 FROM param.f_get_limites_gestion(v_reg_cbte.fecha);
               SELECT * into v_ges_2 FROM param.f_get_limites_gestion(v_parametros.fecha);

               --sin son diferentes gestiones
               IF v_ges_1.po_id_gestion  != v_ges_2.po_id_gestion THEN

                  IF not  conta.f_act_gestion_transaccion(
                          v_parametros.id_int_comprobante,
                          v_ges_2.po_id_gestion,
                          v_ges_1.po_id_gestion) THEN

                            raise exception 'error al actualizar gestion';
                   END IF;

               END IF;


            END IF;



			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Comprobante modificado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_int_comprobante',v_parametros.id_int_comprobante::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'CONTA_INCBTE_ELI'
 	#DESCRIPCION:	Eliminacion de registros
 	#AUTOR:		admin
 	#FECHA:		29-08-2013 00:28:30
	***********************************/

	elsif(p_transaccion='CONTA_INCBTE_ELI')then

		begin

          --17-03-2021 (may) se comenta el control , porque ya no sera necesario eliminar la relacion del cbte al eliminarlo , porque tambien tiene relacion con el plan de pago
          --si no tiene plan de pago no se realiza la eliminacion

           /*IF EXISTS(select 1
                      from conta.tdoc_compra_venta dcv
                      where dcv.estado_reg = 'activo' and  dcv.id_int_comprobante =  v_parametros.id_int_comprobante) then

                      raise exception 'No puede Eliminar el Comprobante  %, primero Elimine sus Documentos/Facturas registrados.',v_parametros.id_int_comprobante;
           END IF;*/


            v_result = conta.f_eliminar_int_comprobante(p_id_usuario,
                                                        v_parametros._id_usuario_ai,
                                                        v_parametros._nombre_usuario_ai,
                                                        v_parametros.id_int_comprobante,
                                                        'si');  --si indica borrado manualmente



            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje',v_result);
            v_resp = pxp.f_agrega_clave(v_resp,'id_int_comprobante',v_parametros.id_int_comprobante::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'CONTA_INCBTE_VAL'
 	#DESCRIPCION:	Validación del comprobante
 	#AUTOR:			rcm
 	#FECHA:			05/09/2013
	***********************************/

	elsif(p_transaccion='CONTA_INCBTE_VAL')then

		begin

            --validaciones
            select * into v_reg_cbte
            from conta.tint_comprobante ic where ic.id_int_comprobante = v_parametros.id_int_comprobante;

            IF v_reg_cbte.estado_reg != 'borrador' THEN
               raise exception 'solo puede validar  comprobantes en borrador';
            END IF;


			--Lamada a la función de validación
			v_result = conta.f_validar_cbte( p_id_usuario,
                                             v_parametros._id_usuario_ai,
                                             v_parametros._nombre_usuario_ai,
                                             v_parametros.id_int_comprobante,
                                             v_parametros.igualar);

            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje',v_result);
            v_resp = pxp.f_agrega_clave(v_resp,'id_int_comprobante',v_parametros.id_int_comprobante::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'CONTA_IGUACBTE_IME'
 	#DESCRIPCION:	Igual el cbte por diferencias de tipo de cambio o redondeo
 	#AUTOR:		admin
 	#FECHA:		29-08-2013 00:28:30
	***********************************/

	elsif(p_transaccion='CONTA_IGUACBTE_IME')then

		begin

             IF not conta.f_igualar_cbte(v_parametros.id_int_comprobante, p_id_usuario) THEN
               raise exception 'error al igualar';
             END IF;

            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','validar e igualar cbte');
            v_resp = pxp.f_agrega_clave(v_resp,'id_int_comprobante',v_parametros.id_int_comprobante::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

   /*********************************
 	#TRANSACCION:  'CONTA_SWEDIT_IME'
 	#DESCRIPCION:	Cambia el cbte a modo de edición
 	#AUTOR:		admin
 	#FECHA:		29-08-2013 00:28:30
	***********************************/

	elsif(p_transaccion='CONTA_SWEDIT_IME')then

		begin

          select
            *
          into
           v_reg_cbte
          from conta.tint_comprobante set
          where id_int_comprobante = v_parametros.id_int_comprobante;


          IF  v_reg_cbte.estado_reg != 'borrador'  THEN
             raise exception 'El cbte debe estar en borrador para habilitar la edición';
          END IF;

          IF  v_reg_cbte.sw_editable = 'si'  THEN
             raise exception 'La edición ya se encuentra habilitada ....';
          END IF;


          --obtenemos un backup del cbte
          v_id_int_comprobante_bk = conta.f_backup_int_comprobante(v_parametros.id_int_comprobante);


          --modificamos la bandera para habilitar la edicion
          update conta.tint_comprobante set
               sw_editable = 'si',
               id_usuario_mod = p_id_usuario,
               fecha_mod = now(),
               id_int_comprobante_bk = v_id_int_comprobante_bk
          where id_int_comprobante = v_parametros.id_int_comprobante;




            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','cbte habilitado para edición');
            v_resp = pxp.f_agrega_clave(v_resp,'id_int_comprobante',v_parametros.id_int_comprobante::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

    /*********************************
 	#TRANSACCION:  'CONTA_VOLCARCBTE_IME'
 	#DESCRIPCION:	Volcar comprobante
 	#AUTOR:		admin
 	#FECHA:		29-08-2013 00:28:30
	***********************************/

	elsif(p_transaccion='CONTA_VOLCARCBTE_IME')then

		begin


            select
             *
            into
             v_reg_cbte
            from conta.tint_comprobante ic
            where ic.id_int_comprobante = v_parametros.id_int_comprobante;




            IF  v_reg_cbte.estado_reg != 'validado'  THEN
               raise exception 'solo pueden volcar comprobantes validados';
            END IF;

            IF  v_reg_cbte.volcado = 'si'  THEN
               raise exception 'El comprobante ya se encuentra volcado';
            END IF;

            IF  v_reg_cbte.cbte_reversion = 'si'  THEN
               --comentado a solicitud de lobito el 01/08/2019
               	--raise exception 'No puede volcar un cbte de reversión';
            END IF;

            -- RAC 2/12/2016
            -- solo revisa dependnecia en cbte de reversion total
            -- los parciales peuden tener dependencias
           /* IF v_parametros.sw_validar = 'si' then
              IF  not conta.f_revisar_dependencias(v_parametros.id_int_comprobante)  THEN
                 raise exception 'error por dependencias';
              END IF;
        	END IF;
            */


            --validar que el periodo se encuentre abierto
            IF not param.f_periodo_subsistema_abierto(v_reg_cbte.fecha::date, 'CONTA') THEN
                raise exception 'El periodo se encuentra cerrado en contabilidad para la fecha:  %',v_reg_cbte.fecha;
            END IF;




            select
               rc.id_tipo_relacion_comprobante
            into
              v_id_tipo_relacion_comprobante
            from conta.ttipo_relacion_comprobante rc
            where rc.codigo = 'REVERSION';

            -- insertar comprobante volcado, haciendo referencia al cbte ajustado



            ----------------------------------------
            -- registrar proceso disparado de WF
            ----------------------------------------
            SELECT
                    ps_id_proceso_wf,ps_id_estado_wf, ps_codigo_estado, ps_nro_tramite
             into
                    v_id_proceso_wf,v_id_estado_wf,v_codigo_estado, v_num_tramite
            FROM wf.f_registra_proceso_disparado_wf(
                          p_id_usuario,
                          v_parametros._id_usuario_ai,
                          v_parametros._nombre_usuario_ai,
                          v_reg_cbte.id_estado_wf,
                          NULL,  --id_funcionario wf
                          v_reg_cbte.id_depto,
                          'Cbte de Volcado (Anula el original)',
                          'CBTE', --sipara comprobante
                          '');

            select
              cc.tipo_comprobante,
              cc.descripcion
            into
              v_tipo_comprobante,
              v_clcbt_desc
            from conta.tclase_comprobante cc
            where cc.id_clase_comprobante = v_reg_cbte.id_clase_comprobante;


                  --23-01-2020 (may)
                  --control de fecha de la insercion del comprobante

                  v_periodo_fecha_cbte = date_part('month',v_reg_cbte.fecha);
                  v_periodo_anio_cbte = date_part('year',v_reg_cbte.fecha);


                  SELECT ges.id_gestion
                  INTO v_gestion_cbte
                  FROM param.tgestion ges
                  WHERE ges.gestion = v_periodo_anio_cbte;

                  SELECT per.fecha_ini, per.fecha_fin
                  INTO v_fecha_ini,v_fecha_fin
                  FROM param.tperiodo per
                  WHERE per.periodo = v_periodo_fecha_cbte
                  and per.id_gestion = v_gestion_cbte;

                  v_periodo_mes_now = date_part('month',now());
                  v_periodo_anio_now = date_part('year',now());
			--raise exception 'lleam2 % = % - %= %',v_periodo_fecha_cbte, v_periodo_mes_now,  v_periodo_anio_cbte,v_periodo_anio_now ;
                  IF (v_periodo_fecha_cbte != v_periodo_mes_now and v_periodo_anio_cbte != v_periodo_anio_now ) THEN

                  		IF (v_reg_cbte.fecha < now()::date and v_reg_cbte.fecha > v_fecha_ini and (v_reg_cbte.fecha < v_fecha_fin or v_reg_cbte.fecha = v_fecha_fin)) THEN

                              v_fecha  = v_fecha_fin;
                        ELSIF (v_reg_cbte.fecha < now()::date and v_reg_cbte.fecha> now()::date and v_reg_cbte.fecha > v_fecha_ini and (v_reg_cbte.fecha < v_fecha_fin or v_reg_cbte.fecha = v_fecha_fin)) THEN

                              v_fecha = now()::date;

                        ELSIF (v_reg_cbte.fecha = now()::date and v_reg_cbte.fecha > v_fecha_ini and (v_reg_cbte.fecha < v_fecha_fin or v_reg_cbte.fecha = v_fecha_fin)) THEN

                              v_fecha = now()::date;

                        ELSIF (v_reg_cbte.fecha > now()::date and v_reg_cbte.fecha > v_fecha_ini and (v_reg_cbte.fecha < v_fecha_fin or v_reg_cbte.fecha = v_fecha_fin)) THEN

                              v_fecha = now()::date;

                        ELSE
                              raise exception 'Verificar fecha del comprobante cuando este es de distinto mes y gestión.';

                        END IF;

                ELSIF (v_periodo_fecha_cbte = v_periodo_mes_now and v_periodo_anio_cbte != v_periodo_anio_now ) THEN

                  		IF (v_reg_cbte.fecha < now()::date and v_reg_cbte.fecha > v_fecha_ini and (v_reg_cbte.fecha < v_fecha_fin or v_reg_cbte.fecha = v_fecha_fin)) THEN

                              v_fecha  = v_fecha_fin;
                        ELSIF (v_reg_cbte.fecha < now()::date and v_reg_cbte.fecha> now()::date and v_reg_cbte.fecha > v_fecha_ini and (v_reg_cbte.fecha < v_fecha_fin or v_reg_cbte.fecha = v_fecha_fin)) THEN

                              v_fecha = now()::date;

                        ELSIF (v_reg_cbte.fecha = now()::date and v_reg_cbte.fecha > v_fecha_ini and (v_reg_cbte.fecha < v_fecha_fin or v_reg_cbte.fecha = v_fecha_fin)) THEN

                              v_fecha = now()::date;

                        ELSIF (v_reg_cbte.fecha > now()::date and v_reg_cbte.fecha > v_fecha_ini and (v_reg_cbte.fecha < v_fecha_fin or v_reg_cbte.fecha = v_fecha_fin)) THEN

                              v_fecha = now()::date;

                        ELSE
                              raise exception 'Verficar la fecha del comprobante cuando este es de distinta gestion y mismo mes.';

                        END IF;

                  ELSE

                  		IF (v_reg_cbte.fecha < now()::date and v_reg_cbte.fecha > v_fecha_ini and (v_reg_cbte.fecha < v_fecha_fin or v_reg_cbte.fecha = v_fecha_fin)) THEN

                              v_fecha  = now();
                        ELSIF (v_reg_cbte.fecha < now()::date and v_reg_cbte.fecha> now()::date and v_reg_cbte.fecha > v_fecha_ini and (v_reg_cbte.fecha < v_fecha_fin or v_reg_cbte.fecha = v_fecha_fin)) THEN

                              v_fecha = now()::date;

                        ELSIF (v_reg_cbte.fecha = now()::date and v_reg_cbte.fecha > v_fecha_ini and (v_reg_cbte.fecha < v_fecha_fin or v_reg_cbte.fecha = v_fecha_fin)) THEN

                              v_fecha = now()::date;

                        ELSIF (v_reg_cbte.fecha > now()::date and v_reg_cbte.fecha > v_fecha_ini and (v_reg_cbte.fecha < v_fecha_fin or v_reg_cbte.fecha = v_fecha_fin)) THEN

                              v_fecha = now()::date;

                        ELSE
                              v_fecha = now()::date;

                        END IF;
                  END IF;

                   --raise exception 'llega % < % and % > % and %<%',v_reg_cbte.fecha,  now()::date, v_reg_cbte.fecha, v_fecha_ini,v_reg_cbte.fecha,v_fecha_fin ;

                  --



            -----------------------------
        	--REGISTRO DEL COMPROBANTE
        	-----------------------------
        	insert into conta.tint_comprobante(
                id_clase_comprobante,
                id_subsistema,
                id_depto,
                id_moneda,
                id_periodo,
                id_funcionario_firma1,
                id_funcionario_firma2,
                id_funcionario_firma3,
                tipo_cambio,
                beneficiario,
                estado_reg,
                glosa1,
                fecha,
                glosa2,
                --momento,
                id_usuario_reg,
                fecha_reg,
                id_usuario_mod,
                fecha_mod,
                id_usuario_ai,
                usuario_ai,
                id_int_comprobante_fks,
                cbte_cierre,
                cbte_apertura,
                cbte_aitb,
                manual,
                momento_comprometido,
                momento_ejecutado,
                momento_pagado,
                momento,
                id_tipo_relacion_comprobante,
                fecha_costo_ini,
                fecha_costo_fin,
                id_config_cambiaria,
                tipo_cambio_2,
                localidad,
                id_moneda_tri,
                nro_tramite,
                sw_editable,
                sw_tipo_cambio,
                cbte_reversion,
                id_proceso_wf,
                id_estado_wf,
                forma_cambio,
                tipo_cambio_3,
                id_moneda_act,
                tipo_cbte
          	) values(
              v_reg_cbte.id_clase_comprobante,
              v_reg_cbte.id_subsistema,
              v_reg_cbte.id_depto,
              v_reg_cbte.id_moneda,
              v_reg_cbte.id_periodo,
              v_reg_cbte.id_funcionario_firma1,
              v_reg_cbte.id_funcionario_firma2,
              v_reg_cbte.id_funcionario_firma3,
              v_reg_cbte.tipo_cambio,
              v_reg_cbte.beneficiario,
              'borrador',
              'REVERSION CBTE ('||v_reg_cbte.nro_cbte||',  id:'||v_reg_cbte.id_int_comprobante||' )',
              v_fecha, --v_reg_cbte.fecha,
              v_reg_cbte.glosa2,
              --v_parametros.momento,
              p_id_usuario,
              now(),
              null,
              null,
              v_parametros._id_usuario_ai,
              v_parametros._nombre_usuario_ai,
              (string_to_array(v_parametros.id_int_comprobante::varchar,','))::INTEGER[],
              v_reg_cbte.cbte_cierre,
              v_reg_cbte.cbte_apertura,
              v_reg_cbte.cbte_aitb,
              'no',
              v_reg_cbte.momento_comprometido,
              v_reg_cbte.momento_ejecutado,
              v_reg_cbte.momento_pagado,
              v_reg_cbte.momento,
              v_id_tipo_relacion_comprobante,
              v_reg_cbte.fecha_costo_ini,
              v_reg_cbte.fecha_costo_fin,
              v_reg_cbte.id_config_cambiaria,
              v_reg_cbte.tipo_cambio_2,
              v_reg_cbte.localidad,
              v_reg_cbte.id_moneda_tri,
              v_num_tramite,
              'si',  -- sw_editable
              v_reg_cbte.sw_tipo_cambio, -- RAC 05/12/2016 ....  'si', -- sw_tipo_cambio
			  'si', -- cbte_reversion	, marcamos como cbte de reversion
              v_id_proceso_wf,
              v_id_estado_wf,
              v_reg_cbte.forma_cambio,
              v_reg_cbte.tipo_cambio_3,
              v_reg_cbte.id_moneda_act,
              v_reg_cbte.tipo_cbte
			)RETURNING id_int_comprobante into v_id_int_comprobante;

           update wf.tproceso_wf p set
            descripcion = descripcion||' ('||v_clcbt_desc||'id:'||v_id_int_comprobante::varchar||')'
           where p.id_proceso_wf = v_id_proceso_wf;


            -- listar todas las transacciones originales
            FOR v_registros in (
                     select *
                     from conta.tint_transaccion it
                     where  it.estado_reg = 'activo' and
                     it.id_int_comprobante = v_parametros.id_int_comprobante) LOOP

                   --  insertar transaccion volcada

                   IF v_reg_cbte.momento_comprometido ='si' and v_reg_cbte.momento_ejecutado ='si' and v_reg_cbte.momento_pagado='si' THEN
                   		v_id_partida_ejecucion = NULL;
                   ELSE
                   		v_id_partida_ejecucion = v_registros.id_partida_ejecucion;
                   END IF;

                    -----------------------------
                    --REGISTRO DE LA TRANSACCIÓN
                    -----------------------------

                    insert into conta.tint_transaccion(
                        id_partida,
                        id_centro_costo,
                        estado_reg,
                        id_cuenta,
                        glosa,
                        id_int_comprobante,
                        id_auxiliar,

                        importe_debe,
                        importe_haber,
                        importe_gasto,
                        importe_recurso,

                        id_usuario_reg,
                        fecha_reg,
                        id_usuario_mod,
                        fecha_mod,
                        id_orden_trabajo,
                        tipo_cambio,
                        tipo_cambio_2,
                        tipo_cambio_3,
                        id_moneda,
                        id_moneda_tri,
                        id_moneda_act,
                        importe_debe_mb,
                        importe_haber_mb,
                        importe_recurso_mb,
                        importe_gasto_mb,

                        importe_debe_mt,
                        importe_haber_mt,
                        importe_gasto_mt,
                        importe_recurso_mt ,

                        triangulacion ,
                        actualizacion,
                        id_partida_ejecucion,
                        id_partida_ejecucion_dev

                    ) values(
                        v_registros.id_partida,
                        v_registros.id_centro_costo,
                        'activo',
                        v_registros.id_cuenta,
                        v_registros.glosa,
                        v_id_int_comprobante,  --referencia al cbte volcado
                        v_registros.id_auxiliar,

                        v_registros.importe_haber,   --  insercion volcada de estos registros
                        v_registros.importe_debe, --  insercion volcada de estos registros
                        v_registros.importe_recurso, --  insercion volcada de estos registros
                        v_registros.importe_gasto, --  insercion volcada de estos registros

                        p_id_usuario,
                        now(),
                        null,
                        null,
                        v_registros.id_orden_trabajo,
                        v_registros.tipo_cambio,
                        v_registros.tipo_cambio_2,
                        v_registros.tipo_cambio_3,
                        v_registros.id_moneda,
                        v_registros.id_moneda_tri,
                        v_registros.id_moneda_act,
                        v_registros.importe_haber_mb,--  insercion volcada de estos registros
                        v_registros.importe_debe_mb,
                        v_registros.importe_gasto_mb,--  insercion volcada de estos registros
                        v_registros.importe_recurso_mb,

                        v_registros.importe_haber_mt,--  insercion volcada de estos registros
                        v_registros.importe_debe_mt,
                        v_registros.importe_recurso_mt, --  insercion volcada de estos registros

                        v_registros.importe_gasto_mt,
                        v_registros.triangulacion ,
                        v_registros.actualizacion,
                        v_id_partida_ejecucion,
                        v_registros.id_partida_ejecucion_dev

                    )RETURNING id_int_transaccion into v_id_int_transaccion;


                     --  si el comprobante tiene relaciones de devenago ...(aolo si es un cbte de pago)
                     --  asociamos el pago al nuevo comprobante
                     --  con montos negativos

                     FOR  v_registros_dev in (
                                              select
                                                ird.id_int_rel_devengado,
                                                ird.monto_pago,
                                                ird.monto_pago_mb,
                                                ird.monto_pago_mt,
                                                ird.id_int_transaccion_dev,
                                                it.id_partida_ejecucion_dev,
                                                it.importe_reversion,
                                                it.factor_reversion,
                                                it.monto_pagado_revertido,
                                                ic.fecha,
                                                it.id_partida_ejecucion_rev,
                                                p.codigo as codigo_partida,
                                                it.id_centro_costo as id_presupuesto,
                                                ird.id_partida_ejecucion_pag

                                              from  conta.tint_rel_devengado ird
                                              inner join conta.tint_transaccion it
                                                on it.id_int_transaccion = ird.id_int_transaccion_dev
                                              inner join pre.tpartida p on p.id_partida = it.id_partida

                                              inner join conta.tint_comprobante ic on ic.id_int_comprobante = it.id_int_comprobante
                                              where  ird.id_int_transaccion_pag = v_registros.id_int_transaccion
                                                     and ird.estado_reg = 'activo'
                                                     and p.sw_movimiento = 'presupuestaria'
                                             ) LOOP


                                    insert into conta.tint_rel_devengado(
                                        id_int_transaccion_pag,
                                        id_int_transaccion_dev,
                                        monto_pago,
                                        monto_pago_mb,
                                        monto_pago_mt,
                                        estado_reg,
                                        id_usuario_ai,
                                        fecha_reg,
                                        usuario_ai,
                                        id_usuario_reg,
                                        sw_reversion,
                                        id_partida_ejecucion_pag
                                     ) values(
                                        v_id_int_transaccion,
                                        v_registros_dev.id_int_transaccion_dev,
                                        v_registros_dev.monto_pago*(-1),
                                        v_registros_dev.monto_pago_mb*(-1),
                                        v_registros_dev.monto_pago_mt*(-1),
                                        'activo',
                                        v_parametros._id_usuario_ai,
                                        now(),
                                        v_parametros._nombre_usuario_ai,
                                        p_id_usuario,
                                        'si',
                                        v_registros_dev.id_partida_ejecucion_pag
                                    ) ;

                            --isnerta relacion de devengado con la reversion
                            --marcando el sw de reversion

                    END LOOP;

            END LOOP;

            --marcar el cbte original como volcado
            update conta.tint_comprobante c set
              volcado = 'si'
            where c.id_int_comprobante =  v_parametros.id_int_comprobante;

            IF v_parametros.sw_validar = 'si' then
                --solictar validacion del comprobante
                v_result = conta.f_validar_cbte(p_id_usuario,
                                                   v_parametros._id_usuario_ai,
                                                   v_parametros._nombre_usuario_ai,
                                                   v_id_int_comprobante,
                                                   'si');

                v_resp = pxp.f_agrega_clave(v_resp,'mensaje','fue volcado y validado el cbte : id '||v_parametros.id_int_comprobante::varchar);

           	else
               v_resp = pxp.f_agrega_clave(v_resp,'mensaje','fue volcado en borrador el cbte : id '||v_parametros.id_int_comprobante::varchar);
            end if;
            --Definicion de la respuesta

            v_resp = pxp.f_agrega_clave(v_resp,'id_int_comprobante',v_parametros.id_int_comprobante::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;


    /*********************************
 	#TRANSACCION:  'CONTA_GETRAIZ_IME'
 	#DESCRIPCION:	Busca el cbte relacionado raiz
 	#AUTOR:		rac
 	#FECHA:		11/04/2016 00:28:30
	***********************************/

	elsif(p_transaccion='CONTA_SWEDIT_IME')then

		begin

              WITH RECURSIVE path_rec(id_int_comprobante, id_int_comprobante_fks,nro_tramite,nro_cbte,glosa1 ) AS (

                      SELECT
                        c.id_int_comprobante,
                        c.id_int_comprobante_fks,
                        c.nro_tramite,
                        c.nro_cbte,
                        c.glosa1
                      FROM conta.tint_comprobante c
                      WHERE c.id_int_comprobante = v_parametros.id_int_comprobante

                      UNION
                      SELECT
                        c2.id_int_comprobante,
                        c2.id_int_comprobante_fks,
                        c2.nro_tramite,
                        c2.nro_cbte,
                        c2.glosa1
                      FROM conta.tint_comprobante c2
                      inner join path_rec  pr on c2.id_int_comprobante = ANY(pr.id_int_comprobante_fks)


                  )
                  SELECT
                    id_int_comprobante
                  into
                    v_id_int_comprobante
                  FROM path_rec order by id_int_comprobante  limit 1 offset 0;




            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','busqueda de raiz relacionada');
            v_resp = pxp.f_agrega_clave(v_resp,'id_int_comprobante_raiz',COALESCE(v_id_int_comprobante::varchar,'0'));

            --Devuelve la respuesta
            return v_resp;

		end;

   /*********************************
 	#TRANSACCION:  'CD_SIGCBTE_IME'
 	#DESCRIPCION:  cambia al siguiente estado	del comprobante
 	#AUTOR:		RAC
 	#FECHA:		01-06-2016 12:12:51
	***********************************/

	elseif(p_transaccion='CD_SIGCBTE_IME')then
        begin

           /*   PARAMETROS

          $this->setParametro('id_proceso_wf_act','id_proceso_wf_act','int4');
          $this->setParametro('id_tipo_estado','id_tipo_estado','int4');
          $this->setParametro('id_funcionario_wf','id_funcionario_wf','int4');
          $this->setParametro('id_depto_wf','id_depto_wf','int4');
          $this->setParametro('obs','obs','text');
          $this->setParametro('json_procesos','json_procesos','text');
          */


          ----recuperamos el numero de tramite para comprobar que viene de un fondo en avance
        select nro_tramite
        into v_nro_tramite
        from conta.tint_comprobante
        where id_int_comprobante= v_parametros.id_int_comprobante;

                    IF EXISTS(
                           select 1
                          from cd.tcuenta_doc cd
                          where cd.nro_tramite=v_nro_tramite
                          )then
                              for v_registros_cd in (select cd.*
                                                     from cd.tcuenta_doc cd
                                                     where cd.nro_tramite=v_nro_tramite)loop
                                  if v_registros_cd.id_int_comprobante_reposicion = v_parametros.id_int_comprobante THEN
                                        select cbte.estado_reg
                                        into estado_cbte
                                        FROM conta.tint_comprobante cbte
                                        where cbte.id_int_comprobante = v_registros_cd.id_int_comprobante;
                                            if estado_cbte = 'borrador' THEN
                                              raise exception 'No es posible validar el comprobante de Reposición con ID: %  , sin antes validar primero el comprobante de Rendición con ID: % correspondiente al trámite: %',v_registros_cd.id_int_comprobante_reposicion,v_registros_cd.id_int_comprobante, v_registros_cd.nro_tramite;
                                            end if;

                                     /*  -- raise exception 'este comprobante es uno de reposicion de fondos % , %, %',v_registros_cd.id_cuenta_doc , estado_cbte ,v_registros_cd.id_int_comprobante;
                                    elsif v_registros_cd.id_int_comprobante = v_parametros.id_int_comprobante then
                                      raise exception 'este comprobante es uno de rendicion de fondos %',v_registros_cd.id_cuenta_doc;*/
                                    end if;

                                end loop;


                     end if;

          --  obtenermos datos basicos
          select
              ic.id_proceso_wf,
              ic.id_estado_wf,
              ic.estado_reg

             into
              v_id_proceso_wf,
              v_id_estado_wf,
              v_codigo_estado

          from conta.tint_comprobante ic
          where ic.id_int_comprobante =  v_parametros.id_int_comprobante;

         -- recupera datos del estado

           select
            ew.id_tipo_estado ,
            te.codigo
           into
            v_id_tipo_estado,
            v_codigo_estado
          from wf.testado_wf ew
          inner join wf.ttipo_estado te on te.id_tipo_estado = ew.id_tipo_estado
          where ew.id_estado_wf = v_parametros.id_estado_wf_act;


         -- obtener datos tipo estado
           select
                 te.codigo
            into
                 v_codigo_estado_siguiente
           from wf.ttipo_estado te
           where te.id_tipo_estado = v_parametros.id_tipo_estado;

           IF  pxp.f_existe_parametro(p_tabla,'id_depto_wf') THEN
              v_id_depto = v_parametros.id_depto_wf;
           END IF;



         IF v_codigo_estado_siguiente != 'validado' THEN

               IF  pxp.f_existe_parametro(p_tabla,'obs') THEN
                   v_obs = v_parametros.obs;
               ELSE
                   v_obs = '---';
               END IF;

               ---------------------------------------
               -- REGISRTA EL SIGUIENTE ESTADO DEL WF.
               ---------------------------------------


               --configurar acceso directo para la alarma
                v_acceso_directo = '';
                v_clase = '';
                v_parametros_ad = '';
                v_tipo_noti = 'notificacion';
                v_titulo  = 'Visto Bueno';


               IF   v_codigo_estado_siguiente not in('borrador','finalizado','anulado')   THEN
                     v_acceso_directo = '../../../sis_contabilidad/vista/int_comprobante/IntComprobanteVb.php';
                     v_clase = 'IntComprobanteVb';
                     v_parametros_ad = '{filtro_directo:{campo:"cd.id_proceso_wf",valor:"'||v_id_proceso_wf::varchar||'"}}';
                     v_tipo_noti = 'notificacion';
                     v_titulo  = 'Visto Bueno';
               END IF;

               v_id_estado_actual =  wf.f_registra_estado_wf(  v_parametros.id_tipo_estado,
                                                               v_parametros.id_funcionario_wf,
                                                               v_parametros.id_estado_wf_act,
                                                               v_id_proceso_wf,
                                                               p_id_usuario,
                                                               v_parametros._id_usuario_ai,
                                                               v_parametros._nombre_usuario_ai,
                                                               v_id_depto,                       --depto del estado anterior
                                                               v_obs,
                                                               v_acceso_directo,
                                                               v_clase,
                                                               v_parametros_ad,
                                                               v_tipo_noti,
                                                               v_titulo);


                -- actualiza estado en la solicitud
               update conta.tint_comprobante   set
                 id_estado_wf =  v_id_estado_actual,
                 estado_reg = v_codigo_estado_siguiente,
                 id_usuario_mod = p_id_usuario,
                 id_usuario_ai = v_parametros._id_usuario_ai,
                 usuario_ai = v_parametros._nombre_usuario_ai,
                 fecha_mod = now()
               where id_proceso_wf = v_id_proceso_wf;


           ELSE

                --Lamada a la función de validación
				v_result = conta.f_validar_cbte( p_id_usuario,
                                             v_parametros._id_usuario_ai,
                                             v_parametros._nombre_usuario_ai,
                                             v_parametros.id_int_comprobante,
                                             'no',
                                             'pxp',
                                              NULL,
                                              v_parametros.validar_doc);


                 IF v_result != 'Comprobante validado' THEN
                 		v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Se realizo el cambio de estado del cuenta documentada id='||v_parametros.id_int_comprobante);
          				v_resp = pxp.f_agrega_clave(v_resp,'operacion','falla');
                        v_resp = pxp.f_agrega_clave(v_resp,'desc_falla',v_result);
                 END IF;


           END IF;


          -- si hay mas de un estado disponible  preguntamos al usuario
          v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Se realizo el cambio de estado del cuenta documentada id='||v_parametros.id_int_comprobante);
          v_resp = pxp.f_agrega_clave(v_resp,'operacion','cambio_exitoso');


          -- Devuelve la respuesta
          return v_resp;

     end;


	/*********************************
 	#TRANSACCION:  'CD_ANTCBTE_IME'
 	#DESCRIPCION:  retrocede el estado de la cuenta documentada
 	#AUTOR:		   RAC
 	#FECHA:		   01-06-2016 12:12:51
	***********************************/

	elseif(p_transaccion='CD_ANTCBTE_IME')then
        begin


        --obtenermos datos basicos
        select
            ic.id_int_comprobante,
            ic.id_proceso_wf,
            ic.estado_reg,
            pwf.id_tipo_proceso,
            ic.id_estado_wf,
            ic.nro_tramite
        into
            v_rec
        from conta.tint_comprobante  ic
        inner  join wf.tproceso_wf pwf  on  pwf.id_proceso_wf = ic.id_proceso_wf
        where ic.id_proceso_wf  = v_parametros.id_proceso_wf;


        IF v_rec.estado_reg = 'validado' THEN
            raise exception 'El cbte ya se encuentra validado no se puede retroceder';
        END IF;


        v_id_proceso_wf = v_rec.id_proceso_wf;


        --------------------------------------------------
        --Retrocede al estado inmediatamente anterior
        -------------------------------------------------
         --recuperaq estado anterior segun Log del WF
              SELECT

                 ps_id_tipo_estado,
                 ps_id_funcionario,
                 ps_id_usuario_reg,
                 ps_id_depto,
                 ps_codigo_estado,
                 ps_id_estado_wf_ant
              into
                 v_id_tipo_estado,
                 v_id_funcionario,
                 v_id_usuario_reg,
                 v_id_depto,
                 v_codigo_estado,
                 v_id_estado_wf_ant
              FROM wf.f_obtener_estado_ant_log_wf(v_parametros.id_estado_wf);


         --configurar acceso directo para la alarma
             v_acceso_directo = '';
             v_clase = '';
             v_parametros_ad = '';
             v_tipo_noti = 'notificacion';
             v_titulo  = 'Visto Bueno';


           IF   v_codigo_estado_siguiente not in('borrador','validado','anulado')   THEN
                 v_acceso_directo = '../../../sis_contabilidad/vista/int_comprobante/IntComprobanteVb.php';
                 v_clase = 'IntComprobanteVb';
                 v_parametros_ad = '{filtro_directo:{campo:"cd.id_proceso_wf",valor:"'||v_id_proceso_wf::varchar||'"}}';
                 v_tipo_noti = 'notificacion';
                 v_titulo  = 'Visto Bueno';

           END IF;



           v_id_estado_actual = wf.f_registra_estado_wf(v_id_tipo_estado,                --  id_tipo_estado al que retrocede
                                                        v_id_funcionario,                --  funcionario del estado anterior
                                                        v_parametros.id_estado_wf,       --  estado actual ...
                                                        v_id_proceso_wf,                 --  id del proceso actual
                                                        p_id_usuario,                    -- usuario que registra
                                                        v_parametros._id_usuario_ai,
                                                        v_parametros._nombre_usuario_ai,
                                                        v_id_depto,                       --depto del estado anterior
                                                        '[RETROCESO] '|| v_parametros.obs,
                                                        v_acceso_directo,
                                                        v_clase,
                                                        v_parametros_ad,
                                                        v_tipo_noti,
                                                        v_titulo);



            update conta.tint_comprobante   set
               id_estado_wf =  v_id_estado_actual,
               estado_reg = v_codigo_estado,
               id_usuario_mod = p_id_usuario,
               fecha_mod = now(),
               id_usuario_ai = v_parametros._id_usuario_ai,
               usuario_ai = v_parametros._nombre_usuario_ai
            where id_proceso_wf = v_parametros.id_proceso_wf;


         -- si hay mas de un estado disponible  preguntamos al usuario
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Se realizo el cambio de estado del comprobante)');
            v_resp = pxp.f_agrega_clave(v_resp,'operacion','cambio_exitoso');


          --Devuelve la respuesta
            return v_resp;


        end;
	/*********************************
 	#TRANSACCION:  'CONTA_CLONARCBTE_IME'
 	#DESCRIPCION:	Clonar comprobante comprobante
 	#AUTOR:		rac (kplian)
 	#FECHA:		02-06-2016 00:28:30
	***********************************/

	elsif(p_transaccion='CONTA_CLONARCBTE_IME')then

		begin


            select
             ic.*,
             p.id_gestion
            into
             v_reg_cbte
            from conta.tint_comprobante ic
            inner join param.tperiodo p on p.id_periodo = ic.id_periodo
            where ic.id_int_comprobante = v_parametros.id_int_comprobante;



            --validar que el periodo se encuentre abierto
            IF not param.f_periodo_subsistema_abierto(v_reg_cbte.fecha::date, 'CONTA') THEN
                raise exception 'El periodo se encuentra cerrado en contabilidad para la fecha:  %',v_reg_cbte.fecha;
            END IF;


            ----------------------------------------
            -- registrar proceso disparado de WF
            ----------------------------------------

              --  inicia tramite nuevo
              v_codigo_proceso_macro = pxp.f_get_variable_global('conta_codigo_macro_wf_cbte');

              --obtener id del proceso macro
              select
               pm.id_proceso_macro
              into
               v_id_proceso_macro
              from wf.tproceso_macro pm
              where pm.codigo = v_codigo_proceso_macro;


              If v_id_proceso_macro is NULL THEN
                raise exception 'El proceso macro  de codigo % no esta configurado en el sistema WF',v_codigo_proceso_macro;
              END IF;

              --   obtener el codigo del tipo_proceso
              select   tp.codigo
               into v_codigo_tipo_proceso
              from  wf.ttipo_proceso tp
              where   tp.id_proceso_macro = v_id_proceso_macro
                    and tp.estado_reg = 'activo' and tp.inicio = 'si';

              IF v_codigo_tipo_proceso is NULL THEN
               raise exception 'No existe un proceso inicial para el proceso macro indicado % (Revise la configuración)',v_codigo_proceso_macro;
              END IF;

              -- preguntar si se quiere clonar el  con el nro de tramite
             IF    v_parametros.sw_tramite = 'si'  THEN
                	 -- inciar el proceso con un nuevo nro  de  tramite en el sistema de WF
                    SELECT
                       ps_num_tramite ,
                       ps_id_proceso_wf ,
                       ps_id_estado_wf ,
                       ps_codigo_estado
                      into
                       v_num_tramite,
                       v_id_proceso_wf,
                       v_id_estado_wf,
                       v_codigo_estado

                    FROM wf.f_inicia_tramite(
                       p_id_usuario,
                       v_parametros._id_usuario_ai,
                       v_parametros._nombre_usuario_ai,
                       v_reg_cbte.id_gestion,
                       v_codigo_tipo_proceso,
                       null,--v_parametros.id_funcionario,
                       v_reg_cbte.id_depto,
                       'Registro de Cbte manual/clonado',
                       '' );

              ELSE

                    SELECT
                                 ps_id_proceso_wf,
                                 ps_id_estado_wf,
                                 ps_codigo_estado,
                                 ps_nro_tramite
                       into
                                 v_id_proceso_wf,
                                 v_id_estado_wf,
                                 v_codigo_estado,
                                 v_num_tramite
                   FROM wf.f_registra_proceso_disparado_wf(
                                p_id_usuario,
                                v_parametros._id_usuario_ai,
                                v_parametros._nombre_usuario_ai,
                                v_reg_cbte.id_estado_wf,
                                NULL,  --id_funcionario wf
                                v_reg_cbte.id_depto,
                                'Cbte Clonado',
                                'CBTE','');

                  --asocia el comprobante

                  va_id_int_comprobante_fks[1] = v_parametros.id_int_comprobante;
                  select
                     tr.id_tipo_relacion_comprobante
                  into
                     v_id_tipo_relacion_comprobante
                  from conta.ttipo_relacion_comprobante tr
                  where tr.codigo = 'AJUSTE';


              END IF;

              IF  v_codigo_estado != 'borrador' THEN
                raise exception 'el estado inicial para cbtes debe ser borrador, revise la configuración del WF';
              END IF;

             select
              cc.tipo_comprobante,
              cc.descripcion
            into
              v_tipo_comprobante,
              v_clcbt_desc
            from conta.tclase_comprobante cc
            where cc.id_clase_comprobante = v_reg_cbte.id_clase_comprobante;


            -----------------------------
        	--REGISTRO DEL COMPROBANTE
        	-----------------------------
        	insert into conta.tint_comprobante(
                id_clase_comprobante,
                id_subsistema,
                id_depto,
                id_moneda,
                id_periodo,
                id_funcionario_firma1,
                id_funcionario_firma2,
                id_funcionario_firma3,
                tipo_cambio,
                beneficiario,
                estado_reg,
                glosa1,
                fecha,
                glosa2,
                --momento,
                id_usuario_reg,
                fecha_reg,
                id_usuario_mod,
                fecha_mod,
                id_usuario_ai,
                usuario_ai,
                cbte_cierre,
                cbte_apertura,
                cbte_aitb,
                manual,
                momento_comprometido,
                momento_ejecutado,
                momento_pagado,
                momento,
                fecha_costo_ini,
                fecha_costo_fin,
                id_config_cambiaria,
                tipo_cambio_2,
                tipo_cambio_3,
                localidad,
                id_moneda_tri,
                id_moneda_act,
                nro_tramite,
                sw_editable,
                sw_tipo_cambio,
                cbte_reversion,
                id_proceso_wf,
                id_estado_wf,
                forma_cambio,
                id_int_comprobante_fks,
                id_tipo_relacion_comprobante,
                tipo_cbte
          	) values(
              v_reg_cbte.id_clase_comprobante,
              v_reg_cbte.id_subsistema,
              v_reg_cbte.id_depto,
              v_reg_cbte.id_moneda,
              v_reg_cbte.id_periodo,
              v_reg_cbte.id_funcionario_firma1,
              v_reg_cbte.id_funcionario_firma2,
              v_reg_cbte.id_funcionario_firma3,
              v_reg_cbte.tipo_cambio,
              v_reg_cbte.beneficiario,
              'borrador',
              v_reg_cbte.glosa1||' (clonado)',
              v_reg_cbte.fecha,
              v_reg_cbte.glosa2,
              --v_parametros.momento,
              p_id_usuario,
              now(),
              null,
              null,
              v_parametros._id_usuario_ai,
              v_parametros._nombre_usuario_ai,
              v_reg_cbte.cbte_cierre,
              v_reg_cbte.cbte_apertura,
              v_reg_cbte.cbte_aitb,
              'si', ---comprobantes clonados se registran como  manaules
              v_reg_cbte.momento_comprometido,
              v_reg_cbte.momento_ejecutado,
              v_reg_cbte.momento_pagado,
              v_reg_cbte.momento,
              v_reg_cbte.fecha_costo_ini,
              v_reg_cbte.fecha_costo_fin,
              v_reg_cbte.id_config_cambiaria,
              v_reg_cbte.tipo_cambio_2,
              v_reg_cbte.tipo_cambio_3,
              v_reg_cbte.localidad,
              v_reg_cbte.id_moneda_tri,
              v_reg_cbte.id_moneda_act,
              v_num_tramite,
              'si',  -- sw_editable
              'no', -- sw_tipo_cambio
			  'no', -- cbte_reversion	, marcamos como cbte de reversion
              v_id_proceso_wf,
              v_id_estado_wf,
              v_reg_cbte.forma_cambio,
              va_id_int_comprobante_fks,
              v_id_tipo_relacion_comprobante,
              v_reg_cbte.tipo_cbte
			)RETURNING id_int_comprobante into v_id_int_comprobante;

            update wf.tproceso_wf p set
              descripcion = descripcion||' ('||v_clcbt_desc||'id:'||v_id_int_comprobante::varchar||')'
            where p.id_proceso_wf = v_id_proceso_wf;


            -- listar todas las transacciones originales
            FOR v_registros in (
                     select *
                     from conta.tint_transaccion it
                     where  it.estado_reg = 'activo' and
                     it.id_int_comprobante = v_parametros.id_int_comprobante) LOOP

                   --  insertar transaccion volcada

                    -----------------------------
                    --REGISTRO DE LA TRANSACCIÓN
                    -----------------------------

                    insert into conta.tint_transaccion(
                        id_partida,
                        id_centro_costo,
                        estado_reg,
                        id_cuenta,
                        glosa,
                        id_int_comprobante,
                        id_auxiliar,
                        importe_debe,
                        importe_haber,
                        importe_gasto,
                        importe_recurso,

                        id_usuario_reg,
                        fecha_reg,
                        id_usuario_mod,
                        fecha_mod,
                        id_orden_trabajo,
                        tipo_cambio,
                        tipo_cambio_2,
                        tipo_cambio_3,
                        id_moneda,
                        id_moneda_tri,
                        id_moneda_act,
                        importe_debe_mb,
                        importe_haber_mb,
                        importe_recurso_mb,
                        importe_gasto_mb,

                        importe_debe_mt,
                        importe_haber_mt,
                        importe_recurso_mt ,
                        importe_gasto_mt,


                        triangulacion ,
                        actualizacion,
                        id_partida_ejecucion,
                        id_partida_ejecucion_dev

                    ) values(
                        v_registros.id_partida,
                        v_registros.id_centro_costo,
                        'activo',
                        v_registros.id_cuenta,
                        v_registros.glosa,
                        v_id_int_comprobante,  --referencia al cbte volcado
                        v_registros.id_auxiliar,
                        v_registros.importe_debe,
                        v_registros.importe_haber,
                        v_registros.importe_debe, --
                        v_registros.importe_haber, --

                        p_id_usuario,
                        now(),
                        null,
                        null,
                        v_registros.id_orden_trabajo,
                        v_registros.tipo_cambio,
                        v_registros.tipo_cambio_2,
                        v_registros.tipo_cambio_3,
                        v_registros.id_moneda,
                        v_registros.id_moneda_tri,
                        v_registros.id_moneda_act,
                        v_registros.importe_debe_mb,
                        v_registros.importe_haber_mb,
                        v_registros.importe_recurso_mb,
                        v_registros.importe_gasto_mb,
                        v_registros.importe_debe_mt,
                        v_registros.importe_haber_mt,
                        v_registros.importe_recurso_mt,
                        v_registros.importe_gasto_mt,
                        v_registros.triangulacion ,
                        v_registros.actualizacion,
                        NULL,--v_registros.id_partida_ejecucion,       --com oestamos clonado , es mejor no hacer refencia al id_partida ejecucion original
                        NULL --v_registros.id_partida_ejecucion_dev

                    )RETURNING id_int_transaccion into v_id_int_transaccion;

                      /*

                     --  si el comprobante tiene relaciones de devenago (si es un cbte de pago)
                     --  asociamos el pago al nuevo comprobante

                     FOR  v_registros_dev in (
                                              select
                                                ird.id_int_rel_devengado,
                                                ird.monto_pago,
                                                ird.monto_pago_mb,
                                                ird.monto_pago_mt,
                                                ird.id_int_transaccion_dev,
                                                it.id_partida_ejecucion_dev,
                                                it.importe_reversion,
                                                it.factor_reversion,
                                                it.monto_pagado_revertido,
                                                ic.fecha,
                                                it.id_partida_ejecucion_rev,
                                                p.codigo as codigo_partida,
                                                it.id_centro_costo as id_presupuesto,
                                                ird.id_partida_ejecucion_pag

                                              from  conta.tint_rel_devengado ird
                                              inner join conta.tint_transaccion it
                                                on it.id_int_transaccion = ird.id_int_transaccion_dev
                                              inner join pre.tpartida p on p.id_partida = it.id_partida

                                              inner join conta.tint_comprobante ic on ic.id_int_comprobante = it.id_int_comprobante
                                              where  ird.id_int_transaccion_pag = v_registros.id_int_transaccion
                                                     and ird.estado_reg = 'activo'
                                                     and p.sw_movimiento = 'presupuestaria'
                                             ) LOOP


                                    insert into conta.tint_rel_devengado(
                                        id_int_transaccion_pag,
                                        id_int_transaccion_dev,
                                        monto_pago,
                                        monto_pago_mb,
                                        monto_pago_mt,
                                        estado_reg,
                                        id_usuario_ai,
                                        fecha_reg,
                                        usuario_ai,
                                        id_usuario_reg,
                                        sw_reversion,
                                        id_partida_ejecucion_pag
                                     ) values(
                                        v_id_int_transaccion,
                                        v_registros_dev.id_int_transaccion_dev,
                                        v_registros_dev.monto_pago*(-1),
                                        v_registros_dev.monto_pago_mb*(-1),
                                        v_registros_dev.monto_pago_mt*(-1),
                                        'activo',
                                        v_parametros._id_usuario_ai,
                                        now(),
                                        v_parametros._nombre_usuario_ai,
                                        p_id_usuario,
                                        'si',
                                        v_registros_dev.id_partida_ejecucion_pag
                                    ) ;

                            --isnerta relacion de devengado con la reversion
                            --marcando el sw de reversion

                    END LOOP;

                    */

            END LOOP;


            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','fue clonado el cbte : id '||v_parametros.id_int_comprobante::varchar);
            v_resp = pxp.f_agrega_clave(v_resp,'id_int_comprobante',v_parametros.id_int_comprobante::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;


    /*********************************
 	#TRANSACCION:  'CONTA_UPDFECOS_MOD'
 	#DESCRIPCION:	permite modificar las fecha de costos iniciales y finales, en comprobantes validados
 	#AUTOR:		admin
 	#FECHA:		29-08-2013 00:28:30
	***********************************/

	elsif(p_transaccion='CONTA_UPDFECOS_MOD')then

		begin

			 --valida fechas de costos
             IF v_parametros.fecha_costo_fin <  v_parametros.fecha_costo_ini THEN
               raise exception 'LA FECHA FINAL NO PUEDE SER MENOR A LA FECHA INICIAL';
             END IF;
            /* --validador de gestion
			v_anio_gestion = ( select date_part('year',now()))::INTEGER;
		    IF NOT ((date_part('year',v_parametros.fecha_costo_ini) = v_anio_gestion) and (date_part('year',v_parametros.fecha_costo_fin)=v_anio_gestion)) THEN
               raise exception 'LAS FECHAS NO CORRESPONDEN A LA GESTION ACTUAL';
            END IF;
			*/
              --control de fechas inicio y fin
            select date_part('year',com.fecha)
            into v_anio_com
            from conta.tint_comprobante com
            where com.id_int_comprobante = v_id_int_comprobante;
           --raise exception '%, %, %',v_parametros.fecha, v_parametros.fecha_costo_ini, v_parametros.fecha_costo_fin;

            IF NOT ((date_part('year',v_parametros.fecha_costo_ini) = v_anio_com) and (date_part('year',v_parametros.fecha_costo_fin)=v_anio_com)) THEN
               raise exception 'LAS FECHAS NO CORRESPONDEN A LA GESTIÓN, TIENE COMO FECHA %',v_parametros.fecha;
            END IF;
			------------------------------
			--Sentencia de la modificacion
			------------------------------

			update conta.tint_comprobante set
                fecha_costo_ini = v_parametros.fecha_costo_ini,
                fecha_costo_fin = v_parametros.fecha_costo_fin
			where id_int_comprobante = v_parametros.id_int_comprobante;

			update tes.tplan_pago set
                fecha_costo_ini = v_parametros.fecha_costo_ini,
                fecha_costo_fin = v_parametros.fecha_costo_fin

			where id_int_comprobante = v_parametros.id_int_comprobante;

			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Fechas de costos  modificadas en cbte validado');
            v_resp = pxp.f_agrega_clave(v_resp,'id_int_comprobante',v_parametros.id_int_comprobante::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;


		/*********************************
        #TRANSACCION:  'CD_REGCBTE_IME'
        #DESCRIPCION:  Regula comprobante en la estacion central
        #AUTOR:		Maylee Perez Pastor
        #FECHA:		01-13-2019 12:12:51
        ***********************************/

		elseif(p_transaccion='CD_REGCBTE_IME')then
            begin

            		v_conta_codigo_estacion = pxp.f_get_variable_global('conta_codigo_estacion');
                    v_sincronizar = pxp.f_get_variable_global('sincronizar');

            		--abrimos conexion dblink
                    IF v_conta_codigo_estacion != 'CENTRAL'  or v_sincronizar = 'true' THEN
                        select * into v_nombre_conexion from migra.f_crear_conexion();
                    END IF;

                    if pxp.f_get_variable_global('ESTACION_inicio') != 'BOL' then

                          select *
                          into v_nombre_conexion
                          from migra.f_crear_conexion();

                          v_resp =  migra.f_migrar_cbte_a_central(v_parametros.id_int_comprobante, v_nombre_conexion);

					else
                    	raise exception 'Solo las Estaciones Internacionales pueden realizar la regularización del Comprobante';
                    end if;

                   --cerrar la conexion comun (regional -> central)
                   if  v_nombre_conexion is not null then
                        select * into v_resp from migra.f_cerrar_conexion(v_nombre_conexion,'exito');
                   end if;

                   	-- si hay mas de un estado disponible  preguntamos al usuario
                  	v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Se realizo la regularizacion del controbante id='||v_parametros.id_int_comprobante);
                  	v_resp = pxp.f_agrega_clave(v_resp,'operacion','cambio_exitoso');

              -- Devuelve la respuesta
              return v_resp;

         end;


          /*********************************
          #TRANSACCION:  'CONTA_VOLCBTECON_IME'
          #DESCRIPCION:	Volcar comprobante contable
          #AUTOR:		Maylee Perez Pastor
          #FECHA:		23-1-2020 00:28:30
          ***********************************/

          elsif(p_transaccion='CONTA_VOLCBTECON_IME')then

              begin


                  select
                   *
                  into
                   v_reg_cbte
                  from conta.tint_comprobante ic
                  where ic.id_int_comprobante = v_parametros.id_int_comprobante;




                  IF  v_reg_cbte.estado_reg != 'validado'  THEN
                     raise exception 'solo pueden volcar comprobantes validados';
                  END IF;

                  IF  v_reg_cbte.volcado = 'si'  THEN
                     raise exception 'El comprobante ya se encuentra volcado';
                  END IF;

                  IF  v_reg_cbte.cbte_reversion = 'si'  THEN
                     --comentado a solicitud de lobito el 01/08/2019
                      --raise exception 'No puede volcar un cbte de reversión';
                  END IF;

                  -- RAC 2/12/2016
                  -- solo revisa dependnecia en cbte de reversion total
                  -- los parciales peuden tener dependencias
                 /* IF v_parametros.sw_validar = 'si' then
                    IF  not conta.f_revisar_dependencias(v_parametros.id_int_comprobante)  THEN
                       raise exception 'error por dependencias';
                    END IF;
                  END IF;
                  */


                  --validar que el periodo se encuentre abierto
                  IF not param.f_periodo_subsistema_abierto(v_reg_cbte.fecha::date, 'CONTA') THEN
                      raise exception 'El periodo se encuentra cerrado en contabilidad para la fecha:  %',v_reg_cbte.fecha;
                  END IF;




                  select
                     rc.id_tipo_relacion_comprobante
                  into
                    v_id_tipo_relacion_comprobante
                  from conta.ttipo_relacion_comprobante rc
                  where rc.codigo = 'REVERSION';

                  -- insertar comprobante volcado, haciendo referencia al cbte ajustado



                  ----------------------------------------
                  -- registrar proceso disparado de WF
                  ----------------------------------------
                  SELECT
                          ps_id_proceso_wf,ps_id_estado_wf, ps_codigo_estado, ps_nro_tramite
                   into
                          v_id_proceso_wf,v_id_estado_wf,v_codigo_estado, v_num_tramite
                  FROM wf.f_registra_proceso_disparado_wf(
                                p_id_usuario,
                                v_parametros._id_usuario_ai,
                                v_parametros._nombre_usuario_ai,
                                v_reg_cbte.id_estado_wf,
                                NULL,  --id_funcionario wf
                                v_reg_cbte.id_depto,
                                'Cbte de Volcado (Anula el original)',
                                'CBTE', --sipara comprobante
                                '');

                  select
                    cc.tipo_comprobante,
                    cc.descripcion
                  into
                    v_tipo_comprobante,
                    v_clcbt_desc
                  from conta.tclase_comprobante cc
                  where cc.id_clase_comprobante = v_reg_cbte.id_clase_comprobante;

                  --may
                  -- si es presupuestario y con la opcion de Reversion Total contable (validado) , valida comprobante de tipo contable
                      IF (v_reg_cbte.id_clase_comprobante = 3) THEN    -- 3 = Comprobante de Diario Presupuestario
                          v_id_clase_comprobante = 4; 				   -- 4 = Comprobante de Diario Contable
                      ELSIF (v_reg_cbte.id_clase_comprobante = 1) THEN -- 1 = Comprobante de Pago Presupuestario
                          	v_id_clase_comprobante = 5;				   -- 5 = Comprobante de Pago Contable

                      ELSE
                          v_id_clase_comprobante = v_reg_cbte.id_clase_comprobante;
                      END IF;
                  --

                  --23-01-2020 (may)
                  --control de fecha del comprobante

                  v_periodo_fecha_cbte = date_part('month',v_reg_cbte.fecha);
                  v_periodo_anio_cbte = date_part('year',v_reg_cbte.fecha);


                  SELECT ges.id_gestion
                  INTO v_gestion_cbte
                  FROM param.tgestion ges
                  WHERE ges.gestion = v_periodo_anio_cbte;

                  SELECT per.fecha_ini, per.fecha_fin
                  INTO v_fecha_ini,v_fecha_fin
                  FROM param.tperiodo per
                  WHERE per.periodo = v_periodo_fecha_cbte
                  and per.id_gestion = v_gestion_cbte;

                  v_periodo_mes_now = date_part('month',now());
                  v_periodo_anio_now = date_part('year',now());

                  IF (v_periodo_fecha_cbte != v_periodo_mes_now and v_periodo_anio_cbte != v_periodo_anio_now ) THEN
                  		IF (v_reg_cbte.fecha < now()::date and v_reg_cbte.fecha > v_fecha_ini and (v_reg_cbte.fecha < v_fecha_fin or v_reg_cbte.fecha = v_fecha_fin)) THEN

                              v_fecha  = v_fecha_fin;
                        ELSIF (v_reg_cbte.fecha < now()::date and v_reg_cbte.fecha> now()::date and v_reg_cbte.fecha > v_fecha_ini and (v_reg_cbte.fecha < v_fecha_fin or v_reg_cbte.fecha = v_fecha_fin)) THEN

                              v_fecha = now()::date;

                        ELSIF (v_reg_cbte.fecha = now()::date and v_reg_cbte.fecha > v_fecha_ini and (v_reg_cbte.fecha < v_fecha_fin or v_reg_cbte.fecha = v_fecha_fin)) THEN

                              v_fecha = now()::date;

                        ELSIF (v_reg_cbte.fecha > now()::date and v_reg_cbte.fecha > v_fecha_ini and (v_reg_cbte.fecha < v_fecha_fin or v_reg_cbte.fecha = v_fecha_fin)) THEN

                              v_fecha = now()::date;

                        ELSE
                              raise exception 'verificar fechas cbtes';

                        END IF;
                  ELSE
                  		IF (v_reg_cbte.fecha < now()::date and v_reg_cbte.fecha > v_fecha_ini and (v_reg_cbte.fecha < v_fecha_fin or v_reg_cbte.fecha = v_fecha_fin)) THEN

                              v_fecha  = now();
                        ELSIF (v_reg_cbte.fecha < now()::date and v_reg_cbte.fecha> now()::date and v_reg_cbte.fecha > v_fecha_ini and (v_reg_cbte.fecha < v_fecha_fin or v_reg_cbte.fecha = v_fecha_fin)) THEN

                              v_fecha = now()::date;

                        ELSIF (v_reg_cbte.fecha = now()::date and v_reg_cbte.fecha > v_fecha_ini and (v_reg_cbte.fecha < v_fecha_fin or v_reg_cbte.fecha = v_fecha_fin)) THEN

                              v_fecha = now()::date;

                        ELSIF (v_reg_cbte.fecha > now()::date and v_reg_cbte.fecha > v_fecha_ini and (v_reg_cbte.fecha < v_fecha_fin or v_reg_cbte.fecha = v_fecha_fin)) THEN

                              v_fecha = now()::date;

                        ELSE
                              raise exception 'verificar fechas cbtes';

                        END IF;
                  END IF;

                   --raise exception 'llega % < % and % > % and %<%',v_reg_cbte.fecha,  now()::date, v_reg_cbte.fecha, v_fecha_ini,v_reg_cbte.fecha,v_fecha_fin ;



                  --


                  -----------------------------
                  --REGISTRO DEL COMPROBANTE
                  -----------------------------
                  insert into conta.tint_comprobante(
                      id_clase_comprobante,
                      id_subsistema,
                      id_depto,
                      id_moneda,
                      id_periodo,
                      id_funcionario_firma1,
                      id_funcionario_firma2,
                      id_funcionario_firma3,
                      tipo_cambio,
                      beneficiario,
                      estado_reg,
                      glosa1,
                      fecha,
                      glosa2,
                      --momento,
                      id_usuario_reg,
                      fecha_reg,
                      id_usuario_mod,
                      fecha_mod,
                      id_usuario_ai,
                      usuario_ai,
                      id_int_comprobante_fks,
                      cbte_cierre,
                      cbte_apertura,
                      cbte_aitb,
                      manual,
                      momento_comprometido,
                      momento_ejecutado,
                      momento_pagado,
                      momento,
                      id_tipo_relacion_comprobante,
                      fecha_costo_ini,
                      fecha_costo_fin,
                      id_config_cambiaria,
                      tipo_cambio_2,
                      localidad,
                      id_moneda_tri,
                      nro_tramite,
                      sw_editable,
                      sw_tipo_cambio,
                      cbte_reversion,
                      id_proceso_wf,
                      id_estado_wf,
                      forma_cambio,
                      tipo_cambio_3,
                      id_moneda_act
                  ) values(
                    v_id_clase_comprobante,
                    v_reg_cbte.id_subsistema,
                    v_reg_cbte.id_depto,
                    v_reg_cbte.id_moneda,
                    v_reg_cbte.id_periodo,
                    v_reg_cbte.id_funcionario_firma1,
                    v_reg_cbte.id_funcionario_firma2,
                    v_reg_cbte.id_funcionario_firma3,
                    v_reg_cbte.tipo_cambio,
                    v_reg_cbte.beneficiario,
                    'borrador',
                    'REVERSION CBTE ('||v_reg_cbte.nro_cbte||',  id:'||v_reg_cbte.id_int_comprobante||' )',
                    v_fecha, --v_reg_cbte.fecha,
                    v_reg_cbte.glosa2,
                    --v_parametros.momento,
                    p_id_usuario,
                    now(),
                    null,
                    null,
                    v_parametros._id_usuario_ai,
                    v_parametros._nombre_usuario_ai,
                    (string_to_array(v_parametros.id_int_comprobante::varchar,','))::INTEGER[],
                    v_reg_cbte.cbte_cierre,
                    v_reg_cbte.cbte_apertura,
                    v_reg_cbte.cbte_aitb,
                    'no',
                    'no', --v_reg_cbte.momento_comprometido,
                    'no', --v_reg_cbte.momento_ejecutado,
                    'no', --v_reg_cbte.momento_pagado,
                    'contable', --v_reg_cbte.momento,
                    v_id_tipo_relacion_comprobante,
                    v_reg_cbte.fecha_costo_ini,
                    v_reg_cbte.fecha_costo_fin,
                    v_reg_cbte.id_config_cambiaria,
                    v_reg_cbte.tipo_cambio_2,
                    v_reg_cbte.localidad,
                    v_reg_cbte.id_moneda_tri,
                    v_num_tramite,
                    'si',  -- sw_editable
                    v_reg_cbte.sw_tipo_cambio, -- RAC 05/12/2016 ....  'si', -- sw_tipo_cambio
                    'si', -- cbte_reversion	, marcamos como cbte de reversion
                    v_id_proceso_wf,
                    v_id_estado_wf,
                    v_reg_cbte.forma_cambio,
                    v_reg_cbte.tipo_cambio_3,
                    v_reg_cbte.id_moneda_act
                  )RETURNING id_int_comprobante into v_id_int_comprobante;

                 update wf.tproceso_wf p set
                  descripcion = descripcion||' ('||v_clcbt_desc||'id:'||v_id_int_comprobante::varchar||')'
                 where p.id_proceso_wf = v_id_proceso_wf;


                  -- listar todas las transacciones originales
                  FOR v_registros in (
                           select *
                           from conta.tint_transaccion it
                           where  it.estado_reg = 'activo' and
                           it.id_int_comprobante = v_parametros.id_int_comprobante) LOOP

                         --  insertar transaccion volcada

                         IF v_reg_cbte.momento_comprometido ='si' and v_reg_cbte.momento_ejecutado ='si' and v_reg_cbte.momento_pagado='si' THEN
                              v_id_partida_ejecucion = NULL;
                         ELSE
                              v_id_partida_ejecucion = v_registros.id_partida_ejecucion;
                         END IF;

                          -----------------------------
                          --REGISTRO DE LA TRANSACCIÓN
                          -----------------------------

                          insert into conta.tint_transaccion(
                              id_partida,
                              id_centro_costo,
                              estado_reg,
                              id_cuenta,
                              glosa,
                              id_int_comprobante,
                              id_auxiliar,

                              importe_debe,
                              importe_haber,
                              importe_gasto,
                              importe_recurso,

                              id_usuario_reg,
                              fecha_reg,
                              id_usuario_mod,
                              fecha_mod,
                              id_orden_trabajo,
                              tipo_cambio,
                              tipo_cambio_2,
                              tipo_cambio_3,
                              id_moneda,
                              id_moneda_tri,
                              id_moneda_act,
                              importe_debe_mb,
                              importe_haber_mb,
                              importe_recurso_mb,
                              importe_gasto_mb,

                              importe_debe_mt,
                              importe_haber_mt,
                              importe_gasto_mt,
                              importe_recurso_mt ,

                              triangulacion ,
                              actualizacion,
                              id_partida_ejecucion,
                              id_partida_ejecucion_dev

                          ) values(
                              v_registros.id_partida,
                              v_registros.id_centro_costo,
                              'activo',
                              v_registros.id_cuenta,
                              v_registros.glosa,
                              v_id_int_comprobante,  --referencia al cbte volcado
                              v_registros.id_auxiliar,

                              v_registros.importe_haber,   --  insercion volcada de estos registros
                              v_registros.importe_debe, --  insercion volcada de estos registros
                              v_registros.importe_recurso, --  insercion volcada de estos registros
                              v_registros.importe_gasto, --  insercion volcada de estos registros

                              p_id_usuario,
                              now(),
                              null,
                              null,
                              v_registros.id_orden_trabajo,
                              v_registros.tipo_cambio,
                              v_registros.tipo_cambio_2,
                              v_registros.tipo_cambio_3,
                              v_registros.id_moneda,
                              v_registros.id_moneda_tri,
                              v_registros.id_moneda_act,
                              v_registros.importe_haber_mb,--  insercion volcada de estos registros
                              v_registros.importe_debe_mb,
                              v_registros.importe_gasto_mb,--  insercion volcada de estos registros
                              v_registros.importe_recurso_mb,

                              v_registros.importe_haber_mt,--  insercion volcada de estos registros
                              v_registros.importe_debe_mt,
                              v_registros.importe_recurso_mt, --  insercion volcada de estos registros

                              v_registros.importe_gasto_mt,
                              v_registros.triangulacion ,
                              v_registros.actualizacion,
                              v_id_partida_ejecucion,
                              v_registros.id_partida_ejecucion_dev

                          )RETURNING id_int_transaccion into v_id_int_transaccion;


                           --  si el comprobante tiene relaciones de devenago ...(aolo si es un cbte de pago)
                           --  asociamos el pago al nuevo comprobante
                           --  con montos negativos

                           FOR  v_registros_dev in (
                                                    select
                                                      ird.id_int_rel_devengado,
                                                      ird.monto_pago,
                                                      ird.monto_pago_mb,
                                                      ird.monto_pago_mt,
                                                      ird.id_int_transaccion_dev,
                                                      it.id_partida_ejecucion_dev,
                                                      it.importe_reversion,
                                                      it.factor_reversion,
                                                      it.monto_pagado_revertido,
                                                      ic.fecha,
                                                      it.id_partida_ejecucion_rev,
                                                      p.codigo as codigo_partida,
                                                      it.id_centro_costo as id_presupuesto,
                                                      ird.id_partida_ejecucion_pag

                                                    from  conta.tint_rel_devengado ird
                                                    inner join conta.tint_transaccion it
                                                      on it.id_int_transaccion = ird.id_int_transaccion_dev
                                                    inner join pre.tpartida p on p.id_partida = it.id_partida

                                                    inner join conta.tint_comprobante ic on ic.id_int_comprobante = it.id_int_comprobante
                                                    where  ird.id_int_transaccion_pag = v_registros.id_int_transaccion
                                                           and ird.estado_reg = 'activo'
                                                           and p.sw_movimiento = 'presupuestaria'
                                                   ) LOOP


                                          insert into conta.tint_rel_devengado(
                                              id_int_transaccion_pag,
                                              id_int_transaccion_dev,
                                              monto_pago,
                                              monto_pago_mb,
                                              monto_pago_mt,
                                              estado_reg,
                                              id_usuario_ai,
                                              fecha_reg,
                                              usuario_ai,
                                              id_usuario_reg,
                                              sw_reversion,
                                              id_partida_ejecucion_pag
                                           ) values(
                                              v_id_int_transaccion,
                                              v_registros_dev.id_int_transaccion_dev,
                                              v_registros_dev.monto_pago*(-1),
                                              v_registros_dev.monto_pago_mb*(-1),
                                              v_registros_dev.monto_pago_mt*(-1),
                                              'activo',
                                              v_parametros._id_usuario_ai,
                                              now(),
                                              v_parametros._nombre_usuario_ai,
                                              p_id_usuario,
                                              'si',
                                              v_registros_dev.id_partida_ejecucion_pag
                                          ) ;

                                  --isnerta relacion de devengado con la reversion
                                  --marcando el sw de reversion

                          END LOOP;

                  END LOOP;

                  --marcar el cbte original como volcado
                  update conta.tint_comprobante c set
                    volcado = 'si'
                  where c.id_int_comprobante =  v_parametros.id_int_comprobante;

                  IF v_parametros.sw_validar = 'si' then
                      --solictar validacion del comprobante
                      v_result = conta.f_validar_cbte(p_id_usuario,
                                                         v_parametros._id_usuario_ai,
                                                         v_parametros._nombre_usuario_ai,
                                                         v_id_int_comprobante,
                                                         'si');

                      v_resp = pxp.f_agrega_clave(v_resp,'mensaje','fue volcado y validado el cbte : id '||v_parametros.id_int_comprobante::varchar);

                  else
                     v_resp = pxp.f_agrega_clave(v_resp,'mensaje','fue volcado en borrador el cbte : id '||v_parametros.id_int_comprobante::varchar);
                  end if;



                  /*Aqui cambiamos el estado del plan de pago a un estado anterior*/
                  --Actualizamos el estado de convertido cuando se revierta el comprobante
                  --Ismael Valdivia 11/01/2022
                  --Se regresara el estado a todos los comprobantes revertidos
                  /*select pp.convertido into v_convertido
                  from tes.tplan_pago pp
                  where pp.id_int_comprobante = v_parametros.id_int_comprobante;*/

                  --if (v_convertido = 'si') then


                          select pp.id_estado_wf
                                 into
                                 v_id_estado_wf
                          from tes.tplan_pago pp
                          where pp.id_int_comprobante = v_parametros.id_int_comprobante;

                          SELECT

                                   ps_id_tipo_estado,
                                   ps_id_funcionario,
                                   ps_id_usuario_reg,
                                   ps_id_depto,
                                   ps_codigo_estado,
                                   ps_id_estado_wf_ant
                                into
                                   v_id_tipo_estado,
                                   v_id_funcionario,
                                   v_id_usuario_reg,
                                   v_id_depto,
                                   v_codigo_estado,
                                   v_id_estado_wf_ant
                                FROM wf.f_obtener_estado_ant_log_wf(v_id_estado_wf);

                     --configurar acceso directo para la alarma
                         v_acceso_directo = '';
                         v_clase = '';
                         v_parametros_ad = '';
                         v_tipo_noti = 'notificacion';
                         v_titulo  = 'Reversion de Comprobante';

                      -- registra nuevo estado

                      v_id_estado_actual = wf.f_registra_estado_wf(
                          v_id_tipo_estado,                --  id_tipo_estado al que retrocede
                          v_id_funcionario,                --  funcionario del estado anterior
                          v_id_estado_wf,       		   --  estado actual ...
                          v_id_proceso_wf,                 --  id del proceso actual
                          p_id_usuario,                    -- usuario que registra
                          v_parametros._id_usuario_ai,
                          v_parametros._nombre_usuario_ai,
                          v_id_depto,                       --depto del estado anterior
                          '[Comprobante Revertido]',
                          v_acceso_directo,
                          v_clase,
                          v_parametros_ad,
                          v_tipo_noti,
                          v_titulo);


                          update tes.tplan_pago set
                          id_estado_wf = v_id_estado_actual,
                          estado = v_codigo_estado,
                          convertido = 'no'
                          where id_int_comprobante = v_parametros.id_int_comprobante;

                  --end if;


                  -----------------------------------------------------------------------------------
                  /****************************************************************/



                  --Definicion de la respuesta

                  v_resp = pxp.f_agrega_clave(v_resp,'id_int_comprobante',v_parametros.id_int_comprobante::varchar);

                  --Devuelve la respuesta
                  return v_resp;

              end;

              /*********************************
              #TRANSACCION:  'CONTA_CBTEPER_IME'
              #DESCRIPCION:	Genera nuevo cbte para las perdidas e igualar importes por cuestion del tipo de cambio
              #AUTOR:		Maylee Perez Pastor
              #FECHA:		24-3-2020 00:28:30
              ***********************************/

              elsif(p_transaccion='CONTA_CBTEPER_IME')then

                  begin
				      --datos del cbte elegido
                      select *
                      into v_reg_cbte
                      from conta.tint_comprobante ic
                      where ic.id_int_comprobante = v_parametros.id_int_comprobante;

                     --datos del comprobante de la solicitud
                     select *
                     into v_reg_cbte_sol
                     from conta.tint_comprobante ic
                     where ic.id_plantilla_comprobante = 19 -- SOLFONDAV
                     and ic.id_subsistema = v_reg_cbte.id_subsistema
                     and ic.nro_tramite = v_reg_cbte.nro_tramite
                     and ic.id_depto  = v_reg_cbte.id_depto;


			-- si los tipos de cambio son distintos por cuestion de rendicion con otra fecha, estos variarian su importe
              IF (v_reg_cbte.tipo_cambio_2 != v_reg_cbte_sol.tipo_cambio_2) THEN

                      --validar que el periodo se encuentre abierto
                      IF not param.f_periodo_subsistema_abierto(v_reg_cbte.fecha::date, 'CONTA') THEN
                          raise exception 'El periodo se encuentra cerrado en contabilidad para la fecha:  %',v_reg_cbte.fecha;
                      END IF;


                   /*   select rc.id_tipo_relacion_comprobante
                      into v_id_tipo_relacion_comprobante
                      from conta.ttipo_relacion_comprobante rc
                      where rc.codigo = 'IGUALAR';
					*/


                      select cc.tipo_comprobante, cc.descripcion
                      into   v_tipo_comprobante, v_clcbt_desc
                      from conta.tclase_comprobante cc
                      where cc.id_clase_comprobante = v_reg_cbte.id_clase_comprobante;


                      v_id_clase_comprobante = v_reg_cbte.id_clase_comprobante;


                      --control de fecha del comprobante

                      v_periodo_fecha_cbte = date_part('month',v_reg_cbte.fecha);
                      v_periodo_anio_cbte = date_part('year',v_reg_cbte.fecha);


                      SELECT ges.id_gestion
                      INTO v_gestion_cbte
                      FROM param.tgestion ges
                      WHERE ges.gestion = v_periodo_anio_cbte;

                      SELECT per.fecha_ini, per.fecha_fin
                      INTO v_fecha_ini,v_fecha_fin
                      FROM param.tperiodo per
                      WHERE per.periodo = v_periodo_fecha_cbte
                      and per.id_gestion = v_gestion_cbte;

                      v_periodo_mes_now = date_part('month',now());
                      v_periodo_anio_now = date_part('year',now());

                      IF (v_periodo_fecha_cbte != v_periodo_mes_now and v_periodo_anio_cbte != v_periodo_anio_now ) THEN
                            IF (v_reg_cbte.fecha < now()::date and v_reg_cbte.fecha > v_fecha_ini and (v_reg_cbte.fecha < v_fecha_fin or v_reg_cbte.fecha = v_fecha_fin)) THEN

                                  v_fecha  = v_fecha_fin;
                            ELSIF (v_reg_cbte.fecha < now()::date and v_reg_cbte.fecha> now()::date and v_reg_cbte.fecha > v_fecha_ini and (v_reg_cbte.fecha < v_fecha_fin or v_reg_cbte.fecha = v_fecha_fin)) THEN

                                  v_fecha = now()::date;

                            ELSIF (v_reg_cbte.fecha = now()::date and v_reg_cbte.fecha > v_fecha_ini and (v_reg_cbte.fecha < v_fecha_fin or v_reg_cbte.fecha = v_fecha_fin)) THEN

                                  v_fecha = now()::date;

                            ELSIF (v_reg_cbte.fecha > now()::date and v_reg_cbte.fecha > v_fecha_ini and (v_reg_cbte.fecha < v_fecha_fin or v_reg_cbte.fecha = v_fecha_fin)) THEN

                                  v_fecha = now()::date;

                            ELSE
                                  raise exception 'verificar fechas cbtes';

                            END IF;
                      ELSE
                            IF (v_reg_cbte.fecha < now()::date and v_reg_cbte.fecha > v_fecha_ini and (v_reg_cbte.fecha < v_fecha_fin or v_reg_cbte.fecha = v_fecha_fin)) THEN

                                  v_fecha  = now();
                            ELSIF (v_reg_cbte.fecha < now()::date and v_reg_cbte.fecha> now()::date and v_reg_cbte.fecha > v_fecha_ini and (v_reg_cbte.fecha < v_fecha_fin or v_reg_cbte.fecha = v_fecha_fin)) THEN

                                  v_fecha = now()::date;

                            ELSIF (v_reg_cbte.fecha = now()::date and v_reg_cbte.fecha > v_fecha_ini and (v_reg_cbte.fecha < v_fecha_fin or v_reg_cbte.fecha = v_fecha_fin)) THEN

                                  v_fecha = now()::date;

                            ELSIF (v_reg_cbte.fecha > now()::date and v_reg_cbte.fecha > v_fecha_ini and (v_reg_cbte.fecha < v_fecha_fin or v_reg_cbte.fecha = v_fecha_fin)) THEN

                                  v_fecha = now()::date;

                            ELSE
                                  raise exception 'verificar fechas cbtes';

                            END IF;
                      END IF;

                       --raise exception 'llega % < % and % > % and %<%',v_reg_cbte.fecha,  now()::date, v_reg_cbte.fecha, v_fecha_ini,v_reg_cbte.fecha,v_fecha_fin ;

                      --

                    --  inicia tramite nuevo
                    v_codigo_proceso_macro = pxp.f_get_variable_global('conta_codigo_macro_wf_cbte');

                    --obtener id del proceso macro
                    select
                     pm.id_proceso_macro
                    into
                     v_id_proceso_macro
                    from wf.tproceso_macro pm
                    where pm.codigo = v_codigo_proceso_macro;

                    If v_id_proceso_macro is NULL THEN
                      raise exception 'El proceso macro  de codigo % no esta configurado en el sistema WF',v_codigo_proceso_macro;
                    END IF;

                   --   obtener el codigo del tipo_proceso
                    select   tp.codigo
                     into v_codigo_tipo_proceso
                    from  wf.ttipo_proceso tp
                    where   tp.id_proceso_macro = v_id_proceso_macro
                          and tp.estado_reg = 'activo' and tp.inicio = 'si';

                    IF v_codigo_tipo_proceso is NULL THEN
                     raise exception 'No existe un proceso inicial para el proceso macro indicado % (Revise la configuración)',v_codigo_proceso_macro;
                    END IF;

                    --PERIODO  Obtiene el periodo a partir de la fecha
        			v_rec = param.f_get_periodo_gestion(v_fecha);


                  -- inciar el tramite en el sistema de WF
                    SELECT
                       ps_num_tramite ,
                       ps_id_proceso_wf ,
                       ps_id_estado_wf ,
                       ps_codigo_estado
                      into
                       v_num_tramite,
                       v_id_proceso_wf,
                       v_id_estado_wf,
                       v_codigo_estado

                    FROM wf.f_inicia_tramite(
                       p_id_usuario,
                       v_parametros._id_usuario_ai,
                       v_parametros._nombre_usuario_ai,
                       v_rec.po_id_gestion,
                       v_codigo_tipo_proceso,
                       null,--v_parametros.id_funcionario,
                       4,--v_reg_cbte.id_depto,
                       'Registro de Cbte manual',
                       '' );


                    IF  v_codigo_estado != 'borrador' THEN
                      raise exception 'el estado inicial para cbtes debe ser borrador, revise la configuración del WF';
                    END IF;
                    --



                      -----------------------------
                      --REGISTRO DEL COMPROBANTE
                      -----------------------------
                      insert into conta.tint_comprobante(
                          id_clase_comprobante,
                          id_subsistema,
                          id_depto,
                          id_moneda,
                          id_periodo,
                          id_funcionario_firma1,
                          id_funcionario_firma2,
                          id_funcionario_firma3,
                          tipo_cambio,
                          beneficiario,
                          estado_reg,
                          glosa1,
                          fecha,
                          glosa2,
                          --momento,
                          id_usuario_reg,
                          fecha_reg,
                          id_usuario_mod,
                          fecha_mod,
                          id_usuario_ai,
                          usuario_ai,
                          id_int_comprobante_fks,
                          cbte_cierre,
                          cbte_apertura,
                          cbte_aitb,
                          manual,
                          momento_comprometido,
                          momento_ejecutado,
                          momento_pagado,
                          momento,
                          id_tipo_relacion_comprobante,
                          fecha_costo_ini,
                          fecha_costo_fin,
                          id_config_cambiaria,
                          tipo_cambio_2,
                          localidad,
                          id_moneda_tri,
                          nro_tramite,
                          sw_editable,
                          sw_tipo_cambio,
                          cbte_reversion,
                          id_proceso_wf,
                          id_estado_wf,
                          forma_cambio,
                          tipo_cambio_3,
                          id_moneda_act
                      ) values(
                        v_id_clase_comprobante,
                        10, --v_reg_cbte.id_subsistema, --contabilidad
                        4, --depto central cochabamba - v_reg_cbte.id_depto,
                        1, --v_reg_cbte.id_moneda,
                        v_reg_cbte.id_periodo,
                        v_reg_cbte.id_funcionario_firma1,
                        v_reg_cbte.id_funcionario_firma2,
                        v_reg_cbte.id_funcionario_firma3,
                        v_reg_cbte.tipo_cambio,
                        v_reg_cbte.beneficiario,
                        'borrador',
                        --'IGUALAR CBTE PERDIDA ('||v_reg_cbte.nro_cbte||',  id:'||v_reg_cbte.id_int_comprobante||' )',
                        'CBTE POR LA DIFERENCIA DEL TIPO DE CAMBIO ('||v_reg_cbte.nro_cbte||',  id:'||v_reg_cbte.id_int_comprobante||' )',
                        v_fecha, --v_reg_cbte.fecha,
                        v_reg_cbte.glosa2,
                        --v_parametros.momento,
                        p_id_usuario,
                        now(),
                        null,
                        null,
                        v_parametros._id_usuario_ai,
                        v_parametros._nombre_usuario_ai,
                        (string_to_array(v_parametros.id_int_comprobante::varchar,','))::INTEGER[],
                        v_reg_cbte.cbte_cierre,
                        v_reg_cbte.cbte_apertura,
                        v_reg_cbte.cbte_aitb,
                        'no',
                        'no', --v_reg_cbte.momento_comprometido,
                        'no', --v_reg_cbte.momento_ejecutado,
                        'no', --v_reg_cbte.momento_pagado,
                        'presupuestario', --v_reg_cbte.momento,
                        null, --v_id_tipo_relacion_comprobante,
                        v_reg_cbte.fecha_costo_ini,
                        v_reg_cbte.fecha_costo_fin,
                        v_reg_cbte.id_config_cambiaria,
                        v_reg_cbte.tipo_cambio_2,
                        v_reg_cbte.localidad,
                        v_reg_cbte.id_moneda_tri,
                        v_num_tramite,
                        'si',  -- sw_editable
                        v_reg_cbte.sw_tipo_cambio, -- RAC 05/12/2016 ....  'si', -- sw_tipo_cambio
                        'si', -- cbte_reversion	, marcamos como cbte de reversion
                        v_id_proceso_wf,
                        v_id_estado_wf,
                        v_reg_cbte.forma_cambio,
                        v_reg_cbte.tipo_cambio_3,
                        v_reg_cbte.id_moneda_act
                      )RETURNING id_int_comprobante into v_id_int_comprobante;


                     update wf.tproceso_wf p set
                      descripcion = descripcion||' ('||v_clcbt_desc||'id:'||v_id_int_comprobante::varchar||')'
                     where p.id_proceso_wf = v_id_proceso_wf;


                     -- insertar las dos transacciones

                     select sum(it.importe_debe_mb)
                     into v_importe_debe_sol
                     from conta.tint_transaccion it
                     where  it.estado_reg = 'activo' and
                     it.id_int_comprobante = v_reg_cbte_sol.id_int_comprobante;


                     select sum(it.importe_debe_mb)
                     into v_importe_debe
                     from conta.tint_transaccion it
                     where  it.estado_reg = 'activo' and
                     it.id_int_comprobante = v_parametros.id_int_comprobante;

        			--raise exception 'llegasi % - % ',v_importe_debe_sol, v_importe_debe;

        			 IF (v_importe_debe_sol != v_importe_debe) THEN
                     	v_importe_total_debe = v_importe_debe_sol - v_importe_debe;
                     END IF;

                     -- importe triangulacion
                     select tc.oficial
                     into v_ofi_tri
                     from param.ttipo_cambio tc
                     where 	tc.id_moneda = 2
                     and tc.fecha =  v_fecha;

                     v_mon_tri = v_importe_total_debe / v_ofi_tri;

                     -- importe act
                     select tc.oficial
                     into v_ofi_act
                     from param.ttipo_cambio tc
                     where 	tc.id_moneda = 3
                     and tc.fecha =  v_fecha;

                     v_mon_act = v_importe_total_debe / v_ofi_act;


                                  ---------------------------------
                                  --REGISTRO DE LA TRANSACCIÓN DEBE
                                  ---------------------------------

                                  insert into conta.tint_transaccion(
                                      id_partida,
                                      id_centro_costo,
                                      estado_reg,
                                      id_cuenta,
                                      glosa,
                                      id_int_comprobante,
                                      id_auxiliar,

                                      importe_debe,
                                      importe_haber,
                                      importe_gasto,
                                      importe_recurso,

                                      id_usuario_reg,
                                      fecha_reg,
                                      id_usuario_mod,
                                      fecha_mod,
                                      id_orden_trabajo,
                                      tipo_cambio,
                                      tipo_cambio_2,
                                      tipo_cambio_3,
                                      id_moneda,
                                      id_moneda_tri,
                                      id_moneda_act,
                                      importe_debe_mb,
                                      importe_haber_mb,
                                      importe_recurso_mb,
                                      importe_gasto_mb,

                                      importe_debe_mt,
                                      importe_haber_mt,
                                      importe_gasto_mt,
                                      importe_recurso_mt,

                                      importe_debe_ma,
                                      importe_haber_ma,
                                      importe_gasto_ma,
                                      importe_recurso_ma,

                                      triangulacion ,
                                      actualizacion,
                                      id_partida_ejecucion,
                                      id_partida_ejecucion_dev

                                  ) values(
                                      11856, --v_registros.id_partida,
                                      1152, --v_registros.id_centro_costo,
                                      'activo',
                                      29210, --v_registros.id_cuenta,
                                      '',  --v_registros.glosa,
                                      v_id_int_comprobante,  --referencia del cbte
                                      2737, --v_registros.id_auxiliar,

                                      0, 					--v_registros.importe_haber,
                                      v_importe_total_debe, --v_registros.importe_debe,
                                      0, 					--v_registros.importe_recurso,
                                      v_importe_total_debe, --v_registros.importe_gasto,

                                      p_id_usuario,
                                      now(),
                                      null,
                                      null,
                                      28, --v_registros.id_orden_trabajo,
                                      1,					--v_registros.tipo_cambio, --tipo de cambio 1 porq es de la misma moneda base
                                      v_ofi_tri,    	    --v_registros.tipo_cambio_2,
                                      v_mon_act,			--v_registros.tipo_cambio_3,
                                      1, --v_registros.id_moneda,
                                      2, --v_registros.id_moneda_tri,
                                      3, --v_registros.id_moneda_act,

                                      0, 					--v_registros.importe_haber_mb,
                                      v_importe_total_debe, --v_registros.importe_debe_mb,
                                      v_importe_total_debe, --v_registros.importe_gasto_mb,
                                      0, 					--v_registros.importe_recurso_mb,

                                      0, 					--v_registros.importe_haber_mt,
                                      v_mon_tri,            -- v_registros.importe_debe_mt,
                                      0, 					--v_registros.importe_recurso_mt,
                                      v_mon_tri,			--v_registros.importe_gasto_mt,

									  0, 					--v_registros.importe_haber_ma,
                                      v_mon_act,            -- v_registros.importe_debe_ma,
                                      0, 					--v_registros.importe_recurso_ma,
                                      v_mon_act,			--v_registros.importe_gasto_ma,

                                      'no', --v_registros.triangulacion ,
                                      'no', --v_registros.actualizacion,
                                      null, --v_id_partida_ejecucion,
                                      null  --v_registros.id_partida_ejecucion_dev

                                  )RETURNING id_int_transaccion into v_id_int_transaccion;

								  -----------------------------------
                                  --REGISTRO DE LA TRANSACCIÓN HABER
                                  -----------------------------------
          			    		  insert into conta.tint_transaccion(
                                      id_partida,
                                      id_centro_costo,
                                      estado_reg,
                                      id_cuenta,
                                      glosa,
                                      id_int_comprobante,
                                      id_auxiliar,

                                      importe_debe,
                                      importe_haber,
                                      importe_gasto,
                                      importe_recurso,

                                      id_usuario_reg,
                                      fecha_reg,
                                      id_usuario_mod,
                                      fecha_mod,
                                      id_orden_trabajo,
                                      tipo_cambio,
                                      tipo_cambio_2,
                                      tipo_cambio_3,
                                      id_moneda,
                                      id_moneda_tri,
                                      id_moneda_act,
                                      importe_debe_mb,
                                      importe_haber_mb,
                                      importe_recurso_mb,
                                      importe_gasto_mb,

                                      importe_debe_mt,
                                      importe_haber_mt,
                                      importe_gasto_mt,
                                      importe_recurso_mt,

                                      importe_debe_ma,
                                      importe_haber_ma,
                                      importe_gasto_ma,
                                      importe_recurso_ma,

                                      triangulacion ,
                                      actualizacion,
                                      id_partida_ejecucion,
                                      id_partida_ejecucion_dev

                                  ) values(
                                      12105, --v_registros.id_partida,
                                      1135, --v_registros.id_centro_costo,
                                      'activo',
                                      28991, --v_registros.id_cuenta,
                                      '',  --v_registros.glosa,
                                      v_id_int_comprobante,  --referencia del cbte
                                      1880, --v_registros.id_auxiliar,

                                      v_importe_total_debe, 	--v_registros.importe_haber,
                                      0,					--v_registros.importe_debe,
                                      v_importe_total_debe,	--v_registros.importe_recurso,
                                      0, 					--v_registros.importe_gasto,

                                      p_id_usuario,
                                      now(),
                                      null,
                                      null,
                                      28, --v_registros.id_orden_trabajo,
                                      1,					--v_registros.tipo_cambio, --tipo de cambio 1 porq es de la misma moneda base
                                      v_ofi_tri,    	    --v_registros.tipo_cambio_2,
                                      v_mon_act,			--v_registros.tipo_cambio_3,
                                      1, --v_registros.id_moneda,
                                      2, --v_registros.id_moneda_tri,
                                      3, --v_registros.id_moneda_act,

                                      v_importe_total_debe, --v_registros.importe_haber_mb,
                                      0, 					--v_registros.importe_debe_mb,
                                      0, 					--v_registros.importe_gasto_mb,
                                      v_importe_total_debe, --v_registros.importe_recurso_mb,

                                      v_mon_tri, 			--v_registros.importe_haber_mt,
                                      0,            		-- v_registros.importe_debe_mt,
                                      v_mon_tri, 			--v_registros.importe_recurso_mt,
                                      0,					--v_registros.importe_gasto_mt,

									  v_mon_act, 			--v_registros.importe_haber_ma,
                                      0,            		-- v_registros.importe_debe_ma,
                                      v_mon_act, 			--v_registros.importe_recurso_ma,
                                      0,					--v_registros.importe_gasto_ma,

                                      'no', --v_registros.triangulacion ,
                                      'no', --v_registros.actualizacion,
                                      null, --v_id_partida_ejecucion,
                                      null  --v_registros.id_partida_ejecucion_dev

                                  )RETURNING id_int_transaccion into v_id_int_transaccion;



           ELSE
                  		raise exception 'El Tipo de Cambio no varia de los comprobantes, (%).', v_reg_cbte.nro_tramite;

              END IF;

                      --Definicion de la respuesta

                      v_resp = pxp.f_agrega_clave(v_resp,'id_int_comprobante',v_parametros.id_int_comprobante::varchar);

                      --Devuelve la respuesta
                      return v_resp;

                  end;


                  /*********************************
                  #TRANSACCION:  'CONTA_CBTEINCR_IME'
                  #DESCRIPCION:	Genera nuevo cbte para los incrementos e igualar importes por cuestion del tipo de cambio
                  #AUTOR:		Maylee Perez Pastor
                  #FECHA:		24-3-2020 00:28:30
                  ***********************************/

                  elsif(p_transaccion='CONTA_CBTEINCR_IME')then

                      begin

                      raise exception 'No puede generar el nuevo comprobante';

                      end;
      /*********************************
      #TRANSACCION:  'CONTA_DESV_CBT_IME'
      #DESCRIPCION:	Devalida un comprobante
      #AUTOR:		frankin.espinoza
      #FECHA:		17-12-2020 00:28:30
      ***********************************/

      elsif(p_transaccion='CONTA_DESV_CBT_IME')then

        begin
          --raise 'validado: %',v_parametros.id_proceso_wf;
            select
              cbte.id_int_comprobante,
              cbte.id_estado_wf,
              cbte.id_proceso_wf,
              cbte.id_usuario_ai,
              cbte.usuario_ai,
              cbte.momento,
              cbte.manual,
              coalesce(cbte.tipo_cbte,'nacional') tipo_cbte
              into v_registros_int_cbte
            from conta.tint_comprobante cbte
            where cbte.id_proceso_wf = v_parametros.id_proceso_wf and cbte.estado_reg = 'validado';

            SELECT
               cbte.id_estado_wf,

              pw.id_tipo_proceso,
              pw.id_proceso_wf

             into
              v_id_estado_wf,
              v_id_tipo_proceso,
              v_id_proceso_wf

             FROM conta.tint_comprobante cbte
             inner join wf.tproceso_wf pw on pw.id_proceso_wf = cbte.id_proceso_wf
             inner join wf.testado_wf ewf on ewf.id_estado_wf = cbte.id_estado_wf
             WHERE  cbte.id_proceso_wf = v_parametros.id_proceso_wf;


            if v_registros_int_cbte.id_int_comprobante is not null then
                if v_registros_int_cbte.momento = 'contable' then
                    select lib.id_proceso_wf, lib.id_libro_bancos
                    into v_libro_bancos
                    from tes.tts_libro_bancos  lib
                    where lib.id_int_comprobante = v_registros_int_cbte.id_int_comprobante;


                    delete from wf.testado_wf
                    where id_proceso_wf = v_libro_bancos.id_proceso_wf;

                    delete from wf.tproceso_wf
                    where id_proceso_wf = v_libro_bancos.id_proceso_wf;

                    delete from tes.tts_libro_bancos
                    where id_libro_bancos = v_libro_bancos.id_libro_bancos;

                end if;



                delete from pre.tpartida_ejecucion
                where id_int_comprobante = v_registros_int_cbte.id_int_comprobante;

                if v_registros_int_cbte.manual = 'si' and v_registros_int_cbte.tipo_cbte = 'internacional' then
                  update conta.tint_transaccion set
                      id_partida_ejecucion = null
                  where id_int_comprobante = v_registros_int_cbte.id_int_comprobante;
                end if;

                update conta.tint_comprobante set
                    nro_cbte = null
                where id_int_comprobante = v_registros_int_cbte.id_int_comprobante;

                SELECT
                 ps_id_tipo_estado,
                 ps_codigo_estado
               	 into
                 v_id_tipo_estado,
                 v_codigo_estado
               	FROM wf.f_obtener_tipo_estado_inicial_del_tipo_proceso(v_id_tipo_proceso);

                --------------------------------------------------
                --Retrocede al estado inmediatamente anterior
                -------------------------------------------------
                --recuperaq estado anterior segun Log del WF


                SELECT
                   ps_id_funcionario,
                   ps_codigo_estado ,
                   ps_id_depto
                into
                  v_id_funcionario,
                  v_codigo_estado,
                  v_id_depto
                FROM wf.f_obtener_estado_segun_log_wf(v_id_estado_wf, v_id_tipo_estado);



                --configurar acceso directo para la alarma
                v_acceso_directo = '';
                v_clase = '';
                v_parametros_ad = '';
                v_tipo_noti = 'notificacion';
                v_titulo  = 'Notificacion';


                IF   v_codigo_estado_siguiente not in('borrador','supconta','vbconta','finalizado')   THEN
                      v_acceso_directo = '../../../sis_contabilidad/vista/entrega/Entrega.php';
                      v_clase = 'Entrega';
                      v_parametros_ad = '{filtro_directo:{campo:"conta.id_proceso_wf",valor:"'||v_parametros.id_proceso_wf_act::varchar||'"}}';
                      v_tipo_noti = 'notificacion';
                      v_titulo  = 'Notificacion';
                 END IF;


              	-- registra nuevo estado

                v_id_estado_actual = wf.f_registra_estado_wf(
                v_id_tipo_estado,
                v_id_funcionario,
                v_registros_int_cbte.id_estado_wf,
                v_id_proceso_wf,
                p_id_usuario,
                v_registros_int_cbte.id_usuario_ai,
                v_registros_int_cbte.usuario_ai,
                v_id_depto,
                '[RETROCESO] Corrección datos',
                v_acceso_directo,
                v_clase,
                v_parametros_ad,
                v_tipo_noti,
                v_titulo);

                update conta.tint_comprobante   set
                 id_estado_wf =  v_id_estado_actual,
                 estado_reg = v_codigo_estado,
                 id_usuario_mod = p_id_usuario,
                 fecha_mod = now(),
                 id_usuario_ai = v_parametros._id_usuario_ai,
                 usuario_ai = v_parametros._nombre_usuario_ai
                where id_proceso_wf = v_parametros.id_proceso_wf;

		  	end if;
          --Definicion de la respuesta
          v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Comprobante desvalidado con exito (id_int_comprobante'||v_registros_int_cbte.id_int_comprobante||')');
          v_resp = pxp.f_agrega_clave(v_resp,'id_int_comprobante',v_registros_int_cbte.id_int_comprobante::varchar);

          --Devuelve la respuesta
          return v_resp;
      end;

    else

    	raise exception 'Transaccion inexistente: %',p_transaccion;

	end if;

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

ALTER FUNCTION conta.ft_int_comprobante_ime (p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
  OWNER TO postgres;
