CREATE OR REPLACE FUNCTION conta.f_replicar_partida_clase_gasto_por_gestion (
)
RETURNS void AS
$body$
DECLARE
    v_nombre_funcion   	text;
    v_resp    			varchar;
    v_mensaje 			varchar;
    v_record			record;
    v_id_partida		integer;

BEGIN

  v_nombre_funcion = 'conta.f_replicar_partida_clase_gasto_por_gestion';

  for v_record in (select 	cgp.id_partida,
                            cgp.id_clase_gasto
                            from pre.tclase_gasto_partida cgp
                            inner join pre.tpartida par on par.id_partida=cgp.id_partida
                            where par.id_gestion = 16 )loop

           --obtenemos el id_partida equivalente de la nueva gestion
           Select par.id_partida_dos
           into v_id_partida
           from pre.tpartida_ids par
           where par.id_partida_uno=v_record.id_partida;

			--insertamos el registo
            INSERT INTO pre.tclase_gasto_partida
            (
              id_usuario_reg,
              id_usuario_mod,
              fecha_reg,
              fecha_mod,
              estado_reg,
              id_usuario_ai,
              usuario_ai,
              id_clase_gasto,
              id_partida
            )
            VALUES (
              1,
              null,
              now(),
              null,
              'activo',
              null,
              null,
              v_record.id_clase_gasto,
              v_id_partida
            );

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
