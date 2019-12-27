<?php
/**
*@package pXP
*@file gen-MODPlanCuentaDet.php
*@author  (alan.felipez)
*@date 25-11-2019 22:17:20
*@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
*/

class MODPlanCuentaDet extends MODbase{
	
	function __construct(CTParametro $pParam){
		parent::__construct($pParam);
	}
			
	function listarPlanCuentaDet(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='conta.ft_plan_cuenta_det_sel';
		$this->transaccion='CONTA_IPCD_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion
				
		//Definicion de la lista del resultado del query
		$this->captura('id_plan_cuenta_det','int4');
		$this->captura('estado_reg','varchar');
		$this->captura('id_plan_cuenta','int4');
        $this->captura('numero','int4');
		$this->captura('nivel','varchar');
		$this->captura('rubro','varchar');
		$this->captura('grupo','varchar');
		$this->captura('sub_grupo','varchar');
		$this->captura('cuenta','varchar');
		$this->captura('codigo_cuenta','varchar');
		$this->captura('sub_cuenta','varchar');
        $this->captura('sub_sub_cuenta','varchar');
		$this->captura('auxiliar','varchar');
		$this->captura('nombre_cuenta','varchar');
		$this->captura('ajuste','varchar');
		$this->captura('moneda_ajuste','varchar');
		$this->captura('tipo_cuenta','varchar');
		$this->captura('moneda','varchar');
		$this->captura('tip_cuenta','varchar');
		$this->captura('permite_auxiliar','varchar');
		$this->captura('cuenta_sigep','int4');
		$this->captura('partida_sigep_debe','varchar');
		$this->captura('partida_sigep_haber','varchar');
		$this->captura('observaciones','varchar');
		$this->captura('id_usuario_reg','int4');
		$this->captura('fecha_reg','timestamp');
		$this->captura('id_usuario_ai','int4');
		$this->captura('usuario_ai','varchar');
		$this->captura('id_usuario_mod','int4');
		$this->captura('fecha_mod','timestamp');
		$this->captura('usr_reg','varchar');
		$this->captura('usr_mod','varchar');
		
		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();
		
		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function insertarPlanCuentaDet(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='conta.ft_plan_cuenta_det_ime';
		$this->transaccion='CONTA_IPCD_INS';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('id_plan_cuenta','id_plan_cuenta','int4');
		$this->setParametro('nivel','nivel','varchar');
		$this->setParametro('rubro','rubro','varchar');
		$this->setParametro('grupo','grupo','varchar');
		$this->setParametro('sub_grupo','sub_grupo','varchar');
		$this->setParametro('cuenta','cuenta','varchar');
		$this->setParametro('codigo_cuenta','codigo_cuenta','varchar');
		$this->setParametro('sub_cuenta','sub_cuenta','varchar');
		$this->setParametro('auxiliar','auxiliar','varchar');
		$this->setParametro('nombre_cuenta','nombre_cuenta','varchar');
		$this->setParametro('ajuste','ajuste','varchar');
		$this->setParametro('moneda_ajuste','moneda_ajuste','varchar');
		$this->setParametro('tipo_cuenta','tipo_cuenta','varchar');
		$this->setParametro('moneda','moneda','varchar');
		$this->setParametro('tip_cuenta','tip_cuenta','varchar');
		$this->setParametro('permite_auxiliar','permite_auxiliar','varchar');
		$this->setParametro('cuenta_sigep','cuenta_sigep','int4');
		$this->setParametro('partida_sigep_debe','partida_sigep_debe','varchar');
		$this->setParametro('partida_sigep_haber','partida_sigep_haber','varchar');
		$this->setParametro('observaciones','observaciones','varchar');
        $this->setParametro('sub_sub_cuenta','sub_sub_cuenta','varchar');
        $this->setParametro('numero','numero','int4');
        $this->setParametro('relacion_cuenta','relacion_cuenta','varchar');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function modificarPlanCuentaDet(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='conta.ft_plan_cuenta_det_ime';
		$this->transaccion='CONTA_IPCD_MOD';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_plan_cuenta_det','id_plan_cuenta_det','int4');
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('id_plan_cuenta','id_plan_cuenta','int4');
        $this->setParametro('numero','numero','int4');
		$this->setParametro('nivel','nivel','varchar');
		$this->setParametro('rubro','rubro','varchar');
		$this->setParametro('grupo','grupo','varchar');
		$this->setParametro('sub_grupo','sub_grupo','varchar');
		$this->setParametro('cuenta','cuenta','varchar');
		$this->setParametro('codigo_cuenta','codigo_cuenta','varchar');
		$this->setParametro('sub_cuenta','sub_cuenta','varchar');
        $this->setParametro('sub_sub_cuenta','sub_cuenta','varchar');
		$this->setParametro('auxiliar','auxiliar','varchar');
		$this->setParametro('nombre_cuenta','nombre_cuenta','varchar');
		$this->setParametro('ajuste','ajuste','varchar');
		$this->setParametro('moneda_ajuste','moneda_ajuste','varchar');
		$this->setParametro('tipo_cuenta','tipo_cuenta','varchar');
		$this->setParametro('moneda','moneda','varchar');
		$this->setParametro('tip_cuenta','tip_cuenta','varchar');
		$this->setParametro('permite_auxiliar','permite_auxiliar','varchar');
		$this->setParametro('cuenta_sigep','cuenta_sigep','int4');
		$this->setParametro('partida_sigep_debe','partida_sigep_debe','varchar');
		$this->setParametro('partida_sigep_haber','partida_sigep_haber','varchar');
		$this->setParametro('observaciones','observaciones','varchar');
        $this->setParametro('relacion_cuenta','relacion_cuenta','varchar');
		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function eliminarPlanCuentaDet(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='conta.ft_plan_cuenta_det_ime';
		$this->transaccion='CONTA_IPCD_ELI';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_plan_cuenta_det','id_plan_cuenta_det','int4');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
    function generarPlanCuenta(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='conta.ft_plan_cuenta_det_ime';
        $this->transaccion='CONTA_GEN_PLAN_CTA';
        $this->tipo_procedimiento='IME';

        //Define los parametros para la funcion
        $this->setParametro('id_plan_cuenta','id_plan_cuenta','int4');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }



}
?>