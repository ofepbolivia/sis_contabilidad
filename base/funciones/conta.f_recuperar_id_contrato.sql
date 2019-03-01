CREATE OR REPLACE FUNCTION conta.f_recuperar_id_contrato (
)
RETURNS varchar AS
$body$
DECLARE
  v_nombre_funcion varchar;
  v_datos record;
  v_id_contrato integer;
BEGIN
  v_nombre_funcion = 'conta.f_recuperar_id_contrato';


for v_datos in (select
				rev.id_agencia,
				rev.nro_contrato
				from conta.trevisar_comisionistas rev
				where rev.id_agencia is not null)loop

				select con.id_contrato
                into v_id_contrato
                from leg.tcontrato con
                where con.numero = v_datos.nro_contrato;

                update conta.trevisar_comisionistas set
                id_contrato = v_id_contrato
                where nro_contrato = v_datos.nro_contrato;


end loop;

RETURN v_id_contrato;

/*EXCEPTION
WHEN OTHERS THEN
			v_resp='';
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje',SQLERRM);
			v_resp = pxp.f_agrega_clave(v_resp,'codigo_error',SQLSTATE);
			v_resp = pxp.f_agrega_clave(v_resp,'procedimientos',v_nombre_funcion);
			raise exception '%',v_resp;*/
END;
$body$
LANGUAGE 'plpgsql'
VOLATILE
CALLED ON NULL INPUT
SECURITY INVOKER
COST 100;
