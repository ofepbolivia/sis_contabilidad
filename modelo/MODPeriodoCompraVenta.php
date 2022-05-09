<?php
/**
*@package pXP
*@file gen-MODPeriodoCompraVenta.php
*@author  (admin)
*@date 24-08-2015 14:16:54
*@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
*/

class MODPeriodoCompraVenta extends MODbase{

	function __construct(CTParametro $pParam){
		parent::__construct($pParam);
	}

	function listarPeriodoCompraVenta(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='conta.ft_periodo_compra_venta_sel';
		$this->transaccion='CONTA_PCV_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion

		//Definicion de la lista del resultado del query
		$this->captura('id_periodo_compra_venta','int4');
		$this->captura('estado','varchar');
		$this->captura('estado_comisionistas','varchar');
		$this->captura('id_periodo','int4');
		$this->captura('estado_reg','varchar');
		$this->captura('id_depto','int4');
		$this->captura('id_usuario_ai','int4');
		$this->captura('fecha_reg','timestamp');
		$this->captura('usuario_ai','varchar');
		$this->captura('id_usuario_reg','int4');
		$this->captura('id_usuario_mod','int4');
		$this->captura('fecha_mod','timestamp');
		$this->captura('usr_reg','varchar');
		$this->captura('usr_mod','varchar');

		$this->captura('id_gestion','integer');
		$this->captura('fecha_ini','date');
		$this->captura('fecha_fin','date');
		$this->captura('periodo','integer');
		$this->captura('mes','varchar');
		$this->captura('cantidad_cerrado','int4');
		$this->captura('cantidad_abierto','int4');
		$this->captura('cantidad_cerrado_parcial','int4');

		$this->captura('cantidad_abierto_comisionistas','int4');
		$this->captura('cantidad_cerrado_comisionistas','int4');
		//Ejecuta la instruccion
        $this->armarConsulta();
        //echo($this->consulta);exit;
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}



	function generarPeriodosCompraVenta(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='conta.ft_periodo_compra_venta_ime';
		$this->transaccion='CONTA_GENPCV_IME';
		$this->tipo_procedimiento='IME';

		//Define los parametros para la funcion
		$this->setParametro('id_depto','id_depto','int4');
		$this->setParametro('id_gestion','id_gestion','int4');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}

   function cerrarAbrirPeriodo(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='conta.ft_periodo_compra_venta_ime';
		$this->transaccion='CONTA_ABRCERPER_IME';
		$this->tipo_procedimiento='IME';

		//Define los parametros para la funcion
		$this->setParametro('id_periodo_compra_venta','id_periodo_compra_venta','int4');
		$this->setParametro('tipo','tipo','varchar');
		$this->setParametro('observacion','observacion','text');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}

    function listarHistorialPeriodoCompra() {
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='conta.ft_periodo_compra_venta_sel';
		$this->transaccion='CONTA_LOGPECOM_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion

        //Definicion de la lista del resultado del query
        $this->captura('id_log_perido_compra','int4');
		$this->captura('id_periodo_compra_venta','int4');
		$this->captura('estado','varchar');
        $this->captura('estado_reg','varchar');
        $this->captura('id_usuario_ai','int4');
        $this->captura('fecha_reg','timestamp');
        $this->captura('fecha_mod','timestamp');
        $this->captura('usuario_ai','varchar');
		$this->captura('id_usuario_reg','int4');
        $this->captura('id_usuario_mod','int4');
		$this->captura('usr_reg','varchar');
		$this->captura('usr_mod','varchar');
        $this->captura('persona_reg','text');
        $this->captura('persona_mod','text');
				$this->captura('observacion','text');

		//Ejecuta la instruccion
        $this->armarConsulta();
        //echo($this->consulta);exit;
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
    }

	function cerrarPeriodosCompra(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='conta.ft_periodo_compra_venta_ime';
        $this->transaccion='CONTA_ABRCERAUT_IME';
        $this->tipo_procedimiento='IME';
        //definicion de variables
        $this->tipo_conexion='seguridad';
        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }

		function cerrarAbrirPeriodoComisionistas(){
		 //Definicion de variables para ejecucion del procedimiento
		 $this->procedimiento='conta.ft_periodo_compra_venta_ime';
		 $this->transaccion='CONTA_PERCOMI_IME';
		 $this->tipo_procedimiento='IME';

		 //Define los parametros para la funcion
		 $this->setParametro('id_periodo_compra_venta','id_periodo_compra_venta','int4');
		 $this->setParametro('tipo','tipo','varchar');
		 $this->setParametro('observacion','observacion','text');
		 //Ejecuta la instruccion
		 $this->armarConsulta();
		 $this->ejecutarConsulta();

		 //Devuelve la respuesta
		 return $this->respuesta;
	 }

	 function listarHistorialPeriodoCompraComisionistas() {
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='conta.ft_periodo_compra_venta_sel';
		$this->transaccion='CONTA_LOGCOMI_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion
		$this->setCount(false);
        //Definicion de la lista del resultado del query
		$this->setParametro('id_periodo_compra_venta','id_periodo_compra_venta','int4');

		$this->captura('datos','text');

		//Ejecuta la instruccion
    $this->armarConsulta();
        //echo($this->consulta);exit;
		$this->ejecutarConsulta();
		//var_dump("aqui llega la respuesta",$this->respuesta);exit;
		//Devuelve la respuesta
		return $this->respuesta;
   }

}
?>
