CREATE OR REPLACE FUNCTION conta.f_replicar_cuentas_partidas (
  p_id_gestion integer,
  p_id_usuario integer
)
RETURNS void AS
$body$
DECLARE
 v_nombre_funcion   	 text;
 v_resp    				 varchar;
 v_mensaje 			 	 varchar;
 v_registros 			 record;
 v_id_mod_forma_pago 			integer;
 v_id_partidad_new				integer;
 v_id_cuneta_new 				integer;
BEGIN
 v_nombre_funcion = 'conta.f_replicar_cuentas_partidas';

 FOR v_registros in (select 	ca.id_cuenta,
                                pa.id_partida,
                                pa.sw_deha,
       	 						pa.se_rega
                                from conta.tcuenta ca
                                inner join conta.tcuenta_partida pa on pa.id_cuenta = ca.id_cuenta
                                where ca.id_gestion = p_id_gestion)LOOP

 	select i.id_partida_dos
    into
    v_id_partidad_new
    from pre.tpartida_ids i
    where i.id_partida_uno = v_registros.id_partida;

    select d.id_cuenta_dos
    into
    v_id_cuneta_new
  	from conta.tcuenta_ids d
  	where d.id_cuenta_uno = v_registros.id_cuenta;

    IF EXISTS (	select 1
    			from conta.tcuenta_partida pa
                where  pa.id_cuenta = v_id_cuneta_new and pa.id_partida=v_id_partidad_new)THEN

    RAISE NOTICE 'Existe una partidad asociada al cuenta';

 	 ELSE

    INSERT INTO conta.tcuenta_partida (	id_usuario_reg,
                                        id_usuario_mod,
                                        fecha_reg,
                                        fecha_mod,
                                        estado_reg,
                                        id_usuario_ai,
                                        usuario_ai,
                                        id_cuenta,
                                        id_partida,
                                        sw_deha,
                                        se_rega
                                       )VALUES (
                                        p_id_usuario,
                                       	null,
                                        now(),
                                        null,
                                        'activo',
                                        null,
                                        null,
                                        v_id_cuneta_new,
                                        v_id_partidad_new,
                                        v_registros.sw_deha,
                                        v_registros.se_rega);

    END IF;

	END LOOP;




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