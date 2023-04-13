<?php
/**
 *@package pXP
 *@file gen-MODIntComprobante.php
 *@author  (admin)
 *@date 29-08-2013 00:28:30
 *@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
 */
class MODIntComprobante extends MODbase{

    function __construct(CTParametro $pParam){
        parent::__construct($pParam);
    }

    function listarIntComprobante(){
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento='conta.ft_int_comprobante_sel';
        $this->transaccion='CONTA_INCBTE_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion
        $this->setParametro('nombreVista','nombreVista','varchar');

        //Definicion de la lista del resultado del query
        $this->captura('id_int_comprobante','int4');
        $this->captura('id_clase_comprobante','int4');
        $this->captura('id_subsistema','int4');
        $this->captura('id_depto','int4');
        $this->captura('id_moneda','int4');
        $this->captura('id_periodo','int4');
        $this->captura('id_funcionario_firma1','int4');
        $this->captura('id_funcionario_firma2','int4');
        $this->captura('id_funcionario_firma3','int4');
        $this->captura('tipo_cambio','numeric');
        $this->captura('beneficiario','varchar');
        $this->captura('nro_cbte','varchar');
        $this->captura('estado_reg','varchar');
        $this->captura('glosa1','varchar');
        $this->captura('fecha','date');
        $this->captura('glosa2','varchar');
        $this->captura('nro_tramite','varchar');
        $this->captura('momento','varchar');
        $this->captura('id_usuario_reg','int4');
        $this->captura('fecha_reg','timestamp');
        $this->captura('id_usuario_mod','int4');
        $this->captura('fecha_mod','timestamp');
        $this->captura('usr_reg','varchar');
        $this->captura('usr_mod','varchar');
        $this->captura('desc_clase_comprobante','varchar');
        $this->captura('desc_subsistema','varchar');
        $this->captura('desc_depto','text');
        $this->captura('desc_moneda','text');
        $this->captura('desc_firma1','text');
        $this->captura('desc_firma2','text');
        $this->captura('desc_firma3','text');
        $this->captura('momento_comprometido','varchar');
        $this->captura('momento_ejecutado','varchar');
        $this->captura('momento_pagado','varchar');
        $this->captura('manual','varchar');
        $this->captura('id_int_comprobante_fks','text');
        $this->captura('id_tipo_relacion_comprobante','int');
        $this->captura('desc_tipo_relacion_comprobante','varchar');
        $this->captura('id_moneda_base','int4');
        $this->captura('desc_moneda_base','text');
        $this->captura('cbte_cierre','varchar');
        $this->captura('cbte_apertura','varchar');
        $this->captura('cbte_aitb','varchar');
        $this->captura('fecha_costo_ini','date');
        $this->captura('fecha_costo_fin','date');
        $this->captura('tipo_cambio_2','numeric');
        $this->captura('id_moneda_tri','int4');
        $this->captura('sw_tipo_cambio','varchar');
        $this->captura('id_config_cambiaria','int4');
        $this->captura('ope_1','varchar');
        $this->captura('ope_2','varchar');
        $this->captura('desc_moneda_tri','text');
        $this->captura('origen','varchar');
        $this->captura('localidad','varchar');
        $this->captura('sw_editable','varchar');
        $this->captura('cbte_reversion','varchar');
        $this->captura('volcado','varchar');
        $this->captura('id_proceso_wf','int4');
        $this->captura('id_estado_wf','int4');
        $this->captura('fecha_c31','date');
        $this->captura('c31','varchar');
        $this->captura('id_gestion','int4');
        $this->captura('periodo','int4');
        $this->captura('forma_cambio','varchar');

        $this->captura('ope_3','varchar');
        $this->captura('tipo_cambio_3','numeric');
        $this->captura('id_moneda_act','int4');
        //franklin.espinoza 27/09/2020
        $this->captura('id_service_request','integer');
        $this->captura('id_depto_libro','integer');
        $this->captura('id_cuenta_bancaria','integer');
        $this->captura('desc_cuenta_bancaria','text');
        $this->captura('desc_depto_lb','varchar');
        $this->captura('tipo_cbte','varchar');
        $this->captura('reversion','varchar');



        //Ejecuta la instruccion
        $this->armarConsulta();
        //echo $this->getConsulta();exit;
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function listarIntComprobanteWF(){
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento='conta.ft_int_comprobante_sel';
        $this->transaccion='CONTA_INCBTEWF_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion

        $this->setParametro('id_funcionario_usu','id_funcionario_usu','int4');

        //Definicion de la lista del resultado del query
        $this->captura('id_int_comprobante','int4');
        $this->captura('id_clase_comprobante','int4');
        $this->captura('id_subsistema','int4');
        $this->captura('id_depto','int4');
        $this->captura('id_moneda','int4');
        $this->captura('id_periodo','int4');
        $this->captura('id_funcionario_firma1','int4');
        $this->captura('id_funcionario_firma2','int4');
        $this->captura('id_funcionario_firma3','int4');
        $this->captura('tipo_cambio','numeric');
        $this->captura('beneficiario','varchar');
        $this->captura('nro_cbte','varchar');
        $this->captura('estado_reg','varchar');
        $this->captura('glosa1','varchar');
        $this->captura('fecha','date');
        $this->captura('glosa2','varchar');
        $this->captura('nro_tramite','varchar');
        $this->captura('momento','varchar');
        $this->captura('id_usuario_reg','int4');
        $this->captura('fecha_reg','timestamp');
        $this->captura('id_usuario_mod','int4');
        $this->captura('fecha_mod','timestamp');
        $this->captura('usr_reg','varchar');
        $this->captura('usr_mod','varchar');
        $this->captura('desc_clase_comprobante','varchar');
        $this->captura('desc_subsistema','varchar');
        $this->captura('desc_depto','text');
        $this->captura('desc_moneda','text');
        $this->captura('desc_firma1','text');
        $this->captura('desc_firma2','text');
        $this->captura('desc_firma3','text');
        $this->captura('momento_comprometido','varchar');
        $this->captura('momento_ejecutado','varchar');
        $this->captura('momento_pagado','varchar');
        $this->captura('manual','varchar');
        $this->captura('id_int_comprobante_fks','text');
        $this->captura('id_tipo_relacion_comprobante','int');
        $this->captura('desc_tipo_relacion_comprobante','varchar');
        $this->captura('id_moneda_base','int4');
        $this->captura('desc_moneda_base','text');
        $this->captura('cbte_cierre','varchar');
        $this->captura('cbte_apertura','varchar');
        $this->captura('cbte_aitb','varchar');
        $this->captura('fecha_costo_ini','date');
        $this->captura('fecha_costo_fin','date');
        $this->captura('tipo_cambio_2','numeric');
        $this->captura('tipo_cambio_3','numeric');
        $this->captura('id_moneda_tri','int4');
        $this->captura('id_moneda_act','int4');
        $this->captura('sw_tipo_cambio','varchar');
        $this->captura('id_config_cambiaria','int4');
        $this->captura('ope_1','varchar');
        $this->captura('ope_2','varchar');
        $this->captura('desc_moneda_tri','text');
        $this->captura('origen','varchar');
        $this->captura('localidad','varchar');
        $this->captura('sw_editable','varchar');
        $this->captura('cbte_reversion','varchar');
        $this->captura('volcado','varchar');
        $this->captura('id_proceso_wf','int4');
        $this->captura('id_estado_wf','int4');
        $this->captura('fecha_c31','date');
        $this->captura('c31','varchar');
        $this->captura('id_gestion','int4');
        $this->captura('periodo','int4');
        $this->captura('forma_cambio','varchar');





        //Ejecuta la instruccion
        $this->armarConsulta();
        //echo $this->getConsulta();exit;
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function listarSimpleIntComprobante(){
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento='conta.ft_int_comprobante_sel';
        $this->transaccion='CONTA_ICSIM_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion

        //Definicion de la lista del resultado del query
        $this->captura('id_int_comprobante','int4');
        $this->captura('nro_cbte','varchar');
        $this->captura('nro_tramite','varchar');
        $this->captura('fecha','date');
        $this->captura('glosa1','varchar');
        $this->captura('glosa2','varchar');
        $this->captura('id_clase_comprobante','int4');
        $this->captura('codigo','varchar');
        $this->captura('descripcion','varchar');
        $this->captura('desc_moneda','text');



        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();
        //Devuelve la respuesta
        return $this->respuesta;
    }



    function insertarIntComprobante(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='conta.ft_int_comprobante_ime';
        $this->transaccion='CONTA_INCBTE_INS';
        $this->tipo_procedimiento='IME';

        //Define los parametros para la funcion
        $this->setParametro('id_clase_comprobante','id_clase_comprobante','int4');
        $this->setParametro('id_subsistema','id_subsistema','int4');
        $this->setParametro('id_depto','id_depto','int4');
        $this->setParametro('id_moneda','id_moneda','int4');
        $this->setParametro('id_periodo','id_periodo','int4');
        $this->setParametro('id_funcionario_firma1','id_funcionario_firma1','int4');
        $this->setParametro('id_funcionario_firma2','id_funcionario_firma2','int4');
        $this->setParametro('id_funcionario_firma3','id_funcionario_firma3','int4');
        $this->setParametro('tipo_cambio','tipo_cambio','numeric');
        $this->setParametro('beneficiario','beneficiario','varchar');
        $this->setParametro('nro_cbte','nro_cbte','varchar');
        $this->setParametro('estado_reg','estado_reg','varchar');
        $this->setParametro('glosa1','glosa1','varchar');
        $this->setParametro('fecha','fecha','date');
        $this->setParametro('glosa2','glosa2','varchar');
        $this->setParametro('nro_tramite','nro_tramite','varchar');
        $this->setParametro('momento_comprometido','momento_comprometido','varchar');
        $this->setParametro('momento_ejecutado','momento_ejecutado','varchar');
        $this->setParametro('momento_pagado','momento_pagado','varchar');
        $this->setParametro('id_int_comprobante_fks','id_int_comprobante_fks','varchar');
        $this->setParametro('id_tipo_relacion_comprobante','id_tipo_relacion_comprobante','int4');
        $this->setParametro('cbte_cierre','cbte_cierre','varchar');
        $this->setParametro('cbte_apertura','cbte_apertura','varchar');
        $this->setParametro('cbte_aitb','cbte_aitb','varchar');
        $this->setParametro('fecha_costo_ini','fecha_costo_ini','date');
        $this->setParametro('fecha_costo_fin','fecha_costo_fin','date');
        $this->setParametro('tipo_cambio_2','tipo_cambio_2','numeric');
        $this->setParametro('tipo_cambio_3','tipo_cambio_3','numeric');
        $this->setParametro('id_config_cambiaria','id_config_cambiaria','integer');
        $this->setParametro('forma_cambio','forma_cambio','varchar');

        //franklin.espinoza 27/09/2020
        $this->setParametro('id_depto_libro','id_depto_libro','integer');
        $this->setParametro('id_cuenta_bancaria','id_cuenta_bancaria','integer');
        $this->setParametro('reversion','reversion','varchar');


        //Ejecuta la instruccion
        $this->armarConsulta();
        
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function modificarIntComprobante(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='conta.ft_int_comprobante_ime';
        $this->transaccion='CONTA_INCBTE_MOD';
        $this->tipo_procedimiento='IME';

        //Define los parametros para la funcion
        $this->setParametro('id_int_comprobante','id_int_comprobante','int4');
        $this->setParametro('id_clase_comprobante','id_clase_comprobante','int4');
        $this->setParametro('id_int_comprobante_fk','id_int_comprobante_fk','int4');
        $this->setParametro('id_depto','id_depto','int4');
        $this->setParametro('id_moneda','id_moneda','int4');
        $this->setParametro('id_periodo','id_periodo','int4');
        $this->setParametro('id_funcionario_firma1','id_funcionario_firma1','int4');
        $this->setParametro('id_funcionario_firma2','id_funcionario_firma2','int4');
        $this->setParametro('id_funcionario_firma3','id_funcionario_firma3','int4');
        $this->setParametro('tipo_cambio','tipo_cambio','numeric');
        $this->setParametro('beneficiario','beneficiario','varchar');
        $this->setParametro('nro_cbte','nro_cbte','varchar');
        $this->setParametro('estado_reg','estado_reg','varchar');
        $this->setParametro('glosa1','glosa1','varchar');
        $this->setParametro('fecha','fecha','date');
        $this->setParametro('glosa2','glosa2','varchar');
        $this->setParametro('nro_tramite','nro_tramite','varchar');

        $this->setParametro('momento_comprometido','momento_comprometido','varchar');
        $this->setParametro('momento_ejecutado','momento_ejecutado','varchar');
        $this->setParametro('momento_pagado','momento_pagado','varchar');
        $this->setParametro('id_int_comprobante_fks','id_int_comprobante_fks','varchar');
        $this->setParametro('id_tipo_relacion_comprobante','id_tipo_relacion_comprobante','int4');

        $this->setParametro('cbte_cierre','cbte_cierre','varchar');
        $this->setParametro('cbte_apertura','cbte_apertura','varchar');
        $this->setParametro('cbte_aitb','cbte_aitb','varchar');

        $this->setParametro('fecha_costo_ini','fecha_costo_ini','date');
        $this->setParametro('fecha_costo_fin','fecha_costo_fin','date');
        $this->setParametro('tipo_cambio_2','tipo_cambio_2','numeric');
        $this->setParametro('tipo_cambio_3','tipo_cambio_3','numeric');
        $this->setParametro('id_config_cambiaria','id_config_cambiaria','integer');
        $this->setParametro('forma_cambio','forma_cambio','varchar');

        //franklin.espinoza 27/09/2020
        $this->setParametro('id_depto_libro','id_depto_libro','integer');
        $this->setParametro('id_cuenta_bancaria','id_cuenta_bancaria','integer');
        $this->setParametro('reversion','reversion','varchar');


        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function eliminarIntComprobante(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='conta.ft_int_comprobante_ime';
        $this->transaccion='CONTA_INCBTE_ELI';
        $this->tipo_procedimiento='IME';

        //Define los parametros para la funcion
        $this->setParametro('id_int_comprobante','id_int_comprobante','int4');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }


    function validarIntComprobante(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='conta.ft_int_comprobante_ime';
        $this->transaccion='CONTA_INCBTE_VAL';
        $this->tipo_procedimiento='IME';

        //Define los parametros para la funcion
        $this->setParametro('id_int_comprobante','id_int_comprobante','int4');
        $this->setParametro('igualar','igualar','varchar');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function generarDesdePlantilla(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='conta.f_resultados_gen_cbte';
        $this->transaccion='CONTA_GENCBTERES_IME';
        $this->tipo_procedimiento='IME';

        //Define los parametros para la funcion
        $this->setParametro('id_depto','id_depto','int4');
        $this->setParametro('id_resultado_plantilla','id_resultado_plantilla','int4');
        $this->setParametro('desde','desde','date');
        $this->setParametro('hasta','hasta','date');
        $this->setParametro('fecha','fecha','date');
        $this->setParametro('id_int_comprobante','id_int_comprobante','int4');


        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }


    function listarCbteCabecera(){

        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento='conta.ft_int_comprobante_sel';
        $this->transaccion='CONTA_CABCBT_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion
        $this->setCount(false);

        $this->setParametro('id_proceso_wf','id_proceso_wf','int4');

        //Definicion de la lista del resultado del query
        $this->captura('id_int_comprobante','int4');
        $this->captura('id_clase_comprobante','int4');
        $this->captura('id_subsistema','int4');
        $this->captura('id_depto','int4');
        $this->captura('id_moneda','int4');
        $this->captura('id_periodo','int4');
        $this->captura('id_funcionario_firma1','int4');
        $this->captura('id_funcionario_firma2','int4');
        $this->captura('id_funcionario_firma3','int4');
        $this->captura('tipo_cambio','numeric');
        $this->captura('beneficiario','varchar');
        $this->captura('nro_cbte','varchar');
        $this->captura('estado_reg','varchar');
        $this->captura('glosa1','varchar');
        $this->captura('fecha','date');
        $this->captura('glosa2','varchar');
        $this->captura('nro_tramite','varchar');
        $this->captura('momento','varchar');
        $this->captura('id_usuario_reg','int4');
        $this->captura('fecha_reg','timestamp');
        $this->captura('id_usuario_mod','int4');
        $this->captura('fecha_mod','timestamp');
        $this->captura('usr_reg','varchar');
        $this->captura('usr_mod','varchar');
        $this->captura('desc_clase_comprobante','varchar');
        $this->captura('desc_subsistema','varchar');
        $this->captura('desc_depto','text');
        $this->captura('desc_moneda','text');
        $this->captura('desc_firma1','text');
        $this->captura('desc_firma2','text');
        $this->captura('desc_firma3','text');
        $this->captura('momento_comprometido','varchar');
        $this->captura('momento_ejecutado','varchar');
        $this->captura('momento_pagado','varchar');
        $this->captura('manual','varchar');
        $this->captura('id_int_comprobante_fks','text');
        $this->captura('id_tipo_relacion_comprobante','int');
        $this->captura('desc_tipo_relacion_comprobante','varchar');
        $this->captura('id_moneda_base','int4');
        $this->captura('codigo_moneda_base','varchar');
        $this->captura('codigo_depto','varchar');
        $this->captura('documentos','varchar');
        $this->captura('c31','varchar');
        $this->captura('sw_tipo_cambio','varchar');
        $this->captura('localidad','varchar');
        $this->captura('nro_cuenta_bancaria','varchar');
        $this->captura('fecha_costo_ini','varchar');
        $this->captura('fecha_costo_fin','varchar');
        $this->captura('moneda','varchar');
        $this->captura('usuario','varchar');
        //fRnk: adicionado para las fechas de las firmas
        $this->captura('fec_validado','varchar');
        $this->captura('fec_aprobado','varchar');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function listarCbteDetalle(){
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento='conta.ft_int_comprobante_sel';
        $this->transaccion='CONTA_DETCBT_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion
        $this->setCount(false);
        $this->setParametro('id_proceso_wf','id_proceso_wf','int4');

        //Definicion de la lista del resultado del query
        $this->captura('nro_cuenta','varchar');
        $this->captura('nombre_cuenta','varchar');
        $this->captura('codigo_auxiliar','varchar');
        $this->captura('nombre_auxiliar','varchar');
        $this->captura('cc','text');
        $this->captura('codigo_partida','varchar');
        $this->captura('nombre_partida','varchar');
        $this->captura('desc_orden','varchar');
        $this->captura('glosa','varchar');
        $this->captura('importe_gasto','numeric');
        $this->captura('importe_recurso','numeric');
        $this->captura('importe_debe','numeric');
        $this->captura('importe_haber','numeric');
        $this->captura('importe_debe_mb','numeric');
        $this->captura('importe_haber_mb','numeric');

        $this->captura('sw_movimiento','varchar');
        $this->captura('tipo_partida','varchar');
        $this->captura('tipo_cambio','numeric');
        //$this->captura('id_orden_trabajo','integer');

        //Ejecuta la instruccion
        $this->armarConsulta();
        //echo $this->consulta;exit;
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function listarBeneficiarios(){
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento='conta.ft_int_beneficiario_sel';
        $this->transaccion='CONTA_PROVEEV_REP';
        $this->tipo_procedimiento='SEL';//tipo de transaccion
        $this->setCount(false);
        $this->setParametro('id_proceso_wf','id_proceso_wf','int4');

        //Definicion de la lista del resultado del query
        $this->captura('tipo_doc','varchar');
        $this->captura('expedicion','varchar');
        $this->captura('ci','varchar');
        $this->captura('nombre_razon_social','varchar');
        $this->captura('banco','varchar');
        $this->captura('nro_cuenta_bancaria_sigma','varchar');
        $this->captura('importe','numeric');

        //Ejecuta la instruccion
        $this->armarConsulta();

        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function igualarComprobante(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='conta.ft_int_comprobante_ime';
        $this->transaccion='CONTA_IGUACBTE_IME';
        $this->tipo_procedimiento='IME';

        //Define los parametros para la funcion
        $this->setParametro('id_int_comprobante','id_int_comprobante','int4');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function swEditable(){
        //swEditable de variables para ejecucion del procedimiento
        $this->procedimiento='conta.ft_int_comprobante_ime';
        $this->transaccion='CONTA_SWEDIT_IME';
        $this->tipo_procedimiento='IME';

        //Define los parametros para la funcion
        $this->setParametro('id_int_comprobante','id_int_comprobante','int4');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function volcarCbte(){
        //swEditable de variables para ejecucion del procedimiento
        $this->procedimiento='conta.ft_int_comprobante_ime';
        $this->transaccion='CONTA_VOLCARCBTE_IME';
        $this->tipo_procedimiento='IME';

        //Define los parametros para la funcion
        $this->setParametro('id_int_comprobante','id_int_comprobante','int4');
        $this->setParametro('sw_validar','sw_validar','varchar');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function listarCbteDependencias(){
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento='conta.ft_int_comprobante_sel';
        $this-> setCount(false);
        $this->transaccion='CONTA_DEPCBT_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion

        $id_padre = $this->objParam->getParametro('id_padre');

        $this->setParametro('id_padre','id_padre','varchar');
        $this->setParametro('id_int_comprobante_basico','id_int_comprobante_basico','int4');

        $this->captura('id_int_comprobante','int4');
        $this->captura('id_int_comprobante_padre','int4');

        $this->captura('nro_cbte','varchar');
        $this->captura('glosa1','varchar');
        $this->captura('nombre','varchar');
        $this->captura('volcado','varchar');
        $this->captura('cbte_reversion','varchar');
        $this->captura('tipo_nodo','varchar');
        $this->captura('id_proceso_wf','int4');



        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        return $this->respuesta;
    }

    function siguienteEstado(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento = 'conta.ft_int_comprobante_ime';
        $this->transaccion = 'CD_SIGCBTE_IME';
        $this->tipo_procedimiento = 'IME';

        //Define los parametros para la funcion
        $this->setParametro('id_int_comprobante','id_int_comprobante','int4');
        $this->setParametro('id_proceso_wf_act','id_proceso_wf_act','int4');
        $this->setParametro('id_estado_wf_act','id_estado_wf_act','int4');
        $this->setParametro('id_funcionario_usu','id_funcionario_usu','int4');
        $this->setParametro('id_tipo_estado','id_tipo_estado','int4');
        $this->setParametro('id_funcionario_wf','id_funcionario_wf','int4');
        $this->setParametro('id_depto_wf','id_depto_wf','int4');
        $this->setParametro('obs','obs','text');
        $this->setParametro('json_procesos','json_procesos','text');
        $this->setParametro('validar_doc','validar_doc','boolean');



        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();
        //Devuelve la respuesta
        return $this->respuesta;
    }


    function anteriorEstado(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='conta.ft_int_comprobante_ime';
        $this->transaccion='CD_ANTCBTE_IME';
        $this->tipo_procedimiento='IME';
        //Define los parametros para la funcion
        $this->setParametro('id_proceso_wf','id_proceso_wf','int4');
        $this->setParametro('id_estado_wf','id_estado_wf','int4');
        $this->setParametro('obs','obs','varchar');
        $this->setParametro('estado_destino','estado_destino','varchar');
        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function clonarCbte(){
        //swEditable de variables para ejecucion del procedimiento
        $this->procedimiento='conta.ft_int_comprobante_ime';
        $this->transaccion='CONTA_CLONARCBTE_IME';
        $this->tipo_procedimiento='IME';

        //Define los parametros para la funcion
        $this->setParametro('id_int_comprobante','id_int_comprobante','int4');
        $this->setParametro('sw_tramite','sw_tramite','varchar');


        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }


    function modificarFechasCostosCbte(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='conta.ft_int_comprobante_ime';
        $this->transaccion='CONTA_UPDFECOS_MOD';
        $this->tipo_procedimiento='IME';

        //Define los parametros para la funcion
        $this->setParametro('id_int_comprobante','id_int_comprobante','int4');
        $this->setParametro('fecha_costo_ini','fecha_costo_ini','date');
        $this->setParametro('fecha_costo_fin','fecha_costo_fin','date');


        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function listarVerPresCbte(){
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento='conta.f_verificar_presu_cbte_sel';
        $this->transaccion='CONTA_VERPRES_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion
        $this-> setCount(false);
        $this->setTipoRetorno('record');

        $this->setParametro('id_int_comprobante','id_int_comprobante','int4');

        //Definicion de la lista del resultado del query

        $this->captura('id_ver','bigint');
        $this->captura('control_partida','VARCHAR');
        $this->captura('id_par','int4');
        $this->captura('id_agrupador','INTEGER');
        $this->captura('importe_debe','NUMERIC');
        $this->captura('importe_haber','NUMERIC');
        $this->captura('movimiento','VARCHAR');
        $this->captura('id_presupuesto','INTEGER');
        $this->captura('tipo_cambio','NUMERIC');
        $this->captura('monto_mb','NUMERIC');
        $this->captura('verificacion','VARCHAR');
        $this->captura('saldo','NUMERIC');
        $this->captura('codigo_partida','VARCHAR');
        $this->captura('nombre_partida','VARCHAR');
        $this->captura('desc_tipo_presupuesto','VARCHAR');
        $this->captura('descripcion','VARCHAR');




        //Ejecuta la instruccion
        $this->armarConsulta();
        //echo $this->getConsulta();exit;
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }
    //
    function listarRepIntComprobanteDiario(){
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento='conta.ft_int_comprobante_sel';
        $this->transaccion='CONTA_REPINCBTE_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion
        $this->setCount(false);
        //
        $this->setParametro('nombreVista','nombreVista','varchar');
        //Definicion de la lista del resultado del query
        $this->captura('id_int_comprobante','int4');
        $this->captura('id_clase_comprobante','int4');
        $this->captura('id_subsistema','int4');
        $this->captura('id_depto','int4');
        $this->captura('id_moneda','int4');
        $this->captura('id_periodo','int4');
        $this->captura('id_funcionario_firma1','int4');
        $this->captura('id_funcionario_firma2','int4');
        $this->captura('id_funcionario_firma3','int4');
        $this->captura('tipo_cambio','numeric');
        $this->captura('beneficiario','varchar');
        $this->captura('nro_cbte','varchar');
        $this->captura('estado_reg','varchar');
        $this->captura('glosa1','varchar');
        $this->captura('fecha','date');
        $this->captura('glosa2','varchar');
        $this->captura('nro_tramite','varchar');
        $this->captura('momento','varchar');
        $this->captura('id_usuario_reg','int4');
        $this->captura('fecha_reg','timestamp');
        $this->captura('id_usuario_mod','int4');
        $this->captura('fecha_mod','timestamp');
        $this->captura('usr_reg','varchar');
        $this->captura('usr_mod','varchar');
        $this->captura('desc_clase_comprobante','varchar');
        $this->captura('desc_subsistema','varchar');
        $this->captura('desc_depto','text');
        $this->captura('desc_moneda','text');
        $this->captura('desc_firma1','text');
        $this->captura('desc_firma2','text');
        $this->captura('desc_firma3','text');
        $this->captura('momento_comprometido','varchar');
        $this->captura('momento_ejecutado','varchar');
        $this->captura('momento_pagado','varchar');
        $this->captura('manual','varchar');
        $this->captura('id_int_comprobante_fks','text');
        $this->captura('id_tipo_relacion_comprobante','int');
        $this->captura('desc_tipo_relacion_comprobante','varchar');
        $this->captura('id_moneda_base','int4');
        $this->captura('desc_moneda_base','text');
        $this->captura('cbte_cierre','varchar');
        $this->captura('cbte_apertura','varchar');
        $this->captura('cbte_aitb','varchar');
        $this->captura('fecha_costo_ini','date');
        $this->captura('fecha_costo_fin','date');
        $this->captura('tipo_cambio_2','numeric');
        $this->captura('id_moneda_tri','int4');
        $this->captura('sw_tipo_cambio','varchar');
        $this->captura('id_config_cambiaria','int4');
        $this->captura('ope_1','varchar');
        $this->captura('ope_2','varchar');
        $this->captura('desc_moneda_tri','text');
        $this->captura('origen','varchar');
        $this->captura('localidad','varchar');
        $this->captura('sw_editable','varchar');
        $this->captura('cbte_reversion','varchar');
        $this->captura('volcado','varchar');
        $this->captura('id_proceso_wf','int4');
        $this->captura('id_estado_wf','int4');
        $this->captura('fecha_c31','date');
        $this->captura('c31','varchar');
        $this->captura('id_gestion','int4');
        $this->captura('periodo','int4');
        $this->captura('forma_cambio','varchar');
        $this->captura('ope_3','varchar');
        $this->captura('tipo_cambio_3','numeric');
        $this->captura('id_moneda_act','int4');

        $this->captura('id_partida','int4');
        $this->captura('nombre_partida','varchar');
        $this->captura('codigo','varchar');

        $this->armarConsulta();
        //echo $this->getConsulta();exit;
        $this->ejecutarConsulta();
        //Devuelve la respuesta
        return $this->respuesta;
    }

    function listarIntComprobanteTCCCuenta(){
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento='conta.ft_int_comprobante_sel';
        $this->transaccion='CONTA_CBTENCUE_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion

        //Definicion de la lista del resultado del query
        $this->captura('id_int_comprobante','int4');
        $this->captura('fecha','date');
        $this->captura('glosa1','varchar');
        $this->captura('nro_tramite','varchar');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    //filtro listado grilla de comprobantes para las estaciones internacionales en Registro de Comprobantes(contador)EXT
    function listarIntComprobanteEXT(){
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento='conta.ft_int_comprobante_sel';
        $this->transaccion='CONTA_INCBTEEXT_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion

        $this->setParametro('nombreVista','nombreVista','varchar');

        //Definicion de la lista del resultado del query
        $this->captura('id_int_comprobante','int4');
        $this->captura('id_clase_comprobante','int4');
        $this->captura('id_subsistema','int4');
        $this->captura('id_depto','int4');
        $this->captura('id_moneda','int4');
        $this->captura('id_periodo','int4');
        $this->captura('id_funcionario_firma1','int4');
        $this->captura('id_funcionario_firma2','int4');
        $this->captura('id_funcionario_firma3','int4');
        $this->captura('tipo_cambio','numeric');
        $this->captura('beneficiario','varchar');
        $this->captura('nro_cbte','varchar');
        $this->captura('estado_reg','varchar');
        $this->captura('glosa1','varchar');
        $this->captura('fecha','date');
        $this->captura('glosa2','varchar');
        $this->captura('nro_tramite','varchar');
        $this->captura('momento','varchar');
        $this->captura('id_usuario_reg','int4');
        $this->captura('fecha_reg','timestamp');
        $this->captura('id_usuario_mod','int4');
        $this->captura('fecha_mod','timestamp');
        $this->captura('usr_reg','varchar');
        $this->captura('usr_mod','varchar');
        $this->captura('desc_clase_comprobante','varchar');
        $this->captura('desc_subsistema','varchar');
        $this->captura('desc_depto','text');
        $this->captura('desc_moneda','text');
        $this->captura('desc_firma1','text');
        $this->captura('desc_firma2','text');
        $this->captura('desc_firma3','text');
        $this->captura('momento_comprometido','varchar');
        $this->captura('momento_ejecutado','varchar');
        $this->captura('momento_pagado','varchar');
        $this->captura('manual','varchar');
        $this->captura('id_int_comprobante_fks','text');
        $this->captura('id_tipo_relacion_comprobante','int');
        $this->captura('desc_tipo_relacion_comprobante','varchar');
        $this->captura('id_moneda_base','int4');
        $this->captura('desc_moneda_base','text');
        $this->captura('cbte_cierre','varchar');
        $this->captura('cbte_apertura','varchar');
        $this->captura('cbte_aitb','varchar');
        $this->captura('fecha_costo_ini','date');
        $this->captura('fecha_costo_fin','date');
        $this->captura('tipo_cambio_2','numeric');
        $this->captura('id_moneda_tri','int4');
        $this->captura('sw_tipo_cambio','varchar');
        $this->captura('id_config_cambiaria','int4');
        $this->captura('ope_1','varchar');
        $this->captura('ope_2','varchar');
        $this->captura('desc_moneda_tri','text');
        $this->captura('origen','varchar');
        $this->captura('localidad','varchar');
        $this->captura('sw_editable','varchar');
        $this->captura('cbte_reversion','varchar');
        $this->captura('volcado','varchar');
        $this->captura('id_proceso_wf','int4');
        $this->captura('id_estado_wf','int4');
        $this->captura('fecha_c31','date');
        $this->captura('c31','varchar');
        $this->captura('id_gestion','int4');
        $this->captura('periodo','int4');
        $this->captura('forma_cambio','varchar');

        $this->captura('ope_3','varchar');
        $this->captura('tipo_cambio_3','numeric');
        $this->captura('id_moneda_act','int4');



        //Ejecuta la instruccion
        $this->armarConsulta();
        //echo $this->getConsulta();exit;
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    //may regularizacion de comprobantes
    function cbteRegularizacion(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento = 'conta.ft_int_comprobante_ime';
        $this->transaccion = 'CD_REGCBTE_IME';
        $this->tipo_procedimiento = 'IME';

        //Define los parametros para la funcion
        $this->setParametro('id_int_comprobante','id_int_comprobante','int4');
        $this->setParametro('id_proceso_wf_act','id_proceso_wf_act','int4');
        $this->setParametro('id_estado_wf_act','id_estado_wf_act','int4');
        $this->setParametro('id_funcionario_usu','id_funcionario_usu','int4');
        $this->setParametro('id_tipo_estado','id_tipo_estado','int4');
        $this->setParametro('id_funcionario_wf','id_funcionario_wf','int4');
        $this->setParametro('id_depto_wf','id_depto_wf','int4');
        $this->setParametro('obs','obs','text');
        $this->setParametro('json_procesos','json_procesos','text');
        //$this->setParametro('validar_doc','validar_doc','boolean');



        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();
        //Devuelve la respuesta
        return $this->respuesta;
    }

    //may
    function volcarCbteContable(){
        //swEditable de variables para ejecucion del procedimiento
        $this->procedimiento='conta.ft_int_comprobante_ime';
        $this->transaccion='CONTA_VOLCBTECON_IME';
        $this->tipo_procedimiento='IME';

        //Define los parametros para la funcion
        $this->setParametro('id_int_comprobante','id_int_comprobante','int4');
        $this->setParametro('sw_validar','sw_validar','varchar');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    //may
    function cbtePerdidaCbte(){
        //swEditable de variables para ejecucion del procedimiento
        $this->procedimiento='conta.ft_int_comprobante_ime';
        $this->transaccion='CONTA_CBTEPER_IME';
        $this->tipo_procedimiento='IME';

        //Define los parametros para la funcion
        $this->setParametro('id_int_comprobante','id_int_comprobante','int4');
        $this->setParametro('sw_validar','sw_validar','varchar');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    //may
    function cbteIncrementoCbte(){
        //swEditable de variables para ejecucion del procedimiento
        $this->procedimiento='conta.ft_int_comprobante_ime';
        $this->transaccion='CONTA_CBTEINCR_IME';
        $this->tipo_procedimiento='IME';

        //Define los parametros para la funcion
        $this->setParametro('id_int_comprobante','id_int_comprobante','int4');
        $this->setParametro('sw_validar','sw_validar','varchar');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }
    //{develop: franklin.espinoza date: 12/10/2020, description: Guarda Preventivo,Compromiso,Devengado para procesos con Preventivo}
    function guardarDocumentoSigep(){
        //swEditable de variables para ejecucion del procedimiento
        $this->procedimiento='conta.ft_int_comprobante_ime';
        $this->transaccion='CONTA_DOCSIGEP_IME';
        $this->tipo_procedimiento='IME';

        //Define los parametros para la funcion
        $this->setParametro('id_int_comprobante','id_int_comprobante','integer');
        $this->setParametro('preventivo','preventivo','integer');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    function desvalidarCBTE(){
        //swEditable de variables para ejecucion del procedimiento
        $this->procedimiento='conta.ft_int_comprobante_ime';
        $this->transaccion='CONTA_DESV_CBT_IME';
        $this->tipo_procedimiento='IME';

        //Define los parametros para la funcion
        $this->setParametro('id_proceso_wf','id_proceso_wf','int4');
        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    //{develop: franklin.espinoza date: 14/01/2022, description: Lista los comprobantes C21}
    function listarIntComprobanteC21(){
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento='conta.ft_int_comprobante_sel';
        $this->transaccion='CONTA_CBTE_C21_SEL';
        $this->tipo_procedimiento='SEL';//tipo de transaccion

        $this->setParametro('nombreVista','nombreVista','varchar');
        //Definicion de la lista del resultado del query
        $this->captura('id_int_comprobante','int4');
        $this->captura('id_clase_comprobante','int4');
        $this->captura('id_subsistema','int4');
        $this->captura('id_depto','int4');
        $this->captura('id_moneda','int4');
        $this->captura('id_periodo','int4');
        $this->captura('id_funcionario_firma1','int4');
        $this->captura('id_funcionario_firma2','int4');
        $this->captura('id_funcionario_firma3','int4');
        $this->captura('tipo_cambio','numeric');
        $this->captura('beneficiario','varchar');
        $this->captura('nro_cbte','varchar');
        $this->captura('estado_reg','varchar');
        $this->captura('glosa1','varchar');
        $this->captura('fecha','date');
        $this->captura('glosa2','varchar');
        $this->captura('nro_tramite','varchar');
        $this->captura('momento','varchar');
        $this->captura('id_usuario_reg','int4');
        $this->captura('fecha_reg','timestamp');
        $this->captura('id_usuario_mod','int4');
        $this->captura('fecha_mod','timestamp');
        $this->captura('usr_reg','varchar');
        $this->captura('usr_mod','varchar');
        $this->captura('desc_clase_comprobante','varchar');
        $this->captura('desc_subsistema','varchar');
        $this->captura('desc_depto','text');
        $this->captura('desc_moneda','text');
        $this->captura('desc_firma1','text');
        $this->captura('desc_firma2','text');
        $this->captura('desc_firma3','text');
        $this->captura('momento_comprometido','varchar');
        $this->captura('momento_ejecutado','varchar');
        $this->captura('momento_pagado','varchar');
        $this->captura('manual','varchar');
        $this->captura('id_int_comprobante_fks','text');
        $this->captura('id_tipo_relacion_comprobante','int');
        $this->captura('desc_tipo_relacion_comprobante','varchar');
        $this->captura('id_moneda_base','int4');
        $this->captura('desc_moneda_base','text');
        $this->captura('cbte_cierre','varchar');
        $this->captura('cbte_apertura','varchar');
        $this->captura('cbte_aitb','varchar');
        $this->captura('fecha_costo_ini','date');
        $this->captura('fecha_costo_fin','date');
        $this->captura('tipo_cambio_2','numeric');
        $this->captura('id_moneda_tri','int4');
        $this->captura('sw_tipo_cambio','varchar');
        $this->captura('id_config_cambiaria','int4');
        $this->captura('ope_1','varchar');
        $this->captura('ope_2','varchar');
        $this->captura('desc_moneda_tri','text');
        $this->captura('origen','varchar');
        $this->captura('localidad','varchar');
        $this->captura('sw_editable','varchar');
        $this->captura('cbte_reversion','varchar');
        $this->captura('volcado','varchar');
        $this->captura('id_proceso_wf','int4');
        $this->captura('id_estado_wf','int4');
        $this->captura('fecha_c21','date');
        $this->captura('c21','varchar');
        $this->captura('id_gestion','int4');
        $this->captura('periodo','int4');
        $this->captura('forma_cambio','varchar');

        $this->captura('ope_3','varchar');
        $this->captura('tipo_cambio_3','numeric');
        $this->captura('id_moneda_act','int4');
        //franklin.espinoza 27/09/2020
        $this->captura('id_service_request','integer');
        $this->captura('id_depto_libro','integer');
        $this->captura('id_cuenta_bancaria','integer');
        $this->captura('desc_cuenta_bancaria','text');
        $this->captura('desc_depto_lb','varchar');
        $this->captura('tipo_cbte','varchar');
        $this->captura('reversion','varchar');

        $this->captura('momento_devengado','varchar');
        $this->captura('momento_percibido','varchar');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();
        //Devuelve la respuesta
        return $this->respuesta;
    }

    //{develop: franklin.espinoza date: 14/01/2022, description: Insertar Documentos C21}
    function insertarIntComprobanteC21(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='conta.ft_int_comprobante_ime';
        $this->transaccion='CONTA_CBTE_C21_INS';
        $this->tipo_procedimiento='IME';

        //Define los parametros para la funcion
        $this->setParametro('id_depto','id_depto','int4');
        $this->setParametro('id_depto_libro','id_depto_libro','integer');
        $this->setParametro('id_cuenta_bancaria','id_cuenta_bancaria','integer');
        $this->setParametro('glosa1','glosa1','varchar');
        $this->setParametro('fecha','fecha','date');
        $this->setParametro('id_moneda','id_moneda','int4');
        $this->setParametro('forma_cambio','forma_cambio','varchar');
        $this->setParametro('tipo_cambio','tipo_cambio','numeric');
        $this->setParametro('tipo_cambio_2','tipo_cambio_2','numeric');
        $this->setParametro('tipo_cambio_3','tipo_cambio_3','numeric');
        $this->setParametro('id_clase_comprobante','id_clase_comprobante','int4');
        $this->setParametro('momento_devengado','momento_devengado','varchar');
        $this->setParametro('momento_percibido','momento_percibido','varchar');
        $this->setParametro('reversion','reversion','varchar');

        $this->setParametro('id_subsistema','id_subsistema','int4');
        $this->setParametro('id_periodo','id_periodo','int4');
        $this->setParametro('id_config_cambiaria','id_config_cambiaria','integer');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

    //{develop: franklin.espinoza date: 14/01/2022, description: Modificar Documentos C21}
    function modificarIntComprobanteC21(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='conta.ft_int_comprobante_ime';
        $this->transaccion='CONTA_CBTE_C21_MOD';
        $this->tipo_procedimiento='IME';

        //Define los parametros para la funcion
        $this->setParametro('id_int_comprobante','id_int_comprobante','int4');
        $this->setParametro('id_depto','id_depto','int4');
        $this->setParametro('glosa1','glosa1','varchar');
        $this->setParametro('fecha','fecha','date');
        $this->setParametro('id_moneda','id_moneda','int4');
        $this->setParametro('forma_cambio','forma_cambio','varchar');
        $this->setParametro('tipo_cambio','tipo_cambio','numeric');
        $this->setParametro('tipo_cambio_2','tipo_cambio_2','numeric');
        $this->setParametro('tipo_cambio_3','tipo_cambio_3','numeric');
        $this->setParametro('id_clase_comprobante','id_clase_comprobante','int4');
        $this->setParametro('momento_devengado','momento_devengado','varchar');
        $this->setParametro('momento_percibido','momento_percibido','varchar');
        $this->setParametro('reversion','reversion','varchar');

        $this->setParametro('id_subsistema','id_subsistema','int4');
        $this->setParametro('id_periodo','id_periodo','int4');
        $this->setParametro('id_config_cambiaria','id_config_cambiaria','integer');

        $this->setParametro('id_depto_libro','id_depto_libro','integer');
        $this->setParametro('id_cuenta_bancaria','id_cuenta_bancaria','integer');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

}
?>