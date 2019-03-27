CREATE OR REPLACE FUNCTION conta.ft_comisionistas_ime (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Sistema de Contabilidad
 FUNCION: 		conta.ft_comisionistas_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'conta.tcomisionistas'
 AUTOR: 		 (admin)
 FECHA:	        31-05-2017 20:17:02
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
	v_id_comisionista		integer;
    v_revisado				varchar;
    v_registros_json		record;
    v_estado_gestion		varchar;
    v_reccord				record;
    v_registros			 	record;
    v_id_periodo			integer;
    v_id_deptor				integer;

    v_consulta				varchar;
	v_recort				record;
    v_periodo			integer;
    v_id_agencia		integer;
    v_nombre_agencia	varchar;
    v_recorer 			record;
    v_id_periodos		integer;
	v_cont				integer;
    v_numero			varchar;
    v_nit				varchar;
    v_id_agt			integer;
    v_gestion			integer;
    v_acm				record;
    v_acm_insertado		integer;
    v_fecha_max			date;
    v_fechas_periodo	record;
    v_contrato			record;
    v_numero_contrato	varchar;
    v_id_contrato		integer;
    v_nombre_tabla		varchar;
    v_contrato2			record;
    v_datos record;
BEGIN

    v_nombre_funcion = 'conta.ft_comisionistas_ime';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'CONTA_CMS_INS'
 	#DESCRIPCION:	Insercion de registros
 	#AUTOR:		MMV
 	#FECHA:		31-05-2017 20:17:02
	***********************************/

	if(p_transaccion='CONTA_CMS_INS')then

    	  select r.estado
          into v_estado_gestion
          from conta.tperiodo_resolucion r
          where r.id_periodo = v_parametros.id_periodo and r.id_depto = v_parametros.id_depto_conta;

          IF v_estado_gestion = 'cerrado' THEN
        	RAISE EXCEPTION '%','PERIODO CERRADO';
      	  END IF;

        begin
        	--Sentencia de la insercion
        	insert into conta.tcomisionistas(
			nit_comisionista,
			nro_contrato,
			codigo_producto,
			estado_reg,
			descripcion_producto,
			cantidad_total_entregado,
			cantidad_total_vendido,
			precio_unitario,
			monto_total,
			monto_total_comision,
			id_usuario_reg,
			usuario_ai,
			fecha_reg,
			id_usuario_ai,
			fecha_mod,
			id_usuario_mod,
            id_periodo,
            id_depto_conta
          	) values(
			v_parametros.nit_comisionista,
			v_parametros.nro_contrato,
			v_parametros.codigo_producto,
			'activo',
			v_parametros.descripcion_producto,
			v_parametros.cantidad_total_entregado,
			v_parametros.cantidad_total_vendido,
			v_parametros.precio_unitario,
			v_parametros.monto_total,
			v_parametros.monto_total_comision,
			p_id_usuario,
			v_parametros._nombre_usuario_ai,
			now(),
			v_parametros._id_usuario_ai,
			null,
			null,
            v_parametros.id_periodo,
            v_parametros.id_depto_conta
			)RETURNING id_comisionista into v_id_comisionista;

			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Comisionistas  almacenado(a) con exito (id_comisionista'||v_id_comisionista||')');
            v_resp = pxp.f_agrega_clave(v_resp,'id_comisionista',v_id_comisionista::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'CONTA_CMS_MOD'
 	#DESCRIPCION:	Modificacion de registros
 	#AUTOR:		MMV
 	#FECHA:		31-05-2017 20:17:02
	***********************************/

	elsif(p_transaccion='CONTA_CMS_MOD')then

		begin
          select r.estado
          into v_estado_gestion
          from conta.tperiodo_resolucion r
          where r.id_periodo = v_parametros.id_periodo and r.id_depto = v_parametros.id_depto_conta;

          IF v_estado_gestion = 'cerrado' THEN
        	RAISE EXCEPTION '%','PERIODO CERRADO';
      	  END IF;
			--Sentencia de la modificacion
			update conta.tcomisionistas set
			nit_comisionista = v_parametros.nit_comisionista,
			nro_contrato = v_parametros.nro_contrato,
			codigo_producto = v_parametros.codigo_producto,
			descripcion_producto = v_parametros.descripcion_producto,
			cantidad_total_entregado = v_parametros.cantidad_total_entregado,
			cantidad_total_vendido = v_parametros.cantidad_total_vendido,
			precio_unitario = v_parametros.precio_unitario,
			monto_total = v_parametros.monto_total,
			monto_total_comision = v_parametros.monto_total_comision,
			fecha_mod = now(),
			id_usuario_mod = p_id_usuario,
			id_usuario_ai = v_parametros._id_usuario_ai,
			usuario_ai = v_parametros._nombre_usuario_ai
			where id_comisionista=v_parametros.id_comisionista;

			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Comisionistas  modificado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_comisionista',v_parametros.id_comisionista::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'CONTA_CMS_ELI'
 	#DESCRIPCION:	Eliminacion de registros
 	#AUTOR:		MMV
 	#FECHA:		20-06-2017 20:17:02
	***********************************/

	elsif(p_transaccion='CONTA_CMS_ELI')then

		begin
          select 	id_periodo,
        			id_depto_conta
                    into
                    v_id_periodo,
                    v_id_deptor
               	 	from conta.tcomisionistas
                	where id_comisionista = v_parametros.id_comisionista;

        select r.estado
        into
        v_estado_gestion
        from conta.tperiodo_resolucion r
        where r.id_periodo = v_id_periodo and r.id_depto = v_id_deptor ;

          IF v_estado_gestion = 'cerrado' THEN
        	RAISE EXCEPTION '%','PERIODO CERRADO';
      	  END IF;
			--Sentencia de la eliminacion
			delete from conta.tcomisionistas
            where id_comisionista=v_parametros.id_comisionista;

            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Comisionistas  eliminado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_comisionista',v_parametros.id_comisionista::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;
    /*********************************
 	#TRANSACCION:  'CONTA_REV_IME'
 	#DESCRIPCION:	Control revision
 	#AUTOR:		MMV
 	#FECHA:		14-06-2017
	***********************************/
	elsif (p_transaccion='CONTA_REV_IME')then

        begin

            select co.revisado
            		into
                    v_revisado
            from conta.tcomisionistas co
			where co.id_comisionista = v_parametros.id_comisionista;

            if v_revisado = 'si' then
            update conta.tcomisionistas set
            revisado = 'no'
            where id_comisionista = v_parametros.id_comisionista;
            end if;
            if v_revisado = 'no' then
            update conta.tcomisionistas set
            revisado = 'si'
            where id_comisionista = v_parametros.id_comisionista;
            end if;

			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Revision con exito (id_comisionista'||v_parametros.id_comisionista||')');
            v_resp = pxp.f_agrega_clave(v_resp,'id_comisionista',v_parametros.id_comisionista::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;
        /*********************************
 #TRANSACCION: 'CONTA_REV_IMP'
 #DESCRIPCION:	Importacion de archivo txt
 #AUTOR:		MMV
 #FECHA:		20-06-2017 20:17:02
***********************************/

  ELSIF (p_transaccion='CONTA_REV_IMP')THEN

      BEGIN
        FOR v_registros_json IN (SELECT *
                                 FROM json_populate_recordset(NULL :: conta.json_comisionistas,
                                                              v_parametros.arra_json :: JSON)) LOOP


          --verificamos la gestion si esta abierta

          select r.estado
          into v_estado_gestion
          from conta.tperiodo_resolucion r
          where r.id_periodo = v_parametros.id_periodo and r.id_depto = v_parametros.id_depto_conta;

          IF v_estado_gestion = 'cerrado' THEN
        	RAISE EXCEPTION '%','PERIODO CERRADO';
      	  END IF;

          IF EXISTS (select 1
                      from obingresos.tagencia a
                      where a.nit = v_registros_json.nit_comisionista::varchar) THEN
          		select  a.id_agencia,
                		a.nombre
                        into
                        v_id_agencia,
                        v_nombre_agencia
                from obingresos.tagencia a
                where a.nit = v_registros_json.nit_comisionista::varchar;
          ELSE
          		RAISE EXCEPTION 'No existe una agencia con el nit %',v_registros_json.nit_comisionista::varchar;
          END IF;


		insert into conta.tcomisionistas(
			nit_comisionista,
			nro_contrato,
			codigo_producto,
			estado_reg,
			descripcion_producto,
			cantidad_total_entregado,
			cantidad_total_vendido,
			precio_unitario,
			monto_total,
			monto_total_comision,
			id_usuario_reg,
			usuario_ai,
			fecha_reg,
			id_usuario_ai,
			fecha_mod,
			id_usuario_mod,
            id_periodo,
            id_depto_conta,
            nro_boleto,
            id_agencia,
            nombre_agencia,
            registro
          	) values(
			v_registros_json.nit_comisionista::varchar,
			v_registros_json.nro_contrato::varchar,
			v_registros_json.codigo_producto::varchar,
			'activo',
			v_registros_json.descripcion_producto::varchar,
			1,
			1,
			v_registros_json.precio_unitario::numeric(10,2),
			v_registros_json.monto_total::numeric(10,2),
			v_registros_json.monto_total_comision::numeric(10,2),
			p_id_usuario,
			null,
			now(),
			null,
			null,
			null,
            v_parametros.id_periodo::integer,
            v_parametros.id_depto_conta::integer,
            v_registros_json.nro_boleto::varchar,
            v_id_agencia,
            v_nombre_agencia,
            'importe_txt'
			);

      END LOOP;


insert  into conta.trevisar_comisionistas (	  nombre_agencia,
                                              nit_comisionista,
                                              nro_contrato,
                                              precio_unitario ,
                                              monto_total,
                                              monto_total_comision,
                                              id_periodo ,
                                              id_depto_conta ,
                                              id_usuario_reg,
                                              id_agencia
                                              )select	c.nombre_agencia,
                                                        c.nit_comisionista,
                                                        c.nro_contrato,
                                                        sum(c.precio_unitario) as precio_unitario,
                                                        sum(c.monto_total) as monto_total,
                                                        sum(c.monto_total_comision) as monto_total_comision,
                                                        v_parametros.id_periodo::integer as id_periodo,
                                                        v_parametros.id_depto_conta::integer as id_depto_conta,
                                                        p_id_usuario::integer as id_usuario_reg,
                                                        c.id_agencia
                                                        from conta.tcomisionistas c
                                                        where c.id_periodo = v_parametros.id_periodo  and c.id_depto_conta = v_parametros.id_depto_conta and c.id_agencia = v_id_agencia
                                                        group by c.nombre_agencia,c.nit_comisionista,c.nro_contrato,c.id_agencia;


        --Definicion de la respuesta
        v_resp = pxp.f_agrega_clave(v_resp, 'mensaje', 'eliminado(a)');
        v_resp = pxp.f_agrega_clave(v_resp, 'id_comisionista', 10 :: VARCHAR);

        --Devuelve la respuesta
        RETURN v_resp;
      END;
        /*********************************
     #TRANSACCION: 'CONTA_REV_ELITO'
     #DESCRIPCION:	Elimina todos los registros
     #AUTOR:		MMV
     #FECHA:		20-06-2017 20:17:02
    ***********************************/

  ELSIF (p_transaccion='CONTA_REV_ELITO')
    THEN

      BEGIN

          select r.estado
          into v_estado_gestion
          from conta.tperiodo_resolucion r
          where r.id_periodo = v_parametros.id_periodo and r.id_depto = v_parametros.id_depto_conta;

          IF v_estado_gestion = 'cerrado' THEN
        	RAISE EXCEPTION '%','PERIODO CERRADO';
      	  END IF;

        DELETE FROM conta.tcomisionistas cm
        WHERE cm.id_periodo = v_parametros.id_periodo
              AND cm.id_depto_conta = v_parametros.id_depto_conta
              AND cm.revisado = 'no';

        --Definicion de la respuesta
        v_resp = pxp.f_agrega_clave(v_resp, 'mensaje', 'eliminado(a)');

        --Devuelve la respuesta
        RETURN v_resp;

      END;
	 /*********************************
       #TRANSACCION: 'CONTA_REV_CLON'
       #DESCRIPCION: clona de registros
       #AUTOR:		MMV
       #FECHA:		20-06-2017 20:17:02
      ***********************************/

  ELSIF (p_transaccion='CONTA_REV_CLON') THEN

      BEGIN


        --Sentencia de la eliminacion
		--raise exception 'llega %',v_parametros.id_simplificado;
        SELECT *
        INTO v_reccord
        FROM conta.tcomisionistas
        where id_comisionista = v_parametros.id_comisionista;

        /*IF v_reccord.revisado = 'si' THEN
          RAISE EXCEPTION '%','NO SE PUEDE CLONAR CUANDO ESTA REVISADO ';
        END IF;*/
           select 	id_periodo,
        			id_depto_conta
                    into
                    v_id_periodo,
                    v_id_deptor
               	 	from conta.tcomisionistas
                	where id_comisionista = v_parametros.id_comisionista;

        select r.estado
        into
        v_estado_gestion
        from conta.tperiodo_resolucion r
        where r.id_periodo = v_id_periodo and r.id_depto = v_id_deptor ;
        IF v_estado_gestion = 'cerrado' THEN
        	RAISE EXCEPTION '%','PERIODO CERRADO';
      	END IF;

		insert into conta.tcomisionistas(
			nit_comisionista,
			nro_contrato,
			codigo_producto,
			estado_reg,
			descripcion_producto,
			cantidad_total_entregado,
			cantidad_total_vendido,
			precio_unitario,
			monto_total,
			monto_total_comision,
			id_usuario_reg,
			usuario_ai,
			fecha_reg,
			id_usuario_ai,
			fecha_mod,
			id_usuario_mod,
            id_periodo,
            id_depto_conta,
            registro,
            tipo_comisionista
          	) values(
			v_reccord.nit_comisionista,
			v_reccord.nro_contrato,
			v_reccord.codigo_producto,
			'activo',
			v_reccord.descripcion_producto,
			v_reccord.cantidad_total_entregado,
			v_reccord.cantidad_total_vendido,
			v_reccord.precio_unitario,
			v_reccord.monto_total,
			v_reccord.monto_total_comision,
			p_id_usuario,
			v_parametros._nombre_usuario_ai,
			now(),
			v_parametros._id_usuario_ai,
			null,
			null,
            v_reccord.id_periodo,
            v_reccord.id_depto_conta,
			'normal',
            'clonado'
			);


        --Definicion de la respuesta
        v_resp = pxp.f_agrega_clave(v_resp, 'mensaje', 'clonado(a)');
        v_resp = pxp.f_agrega_clave(v_resp, 'id_comisionista', v_parametros.id_comisionista :: VARCHAR);

        --Devuelve la respuesta
        RETURN v_resp;

      END;

      /*********************************
       #TRANSACCION: 'CONTA_REV_NEGR'
       #DESCRIPCION:  lista negra
       #AUTOR:		  MMV
       #FECHA:		  20-06-2017 20:17:02
      ***********************************/

  ELSIF (p_transaccion='CONTA_REV_NEGR') THEN

      BEGIN

          select r.estado
          into v_estado_gestion
          from conta.tperiodo_resolucion r
          where r.id_periodo = v_parametros.id_periodo and r.id_depto = v_parametros.id_depto_conta;

          IF v_estado_gestion = 'cerrado' THEN
        	RAISE EXCEPTION '%','PERIODO CERRADO';
      	  END IF;

        select *
        into
        v_registros
        from conta.tcomisionistas
        where id_comisionista = v_parametros.id_comisionista;

        if v_registros.lista_negra = 'si'then
        update conta.tcomisionistas set
        lista_negra = 'no'
        where id_comisionista = v_parametros.id_comisionista;
        else
        update conta.tcomisionistas set
        lista_negra = 'si'
        where id_comisionista = v_parametros.id_comisionista;
  		end if;
        v_resp = pxp.f_agrega_clave(v_resp, 'mensaje', 'agregado a la lista negra(a)');
   		RETURN v_resp;
   END;

   	/*********************************
 	#TRANSACCION:  'CONTA_CMS_AUN'
 	#DESCRIPCION:	Insercion de registros automatico
 	#AUTOR:		MMV
 	#FECHA:		31-05-2017 20:17:02
	***********************************/
     ELSIF (p_transaccion='CONTA_CMS_AUN') THEN

      BEGIN


      IF  (SELECT max(x.id_periodo)
          from conta.tcomisionistas x) = v_parametros.id_periodo THEN

           raise exception 'Ya existe datos generados para este mes.';
      ELSE

      select p.periodo
               into
               v_periodo
      from param.tperiodo p
      where p.id_periodo = v_parametros.id_periodo and p.id_gestion = v_parametros.id_gestion;

      SELECT ge.gestion
      INTO v_gestion
      FROM param.tgestion ge
      WHERE ge.id_gestion = v_parametros.id_gestion;

      select
      pe.fecha_ini,
      pe.fecha_fin
      into v_fechas_periodo
      from param.tperiodo pe
      where pe.id_periodo = v_parametros.id_periodo;


     FOR v_reccord IN (select 	a.id_agencia,
            a.nombre,
            a.nit,
            '' as codigo,
            'Venta de Servicio de Transporte Aereo' as descripcion,
            1 cantidad,
            bo.neto,
            bo.total,
          (-1* bo.comision) as total_comision,
			bo.nro_boleto,
            bo.fecha_emision
      from obingresos.tagencia a
      inner join obingresos.tboleto bo on bo.id_agencia = a.id_agencia
      where bo.fecha_emision between v_fechas_periodo.fecha_ini and v_fechas_periodo.fecha_fin
      and a.boaagt = 'A' and a.tipo_agencia = 'noiata' and (-1* bo.comision) <> 0 and bo.estado_reg = 'activo'
      --and  EXTRACT(MONTH FROM bo.fecha_emision) = v_periodo
      order by a.nombre


)LOOP

select
co.id_agencia,
co.id_contrato,
co.fecha_inicio,
co.fecha_fin,
co.numero
into v_contrato
from leg.tcontrato co
where co.id_agencia = v_reccord.id_agencia
and (co.fecha_inicio <= v_fechas_periodo.fecha_fin AND co.fecha_fin >= v_fechas_periodo.fecha_ini)
and v_reccord.fecha_emision between co.fecha_inicio and co.fecha_fin;



	insert into conta.tcomisionistas(
			nit_comisionista,
			nro_contrato,
			codigo_producto,
			estado_reg,
			descripcion_producto,
			cantidad_total_entregado,
			cantidad_total_vendido,
			precio_unitario,
			monto_total,
			monto_total_comision,
			id_usuario_reg,
			usuario_ai,
			fecha_reg,
			id_usuario_ai,
			fecha_mod,
			id_usuario_mod,
            id_periodo,
            id_depto_conta,
            registro,
            nombre_agencia,
            revisado,
            nro_boleto,
            id_agencia,
            id_contrato
          	) values(
			v_reccord.nit,
           	RIGHT(v_contrato.numero,19),
			v_reccord.codigo,
			'activo',
			v_reccord.descripcion,
			v_reccord.cantidad,
			v_reccord.cantidad,
			v_reccord.neto,
			v_reccord.total,
			v_reccord.total_comision,
			p_id_usuario,
			v_parametros._nombre_usuario_ai,
			now(),
			v_parametros._id_usuario_ai,
			null,
			null,
            v_parametros.id_periodo,
            v_parametros.id_depto_conta,
            'automatico',
            v_reccord.nombre,
            'no',
            v_reccord.nro_boleto,
            v_reccord.id_agencia,
            v_contrato.id_contrato
			);



END LOOP;

/************************************************************************************************/
/********************************RECUPERACION DE ACM PARA INSERSION******************************/
FOR v_acm IN (select
                  a.id_agencia,
                  a.nombre,
                  a.nit,
                  (select  RIGHT(c.numero,19)
                  from leg.tcontrato c
                  where c.id_agencia = a.id_agencia and c.fecha_fin = (select max(d.fecha_fin)
                  from leg.tcontrato d
                  where d.id_agencia = a.id_agencia)) as nro_contrato,
                  '' as codigo,
                  'Venta de Servicio de Transporte Aereo' as descripcion,
                  1 cantidad,
                  sum(det.neto) as neto,
                  sum(det.neto) as total,
                  mov.monto_total as total_comision,
                  mov.autorizacion__nro_deposito as nro_acm,
                  mov.fecha
                  --bo.nro_boleto
            from obingresos.tagencia a
            inner join obingresos.tmovimiento_entidad mov on mov.id_agencia=a.id_agencia
            inner join obingresos.tacm acm on acm.id_movimiento_entidad = mov.id_movimiento_entidad
            inner join obingresos.tacm_det det on det.id_acm = acm.id_acm
            where mov.fecha between v_fechas_periodo.fecha_ini and v_fechas_periodo.fecha_fin
            /*a.boaagt = 'A' and*/
            and a.tipo_agencia = 'noiata'
            /*and (-1* bo.comision) <> 0 and bo.estado_reg = 'activo'*/
            group by a.id_agencia, mov.autorizacion__nro_deposito, mov.monto_total,mov.fecha--,bo.nro_boleto
            order by a.nombre
)LOOP

select
co.id_agencia,
co.id_contrato,
co.fecha_inicio,
co.fecha_fin,
co.numero
into v_contrato
from leg.tcontrato co
where co.id_agencia = v_acm.id_agencia
and (co.fecha_inicio <= v_fechas_periodo.fecha_fin AND co.fecha_fin >= v_fechas_periodo.fecha_ini)
and v_acm.fecha between co.fecha_inicio and co.fecha_fin;

	insert into conta.tcomisionistas(
			nit_comisionista,
			nro_contrato,
			codigo_producto,
			estado_reg,
			descripcion_producto,
			cantidad_total_entregado,
			cantidad_total_vendido,
			precio_unitario,
			monto_total,
			monto_total_comision,
			id_usuario_reg,
			usuario_ai,
			fecha_reg,
			id_usuario_ai,
			fecha_mod,
			id_usuario_mod,
            id_periodo,
            id_depto_conta,
            registro,
            nombre_agencia,
            revisado,
            nro_boleto,
            id_agencia,
            id_contrato
          	) values(
			v_acm.nit,
            RIGHT(v_contrato.numero,19),
			v_acm.codigo,
			'activo',
			v_acm.descripcion,
			v_acm.cantidad,
			v_acm.cantidad,
			v_acm.neto,
			v_acm.total,
			v_acm.total_comision,
			p_id_usuario,
			v_parametros._nombre_usuario_ai,
			now(),
			v_parametros._id_usuario_ai,
			null,
			null,
            v_parametros.id_periodo,
            v_parametros.id_depto_conta,
            'automatico',
            v_acm.nombre,
            'no',
            v_acm.nro_acm,
            v_acm.id_agencia,
            v_acm.id_contrato
			);
END LOOP;



FOR v_recorer IN( select								c.nombre_agencia,
                                                        c.nit_comisionista,
                                                        c.nro_contrato,
                                                        sum(c.precio_unitario) as precio_unitario,
                                                        sum(c.monto_total) as monto_total,
                                                        sum(c.monto_total_comision) as monto_total_comision,
                                                        c.id_agencia,
                                                        c.id_contrato
                                                        from conta.tcomisionistas c
                                                        where c.id_periodo = v_parametros.id_periodo -- and c.id_depto_conta = v_parametros.id_depto_conta
                                                        group by c.nombre_agencia,c.nit_comisionista,c.nro_contrato,c.id_agencia,c.id_contrato)LOOP

IF EXISTS (select 1
           from conta.trevisar_comisionistas d
           where d.id_agencia = v_recorer.id_agencia and
           d.id_periodo = v_parametros.id_periodo - 1) THEN
           v_revisado = 'si';
             update conta.tcomisionistas set
            revisado = 'si'
            where id_agencia = v_recorer.id_agencia and id_periodo = v_parametros.id_periodo;
           ELSE
           v_revisado = 'no';
           END IF;


insert  into conta.trevisar_comisionistas (	  nombre_agencia,
                                              nit_comisionista,
                                              nro_contrato,
                                              precio_unitario ,
                                              monto_total,
                                              monto_total_comision,
                                              id_periodo ,
                                              id_depto_conta ,
                                              id_usuario_reg,
                                              id_agencia,
                                              revisado,
                                              id_contrato
                                              )VALUES(
                                              v_recorer.nombre_agencia,
                                              v_recorer.nit_comisionista,
                                              RIGHT(v_recorer.nro_contrato,19),
                                              v_recorer.precio_unitario,
                                              v_recorer.monto_total,
                                              v_recorer.monto_total_comision,
                                              v_parametros.id_periodo,
                                              v_parametros.id_depto_conta,
                                              p_id_usuario,
                                              v_recorer.id_agencia,
                                              v_revisado,
                                              v_recorer.id_contrato
                                              --v_contrato.id_contrato
                                              );

		/*select  rc.id_agencia,
        		rc.nro_contrato,
        		rc.nit_comisionista
                into
                v_id_agt,
                v_numero,
                v_nit
        from conta.trevisar_comisionistas rc
        where rc.id_agencia = v_recorer.id_agencia and rc.id_periodo = v_parametros.id_periodo -1 ;

        update conta.trevisar_comisionistas set
        nit_comisionista = v_nit,
        nro_contrato = v_numero
        where id_agencia = v_id_agt and id_periodo = v_parametros.id_periodo;

         update conta.tcomisionistas set
        nit_comisionista = v_nit,
        nro_contrato = RIGHT( v_numero,19)
        where id_agencia = v_id_agt and id_periodo = v_parametros.id_periodo;
*/


	END LOOP;
END IF;

        v_resp = pxp.f_agrega_clave(v_resp, 'mensaje', 'anexos actualizaciones automatic(a)');
        v_resp = pxp.f_agrega_clave(v_resp, 'id_depto_conta', v_parametros.id_depto_conta :: VARCHAR);

        --Devuelve la respuesta
        RETURN v_resp;
     END;


/*********************************
 	#TRANSACCION:  'CONTA_CMS_ACM'
 	#DESCRIPCION:	Insercion de registros automatico
 	#AUTOR:		MMV
 	#FECHA:		31-05-2017 20:17:02
	***********************************/
     ELSIF (p_transaccion='CONTA_CMS_ACM') THEN

      BEGIN

      select p.periodo
               into
               v_periodo
      from param.tperiodo p
      where p.id_periodo = v_parametros.id_periodo and p.id_gestion = v_parametros.id_gestion;

      SELECT ge.gestion
      INTO v_gestion
      FROM param.tgestion ge
      WHERE ge.id_gestion = v_parametros.id_gestion;

      select
      pe.fecha_ini,
      pe.fecha_fin
      into v_fechas_periodo
      from param.tperiodo pe
      where pe.id_periodo = v_parametros.id_periodo;

/************************************************************************************************/
/********************************RECUPERACION DE ACM PARA INSERSION******************************/
FOR v_acm IN (select
                  a.id_agencia,
                  a.nombre,
                  a.nit,
                  (select  RIGHT(c.numero,19)
                  from leg.tcontrato c
                  where c.id_agencia = a.id_agencia and c.fecha_fin = (select max(d.fecha_fin)
                  from leg.tcontrato d
                  where d.id_agencia = a.id_agencia)) as nro_contrato,
                  '' as codigo,
                  'Venta de Servicio de Transporte Aereo' as descripcion,
                  1 cantidad,
                  sum(det.neto) as neto,
                  sum(det.neto) as total,
                  mov.monto_total as total_comision,
                  mov.autorizacion__nro_deposito as nro_acm,
                  mov.fecha
                  --bo.nro_boleto
            from obingresos.tagencia a
            inner join obingresos.tmovimiento_entidad mov on mov.id_agencia=a.id_agencia
            inner join obingresos.tacm acm on acm.id_movimiento_entidad = mov.id_movimiento_entidad
            inner join obingresos.tacm_det det on det.id_acm = acm.id_acm
            where mov.fecha between v_fechas_periodo.fecha_ini and v_fechas_periodo.fecha_fin
            and a.tipo_agencia = 'noiata'
            group by a.id_agencia, mov.autorizacion__nro_deposito, mov.monto_total,mov.fecha--,bo.nro_boleto
            order by a.nombre
)LOOP

select 1
into v_acm_insertado
from conta.tcomisionistas comi
where comi.nro_boleto = v_acm.nro_acm;

select
co.id_agencia,
co.id_contrato,
co.fecha_inicio,
co.fecha_fin,
co.numero
into v_contrato
from leg.tcontrato co
where co.id_agencia = v_acm.id_agencia
and (co.fecha_inicio <= v_fechas_periodo.fecha_fin AND co.fecha_fin >= v_fechas_periodo.fecha_ini)
and v_acm.fecha between co.fecha_inicio and co.fecha_fin;

--raise exception 'NUMERO ES %',v_acm_insertado;
IF (v_acm_insertado <> 1 or v_acm_insertado is null) then
	insert into conta.tcomisionistas(
			nit_comisionista,
			nro_contrato,
			codigo_producto,
			estado_reg,
			descripcion_producto,
			cantidad_total_entregado,
			cantidad_total_vendido,
			precio_unitario,
			monto_total,
			monto_total_comision,
			id_usuario_reg,
			usuario_ai,
			fecha_reg,
			id_usuario_ai,
			fecha_mod,
			id_usuario_mod,
            id_periodo,
            id_depto_conta,
            registro,
            nombre_agencia,
            revisado,
            nro_boleto,
            id_agencia
          	) values(
			v_acm.nit,
            RIGHT(v_contrato.numero,19),
			v_acm.codigo,
			'activo',
			v_acm.descripcion,
			v_acm.cantidad,
			v_acm.cantidad,
			v_acm.neto,
			v_acm.total,
			v_acm.total_comision,
			p_id_usuario,
			v_parametros._nombre_usuario_ai,
			now(),
			v_parametros._id_usuario_ai,
			null,
			null,
            v_parametros.id_periodo,
            v_parametros.id_depto_conta,
            'automatico',
            v_acm.nombre,
            'no',
            v_acm.nro_acm,
            v_acm.id_agencia,
            v_acm.id_contrato
			);
    ELSE
    raise exception 'Los ACMS ya fueron generados para este periodo';
    end if;
END LOOP;



FOR v_recorer IN( select								c.nombre_agencia,
                                                        c.nit_comisionista,
                                                        c.nro_contrato,
                                                        sum(c.precio_unitario) as precio_unitario,
                                                        sum(c.monto_total) as monto_total,
                                                        sum(c.monto_total_comision) as monto_total_comision,
                                                        c.id_agencia,
                                                        c.id_contrato
                                                        from conta.tcomisionistas c
                                                        where c.id_periodo = v_parametros.id_periodo -- and c.id_depto_conta = v_parametros.id_depto_conta
                                                        group by c.nombre_agencia,c.nit_comisionista,c.nro_contrato,c.id_agencia,c.id_contrato)LOOP


IF EXISTS (select 1
           from conta.trevisar_comisionistas d
           where d.id_agencia = v_recorer.id_agencia and
           d.id_periodo = v_parametros.id_periodo - 1) THEN
           v_revisado = 'si';
             update conta.tcomisionistas set
            revisado = 'si'
            where id_agencia = v_recorer.id_agencia and id_periodo = v_parametros.id_periodo;
           ELSE
           v_revisado = 'no';
           END IF;


insert  into conta.trevisar_comisionistas (	  nombre_agencia,
                                              nit_comisionista,
                                              nro_contrato,
                                              precio_unitario ,
                                              monto_total,
                                              monto_total_comision,
                                              id_periodo ,
                                              id_depto_conta ,
                                              id_usuario_reg,
                                              id_agencia,
                                              revisado,
                                              id_contrato
                                              )VALUES(
                                              v_recorer.nombre_agencia,
                                              v_recorer.nit_comisionista,
                                              v_contrato.numero,
                                              v_recorer.precio_unitario,
                                              v_recorer.monto_total,
                                              v_recorer.monto_total_comision,
                                              v_parametros.id_periodo,
                                              v_parametros.id_depto_conta,
                                              p_id_usuario,
                                              v_recorer.id_agencia,
                                              v_revisado,
                                              v_recorer.id_contrato
                                              );

		/*select  rc.id_agencia,
        		rc.nro_contrato,
        		rc.nit_comisionista
                into
                v_id_agt,
                v_numero,
                v_nit
        from conta.trevisar_comisionistas rc
        where rc.id_agencia = v_recorer.id_agencia and rc.id_periodo = v_parametros.id_periodo -1 ;

        update conta.trevisar_comisionistas set
        nit_comisionista = v_nit,
        nro_contrato = v_numero
        where id_agencia = v_id_agt and id_periodo = v_parametros.id_periodo;

         update conta.tcomisionistas set
        nit_comisionista = v_nit,
        nro_contrato = RIGHT( v_numero,19)
        where id_agencia = v_id_agt and id_periodo = v_parametros.id_periodo;*/





	END LOOP;



        v_resp = pxp.f_agrega_clave(v_resp, 'mensaje', 'anexos actualizaciones automatic(a)');
        v_resp = pxp.f_agrega_clave(v_resp, 'id_depto_conta', v_parametros.id_depto_conta :: VARCHAR);

        --Devuelve la respuesta
        RETURN v_resp;
     END;
       /*********************************
 	#TRANSACCION:  'CONTA_RECA_IME'
 	#DESCRIPCION:	Control revision catalogo
 	#AUTOR:		MMV
 	#FECHA:		14-06-2017
	***********************************/
	elsif (p_transaccion='CONTA_RECA_IME')then

        begin

            select co.revisado,
            		co.id_agencia,
                    co.id_periodo
            		into
                    v_revisado,
                    v_id_agencia,
                    v_id_periodos
            from conta.trevisar_comisionistas co
			where co.id_comisionista_rev = v_parametros.id_comisionista_rev;

            if v_revisado = 'si' then
            update conta.trevisar_comisionistas set
            revisado = 'no'
            where id_comisionista_rev = v_parametros.id_comisionista_rev;

            update conta.tcomisionistas set
            revisado = 'no'
            where id_agencia = v_id_agencia and id_periodo = v_id_periodos ;

            end if;
            if v_revisado = 'no' then
            update conta.trevisar_comisionistas set
            revisado = 'si'
           	where id_comisionista_rev = v_parametros.id_comisionista_rev;

            update conta.tcomisionistas set
            revisado = 'si'
            where id_agencia = v_id_agencia and id_periodo = v_id_periodos ;
            end if;

			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Revision con exito (id_comisionista'||v_parametros.id_comisionista_rev||')');
            v_resp = pxp.f_agrega_clave(v_resp,'id_comisionista_rev',v_parametros.id_comisionista_rev::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;
         /*********************************
 	#TRANSACCION:  'CONTA_REM_IME'
 	#DESCRIPCION:	Control revision catalogo
 	#AUTOR:		MMV
 	#FECHA:		14-06-2017
	***********************************/
	elsif (p_transaccion='CONTA_REM_IME')then

        begin
		    UPDATE  conta.trevisar_comisionistas SET
            id_usuario_mod = p_id_usuario,
            fecha_mod = now(),
            id_usuario_ai = v_parametros._id_usuario_ai,
            usuario_ai = v_parametros._nombre_usuario_ai,
            nit_comisionista = v_parametros.nit_comisionista,
            nro_contrato = v_parametros.nro_contrato
            WHERE   id_comisionista_rev = v_parametros.id_comisionista_rev;

            select  c.id_periodo,
            	 	c.id_agencia
                    into
                    v_id_periodos,
                    v_id_agencia

            from conta.trevisar_comisionistas  c
            where c.id_comisionista_rev = v_parametros.id_comisionista_rev;


            UPDATE  conta.tcomisionistas  set
            nro_contrato = RIGHT(v_parametros.nro_contrato,19),
            nit_comisionista = v_parametros.nit_comisionista
            where id_agencia = v_id_agencia and id_periodo = v_id_periodos;


            UPDATE  obingresos.tagencia  set
            nit = v_parametros.nit_comisionista
            where id_agencia = v_id_agencia;

            /*select comi.id_contrato
            into v_contrato
            from conta.trevisar_comisionistas comi
            where comi.id_comisionista_rev = v_parametros.id_comisionista_rev; */

            select
            con.id_contrato,
            con.nro_contrato,
        	con.id_agencia
            into v_contrato
            from conta.trevisar_comisionistas con
            where con.id_comisionista_rev = v_parametros.id_comisionista_rev;

            if(v_contrato.id_contrato is not null) THEN

            UPDATE  leg.tcontrato  set
            numero = v_parametros.nro_contrato
            where id_contrato = v_contrato.id_contrato;

            end if;


			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Revision con exito (id_comisionista'||v_parametros.id_comisionista_rev||')');
            v_resp = pxp.f_agrega_clave(v_resp,'id_comisionista_rev',v_parametros.id_comisionista_rev::varchar);

            --Devuelve la respuesta
            return v_resp;
            end;
 /*********************************
 	#TRANSACCION:  'CONTA_REM_IST'
 	#DESCRIPCION:	insertar por agencia
 	#AUTOR:		MMV
 	#FECHA:		16-94-2018
	***********************************/
    elsif (p_transaccion='CONTA_REM_IST')then

        begin


          select p.periodo
               into
               v_periodo
      from param.tperiodo p
      where p.id_periodo = v_parametros.id_periodo ;

      select
      pe.fecha_ini,
      pe.fecha_fin
      into v_fechas_periodo
      from param.tperiodo pe
      where pe.id_periodo = v_parametros.id_periodo;


      select count(b.id_boleto)
      into
      v_cont
      from obingresos.tboleto b
      where b.fecha_emision between v_fechas_periodo.fecha_ini and v_fechas_periodo.fecha_fin
      and b.id_agencia = v_parametros.id_agencia /*and  EXTRACT(MONTH FROM b.fecha_emision) = v_periodo*/;

     if v_cont = 0 then
     raise exception 'La agencia no cuenta con comisones';
     end if;

 ---   raise exception '%',v_parametros.id_agencia;
     FOR v_reccord IN (/*select
     a.id_agencia,
            a.nombre,
            a.nit,
            (select  RIGHT(c.numero,19)
            from leg.tcontrato c
            where c.id_agencia = a.id_agencia and c.fecha_fin = (select max(d.fecha_fin)
			from leg.tcontrato d
			where d.id_agencia = a.id_agencia)) as nro_contrato,
            '' as codigo,
            'Venta de Servicio de Transporte Aereo' as descripcion,
            1 cantidad,
            bo.neto,
            bo.total,
          (-1* bo.comision) as total_comision,
			bo.nro_boleto
      from obingresos.tagencia a
      inner join obingresos.tboleto_2018 bo on bo.id_agencia = a.id_agencia
      where a.boaagt = 'A' and a.tipo_agencia = 'noiata' and (-1* bo.comision) <> 0 and bo.estado_reg = 'activo'
      and  EXTRACT(MONTH FROM bo.fecha_emision) = v_periodo  and a.id_agencia = v_parametros.id_agencia
      order by a.nombre*/

      select  a.id_agencia,
            a.nombre,
            a.nit,
            (select  RIGHT(c.numero,19)
            from leg.tcontrato c
            where c.id_agencia = a.id_agencia and c.fecha_fin = (select max(d.fecha_fin)
			from leg.tcontrato d
			where d.id_agencia = a.id_agencia)) as nro_contrato,
            '' as codigo,
            'Venta de Servicio de Transporte Aereo' as descripcion,
            1 cantidad,
            (case when  bo.id_moneda_boleto = 1 then
             bo.neto
            else
              param.f_convertir_moneda(2,1,bo.neto,now()::date,'O',2)
           end) as neto,
            (case when bo.id_moneda_boleto = 1 then
             bo.total
           else
              param.f_convertir_moneda(2,1,bo.total,now()::date,'O',2)
          end) as total,
          (case when  bo.id_moneda_boleto = 1 then
             (-1 * bo.comision)
           else
              param.f_convertir_moneda(2,1,(-1 * bo.comision),now()::date,'O',2)
          end)as total_comision,
			bo.nro_boleto
      from obingresos.tagencia a
      inner join obingresos.tboleto bo on bo.id_agencia = a.id_agencia
      where bo.fecha_emision between v_fechas_periodo.fecha_ini and v_fechas_periodo.fecha_fin
      and a.boaagt = 'A' and a.tipo_agencia = 'noiata' and bo.estado_reg = 'activo'
      and  EXTRACT(MONTH FROM bo.fecha_emision) = v_periodo and (-1 * bo.comision) <> 0
      and a.id_agencia = v_parametros.id_agencia
      order by nro_boleto )LOOP


	insert into conta.tcomisionistas(
			nit_comisionista,
			nro_contrato,
			codigo_producto,
			estado_reg,
			descripcion_producto,
			cantidad_total_entregado,
			cantidad_total_vendido,
			precio_unitario,
			monto_total,
			monto_total_comision,
			id_usuario_reg,
			usuario_ai,
			fecha_reg,
			id_usuario_ai,
			fecha_mod,
			id_usuario_mod,
            id_periodo,
            id_depto_conta,
            registro,
            nombre_agencia,
            revisado,
            nro_boleto,
            id_agencia
          	) values(
			v_reccord.nit,
			v_reccord.nro_contrato,
			v_reccord.codigo,
			'activo',
			v_reccord.descripcion,
			v_reccord.cantidad,
			v_reccord.cantidad,
			v_reccord.neto,
			v_reccord.total,
			v_reccord.total_comision,
			p_id_usuario,
			v_parametros._nombre_usuario_ai,
			now(),
			v_parametros._id_usuario_ai,
			null,
			null,
            v_parametros.id_periodo,
            0,
            'automatico',
            v_reccord.nombre,
            'no',
            v_reccord.nro_boleto,
            v_reccord.id_agencia
			);

END LOOP;
insert  into conta.trevisar_comisionistas (	  nombre_agencia,
                                              nit_comisionista,
                                              nro_contrato,
                                              precio_unitario ,
                                              monto_total,
                                              monto_total_comision,
                                              id_periodo ,
                                              id_depto_conta ,
                                              id_usuario_reg,
                                              id_agencia
                                              )select	c.nombre_agencia,
                                                        c.nit_comisionista,
                                                        c.nro_contrato,
                                                        sum(c.precio_unitario) as precio_unitario,
                                                        sum(c.monto_total) as monto_total,
                                                        sum(c.monto_total_comision) as monto_total_comision,
                                                        v_parametros.id_periodo::integer as id_periodo,
                                                        0::integer as id_depto_conta,
                                                        p_id_usuario::integer as id_usuario_reg,
                                                        c.id_agencia
                                                        from conta.tcomisionistas c
                                                        where c.id_periodo = v_parametros.id_periodo  and c.id_agencia = v_parametros.id_agencia
                                                        group by c.nombre_agencia,c.nit_comisionista,c.nro_contrato,c.id_agencia;



			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Revision con exito (id_agencia'||v_parametros.id_agencia||')');
            v_resp = pxp.f_agrega_clave(v_resp,'id_agencia',v_parametros.id_agencia::varchar);

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
