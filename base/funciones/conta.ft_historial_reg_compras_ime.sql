CREATE OR REPLACE FUNCTION conta.ft_historial_reg_compras_ime (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Sistema de Contabilidad
 FUNCION: 		conta.ft_historial_reg_compras_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'conta.thistorial_reg_compras'
 AUTOR: 		 (franklin.espinoza)
 FECHA:	        07-06-2018 15:14:54
 COMENTARIOS:
***************************************************************************
 HISTORIAL DE MODIFICACIONES:
#ISSUE				FECHA				AUTOR				DESCRIPCION
 #0				07-06-2018 15:14:54								Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'conta.thistorial_reg_compras'
 #
 ***************************************************************************/

DECLARE

	v_nro_requerimiento    	integer;
	v_parametros           	record;
	v_id_requerimiento     	integer;
	v_resp		            varchar;
	v_nombre_funcion        text;
	v_mensaje_error         text;
	v_id_historial_reg_compras	integer;

BEGIN

    v_nombre_funcion = 'conta.ft_historial_reg_compras_ime';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'CONTA_HRC_INS'
 	#DESCRIPCION:	Insercion de registros
 	#AUTOR:		franklin.espinoza
 	#FECHA:		07-06-2018 15:14:54
	***********************************/

	if(p_transaccion='CONTA_HRC_INS')then

        begin
        	--Sentencia de la insercion
        	insert into conta.thistorial_reg_compras(
			nit,
			fecha_cambio,
			nro_tramite,
			nro_factura,
			codigo_control,
			nro_autorizacion,
			id_funcionario,
			estado_reg,
			razon_social,
			id_usuario_ai,
			usuario_ai,
			fecha_reg,
			id_usuario_reg,
			fecha_mod,
			id_usuario_mod
          	) values(
			v_parametros.nit,
			v_parametros.fecha_cambio,
			v_parametros.nro_tramite,
			v_parametros.nro_factura,
			v_parametros.codigo_control,
			v_parametros.nro_autorizacion,
			v_parametros.id_funcionario,
			'activo',
			v_parametros.razon_social,
			v_parametros._id_usuario_ai,
			v_parametros._nombre_usuario_ai,
			now(),
			p_id_usuario,
			null,
			null



			)RETURNING id_historial_reg_compras into v_id_historial_reg_compras;

			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','HistorialRegCompras almacenado(a) con exito (id_historial_reg_compras'||v_id_historial_reg_compras||')');
            v_resp = pxp.f_agrega_clave(v_resp,'id_historial_reg_compras',v_id_historial_reg_compras::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'CONTA_HRC_MOD'
 	#DESCRIPCION:	Modificacion de registros
 	#AUTOR:		franklin.espinoza
 	#FECHA:		07-06-2018 15:14:54
	***********************************/

	elsif(p_transaccion='CONTA_HRC_MOD')then

		begin
			--Sentencia de la modificacion
			update conta.thistorial_reg_compras set
			nit = v_parametros.nit,
			fecha_cambio = v_parametros.fecha_cambio,
			nro_tramite = v_parametros.nro_tramite,
			nro_factura = v_parametros.nro_factura,
			codigo_control = v_parametros.codigo_control,
			nro_autorizacion = v_parametros.nro_autorizacion,
			id_funcionario = v_parametros.id_funcionario,
			razon_social = v_parametros.razon_social,
			fecha_mod = now(),
			id_usuario_mod = p_id_usuario,
			id_usuario_ai = v_parametros._id_usuario_ai,
			usuario_ai = v_parametros._nombre_usuario_ai
			where id_historial_reg_compras=v_parametros.id_historial_reg_compras;

			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','HistorialRegCompras modificado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_historial_reg_compras',v_parametros.id_historial_reg_compras::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'CONTA_HRC_ELI'
 	#DESCRIPCION:	Eliminacion de registros
 	#AUTOR:		franklin.espinoza
 	#FECHA:		07-06-2018 15:14:54
	***********************************/

	elsif(p_transaccion='CONTA_HRC_ELI')then

		begin
			--Sentencia de la eliminacion
			delete from conta.thistorial_reg_compras
            where id_historial_reg_compras=v_parametros.id_historial_reg_compras;

            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','HistorialRegCompras eliminado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_historial_reg_compras',v_parametros.id_historial_reg_compras::varchar);

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
$body$
LANGUAGE 'plpgsql'
VOLATILE
CALLED ON NULL INPUT
SECURITY INVOKER
COST 100;