<?php
/**
*@package pXP
*@file gen-MODTipoCambioPais.php
*@author  (ivaldivia)
*@date 07-08-2019 14:12:25
*@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
*/
include '../../lib/lib_modelo/ConexionSqlServer.php';
class MODTipoCambioPais extends MODbase{

	function __construct(CTParametro $pParam){
		parent::__construct($pParam);
		// $this->cone = new conexion();
		// $this->informix = $this->cone->conectarPDOInformix();
	}

	function listarTipoCambioPais(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='conta.ft_tipo_cambio_pais_sel';
		$this->transaccion='CONTA_TCPA_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion

		//Definicion de la lista del resultado del query
		$this->captura('id_tipo_cambio_pais','int4');
		$this->captura('estado_reg','varchar');
		$this->captura('fecha','date');
		$this->captura('oficial','numeric');
		$this->captura('compra','numeric');
		$this->captura('venta','numeric');
		$this->captura('observaciones','varchar');
		$this->captura('id_moneda_pais','int4');
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

	function insertarTipoCambioPais(){
		//Definicion de variables para ejecucion del procedimiento
    /*Recupero los datos del ultimo registro*/
    $cone = new conexion();
    $link = $cone->conectarpdo();
    $copiado = false;

    try {
        $link->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $link->beginTransaction();
    /****************************************/

		$this->procedimiento='conta.ft_tipo_cambio_pais_ime';
		$this->transaccion='CONTA_TCPA_INS';
		$this->tipo_procedimiento='IME';

		//Define los parametros para la funcion
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('fecha','fecha','date');
		$this->setParametro('oficial','oficial','numeric');
		$this->setParametro('compra','compra','numeric');
		$this->setParametro('venta','venta','numeric');
		$this->setParametro('observaciones','observaciones','varchar');
		$this->setParametro('id_moneda_pais','id_moneda_pais','int4');
		$this->setParametro('id_lugar','id_lugar','int4');
		$this->setParametro('id_moneda','id_moneda','int4');

		//Ejecuta la instruccion
    //Ejecuta la instruccion
    $this->armarConsulta();
    $stmt = $link->prepare($this->consulta);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    //recupera parametros devuelto depues de insertar ... (id_formula)
    $resp_procedimiento = $this->divRespuesta($result['f_intermediario_ime']);
    if ($resp_procedimiento['tipo_respuesta']=='ERROR') {
        throw new Exception("Error al ejecutar en la bd", 3);
    }

    $respuesta = $resp_procedimiento['datos'];

		$this->insertarTipoCambioSQLServer($respuesta);

		/***************************************************************************/

    //si todo va bien confirmamos y regresamos el resultado
    $link->commit();
    $this->respuesta=new Mensaje();
    $this->respuesta->setMensaje($resp_procedimiento['tipo_respuesta'],$this->nombre_archivo,$resp_procedimiento['mensaje'],$resp_procedimiento['mensaje_tec'],'base',$this->procedimiento,$this->transaccion,$this->tipo_procedimiento,$this->consulta);
    $this->respuesta->setDatos($respuesta);
  } catch (Exception $e) {
          $link->rollBack();
          $this->respuesta=new Mensaje();
          if ($e->getCode() == 3) {//es un error de un procedimiento almacenado de pxp
              $this->respuesta->setMensaje($resp_procedimiento['tipo_respuesta'],$this->nombre_archivo,$resp_procedimiento['mensaje'],$resp_procedimiento['mensaje_tec'],'base',$this->procedimiento,$this->transaccion,$this->tipo_procedimiento,$this->consulta);
          } else if ($e->getCode() == 2) {//es un error en bd de una consulta
              $this->respuesta->setMensaje('ERROR',$this->nombre_archivo,$e->getMessage(),$e->getMessage(),'modelo','','','','');
          } else {//es un error lanzado con throw exception
              throw new Exception($e->getMessage(), 2);
          }

  }
		return $this->respuesta;
	}
	/**************Insertamos en las base de datos********************************/

	function insertarTipoCambioSQLServer($respuesta) {
		$this->link = new ConexionSqlServer($_SESSION['_SQL_HOST'],$_SESSION['_SQL_USER'], $_SESSION['_SQL_PASS'], $_SESSION['_SQL_BD']);
		$this->conexion = $this->link->conectarSQL();
		try {
		$arreglo_fecha= explode( '/', $this->aParam->getParametro('fecha') );
		$fecha = ($arreglo_fecha[2].'-'.$arreglo_fecha[1].'-'.$arreglo_fecha[0]);

		$idErp = $respuesta['id_tipo_cambio_pais'];
		$id_moneda_pais = $this->aParam->getParametro('id_moneda_pais');
		$oficial = $this->aParam->getParametro('oficial');
		$compra = $this->aParam->getParametro('compra');
		$venta = $this->aParam->getParametro('venta');
		$observaciones = $this->aParam->getParametro('observaciones');
		$estado = 'Activo';

		/***************Realizamos la conexion y el registro de datos********************/
		$sql = "EXEC [ParametrosGenerales].[dbo].[TipoCambioCRUD] N'INS',$idErp,$id_moneda_pais,'$fecha',$oficial,$compra, $venta, '$observaciones','$estado'";
		$consulta = @mssql_query(utf8_decode($sql), $this->conexion);
		/*************************************************************************************************/
		}
		catch (Exception $e) {
				throw new Exception("La conexion a la bd POSTGRESQL ha fallado.");
		}
		$this->link->closeSQL();
	}

	function modificarTipoCambioPaisSQLServer() {
		/*Obtenemos la fecha actual para comprar con la fecha de modificacion*/
		$fecha_registro= explode( '/', $this->aParam->getParametro('fecha_reg') );
		$fecha_reg = ($fecha_registro[2].'-'.$fecha_registro[1].'-'.$fecha_registro[0]);
		$fecha_actual = date("Y")."-".date("m")."-".date("d");
		/*********************************************************************/
		if ($fecha_actual != $fecha_reg) {
				throw new Exception("Solo se puede modificar el tipo de cambio en la misma fecha que se registró. La fecha de registro para este tipo de cambio es del: ".$this->aParam->getParametro('fecha_reg'));
		}
		$this->link = new ConexionSqlServer($_SESSION['_SQL_HOST'],$_SESSION['_SQL_USER'], $_SESSION['_SQL_PASS'], $_SESSION['_SQL_BD']);
		$this->conexion = $this->link->conectarSQL();
		try {
		$arreglo_fecha= explode( '/', $this->aParam->getParametro('fecha') );
		$fecha = ($arreglo_fecha[2].'-'.$arreglo_fecha[1].'-'.$arreglo_fecha[0]);
		$idErp = $this->aParam->getParametro('id_tipo_cambio_pais');
		$id_moneda_pais = $this->aParam->getParametro('id_moneda_pais');
		$oficial = $this->aParam->getParametro('oficial');
		$compra = $this->aParam->getParametro('compra');
		$venta = $this->aParam->getParametro('venta');
		$observaciones = $this->aParam->getParametro('observaciones');
		$estado = 'Activo';
		/***************Realizamos la conexion y el registro de datos********************/
		$sql = "EXEC [ParametrosGenerales].[dbo].[TipoCambioCRUD] N'UPD',$idErp,$id_moneda_pais,'$fecha',$oficial,$compra, $venta, '$observaciones','$estado'";
		$consulta = @mssql_query(utf8_decode($sql), $this->conexion);
		/*************************************************************************************************/

		}
		catch (Exception $e) {
				throw new Exception("La conexion a la bd POSTGRESQL ha fallado.");
		}
		$this->link->closeSQL();
	}

	function eliminarTipoCambioPaisSQLServer($respuesta) {
		$this->link = new ConexionSqlServer($_SESSION['_SQL_HOST'],$_SESSION['_SQL_USER'], $_SESSION['_SQL_PASS'], $_SESSION['_SQL_BD']);
		$this->conexion = $this->link->conectarSQL();
		try {
		$id_tipo_cambio = $respuesta['id_tipo_cambio_pais'];
		/***************Realizamos la conexion y el registro de datos********************/
		$sql = "EXEC [ParametrosGenerales].[dbo].[TipoCambioCRUD] N'DEL',$id_tipo_cambio,0,'',0,0, 0, '',''";
		$consulta = @mssql_query(utf8_decode($sql), $this->conexion);
		/*************************************************************************************************/
		}catch (Exception $e) {
				throw new Exception("La conexion a la bd POSTGRESQL ha fallado.");
		}
		$this->link->closeSQL();
	}
/*******************************************************************************/


/**************************************Recuperamos el codigo del pais*******************************************/
	function pais(){
		$cone = new conexion();
		$link = $cone->conectarpdo();
		$copiado = false;

		$consulta ="select lu.codigo
								from param.tlugar lu
								where lu.id_lugar = ".$this->objParam->getParametro('id_lugar');

		$res = $link->prepare($consulta);
		$res->execute();
		$result = $res->fetchAll(PDO::FETCH_ASSOC);
		return $result[0]['codigo'];
	}
/****************************************************************************************************************/
	function modificarTipoCambioPais(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='conta.ft_tipo_cambio_pais_ime';
		$this->transaccion='CONTA_TCPA_MOD';
		$this->tipo_procedimiento='IME';

		//Define los parametros para la funcion
		$this->setParametro('id_tipo_cambio_pais','id_tipo_cambio_pais','int4');
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('fecha','fecha','date');
		$this->setParametro('oficial','oficial','numeric');
		$this->setParametro('compra','compra','numeric');
		$this->setParametro('venta','venta','numeric');
		$this->setParametro('observaciones','observaciones','varchar');
		$this->setParametro('id_moneda_pais','id_moneda_pais','int4');
		$this->setParametro('fecha_reg','fecha_reg','date');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();
		$this->modificarTipoCambioPaisSQLServer();
		//Devuelve la respuesta
		return $this->respuesta;
	}

	function eliminarTipoCambioPais(){
		//Definicion de variables para ejecucion del procedimiento
		$cone = new conexion();
		$link = $cone->conectarpdo();
		$copiado = false;

		try {
				$link->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				$link->beginTransaction();

		$this->procedimiento='conta.ft_tipo_cambio_pais_ime';
		$this->transaccion='CONTA_TCPA_ELI';
		$this->tipo_procedimiento='IME';

		//Define los parametros para la funcion
		$this->setParametro('id_tipo_cambio_pais','id_tipo_cambio_pais','int4');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$stmt = $link->prepare($this->consulta);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);

		//recupera parametros devuelto depues de insertar ... (id_formula)
		$resp_procedimiento = $this->divRespuesta($result['f_intermediario_ime']);
		if ($resp_procedimiento['tipo_respuesta']=='ERROR') {
				throw new Exception("Error al ejecutar en la bd", 3);
		}

		$respuesta = $resp_procedimiento['datos'];

		$this->eliminarTipoCambioPaisSQLServer($respuesta);


		//si todo va bien confirmamos y regresamos el resultado
		$link->commit();
		$this->respuesta=new Mensaje();
		$this->respuesta->setMensaje($resp_procedimiento['tipo_respuesta'],$this->nombre_archivo,$resp_procedimiento['mensaje'],$resp_procedimiento['mensaje_tec'],'base',$this->procedimiento,$this->transaccion,$this->tipo_procedimiento,$this->consulta);
		$this->respuesta->setDatos($respuesta);
		} catch (Exception $e) {
					$link->rollBack();
					$this->respuesta=new Mensaje();
					if ($e->getCode() == 3) {//es un error de un procedimiento almacenado de pxp
							$this->respuesta->setMensaje($resp_procedimiento['tipo_respuesta'],$this->nombre_archivo,$resp_procedimiento['mensaje'],$resp_procedimiento['mensaje_tec'],'base',$this->procedimiento,$this->transaccion,$this->tipo_procedimiento,$this->consulta);
					} else if ($e->getCode() == 2) {//es un error en bd de una consulta
							$this->respuesta->setMensaje('ERROR',$this->nombre_archivo,$e->getMessage(),$e->getMessage(),'modelo','','','','');
					} else {//es un error lanzado con throw exception
							throw new Exception($e->getMessage(), 2);
					}

		}
		return $this->respuesta;
	}

}
?>
