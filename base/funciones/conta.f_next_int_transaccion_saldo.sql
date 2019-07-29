CREATE OR REPLACE FUNCTION conta.f_next_int_transaccion_saldo (
  p_id_int_transaccion integer,
  p_id_int_comprobante integer,
  p_nro_tramite varchar,
  p_tipo varchar,
  p_monto numeric
)
RETURNS numeric AS
$body$
/**************************************************************************
 SISTEMA:		Sistema Contabilidad
 FUNCION: 		conta.f_next_int_transaccion_saldo
 DESCRIPCION:   Funcion que devuelve saldo.
 AUTOR: 		(franklin.espinoza)
 FECHA:	        25-06-2019
 COMENTARIOS:
***************************************************************************
 HISTORIAL DE MODIFICACIONES:

 DESCRIPCION:
 AUTOR:
 FECHA:
***************************************************************************/

DECLARE

	v_resp		            varchar='';
	v_nombre_funcion        text;
	v_record 				record;
    v_id_comprobante_saldo	integer;
	v_monto_anterior		numeric = 0;
    v_monto_actual			numeric = 0;
    v_tipo					varchar = '';


BEGIN

    v_nombre_funcion = 'conta.f_next_int_transaccion_saldo';
	v_tipo  = p_tipo;

    for v_record in select tic.id_int_comprobante
                        from conta.tint_comprobante tic
                        where tic.nro_tramite = p_nro_tramite and tic.id_int_comprobante < p_id_int_comprobante
                        order by tic.id_int_comprobante desc limit 2  loop

        select coalesce(case when v_tipo = 'haber' then tit.importe_haber_mb else tit.importe_debe_mb end,0)
        into v_monto_actual
        from conta.tint_transaccion tit
        where tit.id_int_comprobante =  v_record.id_int_comprobante and case when v_tipo = 'haber' then tit.importe_haber_mb != 0  else tit.importe_debe_mb != 0 end;
        v_monto_anterior = v_monto_actual - v_monto_anterior;
        v_tipo = case when v_tipo = 'haber' then 'debe' else 'haber' end;

    end loop;

    return v_monto_anterior;
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