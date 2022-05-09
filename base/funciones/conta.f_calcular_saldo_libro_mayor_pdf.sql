CREATE OR REPLACE FUNCTION conta.f_calcular_saldo_libro_mayor_pdf (
  p_importe_debe numeric,
  p_importe_haber numeric,
  p_fecha_desde varchar,
  p_filtro_cuentas varchar,
  p_filtro_ordenes varchar,
  p_filtro_id_auxiliar varchar,
  p_filtro_id_centro_costo varchar,
  p_filtro_partida varchar,
  p_id_cuenta integer,
  p_id_gestion integer
)
RETURNS varchar AS
$body$
DECLARE
  v_nombre_funcion   	text;
  v_resp				varchar;

  v_existencia			integer;
  v_saldo 				numeric;
  v_importe_debe		numeric;
  v_importe_haber		numeric;

  v_monto_temporal		numeric;
  v_tipo				varchar;

  v_saldo_anterior		numeric;
  v_debe_anterior		numeric;
  v_haber_anterior		numeric;

  v_datos_anterior		record;

  v_numero_cuenta		varchar;
  v_inicial_cuenta		varchar;
  v_cuenta_activo_como_pasivo		varchar;
  v_comportamiento		varchar;

  v_cuenta_acreedora_como_pasivo	varchar;
  v_comportamiento_temp	varchar;
  v_fecha_inicio_gestion varchar;
BEGIN
  v_nombre_funcion = 'conta.f_calcular_saldo_libro_mayor_pdf';

  		v_importe_debe = p_importe_debe::numeric;
        v_importe_haber = p_importe_haber::numeric;


       /************************************************************************/
        if (v_importe_debe > 0) THEN
        	v_saldo = v_importe_debe;
            v_tipo = 'debe';
        else
        	v_saldo = v_importe_haber;
            v_tipo = 'haber';
        end if;


        /*Verificamos si existe la tabla temporal para ir almacenando los datos*/
            select count (*) into v_existencia
            from pg_tables
            where tablename='libro_mayor_saldo';
        /***********************************************************************/


    	if (v_existencia = 0) then



        	/*OBTENEMOS EL NUMERO DE CUENTA PARA PONER LAS CONDICIONES DEFINIDAS POR CHARITO*/
              select cue.nro_cuenta into v_numero_cuenta
              from conta.tint_transaccion transa
              inner join conta.tint_comprobante icbte on icbte.id_int_comprobante = transa.id_int_comprobante
              inner join conta.tcuenta cue on cue.id_cuenta = transa.id_cuenta
              where icbte.estado_reg = 'validado' and cue.id_cuenta = p_id_cuenta
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

             /**********************************************************************/

             /******************Condicion para que no se arrastre el saldo anterior en cuentas que inician con 4,5,6********************/

            -- if (v_inicial_cuenta = '4' or v_inicial_cuenta = '5' or v_inicial_cuenta = '6') then
             --		v_saldo_anterior = 0;
             --       v_debe_anterior = 0;
             --       v_haber_anterior = 0;

            -- else

             	  /*Para calcular el saldo por gestion recuperaremos el primer periodo de la gestion seleccionada*/
                  select per.fecha_ini into v_fecha_inicio_gestion
                  from param.tperiodo per
                  where per.id_gestion = p_id_gestion and per.periodo = 1;
                  /*********************************************************************************************/


             	/************************LLAMAMOS A LA FUNCION PARA CALCULAR EL SALDO ANTERIOR****************************************/
                select * into v_datos_anterior
                from conta.f_recuperar_saldo_anterior_libro_mayor(p_filtro_cuentas,p_filtro_id_auxiliar,p_fecha_desde,p_filtro_partida,p_filtro_id_centro_costo,v_fecha_inicio_gestion,p_filtro_ordenes)
                as(saldo_anterior NUMERIC, total_debe_anterior NUMERIC, total_haber_anterior NUMERIC);
              	/*********************************************************************************************************************/

                --v_saldo_anterior = v_datos_anterior.saldo_anterior;
                v_debe_anterior = v_datos_anterior.total_debe_anterior;
                v_haber_anterior = v_datos_anterior.total_haber_anterior;

                if (v_comportamiento = 'activo') then
                	v_saldo_anterior = v_debe_anterior - v_haber_anterior;

                elsif (v_comportamiento = 'pasivo') then
                	v_saldo_anterior = v_haber_anterior - v_debe_anterior;
                end if;

             --end if;

             if (v_comportamiento = 'activo') then

        	/*Calcularemos el saldo siguiente e iremos poniendo aqui las condiciones para la cuenta*/
            	 IF (v_tipo = 'debe') then
                    	v_saldo = v_saldo_anterior + v_saldo;
                    else
                    	v_saldo = v_saldo_anterior - v_saldo;
                    end if;
              ELSIF (v_comportamiento = 'pasivo') then
              	  IF (v_tipo = 'debe') then
                    	v_saldo = v_saldo_anterior - v_saldo;
                    else
                    	v_saldo = v_saldo_anterior + v_saldo;
                    end if;
              end if;

            /***************************************************************************************/

        /*Creamos la tabla temporal*/

    		CREATE TEMPORARY TABLE libro_mayor_saldo (
             							  saldo_temporal NUMERIC,
                                          comportamiento VARCHAR
                                       	 )ON COMMIT DROP;

            insert into	libro_mayor_saldo ( saldo_temporal,
            								comportamiento
                                        )
                                        values(
                                        v_saldo,
                                        v_comportamiento
                                      );

             v_monto_temporal = v_saldo;
     	/***********************************************************/
         else
         			select saldo_temporal, comportamiento into v_monto_temporal, v_comportamiento_temp
                    from libro_mayor_saldo;

         		/*Ponemos la condicion para que se vaya calculado dependiendo si es un activo o un pasivo*/
                if (v_comportamiento_temp = 'activo') then
                	IF (v_tipo = 'debe') then
                    	v_monto_temporal = v_monto_temporal + v_saldo; --HABER
                    else
                    	v_monto_temporal = v_monto_temporal - v_saldo;	--DEBE
                    end if;

                elsif (v_comportamiento_temp = 'pasivo') THEN

                	IF (v_tipo = 'debe') then
                    	v_monto_temporal = v_monto_temporal - v_saldo; --HABER
                    else
                    	v_monto_temporal = v_monto_temporal + v_saldo;	--DEBE
                    end if;

                end if;

         			update libro_mayor_saldo set
                    saldo_temporal = v_monto_temporal;


        end if;

        return v_monto_temporal;



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

ALTER FUNCTION conta.f_calcular_saldo_libro_mayor_pdf (p_importe_debe numeric, p_importe_haber numeric, p_fecha_desde varchar, p_filtro_cuentas varchar, p_filtro_ordenes varchar, p_filtro_id_auxiliar varchar, p_filtro_id_centro_costo varchar, p_filtro_partida varchar, p_id_cuenta integer, p_id_gestion integer)
  OWNER TO postgres;
