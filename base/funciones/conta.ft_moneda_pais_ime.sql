CREATE OR REPLACE FUNCTION conta.ft_moneda_pais_ime (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Sistema de Contabilidad
 FUNCION: 		conta.ft_moneda_pais_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'conta.tmoneda_pais'
 AUTOR: 		 (ivaldivia)
 FECHA:	        07-08-2019 14:05:49
 COMENTARIOS:
***************************************************************************
 HISTORIAL DE MODIFICACIONES:
#ISSUE				FECHA				AUTOR				DESCRIPCION
 #0				07-08-2019 14:05:49								Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'conta.tmoneda_pais'
 #
 ***************************************************************************/

DECLARE

	v_nro_requerimiento    	integer;
	v_parametros           	record;
	v_id_requerimiento     	integer;
	v_resp		            varchar;
	v_nombre_funcion        text;
	v_mensaje_error         text;
	v_id_moneda_pais	integer;

BEGIN

    v_nombre_funcion = 'conta.ft_moneda_pais_ime';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'CONTA_MONPA_INS'
 	#DESCRIPCION:	Insercion de registros
 	#AUTOR:		ivaldivia
 	#FECHA:		07-08-2019 14:05:49
	***********************************/

	if(p_transaccion='CONTA_MONPA_INS')then

    /*********************VERIFICA EXISTENCIA DEL MONEDA BASE Y LA PRIORIDAD*************************/
    	IF EXISTS(SELECT 1 FROM conta.tmoneda_pais mo
        		  WHERE mo.prioridad= v_parametros.prioridad AND
            	  mo.id_lugar = v_parametros.id_lugar) THEN
                IF v_parametros.prioridad = 1 THEN
                	raise exception 'Inserciòn no realizada: Ya existe una moneda base para este pais.';
                ELSE
                	raise exception 'Inserciòn no realizada: Ya existe una moneda con esta prioridad.';
                END IF;
        END IF;
    /************************************************************************************************/

    /***********************VERIFICA LA EXISTENCIA DE UNA REGISTRO DE  MONEDA PARA EL PAIS***************************************************/
        IF EXISTS(SELECT 1 FROM conta.tmoneda_pais mo
        		  WHERE mo.id_moneda= v_parametros.id_moneda AND
            	   mo.id_lugar = v_parametros.id_lugar) THEN
                raise exception 'Inserciòn no realizada: La moneda seleccionada ya esta registrada para este pais.';
        END IF;
	/*********************************************************************************************************************************/
        begin
        	--Sentencia de la insercion
        	insert into conta.tmoneda_pais(
			estado_reg,
			id_moneda,
			origen,
			prioridad,
			tipo_actualizacion,
			id_lugar,
			id_usuario_reg,
			fecha_reg,
			id_usuario_ai,
			usuario_ai,
			id_usuario_mod,
			fecha_mod
          	) values(
			'activo',
			v_parametros.id_moneda,
			v_parametros.origen,
			v_parametros.prioridad,
			v_parametros.tipo_actualizacion,
			v_parametros.id_lugar,
			p_id_usuario,
			now(),
			v_parametros._id_usuario_ai,
			v_parametros._nombre_usuario_ai,
			null,
			null
			)RETURNING id_moneda_pais into v_id_moneda_pais;


			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Moneda Pais almacenado(a) con exito (id_moneda_pais'||v_id_moneda_pais||')');
            v_resp = pxp.f_agrega_clave(v_resp,'id_moneda_pais',v_id_moneda_pais::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'CONTA_MONPA_MOD'
 	#DESCRIPCION:	Modificacion de registros
 	#AUTOR:		ivaldivia
 	#FECHA:		07-08-2019 14:05:49
	***********************************/

	elsif(p_transaccion='CONTA_MONPA_MOD')then

    	/**************************VERIFICA EXISTENCIA DEL REGISTRO****************************/
            IF NOT EXISTS(SELECT 1 FROM conta.tmoneda_pais mo
                          WHERE mo.id_moneda_pais=v_parametros.id_moneda_pais) THEN
                raise exception 'Modificación no realizada: no existe el registro % en la tabla conta.tmoneda_pais',v_parametros.id_moneda_pais;
            END IF;

        /***************************VERIFICA EXISTENCIA DEL MONEDA BASE Y LA PRIORIDAD**************************************/
            IF EXISTS(SELECT 1 FROM conta.tmoneda_pais mo
            		  WHERE mo.id_moneda_pais<>v_parametros.id_moneda_pais
            		  AND mo.prioridad= v_parametros.prioridad AND
            		  mo.id_lugar= v_parametros.id_lugar) THEN
                IF v_parametros.prioridad= 1 THEN
                	raise exception 'Inserciòn no realizada: Ya existe una moneda base para este pais.';
                ELSE
                	raise exception 'Inserciòn no realizada: Ya existe una moneda con esta prioridad.';
                END IF;
            END IF;
         /*******************************************************************************************************************/

         /********************VERIFICA LA EXISTENCIA DE UNA REGISTRO DE  MONEDA PARA EL PAIS*****************/
            IF EXISTS(SELECT 1 FROM conta.tmoneda_pais mo
             		  WHERE mo.id_moneda_pais<>v_parametros.id_moneda_pais
            					AND mo.id_moneda= v_parametros.id_moneda AND
            						mo.id_lugar= v_parametros.id_lugar) THEN
                      raise exception 'Inserciòn no realizada: La moneda seleccionada ya esta registrada para este pais.';
          	END IF;
    	/*******************************************************************************/

		begin
			--Sentencia de la modificacion
			update conta.tmoneda_pais set
			id_moneda = v_parametros.id_moneda,
			origen = v_parametros.origen,
			prioridad = v_parametros.prioridad,
			tipo_actualizacion = v_parametros.tipo_actualizacion,
			id_lugar = v_parametros.id_lugar,
			id_usuario_mod = p_id_usuario,
			fecha_mod = now(),
			id_usuario_ai = v_parametros._id_usuario_ai,
			usuario_ai = v_parametros._nombre_usuario_ai
			where id_moneda_pais=v_parametros.id_moneda_pais;

			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Moneda Pais modificado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_moneda_pais',v_parametros.id_moneda_pais::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'CONTA_MONPA_ELI'
 	#DESCRIPCION:	Eliminacion de registros
 	#AUTOR:		ivaldivia
 	#FECHA:		07-08-2019 14:05:49
	***********************************/

	elsif(p_transaccion='CONTA_MONPA_ELI')then

    	 --VERIFICACIÓN DE EXISTENCIA DEL REGISTRO
            IF NOT EXISTS(SELECT 1 FROM conta.tmoneda_pais mo
                          WHERE mo.id_moneda_pais=v_parametros.id_moneda_pais) THEN
                raise exception 'Eliminación no realizada: registro % en conta.tmoneda_pais inexistente',v_parametros.id_moneda_pais;
            END IF;

		begin
			--Sentencia de la eliminacion
			delete from conta.tmoneda_pais
            where id_moneda_pais=v_parametros.id_moneda_pais;

            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Moneda Pais eliminado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_moneda_pais',v_parametros.id_moneda_pais::varchar);

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

ALTER FUNCTION conta.ft_moneda_pais_ime (p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
  OWNER TO postgres;
