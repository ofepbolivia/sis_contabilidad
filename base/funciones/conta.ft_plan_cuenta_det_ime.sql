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
        	insert into conta.tplan_cuenta_det(
			estado_reg,
			id_plan_cuenta,
			nivel,
			rubro,
			grupo,
			sub_grupo,
			cuenta,
			codigo_cuenta,
			sub_cuenta,
			auxiliar,
			nombre_cuenta,
			ajuste,
			moneda_ajuste,
			operacion,
			moneda,
			estados_financieros,
			permite_auxiliar,
			cuenta_sigep,
			partida_sigep,
			descripcion_partida,
			informacion_partida,
			id_usuario_reg,
			fecha_reg,
			id_usuario_ai,
			usuario_ai,
			id_usuario_mod,
			fecha_mod
          	) values(
			'activo',
			v_parametros.id_plan_cuenta,
			v_parametros.nivel,
			v_parametros.rubro,
			v_parametros.grupo,
			v_parametros.sub_grupo,
			v_parametros.cuenta,
			v_parametros.codigo_cuenta,
			v_parametros.sub_cuenta,
			v_parametros.auxiliar,
			v_parametros.nombre_cuenta,
			v_parametros.ajuste,
			v_parametros.moneda_ajuste,
			v_parametros.operacion,
			v_parametros.moneda,
			v_parametros.estados_financieros,
			v_parametros.permite_auxiliar,
			v_parametros.cuenta_sigep,
			v_parametros.partida_sigep,
			v_parametros.descripcion_partida,
			v_parametros.informacion_partida,
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
			operacion = v_parametros.operacion,
			moneda = v_parametros.moneda,
			estados_financieros = v_parametros.estados_financieros,
			permite_auxiliar = v_parametros.permite_auxiliar,
			cuenta_sigep = v_parametros.cuenta_sigep,
			partida_sigep = v_parametros.partida_sigep,
			descripcion_partida = v_parametros.descripcion_partida,
			informacion_partida = v_parametros.informacion_partida,
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
