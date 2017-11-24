CREATE OR REPLACE FUNCTION conta.ft_doc_compra_venta_ime (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Sistema de Contabilidad
 FUNCION: 		conta.ft_doc_compra_venta_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'conta.tdoc_compra_venta'
 AUTOR: 		RAC KPLIAN
 FECHA:	        18-08-2015 15:57:09
 COMENTARIOS:
***************************************************************************
 HISTORIAL DE MODIFICACIONES:

 ISSUE            FECHA:		      AUTOR               DESCRIPCION
 #0				 18-08-2015        RAC KPLIAN 		Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'conta.tdoc_compra_venta'
 #14, BOA		 18/10/2017		   RAC KPLIAN		Al validar comprobantes vamos actualizar e nro de tramite en doc_compra_venta si estan relacionados en las trasacciones CONTA_DCV_INS y CONTA_ADDCBTE_IME

***************************************************************************/

DECLARE

  v_nro_requerimiento    	integer;
  v_parametros           	record;
  v_registros				record;
  v_id_requerimiento     	integer;
  v_resp		            varchar;
  v_nombre_funcion        text;
  v_mensaje_error         text;
  v_id_doc_compra_venta	integer;
  v_rec					record;
  v_tmp_resp				boolean;
  v_importe_ice			numeric;
  v_revisado				varchar;
  v_sum_total				numeric;
  v_id_proveedor			integer;
  v_id_cliente			integer;
  v_id_tipo_doc_compra_venta integer;
  v_codigo_estado			varchar;
  v_estado_rendicion		varchar;
  v_id_int_comprobante		integer;
  v_tipo_informe			varchar;
  v_razon_social			varchar;
  v_nit						integer;
  v_id_moneda				integer;
  v_nomeda					varchar;
  v_nro_tramite				varchar;
  v_id_periodo				integer;


BEGIN

  v_nombre_funcion = 'conta.ft_doc_compra_venta_ime';
  v_parametros = pxp.f_get_record(p_tabla);

  /*********************************
   #TRANSACCION:  'CONTA_DCV_INS'
   #DESCRIPCION:	Insercion de registros
   #AUTOR:		admin
   #FECHA:		18-08-2015 15:57:09
  ***********************************/

  if(p_transaccion='CONTA_DCV_INS')then

    begin



    if (pxp.f_existe_parametro(p_tabla,'desc_clase_comprobante')) then
        if(v_parametros.desc_clase_comprobante = 'Comprobante de Pago Presupuestario') then
			RAISE  EXCEPTION 'Solo puede registar factoras en Comprobante de Diario Contable';
        end if;
      end if;

      --  calcula valores pode defecto para el tipo de doc compra venta
		IF v_parametros.id_moneda is null THEN
          raise EXCEPTION 'Es necesario indicar la Moneda del documento, revise los datos.';
      END IF;

      IF v_parametros.tipo = 'compra' THEN
        -- paracompras por defecto es
        -- Compras para mercado interno con destino a actividades gravadas
        select
          td.id_tipo_doc_compra_venta
        into
          v_id_tipo_doc_compra_venta
        from conta.ttipo_doc_compra_venta td
        where td.codigo = '1';

      ELSE
        -- para ventas por defecto es
        -- facturas valida
        select
          td.id_tipo_doc_compra_venta
        into
          v_id_tipo_doc_compra_venta
        from conta.ttipo_doc_compra_venta td
        where td.codigo = 'V';

      END IF;

		IF v_parametros.id_moneda is null THEN
          raise EXCEPTION 'Es necesario indicar la Moneda del documento, revise los datos.';
      END IF;

      -- recuepra el periodo de la fecha ...
      --Obtiene el periodo a partir de la fecha
      v_rec = param.f_get_periodo_gestion(v_parametros.fecha);

	  select tipo_informe into v_tipo_informe
      from param.tplantilla
      where id_plantilla = v_parametros.id_plantilla;

      IF v_tipo_informe = 'lcv' THEN
      	  -- valida que periodO de libro de compras y ventas este abierto
      	  v_tmp_resp = conta.f_revisa_periodo_compra_venta(p_id_usuario, v_parametros.id_depto_conta, v_rec.po_id_periodo);
	  END IF;

      --TODO
      --validar que no exsita un documento con el mismo nro y misma razon social  ...?
      --validar que no exista un documento con el mismo nro_autorizacion, nro_factura , y nit y razon social


      IF v_parametros.importe_pendiente > 0 or v_parametros.importe_anticipo > 0 or v_parametros.importe_retgar > 0 THEN

        IF v_parametros.id_auxiliar is null THEN
          raise EXCEPTION 'Es necesario indicar una cuenta corriente, revise los datos.';
        END IF;

      END IF;


      if (pxp.f_existe_parametro(p_tabla,'id_int_comprobante')) then
          v_id_int_comprobante = v_parametros.id_int_comprobante;
          --#14,  se recupera el nro_tramite del comprobante si es que existe
          select
             c.nro_tramite
          into
             v_nro_tramite
          from conta.tint_comprobante c
          where c.id_int_comprobante = v_id_int_comprobante;

      end if;


      --recupera parametrizacion de la plantilla
      select
        *
      into
        v_registros
      from param.tplantilla pla
      where pla.id_plantilla = v_parametros.id_plantilla;

      --PARA COMPRAS
      IF v_parametros.tipo = 'compra' THEN

        IF EXISTS(select
                    1
                  from conta.tdoc_compra_venta dcv
                  inner join param.tplantilla pla on pla.id_plantilla=dcv.id_plantilla
                  where    dcv.estado_reg = 'activo' and  dcv.nit = v_parametros.nit
                           and dcv.nro_autorizacion = v_parametros.nro_autorizacion
                           and dcv.nro_documento = v_parametros.nro_documento
                           and dcv.nro_dui = v_parametros.nro_dui
                           and pla.tipo_informe='lcv') then

          raise exception 'Ya existe un documento registrado con el mismo nro,  nit y nro de autorizacion';

        END IF;

        -- chequear si el proveedor esta registrado
        v_id_proveedor = param.f_check_proveedor(p_id_usuario, v_parametros.nit, upper(trim(v_parametros.razon_social)));

      ELSE
        --TODO  chequear que la factura de venta no este duplicada

        -- chequear el el cliente esta registrado
        v_id_cliente = vef.f_check_cliente(p_id_usuario, v_parametros.nit, upper(trim(v_parametros.razon_social)));
      END IF;


      --si tiene habilitado el ic copiamos el monto excento
      -- OJO considerar que todos los calculos con el monto excento ya estaran
      -- considerando el ice, par ano hacer mayores cambios

      v_importe_ice = NULL;
      IF v_registros.sw_ic = 'si' then
        v_importe_ice = v_parametros.importe_excento;
      END IF;
      ----validacion exento mayot monto mmv
      IF v_parametros.importe_excento > v_parametros.importe_neto THEN
      raise exception 'El Importe Exento: %, no puede ser mayor al Monto Total: %. Revise los importes.',v_parametros.importe_excento,v_parametros.importe_neto;
	  END IF;

      --Sentencia de la insercion
      insert into conta.tdoc_compra_venta(
        tipo,
        importe_excento,
        id_plantilla,
        fecha,
        nro_documento,
        nit,
        importe_ice,
        nro_autorizacion,
        importe_iva,
        importe_descuento,
        importe_descuento_ley,
        importe_pago_liquido,
        importe_doc,
        sw_contabilizar,
        estado,
        id_depto_conta,
        obs,
        estado_reg,
        codigo_control,
        importe_it,
        razon_social,
        id_usuario_ai,
        id_usuario_reg,
        fecha_reg,
        usuario_ai,
        manual,
        id_periodo,
        nro_dui,
        id_moneda,
        importe_pendiente,
        importe_anticipo,
        importe_retgar,
        importe_neto,
        id_proveedor,
        id_cliente,
        id_auxiliar,
        id_tipo_doc_compra_venta,
        id_int_comprobante,
        nro_tramite

      ) values(
        v_parametros.tipo,
        v_parametros.importe_excento,
        v_parametros.id_plantilla,
        v_parametros.fecha,
        v_parametros.nro_documento,
        v_parametros.nit,
        v_importe_ice,
        v_parametros.nro_autorizacion,
        v_parametros.importe_iva,
        v_parametros.importe_descuento,
        v_parametros.importe_descuento_ley,
        v_parametros.importe_pago_liquido,
        v_parametros.importe_doc,
        'si', --sw_contabilizar,
        'registrado', --estado
        v_parametros.id_depto_conta,
        v_parametros.obs,
        'activo',
        upper(COALESCE(v_parametros.codigo_control,'0')),
        v_parametros.importe_it,
        upper(trim(v_parametros.razon_social)),
        v_parametros._id_usuario_ai,
        p_id_usuario,
        now(),
        v_parametros._nombre_usuario_ai,
        'si',
        v_rec.po_id_periodo,
        v_parametros.nro_dui,
        v_parametros.id_moneda,
        COALESCE(v_parametros.importe_pendiente,0),
        COALESCE(v_parametros.importe_anticipo,0),
        COALESCE(v_parametros.importe_retgar,0),
        v_parametros.importe_neto,
        v_id_proveedor,
        v_id_cliente,
        v_parametros.id_auxiliar,
        v_id_tipo_doc_compra_venta,
        v_id_int_comprobante,
        v_nro_tramite
      )RETURNING id_doc_compra_venta into v_id_doc_compra_venta;

      if (pxp.f_existe_parametro(p_tabla,'id_origen')) then
        update conta.tdoc_compra_venta
        set id_origen = v_parametros.id_origen,
          tabla_origen = v_parametros.tabla_origen
        where id_doc_compra_venta = v_id_doc_compra_venta;
      end if;

      if (pxp.f_existe_parametro(p_tabla,'id_tipo_compra_venta')) then
        if(v_parametros.id_tipo_compra_venta is not null) then

          update conta.tdoc_compra_venta
          set id_tipo_doc_compra_venta = v_parametros.id_tipo_compra_venta
          where id_doc_compra_venta = v_id_doc_compra_venta;
        end if;
      end if;

	  if (pxp.f_existe_parametro(p_tabla,'estacion')) then
        if(v_parametros.estacion is not null) then

          update conta.tdoc_compra_venta
          set estacion = v_parametros.estacion
          where id_doc_compra_venta = v_id_doc_compra_venta;
        end if;
      end if;

      if (pxp.f_existe_parametro(p_tabla,'id_agencia')) then
        if(v_parametros.id_agencia is not null) then

          update conta.tdoc_compra_venta
          set id_agencia = v_parametros.id_agencia
          where id_doc_compra_venta = v_id_doc_compra_venta;
        end if;
      end if;

      --Definicion de la respuesta
      v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Documentos Compra/Venta almacenado(a) con exito (id_doc_compra_venta'||v_id_doc_compra_venta||')');
      v_resp = pxp.f_agrega_clave(v_resp,'id_doc_compra_venta',v_id_doc_compra_venta::varchar);

      --Devuelve la respuesta
      return v_resp;

    end;

  /*********************************
   #TRANSACCION:  'CONTA_DCVCAJ_INS'
   #DESCRIPCION:	Insercion de registros
   #AUTOR:		Gonzalo Sarmiento
   #FECHA:		09-02-2017
  ***********************************/

  elsif(p_transaccion='CONTA_DCVCAJ_INS')then

    begin

      --  calcula valores pode defecto para el tipo de doc compra venta

      IF v_parametros.tipo = 'compra' THEN
        -- paracompras por defecto es
        -- Compras para mercado interno con destino a actividades gravadas
        select
          td.id_tipo_doc_compra_venta
        into
          v_id_tipo_doc_compra_venta
        from conta.ttipo_doc_compra_venta td
        where td.codigo = '1';

      ELSE
        -- para ventas por defecto es
        -- facturas valida
        select
          td.id_tipo_doc_compra_venta
        into
          v_id_tipo_doc_compra_venta
        from conta.ttipo_doc_compra_venta td
        where td.codigo = 'V';

      END IF;

      IF v_parametros.id_moneda is null THEN
          raise EXCEPTION 'Es necesario indicar la Moneda del documento, revise los datos.';
      END IF;

      -- recuepra el periodo de la fecha ...
      --Obtiene el periodo a partir de la fecha
      v_rec = param.f_get_periodo_gestion(v_parametros.fecha);

	  select tipo_informe into v_tipo_informe
      from param.tplantilla
      where id_plantilla = v_parametros.id_plantilla;

      IF v_tipo_informe = 'lcv' THEN
      	  -- valida que period de libro de compras y ventas este abierto
      	  v_tmp_resp = conta.f_revisa_periodo_compra_venta(p_id_usuario, v_parametros.id_depto_conta, v_rec.po_id_periodo);
	  END IF;

      --TODO
      --validar que no exsita un documento con el mismo nro y misma razon social  ...?
      --validar que no exista un documento con el mismo nro_autorizacion, nro_factura , y nit y razon social



      IF v_parametros.importe_pendiente > 0 or v_parametros.importe_anticipo > 0 or v_parametros.importe_retgar > 0 THEN

        IF v_parametros.id_auxiliar is null THEN
          raise EXCEPTION 'Es necesario indicar una cuenta corriente, revise los datos.';
        END IF;

      END IF;

      if (pxp.f_existe_parametro(p_tabla,'id_int_comprobante')) then
          v_id_int_comprobante = v_parametros.id_int_comprobante;
      end if;

      --recupera parametrizacion de la plantilla
      select
        *
      into
        v_registros
      from param.tplantilla pla
      where pla.id_plantilla = v_parametros.id_plantilla;

      --PARA COMPRAS
      IF v_parametros.tipo = 'compra' THEN

        IF EXISTS(select
                    1
                  from conta.tdoc_compra_venta dcv
                  inner join param.tplantilla pla on pla.id_plantilla=dcv.id_plantilla
                  where    dcv.estado_reg = 'activo' and  dcv.nit = v_parametros.nit
                           and dcv.nro_autorizacion = v_parametros.nro_autorizacion
                           and dcv.nro_documento = v_parametros.nro_documento
                           and dcv.nro_dui = v_parametros.nro_dui
                           and pla.tipo_informe='lcv') then

          raise exception 'Ya existe un documento registrado con el mismo nro,  nit y nro autorizacion';

        END IF;

        -- chequear si el proveedor esta registrado
        v_id_proveedor = param.f_check_proveedor(p_id_usuario, v_parametros.nit, upper(trim(v_parametros.razon_social)));

      ELSE
        --TODO  chequear que la factura de venta no este duplicada

        -- chequear el el cliente esta registrado
        v_id_cliente = vef.f_check_cliente(p_id_usuario, v_parametros.nit, upper(trim(v_parametros.razon_social)));
      END IF;

      --si tiene habilitado el ic copiamos el monto excento
      -- OJO considerar que todos los calculos con el monto excento ya estaran
      -- considerando el ice, par ano hacer mayores cambios

      v_importe_ice = NULL;
      IF v_registros.sw_ic = 'si' then
        v_importe_ice = v_parametros.importe_excento;
      END IF;

      --Sentencia de la insercion
      insert into conta.tdoc_compra_venta(
        tipo,
        importe_excento,
        id_plantilla,
        fecha,
        nro_documento,
        nit,
        importe_ice,
        nro_autorizacion,
        importe_iva,
        importe_descuento,
        importe_descuento_ley,
        importe_pago_liquido,
        importe_doc,
        sw_contabilizar,
        estado,
        id_depto_conta,
        obs,
        estado_reg,
        codigo_control,
        importe_it,
        razon_social,
        id_usuario_ai,
        id_usuario_reg,
        fecha_reg,
        usuario_ai,
        manual,
        id_periodo,
        nro_dui,
        id_moneda,
        importe_pendiente,
        importe_anticipo,
        importe_retgar,
        importe_neto,
        id_proveedor,
        id_cliente,
        id_auxiliar,
        id_tipo_doc_compra_venta,
        id_int_comprobante,
        estacion
      ) values(
        v_parametros.tipo,
        v_parametros.importe_excento,
        v_parametros.id_plantilla,
        v_parametros.fecha,
        v_parametros.nro_documento,
        v_parametros.nit,
        v_importe_ice,
        v_parametros.nro_autorizacion,
        v_parametros.importe_iva,
        v_parametros.importe_descuento,
        v_parametros.importe_descuento_ley,
        v_parametros.importe_pago_liquido,
        v_parametros.importe_doc,
        'si', --sw_contabilizar,
        'registrado', --estado
        v_parametros.id_depto_conta,
        v_parametros.obs,
        'activo',
        upper(COALESCE(v_parametros.codigo_control,'0')),
        v_parametros.importe_it,
        upper(trim(v_parametros.razon_social)),
        v_parametros._id_usuario_ai,
        p_id_usuario,
        now(),
        v_parametros._nombre_usuario_ai,
        'si',
        v_rec.po_id_periodo,
        v_parametros.nro_dui,
        v_parametros.id_moneda,
        COALESCE(v_parametros.importe_pendiente,0),
        COALESCE(v_parametros.importe_anticipo,0),
        COALESCE(v_parametros.importe_retgar,0),
        v_parametros.importe_neto,
        v_id_proveedor,
        v_id_cliente,
        v_parametros.id_auxiliar,
        v_id_tipo_doc_compra_venta,
        v_id_int_comprobante,
        v_parametros.estacion
      )RETURNING id_doc_compra_venta into v_id_doc_compra_venta;

      if (pxp.f_existe_parametro(p_tabla,'id_origen')) then
        update conta.tdoc_compra_venta
        set id_origen = v_parametros.id_origen,
          tabla_origen = v_parametros.tabla_origen
        where id_doc_compra_venta = v_id_doc_compra_venta;
      end if;

      if (pxp.f_existe_parametro(p_tabla,'id_tipo_compra_venta')) then
        if(v_parametros.id_tipo_compra_venta is not null) then

          update conta.tdoc_compra_venta
          set id_tipo_doc_compra_venta = v_parametros.id_tipo_compra_venta
          where id_doc_compra_venta = v_id_doc_compra_venta;
        end if;
      end if;

      if (pxp.f_existe_parametro(p_tabla,'estacion')) then
        if(v_parametros.estacion is not null) then

          update conta.tdoc_compra_venta
          set estacion = v_parametros.estacion
          where id_doc_compra_venta = v_id_doc_compra_venta;
        end if;
      end if;

      if (pxp.f_existe_parametro(p_tabla,'id_agencia')) then
        if(v_parametros.id_agencia is not null) then

          update conta.tdoc_compra_venta
          set id_agencia = v_parametros.id_agencia
          where id_doc_compra_venta = v_id_doc_compra_venta;
        end if;
      end if;

      --Definicion de la respuesta
      v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Documentos Compra/Venta almacenado(a) con exito (id_doc_compra_venta'||v_id_doc_compra_venta||')');
      v_resp = pxp.f_agrega_clave(v_resp,'id_doc_compra_venta',v_id_doc_compra_venta::varchar);

      --Devuelve la respuesta
      return v_resp;

    end;

  /*********************************
   #TRANSACCION:  'CONTA_DCV_MOD'
   #DESCRIPCION:	Modificacion de registros
   #AUTOR:		admin
   #FECHA:		18-08-2015 15:57:09
  ***********************************/

  elsif(p_transaccion='CONTA_DCV_MOD')then

    begin

    /*  03/11/2016 se comenta ---TODO ojo pensar en alguna alternativa no intrusiva

      select COALESCE(cd.estado,efe.estado) into v_estado_rendicion
      from conta.tdoc_compra_venta d
        left join cd.trendicion_det ren on ren.id_doc_compra_venta = d.id_doc_compra_venta
        left join cd.tcuenta_doc cd on cd.id_cuenta_doc =  ren.id_cuenta_doc_rendicion
        left join tes.tsolicitud_rendicion_det det on det.id_documento_respaldo=d.id_doc_compra_venta
        left join tes.tsolicitud_efectivo efe on efe.id_solicitud_efectivo=det.id_solicitud_efectivo
      where d.id_doc_compra_venta =v_parametros.id_doc_compra_venta;

       -- recuepra el periodo de la fecha ...
      --Obtiene el periodo a partir de la fecha
      v_rec = param.f_get_periodo_gestion(v_parametros.fecha);

      IF v_estado_rendicion NOT IN ('vbrendicion', 'revision') or v_estado_rendicion IS NULL THEN
        -- valida que period de libro de compras y ventas este abierto
        v_tmp_resp = conta.f_revisa_periodo_compra_venta(p_id_usuario, v_parametros.id_depto_conta, v_rec.po_id_periodo);
      END IF;

      */

      select tipo_informe into v_tipo_informe
      from param.tplantilla
      where id_plantilla = v_parametros.id_plantilla;

       v_rec = param.f_get_periodo_gestion(v_parametros.fecha);

       v_id_periodo = v_rec.po_id_periodo;

      -- 13/01/2017
      --TODO RAC, me parece buena idea  que al cerrar el periodo revise que no existan documentos pendientes  antes de cerrar
      -- valida que period de libro de compras y ventas este abierto para la nueva fecha
      IF v_tipo_informe = 'lcv' THEN
	      v_tmp_resp = conta.f_revisa_periodo_compra_venta(p_id_usuario, v_parametros.id_depto_conta, v_rec.po_id_periodo);
	  END IF;

      -- recuepra el periodo de la fecha ...
      --Obtiene el periodo a partir de la fecha
      /*
      v_rec = param.f_get_periodo_gestion(v_parametros.fecha);

      v_tmp_resp = conta.f_revisa_periodo_compra_venta(p_id_usuario, v_parametros.id_depto_conta, v_rec.po_id_periodo);
	  */

      --revisa si el documento no esta marcado como revisado
      select
        dcv.revisado,
        dcv.id_int_comprobante,
        dcv.id_origen,
        dcv.tabla_origen,
        dcv.fecha
      into
        v_registros
      from conta.tdoc_compra_venta dcv where dcv.id_doc_compra_venta =v_parametros.id_doc_compra_venta;

	  v_rec = param.f_get_periodo_gestion(v_registros.fecha);
	  -- valida que period de libro de compras y ventas este abierto para la antigua fecha
      IF v_tipo_informe = 'lcv' THEN
	      v_tmp_resp = conta.f_revisa_periodo_compra_venta(p_id_usuario, v_parametros.id_depto_conta, v_rec.po_id_periodo);
	  END IF;

      IF  v_registros.revisado = 'si' THEN
        IF v_estado_rendicion NOT IN ('vbrendicion','revision') or v_estado_rendicion IS NULL THEN
          raise exception 'los documentos revisados no pueden modificarse';
        END IF;
      END IF;


      IF v_parametros.tipo = 'compra' THEN
        -- chequear si el proveedor esta registrado
        v_id_proveedor = param.f_check_proveedor(p_id_usuario, v_parametros.nit, upper(trim(v_parametros.razon_social)));

      ELSE
        --TODO  chequear que la factura de venta no este duplicada
        -- chequear el el cliente esta registrado
        v_id_cliente = vef.f_check_cliente(p_id_usuario, v_parametros.nit, upper(trim(v_parametros.razon_social)));
      END IF;



      -- validar que no tenga un comprobante asociado
      --RAC 13/01/2017 , se levanta esta restriccion por que es encesario
      -- por posibles errores al registrar
      /* IF  v_registros.id_int_comprobante is not NULL THEN
        raise exception 'No puede editar por que el documento esta acociado al cbte id(%), primero quite esta relacion', v_registros.id_int_comprobante;
      END IF;*/

      if (pxp.f_existe_parametro(p_tabla,'id_int_comprobante')) then
          v_id_int_comprobante = v_parametros.id_int_comprobante;
      end if;

      IF v_id_int_comprobante is null THEN
        v_id_int_comprobante = v_registros.id_int_comprobante;
      END IF;

      -- recupera parametrizacion de la plantilla
      select
        *
      into
        v_registros
      from param.tplantilla pla
      where pla.id_plantilla = v_parametros.id_plantilla;

      --si tiene habilitado el ic copiamos el monto excento
      v_importe_ice = NULL;
      IF v_registros.sw_ic = 'si' then
        v_importe_ice = v_parametros.importe_excento;
      END IF;

      IF v_parametros.importe_pendiente > 0 or v_parametros.importe_anticipo > 0 or v_parametros.importe_retgar > 0 THEN

        IF v_parametros.id_auxiliar is null THEN
          raise EXCEPTION 'es necesario indicar una cuenta corriente';
        END IF;

      END IF;


      --Sentencia de la modificacion
      update conta.tdoc_compra_venta set
        tipo = v_parametros.tipo,
        importe_excento = v_parametros.importe_excento,
        id_plantilla = v_parametros.id_plantilla,
        fecha = v_parametros.fecha,
        nro_documento = v_parametros.nro_documento,
        nit = v_parametros.nit,
        importe_ice = v_importe_ice,
        nro_autorizacion =  upper(COALESCE(v_parametros.nro_autorizacion,'0')),
        importe_iva = v_parametros.importe_iva,
        importe_descuento = v_parametros.importe_descuento,
        importe_descuento_ley = v_parametros.importe_descuento_ley,
        importe_pago_liquido = v_parametros.importe_pago_liquido,
        importe_doc = v_parametros.importe_doc,
        id_depto_conta = v_parametros.id_depto_conta,
        obs = v_parametros.obs,
        codigo_control =  upper(COALESCE(v_parametros.codigo_control,'0')),
        importe_it = v_parametros.importe_it,
        razon_social = upper(trim(v_parametros.razon_social)),
        id_periodo = v_id_periodo,
        nro_dui = v_parametros.nro_dui,
        id_moneda = v_parametros.id_moneda,
        importe_pendiente = COALESCE(v_parametros.importe_pendiente,0),
        importe_anticipo = COALESCE(v_parametros.importe_anticipo,0),
        importe_retgar = COALESCE(v_parametros.importe_retgar,0),
        importe_neto = v_parametros.importe_neto,
        id_proveedor = v_id_proveedor,
        id_cliente = v_id_cliente,
        id_auxiliar = v_parametros.id_auxiliar,
        id_int_comprobante = v_id_int_comprobante
      where id_doc_compra_venta=v_parametros.id_doc_compra_venta;

      if (pxp.f_existe_parametro(p_tabla,'id_tipo_compra_venta')) then
        if(v_parametros.id_tipo_compra_venta is not null) then

          update conta.tdoc_compra_venta
          set id_tipo_doc_compra_venta = v_parametros.id_tipo_compra_venta
          where id_doc_compra_venta = v_parametros.id_doc_compra_venta;
        end if;
      end if;

	  if (pxp.f_existe_parametro(p_tabla,'estacion')) then
        if(v_parametros.estacion is not null) then

          update conta.tdoc_compra_venta
          set estacion = v_parametros.estacion
          where id_doc_compra_venta = v_parametros.id_doc_compra_venta;
        end if;
      end if;

      if (pxp.f_existe_parametro(p_tabla,'id_agencia')) then
        if(v_parametros.id_agencia is not null) then

          update conta.tdoc_compra_venta
          set id_agencia = v_parametros.id_agencia
          where id_doc_compra_venta = v_parametros.id_doc_compra_venta;
        end if;
      end if;

      --Definicion de la respuesta
      v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Documentos Compra/Venta modificado(a)');
      v_resp = pxp.f_agrega_clave(v_resp,'id_doc_compra_venta',v_parametros.id_doc_compra_venta::varchar);

      --Devuelve la respuesta
      return v_resp;

    end;

  /*********************************
   #TRANSACCION:  'CONTA_DCVCAJ_MOD'
   #DESCRIPCION:	Modificacion de registros
   #AUTOR:		Gonzalo Sarmiento
   #FECHA:		09-02-2017
  ***********************************/

  elsif(p_transaccion='CONTA_DCVCAJ_MOD')then

    begin

       v_rec = param.f_get_periodo_gestion(v_parametros.fecha);

       v_id_periodo = v_rec.po_id_periodo;

      -- 13/01/2017
      --TODO RAC, me parece buena idea  que al cerrar el periodo revise que no existan documentos pendientes  antes de cerrar
      -- valida que period de libro de compras y ventas este abierto para la nueva fecha

      select tipo_informe into v_tipo_informe
      from param.tplantilla
      where id_plantilla = v_parametros.id_plantilla;

      IF v_tipo_informe = 'lcv' THEN
	      v_tmp_resp = conta.f_revisa_periodo_compra_venta(p_id_usuario, v_parametros.id_depto_conta, v_rec.po_id_periodo);
	  END IF;

      -- recuepra el periodo de la fecha ...
      --Obtiene el periodo a partir de la fecha
      /*
      v_rec = param.f_get_periodo_gestion(v_parametros.fecha);

      v_tmp_resp = conta.f_revisa_periodo_compra_venta(p_id_usuario, v_parametros.id_depto_conta, v_rec.po_id_periodo);
	  */

      --revisa si el documento no esta marcado como revisado
      select
        dcv.revisado,
        dcv.id_int_comprobante,
        dcv.id_origen,
        dcv.tabla_origen,
        dcv.fecha
      into
        v_registros
      from conta.tdoc_compra_venta dcv where dcv.id_doc_compra_venta =v_parametros.id_doc_compra_venta;

      IF  v_registros.revisado = 'si' THEN
        IF v_estado_rendicion NOT IN ('vbrendicion','revision') or v_estado_rendicion IS NULL THEN
          raise exception 'los documentos revisados no pueden modificarse';
        END IF;
      END IF;

      v_rec = param.f_get_periodo_gestion(v_registros.fecha);
	  -- valida que period de libro de compras y ventas este abierto para la antigua fecha
      IF v_tipo_informe = 'lcv' THEN
	      v_tmp_resp = conta.f_revisa_periodo_compra_venta(p_id_usuario, v_parametros.id_depto_conta, v_rec.po_id_periodo);
	  END IF;

      IF v_parametros.tipo = 'compra' THEN
        -- chequear si el proveedor esta registrado
        v_id_proveedor = param.f_check_proveedor(p_id_usuario, v_parametros.nit, upper(trim(v_parametros.razon_social)));

      ELSE
        --TODO  chequear que la factura de venta no este duplicada
        -- chequear el el cliente esta registrado
        v_id_cliente = vef.f_check_cliente(p_id_usuario, v_parametros.nit, upper(trim(v_parametros.razon_social)));
      END IF;

      if (pxp.f_existe_parametro(p_tabla,'id_int_comprobante')) then
          v_id_int_comprobante = v_parametros.id_int_comprobante;
      end if;

      IF v_id_int_comprobante is null THEN
        v_id_int_comprobante = v_registros.id_int_comprobante;
      END IF;

      -- recupera parametrizacion de la plantilla
      select
        *
      into
        v_registros
      from param.tplantilla pla
      where pla.id_plantilla = v_parametros.id_plantilla;

      --si tiene habilitado el ic copiamos el monto excento
      v_importe_ice = NULL;
      IF v_registros.sw_ic = 'si' then
        v_importe_ice = v_parametros.importe_excento;
      END IF;

      IF v_parametros.importe_pendiente > 0 or v_parametros.importe_anticipo > 0 or v_parametros.importe_retgar > 0 THEN

        IF v_parametros.id_auxiliar is null THEN
          raise EXCEPTION 'es necesario indicar una cuenta corriente';
        END IF;

      END IF;

      --Sentencia de la modificacion
      update conta.tdoc_compra_venta set
        tipo = v_parametros.tipo,
        importe_excento = v_parametros.importe_excento,
        id_plantilla = v_parametros.id_plantilla,
        fecha = v_parametros.fecha,
        nro_documento = v_parametros.nro_documento,
        nit = v_parametros.nit,
        importe_ice = v_importe_ice,
        nro_autorizacion =  upper(COALESCE(v_parametros.nro_autorizacion,'0')),
        importe_iva = v_parametros.importe_iva,
        importe_descuento = v_parametros.importe_descuento,
        importe_descuento_ley = v_parametros.importe_descuento_ley,
        importe_pago_liquido = v_parametros.importe_pago_liquido,
        importe_doc = v_parametros.importe_doc,
        id_depto_conta = v_parametros.id_depto_conta,
        obs = v_parametros.obs,
        codigo_control =  upper(COALESCE(v_parametros.codigo_control,'0')),
        importe_it = v_parametros.importe_it,
        razon_social = upper(trim(v_parametros.razon_social)),
        id_periodo = v_id_periodo,
        nro_dui = v_parametros.nro_dui,
        id_moneda = v_parametros.id_moneda,
        importe_pendiente = COALESCE(v_parametros.importe_pendiente,0),
        importe_anticipo = COALESCE(v_parametros.importe_anticipo,0),
        importe_retgar = COALESCE(v_parametros.importe_retgar,0),
        importe_neto = v_parametros.importe_neto,
        id_proveedor = v_id_proveedor,
        id_cliente = v_id_cliente,
        id_auxiliar = v_parametros.id_auxiliar,
        id_int_comprobante = v_id_int_comprobante,
        estacion = v_parametros.estacion
      where id_doc_compra_venta=v_parametros.id_doc_compra_venta;

      if (pxp.f_existe_parametro(p_tabla,'id_tipo_compra_venta')) then
        if(v_parametros.id_tipo_compra_venta is not null) then

          update conta.tdoc_compra_venta
          set id_tipo_doc_compra_venta = v_parametros.id_tipo_compra_venta
          where id_doc_compra_venta = v_parametros.id_doc_compra_venta;
        end if;
      end if;

      if (pxp.f_existe_parametro(p_tabla,'estacion')) then
        if(v_parametros.estacion is not null) then

          update conta.tdoc_compra_venta
          set estacion = v_parametros.estacion
          where id_doc_compra_venta = v_id_doc_compra_venta;
        end if;
      end if;

      if (pxp.f_existe_parametro(p_tabla,'id_agencia')) then
        if(v_parametros.id_agencia is not null) then

          update conta.tdoc_compra_venta
          set id_agencia = v_parametros.id_agencia
          where id_doc_compra_venta = v_id_doc_compra_venta;
        end if;
      end if;

      --Definicion de la respuesta
      v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Documentos Compra/Venta modificado(a)');
      v_resp = pxp.f_agrega_clave(v_resp,'id_doc_compra_venta',v_parametros.id_doc_compra_venta::varchar);

      --Devuelve la respuesta
      return v_resp;

    end;

  /*********************************
   #TRANSACCION:  'CONTA_DCVBASIC_MOD'
   #DESCRIPCION:	Modificacion basica de documento de compra venta
   #AUTOR:		admin
   #FECHA:		18-08-2015 15:57:09
  ***********************************/

  elsif(p_transaccion='CONTA_DCVBASIC_MOD')then

    begin

      select tcv.codigo into v_codigo_estado
      from conta.ttipo_doc_compra_venta tcv
      where tcv.id_tipo_doc_compra_venta = v_parametros.id_tipo_doc_compra_venta;

      /*Cambiar lso valores a 0 si es una anulacion*/

      if (v_codigo_estado = 'A') then
        update conta.tdoc_compra_venta set
          importe_iva = 0,
          importe_excento = 0,
          importe_descuento = 0,
          importe_descuento_ley = 0,
          importe_pago_liquido = 0,
          importe_doc = 0,
          importe_it = 0,
          importe_pendiente = 0,
          importe_anticipo = 0,
          importe_retgar = 0,
          importe_neto = 0
        where id_doc_compra_venta=v_parametros.id_doc_compra_venta;
      end if;

      --Sentencia de la modificacion
      update conta.tdoc_compra_venta set
        id_tipo_doc_compra_venta = v_parametros.id_tipo_doc_compra_venta
      where id_doc_compra_venta=v_parametros.id_doc_compra_venta;

      --Definicion de la respuesta
      v_resp = pxp.f_agrega_clave(v_resp,'mensaje','estado del documento modificado');
      v_resp = pxp.f_agrega_clave(v_resp,'id_doc_compra_venta',v_parametros.id_doc_compra_venta::varchar);

      --Devuelve la respuesta
      return v_resp;

    end;



  /*********************************
 #TRANSACCION:  'CONTA_DCV_ELI'
 #DESCRIPCION:	Eliminacion de registros
 #AUTOR:		admin
 #FECHA:		18-08-2015 15:57:09
***********************************/

  elsif(p_transaccion='CONTA_DCV_ELI')then

    begin

      --revisa si el documento no esta marcado como revisado
      select
        dcv.revisado,
        dcv.id_int_comprobante,
        dcv.tabla_origen,
        dcv.id_origen,
        dcv.id_depto_conta,
        dcv.fecha,
        dcv.id_plantilla
      into
        v_registros
      from conta.tdoc_compra_venta dcv where dcv.id_doc_compra_venta =v_parametros.id_doc_compra_venta;

      IF  v_registros.revisado = 'si' THEN
        raise exception 'los documentos revisados no pueden eliminarse';
      END IF;

      -- revisar si el archivo es manual o no

      IF v_registros.id_origen is not null THEN
        raise exception 'Solo puede eliminar los documentos insertados manualmente';
      END IF;



      --validar si el periodo de conta esta cerrado o abierto
      -- recuepra el periodo de la fecha ...
      --Obtiene el periodo a partir de la fecha
      v_rec = param.f_get_periodo_gestion(v_registros.fecha);

	  select tipo_informe into v_tipo_informe
      from param.tplantilla
      where id_plantilla = v_registros.id_plantilla;

      -- valida que period de libro de compras y ventas este abierto
      IF v_tipo_informe = 'lcv' THEN
      	 v_tmp_resp = conta.f_revisa_periodo_compra_venta(p_id_usuario, v_registros.id_depto_conta, v_rec.po_id_periodo);
	  END IF;


      --validar que no tenga un comprobante asociado

      IF  v_registros.id_int_comprobante is not NULL THEN
        raise exception 'No puede elimiar por que el documento esta acociado al cbte id(%), primero quite esta relacion', v_registros.id_int_comprobante;
      END IF;


      --Sentencia de la eliminacion
      delete from conta.tdoc_concepto
      where id_doc_compra_venta=v_parametros.id_doc_compra_venta;


      --Sentencia de la eliminacion
      delete from conta.tdoc_compra_venta
      where id_doc_compra_venta=v_parametros.id_doc_compra_venta;



      --Definicion de la respuesta
      v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Documentos Compra/Venta eliminado(a)');
      v_resp = pxp.f_agrega_clave(v_resp,'id_doc_compra_venta',v_parametros.id_doc_compra_venta::varchar);

      --Devuelve la respuesta
      return v_resp;

    end;
  /*********************************
  #TRANSACCION:  'CONTA_CAMREV_IME'
  #DESCRIPCION:	Cambia el estao de la revisón del documento de compra o venta
  #AUTOR:		admin
  #FECHA:		09-09-2015 15:57:09
 ***********************************/

  elsif(p_transaccion='CONTA_CAMREV_IME')then

    begin


      select
        dcv.revisado
      into
        v_registros
      from conta.tdoc_compra_venta dcv where dcv.id_doc_compra_venta =v_parametros.id_doc_compra_venta;


      IF  v_registros.revisado = 'si' THEN
        v_revisado = 'no';
      ELSE
        v_revisado = 'si';
      END IF;


      update conta.tdoc_compra_venta set
        revisado = v_revisado
      where id_doc_compra_venta=v_parametros.id_doc_compra_venta;



      --Definicion de la respuesta
      v_resp = pxp.f_agrega_clave(v_resp,'mensaje','cambio del documento a revisado '||v_revisado|| ' id: '||v_parametros.id_doc_compra_venta);
      v_resp = pxp.f_agrega_clave(v_resp,'id_doc_compra_venta',v_parametros.id_doc_compra_venta::varchar);

      --Devuelve la respuesta
      return v_resp;

    end;


  /*********************************
 #TRANSACCION:  'CONTA_CHKDOCSUM_IME'
 #DESCRIPCION:	verifica si el detalle del documento cuadra con el total
 #AUTOR:		admin
 #FECHA:		09-09-2015 15:57:09
***********************************/

  elsif(p_transaccion='CONTA_CHKDOCSUM_IME')then

    begin


      select
        dcv.importe_doc
      into
        v_registros
      from conta.tdoc_compra_venta dcv where dcv.id_doc_compra_venta =v_parametros.id_doc_compra_venta;


      select
        sum (dc.precio_total)
      into
        v_sum_total
      from conta.tdoc_concepto dc
      where dc.id_doc_compra_venta = v_parametros.id_doc_compra_venta;

      IF COALESCE(v_sum_total,0) !=  COALESCE(v_registros.importe_doc,0)  THEN
        raise exception 'el total del documento no iguala con el total detallado de conceptos';
      END IF;


      --Definicion de la respuesta
      v_resp = pxp.f_agrega_clave(v_resp,'mensaje','cuadra el documento insertado');
      v_resp = pxp.f_agrega_clave(v_resp,'sum_total',v_sum_total::varchar);

      --Devuelve la respuesta
      return v_resp;

    end;

  /*********************************
   #TRANSACCION:  'CONTA_QUITCBTE_ELI'
   #DESCRIPCION:	quita el comprobante del documento
   #AUTOR:		admin
   #FECHA:		25-09-2015 15:57:09
  ***********************************/

  elsif(p_transaccion='CONTA_QUITCBTE_ELI')then

    begin


      update conta.tdoc_compra_venta  set
        id_int_comprobante = NULL
      where id_doc_compra_venta=v_parametros.id_doc_compra_venta;



      --Definicion de la respuesta
      v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Se retiro el cbte del documento '||v_parametros.id_doc_compra_venta);
      v_resp = pxp.f_agrega_clave(v_resp,'id_doc_compra_venta',v_parametros.id_doc_compra_venta::varchar);

      --Devuelve la respuesta
      return v_resp;

    end;
  /*********************************
 #TRANSACCION:  'CONTA_ADDCBTE_IME'
 #DESCRIPCION:	adiciona un documento al comprobante
 #AUTOR:		RAC
 #FECHA:		25-09-2015 15:57:09
***********************************/

  elsif(p_transaccion='CONTA_ADDCBTE_IME')then

    begin

      -- validamos que el documento no tenga otro comprobante

      IF not EXISTS(select
                      1
                    from conta.tdoc_compra_venta dcv
                    where dcv.id_doc_compra_venta = v_parametros.id_doc_compra_venta and dcv.id_int_comprobante is null) THEN

        raise exception 'El documento no existe o ya tiene un cbte relacionado';
      END IF;

      --#14, recupera nro de tramite del cbte

      select
         cbte.nro_tramite
      into
        v_nro_tramite
      from conta.tint_comprobante cbte
      where cbte.id_int_comprobante = v_parametros.id_int_comprobante;

      update conta.tdoc_compra_venta d  set
        id_int_comprobante =  v_parametros.id_int_comprobante,
        nro_tramite =   v_nro_tramite
      where id_doc_compra_venta = v_parametros.id_doc_compra_venta;



      --Definicion de la respuesta
      v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Se adiciono el cbte del documento '||v_parametros.id_doc_compra_venta ||' cbte '||v_parametros.id_int_comprobante);
      v_resp = pxp.f_agrega_clave(v_resp,'id_doc_compra_venta',v_parametros.id_doc_compra_venta::varchar);

      --Devuelve la respuesta
      return v_resp;

    end;
/*********************************
 #TRANSACCION:  'CONTA_RAZONXNIT_GET'
 #DESCRIPCION:	recuperar razon social nit
 #AUTOR:		MMV
 #FECHA:		19-04-2017
***********************************/

  elsif(p_transaccion='CONTA_RAZONXNIT_GET')then

    begin
    --raise EXCEPTION 'esta llegando  %',v_parametros.nit;
    select
        DISTINCT(dcv.nit)::bigint,
        dcv.razon_social,
        m.id_moneda,
        m.moneda
        into
        v_nit,
        v_razon_social,
        v_id_moneda,
        v_nomeda
        from conta.tdoc_compra_venta dcv
        inner join param.tmoneda m on m.id_moneda = dcv.id_moneda
		where dcv.nit != '' and dcv.nit like ''||COALESCE(v_parametros.nit,'-')||'%';
      --Definicion de la respuesta
      v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Transaccion Exitosa');
      v_resp = pxp.f_agrega_clave(v_resp,'razon_social',v_razon_social::varchar);
      v_resp = pxp.f_agrega_clave(v_resp,'id_nomeda',v_id_moneda::varchar);
      v_resp = pxp.f_agrega_clave(v_resp,'moneda',v_nomeda::varchar);
      --Devuelve la respuesta
      return v_resp;

    end;




  else

    raise exception 'Transaccion inexistente: %',p_transaccion;

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