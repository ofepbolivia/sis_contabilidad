CREATE OR REPLACE FUNCTION conta.ft_tipo_cambio_pais_ime (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Sistema de Contabilidad
 FUNCION: 		conta.ft_tipo_cambio_pais_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'conta.ttipo_cambio_pais'
 AUTOR: 		 (ivaldivia)
 FECHA:	        07-08-2019 14:12:25
 COMENTARIOS:
***************************************************************************
 HISTORIAL DE MODIFICACIONES:
#ISSUE				FECHA				AUTOR				DESCRIPCION
 #0				07-08-2019 14:12:25								Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'conta.ttipo_cambio_pais'
 #
 ***************************************************************************/

DECLARE

	v_nro_requerimiento    	integer;
	v_parametros           	record;
	v_id_requerimiento     	integer;
	v_resp		            varchar;
	v_nombre_funcion        text;
	v_mensaje_error         text;
	v_id_tipo_cambio_pais	integer;
    v_id_moneda				integer;
    v_nombre				varchar;

    v_conexion 				varchar;
    v_cadena_cnx			varchar;
    v_sinc					varchar;
	v_consulta				varchar;
    v_id_tipo_cambio		integer;
    v_res_cone				varchar;
BEGIN

    v_nombre_funcion = 'conta.ft_tipo_cambio_pais_ime';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'CONTA_TCPA_INS'
 	#DESCRIPCION:	Insercion de registros
 	#AUTOR:		ivaldivia
 	#FECHA:		07-08-2019 14:12:25
	***********************************/

	if(p_transaccion='CONTA_TCPA_INS')then

    	 /**************************VERIFICA EXISTENCIA DEL registro para la fecha seleccionada**************************/
            IF EXISTS(SELECT 1 FROM conta.ttipo_cambio_pais camb
            		  WHERE camb.fecha= v_parametros.fecha AND
            		  camb.id_moneda_pais= v_parametros.id_moneda_pais) THEN
            	raise exception 'Inserciòn no realizada: Ya existe un tipo de cambio para la moneda seleccionada en esta fecha.';
            END IF;
         /****************************************************************************************************************/

        begin
        	--Sentencia de la insercion
        	insert into conta.ttipo_cambio_pais(
			estado_reg,
			fecha,
			oficial,
			compra,
			venta,
			observaciones,
			id_moneda_pais,
			id_usuario_reg,
			fecha_reg,
			id_usuario_ai,
			usuario_ai,
			id_usuario_mod,
			fecha_mod
          	) values(
			'activo',
			v_parametros.fecha,
			v_parametros.oficial,
			v_parametros.compra,
			v_parametros.venta,
			v_parametros.observaciones,
			v_parametros.id_moneda_pais,
			p_id_usuario,
			now(),
			v_parametros._id_usuario_ai,
			v_parametros._nombre_usuario_ai,
			null,
			null

			)RETURNING id_tipo_cambio_pais into v_id_tipo_cambio_pais;


            /*Alimentar Tabla param.ttipo_cambio*/
            select  lu.nombre, mon.id_moneda into v_nombre, v_id_moneda
            from conta.tmoneda_pais mon
            inner join conta.ttipo_cambio_pais camb on camb.id_moneda_pais = mon.id_moneda_pais
            inner join param.tlugar lu on lu.id_lugar = mon.id_lugar
            where camb.id_tipo_cambio_pais = v_id_tipo_cambio_pais;


            if (v_nombre = 'BOLIVIA') THEN

            insert into param.ttipo_cambio(
			estado_reg,
			fecha,
			observaciones,
			compra,
			venta,
			oficial,
			id_moneda,
			fecha_reg,
			id_usuario_reg,
			fecha_mod,
			id_usuario_mod
          	) values(
			'activo',
			v_parametros.fecha,
			v_parametros.observaciones,
			v_parametros.compra,
			v_parametros.venta,
			v_parametros.oficial,
			v_id_moneda,
			now(),
			p_id_usuario,
			null,
			null

			);


            /*Insertamos tambien en ENDESIS*/

            /*Establecemos la conexion con ENDESIS*/
            v_cadena_cnx =  migra.f_obtener_cadena_conexion();
            v_conexion =  (SELECT dblink_connect(v_cadena_cnx));
            /*************************************************/

            select * FROM dblink(v_cadena_cnx,'select nextval(''param.tpm_tipo_cambio_id_tipo_cambio_seq'')',TRUE)AS t1(resp integer)
            	into v_id_tipo_cambio;

            v_consulta = '
            		INSERT INTO param.tpm_tipo_cambio(
                    id_tipo_cambio,
                    id_moneda,
                    fecha,
                    hora,
                    oficial,
                    compra,
                    venta,
                    observaciones,
                    estado
                    )
                    values(
                    '||v_id_tipo_cambio||',
                    ' || v_id_moneda || ',
                    ''' ||v_parametros.fecha || ''',
                    '''||now()::time||''',
                    '||v_parametros.oficial||',
                    '||v_parametros.compra||',
                    '||v_parametros.venta||',
                    '''||v_parametros.observaciones||''',
                    ''activo''
                    );';


               IF(v_conexion!='OK') THEN

                               raise exception 'FALLA CONEXION A LA BASE DE DATOS CON DBLINK';

                           ELSE


                               perform dblink_exec(v_cadena_cnx,v_consulta,TRUE);

                               v_res_cone=(select dblink_disconnect());



               END IF;

            /************************************/
            end if;

			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Tipo Cambio Pais almacenado(a) con exito (id_tipo_cambio_pais'||v_id_tipo_cambio_pais||')');
            v_resp = pxp.f_agrega_clave(v_resp,'id_tipo_cambio_pais',v_id_tipo_cambio_pais::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'CONTA_TCPA_MOD'
 	#DESCRIPCION:	Modificacion de registros
 	#AUTOR:		ivaldivia
 	#FECHA:		07-08-2019 14:12:25
	***********************************/

	elsif(p_transaccion='CONTA_TCPA_MOD')then

		begin

        	/************************VERIFICA EXISTENCIA DEL REGISTRO*************************************/
            IF NOT EXISTS(SELECT 1
            			  FROM conta.ttipo_cambio_pais camb
                          WHERE camb.id_tipo_cambio_pais=v_parametros.id_tipo_cambio_pais) THEN

               raise exception 'Modificación no realizada: no existe el registro % en la tabla integra.tin_tipo_cambio',v_parametros.id_tipo_cambio_pais;
            END IF;
        	/***********************************************************************************************/

            /**************************VERIFICA EXISTENCIA DEL registro para la fecha seleccionada********************/
            IF EXISTS(SELECT 1 FROM conta.ttipo_cambio_pais camb
                      WHERE camb.fecha= v_parametros.fecha AND
            						camb.id_moneda_pais= v_parametros.id_moneda_pais AND
                                    camb.id_tipo_cambio_pais <> v_parametros.id_tipo_cambio_pais) THEN
            	raise exception 'Inserciòn no realizada: Ya existe un tipo de cambio para esta fecha.';
            END IF;

            /*Actualizamos tambien en ENDESIS*/

            /*Establecemos la conexion con ENDESIS*/
            v_cadena_cnx =  migra.f_obtener_cadena_conexion();
            v_conexion =  (SELECT dblink_connect(v_cadena_cnx));
            /*************************************************/

            v_consulta = '
            		update param.tpm_tipo_cambio set
                    observaciones = '''||v_parametros.observaciones||''',
                    compra = '||v_parametros.compra||',
                    venta = '||v_parametros.venta||',
                    oficial = '||v_parametros.oficial||',
                    id_moneda = '||v_parametros.id_moneda_pais||',
                    where WHERE param.tpm_tipo_cambio.id_tipo_cambio = '||v_parametros.id_tipo_cambio_pais||';';


             IF(v_conexion!='OK') THEN

			                 raise exception 'FALLA CONEXION A LA BASE DE DATOS CON DBLINK';

			             ELSE


                             perform dblink_exec(v_cadena_cnx,v_consulta,TRUE);

                             v_res_cone=(select dblink_disconnect());



             END IF;

            /************************************/

			--Sentencia de la modificacion
			update conta.ttipo_cambio_pais set
			fecha = v_parametros.fecha,
			oficial = v_parametros.oficial,
			compra = v_parametros.compra,
			venta = v_parametros.venta,
			observaciones = v_parametros.observaciones,
			id_moneda_pais = v_parametros.id_moneda_pais,
			id_usuario_mod = p_id_usuario,
			fecha_mod = now(),
			id_usuario_ai = v_parametros._id_usuario_ai,
			usuario_ai = v_parametros._nombre_usuario_ai
			where id_tipo_cambio_pais=v_parametros.id_tipo_cambio_pais;

			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Tipo Cambio Pais modificado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_tipo_cambio_pais',v_parametros.id_tipo_cambio_pais::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'CONTA_TCPA_ELI'
 	#DESCRIPCION:	Eliminacion de registros
 	#AUTOR:		ivaldivia
 	#FECHA:		07-08-2019 14:12:25
	***********************************/

	elsif(p_transaccion='CONTA_TCPA_ELI')then

		begin
        	select mo.id_moneda into v_id_moneda
            from conta.tmoneda_pais mo
            inner join conta.ttipo_cambio_pais cam on cam.id_moneda_pais = mo.id_moneda_pais
            where cam.id_tipo_cambio_pais = v_parametros.id_tipo_cambio_pais;

        	/*Eliminamos tambien en ENDESIS*/

            /*Establecemos la conexion con ENDESIS*/
            v_cadena_cnx =  migra.f_obtener_cadena_conexion();
            v_conexion =  (SELECT dblink_connect(v_cadena_cnx));
            /*************************************************/

            v_consulta = '
            		DELETE FROM param.tpm_tipo_cambio
                    WHERE param.tpm_tipo_cambio.id_tipo_cambio = '||v_parametros.id_tipo_cambio_pais||';';


             IF(v_conexion!='OK') THEN

			                 raise exception 'FALLA CONEXION A LA BASE DE DATOS CON DBLINK';

			             ELSE


                             perform dblink_exec(v_cadena_cnx,v_consulta,TRUE);

                             v_res_cone=(select dblink_disconnect());



             END IF;

            /************************************/


            --Sentencia de la eliminacion
			delete from conta.ttipo_cambio_pais
            where id_tipo_cambio_pais=v_parametros.id_tipo_cambio_pais;





            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Tipo Cambio Pais eliminado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_tipo_cambio_pais',v_parametros.id_tipo_cambio_pais::varchar);
            v_resp = pxp.f_agrega_clave(v_resp,'id_moneda',v_id_moneda::varchar);

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

ALTER FUNCTION conta.ft_tipo_cambio_pais_ime (p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
  OWNER TO postgres;
