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
    v_sincronizar		varchar;

    v_filtro     		varchar;
    v_filtro_fun		varchar;


BEGIN

	v_nombre_funcion = 'conta.ft_reporte_ext_sel';
    v_parametros = pxp.f_get_record(p_tabla);

	 /*********************************
        #TRANSACCION:  'CONTA_REPDETG_SEL'
        #DESCRIPCION:	Reporte de documentos compra y venta del ext
        #AUTOR:		Maylee Perez Pastor
        #FECHA:		28-02-2020
        ***********************************/

        if(p_transaccion='CONTA_REPDETG_SEL')then

             begin

                IF (v_parametros.id_proveedor is null) THEN
                v_filtro = ' ';
                ELSE
                 v_filtro = ' and dc.id_proveedor = '||v_parametros.id_proveedor||'  ';
                END IF;

                IF (v_parametros.id_funcionario is null) THEN
                v_filtro_fun = ' ';
                ELSE
                 v_filtro_fun = ' and op.id_funcionario = '||v_parametros.id_funcionario||'  ';
                END IF;

                v_consulta = ' select    cbte.nro_cbte::varchar,
                                         (to_char(cbte.fecha,''DD/MM/YYYY''))::varchar as fecha,
                                         cbte.tipo_cambio_2::numeric,
                                         prov.desc_proveedor::varchar,
                                         (COALESCE(plan.desc_plantilla,pp.forma_pago))::varchar as desc_plantilla,
                                         dc.nro_documento::varchar,
                                         ci.desc_ingas::varchar as concepto_gasto,
                                         od.descripcion::varchar as observaciones,
                                         cc.codigo_cc::varchar,
                                         (par.codigo||'' - ''|| par.nombre_partida)::varchar as partida,
                                         ot.desc_orden::varchar,
                                         (to_char(pp.fecha_costo_ini,''DD/MM/YYYY''))::varchar as fecha_costo_ini,
                                         (to_char(pp.fecha_costo_fin,''DD/MM/YYYY''))::varchar as fecha_costo_fin,
                                         (''Periodo: ''||pp.fecha_costo_ini||'' - ''||pp.fecha_costo_fin)::varchar as detalle_periodo,
                                         (ci.desc_ingas||'' - ''||pp.obs_monto_no_pagado ||'' - Periodo: ''||(to_char(pp.fecha_costo_ini,''DD/MM/YYYY''))||'' - ''||(to_char(pp.fecha_costo_fin,''DD/MM/YYYY'')))::varchar as detalle_gasto,
                                         cbte.nro_cheque::varchar,
                             			(CASE WHEN cbte.id_moneda = 2 THEN (SELECT sum(tra.importe_debe_mt) FROM conta.tint_transaccion tra WHERE tra.id_int_comprobante = cbte.id_int_comprobante) else 0 END)::numeric as monto_dolares,
                                        (CASE WHEN cbte.id_moneda != 2 THEN (SELECT sum(tra.importe_debe_mb) FROM conta.tint_transaccion tra WHERE tra.id_int_comprobante = cbte.id_int_comprobante) else 0 END)::numeric as monto,
                                        (cb.nro_cuenta||'' - ''||cb.denominacion)::varchar as cuenta_bancaria,
                             			('' '')::varchar as glosa


                                  FROM conta.tint_comprobante cbte
                                join tes.tplan_pago pp on pp.id_int_comprobante = cbte.id_int_comprobante
                                join tes.tobligacion_pago op on op.id_obligacion_pago = pp.id_obligacion_pago
                                join tes.tobligacion_det od on od.id_obligacion_pago = pp.id_obligacion_pago

                                left join conta.tint_transaccion tra on tra.id_int_comprobante = cbte.id_int_comprobante

                                left join conta.tdoc_compra_venta dc on dc.id_int_comprobante = cbte.id_int_comprobante
                                left join param.vproveedor prov on prov.id_proveedor = op.id_proveedor
                                left join param.tplantilla plan on plan.id_plantilla = dc.id_plantilla

                                 join param.tconcepto_ingas ci on ci.id_concepto_ingas = od.id_concepto_ingas
                                 join conta.torden_trabajo ot on ot.id_orden_trabajo = tra.id_orden_trabajo
                                 join pre.tpartida par on par.id_partida = tra.id_partida
                                 join param.vcentro_costo cc on cc.id_centro_costo = tra.id_centro_costo

                                 join tes.tcuenta_bancaria cb on cb.id_cuenta_bancaria = pp.id_cuenta_bancaria

                WHERE cbte.estado_reg = ''validado'' and cbte.fecha BETWEEN  '''||v_parametros.fecha_ini||''' and '''||v_parametros.fecha_fin ||'''
                  '||v_filtro||' '||v_filtro_fun||'

 				ORDER BY cbte.id_int_comprobante ASC

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