CREATE OR REPLACE FUNCTION conta.f_replicar_cuenta_auxiliar_por_gestion (
)
RETURNS void AS
$body$
DECLARE
    v_nombre_funcion   	text;
    v_resp    			varchar;
    v_mensaje 			varchar;
    v_record			record;
    v_id_cuenta			integer;
    v_dato				record;

BEGIN
  v_nombre_funcion = 'conta.f_replicar_cuenta_auxiliar_por_gestion';
  for v_record in (select 	c.id_cuenta,
                            c.nombre_cuenta,
                            au.id_auxiliar,
                            au.nombre_auxiliar
                            from conta.tcuenta c
                            inner  join conta.tcuenta_auxiliar a on a.id_cuenta = c.id_cuenta
                            inner join conta.tauxiliar au on au.id_auxiliar = a.id_auxiliar
                            where c.id_gestion = 15 )loop
            for  v_dato in ( select i.id_cuenta_dos
                                    from conta.tcuenta_ids i
                                    where i.id_cuenta_uno = v_record.id_cuenta)loop

                                    INSERT INTO
  conta.tcuenta_auxiliar
(
  id_usuario_reg,
  id_usuario_mod,
  fecha_reg,
  fecha_mod,
  estado_reg,
  id_usuario_ai,
  usuario_ai,
  id_auxiliar,
  id_cuenta
)
VALUES (
  1,
  null,
  now(),
  null,
  'activo',
  null,
  null,
  v_record.id_auxiliar,
  v_dato.id_cuenta_dos
);


            end loop;

  end loop;
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