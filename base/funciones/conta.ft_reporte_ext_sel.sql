CREATE OR REPLACE FUNCTION conta.ft_reporte_ext_sel (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Sistema de Contabilidad
 FUNCION: 		conta.ft_reporte_ext_sel
 DESCRIPCION:   Funcion que devuelve conjuntos de registros de reportes
 AUTOR: 		 maylee.perez
 FECHA:	        13-08-2020 15:57:09
 COMENTARIOS:
***************************************************************************
 HISTORIAL DE MODIFICACIONES:

 DESCRIPCION:
 AUTOR:
 FECHA:
***************************************************************************/

DECLARE

	v_consulta    		varchar;
	v_parametros  		record;
	v_nombre_funcion   	text;
	v_resp				varchar;

    v_registros 		record;

    v_tabla_origen    	varchar;
    v_filtro     		varchar;

    v_sincronizar		varchar;


BEGIN

	v_nombre_funcion = 'conta.ft_reporte_ext_sel';
    v_parametros = pxp.f_get_record(p_tabla);

	 /*********************************
        #TRANSACCION:  'CONTA_REPDETGASTO_SEL'
        #DESCRIPCION:	Reporte de documentos compra y venta del ext
        #AUTOR:		Maylee Perez Pastor
        #FECHA:		28-02-2020
        ***********************************/

        if(p_transaccion='CONTA_REPDETGASTO_SEL')then

             begin

                IF (v_parametros.id_proveedor is null) THEN
                v_filtro = ' ';
                ELSE
                 v_filtro = ' and dc.id_proveedor = '||v_parametros.id_proveedor||'  ';
                END IF;

                v_consulta = ' select    cbte.nro_cbte::varchar,
                                         ('''')::varchar as nro_registro,
                                         (cbte.nro_cbte||'' - '')::varchar as nro_op_registro,
                                         cbte.fecha::date,
                                         cbte.tipo_cambio_2::numeric,
                                         ('''')::varchar as vacio_factura,
                                         prov.desc_proveedor::varchar,
                                         plan.desc_plantilla::varchar,
                                         dc.nro_documento::varchar,
                                         ci.desc_ingas::varchar as concepto_gasto,
                                         od.descripcion::varchar as observaciones,
                                         ot.desc_orden::varchar,
                                         par.codigo::varchar as partida,
                                         pp.fecha_costo_ini::date,
                                         pp.fecha_costo_fin::date,
                                         (''Periodo: ''||pp.fecha_costo_ini||'' - ''||pp.fecha_costo_fin)::varchar as detalle_periodo,
                                         (ci.desc_ingas||'' - ''||pp.obs_monto_no_pagado ||'' - Periodo: ''||pp.fecha_costo_ini||'' - ''||pp.fecha_costo_fin)::varchar as detalle_gasto,
                                         cbte.nro_cheque::varchar,
                             			(CASE WHEN cbte.id_moneda = 2 THEN pp.liquido_pagable else 0 END)::numeric as monto_dolares,
                                        0::numeric as monto_bs,
                             			(CASE WHEN cbte.id_moneda = 5 THEN pp.liquido_pagable else 0 END)::numeric as monto_argentinos,
                                        cb.denominacion::varchar as cuenta_bancaria,
                             			('''')::varchar as glosa


                                FROM conta.tint_comprobante cbte
                              join tes.tplan_pago pp on pp.id_int_comprobante = cbte.id_int_comprobante
                              join tes.tobligacion_det od on od.id_obligacion_pago = pp.id_obligacion_pago
                              left join conta.tdoc_compra_venta dc on dc.id_int_comprobante = cbte.id_int_comprobante
                              left join param.vproveedor prov on prov.id_proveedor = dc.id_proveedor
                              left join param.tplantilla plan on plan.id_plantilla = dc.id_plantilla

                               join param.tconcepto_ingas ci on ci.id_concepto_ingas = od.id_concepto_ingas
                               join conta.torden_trabajo ot on ot.id_orden_trabajo = od.id_orden_trabajo
                               join pre.tpartida par on par.id_partida = od.id_partida

                               join tes.tcuenta_bancaria cb on cb.id_cuenta_bancaria = pp.id_cuenta_bancaria

                where cbte.estado_reg = ''validado'' and cbte.fecha BETWEEN  '''||v_parametros.fecha_ini||''' and '''||v_parametros.fecha_fin ||'''
                  '||v_filtro||'
                ';

                raise notice '%',v_consulta;
                return v_consulta;

            end;

    else

		raise exception 'Transaccion inexistente';

	end if;

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