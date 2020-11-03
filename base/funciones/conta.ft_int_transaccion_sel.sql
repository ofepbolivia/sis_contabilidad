CREATE OR REPLACE FUNCTION conta.ft_int_transaccion_sel (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Sistema de Contabilidad
 FUNCION: 		conta.ft_int_transaccion_sel
 DESCRIPCION:   Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'conta.tint_transaccion'
 AUTOR: 		 (RAC)
 FECHA:	        01-09-2013 18:10:12
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
    v_cuentas			varchar;
    v_ordenes			varchar;
    v_tipo_cc			varchar;
    v_filtro_cuentas	varchar;
    v_filtro_ordenes	varchar;
    v_filtro_tipo_cc	varchar;
    v_filtro			varchar;

	--planillas
    v_desc_orden		varchar;
	v_codigo_orden		varchar;
    v_planilla			varchar;

   v_id_auxiliar varchar;
   v_fecha_ini varchar;
   v_fecha_fin varchar;
   v_gestion varchar;
   v_id_centro_costo varchar;
   v_id_partida varchar;
   v_filtro_id_auxiliar varchar;
   v_filtro_fecha_ini varchar;
   v_filtro_fecha_fin varchar;
   v_filtro_gestion varchar;
   v_filtro_id_centro_costo varchar;
   v_filtro_id_partida varchar;


  v_numero_cuenta		varchar;
  v_inicial_cuenta		varchar;
  v_cuenta_activo_como_pasivo		varchar;
  v_comportamiento		varchar;

  v_cuenta_acreedora_como_pasivo	varchar;
  v_saldo_anterior		numeric;
  v_debe_anterior		numeric;
  v_haber_anterior		numeric;

  v_fecha_inicio_gestion	varchar;

  v_desc_cuenta	varchar;
  v_desc_partida	varchar;
  v_desc_centro_costo	varchar;
  v_desc_auxiliar	varchar;

  v_datos_anterior	record;
  v_existencia_cuenta integer;
BEGIN

	v_nombre_funcion = 'conta.ft_int_transaccion_sel';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'CONTA_INTRANSA_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		admin
 	#FECHA:		01-09-2013 18:10:12
	***********************************/

	if(p_transaccion='CONTA_INTRANSA_SEL')then

    	begin
    	SELECT case when tpw.nro_tramite like 'PLA%' then 'si' else 'no' end
        into v_planilla
        FROM conta.tint_comprobante tc
        inner join wf.tproceso_wf tpw on tpw.id_proceso_wf = tc.id_proceso_wf
        WHERE tc.id_int_comprobante = v_parametros.id_int_comprobante;

        if(v_parametros.planilla::boolean)then
          v_desc_orden = '(select array_to_string(pxp.aggarray(distinct tot.codigo||'' ''||tot.desc_orden),'','')
                                              from orga.tcargo_presupuesto tcp
                                    inner join conta.torden_trabajo tot on tot.id_orden_trabajo = tcp.id_ot
                                    where tcp.id_centro_costo = transa.id_centro_costo)::varchar as desc_orden';
        else
          v_desc_orden = 'ot.desc_orden';
        end if;
    		--Sentencia de la consulta
			v_consulta:='select
                            transa.id_int_transaccion,
                            transa.id_partida,
                            transa.id_centro_costo,
                            transa.id_partida_ejecucion,
                            transa.estado_reg,
                            transa.id_int_transaccion_fk,
                            transa.id_cuenta,
                            transa.glosa,
                            transa.id_int_comprobante,
                            transa.id_auxiliar,
                            transa.id_usuario_reg,
                            transa.fecha_reg,
                            transa.id_usuario_mod,
                            transa.fecha_mod,
                            usu1.cuenta as usr_reg,
                            usu2.cuenta as usr_mod,
                            CASE par.sw_movimiento
                                WHEN ''flujo'' THEN
                                    ''(F) ''||par.codigo || '' - '' || par.nombre_partida
                                ELSE
                                    par.codigo || '' - '' || par.nombre_partida
                                END  as desc_partida,

                            cc.codigo_cc as desc_centro_costo,
                            cue.nro_cuenta || '' - '' || cue.nombre_cuenta as desc_cuenta,
                            aux.codigo_auxiliar || '' - '' || aux.nombre_auxiliar as desc_auxiliar,
                            par.sw_movimiento as tipo_partida,
                            ot.id_orden_trabajo,
                            '||v_desc_orden||',
                            transa.importe_debe,
                            transa.importe_haber,
                            transa.importe_gasto,
                            transa.importe_recurso,
                            transa.importe_debe_mb,
                            transa.importe_haber_mb,
                            transa.importe_gasto_mb,
                            transa.importe_recurso_mb,
                            transa.banco,
                            transa.forma_pago,
                            transa.nombre_cheque_trans,
                            transa.nro_cuenta_bancaria_trans,
                            transa.nro_cheque,
                            transa.importe_debe_mt,
                            transa.importe_haber_mt,
                            transa.importe_gasto_mt,
                            transa.importe_recurso_mt,

                            transa.importe_debe_ma,
                            transa.importe_haber_ma,
                            transa.importe_gasto_ma,
                            transa.importe_recurso_ma,


                            transa.id_moneda_tri,
                            transa.id_moneda_act,
                            transa.id_moneda,
                            transa.tipo_cambio,
                            transa.tipo_cambio_2,
                            transa.tipo_cambio_3,
                            transa.actualizacion,
                            transa.triangulacion,
                            suo.id_suborden,
                            (''(''||suo.codigo||'') ''||suo.nombre)::varchar as desc_suborden,
                            ot.codigo as codigo_ot,
                            cp.codigo_categoria::varchar,
                            '''||v_planilla||'''::varchar as planilla,
                            transa.id_concepto_ingas,
                            (conig.desc_ingas)::varchar as desc_ingas

                        from conta.tint_transaccion transa
						inner join segu.tusuario usu1 on usu1.id_usuario = transa.id_usuario_reg
                        inner join conta.tcuenta cue on cue.id_cuenta = transa.id_cuenta
						left join segu.tusuario usu2 on usu2.id_usuario = transa.id_usuario_mod
						left join pre.tpartida par on par.id_partida = transa.id_partida
						left join pre.vpresupuesto_cc cc on cc.id_centro_costo = transa.id_centro_costo
						left join conta.tauxiliar aux on aux.id_auxiliar = transa.id_auxiliar
                        left join conta.torden_trabajo ot on ot.id_orden_trabajo =  transa.id_orden_trabajo
                        left join conta.tsuborden suo on suo.id_suborden =  transa.id_suborden
                        left join pre.vcategoria_programatica cp ON cp.id_categoria_programatica = cc.id_categoria_prog

						left join param.tconcepto_ingas conig on conig.id_concepto_ingas = transa.id_concepto_ingas

				        where ';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

			--Devuelve la respuesta
			return v_consulta;

		end;

	/*********************************
 	#TRANSACCION:  'CONTA_INTRANSA_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		admin
 	#FECHA:		01-09-2013 18:10:12
	***********************************/

	elsif(p_transaccion='CONTA_INTRANSA_CONT')then

		begin
			--Sentencia de la consulta de conteo de registros
			v_consulta:='select
                          count(transa.id_int_transaccion) as total,
                          sum(transa.importe_debe) as total_debe,
                          sum(transa.importe_haber) as total_haber,
                          sum(transa.importe_debe_mb) as total_debe_mb,
                          sum(transa.importe_haber_mb) as total_haber_mb,
                          sum(transa.importe_debe_mt) as total_debe_mt,
                          sum(transa.importe_haber_mt) as total_haber_mt,
                          sum(transa.importe_debe_ma) as total_debe_ma,
                          sum(transa.importe_haber_ma) as total_haber_ma,
                          sum(transa.importe_gasto) as total_gasto,
                          sum(transa.importe_recurso) as total_recurso
					     from conta.tint_transaccion transa
						inner join segu.tusuario usu1 on usu1.id_usuario = transa.id_usuario_reg
                        inner join conta.tcuenta cue on cue.id_cuenta = transa.id_cuenta
						left join segu.tusuario usu2 on usu2.id_usuario = transa.id_usuario_mod
						left join pre.tpartida par on par.id_partida = transa.id_partida
						left join pre.vpresupuesto_cc cc on cc.id_centro_costo = transa.id_centro_costo
						left join conta.tauxiliar aux on aux.id_auxiliar = transa.id_auxiliar
                        left join conta.torden_trabajo ot on ot.id_orden_trabajo =  transa.id_orden_trabajo
                        left join conta.tsuborden suo on suo.id_suborden =  transa.id_suborden
                        left join pre.vcategoria_programatica cp ON cp.id_categoria_programatica = cc.id_categoria_prog

                        where  ';



			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
 raise notice '%',v_consulta;
			--Devuelve la respuesta
			return v_consulta;

		end;
	/*********************************
 	#TRANSACCION:  'CONTA_INTMAY_SEL'
 	#DESCRIPCION:	listado de transacicones para el mayor
 	#AUTOR:		rac
 	#FECHA:		24-04-2015 18:10:12
	***********************************/

	elsif(p_transaccion='CONTA_INTMAY_SEL')then

    	begin

            v_cuentas = '0';
            v_ordenes = '0';
            v_tipo_cc = '0';
            v_filtro_cuentas = '0=0';
            v_filtro_ordenes = '0=0';
            v_filtro_tipo_cc = '0=0';

             IF  pxp.f_existe_parametro(p_tabla,'id_cuenta')  THEN

                  IF v_parametros.id_cuenta is not NULL THEN

                      WITH RECURSIVE cuenta_rec (id_cuenta, id_cuenta_padre) AS (
                        SELECT cue.id_cuenta, cue.id_cuenta_padre
                        FROM conta.tcuenta cue
                        WHERE cue.id_cuenta = v_parametros.id_cuenta and cue.estado_reg = 'activo'
                      UNION ALL
                        SELECT cue2.id_cuenta, cue2.id_cuenta_padre
                        FROM cuenta_rec lrec
                        INNER JOIN conta.tcuenta cue2 ON lrec.id_cuenta = cue2.id_cuenta_padre
                        where cue2.estado_reg = 'activo'
                      )
                    SELECT  pxp.list(id_cuenta::varchar)
                      into
                        v_cuentas
                    FROM cuenta_rec;



                    v_filtro_cuentas = ' transa.id_cuenta in ('||v_cuentas||') ';
                END IF;

            END IF;

            IF  pxp.f_existe_parametro(p_tabla,'id_orden_trabajo')  THEN

                  IF v_parametros.id_orden_trabajo is not NULL THEN


                    IF v_parametros.id_orden_trabajo != 0 THEN
                          WITH RECURSIVE orden_rec (id_orden_trabajo, id_orden_trabajo_fk) AS (
                            SELECT cue.id_orden_trabajo, cue.id_orden_trabajo_fk
                            FROM conta.torden_trabajo cue
                            WHERE cue.id_orden_trabajo = v_parametros.id_orden_trabajo and cue.estado_reg = 'activo'
                          UNION ALL
                            SELECT cue2.id_orden_trabajo, cue2.id_orden_trabajo_fk
                            FROM orden_rec lrec
                            INNER JOIN conta.torden_trabajo cue2 ON lrec.id_orden_trabajo = cue2.id_orden_trabajo_fk
                            where cue2.estado_reg = 'activo'
                          )
                        SELECT  pxp.list(id_orden_trabajo::varchar)
                          into
                            v_ordenes
                        FROM orden_rec;

                        v_filtro_ordenes = ' transa.id_orden_trabajo in ('||v_ordenes||') ';
                    ELSE
                        --cuando la orden de trabajo es cero, se requiere msotrar las ordenes de trabajo nulas
                        v_filtro_ordenes = ' transa.id_orden_trabajo is null ';

                    END IF;
                END IF;
            END IF;


            IF  pxp.f_existe_parametro(p_tabla,'id_tipo_cc')  THEN

                  IF v_parametros.id_tipo_cc is not NULL THEN

                      WITH RECURSIVE tipo_cc_rec (id_tipo_cc, id_tipo_cc_fk) AS (
                        SELECT tcc.id_tipo_cc, tcc.id_tipo_cc_fk
                        FROM param.ttipo_cc tcc
                        WHERE tcc.id_tipo_cc = v_parametros.id_tipo_cc and tcc.estado_reg = 'activo'
                      UNION ALL
                        SELECT tcc2.id_tipo_cc, tcc2.id_tipo_cc_fk
                        FROM tipo_cc_rec lrec
                        INNER JOIN param.ttipo_cc tcc2 ON lrec.id_tipo_cc = tcc2.id_tipo_cc_fk
                        where tcc2.estado_reg = 'activo'
                      )
                    SELECT  pxp.list(id_tipo_cc::varchar)
                      into
                        v_tipo_cc
                    FROM tipo_cc_rec;



                    v_filtro_tipo_cc = ' cc.id_tipo_cc in ('||v_tipo_cc||') ';
                END IF;
             END IF;



            --Sentencia de la consulta
			v_consulta:='select
						transa.id_int_transaccion,
						transa.id_partida,
						transa.id_centro_costo,
						transa.id_partida_ejecucion,
						transa.estado_reg,
						transa.id_int_transaccion_fk,
						transa.id_cuenta,
						transa.glosa,
						transa.id_int_comprobante,
						transa.id_auxiliar,
						transa.id_usuario_reg,
						transa.fecha_reg,
						transa.id_usuario_mod,
						transa.fecha_mod,
						usu1.cuenta as usr_reg,
						usu2.cuenta as usr_mod,
                        COALESCE(transa.importe_debe_mb,0) as importe_debe_mb,
                        COALESCE(transa.importe_haber_mb,0) as importe_haber_mb,
                       	COALESCE(transa.importe_gasto_mb,0) as importe_gasto_mb,
						COALESCE(transa.importe_recurso_mb,0) as importe_recurso_mb,

                        COALESCE(transa.importe_debe_mt,0) as importe_debe_mt,
                        COALESCE(transa.importe_haber_mt,0) as importe_haber_mt,
                       	COALESCE(transa.importe_gasto_mt,0) as importe_gasto_mt,
						COALESCE(transa.importe_recurso_mt,0) as importe_recurso_mt,

                        COALESCE(transa.importe_debe_ma,0) as importe_debe_ma,
                        COALESCE(transa.importe_haber_ma,0) as importe_haber_ma,
                       	COALESCE(transa.importe_gasto_ma,0) as importe_gasto_ma,
						COALESCE(transa.importe_recurso_ma,0) as importe_recurso_ma,

                        CASE par.sw_movimiento
                        	WHEN ''flujo'' THEN
								''(F) ''||par.codigo || '' - '' || par.nombre_partida
                            ELSE
                            	par.codigo || '' - '' || par.nombre_partida
                        	END  as desc_partida,

						cc.codigo_cc as desc_centro_costo,
						cue.nro_cuenta || '' - '' || cue.nombre_cuenta as desc_cuenta,
						aux.codigo_auxiliar || '' - '' || aux.nombre_auxiliar as desc_auxiliar,
                        par.sw_movimiento as tipo_partida,
                        ot.id_orden_trabajo,
                        ot.desc_orden,
                        icbte.nro_cbte,
                        icbte.nro_tramite,
                        dep.nombre_corto,
                        icbte.fecha,
                        icbte.glosa1,
                        icbte.id_proceso_wf,
                        icbte.id_estado_wf,
                        icbte.c31,

                        --Remplazando por la subconsulta comentada (Ismael Valdivia 03/11/2020)
                        array_to_string( array_agg( cv.nro_documento), '','' )::varchar as nro_documentos

                        --Comentando esta subconsulta porque tarda en recuerar la informacion (Ismael Valdivia 03/11/2020)
                        /*(select array_to_string( array_agg( cv.nro_documento), '','' )
                         from conta.tdoc_compra_venta  cv
                         where cv.id_int_comprobante=transa.id_int_comprobante)::VARCHAR as nro_documentos*/
                        --------------------------------------------------------------------------------------------------

						from conta.tint_transaccion transa
                        inner join conta.tint_comprobante icbte on icbte.id_int_comprobante = transa.id_int_comprobante
                        inner join param.tdepto dep on dep.id_depto = icbte.id_depto
                        inner join param.tperiodo per on per.id_periodo = icbte.id_periodo
						inner join segu.tusuario usu1 on usu1.id_usuario = transa.id_usuario_reg

                        inner join conta.tcuenta cue on cue.id_cuenta = transa.id_cuenta
                        inner join conta.tconfig_tipo_cuenta ctc on ctc.tipo_cuenta = cue.tipo_cuenta
                        inner join conta.tconfig_subtipo_cuenta csc on csc.id_config_subtipo_cuenta = cue.id_config_subtipo_cuenta
						left join segu.tusuario usu2 on usu2.id_usuario = transa.id_usuario_mod
						left join pre.tpartida par on par.id_partida = transa.id_partida
						left join param.vcentro_costo cc on cc.id_centro_costo = transa.id_centro_costo
						left join conta.tauxiliar aux on aux.id_auxiliar = transa.id_auxiliar
                        left join conta.torden_trabajo ot on ot.id_orden_trabajo =  transa.id_orden_trabajo

                        left join conta.tdoc_compra_venta cv on cv.id_int_comprobante = transa.id_int_comprobante

				        where icbte.estado_reg = ''validado''
                              and ' ||v_filtro_cuentas||'
                              and '||v_filtro_ordenes||'
                              and '||v_filtro_tipo_cc||' and';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;

            --Aqui Aumentamos para agrupar la consulta
            v_consulta:=v_consulta||'group by transa.id_int_transaccion,
                                              usu1.cuenta,
                                              usu2.cuenta,
                                              cue.nro_cuenta,
                                              cue.nombre_cuenta,
                                              par.sw_movimiento,
                                              icbte.nro_cbte,
                                              icbte.nro_tramite,
                                              dep.nombre_corto,
                                              icbte.fecha,
                                              icbte.glosa1,
                                              icbte.id_proceso_wf,
                                              icbte.id_estado_wf,
                                              icbte.c31,
                                              par.codigo,
                                              par.nombre_partida,
                                              cc.codigo_cc,
                                              aux.codigo_auxiliar,
                                              aux.nombre_auxiliar,
                                              ot.id_orden_trabajo';


			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;
            raise notice '%', v_consulta;
			--Devuelve la respuesta
			return v_consulta;

		end;

	/*********************************
 	#TRANSACCION:  'CONTA_INTMAY_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		admin
 	#FECHA:		01-09-2013 18:10:12
	***********************************/

	elsif(p_transaccion='CONTA_INTMAY_CONT')then

		begin
            v_cuentas = '0';
            v_ordenes = '0';
            v_tipo_cc = '0';
            v_filtro_cuentas = '0=0';
            v_filtro_ordenes = '0=0';
            v_filtro_tipo_cc = '0=0';

             IF  pxp.f_existe_parametro(p_tabla,'id_cuenta')  THEN

                  IF v_parametros.id_cuenta is not NULL THEN

                      WITH RECURSIVE cuenta_rec (id_cuenta, id_cuenta_padre) AS (
                        SELECT cue.id_cuenta, cue.id_cuenta_padre
                        FROM conta.tcuenta cue
                        WHERE cue.id_cuenta = v_parametros.id_cuenta and cue.estado_reg = 'activo'
                      UNION ALL
                        SELECT cue2.id_cuenta, cue2.id_cuenta_padre
                        FROM cuenta_rec lrec
                        INNER JOIN conta.tcuenta cue2 ON lrec.id_cuenta = cue2.id_cuenta_padre
                        where cue2.estado_reg = 'activo'
                      )
                    SELECT  pxp.list(id_cuenta::varchar)
                      into
                        v_cuentas
                    FROM cuenta_rec;



                    v_filtro_cuentas = ' transa.id_cuenta in ('||v_cuentas||') ';
                END IF;

            END IF;

             IF  pxp.f_existe_parametro(p_tabla,'id_orden_trabajo')  THEN

                  IF v_parametros.id_orden_trabajo is not NULL THEN


                    IF v_parametros.id_orden_trabajo != 0 THEN
                          WITH RECURSIVE orden_rec (id_orden_trabajo, id_orden_trabajo_fk) AS (
                            SELECT cue.id_orden_trabajo, cue.id_orden_trabajo_fk
                            FROM conta.torden_trabajo cue
                            WHERE cue.id_orden_trabajo = v_parametros.id_orden_trabajo and cue.estado_reg = 'activo'
                          UNION ALL
                            SELECT cue2.id_orden_trabajo, cue2.id_orden_trabajo_fk
                            FROM orden_rec lrec
                            INNER JOIN conta.torden_trabajo cue2 ON lrec.id_orden_trabajo = cue2.id_orden_trabajo_fk
                            where cue2.estado_reg = 'activo'
                          )
                        SELECT  pxp.list(id_orden_trabajo::varchar)
                          into
                            v_ordenes
                        FROM orden_rec;

                        v_filtro_ordenes = ' transa.id_orden_trabajo in ('||v_ordenes||') ';
                    ELSE
                        --cuando la orden de trabajo es cero, se requiere msotrar las ordenes de trabajo nulas
                        v_filtro_ordenes = ' transa.id_orden_trabajo is null ';

                    END IF;
                END IF;
            END IF;


            IF  pxp.f_existe_parametro(p_tabla,'id_tipo_cc')  THEN

                  IF v_parametros.id_tipo_cc is not NULL THEN

                      WITH RECURSIVE tipo_cc_rec (id_tipo_cc, id_tipo_cc_fk) AS (
                        SELECT tcc.id_tipo_cc, tcc.id_tipo_cc_fk
                        FROM param.ttipo_cc tcc
                        WHERE tcc.id_tipo_cc = v_parametros.id_tipo_cc and tcc.estado_reg = 'activo'
                      UNION ALL
                        SELECT tcc2.id_tipo_cc, tcc2.id_tipo_cc_fk
                        FROM tipo_cc_rec lrec
                        INNER JOIN param.ttipo_cc tcc2 ON lrec.id_tipo_cc = tcc2.id_tipo_cc_fk
                        where tcc2.estado_reg = 'activo'
                      )
                    SELECT  pxp.list(id_tipo_cc::varchar)
                      into
                        v_tipo_cc
                    FROM tipo_cc_rec;



                    v_filtro_tipo_cc = ' cc.id_tipo_cc in ('||v_tipo_cc||') ';
                END IF;
             END IF;

            --RAC 16´/05/2017 quite esta suma de la consulta me parece incorecta, pero no estoy 100% seguro

            /*

            sum(CASE cue.valor_incremento
                        	WHEN ''negativo'' THEN
								COALESCE(transa.importe_debe_mb*-1,0)
                            ELSE
                            	COALESCE(transa.importe_debe_mb,0)
                        	END)  as total_debe,
            */

			--Sentencia de la consulta de conteo de registros
			v_consulta:='select
                        count(transa.id_int_transaccion) as total,


                        sum(COALESCE(transa.importe_debe_mb,0)) as total_debe,
                        sum(COALESCE(transa.importe_haber_mb,0)) as total_haber,
                        sum(COALESCE(transa.importe_debe_mt,0)) as total_debe_mt,
                        sum(COALESCE(transa.importe_haber_mt,0)) as total_haber_mt,
                        sum(COALESCE(transa.importe_debe_ma,0)) as total_debe_ma,
                        sum(COALESCE(transa.importe_haber_ma,0)) as total_haber_ma

					    from conta.tint_transaccion transa
                        inner join conta.tint_comprobante icbte on icbte.id_int_comprobante = transa.id_int_comprobante
                        inner join param.tdepto dep on dep.id_depto = icbte.id_depto
                        inner join param.tperiodo per on per.id_periodo = icbte.id_periodo
						inner join segu.tusuario usu1 on usu1.id_usuario = transa.id_usuario_reg

                        inner join conta.tcuenta cue on cue.id_cuenta = transa.id_cuenta
                        inner join conta.tconfig_tipo_cuenta ctc on ctc.tipo_cuenta = cue.tipo_cuenta
                        inner join conta.tconfig_subtipo_cuenta csc on csc.id_config_subtipo_cuenta = cue.id_config_subtipo_cuenta
						left join segu.tusuario usu2 on usu2.id_usuario = transa.id_usuario_mod
						left join pre.tpartida par on par.id_partida = transa.id_partida
						left join param.vcentro_costo cc on cc.id_centro_costo = transa.id_centro_costo
						left join conta.tauxiliar aux on aux.id_auxiliar = transa.id_auxiliar
            left join conta.torden_trabajo ot on ot.id_orden_trabajo =  transa.id_orden_trabajo

		        where icbte.estado_reg = ''validado''
                          and ' ||v_filtro_cuentas||'
                          and '||v_filtro_ordenes||'
                          and '||v_filtro_tipo_cc||' and';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
            --raise notice '%',v_consulta;

			--Devuelve la respuesta
			return v_consulta;

		end;

--INICIO para MayorResumido

/*********************************
 	#TRANSACCION:  'CONTA_MAYRE_SEL'
 	#DESCRIPCION:	listado de transacicones para el mayor
 	#AUTOR:
 	#FECHA:
	***********************************/

	elsif(p_transaccion='CONTA_MAYRE_SEL')then

    	begin

            v_cuentas = '0';
            v_ordenes = '0';
            v_tipo_cc = '0';
            v_filtro_cuentas = '0=0';
            v_filtro_ordenes = '0=0';
            v_filtro_tipo_cc = '0=0';

             IF  pxp.f_existe_parametro(p_tabla,'id_cuenta')  THEN

                  IF v_parametros.id_cuenta is not NULL THEN

                      WITH RECURSIVE cuenta_rec (id_cuenta, id_cuenta_padre) AS (
                        SELECT cue.id_cuenta, cue.id_cuenta_padre
                        FROM conta.tcuenta cue
                        WHERE cue.id_cuenta = v_parametros.id_cuenta and cue.estado_reg = 'activo'
                      UNION ALL
                        SELECT cue2.id_cuenta, cue2.id_cuenta_padre
                        FROM cuenta_rec lrec
                        INNER JOIN conta.tcuenta cue2 ON lrec.id_cuenta = cue2.id_cuenta_padre
                        where cue2.estado_reg = 'activo'
                      )
                    SELECT  pxp.list(id_cuenta::varchar)
                      into
                        v_cuentas
                    FROM cuenta_rec;



                    v_filtro_cuentas = ' transa.id_cuenta in ('||v_cuentas||') ';
                END IF;

            END IF;

            IF  pxp.f_existe_parametro(p_tabla,'id_orden_trabajo')  THEN

                  IF v_parametros.id_orden_trabajo is not NULL THEN


                    IF v_parametros.id_orden_trabajo != 0 THEN
                          WITH RECURSIVE orden_rec (id_orden_trabajo, id_orden_trabajo_fk) AS (
                            SELECT cue.id_orden_trabajo, cue.id_orden_trabajo_fk
                            FROM conta.torden_trabajo cue
                            WHERE cue.id_orden_trabajo = v_parametros.id_orden_trabajo and cue.estado_reg = 'activo'
                          UNION ALL
                            SELECT cue2.id_orden_trabajo, cue2.id_orden_trabajo_fk
                            FROM orden_rec lrec
                            INNER JOIN conta.torden_trabajo cue2 ON lrec.id_orden_trabajo = cue2.id_orden_trabajo_fk
                            where cue2.estado_reg = 'activo'
                          )
                        SELECT  pxp.list(id_orden_trabajo::varchar)
                          into
                            v_ordenes
                        FROM orden_rec;

                        v_filtro_ordenes = ' transa.id_orden_trabajo in ('||v_ordenes||') ';
                    ELSE
                        --cuando la orden de trabajo es cero, se requiere msotrar las ordenes de trabajo nulas
                        v_filtro_ordenes = ' transa.id_orden_trabajo is null ';

                    END IF;
                END IF;
            END IF;


            IF  pxp.f_existe_parametro(p_tabla,'id_tipo_cc')  THEN

                  IF v_parametros.id_tipo_cc is not NULL THEN

                      WITH RECURSIVE tipo_cc_rec (id_tipo_cc, id_tipo_cc_fk) AS (
                        SELECT tcc.id_tipo_cc, tcc.id_tipo_cc_fk
                        FROM param.ttipo_cc tcc
                        WHERE tcc.id_tipo_cc = v_parametros.id_tipo_cc and tcc.estado_reg = 'activo'
                      UNION ALL
                        SELECT tcc2.id_tipo_cc, tcc2.id_tipo_cc_fk
                        FROM tipo_cc_rec lrec
                        INNER JOIN param.ttipo_cc tcc2 ON lrec.id_tipo_cc = tcc2.id_tipo_cc_fk
                        where tcc2.estado_reg = 'activo'
                      )
                    SELECT  pxp.list(id_tipo_cc::varchar)
                      into
                        v_tipo_cc
                    FROM tipo_cc_rec;



                    v_filtro_tipo_cc = ' cc.id_tipo_cc in ('||v_tipo_cc||') ';
                END IF;
             END IF;



            --Sentencia de la consulta
			v_consulta:='select
						transa.id_int_transaccion,
						transa.id_partida,
						transa.id_centro_costo,
						transa.id_partida_ejecucion,
						transa.estado_reg,
						transa.id_int_transaccion_fk,
						transa.id_cuenta,
						transa.glosa,
						transa.id_int_comprobante,
						transa.id_auxiliar,
						transa.id_usuario_reg,
						transa.fecha_reg,
						transa.id_usuario_mod,
						transa.fecha_mod,
						usu1.cuenta as usr_reg,
						usu2.cuenta as usr_mod,
                        COALESCE(transa.importe_debe_mb,0) as importe_debe_mb,
                        COALESCE(transa.importe_haber_mb,0) as importe_haber_mb,
                       	COALESCE(transa.importe_gasto_mb,0) as importe_gasto_mb,
						COALESCE(transa.importe_recurso_mb,0) as importe_recurso_mb,

                        COALESCE(transa.importe_debe_mt,0) as importe_debe_mt,
                        COALESCE(transa.importe_haber_mt,0) as importe_haber_mt,
                       	COALESCE(transa.importe_gasto_mt,0) as importe_gasto_mt,
						COALESCE(transa.importe_recurso_mt,0) as importe_recurso_mt,

                        COALESCE(transa.importe_debe_ma,0) as importe_debe_ma,
                        COALESCE(transa.importe_haber_ma,0) as importe_haber_ma,
                       	COALESCE(transa.importe_gasto_ma,0) as importe_gasto_ma,
						COALESCE(transa.importe_recurso_ma,0) as importe_recurso_ma,

                        CASE par.sw_movimiento
                        	WHEN ''flujo'' THEN
								''(F) ''||par.codigo || '' - '' || par.nombre_partida
                            ELSE
                            	par.codigo || '' - '' || par.nombre_partida
                        	END  as desc_partida,

						cc.codigo_cc as desc_centro_costo,
						cue.nro_cuenta || '' - '' || cue.nombre_cuenta as desc_cuenta,
						aux.codigo_auxiliar || '' - '' || aux.nombre_auxiliar as desc_auxiliar,
                        par.sw_movimiento as tipo_partida,
                        ot.id_orden_trabajo,
                        ot.desc_orden,
                        icbte.nro_cbte,
                        icbte.nro_tramite,
                        dep.nombre_corto,
                        icbte.fecha,
                        icbte.glosa1,
                        icbte.id_proceso_wf,
                        icbte.id_estado_wf,
                        (transa.importe_haber_mb - transa.importe_debe_mb)::numeric as saldo_mb,
						(transa.importe_haber_mt - transa.importe_debe_mt)::numeric as saldo_mt


						from conta.tint_transaccion transa
                        inner join conta.tint_comprobante icbte on icbte.id_int_comprobante = transa.id_int_comprobante
                        inner join param.tdepto dep on dep.id_depto = icbte.id_depto
                        inner join param.tperiodo per on per.id_periodo = icbte.id_periodo
						inner join segu.tusuario usu1 on usu1.id_usuario = transa.id_usuario_reg

                        inner join conta.tcuenta cue on cue.id_cuenta = transa.id_cuenta
                        inner join conta.tconfig_tipo_cuenta ctc on ctc.tipo_cuenta = cue.tipo_cuenta
                        inner join conta.tconfig_subtipo_cuenta csc on csc.id_config_subtipo_cuenta = cue.id_config_subtipo_cuenta
						left join segu.tusuario usu2 on usu2.id_usuario = transa.id_usuario_mod
						left join pre.tpartida par on par.id_partida = transa.id_partida
						left join param.vcentro_costo cc on cc.id_centro_costo = transa.id_centro_costo
						left join conta.tauxiliar aux on aux.id_auxiliar = transa.id_auxiliar
                        left join conta.torden_trabajo ot on ot.id_orden_trabajo =  transa.id_orden_trabajo
				        where icbte.estado_reg = ''validado''
                              and ' ||v_filtro_cuentas||'
                              and '||v_filtro_ordenes||'
                              and '||v_filtro_tipo_cc||'

                               and';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;
            raise notice '%', v_consulta;
			--Devuelve la respuesta
			return v_consulta;

		end;

	/*********************************
 	#TRANSACCION:  'CONTA_MAYRE_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		admin
 	#FECHA:		30-05-2018 12:18:12
	***********************************/

	elsif(p_transaccion='CONTA_MAYRE_CONT')then

		begin
            v_cuentas = '0';
            v_ordenes = '0';
            v_tipo_cc = '0';
            v_filtro_cuentas = '0=0';
            v_filtro_ordenes = '0=0';
            v_filtro_tipo_cc = '0=0';

             IF  pxp.f_existe_parametro(p_tabla,'id_cuenta')  THEN

                  IF v_parametros.id_cuenta is not NULL THEN

                      WITH RECURSIVE cuenta_rec (id_cuenta, id_cuenta_padre) AS (
                        SELECT cue.id_cuenta, cue.id_cuenta_padre
                        FROM conta.tcuenta cue
                        WHERE cue.id_cuenta = v_parametros.id_cuenta and cue.estado_reg = 'activo'
                      UNION ALL
                        SELECT cue2.id_cuenta, cue2.id_cuenta_padre
                        FROM cuenta_rec lrec
                        INNER JOIN conta.tcuenta cue2 ON lrec.id_cuenta = cue2.id_cuenta_padre
                        where cue2.estado_reg = 'activo'
                      )
                    SELECT  pxp.list(id_cuenta::varchar)
                      into
                        v_cuentas
                    FROM cuenta_rec;



                    v_filtro_cuentas = ' transa.id_cuenta in ('||v_cuentas||') ';
                END IF;

            END IF;

             IF  pxp.f_existe_parametro(p_tabla,'id_orden_trabajo')  THEN

                  IF v_parametros.id_orden_trabajo is not NULL THEN


                    IF v_parametros.id_orden_trabajo != 0 THEN
                          WITH RECURSIVE orden_rec (id_orden_trabajo, id_orden_trabajo_fk) AS (
                            SELECT cue.id_orden_trabajo, cue.id_orden_trabajo_fk
                            FROM conta.torden_trabajo cue
                            WHERE cue.id_orden_trabajo = v_parametros.id_orden_trabajo and cue.estado_reg = 'activo'
                          UNION ALL
                            SELECT cue2.id_orden_trabajo, cue2.id_orden_trabajo_fk
                            FROM orden_rec lrec
                            INNER JOIN conta.torden_trabajo cue2 ON lrec.id_orden_trabajo = cue2.id_orden_trabajo_fk
                            where cue2.estado_reg = 'activo'
                          )
                        SELECT  pxp.list(id_orden_trabajo::varchar)
                          into
                            v_ordenes
                        FROM orden_rec;

                        v_filtro_ordenes = ' transa.id_orden_trabajo in ('||v_ordenes||') ';
                    ELSE
                        --cuando la orden de trabajo es cero, se requiere msotrar las ordenes de trabajo nulas
                        v_filtro_ordenes = ' transa.id_orden_trabajo is null ';

                    END IF;
                END IF;
            END IF;


            IF  pxp.f_existe_parametro(p_tabla,'id_tipo_cc')  THEN

                  IF v_parametros.id_tipo_cc is not NULL THEN

                      WITH RECURSIVE tipo_cc_rec (id_tipo_cc, id_tipo_cc_fk) AS (
                        SELECT tcc.id_tipo_cc, tcc.id_tipo_cc_fk
                        FROM param.ttipo_cc tcc
                        WHERE tcc.id_tipo_cc = v_parametros.id_tipo_cc and tcc.estado_reg = 'activo'
                      UNION ALL
                        SELECT tcc2.id_tipo_cc, tcc2.id_tipo_cc_fk
                        FROM tipo_cc_rec lrec
                        INNER JOIN param.ttipo_cc tcc2 ON lrec.id_tipo_cc = tcc2.id_tipo_cc_fk
                        where tcc2.estado_reg = 'activo'
                      )
                    SELECT  pxp.list(id_tipo_cc::varchar)
                      into
                        v_tipo_cc
                    FROM tipo_cc_rec;



                    v_filtro_tipo_cc = ' cc.id_tipo_cc in ('||v_tipo_cc||') ';
                END IF;
             END IF;

            --RAC 16´/05/2017 quite esta suma de la consulta me parece incorecta, pero no estoy 100% seguro

            /*

            sum(CASE cue.valor_incremento
                        	WHEN ''negativo'' THEN
								COALESCE(transa.importe_debe_mb*-1,0)
                            ELSE
                            	COALESCE(transa.importe_debe_mb,0)
                        	END)  as total_debe,
            */

			--Sentencia de la consulta de conteo de registros
			v_consulta:='select
                        count(transa.id_int_transaccion) as total,


                        sum(COALESCE(transa.importe_debe_mb,0)) as total_debe,
                        sum(COALESCE(transa.importe_haber_mb,0)) as total_haber,
                        sum(COALESCE(transa.importe_debe_mt,0)) as total_debe_mt,
                        sum(COALESCE(transa.importe_haber_mt,0)) as total_haber_mt,
                        sum(COALESCE(transa.importe_debe_ma,0)) as total_debe_ma,
                        sum(COALESCE(transa.importe_haber_ma,0)) as total_haber_ma

					    from conta.tint_transaccion transa
                        inner join conta.tint_comprobante icbte on icbte.id_int_comprobante = transa.id_int_comprobante
                        inner join param.tdepto dep on dep.id_depto = icbte.id_depto
                        inner join param.tperiodo per on per.id_periodo = icbte.id_periodo
						inner join segu.tusuario usu1 on usu1.id_usuario = transa.id_usuario_reg

                        inner join conta.tcuenta cue on cue.id_cuenta = transa.id_cuenta
                        inner join conta.tconfig_tipo_cuenta ctc on ctc.tipo_cuenta = cue.tipo_cuenta
                        inner join conta.tconfig_subtipo_cuenta csc on csc.id_config_subtipo_cuenta = cue.id_config_subtipo_cuenta
						left join segu.tusuario usu2 on usu2.id_usuario = transa.id_usuario_mod
						left join pre.tpartida par on par.id_partida = transa.id_partida
						left join param.vcentro_costo cc on cc.id_centro_costo = transa.id_centro_costo
						left join conta.tauxiliar aux on aux.id_auxiliar = transa.id_auxiliar
                        left join conta.torden_trabajo ot on ot.id_orden_trabajo =  transa.id_orden_trabajo
				        where icbte.estado_reg = ''validado''
                              and ' ||v_filtro_cuentas||'
                              and '||v_filtro_ordenes||'
                              and '||v_filtro_tipo_cc||' and';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
            --raise notice '%',v_consulta;

			--Devuelve la respuesta
			return v_consulta;

		end;
--FIN para MayorResumido

--INICIO para DetalleComprobante

/*********************************
 	#TRANSACCION:  'CONTA_DECOM_SEL'
 	#DESCRIPCION:	listado de transacicones para el mayor
 	#AUTOR:
 	#FECHA:
	***********************************/

	elsif(p_transaccion='CONTA_DECOM_SEL')then

    	begin

            v_cuentas = '0';
            v_ordenes = '0';
            v_tipo_cc = '0';
            v_filtro_cuentas = '0=0';
            v_filtro_ordenes = '0=0';
            v_filtro_tipo_cc = '0=0';

             IF  pxp.f_existe_parametro(p_tabla,'id_cuenta')  THEN

                  IF v_parametros.id_cuenta is not NULL THEN

                      WITH RECURSIVE cuenta_rec (id_cuenta, id_cuenta_padre) AS (
                        SELECT cue.id_cuenta, cue.id_cuenta_padre
                        FROM conta.tcuenta cue
                        WHERE cue.id_cuenta = v_parametros.id_cuenta and cue.estado_reg = 'activo'
                      UNION ALL
                        SELECT cue2.id_cuenta, cue2.id_cuenta_padre
                        FROM cuenta_rec lrec
                        INNER JOIN conta.tcuenta cue2 ON lrec.id_cuenta = cue2.id_cuenta_padre
                        where cue2.estado_reg = 'activo'
                      )
                    SELECT  pxp.list(id_cuenta::varchar)
                      into
                        v_cuentas
                    FROM cuenta_rec;



                    v_filtro_cuentas = ' transa.id_cuenta in ('||v_cuentas||') ';
                END IF;

            END IF;

            IF  pxp.f_existe_parametro(p_tabla,'id_orden_trabajo')  THEN

                  IF v_parametros.id_orden_trabajo is not NULL THEN


                    IF v_parametros.id_orden_trabajo != 0 THEN
                          WITH RECURSIVE orden_rec (id_orden_trabajo, id_orden_trabajo_fk) AS (
                            SELECT cue.id_orden_trabajo, cue.id_orden_trabajo_fk
                            FROM conta.torden_trabajo cue
                            WHERE cue.id_orden_trabajo = v_parametros.id_orden_trabajo and cue.estado_reg = 'activo'
                          UNION ALL
                            SELECT cue2.id_orden_trabajo, cue2.id_orden_trabajo_fk
                            FROM orden_rec lrec
                            INNER JOIN conta.torden_trabajo cue2 ON lrec.id_orden_trabajo = cue2.id_orden_trabajo_fk
                            where cue2.estado_reg = 'activo'
                          )
                        SELECT  pxp.list(id_orden_trabajo::varchar)
                          into
                            v_ordenes
                        FROM orden_rec;

                        v_filtro_ordenes = ' transa.id_orden_trabajo in ('||v_ordenes||') ';
                    ELSE
                        --cuando la orden de trabajo es cero, se requiere msotrar las ordenes de trabajo nulas
                        v_filtro_ordenes = ' transa.id_orden_trabajo is null ';

                    END IF;
                END IF;
            END IF;


            IF  pxp.f_existe_parametro(p_tabla,'id_tipo_cc')  THEN

                  IF v_parametros.id_tipo_cc is not NULL THEN

                      WITH RECURSIVE tipo_cc_rec (id_tipo_cc, id_tipo_cc_fk) AS (
                        SELECT tcc.id_tipo_cc, tcc.id_tipo_cc_fk
                        FROM param.ttipo_cc tcc
                        WHERE tcc.id_tipo_cc = v_parametros.id_tipo_cc and tcc.estado_reg = 'activo'
                      UNION ALL
                        SELECT tcc2.id_tipo_cc, tcc2.id_tipo_cc_fk
                        FROM tipo_cc_rec lrec
                        INNER JOIN param.ttipo_cc tcc2 ON lrec.id_tipo_cc = tcc2.id_tipo_cc_fk
                        where tcc2.estado_reg = 'activo'
                      )
                    SELECT  pxp.list(id_tipo_cc::varchar)
                      into
                        v_tipo_cc
                    FROM tipo_cc_rec;



                    v_filtro_tipo_cc = ' cc.id_tipo_cc in ('||v_tipo_cc||') ';
                END IF;
             END IF;

			--raise exception 'cadena: %, b: %, c: %, d: %', v_filtro_cuentas, v_filtro_ordenes, v_filtro_tipo_cc, v_parametros.filtro;

            --Sentencia de la consulta
			v_consulta:='select
						transa.id_int_transaccion,
						transa.id_partida,
						transa.id_centro_costo,
						transa.id_partida_ejecucion,
						transa.estado_reg,
						transa.id_int_transaccion_fk,
						transa.id_cuenta,
						transa.glosa,
						transa.id_int_comprobante,
						transa.id_auxiliar,
						transa.id_usuario_reg,
						transa.fecha_reg,
						transa.id_usuario_mod,
						transa.fecha_mod,
						usu1.cuenta as usr_reg,
						usu2.cuenta as usr_mod,
                        COALESCE(transa.importe_debe_mb,0) as importe_debe_mb,
                        COALESCE(transa.importe_haber_mb,0) as importe_haber_mb,

                        conta.f_next_int_transaccion_monto(transa.id_int_transaccion,icbte.id_int_comprobante,icbte.nro_tramite,
                        case when COALESCE(transa.importe_debe_mb,0) != 0 then ''debe'' else ''haber'' end,
                        case when COALESCE(transa.importe_debe_mb,0) != 0 then transa.importe_debe_mb else transa.importe_haber_mb end)::numeric as importe_saldo_mb,

                       	COALESCE(transa.importe_gasto_mb,0) as importe_gasto_mb,
						COALESCE(transa.importe_recurso_mb,0) as importe_recurso_mb,

                        COALESCE(transa.importe_debe_mt,0) as importe_debe_mt,
                        COALESCE(transa.importe_haber_mt,0) as importe_haber_mt,
                       	COALESCE(transa.importe_gasto_mt,0) as importe_gasto_mt,
						COALESCE(transa.importe_recurso_mt,0) as importe_recurso_mt,

                        COALESCE(transa.importe_debe_ma,0) as importe_debe_ma,
                        COALESCE(transa.importe_haber_ma,0) as importe_haber_ma,
                       	COALESCE(transa.importe_gasto_ma,0) as importe_gasto_ma,
						COALESCE(transa.importe_recurso_ma,0) as importe_recurso_ma,

                        CASE par.sw_movimiento
                        	WHEN ''flujo'' THEN
								''(F) ''||par.codigo || '' - '' || par.nombre_partida
                            ELSE
                            	par.codigo || '' - '' || par.nombre_partida
                        	END  as desc_partida,

						cc.codigo_cc as desc_centro_costo,
						cue.nro_cuenta || '' - '' || cue.nombre_cuenta as desc_cuenta,
						aux.codigo_auxiliar || '' - '' || aux.nombre_auxiliar as desc_auxiliar,
                        par.sw_movimiento as tipo_partida,
                        ot.id_orden_trabajo,
                        ot.desc_orden,
                        icbte.nro_cbte,
                        icbte.nro_tramite,
                        dep.nombre_corto,
                        icbte.fecha,
                        icbte.glosa1,
                        icbte.id_proceso_wf,
                        icbte.id_estado_wf,
                        icbte.c31,
                        ccom.descripcion,
                        icbte.fecha_costo_ini,
                        icbte.fecha_costo_fin,
                        icbte.id_int_comprobante_fks::varchar as comprobante_fks,
                        ot.codigo,

                      	(select array_to_string( array_agg( cv.nro_documento), '','' )
                         from conta.tdoc_compra_venta  cv
                         where cv.id_int_comprobante=transa.id_int_comprobante)::VARCHAR as nro_documentos

                        from conta.tint_transaccion transa
                        inner join conta.tint_comprobante icbte on icbte.id_int_comprobante = transa.id_int_comprobante
                        inner join conta.tclase_comprobante ccom on ccom.id_clase_comprobante = icbte.id_clase_comprobante

                        inner join param.tdepto dep on dep.id_depto = icbte.id_depto
                        inner join param.tperiodo per on per.id_periodo = icbte.id_periodo
						inner join segu.tusuario usu1 on usu1.id_usuario = transa.id_usuario_reg
						 inner join conta.tcuenta cue on cue.id_cuenta = transa.id_cuenta
                        inner join conta.tconfig_tipo_cuenta ctc on ctc.tipo_cuenta = cue.tipo_cuenta
                        inner join conta.tconfig_subtipo_cuenta csc on csc.id_config_subtipo_cuenta = cue.id_config_subtipo_cuenta
						left join segu.tusuario usu2 on usu2.id_usuario = transa.id_usuario_mod
						left join pre.tpartida par on par.id_partida = transa.id_partida
						left join param.vcentro_costo cc on cc.id_centro_costo = transa.id_centro_costo
						left join conta.tauxiliar aux on aux.id_auxiliar = transa.id_auxiliar
                        left join conta.torden_trabajo ot on ot.id_orden_trabajo =  transa.id_orden_trabajo
				        where icbte.estado_reg = ''validado''
                              and ' ||v_filtro_cuentas||'
                              and '||v_filtro_ordenes||'
                              and '||v_filtro_tipo_cc||'

                               and';
--raise exception 'a: %, b: %, c: %, d: %, e:%', v_parametros.filtro, v_parametros.ordenacion, v_parametros.dir_ordenacion, v_parametros.cantidad, v_parametros.puntero;
			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||' order by icbte.nro_tramite asc, ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ', id_int_comprobante asc  limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;
            raise notice '%', v_consulta;
			--Devuelve la respuesta
			return v_consulta;

		end;

	/*********************************
 	#TRANSACCION:  'CONTA_MAYRE_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		admin
 	#FECHA:		30-05-2018 12:18:12
	***********************************/

	elsif(p_transaccion='CONTA_DECOM_CONT')then

		begin
            v_cuentas = '0';
            v_ordenes = '0';
            v_tipo_cc = '0';
            v_filtro_cuentas = '0=0';
            v_filtro_ordenes = '0=0';
            v_filtro_tipo_cc = '0=0';

             IF  pxp.f_existe_parametro(p_tabla,'id_cuenta')  THEN

                  IF v_parametros.id_cuenta is not NULL THEN

                      WITH RECURSIVE cuenta_rec (id_cuenta, id_cuenta_padre) AS (
                        SELECT cue.id_cuenta, cue.id_cuenta_padre
                        FROM conta.tcuenta cue
                        WHERE cue.id_cuenta = v_parametros.id_cuenta and cue.estado_reg = 'activo'
                      UNION ALL
                        SELECT cue2.id_cuenta, cue2.id_cuenta_padre
                        FROM cuenta_rec lrec
                        INNER JOIN conta.tcuenta cue2 ON lrec.id_cuenta = cue2.id_cuenta_padre
                        where cue2.estado_reg = 'activo'
                      )
                    SELECT  pxp.list(id_cuenta::varchar)
                      into
                        v_cuentas
                    FROM cuenta_rec;



                    v_filtro_cuentas = ' transa.id_cuenta in ('||v_cuentas||') ';
                END IF;

            END IF;

             IF  pxp.f_existe_parametro(p_tabla,'id_orden_trabajo')  THEN

                  IF v_parametros.id_orden_trabajo is not NULL THEN


                    IF v_parametros.id_orden_trabajo != 0 THEN
                          WITH RECURSIVE orden_rec (id_orden_trabajo, id_orden_trabajo_fk) AS (
                            SELECT cue.id_orden_trabajo, cue.id_orden_trabajo_fk
                            FROM conta.torden_trabajo cue
                            WHERE cue.id_orden_trabajo = v_parametros.id_orden_trabajo and cue.estado_reg = 'activo'
                          UNION ALL
                            SELECT cue2.id_orden_trabajo, cue2.id_orden_trabajo_fk
                            FROM orden_rec lrec
                            INNER JOIN conta.torden_trabajo cue2 ON lrec.id_orden_trabajo = cue2.id_orden_trabajo_fk
                            where cue2.estado_reg = 'activo'
                          )
                        SELECT  pxp.list(id_orden_trabajo::varchar)
                          into
                            v_ordenes
                        FROM orden_rec;

                        v_filtro_ordenes = ' transa.id_orden_trabajo in ('||v_ordenes||') ';
                    ELSE
                        --cuando la orden de trabajo es cero, se requiere msotrar las ordenes de trabajo nulas
                        v_filtro_ordenes = ' transa.id_orden_trabajo is null ';

                    END IF;
                END IF;
            END IF;


            IF  pxp.f_existe_parametro(p_tabla,'id_tipo_cc')  THEN

                  IF v_parametros.id_tipo_cc is not NULL THEN

                      WITH RECURSIVE tipo_cc_rec (id_tipo_cc, id_tipo_cc_fk) AS (
                        SELECT tcc.id_tipo_cc, tcc.id_tipo_cc_fk
                        FROM param.ttipo_cc tcc
                        WHERE tcc.id_tipo_cc = v_parametros.id_tipo_cc and tcc.estado_reg = 'activo'
                      UNION ALL
                        SELECT tcc2.id_tipo_cc, tcc2.id_tipo_cc_fk
                        FROM tipo_cc_rec lrec
                        INNER JOIN param.ttipo_cc tcc2 ON lrec.id_tipo_cc = tcc2.id_tipo_cc_fk
                        where tcc2.estado_reg = 'activo'
                      )
                    SELECT  pxp.list(id_tipo_cc::varchar)
                      into
                        v_tipo_cc
                    FROM tipo_cc_rec;



                    v_filtro_tipo_cc = ' cc.id_tipo_cc in ('||v_tipo_cc||') ';
                END IF;
             END IF;

            --RAC 16´/05/2017 quite esta suma de la consulta me parece incorecta, pero no estoy 100% seguro

            /*

            sum(CASE cue.valor_incremento
                        	WHEN ''negativo'' THEN
								COALESCE(transa.importe_debe_mb*-1,0)
                            ELSE
                            	COALESCE(transa.importe_debe_mb,0)
                        	END)  as total_debe,
            */

			--Sentencia de la consulta de conteo de registros
			v_consulta:='select
                        count(transa.id_int_transaccion) as total,


                        sum(COALESCE(transa.importe_debe_mb,0)) as total_debe,
                        sum(COALESCE(transa.importe_haber_mb,0)) as total_haber,
                        sum(COALESCE(transa.importe_debe_mt,0)) as total_debe_mt,
                        sum(COALESCE(transa.importe_haber_mt,0)) as total_haber_mt,
                        sum(COALESCE(transa.importe_debe_ma,0)) as total_debe_ma,
                        sum(COALESCE(transa.importe_haber_ma,0)) as total_haber_ma

					    from conta.tint_transaccion transa
                        inner join conta.tint_comprobante icbte on icbte.id_int_comprobante = transa.id_int_comprobante
                        inner join conta.tclase_comprobante ccom on ccom.id_clase_comprobante = icbte.id_clase_comprobante

                        inner join param.tdepto dep on dep.id_depto = icbte.id_depto
                        inner join param.tperiodo per on per.id_periodo = icbte.id_periodo
						inner join segu.tusuario usu1 on usu1.id_usuario = transa.id_usuario_reg

                        inner join conta.tcuenta cue on cue.id_cuenta = transa.id_cuenta
                        inner join conta.tconfig_tipo_cuenta ctc on ctc.tipo_cuenta = cue.tipo_cuenta
                        inner join conta.tconfig_subtipo_cuenta csc on csc.id_config_subtipo_cuenta = cue.id_config_subtipo_cuenta
						left join segu.tusuario usu2 on usu2.id_usuario = transa.id_usuario_mod
						left join pre.tpartida par on par.id_partida = transa.id_partida
						left join param.vcentro_costo cc on cc.id_centro_costo = transa.id_centro_costo
						left join conta.tauxiliar aux on aux.id_auxiliar = transa.id_auxiliar
                        left join conta.torden_trabajo ot on ot.id_orden_trabajo =  transa.id_orden_trabajo
				        where icbte.estado_reg = ''validado''
                              and ' ||v_filtro_cuentas||'
                              and '||v_filtro_ordenes||'
                              and '||v_filtro_tipo_cc||' and';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
            --raise notice '%',v_consulta;

			--Devuelve la respuesta
			return v_consulta;

		end;
--FIN para DetalleComprobante


    /*********************************
 	#TRANSACCION:  'CONTA_INTANA_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		admin
 	#FECHA:		01-09-2013 18:10:12
	***********************************/

	elseif(p_transaccion='CONTA_INTANA_SEL')then

    	begin

             if pxp.f_existe_parametro(p_tabla,'id_periodo') then
               v_filtro = ' id_periodo='||v_parametros.id_periodo::varchar;
             elseif pxp.f_existe_parametro(p_tabla,'fecha_ini') then
               v_filtro = ' fecha BETWEEN '''||v_parametros.fecha_ini||'''::date and '''||v_parametros.fecha_fin||'''::Date';
             else
                v_filtro = ' 0=0 ';
             end if;


    		--Sentencia de la consulta
			v_consulta:='SELECT
            				id_orden_trabajo,
                            sum(importe_debe_mb) as importe_debe_mb,
                            sum(importe_haber_mb) as importe_haber_mb,
                            sum(importe_debe_mt) as importe_debe_mt,
                            sum(importe_haber_mt) as importe_haber_mt,
                            sum(importe_debe_ma) as importe_debe_ma,
                            sum(importe_haber_ma) as importe_haber_ma,
                            codigo_ot::varchar,
                            desc_orden::varchar

                          FROM
                            conta.vint_transaccion_analisis  v
                          where    '||v_parametros.id_tipo_cc::varchar||' =ANY(ids) and '||v_filtro|| ' and ';


              --Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;


            v_consulta:=v_consulta||'
                            group by
                                id_orden_trabajo,
                                codigo_ot,
                                desc_orden ';


			--Definicion de la respuesta

			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;
            raise notice '%',v_consulta;
			--Devuelve la respuesta
			return v_consulta;

		end;

    /*********************************
 	#TRANSACCION:  'CONTA_INTANA_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		admin
 	#FECHA:		01-09-2013 18:10:12
	***********************************/

	elsif(p_transaccion='CONTA_INTANA_CONT')then

		begin

             if pxp.f_existe_parametro(p_tabla,'id_periodo') then
               v_filtro = ' id_periodo='||v_parametros.id_periodo::varchar;
             elseif pxp.f_existe_parametro(p_tabla,'fecha_ini') then
               v_filtro = ' fecha BETWEEN '''||v_parametros.fecha_ini||'''::date and '''||v_parametros.fecha_fin||'''::Date';
             else
                v_filtro = ' 0=0 ';
             end if;


             v_consulta:='WITH parcial AS (
                                            SELECT
                                                      id_orden_trabajo as id_orden_trabajo,
                                                      sum(importe_debe_mb) as importe_debe_mb,
                                                      sum(importe_haber_mb) as importe_haber_mb,
                                                      sum(importe_debe_mt) as importe_debe_mt,
                                                      sum(importe_haber_mt) as importe_haber_mt,
                                                      sum(importe_debe_ma) as importe_debe_ma,
                                                      sum(importe_haber_ma) as importe_haber_ma
                                                   FROM
                                                      conta.vint_transaccion_analisis  v
                                                   where    '||v_parametros.id_tipo_cc::varchar||' =ANY(ids) and '||v_filtro|| ' and ';

             v_consulta:=v_consulta||v_parametros.filtro;

             v_consulta:= v_consulta|| 'group by
                                                    id_orden_trabajo,
                                                    codigo_ot,
                                                    desc_orden  )

                                             SELECT
                                                   count(id_orden_trabajo) as total,
                                                   sum(importe_debe_mb) as importe_debe_mb,
                                                   sum(importe_haber_mb) as importe_haber_mb,
                                                   sum(importe_debe_mt) as importe_debe_mt,
                                                   sum(importe_haber_mt) as importe_haber_mt,
                                                   sum(importe_debe_ma) as importe_debe_ma,
                                                   sum(importe_haber_ma) as importe_haber_ma
                                            FROM parcial';

            raise notice '%',v_consulta;

			--Devuelve la respuesta
			return v_consulta;

		end;

    /*********************************
 	#TRANSACCION:  'CONTA_INTPAR_SEL'
 	#DESCRIPCION:	consulta de analisis de partidas por tipo_cc
 	#AUTOR:		admin
 	#FECHA:		01-09-2013 18:10:12
	***********************************/

	elseif(p_transaccion='CONTA_INTPAR_SEL')then

    	begin

             if pxp.f_existe_parametro(p_tabla,'id_periodo') then
               v_filtro = ' id_periodo='||v_parametros.id_periodo::varchar;
             elseif pxp.f_existe_parametro(p_tabla,'fecha_ini') then
               v_filtro = ' fecha BETWEEN '''||v_parametros.fecha_ini||'''::date and '''||v_parametros.fecha_fin||'''::Date';
             else
                v_filtro = ' 0=0 ';
             end if;


    		--Sentencia de la consulta
			v_consulta:='SELECT
            				id_partida,
                            sum(importe_debe_mb) as importe_debe_mb,
                            sum(importe_haber_mb) as importe_haber_mb,
                            sum(importe_debe_mt) as importe_debe_mt,
                            sum(importe_haber_mt) as importe_haber_mt,
                            sum(importe_debe_ma) as importe_debe_ma,
                            sum(importe_haber_ma) as importe_haber_ma,
                            codigo_partida::varchar,
                            sw_movimiento::varchar,
                            descripcion_partida::varchar

                          FROM
                            conta.vint_transaccion_analisis  v
                          where    '||v_parametros.id_tipo_cc::varchar||' =ANY(ids) and '||v_filtro|| ' and ';


              --Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;


            v_consulta:=v_consulta||'
                            group by
                                id_partida,
                                codigo_partida,
                                descripcion_partida,
                                sw_movimiento ';


			--Definicion de la respuesta

			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

            raise notice '%',v_consulta;
			--Devuelve la respuesta
			return v_consulta;

		end;

    /*********************************
 	#TRANSACCION:  'CONTA_INTPAR_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		admin
 	#FECHA:		01-09-2013 18:10:12
	***********************************/

	elsif(p_transaccion='CONTA_INTPAR_CONT')then

		begin

             if pxp.f_existe_parametro(p_tabla,'id_periodo') then
               v_filtro = ' id_periodo='||v_parametros.id_periodo::varchar;
             elseif pxp.f_existe_parametro(p_tabla,'fecha_ini') then
               v_filtro = ' fecha BETWEEN '''||v_parametros.fecha_ini||'''::date and '''||v_parametros.fecha_fin||'''::Date';
             else
                v_filtro = ' 0=0 ';
             end if;


             v_consulta:='WITH parcial AS (
                                            SELECT
                                                      id_partida as id_partida,
                                                      sum(importe_debe_mb) as importe_debe_mb,
                                                      sum(importe_haber_mb) as importe_haber_mb,
                                                      sum(importe_debe_mt) as importe_debe_mt,
                                                      sum(importe_haber_mt) as importe_haber_mt,
                                                      sum(importe_debe_ma) as importe_debe_ma,
                                                      sum(importe_haber_ma) as importe_haber_ma
                                                   FROM
                                                      conta.vint_transaccion_analisis  v
                                                   where    '||v_parametros.id_tipo_cc::varchar||' =ANY(ids) and '||v_filtro|| ' and ';

             v_consulta:=v_consulta||v_parametros.filtro;

             v_consulta:= v_consulta|| 'group by
                                                      id_partida,
                                                      codigo_partida,
                                                      descripcion_partida,
                                                      sw_movimiento  )

                                             SELECT
                                                   count(id_partida) as total,
                                                   sum(importe_debe_mb) as importe_debe_mb,
                                                   sum(importe_haber_mb) as importe_haber_mb,
                                                   sum(importe_debe_mt) as importe_debe_mt,
                                                   sum(importe_haber_mt) as importe_haber_mt,
                                                   sum(importe_debe_ma) as importe_debe_ma,
                                                   sum(importe_haber_ma) as importe_haber_ma
                                            FROM parcial';



            raise notice '%',v_consulta;

			--Devuelve la respuesta
			return v_consulta;

		end;

    /*********************************
 	#TRANSACCION:  'CONTA_INTCUE_SEL'
 	#DESCRIPCION:	consulta de analisis de cuentas por tipo_cc
 	#AUTOR:		admin
 	#FECHA:		01-09-2013 18:10:12
	***********************************/

	elseif(p_transaccion='CONTA_INTCUE_SEL')then

    	begin

             if pxp.f_existe_parametro(p_tabla,'id_periodo') then
               v_filtro = ' id_periodo='||v_parametros.id_periodo::varchar;
             elseif pxp.f_existe_parametro(p_tabla,'fecha_ini') then
               v_filtro = ' fecha BETWEEN '''||v_parametros.fecha_ini||'''::date and '''||v_parametros.fecha_fin||'''::Date';
             else
                v_filtro = ' 0=0 ';
             end if;


    		--Sentencia de la consulta
			v_consulta:='SELECT
            				id_cuenta,
                            sum(importe_debe_mb) as importe_debe_mb,
                            sum(importe_haber_mb) as importe_haber_mb,
                            sum(importe_debe_mt) as importe_debe_mt,
                            sum(importe_haber_mt) as importe_haber_mt,
                            sum(importe_debe_ma) as importe_debe_ma,
                            sum(importe_haber_ma) as importe_haber_ma,
                            codigo_cuenta::varchar,
                            tipo_cuenta::varchar,
                            descripcion_cuenta::varchar

                          FROM
                            conta.vint_transaccion_analisis  v
                          where    '||v_parametros.id_tipo_cc::varchar||' =ANY(ids) and '||v_filtro|| ' and ';


              --Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;


            v_consulta:=v_consulta||'
                            group by
                                id_cuenta,
                                codigo_cuenta,
                                descripcion_cuenta,
                                tipo_cuenta ';


			--Definicion de la respuesta

			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

            raise notice '%',v_consulta;
			--Devuelve la respuesta
			return v_consulta;

		end;

    /*********************************
 	#TRANSACCION:  'CONTA_INTCUE_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		admin
 	#FECHA:		01-09-2013 18:10:12
	***********************************/

	elsif(p_transaccion='CONTA_INTCUE_CONT')then

		begin

             if pxp.f_existe_parametro(p_tabla,'id_periodo') then
               v_filtro = ' id_periodo='||v_parametros.id_periodo::varchar;
             elseif pxp.f_existe_parametro(p_tabla,'fecha_ini') then
               v_filtro = ' fecha BETWEEN '''||v_parametros.fecha_ini||'''::date and '''||v_parametros.fecha_fin||'''::Date';
             else
                v_filtro = ' 0=0 ';
             end if;


             v_consulta:='WITH parcial AS (
                                            SELECT
                                                      id_cuenta as id_cuenta,
                                                      sum(importe_debe_mb) as importe_debe_mb,
                                                      sum(importe_haber_mb) as importe_haber_mb,
                                                      sum(importe_debe_mt) as importe_debe_mt,
                                                      sum(importe_haber_mt) as importe_haber_mt,
                                                      sum(importe_debe_ma) as importe_debe_ma,
                                                      sum(importe_haber_ma) as importe_haber_ma
                                                   FROM
                                                      conta.vint_transaccion_analisis  v
                                                   where    '||v_parametros.id_tipo_cc::varchar||' =ANY(ids) and '||v_filtro|| ' and ';

             v_consulta:=v_consulta||v_parametros.filtro;

             v_consulta:= v_consulta|| 'group by
                                                      id_cuenta,
                                                      codigo_cuenta,
                                                      descripcion_cuenta,
                                                      tipo_cuenta  )

                                             SELECT
                                                   count(id_cuenta) as total,
                                                   sum(importe_debe_mb) as importe_debe_mb,
                                                   sum(importe_haber_mb) as importe_haber_mb,
                                                   sum(importe_debe_mt) as importe_debe_mt,
                                                   sum(importe_haber_mt) as importe_haber_mt,
                                                   sum(importe_debe_ma) as importe_debe_ma,
                                                   sum(importe_haber_ma) as importe_haber_ma
                                            FROM parcial';



            raise notice '%',v_consulta;

			--Devuelve la respuesta
			return v_consulta;

		end;

    /*Aumentando para los reportes Ismael Valdivia (09/12/2019)*/

    /*********************************
 	#TRANSACCION:  'CONTA_LISTMAY_SEL'
 	#DESCRIPCION:	listado de transacicones para mostrar el detalle del libro mayor
 	#AUTOR:		Ismael Valdivia
 	#FECHA:		09-12-2019 10:20:00
	***********************************/

	elsif(p_transaccion='CONTA_LISTMAY_SEL')then

    	begin



            v_filtro_ordenes = '0=0';
            /*Filtros para obtener datos*/
            v_filtro_id_auxiliar = '0=0';
            v_filtro_fecha_ini = '0=0';
            v_filtro_fecha_fin = '0=0';
            v_filtro_gestion = '0=0';
            v_filtro_id_centro_costo = '0=0';
            v_filtro_id_partida = '0=0';
            v_filtro_cuentas = '0=0';
            --raise exception 'filtro es %',v_parametros.filtro;
			IF (v_parametros.id_auxiliar is not null)  THEN
            	v_filtro_id_auxiliar = 'transa.id_auxiliar = '||v_parametros.id_auxiliar||'';
            END IF;

            IF  (v_parametros.id_gestion is not null)  THEN
            	v_filtro_gestion = 'per.id_gestion = '||v_parametros.id_gestion||'';
            end if;

            IF  (v_parametros.id_centro_costo is not null)  THEN
            	v_filtro_id_centro_costo = 'transa.id_centro_costo = '||v_parametros.id_centro_costo||'';
            end if;

             IF (v_parametros.id_partida is not null)  THEN
            	v_filtro_id_partida = 'transa.id_partida = '||v_parametros.id_partida||'';
            end if;

             IF (v_parametros.id_cuenta is not null)  THEN
            	v_filtro_cuentas = 'transa.id_cuenta = '||v_parametros.id_cuenta||'';
            end if;

            IF (v_parametros.id_orden_trabajo is not null)  THEN
            	v_filtro_ordenes = 'transa.id_orden_trabajo = '||v_parametros.id_orden_trabajo||'';
            end if;

            /*********************************************************************************************/

           /*Para calcular el saldo por gestion recuperaremos el primer periodo de la gestion seleccionada*/
            select per.fecha_ini into v_fecha_inicio_gestion
            from param.tperiodo per
            where per.id_gestion = v_parametros.id_gestion and per.periodo = 1;
            /*********************************************************************************************/

            /************************LLAMAMOS A LA FUNCION PARA CALCULAR EL SALDO ANTERIOR****************************************/
            select * into v_datos_anterior
            from conta.f_recuperar_saldo_anterior_libro_mayor(v_filtro_cuentas,v_filtro_id_auxiliar,v_parametros.desde,v_filtro_id_partida,v_filtro_id_centro_costo,v_fecha_inicio_gestion,v_filtro_ordenes)
            as(saldo_anterior NUMERIC, total_debe_anterior NUMERIC, total_haber_anterior NUMERIC);
            /*********************************************************************************************************************/

            /*Iniciamos la variable*/
            v_saldo_anterior = 0;
            /***********************/

            /***Consulta para recuperar la cuenta en la cabecera***/
             select cue.nro_cuenta into v_numero_cuenta
              from conta.tint_transaccion transa
              inner join conta.tint_comprobante icbte on icbte.id_int_comprobante = transa.id_int_comprobante
              inner join conta.tcuenta cue on cue.id_cuenta = transa.id_cuenta
              where icbte.estado_reg = 'validado' and cue.id_cuenta = v_parametros.id_cuenta
              limit 1;
            /******************************************************/

             select substring (v_numero_cuenta from 1 for 1) into v_inicial_cuenta;

              if (v_inicial_cuenta = '1') then

                  /*Recuperamos los 3 primero digitos para saber si es una cuenta de comportamiento pasivo o activo*/
                  select substring (v_numero_cuenta from 1 for 3) into v_cuenta_activo_como_pasivo;
                  /*************************************************************************************************/

                  /*Si el numero de cuenta es 124 o 114 pertenece a una cuenta de Activo pero Se comporta como un pasivo la formula es
                  Saldo_anterior + (Haber - Debe) si se comporta como un activo la formula es Saldo_anterior + (Debe - Haber)*/

                      if (v_cuenta_activo_como_pasivo = '124' OR  v_cuenta_activo_como_pasivo = '114') THEN
                      		v_comportamiento = 'pasivo';
                      else
                      		v_comportamiento = 'activo';
                      end if;


              end if;

              /*Si la cuenta inicia con 4 o 6 pertenece a un activo*/
              if (v_inicial_cuenta = '4' or v_inicial_cuenta = '6') then
              		v_comportamiento = 'activo';
              end if;
              /*Si la cuenta inicia con 2 o 3 o 5 pertenece a un pasivo*/
               if (v_inicial_cuenta = '2' or v_inicial_cuenta = '3' or v_inicial_cuenta = '5') then
              		v_comportamiento = 'pasivo';
              end if;

              if (v_inicial_cuenta = '8') then
                  /*Recuperamos los 3 primero digitos para saber si es una cuenta de comportamiento pasivo o activo*/
                  select substring (v_numero_cuenta from 1 for 2) into v_cuenta_acreedora_como_pasivo;
                  /*************************************************************************************************/

                  if (v_cuenta_acreedora_como_pasivo = '81') then
						v_comportamiento = 'activo';
                  elsif (v_cuenta_acreedora_como_pasivo = '82') then
                  		v_comportamiento = 'pasivo';
                  end if;

              end if;

            if (v_comportamiento = 'pasivo') then

            		v_saldo_anterior = COALESCE (v_datos_anterior.total_haber_anterior,0) - COALESCE (v_datos_anterior.total_debe_anterior,0);

            elsif (v_comportamiento = 'activo') then

            		v_saldo_anterior = COALESCE (v_datos_anterior.total_debe_anterior,0) - COALESCE (v_datos_anterior.total_haber_anterior,0);

            end if;

            --Sentencia de la consulta
			v_consulta='
            		    (select
            			0::integer as id_int_transaccion,
						NULL::integer as id_partida,
						NULL::integer as id_centro_costo,
						NULL::integer as id_partida_ejecucion,
						''activo''::varchar as estado_reg,
						NULL::integer as id_int_transaccion_fk,
						0::integer as id_cuenta,
						''''::varchar as glosa,
						NULL::integer as id_int_comprobante,
						NULL::integer as id_auxiliar,
						NULL::integer as id_usuario_reg,
						now()::date as fecha_reg,
						NULL::integer as id_usuario_mod,
						NULL::date as fecha_mod,
						''''::varchar as usr_reg,
						''''::varchar as usr_mod,

                        '||v_datos_anterior.total_debe_anterior||'::numeric as total_debe_anterior,
                        '||v_datos_anterior.total_haber_anterior||'::numeric as total_haber_anterior,

                        ''''::varchar as desc_partida,
						''''::varchar as desc_centro_costo,
						''''::varchar as desc_cuenta,
						''''::varchar desc_auxiliar,
                        ''''::varchar as tipo_partida,
                        NULL::integer as id_orden_trabajo,
                        ''''::varchar as desc_orden,
                        ''''::varchar as nro_cbte,
                        ''''::varchar as nro_tramite,
                        ''''::varchar as nombre_corto,
                       	NULL::date as fecha,
                        ''SALDO ANTERIOR''::varchar as glosa1,
                        ''''::varchar as codigo,

                        /*Recuperando datos sugeridos por lobito*/
                        ''''::varchar as c31,
                        NULL::date as fecha_costo_ini,
                        NULL::date as fecha_costo_fin,
                        /**********************************/

                        ''''::varchar as nro_factura,
                        '||v_saldo_anterior||'::numeric as saldo_anterior)

                        UNION

            			(select
						transa.id_int_transaccion::integer,
						transa.id_partida::integer,
						transa.id_centro_costo::integer,
						transa.id_partida_ejecucion::integer,
						transa.estado_reg::varchar,
						transa.id_int_transaccion_fk::integer,
						transa.id_cuenta::integer,
						transa.glosa::varchar,
						transa.id_int_comprobante::integer,
						transa.id_auxiliar::integer,
						transa.id_usuario_reg::integer,
						transa.fecha_reg::date,
						transa.id_usuario_mod::integer,
						transa.fecha_mod::date,
						usu1.cuenta::varchar as usr_reg,
						usu2.cuenta::varchar as usr_mod,
                        COALESCE(transa.importe_debe_mb,0)::numeric as importe_debe_mb,
                        COALESCE(transa.importe_haber_mb,0)::numeric as importe_haber_mb,
                        (CASE par.sw_movimiento
                        	WHEN ''flujo'' THEN
								''(F) ''||par.codigo || '' - '' || par.nombre_partida
                            ELSE
                            	par.codigo || '' - '' || par.nombre_partida
                        	END)::varchar  as desc_partida,
						(cc.codigo_cc)::varchar as desc_centro_costo,
						(cue.nro_cuenta || '' - '' || cue.nombre_cuenta)::varchar as desc_cuenta,
						(aux.codigo_auxiliar || '' - '' || aux.nombre_auxiliar)::varchar as desc_auxiliar,
                        par.sw_movimiento::varchar as tipo_partida,
                        ot.id_orden_trabajo::integer,
                        ot.desc_orden::varchar,
                        icbte.nro_cbte::varchar,
                        icbte.nro_tramite::varchar,
                        dep.nombre_corto::varchar,
                        icbte.fecha::date,
                        icbte.glosa1::varchar,
                        par.codigo::varchar,

                        /*Recuperando datos sugeridos por lobito*/
                        icbte.c31::varchar,
                        icbte.fecha_costo_ini::date,
                        icbte.fecha_costo_fin::date,
                        /**********************************/

                        COALESCE(string_agg(venta.nro_documento, '',''), '''')::varchar as nro_factura,
						conta.f_calcular_saldo_libro_mayor_pdf(COALESCE(transa.importe_debe_mb,0),COALESCE(transa.importe_haber_mb,0),'''||v_parametros.desde||''','''||v_filtro_cuentas||''','''||v_filtro_ordenes||''','''||v_filtro_id_auxiliar||''','''||v_filtro_id_centro_costo||''','''||v_filtro_id_partida||''','||v_parametros.id_cuenta||','||v_parametros.id_gestion||')::numeric as importe_saldo_mb

						from conta.tint_transaccion transa
                        inner join conta.tint_comprobante icbte on icbte.id_int_comprobante = transa.id_int_comprobante
                        left join conta.tdoc_compra_venta venta on venta.id_int_comprobante = icbte.id_int_comprobante
                        inner join param.tdepto dep on dep.id_depto = icbte.id_depto
                        inner join param.tperiodo per on per.id_periodo = icbte.id_periodo
						inner join segu.tusuario usu1 on usu1.id_usuario = transa.id_usuario_reg

                        inner join conta.tcuenta cue on cue.id_cuenta = transa.id_cuenta
                        inner join conta.tconfig_tipo_cuenta ctc on ctc.tipo_cuenta = cue.tipo_cuenta
                        inner join conta.tconfig_subtipo_cuenta csc on csc.id_config_subtipo_cuenta = cue.id_config_subtipo_cuenta
						left join segu.tusuario usu2 on usu2.id_usuario = transa.id_usuario_mod
						left join pre.tpartida par on par.id_partida = transa.id_partida
						left join param.vcentro_costo cc on cc.id_centro_costo = transa.id_centro_costo
						left join conta.tauxiliar aux on aux.id_auxiliar = transa.id_auxiliar
                        left join conta.torden_trabajo ot on ot.id_orden_trabajo =  transa.id_orden_trabajo
				        where icbte.estado_reg = ''validado''
                        and '||v_filtro_cuentas||'
                        and '||v_filtro_id_auxiliar||'
                        and '||v_filtro_gestion||'
                        and '||v_filtro_id_centro_costo||'
                        and '||v_filtro_id_partida||'
                        and '||v_filtro_ordenes||'
                        and icbte.fecha::date  >= '''||v_parametros.desde||'''
                        and icbte.fecha::date  <= '''||v_parametros.hasta||'''
                        and ';
                        v_consulta:=v_consulta||v_parametros.filtro;

						v_consulta:= v_consulta|| '
			group by transa.id_int_transaccion,
                                        transa.id_partida,
                                        transa.id_centro_costo,
                                        transa.id_partida_ejecucion,
                                        transa.estado_reg,
                                        transa.id_int_transaccion_fk,
                                        transa.id_cuenta,
                                        transa.glosa,
                                        transa.id_int_comprobante,
                                        transa.id_auxiliar,
                                        transa.id_usuario_reg,
                                        transa.fecha_reg,
                                        transa.id_usuario_mod,
                                        transa.fecha_mod,
                                        usu1.cuenta,
                                        usu2.cuenta,
                                        COALESCE(transa.importe_debe_mb,0),
                                        COALESCE(transa.importe_haber_mb,0),
                                        par.nombre_partida,
                                        cc.codigo_cc,
                                        cue.nro_cuenta,
                                        cue.nombre_cuenta,
                                        aux.codigo_auxiliar,
                                        par.sw_movimiento,
                                        ot.id_orden_trabajo,
                                        dep.nombre_corto,
                                        aux.nombre_auxiliar,
                                        icbte.nro_tramite,
                                        icbte.fecha,
                                        icbte.glosa1,
                                        icbte.nro_cbte,
                                        par.codigo,
                                        par.tipo,icbte.c31,
                                        icbte.fecha_costo_ini,
				                        icbte.fecha_costo_fin,
                                        ot.desc_orden
                                        ORDER BY id_int_transaccion ASC)';

                --v_consulta:=v_consulta||' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

			return v_consulta;
		end;

	/*********************************
 	#TRANSACCION:  'CONTA_LISTMAY_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		Ismael Valdivia
 	#FECHA:		01-09-2013 18:10:12
	***********************************/

	elsif(p_transaccion='CONTA_LISTMAY_CONT')then

		begin
            v_cuentas = '0';
            v_filtro_cuentas = '0=0';
            v_filtro_ordenes = '0=0';
            v_filtro_tipo_cc = '0=0';

            /*Filtros para obtener datos*/
            v_filtro_id_auxiliar = '0=0';
            v_filtro_fecha_ini = '0=0';
            v_filtro_fecha_fin = '0=0';
            v_filtro_gestion = '0=0';
            v_filtro_id_centro_costo = '0=0';
            v_filtro_id_partida = '0=0';
            v_filtro_cuentas = '0=0';

            IF (v_parametros.id_auxiliar is not null)  THEN
            	v_filtro_id_auxiliar = 'transa.id_auxiliar = '||v_parametros.id_auxiliar||'';
            END IF;

            IF  (v_parametros.id_gestion is not null)  THEN
            	v_filtro_gestion = 'per.id_gestion = '||v_parametros.id_gestion||'';
            end if;

            IF  (v_parametros.id_centro_costo is not null)  THEN
            	v_filtro_id_centro_costo = 'transa.id_centro_costo = '||v_parametros.id_centro_costo||'';
            end if;

             IF (v_parametros.id_partida is not null)  THEN
            	v_filtro_id_partida = 'transa.id_partida = '||v_parametros.id_partida||'';
            end if;

             IF (v_parametros.id_cuenta is not null)  THEN
            	v_filtro_cuentas = 'transa.id_cuenta = '||v_parametros.id_cuenta||'';
            end if;

             IF (v_parametros.id_orden_trabajo is not null)  THEN
            	v_filtro_ordenes = 'transa.id_orden_trabajo = '||v_parametros.id_orden_trabajo||'';
            end if;

            /*********************************************************************************************/


           /*Para calcular el saldo por gestion recuperaremos el primer periodo de la gestion seleccionada*/
            select per.fecha_ini into v_fecha_inicio_gestion
            from param.tperiodo per
            where per.id_gestion = v_parametros.id_gestion and per.periodo = 1;
            /*********************************************************************************************/

            /************************LLAMAMOS A LA FUNCION PARA CALCULAR EL SALDO ANTERIOR****************************************/
            select * into v_datos_anterior
            from conta.f_recuperar_saldo_anterior_libro_mayor(v_filtro_cuentas,v_filtro_id_auxiliar,v_parametros.desde,v_filtro_id_partida,v_filtro_id_centro_costo,v_fecha_inicio_gestion,v_filtro_ordenes)
            as(saldo_anterior NUMERIC, total_debe_anterior NUMERIC, total_haber_anterior NUMERIC);
            /*********************************************************************************************************************/



             IF  pxp.f_existe_parametro(p_tabla,'id_cuenta')  THEN

                  IF v_parametros.id_cuenta is not NULL THEN

                      WITH RECURSIVE cuenta_rec (id_cuenta, id_cuenta_padre) AS (
                        SELECT cue.id_cuenta, cue.id_cuenta_padre
                        FROM conta.tcuenta cue
                        WHERE cue.id_cuenta = v_parametros.id_cuenta and cue.estado_reg = 'activo'
                      UNION ALL
                        SELECT cue2.id_cuenta, cue2.id_cuenta_padre
                        FROM cuenta_rec lrec
                        INNER JOIN conta.tcuenta cue2 ON lrec.id_cuenta = cue2.id_cuenta_padre
                        where cue2.estado_reg = 'activo'
                      )
                    SELECT  pxp.list(id_cuenta::varchar)
                      into
                        v_cuentas
                    FROM cuenta_rec;


                    v_filtro_cuentas = ' transa.id_cuenta in ('||v_cuentas||') ';

                    /*Aqui aumentamos para calcular el saldo al Final de la grilla*/
                    /*OBTENEMOS EL NUMERO DE CUENTA PARA PONER LAS CONDICIONES DEFINIDAS POR CHARITO*/
                      select cue.nro_cuenta into v_numero_cuenta
                      from conta.tint_transaccion transa
                      inner join conta.tint_comprobante icbte on icbte.id_int_comprobante = transa.id_int_comprobante
                      inner join conta.tcuenta cue on cue.id_cuenta = transa.id_cuenta
                      where icbte.estado_reg = 'validado' and cue.id_cuenta = v_parametros.id_cuenta
                      limit 1;

					if (v_numero_cuenta is not NULL) then

                      select substring (v_numero_cuenta from 1 for 1) into v_inicial_cuenta;

                      if (v_inicial_cuenta = '1') then

                          /*Recuperamos los 3 primero digitos para saber si es una cuenta de comportamiento pasivo o activo*/
                          select substring (v_numero_cuenta from 1 for 3) into v_cuenta_activo_como_pasivo;
                          /*************************************************************************************************/

                          /*Si el numero de cuenta es 124 o 114 pertenece a una cuenta de Activo pero Se comporta como un pasivo la formula es
                          Saldo_anterior + (Haber - Debe) si se comporta como un activo la formula es Saldo_anterior + (Debe - Haber)*/

                              if (v_cuenta_activo_como_pasivo = '124' OR  v_cuenta_activo_como_pasivo = '114') THEN
                                    v_comportamiento = 'pasivo';
                              else
                                    v_comportamiento = 'activo';
                              end if;


                      end if;

                            /*Si la cuenta inicia con 4 o 6 pertenece a un activo*/
                            if (v_inicial_cuenta = '4' or v_inicial_cuenta = '6') then
                                  v_comportamiento = 'activo';
                            end if;
                            /*Si la cuenta inicia con 2 o 3 o 5 pertenece a un pasivo*/
                             if (v_inicial_cuenta = '2' or v_inicial_cuenta = '3' or v_inicial_cuenta = '5') then
                                  v_comportamiento = 'pasivo';
                            end if;

                        if (v_inicial_cuenta = '8') then
                            /*Recuperamos los 3 primero digitos para saber si es una cuenta de comportamiento pasivo o activo*/
                            select substring (v_numero_cuenta from 1 for 2) into v_cuenta_acreedora_como_pasivo;
                            /*************************************************************************************************/

                            if (v_cuenta_acreedora_como_pasivo = '81') then
                                  v_comportamiento = 'activo';
                            elsif (v_cuenta_acreedora_como_pasivo = '82') then
                                  v_comportamiento = 'pasivo';
                            end if;

                        end if;

                     /**********************************************************************/
                     else
                     	v_comportamiento = 'ninguno';
                     end if;
                END IF;

            END IF;




			--Sentencia de la consulta de conteo de registros
			v_consulta:='select
                        count(transa.id_int_transaccion) as total,
                        (sum(COALESCE(transa.importe_debe_mb,0)) + '||v_datos_anterior.total_debe_anterior||') as total_debe,
                        (sum(COALESCE(transa.importe_haber_mb,0)) + '||v_datos_anterior.total_haber_anterior||' ) as total_haber,
                        (CASE
                         	  WHEN '''||v_comportamiento||''' = ''ninguno''  THEN
                              NULL
                              WHEN '''||v_comportamiento||''' = ''activo''  THEN
                              ((sum(COALESCE(transa.importe_debe_mb,0)) + '||v_datos_anterior.total_debe_anterior||')-(sum(COALESCE(transa.importe_haber_mb,0)) + '||v_datos_anterior.total_haber_anterior||' ))
                              ELSE
                              ((sum(COALESCE(transa.importe_haber_mb,0)) + '||v_datos_anterior.total_haber_anterior||' )-(sum(COALESCE(transa.importe_debe_mb,0)) + '||v_datos_anterior.total_debe_anterior||'))
                        END) as total_saldo
					    from conta.tint_transaccion transa
                        inner join conta.tint_comprobante icbte on icbte.id_int_comprobante = transa.id_int_comprobante
                        inner join param.tperiodo per on per.id_periodo = icbte.id_periodo
                        --left join conta.tdoc_compra_venta venta on venta.id_int_comprobante = icbte.id_int_comprobante
						where icbte.estado_reg = ''validado''
                              and ' ||v_filtro_cuentas||'
                              and '||v_filtro_ordenes||'
                              and';

			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
            raise notice '%',v_consulta;

			--Devuelve la respuesta
			return v_consulta;

		end;




	/*********************************
 	#TRANSACCION:  'CONTA_REPLIBMAY_SEL'
 	#DESCRIPCION:	Consulta para obtener los datos y sacar reporte del libro mayor
 	#AUTOR:		Ismael Valdivia
 	#FECHA:		03-12-2019 11:15:00
	***********************************/

	elsif(p_transaccion='CONTA_REPLIBMAY_SEL')then

    	begin

            v_cuentas = '0';
            v_ordenes = '0';
            v_tipo_cc = '0';
            v_filtro_cuentas = '0=0';
            v_filtro_ordenes = '0=0';
            v_filtro_tipo_cc = '0=0';

            /*Filtros para obtener datos*/
            v_id_auxiliar = '0';
            v_fecha_ini = '0';
            v_fecha_fin = '0';
            v_gestion = '0';
            v_id_centro_costo = '0';
            v_id_partida = '0';


            v_filtro_id_auxiliar = '0=0';
            v_filtro_fecha_ini = '0=0';
            v_filtro_fecha_fin = '0=0';
            v_filtro_gestion = '0=0';
            v_filtro_id_centro_costo = '0=0';
            v_filtro_id_partida = '0=0';

            IF (v_parametros.id_auxiliar is not null)  THEN
            	v_filtro_id_auxiliar = 'transa.id_auxiliar = '||v_parametros.id_auxiliar||'';
            END IF;

            IF  (v_parametros.id_gestion is not null)  THEN
            	v_filtro_gestion = 'per.id_gestion = '||v_parametros.id_gestion||'';
            end if;

            IF  (v_parametros.id_centro_costo is not null)  THEN
            	v_filtro_id_centro_costo = 'transa.id_centro_costo = '||v_parametros.id_centro_costo||'';
            end if;

             IF (v_parametros.id_partida is not null)  THEN
            	v_filtro_id_partida = 'transa.id_partida = '||v_parametros.id_partida||'';
            end if;

             IF (v_parametros.id_orden_trabajo is not null)  THEN
            	v_filtro_ordenes = 'transa.id_orden_trabajo = '||v_parametros.id_orden_trabajo||'';
            end if;

             /****************************/

             IF  pxp.f_existe_parametro(p_tabla,'id_cuenta')  THEN

                  IF v_parametros.id_cuenta is not NULL THEN

                      WITH RECURSIVE cuenta_rec (id_cuenta, id_cuenta_padre) AS (
                        SELECT cue.id_cuenta, cue.id_cuenta_padre
                        FROM conta.tcuenta cue
                        WHERE cue.id_cuenta = v_parametros.id_cuenta and cue.estado_reg = 'activo'
                      UNION ALL
                        SELECT cue2.id_cuenta, cue2.id_cuenta_padre
                        FROM cuenta_rec lrec
                        INNER JOIN conta.tcuenta cue2 ON lrec.id_cuenta = cue2.id_cuenta_padre
                        where cue2.estado_reg = 'activo'
                      )
                    SELECT  pxp.list(id_cuenta::varchar)
                      into
                        v_cuentas
                    FROM cuenta_rec;



                    v_filtro_cuentas = ' transa.id_cuenta in ('||v_cuentas||') ';
                END IF;

            END IF;


            --Sentencia de la consulta
			v_consulta ='select

                                        /*Recuperando datos sugeridos por lobito*/
                                        icbte.c31,
                                        icbte.nro_tramite,
                                        icbte.fecha_costo_ini,
				                        icbte.fecha_costo_fin,
                                        ot.desc_orden,
                                        /**********************************/

                                        COALESCE(transa.importe_debe_mb,0) as importe_debe_mb,
                                        COALESCE(transa.importe_haber_mb,0) as importe_haber_mb,
                                        icbte.fecha,
                                        icbte.glosa1,
                                        icbte.nro_cbte,
                                        par.codigo,
                                        COALESCE(string_agg(venta.nro_documento, '',''), '''')::varchar as nro_factura,
                                        conta.f_calcular_saldo_libro_mayor_pdf(COALESCE(transa.importe_debe_mb,0),COALESCE(transa.importe_haber_mb,0),'''||v_parametros.desde||''','''||v_filtro_cuentas||''','''||v_filtro_ordenes||''','''||v_filtro_id_auxiliar||''','''||v_filtro_id_centro_costo||''','''||v_filtro_id_partida||''','||v_parametros.id_cuenta||','||v_parametros.id_gestion||')::numeric as importe_saldo_mb

            			from conta.tint_transaccion transa
                        inner join conta.tint_comprobante icbte on icbte.id_int_comprobante = transa.id_int_comprobante
                        left join conta.tdoc_compra_venta venta on venta.id_int_comprobante = icbte.id_int_comprobante
                        inner join param.tdepto dep on dep.id_depto = icbte.id_depto
                        inner join param.tperiodo per on per.id_periodo = icbte.id_periodo
						inner join segu.tusuario usu1 on usu1.id_usuario = transa.id_usuario_reg

                        inner join conta.tcuenta cue on cue.id_cuenta = transa.id_cuenta
                        inner join conta.tconfig_tipo_cuenta ctc on ctc.tipo_cuenta = cue.tipo_cuenta
                        inner join conta.tconfig_subtipo_cuenta csc on csc.id_config_subtipo_cuenta = cue.id_config_subtipo_cuenta
						left join segu.tusuario usu2 on usu2.id_usuario = transa.id_usuario_mod
						left join pre.tpartida par on par.id_partida = transa.id_partida
						left join param.vcentro_costo cc on cc.id_centro_costo = transa.id_centro_costo
						left join conta.tauxiliar aux on aux.id_auxiliar = transa.id_auxiliar
                        left join conta.torden_trabajo ot on ot.id_orden_trabajo =  transa.id_orden_trabajo
				        where icbte.estado_reg = ''validado''
                              and ' ||v_filtro_cuentas||'
                              and '||v_filtro_ordenes||'
                                                            and '||v_filtro_id_auxiliar||'
                                                            and '||v_filtro_gestion||'
                                                            and '||v_filtro_id_centro_costo||'
                                                            and '||v_filtro_id_partida||'
                                                        	and icbte.fecha::date  >= '''||v_parametros.desde||'''
                                                            and icbte.fecha::date  <= '''||v_parametros.hasta||'''
                                                            and ';
                              v_consulta:=v_consulta||v_parametros.filtro;
                              v_consulta:= v_consulta|| '
                                  group by transa.id_int_transaccion,
                                        transa.id_partida,
                                        transa.id_centro_costo,
                                        transa.id_partida_ejecucion,
                                        transa.estado_reg,
                                        transa.id_int_transaccion_fk,
                                        transa.id_cuenta,
                                        transa.glosa,
                                        transa.id_int_comprobante,
                                        transa.id_auxiliar,
                                        transa.id_usuario_reg,
                                        transa.fecha_reg,
                                        transa.id_usuario_mod,
                                        transa.fecha_mod,
                                        usu1.cuenta,
                                        usu2.cuenta,
                                        COALESCE(transa.importe_debe_mb,0),
                                        COALESCE(transa.importe_haber_mb,0),
                                        icbte.nro_tramite,
                                        icbte.fecha,
                                        icbte.glosa1,
                                        icbte.nro_cbte,
                                        par.codigo,
                                        par.tipo,
                                        icbte.c31,
                                        icbte.nro_tramite,
                                        icbte.fecha_costo_ini,
				                        icbte.fecha_costo_fin,
                                        ot.desc_orden
                                        order by transa.id_int_transaccion asc';


			--Devuelve la respuesta
			return v_consulta;

		end;


 /*********************************
 	#TRANSACCION:  'CONTA_ANTELIBMAY_SEL'
 	#DESCRIPCION:	Consulta para recuperar el saldo anterior
 	#AUTOR:		Ismael Valdivia
 	#FECHA:		03-12-2019 11:15:00
	***********************************/

	elsif(p_transaccion='CONTA_ANTELIBMAY_SEL')then

    	begin

            v_cuentas = '0';
            v_ordenes = '0';
            v_tipo_cc = '0';
            v_filtro_cuentas = '0=0';
            v_filtro_ordenes = '0=0';
            v_filtro_tipo_cc = '0=0';

            /*Filtros para obtener datos*/
            v_id_auxiliar = '0';
            v_fecha_ini = '0';
            v_fecha_fin = '0';
            v_gestion = '0';
            v_id_centro_costo = '0';
            v_id_partida = '0';


            v_filtro_id_auxiliar = '0=0';
            v_filtro_fecha_ini = '0=0';
            v_filtro_fecha_fin = '0=0';
            v_filtro_gestion = '0=0';
            v_filtro_id_centro_costo = '0=0';
            v_filtro_id_partida = '0=0';


            IF (v_parametros.id_auxiliar is not null)  THEN
            	v_filtro_id_auxiliar = 'transa.id_auxiliar = '||v_parametros.id_auxiliar||'';
            END IF;

            IF  (v_parametros.id_gestion is not null)  THEN
            	v_filtro_gestion = 'per.id_gestion = '||v_parametros.id_gestion||'';
            end if;

            IF  (v_parametros.id_centro_costo is not null)  THEN
            	v_filtro_id_centro_costo = 'transa.id_centro_costo = '||v_parametros.id_centro_costo||'';
            end if;

             IF (v_parametros.id_partida is not null)  THEN
            	v_filtro_id_partida = 'transa.id_partida = '||v_parametros.id_partida||'';
            end if;

            IF (v_parametros.id_orden_trabajo is not null)  THEN
            	v_filtro_ordenes = 'transa.id_orden_trabajo = '||v_parametros.id_orden_trabajo||'';
            end if;

             /****************************/

             IF  pxp.f_existe_parametro(p_tabla,'id_cuenta')  THEN

                  IF v_parametros.id_cuenta is not NULL THEN

                      WITH RECURSIVE cuenta_rec (id_cuenta, id_cuenta_padre) AS (
                        SELECT cue.id_cuenta, cue.id_cuenta_padre
                        FROM conta.tcuenta cue
                        WHERE cue.id_cuenta = v_parametros.id_cuenta and cue.estado_reg = 'activo'
                      UNION ALL
                        SELECT cue2.id_cuenta, cue2.id_cuenta_padre
                        FROM cuenta_rec lrec
                        INNER JOIN conta.tcuenta cue2 ON lrec.id_cuenta = cue2.id_cuenta_padre
                        where cue2.estado_reg = 'activo'
                      )
                    SELECT  pxp.list(id_cuenta::varchar)
                      into
                        v_cuentas
                    FROM cuenta_rec;



                    v_filtro_cuentas = ' transa.id_cuenta in ('||v_cuentas||') ';
                END IF;

            END IF;


            /*Ponemos condiciones para recuperar el numero de cuenta*/
            select cue.nro_cuenta into v_numero_cuenta
              from conta.tint_transaccion transa
              inner join conta.tint_comprobante icbte on icbte.id_int_comprobante = transa.id_int_comprobante
              inner join conta.tcuenta cue on cue.id_cuenta = transa.id_cuenta
              where icbte.estado_reg = 'validado' and cue.id_cuenta = v_parametros.id_cuenta
              limit 1;

            select substring (v_numero_cuenta from 1 for 1) into v_inicial_cuenta;


              if (v_inicial_cuenta = '1') then

                  /*Recuperamos los 3 primero digitos para saber si es una cuenta de comportamiento pasivo o activo*/
                  select substring (v_numero_cuenta from 1 for 3) into v_cuenta_activo_como_pasivo;
                  /*************************************************************************************************/

                  /*Si el numero de cuenta es 124 o 114 pertenece a una cuenta de Activo pero Se comporta como un pasivo la formula es
                  Saldo_anterior + (Haber - Debe) si se comporta como un activo la formula es Saldo_anterior + (Debe - Haber)*/

                      if (v_cuenta_activo_como_pasivo = '124' OR  v_cuenta_activo_como_pasivo = '114') THEN
                      		v_comportamiento = 'pasivo';
                      else
                      		v_comportamiento = 'activo';
                      end if;


              end if;

              /*Si la cuenta inicia con 4 o 6 pertenece a un activo*/
              if (v_inicial_cuenta = '4' or v_inicial_cuenta = '6') then
              		v_comportamiento = 'activo';
              end if;
              /*Si la cuenta inicia con 2 o 3 o 5 pertenece a un pasivo*/
               if (v_inicial_cuenta = '2' or v_inicial_cuenta = '3' or v_inicial_cuenta = '5') then
              		v_comportamiento = 'pasivo';
              end if;

              if (v_inicial_cuenta = '8') then
                  /*Recuperamos los 3 primero digitos para saber si es una cuenta de comportamiento pasivo o activo*/
                  select substring (v_numero_cuenta from 1 for 2) into v_cuenta_acreedora_como_pasivo;
                  /*************************************************************************************************/

                  if (v_cuenta_acreedora_como_pasivo = '81') then
						v_comportamiento = 'activo';
                  elsif (v_cuenta_acreedora_como_pasivo = '82') then
                  		v_comportamiento = 'pasivo';
                  end if;

              end if;


             /*Quitando esta condicion para recuperar el saldo anterior por gestion
             posiblemente quitar
             if (v_inicial_cuenta = '4' or v_inicial_cuenta = '5' or v_inicial_cuenta = '6') then
             		v_consulta = 'select 0::numeric as saldo_anterior,
                    					 0::numeric as total_debe_anterior,
                        				 0::numeric as total_haber_anterior';   */

             /*Para calcular el saldo por gestion recuperaremos el primer periodo de la gestion seleccionada*/
 			  select per.fecha_ini into v_fecha_inicio_gestion
              from param.tperiodo per
              where per.id_gestion = v_parametros.id_gestion and per.periodo = 1;
              /*********************************************************************************************/


             --else

             /*Si el comportamiento es como un pasivo ejecutamos la siguiente consulta*/
               if (v_comportamiento = 'pasivo') then
               v_consulta ='select
                          COALESCE((SUM (COALESCE(transa.importe_haber_mb,0)) - SUM (COALESCE(transa.importe_debe_mb,0))),0)::numeric as saldo_anterior,
                          COALESCE((SUM (COALESCE(transa.importe_debe_mb,0))),0) as total_debe_anterior,
                          COALESCE((SUM (COALESCE(transa.importe_haber_mb,0))),0) as total_haber_anterior


                          from conta.tint_transaccion transa
                          inner join conta.tint_comprobante icbte on icbte.id_int_comprobante = transa.id_int_comprobante
                          inner join param.tperiodo per on per.id_periodo = icbte.id_periodo
                          where icbte.estado_reg = ''validado''
                                and ' ||v_filtro_cuentas||'
                                and '||v_filtro_ordenes||'
                                and '||v_filtro_id_auxiliar||'
                                and '||v_filtro_id_centro_costo||'
                                and '||v_filtro_id_partida||'
                                and icbte.fecha::date  >= '''||v_fecha_inicio_gestion||'''
                                and icbte.fecha::date  < '''||v_parametros.desde||'''
                                ';
               elsif (v_comportamiento = 'activo') then
                  v_consulta ='select
                          COALESCE((SUM (COALESCE(transa.importe_debe_mb,0)) - SUM (COALESCE(transa.importe_haber_mb,0))),0)::numeric as saldo_anterior,
                          COALESCE((SUM (COALESCE(transa.importe_debe_mb,0))),0) as total_debe_anterior,
                          COALESCE((SUM (COALESCE(transa.importe_haber_mb,0))),0) as total_haber_anterior


                          from conta.tint_transaccion transa
                          inner join conta.tint_comprobante icbte on icbte.id_int_comprobante = transa.id_int_comprobante
                          inner join param.tperiodo per on per.id_periodo = icbte.id_periodo
                          where icbte.estado_reg = ''validado''
                                and ' ||v_filtro_cuentas||'
                                and '||v_filtro_ordenes||'
                                and '||v_filtro_id_auxiliar||'
                                and '||v_filtro_id_centro_costo||'
                                and '||v_filtro_id_partida||'
                                and icbte.fecha::date  >= '''||v_fecha_inicio_gestion||'''
                                and icbte.fecha::date  < '''||v_parametros.desde||'''
                                ';
               end if;


			--end if;

			return v_consulta;

		end;


        /*********************************
 	#TRANSACCION:  'CONTA_CABELIBMAY_SEL'
 	#DESCRIPCION:	Consulta para obtener los datos y sacar reporte del libro mayor
 	#AUTOR:		Ismael Valdivia
 	#FECHA:		12-12-2019 09:55:00
	***********************************/

	elsif(p_transaccion='CONTA_CABELIBMAY_SEL')then

    	begin
        	/*Recuperamos la cabezera de acuerdo a los parametros que se envian*/

            if (v_parametros.id_cuenta is not null) then
        	/***Consulta para recuperar la cuenta en la cabezera***/
              select
              LIST (DISTINCT(cue.nro_cuenta || ' - ' || cue.nombre_cuenta)) as desc_cuenta into v_desc_cuenta
              from conta.tcuenta cue
              where cue.estado_reg = 'activo'
              and cue.id_cuenta = v_parametros.id_cuenta;
            else
            	v_desc_cuenta = '';
            end if;
              /******************************************************/

            IF (v_parametros.id_partida is not null) then
              /***Consulta para recuperar la partidad en la cabezera***/
              select
              LIST (DISTINCT CASE par.sw_movimiento
              WHEN 'flujo' THEN
              '(F) '||par.codigo || ' - ' || par.nombre_partida
              ELSE
              par.codigo || ' - '|| par.nombre_partida
              END ) as desc_partida into v_desc_partida
              from pre.tpartida par
              where par.estado_reg = 'activo'
              and par.id_partida = v_parametros.id_partida;
              /*******************************************************/
            else
              v_desc_partida = '';
            end if;

            IF (v_parametros.id_centro_costo is not null) then
              /****Consulta para recuperar el centro de costo en la cabecera*****/
              select
              LIST (DISTINCT cc.codigo_cc) as desc_centro_costo into v_desc_centro_costo
              from param.vcentro_costo cc
              where cc.estado_reg = 'activo' and cc.id_centro_costo = v_parametros.id_centro_costo;
              /******************************************************************/
            ELSE
              v_desc_centro_costo = '';
            end if;

            IF (v_parametros.id_auxiliar is not null) then
              /***Consulta para recuperar el auxiliar en la cabecera***/
              select
              LIST (DISTINCT(aux.codigo_auxiliar || ' - ' || aux.nombre_auxiliar)) as desc_auxiliar into v_desc_auxiliar
              from conta.tauxiliar aux
              where aux.estado_reg = 'activo'
              and aux.id_auxiliar = v_parametros.id_auxiliar;
            else
              v_desc_auxiliar = '';
            end if;
              /**********************************************************************************/

            --Sentencia de la consulta
			v_consulta ='select '''||v_desc_cuenta||'''::varchar as desc_cuenta,
            				    '''||v_desc_partida||'''::varchar as desc_partida,
                                '''||v_desc_centro_costo||'''::varchar as desc_centro_costo,
                                '''||v_desc_auxiliar||'''::varchar as desc_auxiliar';
			--Devuelve la respuesta


			return v_consulta;

		end;

/*****************************************************************************************************************************************************************/

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

ALTER FUNCTION conta.ft_int_transaccion_sel (p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
  OWNER TO postgres;
