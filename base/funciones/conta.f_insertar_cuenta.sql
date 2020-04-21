CREATE OR REPLACE FUNCTION conta.f_insertar_cuenta (
  p_id_usuario integer,
  p_id_cuenta_padre integer,
  p_id_gestion integer,
  p_registros public.hstore
)
RETURNS integer AS
$body$
/**************************************************************************
 FUNCION: 		conta.f_insertar_cuenta
 DESCRIPCION:   realiza la insercion de cuentas a partir de la funcion conta.ft_plan_cuenta_det_ime
 AUTOR: 	    Alan Felipez
 FECHA:	        18/12/2019
 COMENTARIOS:
***************************************************************************
 HISTORIA DE MODIFICACIONES:

 DESCRIPCION:
 AUTOR:
 FECHA:
 ***************************************************************************/


DECLARE
  v_nombre_funcion   	text;
  v_resp				varchar;
  v_id_cuenta			integer;
  v_nro_cuenta			varchar;
  v_id_moneda			integer;
  v_codigo_moneda		varchar;
  v_eeff				varchar[];
  v_sw_transaccional	varchar;
  v_nombre_cuenta 		varchar;
  v_sw_transaccional_padre	varchar;
  v_sw_auxiliar			varchar;
  v_id_cuenta_ant_gestion	integer;
  v_id_gestion_ant			integer;
BEGIN

  v_nombre_funcion:='conta.f_insertar_cuenta';



  if ((p_registros->'sub_cuenta')::varchar = '')then
  	v_nro_cuenta = (p_registros->'codigo_cuenta')::varchar;
  elsif ((p_registros->'sub_sub_cuenta')::varchar = '') then
  	v_nro_cuenta = (p_registros->'codigo_cuenta')::varchar||'.'||(p_registros->'sub_cuenta')::varchar;
  else
  	v_nro_cuenta = (p_registros->'codigo_cuenta')::varchar||'.'||(p_registros->'sub_cuenta')::varchar||'.'||(p_registros->'sub_sub_cuenta')::varchar;
  end if;

 v_eeff [1]= lower((p_registros->'tip_cuenta')::varchar);
  if ((p_registros->'moneda')::varchar = 'BOB')then
  	v_codigo_moneda='Bs';
  elsif ((p_registros->'moneda')::varchar = 'ARS')then
  	v_codigo_moneda='$';
  elseif ((p_registros->'moneda')::varchar = 'EUR')then
  	v_codigo_moneda='€';
  elseif ((p_registros->'moneda')::varchar = 'BRL')then
  	v_codigo_moneda='R$';
  elseif ((p_registros->'moneda')::varchar = 'PEN')then
  	v_codigo_moneda='S';
  elseif ((p_registros->'moneda')::varchar = 'SEK')then
  	v_codigo_moneda='SEK';
  elseif ((p_registros->'moneda')::varchar = 'NOK')then
  	v_codigo_moneda='NOK';
  elseif ((p_registros->'moneda')::varchar = 'USD')then
  	v_codigo_moneda='$us';
  elseif ((p_registros->'moneda')::varchar = 'GBP')then
  	v_codigo_moneda='£';
  elseif ((p_registros->'moneda')::varchar = 'CHF')then
  	v_codigo_moneda='CHF';
  elseif ((p_registros->'moneda')::varchar = 'BRG')then
  	v_codigo_moneda='R$';
  elseif ((p_registros->'moneda')::varchar = 'DKK')then
  	v_codigo_moneda='DKK';
  elseif ((p_registros->'moneda')::varchar = 'CZK')then
  	v_codigo_moneda='CZK';
  end if;
  select id_moneda
  into v_id_moneda
  from param.tmoneda
  where codigo=v_codigo_moneda;

  select substring((p_registros->'nombre_cuenta')::varchar from 1 for 100)
  into v_nombre_cuenta;

  --if ((p_registros->'tipo_cuenta')::varchar='MOVIM' or (p_registros->'tipo_cuenta')::varchar='MOVIM.') then
  	v_sw_transaccional='movimiento';
 /* elsif ((p_registros->'tipo_cuenta')::varchar='TITULAR')THEN
  	v_sw_transaccional='titular';
  end if;*/
  if(lower((p_registros->'permite_auxiliar')::varchar)!='')then
  	v_sw_auxiliar=lower((p_registros->'permite_auxiliar')::varchar);
  else
  	v_sw_auxiliar='si';
  end if;

   INSERT INTO conta.tcuenta
    (
      id_usuario_reg,
      fecha_reg,
      estado_reg,
      id_empresa,
      id_cuenta_padre,
      nro_cuenta,
      id_gestion,
      id_moneda,
      nombre_cuenta,
      desc_cuenta,
      sw_transaccional,
      sw_auxiliar,
      cuenta_sigma,
      cuenta_flujo_sigma,
      eeff
    )
    VALUES (
      p_id_usuario,
      now(),
      'activo',
 	  1,
      p_id_cuenta_padre,
      v_nro_cuenta,
      p_id_gestion,
      v_id_moneda,
      v_nombre_cuenta,
      v_nombre_cuenta,
      v_sw_transaccional,
      v_sw_auxiliar,
      (p_registros->'cuenta_sigep')::INTEGER,
      null,
      v_eeff
    ) RETURNING id_cuenta into v_id_cuenta;

    --guardo informacion del id_cuenta creada para luego ser usada para insertar auxiliares y partidas
	update conta.tplan_cuenta_det
	set id_cuenta_asociada = v_id_cuenta
	WHERE id_plan_cuenta_det = (p_registros->'id_plan_cuenta_det')::integer;

    --actulizar informacion de los nodos que son de tipo movimiento a titular si tienen hijos
    if(p_id_cuenta_padre is not null) then
    	select cu.sw_transaccional
        into v_sw_transaccional_padre
        from conta.tcuenta cu
        where cu.id_cuenta=p_id_cuenta_padre;
        if(v_sw_transaccional_padre !='titular') then
        	update conta.tcuenta
            set sw_transaccional='titular'
            where id_cuenta = p_id_cuenta_padre;
        end if;

    end if;
    --asociar cuentas de la gestion destino con la actual
    if((p_registros->'relacion_cuenta')::varchar != '') THEN
    	select ges.id_gestion
        into v_id_gestion_ant
        from param.tgestion ges
        where gestion = ((select ges.gestion
        				  from param.tgestion ges
                          where ges.id_gestion = p_id_gestion)-1);

        --sentencia de insercion de la relacion de cuentas
        if exists (select 1
                  from conta.tcuenta
                   where nro_cuenta =((p_registros->'relacion_cuenta')::varchar)
                   and id_gestion = v_id_gestion_ant)then
        	select cu.id_cuenta
    		into v_id_cuenta_ant_gestion
        	from conta.tcuenta cu
        	where cu.nro_cuenta =((p_registros->'relacion_cuenta')::varchar) and cu.id_gestion = v_id_gestion_ant;

        	if exists (select 1
            			from conta.tcuenta_ids
                        where id_cuenta_uno =v_id_cuenta_ant_gestion)then
            	update conta.tcuenta_ids
                set id_cuenta_dos=v_id_cuenta
                where id_cuenta_uno =v_id_cuenta_ant_gestion;
            else
                insert into conta.tcuenta_ids
                (
                id_cuenta_uno,
                id_cuenta_dos
                )VALUES
                (
                v_id_cuenta_ant_gestion,
                v_id_cuenta
                );
            end if;


        else
        	--en caso de que no se encuentre una cuenta equivalente registramos informacion para ver la cuenta sin relacion
        	update conta.tplan_cuenta_det
            set	relacion_cuenta = 'sin relacion de cuentas'
            where id_plan_cuenta_det = (p_registros->'id_plan_cuenta_det')::integer;
        end if;
    else
    --en caso de que no se encuentre una cuenta equivalente registramos informacion para ver la cuenta sin relacion
        update conta.tplan_cuenta_det
        set	relacion_cuenta = 'sin relacion de cuentas'
        where id_plan_cuenta_det = (p_registros->'id_plan_cuenta_det')::integer;

    end if;

	return v_id_cuenta;
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

COMMENT ON FUNCTION conta.f_insertar_cuenta(p_id_usuario integer, p_id_cuenta_padre integer, p_id_gestion integer, p_registros public.hstore)
IS 'funcion que inserta una cuenta generada a partir de una plantilla excel almacenada en conta.tplan_cuenta_det';

ALTER FUNCTION conta.f_insertar_cuenta (p_id_usuario integer, p_id_cuenta_padre integer, p_id_gestion integer, p_registros public.hstore)
  OWNER TO postgres;