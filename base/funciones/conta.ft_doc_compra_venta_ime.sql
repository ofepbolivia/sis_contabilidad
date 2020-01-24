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
  v_sw_nit					varchar;
    v_plantilla			varchar;

  v_dui_importe			numeric;
  v_id_planttilla integer;
  v_plantilla_des varchar;
  v_id_funcionario			INTEGER;

  v_tipo_obligacion			varchar;

  v_id_depto_conta			INTEGER;
  v_id_depto_destino		INTEGER;
  v_id_plan_pago			INTEGER;
  v_id_plan_pago_dcv		INTEGER;
  v_id_depto_contatipo		INTEGER;
  v_num_tramite				varchar;
  v_cuenta					varchar;
  v_fecha_venci				date;
  v_estacion				varchar;
  v_moneda					varchar;
  v_tipo_cambio				numeric;
  v_codigo_control			varchar;
  v_autorizacion			varchar;
  v_factura					record;
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

		if pxp.f_existe_parametro(p_tabla,'tipo_cambio') then
			v_tipo_cambio = v_parametros.tipo_cambio;
		else
	        v_tipo_cambio = null;
        end if;

    if (pxp.f_existe_parametro(p_tabla,'desc_clase_comprobante')) then
        if(v_parametros.desc_clase_comprobante = 'Comprobante de Pago Contable') then
			RAISE  EXCEPTION 'Solo puede registar factoras en Comprobante de Pago Presupuestario';
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

	  --para facturas del SP , tipo de obligaciones internacionales
     if (pxp.f_existe_parametro(p_tabla,'id_plan_pago')) then
        SELECT op.tipo_obligacion
        INTO v_tipo_obligacion
        FROM tes.tobligacion_pago op
        inner join tes.tplan_pago pp on pp.id_obligacion_pago = op.id_obligacion_pago
        WHERE pp.id_plan_pago = v_parametros.id_plan_pago ;

        SELECT dd.id_depto_destino
        INTO v_id_depto_destino
        FROM  param.tdepto_depto dd
        inner join tes.tobligacion_pago op on op.id_depto = dd.id_depto_origen
        inner join tes.tplan_pago pp on pp.id_obligacion_pago = op.id_obligacion_pago
        WHERE pp.id_plan_pago = v_parametros.id_plan_pago;


        IF v_tipo_informe = 'lcv' THEN
            IF (v_tipo_obligacion= 'sp' or v_tipo_obligacion= 'spd' or v_tipo_obligacion= 'spi' or v_tipo_obligacion= 'pago_especial_spi')THEN
              v_tmp_resp = conta.f_revisa_periodo_compra_venta(p_id_usuario, v_id_depto_destino, v_rec.po_id_periodo);
            ELSE IF (v_tipo_obligacion= 'sp'or v_tipo_obligacion= 'spd'  or v_tipo_obligacion= 'spi' or v_tipo_obligacion= 'pago_especial_spi')THEN
              -- valida que periodO de libro de compras y ventas este abierto
              v_tmp_resp = conta.f_revisa_periodo_compra_venta(p_id_usuario, v_parametros.id_depto_conta, v_rec.po_id_periodo);
                 END IF;
            END IF;
        END IF;

     ELSE
     	IF v_tipo_informe = 'lcv' THEN
               -- valida que periodO de libro de compras y ventas este abierto
               v_tmp_resp = conta.f_revisa_periodo_compra_venta(p_id_usuario, v_parametros.id_depto_conta, v_rec.po_id_periodo);
        END IF;
     END IF;

    IF (v_tipo_obligacion in ('sp','spd','spi','pago_especial_spi'))THEN
        v_id_depto_contatipo = v_id_depto_destino;
    ELSE
        v_id_depto_contatipo = v_parametros.id_depto_conta;
    END IF;
--raise exception 'llega  f %=%',v_parametros.id_depto_conta, v_id_depto_contatipo;
     --

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

        	select per.nombre_completo1
            into v_cuenta
            from conta.tdoc_compra_venta dcv
            inner join segu.tusuario usu1 on usu1.id_usuario = dcv.id_usuario_reg
            inner join segu.vpersona per on per.id_persona = usu1.id_persona
            where dcv.nro_documento = v_parametros.nro_documento
            limit 1;

      /*  IF EXISTS(select
                    1
                  from conta.tdoc_compra_venta dcv
                  inner join param.tplantilla pla on pla.id_plantilla=dcv.id_plantilla
                  where    dcv.estado_reg = 'activo' and  dcv.nit = v_parametros.nit
                           and dcv.nro_autorizacion = v_parametros.nro_autorizacion
                           and dcv.nro_documento = v_parametros.nro_documento
                           and dcv.nro_dui = v_parametros.nro_dui
                           and pla.tipo_informe='lcv') then

          raise exception 'Ya existe un documento registrado con el mismo Número de Nit % y Número de Autorización % , por el usuario %',v_parametros.nit,  v_parametros.nro_autorizacion, v_cuenta;

        END IF;*/

        --controles para que no se repita el documento
        		  --control numero de documento
             	  IF EXISTS(select 1
                            from conta.tdoc_compra_venta dcv
                            inner join param.tplantilla pla on pla.id_plantilla=dcv.id_plantilla
                            where    dcv.estado_reg = 'activo' and  dcv.nro_documento = v_parametros.nro_documento
                            and dcv.fecha =v_parametros.fecha
                            and dcv.razon_social =v_parametros.razon_social
                            and dcv.importe_doc = v_parametros.importe_doc)THEN

                       raise exception 'Ya existe un Documento/Factura registrado con el mismo Número: %,Fecha: %, Razón Social: % y Monto: %  por el usuario %.',v_parametros.nro_documento,v_parametros.fecha,v_parametros.razon_social,v_parametros.importe_doc, v_cuenta;

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

      select p.sw_nit, p.sw_autorizacion
      into
      v_sw_nit
      v_autorizacion
      from param.tplantilla p
      where p.id_plantilla = v_parametros.id_plantilla;

      IF v_autorizacion  ='si' and v_parametros.nro_autorizacion = '' THEN
      	raise exception 'Falta registrar el Número de Autorización';
      END IF;

      IF  v_sw_nit = 'si' and  v_parametros.nit = '' THEN
      	raise exception 'Falta registrar el Nit';
      END IF;

      IF v_parametros.razon_social is null or v_parametros.razon_social = '' THEN
      	raise exception 'Falta registrar el Razon Social';
      END IF;

      select plt.desc_plantilla
        into
        v_plantilla
        from param.tplantilla plt
        where plt.id_plantilla = v_parametros.id_plantilla;


      /*   if v_plantilla = 'Póliza de Importación - DUI'then

         v_dui_importe = v_parametros.importe_pago_liquido;
         else

         v_dui_importe = v_parametros.importe_doc;
        end if;*/
--raise exception 'verificando';

	--para actualizar el plan de pago
	  if (pxp.f_existe_parametro(p_tabla,'id_plan_pago')) then
          v_id_plan_pago = v_parametros.id_plan_pago;
          --#15,  se recupera el nro_tramite del comprobante si es que existe
          select
             c.nro_tramite
          into
             v_nro_tramite
          from conta.tint_comprobante c
          where c.id_int_comprobante = v_id_int_comprobante;

      end if;

            select pp.id_plan_pago
            into v_id_plan_pago_dcv
            from tes.tplan_pago pp
            inner join conta.tdoc_compra_venta dcv on dcv.id_plan_pago = pp.id_plan_pago
            where dcv.id_int_comprobante = v_id_int_comprobante;

		if pxp.f_existe_parametro(p_tabla,'fecha_vencimiento') then
			v_fecha_venci = v_parametros.fecha_vencimiento;
		else
	        v_fecha_venci = null;
        end if;

        --controles
        IF (v_parametros.importe_doc is NULL) THEN
        	RAISE EXCEPTION 'Falta completar el campo MONTO';
        END IF;

        IF (v_parametros.id_plantilla is NULL) THEN
        	RAISE EXCEPTION 'Falta completar el campo TIPO DE DOCUMENTO';
        END IF;

        IF (v_parametros.id_moneda = 2) THEN
          SELECT mo.codigo
          INTO v_moneda
          FROM param.tmoneda mo
          WHERE mo.id_moneda = v_parametros.id_moneda;

          IF (v_tipo_cambio is NULL or v_tipo_cambio < 1) THEN
              RAISE EXCEPTION 'Falta completar el campo TIPO DE CAMBIO para la Moneda %', v_moneda;
          END IF;
        END IF;

        -- para que el codigo_control no is null
        if (v_parametros.codigo_control is NUll or v_parametros.codigo_control = '' or v_parametros.codigo_control = ' ') then
        	v_codigo_control =  '0' ;
        else
        	v_codigo_control = v_parametros.codigo_control;
        end if;

--IF (v_parametros.id_int_comprobante is Null) THEN
IF (v_id_int_comprobante is Null) THEN

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
        nro_tramite,
        id_plan_pago,
        fecha_vencimiento,
        tipo_cambio

      ) values(
        v_parametros.tipo,
        COALESCE(v_parametros.importe_excento,0),
        v_parametros.id_plantilla,
        v_parametros.fecha,
        v_parametros.nro_documento,
        v_parametros.nit,
        v_importe_ice,
        v_parametros.nro_autorizacion,
        COALESCE(v_parametros.importe_iva,0),
        COALESCE(v_parametros.importe_descuento,0),
        COALESCE(v_parametros.importe_descuento_ley,0),
        COALESCE(v_parametros.importe_pago_liquido,0),
      	COALESCE(v_parametros.importe_doc,0), --Dui
        'si', --sw_contabilizar,
        'registrado', --estado
        --v_parametros.id_depto_conta,
        v_id_depto_contatipo,
        v_parametros.obs,
        'activo',
        upper(v_codigo_control),
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
        v_nro_tramite,
        v_id_plan_pago,
        v_fecha_venci,
        COALESCE(v_tipo_cambio,1)

      )RETURNING id_doc_compra_venta into v_id_doc_compra_venta;


ELSE  --raise exception 'llega2 %',v_i;

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
        nro_tramite,
        id_plan_pago,
        fecha_vencimiento,
        tipo_cambio

      ) values(
        v_parametros.tipo,
        COALESCE(v_parametros.importe_excento,0),
        v_parametros.id_plantilla,
        v_parametros.fecha,
        v_parametros.nro_documento,
        v_parametros.nit,
        v_importe_ice,
        v_parametros.nro_autorizacion,
        COALESCE(v_parametros.importe_iva,0),
        COALESCE(v_parametros.importe_descuento,0),
        COALESCE(v_parametros.importe_descuento_ley,0),
        COALESCE(v_parametros.importe_pago_liquido,0),
      	COALESCE(v_parametros.importe_doc,0), --Dui
        'si', --sw_contabilizar,
        'registrado', --estado
        --v_parametros.id_depto_conta,
        v_id_depto_contatipo,
        v_parametros.obs,
        'activo',
        upper(v_codigo_control),
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
        v_nro_tramite,
        v_id_plan_pago_dcv,
        v_fecha_venci,
        COALESCE(v_tipo_cambio,1)
      )RETURNING id_doc_compra_venta into v_id_doc_compra_venta;
END IF;


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

      --21-01-2020 (may) modificacion para que el liquido pagable no se reistre como null ni 0
	  IF (v_parametros.importe_pago_liquido is null or v_parametros.importe_pago_liquido = 0) THEN
      	RAISE EXCEPTION 'Líquido Pagado debe ser mayor a 0';
      END IF;

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

        -- para que el codigo_control no is null
        if (v_parametros.codigo_control is NUll or v_parametros.codigo_control = '' or v_parametros.codigo_control = ' ') then
        	v_codigo_control =  '0' ;
        else
        	v_codigo_control = v_parametros.codigo_control;
        end if;

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
        COALESCE(v_parametros.importe_excento,0),
        v_parametros.id_plantilla,
        v_parametros.fecha,
        v_parametros.nro_documento,
        v_parametros.nit,
        v_importe_ice,
        v_parametros.nro_autorizacion,
        COALESCE(v_parametros.importe_iva,0),
        COALESCE(v_parametros.importe_descuento,0),
        COALESCE(v_parametros.importe_descuento_ley,0),
        COALESCE(v_parametros.importe_pago_liquido,0),
        v_parametros.importe_doc,
        'si', --sw_contabilizar,
        'registrado', --estado
        v_parametros.id_depto_conta,
        v_parametros.obs,
        'activo',
        upper(v_codigo_control),
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

       --21-01-2020 (may) modificacion para que el liquido pagable no se reistre como null ni 0
	  IF (v_parametros.importe_pago_liquido is null or v_parametros.importe_pago_liquido = 0) THEN
      	RAISE EXCEPTION 'Líquido Pagado debe ser mayor a 0';
      END IF;

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

        if pxp.f_existe_parametro(p_tabla,'tipo_cambio') then
          	v_tipo_cambio = v_parametros.tipo_cambio;
      	else
          	v_tipo_cambio = null;
      	end if;

      select tipo_informe into v_tipo_informe
      from param.tplantilla
      where id_plantilla = v_parametros.id_plantilla;

       v_rec = param.f_get_periodo_gestion(v_parametros.fecha);

       v_id_periodo = v_rec.po_id_periodo;

      -- 13/01/2017
      --TODO RAC, me parece buena idea  que al cerrar el periodo revise que no existan documentos pendientes  antes de cerrar
      -- valida que period de libro de compras y ventas este abierto para la nueva fecha

       /* --para facturas del SP

     SELECT op.tipo_obligacion
      INTO v_tipo_obligacion
      FROM tes.tobligacion_pago op
      inner join tes.tplan_pago pp on pp.id_obligacion_pago = op.id_obligacion_pago
      WHERE pp.id_plan_pago = v_parametros.id_plan_pago ;

      SELECT dd.id_depto_destino
      INTO v_id_depto_destino
      FROM  param.tdepto_depto dd
      inner join tes.tobligacion_pago op on op.id_depto = dd.id_depto_origen
      inner join tes.tplan_pago pp on pp.id_obligacion_pago = op.id_obligacion_pago
      left join conta.tdoc_compra_venta dc on dc.id_plan_pago = pp.id_plan_pago
      WHERE dc.id_doc_compra_venta = v_parametros.id_doc_compra_venta;
      --pp.id_plan_pago = v_parametros.id_plan_pago;

	IF v_tipo_informe = 'lcv' THEN
    	IF (v_tipo_obligacion= 'sp')THEN
          v_tmp_resp = conta.f_revisa_periodo_compra_venta(p_id_usuario, v_id_depto_destino, v_rec.po_id_periodo);
        ELSE IF (v_tipo_obligacion= 'sp')THEN
          -- valida que periodO de libro de compras y ventas este abierto
          v_tmp_resp = conta.f_revisa_periodo_compra_venta(p_id_usuario, v_parametros.id_depto_conta, v_rec.po_id_periodo);
        	 END IF;
        END IF;
	END IF;*/
     --
--raise exception 'llega %',v_tipo_informe;
  /*    IF v_tipo_informe = 'lcv' THEN
	      v_tmp_resp = conta.f_revisa_periodo_compra_venta(p_id_usuario, v_parametros.id_depto_conta, v_rec.po_id_periodo);
	  END IF;
      */
     --raise exception 'llega %',v_id_depto_destino;
      IF v_tipo_informe = 'lcv' THEN

          v_tmp_resp = conta.f_revisa_periodo_compra_venta(p_id_usuario, v_id_depto_destino, v_rec.po_id_periodo);
      ELSE
          -- valida que periodO de libro de compras y ventas este abierto
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

		if pxp.f_existe_parametro(p_tabla,'fecha_vencimiento') then
			v_fecha_venci = v_parametros.fecha_vencimiento;
		else
	        v_fecha_venci = null;
        end if;

        -- para que el codigo_control no is null
        if (v_parametros.codigo_control is NUll or v_parametros.codigo_control = '' or v_parametros.codigo_control = ' ') then
        	v_codigo_control =  '0' ;
        else
        	v_codigo_control = v_parametros.codigo_control;
        end if;

        --controles
        --raise exception 'llegaa %',v_parametros.id_plantilla;
          select p.sw_nit, p.sw_autorizacion
          into
          v_sw_nit
          v_autorizacion
          from param.tplantilla p
          where p.id_plantilla = v_parametros.id_plantilla;

          IF v_autorizacion  ='si' and v_parametros.nro_autorizacion = '' THEN
            raise exception 'Falta registrar el Número de Autorización';
          END IF;

          IF  v_sw_nit = 'si' and  v_parametros.nit = '' THEN
          	raise exception 'Falta registrar el Nit';
          END IF;

          IF v_parametros.razon_social is null or v_parametros.razon_social = '' THEN
          	raise exception 'Falta registrar el Razon Social';
          END IF;

      --Sentencia de la modificacion
      update conta.tdoc_compra_venta set
        tipo = v_parametros.tipo,
        importe_excento = COALESCE(v_parametros.importe_excento,0),
        id_plantilla = v_parametros.id_plantilla,
        fecha = v_parametros.fecha,
        nro_documento = v_parametros.nro_documento,
        nit = v_parametros.nit,
        importe_ice = v_importe_ice,
        nro_autorizacion =  upper(COALESCE(v_parametros.nro_autorizacion,'0')),
        importe_iva = COALESCE(v_parametros.importe_iva,0),
        importe_descuento = COALESCE(v_parametros.importe_descuento,0),
        importe_descuento_ley = COALESCE(v_parametros.importe_descuento_ley,0),
        importe_pago_liquido = COALESCE(v_parametros.importe_pago_liquido,0),
        importe_doc = COALESCE(v_parametros.importe_doc,0),
        id_depto_conta = v_parametros.id_depto_conta,
        obs = v_parametros.obs,
        codigo_control =  upper(v_codigo_control),
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
        fecha_vencimiento = v_fecha_venci,
        tipo_cambio = COALESCE(v_tipo_cambio,1)
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

       --21-01-2020 (may) modificacion para que el liquido pagable no se reistre como null ni 0
	  IF (v_parametros.importe_pago_liquido is null or v_parametros.importe_pago_liquido = 0) THEN
      	RAISE EXCEPTION 'Líquido Pagado debe ser mayor a 0';
      END IF;

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

        -- para que el codigo_control no is null
        if (v_parametros.codigo_control is NUll or v_parametros.codigo_control = '' or v_parametros.codigo_control = ' ') then
        	v_codigo_control =  '0' ;
        else
        	v_codigo_control = v_parametros.codigo_control;
        end if;


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
        importe_iva = COALESCE(v_parametros.importe_iva,0),
        importe_descuento = COALESCE(v_parametros.importe_descuento,0),
        importe_descuento_ley = COALESCE(v_parametros.importe_descuento_ley,0),
        importe_pago_liquido = COALESCE(v_parametros.importe_pago_liquido,0),
        importe_doc = v_parametros.importe_doc,
        id_depto_conta = v_parametros.id_depto_conta,
        obs = v_parametros.obs,
        codigo_control =  upper(v_codigo_control),
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

       --21-01-2020 (may) modificacion para que el liquido pagable no se reistre como null ni 0
	  IF (v_parametros.importe_pago_liquido is null or v_parametros.importe_pago_liquido = 0) THEN
      	RAISE EXCEPTION 'Líquido Pagado debe ser mayor a 0';
      END IF;

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

	  --Historial de validaciones de una factura
      SELECT tf.id_funcionario
      INTO v_id_funcionario
      FROM segu.tusuario tu
      INNER JOIN orga.tfuncionario tf on tf.id_persona = tu.id_persona
      WHERE tu.id_usuario = p_id_usuario ;

      insert into conta.thistorial_reg_compras(
      		id_doc_compra_venta,
			id_funcionario,
            fecha_cambio,
			estado_reg,
			id_usuario_ai,
			usuario_ai,
			fecha_reg,
			id_usuario_reg,
			fecha_mod,
			id_usuario_mod
          	) values(
            v_parametros.id_doc_compra_venta,
			v_id_funcionario,
            now(),
			'activo',
			v_parametros._id_usuario_ai,
			v_parametros._nombre_usuario_ai,
			now(),
			p_id_usuario,
			null,
			null
		);

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



      select dcv.importe_doc,
      		dcv.id_plantilla,
            dcv.importe_pago_liquido
      into
        v_registros
      from conta.tdoc_compra_venta dcv where dcv.id_doc_compra_venta =v_parametros.id_doc_compra_venta;


      select pl.desc_plantilla
      into v_plantilla_des
      from param.tplantilla pl
      where pl.id_plantilla = v_registros.id_plantilla;

      select
        sum (dc.precio_total)
      into
        v_sum_total
      from conta.tdoc_concepto dc
      where dc.id_doc_compra_venta = v_parametros.id_doc_compra_venta;

	  IF v_plantilla_des <> 'Póliza de Importación - DUI' THEN
        IF COALESCE(v_sum_total,0) !=  COALESCE(v_registros.importe_doc,0)  THEN
        raise exception 'El total del documento no iguala con el total detallado de conceptos';
      END IF;
      ELSE
      IF COALESCE(v_sum_total,0) !=  COALESCE(v_registros.importe_pago_liquido,0)  THEN
        raise exception 'El total del documento liquido no iguala con el total detallado de conceptos';
      END IF;

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

      if pxp.f_get_variable_global('ESTACION_inicio') = 'BOL' then
			update conta.tdoc_compra_venta  set
        		id_int_comprobante = NULL,
        		id_plan_pago =NULL
      		where id_doc_compra_venta=v_parametros.id_doc_compra_venta;
    	else
			update conta.tdoc_compra_venta  set
        		id_int_comprobante = NULL,
        		id_plan_pago =NULL--15/11/2019 Alan
      		where id_doc_compra_venta=v_parametros.id_doc_compra_venta;
      	end if;



      --Definicion de la respuesta
      v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Se retiro el cbte del documento '||v_parametros.id_doc_compra_venta);
      v_resp = pxp.f_agrega_clave(v_resp,'id_doc_compra_venta',v_parametros.id_doc_compra_venta::varchar);

      --Devuelve la respuesta
      return v_resp;

    end;
   /*********************************
 #TRANSACCION:  'CONTA_ADDCBTE_IME'
 #DESCRIPCION:	adiciona un documento al comprobante
 #AUTOR:		RAC mod Alan 06/11/2019
 #FECHA:		25-09-2015 15:57:09
***********************************/

  elsif(p_transaccion='CONTA_ADDCBTE_IME')then

    begin

  	 v_estacion = pxp.f_get_variable_global('ESTACION_inicio');

        IF(v_estacion = 'BUE')THEN

        	IF v_parametros.id_int_comprobante is not null then
                    IF not EXISTS(select
                                1
                              from conta.tdoc_compra_venta dcv
                              where dcv.id_doc_compra_venta = v_parametros.id_doc_compra_venta and dcv.id_int_comprobante is null) THEN

                   		 raise exception 'El documento no existe o ya tiene un cbte relacionado';
                    END IF;

                    --#14, recupera nro de tramite del cbte
                    select cbte.nro_tramite
                    into v_nro_tramite
                    from conta.tint_comprobante cbte
                    where cbte.id_int_comprobante = v_parametros.id_int_comprobante;

                    update conta.tdoc_compra_venta d  set
                      id_int_comprobante =  v_parametros.id_int_comprobante,
                      nro_tramite =   v_nro_tramite
                    where id_doc_compra_venta = v_parametros.id_doc_compra_venta;

            ELSE

                  --para tipo de obligaciones internacionales sp, spd y spi, porque se añade desde un estado en borrador
                  SELECT op.tipo_obligacion
                  INTO v_tipo_obligacion
                  FROM tes.tobligacion_pago op
                  inner join tes.tplan_pago pp on pp.id_obligacion_pago = op.id_obligacion_pago
                  WHERE pp.id_plan_pago = v_parametros.id_plan_pago;


                IF(v_tipo_obligacion in ('sp','spd','pago_especial_spi', 'spi'))THEN

                   -- validamos que el documento no tenga otro comprobante

                    IF not EXISTS(select
                                    1
                                  from conta.tdoc_compra_venta dcv
                                  where dcv.id_doc_compra_venta = v_parametros.id_doc_compra_venta and dcv.id_plan_pago is null) THEN

                      raise exception 'El documento no existe o ya tiene un plan de pago relacionado';
                    END IF;

                    --recupera nro de tramite del cbte
                    select op.num_tramite
                    into v_num_tramite
                    from tes.tplan_pago pp
                    inner join tes.tobligacion_pago op on op.id_obligacion_pago = pp.id_obligacion_pago
                    where pp.id_plan_pago = v_parametros.id_plan_pago ;

                    update conta.tdoc_compra_venta d  set
                      id_plan_pago =  v_parametros.id_plan_pago,
                      nro_tramite =   v_nro_tramite
                    where id_doc_compra_venta = v_parametros.id_doc_compra_venta;

                  ELSE

                      if v_parametros.id_plan_pago is null then
                          v_parametros.id_plan_pago = 0;
                      end if;

                      update conta.tdoc_compra_venta d  set
                        id_plan_pago =  v_parametros.id_plan_pago
                      where id_doc_compra_venta = v_parametros.id_doc_compra_venta;
                  END IF;
            END IF;

      ELSE
          -- validamos que el documento no tenga otro comprobante

            IF not EXISTS(select
                            1
                          from conta.tdoc_compra_venta dcv
                          where dcv.id_doc_compra_venta = v_parametros.id_doc_compra_venta and dcv.id_int_comprobante is null) THEN

              raise exception 'El documento no existe o ya tiene un cbte relacionado';
            END IF;

            --#14, recupera nro de tramite del cbte
            select cbte.nro_tramite
            into v_nro_tramite
            from conta.tint_comprobante cbte
            where cbte.id_int_comprobante = v_parametros.id_int_comprobante;

            update conta.tdoc_compra_venta d  set
              id_int_comprobante =  v_parametros.id_int_comprobante,
              nro_tramite =   v_nro_tramite
            where id_doc_compra_venta = v_parametros.id_doc_compra_venta;
      END IF;
       --
      --Definicion de la respuesta
      --v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Se adiciono el cbte del documento '||v_parametros.id_doc_compra_venta ||' cbte '||v_parametros.id_int_comprobante);
      v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Se adiciono el cbte del documento '||v_parametros.id_doc_compra_venta );
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

 /*********************************
   #TRANSACCION:  'CONTA_ELIRAIRBP_ELI'
   #DESCRIPCION:	quita el comprobante del documento
   #AUTOR:		admin
   #MODIFICADO: breydi.vasquez
   #FECHA_MOD: 	21/11/2019
   #FECHA:		25-09-2015 15:57:09
  ***********************************/

  elsif(p_transaccion='CONTA_ELIRAIRBP_ELI')then

    begin

    	--modificado por motivo de archivos Airbp
		--verifica que el periodo este abierto caso contrario no permite la eliminacion.
	  if(conta.f_revisa_periodo_compra_venta(p_id_usuario, v_parametros.id_depto_conta, v_parametros.id_periodo))then

	      --verifica inicialmente si la factura se encuentra revisado. si esta revisado no se elimina;
          for v_factura in (select id_doc_compra_venta, revisado
                            from conta.tdoc_compra_venta
                            where id_int_comprobante = v_parametros.id_int_comprobante)
          				 loop
          	if v_factura.revisado is null or v_factura.revisado = 'no' then
                delete from conta.tdoc_compra_venta
                where id_int_comprobante = v_parametros.id_int_comprobante
                and id_doc_compra_venta = v_factura.id_doc_compra_venta;
            end if;
          end loop;

	  end if;

      --Definicion de la respuesta
      v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Se retiro el cbte del documento '||v_parametros.id_int_comprobante);
      v_resp = pxp.f_agrega_clave(v_resp,'id_doc_compra_venta',v_parametros.id_int_comprobante::varchar);

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
