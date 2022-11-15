CREATE OR REPLACE FUNCTION "conta"."ft_int_beneficiario_ime" (	
				p_administrador integer, p_id_usuario integer, p_tabla character varying, p_transaccion character varying)
RETURNS character varying AS
$BODY$

/**************************************************************************
 SISTEMA:		Sistema de Contabilidad
 FUNCION: 		conta.ft_int_beneficiario_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'conta.tint_beneficiario'
 AUTOR: 		 (admin)
 FECHA:	        27-10-2022 14:42:32
 COMENTARIOS:	
***************************************************************************
 HISTORIAL DE MODIFICACIONES:
#ISSUE				FECHA				AUTOR				DESCRIPCION
 #0				27-10-2022 14:42:32								Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'conta.tint_beneficiario'	
 #
 ***************************************************************************/

DECLARE

	v_nro_requerimiento    	integer;
	v_parametros           	record;
	v_id_requerimiento     	integer;
	v_resp		            varchar;
	v_nombre_funcion        text;
	v_mensaje_error         text;
	v_id_beneficiario	integer;
			    
BEGIN

    v_nombre_funcion = 'conta.ft_int_beneficiario_ime';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************    
 	#TRANSACCION:  'CONTA_intBenef_INS'
 	#DESCRIPCION:	Insercion de registros
 	#AUTOR:		admin	
 	#FECHA:		27-10-2022 14:42:32
	***********************************/

	if(p_transaccion='CONTA_intBenef_INS')then
					
        begin
        	--Sentencia de la insercion
        	insert into conta.tint_beneficiario(
			estado_reg,
			id_funcionario_beneficiario,
			id_int_comprobante,
			id_concepto_ingas,
			id_centro_costo,
			id_partida,
			glosa,
			banco,
			nro_cuenta_bancaria_sigma,
			importe,
			id_usuario_reg,
			fecha_reg,
			id_usuario_ai,
			usuario_ai,
			id_usuario_mod,
			fecha_mod
          	) values(
			'activo',
			v_parametros.id_funcionario_beneficiario,
			v_parametros.id_int_comprobante,
			v_parametros.id_concepto_ingas,
			v_parametros.id_centro_costo,
			v_parametros.id_partida,
			v_parametros.glosa,
			v_parametros.banco,
			v_parametros.nro_cuenta_bancaria_sigma,
			v_parametros.importe,
			p_id_usuario,
			now(),
			v_parametros._id_usuario_ai,
			v_parametros._nombre_usuario_ai,
			null,
			null
							
			
			
			)RETURNING id_beneficiario into v_id_beneficiario;
			
			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','intBeneficiario almacenado(a) con exito (id_beneficiario'||v_id_beneficiario||')'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_beneficiario',v_id_beneficiario::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************    
 	#TRANSACCION:  'CONTA_intBenef_MOD'
 	#DESCRIPCION:	Modificacion de registros
 	#AUTOR:		admin	
 	#FECHA:		27-10-2022 14:42:32
	***********************************/

	elsif(p_transaccion='CONTA_intBenef_MOD')then

		begin
			--Sentencia de la modificacion
			update conta.tint_beneficiario set
			id_funcionario_beneficiario = v_parametros.id_funcionario_beneficiario,
			id_int_comprobante = v_parametros.id_int_comprobante,
			id_concepto_ingas = v_parametros.id_concepto_ingas,
			id_centro_costo = v_parametros.id_centro_costo,
			id_partida = v_parametros.id_partida,
			glosa = v_parametros.glosa,
			banco = v_parametros.banco,
			nro_cuenta_bancaria_sigma = v_parametros.nro_cuenta_bancaria_sigma,
			importe = v_parametros.importe,
			id_usuario_mod = p_id_usuario,
			fecha_mod = now(),
			id_usuario_ai = v_parametros._id_usuario_ai,
			usuario_ai = v_parametros._nombre_usuario_ai
			where id_beneficiario=v_parametros.id_beneficiario;
               
			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','intBeneficiario modificado(a)'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_beneficiario',v_parametros.id_beneficiario::varchar);
               
            --Devuelve la respuesta
            return v_resp;
            
		end;

	/*********************************    
 	#TRANSACCION:  'CONTA_intBenef_ELI'
 	#DESCRIPCION:	Eliminacion de registros
 	#AUTOR:		admin	
 	#FECHA:		27-10-2022 14:42:32
	***********************************/

	elsif(p_transaccion='CONTA_intBenef_ELI')then

		begin
			--Sentencia de la eliminacion
			delete from conta.tint_beneficiario
            where id_beneficiario=v_parametros.id_beneficiario;
               
            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','intBeneficiario eliminado(a)'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_beneficiario',v_parametros.id_beneficiario::varchar);
              
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
ALTER FUNCTION "conta"."ft_int_beneficiario_ime"(integer, integer, character varying, character varying) OWNER TO postgres;
