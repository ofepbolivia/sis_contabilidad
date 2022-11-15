<?php
/**
*@package pXP
*@file gen-MODIntBeneficiario.php
*@author  (admin)
*@date 27-10-2022 14:42:32
*@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
*/

class MODIntBeneficiario extends MODbase{
	
	function __construct(CTParametro $pParam){
		parent::__construct($pParam);
	}
			
	function listarIntBeneficiario(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='conta.ft_int_beneficiario_sel';
		$this->transaccion='CONTA_intBenef_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion

		//captura parametros adicionales para el count
        $this->capturaCount('total_importe', 'numeric');
				
		//Definicion de la lista del resultado del query
		$this->captura('id_beneficiario','int4');
		$this->captura('estado_reg','varchar');
		$this->captura('id_funcionario_beneficiario','int4');
		$this->captura('id_int_comprobante','int4');
		$this->captura('banco','varchar');
		$this->captura('nro_cuenta_bancaria_sigma','varchar');
		$this->captura('importe','numeric');
		$this->captura('id_usuario_reg','int4');
		$this->captura('fecha_reg','timestamp');
		$this->captura('id_usuario_ai','int4');
		$this->captura('usuario_ai','varchar');
		$this->captura('id_usuario_mod','int4');
		$this->captura('fecha_mod','timestamp');
		$this->captura('usr_reg','varchar');
		$this->captura('usr_mod','varchar');
		$this->captura('tipo_doc','varchar');
		$this->captura('nro_documento','varchar');
		$this->captura('nombre_razon_social','varchar');
		
		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();
		
		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function insertarIntBeneficiario(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='conta.ft_int_beneficiario_ime';
		$this->transaccion='CONTA_intBenef_INS';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('id_funcionario_beneficiario','id_funcionario_beneficiario','int4');
		$this->setParametro('id_int_comprobante','id_int_comprobante','int4');
		$this->setParametro('banco','banco','varchar');
		$this->setParametro('nro_cuenta_bancaria_sigma','nro_cuenta_bancaria_sigma','varchar');
		$this->setParametro('importe','importe','numeric');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function modificarIntBeneficiario(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='conta.ft_int_beneficiario_ime';
		$this->transaccion='CONTA_intBenef_MOD';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_beneficiario','id_beneficiario','int4');
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('id_funcionario_beneficiario','id_funcionario_beneficiario','int4');
		$this->setParametro('id_int_comprobante','id_int_comprobante','int4');
		$this->setParametro('banco','banco','varchar');
		$this->setParametro('nro_cuenta_bancaria_sigma','nro_cuenta_bancaria_sigma','varchar');
		$this->setParametro('importe','importe','numeric');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function eliminarIntBeneficiario(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='conta.ft_int_beneficiario_ime';
		$this->transaccion='CONTA_intBenef_ELI';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_beneficiario','id_beneficiario','int4');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}

	function listarIntBeneficiarioProvCombo(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='conta.ft_int_beneficiario_sel';
		$this->transaccion='CONTA_PROVEEV_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion
				
		//Definicion de la lista del resultado del query
		$this->captura('id_funcionario_beneficiario','int4');
		$this->captura('nombre_razon_social','varchar');
		$this->captura('nro_documento','varchar');
		$this->captura('banco_beneficiario','varchar');
		$this->captura('nro_cuenta','varchar');

		//Ejecuta la instruccion
		$this->armarConsulta();
		
		$this->ejecutarConsulta();
		
		//Devuelve la respuesta
		return $this->respuesta;
	}
			
}
?>