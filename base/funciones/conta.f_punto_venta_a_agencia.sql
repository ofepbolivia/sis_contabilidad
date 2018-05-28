CREATE OR REPLACE FUNCTION conta.f_punto_venta_a_agencia (
)
RETURNS void AS
$body$
DECLARE
    v_nombre_funcion   	text;
    v_resp    			varchar;
    v_mensaje 			varchar;
    v_punto_venta			record;
  	v_record				record;
    v_codigo 				varchar;

BEGIN

	FOR v_punto_venta IN  (select 	p.codigo,
        							d.id_doc_compra_venta
        							from conta.tdoc_compra_venta d
									left join vef.tpunto_venta p on p.id_punto_venta = d.id_punto_venta
                                    where d.id_punto_venta is not null ) LOOP

		select  id_agencia,
				codigo
                into
                v_record
        		from obingresos.tagencia ota
                where ota.codigo =v_punto_venta.codigo;

 UPDATE conta.tdoc_compra_venta  SET
          id_agencia = v_record.id_agencia,
          id_punto_venta = null
          where id_doc_compra_venta = v_punto_venta.id_doc_compra_venta;


END LOOP;
END;
$body$
LANGUAGE 'plpgsql'
VOLATILE
CALLED ON NULL INPUT
SECURITY INVOKER
COST 100;