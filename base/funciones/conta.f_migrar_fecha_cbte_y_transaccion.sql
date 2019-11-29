CREATE OR REPLACE FUNCTION conta.f_migrar_fecha_cbte_y_transaccion (
  v_id_comprobante integer,
  v_id_depto integer
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Sistema de Contabilidad
 FUNCION: 		conta.f_migrar_fecha_cbte_y_transaccion
 DESCRIPCION:   Funcion que realiza un UPDATE a las tablas comprobante y transaccion
 AUTOR: 		Maylee Perez Pastor
 FECHA:	        27-11-2019
 COMENTARIOS:   sentencia de llamado a la funcion select conta.f_migrar_fecha_cbte_y_transaccion(id_int_comprobante, id_depto)
***************************************************************************/

DECLARE
		v_registros  			record;
        v_tipo_cambio_pais		record;
        v_fecha					date;
        v_tipo_cambio			numeric;
        v_id_moneda				integer;
        v_tipo_cambio_2			numeric;
        v_int_cbte				record;
        v_valor_debe_mb			numeric;
        v_valor_haber_mb		numeric;
        v_valor_gasto_mb		numeric;
        v_valor_recurso_mb		numeric;
        v_valor_debe_mt			numeric;
        v_valor_haber_mt		numeric;
        v_valor_gasto_mt		numeric;
        v_valor_recurso_mt		numeric;

BEGIN


                    --datos de tint_comprobante
                    SELECT cbte.fecha, cbte.tipo_cambio, cbte.id_moneda
                    INTO v_fecha, v_tipo_cambio, v_id_moneda
                    FROM conta.tint_comprobante cbte
                    WHERE cbte.id_int_comprobante = v_id_comprobante
                    and cbte.id_depto = 50; --bue

                	--datos de ttipo_cambio_pais
                   /* SELECT tcp.*
                    INTO v_tipo_cambio_pais
                    FROM conta.ttipo_cambio_pais tcp
                    WHERE tcp.id_moneda_pais = v_id_moneda
                    and tcp.fecha = v_fecha;    */

                    SELECT tcp.*
                    INTO v_tipo_cambio_pais
                    FROM conta.tmoneda_pais mp
                    join conta.ttipo_cambio_pais tcp on tcp.id_moneda_pais =mp.id_moneda_pais
                    WHERE mp.id_moneda = v_id_moneda
                    and tcp.fecha = v_fecha;

                    --datos de tint_transaccion
                    SELECT tra.tipo_cambio_2
                    INTO v_tipo_cambio_2
                    FROM conta.tint_transaccion tra
                    WHERE tra.id_int_comprobante = v_id_comprobante;

                    --condicion si es en dolares o su moneda seleccionada
                    --IF (v_id_moneda != 2) THEN

                          --Update para el comprobante
                          IF (v_fecha = v_tipo_cambio_pais.fecha) THEN
                              UPDATE conta.tint_comprobante SET
                              tipo_cambio_2 = v_tipo_cambio_pais.oficial
                              WHERE id_int_comprobante =  v_id_comprobante;
                          ELSE
                              raise exception 'no tiene la  fecha registrada en los tipos de cambio del cbte';
                          END IF;



                          FOR v_int_cbte in (SELECT *
                                              FROM conta.tint_transaccion tra
                                              WHERE tra.id_int_comprobante = v_id_comprobante )loop

                               v_valor_debe_mb =  param.f_convertir_moneda (v_id_moneda, 1,   v_int_cbte.importe_debe, v_fecha,'CUS',50, v_tipo_cambio_pais.oficial, 'no');
                               v_valor_haber_mb =  param.f_convertir_moneda (v_id_moneda, 1,   v_int_cbte.importe_haber, v_fecha,'CUS',50, v_tipo_cambio_pais.oficial, 'no');
                               v_valor_gasto_mb =  param.f_convertir_moneda (v_id_moneda, 1,   v_int_cbte.importe_gasto, v_fecha,'CUS',50, v_tipo_cambio_pais.oficial, 'no');
                               v_valor_recurso_mb =  param.f_convertir_moneda(v_id_moneda, 1,   v_int_cbte.importe_recurso, v_fecha,'CUS',50, v_tipo_cambio_pais.oficial, 'no');

                               /*v_valor_debe_mt =  param.f_convertir_moneda (1, 2,   v_valor_debe_mb, v_registros.fecha,'CUS',50, 6.96, 'no');
                               v_valor_haber_mt =  param.f_convertir_moneda (1,2,   v_valor_haber_mb, v_registros.fecha,'CUS',50, 6.96, 'no');
                               v_valor_gasto_mt =  param.f_convertir_moneda (1,2,   v_valor_gasto_mb, v_registros.fecha,'CUS',50, 6.96, 'no');
                               v_valor_recurso_mt =  param.f_convertir_moneda(1,2,  v_valor_recurso_mb, v_registros.fecha,'CUS',50, 6.96, 'no');
                              */

                              IF (v_fecha = v_tipo_cambio_pais.fecha) THEN

                                  --Update para las transacciones
                                  UPDATE conta.tint_transaccion SET
                                  tipo_cambio_2 = v_tipo_cambio_pais.oficial,

                                  importe_debe_mb =v_valor_debe_mb,
                                  importe_haber_mb =v_valor_haber_mb,
                                  importe_gasto_mb =v_valor_gasto_mb,
                                  importe_recurso_mb =v_valor_recurso_mb/*,

                                  importe_debe_mt =v_valor_debe_mt,
                                  importe_haber_mt =v_valor_haber_mt,
                                  importe_gasto_mt =v_valor_gasto_mt,
                                  importe_recurso_mt =v_valor_recurso_mt*/


                                  WHERE id_int_transaccion =  v_int_cbte.id_int_transaccion;

                              ELSE
                                  raise exception 'no tiene la  fecha registrada';
                              END IF;


                          end loop;



                   /* ELSE
                        --moneda en dolares

                        IF (v_id_moneda = 2) THEN
                        	IF (v_id_depto = 50) THEN --bue

                            	--Update para el comprobante
                                UPDATE conta.tint_comprobante SET
                                tipo_cambio_2 = 1,
                                tipo_cambio_3 = 1
                                WHERE id_int_comprobante =  v_id_comprobante;


                                FOR v_int_cbte in (SELECT tra.id_int_transaccion
                                                    FROM conta.tint_transaccion tra
                                                    WHERE tra.id_int_comprobante = v_id_comprobante )loop


                                        --Update para las transacciones
                                        UPDATE conta.tint_transaccion SET
                                        tipo_cambio_2 = 1,
                               			tipo_cambio_3 = 1
                                        WHERE id_int_transaccion =  v_int_cbte;

                                end loop;


                            END IF;

                        END IF;





                    END IF;*/



   return 'exito';
END;
$body$
LANGUAGE 'plpgsql'
VOLATILE
CALLED ON NULL INPUT
SECURITY INVOKER
COST 100;