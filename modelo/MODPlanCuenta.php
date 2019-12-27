<?php
/**
*@package pXP
*@file gen-MODPlanCuenta.php
*@author  (alan.felipez)
*@date 25-11-2019 22:15:53
*@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
*/

class MODPlanCuenta extends MODbase{
	
	function __construct(CTParametro $pParam){
		parent::__construct($pParam);
	}
			
	function listarPlanCuenta(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='conta.ft_plan_cuenta_sel';
		$this->transaccion='CONTA_IPC_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion
				
		//Definicion de la lista del resultado del query
		$this->captura('id_plan_cuenta','int4');
		$this->captura('estado_reg','varchar');
		$this->captura('nombre','varchar');
		$this->captura('estado','varchar');
        $this->captura('id_gestion','int4');
		$this->captura('id_usuario_reg','int4');
		$this->captura('fecha_reg','timestamp');
		$this->captura('id_usuario_ai','int4');
		$this->captura('usuario_ai','varchar');
		$this->captura('id_usuario_mod','int4');
		$this->captura('fecha_mod','timestamp');
        $this->captura('desc_gestion','int4');
		$this->captura('usr_reg','varchar');
		$this->captura('usr_mod','varchar');
		
		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();
		
		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function insertarPlanCuenta(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='conta.ft_plan_cuenta_ime';
		$this->transaccion='CONTA_IPC_INS';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('nombre','nombre','varchar');
		$this->setParametro('estado','estado','varchar');
        $this->setParametro('id_gestion','id_gestion','int4');
		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function modificarPlanCuenta(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='conta.ft_plan_cuenta_ime';
		$this->transaccion='CONTA_IPC_MOD';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_plan_cuenta','id_plan_cuenta','int4');
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('nombre','nombre','varchar');
		$this->setParametro('estado','estado','varchar');
        $this->setParametro('id_gestion','id_gestion','int4');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}

    function actualizarEstado(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='conta.ft_plan_cuenta_ime';
        $this->transaccion='CONTA_ACT_EST';
        $this->tipo_procedimiento='IME';

        //Define los parametros para la funcion
        $this->setParametro('id_plan_cuenta','id_plan_cuenta','int4');
        $this->setParametro('estado_reg','estado_reg','varchar');
        $this->setParametro('estado','estado','varchar');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

	function eliminarPlanCuenta(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='conta.ft_plan_cuenta_ime';
		$this->transaccion='CONTA_IPC_ELI';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_plan_cuenta','id_plan_cuenta','int4');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
    function revertirCargaArchivoExcel(){
        $this->procedimiento='conta.ft_plan_cuenta_ime';
        $this->transaccion='CONTA_PLANCTA_ELI';
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