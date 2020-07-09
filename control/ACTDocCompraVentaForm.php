<?php
/**
*@package pXP
*@file gen-ACTDocCompraVenta.php
*@author  (admin)
*@date 18-08-2015 15:57:09
*@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
*/
require_once(dirname(__FILE__).'/../../pxp/pxpReport/DataSource.php');
require_once dirname(__FILE__).'/../../pxp/lib/lib_reporte/ReportePDFFormulario.php';
require_once(dirname(__FILE__).'/../reportes/RLcv.php');
require_once(dirname(__FILE__).'/../reportes/RLcvVentas.php');
require_once(dirname(__FILE__).'/../reportes/RLcvXls.php');
require_once(dirname(__FILE__).'/../reportes/RLibroComprasNCDPDF.php');
require_once(dirname(__FILE__).'/../reportes/RLibroComprasNCDXLS.php');

class ACTDocCompraVentaForm extends ACTbase{    
			
	
	
	
	function recuperarDatosLCV(){    	
		$this->objFunc = $this->create('MODDocCompraVenta');
		$cbteHeader = $this->objFunc->listarRepLCVForm($this->objParam);
		if($cbteHeader->getTipo() == 'EXITO'){				
			return $cbteHeader;
		}
        else{
		    $cbteHeader->imprimirRespuesta($cbteHeader->generarJson());
			exit;
		}              
		
    }
	
	function recuperarDatosErpEndensisLCV(){    	
		$this->objFunc = $this->create('MODDocCompraVenta');
		$cbteHeader = $this->objFunc->listarRepLCVFormErpEndesis($this->objParam);
		if($cbteHeader->getTipo() == 'EXITO'){				
			return $cbteHeader;
		}
        else{
		    $cbteHeader->imprimirRespuesta($cbteHeader->generarJson());
			exit;
		}              
		
    }
	
	function recuperarDatosEntidad(){    	
		$this->objFunc = $this->create('sis_parametros/MODEntidad');
		$cbteHeader = $this->objFunc->getEntidad($this->objParam);
		if($cbteHeader->getTipo() == 'EXITO'){				
			return $cbteHeader;
		}
        else{
		    $cbteHeader->imprimirRespuesta($cbteHeader->generarJson());
			exit;
		}              
		
    }
	
	function recuperarDatosPeriodo(){    	
		$this->objFunc = $this->create('sis_parametros/MODPeriodo');
		$cbteHeader = $this->objFunc->getPeriodoById($this->objParam);
		if($cbteHeader->getTipo() == 'EXITO'){				
			return $cbteHeader;
		}
        else{
		    $cbteHeader->imprimirRespuesta($cbteHeader->generarJson());
			exit;
		}              
		
    }


    function reporteLCV(){


        if($this->objParam->getParametro('formato_reporte')=='pdf'){

            if( 'lcncd' != $this->objParam->getParametro('tipo_lcv') ) {
                $nombreArchivo = uniqid(md5(session_id()).'Egresos') . '.pdf';
                if($this->objParam->getParametro('tipo_lcv')=='endesis_erp'){
                    $dataSource = $this->recuperarDatosErpEndensisLCV();
                }else{
                    $dataSource = $this->recuperarDatosLCV();
                }
                $dataEntidad = $this->recuperarDatosEntidad();
                $dataPeriodo = $this->recuperarDatosPeriodo();
            }else{

                if( 'lcncd' == $this->objParam->getParametro('tipo_lcv') ){
                    $nombreArchivo = uniqid(md5(session_id()).'Libro Compras Notas C-D') . '.pdf';
                }else{
                    $nombreArchivo = uniqid(md5(session_id()).'Libro Ventas Notas C-D') . '.pdf';
                }
                $this->objFunc = $this->create('MODDocCompraVenta');
                $this->res = $this->objFunc->reporteLibroCompraNCD($this->objParam);
                $this->datos = $this->res->getDatos();
            }

            //parametros basicos
            $tamano = 'LETTER';
            $orientacion = 'L';

            if( 'lcncd' != $this->objParam->getParametro('tipo_lcv') ) {
                $titulo = 'Consolidado';
            }else{
                $titulo = 'Libro Compras Notas C-D';
            }

            $this->objParam->addParametro('orientacion',$orientacion);
            $this->objParam->addParametro('tamano',$tamano);
            $this->objParam->addParametro('titulo_archivo',$titulo);
            $this->objParam->addParametro('nombre_archivo',$nombreArchivo);


            if( 'lcncd' != $this->objParam->getParametro('tipo_lcv') ) {
                //Instancia la clase de pdf
                if ($this->objParam->getParametro('tipo_lcv') == 'lcv_compras' || $this->objParam->getParametro('tipo_lcv') == 'endesis_erp') {
                    $reporte = new RLcv($this->objParam);
                } else {
                    $reporte = new RLcvVentas($this->objParam);
                }
                $reporte->datosHeader($dataSource->getDatos(), $dataSource->extraData, $dataEntidad->getDatos(), $dataPeriodo->getDatos());
            }else{
                $reporte = new RLibroComprasNCDPDF($this->objParam);
                $reporte->setDatos($this->datos);
            }


            $reporte->generarReporte();
            $reporte->output($reporte->url_archivo,'F');

            $this->mensajeExito=new Mensaje();
            $this->mensajeExito->setMensaje('EXITO','Reporte.php','Reporte generado','Se generó con éxito el reporte: '.$nombreArchivo,'control');
            $this->mensajeExito->setArchivoGenerado($nombreArchivo);
            $this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());
        }

        if($this->objParam->getParametro('formato_reporte') == 'xls'){
            if( 'lcncd' != $this->objParam->getParametro('tipo_lcv') ) {
                $this->objFun=$this->create('MODDocCompraVenta');

                if($this->objParam->getParametro('tipo_lcv')=='endesis_erp'){
                    $this->res = $this->objFun->listarRepLCVFormErpEndesis();
                }else{
                    $this->res = $this->objFun->listarRepLCVForm();
                }

                if($this->res->getTipo()=='ERROR'){
                    $this->res->imprimirRespuesta($this->res->generarJson());
                    exit;
                }
                //obtener titulo de reporte
                $titulo ='Lcv';
                //Genera el nombre del archivo (aleatorio + titulo)
                $nombreArchivo=uniqid(md5(session_id()).$titulo);
                $nombreArchivo.='.xls';

                $this->objParam->addParametro('nombre_archivo',$nombreArchivo);
                $this->objParam->addParametro('datos',$this->res->datos);
                //Instancia la clase de excel
                $this->objReporteFormato=new RLcvXls($this->objParam);
            }else{

                if( 'lcncd' == $this->objParam->getParametro('tipo_lcv') ){
                    $nombreArchivo = uniqid(md5(session_id()).'Libro Compras Notas C-D') . '.xls';
                }else{
                    $nombreArchivo = uniqid(md5(session_id()).'Libro Ventas Notas C-D') . '.xls';
                }
                $this->objFunc = $this->create('MODDocCompraVenta');
                $this->res = $this->objFunc->reporteLibroCompraNCD($this->objParam);
                $this->datos = $this->res->getDatos();

                $this->objParam->addParametro('nombre_archivo',$nombreArchivo);
                $this->objParam->addParametro('datos',$this->datos);

                //Instancia la clase de excel
                $this->objReporteFormato=new RLibroComprasNCDXLS($this->objParam);
            }

            $this->objReporteFormato->generarDatos();
            $this->objReporteFormato->generarReporte();

            $this->mensajeExito=new Mensaje();
            $this->mensajeExito->setMensaje('EXITO','Reporte.php','Reporte generado', 'Se generó con éxito el reporte: '.$nombreArchivo,'control');
            $this->mensajeExito->setArchivoGenerado($nombreArchivo);
            $this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());

        }
        if($this->objParam->getParametro('formato_reporte')!='pdf' && $this->objParam->getParametro('formato_reporte')!='xls'){
            if( 'lcncd' != $this->objParam->getParametro('tipo_lcv') ) {
                $this->exportarTxtLcvLCV();
            }else{
                $this->objFunc = $this->create('MODDocCompraVenta');
                $this->res = $this->objFunc->reporteLibroCompraNCD($this->objParam);
                $this->datos = $this->res->getDatos();

                $nombreArchivo = $this->crearArchivoTXT_CSV($this->datos, $this->objParam);

                $this->mensajeExito=new Mensaje();
                $this->mensajeExito->setMensaje('EXITO','Reporte.php','Se genero con exito el archivo LCV '.$nombreArchivo, 'Se genero con exito el archivo LCV '.$nombreArchivo,'control');
                $this->mensajeExito->setArchivoGenerado($nombreArchivo);

                $this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());
            }

        }
    }

    function crearArchivoTXT_CSV($res, $Obj) {
        $separador = '|';
        if($this->objParam->getParametro('formato_reporte') =='txt'){
            $separador = "|";
            $ext = '.txt';
        }else{
            $separador = ",";
            $ext = '.csv';
        }
        /*******************************
         *  FORMATEA NOMBRE DE ARCHIVO
         * compras_MMAAAA_NIT.txt
         * o
         * ventas_MMAAAA_NIT.txt
         * ********************************/
        $NIT = 	$res[0]['nit_empresa'];
        if( $this->objParam->getParametro('filtro_sql') == 'periodo' ){
            $sufijo = ($res[0]['periodo_num']<10?'0'.$res[0]['periodo_num']:$res[0]['periodo_num']).$res[0]['gestion'];
        }else{
            $sufijo=$this->objParam->getParametro('fecha_ini').'_'.$this->objParam->getParametro('fecha_fin');
        }

        if($this->objParam->getParametro('tipo_lcv')=='lcncd'){
            $nombre = 'compras_notas_'.$sufijo.'_'.$NIT;
        }else{
            $nombre = 'ventas__notas'.$sufijo.'_'.$NIT;
        }
        $nombre=str_replace("/", "", $nombre);

        $data = $res;
        $fileName = $nombre.$ext;
        //create file
        $file = fopen("../../../reportes_generados/$fileName","w+");
        $ctd = 1;
        /*if($this->objParam->getParametro('formato_reporte') !='txt'){
            //AÑADE EL BOMM PARA NO TENER PROBLEMAS AL LEER DE APLICACIONES EXTERNAS
            fwrite($file, pack("CCC",0xef,0xbb,0xbf));
        }*/
        /******************************
         *  IMPRIME CABECERA PARA CSV
         *****************************/
        /*if($this->objParam->getParametro('formato_reporte') !='txt'){

            if($this->objParam->getParametro('tipo_lcv')=='lcv_compras' || $this->objParam->getParametro('tipo_lcv')=='endesis_erp'){

                if($dataPeriodoArray['gestion']<2017) {
                    fwrite($file, "-" . $separador .
                        'N#' . $separador .
                        'FECHA DE LA FACTURA O DUI' . $separador .
                        'NIT PROVEEDOR' . $separador .
                        'NOMBRE O RAZON SOCIAL' . $separador .
                        'N# de LA FACTURA.' . $separador .
                        'N# de DUI' . $separador .
                        'N# de AUTORIZACION' . $separador .
                        "IMPORTE TOTAL DE LA COMPRA A" . $separador .
                        "IMPORTE NO SUJETO A CREDITO FISCAL B" . $separador .
                        "SUBTOTAL C = A - B" . $separador .
                        "DESCUENTOS BONOS Y REBAJAS  D" . $separador .
                        "IMPORTE SUJETO a CREDITO FISCAL E = C-D" . $separador .
                        "CREDITO FISCAL F = E*13%" . $separador .
                        'CODIGO DE CONTROL' . $separador .
                        'TIPO DE COMPRA' . "\r\n");
                }else{
                    fwrite($file, "-" . $separador .
                        'N#' . $separador .
                        'FECHA DE LA FACTURA O DUI' . $separador .
                        'NIT PROVEEDOR' . $separador .
                        'NOMBRE O RAZON SOCIAL' . $separador .
                        'N# de LA FACTURA.' . $separador .
                        'N# de DUI' . $separador .
                        'N# de AUTORIZACION' . $separador .
                        "IMPORTE TOTAL DE LA COMPRA A" . $separador .
                        "IMPORTE NO SUJETO A CREDITO FISCAL B" . $separador .
                        "SUBTOTAL C = A - B" . $separador .
                        "DESCUENTOS BONOS Y REBAJAS SUJETAS AL IVA D" . $separador .
                        "IMPORTE SUJETO a CREDITO FISCAL E = C-D" . $separador .
                        "CREDITO FISCAL F = E*13%" . $separador .
                        'CODIGO DE CONTROL' . $separador .
                        'TIPO DE COMPRA' . "\r\n");
                }

            }
            else{
                fwrite ($file,  "-".$separador.
                    'N#'.$separador.
                    'FECHA DE LA FACTURA'.$separador.
                    'N# de LA FACTURA'.$separador.
                    'N# de AUTORIZACION'.$separador.
                    'ESTADO'.$separador.
                    'NIT CLIENTE'.$separador.
                    'NOMBRE O RAZON SOCIAL'.$separador.
                    "IMPORTE TOTAL DE LA VENTA A".$separador.
                    "IMPORTE ICE/ IEHD/ TASAS B".$separador.
                    "EXPORTACIO. Y OPERACIONES EXENTAS C".$separador.
                    "VENTAS GRAVADAS TASA CERO D".$separador.
                    "SUBTOTAL E = A-B-C-D".$separador.
                    "DESCUENTOS BONOS Y REBAJAS OTORGADAS F".$separador.
                    "IMPORTE BASE DEBITO FISCAL G = E-F".$separador.
                    "DEBITO FISCAL H = G*13%".$separador.
                    'CODIGO DE CONTROL'."\r\n");
            }
        }*/
        /**************************
         *  IMPRIME CUERPO
         **************************/
        foreach ($data as $val) {

            if($this->objParam->getParametro('tipo_lcv')=='lcncd'){
                fwrite ($file,  "2".$separador.
                    $ctd.$separador.
                    date("d/m/Y", strtotime( $val['fecha_nota'])).$separador.
                    $val['num_nota'].$separador.
                    $val['num_autorizacion'].$separador.
                    $val['estado'].$separador.
                    $val['nit'].$separador.
                    $val['razon_social'].$separador.
                    number_format($val['total_devuelto'],2,'.', '') .$separador.
                    number_format($val['rc_iva'],2,'.', '') .$separador.
                    $val['codigo_control'].$separador.
                    date("d/m/Y", strtotime( $val['fecha_original'])).$separador.
                    $val['num_factura'].$separador.
                    $val['nroaut_anterior'].$separador.
                    number_format($val['importe_total'],2,'.', '')."\r\n");

            }
            /*else{
                fwrite ($file,  "3".$separador.
                    $ctd.$separador.
                    $newDate.$separador.
                    $val['nro_documento'].$separador.
                    $val['nro_autorizacion'].$separador.
                    $val['tipo_doc'].$separador.
                    $val['nit'].$separador.
                    $val['razon_social'].$separador.
                    $val['importe_doc'].$separador.
                    $val['importe_ice'].$separador.
                    $val['importe_excento'].$separador.
                    $val['venta_gravada_cero'].$separador.
                    $val['subtotal_venta'].$separador.
                    $val['importe_descuento'].$separador.
                    $val['sujeto_df'].$separador.
                    $val['importe_iva'].$separador.
                    $val['codigo_control']."\r\n");
            }*/
            $ctd = $ctd + 1;
        } //end for
        fclose($file);
        return $fileName;
    }

    function exportarTxtLcvLCV(){
		
		//crea el objetoFunProcesoMacro que contiene todos los metodos del sistema de workflow
		$this->objFun=$this->create('MODDocCompraVenta');		
		
		if($this->objParam->getParametro('tipo_lcv')=='endesis_erp'){
			$this->res = $this->objFun->listarRepLCVFormErpEndesis();
		}else{
			$this->res = $this->objFun->listarRepLCVForm();
		}
		
		if($this->res->getTipo()=='ERROR'){
			$this->res->imprimirRespuesta($this->res->generarJson());
			exit;
		}
		
		$nombreArchivo = $this->crearArchivoExportacion($this->res, $this->objParam);
		
		$this->mensajeExito=new Mensaje();
		$this->mensajeExito->setMensaje('EXITO','Reporte.php','Se genero con exito el archivo LCV'.$nombreArchivo,
										'Se genero con exito el archivo LCV'.$nombreArchivo,'control');
		$this->mensajeExito->setArchivoGenerado($nombreArchivo);
		
		$this->res->imprimirRespuesta($this->mensajeExito->generarJson());

	}
	
	function crearArchivoExportacion($res, $Obj) {
			
			
		
		
		$separador = '|';
		if($this->objParam->getParametro('formato_reporte') =='txt')
		{
			$separador = "|";
			$ext = '.txt';
		}
		else{
			$separador = ",";
			$ext = '.csv';
		}
		
		
		/*******************************
		 *  FORMATEA NOMBRE DE ARCHIVO
		 * compras_MMAAAA_NIT.txt     
		 * o    
		 * ventas_MMAAAA_NIT.txt
		 * 
		 * ********************************/
		 
		$dataEntidad = $this->recuperarDatosEntidad();
		$dataEntidadArray = $dataEntidad->getDatos();
		$NIT = 	$dataEntidadArray['nit'];
		 
		if($this->objParam->getParametro('filtro_sql')=='periodo'){
			$dataPeriodo = $this->recuperarDatosPeriodo();
			$dataPeriodoArray = $dataPeriodo->getDatos();
		    $sufijo = $dataPeriodoArray['periodo'].$dataPeriodoArray['gestion'];
		}
		else{
			$sufijo=$this->objParam->getParametro('fecha_ini').'_'.$this->objParam->getParametro('fecha_fin');
		}
		
		if($this->objParam->getParametro('tipo_lcv')=='lcv_compras' || $this->objParam->getParametro('tipo_lcv')=='endesis_erp'){			
			 $nombre = 'compras_'.$sufijo.'_'.$NIT;
		}
		else{
			 $nombre = 'ventas_'.$sufijo.'_'.$NIT;
		}
		
		$nombre=str_replace("/", "", $nombre);
		
		
		$data = $res -> getDatos();
		$fileName = $nombre.$ext;
		//create file
		$file = fopen("../../../reportes_generados/$fileName","w+");
		$ctd = 1;
		
		if($this->objParam->getParametro('formato_reporte') !='txt'){
			//AÑADE EL BOMM PARA NO TENER PROBLEMAS AL LEER DE APLICACIONES EXTERNAS
		    fwrite($file, pack("CCC",0xef,0xbb,0xbf));
		}
		
		
		
		
		/******************************
		 *  IMPRIME CABECERA PARA CSV
		 *****************************/
		if($this->objParam->getParametro('formato_reporte') !='txt')
		{
			
			if($this->objParam->getParametro('tipo_lcv')=='lcv_compras' || $this->objParam->getParametro('tipo_lcv')=='endesis_erp'){

					if($dataPeriodoArray['gestion']<2017) {
						fwrite($file, "-" . $separador .
								'N#' . $separador .
								'FECHA DE LA FACTURA O DUI' . $separador .
								'NIT PROVEEDOR' . $separador .
								'NOMBRE O RAZON SOCIAL' . $separador .
								'N# de LA FACTURA.' . $separador .
								'N# de DUI' . $separador .
								'N# de AUTORIZACION' . $separador .
								"IMPORTE TOTAL DE LA COMPRA A" . $separador .
								"IMPORTE NO SUJETO A CREDITO FISCAL B" . $separador .
								"SUBTOTAL C = A - B" . $separador .
								"DESCUENTOS BONOS Y REBAJAS  D" . $separador .
								"IMPORTE SUJETO a CREDITO FISCAL E = C-D" . $separador .
								"CREDITO FISCAL F = E*13%" . $separador .
								'CODIGO DE CONTROL' . $separador .
								'TIPO DE COMPRA' . "\r\n");
					}else{
						fwrite($file, "-" . $separador .
								'N#' . $separador .
								'FECHA DE LA FACTURA O DUI' . $separador .
								'NIT PROVEEDOR' . $separador .
								'NOMBRE O RAZON SOCIAL' . $separador .
								'N# de LA FACTURA.' . $separador .
								'N# de DUI' . $separador .
								'N# de AUTORIZACION' . $separador .
								"IMPORTE TOTAL DE LA COMPRA A" . $separador .
								"IMPORTE NO SUJETO A CREDITO FISCAL B" . $separador .
								"SUBTOTAL C = A - B" . $separador .
								"DESCUENTOS BONOS Y REBAJAS SUJETAS AL IVA D" . $separador .
								"IMPORTE SUJETO a CREDITO FISCAL E = C-D" . $separador .
								"CREDITO FISCAL F = E*13%" . $separador .
								'CODIGO DE CONTROL' . $separador .
								'TIPO DE COMPRA' . "\r\n");
					}
									
			 }
			 else{
				 	fwrite ($file,  "-".$separador.
				 	        'N#'.$separador.
	                        'FECHA DE LA FACTURA'.$separador.
	                        'N# de LA FACTURA'.$separador.
	                        'N# de AUTORIZACION'.$separador.        
							'ESTADO'.$separador.
	                        'NIT CLIENTE'.$separador.      
	                        'NOMBRE O RAZON SOCIAL'.$separador.      
	                        "IMPORTE TOTAL DE LA VENTA A".$separador.
	                        "IMPORTE ICE/ IEHD/ TASAS B".$separador.          
	                        "EXPORTACIO. Y OPERACIONES EXENTAS C".$separador.
	                        "VENTAS GRAVADAS TASA CERO D".$separador.    
	                        "SUBTOTAL E = A-B-C-D".$separador.  
	                        "DESCUENTOS BONOS Y REBAJAS OTORGADAS F".$separador.     
	                        "IMPORTE BASE DEBITO FISCAL G = E-F".$separador.   
	                        "DEBITO FISCAL H = G*13%".$separador.   
	                        'CODIGO DE CONTROL'."\r\n");
				 }
		}
		
		/**************************
		 *  IMPRIME CUERPO
		 **************************/
		
		foreach ($data as $val) {			
			
			 $newDate = date("d/m/Y", strtotime( $val['fecha']));			 
			 if($this->objParam->getParametro('tipo_lcv')=='lcv_compras' || $this->objParam->getParametro('tipo_lcv')=='endesis_erp'){
						
					
					fwrite ($file,  "1".$separador.
				 	                $ctd.$separador.
			                        $newDate.$separador.
			                        $val['nit'].$separador.
			                        $val['razon_social'].$separador. 
			                        $val['nro_documento'].$separador.
									$val['nro_dui'].$separador.
			                        $val['nro_autorizacion'].$separador.
			                        $val['importe_doc'].$separador.
			                        $val['total_excento'].$separador.
									$val['subtotal'].$separador.
									$val['importe_descuento'].$separador.
									$val['sujeto_cf'].$separador.
									$val['importe_iva'].$separador.
									$val['codigo_control'].$separador.
			                        $val['tipo_doc']."\r\n");
		              			
			 }
			 else{
				 	fwrite ($file,  "3".$separador.
				 	        $ctd.$separador.
	                        $newDate.$separador.
	                        $val['nro_documento'].$separador.
	                        $val['nro_autorizacion'].$separador.        
							$val['tipo_doc'].$separador.
	                        $val['nit'].$separador.      
	                        $val['razon_social'].$separador.	                            
	                        $val['importe_doc'].$separador.
	                        $val['importe_ice'].$separador.          
	                        $val['importe_excento'].$separador.
	                        $val['venta_gravada_cero'].$separador.    
	                        $val['subtotal_venta'].$separador.  
	                        $val['importe_descuento'].$separador.     
	                        $val['sujeto_df'].$separador.   
	                        $val['importe_iva'].$separador.   
	                        $val['codigo_control']."\r\n");		
	                        
	                  				
				
			 }
			 
			 	  		
			 $ctd = $ctd + 1;
         } //end for
         
     
				
		fclose($file);
		return $fileName;
	}
	
	
	
			
}

?>