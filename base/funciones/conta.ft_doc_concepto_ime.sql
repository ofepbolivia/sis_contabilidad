CREATE OR REPLACE FUNCTION conta.ft_doc_concepto_ime (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Sistema de Contabilidad
 FUNCION: 		conta.ft_doc_concepto_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'conta.tdoc_concepto'
 AUTOR: 		 (admin)
 FECHA:	        15-09-2015 13:09:45
 COMENTARIOS:	
***************************************************************************
 HISTORIAL DE MODIFICACIONES:

 DESCRIPCION:	
 AUTOR:			
 FECHA:		
***************************************************************************/

DECLARE

	v_nro_requerimiento    	integer;
	v_parametros           	record;
	v_id_requerimiento     	integer;
	v_resp		            varchar;
	v_nombre_funcion        text;
	v_mensaje_error         text;
	v_id_doc_concepto		integer;
    v_registros_doc			record;
    v_registros_cig			record;
    v_codigo_rel            varchar;
    v_id_cuenta  			integer;
    v_id_partida	integer;
    v_id_auxiliar	integer;

    --12-11-2021 (may)
    cuenta_doc_det			record;
    v_nombre_partida_registro	varchar;
    v_importe_partida_sol	numeric;
    v_importe_partida_sol_total		numeric;
    v_partida				record;
    v_nombre_centro_registro	varchar;
    v_total_rendido				numeric;

    v_gestion					integer;
    v_id_gestion_sol			integer;
    v_id_gestion				integer;

BEGIN

    v_nombre_funcion = 'conta.ft_doc_concepto_ime';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************    
 	#TRANSACCION:  'CONTA_DOCC_INS'
 	#DESCRIPCION:	Insercion de registros
 	#AUTOR:		admin	
 	#FECHA:		15-09-2015 13:09:45
	***********************************/

	if(p_transaccion='CONTA_DOCC_INS')then
					
        begin
        
            select
            cig.desc_ingas
            into
            v_registros_cig
            from param.tconcepto_ingas cig
            where cig.id_concepto_ingas =  v_parametros.id_concepto_ingas;
            
            
            
            
            --obtiene datos del documento
            
             SELECT 
              dcv.tipo,
              dcv.id_periodo,
              per.id_gestion,
              dcv.importe_doc
             into
              v_registros_doc
             FROM conta.tdoc_compra_venta dcv 
             inner join param.tperiodo per on per.id_periodo = dcv.id_periodo
             where dcv.id_doc_compra_venta = v_parametros.id_doc_compra_venta;
             
         
             --obtener partida, cuenta auxiliar del concepto de gasto
            IF v_registros_doc.tipo = 'compra' THEN
               v_codigo_rel = 'CUECOMP';  --codigo de relacion contable para compras
            ELSE
               v_codigo_rel = 'CUEVENT';  --codigo de relacion contable para ventas
            END IF;
            
            --02-02-2022 (may) se quita filtro gestion porque ya se tiene gestion de la solicitud
            SELECT cco.id_gestion
            INTO v_id_gestion_sol
            FROM param.tcentro_costo cco
            WHERE cco.id_centro_costo = v_parametros.id_centro_costo;

            --02-02-2022 (may) si la gestion de la solicitud no es la misma a la gestion de la factura, se tomara en cuenta la gestion de la solicitud
            IF (v_registros_doc.id_gestion != v_id_gestion_sol) THEN
            	v_id_gestion  = v_id_gestion_sol;
            ELSE -- si la gestion de la solicitud es IGUAL a la gestion de la factura, se tomara la gestion normal como lo hace de la factura
            	v_id_gestion  = v_registros_doc.id_gestion;
            END IF;
            --

            --Validar si tiene relacion contable
            SELECT 
              ps_id_partida ,
              ps_id_cuenta,
              ps_id_auxiliar
            into 
              v_id_partida,
              v_id_cuenta, 
              v_id_auxiliar
            FROM conta.f_get_config_relacion_contable(v_codigo_rel, 
                                                     v_id_gestion, --v_registros_doc.id_gestion,
                                                     v_parametros.id_concepto_ingas, 
                                                     v_parametros.id_centro_costo,  
                                                     'No se encontro relación contable para el conceto de gasto: '||v_registros_cig.desc_ingas||'. <br> Mensaje: ');
          
        
           IF  v_id_cuenta is NULL THEN
               raise exception 'no se encontro relacion contable ...';
           END IF;



             --01-11-2021 (may) para controlar a FA con detalle, sea el mismo centro de costo

             SELECT cdd.id_partida,
             		cdd.id_cc,
                    cd.habilitar_det_ren,
                    cd.importe,
                    cd.nro_tramite,
                    cdd.id_partida_ejecucion,
                    cd.detalle_cuenta_doc,
                    cd.id_cuenta_doc,
                    cd.id_moneda,
                    cd.fecha,
                    rd.id_rendicion_det
             INTO cuenta_doc_det
             FROM cd.tcuenta_doc_det cdd
             inner join cd.tcuenta_doc cd on cd.id_cuenta_doc = cdd.id_cuenta_doc
             inner join cd.trendicion_det rd on rd.id_cuenta_doc = cd.id_cuenta_doc
             WHERE rd.id_doc_compra_venta = v_parametros.id_doc_compra_venta;

             IF (cuenta_doc_det.importe < v_registros_doc.importe_doc ) THEN
             	RAISE EXCEPTION 'El importe total de la rendicion es de % y no puede ser mayor al importe solicitado % en el Fondo en Avance %.', v_registros_doc.importe_doc, cuenta_doc_det.importe, cuenta_doc_det.nro_tramite;
             END IF;


             SELECT sum(cdd.importe)
             INTO v_importe_partida_sol
             FROM cd.tcuenta_doc_det cdd
             inner join cd.tcuenta_doc cd on cd.id_cuenta_doc = cdd.id_cuenta_doc
             inner join cd.trendicion_det rd on rd.id_cuenta_doc = cd.id_cuenta_doc
             left join pre.tpartida par on par.id_partida = cdd.id_partida
             WHERE rd.id_doc_compra_venta = v_parametros.id_doc_compra_venta
             and par.id_partida = cuenta_doc_det.id_partida;

             SELECT sum(dc.precio_total)
             INTO v_importe_partida_sol_total
             FROM conta.tdoc_concepto dc
             WHERE dc.id_partida = v_id_partida
             and dc.id_doc_compra_venta = v_parametros.id_doc_compra_venta;


             --18-02-2022 (may) modificacion de importe a nuevo campo importe_ajuste que es segun a los importes del ajuste por presupuestos
             --FOR v_partida in ( SELECT sum(cdd.importe) as importe,  cdd.id_partida, cdd.id_cc
             FOR v_partida in ( SELECT sum(cdd.importe_ajuste) as importe,  cdd.id_partida, cdd.id_cc
                                 FROM cd.tcuenta_doc_det cdd
                                 inner join cd.tcuenta_doc cd on cd.id_cuenta_doc = cdd.id_cuenta_doc
                                 inner join cd.trendicion_det rd on rd.id_cuenta_doc = cd.id_cuenta_doc
                                 left join pre.tpartida par on par.id_partida = cdd.id_partida
                                 WHERE rd.id_doc_compra_venta = v_parametros.id_doc_compra_venta
                                 group by cdd.id_partida , cdd.id_cc
             						) LOOP

                               --verificar del importe comprometido
                              /*select sum(c.importe)
                              into v_total_rendido
                              from cd.tcuenta_doc c
                              where  c.id_cuenta_doc_fk = cuenta_doc_det.id_cuenta_doc
                              and c.estado = 'rendido' and c.estado_reg = 'activo';   */


                             IF (v_partida.id_partida = v_id_partida and v_partida.id_cc = v_parametros.id_centro_costo) THEN

                             	  v_total_rendido= round((v_partida.importe * 0.87), 2) ; --el importe comprometido

                                  SELECT par.nombre_partida
                                  INTO v_nombre_partida_registro
                                  FROM pre.tpartida par
                                  WHERE par.id_partida = v_id_partida;

                                  IF (v_partida.importe < v_parametros.precio_total ) THEN
                                      RAISE EXCEPTION 'El importe de la Partida % es de % y no puede ser mayor al importe solicitado %, importe devengado %, en el Fondo en Avance %.',upper(v_nombre_partida_registro), v_parametros.precio_total, v_partida.importe,COALESCE(v_total_rendido,0),cuenta_doc_det.nro_tramite;
                                  END IF;
                             END IF;
             END LOOP;
             --

             --16-12-2021 (may)para controlar que registren para el mismo centro de costo y partida del detalle del FA
             IF (cuenta_doc_det.detalle_cuenta_doc ='si')THEN
             	IF NOT EXISTS(SELECT 1
                            FROM cd.tcuenta_doc_det cdet
                            WHERE cdet.id_cuenta_doc = cuenta_doc_det.id_cuenta_doc
                            and cdet.id_partida = v_id_partida
                            and cdet.id_cc = v_parametros.id_centro_costo
                           ) THEN

                          SELECT par.nombre_partida
                          INTO v_nombre_partida_registro
                          FROM pre.tpartida par
                          WHERE par.id_partida = v_id_partida;

                          SELECT presu.codigo_cc
                          INTO v_nombre_centro_registro
                          FROM pre.vpresupuesto_cc presu
                          WHERE presu.id_centro_costo = v_parametros.id_centro_costo;

                          RAISE EXCEPTION 'En el detalle registrado del inicio de Fondo en Avance no se encuentra el Centro de Costo % y la Partida %. Verificar el registro.',v_nombre_centro_registro,v_nombre_partida_registro;

             	END IF;
             END IF;


             --

        
        	--Sentencia de la insercion
        	insert into conta.tdoc_concepto(
			estado_reg,
			id_orden_trabajo,
			id_centro_costo,
			id_concepto_ingas,
			descripcion,
			cantidad_sol,
			precio_unitario,
			precio_total,
			id_usuario_reg,
			fecha_reg,
			id_usuario_mod,
			fecha_mod,
            id_doc_compra_venta,
            precio_total_final,
            id_partida
          	) values(
			'activo',
			v_parametros.id_orden_trabajo,
			v_parametros.id_centro_costo,
			v_parametros.id_concepto_ingas,
			v_parametros.descripcion,
			v_parametros.cantidad_sol,
			v_parametros.precio_unitario,
			v_parametros.precio_total,
			p_id_usuario,
			now(),			
			null,
			null,            
            v_parametros.id_doc_compra_venta,
            v_parametros.precio_total_final,
            v_id_partida
			)RETURNING id_doc_concepto into v_id_doc_concepto;
			
			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','CONCEPTO almacenado(a) con exito (id_doc_concepto'||v_id_doc_concepto||')'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_doc_concepto',v_id_doc_concepto::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************    
 	#TRANSACCION:  'CONTA_DOCC_MOD'
 	#DESCRIPCION:	Modificacion de registros
 	#AUTOR:		admin	
 	#FECHA:		15-09-2015 13:09:45
	***********************************/

	elsif(p_transaccion='CONTA_DOCC_MOD')then

		begin
        
              select
              cig.desc_ingas
              into
              v_registros_cig
              from param.tconcepto_ingas cig
              where cig.id_concepto_ingas =  v_parametros.id_concepto_ingas;
              
            --obtiene datos del documento
            
             SELECT 
              dcv.tipo,
              dcv.id_periodo,
              per.id_gestion,
              dcv.importe_doc
             into
              v_registros_doc
             FROM conta.tdoc_compra_venta dcv 
             inner join param.tperiodo per on per.id_periodo = dcv.id_periodo
             where dcv.id_doc_compra_venta = v_parametros.id_doc_compra_venta;
             
         
             --obtener partida, cuenta auxiliar del concepto de gasto
            IF v_registros_doc.tipo = 'compra' THEN
               v_codigo_rel = 'CUECOMP';  --codigo de relacion contable para compras
            ELSE
               v_codigo_rel = 'CUEVENT';  --codigo de relacion contable para ventas
            END IF;
            
            --02-02-2022 (may) se quita filtro gestion porque ya se tiene gestion de la solicitud
            SELECT cco.id_gestion
            INTO v_id_gestion_sol
            FROM param.tcentro_costo cco
            WHERE cco.id_centro_costo = v_parametros.id_centro_costo;

            --02-02-2022 (may) si la gestion de la solicitud no es la misma a la gestion de la factura, se tomara en cuenta la gestion de la solicitud
            IF (v_registros_doc.id_gestion != v_id_gestion_sol) THEN
            	v_id_gestion  = v_id_gestion_sol;
            ELSE -- si la gestion de la solicitud es IGUAL a la gestion de la factura, se tomara la gestion normal como lo hace de la factura
            	v_id_gestion  = v_registros_doc.id_gestion;
            END IF;
            --

            --Validar si tiene relacion contable
            SELECT 
              ps_id_partida ,
              ps_id_cuenta,
              ps_id_auxiliar
            into 
              v_id_partida,
              v_id_cuenta, 
              v_id_auxiliar
            FROM conta.f_get_config_relacion_contable(v_codigo_rel, 
                                                     v_id_gestion, --v_registros_doc.id_gestion,
                                                     v_parametros.id_concepto_ingas, 
                                                     v_parametros.id_centro_costo,  
                                                     'No se encontro relación contable para el conceto de gasto: '||v_registros_cig.desc_ingas||'. <br> Mensaje: ');

        --01-11-2021 (may) para controlar a FA con detalle, sea el mismo centro de costo
          SELECT cdd.id_partida,
             		cdd.id_cc,
                    cd.habilitar_det_ren,
                    cd.importe,
                    cd.nro_tramite,
                    cdd.id_partida_ejecucion,
                    cd.detalle_cuenta_doc,
                    cd.id_cuenta_doc,
                    cd.id_moneda,
                    cd.fecha,
                    rd.id_rendicion_det
             INTO cuenta_doc_det
             FROM cd.tcuenta_doc_det cdd
             inner join cd.tcuenta_doc cd on cd.id_cuenta_doc = cdd.id_cuenta_doc
             inner join cd.trendicion_det rd on rd.id_cuenta_doc = cd.id_cuenta_doc
             inner join conta.tdoc_concepto dc on dc.id_doc_compra_venta = rd.id_doc_compra_venta
             WHERE dc.id_doc_concepto = v_parametros.id_doc_concepto;

             IF (cuenta_doc_det.importe < v_registros_doc.importe_doc ) THEN
             	RAISE EXCEPTION 'El importe total de la rendicion es de % y no puede ser mayor al importe solicitado % en el Fondo en Avance %.', v_registros_doc.importe_doc, cuenta_doc_det.importe, cuenta_doc_det.nro_tramite;
             END IF;

             --16-12-2021 (may)para controlar que registren para el mismo centro de costo y partida del detalle del FA
             IF (cuenta_doc_det.detalle_cuenta_doc ='si')THEN
               IF NOT EXISTS(SELECT 1
                              FROM cd.tcuenta_doc_det cdet
                              WHERE cdet.id_cuenta_doc = cuenta_doc_det.id_cuenta_doc
                              and cdet.id_partida = v_id_partida
                              and cdet.id_cc = v_parametros.id_centro_costo
                             ) THEN

                            SELECT par.nombre_partida
                            INTO v_nombre_partida_registro
                            FROM pre.tpartida par
                            WHERE par.id_partida = v_id_partida;

                            SELECT presu.codigo_cc
                            INTO v_nombre_centro_registro
                            FROM pre.vpresupuesto_cc presu
                            WHERE presu.id_centro_costo = v_parametros.id_centro_costo;

                            RAISE EXCEPTION 'En el detalle registrado del inicio de Fondo en Avance no se encuentra el Centro de Costo % y la Partida %. Verificar el registro.',v_nombre_centro_registro,v_nombre_partida_registro;

               END IF;
             END IF;

             --


			--Sentencia de la modificacion
			update conta.tdoc_concepto set
                id_orden_trabajo = v_parametros.id_orden_trabajo,
                id_centro_costo = v_parametros.id_centro_costo,
                id_concepto_ingas = v_parametros.id_concepto_ingas,
                descripcion = v_parametros.descripcion,
                cantidad_sol = v_parametros.cantidad_sol,
                precio_unitario = v_parametros.precio_unitario,
                precio_total = v_parametros.precio_total,
                id_usuario_mod = p_id_usuario,
                fecha_mod = now(),
                precio_total_final = v_parametros.precio_total_final,
                id_partida = v_id_partida
			where id_doc_concepto=v_parametros.id_doc_concepto;
               
			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','CONCEPTO modificado(a)'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_doc_concepto',v_parametros.id_doc_concepto::varchar);
               
            --Devuelve la respuesta
            return v_resp;
            
		end;

	/*********************************    
 	#TRANSACCION:  'CONTA_DOCC_ELI'
 	#DESCRIPCION:	Eliminacion de registros
 	#AUTOR:		admin	
 	#FECHA:		15-09-2015 13:09:45
	***********************************/

	elsif(p_transaccion='CONTA_DOCC_ELI')then

		begin
			--Sentencia de la eliminacion
			delete from conta.tdoc_concepto
            where id_doc_concepto=v_parametros.id_doc_concepto;
               
            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','CONCEPTO eliminado(a)'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_doc_concepto',v_parametros.id_doc_concepto::varchar);
              
            --Devuelve la respuesta
            return v_resp;

		end;
        
   /*********************************    
 	#TRANSACCION:  'CONTA_VERCONCEP_IME'
 	#DESCRIPCION:	recupera relacion contable para el concepto indicado
 	#AUTOR:		admin	
 	#FECHA:		15-09-2015 13:09:45
	***********************************/

	elsif(p_transaccion='CONTA_VERCONCEP_IME')then

		begin
            
            --obtener partida, cuenta auxiliar del concepto de gasto
            IF v_parametros.relacion = 'compra' THEN
               v_codigo_rel = 'CUECOMP';  --codigo de relacion contable para compras
            ELSE
               v_codigo_rel = 'CUEVENT';  --codigo de relacion contable para ventas
            END IF;
            
            SELECT 
              ps_id_partida ,
              ps_id_cuenta,
              ps_id_auxiliar
            into 
              v_id_partida,
              v_id_cuenta, 
              v_id_auxiliar
           FROM conta.f_get_config_relacion_contable(v_codigo_rel, 
                                                     v_parametros.id_gestion, 
                                                     v_parametros.id_concepto_ingas, 
                                                     v_parametros.id_centro_costo,  
                                                     'No se encontro relación contable este concepto <br> Mensaje: ');
          
			
            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','CONCEPTO verificado(a)'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_concepto_ingas',v_parametros.id_concepto_ingas::varchar);
            v_resp = pxp.f_agrega_clave(v_resp,'id_partida',v_id_partida::varchar);
            v_resp = pxp.f_agrega_clave(v_resp,'id_cuenta',v_id_cuenta::varchar);
            v_resp = pxp.f_agrega_clave(v_resp,'id_auxiliar',v_id_auxiliar::varchar);
              
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