<?php
/**
*@package pXP
*@file gen-MODHistorialRegCompras.php
*@author  (franklin.espinoza)
*@date 07-06-2018 15:14:54
*@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
*/

class MODHistorialRegCompras extends MODbase{
	
	function __construct(CTParametro $pParam){
		parent::__construct($pParam);
	}
			
	function listarHistorialRegCompras(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='conta.ft_historial_reg_compras_sel';
		$this->transaccion='CONTA_HRC_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion
				
		//Definicion de la lista del resultado del query
		$this->captura('id_historial_reg_compras','int4');
		$this->captura('id_doc_compra_venta','integer');
		$this->captura('nit','varchar');
		$this->captura('fecha_cambio','timestamp');
		$this->captura('nro_tramite','varchar');
		$this->captura('nro_documento','varchar');
		$this->captura('codigo_control','varchar');
		$this->captura('nro_autorizacion','varchar');
		$this->captura('id_funcionario','int4');
		$this->captura('estado_reg','varchar');
		$this->captura('razon_social','varchar');
		$this->captura('id_usuario_ai','int4');
		$this->captura('usuario_ai','varchar');
		$this->captura('fecha_reg','timestamp');
		$this->captura('id_usuario_reg','int4');
		$this->captura('fecha_mod','timestamp');
		$this->captura('id_usuario_mod','int4');
		$this->captura('usr_reg','varchar');
		$this->captura('usr_mod','varchar');
		$this->captura('desc_func','varchar');
		$this->captura('importe_neto','numeric');

		
		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();
		
		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function insertarHistorialRegCompras(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='conta.ft_historial_reg_compras_ime';
		$this->transaccion='CONTA_HRC_INS';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('nit','nit','varchar');
		$this->setParametro('fecha_cambio','fecha_cambio','date');
		$this->setParametro('nro_tramite','nro_tramite','varchar');
		$this->setParametro('nro_factura','nro_factura','varchar');
		$this->setParametro('codigo_control','codigo_control','varchar');
		$this->setParametro('nro_autorizacion','nro_autorizacion','varchar');
		$this->setParametro('id_funcionario','id_funcionario','int4');
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('razon_social','razon_social','varchar');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function modificarHistorialRegCompras(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='conta.ft_historial_reg_compras_ime';
		$this->transaccion='CONTA_HRC_MOD';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_historial_reg_compras','id_historial_reg_compras','int4');
		$this->setParametro('nit','nit','varchar');
		$this->setParametro('fecha_cambio','fecha_cambio','date');
		$this->setParametro('nro_tramite','nro_tramite','varchar');
		$this->setParametro('nro_factura','nro_factura','varchar');
		$this->setParametro('codigo_control','codigo_control','varchar');
		$this->setParametro('nro_autorizacion','nro_autorizacion','varchar');
		$this->setParametro('id_funcionario','id_funcionario','int4');
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('razon_social','razon_social','varchar');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function eliminarHistorialRegCompras(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='conta.ft_historial_reg_compras_ime';
		$this->transaccion='CONTA_HRC_ELI';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_historial_reg_compras','id_historial_reg_compras','int4');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
}
?>