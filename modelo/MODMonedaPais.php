<?php
/**
*@package pXP
*@file gen-MODMonedaPais.php
*@author  (ivaldivia)
*@date 07-08-2019 14:05:49
*@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
*/
include '../../lib/lib_modelo/ConexionSqlServer.php';
class MODMonedaPais extends MODbase{

	function __construct(CTParametro $pParam){
		parent::__construct($pParam);
	}

	function listarMonedaPais(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='conta.ft_moneda_pais_sel';
		$this->transaccion='CONTA_MONPA_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion

		//Definicion de la lista del resultado del query
		$this->captura('id_moneda_pais','int4');
		$this->captura('estado_reg','varchar');
		$this->captura('id_moneda','int4');
		$this->captura('origen','varchar');
		$this->captura('prioridad','int4');
		$this->captura('tipo_actualizacion','varchar');
		$this->captura('id_lugar','int4');
		$this->captura('id_usuario_reg','int4');
		$this->captura('fecha_reg','timestamp');
		$this->captura('id_usuario_ai','int4');
		$this->captura('usuario_ai','varchar');
		$this->captura('id_usuario_mod','int4');
		$this->captura('fecha_mod','timestamp');
		$this->captura('usr_reg','varchar');
		$this->captura('usr_mod','varchar');
		$this->captura('desc_moneda','varchar');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}

	function insertarMonedaPais(){
		//Definicion de variables para ejecucion del procedimiento

		/*Recupero los datos del ultimo registro*/
    $cone = new conexion();
    $link = $cone->conectarpdo();
    $copiado = false;

    try {
        $link->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $link->beginTransaction();


		$this->procedimiento='conta.ft_moneda_pais_ime';
		$this->transaccion='CONTA_MONPA_INS';
		$this->tipo_procedimiento='IME';

		//Define los parametros para la funcion
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('id_moneda','id_moneda','int4');
		$this->setParametro('origen','origen','varchar');
		$this->setParametro('prioridad','prioridad','int4');
		$this->setParametro('tipo_actualizacion','tipo_actualizacion','varchar');
		$this->setParametro('id_lugar','id_lugar','int4');
		$this->setParametro('id_sql_server','id_sql_server','int4');

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

		$this->insertarMonedaPaisSQLServer($respuesta);

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

	function insertarMonedaPaisSQLServer($respuesta) {
		$this->link = new ConexionSqlServer($_SESSION['_SQL_HOST'],$_SESSION['_SQL_USER'], $_SESSION['_SQL_PASS'], $_SESSION['_SQL_BD']);
		$this->conexion = $this->link->conectarSQL();
		try {
		$id = $respuesta['id_moneda_pais'];
		$id_moneda = $this->aParam->getParametro('id_moneda');
		$estado = 'Activo';
		$origen = $this->aParam->getParametro('origen');
		$prioridad = $this->aParam->getParametro('prioridad');
		$tipo_actualizacion = $this->aParam->getParametro('tipo_actualizacion');
		$id_sql = $this->aParam->getParametro('id_sql_server');
		/***************Realizamos la conexion y el registro de datos********************/
		$sql = "EXEC [ParametrosGenerales].[dbo].[MonedaPaisCRUD] N'INS',$id,$id_moneda,$id_sql,'$estado','$origen', $prioridad, '$tipo_actualizacion'";

		$consulta = @mssql_query(utf8_decode($sql), $this->conexion);
		 //echo $sql;exit;
		/*************************************************************************************************/
		}catch (Exception $e) {
				throw new Exception("La conexion a la bd POSTGRESQL ha fallado.");
		}
		$this->link->closeSQL();
	}

	function modificarMonedaPaisSQLServer() {
		$this->link = new ConexionSqlServer($_SESSION['_SQL_HOST'],$_SESSION['_SQL_USER'], $_SESSION['_SQL_PASS'], $_SESSION['_SQL_BD']);
		$this->conexion = $this->link->conectarSQL();
		try {
		$id = $this->aParam->getParametro('id_moneda_pais');
		$id_moneda = $this->aParam->getParametro('id_moneda');
		$estado = $this->aParam->getParametro('estado_reg');
		$origen = $this->aParam->getParametro('origen');
		$prioridad = $this->aParam->getParametro('prioridad');
		$tipo_actualizacion = $this->aParam->getParametro('tipo_actualizacion');
		$id_sql = $this->aParam->getParametro('id_sql_server');
		/***************Realizamos la conexion y el registro de datos********************/
		$sql = "EXEC [ParametrosGenerales].[dbo].[MonedaPaisCRUD] N'UPD',$id,$id_moneda,$id_sql,'$estado','$origen', $prioridad, '$tipo_actualizacion'";
		$consulta = @mssql_query(utf8_decode($sql), $this->conexion);
		/*************************************************************************************************/
		}catch (Exception $e) {
				throw new Exception("La conexion a la bd POSTGRESQL ha fallado.");
		}
		$this->link->closeSQL();
	}

	function eliminarMonedaPaisSQLServer($respuesta) {
		$this->link = new ConexionSqlServer($_SESSION['_SQL_HOST'],$_SESSION['_SQL_USER'], $_SESSION['_SQL_PASS'], $_SESSION['_SQL_BD']);
		$this->conexion = $this->link->conectarSQL();
		try {
		$id_moneda_pais = $respuesta['id_moneda_pais'];
		/***************Realizamos la conexion y el registro de datos********************/
		$sql = "EXEC [ParametrosGenerales].[dbo].[MonedaPaisCRUD] 'DEL',$id_moneda_pais,0,0,'','', 0, ''";
		$consulta = @mssql_query(utf8_decode($sql), $this->conexion);
		/*************************************************************************************************/
		}catch (Exception $e) {
				throw new Exception("La conexion a la bd POSTGRESQL ha fallado.");
		}
		$this->link->closeSQL();
	}

	function modificarMonedaPais(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='conta.ft_moneda_pais_ime';
		$this->transaccion='CONTA_MONPA_MOD';
		$this->tipo_procedimiento='IME';

		//Define los parametros para la funcion
		$this->setParametro('id_moneda_pais','id_moneda_pais','int4');
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('id_moneda','id_moneda','int4');
		$this->setParametro('origen','origen','varchar');
		$this->setParametro('prioridad','prioridad','int4');
		$this->setParametro('tipo_actualizacion','tipo_actualizacion','varchar');
		$this->setParametro('id_lugar','id_lugar','int4');
		$this->setParametro('id_sql_server','id_sql_server','int4');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();
		$this->modificarMonedaPaisSQLServer();
		//Devuelve la respuesta
		return $this->respuesta;
	}

	function eliminarMonedaPais(){
		//Definicion de variables para ejecucion del procedimiento
		/*Recupero los datos del ultimo registro*/
    $cone = new conexion();
    $link = $cone->conectarpdo();
    $copiado = false;

    try {
        $link->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $link->beginTransaction();

		$this->procedimiento='conta.ft_moneda_pais_ime';
		$this->transaccion='CONTA_MONPA_ELI';
		$this->tipo_procedimiento='IME';

		//Define los parametros para la funcion
		$this->setParametro('id_moneda_pais','id_moneda_pais','int4');

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

		$this->eliminarMonedaPaisSQLServer($respuesta);

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
