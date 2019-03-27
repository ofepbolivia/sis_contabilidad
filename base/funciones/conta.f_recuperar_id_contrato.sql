CREATE OR REPLACE FUNCTION conta.f_recuperar_id_contrato (
)
RETURNS varchar AS
$body$
DECLARE
  v_nombre_funcion   	text;
	v_resp				varchar;
  v_datos record;
  v_id_contrato integer;
  v_contrato	record;
BEGIN
  v_nombre_funcion = 'conta.f_recuperar_id_contrato';


for v_datos in (select
				rev.id_agencia,
				rev.nro_contrato
				from conta.tcomisionistas rev
				where rev.id_agencia is not null and rev.nro_contrato is not null
                group by rev.id_agencia,
				rev.nro_contrato)loop

				select con.id_contrato, con.numero, con.id_agencia
                into v_contrato
                from leg.tcontrato con
                where RIGHT (con.numero,19) = RIGHT (v_datos.nro_contrato::varchar,19) and con.id_agencia = v_datos.id_agencia;

                update conta.trevisar_comisionistas set
                id_contrato = v_contrato.id_contrato
                where nro_contrato = v_contrato.numero::varchar and id_agencia = v_contrato.id_agencia;


end loop;

v_resp = pxp.f_agrega_clave(v_resp, 'mensaje', 'anexos actualizaciones automatic(a)');
v_resp = pxp.f_agrega_clave(v_resp, 'id_contrato', v_id_contrato :: VARCHAR);

--Devuelve la respuesta
RETURN v_resp;
END;
$body$
LANGUAGE 'plpgsql'
VOLATILE
CALLED ON NULL INPUT
SECURITY INVOKER
COST 100;
