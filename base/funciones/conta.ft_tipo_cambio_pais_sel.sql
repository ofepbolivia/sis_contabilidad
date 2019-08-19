CREATE OR REPLACE FUNCTION conta.ft_tipo_cambio_pais_sel (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Sistema de Contabilidad
 FUNCION: 		conta.ft_tipo_cambio_pais_sel
 DESCRIPCION:   Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'conta.ttipo_cambio_pais'
 AUTOR: 		 (ivaldivia)
 FECHA:	        07-08-2019 14:12:25
 COMENTARIOS:
***************************************************************************
 HISTORIAL DE MODIFICACIONES:
#ISSUE				FECHA				AUTOR				DESCRIPCION
 #0				07-08-2019 14:12:25								Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'conta.ttipo_cambio_pais'
 #
 ***************************************************************************/

DECLARE

	v_consulta    		varchar;
	v_parametros  		record;
	v_nombre_funcion   	text;
	v_resp				varchar;

BEGIN

	v_nombre_funcion = 'conta.ft_tipo_cambio_pais_sel';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'CONTA_TCPA_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		ivaldivia
 	#FECHA:		07-08-2019 14:12:25
	***********************************/

	if(p_transaccion='CONTA_TCPA_SEL')then

    	begin
    		--Sentencia de la consulta
			v_consulta:='select
						tcpa.id_tipo_cambio_pais,
						tcpa.estado_reg,
						tcpa.fecha,
						tcpa.oficial,
						tcpa.compra,
						tcpa.venta,
						tcpa.observaciones,
						tcpa.id_moneda_pais,
						tcpa.id_usuario_reg,
						tcpa.fecha_reg,
						tcpa.id_usuario_ai,
						tcpa.usuario_ai,
						tcpa.id_usuario_mod,
						tcpa.fecha_mod,
						usu1.cuenta as usr_reg,
						usu2.cuenta as usr_mod
						from conta.ttipo_cambio_pais tcpa
						inner join segu.tusuario usu1 on usu1.id_usuario = tcpa.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = tcpa.id_usuario_mod
				        where  ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

			--Devuelve la respuesta
			return v_consulta;

		end;

	/*********************************
 	#TRANSACCION:  'CONTA_TCPA_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		ivaldivia
 	#FECHA:		07-08-2019 14:12:25
	***********************************/

	elsif(p_transaccion='CONTA_TCPA_CONT')then

		begin
			--Sentencia de la consulta de conteo de registros
			v_consulta:='select count(id_tipo_cambio_pais)
					    from conta.ttipo_cambio_pais tcpa
					    inner join segu.tusuario usu1 on usu1.id_usuario = tcpa.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = tcpa.id_usuario_mod
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

ALTER FUNCTION conta.ft_tipo_cambio_pais_sel (p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
  OWNER TO postgres;
