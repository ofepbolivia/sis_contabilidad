CREATE OR REPLACE FUNCTION "conta"."ft_plan_cuenta_det_sel"(	
				p_administrador integer, p_id_usuario integer, p_tabla character varying, p_transaccion character varying)
RETURNS character varying AS
$BODY$
/**************************************************************************
 SISTEMA:		Sistema de Contabilidad
 FUNCION: 		conta.ft_plan_cuenta_det_sel
 DESCRIPCION:   Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'conta.tplan_cuenta_det'
 AUTOR: 		 (alan.felipez)
 FECHA:	        25-11-2019 22:17:20
 COMENTARIOS:	
***************************************************************************
 HISTORIAL DE MODIFICACIONES:
#ISSUE				FECHA				AUTOR				DESCRIPCION
 #0				25-11-2019 22:17:20								Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'conta.tplan_cuenta_det'	
 #
 ***************************************************************************/

DECLARE

	v_consulta    		varchar;
	v_parametros  		record;
	v_nombre_funcion   	text;
	v_resp				varchar;
			    
BEGIN

	v_nombre_funcion = 'conta.ft_plan_cuenta_det_sel';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************    
 	#TRANSACCION:  'CONTA_IPCD_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		alan.felipez	
 	#FECHA:		25-11-2019 22:17:20
	***********************************/

	if(p_transaccion='CONTA_IPCD_SEL')then
     				
    	begin
    		--Sentencia de la consulta
			v_consulta:='select
						ipcd.id_plan_cuenta_det,
						ipcd.estado_reg,
						ipcd.id_plan_cuenta,
            ipcd.numero,
						ipcd.nivel,
						ipcd.rubro,
						ipcd.grupo,
						ipcd.sub_grupo,
						ipcd.cuenta,
						ipcd.codigo_cuenta,
						ipcd.sub_cuenta,
            ipcd.sub_sub_cuenta,
            ipcd.sub_sub_sub_cuenta,
						ipcd.auxiliar,
						ipcd.nombre_cuenta,
						ipcd.ajuste,
						ipcd.moneda_ajuste,
						ipcd.tipo_cuenta,--columna tipo de cuenta
						ipcd.moneda,
						ipcd.tip_cuenta,--columna  tipo cuenta
						ipcd.permite_auxiliar,
						ipcd.cuenta_sigep,
						ipcd.partida_sigep_debe,
						ipcd.partida_sigep_haber,
						ipcd.observaciones,
						ipcd.id_usuario_reg,
						ipcd.fecha_reg,
						ipcd.id_usuario_ai,
						ipcd.usuario_ai,
						ipcd.id_usuario_mod,
						ipcd.fecha_mod,
						usu1.cuenta as usr_reg,
						usu2.cuenta as usr_mod	
						from conta.tplan_cuenta_det ipcd
						inner join segu.tusuario usu1 on usu1.id_usuario = ipcd.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = ipcd.id_usuario_mod
				        where  ';
			
			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

			--Devuelve la respuesta
			return v_consulta;
						
		end;

	/*********************************    
 	#TRANSACCION:  'CONTA_IPCD_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		alan.felipez	
 	#FECHA:		25-11-2019 22:17:20
	***********************************/

	elsif(p_transaccion='CONTA_IPCD_CONT')then

		begin
			--Sentencia de la consulta de conteo de registros
			v_consulta:='select count(id_plan_cuenta_det)
					    from conta.tplan_cuenta_det ipcd
					    inner join segu.tusuario usu1 on usu1.id_usuario = ipcd.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = ipcd.id_usuario_mod
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
ALTER FUNCTION "conta"."ft_plan_cuenta_det_sel"(integer, integer, character varying, character varying) OWNER TO postgres;
