CREATE OR REPLACE FUNCTION conta.ft_periodo_compra_venta_ime (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Sistema de Contabilidad
 FUNCION: 		conta.ft_periodo_compra_venta_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'conta.tperiodo_compra_venta'
 AUTOR: 		 (admin)
 FECHA:	        24-08-2015 14:16:54
 COMENTARIOS:
***************************************************************************
 HISTORIAL DE MODIFICACIONES:

 DESCRIPCION:	aumento de controles para cierre y apertura de periodo de compra venta
 AUTOR:			breydi vasquez
 FECHA:		    06/12/2019
***************************************************************************/

DECLARE

	v_nro_requerimiento    	integer;
	v_parametros           	record;
	v_id_requerimiento     	integer;
	v_resp		            varchar;
	v_nombre_funcion        text;
	v_mensaje_error         text;
	v_id_periodo_compra_venta	integer;
    v_registros					record;
    v_fecha_fin					date;
    v_estado					varchar;
	v_perido_compra_venta			record;
    v_id_periodo					int4;
    v_id_gestion					int4;
    v_estado_actualizado			varchar;
	v_periodos_permitidos			varchar;
	v_fecha_permitida				date;
	v_fecha_cerrado_parcial 		varchar;
	v_fecha_cerrado				varchar;
    v_traer_data				varchar;
    v_id_depto_cv				integer;
	v_id_depto					integer;


    v_id_gestion_periodo		integer;
    v_id_depto_periodo			integer;
    v_estado_periodo			varchar;
    v_periodo_literal			varchar;

BEGIN

    v_nombre_funcion = 'conta.ft_periodo_compra_venta_ime';
    v_parametros = pxp.f_get_record(p_tabla);

	 /*********************************
 	#TRANSACCION:  'CONTA_GENPCV_IME'
 	#DESCRIPCION:	Genracion losperiodos de compra venta para el depto y gention selecionados
 	#AUTOR:		    Rensi Arteaga Copari
 	#FECHA:			24-08-2015 13:58:30
	***********************************/

	if(p_transaccion='CONTA_GENPCV_IME')then

		begin



        	--obtener los registros de la tabla periodo que no esten en la tabla tperiodo_compra_venta
            FOR v_registros in  (
                select
                    per.id_periodo
                from param.tperiodo as per
                where per.estado_reg = 'activo'
                and per.id_periodo not in (
                	select pcv.id_periodo
                    from conta.tperiodo_compra_venta pcv
                    inner join param.tperiodo p2 on p2.id_periodo = pcv.id_periodo
                    where pcv.id_depto = v_parametros.id_depto
                    and p2.id_gestion = v_parametros.id_gestion
                ) and per.id_gestion = v_parametros.id_gestion
            ) LOOP

              INSERT INTO  conta.tperiodo_compra_venta
                        (
                          id_usuario_reg,
                          fecha_reg,
                          estado_reg,
                          id_depto,
                          id_periodo,
                          estado
                        )
                        VALUES (
                           p_id_usuario,
                           now(),
                          'activo',
                          v_parametros.id_depto,
                          v_registros.id_periodo,
                          'abierto'
                        );

            END LOOP;

            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Periodos compra venta generados el depto '||v_parametros.id_depto::varchar);
            v_resp = pxp.f_agrega_clave(v_resp,'id_gestion',v_parametros.id_gestion::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;
    /*********************************
 	#TRANSACCION:  'CONTA_ABRCERPER_IME'
 	#DESCRIPCION:	abre, cierra o cierra parcialmente los periodos de libro de compras y ventas
 	#AUTOR:		    Rensi Arteaga Copari
 	#FECHA:			24-08-2015 13:58:30
	***********************************/

	elsif(p_transaccion='CONTA_ABRCERPER_IME')then

		begin
			v_periodos_permitidos = pxp.f_get_variable_global('conta_periodos_mod_mes');
           select (date_trunc('month', now()) + interval '1 month' - interval '1 day')::date-(v_periodos_permitidos||' month')::interval
           into v_fecha_permitida;
           select
             per.fecha_fin
           into
             v_fecha_fin
           from conta.tperiodo_compra_venta pcv
           inner join param.tperiodo per on per.id_periodo = pcv.id_periodo
            where pcv.id_periodo_compra_venta = v_parametros.id_periodo_compra_venta;


        	--todo para abrir el perido revisar que el periodo de conta del periodo correspondiente este cerrado
            IF not param.f_periodo_subsistema_abierto(v_fecha_fin, 'CONTA') THEN
              raise exception 'El periodo se encuentra cerrado en contabilidad';
            END IF;

			IF v_fecha_fin <= v_fecha_permitida THEN
             -- raise exception 'No se puede abrir periodos que ya cumplieron mas de % meses de antiguedad.', v_periodos_permitidos;
            END IF;

            IF  v_parametros.tipo = 'cerrar' THEN
             v_estado = 'cerrado';


             /*Control para no cerrar periodos despues del periodo actual Ismael Valdivia (15/11/2021)*/
              select per.id_gestion,
                     pcv.id_depto
                     into
                     v_id_gestion_periodo,
                     v_id_depto_periodo
              from conta.tperiodo_compra_venta pcv
              inner join param.tperiodo per on per.id_periodo = pcv.id_periodo
              where pcv.id_periodo_compra_venta = v_parametros.id_periodo_compra_venta;



              select
                     pcv.estado,
                     (param.f_literal_periodo(pcv.id_periodo))
                     into
                     v_estado_periodo,
                     v_periodo_literal
              from conta.tperiodo_compra_venta pcv
              inner join param.tperiodo per on per.id_periodo = pcv.id_periodo
              where per.id_gestion = v_id_gestion_periodo and pcv.id_depto = v_id_depto_periodo
              and pcv.id_periodo_compra_venta < v_parametros.id_periodo_compra_venta
              order by pcv.id_periodo_compra_venta DESC
              limit 1;


             if (v_estado_periodo = 'abierto' or v_estado_periodo = 'cerrado_parcial') then
             	raise exception 'El periodo <b>%</b> se encuentra en estado <b>%</b>, favor cerrar el periodo mencionado para realizar el cierre seleccionado',v_periodo_literal,v_estado_periodo;
             end if;
             /*****************************************************************************/

             /*Aumentando para que se jale informacion de la tabla sfe.tfactura para los comisionistas*/
             --Modifi (Ismael Valdivia 13/04/2021)
             select pcv.id_depto
             	    into
                    v_id_depto_cv
             from conta.tperiodo_compra_venta pcv
             where pcv.id_periodo_compra_venta = v_parametros.id_periodo_compra_venta;

             select dep.id_depto
             into
             v_id_depto
             from param.tdepto dep
             where trim(dep.codigo) = 'CON';

             if (v_id_depto_cv = v_id_depto) then
             	v_traer_data = vef.ft_insertar_acumulacion_comisionistas_ime();
             end if;


             /*****************************************************************************************/
            ELSIF  v_parametros.tipo = 'cerrar_parcial' THEN
             v_estado = 'cerrado_parcial';

             /*Control para no cerrar periodos despues del periodo actual Ismael Valdivia (15/11/2021)*/
              select per.id_gestion,
                     pcv.id_depto
                     into
                     v_id_gestion_periodo,
                     v_id_depto_periodo
              from conta.tperiodo_compra_venta pcv
              inner join param.tperiodo per on per.id_periodo = pcv.id_periodo
              where pcv.id_periodo_compra_venta = v_parametros.id_periodo_compra_venta;



              select
                     pcv.estado,
                     (param.f_literal_periodo(pcv.id_periodo))
                     into
                     v_estado_periodo,
                     v_periodo_literal
              from conta.tperiodo_compra_venta pcv
              inner join param.tperiodo per on per.id_periodo = pcv.id_periodo
              where per.id_gestion = v_id_gestion_periodo and pcv.id_depto = v_id_depto_periodo
              and pcv.id_periodo_compra_venta < v_parametros.id_periodo_compra_venta
              order by pcv.id_periodo_compra_venta DESC
              limit 1;


             if (v_estado_periodo = 'abierto') then
             	raise exception 'El periodo <b>%</b> se encuentra en estado <b>%</b>, favor cerrar el periodo mencionado para realizar el cierre seleccionado',v_periodo_literal,v_estado_periodo;
             end if;
             /*****************************************************************************/



             /*Aumentando para que se jale informacion de la tabla sfe.tfactura para los comisionistas*/
             --Modifi (Ismael Valdivia 13/04/2021)
             select pcv.id_depto
             	    into
                    v_id_depto_cv
             from conta.tperiodo_compra_venta pcv
             where pcv.id_periodo_compra_venta = v_parametros.id_periodo_compra_venta;

             select dep.id_depto
             into
             v_id_depto
             from param.tdepto dep
             where trim(dep.codigo) = 'CON';

             if (v_id_depto_cv = v_id_depto) then
             	v_traer_data = vef.ft_insertar_acumulacion_comisionistas_ime();
             end if;
             /*****************************************************************************************/
            ELSE

             v_estado = 'abierto';

             /*Control para no cerrar periodos despues del periodo actual Ismael Valdivia (15/11/2021)*/
              select per.id_gestion,
                     pcv.id_depto
                     into
                     v_id_gestion_periodo,
                     v_id_depto_periodo
              from conta.tperiodo_compra_venta pcv
              inner join param.tperiodo per on per.id_periodo = pcv.id_periodo
              where pcv.id_periodo_compra_venta = v_parametros.id_periodo_compra_venta;



              select
                     pcv.estado,
                     (param.f_literal_periodo(pcv.id_periodo))
                     into
                     v_estado_periodo,
                     v_periodo_literal
              from conta.tperiodo_compra_venta pcv
              inner join param.tperiodo per on per.id_periodo = pcv.id_periodo
              where per.id_gestion = v_id_gestion_periodo and pcv.id_depto = v_id_depto_periodo
              and pcv.id_periodo_compra_venta > v_parametros.id_periodo_compra_venta
              order by pcv.id_periodo_compra_venta ASC
              limit 1;


             if (v_estado_periodo = 'cerrado' or v_estado_periodo = 'cerrado_parcial') then
             	raise exception 'El periodo <b>%</b> se encuentra en estado <b>%</b>, favor Abrir el periodo mencionado para realizar el cierre seleccionado',v_periodo_literal,v_estado_periodo;
             end if;
             /*****************************************************************************/







             /*Abrimos el periodo en la tabla tacumulacion comisionistas para traer la data*/
             update vef.tacumulacion_comisionistas set
             estado = v_estado
             where id_periodo >= (select per.id_periodo
             					 from conta.tperiodo_compra_venta per
             					 where per.id_periodo_compra_venta = v_parametros.id_periodo_compra_venta);


            END IF;

            -- modificado (breydi.vasquez) incremento de columans no registradas y
            -- control de veces que fueron cerradas y abiertas los periodos


            update conta.tperiodo_compra_venta pcv set
              estado = v_estado,
			  id_usuario_mod = p_id_usuario,
              fecha_mod = now(),
              id_usuario_ai = v_parametros._id_usuario_ai,
              usuario_ai = v_parametros._nombre_usuario_ai
            where pcv.id_periodo_compra_venta = v_parametros.id_periodo_compra_venta;

            -- registro log de cambios de periodos de compra contabilidad
            insert into conta.tlog_periodo_compra
                        (
                          id_usuario_reg,
                          fecha_reg,
                          estado_reg,
                          id_periodo_compra_venta,
                          estado,
                          id_usuario_ai,
                          observacion
                        )
                        VALUES (
                           p_id_usuario,
                           now(),
                          'activo',
                          v_parametros.id_periodo_compra_venta,
                          v_estado,
                          v_parametros._id_usuario_ai,
                          v_parametros.observacion
                        );

            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','periodo de libro de compra y ventas pasa al estado: ' || v_estado);
            v_resp = pxp.f_agrega_clave(v_resp,'id_periodo_compra_venta',v_parametros.id_periodo_compra_venta::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'CONTA_ABRCERAUT_IME'
 	#DESCRIPCION:	action cierra los periodos de libro de compras y ventas
 	#AUTOR:		    yamil.medina
 	#FECHA:			24-12-2019
	***********************************/

	elsif(p_transaccion='CONTA_ABRCERAUT_IME')then

		begin
			v_fecha_cerrado_parcial = pxp.f_get_variable_global('conta_periodo_cerrado_parcial');
            v_fecha_cerrado = pxp.f_get_variable_global('conta_periodo_cerrado');

        	if date_part('day',now())::integer in (v_fecha_cerrado::integer,v_fecha_cerrado_parcial::integer) then

                select id_periodo,
                        id_gestion
                    into v_id_periodo,
                         v_id_gestion
                  from param.tperiodo per
                       where per.fecha_ini <= now()::date-'1 month'::interval
                         and per.fecha_fin >= now()::date-'1 month'::interval
                         and per.id_gestion is not null
                         limit 1 offset 0;

                if(v_id_periodo is null)then
                    raise exception 'No existe periodo para la fecha %', g_fecha;
                end if;

                if date_part('day',now())::integer in (v_fecha_cerrado::integer) then
                    v_estado_actualizado = 'cerrado';
                elsif date_part('day',now())::integer in (v_fecha_cerrado_parcial::integer) then
                    v_estado_actualizado = 'cerrado_parcial';
                end if;

              if (v_estado_actualizado is not null or v_estado_actualizado != '')then
                for v_registros in select depto.id_depto
                                    from param.tdepto depto
                                    inner join segu.tsubsistema subsis on subsis.id_subsistema=depto.id_subsistema
                                    where depto.estado_reg ='activo'
                                    and subsis.codigo = 'CONTA'
                  loop

                        select pcv.id_periodo_compra_venta,
                               pcv.estado,
                               per.fecha_fin
                        into  v_perido_compra_venta
                        from conta.tperiodo_compra_venta pcv
                        inner join param.tperiodo per on per.id_periodo = pcv.id_periodo
                        where  per.id_gestion = v_id_gestion
                        and pcv.id_depto = v_registros.id_depto
                        and pcv.id_periodo = v_id_periodo;

                        --if  (v_perido_compra_venta.estado = 'abierto')then

                            update conta.tperiodo_compra_venta set
                            estado = v_estado_actualizado,
                            id_usuario_mod = 1,
                            fecha_mod = now()
                            where id_periodo_compra_venta = v_perido_compra_venta.id_periodo_compra_venta;

                            insert into conta.tlog_periodo_compra
                                ( id_usuario_reg,
                                  fecha_reg,
                                  estado_reg,
                                  id_periodo_compra_venta,
                                  estado,
                                  id_usuario_ai,
                                  observacion)
                                VALUES (
                                   364,
                                   now(),
                                  'activo',
                                  v_perido_compra_venta.id_periodo_compra_venta,
                                  v_estado_actualizado,
                                  null,
                                  'Cierre automatico programado por contabilidad');
                      --  end if;

                end loop;
              end if;
            end if;
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Proceso ejecutao con exito');
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

ALTER FUNCTION conta.ft_periodo_compra_venta_ime (p_administrador integer, p_id_usuario integer, p_tabla varchar, p_transaccion varchar)
  OWNER TO postgres;
