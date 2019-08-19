CREATE OR REPLACE FUNCTION conta.ft_moneda_pais_sel (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Sistema de Contabilidad
 FUNCION: 		conta.ft_moneda_pais_sel
 DESCRIPCION:   Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'conta.tmoneda_pais'
 AUTOR: 		 (ivaldivia)
 FECHA:	        07-08-2019 14:05:49
 COMENTARIOS:
***************************************************************************
 HISTORIAL DE MODIFICACIONES:
#ISSUE				FECHA				AUTOR				DESCRIPCION
 #0				07-08-2019 14:05:49								Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'conta.tmoneda_pais'
 #
 ***************************************************************************/

DECLARE

	v_consulta    		varchar;
	v_parametros  		record;
	v_nombre_funcion   	text;
	v_resp				varchar;

BEGIN

	v_nombre_funcion = 'conta.ft_moneda_pais_sel';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'CONTA_MONPA_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		ivaldivia
 	#FECHA:		07-08-2019 14:05:49
	***********************************/

	if(p_transaccion='CONTA_MONPA_SEL')then

    	begin
    		--Sentencia de la consulta
			v_consulta:='select
						monpa.id_moneda_pais,
						monpa.estado_reg,
						monpa.id_moneda,
						monpa.origen,
						monpa.prioridad,
						monpa.tipo_actualizacion,
						monpa.id_lugar,
						monpa.id_usuario_reg,
						monpa.fecha_reg,
						monpa.id_usuario_ai,
						monpa.usuario_ai,
						monpa.id_usuario_mod,
						monpa.fecha_mod,
						usu1.cuenta as usr_reg,
						usu2.cuenta as usr_mod,
                        (mon.codigo || '' ('' || mon.moneda || '')'')::varchar as desc_moneda
						from conta.tmoneda_pais monpa
						inner join segu.tusuario usu1 on usu1.id_usuario = monpa.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = monpa.id_usuario_mod
                        inner join param.tmoneda mon on mon.id_moneda = monpa.id_moneda
				        where  ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

			--Devuelve la respuesta
			return v_consulta;

		end;

	/*********************************
 	#TRANSACCION:  'CONTA_MONPA_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		ivaldivia
 	#FECHA:		07-08-2019 14:05:49
	***********************************/

	elsif(p_transaccion='CONTA_MONPA_CONT')then

		begin
			--Sentencia de la consulta de conteo de registros
			v_consulta:='select count(id_moneda_pais)
					    from conta.tmoneda_pais monpa
					    inner join segu.tusuario usu1 on usu1.id_usuario = monpa.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = monpa.id_usuario_mod
                        inner join param.tmoneda mon on mon.id_moneda = monpa.id_moneda
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

ALTER FUNCTION conta.ft_moneda_pais_sel (p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
  OWNER TO postgres;
