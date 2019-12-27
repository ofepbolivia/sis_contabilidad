CREATE OR REPLACE FUNCTION "conta"."ft_plan_cuenta_det_ime" (	
				p_administrador integer, p_id_usuario integer, p_tabla character varying, p_transaccion character varying)
RETURNS character varying AS
$BODY$

/**************************************************************************
 SISTEMA:		Sistema de Contabilidad
 FUNCION: 		conta.ft_plan_cuenta_det_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'conta.tplan_cuenta_det'
 AUTOR: 		 (alan.felipez)
 FECHA:	        25-11-2019 22:17:20
 COMENTARIOS:	
***************************************************************************
 HISTORIAL DE MODIFICACIONES:
#ISSUE				FECHA				AUTOR				DESCRIPCION
 #0				25-11-2019 22:17:20								Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'conta.tplan_cuenta_det'	
 #
 ***************************************************************************/

DECLARE

	v_nro_requerimiento    	integer;
	v_parametros           	record;
	v_id_requerimiento     	integer;
	v_resp		            varchar;
	v_nombre_funcion        text;
	v_mensaje_error         text;
	v_id_plan_cuenta_det	integer;

	v_registros				record;
  v_estado				  varchar;
  v_nombre				  varchar;

    ------variables para la insercion del plan de cuentas nuevo----------
    ------Alan 17/12/2019------
  v_gestion_destino	  	integer;
  v_id_gestion_destino	integer;
  v_registros_pc			  record;
  v_id_cuentas_padre		integer[];
  nivel					        integer;
	v_codigo_aux			    integer;
			    
BEGIN

    v_nombre_funcion = 'conta.ft_plan_cuenta_det_ime';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************    
 	#TRANSACCION:  'CONTA_IPCD_INS'
 	#DESCRIPCION:	Insercion de registros
 	#AUTOR:		alan.felipez	
 	#FECHA:		25-11-2019 22:17:20
	***********************************/

	if(p_transaccion='CONTA_IPCD_INS')then
					
        begin
        	--Sentencia de la insercion
        	--Sentencia de la insercion
        	insert into conta.tplan_cuenta_det(
          estado_reg,
          id_plan_cuenta,
          numero,
          nivel,
          rubro,
          grupo,
          sub_grupo,
          cuenta,
          codigo_cuenta,
          sub_cuenta,
          sub_sub_cuenta,
          auxiliar,
          nombre_cuenta,
          ajuste,
          moneda_ajuste,
          tipo_cuenta,
          moneda,
          tip_cuenta,
          permite_auxiliar,
          cuenta_sigep,
          partida_sigep_debe,
          partida_sigep_haber,
          observaciones,
          relacion_cuenta,--relacion de la anterior gestion
          id_usuario_reg,
          fecha_reg,
          id_usuario_ai,
          usuario_ai,
          id_usuario_mod,
          fecha_mod
                ) values(
          'activo',
          v_parametros.id_plan_cuenta::integer,
          v_parametros.numero,
          v_parametros.nivel,
          v_parametros.rubro,
          v_parametros.grupo,
          v_parametros.sub_grupo,
          v_parametros.cuenta,
          v_parametros.codigo_cuenta,
          v_parametros.sub_cuenta,
          v_parametros.sub_sub_cuenta,
          v_parametros.auxiliar,
          v_parametros.nombre_cuenta,
          v_parametros.ajuste,
          v_parametros.moneda_ajuste,
          v_parametros.tipo_cuenta,
          v_parametros.moneda,
          v_parametros.tip_cuenta,
          v_parametros.permite_auxiliar,
          v_parametros.cuenta_sigep::integer,
          v_parametros.partida_sigep_debe,
          v_parametros.partida_sigep_haber,
          v_parametros.observaciones,
          v_parametros.relacion_cuenta,
          p_id_usuario,
          now(),
          v_parametros._id_usuario_ai,
          v_parametros._nombre_usuario_ai,
          null,
          null



          )RETURNING id_plan_cuenta_det into v_id_plan_cuenta_det;
			
			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','ImportarPlanCuentaDet almacenado(a) con exito (id_plan_cuenta_det'||v_id_plan_cuenta_det||')'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_plan_cuenta_det',v_id_plan_cuenta_det::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

  /*********************************
 	#TRANSACCION:  'CONTA_GEN_PLAN_CTA'
 	#DESCRIPCION:	genera el plan de cuentas para la siguiente gestion a partir del archivo excel ya generado
 	#AUTOR:		alan.felipez
 	#FECHA:		27-11-2019 22:17:20
	***********************************/

	elsif(p_transaccion='CONTA_GEN_PLAN_CTA')then

        begin
          --raise exception 'llega generar plan de cuentas parametros %',v_parametros;

        	--Sentencia de la insercion
            --verificamos que no exista un plan de cuentas para la gestion destino
            select	pc.id_gestion,ges.gestion
            into	v_id_gestion_destino, v_gestion_destino
            from	conta.tplan_cuenta pc
            inner join param.tgestion ges on ges.id_gestion=pc.id_gestion
            where	pc.id_plan_cuenta = v_parametros.id_plan_cuenta;

            if not exists (select 1
              			  from param.tgestion
                          where id_gestion = v_id_gestion_destino
                          		and estado_reg = 'activo')then
            	raise exception 'la gestión destino % no se encuentra activa, active primero la gestión)',v_gestion_destino;
            end if;

           --Revisamos si ya se registro un plan de cuentas con la gestion destino

           if exists (select 1
           			 from conta.tcuenta
                     where id_gestion=v_id_gestion_destino)then
           		raise exception 'ya existe un plan de cuentas registrado para la gestión %',v_gestion_destino;
           end if;
            --recuperamos el detalle del archivo excel cargado con el id_plan_cuenta asignado
           	v_codigo_aux=202000000;
            nivel=1;
            for v_registros_pc in (SELECT pcd.*
                                    from conta.tplan_cuenta_det pcd
                                    where pcd.id_plan_cuenta = v_parametros.id_plan_cuenta
                                    order by pcd.numero) loop


                if(v_registros_pc.rubro != '' and v_registros_pc.grupo='' )then
                	nivel=1;

                elsif (v_registros_pc.rubro != '' and v_registros_pc.grupo !='' and v_registros_pc.sub_grupo = '') then
                	nivel=2;

                elsif (v_registros_pc.rubro != '' and v_registros_pc.grupo !='' and v_registros_pc.sub_grupo != '' and v_registros_pc.cuenta = '' ) then
                	nivel=3;

                elsif (v_registros_pc.rubro != '' and v_registros_pc.grupo !='' and v_registros_pc.sub_grupo != '' and v_registros_pc.cuenta != '' and v_registros_pc.sub_cuenta = '') then
                	nivel=4;

                elsif (v_registros_pc.rubro != '' and v_registros_pc.grupo !='' and v_registros_pc.sub_grupo != '' and v_registros_pc.cuenta != '' and v_registros_pc.sub_cuenta != '' and v_registros_pc.sub_sub_cuenta = '')then
                	nivel= 5;

                elsif (v_registros_pc.sub_sub_cuenta != '' and v_registros_pc.auxiliar = '')then
                	nivel= 6;

                elsif (v_registros_pc.rubro != '' and v_registros_pc.grupo !='' and v_registros_pc.sub_grupo != '' and v_registros_pc.cuenta != '' and v_registros_pc.sub_cuenta != '' and v_registros_pc.sub_sub_cuenta != ''  and v_registros_pc.auxiliar != '')then
                	nivel= 7;

                     --PERFORM conta.f_insertar_auxiliar(p_id_usuario,hstore(v_registros_pc),v_id_cuentas_padre[6],v_codigo_aux::varchar);
                      update conta.tplan_cuenta_det
                      set id_cuenta_asociada = v_id_cuentas_padre[6]
                      WHERE id_plan_cuenta_det = v_registros_pc.id_plan_cuenta_det;

                      --actualizar la informacion de la cuenta si no tuviera la inclusion de auxiliares
                      if not exists (select 1
                      				 from conta.tcuenta
                                     where id_cuenta = v_id_cuentas_padre[6] and sw_auxiliar = 'si')then
                      		update conta.tcuenta
                            set sw_auxiliar = 'si'
                            where id_cuenta=v_id_cuentas_padre[6];
                      end if;

                end if;

                if (nivel = 1)then
                	v_id_cuentas_padre[nivel]=conta.f_insertar_cuenta(p_id_usuario,NULL,v_id_gestion_destino, hstore(v_registros_pc));
                elsif (nivel in (2,3,4,5,6)) then
                	v_id_cuentas_padre[nivel]=conta.f_insertar_cuenta(p_id_usuario,v_id_cuentas_padre[nivel-1],v_id_gestion_destino, hstore(v_registros_pc));
                    /*update conta.tplan_cuenta_det
                      set id_cuenta_asociada = v_id_cuentas_padre[nivel]
                      WHERE id_plan_cuenta_det = v_registros_pc.id_plan_cuenta_det;*/
                end if;


            end loop;
            --insertamos la asociacion a las partidas
            perform conta.f_insertar_partida(p_id_usuario,v_parametros.id_plan_cuenta);
            --insertamos la asociacion de auxiliares
           	perform conta.f_insertar_auxiliar(p_id_usuario,v_parametros.id_plan_cuenta);
        	--actualizar informacion del plan de cuenta
           update conta.tplan_cuenta
           set	estado='finalizado'
           where id_plan_cuenta = v_parametros.id_plan_cuenta;

			--raise exception 'llega generar plan de cuentas parametros %',v_parametros;
			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','plan de cuentas generado exitosamente');
            v_resp = pxp.f_agrega_clave(v_resp,'id_plan_cuenta_det','');


            --Devuelve la respuesta
            return v_resp;

		end;


	/*********************************    
 	#TRANSACCION:  'CONTA_IPCD_MOD'
 	#DESCRIPCION:	Modificacion de registros
 	#AUTOR:		alan.felipez	
 	#FECHA:		25-11-2019 22:17:20
	***********************************/

	elsif(p_transaccion='CONTA_IPCD_MOD')then

		begin
			--Sentencia de la modificacion
			update conta.tplan_cuenta_det set
			id_plan_cuenta = v_parametros.id_plan_cuenta,
      numero=v_parametros.numero,
			nivel = v_parametros.nivel,
			rubro = v_parametros.rubro,
			grupo = v_parametros.grupo,
			sub_grupo = v_parametros.sub_grupo,
			cuenta = v_parametros.cuenta,
			codigo_cuenta = v_parametros.codigo_cuenta,
			sub_cuenta = v_parametros.sub_cuenta,
			auxiliar = v_parametros.auxiliar,
			nombre_cuenta = v_parametros.nombre_cuenta,
			ajuste = v_parametros.ajuste,
			moneda_ajuste = v_parametros.moneda_ajuste,
			tipo_cuenta = v_parametros.tipo_cuenta,
			moneda = v_parametros.moneda,
			tip_cuenta = v_parametros.tip_cuenta,
			permite_auxiliar = v_parametros.permite_auxiliar,
			cuenta_sigep = v_parametros.cuenta_sigep,
			partida_sigep_debe = v_parametros.partida_sigep_debe,
			partida_sigep_haber = v_parametros.partida_sigep_haber,
			observaciones = v_parametros.observaciones,
      relacion_cuenta = v_parametros.relacion_cuenta,
			id_usuario_mod = p_id_usuario,
			fecha_mod = now(),
			id_usuario_ai = v_parametros._id_usuario_ai,
			usuario_ai = v_parametros._nombre_usuario_ai
			where id_plan_cuenta_det=v_parametros.id_plan_cuenta_det;
               
			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','ImportarPlanCuentaDet modificado(a)'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_plan_cuenta_det',v_parametros.id_plan_cuenta_det::varchar);
               
            --Devuelve la respuesta
            return v_resp;
            
		end;

	/*********************************    
 	#TRANSACCION:  'CONTA_IPCD_ELI'
 	#DESCRIPCION:	Eliminacion de registros
 	#AUTOR:		alan.felipez	
 	#FECHA:		25-11-2019 22:17:20
	***********************************/

	elsif(p_transaccion='CONTA_IPCD_ELI')then

		begin
			--Sentencia de la eliminacion
			delete from conta.tplan_cuenta_det
            where id_plan_cuenta_det=v_parametros.id_plan_cuenta_det;
               
            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','ImportarPlanCuentaDet eliminado(a)'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_plan_cuenta_det',v_parametros.id_plan_cuenta_det::varchar);
              
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
$BODY$
LANGUAGE 'plpgsql' VOLATILE
COST 100;
ALTER FUNCTION "conta"."ft_plan_cuenta_det_ime"(integer, integer, character varying, character varying) OWNER TO postgres;
