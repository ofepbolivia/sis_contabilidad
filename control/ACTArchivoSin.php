<?php
/**
 *@package pXP
 *@file gen-ACTArchivoAirbp.php
 *@author  (wherrera)
 *@date 05-03-2024 
 *@description importacion de datos del SIN
 */
include_once(dirname(__FILE__) . '/../../lib/lib_general/ExcelInput.php');
require_once(dirname(__FILE__) . '/../modelo/MODDocCompraVenta.php');

class ACTArchivoSin extends ACTbase
{

	function importarArchivo()
	{
		$codigoArchivo = $this->objParam->getParametro('codigo');

		$arregloFiles = $this->objParam->getArregloFiles();
		$ext = pathinfo($arregloFiles['archivo']['name']);
		$extension = $ext['extension'];
		$error = 'no';
		$mensaje_completo = '';
		$total_facts=0;
		$total_saved=0;		
		//validar errores unicos del archivo: existencia, copia y extension
		if (isset($arregloFiles['archivo']) && is_uploaded_file($arregloFiles['archivo']['tmp_name'])) {
			if (!in_array($extension, array('xls', 'xlsx', 'XLS', 'XLSX'))) {
				$mensaje_completo = "La extensión del archivo debe ser XLS o XLSX";
				$error = 'error_fatal';
			} else {
				//procesa Archivo
				$archivoExcel = new ExcelInput($arregloFiles['archivo']['tmp_name'], $codigoArchivo);
				$archivoExcel->recuperarColumnasExcel();

				$arrayArchivo = $archivoExcel->leerColumnasArchivoExcel();
				$idDocumento = 0;
				$importeExcento = 0;		
				$totalFactura = 0;

				$idPlantilla = 1;
				$idMoneda=1;
				$idDeptoConta = 7;
				$tipo = 'compra';
				if ($arrayArchivo == null || count($arrayArchivo) == 0) {
					throw new Exception('No se recupero ningun registro del archivo verifique su contenido');
				}
				$total_facts=count($arrayArchivo);
				foreach ($arrayArchivo as $fila) { 
					$renglon = new CTParametro('', null, '', '');
					$this->setIniValues($renglon);
					$objParam = $this->recoverLineExcel($fila, $renglon);
					if (!$objParam) {
						continue;
					}
					$objParam->addParametro('id_plantilla', $idPlantilla);
					$objParam->addParametro('tipo', $tipo);			
					$objParam->addParametro('id_moneda', $idMoneda);		
					$objParam->addParametro('id_depto_conta', $idDeptoConta);
					$objParam->addParametro('importe_doc', $objParam->getParametro('importe_neto'));

					$objFunc = new MODDocCompraVenta($objParam);
					$res = $objFunc->insertarDocCompraVenta();

					if ($res->getTipo() == 'ERROR') {
						//$error = 'error';
						$mensaje_completo = "Error al guardar " . $res->getMensajeTec();
						//break;
					} else {
						$datos = $res->getDatos();
						$idDocumento = $datos['id_doc_compra_venta'];
						$total_saved = $total_saved + 1;
					}
				}

			}
		} 
		//upload directory
		$upload_dir = "/tmp/";
		//create file name
		$file_path = $upload_dir . $arregloFiles['archivo']['name'];

		//move uploaded file to upload dir
		if (!move_uploaded_file($arregloFiles['archivo']['tmp_name'], $file_path)) {
			//error moving upload file
			$mensaje_completo = "Error al guardar el archivo csv en disco";
			$error = 'error_fatal';
		}
		// }

		//armar respuesta en caso de exito o error en algunas tuplas
		if ($error == 'error') {
			$mensajeRes = new Mensaje();
			$mensajeRes->setMensaje('ERROR', "ACTArchivoSin.php", 'Ocurrieron los siguientes errores : ' . $mensaje_completo, $mensaje_completo, 'control');
			$mensajeRes->imprimirRespuesta($mensajeRes->generarJson());			
		} else {
			$mensajeRes = new Mensaje();
			$mensajeRes->setMensaje(
				'EXITO',
				'ACTArchivoSin.php',
				'La importación fue ejecutada con éxito, cargados ' . $total_saved. ' de ' . $total_facts,
				'La importación fue ejecutada con éxito',
				'control'
			);
			$mensajeRes->imprimirRespuesta($mensajeRes->generarJson());
		}

		//devolver respuesta
		
	}

	public function parserExcel($file_path)
	{

	}
	public function recoverLineExcel($fila, $renglon = false)
	{
		if (!isset($fila['nro_documento']) || !isset($fila['nro_autorizacion'])) {
			return false;
		}
		if (!$renglon) {
			$renglon = new CTParametro('', null, '', '');		
		}
		foreach ($fila as $k => $v) {
			$renglon->addParametro($k, $v);
		}		
/* 
		$renglon->addParametro('fecha', $fila['fecha']);
		$renglon->addParametro('nit', $fila['nit']);
		$renglon->addParametro('nro_autorizacion', $fila['nro_autorizacion']);
		$renglon->addParametro('nro_documento', $fila['nro_documento']);
		$renglon->addParametro('codigo_control', $fila['codigo_control']);
		$renglon->addParametro('total_bs', $fila['total_bs']); */
		return $renglon;
	}
	function setIniValues($obj){
		$obj->addParametro('importe_ice', 0);
        $obj->addParametro('importe_iva', 0);
        $obj->addParametro('importe_descuento', 0);
        $obj->addParametro('importe_doc', 0);
        $obj->addParametro('importe_it', 0);
        $obj->addParametro('importe_descuento_ley', 0);
        $obj->addParametro('importe_pago_liquido', 0);
        $obj->addParametro('id_moneda', 1);
        $obj->addParametro('importe_pendiente', 0);
        $obj->addParametro('importe_anticipo', 0);
        $obj->addParametro('importe_retgar', 0);
        $obj->addParametro('importe_neto', 0);
        $obj->addParametro('importe_iehd',0);
        $obj->addParametro('importe_ipj', 0);
        $obj->addParametro('importe_tasas', 0);
        $obj->addParametro('importe_gift_card',0);        
        $obj->addParametro('obs','');        		
		$obj->addParametro('nro_dui','');        		
		$obj->addParametro('id_auxiliar','');        		
	}
}

?>

