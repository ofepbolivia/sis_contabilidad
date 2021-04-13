CREATE OR REPLACE FUNCTION conta.ft_doc_compra_venta_sel (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Sistema de Contabilidad
 FUNCION: 		conta.ft_doc_compra_venta_sel
 DESCRIPCION:   Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'conta.tdoc_compra_venta'
 AUTOR: 		 (admin)
 FECHA:	        18-08-2015 15:57:09
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
    v_id_entidad		integer;
    v_id_deptos			varchar;
    v_registros 		record;
    v_reg_entidad		record;
    v_tabla_origen    	varchar;
    v_filtro     		varchar;
    v_tipo   			varchar;
    v_sincronizar		varchar;
    v_gestion			integer;
    v_periodo			integer;

    --franklin.espinoza 10/01/2020 variable libro diario
    v_host 				varchar;
    v_puerto 			varchar;
    v_dbname 			varchar;
    p_user 				varchar;
    v_password 			varchar;
    v_cadena_factura	varchar;
    v_fecha_ini			date;
    v_fecha_fin			date;
	v_conexion 			varchar;

    v_id_periodo		integer;
    v_id_gestion		integer;
    --breydi.vasquez 09/02/2021 variable reporte Iata
    v_gestion_ini		integer;
    v_gestion_fin		integer;
    v_filtro_correccion	varchar;

    v_boletos_filtro    varchar = '';
BEGIN

	v_nombre_funcion = 'conta.ft_doc_compra_venta_sel';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'CONTA_DCV_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		admin
 	#FECHA:		18-08-2015 15:57:09
	***********************************/

	if(p_transaccion='CONTA_DCV_SEL')then

    	begin
    		--Sentencia de la consulta
			v_consulta:='select
                            dcv.id_doc_compra_venta,
                            dcv.revisado,
                            dcv.movil,
                            dcv.tipo,
                            COALESCE(dcv.importe_excento,0)::numeric as importe_excento,
                            dcv.id_plantilla,
                            dcv.fecha,
                            dcv.nro_documento,
                            dcv.nit,
                            COALESCE(dcv.importe_ice,0)::numeric as importe_ice,
                            dcv.nro_autorizacion,
                            COALESCE(dcv.importe_iva,0)::numeric as importe_iva,
                            COALESCE(dcv.importe_descuento,0)::numeric as importe_descuento,
                            COALESCE(dcv.importe_doc,0)::numeric as importe_doc,
                            dcv.sw_contabilizar,
                            COALESCE(dcv.tabla_origen,''ninguno'') as tabla_origen,
                            dcv.estado,
                            dcv.id_depto_conta,
                            dcv.id_origen,
                            dcv.obs,
                            dcv.estado_reg,
                            dcv.codigo_control,
                            COALESCE(dcv.importe_it,0)::numeric as importe_it,
                            dcv.razon_social,
                            dcv.id_usuario_ai,
                            dcv.id_usuario_reg,
                            dcv.fecha_reg,
                            dcv.usuario_ai,
                            dcv.id_usuario_mod,
                            dcv.fecha_mod,
                            usu1.cuenta as usr_reg,
                            usu2.cuenta as usr_mod,
                            dep.nombre as desc_depto,
                            pla.desc_plantilla,
                            COALESCE(dcv.importe_descuento_ley,0)::numeric as importe_descuento_ley,
                            COALESCE(dcv.importe_pago_liquido,0)::numeric as importe_pago_liquido,
                            dcv.nro_dui,
                            dcv.id_moneda,
                            mon.codigo as desc_moneda,
                            dcv.id_int_comprobante,
                            COALESCE(dcv.nro_tramite,''''),
                            COALESCE(ic.nro_cbte,dcv.id_int_comprobante::varchar)::varchar  as desc_comprobante,
                            COALESCE(dcv.importe_pendiente,0)::numeric as importe_pendiente,
                            COALESCE(dcv.importe_anticipo,0)::numeric as importe_anticipo,
                            COALESCE(dcv.importe_retgar,0)::numeric as importe_retgar,
                            COALESCE(dcv.importe_neto,0)::numeric as importe_neto,
                            aux.id_auxiliar,
                            aux.codigo_auxiliar,
                            aux.nombre_auxiliar,
                            dcv.id_tipo_doc_compra_venta,
                            (tdcv.codigo||'' - ''||tdcv.nombre)::Varchar as desc_tipo_doc_compra_venta,
                            (dcv.importe_doc -  COALESCE(dcv.importe_descuento,0) - COALESCE(dcv.importe_excento,0))     as importe_aux_neto,
                            dcv.id_plan_pago,
                            dcv.fecha_vencimiento,
                            dcv.tipo_cambio

						from conta.tdoc_compra_venta dcv
                          inner join segu.tusuario usu1 on usu1.id_usuario = dcv.id_usuario_reg
                          inner join param.tplantilla pla on pla.id_plantilla = dcv.id_plantilla
                          inner join param.tmoneda mon on mon.id_moneda = dcv.id_moneda
                          inner join conta.ttipo_doc_compra_venta tdcv on tdcv.id_tipo_doc_compra_venta = dcv.id_tipo_doc_compra_venta
                          left join conta.tauxiliar aux on aux.id_auxiliar = dcv.id_auxiliar
                          left join conta.tint_comprobante ic on ic.id_int_comprobante = dcv.id_int_comprobante
                          left join param.tdepto dep on dep.id_depto = dcv.id_depto_conta
                          left join segu.tusuario usu2 on usu2.id_usuario = dcv.id_usuario_mod
				        where ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
            v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;
			raise notice 'v_consulta: %', v_consulta;
			--Devuelve la respuesta
			return v_consulta;

		end;
     /*********************************
 	#TRANSACCION:  'CONTA_DCV_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		admin
 	#FECHA:		18-08-2015 15:57:09
	***********************************/

	elsif(p_transaccion='CONTA_DCV_CONT')then

		begin
			--Sentencia de la consulta de conteo de registros
			v_consulta:='select
                              count(dcv.id_doc_compra_venta),
                              COALESCE(sum(dcv.importe_ice),0)::numeric  as total_importe_ice,
                              COALESCE(sum(dcv.importe_excento),0)::numeric  as total_importe_excento,
                              COALESCE(sum(dcv.importe_it),0)::numeric  as total_importe_it,
                              COALESCE(sum(dcv.importe_iva),0)::numeric  as total_importe_iva,
                              COALESCE(sum(dcv.importe_descuento),0)::numeric  as total_importe_descuento,
                              COALESCE(sum(dcv.importe_doc),0)::numeric  as total_importe_doc,
                              COALESCE(sum(dcv.importe_retgar),0)::numeric  as total_importe_retgar,
                              COALESCE(sum(dcv.importe_anticipo),0)::numeric  as total_importe_anticipo,
                              COALESCE(sum(dcv.importe_pendiente),0)::numeric  as tota_importe_pendiente,
                              COALESCE(sum(dcv.importe_neto),0)::numeric  as total_importe_neto,
                              COALESCE(sum(dcv.importe_descuento_ley),0)::numeric  as total_importe_descuento_ley,
                              COALESCE(sum(dcv.importe_pago_liquido),0)::numeric  as total_importe_pago_liquido,
                              COALESCE(sum(dcv.importe_doc -  COALESCE(dcv.importe_descuento,0) - COALESCE(dcv.importe_excento,0)),0) as total_importe_aux_neto

					   from conta.tdoc_compra_venta dcv
                          inner join segu.tusuario usu1 on usu1.id_usuario = dcv.id_usuario_reg
                          inner join param.tplantilla pla on pla.id_plantilla = dcv.id_plantilla
                          inner join param.tmoneda mon on mon.id_moneda = dcv.id_moneda
                          inner join conta.ttipo_doc_compra_venta tdcv on tdcv.id_tipo_doc_compra_venta = dcv.id_tipo_doc_compra_venta
                          left join conta.tauxiliar aux on aux.id_auxiliar = dcv.id_auxiliar
                          left join conta.tint_comprobante ic on ic.id_int_comprobante = dcv.id_int_comprobante
                          left join param.tdepto dep on dep.id_depto = dcv.id_depto_conta
                          left join segu.tusuario usu2 on usu2.id_usuario = dcv.id_usuario_mod
				        where ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
            raise notice '%', v_consulta;
			--Devuelve la respuesta
			return v_consulta;

		end;


    /*********************************
 	#TRANSACCION:  'CONTA_DCVCAJ_SEL'
 	#DESCRIPCION:	Consulta de libro de compras que considera agencias , propio de BOA
 	#AUTOR:		Gonzalos, ...  Modificado Rensi
 	#FECHA:		26-05-2017 15:57:09
	***********************************/

	elsif(p_transaccion='CONTA_DCVCAJ_SEL')then

    	begin

        	--(may) 13-11-2019 modificacion para distinta vista y por usuario
            IF (v_parametros.nombreVista = 'DocCompraCajero') THEN

                IF p_administrador !=1 THEN
                   v_filtro = 'dcv.id_usuario_reg = '||p_id_usuario::varchar||' and pla.id_plantilla = 36   and ';
                 ELSE
                   v_filtro = 'pla.id_plantilla = 36 and ';
                END IF;

            ELSE
            	v_filtro = ' ';
            END IF;

    		--Sentencia de la consulta
			v_consulta:='select
                            dcv.id_doc_compra_venta,
                            dcv.revisado,
                            dcv.movil,
                            dcv.tipo,
                            COALESCE(dcv.importe_excento,0)::numeric as importe_excento,
                            dcv.id_plantilla,
                            dcv.fecha,
                            dcv.nro_documento,
                            dcv.nit,
                            COALESCE(dcv.importe_ice,0)::numeric as importe_ice,
                            dcv.nro_autorizacion,
                            COALESCE(dcv.importe_iva,0)::numeric as importe_iva,
                            COALESCE(dcv.importe_descuento,0)::numeric as importe_descuento,
                            COALESCE(dcv.importe_doc,0)::numeric as importe_doc,
                            dcv.sw_contabilizar,
                            COALESCE(dcv.tabla_origen,''ninguno'') as tabla_origen,
                            dcv.estado,
                            dcv.id_depto_conta,
                            dcv.id_origen,
                            dcv.obs,
                            dcv.estado_reg,
                            dcv.codigo_control,
                            COALESCE(dcv.importe_it,0)::numeric as importe_it,
                            dcv.razon_social,
                            dcv.id_usuario_ai,
                            dcv.id_usuario_reg,
                            dcv.fecha_reg,
                            dcv.usuario_ai,
                            dcv.id_usuario_mod,
                            dcv.fecha_mod,
                            usu1.cuenta as usr_reg,
                            usu2.cuenta as usr_mod,
                            dep.nombre as desc_depto,
                            pla.desc_plantilla,
                            COALESCE(dcv.importe_descuento_ley,0)::numeric as importe_descuento_ley,
                            COALESCE(dcv.importe_pago_liquido,0)::numeric as importe_pago_liquido,
                            dcv.nro_dui,
                            dcv.id_moneda,
                            mon.codigo as desc_moneda,
                            dcv.id_int_comprobante,
                            dcv.nro_tramite,
                            COALESCE(ic.nro_cbte,dcv.id_int_comprobante::varchar)::varchar  as desc_comprobante,
                            COALESCE(dcv.importe_pendiente,0)::numeric as importe_pendiente,
                            COALESCE(dcv.importe_anticipo,0)::numeric as importe_anticipo,
                            COALESCE(dcv.importe_retgar,0)::numeric as importe_retgar,
                            COALESCE(dcv.importe_neto,0)::numeric as importe_neto,
                            aux.id_auxiliar,
                            aux.codigo_auxiliar,
                            aux.nombre_auxiliar,
                            dcv.id_tipo_doc_compra_venta,
                            (tdcv.codigo||'' - ''||tdcv.nombre)::Varchar as desc_tipo_doc_compra_venta,
                            (dcv.importe_doc -  COALESCE(dcv.importe_descuento,0) - COALESCE(dcv.importe_excento,0))     as importe_aux_neto,
                            dcv.estacion,
                            dcv.id_punto_venta,
                            (ob.nombre ||'' - ''|| upper(ob.tipo_agencia))::Varchar as nombre,
                            dcv.id_agencia,
                            ob.codigo_noiata,
                            ob.codigo_int,
                            ic.c31,
                            dcv.fecha_vencimiento,
                            dcv.tipo_cambio

						from conta.tdoc_compra_venta dcv
                          inner join segu.tusuario usu1 on usu1.id_usuario = dcv.id_usuario_reg
                          inner join param.tplantilla pla on pla.id_plantilla = dcv.id_plantilla
                          inner join param.tmoneda mon on mon.id_moneda = dcv.id_moneda
                          inner join conta.ttipo_doc_compra_venta tdcv on tdcv.id_tipo_doc_compra_venta = dcv.id_tipo_doc_compra_venta
                          left join conta.tauxiliar aux on aux.id_auxiliar = dcv.id_auxiliar
                          left join conta.tint_comprobante ic on ic.id_int_comprobante = dcv.id_int_comprobante
                          left join obingresos.tagencia ob on ob.id_agencia = dcv.id_agencia
                          left join param.tdepto dep on dep.id_depto = dcv.id_depto_conta
                          left join segu.tusuario usu2 on usu2.id_usuario = dcv.id_usuario_mod

				        where '||v_filtro||' ';


			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

			--Devuelve la respuesta
			return v_consulta;

		end;

     /*********************************
 	#TRANSACCION:  'CONTA_DCVCAJ_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		admin
 	#FECHA:		18-08-2015 15:57:09
	***********************************/

	elsif(p_transaccion='CONTA_DCVCAJ_CONT')then

		begin

        	--(may) 13-11-2019 modificacion para distinta vista y por usuario
            IF (v_parametros.nombreVista = 'DocCompraCajero') THEN

                IF p_administrador !=1 THEN
                   v_filtro = 'dcv.id_usuario_reg = '||p_id_usuario::varchar||' and pla.id_plantilla = 36   and ';
                 ELSE
                   v_filtro = 'pla.id_plantilla = 36 and ';
                END IF;

            ELSE
            	v_filtro = ' ';
            END IF;

			--Sentencia de la consulta de conteo de registros
			v_consulta:='select
                              count(dcv.id_doc_compra_venta),
                              COALESCE(sum(dcv.importe_ice),0)::numeric  as total_importe_ice,
                              COALESCE(sum(dcv.importe_excento),0)::numeric  as total_importe_excento,
                              COALESCE(sum(dcv.importe_it),0)::numeric  as total_importe_it,
                              COALESCE(sum(dcv.importe_iva),0)::numeric  as total_importe_iva,
                              COALESCE(sum(dcv.importe_descuento),0)::numeric  as total_importe_descuento,
                              COALESCE(sum(dcv.importe_doc),0)::numeric  as total_importe_doc,
                              COALESCE(sum(dcv.importe_retgar),0)::numeric  as total_importe_retgar,
                              COALESCE(sum(dcv.importe_anticipo),0)::numeric  as total_importe_anticipo,
                              COALESCE(sum(dcv.importe_pendiente),0)::numeric  as tota_importe_pendiente,
                              COALESCE(sum(dcv.importe_neto),0)::numeric  as total_importe_neto,
                              COALESCE(sum(dcv.importe_descuento_ley),0)::numeric  as total_importe_descuento_ley,
                              COALESCE(sum(dcv.importe_pago_liquido),0)::numeric  as total_importe_pago_liquido,
                              COALESCE(sum(dcv.importe_doc -  COALESCE(dcv.importe_descuento,0) - COALESCE(dcv.importe_excento,0)),0) as total_importe_aux_neto

					  from conta.tdoc_compra_venta dcv
                          inner join segu.tusuario usu1 on usu1.id_usuario = dcv.id_usuario_reg
                          inner join param.tplantilla pla on pla.id_plantilla = dcv.id_plantilla
                          inner join param.tmoneda mon on mon.id_moneda = dcv.id_moneda
                          inner join conta.ttipo_doc_compra_venta tdcv on tdcv.id_tipo_doc_compra_venta = dcv.id_tipo_doc_compra_venta
                          left join conta.tauxiliar aux on aux.id_auxiliar = dcv.id_auxiliar
                          left join conta.tint_comprobante ic on ic.id_int_comprobante = dcv.id_int_comprobante
                          left join obingresos.tagencia ob on ob.id_agencia = dcv.id_agencia
                          left join param.tdepto dep on dep.id_depto = dcv.id_depto_conta
                          left join segu.tusuario usu2 on usu2.id_usuario = dcv.id_usuario_mod
				        where '||v_filtro||'  ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
            raise notice '%', v_consulta;
			--Devuelve la respuesta
			return v_consulta;

		end;

	/*********************************
 	#TRANSACCION:  'CONTA_DCVNA_SEL'
 	#DESCRIPCION:	colulta nit y razon social a parti del nro de autorizacion
 	#AUTOR:		Rensi Arteaga Copari
 	#FECHA:		18-08-2015 15:57:09
	***********************************/

	elsif(p_transaccion='CONTA_DCVNA_SEL')then

    	begin
    		--Sentencia de la consulta
			v_consulta:='select
                          DISTINCT(dcv.nro_autorizacion)::numeric,
                          dcv.nit,
                          dcv.razon_social
                          from conta.tdoc_compra_venta dcv
                        where  dcv.nro_autorizacion != '''' and dcv.nro_autorizacion like '''||COALESCE(v_parametros.nro_autorizacion,'-')||'%''';


            v_consulta:=v_consulta||'  limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;



			--Devuelve la respuesta
			return v_consulta;

		end;
    /*********************************
 	#TRANSACCION:  'CONTA_DCVNA_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		admin
 	#FECHA:		18-08-2015 15:57:09
	***********************************/

	elsif(p_transaccion='CONTA_DCVNA_CONT')then

		begin

            v_consulta:='select
                          count(DISTINCT(dcv.nro_autorizacion))
                        from conta.tdoc_compra_venta dcv
                        where dcv.nro_autorizacion != '''' and dcv.nro_autorizacion like '''||COALESCE(v_parametros.nro_autorizacion,'-')||'%'' ';


			--Devuelve la respuesta
			return v_consulta;

		end;

    /*********************************
 	#TRANSACCION:  'CONTA_DCVNIT_SEL'
 	#DESCRIPCION:	colulta  razon social a partir del nro de nit
 	#AUTOR:		Rensi Arteaga Copari
 	#FECHA:		18-08-2015 15:57:09
	***********************************/

	elsif(p_transaccion='CONTA_DCVNIT_SEL')then

    	begin
    		--Sentencia de la consulta
			v_consulta:='select
                           DISTINCT(dcv.nit)::bigint,
                           dcv.razon_social
                          from conta.tdoc_compra_venta dcv
                          inner join param.tplantilla pla on pla.id_plantilla = dcv.id_plantilla
                        where dcv.nit != '''' and pla.tipo_informe = ''lcv'' and dcv.nit like '''||COALESCE(v_parametros.nit,'-')||'%''';


            v_consulta:=v_consulta||'  limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;



			--Devuelve la respuesta
			return v_consulta;

		end;
    /*********************************
 	#TRANSACCION:  'CONTA_DCVNIT_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		admin
 	#FECHA:		18-08-2015 15:57:09
	***********************************/

	elsif(p_transaccion='CONTA_DCVNIT_CONT')then

		begin

            v_consulta:='select
                          count(DISTINCT(dcv.nit))
                        from conta.tdoc_compra_venta dcv
                        where dcv.nit != '''' and dcv.nit like '''||COALESCE(v_parametros.nit,'-')||'%'' ';


			--Devuelve la respuesta
			return v_consulta;

		end;
    /*********************************
 	#TRANSACCION:  'CONTA_REPLCV_SEL'
 	#DESCRIPCION:	listado para reporte de libro de compras y ventas
 	#AUTOR:		admin
 	#FECHA:		18-08-2015 15:57:09
	***********************************/

	ELSEIF(p_transaccion='CONTA_REPLCV_SEL')then

    	begin




            select
              d.id_entidad,
              d.id_subsistema
            into
              v_registros
            from param.tdepto  d
            where  d.id_depto = v_parametros.id_depto;


            IF v_registros.id_entidad is null THEN
              raise exception 'El departamento contable no tiene definido la entidad a la que pertenece';
            END IF;

            select
              pxp.list(d.id_depto::varchar)
            into
              v_id_deptos
            from param.tdepto d
            where d.id_entidad  = v_registros.id_entidad
                  and  d.id_subsistema = v_registros.id_subsistema ;



    		--Sentencia de la consulta
			v_consulta:='SELECT
                              id_doc_compra_venta,
                              tipo,
                              fecha,
                              nit,
                              razon_social,
                              COALESCE(nro_documento,''0'')::Varchar,
                              COALESCE(nro_dui,''0'')::Varchar,
                              nro_autorizacion,
                              importe_doc,
                              total_excento,
                              sujeto_cf,
                              importe_descuento,
                              subtotal,
                              credito_fiscal,
                              importe_iva,
                              codigo_control,
                              tipo_doc,
                              id_plantilla,
                              id_moneda,
                              codigo_moneda,
                              id_periodo,
                              id_gestion,
                              periodo,
                              gestion,
                              venta_gravada_cero,
                              subtotal_venta,
                              sujeto_df,
                              importe_ice,
                              importe_excento
                        FROM
                          conta.vlcv lcv
                        where      lcv.tipo = '''||v_parametros.tipo||'''
                               and lcv.id_periodo = '||v_parametros.id_periodo||'
                               and id_depto_conta in ( '||v_id_deptos||')
                        order by fecha, id_doc_compra_venta';

			raise notice '%', v_consulta;
			--Devuelve la respuesta
			return v_consulta;

		end;


    /*********************************
 	#TRANSACCION:  'CONTA_REPLCV_FRM'
 	#DESCRIPCION:	listado para reporte de libro de compras y ventas  desde formualrio, incialmente usar datos de endesis
 	#AUTOR:		admin
 	#FECHA:		18-08-2015 15:57:09
	***********************************/

	ELSEIF(p_transaccion='CONTA_REPLCV_FRM')then

    	begin

           v_sincronizar = pxp.f_get_variable_global('sincronizar');

           if v_parametros.filtro_sql = 'fechas' then
           	v_gestion = date_part('year',v_parametros.fecha_ini);
           else
           	SELECT gestion into v_gestion
           	FROM param.tgestion
           	WHERE id_gestion=v_parametros.id_gestion;
           end if;

           IF v_gestion < 2017  THEN
              v_tabla_origen = 'conta.tlcv_endesis';
           ELSE
              v_tabla_origen = 'conta.vlcv';
           END IF;

           IF v_parametros.filtro_sql = 'periodo'  THEN
               v_filtro =  ' (lcv.id_periodo = '||v_parametros.id_periodo||')  ';
           ELSE
               v_filtro =  ' (lcv.fecha::Date between '''||v_parametros.fecha_ini||'''::Date  and '''||v_parametros.fecha_fin||'''::date)  ';
           END IF;


          IF v_parametros.tipo_lcv = 'lcv_compras'  THEN
              v_tipo = 'compra';
          ELSE
              v_tipo = 'venta';
          END IF;

          --Sentencia de la consulta
		  v_consulta:='SELECT id_doc_compra_venta::BIGINT,
                               tipo::Varchar,
                               fecha::date,
                               nit::varchar,
                               razon_social::Varchar,
                               COALESCE(nro_documento::varchar, ''0'')::Varchar,
                               COALESCE(nro_dui::varchar, ''0'')::Varchar,
                               nro_autorizacion::Varchar,
                               importe_doc::numeric,
                               total_excento::numeric,
                               sujeto_cf::numeric,
                               importe_descuento::numeric,
                               subtotal::numeric,
                               credito_fiscal::numeric,
                               importe_iva::numeric,
                               codigo_control::varchar,
                               tipo_doc::varchar,
                               id_plantilla::integer,
                               id_moneda::integer,
                               codigo_moneda::Varchar,
                               id_periodo::integer,
                               id_gestion::integer,
                               periodo::integer,
                               gestion::integer,
                               venta_gravada_cero::numeric,
                               subtotal_venta::numeric,
                               sujeto_df::numeric,
                               importe_ice::numeric,
                               importe_excento::numeric
                        FROM '||v_tabla_origen||' lcv
                        where  lcv.tipo = '''||v_tipo||'''
                               and id_moneda = '||param.f_get_moneda_base()||'
                               and '||v_filtro||'
                        order by fecha, id_doc_compra_venta';

			raise notice '%', v_consulta;
			--Devuelve la respuesta
			return v_consulta;

		end;

    /*********************************
 	#TRANSACCION:  'CONTA_REPLCV_ENDESIS_ERP'
 	#DESCRIPCION:	listado consolidado para reporte de libro de compras y ventas  desde formulario, tanto del endesis como del erp
 	#AUTOR:		Gonzalo Sarmiento Sejas
 	#FECHA:		18-08-2015 15:57:09
	***********************************/

	ELSEIF(p_transaccion='CONTA_REPLCV_ENDERP')then

    	begin

           IF v_parametros.filtro_sql = 'periodo'  THEN
               v_filtro =  ' (lcv.id_periodo = '||v_parametros.id_periodo||')  ';
           ELSE
               v_filtro =  ' (lcv.fecha::Date between '''||v_parametros.fecha_ini||'''::Date  and '''||v_parametros.fecha_fin||'''::date)  ';
           END IF;

           IF v_parametros.id_usuario != 0 THEN
           		v_filtro = v_filtro || ' and lcv.id_usuario_reg='||v_parametros.id_usuario||' ';
           END IF;

          IF v_parametros.tipo_lcv = 'lcv_compras' or v_parametros.tipo_lcv='endesis_erp' THEN
              v_tipo = 'compra';
          ELSE
              v_tipo = 'venta';
          END IF;

          --Sentencia de la consulta
		  v_consulta:='SELECT id_doc_compra_venta::BIGINT,
                               tipo::Varchar,
                               fecha::date,
                               nit::varchar,
                               razon_social::Varchar,
                               COALESCE(nro_documento::varchar, ''0'')::Varchar,
                               COALESCE(nro_dui::varchar, ''0'')::Varchar,
                               nro_autorizacion::Varchar,
                               importe_doc::numeric,
                               total_excento::numeric,
                               sujeto_cf::numeric,
                               importe_descuento::numeric,
                               subtotal::numeric,
                               credito_fiscal::numeric,
                               importe_iva::numeric,
                               codigo_control::varchar,
                               tipo_doc::varchar,
                               id_plantilla::integer,
                               id_moneda::integer,
                               codigo_moneda::Varchar,
                               id_periodo::integer,
                               id_gestion::integer,
                               periodo::integer,
                               gestion::integer,
                               venta_gravada_cero::numeric,
                               subtotal_venta::numeric,
                               sujeto_df::numeric,
                               importe_ice::numeric,
                               importe_excento::numeric
                        FROM conta.tlcv_endesis lcv
                        where  lcv.tipo = '''||v_tipo||'''
                               and id_moneda = '||param.f_get_moneda_base()||'
                               and '||v_filtro||'
                        UNION ALL
                        SELECT id_doc_compra_venta::BIGINT,
                               tipo::Varchar,
                               fecha::date,
                               nit::varchar,
                               razon_social::Varchar,
                               COALESCE(nro_documento::varchar, ''0'')::Varchar,
                               COALESCE(nro_dui::varchar, ''0'')::Varchar,
                               nro_autorizacion::Varchar,
                               importe_doc::numeric,
                               total_excento::numeric,
                               sujeto_cf::numeric,
                               importe_descuento::numeric,
                               subtotal::numeric,
                               credito_fiscal::numeric,
                               importe_iva::numeric,
                               codigo_control::varchar,
                               tipo_doc::varchar,
                               id_plantilla::integer,
                               id_moneda::integer,
                               codigo_moneda::Varchar,
                               id_periodo::integer,
                               id_gestion::integer,
                               periodo::integer,
                               gestion::integer,
                               venta_gravada_cero::numeric,
                               subtotal_venta::numeric,
                               sujeto_df::numeric,
                               importe_ice::numeric,
                               importe_excento::numeric
                        FROM conta.vlcv lcv
                        where  lcv.tipo = '''||v_tipo||'''
                               and id_moneda = '||param.f_get_moneda_base()||'
                               and '||v_filtro||'
                        order by fecha, id_doc_compra_venta';

			raise notice '%', v_consulta;
			--Devuelve la respuesta
			return v_consulta;

		end;

    /*********************************
 	#TRANSACCION:  'CONTA_REP_DIF'
 	#DESCRIPCION:	Reporte detalde de facturas fecha contra periodo diferente
 	#AUTOR:		MMV
 	#FECHA:		24-11-2017
	***********************************/

	ELSEIF(p_transaccion='CONTA_REP_DIF')then

    	begin
        v_consulta:='select
						dff.id_doc_compra_venta,
						dff.nro_documento,
						dff.nro_autorizacion,
						dff.desc_persona,
						dff.importe_iva,
						dff.periodo_doc,
						dff.nro_tramite,
						dff.nombre,
						dff.codigo_control,
						dff.fecha,
						dff.importe_ice,
						dff.importe_pago_liquido,
						dff.tipo,
						dff.obs,
						dff.nit,
						dff.desc_plantilla,
						dff.razon_social,
						dff.importe_doc,
						dff.importe_excento,
						dff.periodo,
						dff.importe_neto,
						dff.importe_it,
						dff.importe_descuento_ley,
                        dff.gestion
						from conta.vdiferencia_periodo dff
                        where';
             v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

        	 raise notice '%', v_consulta;
			--Devuelve la respuesta
			return v_consulta;

		end;

      /*********************************
 	#TRANSACCION:  'CONTA_DCVLIST_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		Maylee Perez Pastor
 	#FECHA:		26-08-2019 15:57:09
	***********************************/

	elsif(p_transaccion='CONTA_DCVLIST_SEL')then

    	begin
    		--Sentencia de la consulta
			v_consulta:='select
                            dcv.id_doc_compra_venta,
                            dcv.revisado,
                            dcv.movil,
                            dcv.tipo,
                            COALESCE(dcv.importe_excento,0)::numeric as importe_excento,
                            dcv.id_plantilla,
                            dcv.fecha,
                            dcv.nro_documento,
                            dcv.nit,
                            COALESCE(dcv.importe_ice,0)::numeric as importe_ice,
                            dcv.nro_autorizacion,
                            COALESCE(dcv.importe_iva,0)::numeric as importe_iva,
                            COALESCE(dcv.importe_descuento,0)::numeric as importe_descuento,
                            COALESCE(dcv.importe_doc,0)::numeric as importe_doc,
                            dcv.sw_contabilizar,
                            COALESCE(dcv.tabla_origen,''ninguno'') as tabla_origen,
                            dcv.estado,
                            dcv.id_depto_conta,
                            dcv.id_origen,
                            dcv.obs,
                            dcv.estado_reg,
                            dcv.codigo_control,
                            COALESCE(dcv.importe_it,0)::numeric as importe_it,
                            dcv.razon_social,
                            dcv.id_usuario_ai,
                            dcv.id_usuario_reg,
                            dcv.fecha_reg,
                            dcv.usuario_ai,
                            dcv.id_usuario_mod,
                            dcv.fecha_mod,
                            usu1.cuenta as usr_reg,
                            usu2.cuenta as usr_mod,
                            dep.nombre as desc_depto,
                            pla.desc_plantilla,
                            COALESCE(dcv.importe_descuento_ley,0)::numeric as importe_descuento_ley,
                            COALESCE(dcv.importe_pago_liquido,0)::numeric as importe_pago_liquido,
                            dcv.nro_dui,
                            dcv.id_moneda,
                            mon.codigo as desc_moneda,
                            dcv.id_int_comprobante,
                            COALESCE(dcv.nro_tramite,''''),
                            COALESCE(ic.nro_cbte,dcv.id_int_comprobante::varchar)::varchar  as desc_comprobante,
                            COALESCE(dcv.importe_pendiente,0)::numeric as importe_pendiente,
                            COALESCE(dcv.importe_anticipo,0)::numeric as importe_anticipo,
                            COALESCE(dcv.importe_retgar,0)::numeric as importe_retgar,
                            COALESCE(dcv.importe_neto,0)::numeric as importe_neto,
                            aux.id_auxiliar,
                            aux.codigo_auxiliar,
                            aux.nombre_auxiliar,
                            dcv.id_tipo_doc_compra_venta,
                            (tdcv.codigo||'' - ''||tdcv.nombre)::Varchar as desc_tipo_doc_compra_venta,
                            (dcv.importe_doc -  COALESCE(dcv.importe_descuento,0) - COALESCE(dcv.importe_excento,0))     as importe_aux_neto,
                            dcv.id_plan_pago,
                            dcv.fecha_vencimiento,
                            dcv.id_proveedor,
                            dcv.tipo_cambio

						from conta.tdoc_compra_venta dcv
                          inner join segu.tusuario usu1 on usu1.id_usuario = dcv.id_usuario_reg
                          inner join param.tplantilla pla on pla.id_plantilla = dcv.id_plantilla
                          inner join param.tmoneda mon on mon.id_moneda = dcv.id_moneda
                          inner join conta.ttipo_doc_compra_venta tdcv on tdcv.id_tipo_doc_compra_venta = dcv.id_tipo_doc_compra_venta
                          left join conta.tauxiliar aux on aux.id_auxiliar = dcv.id_auxiliar
                          left join conta.tint_comprobante ic on ic.id_int_comprobante = dcv.id_int_comprobante
                          left join param.tdepto dep on dep.id_depto = dcv.id_depto_conta
                          left join segu.tusuario usu2 on usu2.id_usuario = dcv.id_usuario_mod
				        where ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
            v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;
			raise notice 'v_consulta: %', v_consulta;
			--Devuelve la respuesta
			return v_consulta;

		end;
     /*********************************
 	#TRANSACCION:  'CONTA_DCVLIST_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		Maylee Perez Pastor
 	#FECHA:		26-08-2019 15:57:09
	***********************************/

	elsif(p_transaccion='CONTA_DCVLIST_CONT')then

		begin
			--Sentencia de la consulta de conteo de registros
			v_consulta:='select
                              count(dcv.id_doc_compra_venta),
                              COALESCE(sum(dcv.importe_ice),0)::numeric  as total_importe_ice,
                              COALESCE(sum(dcv.importe_excento),0)::numeric  as total_importe_excento,
                              COALESCE(sum(dcv.importe_it),0)::numeric  as total_importe_it,
                              COALESCE(sum(dcv.importe_iva),0)::numeric  as total_importe_iva,
                              COALESCE(sum(dcv.importe_descuento),0)::numeric  as total_importe_descuento,
                              COALESCE(sum(dcv.importe_doc),0)::numeric  as total_importe_doc,
                              COALESCE(sum(dcv.importe_retgar),0)::numeric  as total_importe_retgar,
                              COALESCE(sum(dcv.importe_anticipo),0)::numeric  as total_importe_anticipo,
                              COALESCE(sum(dcv.importe_pendiente),0)::numeric  as tota_importe_pendiente,
                              COALESCE(sum(dcv.importe_neto),0)::numeric  as total_importe_neto,
                              COALESCE(sum(dcv.importe_descuento_ley),0)::numeric  as total_importe_descuento_ley,
                              COALESCE(sum(dcv.importe_pago_liquido),0)::numeric  as total_importe_pago_liquido,
                              COALESCE(sum(dcv.importe_doc -  COALESCE(dcv.importe_descuento,0) - COALESCE(dcv.importe_excento,0)),0) as total_importe_aux_neto

					   from conta.tdoc_compra_venta dcv
                          inner join segu.tusuario usu1 on usu1.id_usuario = dcv.id_usuario_reg
                          inner join param.tplantilla pla on pla.id_plantilla = dcv.id_plantilla
                          inner join param.tmoneda mon on mon.id_moneda = dcv.id_moneda
                          inner join conta.ttipo_doc_compra_venta tdcv on tdcv.id_tipo_doc_compra_venta = dcv.id_tipo_doc_compra_venta
                          left join conta.tauxiliar aux on aux.id_auxiliar = dcv.id_auxiliar
                          left join conta.tint_comprobante ic on ic.id_int_comprobante = dcv.id_int_comprobante
                          left join param.tdepto dep on dep.id_depto = dcv.id_depto_conta
                          left join segu.tusuario usu2 on usu2.id_usuario = dcv.id_usuario_mod
				        where dcv.id_int_comprobante is Null and dcv.id_plan_pago is Null and ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
            raise notice '%', v_consulta;
			--Devuelve la respuesta
			return v_consulta;

		end;

        /*********************************
 	#TRANSACCION:  'CONTA_DCV_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		Maylee Perez Pastor
 	#FECHA:		31-08-2019 15:57:09
	***********************************/

	elsif(p_transaccion='CONTA_DCV_SEL')then

    	begin
    		--Sentencia de la consulta
			v_consulta:='select
                            dcv.id_doc_compra_venta,
                            dcv.revisado,
                            dcv.movil,
                            dcv.tipo,
                            COALESCE(dcv.importe_excento,0)::numeric as importe_excento,
                            dcv.id_plantilla,
                            dcv.fecha,
                            dcv.nro_documento,
                            dcv.nit,
                            COALESCE(dcv.importe_ice,0)::numeric as importe_ice,
                            dcv.nro_autorizacion,
                            COALESCE(dcv.importe_iva,0)::numeric as importe_iva,
                            COALESCE(dcv.importe_descuento,0)::numeric as importe_descuento,
                            COALESCE(dcv.importe_doc,0)::numeric as importe_doc,
                            dcv.sw_contabilizar,
                            COALESCE(dcv.tabla_origen,''ninguno'') as tabla_origen,
                            dcv.estado,
                            dcv.id_depto_conta,
                            dcv.id_origen,
                            dcv.obs,
                            dcv.estado_reg,
                            dcv.codigo_control,
                            COALESCE(dcv.importe_it,0)::numeric as importe_it,
                            dcv.razon_social,
                            dcv.id_usuario_ai,
                            dcv.id_usuario_reg,
                            dcv.fecha_reg,
                            dcv.usuario_ai,
                            dcv.id_usuario_mod,
                            dcv.fecha_mod,
                            usu1.cuenta as usr_reg,
                            usu2.cuenta as usr_mod,
                            dep.nombre as desc_depto,
                            pla.desc_plantilla,
                            COALESCE(dcv.importe_descuento_ley,0)::numeric as importe_descuento_ley,
                            COALESCE(dcv.importe_pago_liquido,0)::numeric as importe_pago_liquido,
                            dcv.nro_dui,
                            dcv.id_moneda,
                            mon.codigo as desc_moneda,
                            dcv.id_int_comprobante,
                            COALESCE(dcv.nro_tramite,''''),
                            COALESCE(ic.nro_cbte,dcv.id_int_comprobante::varchar)::varchar  as desc_comprobante,
                            COALESCE(dcv.importe_pendiente,0)::numeric as importe_pendiente,
                            COALESCE(dcv.importe_anticipo,0)::numeric as importe_anticipo,
                            COALESCE(dcv.importe_retgar,0)::numeric as importe_retgar,
                            COALESCE(dcv.importe_neto,0)::numeric as importe_neto,
                            aux.id_auxiliar,
                            aux.codigo_auxiliar,
                            aux.nombre_auxiliar,
                            dcv.id_tipo_doc_compra_venta,
                            (tdcv.codigo||'' - ''||tdcv.nombre)::Varchar as desc_tipo_doc_compra_venta,
                            (dcv.importe_doc -  COALESCE(dcv.importe_descuento,0) - COALESCE(dcv.importe_excento,0))     as importe_aux_neto,
                            dcv.id_plan_pago,
                            dcv.fecha_vencimiento,
                            dcv.tipo_cambio

						from conta.tdoc_compra_venta dcv
                          inner join segu.tusuario usu1 on usu1.id_usuario = dcv.id_usuario_reg
                          inner join param.tplantilla pla on pla.id_plantilla = dcv.id_plantilla
                          inner join param.tmoneda mon on mon.id_moneda = dcv.id_moneda
                          inner join conta.ttipo_doc_compra_venta tdcv on tdcv.id_tipo_doc_compra_venta = dcv.id_tipo_doc_compra_venta
                          left join conta.tauxiliar aux on aux.id_auxiliar = dcv.id_auxiliar
                          left join conta.tint_comprobante ic on ic.id_int_comprobante = dcv.id_int_comprobante
                          left join param.tdepto dep on dep.id_depto = dcv.id_depto_conta
                          left join segu.tusuario usu2 on usu2.id_usuario = dcv.id_usuario_mod
				        where ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
            v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;
			raise notice 'v_consulta: %', v_consulta;
			--Devuelve la respuesta
			return v_consulta;

		end;
     /*********************************
 	#TRANSACCION:  'CONTA_DCV_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		Maylee Perez Pastor
 	#FECHA:		31-08-2019 15:57:09
	***********************************/

	elsif(p_transaccion='CONTA_DCV_CONT')then

		begin
			--Sentencia de la consulta de conteo de registros
			v_consulta:='select

					   from conta.tdoc_compra_venta dcv
                          inner join segu.tusuario usu1 on usu1.id_usuario = dcv.id_usuario_reg
                          inner join param.tplantilla pla on pla.id_plantilla = dcv.id_plantilla
                          inner join param.tmoneda mon on mon.id_moneda = dcv.id_moneda
                          inner join conta.ttipo_doc_compra_venta tdcv on tdcv.id_tipo_doc_compra_venta = dcv.id_tipo_doc_compra_venta
                          left join conta.tauxiliar aux on aux.id_auxiliar = dcv.id_auxiliar
                          left join conta.tint_comprobante ic on ic.id_int_comprobante = dcv.id_int_comprobante
                          left join param.tdepto dep on dep.id_depto = dcv.id_depto_conta
                          left join segu.tusuario usu2 on usu2.id_usuario = dcv.id_usuario_mod
				        where ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
            raise notice '%', v_consulta;
			--Devuelve la respuesta
			return v_consulta;

		end;


        /*********************************
        #TRANSACCION:  'CONTA_REPDCVEXT_SEL'
        #DESCRIPCION:	Reporte de documentos compra y venta del ext
        #AUTOR:		Maylee Perez Pastor
        #FECHA:		28-02-2020
        ***********************************/

        elsif(p_transaccion='CONTA_REPDCVEXT_SEL')then

            begin
                --dcv.id_plantilla = 41 nota de credito

                v_consulta = ' select   prov.num_proveedor::varchar,
                                        dcv.razon_social::varchar,
                                        dcv.nit::varchar,
                                        prov.condicion::varchar,
                                        prov.actividad::varchar,
                                        dcv.costo_directo::varchar,
                                        dcv.obs::varchar,
                                        cbte.fecha::date,
                                        plan.desc_plantilla::varchar,
                                        dcv.fecha_doc::date,
                                        plan.codigo::varchar,
                                        plan.letra_tipo_plantilla::varchar,
                                        dcv.c_emisor::varchar,
                                        dcv.nro_documento::varchar,
                                        cbte.id_int_comprobante::integer,

                                        (COALESCE(CASE WHEN dcv.id_moneda = 2 AND dcv.id_plantilla = 41 THEN -(COALESCE (dcv.importe_excento, 0) * COALESCE (dcv.tipo_cambio, 0))
                                                       WHEN dcv.id_moneda = 2 AND dcv.id_plantilla != 41 THEN (COALESCE (dcv.importe_excento, 0) * COALESCE (dcv.tipo_cambio, 0))
                                                       WHEN dcv.id_moneda != 2 AND dcv.id_plantilla = 41 THEN -(COALESCE(dcv.importe_excento, 0))::numeric
                                                       WHEN dcv.id_moneda != 2 AND dcv.id_plantilla != 41 THEN (COALESCE(dcv.importe_excento, 0))::numeric
                                        END))::numeric as importe_excento,

                                        (COALESCE(CASE WHEN dcv.id_moneda = 2 AND dcv.id_plantilla = 41 THEN -(COALESCE (dcv.no_gravado, 0) * COALESCE (dcv.tipo_cambio, 0))
                                                       WHEN dcv.id_moneda = 2 AND dcv.id_plantilla != 41 THEN (COALESCE (dcv.no_gravado, 0) * COALESCE (dcv.tipo_cambio, 0))
                                                       WHEN dcv.id_moneda != 2 AND dcv.id_plantilla = 41 THEN -(COALESCE(dcv.no_gravado, 0))::numeric
                                                       WHEN dcv.id_moneda != 2 AND dcv.id_plantilla != 41 THEN (COALESCE(dcv.no_gravado, 0))::numeric
                                        END))::numeric as no_gravado,

                                        (COALESCE(CASE WHEN dcv.id_moneda = 2 AND dcv.id_plantilla = 41 THEN -(COALESCE (dcv.base_21, 0) * COALESCE (dcv.tipo_cambio, 0))
                                                       WHEN dcv.id_moneda = 2 AND dcv.id_plantilla != 41 THEN (COALESCE (dcv.base_21, 0) * COALESCE (dcv.tipo_cambio, 0))
                                                       WHEN dcv.id_moneda != 2 AND dcv.id_plantilla = 41 THEN -(COALESCE(dcv.base_21, 0))::numeric
                                                       WHEN dcv.id_moneda != 2 AND dcv.id_plantilla != 41 THEN (COALESCE(dcv.base_21, 0))::numeric
                                        END))::numeric as base_21,

                                        (COALESCE(CASE WHEN dcv.id_moneda = 2 AND dcv.id_plantilla = 41 THEN -(COALESCE (dcv.base_27, 0) * COALESCE (dcv.tipo_cambio, 0))
                                                       WHEN dcv.id_moneda = 2 AND dcv.id_plantilla != 41 THEN (COALESCE (dcv.base_27, 0) * COALESCE (dcv.tipo_cambio, 0))
                                                       WHEN dcv.id_moneda != 2 AND dcv.id_plantilla = 41 THEN -(COALESCE(dcv.base_27, 0))::numeric
                                                       WHEN dcv.id_moneda != 2 AND dcv.id_plantilla != 41 THEN (COALESCE(dcv.base_27, 0))::numeric
                                        END))::numeric as base_27,

                                        (COALESCE(CASE WHEN dcv.id_moneda = 2 AND dcv.id_plantilla = 41 THEN -(COALESCE (dcv.base_10_5, 0) * COALESCE (dcv.tipo_cambio, 0))
                                                       WHEN dcv.id_moneda = 2 AND dcv.id_plantilla != 41 THEN (COALESCE (dcv.base_10_5, 0) * COALESCE (dcv.tipo_cambio, 0))
                                                       WHEN dcv.id_moneda != 2 AND dcv.id_plantilla = 41 THEN -(COALESCE(dcv.base_10_5, 0))::numeric
                                                       WHEN dcv.id_moneda != 2 AND dcv.id_plantilla != 41 THEN (COALESCE(dcv.base_10_5, 0))::numeric
                                        END))::numeric as base_10_5,

                                        (COALESCE(CASE WHEN dcv.id_moneda = 2 AND dcv.id_plantilla = 41 THEN -(COALESCE (dcv.base_2_5, 0) * COALESCE (dcv.tipo_cambio, 0))
                                                       WHEN dcv.id_moneda = 2 AND dcv.id_plantilla != 41 THEN (COALESCE (dcv.base_2_5, 0) * COALESCE (dcv.tipo_cambio, 0))
                                                       WHEN dcv.id_moneda != 2 AND dcv.id_plantilla = 41 THEN -(COALESCE(dcv.base_2_5, 0))::numeric
                                                       WHEN dcv.id_moneda != 2 AND dcv.id_plantilla != 41 THEN (COALESCE(dcv.base_2_5, 0))::numeric
                                        END))::numeric as base_2_5,

                                        (COALESCE(CASE WHEN dcv.id_moneda = 2 AND dcv.id_plantilla = 41 THEN -(COALESCE (dcv.base_21 * 0.21, 0) * COALESCE (dcv.tipo_cambio, 0))
                                                       WHEN dcv.id_moneda = 2 AND dcv.id_plantilla != 41 THEN (COALESCE (dcv.base_21 * 0.21, 0) * COALESCE (dcv.tipo_cambio, 0))
                                                       WHEN dcv.id_moneda != 2 AND dcv.id_plantilla = 41 THEN -(COALESCE((dcv.base_21 * 0.21), 0))::numeric
                                                       WHEN dcv.id_moneda != 2 AND dcv.id_plantilla != 41 THEN (COALESCE((dcv.base_21 * 0.21), 0))::numeric
                                        END))::numeric as impor_base_21,

                                        (COALESCE(CASE WHEN dcv.id_moneda = 2 AND dcv.id_plantilla = 41 THEN -(COALESCE (dcv.base_27 * 0.27, 0) * COALESCE (dcv.tipo_cambio, 0))
                                                       WHEN dcv.id_moneda = 2 AND dcv.id_plantilla != 41 THEN (COALESCE (dcv.base_27 * 0.27, 0) * COALESCE (dcv.tipo_cambio, 0))
                                                       WHEN dcv.id_moneda != 2 AND dcv.id_plantilla = 41 THEN -(COALESCE((dcv.base_27 * 0.27), 0))::numeric
                                                       WHEN dcv.id_moneda != 2 AND dcv.id_plantilla != 41 THEN (COALESCE((dcv.base_27 * 0.27), 0))::numeric
                                        END))::numeric as impor_base_27,

                                        (COALESCE(CASE WHEN dcv.id_moneda = 2 AND dcv.id_plantilla = 41 THEN -(COALESCE (dcv.base_10_5 * 0.105, 0) * COALESCE (dcv.tipo_cambio, 0))
                                                       WHEN dcv.id_moneda = 2 AND dcv.id_plantilla != 41 THEN (COALESCE (dcv.base_10_5 * 0.105, 0) * COALESCE (dcv.tipo_cambio, 0))
                                                       WHEN dcv.id_moneda != 2 AND dcv.id_plantilla = 41 THEN -(COALESCE((dcv.base_10_5 * 0.105), 0))::numeric
                                                       WHEN dcv.id_moneda != 2 AND dcv.id_plantilla != 41 THEN (COALESCE((dcv.base_10_5 * 0.105), 0))::numeric
                                        END))::numeric as impor_base_10_5,

                                        (COALESCE(CASE WHEN dcv.id_moneda = 2 AND dcv.id_plantilla = 41 THEN -(COALESCE (dcv.base_2_5 * 0.025, 0) * COALESCE (dcv.tipo_cambio, 0))
                                                       WHEN dcv.id_moneda = 2 AND dcv.id_plantilla != 41 THEN (COALESCE (dcv.base_2_5 * 0.025, 0) * COALESCE (dcv.tipo_cambio, 0))
                                                       WHEN dcv.id_moneda != 2 AND dcv.id_plantilla = 41 THEN -(COALESCE((dcv.base_2_5 * 0.025), 0))::numeric
                                                       WHEN dcv.id_moneda != 2 AND dcv.id_plantilla != 41 THEN (COALESCE((dcv.base_2_5 * 0.025), 0))::numeric
                                        END))::numeric as impor_base_2_5,

                                        (COALESCE(CASE WHEN dcv.id_moneda = 2 AND dcv.id_plantilla = 41 THEN -(COALESCE (dcv.percepcion_caba, 0) * COALESCE (dcv.tipo_cambio, 0))
                                                       WHEN dcv.id_moneda = 2 AND dcv.id_plantilla != 41 THEN (COALESCE (dcv.percepcion_caba, 0) * COALESCE (dcv.tipo_cambio, 0))
                                                       WHEN dcv.id_moneda != 2 AND dcv.id_plantilla = 41 THEN -(COALESCE(dcv.percepcion_caba, 0))::numeric
                                                       WHEN dcv.id_moneda != 2 AND dcv.id_plantilla != 41 THEN (COALESCE(dcv.percepcion_caba, 0))::numeric
                                        END))::numeric as percepcion_caba,

                                        (COALESCE(CASE WHEN dcv.id_moneda = 2 AND dcv.id_plantilla = 41 THEN -(COALESCE (dcv.percepcion_bue, 0) * COALESCE (dcv.tipo_cambio, 0))
                                                       WHEN dcv.id_moneda = 2 AND dcv.id_plantilla != 41 THEN (COALESCE (dcv.percepcion_bue, 0) * COALESCE (dcv.tipo_cambio, 0))
                                                       WHEN dcv.id_moneda != 2 AND dcv.id_plantilla = 41 THEN -(COALESCE(dcv.percepcion_bue, 0))::numeric
                                                       WHEN dcv.id_moneda != 2 AND dcv.id_plantilla != 41 THEN (COALESCE(dcv.percepcion_bue, 0))::numeric
                                        END))::numeric as percepcion_bue,

                                        (COALESCE(CASE WHEN dcv.id_moneda = 2 AND dcv.id_plantilla = 41 THEN -(COALESCE (dcv.percepcion_iva, 0) * COALESCE (dcv.tipo_cambio, 0))
                                                       WHEN dcv.id_moneda = 2 AND dcv.id_plantilla != 41 THEN (COALESCE (dcv.percepcion_iva, 0) * COALESCE (dcv.tipo_cambio, 0))
                                                       WHEN dcv.id_moneda != 2 AND dcv.id_plantilla = 41 THEN -(COALESCE(dcv.percepcion_iva, 0))::numeric
                                                       WHEN dcv.id_moneda != 2 AND dcv.id_plantilla != 41 THEN (COALESCE(dcv.percepcion_iva, 0))::numeric
                                        END))::numeric as percepcion_iva,

                                        (COALESCE(CASE WHEN dcv.id_moneda = 2 AND dcv.id_plantilla = 41 THEN -(COALESCE (dcv.percepcion_salta, 0) * COALESCE (dcv.tipo_cambio, 0))
                                                       WHEN dcv.id_moneda = 2 AND dcv.id_plantilla != 41 THEN (COALESCE (dcv.percepcion_salta, 0) * COALESCE (dcv.tipo_cambio, 0))
                                                       WHEN dcv.id_moneda != 2 AND dcv.id_plantilla = 41 THEN -(COALESCE(dcv.percepcion_salta, 0))::numeric
                                                       WHEN dcv.id_moneda != 2 AND dcv.id_plantilla != 41 THEN (COALESCE(dcv.percepcion_salta, 0))::numeric
                                        END))::numeric as percepcion_salta,

                                        (COALESCE(CASE WHEN dcv.id_moneda = 2 AND dcv.id_plantilla = 41 THEN -(COALESCE (dcv.imp_internos, 0) * COALESCE (dcv.tipo_cambio, 0))
                                                       WHEN dcv.id_moneda = 2 AND dcv.id_plantilla != 41 THEN (COALESCE (dcv.imp_internos, 0) * COALESCE (dcv.tipo_cambio, 0))
                                                       WHEN dcv.id_moneda != 2 AND dcv.id_plantilla = 41 THEN -(COALESCE(dcv.imp_internos, 0))::numeric
                                                       WHEN dcv.id_moneda != 2 AND dcv.id_plantilla != 41 THEN (COALESCE(dcv.imp_internos, 0))::numeric
                                        END))::numeric as imp_internos,

                                        (COALESCE(CASE WHEN dcv.id_moneda = 2 AND dcv.id_plantilla = 41 THEN -(COALESCE (dcv.percepcion_tucuman, 0) * COALESCE (dcv.tipo_cambio, 0))
                                                       WHEN dcv.id_moneda = 2 AND dcv.id_plantilla != 41 THEN (COALESCE (dcv.percepcion_tucuman, 0) * COALESCE (dcv.tipo_cambio, 0))
                                                       WHEN dcv.id_moneda != 2 AND dcv.id_plantilla = 41 THEN -(COALESCE(dcv.percepcion_tucuman, 0))::numeric
                                                       WHEN dcv.id_moneda != 2 AND dcv.id_plantilla != 41 THEN (COALESCE(dcv.percepcion_tucuman, 0))::numeric
                                        END))::numeric as percepcion_tucuman,

                                        (COALESCE(CASE WHEN dcv.id_moneda = 2 AND dcv.id_plantilla = 41 THEN -(COALESCE (dcv.percepcion_corrientes, 0) * COALESCE (dcv.tipo_cambio, 0))
                                                       WHEN dcv.id_moneda = 2 AND dcv.id_plantilla != 41 THEN (COALESCE (dcv.percepcion_corrientes, 0) * COALESCE (dcv.tipo_cambio, 0))
                                                       WHEN dcv.id_moneda != 2 AND dcv.id_plantilla = 41 THEN -(COALESCE(dcv.percepcion_corrientes, 0))::numeric
                                                       WHEN dcv.id_moneda != 2 AND dcv.id_plantilla != 41 THEN (COALESCE(dcv.percepcion_corrientes, 0))::numeric
                                        END))::numeric as percepcion_corrientes,

                                        (COALESCE(CASE WHEN dcv.id_moneda = 2 AND dcv.id_plantilla = 41 THEN -(COALESCE (dcv.otros_impuestos, 0) * COALESCE (dcv.tipo_cambio, 0))
                                                       WHEN dcv.id_moneda = 2 AND dcv.id_plantilla != 41 THEN (COALESCE (dcv.otros_impuestos, 0) * COALESCE (dcv.tipo_cambio, 0))
                                                       WHEN dcv.id_moneda != 2 AND dcv.id_plantilla = 41 THEN -(COALESCE(dcv.otros_impuestos, 0))::numeric
                                                       WHEN dcv.id_moneda != 2 AND dcv.id_plantilla != 41 THEN (COALESCE(dcv.otros_impuestos, 0))::numeric
                                        END))::numeric as otros_impuestos,

                                        (COALESCE(CASE WHEN dcv.id_moneda = 2 AND dcv.id_plantilla = 41 THEN -(COALESCE (dcv.percepcion_neuquen, 0) * COALESCE (dcv.tipo_cambio, 0))
                                                       WHEN dcv.id_moneda = 2 AND dcv.id_plantilla != 41 THEN (COALESCE (dcv.percepcion_neuquen, 0) * COALESCE (dcv.tipo_cambio, 0))
                                                       WHEN dcv.id_moneda != 2 AND dcv.id_plantilla = 41 THEN -(COALESCE(dcv.percepcion_neuquen, 0))::numeric
                                                       WHEN dcv.id_moneda != 2 AND dcv.id_plantilla != 41 THEN (COALESCE(dcv.percepcion_neuquen, 0))::numeric
                                        END))::numeric as percepcion_neuquen,

										(COALESCE(CASE WHEN dcv.id_moneda = 2  THEN (COALESCE (vce.importe_postergacion_covid, 0) * COALESCE (dcv.tipo_cambio, 0))
                                                     WHEN dcv.id_moneda != 2 THEN (COALESCE(vce.importe_postergacion_covid, 0))::numeric
                                        END))::numeric as importe_postergacion_covid,

                                        (COALESCE(CASE WHEN dcv.id_moneda = 2 AND dcv.id_plantilla = 41 THEN -(COALESCE((COALESCE(dcv.importe_excento, 0) + COALESCE(dcv.no_gravado, 0) +
                                                                                                                COALESCE(dcv.base_21, 0) + COALESCE(dcv.base_27, 0) +
                                                                                                                COALESCE(dcv.base_10_5, 0) + COALESCE(dcv.base_2_5, 0) +
                                                                                                                COALESCE((dcv.base_21 * 0.21), 0) + COALESCE((dcv.base_27 * 0.27), 0) +
                                                                                                                COALESCE((dcv.base_10_5 * 0.105), 0) +  COALESCE((dcv.base_2_5 *  0.025 ), 0) +
                                                                                                                COALESCE( dcv.percepcion_caba, 0) + COALESCE( dcv.percepcion_bue, 0) +
                                                                                                                COALESCE(dcv.percepcion_iva, 0) + COALESCE(dcv.percepcion_salta, 0) +
                                                                                                                COALESCE(dcv.imp_internos, 0) + COALESCE(dcv.percepcion_tucuman, 0) +
                                                                                                                COALESCE(dcv.percepcion_corrientes, 0) + COALESCE(dcv.otros_impuestos, 0) +
                                                                                                                COALESCE(dcv.percepcion_neuquen, 0) + COALESCE(vce.importe_postergacion_covid, 0)
                                                                                                                ),0) * COALESCE (dcv.tipo_cambio, 0))
                                                       WHEN dcv.id_moneda = 2 AND dcv.id_plantilla != 41 THEN (COALESCE((COALESCE(dcv.importe_excento, 0) + COALESCE(dcv.no_gravado, 0) +
                                                                                                                COALESCE(dcv.base_21, 0) + COALESCE(dcv.base_27, 0) +
                                                                                                                COALESCE(dcv.base_10_5, 0) + COALESCE(dcv.base_2_5, 0) +
                                                                                                                COALESCE((dcv.base_21 * 0.21), 0) + COALESCE((dcv.base_27 * 0.27), 0) +
                                                                                                                COALESCE((dcv.base_10_5 * 0.105), 0) +  COALESCE((dcv.base_2_5 *  0.025 ), 0) +
                                                                                                                COALESCE( dcv.percepcion_caba, 0) + COALESCE( dcv.percepcion_bue, 0) +
                                                                                                                COALESCE(dcv.percepcion_iva, 0) + COALESCE(dcv.percepcion_salta, 0) +
                                                                                                                COALESCE(dcv.imp_internos, 0) + COALESCE(dcv.percepcion_tucuman, 0) +
                                                                                                                COALESCE(dcv.percepcion_corrientes, 0) + COALESCE(dcv.otros_impuestos, 0) +
                                                                                                                COALESCE(dcv.percepcion_neuquen, 0) + COALESCE(vce.importe_postergacion_covid, 0)
                                                                                                                ),0) * COALESCE (dcv.tipo_cambio, 0))

                                                       WHEN dcv.id_moneda != 2 AND dcv.id_plantilla = 41 THEN -(COALESCE((COALESCE(dcv.importe_excento, 0) + COALESCE(dcv.no_gravado, 0) +
                                                                                                                COALESCE(dcv.base_21, 0) + COALESCE(dcv.base_27, 0) +
                                                                                                                COALESCE(dcv.base_10_5, 0) + COALESCE(dcv.base_2_5, 0) +
                                                                                                                COALESCE((dcv.base_21 * 0.21), 0) + COALESCE((dcv.base_27 * 0.27), 0) +
                                                                                                                COALESCE((dcv.base_10_5 * 0.105), 0) +  COALESCE((dcv.base_2_5 *  0.025 ), 0) +
                                                                                                                COALESCE( dcv.percepcion_caba, 0) + COALESCE( dcv.percepcion_bue, 0) +
                                                                                                                COALESCE(dcv.percepcion_iva, 0) + COALESCE(dcv.percepcion_salta, 0) +
                                                                                                                COALESCE(dcv.imp_internos, 0) + COALESCE(dcv.percepcion_tucuman, 0) +
                                                                                                                COALESCE(dcv.percepcion_corrientes, 0) + COALESCE(dcv.otros_impuestos, 0) +
                                                                                                                COALESCE(dcv.percepcion_neuquen, 0) + COALESCE(vce.importe_postergacion_covid, 0)
                                                                                                                ),0))::numeric
                                                       WHEN dcv.id_moneda != 2 AND dcv.id_plantilla != 41 THEN (COALESCE((COALESCE(dcv.importe_excento, 0) + COALESCE(dcv.no_gravado, 0) +
                                                                                                                COALESCE(dcv.base_21, 0) + COALESCE(dcv.base_27, 0) +
                                                                                                                COALESCE(dcv.base_10_5, 0) + COALESCE(dcv.base_2_5, 0) +
                                                                                                                COALESCE((dcv.base_21 * 0.21), 0) + COALESCE((dcv.base_27 * 0.27), 0) +
                                                                                                                COALESCE((dcv.base_10_5 * 0.105), 0) +  COALESCE((dcv.base_2_5 *  0.025 ), 0) +
                                                                                                                COALESCE( dcv.percepcion_caba, 0) + COALESCE( dcv.percepcion_bue, 0) +
                                                                                                                COALESCE(dcv.percepcion_iva, 0) + COALESCE(dcv.percepcion_salta, 0) +
                                                                                                                COALESCE(dcv.imp_internos, 0) + COALESCE(dcv.percepcion_tucuman, 0) +
                                                                                                                COALESCE(dcv.percepcion_corrientes, 0) + COALESCE(dcv.otros_impuestos, 0) +
                                                                                                                COALESCE(dcv.percepcion_neuquen, 0)+ COALESCE(vce.importe_postergacion_covid, 0)
                                                                                                                ),0))::numeric
                                        END))::numeric as total


                                from conta.vdoc_compra_venta_ext dcv
                                left join param.tproveedor prov on prov.id_proveedor = dcv.id_proveedor
                                left join conta.tint_comprobante cbte on cbte.id_int_comprobante = dcv.id_int_comprobante
                                left join param.tplantilla plan on plan.id_plantilla = dcv.id_plantilla

                                left join conta.tdoc_compra_venta_ext vce on vce.id_doc_compra_venta = dcv.id_doc_compra_venta

                where cbte.fecha BETWEEN  '''||v_parametros.fecha_ini||''' and '''||v_parametros.fecha_fin ||'''
                ';

                raise notice '%',v_consulta;
                return v_consulta;

            end;


            /*********************************
            #TRANSACCION:  'CONTA_DCVEXT_SEL'
            #DESCRIPCION:	Consulta de datos
            #AUTOR:		Maylee Perez Pastor
            #FECHA:		13-03-2020 15:57:09
            ***********************************/

            elsif(p_transaccion='CONTA_DCVEXT_SEL')then

                begin
                    --Sentencia de la consulta
                    v_consulta:='select
                                    dcv.id_doc_compra_venta,
                                    dcv.revisado,
                                    dcv.movil,
                                    dcv.tipo,
                                    COALESCE(dcv.importe_excento,0)::numeric as importe_excento,
                                    dcv.id_plantilla,
                                    dcv.fecha,
                                    dcv.nro_documento,
                                    dcv.nit,
                                    COALESCE(dcv.importe_ice,0)::numeric as importe_ice,
                                    dcv.nro_autorizacion,
                                    COALESCE(dcv.importe_iva,0)::numeric as importe_iva,
                                    COALESCE(dcv.importe_descuento,0)::numeric as importe_descuento,
                                    COALESCE(dcv.importe_doc,0)::numeric as importe_doc,
                                    dcv.sw_contabilizar,
                                    COALESCE(dcv.tabla_origen,''ninguno'') as tabla_origen,
                                    dcv.estado,
                                    dcv.id_depto_conta,
                                    dcv.id_origen,
                                    dcv.obs,
                                    dcv.estado_reg,
                                    dcv.codigo_control,
                                    COALESCE(dcv.importe_it,0)::numeric as importe_it,
                                    dcv.razon_social,
                                    dcv.id_usuario_ai,
                                    dcv.id_usuario_reg,
                                    dcv.fecha_reg,
                                    dcv.usuario_ai,
                                    dcv.id_usuario_mod,
                                    dcv.fecha_mod,
                                    usu1.cuenta as usr_reg,
                                    usu2.cuenta as usr_mod,
                                    dep.nombre as desc_depto,
                                    pla.desc_plantilla,
                                    COALESCE(dcv.importe_descuento_ley,0)::numeric as importe_descuento_ley,
                                    COALESCE(dcv.importe_pago_liquido,0)::numeric as importe_pago_liquido,
                                    dcv.nro_dui,
                                    dcv.id_moneda,
                                    mon.codigo as desc_moneda,
                                    dcv.id_int_comprobante,
                                    COALESCE(dcv.nro_tramite,''''),
                                    COALESCE(ic.nro_cbte,dcv.id_int_comprobante::varchar)::varchar  as desc_comprobante,
                                    COALESCE(dcv.importe_pendiente,0)::numeric as importe_pendiente,
                                    COALESCE(dcv.importe_anticipo,0)::numeric as importe_anticipo,
                                    COALESCE(dcv.importe_retgar,0)::numeric as importe_retgar,
                                    COALESCE(dcv.importe_neto,0)::numeric as importe_neto,
                                    aux.id_auxiliar,
                                    aux.codigo_auxiliar,
                                    aux.nombre_auxiliar,
                                    dcv.id_tipo_doc_compra_venta,
                                    (tdcv.codigo||'' - ''||tdcv.nombre)::Varchar as desc_tipo_doc_compra_venta,
                                    (dcv.importe_doc -  COALESCE(dcv.importe_descuento,0) - COALESCE(dcv.importe_excento,0))     as importe_aux_neto,
                                    dcv.id_plan_pago,
                                    dcv.fecha_vencimiento,
                                    dcv.tipo_cambio,
                                    dcvext.costo_directo,
                                    dcvext.c_emisor,
                                    dcvext.no_gravado,
                                    dcvext.base_21,
                                    dcvext.base_27,
                                    dcvext.base_10_5,
                                    dcvext.base_2_5,
                                    dcvext.percepcion_caba,
                                    dcvext.percepcion_bue,
                                    dcvext.percepcion_iva,
                                    dcvext.percepcion_salta,
                                    dcvext.imp_internos,
                                    dcvext.percepcion_tucuman,
                                    dcvext.percepcion_corrientes,
                                    dcvext.otros_impuestos,
                                    dcvext.percepcion_neuquen,
                          			prov.num_proveedor,
                         			dcv.id_proveedor,
                                    prov.condicion,
                                    prov.desc_proveedor,

                                    dcvext.importe_postergacion_covid::numeric

                                from conta.tdoc_compra_venta dcv
                                  inner join segu.tusuario usu1 on usu1.id_usuario = dcv.id_usuario_reg
                                  inner join param.tplantilla pla on pla.id_plantilla = dcv.id_plantilla
                                  inner join param.tmoneda mon on mon.id_moneda = dcv.id_moneda
                                  inner join conta.ttipo_doc_compra_venta tdcv on tdcv.id_tipo_doc_compra_venta = dcv.id_tipo_doc_compra_venta
                                  left join conta.tauxiliar aux on aux.id_auxiliar = dcv.id_auxiliar
                                  left join conta.tint_comprobante ic on ic.id_int_comprobante = dcv.id_int_comprobante
                                  left join param.tdepto dep on dep.id_depto = dcv.id_depto_conta
                                  left join segu.tusuario usu2 on usu2.id_usuario = dcv.id_usuario_mod

                                  left join conta.tdoc_compra_venta_ext dcvext on dcvext.id_doc_compra_venta = dcv.id_doc_compra_venta
                                  left join param.vproveedor2 prov on prov.id_proveedor = dcv.id_proveedor

                                where ';

                    --Definicion de la respuesta
                    v_consulta:=v_consulta||v_parametros.filtro;
                    v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;
                    raise notice 'v_consulta: %', v_consulta;
                    --Devuelve la respuesta
                    return v_consulta;

                end;
             /*********************************
            #TRANSACCION:  'CONTA_DCVEXT_CONT'
            #DESCRIPCION:	Conteo de registros
            #AUTOR:		Maylee Perez Pastor
            #FECHA:		13-03-2020 15:57:09
            ***********************************/

            elsif(p_transaccion='CONTA_DCVEXT_CONT')then

                begin
                    --Sentencia de la consulta de conteo de registros
                    v_consulta:='select
                                      count(dcv.id_doc_compra_venta),
                                      COALESCE(sum(dcv.importe_ice),0)::numeric  as total_importe_ice,
                                      COALESCE(sum(dcv.importe_excento),0)::numeric  as total_importe_excento,
                                      COALESCE(sum(dcv.importe_it),0)::numeric  as total_importe_it,
                                      COALESCE(sum(dcv.importe_iva),0)::numeric  as total_importe_iva,
                                      COALESCE(sum(dcv.importe_descuento),0)::numeric  as total_importe_descuento,
                                      COALESCE(sum(dcv.importe_doc),0)::numeric  as total_importe_doc,
                                      COALESCE(sum(dcv.importe_retgar),0)::numeric  as total_importe_retgar,
                                      COALESCE(sum(dcv.importe_anticipo),0)::numeric  as total_importe_anticipo,
                                      COALESCE(sum(dcv.importe_pendiente),0)::numeric  as tota_importe_pendiente,
                                      COALESCE(sum(dcv.importe_neto),0)::numeric  as total_importe_neto,
                                      COALESCE(sum(dcv.importe_descuento_ley),0)::numeric  as total_importe_descuento_ley,
                                      COALESCE(sum(dcv.importe_pago_liquido),0)::numeric  as total_importe_pago_liquido,
                                      COALESCE(sum(dcv.importe_doc -  COALESCE(dcv.importe_descuento,0) - COALESCE(dcv.importe_excento,0)),0) as total_importe_aux_neto

                               from conta.tdoc_compra_venta dcv
                                  inner join segu.tusuario usu1 on usu1.id_usuario = dcv.id_usuario_reg
                                  inner join param.tplantilla pla on pla.id_plantilla = dcv.id_plantilla
                                  inner join param.tmoneda mon on mon.id_moneda = dcv.id_moneda
                                  inner join conta.ttipo_doc_compra_venta tdcv on tdcv.id_tipo_doc_compra_venta = dcv.id_tipo_doc_compra_venta
                                  left join conta.tauxiliar aux on aux.id_auxiliar = dcv.id_auxiliar
                                  left join conta.tint_comprobante ic on ic.id_int_comprobante = dcv.id_int_comprobante
                                  left join param.tdepto dep on dep.id_depto = dcv.id_depto_conta
                                  left join segu.tusuario usu2 on usu2.id_usuario = dcv.id_usuario_mod

                                  left join conta.tdoc_compra_venta_ext dcvext on dcvext.id_doc_compra_venta = dcv.id_doc_compra_venta
                                  left join param.tproveedor prov on prov.id_proveedor = dcv.id_proveedor

                                where ';

                    --Definicion de la respuesta
                    v_consulta:=v_consulta||v_parametros.filtro;
                    raise notice '%', v_consulta;
                    --Devuelve la respuesta
                    return v_consulta;

                end;

    /*********************************
 	#TRANSACCION:  'CONTA_DFACXFUN_SEL'
 	#DESCRIPCION:	Consulta de facturas registradas por funcionario
 	#AUTOR:		breydi vasquez
 	#FECHA:		07-05-2020
	***********************************/

	elsif(p_transaccion='CONTA_DFACXFUN_SEL')then

    	begin

    		--Sentencia de la consulta
			v_consulta:='select
                            dcv.id_doc_compra_venta,
                            dcv.revisado,
                            dcv.movil,
                            dcv.tipo,
                            COALESCE(dcv.importe_excento,0)::numeric as importe_excento,
                            dcv.id_plantilla,
                            dcv.fecha,
                            dcv.nro_documento,
                            dcv.nit,
                            COALESCE(dcv.importe_ice,0)::numeric as importe_ice,
                            dcv.nro_autorizacion,
                            COALESCE(dcv.importe_iva,0)::numeric as importe_iva,
                            COALESCE(dcv.importe_descuento,0)::numeric as importe_descuento,
                            COALESCE(dcv.importe_doc,0)::numeric as importe_doc,
                            dcv.sw_contabilizar,
                            COALESCE(dcv.tabla_origen,''ninguno'') as tabla_origen,
                            dcv.estado,
                            dcv.id_depto_conta,
                            dcv.id_origen,
                            dcv.obs,
                            dcv.estado_reg,
                            dcv.codigo_control,
                            COALESCE(dcv.importe_it,0)::numeric as importe_it,
                            dcv.razon_social,
                            dcv.id_usuario_ai,
                            dcv.id_usuario_reg,
                            dcv.fecha_reg,
                            dcv.usuario_ai,
                            dcv.id_usuario_mod,
                            dcv.fecha_mod,
                            usu1.cuenta as usr_reg,
                            usu2.cuenta as usr_mod,
                            dep.nombre as desc_depto,
                            pla.desc_plantilla,
                            COALESCE(dcv.importe_descuento_ley,0)::numeric as importe_descuento_ley,
                            COALESCE(dcv.importe_pago_liquido,0)::numeric as importe_pago_liquido,
                            dcv.nro_dui,
                            dcv.id_moneda,
                            mon.codigo as desc_moneda,
                            dcv.id_int_comprobante,
                            dcv.nro_tramite,
                            COALESCE(ic.nro_cbte,dcv.id_int_comprobante::varchar)::varchar  as desc_comprobante,
                            COALESCE(dcv.importe_pendiente,0)::numeric as importe_pendiente,
                            COALESCE(dcv.importe_anticipo,0)::numeric as importe_anticipo,
                            COALESCE(dcv.importe_retgar,0)::numeric as importe_retgar,
                            COALESCE(dcv.importe_neto,0)::numeric as importe_neto,
                            aux.id_auxiliar,
                            aux.codigo_auxiliar,
                            aux.nombre_auxiliar,
                            dcv.id_tipo_doc_compra_venta,
                            (tdcv.codigo||'' - ''||tdcv.nombre)::Varchar as desc_tipo_doc_compra_venta,
                            (dcv.importe_doc -  COALESCE(dcv.importe_descuento,0) - COALESCE(dcv.importe_excento,0))     as importe_aux_neto,
                            dcv.estacion,
                            dcv.id_punto_venta,
                            (ob.nombre ||'' - ''|| upper(ob.tipo_agencia))::Varchar as nombre,
                            dcv.id_agencia,
                            ob.codigo_noiata,
                            ob.codigo_int,
                            ic.c31,
                            dcv.fecha_vencimiento,
                            dcv.tipo_cambio

						from conta.tdoc_compra_venta dcv
                          inner join segu.tusuario usu1 on usu1.id_usuario = dcv.id_usuario_reg
                          inner join param.tplantilla pla on pla.id_plantilla = dcv.id_plantilla
                          inner join param.tmoneda mon on mon.id_moneda = dcv.id_moneda
                          inner join conta.ttipo_doc_compra_venta tdcv on tdcv.id_tipo_doc_compra_venta = dcv.id_tipo_doc_compra_venta
                          left join conta.tauxiliar aux on aux.id_auxiliar = dcv.id_auxiliar
                          left join conta.tint_comprobante ic on ic.id_int_comprobante = dcv.id_int_comprobante
                          left join obingresos.tagencia ob on ob.id_agencia = dcv.id_agencia
                          left join param.tdepto dep on dep.id_depto = dcv.id_depto_conta
                          left join segu.tusuario usu2 on usu2.id_usuario = dcv.id_usuario_mod
				        where  dcv.id_usuario_reg = '||p_id_usuario||' and ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

			--Devuelve la respuesta
        	raise notice '%', v_consulta;
			return v_consulta;

		end;

     /*********************************
 	#TRANSACCION:  'CONTA_DFACXFUN_CONT'
 	#DESCRIPCION:	Conteo de registros facturas registradas por funcionario
 	#AUTOR:		breydi vasquez
 	#FECHA:		07-05-2020
	***********************************/

	elsif(p_transaccion='CONTA_DFACXFUN_CONT')then

		begin
			--Sentencia de la consulta de conteo de registros
			v_consulta:='select
                              count(dcv.id_doc_compra_venta),
                              COALESCE(sum(dcv.importe_ice),0)::numeric  as total_importe_ice,
                              COALESCE(sum(dcv.importe_excento),0)::numeric  as total_importe_excento,
                              COALESCE(sum(dcv.importe_it),0)::numeric  as total_importe_it,
                              COALESCE(sum(dcv.importe_iva),0)::numeric  as total_importe_iva,
                              COALESCE(sum(dcv.importe_descuento),0)::numeric  as total_importe_descuento,
                              COALESCE(sum(dcv.importe_doc),0)::numeric  as total_importe_doc,
                              COALESCE(sum(dcv.importe_retgar),0)::numeric  as total_importe_retgar,
                              COALESCE(sum(dcv.importe_anticipo),0)::numeric  as total_importe_anticipo,
                              COALESCE(sum(dcv.importe_pendiente),0)::numeric  as tota_importe_pendiente,
                              COALESCE(sum(dcv.importe_neto),0)::numeric  as total_importe_neto,
                              COALESCE(sum(dcv.importe_descuento_ley),0)::numeric  as total_importe_descuento_ley,
                              COALESCE(sum(dcv.importe_pago_liquido),0)::numeric  as total_importe_pago_liquido,
                              COALESCE(sum(dcv.importe_doc -  COALESCE(dcv.importe_descuento,0) - COALESCE(dcv.importe_excento,0)),0) as total_importe_aux_neto

					  from conta.tdoc_compra_venta dcv
                          inner join segu.tusuario usu1 on usu1.id_usuario = dcv.id_usuario_reg
                          inner join param.tplantilla pla on pla.id_plantilla = dcv.id_plantilla
                          inner join param.tmoneda mon on mon.id_moneda = dcv.id_moneda
                          inner join conta.ttipo_doc_compra_venta tdcv on tdcv.id_tipo_doc_compra_venta = dcv.id_tipo_doc_compra_venta
                          left join conta.tauxiliar aux on aux.id_auxiliar = dcv.id_auxiliar
                          left join conta.tint_comprobante ic on ic.id_int_comprobante = dcv.id_int_comprobante
                          left join obingresos.tagencia ob on ob.id_agencia = dcv.id_agencia
                          left join param.tdepto dep on dep.id_depto = dcv.id_depto_conta
                          left join segu.tusuario usu2 on usu2.id_usuario = dcv.id_usuario_mod
				        where  dcv.id_usuario_reg = '||p_id_usuario||' and ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			--Devuelve la respuesta
			return v_consulta;

		end;

/*******************************
 #TRANSACCION:  CONTA_DEPFUNCON_SEL
 #DESCRIPCION:	Listado de departamento filtrados por los usuarios para registro facturas
 #AUTOR:		breydi vasquez
 #FECHA:		08-05-2020
***********************************/


     elsif(p_transaccion='CONTA_DEPFUNCON_SEL')then

          BEGIN
               v_consulta:='SELECT
                            DEPPTO.id_depto,
                            DEPPTO.codigo,
                            DEPPTO.nombre,
                            DEPPTO.nombre_corto,
                            DEPPTO.id_subsistema,
                            DEPPTO.estado_reg,
                            DEPPTO.fecha_reg,
                            DEPPTO.id_usuario_reg,
                            DEPPTO.fecha_mod,
                            DEPPTO.id_usuario_mod,
                            PERREG.nombre_completo1 as usureg,
                            PERMOD.nombre_completo1 as usumod,
                            SUBSIS.codigo||'' - ''||SUBSIS.nombre as desc_subsistema
                            FROM param.tdepto DEPPTO
                            INNER JOIN segu.tsubsistema SUBSIS on SUBSIS.id_subsistema=DEPPTO.id_subsistema
                            INNER JOIN segu.tusuario USUREG on USUREG.id_usuario=DEPPTO.id_usuario_reg
                            INNER JOIN segu.vpersona PERREG on PERREG.id_persona=USUREG.id_persona
                            LEFT JOIN segu.tusuario USUMOD on USUMOD.id_usuario=DEPPTO.id_usuario_mod
                            LEFT JOIN segu.vpersona PERMOD on PERMOD.id_persona=USUMOD.id_persona
                            WHERE DEPPTO.estado_reg =''activo'' and
                            SUBSIS.codigo = ''' ||v_parametros.codigo_subsistema||''' and ';

               v_consulta:=v_consulta||v_parametros.filtro;
               v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' OFFSET ' || v_parametros.puntero;
               raise notice '%',v_consulta;
               return v_consulta;


         END;

 /*******************************
 #TRANSACCION:  CONTA_DEPFUNCON_CONT
 #DESCRIPCION:	Conteo de la cantidad de departamentos por usuario para registro facturas
 #AUTOR:		breydi vasquez
 #FECHA:		08-05-2020
***********************************/

     elsif(p_transaccion='CONTA_DEPFUNCON_CONT')then
        BEGIN

               v_consulta:='SELECT
                                  count(DEPPTO.id_depto)
                            FROM param.tdepto DEPPTO
                            INNER JOIN segu.tsubsistema SUBSIS on SUBSIS.id_subsistema=DEPPTO.id_subsistema
                            INNER JOIN segu.tusuario USUREG on USUREG.id_usuario=DEPPTO.id_usuario_reg
                            INNER JOIN segu.vpersona PERREG on PERREG.id_persona=USUREG.id_persona
                            LEFT JOIN segu.tusuario USUMOD on USUMOD.id_usuario=DEPPTO.id_usuario_mod
                            LEFT JOIN segu.vpersona PERMOD on PERMOD.id_persona=USUMOD.id_persona
                            WHERE DEPPTO.estado_reg =''activo'' and
                            SUBSIS.codigo = ''' ||v_parametros.codigo_subsistema||''' and ';

               v_consulta:=v_consulta||v_parametros.filtro;
               return v_consulta;
         END;
    /*********************************
    #TRANSACCION:  'CONTA_LIBROCNCD_SEL'
    #DESCRIPCION:	Reporte Libro de Compras Notas de Credito-Debito
    #AUTOR:		franklin.espinoza
    #FECHA:		01-06-2020 10:10:09
    ***********************************/
    elsif(p_transaccion = 'CONTA_LIBROCNCD_SEL')then
    	begin

            if pxp.f_existe_parametro(p_tabla, 'filtro_sql') then
              if 'periodo' = v_parametros.filtro_sql then
                  select tper.fecha_ini, tper.fecha_fin
                  into v_registros
                  from param.tperiodo tper
                  where tper.id_periodo = v_parametros.id_periodo;
                  v_filtro = 'tnc.fecha between '''||v_registros.fecha_ini||'''::date and '''||v_registros.fecha_fin||'''::date';

                  v_id_periodo = v_parametros.id_periodo;

                  v_gestion = date_part('year', v_registros.fecha_ini);
            	  v_periodo = date_part('month', v_registros.fecha_ini);
              elsif 'fechas' = v_parametros.filtro_sql then
                  v_filtro = 'tnc.fecha between '''||v_parametros.fecha_ini||'''::date and '''||v_parametros.fecha_fin||'''::date';

                  v_gestion = date_part('year', v_parametros.fecha_ini);
            	  v_periodo = date_part('month', v_parametros.fecha_ini);

                  select tg.id_gestion
                  into v_id_gestion
                  from param.tgestion tg
                  where tg.gestion = v_gestion;

                  select tper.id_periodo
                  into v_id_periodo
                  from param.tperiodo tper
                  where tper.id_gestion = v_id_gestion and tper.periodo = v_periodo;

              end if;
            end if;
--raise 'v_id_gestion: %, v_id_periodo: %',v_id_gestion,v_id_periodo;

            select tem.nombre, tem.nit
            into v_registros
            from param.tempresa tem
            where tem.codigo = '578';

            --raise 'parametros: %', v_filtro;
            --Sentencia de la consulta de conteo de registros
            v_consulta:='select
              tnc.fecha::date as fecha_nota,
              tnc.nro_nota::varchar as num_nota,
              tnc.nroaut as num_autorizacion,
              (case when tnc.estado = ''1'' then ''V'' else ''A'' end)::varchar as estado,
              case when tnc.estado != ''1'' then ''0'' else tnc.nit end nit,
              (case when tnc.estado = ''1'' then tnc.razon else ''ANULADA'' end)::varchar as razon_social,
              tnc.total_devuelto::numeric,
              tnc.credfis::numeric as rc_iva,
              case when tnc.estado != ''1'' then ''0'' else tnc.codigo_control end codigo_control,
              case when tnc.estado != ''1'' then null else tnc.fecha_fac::date end fecha_original,
              case when tnc.estado != ''1'' then ''0'' else tnc.nrofac end num_factura,
              case when tnc.estado != ''1'' then ''0'' else tnc.nroaut_anterior end nroaut_anterior,
              (tnc.total_devuelto + tnc.excento)::numeric as importe_total,
              '||v_gestion||'::integer as gestion,
              param.f_literal_periodo('||v_id_periodo||') as periodo,
              '''||v_registros.nombre||'''::varchar as razon_empresa,
              '''||v_registros.nit||'''::varchar as nit_empresa,
              '''||v_periodo||'''::varchar as periodo_num
            from decr.tnota tnc
            where '||v_filtro;

			v_consulta = v_consulta||' order by tnc.fecha asc, tnc.nro_nota::integer asc ';
            raise notice '%', v_consulta;
            --Devuelve la respuesta
            return v_consulta;

        end;

	/*********************************
    #TRANSACCION:  'CONTA_LIBROVNCD_SEL'
    #DESCRIPCION:	Reporte Libro de Ventas Notas de Credito-Debito
    #AUTOR:		franklin.espinoza
    #FECHA:		20-1-2021 10:10:09
    ***********************************/
    elsif(p_transaccion = 'CONTA_LIBROVNCD_SEL')then
    	begin

            if pxp.f_existe_parametro(p_tabla, 'filtro_sql') then
              if 'periodo' = v_parametros.filtro_sql then
                  select tper.fecha_ini, tper.fecha_fin
                  into v_registros
                  from param.tperiodo tper
                  where tper.id_periodo = v_parametros.id_periodo;
                  v_filtro = 'tnc.fecha between '''||v_registros.fecha_ini||'''::date and '''||v_registros.fecha_fin||'''::date';

                  v_id_periodo = v_parametros.id_periodo;

                  v_gestion = date_part('year', v_registros.fecha_ini);
            	  v_periodo = date_part('month', v_registros.fecha_ini);
              elsif 'fechas' = v_parametros.filtro_sql then
                  v_filtro = 'tnc.fecha between '''||v_parametros.fecha_ini||'''::date and '''||v_parametros.fecha_fin||'''::date';

                  v_gestion = date_part('year', v_parametros.fecha_ini);
            	  v_periodo = date_part('month', v_parametros.fecha_ini);

                  select tg.id_gestion
                  into v_id_gestion
                  from param.tgestion tg
                  where tg.gestion = v_gestion;

                  select tper.id_periodo
                  into v_id_periodo
                  from param.tperiodo tper
                  where tper.id_gestion = v_id_gestion and tper.periodo = v_periodo;

              end if;
            end if;

            select tem.nombre, tem.nit
            into v_registros
            from param.tempresa tem
            where tem.codigo = '578';

            --Sentencia de la consulta de conteo de registros
            v_consulta:='select
              tnc.fecha::date as fecha_nota,
              tnc.nro_nota::varchar as num_nota,
              tnc.nroaut as num_autorizacion,
              (case when tnc.estado = ''1'' then ''V'' else ''A'' end)::varchar as estado,
              tnc.nit,
              (case when tnc.estado = ''1'' then tnc.razon else ''ANULADA'' end)::varchar as razon_social,

              tnc.total_devuelto::numeric,
              tnc.credfis::numeric as rc_iva,
              tnc.codigo_control,
              tnc.fecha_fac::date as fecha_original,
              tnc.nrofac as num_factura,
              tnc.billete,
              (tnc.total_devuelto + tnc.excento)::numeric as importe_total,
              '||v_gestion||'::integer as gestion,
              param.f_literal_periodo('||v_id_periodo||') as periodo,
              '''||v_registros.nombre||'''::varchar as razon_empresa,
              '''||v_registros.nit||'''::varchar as nit_empresa,
              '''||v_periodo||'''::varchar as periodo_num
            from decr.tnota_agencia tnc
            where '||v_filtro;

			v_consulta = v_consulta||' order by tnc.fecha asc, tnc.nro_nota::integer asc ';
            raise notice '%', v_consulta;
            --Devuelve la respuesta
            return v_consulta;

        end;

    /*********************************
 	#TRANSACCION:  'CONTA_R_LIB_VEN_SEL'
 	#DESCRIPCION:	listado para reporte de libro de ventas  desde formualrio
 	#AUTOR:		franklin.espinoza
 	#FECHA:		10-01-2021 15:57:09
	***********************************/

	ELSEIF(p_transaccion='CONTA_R_LIB_VEN_SEL')then

    	begin
			--raise 'llega';
			if v_parametros.filtro_sql = 'periodo' then

                SELECT gestion into v_gestion
               	FROM param.tgestion
               	WHERE id_gestion=v_parametros.id_gestion;

              	SELECT tp.fecha_ini, tp.fecha_fin
                into v_fecha_ini, v_fecha_fin
               	FROM param.tperiodo tp
               	WHERE tp.id_periodo = v_parametros.id_periodo;
            else
            	v_fecha_ini = v_parametros.fecha_ini;
                v_fecha_fin = v_parametros.fecha_fin;

                v_gestion =  date_part('year',v_fecha_ini);
            end if;

        	v_host     = pxp.f_get_variable_global('sincroniza_ip_facturacion');
          	v_puerto   = pxp.f_get_variable_global('sincroniza_puerto_facturacion');
          	v_dbname   = 'db_facturas_'||v_gestion;
          	p_user     = pxp.f_get_variable_global('sincronizar_user_facturacion');
          	v_password = pxp.f_get_variable_global('sincronizar_password_facturacion');

          	v_cadena_factura = 'hostaddr='||v_host||' port='||v_puerto||' dbname='||v_dbname||' user='||p_user||' password='||v_password;

            --raise notice 'v_cadena_factura: %, %, %',v_fecha_ini, v_fecha_fin, v_cadena_factura;
            --raise 'fin';
            v_conexion = (select dblink_connect('db_facturas',v_cadena_factura));

            v_boletos_filtro = 'nro_factura not in (
            										''''9302404259855'''', ''''9302404527013'''', ''''9302404527028'''', ''''9302404527029'''', ''''9302404527101'''',
                                                    ''''9302404527408'''', ''''9302404527409'''', ''''9302404527410'''', ''''9302404617007'''', ''''9302404617008'''',
                                                    ''''9302404527411'''', ''''9302404527458'''', ''''9302404527530'''', ''''9302404527535'''', ''''9302404527536'''',
                                                    ''''9302404527670'''', ''''9302404597893'''', ''''9302404597894'''', ''''9302404628354'''', ''''9303852514552'''',
                                                    ''''9302404597915'''', ''''9302404597916'''', ''''9302404597918'''', ''''9302404612749'''', ''''9302404617006'''',
                                                    ''''9302404534843'''', ''''9302404534845'''', ''''9302404534848'''', ''''9304550144895'''', ''''9304550144896'''',
                                                    ''''9302404533178'''', ''''9302404533179'''', ''''9302404533180'''', ''''9302404533181'''', ''''9302404533182'''',
                                                    ''''9302404533183'''', ''''9302404533184'''', ''''9302404533185'''', ''''9302404533186'''', ''''9302404533187'''',
                                                    ''''9302404533188'''', ''''9302404533189'''', ''''9302404533190'''', ''''9302404533191'''', ''''9302404533192'''',
                                                    ''''9302404533193'''', ''''9302404533194'''', ''''9304550144897'''', ''''9302404551977'''', ''''9302404551978'''',
                                                    ''''9302404551979'''', ''''9302404591149'''', ''''9302404527408'''', ''''9302404527409'''', ''''9302404527410'''',
                                                    ''''9302404527411'''', ''''9307592617064'''', ''''9307592617065'''', ''''9302404527458'''', ''''9302404591528'''',
													''''9302404591529'''', ''''9302404527530'''', ''''9302404527535'''', ''''9302404527536'''', ''''9307592763246'''',
													''''9307592763247'''', ''''9307592763248'''', ''''9307592763249'''', ''''9307592763250'''', ''''9302404597918'''',
                                                    ''''9302404244964'''', ''''9302404244965'''', ''''9302404244966'''', ''''9302404244967'''', ''''9302404244968'''',
                                                    ''''9302404244969'''', ''''9302404617006'''', ''''9302404617007'''', ''''9302404617008'''', ''''9302404597893'''',
                                                    ''''9302404597894'''', ''''9302404597915'''', ''''9302404597916'''', ''''9302404628354'''', ''''9302404614748'''',
                                                    ''''9302404527670'''', ''''9302404641101'''', ''''9302404641102'''')';

           	--Sentencia de la consulta
		  	v_consulta = 'select id_factura,
                                fecha_factura,
                                trim(nro_factura) nro_factura,
                                case when nro_autorizacion is null or trim(nro_autorizacion) = '''''''' then ''''0'''' else trim(nro_autorizacion) end nro_autorizacion,
                                (case when trim(estado) = ''''ANULADA'''' then ''''A''''
                                	  when trim(estado) = ''''VIGENTE'''' or trim(estado) = ''''VÁLIDA''''  then ''''V''''
                                      when trim(estado) = ''''EXTRAVIADA'''' then ''''E''''
                                      when trim(estado) = ''''NO UTILIZADA'''' then ''''N''''
                                      when trim(estado) = ''''CONTINGENCIA'''' and (coalesce(importe_total_venta,0.00::numeric))::numeric = 0 then ''''A''''
                                      when trim(estado) = ''''CONTINGENCIA'''' then ''''C''''
                                      when trim(estado) = ''''LIBRE CONSIGNACION'''' then ''''L''''
                                      else trim(estado) end)::varchar estado,
                                case when length( nit_ci_cli) > 13 then substr(nit_ci_cli,1,13) else trim(nit_ci_cli) end nit_ci_cli,
                                trim(razon_social_cli) razon_social_cli,

                                (coalesce(importe_total_venta,0.00::numeric))::numeric importe_total_venta,
                                (coalesce(importe_otros_no_suj_iva,0.00::numeric))::numeric importe_otros_no_suj_iva,
                                (coalesce(exportacion_excentas,0.00::numeric))::numeric exportacion_excentas,
                                (coalesce(ventas_tasa_cero,0.00::numeric))::numeric ventas_tasa_cero,
                                (coalesce(descuento_rebaja_suj_iva,0.00::numeric))::numeric descuento_rebaja_suj_iva,
                                (coalesce(importe_debito_fiscal,0.00::numeric))::numeric importe_debito_fiscal,

                                case when (coalesce(codigo_control,''''0''''))::varchar = ''''NULL''''  then ''''0'''' else (coalesce(codigo_control,''''0''''))::varchar end  codigo_control,
                                (coalesce(tipo_factura,''''''''))::varchar tipo_factura,
                                id_origen,
                                sistema_origen
                        from sfe.tfactura tfa
                        where '||v_boletos_filtro||' and tfa.fecha_factura between '''''||v_fecha_ini||'''''::date and '''''||v_fecha_fin||'''''::date and tfa.estado_reg = ''''activo''''
                        order by tfa.fecha_factura asc --limit 100';


                if v_conexion != 'OK' then
                      raise exception 'ERROR DE CONEXION A LA BASE DE DATOS CON DBLINK';
                else

                  --perform dblink_exec(v_cadena_factura,v_consulta,TRUE);

                  v_consulta = 'select  fac.id_factura,
                   						fac.fecha_factura,

                                        coalesce(fac.nro_factura,''''::varchar) nro_factura,

                                        (case when fac.tipo_factura = ''manual'' and fac.sistema_origen = ''ERP'' and fac.id_factura not in (''1201'',
''1211'',
''1213'',
''1215'',
''1216'',
''1217'',
''1224'',
''1226'',
''1229'',
''1230'',
''1232'',
''1233'',
''1234'',
''1235'',
''1236'',
''1237'',
''1238'',
''1241'',
''1242'',
''1243'',
''1244'',
''1245'',
''1246'',
''1247'',
''1248'',
''1249'',
''1250'',
''1254'',
''1255'',
''1256'',
''1257'',
''1259'',
''1260'',
''1659'',

''177536'',
''177537'',
''177526'',
''177527'',
''177534'',
''177535'') then
                                        (select tdos.nroaut from vef.tventa tve inner join vef.tdosificacion tdos on tdos.id_dosificacion = tve.id_dosificacion where tve.id_venta = fac.id_origen)::varchar
                                         when fac.tipo_factura = ''computarizada'' and fac.sistema_origen = ''ERP'' and fac.estado = ''A'' and fac.nro_autorizacion is null then
                                        (select tdos.nroaut from vef.tventa tve inner join vef.tdosificacion tdos on tdos.id_dosificacion = tve.id_dosificacion where tve.id_venta = fac.id_origen)::varchar
                                        else coalesce(fac.nro_autorizacion,''''::varchar) end ) nro_autorizacion,

                                        coalesce(fac.estado,''''::varchar) estado,
                                        coalesce(fac.nit_ci_cli,''''::varchar) nit_ci_cli,
                                        coalesce(fac.razon_social_cli,''''::varchar) razon_social_cli,

                                        /*(coalesce(importe_total_venta::numeric,0::numeric))::numeric*/ fac.importe_total_venta,
                                        /*(coalesce(importe_otros_no_suj_iva::numeric,0::numeric))::numeric*/ fac.importe_otros_no_suj_iva,
                                        /*(coalesce(exportacion_excentas::numeric,0::numeric))::numeric*/ fac.exportacion_excentas,
                                        /*(coalesce(ventas_tasa_cero::numeric,0::numeric))::numeric*/ fac.ventas_tasa_cero,
                                        /*(coalesce(descuento_rebaja_suj_iva::numeric,0::numeric))::numeric*/ fac.descuento_rebaja_suj_iva,
                                        /*(coalesce(importe_debito_fiscal::numeric,0::numeric))::numeric*/ fac.importe_debito_fiscal,

                                        (case when fac.codigo_control is null or fac.codigo_control = '''' then ''0''
                                        else fac.codigo_control end ) codigo_control,
                                        fac.tipo_factura,
                                        fac.id_origen,
                                        fac.sistema_origen

                                 from dblink(''' || v_cadena_factura || ''', '''|| v_consulta ||''') as
                            		fac(
                            			id_factura integer,
                                        fecha_factura date,
                                        nro_factura varchar,
                                        nro_autorizacion varchar,
                                        estado varchar,
                                        nit_ci_cli varchar,
                                        razon_social_cli varchar,

                                        importe_total_venta numeric,
                                        importe_otros_no_suj_iva numeric,
                                        exportacion_excentas numeric,
                                        ventas_tasa_cero numeric,
                                        descuento_rebaja_suj_iva numeric,
                                        importe_debito_fiscal numeric,

                                        codigo_control varchar,
                                        tipo_factura varchar,
                                        id_origen integer,
                                        sistema_origen varchar
                                    ) where case when (('||date_part('month',v_fecha_ini)||'=1 and '||date_part('year',v_fecha_ini)||'=2021) or ('||date_part('month',v_fecha_fin)||'=1 and '||date_part('year',v_fecha_fin)||'=2021)) then 0=0 else fac.nro_factura not in (
                                    select nro_boleto from vef.tboletos_asociados_fact tba
                                    inner join vef.tventa tv on tv.id_venta = tba.id_venta
                                    where tv.estado = ''finalizado'' and tv.tipo_factura in (''manual'',''computarizada'') and tv.estado_reg = ''activo'' and tba.estado_reg = ''activo'') end
                                    order by fecha_factura asc, nro_factura asc
                            ';

                  v_conexion = (select dblink_disconnect('db_facturas'));

                end if;

			raise notice '%', v_consulta;
			--Devuelve la respuesta
			return v_consulta;

		end;

    /*********************************
 	#TRANSACCION:  'CONTA_GET_DOC_VEN_SEL'
 	#DESCRIPCION:	listado para reporte de libro de ventas  desde formualrio
 	#AUTOR:		franklin.espinoza
 	#FECHA:		10-01-2021 15:57:09
	***********************************/

	ELSEIF(p_transaccion='CONTA_GET_VENTA_SEL')then

    	begin

            v_fecha_ini = v_parametros.fecha_desde;
            v_fecha_fin = v_parametros.fecha_hasta;
            v_gestion =  date_part('year',v_fecha_ini);

        	v_host     = pxp.f_get_variable_global('sincroniza_ip_facturacion');
          	v_puerto   = pxp.f_get_variable_global('sincroniza_puerto_facturacion');
          	v_dbname   = 'db_facturas_'||v_gestion;
          	p_user     = pxp.f_get_variable_global('sincronizar_user_facturacion');
          	v_password = pxp.f_get_variable_global('sincronizar_password_facturacion');

          	v_cadena_factura = 'hostaddr='||v_host||' port='||v_puerto||' dbname='||v_dbname||' user='||p_user||' password='||v_password;

            v_conexion = (select dblink_connect('db_facturas',v_cadena_factura));



           	--Sentencia de la consulta
		  	v_consulta = 'select id_factura,
                                fecha_factura,
                                nro_factura,
                                nro_autorizacion,
                                estado,
                                nit_ci_cli,
                                razon_social_cli,
                                importe_total_venta,
                                importe_otros_no_suj_iva,
                                exportacion_excentas,
                                ventas_tasa_cero,
                                descuento_rebaja_suj_iva,
                                importe_debito_fiscal,
                                codigo_control,
                                tipo_factura,
                                id_origen,
                                sistema_origen,
                                desc_ruta,
                                revision_nit,
                                otr
                        from sfe.tfactura tfa
                        where tfa.fecha_factura between '''''||v_fecha_ini||'''''::date and '''''||v_fecha_fin||'''''::date and case when '''''||v_parametros.tipo_show||''''' = ''''CORRECTO'''' then tfa.revision_nit in (''''CORREGIDO'''',''''CORRECTO'''') else tfa.revision_nit = '''''||v_parametros.tipo_show||''''' end and ';
            --raise 'cantidad: %, offset: %', v_parametros.cantidad, v_parametros.puntero;
            v_parametros.filtro = regexp_replace(v_parametros.filtro, '''', '''''', 'g');
            v_consulta = v_consulta||v_parametros.filtro;
			v_consulta = v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

                if v_conexion != 'OK' then
                      raise exception 'ERROR DE CONEXION A LA BASE DE DATOS CON DBLINK';
                else

                  --perform dblink_exec(v_cadena_factura,v_consulta,TRUE);

                  v_consulta = 'select  id_factura,
                   						fecha_factura,
                                        nro_factura,
                                        nro_autorizacion,
                                        estado,
                                        nit_ci_cli,
                                        razon_social_cli,
                                        coalesce(importe_total_venta,0) importe_total_venta,
                                        coalesce(importe_otros_no_suj_iva,0) importe_otros_no_suj_iva,
                                        coalesce(exportacion_excentas,0) exportacion_excentas,
                                        coalesce(ventas_tasa_cero,0) ventas_tasa_cero,
                                        coalesce(descuento_rebaja_suj_iva,0) descuento_rebaja_suj_iva,
                                        coalesce(importe_debito_fiscal,0) importe_debito_fiscal,
                                        coalesce(codigo_control,''''::varchar) codigo_control,
                                        tipo_factura,
                                        id_origen,
                                        sistema_origen,
                                        desc_ruta,
                                		revision_nit,
                                        otr

                                 from dblink(''' || v_cadena_factura || ''', '''|| v_consulta ||''') as
                            		fac(
                            			id_factura integer,
                                        fecha_factura date,
                                        nro_factura varchar,
                                        nro_autorizacion varchar,
                                        estado varchar,
                                        nit_ci_cli varchar,
                                        razon_social_cli varchar,
                                        importe_total_venta numeric,
                                        importe_otros_no_suj_iva numeric,
                                        exportacion_excentas numeric,
                                        ventas_tasa_cero numeric,
                                        descuento_rebaja_suj_iva numeric,
                                        importe_debito_fiscal numeric,
                                        codigo_control varchar,
                                        tipo_factura varchar,
                                        id_origen integer,
                                        sistema_origen varchar,
                                        desc_ruta varchar,
                                		revision_nit varchar,
                                        otr varchar
                                    )
                            ';

                  v_conexion = (select dblink_disconnect('db_facturas'));

                end if;

			--v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;
			--Devuelve la respuesta
			return v_consulta;

		end;

	/*********************************
 	#TRANSACCION:  'CONTA_GET_VENTA_CONT'
 	#DESCRIPCION:	listado para reporte de libro de ventas  desde formualrio
 	#AUTOR:		franklin.espinoza
 	#FECHA:		10-01-2021 15:57:09
	***********************************/

	ELSEIF(p_transaccion='CONTA_GET_VENTA_CONT')then

    	begin
        	--raise 'v_parametros: %', v_parametros;
            v_fecha_ini = v_parametros.fecha_desde;
            v_fecha_fin = v_parametros.fecha_hasta;
            v_gestion =  date_part('year',v_fecha_ini);

        	v_host     = pxp.f_get_variable_global('sincroniza_ip_facturacion');
          	v_puerto   = pxp.f_get_variable_global('sincroniza_puerto_facturacion');
          	v_dbname   = 'db_facturas_'||v_gestion;
          	p_user     = pxp.f_get_variable_global('sincronizar_user_facturacion');
          	v_password = pxp.f_get_variable_global('sincronizar_password_facturacion');

          	v_cadena_factura = 'hostaddr='||v_host||' port='||v_puerto||' dbname='||v_dbname||' user='||p_user||' password='||v_password;

            v_conexion = (select dblink_connect('db_facturas',v_cadena_factura));

           	--Sentencia de la consulta
		  	v_consulta = 'select count(id_factura) as contador
                        from sfe.tfactura tfa
                        where tfa.fecha_factura between '''''||v_fecha_ini||'''''::date and '''''||v_fecha_fin||'''''::date and case when '''''||v_parametros.tipo_show||''''' = ''''CORRECTO'''' then tfa.revision_nit in (''''CORREGIDO'''',''''CORRECTO'''') else tfa.revision_nit = '''''||v_parametros.tipo_show||''''' end and  ';--order by tfa.fecha_factura asc

            v_parametros.filtro = regexp_replace(v_parametros.filtro, '''', '''''', 'g');
            v_consulta = v_consulta||v_parametros.filtro;

                if v_conexion != 'OK' then
                      raise exception 'ERROR DE CONEXION A LA BASE DE DATOS CON DBLINK';
                else

                  --perform dblink_exec(v_cadena_factura,v_consulta,TRUE);

                  v_consulta = 'select  contador

                                 from dblink(''' || v_cadena_factura || ''', '''|| v_consulta ||''') as
                            		fac(
                            			contador bigint
                                    )
                            ';

                  v_conexion = (select dblink_disconnect('db_facturas'));

                end if;

			raise notice '%', v_consulta;
			--Devuelve la respuesta
			return v_consulta;

		end;

    /*********************************
 	#TRANSACCION:  'CONTA_CORRECION_SEL'
 	#DESCRIPCION:	listado para reporte de libro de ventas  desde formualrio
 	#AUTOR:		franklin.espinoza
 	#FECHA:		10-01-2021 15:57:09
	***********************************/

	ELSEIF(p_transaccion='CONTA_CORRECION_SEL')then

    	begin



           	--Sentencia de la consulta
		  	v_consulta = 'select tcd.id_correcion_doc,
                                tcd.id_factura,
                                tcd.nit_ci_cli,
                                tcd.razon_social_cli,
                                tcd.fecha_reg,
                                tcd.id_usuario_reg,
                                vf.desc_funcionario2::varchar as usr_reg,
                                tcd.estado_reg

                        from conta.tcorrecion_doc tcd

                        inner join segu.tusuario usu1 on usu1.id_usuario = tcd.id_usuario_reg
                        inner join orga.vfuncionario vf on vf.id_persona = usu1.id_persona

                        where tcd.id_factura = '||v_parametros.id_factura;


			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;
			raise notice 'v_consulta: %',v_consulta;
            --Devuelve la respuesta
			return v_consulta;

		end;

    /*********************************
 	#TRANSACCION:  'CONTA_CORRECION_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		admin
 	#FECHA:		12-08-2016 14:29:16
	***********************************/

	elsif(p_transaccion='CONTA_CORRECION_CONT')then

		begin
			--Sentencia de la consulta de conteo de registros
			v_consulta = 'select count(id_correcion_doc)

                        from conta.tcorrecion_doc tcd

                        inner join segu.tusuario usu1 on usu1.id_usuario = tcd.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = tcd.id_usuario_mod

                        where tcd.id_factura = '||v_parametros.id_factura;

			--Definicion de la respuesta
			--v_consulta:=v_consulta||v_parametros.filtro;

			--Devuelve la respuesta
			return v_consulta;

		end;

    /*********************************
    #TRANSACCION:  'CONTA_IATAREP_SEL'
    #DESCRIPCION:	Reporte iata boletos
    #AUTOR:		breydi.vasquez
    #FECHA:		03-02-2021
    ***********************************/
    elsif(p_transaccion = 'CONTA_IATAREP_SEL')then
    	begin

        	v_filtro = ' 0=0 ';

              IF 'periodo' = v_parametros.filtro_sql THEN

                  select tper.fecha_ini, tper.fecha_fin
                  into v_registros
                  from param.tperiodo tper
                  where tper.id_periodo = v_parametros.id_periodo;
                  v_filtro = ' fecha_factura between '''||v_registros.fecha_ini||'''::date and '''||v_registros.fecha_fin::date||'''::date';

                  v_gestion_ini = date_part('year', v_registros.fecha_ini);
                  v_gestion_fin = date_part('year', v_registros.fecha_fin);

                  v_fecha_ini = v_registros.fecha_ini;

              ELSIF 'fechas' = v_parametros.filtro_sql THEN

                  v_filtro = ' fecha_factura between '''||v_parametros.fecha_ini||'''::date and '''||v_parametros.fecha_fin::date||'''::date';

                  v_gestion_ini = date_part('year', v_parametros.fecha_ini::date);
                  v_gestion_fin = date_part('year', v_parametros.fecha_fin::date);

                  v_fecha_ini = v_parametros.fecha_ini;

              END IF;


            IF (v_gestion_ini != v_gestion_fin) then
              raise exception 'Solo se puede recuperar información de la misma Gestión favor verifique los datos.';
            END if;



             --raise 'parametros: %', v_filtro;
          create temp table tfactura_temp_iata (presentacion			varchar,
                                                tipo_transaccion		varchar,
                                                nro_factura 			varchar(13),
                                                origen_servicio 		varchar(3),
                                                fecha_transaccion 		varchar,
                                                nit_linea_aerea 		varchar(14),
                                                nombre_pasajero			text,
                                                t_iva					numeric(18,2),
                                                moneda					varchar(3),
                                                nit_ci_beneficiario		varchar,
                                                venta_propia      varchar(50),
                                                id_origen         integer

        	)on commit drop;

        	v_host     = pxp.f_get_variable_global('sincroniza_ip_facturacion');
          	v_puerto   = pxp.f_get_variable_global('sincroniza_puerto_facturacion');
          	v_dbname   = 'db_facturas_'||v_gestion_ini;
          	p_user     = pxp.f_get_variable_global('sincronizar_user_facturacion');
          	v_password = pxp.f_get_variable_global('sincronizar_password_facturacion');

          	v_cadena_factura = 'hostaddr='||v_host||' port='||v_puerto||' dbname='||v_dbname||' user='||p_user||' password='||v_password;

            v_conexion = (select dblink_connect('db_facturas',v_cadena_factura));

            IF v_conexion != 'OK' then

                      raise exception 'ERROR DE CONEXION A LA BASE DE DATOS CON DBLINK';
            ELSE

			insert into tfactura_temp_iata
            SELECT *
                            FROM dblink(v_cadena_factura,
                                        'SELECT ''0''::varchar as presentacion,
                                        		''1''::varchar as tipo_transaccion,
                                                nro_factura,
                                                origen_servicio,
                                                to_char(fecha_factura,''DD/MM/YYYY''),
                                                '||v_parametros.nit_linea_aerea||',
                                                nombre_pasajero,
                                                (coalesce(importe_total_venta, 0) - coalesce(importe_otros_no_suj_iva, 0)),
                                                ''BOB''::varchar,
                                                case when length( nit_ci_cli) > 13 then substr(nit_ci_cli,1,13) else trim(nit_ci_cli) end nit_ci_cli,
                                                case when tipo_venta in (''IATA'') and pais_emision = ''BO'' then
                                                  ''no_propia''
                                                else
                                                  ''propia''
                                                end venta_propia,
                                                id_origen
                                          FROM sfe.tfactura
                                          WHERE  estado_reg = ''activo''
                                          AND  sistema_origen = ''STAGE DB''
                                          AND  estado = ''VIGENTE''
                                          AND tipo_factura in (''TKTT'', ''EMDS'')
                                         AND '||v_filtro||'
                                         order by fecha_factura ASC, nro_factura ASC
                                         ')
                            AS t1(presentacion			varchar,
                            	  tipo_transaccion		varchar,
                            	  nro_factura 			varchar(13),
                                  origen_servicio 		varchar(3),
                                  fecha_transaccion 	varchar,
                                  nit_linea_aerea 		varchar(14),
                                  nombre_pasajero		text,
                                  t_iva					numeric(18,2),
                                  moneda				varchar(3),
                                  nit_ci_beneficiario	varchar,
                                  venta_propia      varchar,
                                  id_origen         integer);

            v_conexion = (select dblink_disconnect('db_facturas'));

          	END IF;

            IF v_fecha_ini < '2021-02-01' THEN
                v_consulta:='
                            SELECT TO_JSON(ROW_TO_JSON(jsonD) :: TEXT) #>> ''{}'' AS jsonData
                            FROM (
                                   SELECT
                                     (
                                       SELECT ARRAY_TO_JSON(ARRAY_AGG(ROW_TO_JSON(t_iata))) as data
                                       FROM
                                         ( SELECT * FROM tfactura_temp_iata
                                                    where venta_propia = ''propia''
                                         ) t_iata
                                     )
                                 ) jsonD';
             ELSE

             v_consulta:='
                         SELECT TO_JSON(ROW_TO_JSON(jsonD) :: TEXT) #>> ''{}'' AS jsonData
                         FROM (
                                SELECT
                                  (
                                    SELECT ARRAY_TO_JSON(ARRAY_AGG(ROW_TO_JSON(t_iata))) as data
                                    FROM
                                      ( SELECT * FROM tfactura_temp_iata
                                         where venta_propia = ''propia''

                                         and nro_factura not in (select  bf.nro_boleto from vef.tboletos_asociados_fact bf
                                           inner join vef.tventa v on v.id_venta = bf.id_venta
                                           where v.estado = ''finalizado'' and v.tipo_factura in (''manual'', ''computarizada'')
                                           and bf.estado_reg = ''activo'')
                                      ) t_iata
                                  )
                              ) jsonD';
               END IF;

			--Devuelve la respuesta
			return v_consulta;

        end;
     /*********************************
 	#TRANSACCION:  'CONTA_DOC_GEN_SEL'
 	#DESCRIPCION:	listado de documentos generados
 	#AUTOR:		franklin.espinoza
 	#FECHA:		10-01-2021 15:57:09
	***********************************/

	ELSEIF(p_transaccion='CONTA_DOC_GEN_SEL')then

    	begin

           	--Sentencia de la consulta
		  	v_consulta = 'select tcd.id_documento_generado,
                                tcd.url url_documento,
                                tcd.file_name nombre_documento,
                                tcd.size,
                                tcd.estado_reg,
                                tcd.fecha_reg,
                                tcd.fecha_generacion,
                                vf.desc_funcionario2::varchar as usr_reg,
                                tcd.fecha_ini,
                                tcd.fecha_fin,
                                tcd.format formato

                        from conta.tdocumento_generado tcd

                        inner join segu.tusuario usu1 on usu1.id_usuario = tcd.id_usuario_reg
                        inner join orga.vfuncionario vf on vf.id_persona = usu1.id_persona

                        where tcd.id_usuario_reg = '||p_id_usuario||' and tcd.estado_reg != ''inactivo'' and tcd.fecha_generacion::date = current_date and '||v_parametros.filtro;


			v_consulta = v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;
			raise notice 'v_consulta: %',v_consulta;
            --Devuelve la respuesta
			return v_consulta;

		end;

    /*********************************
 	#TRANSACCION:  'CONTA_DOC_GEN_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		admin
 	#FECHA:		12-08-2016 14:29:16
	***********************************/

	elsif(p_transaccion='CONTA_DOC_GEN_CONT')then

		begin
			--Sentencia de la consulta de conteo de registros
			v_consulta = 'select count(id_documento_generado)

                        from conta.tdocumento_generado tcd

                        inner join segu.tusuario usu1 on usu1.id_usuario = tcd.id_usuario_reg
                        inner join orga.vfuncionario vf on vf.id_persona = usu1.id_persona

                        where tcd.id_usuario_reg = '||p_id_usuario||' and tcd.estado_reg != ''inactivo'' and tcd.fecha_generacion::date = current_date and '||v_parametros.filtro;


			--Devuelve la respuesta
			return v_consulta;

		end;

    /*********************************
 	#TRANSACCION:  'CONTA_G_FAC_TIPO_SEL'
 	#DESCRIPCION:	listado facturas de libro de ventas
 	#AUTOR:		franklin.espinoza
 	#FECHA:		10-01-2021 15:57:09
	***********************************/

	ELSEIF(p_transaccion='CONTA_G_FAC_TIP_SEL')then

    	begin

            v_fecha_ini = v_parametros.fecha_desde;
            v_fecha_fin = v_parametros.fecha_hasta;
            v_gestion =  date_part('year',v_fecha_ini);

        	v_host     = pxp.f_get_variable_global('sincroniza_ip_facturacion');
          	v_puerto   = pxp.f_get_variable_global('sincroniza_puerto_facturacion');
          	v_dbname   = 'db_facturas_'||v_gestion;
          	p_user     = pxp.f_get_variable_global('sincronizar_user_facturacion');
          	v_password = pxp.f_get_variable_global('sincronizar_password_facturacion');

          	v_cadena_factura = 'hostaddr='||v_host||' port='||v_puerto||' dbname='||v_dbname||' user='||p_user||' password='||v_password;

            v_conexion = (select dblink_connect('db_facturas',v_cadena_factura));

			v_boletos_filtro = 'nro_factura not in (
            										''''9302404259855'''', ''''9302404527013'''', ''''9302404527028'''', ''''9302404527029'''', ''''9302404527101'''',
                                                    ''''9302404527408'''', ''''9302404527409'''', ''''9302404527410'''', ''''9302404617007'''', ''''9302404617008'''',
                                                    ''''9302404527411'''', ''''9302404527458'''', ''''9302404527530'''', ''''9302404527535'''', ''''9302404527536'''',
                                                    ''''9302404527670'''', ''''9302404597893'''', ''''9302404597894'''', ''''9302404628354'''', ''''9303852514552'''',
                                                    ''''9302404597915'''', ''''9302404597916'''', ''''9302404597918'''', ''''9302404612749'''', ''''9302404617006'''',
                                                    ''''9302404534843'''', ''''9302404534845'''', ''''9302404534848'''', ''''9304550144895'''', ''''9304550144896'''',
                                                    ''''9302404533178'''', ''''9302404533179'''', ''''9302404533180'''', ''''9302404533181'''', ''''9302404533182'''',
                                                    ''''9302404533183'''', ''''9302404533184'''', ''''9302404533185'''', ''''9302404533186'''', ''''9302404533187'''',
                                                    ''''9302404533188'''', ''''9302404533189'''', ''''9302404533190'''', ''''9302404533191'''', ''''9302404533192'''',
                                                    ''''9302404533193'''', ''''9302404533194'''', ''''9304550144897'''', ''''9302404551977'''', ''''9302404551978'''',
                                                    ''''9302404551979'''', ''''9302404591149'''', ''''9302404527408'''', ''''9302404527409'''', ''''9302404527410'''',
                                                    ''''9302404527411'''', ''''9307592617064'''', ''''9307592617065'''', ''''9302404527458'''', ''''9302404591528'''',
													''''9302404591529'''', ''''9302404527530'''', ''''9302404527535'''', ''''9302404527536'''', ''''9307592763246'''',
													''''9307592763247'''', ''''9307592763248'''', ''''9307592763249'''', ''''9307592763250'''', ''''9302404597918'''',
                                                    ''''9302404244964'''', ''''9302404244965'''', ''''9302404244966'''', ''''9302404244967'''', ''''9302404244968'''',
                                                    ''''9302404244969'''', ''''9302404617006'''', ''''9302404617007'''', ''''9302404617008'''', ''''9302404597893'''',
                                                    ''''9302404597894'''', ''''9302404597915'''', ''''9302404597916'''', ''''9302404628354'''', ''''9302404614748'''',
                                                    ''''9302404527670'''', ''''9302404641101'''', ''''9302404641102''''
                                                    )';

           	--Sentencia de la consulta
		  	v_consulta = 'select id_factura,
                                fecha_factura,
                                nro_factura,
                                nro_autorizacion,
                                estado,
                                nit_ci_cli,
                                razon_social_cli,
                                importe_total_venta,
                                importe_otros_no_suj_iva,
                                exportacion_excentas,
                                ventas_tasa_cero,
                                descuento_rebaja_suj_iva,
                                importe_debito_fiscal,
                                codigo_control,
                                tipo_factura,
                                id_origen,
                                sistema_origen,
                                desc_ruta,
                                revision_nit,
                                otr
                        from sfe.tfactura tfa
                        where '||v_boletos_filtro||' and tfa.fecha_factura between '''''||v_fecha_ini||'''''::date and '''''||v_fecha_fin||'''''::date and tfa.estado_reg = ''''activo'''' and ';--order by tfa.fecha_factura asc
            --raise 'cantidad: %, offset: %', v_parametros.cantidad, v_parametros.puntero;
            v_parametros.filtro = regexp_replace(v_parametros.filtro, '''', '''''', 'g');
            v_consulta = v_consulta||v_parametros.filtro;
			v_consulta = v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;
			--raise notice 'v_consulta: %',v_consulta;
                if v_conexion != 'OK' then
                      raise exception 'ERROR DE CONEXION A LA BASE DE DATOS CON DBLINK';
                else


                  --perform dblink_exec(v_cadena_factura,v_consulta,TRUE);

                  v_consulta = 'select  id_factura,
                   						fecha_factura,
                                        nro_factura,
                                        --nro_autorizacion,

                                        (case when tipo_factura = ''manual'' and sistema_origen = ''ERP'' then
                                        (select tdos.nroaut from vef.tventa tve inner join vef.tdosificacion tdos on tdos.id_dosificacion = tve.id_dosificacion where tve.id_venta = id_origen)::varchar
                                         when tipo_factura = ''computarizada'' and sistema_origen = ''ERP'' and estado = ''ANULADA'' and nro_autorizacion is null then
                                        (select tdos.nroaut from vef.tventa tve inner join vef.tdosificacion tdos on tdos.id_dosificacion = tve.id_dosificacion where tve.id_venta = id_origen)::varchar
                                        else coalesce(nro_autorizacion,''0''::varchar) end ) nro_autorizacion,

                                        estado,
                                        nit_ci_cli,
                                        razon_social_cli,
                                        coalesce(importe_total_venta,0) importe_total_venta,
                                        coalesce(importe_otros_no_suj_iva,0) importe_otros_no_suj_iva,
                                        coalesce(exportacion_excentas,0) exportacion_excentas,
                                        coalesce(ventas_tasa_cero,0) ventas_tasa_cero,
                                        coalesce(descuento_rebaja_suj_iva,0) descuento_rebaja_suj_iva,
                                        coalesce(importe_debito_fiscal,0) importe_debito_fiscal,
                                        coalesce(codigo_control,''0''::varchar) codigo_control,
                                        tipo_factura,
                                        id_origen,
                                        sistema_origen,
                                        desc_ruta,
                                		revision_nit,
                                        otr

                                 from dblink(''' || v_cadena_factura || ''', '''|| v_consulta ||''') as
                            		fac(
                            			id_factura integer,
                                        fecha_factura date,
                                        nro_factura varchar,
                                        nro_autorizacion varchar,
                                        estado varchar,
                                        nit_ci_cli varchar,
                                        razon_social_cli varchar,
                                        importe_total_venta numeric,
                                        importe_otros_no_suj_iva numeric,
                                        exportacion_excentas numeric,
                                        ventas_tasa_cero numeric,
                                        descuento_rebaja_suj_iva numeric,
                                        importe_debito_fiscal numeric,
                                        codigo_control varchar,
                                        tipo_factura varchar,
                                        id_origen integer,
                                        sistema_origen varchar,
                                        desc_ruta varchar,
                                		revision_nit varchar,
                                        otr varchar
                                    ) order by fecha_factura asc, nro_factura asc
                            ';

                  v_conexion = (select dblink_disconnect('db_facturas'));

                end if;

			--v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;
			--Devuelve la respuesta
			return v_consulta;

		end;

	/*********************************
 	#TRANSACCION:  'CONTA_G_FAC_TIP_CONT'
 	#DESCRIPCION:	listado para reporte de libro de ventas  desde formualrio
 	#AUTOR:		franklin.espinoza
 	#FECHA:		10-01-2021 15:57:09
	***********************************/

	ELSEIF(p_transaccion='CONTA_G_FAC_TIP_CONT')then

    	begin
        	--raise 'v_parametros: %', v_parametros;
            v_fecha_ini = v_parametros.fecha_desde;
            v_fecha_fin = v_parametros.fecha_hasta;
            v_gestion =  date_part('year',v_fecha_ini);

        	v_host     = pxp.f_get_variable_global('sincroniza_ip_facturacion');
          	v_puerto   = pxp.f_get_variable_global('sincroniza_puerto_facturacion');
          	v_dbname   = 'db_facturas_'||v_gestion;
          	p_user     = pxp.f_get_variable_global('sincronizar_user_facturacion');
          	v_password = pxp.f_get_variable_global('sincronizar_password_facturacion');

          	v_cadena_factura = 'hostaddr='||v_host||' port='||v_puerto||' dbname='||v_dbname||' user='||p_user||' password='||v_password;

            v_conexion = (select dblink_connect('db_facturas',v_cadena_factura));


            v_boletos_filtro = 'nro_factura not in (
            										''''9302404259855'''', ''''9302404527013'''', ''''9302404527028'''', ''''9302404527029'''', ''''9302404527101'''',
                                                    ''''9302404527408'''', ''''9302404527409'''', ''''9302404527410'''', ''''9302404617007'''', ''''9302404617008'''',
                                                    ''''9302404527411'''', ''''9302404527458'''', ''''9302404527530'''', ''''9302404527535'''', ''''9302404527536'''',
                                                    ''''9302404527670'''', ''''9302404597893'''', ''''9302404597894'''', ''''9302404628354'''', ''''9303852514552'''',
                                                    ''''9302404597915'''', ''''9302404597916'''', ''''9302404597918'''', ''''9302404612749'''', ''''9302404617006'''',
                                                    ''''9302404534843'''', ''''9302404534845'''', ''''9302404534848'''', ''''9304550144895'''', ''''9304550144896'''',
                                                    ''''9302404533178'''', ''''9302404533179'''', ''''9302404533180'''', ''''9302404533181'''', ''''9302404533182'''',
                                                    ''''9302404533183'''', ''''9302404533184'''', ''''9302404533185'''', ''''9302404533186'''', ''''9302404533187'''',
                                                    ''''9302404533188'''', ''''9302404533189'''', ''''9302404533190'''', ''''9302404533191'''', ''''9302404533192'''',
                                                    ''''9302404533193'''', ''''9302404533194'''', ''''9304550144897'''', ''''9302404551977'''', ''''9302404551978'''',
                                                    ''''9302404551979'''', ''''9302404591149'''', ''''9302404527408'''', ''''9302404527409'''', ''''9302404527410'''',
                                                    ''''9302404527411'''', ''''9307592617064'''', ''''9307592617065'''', ''''9302404527458'''', ''''9302404591528'''',
													''''9302404591529'''', ''''9302404527530'''', ''''9302404527535'''', ''''9302404527536'''', ''''9307592763246'''',
													''''9307592763247'''', ''''9307592763248'''', ''''9307592763249'''', ''''9307592763250'''', ''''9302404597918'''',
                                                    ''''9302404244964'''', ''''9302404244965'''', ''''9302404244966'''', ''''9302404244967'''', ''''9302404244968'''',
                                                    ''''9302404244969'''', ''''9302404617006'''', ''''9302404617007'''', ''''9302404617008'''', ''''9302404597893'''',
                                                    ''''9302404597894'''', ''''9302404597915'''', ''''9302404597916'''', ''''9302404628354'''', ''''9302404614748'''',
                                                    ''''9302404527670'''', ''''9302404641101'''', ''''9302404641102''''
                                                    )';

           	--Sentencia de la consulta
		  	v_consulta = 'select count(id_factura) as contador,

              			  sum(importe_total_venta) importe_total_venta,
                          sum(importe_otros_no_suj_iva) importe_otros_no_suj_iva,
                          sum(exportacion_excentas) exportacion_excentas,
                          sum(ventas_tasa_cero) ventas_tasa_cero,
                          sum(descuento_rebaja_suj_iva) descuento_rebaja_suj_iva,
                          sum(importe_debito_fiscal) importe_debito_fiscal

                        from sfe.tfactura tfa
                        where '||v_boletos_filtro||' and tfa.fecha_factura between '''''||v_fecha_ini||'''''::date and '''''||v_fecha_fin||'''''::date and tfa.estado_reg = ''''activo'''' and ';--order by tfa.fecha_factura asc

            v_parametros.filtro = regexp_replace(v_parametros.filtro, '''', '''''', 'g');
            v_consulta = v_consulta||v_parametros.filtro;

                if v_conexion != 'OK' then
                      raise exception 'ERROR DE CONEXION A LA BASE DE DATOS CON DBLINK';
                else

                  --perform dblink_exec(v_cadena_factura,v_consulta,TRUE);

                  v_consulta = 'select  contador,
                  						importe_total_venta,
                  						importe_otros_no_suj_iva,
                                        exportacion_excentas,
                                        ventas_tasa_cero,
                                        descuento_rebaja_suj_iva,
                                        importe_debito_fiscal

                                 from dblink(''' || v_cadena_factura || ''', '''|| v_consulta ||''') as
                            		fac(
                            			contador bigint,
                                        importe_total_venta numeric,
                                        importe_otros_no_suj_iva numeric,
                                        exportacion_excentas numeric,
                                        ventas_tasa_cero numeric,
                                        descuento_rebaja_suj_iva numeric,
                                        importe_debito_fiscal numeric
                                    )';

                  v_conexion = (select dblink_disconnect('db_facturas'));

                end if;

			raise notice '%', v_consulta;
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
$body$
LANGUAGE 'plpgsql'
VOLATILE
CALLED ON NULL INPUT
SECURITY INVOKER
COST 100;