CREATE OR REPLACE FUNCTION conta.f_auxiliar_ime (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Sistema de Contabilidad
 FUNCION: 		conta.f_auxiliar_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'conta.tauxiliar'
 AUTOR: 		Gonzalo Sarmiento Sejas
 FECHA:	        21-02-2013 20:44:52
 COMENTARIOS:
***************************************************************************
 HISTORIAL DE MODIFICACIONES:

 DESCRIPCION:
 AUTOR:
 FECHA:
***************************************************************************/

DECLARE

	v_nro_requerimiento    	integer;
	v_parametros           	record;
	v_id_requerimiento     	integer;
	v_resp		            varchar;
	v_nombre_funcion        text;
	v_mensaje_error         text;
	v_id_auxiliar	        integer;
	--variables para control de codigo y nombre de auxiliar duplicado
    v_contador				integer;
	v_valid					varchar;
    v_registros				record;

    --variables replicacion auxiliar endesis (franklin.espinoza)
    v_cadena_conn			varchar;
    v_cadena_execute		varchar;
    v_usuario_reg			varchar;
    v_res_conn				varchar;
	v_existencia			integer;
BEGIN

    v_nombre_funcion = 'conta.f_auxiliar_ime';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'CONTA_AUXCTA_INS'
 	#DESCRIPCION:	Insercion de registros
 	#AUTOR:		Gonzalo Sarmiento Sejas
 	#FECHA:		21-02-2013 20:44:52
	***********************************/

	if(p_transaccion='CONTA_AUXCTA_INS')then

        begin

			select count(*) into v_existencia
             from conta.tauxiliar auxi
             where auxi.codigo_auxiliar = v_parametros.codigo_auxiliar or
                   auxi.nombre_auxiliar = v_parametros.nombre_auxiliar;

            if v_existencia > 0 then
             raise exception 'El codigo o nombre del auxiliar ya se encuentran registrados';
            end if;

        	--Sentencia de la insercion
        	insert into conta.tauxiliar(
			--id_empresa,
			estado_reg,
			codigo_auxiliar,
			nombre_auxiliar,
			fecha_reg,
			id_usuario_reg,
			id_usuario_mod,
			fecha_mod,
            corriente
          	) values(
			--v_parametros.id_empresa,
			'activo',
			v_parametros.codigo_auxiliar,
			v_parametros.nombre_auxiliar,
			now(),
			p_id_usuario,
			null,
			null,
            --24-03-2021 (may) modificacion que se quite el campo y se registre todos como NO
            --v_parametros.corriente
            'no'

			)RETURNING id_auxiliar into v_id_auxiliar;

            select tu.cuenta
            into v_usuario_reg
            from segu.tusuario tu
            where tu.id_usuario = p_id_usuario;

			--24-03-2021 (may) se quita conexion a endesis
            /*v_cadena_conn =  migra.f_obtener_cadena_conexion();

            v_cadena_execute = 'select sci.f_insertar_replica_aux('''||v_parametros.codigo_auxiliar::varchar||''','''||v_parametros.nombre_auxiliar::varchar||''',
            '''||v_usuario_reg::varchar||''','''||v_parametros.corriente::varchar||''',''INS'')';

            v_resp =  (SELECT dblink_connect(v_cadena_conn));

            if v_resp != 'OK' THEN
            	raise exception 'FALLA CONEXION A LA BASE DE DATOS ENDESIS CON DBLINK';
            else
                PERFORM * FROM dblink(v_cadena_execute,true) AS ( xx varchar);
            end if;

            v_res_conn=(select dblink_disconnect());*/

			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Auxiliares de Cuenta almacenado(a) con exito (id_auxiliar'||v_id_auxiliar||')');
            v_resp = pxp.f_agrega_clave(v_resp,'id_auxiliar',v_id_auxiliar::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'CONTA_AUXCTA_MOD'
 	#DESCRIPCION:	Modificacion de registros
 	#AUTOR:		Gonzalo Sarmiento Sejas
 	#FECHA:		21-02-2013 20:44:52
	***********************************/

	elsif(p_transaccion='CONTA_AUXCTA_MOD')then

		begin
			--Sentencia de la modificacion
			update conta.tauxiliar set
			--id_empresa = v_parametros.id_empresa,
			codigo_auxiliar = v_parametros.codigo_auxiliar,
			nombre_auxiliar = v_parametros.nombre_auxiliar,
			id_usuario_mod = p_id_usuario,
			fecha_mod = now(),
            --24-03-2021 (may) modificacion que se quite el campo y se registre todos como NO
            --corriente = v_parametros.corriente
            corriente = 'no'
			where id_auxiliar=v_parametros.id_auxiliar;

            select tu.cuenta
            into v_usuario_reg
            from segu.tusuario tu
            where tu.id_usuario = p_id_usuario;

			--24-03-2021 (may) se quita conexion a endesis
            /*v_cadena_conn =  migra.f_obtener_cadena_conexion();

            v_cadena_execute = 'select sci.f_insertar_replica_aux('''||v_parametros.codigo_auxiliar::varchar||''','''||v_parametros.nombre_auxiliar::varchar||''',
            '''||v_usuario_reg::varchar||''','''||v_parametros.corriente::varchar||''',''UPD'')';

            v_resp =  (SELECT dblink_connect(v_cadena_conn));

            if v_resp != 'OK' THEN
            	raise exception 'FALLA CONEXION A LA BASE DE DATOS ENDESIS CON DBLINK';
            else
                PERFORM * FROM dblink(v_cadena_execute,true) AS ( xx varchar);
            end if;

            v_res_conn=(select dblink_disconnect());*/

			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Auxiliares de Cuenta modificado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_auxiliar',v_parametros.id_auxiliar::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'CONTA_AUXCTA_ELI'
 	#DESCRIPCION:	Eliminacion de registros
 	#AUTOR:		Gonzalo Sarmiento Sejas
 	#FECHA:		21-02-2013 20:44:52
	***********************************/

	elsif(p_transaccion='CONTA_AUXCTA_ELI')then

		begin
			--Sentencia de la eliminacion
            -- 24-03-2021 (may) modificacion solo update
			/*delete from conta.tauxiliar
            where id_auxiliar=v_parametros.id_auxiliar;*/

            UPDATE conta.tauxiliar SET
            estado_reg = 'inactivo'
            WHERE id_auxiliar=v_parametros.id_auxiliar;

			--24-03-2021 (may) se quita conexion a endesis
        	/*v_cadena_conn =  migra.f_obtener_cadena_conexion();

            v_cadena_execute = 'select sci.f_insertar_replica_aux('''||v_parametros.codigo_auxiliar::varchar||''','''||v_parametros.nombre_auxiliar::varchar||''',
            ''''::varchar,''''::varchar,''DEL'')';

            v_resp =  (SELECT dblink_connect(v_cadena_conn));

            if v_resp != 'OK' THEN
            	raise exception 'FALLA CONEXION A LA BASE DE DATOS ENDESIS CON DBLINK';
            else
                PERFORM * FROM dblink(v_cadena_execute,true) AS ( xx varchar);
            end if;

            v_res_conn=(select dblink_disconnect());*/

            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Auxiliares de Cuenta eliminado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_auxiliar',v_parametros.id_auxiliar::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;
	/*********************************
 	#TRANSACCION:  'CONTA_COD_AUX_VAL'
 	#DESCRIPCION:	Control para que no se repita el codigo auxiliar y nombre auxiliar
 	#AUTOR:		Franklin Espinoza
 	#FECHA:		13-06-2017 10:44:52
	***********************************/

	elsif(p_transaccion='CONTA_COD_AUX_VAL')then

		begin
			select count(taux.id_auxiliar)
            INTO v_contador
            from conta.tauxiliar taux
            where    taux.codigo_auxiliar = trim(both ' ' from v_parametros.codigo_auxiliar)

                 AND taux.nombre_auxiliar = trim(both ' ' from v_parametros.nombre_auxiliar) ;

            IF(v_contador>=1)THEN
        		v_valid = 'true';
            ELSE
            	v_valid = 'false';
			END IF;
            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Existe el Reclamo');
            v_resp = pxp.f_agrega_clave(v_resp,'v_valid',v_valid);
            --Devuelve la respuesta
            return v_resp;
		end;


     /*********************************
    #TRANSACCION:  'CONTA_COD_AUX_GET'
    #DESCRIPCION:   Recupera los datos de la auxiliar
    #AUTOR:     manu
    #FECHA:     10-10-2017 16:03:19
    ***********************************/

    elsif(p_transaccion='CONTA_COD_AUX_GET')then
        begin
            --Sentencia de la eliminacion
            select a.id_auxiliar,a.id_empresa,a.nombre_auxiliar,a.codigo_auxiliar,a.corriente
            into v_registros
            from conta.tauxiliar a
            where a.estado_reg='activo' and a.id_auxiliar= v_parametros.id_auxiliar;
            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Empresa recuperada(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_auxiliar',v_registros.id_auxiliar::varchar);
            v_resp = pxp.f_agrega_clave(v_resp,'id_empresa',v_registros.id_empresa::varchar);
            v_resp = pxp.f_agrega_clave(v_resp,'nombre_auxiliar',v_registros.nombre_auxiliar::varchar);
            v_resp = pxp.f_agrega_clave(v_resp,'codigo_auxiliar',v_registros.codigo_auxiliar::varchar);
            v_resp = pxp.f_agrega_clave(v_resp,'corriente',v_registros.corriente::varchar);
            --Devuelve la respuesta
            return v_resp;
        end;

        /*********************************
        #TRANSACCION:  'CONTA_AUXCTACO_INS'
        #DESCRIPCION:	Insercion de registros
        #AUTOR:		Maylee Perez Pastor
        #FECHA:		24-03-2021 20:44:52
        ***********************************/

        elsif(p_transaccion='CONTA_AUXCTACO_INS')then

            begin

                select count(*) into v_existencia
                 from conta.tauxiliar auxi
                 where auxi.codigo_auxiliar = v_parametros.codigo_auxiliar or
                       auxi.nombre_auxiliar = v_parametros.nombre_auxiliar;

                if v_existencia > 0 then
                 raise exception 'El codigo o nombre del auxiliar ya se encuentran registrados';
                end if;

                --Sentencia de la insercion
                insert into conta.tauxiliar(
                --id_empresa,
                estado_reg,
                codigo_auxiliar,
                nombre_auxiliar,
                fecha_reg,
                id_usuario_reg,
                id_usuario_mod,
                fecha_mod,
                corriente,
                tipo
                ) values(
                --v_parametros.id_empresa,
                'activo',
                v_parametros.codigo_auxiliar,
                v_parametros.nombre_auxiliar,
                now(),
                p_id_usuario,
                null,
                null,
                --24-03-2021 (may) modificacion que se quite el campo y se registre todos como NO
                --v_parametros.corriente
                'si',
                v_parametros.tipo

                )RETURNING id_auxiliar into v_id_auxiliar;

                select tu.cuenta
                into v_usuario_reg
                from segu.tusuario tu
                where tu.id_usuario = p_id_usuario;


                --Definicion de la respuesta
                v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Auxiliares de Cuenta almacenado(a) con exito (id_auxiliar'||v_id_auxiliar||')');
                v_resp = pxp.f_agrega_clave(v_resp,'id_auxiliar',v_id_auxiliar::varchar);

                --Devuelve la respuesta
                return v_resp;

            end;

        /*********************************
        #TRANSACCION:  'CONTA_AUXCTACO_MOD'
        #DESCRIPCION:	Modificacion de registros
        #AUTOR:		Maylee Perez Pastor
        #FECHA:		24-03-2021 20:44:52
        ***********************************/

        elsif(p_transaccion='CONTA_AUXCTACO_MOD')then

            begin
                --Sentencia de la modificacion
                update conta.tauxiliar set
                --id_empresa = v_parametros.id_empresa,
                codigo_auxiliar = v_parametros.codigo_auxiliar,
                nombre_auxiliar = v_parametros.nombre_auxiliar,
                id_usuario_mod = p_id_usuario,
                fecha_mod = now(),
                --24-03-2021 (may) modificacion que se quite el campo y se registre todos como NO
                --corriente = v_parametros.corriente
                corriente = 'si',
                tipo = v_parametros.tipo
                where id_auxiliar=v_parametros.id_auxiliar;

                select tu.cuenta
                into v_usuario_reg
                from segu.tusuario tu
                where tu.id_usuario = p_id_usuario;

                --Definicion de la respuesta
                v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Auxiliares de Cuenta modificado(a)');
                v_resp = pxp.f_agrega_clave(v_resp,'id_auxiliar',v_parametros.id_auxiliar::varchar);

                --Devuelve la respuesta
                return v_resp;

            end;

        /*********************************
        #TRANSACCION:  'CONTA_AUXCTACO_ELI'
        #DESCRIPCION:	Eliminacion de registros
        #AUTOR:		Maylee Perez Pastor
        #FECHA:		24-03-2021 20:44:52
        ***********************************/

        elsif(p_transaccion='CONTA_AUXCTACO_ELI')then

            begin
                --Sentencia de la eliminacion
                /*delete from conta.tauxiliar
                where id_auxiliar=v_parametros.id_auxiliar;*/

                UPDATE conta.tauxiliar SET
                estado_reg = 'inactivo'
                WHERE id_auxiliar=v_parametros.id_auxiliar;


                --Definicion de la respuesta
                v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Auxiliares de Cuenta eliminado(a)');
                v_resp = pxp.f_agrega_clave(v_resp,'id_auxiliar',v_parametros.id_auxiliar::varchar);

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
