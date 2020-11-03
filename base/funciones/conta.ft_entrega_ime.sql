CREATE OR REPLACE FUNCTION conta.ft_entrega_ime (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Sistema de Contabilidad
 FUNCION: 		conta.ft_entrega_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'conta.tentrega'
 AUTOR: 		 (admin)
 FECHA:	        17-11-2016 19:50:19
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
	v_id_requerimiento     	integer;
	v_resp		            varchar;
	v_nombre_funcion        text;
	v_mensaje_error         text;
	v_id_entrega			integer;
    v_ids					varchar[];
    v_i						integer;
    v_id_entrega_det		integer;
    v_c31					varchar;
    v_registros_ent			record;
    v_registros				record;
    v_sw_tmp				boolean;
    v_registros_aux			record;
    v_cont					integer;
    v_aux					boolean;
    v_valor					varchar;

     v_entrega	record;
     v_pedir_obs		    	varchar;
     v_id_tipo_estado		integer;
     v_id_estado_wf			integer;
     v_obs					text;
     v_id_depto				integer;
     v_id_estado_actual	integer;


     v_acceso_directo  	varchar;
    v_clase   			varchar;
    v_parametros_ad   	varchar;
    v_tipo_noti  		varchar;
    v_titulo   			varchar;
     v_codigo_estado_siguiente varchar;
     v_registros_proc	record;
     v_id_proceso_wf			integer;

    v_codigo_estado 		varchar;
     v_codigo_tipo_pro	varchar;
    v_codigo_llave		varchar;

    v_codigo_tipo_proceso 	varchar;
    v_id_proceso_macro    	integer;
     v_gestion 				integer;
     v_nro_tramite			varchar;

     v_estados  varchar;
      v_id_funcionario	integer;
    v_id_usuario_reg	integer;
    v_id_estado_wf_ant	integer;
     v_registros_en		record;
     v_estado_wf_com    integer;

     v_reg_cbte record;
     v_ids_entregas			integer[];
	 v_entg					integer;
     v_is_presupuestaria	integer;
	 v_par_pre				integer := 0;
     v_par_flu				integer := 0;

     v_registros_int_cbte	record;
     v_prioridad_cbte		integer;

     v_fecha_cbte			date;

     --franklin.espinoza 28/09/2020 --entrega cbtes diferenciado por clase de gasto.
     v_cbt_clase_gasto_m		record;
     v_cbt_clase_gasto_d		record;
     v_clase_gasto				integer=0;

     v_comprobante_mixto		varchar='';
     v_id_int_comprobante		integer;
     v_filtro_mixto				varchar;
     v_contador_validado		integer=0;
	 v_total_validado			integer=0;
     v_tipo_comprobante			varchar;
     v_consulta_entrega			varchar = '';
     v_consulta_entrega_d		varchar = '';
     v_tipo_entrega				varchar;

     v_entrega_validado			varchar;
BEGIN

    v_nombre_funcion = 'conta.ft_entrega_ime';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'CONTA_ENT_ELI'
 	#DESCRIPCION:	Eliminacion de registros
 	#AUTOR:		admin
 	#FECHA:		17-11-2016 19:50:19
	***********************************/

	if(p_transaccion='CONTA_ENT_ELI')then

		begin
			--Sentencia de la eliminacion

            select
               *
            into
              v_registros_ent
            from conta.tentrega en
            where en.id_entrega =v_parametros.id_entrega;

            if v_registros_ent.estado != 'borrador' then
               raise exception 'Solo puede borrar entregas en estado borrador';
            end if;


            FOR v_registros in (
            						select *
                                    from conta.tentrega_det ed
                                    where ed.id_entrega =  v_parametros.id_entrega
                                 ) LOOP

                      --> quita la relacion con la entrega
                      update conta.tint_comprobante  set
                         c31 = NULL,
                         fecha_c31 = NULL
                      where id_int_comprobante = v_registros.id_int_comprobante;

             END LOOP;

             delete from conta.tentrega_det
             where id_entrega=v_parametros.id_entrega;

			 delete from conta.tentrega
             where id_entrega=v_parametros.id_entrega;

            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Entrega eliminado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_entrega',v_parametros.id_entrega::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

    /****************************************
 	#TRANSACCION:  'CONTA_CRENT_INS'
 	#DESCRIPCION:	Creación de entregas, se ejecuta desde la interface de libro diario
 	#AUTOR:		RAC KPLIAN
 	#FECHA:		17-11-2016 19:50:19
	************************************************/

	elsif(p_transaccion='CONTA_CRENT_INS')then

		begin
        	--verificar que el comprobante este asociado a una partida presupuestaria
        	v_ids_entregas = string_to_array(v_parametros.id_int_comprobantes,',');

           FOREACH v_entg IN ARRAY v_ids_entregas
           LOOP
             select count(comprobantes.*)
              into v_is_presupuestaria
              from (
              SELECT inc.id_int_comprobante
              FROM   conta.vint_comprobante inc
                     inner join param.tmoneda mon ON mon.id_moneda = inc.id_moneda
                     inner join param.tperiodo per ON per.id_periodo = inc.id_periodo
                     inner join conta.tclase_comprobante cc ON cc.id_clase_comprobante = inc.id_clase_comprobante
                     join conta.tint_comprobante cbt ON cbt.id_int_comprobante = inc.id_int_comprobante
                     join conta.tint_transaccion trp ON trp.id_int_comprobante = cbt.id_int_comprobante
                     join pre.tpartida par ON par.id_partida = trp.id_partida
              WHERE  inc.id_int_comprobante = v_entg
                     AND par.sw_movimiento = 'presupuestaria'
              UNION
              SELECT inc.id_int_comprobante
              FROM   conta.vint_comprobante inc
                     inner join param.tmoneda mon ON mon.id_moneda = inc.id_moneda
                     inner join param.tperiodo per ON per.id_periodo = inc.id_periodo
                     inner join conta.tclase_comprobante cc ON cc.id_clase_comprobante = inc.id_clase_comprobante
                     join conta.tint_comprobante cbt ON cbt.id_int_comprobante = inc.id_int_comprobante
                     join tes.tplan_pago pg  ON pg.id_int_comprobante = cbt.id_int_comprobante
                     join tes.tplan_pago dev ON dev.id_plan_pago = pg.id_plan_pago_fk
                     join conta.tint_transaccion trd ON trd.id_int_comprobante = dev.id_int_comprobante
                     join pre.tpartida par ON par.id_partida = trd.id_partida
              WHERE  inc.id_int_comprobante = v_entg
                     AND par.sw_movimiento = 'presupuestaria') comprobantes;

              IF v_is_presupuestaria<=0 THEN
              	v_par_flu := v_par_flu + 1;
              ELSE
              	v_par_pre := v_par_pre + 1;
              END IF;
           END LOOP;

		   if v_par_pre > 0 and v_par_flu > 0 then
             raise exception 'Error no es posible mezclar comprobantes que no ejecutan presupuesto con los que si ejecutan.';
           end if;
           select tp.codigo, pm.id_proceso_macro
           into   v_codigo_tipo_proceso, v_id_proceso_macro
           from  wf.tproceso_macro pm
           inner join wf.ttipo_proceso tp on tp.id_proceso_macro = pm.id_proceso_macro
           where pm.codigo='CON-EN' and tp.estado_reg = 'activo' and tp.inicio = 'si';

           --Obtenemos la gestion
           select g.id_gestion
           into v_gestion
           from param.tgestion g
           where g.gestion = EXTRACT(YEAR FROM current_date);


           SELECT i.id_estado_wf
                    into v_estado_wf_com
		   from conta.tint_comprobante i
           WHERE i.id_int_comprobante::VARCHAR = ANY(string_to_array(v_parametros.id_int_comprobantes,','));

           ------------------------------
            --registra procesos disparados
            ------------------------------

           	SELECT
            ps_id_proceso_wf,
            ps_id_estado_wf,
            ps_codigo_estado
            into
            v_id_proceso_wf,
            v_id_estado_wf,
            v_codigo_estado
            FROM wf.f_registra_proceso_disparado_wf(
                               p_id_usuario,
                               v_parametros._id_usuario_ai,
                               v_parametros._nombre_usuario_ai,
                               v_estado_wf_com,
                               NULL,
                               NULL,
                               NULL,
                               --v_registros_proc.obs_pro,
                               'CONEN',
                               'CONEN');
            if v_parametros.total_cbte < 1 then
                raise exception 'No tiene comprobantes para entregar';
             end if;



             v_ids = string_to_array(v_parametros.id_int_comprobantes,',');
             v_i = 1;

            SELECT cbte.fecha
            INTO	v_fecha_cbte
            FROM  conta.tint_comprobante cbte
            WHERE cbte.id_int_comprobante::integer = v_ids[v_i]::integer;

           insert into conta.tentrega(
                      fecha_c31,
                      c31,
                      estado,
                      estado_reg,
                      id_usuario_ai,
                      usuario_ai,
                      fecha_reg,
                      id_usuario_reg,
                      fecha_mod,
                      id_usuario_mod,
                      id_depto_conta,
                      id_proceso_wf,
					  id_estado_wf
                  ) values(
                      --17-03-2020 (may) modificacion para que inserte con la fecha de los cbtes
                      --now(),
                      v_fecha_cbte,
                      '',
                      v_codigo_estado, --> estado de la entrega
                      'activo',
                      v_parametros._id_usuario_ai,
                      v_parametros._nombre_usuario_ai,
                      now(),
                      p_id_usuario,
                      null,
                      null,
                      v_parametros.id_depto_conta,
                      v_id_proceso_wf,
                      v_id_estado_wf
			)RETURNING id_entrega into v_id_entrega;
            --


            WHILE v_i <= v_parametros.total_cbte LOOP

                 --  valida que no tenga ninguna entrega ya configurada
                 v_c31 = null;
                 select
                    c.c31
                  into
                    v_c31
                 from conta.tint_comprobante c
                 where c.id_int_comprobante =   v_ids[v_i]::integer;

                 if v_c31 is not null  and trim(v_c31) != ''   then
                    raise exception 'El comprobantes (id: %)  ya se encuentra relacionado con la entrega o C31: %', v_ids[v_i] ,v_c31;
                 end if;

                 --Sentencia de la insercion
                insert into conta.tentrega_det(
                    estado_reg,
                    id_int_comprobante,
                    id_entrega,
                    id_usuario_reg,
                    fecha_reg,
                    usuario_ai,
                    id_usuario_ai,
                    id_usuario_mod,
                    fecha_mod
                ) values(
                    'activo',
                    v_ids[v_i]::integer,
                    v_id_entrega,
                    p_id_usuario,
                    now(),
                    v_parametros._nombre_usuario_ai,
                    v_parametros._id_usuario_ai,
                    null,
                    null
                )RETURNING id_entrega_det into v_id_entrega_det;

                --  temporalmente marca el cbte relacionado a la entrega
                update conta.tint_comprobante  set
                    c31 =  'ENT ID:'||v_id_entrega::varchar,
                    fecha_c31 = now()
                where id_int_comprobante = v_ids[v_i]::integer;

                v_i = v_i + 1;

            END LOOP;


            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Creación de entrega contable para los cbte '||v_parametros.id_int_comprobantes);
            v_resp = pxp.f_agrega_clave(v_resp,'id_entrega',v_id_entrega::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

/****************************************
 	#TRANSACCION:  'CONTA_FINENTR_INS'
 	#DESCRIPCION:	Fin de la entrega
 	#AUTOR:		RAC KPLIAN
 	#FECHA:		17-11-2016 19:50:19
	************************************************/

	elsif(p_transaccion='CONTA_FINENTR_INS')then

		begin


			--17-06-2020 (may) se quita porque todos se realizaran de una misma relacion
			/*if(v_parametros.id_tipo_relacion_comprobante is null or v_parametros.id_tipo_relacion_comprobante = 0)then
              raise exception 'El campo "Incluir Relación" no puede ser nulo, por favor elegir una opción';
            end if;	*/
            --

            --  verificar que la estrega este en estado  borrador
            select
              *
            into
              v_registros
            from conta.tentrega en
            where en.id_entrega = v_parametros.id_entrega;


            --  actualizar el la fecha y nro de c31 en la entrega, c31 y fecha  en los cbte

            update conta.tentrega set
              c31 = v_parametros.c31,
              fecha_c31 = v_parametros.fecha_c31,
              obs = v_parametros.obs
              --17-06-2020 (may) se quita porque todos se realizaran de una misma relacion
              --id_tipo_relacion_comprobante = v_parametros.id_tipo_relacion_comprobante
            where id_entrega = v_parametros.id_entrega;

            v_sw_tmp = true;
            --  lista todos los cbtes a entrega
            v_cont = 0;
            FOR v_registros_ent in (
            						select
                                       ed.id_entrega,
                                       ed.id_entrega_det,
                                       cbte.id_int_comprobante,
                                       cbte.id_tipo_relacion_comprobante,
                                       cbte.id_int_comprobante_fks
                                    from conta.tentrega_det ed
                                    inner join conta.tint_comprobante cbte on cbte.id_int_comprobante = ed.id_int_comprobante
                                    where ed.id_entrega =  v_parametros.id_entrega
                                 ) LOOP
                  --  actulizar los combronbastes relacionados


                  --  temporalmente marca el cbte relacionado a la entrega
                  update conta.tint_comprobante  set
                      c31 =  v_parametros.c31,
                      fecha_c31 = v_parametros.fecha_c31
                  where id_int_comprobante = v_registros_ent.id_int_comprobante;

                  --17-06-2020 (may) se quita porque todos se realizaran de una misma relacion
                  /*--  si existe relacion,  identificar los comprobantes relacionados SIN NRO DE C31
                  IF v_parametros.id_tipo_relacion_comprobante is not null AND v_parametros.id_tipo_relacion_comprobante = v_registros_ent.id_tipo_relacion_comprobante THEN


                       --  actualizar los comprobantes relacionados
                        FOR v_registros_aux in (select
                                                  cbte.id_int_comprobante,
                                                  cbte.c31
                                              from conta.tint_comprobante cbte
                                              where cbte.id_int_comprobante = ANY(v_registros_ent.id_int_comprobante_fks)) LOOP



                                --actulizamos solo si no tiene un C31 relacionado
                                IF (v_registros_aux.c31 is null or trim(v_registros_aux.c31) = '') THEN

                                      --actuliza cbte relacionado
                                      update conta.tint_comprobante  set
                                          c31 =  v_parametros.c31,
                                          fecha_c31 = v_parametros.fecha_c31
                                      where id_int_comprobante = v_registros_aux.id_int_comprobante;

                                 END IF;

                                v_cont = v_cont + 1;
                        END LOOP;

                  END IF;
				  */

				  --16-06-2020 (may) para que este realice la actualizacion a su cbte de diario y de pago

                      FOR v_registros_aux in (select
                                                      cbte.id_int_comprobante,
                                                      cbte.c31
                                                  from conta.tint_comprobante cbte
                                                  where v_registros_ent.id_int_comprobante =  ANY(cbte.id_int_comprobante_fks) ) LOOP

                                          --actualiza cbte relacionado
                                          update conta.tint_comprobante  set
                                              c31 =  v_parametros.c31,
                                              fecha_c31 = v_parametros.fecha_c31
                                          where id_int_comprobante = v_registros_aux.id_int_comprobante;


                                    v_cont = v_cont + 1;
                       END LOOP;

                  --


                     --  IF verificar si es necesario llamar a libro de bancos
                     v_valor = param.f_get_depto_param( v_registros.id_depto_conta, 'ENTREGA');

                     IF v_valor = 'SI' THEN
                         -- llamda a libro de bnacos
                         v_aux = tes.f_integracion_libro_bancos(p_id_usuario,v_registros_ent.id_int_comprobante); --si es una esntrega

                     END IF;

                     --  marca si tiene cbtes
                     v_sw_tmp = false;

             END LOOP;


            --s no tiene cbtes lanza un error
            IF v_sw_tmp THEN
               raise exception 'no tiene ningun comprobante';
            END IF;


            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','cambio de estado para la entrega id: '||v_parametros.id_entrega);
            v_resp = pxp.f_agrega_clave(v_resp,'id_entrega',v_id_entrega::varchar);
            v_resp = pxp.f_agrega_clave(v_resp,'v_cont',v_cont::varchar);


            --Devuelve la respuesta
            return v_resp;

		end;

    /*********************************
 	#TRANSACCION:  'CONTA_SIG_INS'
 	#DESCRIPCION:	Siguiente Estado
 	#AUTOR:		MMV
 	#FECHA:		18-01-2017 11:32:59
	***********************************/

    elseif(p_transaccion='CONTA_SIG_INS') then
    	begin

         --recupera toda la tabla solicitud

     	SELECT en.*
        into v_entrega
        FROM conta.tentrega en
        where en.id_proceso_wf = v_parametros.id_proceso_wf_act;

          select
            ew.id_tipo_estado,
            te.pedir_obs,
            ew.id_estado_wf
           into
            v_id_tipo_estado,
            v_pedir_obs,
            v_id_estado_wf

          from wf.testado_wf ew
          inner join wf.ttipo_estado te on te.id_tipo_estado = ew.id_tipo_estado
          where ew.id_estado_wf =  v_parametros.id_estado_wf_act;


           -- obtener datos tipo estado siguiente //codigo=borrador
           select te.codigo into
             v_codigo_estado_siguiente
           from wf.ttipo_estado te
           where te.id_tipo_estado = v_parametros.id_tipo_estado;


           IF  pxp.f_existe_parametro(p_tabla,'id_depto_wf') THEN
           	 v_id_depto = v_parametros.id_depto_wf;
           END IF;

           IF  pxp.f_existe_parametro(p_tabla,'obs') THEN
           	 v_obs = v_parametros.obs;
           ELSE
           	 v_obs='---';
           END IF;

             --configurar acceso directo para la alarma
             v_acceso_directo = '';
             v_clase = '';
             v_parametros_ad = '';
             v_tipo_noti = 'notificacion';
             v_titulo  = 'Entrega';

          --10-03-2020 (may) modificacion para entregas , cuando este este finalizado los de prioridad 3 (internacionales), se validen automaticamente cada una.
          --comentado y reemplazado franklin.espinoza 20/10/2020
          /*select de.prioridad
          into v_prioridad_cbte
          from conta.tentrega_det ed
          inner join conta.tentrega ent on ent.id_entrega = ed.id_entrega
          inner join conta.tint_comprobante cbte on cbte.id_int_comprobante = ed.id_int_comprobante
          JOIN param.tdepto de ON de.id_depto = ent.id_depto_conta
          where ed.id_entrega =  v_entrega.id_entrega;*/

          --franklin.espinoza 20/10/2020 consulta anterior devuelve un conjunto de registros lo correcto es un solo valor
		  	select de.prioridad, coalesce(ent.validado,'no')
			into v_prioridad_cbte, v_entrega_validado
            from conta.tentrega ent
            inner join param.tdepto de on de.id_depto = ent.id_depto_conta
            where ent.id_entrega =  v_entrega.id_entrega;

            IF v_codigo_estado_siguiente = 'finalizado' and v_entrega.c31 = '' THEN

            	raise exception 'Debe ingresar numero de c31 antes de finalizar la entrega';

			--28/10/2020 franklin.espinoza se adiciona la regla -> v_entrega_validado = 'no' para los comprobantes de regularizacion
            ELSIF (v_codigo_estado_siguiente in ('finalizado') and v_prioridad_cbte = 3 and v_entrega_validado = 'no') THEN --10-03-2020 (may) prioridad 3 para las internacionales, se valida cbte cuando la entrega finaliza

                FOR v_registros_int_cbte in ( select ed.id_entrega,
                                                     cbte.id_int_comprobante,
                                                     de.prioridad
                                              from conta.tentrega_det ed
                                              inner join conta.tentrega ent on ent.id_entrega = ed.id_entrega
                                              inner join conta.tint_comprobante cbte on cbte.id_int_comprobante = ed.id_int_comprobante
                                              JOIN param.tdepto de ON de.id_depto = ent.id_depto_conta
                                              where ed.id_entrega =  v_entrega.id_entrega
                                             ) LOOP
                 --raise exception 'llega %',v_registros_int_cbte.id_int_comprobante;

                                 v_resp = conta.f_validar_cbte(p_id_usuario,
                                                               v_parametros._id_usuario_ai,
                                                               v_parametros._nombre_usuario_ai,
                                                               v_registros_int_cbte.id_int_comprobante,
                                                               'si');

                END LOOP;

           END IF;

            IF   v_codigo_estado_siguiente not in('borrador','supconta','vbconta','finalizado')THEN

                  v_acceso_directo = '../../../sis_contabilidad/vista/entrega/Entrega.php';
                  v_clase = 'Entrega';
                  v_parametros_ad = '{filtro_directo:{campo:"conta.id_proceso_wf",valor:"'||
                  v_parametros.id_proceso_wf_act::varchar||'"}}';
                  v_tipo_noti = 'notificacion';
                  v_titulo  = 'Notificacion';
             END IF;


             -- hay que recuperar el supervidor que seria el estado inmediato...
            	v_id_estado_actual =  wf.f_registra_estado_wf(v_parametros.id_tipo_estado,
                                                             v_parametros.id_funcionario_wf,
                                                             v_parametros.id_estado_wf_act,
                                                             v_parametros.id_proceso_wf_act,
                                                             p_id_usuario,
                                                             v_parametros._id_usuario_ai,
                                                             v_parametros._nombre_usuario_ai,
                                                             v_id_depto,
                                                             ' Obs:'||v_obs,
                                                             v_acceso_directo,
                                                             v_clase,
                                                             v_parametros_ad,
                                                             v_tipo_noti,
                                                             v_titulo);


         		IF conta.f_procesar_estados_entrega(p_id_usuario,
           											v_parametros._id_usuario_ai,
                                            		v_parametros._nombre_usuario_ai,
                                            		v_id_estado_actual,
                                            		v_parametros.id_proceso_wf_act,
                                            		v_codigo_estado_siguiente) THEN

         			RAISE NOTICE 'PASANDO DE ESTADO';

          		END IF;



          --------------------------------------
          -- registra los procesos disparados
          --------------------------------------

          FOR v_registros_proc in ( select * from json_populate_recordset(null::wf.proceso_disparado_wf, v_parametros.json_procesos::json)) LOOP

               --get cdigo tipo proceso
               select
                  tp.codigo,
                  tp.codigo_llave
               into
                  v_codigo_tipo_pro,
                  v_codigo_llave
               from wf.ttipo_proceso tp
                where  tp.id_tipo_proceso =  v_registros_proc.id_tipo_proceso_pro;


               -- disparar creacion de procesos seleccionados

              SELECT
                       ps_id_proceso_wf,
                       ps_id_estado_wf,
                       ps_codigo_estado
                 into
                       v_id_proceso_wf,
                       v_id_estado_wf,
                       v_codigo_estado
              FROM wf.f_registra_proceso_disparado_wf(
                       p_id_usuario,
                       v_parametros._id_usuario_ai,
                       v_parametros._nombre_usuario_ai,
                       v_id_estado_actual,
                       v_registros_proc.id_funcionario_wf_pro,
                       v_registros_proc.id_depto_wf_pro,
                       v_registros_proc.obs_pro,
                       v_codigo_tipo_pro,
                       v_codigo_tipo_pro);

           END LOOP;

          -- si hay mas de un estado disponible  preguntamos al usuario
          v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Se realizo el cambio de estado de Solicitud)');
          v_resp = pxp.f_agrega_clave(v_resp,'operacion','cambio_exitoso');
          v_resp = pxp.f_agrega_clave(v_resp,'v_codigo_estado_siguiente',v_codigo_estado_siguiente);

          -- Devuelve la respuesta
          return v_resp;
        end;

/*********************************
 	#TRANSACCION:  'CONTA_ANT_INS'
 	#DESCRIPCION:	Retrosede un estado anterior
 	#AUTOR:		MMA
 	#FECHA:		20-01-2017
	***********************************/

	elseif(p_transaccion='CONTA_ANT_INS')then
        begin

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


           --
          select
               ew.id_proceso_wf
            into
               v_id_proceso_wf
          from wf.testado_wf ew
          where ew.id_estado_wf= v_id_estado_wf_ant;


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
              v_parametros.id_estado_wf,
              v_id_proceso_wf,
              p_id_usuario,
              v_parametros._id_usuario_ai,
              v_parametros._nombre_usuario_ai,
              v_id_depto,
              '[RETROCESO] '|| v_parametros.obs,
              v_acceso_directo,
              v_clase,
              v_parametros_ad,
              v_tipo_noti,
              v_titulo);




            IF  not conta.f_ant_estado_entrega_wf(p_id_usuario,
                                                   v_parametros._id_usuario_ai,
                                                   v_parametros._nombre_usuario_ai,
                                                   v_id_estado_actual,
                                                   v_parametros.id_proceso_wf,
                                                   v_codigo_estado) THEN

               raise exception 'Error al retroceder estado';

            END IF;


         -- si hay mas de un estado disponible  preguntamos al usuario
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Se realizo el cambio de estado)');
            v_resp = pxp.f_agrega_clave(v_resp,'operacion','cambio_exitoso');


          --Devuelve la respuesta
            return v_resp;





        end;
    /****************************************
 	#TRANSACCION:  'CONTA_CRENT_SIG_INS'
 	#DESCRIPCION:	Creación de entregas, se ejecuta desde la interface de libro diario
    #AUTOR:			franklin.espinoza
 	#FECHA:		22-09-2020 10:50:19
	************************************************/

	elsif(p_transaccion='CONTA_CRENT_SIG_INS')then

		begin
        	--verificar que el comprobante este asociado a una partida presupuestaria
        	v_ids_entregas = string_to_array(v_parametros.id_int_comprobantes,',');

            FOREACH v_entg IN ARRAY v_ids_entregas LOOP

             	select count(comprobantes.*)
                into v_is_presupuestaria
                from (
                SELECT inc.id_int_comprobante
                FROM   conta.vint_comprobante inc
                       inner join param.tmoneda mon ON mon.id_moneda = inc.id_moneda
                       inner join param.tperiodo per ON per.id_periodo = inc.id_periodo
                       inner join conta.tclase_comprobante cc ON cc.id_clase_comprobante = inc.id_clase_comprobante
                       join conta.tint_comprobante cbt ON cbt.id_int_comprobante = inc.id_int_comprobante
                       join conta.tint_transaccion trp ON trp.id_int_comprobante = cbt.id_int_comprobante
                       join pre.tpartida par ON par.id_partida = trp.id_partida
                WHERE  inc.id_int_comprobante = v_entg
                       AND par.sw_movimiento = 'presupuestaria'
                UNION
                SELECT inc.id_int_comprobante
                FROM   conta.vint_comprobante inc
                       inner join param.tmoneda mon ON mon.id_moneda = inc.id_moneda
                       inner join param.tperiodo per ON per.id_periodo = inc.id_periodo
                       inner join conta.tclase_comprobante cc ON cc.id_clase_comprobante = inc.id_clase_comprobante
                       join conta.tint_comprobante cbt ON cbt.id_int_comprobante = inc.id_int_comprobante
                       join tes.tplan_pago pg  ON pg.id_int_comprobante = cbt.id_int_comprobante
                       join tes.tplan_pago dev ON dev.id_plan_pago = pg.id_plan_pago_fk
                       join conta.tint_transaccion trd ON trd.id_int_comprobante = dev.id_int_comprobante
                       join pre.tpartida par ON par.id_partida = trd.id_partida
                WHERE  inc.id_int_comprobante = v_entg
                       AND par.sw_movimiento = 'presupuestaria') comprobantes;

              IF v_is_presupuestaria<=0 THEN
                v_par_flu := v_par_flu + 1;
              ELSE
                v_par_pre := v_par_pre + 1;
              END IF;

        	END LOOP;

           	if v_par_pre > 0 and v_par_flu > 0 then
             	raise exception 'Error no es posible mezclar comprobantes que no ejecutan presupuesto con los que si ejecutan.';
           	end if;

           	if v_parametros.total_cbte < 1 then
                raise exception 'No tiene comprobantes para entregar';
            end if;

            execute('select distinct tc.tipo_comprobante
            from conta.tint_comprobante tic
            inner join conta.tclase_comprobante tc on tc.id_clase_comprobante = tic.id_clase_comprobante
            where tic.id_int_comprobante in ('||v_parametros.id_int_comprobantes||')') into v_tipo_comprobante;

			--generar consulta de acuerdo al tipo de comprobante
			if v_tipo_comprobante = 'contable' then
            	v_consulta_entrega = 'select distinct tic.id_int_comprobante, cg.codigo::integer clase_gasto
                                      from conta.tint_comprobante tic
                                      inner join conta.tint_transaccion transa on transa.id_int_comprobante = tic.id_int_comprobante and transa.importe_debe != 0
                                      inner join conta.tcuenta cue on cue.id_cuenta = transa.id_cuenta
                                      inner join pre.tclase_gasto_cuenta tcgc on tcgc.id_cuenta = cue.id_cuenta
                                      inner join pre.tclase_gasto cg ON cg.id_clase_gasto= tcgc.id_clase_gasto
                                      where tic.id_int_comprobante in ('||v_parametros.id_int_comprobantes||')
                                      order by tic.id_int_comprobante asc';
            elsif v_tipo_comprobante = 'presupuestario' then
            	v_consulta_entrega = 'select distinct tic.id_int_comprobante, cg.codigo::integer clase_gasto
                  					  from conta.tint_comprobante tic
                                      inner join conta.tint_transaccion transa on transa.id_int_comprobante = tic.id_int_comprobante
                                      inner join pre.tpartida par on par.id_partida = transa.id_partida
                                      inner join pre.tclase_gasto_partida tcgp ON tcgp.id_partida = par.id_partida
                                      inner join pre.tclase_gasto cg ON cg.id_clase_gasto= tcgp.id_clase_gasto
                                      where tic.id_int_comprobante in ('||v_parametros.id_int_comprobantes||')
                                      order by tic.id_int_comprobante asc';
            end if;

			for v_cbt_clase_gasto_m in  execute(v_consulta_entrega) loop
				if v_id_int_comprobante is not null then
                  if v_cbt_clase_gasto_m.id_int_comprobante = v_id_int_comprobante then
                      v_comprobante_mixto = v_comprobante_mixto||v_cbt_clase_gasto_m.id_int_comprobante||',';
                  end if;
                end if;

                v_id_int_comprobante = v_cbt_clase_gasto_m.id_int_comprobante;
            end loop;

            v_comprobante_mixto = trim(v_comprobante_mixto,',');
            v_filtro_mixto = '';
            v_tipo_entrega = v_parametros.tipo||'_una_cg';

            if v_comprobante_mixto != '' then
            	v_filtro_mixto = ' and tic.id_int_comprobante not in ('||v_comprobante_mixto||') ';
                v_tipo_entrega = v_parametros.tipo||'_mas_cg';
            end if;

            --generar consulta de acuerdo al tipo de comprobante
            if v_tipo_comprobante = 'contable' then
            	v_consulta_entrega = 'select distinct tic.id_int_comprobante, cg.codigo::integer clase_gasto, tic.glosa1
                					  from conta.tint_comprobante tic
                                      inner join conta.tint_transaccion transa on transa.id_int_comprobante = tic.id_int_comprobante and transa.importe_debe != 0
                                      inner join conta.tcuenta cue on cue.id_cuenta = transa.id_cuenta
                                      inner join pre.tclase_gasto_cuenta tcgc on tcgc.id_cuenta = cue.id_cuenta
                                      inner join pre.tclase_gasto cg ON cg.id_clase_gasto= tcgc.id_clase_gasto
                                      where tic.id_int_comprobante in ('||v_parametros.id_int_comprobantes||')'||v_filtro_mixto||'
                                      order by cg.codigo::integer asc';
            elsif v_tipo_comprobante = 'presupuestario' then
            	v_consulta_entrega = 'select distinct tic.id_int_comprobante, cg.codigo::integer clase_gasto, tic.glosa1
                					  from conta.tint_comprobante tic
                                      inner join conta.tint_transaccion transa on transa.id_int_comprobante = tic.id_int_comprobante
                                      inner join pre.tpartida par on par.id_partida = transa.id_partida
                                      inner join pre.tclase_gasto_partida tcgp ON tcgp.id_partida = par.id_partida
                                      inner join pre.tclase_gasto cg ON cg.id_clase_gasto= tcgp.id_clase_gasto
                                      where tic.id_int_comprobante in ('||v_parametros.id_int_comprobantes||')'||v_filtro_mixto||'
                                      order by cg.codigo::integer asc';
            end if;


          	for v_cbt_clase_gasto_m in  execute(v_consulta_entrega) loop --raise 'v_comprobante_mixto:a %', v_cbt_clase_gasto_m.clase_gasto;
            	if v_cbt_clase_gasto_m.clase_gasto != v_clase_gasto then

                    select tp.codigo, pm.id_proceso_macro
                    into   v_codigo_tipo_proceso, v_id_proceso_macro
                    from  wf.tproceso_macro pm
                    inner join wf.ttipo_proceso tp on tp.id_proceso_macro = pm.id_proceso_macro
                    where pm.codigo='CON-EN' and tp.estado_reg = 'activo' and tp.inicio = 'si';

                    --Obtenemos la gestion
                    select g.id_gestion into v_gestion
                    from param.tgestion g
                    where g.gestion = EXTRACT(YEAR FROM current_date);

                    SELECT i.id_estado_wf into v_estado_wf_com
                    from conta.tint_comprobante i
                    WHERE i.id_int_comprobante::VARCHAR = ANY(string_to_array(v_parametros.id_int_comprobantes,','));

                    ------------------------------
                    --registra procesos disparados
                    ------------------------------
                    SELECT ps_id_proceso_wf, ps_id_estado_wf, ps_codigo_estado
                    into   v_id_proceso_wf, v_id_estado_wf, v_codigo_estado
                    FROM wf.f_registra_proceso_disparado_wf( p_id_usuario, v_parametros._id_usuario_ai, v_parametros._nombre_usuario_ai,
                                       						 v_estado_wf_com, NULL, NULL, NULL, 'CONEN', 'CONEN');

                    SELECT cbte.fecha INTO	v_fecha_cbte
                    FROM  conta.tint_comprobante cbte
                    WHERE cbte.id_int_comprobante::integer = v_cbt_clase_gasto_m.id_int_comprobante;


                	insert into conta.tentrega(
                            fecha_c31,
                            c31,
                            estado,
                            estado_reg,
                            id_usuario_ai,
                            usuario_ai,
                            fecha_reg,
                            id_usuario_reg,
                            fecha_mod,
                            id_usuario_mod,
                            id_depto_conta,
                            id_proceso_wf,
                            id_estado_wf,
                            tipo,
                            glosa
                    ) values(
                            v_fecha_cbte,
                            '',
                            v_codigo_estado, --> estado de la entrega
                            'activo',
                            v_parametros._id_usuario_ai,
                            v_parametros._nombre_usuario_ai,
                            now(),
                            p_id_usuario,
                            null,
                            null,
                            v_parametros.id_depto_conta,
                            v_id_proceso_wf,
                            v_id_estado_wf,
                            v_tipo_entrega,
                            v_cbt_clase_gasto_m.glosa1
                  	)RETURNING id_entrega into v_id_entrega;

                    --generar consulta de acuerdo al tipo de comprobante
                    if v_tipo_comprobante = 'contable' then
                    	v_consulta_entrega = 'select distinct tic.id_int_comprobante, cg.codigo::integer clase_gasto
                                             from conta.tint_comprobante tic
                                             inner join conta.tint_transaccion transa on transa.id_int_comprobante = tic.id_int_comprobante and transa.importe_debe != 0
                                             inner join conta.tcuenta cue on cue.id_cuenta = transa.id_cuenta
                                      		 inner join pre.tclase_gasto_cuenta tcgc on tcgc.id_cuenta = cue.id_cuenta
                                      		 inner join pre.tclase_gasto cg ON cg.id_clase_gasto= tcgc.id_clase_gasto
                                             where tic.id_int_comprobante in ('||v_parametros.id_int_comprobantes||')'||v_filtro_mixto||'
                                             and cg.codigo::integer = '||v_cbt_clase_gasto_m.clase_gasto||'
                                             order by cg.codigo::integer asc';
                    elsif v_tipo_comprobante = 'presupuestario' then

                        v_consulta_entrega = 'select distinct tic.id_int_comprobante, cg.codigo::integer clase_gasto
                                             from conta.tint_comprobante tic
                                             inner join conta.tint_transaccion transa on transa.id_int_comprobante = tic.id_int_comprobante
                                             inner join pre.tpartida par on par.id_partida = transa.id_partida
                                             inner join pre.tclase_gasto_partida tcgp ON tcgp.id_partida = par.id_partida
                                             inner join pre.tclase_gasto cg ON cg.id_clase_gasto= tcgp.id_clase_gasto
                                             where tic.id_int_comprobante in ('||v_parametros.id_int_comprobantes||')'||v_filtro_mixto||'
                                             and cg.codigo::integer = '||v_cbt_clase_gasto_m.clase_gasto||'
                                             order by cg.codigo::integer asc';
                    end if;

                    for v_cbt_clase_gasto_d in  execute(v_consulta_entrega) loop
						--raise 'v_cbt_clase_gasto_d: b %', v_cbt_clase_gasto_d;
                         --valida que no tenga ninguna entrega ya configurada
                         v_c31 = null;
                         select c.c31 into v_c31
                         from conta.tint_comprobante c
                         where c.id_int_comprobante =   v_cbt_clase_gasto_d.id_int_comprobante;

                         if v_c31 is not null  and trim(v_c31) != ''   then
                            raise exception 'El comprobantes (A) (id: %)  ya se encuentra relacionado con la entrega o C31: %', v_cbt_clase_gasto_d.id_int_comprobante ,v_c31;
                         end if;

                         --Sentencia de la insercion
                        insert into conta.tentrega_det(
                            estado_reg,
                            id_int_comprobante,
                            id_entrega,
                            id_usuario_reg,
                            fecha_reg,
                            usuario_ai,
                            id_usuario_ai,
                            id_usuario_mod,
                            fecha_mod
                        ) values(
                            'activo',
                            v_cbt_clase_gasto_d.id_int_comprobante,
                            v_id_entrega,
                            p_id_usuario,
                            now(),
                            v_parametros._nombre_usuario_ai,
                            v_parametros._id_usuario_ai,
                            null,
                            null
                        )RETURNING id_entrega_det into v_id_entrega_det;

                        --  temporalmente marca el cbte relacionado a la entrega
                        update conta.tint_comprobante  set
                            c31 =  'ENT ID:'||v_id_entrega::varchar,
                            fecha_c31 = now()
                        where id_int_comprobante = v_cbt_clase_gasto_d.id_int_comprobante;

                  END LOOP;
            	end if;

                v_clase_gasto = v_cbt_clase_gasto_m.clase_gasto;

            end loop;--for clase de gasto

			if v_comprobante_mixto != '' then
            	for v_cbt_clase_gasto_m in  execute('select distinct tic.id_int_comprobante, tic.glosa1
            								   from conta.tint_comprobante tic
                                               where tic.id_int_comprobante in ('||v_comprobante_mixto||')
                                               order by tic.id_int_comprobante asc') loop


                    select tp.codigo, pm.id_proceso_macro
                    into   v_codigo_tipo_proceso, v_id_proceso_macro
                    from  wf.tproceso_macro pm
                    inner join wf.ttipo_proceso tp on tp.id_proceso_macro = pm.id_proceso_macro
                    where pm.codigo='CON-EN' and tp.estado_reg = 'activo' and tp.inicio = 'si';

                    --Obtenemos la gestion
                    select g.id_gestion into v_gestion
                    from param.tgestion g
                    where g.gestion = EXTRACT(YEAR FROM current_date);

                    SELECT i.id_estado_wf into v_estado_wf_com
                    from conta.tint_comprobante i
                    WHERE i.id_int_comprobante::VARCHAR = ANY(string_to_array(v_comprobante_mixto,','));

                    ------------------------------
                    --registra procesos disparados
                    ------------------------------
                    SELECT ps_id_proceso_wf, ps_id_estado_wf, ps_codigo_estado
                    into   v_id_proceso_wf, v_id_estado_wf, v_codigo_estado
                    FROM wf.f_registra_proceso_disparado_wf( p_id_usuario, v_parametros._id_usuario_ai, v_parametros._nombre_usuario_ai,
                                       						 v_estado_wf_com, NULL, NULL, NULL, 'CONEN', 'CONEN');

                    SELECT cbte.fecha INTO	v_fecha_cbte
                    FROM  conta.tint_comprobante cbte
                    WHERE cbte.id_int_comprobante::integer = v_cbt_clase_gasto_m.id_int_comprobante;


                	insert into conta.tentrega(
                            fecha_c31,
                            c31,
                            estado,
                            estado_reg,
                            id_usuario_ai,
                            usuario_ai,
                            fecha_reg,
                            id_usuario_reg,
                            fecha_mod,
                            id_usuario_mod,
                            id_depto_conta,
                            id_proceso_wf,
                            id_estado_wf,
                            tipo,
                            glosa
                    ) values(
                            v_fecha_cbte,
                            '',
                            v_codigo_estado, --> estado de la entrega
                            'activo',
                            v_parametros._id_usuario_ai,
                            v_parametros._nombre_usuario_ai,
                            now(),
                            p_id_usuario,
                            null,
                            null,
                            v_parametros.id_depto_conta,
                            v_id_proceso_wf,
                            v_id_estado_wf,
                            v_tipo_entrega,
                            v_cbt_clase_gasto_m.glosa1
                  	)RETURNING id_entrega into v_id_entrega;

                   --valida que no tenga ninguna entrega ya configurada
                   v_c31 = null;
                   select c.c31 into v_c31
                   from conta.tint_comprobante c
                   where c.id_int_comprobante =   v_cbt_clase_gasto_m.id_int_comprobante;

                   if v_c31 is not null  and trim(v_c31) != ''   then
                      raise exception 'El comprobantes (B) (id: %)  ya se encuentra relacionado con la entrega o C31: %', v_cbt_clase_gasto_m.id_int_comprobante ,v_c31;
                   end if;

                   --Sentencia de la insercion
                  insert into conta.tentrega_det(
                      estado_reg,
                      id_int_comprobante,
                      id_entrega,
                      id_usuario_reg,
                      fecha_reg,
                      usuario_ai,
                      id_usuario_ai,
                      id_usuario_mod,
                      fecha_mod
                  ) values(
                      'activo',
                      v_cbt_clase_gasto_m.id_int_comprobante,
                      v_id_entrega,
                      p_id_usuario,
                      now(),
                      v_parametros._nombre_usuario_ai,
                      v_parametros._id_usuario_ai,
                      null,
                      null
                  )RETURNING id_entrega_det into v_id_entrega_det;

                  --  temporalmente marca el cbte relacionado a la entrega
                  update conta.tint_comprobante  set
                      c31 =  'ENT ID:'||v_id_entrega::varchar,
                      fecha_c31 = now()
                  where id_int_comprobante = v_cbt_clase_gasto_m.id_int_comprobante;

            	end loop;--for clase de gasto
            end if;

            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Creación de entrega contable para los cbte '||v_parametros.id_int_comprobantes);
            v_resp = pxp.f_agrega_clave(v_resp,'id_entrega',v_id_entrega::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

    /*********************************
 	#TRANSACCION:  'CONTA_ENT_MOD'
 	#DESCRIPCION:	Definir la Glosa de una entrega
 	#AUTOR:		franklin.espinoza
 	#FECHA:		20-10-2020
	***********************************/

	elseif(p_transaccion='CONTA_ENT_MOD')then
    	begin
          	update conta.tentrega set
              	glosa = v_parametros.glosa
          	where id_entrega = v_parametros.id_entrega;

          	-- si hay mas de un estado disponible  preguntamos al usuario
          	v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Transacción modificado(a)');
          	v_resp = pxp.f_agrega_clave(v_resp,'id_entrega',v_parametros.id_entrega::varchar);

        	--Devuelve la respuesta
          	return v_resp;
        end;

    /*********************************
 	#TRANSACCION:  'CONTA_VALCBTENT_INS'
 	#DESCRIPCION:	Validar Comprobantes de la entrega
 	#AUTOR:		franklin.espinoza
 	#FECHA:		20-10-2020
	***********************************/

	elseif(p_transaccion='CONTA_VALCBTENT_INS')then
    	begin

        	select  count(ed.id_entrega)
            into v_total_validado
            from conta.tentrega_det ed
            inner join conta.tentrega ent on ent.id_entrega = ed.id_entrega
            inner join conta.tint_comprobante cbte on cbte.id_int_comprobante = ed.id_int_comprobante
            JOIN param.tdepto de ON de.id_depto = ent.id_depto_conta
            where ed.id_entrega =  v_parametros.id_entrega and cbte.estado_reg != 'validado';
                --raise 'validacion: %', v_total_validado;
        	FOR v_registros_int_cbte in ( select
            								ed.id_entrega,
                                            cbte.id_int_comprobante,
                                            de.prioridad,
                                           	ent.id_usuario_ai,
                                            ent.usuario_ai
                                          from conta.tentrega_det ed
                                          inner join conta.tentrega ent on ent.id_entrega = ed.id_entrega
                                          inner join conta.tint_comprobante cbte on cbte.id_int_comprobante = ed.id_int_comprobante
                                          JOIN param.tdepto de ON de.id_depto = ent.id_depto_conta
                                          where ed.id_entrega =  v_parametros.id_entrega and cbte.estado_reg != 'validado' ) LOOP

            	v_resp = conta.f_validar_cbte( p_id_usuario,
                                               v_registros_int_cbte.id_usuario_ai,
                                               v_registros_int_cbte.usuario_ai,
                                               v_registros_int_cbte.id_int_comprobante,
                                               'si'
                							 );
                v_contador_validado = v_contador_validado + 1;

            END LOOP;

            if v_total_validado = v_contador_validado then
            	update conta.tentrega set
              		validado = 'si'
          		where id_entrega = v_parametros.id_entrega;
            else
            	update conta.tentrega set
              		validado = 'no'
          		where id_entrega = v_parametros.id_entrega;
            end if;



          	-- si hay mas de un estado disponible  preguntamos al usuario
          	v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Validación Comprobantes');
          	v_resp = pxp.f_agrega_clave(v_resp,'id_entrega',v_parametros.id_entrega::varchar);
            v_resp = pxp.f_agrega_clave(v_resp,'status',true::varchar);

        	--Devuelve la respuesta
          	return v_resp;
        end;

	/*********************************
 	#TRANSACCION:  'CONTA_DESVALCBTS_INS'
 	#DESCRIPCION:	Desvalidar Comprobantes de la entrega
 	#AUTOR:		franklin.espinoza
 	#FECHA:		30-10-2020
	***********************************/

	elseif(p_transaccion='CONTA_DESVALCBTS_INS')then
    	begin

        	select  count(ed.id_entrega)
            into v_total_validado
            from conta.tentrega_det ed
            inner join conta.tentrega ent on ent.id_entrega = ed.id_entrega
            inner join conta.tint_comprobante cbte on cbte.id_int_comprobante = ed.id_int_comprobante
            JOIN param.tdepto de ON de.id_depto = ent.id_depto_conta
            where ed.id_entrega =  v_parametros.id_entrega and cbte.estado_reg = 'validado';
                --raise 'validacion: %', v_total_validado;
        	FOR v_registros_int_cbte in ( select
            								ed.id_entrega,
                                            cbte.id_int_comprobante,
                                            de.prioridad,
                                           	ent.id_usuario_ai,
                                            ent.usuario_ai,
                                            cbte.id_estado_wf,
                                            cbte.id_proceso_wf
                                          from conta.tentrega_det ed
                                          inner join conta.tentrega ent on ent.id_entrega = ed.id_entrega
                                          inner join conta.tint_comprobante cbte on cbte.id_int_comprobante = ed.id_int_comprobante
                                          JOIN param.tdepto de ON de.id_depto = ent.id_depto_conta
                                          where ed.id_entrega =  v_parametros.id_entrega and cbte.estado_reg = 'validado' ) LOOP

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
                FROM wf.f_obtener_estado_ant_log_wf(v_registros_int_cbte.id_estado_wf);

              	select ew.id_proceso_wf
                into v_id_proceso_wf
              	from wf.testado_wf ew
              	where ew.id_estado_wf= v_id_estado_wf_ant;


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
            	where id_proceso_wf = v_registros_int_cbte.id_proceso_wf;



                v_contador_validado = v_contador_validado + 1;

            END LOOP;

            if v_total_validado = v_contador_validado then
            	update conta.tentrega set
              		validado = 'no'
          		where id_entrega = v_parametros.id_entrega;
            end if;



          	-- si hay mas de un estado disponible  preguntamos al usuario
          	v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Validación Comprobantes');
          	v_resp = pxp.f_agrega_clave(v_resp,'id_entrega',v_parametros.id_entrega::varchar);
            v_resp = pxp.f_agrega_clave(v_resp,'status',true::varchar);

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