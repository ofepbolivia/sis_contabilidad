CREATE OR REPLACE FUNCTION "conta"."ft_int_beneficiario_sel"(	
				p_administrador integer, p_id_usuario integer, p_tabla character varying, p_transaccion character varying)
RETURNS character varying AS
$BODY$
/**************************************************************************
 SISTEMA:		Sistema de Contabilidad
 FUNCION: 		conta.ft_int_beneficiario_sel
 DESCRIPCION:   Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'conta.tint_beneficiario'
 AUTOR: 		 (admin)
 FECHA:	        27-10-2022 14:42:32
 COMENTARIOS:	
***************************************************************************
 HISTORIAL DE MODIFICACIONES:
#ISSUE				FECHA				AUTOR				DESCRIPCION
 #0				27-10-2022 14:42:32								Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'conta.tint_beneficiario'	
 #
 ***************************************************************************/

DECLARE

	v_consulta    		varchar;
	v_parametros  		record;
	v_nombre_funcion   	text;
	v_resp				varchar;
			    
BEGIN

	v_nombre_funcion = 'conta.ft_int_beneficiario_sel';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************    
 	#TRANSACCION:  'CONTA_intBenef_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		admin	
 	#FECHA:		27-10-2022 14:42:32
	***********************************/

	if(p_transaccion='CONTA_intBenef_SEL')then
     				
    	begin
    		--Sentencia de la consulta
			v_consulta:='select
						intbenef.id_beneficiario,
						intbenef.estado_reg,
						intbenef.id_funcionario_beneficiario,
						intbenef.id_int_comprobante,
						intbenef.id_concepto_ingas,
						intbenef.id_centro_costo,
						intbenef.id_partida,
						intbenef.glosa,
						intbenef.banco,
						intbenef.nro_cuenta_bancaria_sigma,
						intbenef.importe,
						intbenef.id_usuario_reg,
						intbenef.fecha_reg,
						intbenef.id_usuario_ai,
						intbenef.usuario_ai,
						intbenef.id_usuario_mod,
						intbenef.fecha_mod,
						usu1.cuenta as usr_reg,
						usu2.cuenta as usr_mod	
						from conta.tint_beneficiario intbenef
						inner join segu.tusuario usu1 on usu1.id_usuario = intbenef.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = intbenef.id_usuario_mod
				        where  ';
			
			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

			--Devuelve la respuesta
			return v_consulta;
						
		end;

	/*********************************    
 	#TRANSACCION:  'CONTA_intBenef_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		admin	
 	#FECHA:		27-10-2022 14:42:32
	***********************************/

	elsif(p_transaccion='CONTA_intBenef_CONT')then

		begin
			--Sentencia de la consulta de conteo de registros
			v_consulta:='select count(id_beneficiario)
					    from conta.tint_beneficiario intbenef
					    inner join segu.tusuario usu1 on usu1.id_usuario = intbenef.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = intbenef.id_usuario_mod
					    where ';
			
			--Definicion de la respuesta		    
			v_consulta:=v_consulta||v_parametros.filtro;

			--Devuelve la respuesta
			return v_consulta;

		end;
					
	else
					     
		raise exception 'Transaccion inexistente';
					         
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
ALTER FUNCTION "conta"."ft_int_beneficiario_sel"(integer, integer, character varying, character varying) OWNER TO postgres;
