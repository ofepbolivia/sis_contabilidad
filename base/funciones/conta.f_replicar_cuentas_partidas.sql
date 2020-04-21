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
 v_id_cuneta_new 				integer;

  --27/12/2019
 v_registros_aux			record;
 v_partida_dos				integer;
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
    v_partida_dos
    from pre.tpartida_ids i
    where i.id_partida_uno = v_registros.id_partida;

    select d.id_cuenta_dos
    into
    v_id_cuneta_new
  	from conta.tcuenta_ids d
  	where d.id_cuenta_uno = v_registros.id_cuenta;

    IF EXISTS (	select 1
    			from conta.tcuenta_partida pa
                where  pa.id_cuenta = v_id_cuneta_new and pa.id_partida=v_partida_dos)THEN

    RAISE NOTICE 'Existe una partida asociada al cuenta';

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
                                        v_partida_dos,
                                        v_registros.sw_deha,
                                        v_registros.se_rega);

    END IF;

	END LOOP;

	--Alan 27/12/2019
 FOR v_registros_aux in (select ca.id_cuenta,
                               aux.id_auxiliar,
                               ca.nombre_cuenta
                                from conta.tcuenta ca
                                inner join conta.tcuenta_auxiliar aux on aux.id_cuenta = ca.id_cuenta
                                where ca.id_gestion = p_id_gestion)LOOP

    select d.id_cuenta_dos
    into
    v_id_cuneta_new
  	from conta.tcuenta_ids d
  	where d.id_cuenta_uno = v_registros_aux.id_cuenta;

    IF EXISTS (	select 1
    			from conta.tcuenta_auxiliar aux
                where  aux.id_cuenta = v_id_cuneta_new and aux.id_auxiliar=v_registros_aux.id_auxiliar)THEN

    --RAISE NOTICE 'el auxiliar % ya se encuentra asociada a la cuenta %',v_id_cuneta_new,v_registros_aux.nombre_cuenta;

 	 ELSE

    				insert into conta.tcuenta_auxiliar(
                         							id_usuario_reg,
                                                    id_usuario_mod,
                                                    fecha_reg,
                                                    fecha_mod,
                                                    estado_reg,
                                                    id_usuario_ai,
                                                    usuario_ai,
                                                    id_auxiliar,
                                                    id_cuenta
                                                   )VALUES(
                                                   p_id_usuario,
                                                    null,
                                                    now(),
                                                    null,
                                                    'activo',
                                                    null,
                                                    null,
                                                    v_registros_aux.id_auxiliar,
                                                    v_id_cuneta_new
                                                   );

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