CREATE OR REPLACE FUNCTION conta.f_insertar_auxiliar (
  p_id_usuario integer,
  p_id_plan_cuenta integer
)
RETURNS void AS
$body$
/**************************************************************************
 FUNCION: 		conta.f_insertar_auxiliar
 DESCRIPCION:   realiza la insercion de cuentas auxiliares a partir de la funcion conta.ft_plan_cuenta_det_ime
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
  v_nombre_funcion   	text;
  v_resp				varchar;
  v_id_auxiliar			integer;
  v_id_cuenta_auxiliar	integer;
  v_nombre_auxiliar		varchar;
  v_registros 			record;
  v_codigo_aux			BIGINT;
BEGIN

  v_nombre_funcion:='conta.f_insertar_auxiliar';

  select ges.gestion
  into v_codigo_aux
  from conta.tplan_cuenta pc
  inner join param.tgestion ges on ges.id_gestion = pc.id_gestion
  where pc.id_plan_cuenta=p_id_plan_cuenta;
  v_codigo_aux = v_codigo_aux*10000000;

  for v_registros in (select pcd.numero, pcd.nombre_cuenta, pcd.id_cuenta_asociada
                      from conta.tplan_cuenta_det pcd
                      where pcd.auxiliar !='' and pcd.id_plan_cuenta= p_id_plan_cuenta
                      order by pcd.numero)loop

              select substring((v_registros.nombre_cuenta)::varchar from 1 for 100)
              into v_nombre_auxiliar;
             v_codigo_aux=v_codigo_aux+10;

  			if exists (select 1
            			from conta.tauxiliar
                        where codigo_auxiliar=v_codigo_aux::varchar and estado_reg='activo')then
            	select aux.id_auxiliar
                into v_id_auxiliar
                from conta.tauxiliar aux
                where aux.codigo_auxiliar=v_codigo_aux::varchar and aux.estado_reg='activo';

            elsif exists (select 1
            			from conta.tauxiliar
                        where nombre_auxiliar=v_nombre_auxiliar::varchar and estado_reg='activo')then
            	select aux.id_auxiliar
                into v_id_auxiliar
                from conta.tauxiliar aux
                where aux.nombre_auxiliar=v_nombre_auxiliar::varchar and aux.estado_reg='activo';
            else
            	--insercion de la cuenta auxiliar
              insert into conta.tauxiliar
              (
              id_usuario_reg,
              fecha_reg,
              estado_reg,
              id_empresa,
              codigo_auxiliar,
              nombre_auxiliar
              )VALUES
              (
                 p_id_usuario,
                 now(),
                 'activo',
                 1,
                 v_codigo_aux::varchar,
                 v_nombre_auxiliar
              )returning id_auxiliar into v_id_auxiliar;
            end if;

              /*--insercion de la cuenta auxiliar
              insert into conta.tauxiliar
              (
              id_usuario_reg,
              fecha_reg,
              estado_reg,
              id_empresa,
              codigo_auxiliar,
              nombre_auxiliar
              )VALUES
              (
                 p_id_usuario,
                 now(),
                 'activo',
                 1,
                 v_codigo_aux::varchar,
                 v_nombre_auxiliar
              )returning id_auxiliar into v_id_auxiliar;
               */


              --insercion de la relacion con la cuenta
              insert into conta.tcuenta_auxiliar
              (
                 id_usuario_reg,
                fecha_reg,
                estado_reg,
                id_auxiliar,
                id_cuenta
              )values
              (
                 p_id_usuario,
                 now(),
                 'activo',
                v_id_auxiliar,
                v_registros.id_cuenta_asociada
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

ALTER FUNCTION conta.f_insertar_auxiliar (p_id_usuario integer, p_id_plan_cuenta integer)
  OWNER TO postgres;