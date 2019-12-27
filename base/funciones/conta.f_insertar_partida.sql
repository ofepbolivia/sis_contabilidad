CREATE OR REPLACE FUNCTION conta.f_insertar_partida (
  p_id_usuario integer,
  p_id_plan_cuenta integer
)
RETURNS void AS
$body$
/**************************************************************************
 FUNCION: 		conta.f_insertar_auxiliar
 DESCRIPCION:   realiza la insercion de partidas a partir de la funcion conta.ft_plan_cuenta_det_ime
 AUTOR: 	    Alan Felipez
 FECHA:	        19/12/2019
 COMENTARIOS:
***************************************************************************
 HISTORIA DE MODIFICACIONES:

 DESCRIPCION:
 AUTOR:
 FECHA:
 ***************************************************************************/


DECLARE
  v_nombre_funcion   		text;
  v_resp					varchar;
  v_id_gestion				integer;
  v_partidas				varchar;
  v_registros_cuenta		record;
  v_partida_debe			varchar;
  v_reg_par_debe			record;
  v_reg_partida_debe		record;
  v_partida_haber			varchar;
  v_reg_par_haber			record;
v_reg_partida_haber			record;
BEGIN

  v_nombre_funcion:='conta.f_insertar_partida';
  --recuperamos el id de la gestion destino
  select pc.id_gestion
  into v_id_gestion
  from conta.tplan_cuenta pc
  where pc.id_plan_cuenta = p_id_plan_cuenta;



  for v_registros_cuenta in (SELECT cu.*
                            from	conta.tcuenta cu
                            where cu.sw_transaccional='movimiento' and cu.id_gestion=v_id_gestion)loop
  		--si existe la cuenta registrada en el plan detalle se procede a recuperar las partidas
        /*if exist (select 1
                  from conta.tplan_cuenta_det
                  where id_plan_cuenta=p_id_plan_cuenta and id_cuenta_destino=v_registros_cuenta.id_cuenta)then*/

                  select pcd.partida_sigep_debe, pcd.partida_sigep_haber
                  into v_partida_debe, v_partida_haber
                  from conta.tplan_cuenta_det pcd
                  where pcd.id_plan_cuenta=p_id_plan_cuenta and pcd.id_cuenta_asociada=v_registros_cuenta.id_cuenta;

                  /*select unnest(string_to_array(v_partida_debe::varchar,','))
                  into v_reg_par_debe;*/
                  --insertarmos las partidas debe correspondientes a su id_cuenta

                  for v_reg_par_debe in ( select TRIM(unnest(string_to_array(v_partida_debe::varchar,',')))  as partida)LOOP
                        for v_reg_partida_debe in (select par.id_partida, par.tipo
                                                    from pre.tpartida par
                                                    where par.codigo = v_reg_par_debe.partida and par.id_gestion=v_id_gestion)loop
                        	insert into conta.tcuenta_partida
                            (
                            id_usuario_reg,
                            fecha_reg,
                            estado_reg,
                            id_cuenta,
                            id_partida,
                            sw_deha,
                            se_rega
                            )values
                            (
                            p_id_usuario,
                            now(),
                            'activo',
                            v_registros_cuenta.id_cuenta,
                            v_reg_partida_debe.id_partida,
                            'debe',
                            v_reg_partida_debe.tipo
                            );
                        end loop;
                  end loop;

                  /*select unnest(string_to_array(v_partida_haber::varchar,','))
                  into v_reg_par_haber;*/

                   --insertarmos las partidas haber correspondientes a su id_cuenta

                  for v_reg_par_haber in ( select TRIM(unnest(string_to_array(v_partida_haber::varchar,',')))  as partida)LOOP
                        for v_reg_partida_haber in (select par.id_partida, par.tipo
                                                    from pre.tpartida par
                                                    where par.codigo = v_reg_par_haber.partida and par.id_gestion=v_id_gestion)loop
                        	insert into conta.tcuenta_partida
                            (
                            id_usuario_reg,
                            fecha_reg,
                            estado_reg,
                            id_cuenta,
                            id_partida,
                            sw_deha,
                            se_rega
                            )values
                            (
                            p_id_usuario,
                            now(),
                            'activo',
                            v_registros_cuenta.id_cuenta,
                            v_reg_partida_haber.id_partida,
                            'haber',
                            v_reg_partida_haber.tipo
                            );
                        end loop;
                  end loop;
        --end if;
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

ALTER FUNCTION conta.f_insertar_partida (p_id_usuario integer, p_id_plan_cuenta integer)
  OWNER TO postgres;