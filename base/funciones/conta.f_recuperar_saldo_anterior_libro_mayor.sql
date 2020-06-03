CREATE OR REPLACE FUNCTION conta.f_recuperar_saldo_anterior_libro_mayor (
  p_id_cuenta text,
  p_id_auxiliar text,
  p_fecha_ini text,
  p_id_partida text,
  p_id_centro_costo text,
  p_fecha_ini_gestion text,
  p_id_orden_trabajo text
)
RETURNS SETOF record AS
$body$
declare
consulta text;
filtro text;
begin



filtro := ''||p_id_cuenta||'and '||p_id_auxiliar||'and icbte.fecha::date < '''||p_fecha_ini||'''and '||p_id_partida||'and '||p_id_centro_costo||'and '||p_id_orden_trabajo||'';

consulta := 'select
        COALESCE ((SUM (COALESCE(transa.importe_haber_mb,0)) - SUM (COALESCE(transa.importe_debe_mb,0))),0)::numeric as saldo_anterior,
        COALESCE (SUM (COALESCE(transa.importe_debe_mb,0)),0) as total_debe_anterior,
        COALESCE (SUM (COALESCE(transa.importe_haber_mb,0)),0) as total_haber_anterior
        from conta.tint_transaccion transa
        inner join conta.tint_comprobante icbte on icbte.id_int_comprobante = transa.id_int_comprobante
        inner join param.tperiodo per on per.id_periodo = icbte.id_periodo
        where icbte.estado_reg = ''validado''
              and icbte.fecha::date  >= '''||p_fecha_ini_gestion||''' and '||filtro;
return query execute consulta;
end;
$body$
LANGUAGE 'plpgsql'
VOLATILE
CALLED ON NULL INPUT
SECURITY INVOKER
COST 100 ROWS 1000;

ALTER FUNCTION conta.f_recuperar_saldo_anterior_libro_mayor (p_id_cuenta text, p_id_auxiliar text, p_fecha_ini text, p_id_partida text, p_id_centro_costo text, p_fecha_ini_gestion text, p_id_orden_trabajo text)
  OWNER TO postgres;
