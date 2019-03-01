CREATE OR REPLACE FUNCTION conta.f_consolidacion_fondos_sel (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Sistema de Contabilidad
 FUNCION: 		conta.f_consolidacion_fondos_sel
 DESCRIPCION:   Funcion para reporte de consolidacion de fondos 'conta.f_consolidacion_fondos_sel'
 AUTOR: 		 (admin)
 FECHA:	        07-12-2018 12:55:30
 COMENTARIOS:
***************************************************************************
 HISTORIAL DE MODIFICACIONES:

 DESCRIPCION:
 AUTOR:
 FECHA:
***************************************************************************/

DECLARE

	v_consulta    		varchar;
	v_parametros  		record;
	v_nombre_funcion   	text;
	v_resp				varchar;


BEGIN

	v_nombre_funcion = 'conta.f_consolidacion_fondos_sel';
    v_parametros = pxp.f_get_record(p_tabla);

	  /*********************************
 	#TRANSACCION:  'CONTA_RCONFONFIN_SEL'
 	#DESCRIPCION:	Obtener reporte de consolidadion de fondos no finalizados
 	#AUTOR:		admin
 	#FECHA:		07-12-2018
	***********************************/

	if(p_transaccion='CONTA_RCONFONFIN_SEL')then

    	begin

        	v_consulta = 'select
                                nf.nro_tramite::varchar,
                                nf.beneficiario::varchar,
                                nf.nro_cheque::varchar,
                                nf.codigo_categoria::varchar,
                                nf.partida::varchar,
                                nf.importe::numeric


                        from conta.vconso_fondos_no_fin nf



                  where nf.fecha BETWEEN '''||v_parametros.fecha_ini||''' and '''||v_parametros.fecha_fin ||'''

                  	';

       --raise notice '%', v_consulta;
            return v_consulta;

--raise notice '%', v_consulta;
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