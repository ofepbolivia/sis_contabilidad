CREATE OR REPLACE FUNCTION conta.ft_historial_reg_compras_sel (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Sistema de Contabilidad
 FUNCION: 		conta.ft_historial_reg_compras_sel
 DESCRIPCION:   Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'conta.thistorial_reg_compras'
 AUTOR: 		 (franklin.espinoza)
 FECHA:	        07-06-2018 15:14:54
 COMENTARIOS:
***************************************************************************
 HISTORIAL DE MODIFICACIONES:
#ISSUE				FECHA				AUTOR				DESCRIPCION
 #0				07-06-2018 15:14:54								Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'conta.thistorial_reg_compras'
 #
 ***************************************************************************/

DECLARE

	v_consulta    		varchar;
	v_parametros  		record;
	v_nombre_funcion   	text;
	v_resp				varchar;

BEGIN

	v_nombre_funcion = 'conta.ft_historial_reg_compras_sel';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'CONTA_HRC_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		franklin.espinoza
 	#FECHA:		07-06-2018 15:14:54
	***********************************/

	if(p_transaccion='CONTA_HRC_SEL')then

    	begin
    		--Sentencia de la consulta
			v_consulta:='select
						hrc.id_historial_reg_compras::integer,
                        tdc.id_doc_compra_venta::integer,
						tdc.nit::varchar,
						hrc.fecha_cambio::timestamp,
						tdc.nro_tramite::varchar,
						tdc.nro_documento::varchar,
						tdc.codigo_control::varchar,
						tdc.nro_autorizacion::varchar,
						hrc.id_funcionario,
						hrc.estado_reg,
						tdc.razon_social::varchar,
						hrc.id_usuario_ai,
						hrc.usuario_ai,
						hrc.fecha_reg,
						hrc.id_usuario_reg,
						hrc.fecha_mod,
						hrc.id_usuario_mod,
						usu1.cuenta as usr_reg,
						usu2.cuenta as usr_mod,
                        vf.desc_funcionario1::varchar as desc_func,
                        tdc.importe_neto
						from conta.thistorial_reg_compras hrc
						inner join segu.tusuario usu1 on usu1.id_usuario = hrc.id_usuario_reg
						inner join orga.vfuncionario vf on vf.id_funcionario = hrc.id_funcionario
                        left join segu.tusuario usu2 on usu2.id_usuario = hrc.id_usuario_mod
                        left join conta.tdoc_compra_venta tdc on tdc.id_doc_compra_venta = hrc.id_doc_compra_venta

				        where  ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;
			raise notice 'v_consulta: %', v_consulta;
			--Devuelve la respuesta
			return v_consulta;

		end;

	/*********************************
 	#TRANSACCION:  'CONTA_HRC_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		franklin.espinoza
 	#FECHA:		07-06-2018 15:14:54
	***********************************/

	elsif(p_transaccion='CONTA_HRC_CONT')then

		begin
			--Sentencia de la consulta de conteo de registros
			v_consulta:='select count(id_historial_reg_compras)
					    from conta.thistorial_reg_compras hrc
					    inner join segu.tusuario usu1 on usu1.id_usuario = hrc.id_usuario_reg
                        inner join orga.vfuncionario vf on vf.id_funcionario = hrc.id_funcionario
						left join segu.tusuario usu2 on usu2.id_usuario = hrc.id_usuario_mod
                        left join conta.tdoc_compra_venta tdc on tdc.id_doc_compra_venta = hrc.id_doc_compra_venta
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
$body$
LANGUAGE 'plpgsql'
VOLATILE
CALLED ON NULL INPUT
SECURITY INVOKER
COST 100;