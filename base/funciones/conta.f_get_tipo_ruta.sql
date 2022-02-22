CREATE OR REPLACE FUNCTION conta.f_get_tipo_ruta (
  p_sistema_origen varchar,
  p_id_origen integer
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Sistema Contabilidad
 FUNCION: 		conta.f_get_tipo_ruta
 DESCRIPCION:   Funcion que recupera el tipo ruta
 AUTOR: 		(franklin.espinoza)
 FECHA:	        10-12-2021 15:15:26
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
    v_tipo_ruta 			varchar;
BEGIN

    v_nombre_funcion = 'conta.f_get_tipo_ruta';

	if p_sistema_origen = 'ERP' then
    	select case when gas.desc_ingas like '%INTERNACIONAL%' then 'I' else 'N' end
        into v_tipo_ruta
        from vef.tventa ven
        inner join vef.tventa_detalle det on det.id_venta = ven.id_venta
        inner join param.tconcepto_ingas gas on gas.id_concepto_ingas = det.id_producto
        where ven.id_venta = p_id_origen;
    elsif p_sistema_origen = 'CARGA' then
    	select case when gas.desc_ingas like '%INTERNACIONAL%' then 'I' else 'N' end
        into v_tipo_ruta
        from vef.tventa ven
        inner join vef.tventa_detalle det on det.id_venta = ven.id_venta
        inner join param.tconcepto_ingas gas on gas.id_concepto_ingas = det.id_producto
        where ven.id_sistema_origen = p_id_origen;
    end if;

    RETURN v_tipo_ruta;

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

ALTER FUNCTION conta.f_get_tipo_ruta (p_sistema_origen varchar, p_id_origen integer)
  OWNER TO postgres;