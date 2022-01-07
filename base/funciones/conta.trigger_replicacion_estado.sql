CREATE OR REPLACE FUNCTION conta.trigger_replicacion_estado (
)
RETURNS trigger AS
$body$
  /**************************************************************************
   SISTEMA ERP
  ***************************************************************************
   SCRIPT: 		trigger replicar estado periodo compra venta
   DESCRIPCIÓN: 	Actualizara el estado en la nueva base de datos cuando se haga el cierre de periodo
   AUTOR: 		Ismael.Valdivia
   FECHA:			08-11-2021
   COMENTARIOS:
  ***************************************************************************
   HISTORIA DE MODIFICACIONES:

  ***************************************************************************/
  --------------------------
  -- CUERPO DE LA FUNCIÓN --
  --------------------------

  --**** DECLARACION DE VARIABLES DE LA FUNCIÓN (LOCALES) ****---


  DECLARE
      --PARÁMETROS FIJOS
      v_periodo record;
      v_periodo_anterior	record;
      v_codigo_depto	varchar;

      /*Variables para conexion a la nueva DB*/
      v_host	varchar;
      v_puerto	varchar;
      v_dbname	varchar;
      v_cuenta_usu	varchar;
      v_pass_usu	varchar;
      v_cadena_cnx	varchar;
      v_conexion	varchar;
      p_user	varchar;
      v_semilla	varchar;
      v_password	varchar;
      v_consulta	varchar;
      v_res_cone	varchar;
      v_gestion_rec		varchar;
      /***************************************/
  BEGIN

        --*** EJECUCIÓN DEL PROCEDIMIENTO ESPECÍFICO
      IF TG_OP = 'UPDATE' THEN

        BEGIN

        	/*Verificamos si el cierre se esta haciendo en el departamento de contabilidad*/
            select
                   dep.codigo
            into
            	  v_codigo_depto
            from param.tdepto dep
            where dep.id_depto = NEW.id_depto;

            /*Si el departamento es de Contabilidad Central entonces hacemos la conexion para mandar el estado*/
             if (v_codigo_depto = 'CON') then


             select ges.gestion
             into
             v_gestion_rec
             from conta.tperiodo_compra_venta pv
             inner join param.tperiodo per on per.id_periodo = pv.id_periodo
             inner join param.tgestion ges on ges.id_gestion = per.id_gestion
             where pv.id_periodo_compra_venta = new.id_periodo_compra_venta;

             /*Aqui para hacer conexion con el usuario Encargado*/
             v_host=pxp.f_get_variable_global('sincroniza_ip_facturacion');
             v_puerto=pxp.f_get_variable_global('sincroniza_puerto_facturacion');
             v_dbname='db_facturas_'||v_gestion_rec;

             if (current_user != 'dbkerp_conexion') then
                 select  usu.cuenta,
                         usu.contrasena
                         into
                         v_cuenta_usu,
                         v_pass_usu
                  from segu.tusuario usu
                  where usu.id_usuario = new.id_usuario_mod;

                 p_user= current_user;

                 v_semilla = pxp.f_get_variable_global('semilla_erp');

                 select md5(v_semilla||v_pass_usu) into v_password;
             else
             	select  usu.cuenta,
                         usu.contrasena
                         into
                         v_cuenta_usu,
                         v_pass_usu
                  from segu.tusuario usu
                  where usu.id_usuario = 366;

                 p_user= 'dbkerp_notificaciones';

                 v_semilla = pxp.f_get_variable_global('semilla_erp');

                 select md5(v_semilla||v_pass_usu) into v_password;
             end if;

             v_cadena_cnx = 'hostaddr='||v_host||' port='||v_puerto||' dbname='||v_dbname||' user='||p_user||' password='||v_password;

             v_conexion = (SELECT dblink_connect(v_cadena_cnx));
             /***************************************************/

             /*Aqui procedemos con el update en la nueva DB*/
             v_consulta = 'update sfe.tperiodo_compra_venta_local set
                            estado = '''||new.estado||''',
                            fecha_mod = '''||now()||''',
                            usuario_mod = '''||current_user||'''
                            where id_periodo_compra_venta = '||new.id_periodo_compra_venta||';';
             /**********************************************/

             IF(v_conexion!='OK') THEN
              		raise exception 'ERROR DE CONEXION A LA BASE DE DATOS CON DBLINK';
              ELSE
                       perform dblink_exec(v_cadena_cnx,v_consulta,TRUE);

              	v_res_cone=(select dblink_disconnect());

              END IF;



             end if;
            /***************************************************************************************************/

        END;

     END IF;

     RETURN NULL;

  END;
$body$
LANGUAGE 'plpgsql'
VOLATILE
CALLED ON NULL INPUT
SECURITY INVOKER
COST 100;

ALTER FUNCTION conta.trigger_replicacion_estado ()
  OWNER TO postgres;