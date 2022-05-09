CREATE OR REPLACE FUNCTION conta.ft_entrega_sel (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/************************************************************************** SISTEMA:        Sistema de Contabilidad FUNCION:         conta.ft_entrega_sel DESCRIPCION:   Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'conta.tentrega'
 AUTOR:          (admin)
 FECHA:            17-11-2016 19:50:19
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
  v_filtro varchar;
  v_record record;
  v_id_usuario_rev record;

BEGIN

  v_nombre_funcion = 'conta.ft_entrega_sel';
  v_parametros = pxp.f_get_record(p_tabla);

  /*********************************
     #TRANSACCION:  'CONTA_ENT_SEL'
     #DESCRIPCION:    Consulta de datos
     #AUTOR:        admin
     #FECHA:        17-11-2016 19:50:19
    ***********************************/

  if(p_transaccion='CONTA_ENT_SEL')then

    begin
      --Sentencia de la consulta
      v_filtro = '';
      SELECT vfcl.id_oficina,
             vfcl.nombre_cargo,
             vfcl.oficina_nombre,
             tf.id_funcionario,
             vfcl.desc_funcionario1
      INTO v_record
      FROM segu.tusuario tu
           INNER JOIN orga.tfuncionario tf on tf.id_persona = tu.id_persona
           INNER JOIN orga.vfuncionario_cargo_lugar vfcl on vfcl.id_funcionario
             = tf.id_funcionario
      WHERE tu.id_usuario = p_id_usuario;

      if p_administrador     THEN
        v_filtro = ' 0=0 AND ';

        ELSIF v_parametros.pes_estado = 'EntregaConsulta' THEN
        select u.id_persona,
               count(u.id_usuario)::varchar as cant_reg
        into v_id_usuario_rev
        from wf.testado_wf es
             inner JOIN orga.tfuncionario fu on fu.id_funcionario =
               es.id_funcionario
             inner join segu.tusuario u on u.id_persona = fu.id_persona
             LEFT JOIN wf.testado_wf te ON te.id_estado_anterior =
               es.id_estado_wf
             LEFT JOIN conta.tentrega e on e.id_estado_wf = es.id_estado_wf
        WHERE e.estado = 'vbconta'
        GROUP BY u.id_usuario;

        IF(v_id_usuario_rev.cant_reg IS NULL)THEN

          v_filtro = 'tew.id_funcionario = '||v_record.id_funcionario||' AND  ';
          ELSE

          v_filtro = '(ent.id_usuario_mod = '||v_id_usuario_rev.id_persona||
          ' OR  tew.id_funcionario = '||v_record.id_funcionario||') AND';
        END IF;
      END IF;

      v_consulta:='select
                            ent.id_entrega,
                            ent.fecha_c31,
                            ent.c31,
                            ent.estado,
                            ent.estado_reg,
                            ent.id_usuario_ai,
                            ent.usuario_ai,
                            ent.fecha_reg,
                            ent.id_usuario_reg,
                            ent.fecha_mod,
                            ent.id_usuario_mod,
                            usu1.cuenta as usr_reg,
                            usu2.cuenta as usr_mod,
                            ent.id_depto_conta,
                            ent.id_estado_wf,
                            ent.id_proceso_wf,
                            com.nro_tramite::varchar,
                            com.desc_moneda::varchar,
                            (select sum(pp.monto)
                             from conta.tentrega_det ende
                             inner join tes.tplan_pago pp on pp.id_int_comprobante = ende.id_int_comprobante
                             where ende.id_entrega = ent.id_entrega) as monto,
                             com.tipo_cambio_2,
                             to_char(com.fecha,''DD/MM/YYYY'')::varchar as fecha,
							com.id_clase_comprobante,
							ent.id_service_request,
                            com.localidad,
                            ent.glosa,
                            ent.tipo,
                            ent.validado,
                            tic.tipo_cbte,
                            coalesce(tic.reversion,''no'') as reversion,
                            ent.nro_deposito::varchar,
                            ent.fecha_deposito::date,
                            ent.monto_deposito::numeric,
                            ent.monto::numeric monto_total

						from conta.tentrega ent
						inner join segu.tusuario usu1 on usu1.id_usuario = ent.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = ent.id_usuario_mod
				        inner join conta.tentrega_det det on det.id_entrega = ent.id_entrega
                        inner join conta.vint_comprobante com on com.id_int_comprobante = det.id_int_comprobante
						inner join conta.tint_comprobante tic on tic.id_int_comprobante = com.id_int_comprobante
                        where  '||v_filtro;

      --Definicion de la respuesta
      v_consulta:=v_consulta||v_parametros.filtro;
      v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' ||
      v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad ||
      ' offset ' || v_parametros.puntero;

      --Devuelve la respuesta
      return v_consulta;

    end;

    /*********************************
     #TRANSACCION:  'CONTA_ENT_CONT'
     #DESCRIPCION:    Conteo de registros
     #AUTOR:        admin
     #FECHA:        17-11-2016 19:50:19
    ***********************************/

    elsif(p_transaccion='CONTA_ENT_CONT')then

    begin
      --Sentencia de la consulta de conteo de registros
      v_consulta:='select count(ent.id_entrega)
					    from conta.tentrega ent
						inner join segu.tusuario usu1 on usu1.id_usuario = ent.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = ent.id_usuario_mod
				        inner join conta.tentrega_det det on det.id_entrega = ent.id_entrega
                        inner join conta.vint_comprobante com on com.id_int_comprobante = det.id_int_comprobante
                        inner join conta.tint_comprobante tic on tic.id_int_comprobante = com.id_int_comprobante
                       	where ';

      --Definicion de la respuesta
      v_consulta:=v_consulta||v_parametros.filtro;

      --Devuelve la respuesta
      return v_consulta;

    end;

    --detalle del comprobante
    /*********************************
     #TRANSACCION:  'CONTA_DET_COM'
     #DESCRIPCION:    Consulta de datos
     #AUTOR:        admin
     #FECHA:        23-05-2018 10:38:19
    ***********************************/

     elseif(p_transaccion='CONTA_DETCOM_SEL')then
      BEGIN

        v_consulta:='select
                            ent.id_entrega,
                            coalesce(ent.fecha_c31,com.fecha_c31),
                            coalesce( ent.c31,com.c31::varchar),
                            ent.estado,
                            ent.estado_reg,
                            ent.id_usuario_ai,
                            ent.usuario_ai,
                            com.fecha,
                            ent.id_usuario_reg,
                            ent.fecha_mod,
                            ent.id_usuario_mod,
                            usu1.cuenta as usr_reg,
                            usu2.cuenta as usr_mod,
                            ent.id_depto_conta,
                            com.id_estado_wf,
                            com.id_proceso_wf,
                            com.nro_tramite::varchar,
                            com.beneficiario::varchar,
    						com.nro_cbte::varchar,
                            com.desc_clase_comprobante::varchar,
                            com.glosa1::varchar,
                            com.desc_depto::varchar,
                            com.c31::varchar as c31comp,
                            com.fecha_c31 as fecha_c31comp,
                            com.id_int_comprobante,
                            ccom.id_clase_comprobante,
                            com.usr_reg as usr_reg_comprobante,
                            --tran.importe_haber
                            sum (tran.importe_haber) as total_importe,
                            com.id_tipo_relacion_comprobante,
                            com.desc_tipo_relacion_comprobante


						from conta.vint_comprobante com
                        inner join conta.tclase_comprobante ccom on ccom.id_clase_comprobante = com.id_clase_comprobante
                        inner join conta.tint_transaccion tran on tran.id_int_comprobante = com.id_int_comprobante
                        left join conta.tentrega_det det on det.id_int_comprobante = com.id_int_comprobante
                        left join conta.tentrega ent on ent.id_entrega = det.id_entrega
                        left join segu.tusuario usu1 on usu1.id_usuario = ent.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = ent.id_usuario_mod
				        where com.nro_cbte is not null and

                         ';


        --Definicion de la respuesta
        v_consulta:=v_consulta||v_parametros.filtro;
        v_consulta:=v_consulta||'group by  ent.id_entrega,
        									com.fecha,
                                            usu1.cuenta,
                                            usu2.cuenta,
                                            com.nro_tramite,
                                            com.beneficiario,
                                            com.nro_cbte,
                                            com.desc_clase_comprobante,
                                            com.glosa1,
                                            com.desc_depto,
                                            com.c31,
                                            com.fecha_c31,
                                            com.id_int_comprobante,
                                            ccom.id_clase_comprobante,
                                            com.usr_reg,
                                            com.id_estado_wf,
                                            com.id_proceso_wf,
                                            com.id_tipo_relacion_comprobante,
                            				com.desc_tipo_relacion_comprobante
                                            '||' order by ' ||v_parametros.ordenacion|| ' ' ||
        v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad ||
        ' offset ' || v_parametros.puntero;



        RAISE NOTICE '%',v_consulta;
        --RAISE EXCEPTION 'el valor es: %',v_consulta ;
        --Devuelve la respuesta
        return v_consulta;

      end;
      /*********************************
     #TRANSACCION:  'CONTA_DETCOM_CONT'
     #DESCRIPCION:    Conteo de registros
     #AUTOR:        admin
     #FECHA:        23-05-2018 10:38:19
    ***********************************/

    elsif(p_transaccion='CONTA_DETCOM_CONT')then

    begin
      --Sentencia de la consulta de conteo de registros
      v_consulta:='select count(com.id_int_comprobante)

                        from conta.vint_comprobante com
                      	inner join conta.tclase_comprobante ccom on ccom.id_clase_comprobante = com.id_clase_comprobante
                        --inner join conta.tint_transaccion tran on tran.id_int_comprobante = com.id_int_comprobante
                        left join conta.tentrega_det det on det.id_int_comprobante = com.id_int_comprobante
                        left join conta.tentrega ent on ent.id_entrega = det.id_entrega
                        left join segu.tusuario usu1 on usu1.id_usuario = ent.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = ent.id_usuario_mod
				        where
                          ';

      --Definicion de la respuesta
      v_consulta:=v_consulta||v_parametros.filtro;
/*v_consulta:=v_consulta||'group by com.fecha,
                                            usu1.cuenta,
                                            usu2.cuenta,
                                            com.nro_tramite,
                                            com.beneficiario,
                                            com.nro_cbte,
                                            com.desc_clase_comprobante,
                                            com.glosa1,
                                            com.desc_depto,
                                            com.c31,
                                            com.fecha_c31,
                                            com.id_int_comprobante';*/

      --Devuelve la respuesta
      return v_consulta;

    end;
      ----end Detalle del comprobante

    /*********************************
     #TRANSACCION:  'CONTA_REPENT_SEL'
     #DESCRIPCION:    Consulta de datos para reporte de entrega
     #AUTOR:        RAC KPLIAN
     #FECHA:        23-11-2016 19:50:19
    ***********************************/
    elseif(p_transaccion='CONTA_REPENT_SEL')then

    begin
      --Sentencia de la consulta
      --12-06-2020 (may) OBSERVACIONES SOBRE EL FULL JOIN ESTE HACE REPETIR REGISTRO PARA EL REPORTE DE ENTREGAS
      v_consulta:=' SELECT  ent.id_entrega,
                             ent.estado::varchar,
                             ent.c31::varchar,
                             ent.id_depto_conta,
                             ent.fecha_c31,
                             par.codigo::varchar,
                             par.nombre_partida::varchar,
                             trd.importe_debe_mb,
                             trd.importe_haber_mb,
                             CASE
                               WHEN trd.factor_reversion > 0::numeric THEN trd.importe_debe_mb /(1::
                                 numeric - trd.factor_reversion)
                               ELSE trd.importe_debe_mb
                             END AS importe_debe_mb_completo,
                             CASE
                               WHEN trd.factor_reversion > 0::numeric THEN trd.importe_haber_mb /(1
                                 ::numeric - trd.factor_reversion)
                               ELSE trd.importe_haber_mb
                             END AS importe_haber_mb_completo,
                             trd.importe_gasto_mb,
                             trd.importe_recurso_mb,
                             trd.factor_reversion,
                             pr.codigo_cc::varchar,
                             cp.codigo_categoria::varchar,
                             cg.codigo::varchar AS codigo_cg,
                             cg.nombre::varchar AS nombre_cg,
                             cbt.beneficiario::varchar,
                             cbt.glosa1::varchar,
                             cbt.id_int_comprobante,
                             trd.id_int_comprobante AS id_int_comprobante_dev,
                             COALESCE(cb.nro_cuenta, ''SIN CUENTA''::character varying) AS nro_cuenta,
                             cb.nombre_institucion,
                             trd.importe_debe,
                             trd.importe_haber,
                             mm.moneda AS moneda_original
                      FROM conta.tentrega ent
                           JOIN conta.tentrega_det ed ON ed.id_entrega = ent.id_entrega
                           JOIN conta.tint_comprobante cbt ON cbt.id_int_comprobante =
                             ed.id_int_comprobante
                           JOIN tes.tplan_pago pg ON pg.id_int_comprobante = cbt.id_int_comprobante
                           JOIN tes.tplan_pago dev ON dev.id_plan_pago = pg.id_plan_pago_fk
                           JOIN conta.tint_transaccion trd ON trd.id_int_comprobante =
                             dev.id_int_comprobante
                           JOIN pre.tpartida par ON par.id_partida = trd.id_partida
                           JOIN pre.vpresupuesto_cc pr ON pr.id_centro_costo = trd.id_centro_costo
                           JOIN pre.vcategoria_programatica cp ON cp.id_categoria_programatica =
                             pr.id_categoria_prog
                           LEFT JOIN pre.tclase_gasto_partida cgp ON cgp.id_partida = par.id_partida
                           LEFT JOIN pre.tclase_gasto cg ON cg.id_clase_gasto = cgp.id_clase_gasto
                           LEFT JOIN tes.vcuenta_bancaria cb ON cb.id_cuenta_bancaria =
                             cbt.id_cuenta_bancaria
                           LEFT JOIN param.tmoneda mm ON trd.id_moneda = mm.id_moneda
                      WHERE par.sw_movimiento::text = ''presupuestaria''::text
                      and ent.id_entrega = '||v_parametros.id_entrega||'
                      UNION ALL
                      SELECT ent.id_entrega,
                             ent.estado::varchar,
                             ent.c31::varchar,
                             ent.id_depto_conta,
                             ent.fecha_c31,
                             par.codigo::varchar,
                             par.nombre_partida::varchar,
                             trp.importe_debe_mb,
                             trp.importe_haber_mb,
                             CASE
                               WHEN trp.factor_reversion > 0::numeric THEN trp.importe_debe_mb /(1::
                                 numeric - trp.factor_reversion)
                               ELSE trp.importe_debe_mb
                             END AS importe_debe_mb_completo,
                             CASE
                               WHEN trp.factor_reversion > 0::numeric THEN trp.importe_haber_mb /(1
                                 ::numeric - trp.factor_reversion)
                               ELSE trp.importe_haber_mb
                             END AS importe_haber_mb_completo,
                             trp.importe_gasto_mb,
                             trp.importe_recurso_mb,
                             trp.factor_reversion,
                             pr.codigo_cc::varchar,
                             cp.codigo_categoria::varchar,
                             cg.codigo::varchar AS codigo_cg,
                             cg.nombre::varchar AS nombre_cg,
                             cbt.beneficiario::varchar,
                             cbt.glosa1::varchar,
                             cbt.id_int_comprobante,
                             trp.id_int_comprobante AS id_int_comprobante_dev,
                             COALESCE(cb.nro_cuenta, ''SIN CUENTA''::character varying) AS nro_cuenta,
                             cb.nombre_institucion,
                             trp.importe_debe,
                             trp.importe_haber,
                             m.moneda AS moneda_original
                      FROM conta.tentrega ent
                           JOIN conta.tentrega_det ed ON ed.id_entrega = ent.id_entrega
                           JOIN conta.tint_comprobante cbt ON cbt.id_int_comprobante =
                             ed.id_int_comprobante
                           JOIN conta.tint_transaccion trp ON trp.id_int_comprobante =
                             cbt.id_int_comprobante
                           JOIN pre.tpartida par ON par.id_partida = trp.id_partida
                           JOIN pre.vpresupuesto_cc pr ON pr.id_centro_costo = trp.id_centro_costo
                           JOIN pre.vcategoria_programatica cp ON cp.id_categoria_programatica =
                             pr.id_categoria_prog
                           LEFT JOIN pre.tclase_gasto_partida cgp ON cgp.id_partida = par.id_partida
                           LEFT JOIN pre.tclase_gasto cg ON cg.id_clase_gasto = cgp.id_clase_gasto
                           LEFT JOIN tes.vcuenta_bancaria cb ON cb.id_cuenta_bancaria =
                             cbt.id_cuenta_bancaria
                           LEFT JOIN param.tmoneda m ON trp.id_moneda = m.id_moneda
                      WHERE par.sw_movimiento::text = ''presupuestaria''::text
                      and ent.id_entrega = '||v_parametros.id_entrega||'

                      ORDER by nro_cuenta, codigo_cg , codigo_categoria , codigo';



     /* v_consulta:='select    COALESCE(t1.id_entrega, t2.id_entrega) as id_entrega,
                             COALESCE(t1.estado, t2.estado)::varchar as estado,
                             COALESCE(t1.c31, t2.c31)::varchar as c31,
                             COALESCE(t1.id_depto_conta, t2.id_depto_conta) as id_depto_conta,
                             COALESCE(t1.fecha_c31, t2.fecha_c31) as fecha_c31,
                             COALESCE(t1.codigo, t2.codigo)::varchar as codigo,
                             COALESCE(t1.nombre_partida, t2.nombre_partida)::varchar as nombre_partida,
                             COALESCE(t1.importe_debe_mb, t2.importe_debe_mb)::numeric as importe_debe_mb,
                             COALESCE(t1.importe_haber_mb, t2.importe_haber_mb)::numeric as importe_haber_mb,
                             COALESCE(t1.importe_debe_mb_completo, t2.importe_debe_mb_completo)::numeric as importe_debe_mb_completo,
                             COALESCE(t1.importe_haber_mb_completo, t2.importe_haber_mb_completo)::numeric as importe_haber_mb_completo,
                             COALESCE(t1.importe_gasto_mb, t2.importe_gasto_mb)::numeric as importe_gasto_mb,
                             COALESCE(t1.importe_recurso_mb, t2.importe_recurso_mb)::numeric as importe_recurso_mb,
                             COALESCE(t1.factor_reversion, t2.factor_reversion)::numeric as factor_reversion,
                             COALESCE(t1.codigo_cc, t2.codigo_cc)::varchar as codigo_cc,
                             COALESCE(t1.codigo_categoria, t2.codigo_categoria)::varchar as codigo_categoria,
                             COALESCE(t1.codigo_cg, t2.codigo_cg)::varchar as codigo_cg,
                             COALESCE(t1.nombre_cg, t2.nombre_cg)::varchar as nombre_cg,
                             COALESCE(t1.beneficiario, t2.beneficiario)::varchar as beneficiario,
                             COALESCE(t1.glosa1, t2.glosa1)::varchar as glosa1,
                             COALESCE(t1.id_int_comprobante, t2.id_int_comprobante) as id_int_comprobante,
                             COALESCE(t1.id_int_comprobante_dev, t2.id_int_comprobante_dev) as id_int_comprobante_dev,
                             COALESCE(t1.nro_cuenta, t2.nro_cuenta) as nro_cuenta,
                             COALESCE(t1.nombre_institucion, t2.nombre_institucion) as nombre_institucion,
                             COALESCE(t1.importe_debe, t2.importe_debe) as importe_debe,
                             COALESCE(t1.importe_haber, t2.importe_haber) as importe_haber,
                             COALESCE(t1.moneda_original, t2.moneda_original) as moneda_original
                    from
                    (SELECT  ent.id_entrega,
                             ent.estado,
                             ent.c31,
                             ent.id_depto_conta,
                             ent.fecha_c31,
                             par.codigo,
                             par.nombre_partida,
                             trd.importe_debe_mb,
                             trd.importe_haber_mb,
                             CASE
                               WHEN trd.factor_reversion > 0::numeric THEN trd.importe_debe_mb /(1::
                                 numeric - trd.factor_reversion)
                               ELSE trd.importe_debe_mb
                             END AS importe_debe_mb_completo,
                             CASE
                               WHEN trd.factor_reversion > 0::numeric THEN trd.importe_haber_mb /(1
                                 ::numeric - trd.factor_reversion)
                               ELSE trd.importe_haber_mb
                             END AS importe_haber_mb_completo,
                             trd.importe_gasto_mb,
                             trd.importe_recurso_mb,
                             trd.factor_reversion,
                             pr.codigo_cc,
                             cp.codigo_categoria,
                             cg.codigo AS codigo_cg,
                             cg.nombre AS nombre_cg,
                             cbt.beneficiario,
                             cbt.glosa1,
                             cbt.id_int_comprobante,
                             trd.id_int_comprobante AS id_int_comprobante_dev,
                             COALESCE(cb.nro_cuenta, ''SIN CUENTA''::character varying) AS nro_cuenta,
                             cb.nombre_institucion,
                             trd.importe_debe,
                             trd.importe_haber,
                             mm.moneda AS moneda_original
                      FROM conta.tentrega ent
                           JOIN conta.tentrega_det ed ON ed.id_entrega = ent.id_entrega
                           JOIN conta.tint_comprobante cbt ON cbt.id_int_comprobante =
                             ed.id_int_comprobante
                           JOIN tes.tplan_pago pg ON pg.id_int_comprobante = cbt.id_int_comprobante
                           JOIN tes.tplan_pago dev ON dev.id_plan_pago = pg.id_plan_pago_fk
                           JOIN conta.tint_transaccion trd ON trd.id_int_comprobante =
                             dev.id_int_comprobante
                           JOIN pre.tpartida par ON par.id_partida = trd.id_partida
                           JOIN pre.vpresupuesto_cc pr ON pr.id_centro_costo = trd.id_centro_costo
                           JOIN pre.vcategoria_programatica cp ON cp.id_categoria_programatica =
                             pr.id_categoria_prog
                           LEFT JOIN pre.tclase_gasto_partida cgp ON cgp.id_partida = par.id_partida
                           LEFT JOIN pre.tclase_gasto cg ON cg.id_clase_gasto = cgp.id_clase_gasto
                           LEFT JOIN tes.vcuenta_bancaria cb ON cb.id_cuenta_bancaria =
                             cbt.id_cuenta_bancaria
                           LEFT JOIN param.tmoneda mm ON trd.id_moneda = mm.id_moneda
                      WHERE par.sw_movimiento::text = ''presupuestaria''::text
                      and ent.id_entrega = '||v_parametros.id_entrega||'
                      UNION ALL
                      SELECT ent.id_entrega,
                             ent.estado,
                             ent.c31,
                             ent.id_depto_conta,
                             ent.fecha_c31,
                             par.codigo,
                             par.nombre_partida,
                             trp.importe_debe_mb,
                             trp.importe_haber_mb,
                             CASE
                               WHEN trp.factor_reversion > 0::numeric THEN trp.importe_debe_mb /(1::
                                 numeric - trp.factor_reversion)
                               ELSE trp.importe_debe_mb
                             END AS importe_debe_mb_completo,
                             CASE
                               WHEN trp.factor_reversion > 0::numeric THEN trp.importe_haber_mb /(1
                                 ::numeric - trp.factor_reversion)
                               ELSE trp.importe_haber_mb
                             END AS importe_haber_mb_completo,
                             trp.importe_gasto_mb,
                             trp.importe_recurso_mb,
                             trp.factor_reversion,
                             pr.codigo_cc,
                             cp.codigo_categoria,
                             cg.codigo AS codigo_cg,
                             cg.nombre AS nombre_cg,
                             cbt.beneficiario,
                             cbt.glosa1,
                             cbt.id_int_comprobante,
                             trp.id_int_comprobante AS id_int_comprobante_dev,
                             COALESCE(cb.nro_cuenta, ''SIN CUENTA''::character varying) AS nro_cuenta,
                             cb.nombre_institucion,
                             trp.importe_debe,
                             trp.importe_haber,
                             m.moneda AS moneda_original
                      FROM conta.tentrega ent
                           JOIN conta.tentrega_det ed ON ed.id_entrega = ent.id_entrega
                           JOIN conta.tint_comprobante cbt ON cbt.id_int_comprobante =
                             ed.id_int_comprobante
                           JOIN conta.tint_transaccion trp ON trp.id_int_comprobante =
                             cbt.id_int_comprobante
                           JOIN pre.tpartida par ON par.id_partida = trp.id_partida
                           JOIN pre.vpresupuesto_cc pr ON pr.id_centro_costo = trp.id_centro_costo
                           JOIN pre.vcategoria_programatica cp ON cp.id_categoria_programatica =
                             pr.id_categoria_prog
                           LEFT JOIN pre.tclase_gasto_partida cgp ON cgp.id_partida = par.id_partida
                           LEFT JOIN pre.tclase_gasto cg ON cg.id_clase_gasto = cgp.id_clase_gasto
                           LEFT JOIN tes.vcuenta_bancaria cb ON cb.id_cuenta_bancaria =
                             cbt.id_cuenta_bancaria
                           LEFT JOIN param.tmoneda m ON trp.id_moneda = m.id_moneda
                      WHERE par.sw_movimiento::text = ''presupuestaria''::text
                      and ent.id_entrega = '||v_parametros.id_entrega||') t1
                      full join
                      (SELECT ent.id_entrega,
                             ent.estado,
                             ent.c31,
                             ent.id_depto_conta,
                             ent.fecha_c31,
                             par.codigo,
                             par.nombre_partida,
                             trp.importe_debe_mb,
                             trp.importe_haber_mb,
                             CASE
                               WHEN trp.factor_reversion > 0::numeric THEN trp.importe_debe_mb /(1::
                                 numeric - trp.factor_reversion)
                               ELSE trp.importe_debe_mb
                             END AS importe_debe_mb_completo,
                             CASE
                               WHEN trp.factor_reversion > 0::numeric THEN trp.importe_haber_mb /(1
                                 ::numeric - trp.factor_reversion)
                               ELSE trp.importe_haber_mb
                             END AS importe_haber_mb_completo,
                             trp.importe_gasto_mb,
                             trp.importe_recurso_mb,
                             trp.factor_reversion,
                             pr.codigo_cc,
                             cp.codigo_categoria,
                             cg.codigo AS codigo_cg,
                             cg.nombre AS nombre_cg,
                             cbt.beneficiario,
                             cbt.glosa1,
                             cbt.id_int_comprobante,
                             trp.id_int_comprobante AS id_int_comprobante_dev,
                             COALESCE(cb.nro_cuenta, ''SIN CUENTA''::character varying) AS nro_cuenta,
                             cb.nombre_institucion,
                             trp.importe_debe,
                             trp.importe_haber,
                             m.moneda AS moneda_original
                      FROM conta.tentrega ent
                           JOIN conta.tentrega_det ed ON ed.id_entrega = ent.id_entrega
                           JOIN conta.tint_comprobante cbt ON cbt.id_int_comprobante = ed.id_int_comprobante
                           JOIN conta.tint_transaccion trp ON trp.id_int_comprobante = cbt.id_int_comprobante
                           JOIN pre.tpartida par ON par.id_partida = trp.id_partida
                           JOIN pre.vpresupuesto_cc pr ON pr.id_centro_costo = trp.id_centro_costo
                           JOIN pre.vcategoria_programatica cp ON cp.id_categoria_programatica = pr.id_categoria_prog
                           JOIN pre.tclase_gasto_partida cgp ON cgp.id_partida = par.id_partida
                           JOIN pre.tclase_gasto cg ON cg.id_clase_gasto = cgp.id_clase_gasto
                           LEFT JOIN tes.vcuenta_bancaria cb ON cb.id_cuenta_bancaria = cbt.id_cuenta_bancaria
                           LEFT JOIN param.tmoneda m ON trp.id_moneda = m.id_moneda
                      WHERE par.sw_movimiento::text = ''flujo''::text
                      and ent.id_entrega = '||v_parametros.id_entrega||') t2
                      on t1.id_int_comprobante = t2.id_int_comprobante
                      ORDER by nro_cuenta, codigo_cg , codigo_categoria , codigo';*/

      --Devuelve la respuesta
      raise notice '--> %',v_consulta;
      return v_consulta;

    end;


      --asd
      else

      raise exception 'Transaccion inexistente';

    end if;

    EXCEPTION

    WHEN OTHERS THEN
    v_resp='';
    v_resp = pxp.f_agrega_clave(v_resp,'mensaje',SQLERRM);
    v_resp = pxp.f_agrega_clave(v_resp,'codigo_error',SQLSTATE);
    v_resp = pxp.f_agrega_clave(v_resp,'procedimientos',v_nombre_funcion
    );
    raise exception '%',v_resp;
  END;
$body$
LANGUAGE 'plpgsql'
VOLATILE
CALLED ON NULL INPUT
SECURITY INVOKER
COST 100;