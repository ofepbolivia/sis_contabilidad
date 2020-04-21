CREATE OR REPLACE FUNCTION "conta"."ft_plan_cuenta_ime" (	
				p_administrador integer, p_id_usuario integer, p_tabla character varying, p_transaccion character varying)
RETURNS character varying AS
$BODY$

/**************************************************************************
 SISTEMA:		Sistema de Contabilidad
 FUNCION: 		conta.ft_plan_cuenta_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'conta.tplan_cuenta'
 AUTOR: 		 (alan.felipez)
 FECHA:	        25-11-2019 22:15:53
 COMENTARIOS:	
***************************************************************************
 HISTORIAL DE MODIFICACIONES:
#ISSUE				FECHA				AUTOR				DESCRIPCION
 #0				25-11-2019 22:15:53								Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'conta.tplan_cuenta'	
 #
 ***************************************************************************/

DECLARE

	v_nro_requerimiento    	integer;
	v_parametros           	record;
	v_id_requerimiento     	integer;
	v_resp		            varchar;
	v_nombre_funcion        text;
	v_mensaje_error         text;
	v_id_plan_cuenta	integer;


    v_estado				varchar;
    v_nombre				varchar;
			    
BEGIN

    v_nombre_funcion = 'conta.ft_plan_cuenta_ime';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************    
 	#TRANSACCION:  'CONTA_IPC_INS'
 	#DESCRIPCION:	Insercion de registros
 	#AUTOR:		alan.felipez	
 	#FECHA:		25-11-2019 22:15:53
	***********************************/

	if(p_transaccion='CONTA_IPC_INS')then
					
        begin
        	--Sentencia de la insercion
        insert into conta.tplan_cuenta(
        estado_reg,
        nombre,
        estado,
              id_gestion,
        id_usuario_reg,
        fecha_reg,
        id_usuario_ai,
        usuario_ai,
        id_usuario_mod,
        fecha_mod
              ) values(
        'activo',
        v_parametros.nombre,
        --v_parametros.estado,/26/11/2019 Alan
              'borrador',
              v_parametros.id_gestion,
        p_id_usuario,
        now(),
        v_parametros._id_usuario_ai,
        v_parametros._nombre_usuario_ai,
        null,
        null
							
			
			
			)RETURNING id_plan_cuenta into v_id_plan_cuenta;
			
			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','ImportarPlanCuenta almacenado(a) con exito (id_plan_cuenta'||v_id_plan_cuenta||')'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_plan_cuenta',v_id_plan_cuenta::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************    
 	#TRANSACCION:  'CONTA_IPC_MOD'
 	#DESCRIPCION:	Modificacion de registros
 	#AUTOR:		alan.felipez	
 	#FECHA:		25-11-2019 22:15:53
	***********************************/

	elsif(p_transaccion='CONTA_IPC_MOD')then

		begin
			--Sentencia de la modificacion
			update conta.tplan_cuenta set
			nombre = v_parametros.nombre,
			estado = v_parametros.estado,
            id_gestion = v_parametros.id_gestion,
			id_usuario_mod = p_id_usuario,
			fecha_mod = now(),
			id_usuario_ai = v_parametros._id_usuario_ai,
			usuario_ai = v_parametros._nombre_usuario_ai
			where id_plan_cuenta=v_parametros.id_plan_cuenta;
               
			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','ImportarPlanCuenta modificado(a)'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_plan_cuenta',v_parametros.id_plan_cuenta::varchar);
               
            --Devuelve la respuesta
            return v_resp;
            
		end;
  /*********************************
 	#TRANSACCION:  'CONTA_ACT_EST'
 	#DESCRIPCION:	Actualiza el estado del registro de plan de cuentas
 	#AUTOR:		alan.felipez
 	#FECHA:		27-11-2019 22:15:53
	***********************************/

	elsif(p_transaccion='CONTA_ACT_EST')then

		begin
        	--seleccionamos el estado actual del registro
        	select estado, nombre
            into v_estado, v_nombre
            from conta.tplan_cuenta
            where id_plan_cuenta=v_parametros.id_plan_cuenta;
            --vemos el procedimiento a seguir
        	if (v_parametros.estado='siguiente_estado')then
        		IF v_estado='borrador' THEN
                	v_estado='registrado';
                elsif v_estado='registrado' then
                	v_estado='finalizado';
                else
                	--raise exception 'no existe un siguiente estado para el plan de cuentas: %',v_nombre;
                end if;
            else
            	IF v_estado='registrado' THEN
                	v_estado='borrador';
                    delete from conta.tplan_cuenta_det
            		where id_plan_cuenta=v_parametros.id_plan_cuenta;
                elsif v_estado='finalizado' then
                	v_estado='registrado';
                else
                	raise exception 'no existe un anterior estado para el plan de cuentas: %',v_nombre;
                end if;
        	end if;
			--Sentencia de la modificacion
			update conta.tplan_cuenta set
			estado = v_estado,
			id_usuario_mod = p_id_usuario,
			fecha_mod = now(),
			id_usuario_ai = v_parametros._id_usuario_ai,
			usuario_ai = v_parametros._nombre_usuario_ai
			where id_plan_cuenta=v_parametros.id_plan_cuenta;

			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','ImportarPlanCuenta modificado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_plan_cuenta',v_parametros.id_plan_cuenta::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;
	/*********************************    
 	#TRANSACCION:  'CONTA_IPC_ELI'
 	#DESCRIPCION:	Eliminacion de registros
 	#AUTOR:		alan.felipez	
 	#FECHA:		25-11-2019 22:15:53
	***********************************/

	elsif(p_transaccion='CONTA_IPC_ELI')then

		begin
			--Sentencia de la eliminacion
			delete from conta.tplan_cuenta
            where id_plan_cuenta=v_parametros.id_plan_cuenta;
               
            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','ImportarPlanCuenta eliminado(a)'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_plan_cuenta',v_parametros.id_plan_cuenta::varchar);
              
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
ALTER FUNCTION "conta"."ft_plan_cuenta_ime"(integer, integer, character varying, character varying) OWNER TO postgres;
