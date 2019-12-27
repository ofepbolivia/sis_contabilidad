<?php
/**
*@package pXP
*@file gen-ACTPlanCuenta.php
*@author  (alan.felipez)
*@date 25-11-2019 22:15:53
*@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
*/
include_once(dirname(__FILE__).'/../../lib/lib_general/funciones.inc.php');
require_once(dirname(__FILE__).'/../../pxp/pxpReport/ReportWriter.php');
//require_once(dirname(__FILE__).'/../../sis_tesoreria/reportes/RLibroBancos.php');
//require_once(dirname(__FILE__).'/../reportes/RMemoCajaChica.php');
require_once(dirname(__FILE__).'/../../pxp/pxpReport/DataSource.php');
include_once(dirname(__FILE__).'/../../lib/PHPMailer/class.phpmailer.php');
include_once(dirname(__FILE__).'/../../lib/PHPMailer/class.smtp.php');
include_once(dirname(__FILE__).'/../../lib/lib_general/cls_correo_externo.php');
//include_once(dirname(__FILE__).'/../../sis_obingresos/control/ACTArchivoAcmDet.php');

include_once(dirname(__FILE__).'/../../lib/lib_general/ExcelInput.php');


class ACTPlanCuenta extends ACTbase{    
			
	function listarPlanCuenta(){
		$this->objParam->defecto('ordenacion','id_plan_cuenta');

		$this->objParam->defecto('dir_ordenacion','asc');
		if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
			$this->objReporte = new Reporte($this->objParam,$this);
			$this->res = $this->objReporte->generarReporteListado('MODPlanCuenta','listarPlanCuenta');
		} else{
			$this->objFunc=$this->create('MODPlanCuenta');
			
			$this->res=$this->objFunc->listarPlanCuenta($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
				
	function insertarPlanCuenta(){
		$this->objFunc=$this->create('MODPlanCuenta');	
		if($this->objParam->insertar('id_plan_cuenta')){
			$this->res=$this->objFunc->insertarPlanCuenta($this->objParam);			
		} else{			
			$this->res=$this->objFunc->modificarPlanCuenta($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
						
	function eliminarPlanCuenta(){
			$this->objFunc=$this->create('MODPlanCuenta');	
		$this->res=$this->objFunc->eliminarPlanCuenta($this->objParam);
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
    function listarPlantillaArchivoExcel(){
        $this->objParam->defecto('ordenacion','id_plantilla_archivo_excel');

        $this->objParam->defecto('dir_ordenacion','asc');

        if($this->objParam->getParametro('archivoAcm') == 'EXTPC'){
            $this->objParam->addFiltro(" arxls.codigo in(''EXTPC'') ");
        }
        if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
            $this->objReporte = new Reporte($this->objParam,$this);
            $this->res = $this->objReporte->generarReporteListado('sis_parametros/MODPlantillaArchivoExcel','listarPlantillaArchivoExcel');
        } else{
            $this->objFunc=$this->create('sis_parametros/MODPlantillaArchivoExcel');

            $this->res=$this->objFunc->listarPlantillaArchivoExcel($this->objParam);
        }

        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function cargarArchivoExcel(){
        //validar extension del archivo
        $id_archivo_acm = $this->objParam->getParametro('id_plan_cuenta');
        //obtencion del codigo de la plantilla excel
        $codigoArchivo = $this->objParam->getParametro('codigo');

        $arregloFiles = $this->objParam->getArregloFiles();
        $ext = pathinfo($arregloFiles['archivo']['name']);
        $extension = $ext['extension'];

        $error = 'no';
        $mensaje_completo = '';
        //validar errores unicos del archivo: existencia, copia y extension
        if(isset($arregloFiles['archivo']) && is_uploaded_file($arregloFiles['archivo']['tmp_name'])){

            //procesa Archivo
            $archivoExcel = new ExcelInput($arregloFiles['archivo']['tmp_name'], $codigoArchivo);
            $archivoExcel->recuperarColumnasExcel();

            $arrayArchivo = $archivoExcel->leerColumnasArchivoExcel();

            //var_dump($arrayArchivo); exit;
            foreach ($arrayArchivo as $fila) {

                $this->objParam->addParametro('estado_reg', '');
                $this->objParam->addParametro('id_plan_cuenta', $id_archivo_acm);
                $this->objParam->addParametro('nivel', $fila['nivel'] == NULL ? '' : $fila['nivel']);
                $this->objParam->addParametro('rubro',$fila['rubro'] == NULL ? '' : $fila['rubro']);
                $this->objParam->addParametro('grupo',$fila['grupo'] == NULL ? '' : $fila['grupo']);
                $this->objParam->addParametro('sub_grupo',$fila['sub_grupo'] == NULL ? '' : $fila['sub_grupo']);
                $this->objParam->addParametro('cuenta',$fila['cuenta'] == NULL ? '' : $fila['cuenta']);
                $this->objParam->addParametro('codigo_cuenta',$fila['codigo_cuenta'] == NULL ? '' : $fila['codigo_cuenta']);
                $this->objParam->addParametro('sub_cuenta',$fila['sub_cuenta'] == NULL ? '' : $fila['sub_cuenta']);
                $this->objParam->addParametro('auxiliar',$fila['auxiliar'] == NULL ? '' : $fila['auxiliar']);
                $this->objParam->addParametro('nombre_cuenta',$fila['nombre_cuenta'] == NULL ? '' : $fila['nombre_cuenta']);
                $this->objParam->addParametro('ajuste',$fila['ajuste'] == NULL ? '' : $fila['ajuste']);
                $this->objParam->addParametro('moneda_ajuste',$fila['moneda_ajuste'] == NULL ? '' : $fila['moneda_ajuste']);
                $this->objParam->addParametro('tipo_cuenta',$fila['tipo_cuenta'] == NULL ? '' : $fila['tipo_cuenta']);
                $this->objParam->addParametro('moneda',$fila['moneda'] == NULL ? '' : $fila['moneda']);
                $this->objParam->addParametro('tip_cuenta',$fila['tip_cuenta'] == NULL ? '' : $fila['tip_cuenta']);
                $this->objParam->addParametro('permite_auxiliar',$fila['permite_auxiliar'] == NULL ? '' : $fila['permite_auxiliar']);
                $this->objParam->addParametro('cuenta_sigep',$fila['cuenta_sigep'] == NULL ? '' : $fila['cuenta_sigep']);
                $this->objParam->addParametro('partida_sigep_debe',$fila['partida_sigep_debe'] == NULL ? '' : $fila['partida_sigep_debe']);
                $this->objParam->addParametro('partida_sigep_haber',$fila['partida_sigep_haber'] == NULL ? '' : $fila['partida_sigep_haber']);
                $this->objParam->addParametro('observaciones',$fila['observaciones'] == NULL ? '' : $fila['observaciones']);
                $this->objParam->addParametro('sub_sub_cuenta',$fila['sub_sub_cuenta'] == NULL ? '' : $fila['sub_sub_cuenta']);
                $this->objParam->addParametro('numero',$fila['numero'] == NULL ? '' : $fila['numero']);
                $this->objParam->addParametro('relacion_cuenta',$fila['relacion_cuenta'] == NULL ? '' : $fila['relacion_cuenta']);
                //var_dump('llega');exit;
                $this->objFunc = $this->create('sis_contabilidad/MODPlanCuentaDet');
                $this->res = $this->objFunc->insertarPlanCuentaDet($this->objParam);

                if($this->res->getTipo()=='ERROR'){
                    $error = 'error';
                    $mensaje_completo = "Error al guardar el fila en tabla ". $this->res->getMensajeTec();
                }
            }

            //upload directory
            $upload_dir = "/tmp/";
            //create file name
            $file_path = $upload_dir . $arregloFiles['archivo']['name'];

            //move uploaded file to upload dir
            if (!move_uploaded_file($arregloFiles['archivo']['tmp_name'], $file_path)) {
                //error moving upload file
                $mensaje_completo = "Error al guardar el archivo ACM en disco";
                $error = 'error_fatal';
            }
            // }
        } else {
            $mensaje_completo = "No se subio el archivo";
            $error = 'error_fatal';
        }
        //armar respuesta en error fatal
        if ($error == 'error_fatal') {

            $this->mensajeRes=new Mensaje();
            $this->mensajeRes->setMensaje('ERROR','ACTColumnaCalor.php',$mensaje_completo,
                $mensaje_completo,'control');
            //si no es error fatal proceso el archivo
        } else {
            $lines = file($file_path);

        }
        //armar respuesta en caso de exito o error en algunas tuplas
        if ($error == 'error') {
            $this->mensajeRes=new Mensaje();
            $this->mensajeRes->setMensaje('ERROR','ACTPlanCuentaDet.php','Ocurrieron los siguientes errores : ' . $mensaje_completo,
                $mensaje_completo,'control');

        } else if ($error == 'no') {
            $this->mensajeRes=new Mensaje();
            $this->mensajeRes->setMensaje('EXITO','ACTPlanCuentaDet.php','El archivo fue ejecutado con éxito',
                'El archivo fue ejecutado con éxito','control');
            $this->objParam->addParametro('estado','siguiente_estado');
            $this->objFunc=$this->create('MODPlanCuenta');
            $this->res=$this->objFunc->actualizarEstado($this->objParam);
        }

        //devolver respuesta
        $this->mensajeRes->imprimirRespuesta($this->mensajeRes->generarJson());
        //return $this->respuesta;
    }

    function revertirCargaArchivoExcel(){
        $this->objParam->addParametro('estado','anterior_estado');
        $this->objFunc=$this->create('MODPlanCuenta');
        $this->res=$this->objFunc->actualizarEstado($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }
			
}

?>