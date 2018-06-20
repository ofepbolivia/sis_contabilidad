CREATE OR REPLACE FUNCTION conta.f_agt_ids (
)
RETURNS varchar AS
$body$
DECLARE
  v_nombre_funcion varchar;
  v_resp varchar;
  v_datos record;
  v_id varchar;
  v_contador  integer;
BEGIN
  v_nombre_funcion = 'conta.f_armar_error_presupuesto';

    CREATE TEMPORARY TABLE temp_evaluacion (
                                      id_agencia varchar
                                      )ON COMMIT DROP;

  for v_datos in (select doc.id_agencia
from conta.tdoc_compra_venta doc
where doc.id_agencia is not null)loop




               IF (  select count( ag.id_agencia)

                    from obingresos.tagencia ag
                    where ag.id_agencia = v_datos.id_agencia) = 0 then

                  insert into  temp_evaluacion (
                                      id_agencia
                                      )select v_datos.id_agencia::varchar;

                  end if;


end loop;

  select pxp.list(id_agencia)
             INTO v_id
             from temp_evaluacion ;

RETURN v_id;

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