CREATE OR REPLACE FUNCTION conta.f_actualizar_nit_contrato (
  p_id_contrato integer,
  p_id_agencia integer,
  p_nit varchar,
  p_numero_contrato varchar
)
RETURNS varchar AS
$body$
DECLARE
  v_nombre_funcion   	text;
  v_resp				varchar;
  v_id_gestion			integer;
  v_periodo				record;
  v_datos				record;
  v_id_contrato			record;
  v_nro_contrato		varchar;
BEGIN
  v_nombre_funcion = 'conta.f_actualizar_NIT_contrato';


  update obingresos.tagencia set
  nit = p_nit
  where id_agencia = p_id_agencia;

  update leg.tcontrato set
  numero = p_numero_contrato
  where id_contrato = p_id_contrato;



return 'Exito';

END;
$body$
LANGUAGE 'plpgsql'
VOLATILE
CALLED ON NULL INPUT
SECURITY INVOKER
COST 100;