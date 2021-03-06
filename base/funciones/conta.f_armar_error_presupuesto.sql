CREATE OR REPLACE FUNCTION conta.f_armar_error_presupuesto (
  p_resp_ges numeric [],
  p_id_presupuesto integer,
  p_codigo_partida varchar,
  p_id_moneda integer,
  p_id_moneda_base integer,
  p_momento_presupeustario varchar,
  p_monto_cmp_mb numeric
)
RETURNS varchar AS
$body$
/*
	Autor: RAC (KPLIAN)
    Fecha: 06-04-2016
    Descripción: funcion que formatea el errores de la gestion de presupuesto en contabilidad
*/

DECLARE
  v_nombre_funcion varchar;
  v_resp varchar;
  v_codico_cc varchar;
  v_mensaje_error varchar;
  v_tmp varchar;

  --variables para mostrar el codigo de moneda
  v_codigo_base 	varchar;
  v_codigo_tri		varchar;

BEGIN
  v_nombre_funcion = 'conta.f_armar_error_presupuesto';

  --  recuperamos datos del presupuesto

             select
                pre.codigo_cc
             into
                v_codico_cc
             from pre.vpresupuesto_cc pre
             where pre.id_centro_costo = p_id_presupuesto;

             select tm.codigo
             into v_codigo_tri
             from param.tmoneda tm
             where tm.id_moneda = p_id_moneda;

             select tm.codigo
             into v_codigo_base
             from param.tmoneda tm
             where tm.id_moneda = p_id_moneda_base;

             IF p_resp_ges[4] is not null and  p_resp_ges[4] = 1  THEN
                  v_tmp = format('el presupuesto no alcanza por diferencia cambiaria, en moneda base tenemos:   %s y se requiere %s ', p_resp_ges[3]::varchar, p_monto_cmp_mb::varchar);
             ELSE



                  IF p_id_moneda_base = p_id_moneda THEN
                  	--IF p_resp_ges[3] < p_monto_cmp_mb THEN
                      --v_tmp = format('Solo se tiene disponible un monto en moneda base de:  %s y se requiere; %s', p_resp_ges[3]::varchar, p_monto_cmp_mb::varchar);
                      v_tmp = format('Solo se tiene saldo disponible a comprometer de:  %s %s. y se requiere; %s %s.', p_resp_ges[3]::varchar,v_codigo_base, p_monto_cmp_mb::varchar, v_codigo_tri);
                  	--END IF;
                  ELSE
                      --v_tmp =  format('Solo se tiene disponible un monto de:  %s y se requiere  %s', p_resp_ges[3]::varchar, p_monto_cmp_mb::varchar);
                      v_tmp =  format('Solo se tiene saldo disponible a comprometer de:  %s %s. y se requiere  %s %s.', p_resp_ges[3]::varchar, v_codigo_base, p_monto_cmp_mb::varchar, v_codigo_base);
                  END IF;

             END IF;

             v_mensaje_error =  format('<BR/> (%s) Pres: %s, partida %s  <BR/> --> %s', p_momento_presupeustario::varchar, v_codico_cc::varchar, p_codigo_partida::varchar, v_tmp::varchar);


 return v_mensaje_error;

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
