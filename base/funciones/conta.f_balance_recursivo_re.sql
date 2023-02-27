--------------- SQL ---------------

CREATE OR REPLACE FUNCTION conta.f_balance_recursivo_re (
	p_desde date,
	p_hasta date,
	p_id_deptos character varying,
	p_nivel_ini integer,
	p_nivel_final integer,
	p_id_cuenta_padre integer,
	p_tipo_cuenta character varying,
	p_incluir_cierre character varying DEFAULT 'no'::character varying,
	p_tipo_balance character varying DEFAULT 'general'::character varying)
    RETURNS numeric
    LANGUAGE 'plpgsql'
    COST 100
    VOLATILE PARALLEL UNSAFE
AS $BODY$

DECLARE


v_parametros  		record;
v_registros 		record;
v_nombre_funcion   	text;
v_resp				varchar;
v_nivel				integer;
v_suma				numeric;
v_mayor				numeric;
va_mayor			numeric[];
v_id_gestion  		integer;
va_tipo_cuenta		varchar[];
v_gestion 			integer;
v_sw_force			boolean;


BEGIN

    v_nombre_funcion = 'conta.f_balance_recursivo_re';
    -- 0) inicia suma
    v_suma = 0;
    v_sw_force = FALSE;

    --arma array de tipos de cuenta
    va_tipo_cuenta = string_to_array(p_tipo_cuenta,',');

    v_gestion = (SELECT EXTRACT(YEAR FROM p_hasta))::integer;
select
    ges.id_gestion
into
    v_id_gestion
from param.tgestion ges
WHERE ges.gestion = v_gestion  and ges.estado_reg = 'activo';


-- incremetmaos el nivel
v_nivel = p_nivel_ini +1;

    -- obtiene la gestion de la fecha inicial

select
    ges.id_gestion
into
    v_id_gestion
from param.tgestion ges
WHERE ges.gestion = (SELECT EXTRACT(YEAR FROM p_hasta::Date))::integer  and ges.estado_reg = 'activo';

-- FOR listado de cuenta basicas de la gestion
FOR  v_registros in (
                              select c.id_cuenta,
                                 c.nro_cuenta,
                                 c.nombre_cuenta,
                                 c.nivel_cuenta,
                                 c.id_cuenta_padre,
                                 c.tipo_cuenta,
                                 c.sw_transaccional,
                                 cc.movimiento
                                from conta.tcuenta c
                                inner join conta.tconfig_tipo_cuenta cc on cc.tipo_cuenta = c.tipo_cuenta
                                where c.estado_reg = 'activo'
                                    and
                                      CASE
                                          WHEN p_id_cuenta_padre is null THEN
                                               c.id_cuenta_padre is NULL and c.id_gestion = v_id_gestion
                                          ELSE
                                               c.id_cuenta_padre = p_id_cuenta_padre
                                          END

                                   AND
                                   (  (p_tipo_balance = 'general'  and  'balance' = ANY(c.eeff))
                                         or
                                       (p_tipo_balance = 'resultado'  and  'resultado' = ANY(c.eeff))
                                          or
                                       (p_tipo_balance = 'todos' and c.tipo_cuenta = ANY(va_tipo_cuenta))
                                     )
                                   )   LOOP



                   IF v_registros.sw_transaccional = 'movimiento' THEN
                       va_mayor = conta.f_mayor_cuenta(v_registros.id_cuenta, p_desde, p_hasta, p_id_deptos, p_incluir_cierre);
                       v_mayor = va_mayor[1];
ELSE
                       -- llamada recursiva del balance general
                        v_mayor = conta.f_balance_recursivo_re(
                                               p_desde,
                                               p_hasta,
                                               p_id_deptos,
                                               v_nivel,
                                               p_nivel_final,
                                               v_registros.id_cuenta,
                                               p_tipo_cuenta,
                                               p_incluir_cierre,
                                               p_tipo_balance);
END IF;



                 -- insetamos en tabla temporal
insert  into temp_balancefre (
    id_cuenta,
    nro_cuenta,
    nombre_cuenta,
    id_cuenta_padre,
    monto,
    nivel,
    tipo_cuenta,
    movimiento)
VALUES(
          v_registros.id_cuenta,
          v_registros.nro_cuenta,
          v_registros.nombre_cuenta,
          v_registros.id_cuenta_padre,
          v_mayor,
          p_nivel_ini,
          v_registros.tipo_cuenta,
          v_registros.movimiento );


-- incrementamos suma
v_suma = v_suma + COALESCE(v_mayor,0);

END LOOP;


   --reronarmos la suma del balance ...

RETURN v_suma;


EXCEPTION

	WHEN OTHERS THEN
		v_resp='';
		v_resp = pxp.f_agrega_clave(v_resp,'mensaje',SQLERRM);
		v_resp = pxp.f_agrega_clave(v_resp,'codigo_error',SQLSTATE);
		v_resp = pxp.f_agrega_clave(v_resp,'procedimientos',v_nombre_funcion);
		raise exception '%',v_resp;

END;
$BODY$;