CREATE OR REPLACE FUNCTION conta.ft_banca_compra_venta_sel(p_administrador int4, p_id_usuario int4, p_tabla varchar, p_transaccion varchar)
  RETURNS varchar
AS
$BODY$
  /************************************************************************** 
  SISTEMA:        Sistema de Contabilidad
 FUNCION:         conta.ft_banca_compra_venta_sel
 DESCRIPCION:   Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'conta.tbanca_compra_venta'
 AUTOR:          (admin)
 FECHA:            11-09-2015 14:36:46
 COMENTARIOS:    
***************************************************************************
 HISTORIAL DE MODIFICACIONES:

 DESCRIPCION:    
 AUTOR:            
 FECHA:        
***************************************************************************/

DECLARE

  v_consulta varchar;
  v_parametros record;
  v_nombre_funcion text;
  v_resp varchar;

  v_record record;
  v_host varchar;
  
  v_id_banca_compra_venta_seleccionado integer;
  v_gestion INTEGER;

BEGIN

  v_nombre_funcion = 'conta.ft_banca_compra_venta_sel';
  v_parametros = pxp.f_get_record(p_tabla);

  v_host:='dbname=dbendesis host=192.168.100.30 user=ende_pxp password=ende_pxp'
    ;

  /*********************************    
     #TRANSACCION:  'CONTA_BANCA_SEL'
     #DESCRIPCION:    Consulta de datos
     #AUTOR:        admin    
     #FECHA:        11-09-2015 14:36:46
    ***********************************/

  if(p_transaccion='CONTA_BANCA_SEL')then

    begin

      
      --Sentencia de la consulta
      
      --raise exception '%',v_parametros.acumulado;
     
      v_id_banca_compra_venta_seleccionado = 0; 
      IF v_parametros.acumulado = 'si'
      then
      v_id_banca_compra_venta_seleccionado = v_parametros.id_banca_compra_venta;
      end if;             
                        
      
       if v_parametros.banca_documentos = 'endesis'
        then
        
        --creacion de tabla temporal del endesis 
          v_consulta:='WITH tabla_temporal_documentos AS (
              SELECT * FROM dblink('''||v_host||''',
          ''SELECT id_documento,razon_social FROM sci.tct_documento''
                   ) AS d (id_documento integer,razon_social varchar(255))
              )';
              
              v_consulta:=v_consulta||' select
						banca.id_banca_compra_venta,
						banca.num_cuenta_pago,
						banca.tipo_documento_pago,
						banca.num_documento,
						banca.monto_acumulado,
						banca.estado_reg,
						banca.nit_ci,
						banca.importe_documento,
						banca.fecha_documento,
						banca.modalidad_transaccion,
						banca.tipo_transaccion,
						banca.autorizacion,
						banca.monto_pagado,
						banca.fecha_de_pago,
						banca.razon,
						banca.tipo,
						banca.num_documento_pago,
						banca.num_contrato,
						banca.nit_entidad,
						banca.fecha_reg,
						banca.usuario_ai,
						banca.id_usuario_reg,
						banca.id_usuario_ai,
						banca.id_usuario_mod,
						banca.fecha_mod,
                        banca.id_periodo,
						usu1.cuenta as usr_reg,
						usu2.cuenta as usr_mod,
                        confmo.descripcion as desc_modalidad_transaccion,
                        conftt.descripcion as desc_tipo_transaccion,
                        conftd.descripcion as desc_tipo_documento_pago,
                        banca.revisado,
                        banca.id_contrato,
                        banca.id_proveedor,
                        provee.desc_proveedor as desc_proveedor2,
                        contra.objeto as desc_contrato,
                        banca.id_cuenta_bancaria,
                        cuenta.denominacion as desc_cuenta_bancaria,
                        banca.id_documento,
                        doc.razon_social as desc_documento,
                        param.f_literal_periodo(banca.id_periodo) as periodo,
                        banca.saldo,
                        contra.monto as monto_contrato,
                        ges.gestion,
                        '||v_id_banca_compra_venta_seleccionado||' as banca_seleccionada,
 						banca.numero_cuota,
            			banca.tramite_cuota,
                        banca.id_proceso_wf,
                        banca.resolucion,
                        contra.tipo_monto,
                        banca.retencion_cuota,
                        banca.multa_cuota,
                        provee.rotulo_comercial,
                        banca.estado_libro,
                        banca.periodo_servicio,
                        banca.lista_negra,
                        banca.tipo_bancarizacion
						from conta.tbanca_compra_venta banca
						inner join segu.tusuario usu1 on usu1.id_usuario = banca.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = banca.id_usuario_mod
                        left join conta.tconfig_banca confmo on confmo.digito = banca.modalidad_transaccion
                        left join conta.tconfig_banca conftt on conftt.digito = banca.tipo_transaccion
                        left join conta.tconfig_banca conftd on conftd.digito = banca.tipo_documento_pago
                        left join param.vproveedor provee on provee.id_proveedor = banca.id_proveedor
                        left join leg.tcontrato contra on contra.id_contrato = banca.id_contrato                        
                        left join tes.tcuenta_bancaria cuenta on cuenta.id_cuenta_bancaria = banca.id_cuenta_bancaria                        
                        inner join param.tperiodo per on per.id_periodo = banca.id_periodo
                        inner join param.tgestion ges on ges.id_gestion = per.id_gestion
                        left join tabla_temporal_documentos doc on doc.id_documento = banca.id_documento
                        where ';

                        
       
       elsif v_parametros.banca_documentos = 'pxp'
       then
       
       v_consulta:='select
						banca.id_banca_compra_venta,
						banca.num_cuenta_pago,
						banca.tipo_documento_pago,
						banca.num_documento,
						banca.monto_acumulado,
						banca.estado_reg,
						banca.nit_ci,
						banca.importe_documento,
						banca.fecha_documento,
						banca.modalidad_transaccion,
						banca.tipo_transaccion,
						banca.autorizacion,
						banca.monto_pagado,
						banca.fecha_de_pago,
						banca.razon,
						banca.tipo,
						banca.num_documento_pago,
						banca.num_contrato,
						banca.nit_entidad,
						banca.fecha_reg,
						banca.usuario_ai,
						banca.id_usuario_reg,
						banca.id_usuario_ai,
						banca.id_usuario_mod,
						banca.fecha_mod,
                        banca.id_periodo,
						usu1.cuenta as usr_reg,
						usu2.cuenta as usr_mod,
                        confmo.descripcion as desc_modalidad_transaccion,
                        conftt.descripcion as desc_tipo_transaccion,
                        conftd.descripcion as desc_tipo_documento_pago,
                        banca.revisado,
                        banca.id_contrato,
                        banca.id_proveedor,
                        provee.desc_proveedor as desc_proveedor2,
                        contra.objeto as desc_contrato,
                        banca.id_cuenta_bancaria,
                        cuenta.denominacion as desc_cuenta_bancaria,
                        banca.id_documento,
                        doc.razon_social::varchar as desc_documento,
                        param.f_literal_periodo(banca.id_periodo) as periodo,
                        banca.saldo,
                        contra.monto as monto_contrato,
                        ges.gestion,
                        '||v_id_banca_compra_venta_seleccionado||' as banca_seleccionada,
                        
                        banca.numero_cuota,
            			banca.tramite_cuota	,
                        banca.id_proceso_wf,
                        banca.resolucion,
                        contra.tipo_monto,
                        banca.retencion_cuota,
                        banca.multa_cuota,
                        provee.rotulo_comercial,
                        banca.estado_libro,
                        banca.periodo_servicio,
                        banca.lista_negra,
                        banca.tipo_bancarizacion
						from conta.tbanca_compra_venta banca
						inner join segu.tusuario usu1 on usu1.id_usuario = banca.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = banca.id_usuario_mod
                        left join conta.tconfig_banca confmo on confmo.digito = banca.modalidad_transaccion
                        left join conta.tconfig_banca conftt on conftt.digito = banca.tipo_transaccion
                        left join conta.tconfig_banca conftd on conftd.digito = banca.tipo_documento_pago
                        inner join param.vproveedor provee on provee.id_proveedor = banca.id_proveedor
                        left join leg.tcontrato contra on contra.id_contrato = banca.id_contrato                        
                        left join tes.tcuenta_bancaria cuenta on cuenta.id_cuenta_bancaria = banca.id_cuenta_bancaria                        
                        inner join param.tperiodo per on per.id_periodo = banca.id_periodo
                        inner join param.tgestion ges on ges.id_gestion = per.id_gestion
                        left join conta.tdoc_compra_venta doc on doc.id_doc_compra_venta = banca.id_documento
                        where ';
                        
       
        end if;
        
     
      --Definicion de la respuesta
      v_consulta:=v_consulta||v_parametros.filtro;
      
      
       
      v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' ||
        v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad ||
        ' offset ' || v_parametros.puntero;
        
       
      

      --Devuelve la respuesta
      return v_consulta;

    end;

    /*********************************    
     #TRANSACCION:  'CONTA_BANCA_CONT'
     #DESCRIPCION:    Conteo de registros
     #AUTOR:        admin    
     #FECHA:        11-09-2015 14:36:46
    ***********************************/

    elsif(p_transaccion='CONTA_BANCA_CONT')then

    begin
      --Sentencia de la consulta de conteo de registros
      v_consulta:='select count(id_banca_compra_venta)
					    from conta.tbanca_compra_venta banca
					    inner join segu.tusuario usu1 on usu1.id_usuario = banca.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = banca.id_usuario_mod
						                        inner join param.vproveedor provee on provee.id_proveedor = banca.id_proveedor

                        inner join conta.tconfig_banca confmo on confmo.digito = banca.modalidad_transaccion
                        inner join conta.tconfig_banca conftt on conftt.digito = banca.tipo_transaccion
                        inner join conta.tconfig_banca conftd on conftd.digito = banca.tipo_documento_pago

                        left join leg.tcontrato contra on contra.id_contrato = banca.id_contrato
                        left join tes.tcuenta_bancaria cuenta on cuenta.id_cuenta_bancaria = banca.id_cuenta_bancaria
                        inner join param.tperiodo per on per.id_periodo = banca.id_periodo
                        inner join param.tgestion ges on ges.id_gestion = per.id_gestion
					    where ';

      --Definicion de la respuesta            
      v_consulta:=v_consulta||v_parametros.filtro;

      --Devuelve la respuesta
      return v_consulta;

    end;

    /*********************************
     #TRANSACCION:  'CONTA_BANCA_POSIB'
     #DESCRIPCION:    datos posibles para bancarizar
     #AUTOR:        favio figueroa
     #FECHA:        06-11-2017 14:36:46
    ***********************************/

    elsif(p_transaccion='CONTA_BANCA_POSIB')then

    begin
      --Sentencia de la consulta de conteo de registros


      SELECT gestion into v_gestion FROM param.tgestion where id_gestion = v_parametros.id_gestion;
      v_host:='dbname=dbendesis host=192.168.100.30 user=ende_pxp password=ende_pxp';

      --creacion de tabla temporal del endesis
      v_consulta = conta.f_obtener_string_documento_bancarizacion(v_gestion::INTEGER);

      v_consulta:= v_consulta || 'select pg_pagado.id_plan_pago as id_plan_pago_pagado,
      pg_devengado.id_plan_pago id_plan_pago_devengado,
      libro.comprobante_sigma,
      libro.id_libro_bancos,
      libro.tipo,
      doc.id_documento::bigint as id_documento,
      doc.razon_social,
      doc.fecha_documento,
      doc.nro_documento::varchar,
       doc.nro_autorizacion,
      doc.importe_total,
      doc.nro_nit,
      plantilla.tipo_informe,
      plantilla.tipo_plantilla,
      pg_devengado.fecha_dev,
      pg_pagado.fecha_pag,
      pg_devengado.fecha_costo_ini,
      pg_devengado.fecha_costo_fin,
      libro.fecha as fecha_pago,
      cuenta.id_cuenta_bancaria,
      cuenta.denominacion,
      cuenta.nro_cuenta,
      provee.id_proveedor,
      contra.numero as numero_contrato,
      contra.id_contrato,
      contra.monto as monto_contrato,
      contra.bancarizacion,
      obliga.num_tramite,
      pg_devengado.nro_cuota,
       pg_pagado.forma_pago,
      sigma.comprobante_c31,
      sigma.fecha_entrega,
      pg_pagado.id_cuenta_bancaria as id_cuenta_bancaria_plan_pago,
      libro.nro_cheque,
      pg_pagado.id_proceso_wf,
      contra.resolucion_bancarizacion,
      pg_pagado.monto_retgar_mo,
      pg_pagado.liquido_pagable,
      pg_pagado.monto as monto_pago,
      pg_pagado.otros_descuentos,
      pg_pagado.descuento_inter_serv,
      libro.estado as estado_libro,
      libro.importe_cheque,
      doc.importe_debe::integer,
      doc.importe_gasto::integer,
      sigma.importe_recurso::integer,
      sigma.importe_haber::integer,
      contra.tipo_monto
      --libro_fk.importe_cheque as importe_cheque_fk
      --libro_fk.nro_cheque as nro_cheque_fk
from tes.tplan_pago pg_pagado
inner join tes.tplan_pago pg_devengado on pg_devengado.id_plan_pago = pg_pagado.id_plan_pago_fk
inner join param.tplantilla plantilla  on plantilla.id_plantilla = pg_devengado.id_plantilla

left join tabla_temporal_sigma sigma on sigma.id_int_comprobante = pg_pagado.id_int_comprobante
left join tes.tts_libro_bancos libro on libro.id_int_comprobante = pg_pagado.id_int_comprobante
--left join tes.tts_libro_bancos libro_fk on libro_fk.id_libro_bancos_fk = libro.id_libro_bancos


left join tes.tcuenta_bancaria cuenta on cuenta.id_cuenta_bancaria = pg_pagado.id_cuenta_bancaria

inner join tes.tobligacion_pago obliga on obliga.id_obligacion_pago = pg_pagado.id_obligacion_pago
left join leg.tcontrato contra on contra.id_contrato = obliga.id_contrato

inner join param.tproveedor provee on provee.id_proveedor = obliga.id_proveedor

inner join tabla_temporal_documentos doc on doc.id_int_comprobante = pg_devengado.id_int_comprobante


where pg_pagado.estado=''pagado'' and pg_devengado.estado = ''devengado''
and (libro.tipo=''cheque'' or  pg_pagado.forma_pago = ''transferencia'' or pg_pagado.forma_pago = ''cheque'')
and ( pg_pagado.forma_pago = ''transferencia'' or pg_pagado.forma_pago=''cheque'')
-- and plantilla.tipo_informe in (''lcv'',''retenciones'')

and (
        libro.estado in (''cobrado'',''entregado'',''anulado'',''borrador'',''depositado'')
        or libro.estado is null
        or (pg_pagado.forma_pago = ''transferencia'' and libro.estado in(''cobrado'',''entregado'',''anulado'',''borrador'') )
      )


and (
(doc.importe_total >= 50000)
 or (contra.bancarizacion = ''si'' and contra.tipo_monto=''cerrado'')
  or (contra.bancarizacion=''si'' and contra.tipo_monto=''abierto'' and doc.importe_total >= 50000)
  ) and  ';

      --Definicion de la respuesta
      v_consulta:=v_consulta||v_parametros.filtro;
      v_consulta = v_consulta || ' ORDER BY doc.fecha_documento,doc.nro_documento ,libro.estado asc ';

      --Devuelve la respuesta
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
$BODY$
LANGUAGE plpgsql VOLATILE;