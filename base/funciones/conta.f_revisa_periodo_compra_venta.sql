CREATE OR REPLACE FUNCTION conta.f_revisa_periodo_compra_venta (
  p_id_usuario integer,
  p_id_depto_conta integer,
  p_id_periodo integer
)
RETURNS boolean AS
$body$
/*
	Autor: Rensi Arteaga Copari (KPLIAN)
    Fecha: 25/08/2015
    Descripción: Revisa si el periodo del libro de compra ventas  se encuentra abierto y si el usuario tiene permido para
    insertar o modificar documentos para la fecha y departametno indicados
*/

DECLARE


v_parametros  		record;
v_registros 		record;
v_nombre_funcion   	text;
v_resp				varchar;
v_nivel				integer;
v_monto				numeric;
v_mayor				numeric;
v_id_gestion  		integer;
v_tmp_formula		varchar;
v_formula_evaluado	varchar;
v_columna			varchar;
v_columna_nueva     varchar[];
v_sw_busqueda		boolean;
v_i					integer;
v_k					integer;
va_variables		varchar[];
v_monto_haber		numeric;
v_monto_debe		numeric;
v_reg_pcv           record;

v_depto			   varchar;

BEGIN

   v_nombre_funcion = 'conta.f_revisa_periodo_compra_venta';


   select
    *
   into
    v_reg_pcv
   from conta.tperiodo_compra_venta pcv
   inner join param.tperiodo per on per.id_periodo=pcv.id_periodo
   where pcv.id_periodo = p_id_periodo
   and pcv.id_depto = p_id_depto_conta
   and pcv.estado_reg = 'activo';

--aumento de consulta para optener el nombre del departamento y agregarlo al raise
   select dep.nombre into v_depto
  from conta.tperiodo_compra_venta codep
  inner join param.tdepto dep on dep.id_depto = codep.id_depto
  where codep.id_depto = p_id_depto_conta
  and codep.id_periodo = p_id_periodo;

   IF  v_reg_pcv is null THEN
     raise exception 'No se encontró un periodo para el departamento contable y fecha determinados';
   END IF;

    IF v_reg_pcv.estado = 'abierto' THEN
       RETURN TRUE;
    ELSIF   v_reg_pcv.estado = 'cerrado' THEN
              raise exception 'El periodo % del libro de compras y ventas se encuentra Cerrado. para el departamento de %', pxp.f_obtener_literal_periodo(v_reg_pcv.periodo,0), v_depto ;
    ELSIF   v_reg_pcv.estado = 'cerrado_parcial' THEN

      --TODO verifica si el usuario tiene permisos,
        -- puede crearce una tabla espcifica para otorgar permisos a los usuario por depto de conta
        -- puede ser tambien la tabla de usuarios del depto
		--SE QUEMO AL DEPARTAMENTO 4 CENTRAL CON POR Q SOLO ESTOS USUARIOS TIENE PERMISO
        IF exists (select 1 from param.tdepto_usuario du
                  where du.id_depto = 4
                  and du.id_usuario = p_id_usuario
                  and du.estado_reg = 'activo')  THEN

            RETURN TRUE;

        ELSE
                      raise exception 'El periodo % se encuentra parcialmente cerrado, para el departamento de % y el usuario no es miembro del departametno de contabilidad.',pxp.f_obtener_literal_periodo(v_reg_pcv.periodo,0), v_depto;
        END IF;

    ELSE
     raise exception 'Estado no reconocido';
   END IF;





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
STABLE
CALLED ON NULL INPUT
SECURITY INVOKER
COST 100;
