CREATE OR REPLACE FUNCTION "conta"."ft_plan_cuenta_sel"(	
				p_administrador integer, p_id_usuario integer, p_tabla character varying, p_transaccion character varying)
RETURNS character varying AS
$BODY$
/**************************************************************************
 SISTEMA:		Sistema de Contabilidad
 FUNCION: 		conta.ft_plan_cuenta_sel
 DESCRIPCION:   Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'conta.tplan_cuenta'
 AUTOR: 		 (alan.felipez)
 FECHA:	        25-11-2019 22:15:53
 COMENTARIOS:	
***************************************************************************
 HISTORIAL DE MODIFICACIONES:
#ISSUE				FECHA				AUTOR				DESCRIPCION
 #0				25-11-2019 22:15:53								Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'conta.tplan_cuenta'	
 #
 ***************************************************************************/

DECLARE

	v_consulta    		varchar;
	v_parametros  		record;
	v_nombre_funcion   	text;
	v_resp				varchar;
			    
BEGIN

	v_nombre_funcion = 'conta.ft_plan_cuenta_sel';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************    
 	#TRANSACCION:  'CONTA_IPC_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		alan.felipez	
 	#FECHA:		25-11-2019 22:15:53
	***********************************/

	if(p_transaccion='CONTA_IPC_SEL')then
     				
    	begin
    		--Sentencia de la consulta
			v_consulta:='select
						ipc.id_plan_cuenta,
						ipc.estado_reg,
						ipc.nombre,
						ipc.estado,
                        ipc.id_gestion,
						ipc.id_usuario_reg,
						ipc.fecha_reg,
						ipc.id_usuario_ai,
						ipc.usuario_ai,
						ipc.id_usuario_mod,
						ipc.fecha_mod,
                        ges.gestion as desc_gestion,
						usu1.cuenta as usr_reg,
						usu2.cuenta as usr_mod
						from conta.tplan_cuenta ipc
						inner join segu.tusuario usu1 on usu1.id_usuario = ipc.id_usuario_reg
                        inner join param.tgestion ges on ges.id_gestion = ipc.id_gestion
						left join segu.tusuario usu2 on usu2.id_usuario = ipc.id_usuario_mod
				        where  ';
			
			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

			--Devuelve la respuesta
			return v_consulta;
						
		end;

	/*********************************    
 	#TRANSACCION:  'CONTA_IPC_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		alan.felipez	
 	#FECHA:		25-11-2019 22:15:53
	***********************************/

	elsif(p_transaccion='CONTA_IPC_CONT')then

		begin
			--Sentencia de la consulta de conteo de registros
			v_consulta:='select count(id_plan_cuenta)
					    from conta.tplan_cuenta ipc
					    inner join segu.tusuario usu1 on usu1.id_usuario = ipc.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = ipc.id_usuario_mod
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
ALTER FUNCTION "conta"."ft_plan_cuenta_sel"(integer, integer, character varying, character varying) OWNER TO postgres;
