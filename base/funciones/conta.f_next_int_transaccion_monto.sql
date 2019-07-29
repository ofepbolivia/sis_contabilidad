CREATE OR REPLACE FUNCTION conta.f_next_int_transaccion_monto (
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
 FUNCION: 		f_next_int_transaccion_monto
 DESCRIPCION:   Funcion que devuelve la diferencia entre el actual y el anterior registro entre comprobantes.
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
	v_monto_anterior_1		numeric = 0;
    v_monto_actual_1		numeric = 0;
    v_monto_anterior_2		numeric = 0;
    v_monto_actual_2		numeric = 0;
    v_tipo					varchar = '';
    v_contador				integer = 0;
    v_saldo_anterior		numeric = 0;
    v_id_int_comprobante	integer;
    v_importe_haber 		numeric = 0;

BEGIN

    v_nombre_funcion = 'conta.f_next_int_transaccion';
	v_tipo  = p_tipo;
    for v_record in select tic.id_int_comprobante
                        from conta.tint_comprobante tic
                        where tic.nro_tramite = p_nro_tramite and tic.id_int_comprobante <= p_id_int_comprobante
                        order by tic.id_int_comprobante desc limit 2  loop

        select coalesce(case when v_tipo = 'haber' then tit.importe_haber_mb else tit.importe_debe_mb end,0), tit.importe_haber_mb
        into v_monto_actual_1, v_importe_haber
        from conta.tint_transaccion tit
        where tit.id_int_comprobante =  v_record.id_int_comprobante and case when v_tipo = 'haber' then tit.importe_haber_mb != 0  else tit.importe_debe_mb != 0 end;

        v_contador = v_contador + 1;
         v_monto_anterior_1 = v_monto_actual_1 - v_monto_anterior_1 ;

        v_id_int_comprobante = v_record.id_int_comprobante;

        /*if v_contador = 2  and (p_tipo ='haber' or p_tipo ='debe') then
            v_saldo_anterior = conta.f_next_int_transaccion_saldo(p_id_int_transaccion, v_id_int_comprobante, p_nro_tramite, p_tipo, v_monto_actual_1);
        end if;*/

        v_tipo = case when v_tipo = 'haber' then 'debe' else 'haber' end;
    end loop;

	--calcula saldo anterior
    if v_contador = 2  and (p_tipo ='haber' or p_tipo ='debe') then
      v_tipo  = p_tipo;
      for v_id_comprobante_saldo in select tic.id_int_comprobante
                                    from conta.tint_comprobante tic
                                    where tic.nro_tramite = p_nro_tramite and tic.id_int_comprobante < v_id_int_comprobante
                                    order by tic.id_int_comprobante desc limit 2  loop

          select case when v_tipo = 'haber' then tit.importe_haber_mb else tit.importe_debe_mb end
          into v_monto_actual_2
          from conta.tint_transaccion tit
          where tit.id_int_comprobante =  v_id_comprobante_saldo and case when v_tipo = 'haber' then tit.importe_haber_mb != 0  else tit.importe_debe_mb != 0 end;

          v_monto_anterior_2 = v_monto_actual_2 - v_monto_anterior_2;
          v_tipo = case when v_tipo = 'haber' then 'debe' else 'haber' end;
      end loop;
    end if;

    if p_tipo = 'haber' then
    	if v_contador = 2  then
            return v_monto_anterior_1*(-1)+ v_monto_anterior_2;
        else
        	return p_monto;
        end if;
    else
        if v_contador = 2 then
            return  v_monto_anterior_2 - p_monto + v_importe_haber;
        else
        	return v_monto_anterior_1;
        end if;

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