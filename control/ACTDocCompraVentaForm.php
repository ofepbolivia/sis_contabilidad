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
require_once(dirname(__FILE__).'/../reportes/RLibroVentasNCDPDF.php');
require_once(dirname(__FILE__).'/../reportes/RLibroComprasNCDXLS.php');
require_once(dirname(__FILE__).'/../reportes/RLibroDeVentas.php');
require_once(dirname(__FILE__).'/../reportes/RLibroDeVentasXLS.php');
require_once(dirname(__FILE__).'/../reportes/RIngresosGravadosPDF.php');

require_once(dirname(__FILE__).'/../reportes/estandar/RLibroComprasSiatXLS.php');
require_once(dirname(__FILE__).'/../reportes/estandar/RLibroVentasSiatXLS.php');

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

            if( 'lcncd' != $this->objParam->getParametro('tipo_lcv') && 'lvncd' != $this->objParam->getParametro('tipo_lcv') && 'repo_ing_gravado' != $this->objParam->getParametro('tipo_lcv')) {
                $nombreArchivo = uniqid(md5(session_id()).'Egresos') . '.pdf';
                if($this->objParam->getParametro('tipo_lcv')=='endesis_erp'){
                    $dataSource = $this->recuperarDatosErpEndensisLCV();
                }else{
                    if ( 'lcv_ventas' == $this->objParam->getParametro('tipo_lcv')){
                        $this->objFunc = $this->create('MODDocCompraVenta');
                        $dataSource = $this->objFunc->listarRepLibroVentas($this->objParam);
                        //$this->datos = $this->res->getDatos();
						$nombreArchivo = uniqid('LibroVentasEstandar').'.pdf';
                    }else {

                        $dataSource = $this->recuperarDatosLCV();
                    }
                }
                $dataEntidad = $this->recuperarDatosEntidad();
                $dataPeriodo = $this->recuperarDatosPeriodo();
            }else{

                if( 'lcncd' == $this->objParam->getParametro('tipo_lcv') ){
                    $nombreArchivo = uniqid(md5(session_id()).'Libro Compras Notas C-D') . '.pdf';
					$this->objFunc = $this->create('MODDocCompraVenta');
					$this->res = $this->objFunc->reporteLibroCompraNCD($this->objParam);
                }else{

                	if ( $this->objParam->getParametro('tipo_lcv') != 'repo_ing_gravado' ) {
						$nombreArchivo = uniqid(md5(session_id()) . 'Libro Ventas Notas C-D') . '.pdf';
						$this->objFunc = $this->create('MODDocCompraVenta');
						$this->res = $this->objFunc->reporteLibroVentaNCD($this->objParam);
					}else{
						$nombreArchivo = uniqid('IngresosGravadosATT').'.pdf';
						$this->objFunc = $this->create('MODDocCompraVenta');
						$this->res = $this->objFunc->reporteIngresosGravados($this->objParam);
						$dataEntidad = $this->recuperarDatosEntidad();
						$dataPeriodo = $this->recuperarDatosPeriodo();
					}
                }

                $this->datos = $this->res->getDatos();
            }

            //parametros basicos
            $tamano = 'LETTER';
            $orientacion = 'L';

            if( 'lcncd' != $this->objParam->getParametro('tipo_lcv') && 'lvncd' != $this->objParam->getParametro('tipo_lcv') && 'repo_ing_gravado' != $this->objParam->getParametro('tipo_lcv')) {
				if('lcv_ventas' == $this->objParam->getParametro('tipo_lcv')){
					$titulo = 'LibroVentasEstandar';
				}else{
					$titulo = 'Consolidado';
					if ($this->objParam->getParametro('tipo_lcv') == 'lce_siat'){
						$titulo = 'LibroComprasEstandarSiat';
					}else if ($this->objParam->getParametro('tipo_lcv') == 'lve_siat'){
						$titulo = 'LibroVentasEstandarSiat';
					}
				}
            }else{
            	if ( 'lcncd' == $this->objParam->getParametro('tipo_lcv') ) {
					$titulo = 'Libro Compras Notas C-D';
				}else {
            		if ( $this->objParam->getParametro('tipo_lcv') != 'repo_ing_gravado' ) {
						$titulo = 'Libro Ventas Notas C-D';
					}else{
						$titulo = 'IngresosGravadosATT';
					}
				}
			}

            if ( $this->objParam->getParametro('tipo_lcv') == 'repo_ing_gravado' ) {
				$orientacion = 'P';
			}
            $this->objParam->addParametro('orientacion',$orientacion);
            $this->objParam->addParametro('tamano',$tamano);
            $this->objParam->addParametro('titulo_archivo',$titulo);
            $this->objParam->addParametro('nombre_archivo',$nombreArchivo);

			if ( ( 'lcv_ventas' == $this->objParam->getParametro('tipo_lcv') || 'repo_ing_gravado' == $this->objParam->getParametro('tipo_lcv') ) && true){

				$NEW_LINE = "\r\n";

				ignore_user_abort(true);

				header('Connection: close' . $NEW_LINE);
				header('Content-Encoding: none' . $NEW_LINE);
				ob_start();

				$this->mensajeExito=new Mensaje();
				$this->mensajeExito->setMensaje('EXITO','Reporte.php','Reporte generado '.$nombreArchivo,'Se generó con éxito el reporte: '.$nombreArchivo,'control');
				$this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());

				$size = ob_get_length();
				header('Content-Length: ' . $size, TRUE);
				ob_end_flush();
				ob_flush();
				flush();
				session_write_close();
				//fastcgi_finish_request();
				//set_time_limit(0);//avoid timeout
				//ini_set('memory_limit','-1'); //avoid insufficient memory


			}

            if( 'lcncd' != $this->objParam->getParametro('tipo_lcv') && 'lvncd' != $this->objParam->getParametro('tipo_lcv') && 'repo_ing_gravado' != $this->objParam->getParametro('tipo_lcv') ) {
                //Instancia la clase de pdf
                if ($this->objParam->getParametro('tipo_lcv') == 'lcv_compras' || $this->objParam->getParametro('tipo_lcv') == 'endesis_erp') {
                    $reporte = new RLcv($this->objParam);
                } else {
                    if ( 'lcv_ventas' == $this->objParam->getParametro('tipo_lcv')){
                        $reporte = new RLibroDeVentas($this->objParam);
                    }else {
                        $reporte = new RLcvVentas($this->objParam);
                    }
                }
                $reporte->datosHeader($dataSource->getDatos(), $dataSource->extraData, $dataEntidad->getDatos(), $dataPeriodo->getDatos());
            }else{
            	if ('lcncd' == $this->objParam->getParametro('tipo_lcv')) {
					$reporte = new RLibroComprasNCDPDF($this->objParam);
					$reporte->setDatos($this->datos);
				}else{
            		if ( $this->objParam->getParametro('tipo_lcv') != 'repo_ing_gravado' ) {
						$reporte = new RLibroVentasNCDPDF($this->objParam);
						$reporte->setDatos($this->datos);
					}else{
						$reporte = new RIngresosGravadosPDF($this->objParam);
						$reporte->datosHeader($this->datos, null, $dataEntidad->getDatos(), $dataPeriodo->getDatos());
						//$reporte->setDatos($this->datos);
					}
				}
            }

			$reporte->generarReporte();
            //var_dump('url', $reporte->url_archivo);exit;
			if ( ( 'lcv_ventas' == $this->objParam->getParametro('tipo_lcv') || 'repo_ing_gravado' == $this->objParam->getParametro('tipo_lcv') ) && true ){
				$url_absolute = $reporte->url_archivo;
				$reporte->output($reporte->url_archivo,'F');

				/** Convertir a megas **/
				$file_size = filesize($url_absolute);
				$units = array('B', 'KB', 'MB', 'GB', 'TB');

				$bytes = max($file_size, 0);
				$pow = floor(($bytes ? log($bytes) : 0) / log(1024));
				$pow = min($pow, count($units) - 1);

				// $bytes /= pow(1024, $pow);
				// $bytes /= (1 << (10 * $pow));

				$file_size = round($bytes, 2) . ' ' . $units[$pow];
				/** Convertir a megas **/

				$url_absolute = './../../../reportes_generados/'.$nombreArchivo;

				$cone = new conexion();
				$link = $cone->conectarpdo();

				$sql = "UPDATE  conta.tdocumento_generado SET
                      estado_reg = 'OLD'
                    WHERE format = 'pdf' and estado_reg!= 'inactivo'" ;

				$stmt = $link->prepare($sql);
				$stmt->execute();

				$id_gestion = $this->objParam->getParametro('id_gestion');
				$id_periodo = $this->objParam->getParametro('id_periodo');

				if ( $id_gestion != '' && $id_periodo != '' ){

					$sql = "select tper.periodo, tges.gestion
                	from param.tperiodo tper
                	inner join param.tgestion tges on tges.id_gestion = tper.id_gestion
               	 	where  tper.id_periodo = ". $id_periodo." and tper.id_gestion = ".$id_gestion;

					$registros = $link->prepare( $sql );
					$registros->execute();
					$registros = $registros->fetchAll( PDO::FETCH_OBJ );
					$periodo = $registros[0]->periodo;
					$gestion = $registros[0]->gestion;

					$fecha_ini = date('d/m/Y', mktime(0,0,0, $periodo, 1, $gestion));
					$dia = date("d", mktime(0,0,0, $periodo+1, 0, $gestion));
					$fecha_fin = date('d/m/Y', mktime(0,0,0, $periodo, $dia, $gestion));

				}else{
					$fecha_ini = $this->objParam->getParametro('fecha_ini');
					$fecha_fin = $this->objParam->getParametro('fecha_fin');
				}

				$sql = "INSERT INTO conta.tdocumento_generado(id_usuario_reg, url, size, fecha_generacion, file_name, format, estado_reg, fecha_ini, fecha_fin) VALUES (".$_SESSION["ss_id_usuario"]."::integer, '".$url_absolute."', '".$file_size."', now(), '".$nombreArchivo."', 'pdf', 'NEW', '".$fecha_ini."'::date, '".$fecha_fin."'::date) ";

				$stmt = $link->prepare($sql);
				$stmt->execute();

				/**enviar alert al usuario para indicar que el reporte ha sido generado**/
				$evento = "enviarMensajeUsuario";

				//mandamos datos al websocket
				$data = array(
					"mensaje" => 'Estimado Funcionario, su Reporte ya ha sido generado: '.$nombreArchivo,
					"tipo_mensaje" => 'alert',
					"titulo" => 'Alerta Reporte',
					"id_usuario" => $_SESSION["ss_id_usuario"],
					"destino" => 'Unico',
					"evento" => $evento,
					"url" => 'url_prueba'
				);

				$send = array(
					"tipo" => "enviarMensajeUsuario",
					"data" => $data
				);

				$usuarios_socket = $this->dispararEventoWS($send);

				$usuarios_socket =json_decode($usuarios_socket, true);
				/**enviar alert al usuario para indicar que el reporte ha sido generado**/
				//chmod($url_move, 0777);
				//copy ( $url_move, '/var/www/html/kerp/uploaded_files/sis_workflow/DocumentoWf/');
				//copy ( $url_move, '/var/www/html/kerp/uploaded_files/sis_contabilidad/');
			}else{
				$reporte->output($reporte->url_archivo,'F');
			}

            $this->mensajeExito=new Mensaje();
            $this->mensajeExito->setMensaje('EXITO','Reporte.php','Reporte generado','Se generó con éxito el reporte: '.$nombreArchivo,'control');
            $this->mensajeExito->setArchivoGenerado($nombreArchivo);
            $this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());
        }

        if($this->objParam->getParametro('formato_reporte') == 'xls'){

			$array_tipo = array('lce_siat','lve_siat','lc_on_siat','lv_on_siat','lc_es_on_siat','lv_es_on_siat');
        	if( !in_array( $this->objParam->getParametro('tipo_lcv'), $array_tipo) ) {

				if ('lcncd' != $this->objParam->getParametro('tipo_lcv')) {
					$this->objFun = $this->create('MODDocCompraVenta');

					if ($this->objParam->getParametro('tipo_lcv') == 'endesis_erp') {
						$this->res = $this->objFun->listarRepLCVFormErpEndesis();
					} else {
						if ('lcv_ventas' == $this->objParam->getParametro('tipo_lcv')) {
							$this->objFunc = $this->create('MODDocCompraVenta');
							$this->res = $this->objFunc->listarRepLibroVentas($this->objParam);
						} else {
							$this->res = $this->objFun->listarRepLCVForm();
						}
					}

					if ($this->res->getTipo() == 'ERROR') {
						$this->res->imprimirRespuesta($this->res->generarJson());
						exit;
					}

					//obtener titulo de reporte
					if ('lcv_ventas' == $this->objParam->getParametro('tipo_lcv')) {
						$titulo = 'LibroVentasEstandar';
						$nombreArchivo = uniqid('LibroVentasEstandar') . '.xls';
					} else {
						$titulo = 'Lcv';
						$nombreArchivo = uniqid('LibroComprasEstandar') . '.xls';
					}
					//Genera el nombre del archivo (aleatorio + titulo)
					//$nombreArchivo=uniqid(md5(session_id()).$titulo);

					$this->objParam->addParametro('nombre_archivo', $nombreArchivo);
					$this->objParam->addParametro('datos', $this->res->datos);

					if ('lcv_ventas' == $this->objParam->getParametro('tipo_lcv') && true) {

						$NEW_LINE = "\r\n";

						ignore_user_abort(true);

						header('Connection: close' . $NEW_LINE);
						header('Content-Encoding: none' . $NEW_LINE);
						ob_start();

						$this->mensajeExito = new Mensaje();
						$this->mensajeExito->setMensaje('EXITO', 'Reporte.php', 'Reporte generado ' . $nombreArchivo, 'Se generó con éxito el reporte: ' . $nombreArchivo, 'control');
						$this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());

						$size = ob_get_length();
						header('Content-Length: ' . $size, TRUE);
						ob_end_flush();
						ob_flush();
						flush();
						session_write_close();

					}

					//Instancia la clase de excel
					if ('lcv_ventas' == $this->objParam->getParametro('tipo_lcv')) {
						$this->objReporteFormato = new RLibroDeVentasXLS($this->objParam);
					} else {
						$this->objReporteFormato = new RLcvXls($this->objParam);
					}
				} else {

					if ('lcncd' == $this->objParam->getParametro('tipo_lcv')) {
						$nombreArchivo = uniqid(md5(session_id()) . 'Libro Compras Notas C-D') . '.xls';
					} else {
						$nombreArchivo = uniqid(md5(session_id()) . 'Libro Ventas Notas C-D') . '.xls';
					}
					$this->objFunc = $this->create('MODDocCompraVenta');
					$this->res = $this->objFunc->reporteLibroCompraNCD($this->objParam);
					$this->datos = $this->res->getDatos();

					$this->objParam->addParametro('nombre_archivo', $nombreArchivo);
					$this->objParam->addParametro('datos', $this->datos);

					//Instancia la clase de excel
					$this->objReporteFormato = new RLibroComprasNCDXLS($this->objParam);
				}

				$this->objReporteFormato->generarDatos();

				$url_file_xls = $this->objReporteFormato->generarReporte(); //var_dump('$url_file',$url_file);exit;

				if ('lcv_ventas' == $this->objParam->getParametro('tipo_lcv') && true) {


					/** Convertir a megas **/
					$file_size = filesize($url_file_xls);
					$units = array('B', 'KB', 'MB', 'GB', 'TB');

					$bytes = max($file_size, 0);
					$pow = floor(($bytes ? log($bytes) : 0) / log(1024));
					$pow = min($pow, count($units) - 1);


					$file_size = round($bytes, 2) . ' ' . $units[$pow];
					/** Convertir a megas **/

					//$url_absolute = './../../../reportes_generados/'.$nombreArchivo;
					$url_absolute = $url_file_xls;

					$cone = new conexion();
					$link = $cone->conectarpdo();

					$sql = "UPDATE  conta.tdocumento_generado SET
                      estado_reg = 'OLD'
                    WHERE format = 'xls' and estado_reg != 'inactivo'";

					$stmt = $link->prepare($sql);
					$stmt->execute();

					$id_gestion = $this->objParam->getParametro('id_gestion');
					$id_periodo = $this->objParam->getParametro('id_periodo');

					if ($id_gestion != '' && $id_periodo != '') {

						$sql = "select tper.periodo, tges.gestion
                	from param.tperiodo tper
                	inner join param.tgestion tges on tges.id_gestion = tper.id_gestion
               	 	where  tper.id_periodo = " . $id_periodo . " and tper.id_gestion = " . $id_gestion;

						$registros = $link->prepare($sql);
						$registros->execute();
						$registros = $registros->fetchAll(PDO::FETCH_OBJ);
						$periodo = $registros[0]->periodo;
						$gestion = $registros[0]->gestion;

						$fecha_ini = date('d/m/Y', mktime(0, 0, 0, $periodo, 1, $gestion));
						$dia = date("d", mktime(0, 0, 0, $periodo + 1, 0, $gestion));
						$fecha_fin = date('d/m/Y', mktime(0, 0, 0, $periodo, $dia, $gestion));

					} else {
						$fecha_ini = $this->objParam->getParametro('fecha_ini');
						$fecha_fin = $this->objParam->getParametro('fecha_fin');
					}

					$sql = "INSERT INTO conta.tdocumento_generado(id_usuario_reg, url, size, fecha_generacion, file_name, format, estado_reg, fecha_ini, fecha_fin) VALUES (" . $_SESSION["ss_id_usuario"] . "::integer, '" . $url_absolute . "', '" . $file_size . "', now(), '" . $nombreArchivo . "', 'xls', 'NEW', '" . $fecha_ini . "'::date, '" . $fecha_fin . "'::date) ";

					$stmt = $link->prepare($sql);
					$stmt->execute();

					/**enviar alert al usuario para indicar que el reporte ha sido generado**/
					$evento = "enviarMensajeUsuario";

					//mandamos datos al websocket
					$data = array(
						"mensaje" => 'Estimado Funcionario, su Reporte ya ha sido generado: ' . $nombreArchivo,
						"tipo_mensaje" => 'alert',
						"titulo" => 'Alerta Reporte',
						"id_usuario" => $_SESSION["ss_id_usuario"],
						"destino" => 'Unico',
						"evento" => $evento,
						"url" => 'url_prueba'
					);

					$send = array(
						"tipo" => "enviarMensajeUsuario",
						"data" => $data
					);

					$usuarios_socket = $this->dispararEventoWS($send);

					$usuarios_socket = json_decode($usuarios_socket, true);
					/**enviar alert al usuario para indicar que el reporte ha sido generado**/
					//chmod($url_move, 0777);
					//copy ( $url_move, '/var/www/html/kerp/uploaded_files/sis_workflow/DocumentoWf/');
					//copy ( $url_move, '/var/www/html/kerp/uploaded_files/sis_contabilidad/');
				}

				$this->mensajeExito = new Mensaje();
				$this->mensajeExito->setMensaje('EXITO', 'Reporte.php', 'Reporte generado', 'Se generó con éxito el reporte: ' . $nombreArchivo, 'control');
				$this->mensajeExito->setArchivoGenerado($nombreArchivo);
				$this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());

			}else{
				$tipo_libro = $this->objParam->getParametro('tipo_lcv');
				if ( $tipo_libro == 'lce_siat' || $tipo_libro == 'lc_on_siat' || $tipo_libro == 'lc_es_on_siat' ) {
					$nombreArchivo = uniqid('RegistroComprasEstandarSiat') . '.xls';

					$dataSource = $this->recuperarDatosLCV();
					$this->objParam->addParametro('nombre_archivo', $nombreArchivo);
					$this->objParam->addParametro('datos', $dataSource->getDatos());

					$dataEntidad = $this->recuperarDatosEntidad();
					$dataPeriodo = $this->recuperarDatosPeriodo();
					$this->objParam->addParametro('dataEntidad', $dataEntidad->getDatos());
					$this->objParam->addParametro('dataPeriodo', $dataPeriodo->getDatos());

					$this->objReporteFormato = new RLibroComprasSiatXLS($this->objParam);

					$this->objReporteFormato->imprimeDatos();
					$url_file_xls = $this->objReporteFormato->generarReporte();

				} else if ( $tipo_libro == 'lve_siat' || $tipo_libro == 'lv_on_siat' || $tipo_libro == 'lv_es_on_siat' ) {
					$nombreArchivo = uniqid('RegistroVentasEstandarSiat') . '.xls';

					$this->objFunc = $this->create('MODDocCompraVenta');
					$dataSource = $this->objFunc->listarRepLibroVentas($this->objParam);

					$this->objParam->addParametro('nombre_archivo', $nombreArchivo);
					$this->objParam->addParametro('datos', $dataSource->getDatos());

					$dataEntidad = $this->recuperarDatosEntidad();
					$dataPeriodo = $this->recuperarDatosPeriodo();
					$this->objParam->addParametro('dataEntidad', $dataEntidad->getDatos());
					$this->objParam->addParametro('dataPeriodo', $dataPeriodo->getDatos());

					$this->objReporteFormato = new RLibroVentasSiatXLS($this->objParam);

					$this->objReporteFormato->imprimeDatos();
					$url_file_xls = $this->objReporteFormato->generarReporte();
				}



				$this->mensajeExito = new Mensaje();
				$this->mensajeExito->setMensaje('EXITO', 'Reporte.php', 'Reporte generado', 'Se generó con éxito el reporte: ' . $nombreArchivo, 'control');
				$this->mensajeExito->setArchivoGenerado($nombreArchivo);
				$this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());
			}
        }
        if($this->objParam->getParametro('formato_reporte')!='pdf' && $this->objParam->getParametro('formato_reporte')!='xls'){

			if( $this->objParam->getParametro('tipo_lcv') != 'lce_siat' && $this->objParam->getParametro('tipo_lcv') != 'lve_siat' ) {
				if ('lcncd' != $this->objParam->getParametro('tipo_lcv')) {

					if ('lcv_ventas' == $this->objParam->getParametro('tipo_lcv')) {

						$this->objFunc = $this->create('MODDocCompraVenta');
						$this->res = $this->objFunc->listarRepLibroVentas($this->objParam);

						if ($this->res->getTipo() == 'ERROR') {
							$this->res->imprimirRespuesta($this->res->generarJson());
							exit;
						}

						$nombreArchivo = $this->crearArchivoExportacionLibroVenta($this->res, $this->objParam);

						$this->mensajeExito = new Mensaje();
						$this->mensajeExito->setMensaje('EXITO', 'Reporte.php', 'Se genero con exito el archivo Libro de Ventas' . $nombreArchivo,
							'Se genero con exito el archivo Libro de Ventas' . $nombreArchivo, 'control');
						$this->mensajeExito->setArchivoGenerado($nombreArchivo);

						$this->res->imprimirRespuesta($this->mensajeExito->generarJson());

					} else if ('lvncd' == $this->objParam->getParametro('tipo_lcv')) {


						$this->objFunc = $this->create('MODDocCompraVenta');
						$this->res = $this->objFunc->reporteLibroVentaNCD($this->objParam);
						$this->datos = $this->res->getDatos();

						$nombreArchivo = $this->crearArchivoTXT_CSV($this->datos, $this->objParam);

						$this->mensajeExito = new Mensaje();
						$this->mensajeExito->setMensaje('EXITO', 'Reporte.php', 'Se genero con exito el archivo LV ' . $nombreArchivo, 'Se genero con exito el archivo Libro Ventas ' . $nombreArchivo, 'control');
						$this->mensajeExito->setArchivoGenerado($nombreArchivo);
						$this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());
					} /**breydi.vasquez*/
					else if ('repo_iata' == $this->objParam->getParametro('tipo_lcv')) {
						$this->reporteIata();
					} /***/
					else {
						$this->exportarTxtLcvLCV();
					}
				} else {
					$this->objFunc = $this->create('MODDocCompraVenta');
					$this->res = $this->objFunc->reporteLibroCompraNCD($this->objParam);
					$this->datos = $this->res->getDatos();

					$nombreArchivo = $this->crearArchivoTXT_CSV($this->datos, $this->objParam);

					$this->mensajeExito = new Mensaje();
					$this->mensajeExito->setMensaje('EXITO', 'Reporte.php', 'Se genero con exito el archivo LCV ' . $nombreArchivo, 'Se genero con exito el archivo LCV ' . $nombreArchivo, 'control');
					$this->mensajeExito->setArchivoGenerado($nombreArchivo);

					$this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());
				}
			}else{

				if ($this->objParam->getParametro('tipo_lcv') == 'lce_siat') {
					$this->res = $this->recuperarDatosLCV();
					$nombreArchivo = $this->crearLibroVentaSiatTXT($this->res, $this->objParam);
				} else if ($this->objParam->getParametro('tipo_lcv') == 'lve_siat') {
					$this->objFunc = $this->create('MODDocCompraVenta');
					$this->res = $this->objFunc->listarRepLibroVentas($this->objParam);
					$nombreArchivo = $this->crearLibroVentaSiatTXT($this->res, $this->objParam);
				}



				$this->mensajeExito = new Mensaje();
				$this->mensajeExito->setMensaje('EXITO', 'Reporte.php', 'Se genero con exito el archivo Libro de Ventas' . $nombreArchivo,
					'Se genero con exito el archivo Libro de Ventas' . $nombreArchivo, 'control');
				$this->mensajeExito->setArchivoGenerado($nombreArchivo);

				$this->res->imprimirRespuesta($this->mensajeExito->generarJson());
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
			$fecha_original = $val['fecha_original']==null ? '0': date("d/m/Y", strtotime( $val['fecha_original']));
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
					$fecha_original.$separador.
                    $val['num_factura'].$separador.
                    $val['nroaut_anterior'].$separador.
                    number_format($val['importe_total'],2,'.', '')."\r\n");

            } else{
            	if ($val['codigo_control']== null || $val['codigo_control'] == '') {
            		$codigo_control = '0';
				}else{
					$codigo_control = $val['codigo_control'];
				}
                fwrite ($file,  "7".$separador.
                    $ctd.$separador.
					date("d/m/Y", strtotime( $val['fecha_nota'])).$separador.
                    $val['num_nota'].$separador.
                    $val['num_autorizacion'].$separador.
                    //$val['tipo_doc'].$separador.
                    $val['nit'].$separador.
                    $val['razon_social'].$separador.
					number_format($val['total_devuelto'],2,'.', '').$separador.
					number_format($val['rc_iva'],2,'.', '').$separador.
					$codigo_control.$separador.
					date("d/m/Y", strtotime( $val['fecha_original'])).$separador.
                    $val['num_factura'].$separador.
					$val['num_autorizacion_original'].$separador.
					number_format($val['monto_total_fac'],2,'.', '')."\r\n");
            }
            $ctd = $ctd + 1;
        } //end for
        fclose($file);
        return $fileName;
    }

    function exportarTxtLcvLCV(){
		//crea el objetoFunProcesoMacro que contiene todos los metodos del sistema de workflow
		$this->objFun=$this->create('MODDocCompraVenta');

		if($this->objParam->getParametro('tipo_lcv')=='endesis_erp'){
			$this->objFun=$this->create('MODDocCompraVenta');
			$this->res = $this->objFun->listarRepLCVFormErpEndesis();
		}else{
			if ($this->objParam->getParametro('tipo_lcv') == 'lvncd'){
				$this->objFun = $this->create('MODDocCompraVenta');
				$this->res    = $this->objFun->reporteLibroVentaNCD($this->objParam);
				//var_dump('$this->res', $this->res );exit;
			}else {
				$this->objFun=$this->create('MODDocCompraVenta');
				$this->res = $this->objFun->listarRepLCVForm();
			}
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
		if($this->objParam->getParametro('formato_reporte') == 'txt')
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
		//var_dump($this->objParam->getParametro('tipo_lcv'), $this->objParam->getParametro('formato_reporte'), $data);exit;
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

			 } else{
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

	function tabulacion($cantidad){
	    $tabulado = "";
	    for($i = 1; $i <= $cantidad; $i++){
	        $tabulado .= "\t";
        }

	    return $tabulado;
    }

	function crearArchivoExportacionLibroVenta($res, $Obj) {

		$separador = '|';
		if($this->objParam->getParametro('formato_reporte') =='txt'){
			$separador = "|";
			$ext = '.txt';
		}else{
			$separador = "|";
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
//var_dump('$dataEntidad', $dataEntidadArray);exit;
		if($this->objParam->getParametro('filtro_sql')=='periodo'){
			$dataPeriodo = $this->recuperarDatosPeriodo();
			$dataPeriodoArray = $dataPeriodo->getDatos();
		    $sufijo = $dataPeriodoArray['periodo'].$dataPeriodoArray['gestion'];
		}
		else{
			$sufijo=$this->objParam->getParametro('fecha_ini').'_'.$this->objParam->getParametro('fecha_fin');
		}



		$nombre = 'ventas_'.$sufijo.'_'.$NIT;


		$nombre=str_replace("/", "", $nombre);

        //var_dump('$sufijo', $sufijo, $NIT, $nombre);exit;

		$data = $res -> getDatos(); //var_dump('$data', $data);exit;

        $lista= [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,60];

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
		if($this->objParam->getParametro('formato_reporte') !='txt') {


            fwrite ($file,  "-".$separador.
                    'N#'.$separador.
                    'FECHA DE LA FACTURA'.$separador.
                    'N° DE LA FACTURA'.$separador.
                    'N° DE AUTORIZACION'.$separador.
                    'ESTADO'.$separador.
                    'NIT/CI CLIENTE'.$separador.
                    'NOMBRE O RAZON SOCIAL'.$separador.
                    "IMPORTE TOTAL DE LA VENTA A".$separador.
                    "IMPORTE ICE/ IEHD/ TASAS B".$separador.
                    "EXPORTACION Y OPERACIONES EXENTAS C".$separador.
                    "VENTAS GRAVADAS TASA CERO D".$separador.
                    "SUBTOTAL E = A-B-C-D".$separador.
                    "DESCUENTOS BONOS Y REBAJAS SUJETAS AL IVA F".$separador.
                    "IMPORTE BASE DEBITO FISCAL G = E-F".$separador.
                    "DEBITO FISCAL H = G*13%".$separador.
                    'CODIGO DE CONTROL'."\r\n");

		}

        /*if($this->objParam->getParametro('formato_reporte') == 'txt') {


            fwrite ($file,  "BOLIVIANA DE AVIACION (BoA)".$this->tabulacion(5).
                'LIBRO DE VENTAS'.$this->tabulacion(5).
                'Pagina: '."\r\n");

            fwrite ($file,  "                           ".$this->tabulacion(5).
                "   ESTANDAR"."\r\n");

            fwrite ($file,  "------------------------------------------------------------------------------------------------------------------------------------"."\r\n");

            fwrite ($file,  "              Periodo: ".$this->tabulacion(2).$dataPeriodoArray['gestion']."\t".$dataPeriodoArray['periodo']."\r\n");

            fwrite ($file,  "Nombre o Razón Social: \t".$dataEntidadArray['nombre'].$this->tabulacion(1)."NIT: ".$dataEntidadArray['nit'].$this->tabulacion(2)."EXPRESADO EN BOLIVIANOS"."\r\n");

            fwrite ($file,  "------------------------------------------------------------------------------------------------------------------------------------"."\r\n");

            //fwrite($file, html_entity_decode())

            fwrite ($file,  "Nro. ".$this->tabulacion(0).
                "FECHA DE LA ".$this->tabulacion(0).
                "Nro. DE LA ".$this->tabulacion(0).
                "Nro. DE ".$this->tabulacion(0).
                "ESTADO ".$this->tabulacion(0).
                "NOMBRE O RAZON SOCIAL ".$this->tabulacion(0).
                "IMPORTE TOTAL ".$this->tabulacion(0).
                "IMPORTE ICE/IEHD ".$this->tabulacion(0).
                "EXPORT. ".$this->tabulacion(0).
                "V-GRAVADAS ".$this->tabulacion(0).
                "\r\n");
        }*/

        $row_counter = 1;

		/**************************
		 *  IMPRIME CUERPO
		 **************************/

		foreach ($data as $val) {

		    $subtotal = round($val['importe_total_venta'] - $val['importe_otros_no_suj_iva'] - $val['exportacion_excentas'] - $val['ventas_tasa_cero'], 2);
		    $importe_debito = round($subtotal - $val['descuento_rebaja_suj_iva'], 2);
		    $debito_fiscal = round($importe_debito * 0.13,2);
		    $newDate = date("d/m/Y", strtotime( $val['fecha_factura']));
			$codigo_control = $val['codigo_control']==null || $val['codigo_control']==NULL || $val['codigo_control'] == ''?'0':$val['codigo_control'];
		    /*if ($this->objParam->getParametro('formato_reporte') == 'txt' && $row_counter <= 34){
                fwrite ($file,  $ctd.
                    "    ".$newDate." ".
                    " ".$val['nro_factura']." ".
                    $val['nro_autorizacion']." ".
                    $val['estado']." ".
                    $val['razon_social_cli']." ".
                    $val['importe_total_venta']." ".
                    $val['importe_otros_no_suj_iva']." ".
                    $val['exportacion_excentas']." ".
                    $val['ventas_tasa_cero'].
                    "\r\n");
            }else {*/
                fwrite($file, "3" . $separador .
                    $ctd . $separador .
                    $newDate . $separador .
                    $val['nro_factura'] . $separador .
                    $val['nro_autorizacion'] . $separador .
                    $val['estado'] . $separador .
                    $val['nit_ci_cli'] . $separador .
                    $val['razon_social_cli'] . $separador .

                    $val['importe_total_venta'] . $separador .
                    $val['importe_otros_no_suj_iva'] . $separador .
                    $val['exportacion_excentas'] . $separador .
                    $val['ventas_tasa_cero'] . $separador .

                    $subtotal . $separador .
                    $val['descuento_rebaja_suj_iva'] . $separador .
                    $importe_debito . $separador .
                    $debito_fiscal . $separador .
					$codigo_control . "\r\n");
            //}
		    /*if(in_array($row_counter/34,$lista)){
                fwrite ($file,  "------------------------------------------------------------------------------------------------------------------------------------"."\r\n\n\n\n\n");
                $row_counter = 38;
            }

            if(in_array($row_counter/38,$lista)){

                fwrite ($file,  "BOLIVIANA DE AVIACION (BoA)".$this->tabulacion(5).
                    'LIBRO DE VENTAS'.$this->tabulacion(5).
                    'Pagina: '."\r\n");

                fwrite ($file,  "                           ".$this->tabulacion(5).
                    "   ESTANDAR"."\r\n");

                fwrite ($file,  "------------------------------------------------------------------------------------------------------------------------------------"."\r\n");

                fwrite ($file,  "              Periodo: ".$this->tabulacion(2).$dataPeriodoArray['gestion']."\t".$dataPeriodoArray['periodo']."\r\n");

                fwrite ($file,  "Nombre o Razón Social: \t".$dataEntidadArray['nombre'].$this->tabulacion(1)."NIT: ".$dataEntidadArray['nit'].$this->tabulacion(2)."EXPRESADO EN BOLIVIANOS"."\r\n");

                fwrite ($file,  "------------------------------------------------------------------------------------------------------------------------------------"."\r\n");

                //fwrite($file, html_entity_decode())

                fwrite ($file,  "Nro. ".$this->tabulacion(0).
                    "FECHA DE LA ".$this->tabulacion(0).
                    "Nro. DE LA ".$this->tabulacion(0).
                    "Nro. DE ".$this->tabulacion(0).
                    "ESTADO ".$this->tabulacion(0).
                    "NOMBRE O RAZON SOCIAL ".$this->tabulacion(0).
                    "IMPORTE TOTAL ".$this->tabulacion(0).
                    "IMPORTE ICE/IEHD ".$this->tabulacion(0).
                    "EXPORT. ".$this->tabulacion(0).
                    "V-GRAVADAS ".$this->tabulacion(0).
                    "\r\n");

                $row_counter = 1;

            }*/
			 $ctd = $ctd + 1;
            $row_counter ++;
         } //end for



		fclose($file);
		return $fileName;
	}

	function crearLibroVentaSiatTXT($res, $Obj){

		$separador = '|';
		if($this->objParam->getParametro('formato_reporte') =='txt'){
			$separador = "|";
			$ext = '.txt';
		}else{
			$separador = "|";
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



		$nombre = 'ventas_'.$sufijo.'_'.$NIT;


		$nombre=str_replace("/", "", $nombre);

		//var_dump('$sufijo', $sufijo, $NIT, $nombre);exit;

		$data = $res -> getDatos(); //var_dump('$data', $data);exit;

		$lista= [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,60];

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
		if($this->objParam->getParametro('formato_reporte') !='txt') {


			fwrite ($file,  "-".$separador.
				'N#'.$separador.
				'FECHA DE LA FACTURA'.$separador.
				'N° DE LA FACTURA'.$separador.
				'N° DE AUTORIZACION'.$separador.
				'ESTADO'.$separador.
				'NIT/CI CLIENTE'.$separador.
				'NOMBRE O RAZON SOCIAL'.$separador.
				"IMPORTE TOTAL DE LA VENTA A".$separador.
				"IMPORTE ICE/ IEHD/ TASAS B".$separador.
				"EXPORTACION Y OPERACIONES EXENTAS C".$separador.
				"VENTAS GRAVADAS TASA CERO D".$separador.
				"SUBTOTAL E = A-B-C-D".$separador.
				"DESCUENTOS BONOS Y REBAJAS SUJETAS AL IVA F".$separador.
				"IMPORTE BASE DEBITO FISCAL G = E-F".$separador.
				"DEBITO FISCAL H = G*13%".$separador.
				'CODIGO DE CONTROL'."\r\n");

		}

		/*if($this->objParam->getParametro('formato_reporte') == 'txt') {


            fwrite ($file,  "BOLIVIANA DE AVIACION (BoA)".$this->tabulacion(5).
                'LIBRO DE VENTAS'.$this->tabulacion(5).
                'Pagina: '."\r\n");

            fwrite ($file,  "                           ".$this->tabulacion(5).
                "   ESTANDAR"."\r\n");

            fwrite ($file,  "------------------------------------------------------------------------------------------------------------------------------------"."\r\n");

            fwrite ($file,  "              Periodo: ".$this->tabulacion(2).$dataPeriodoArray['gestion']."\t".$dataPeriodoArray['periodo']."\r\n");

            fwrite ($file,  "Nombre o Razón Social: \t".$dataEntidadArray['nombre'].$this->tabulacion(1)."NIT: ".$dataEntidadArray['nit'].$this->tabulacion(2)."EXPRESADO EN BOLIVIANOS"."\r\n");

            fwrite ($file,  "------------------------------------------------------------------------------------------------------------------------------------"."\r\n");

            //fwrite($file, html_entity_decode())

            fwrite ($file,  "Nro. ".$this->tabulacion(0).
                "FECHA DE LA ".$this->tabulacion(0).
                "Nro. DE LA ".$this->tabulacion(0).
                "Nro. DE ".$this->tabulacion(0).
                "ESTADO ".$this->tabulacion(0).
                "NOMBRE O RAZON SOCIAL ".$this->tabulacion(0).
                "IMPORTE TOTAL ".$this->tabulacion(0).
                "IMPORTE ICE/IEHD ".$this->tabulacion(0).
                "EXPORT. ".$this->tabulacion(0).
                "V-GRAVADAS ".$this->tabulacion(0).
                "\r\n");
        }*/

		$row_counter = 1;

		/**************************
		 *  IMPRIME CUERPO
		 **************************/

		foreach ($data as $val) {

			$subtotal = round($val['importe_total_venta'] - $val['importe_otros_no_suj_iva'] - $val['exportacion_excentas'] - $val['ventas_tasa_cero'], 2);
			$importe_debito = round($subtotal - $val['descuento_rebaja_suj_iva'], 2);
			$debito_fiscal = round($importe_debito * 0.13,2);
			$newDate = date("d/m/Y", strtotime( $val['fecha_factura']));
			$codigo_control = $val['codigo_control']==null || $val['codigo_control']==NULL || $val['codigo_control'] == ''?'0':$val['codigo_control'];
			/*if ($this->objParam->getParametro('formato_reporte') == 'txt' && $row_counter <= 34){
                fwrite ($file,  $ctd.
                    "    ".$newDate." ".
                    " ".$val['nro_factura']." ".
                    $val['nro_autorizacion']." ".
                    $val['estado']." ".
                    $val['razon_social_cli']." ".
                    $val['importe_total_venta']." ".
                    $val['importe_otros_no_suj_iva']." ".
                    $val['exportacion_excentas']." ".
                    $val['ventas_tasa_cero'].
                    "\r\n");
            }else {*/
			fwrite($file, "3" . $separador .
				$ctd . $separador .
				$newDate . $separador .
				$val['nro_factura'] . $separador .
				$val['nro_autorizacion'] . $separador .
				$val['estado'] . $separador .
				$val['nit_ci_cli'] . $separador .
				$val['razon_social_cli'] . $separador .

				$val['importe_total_venta'] . $separador .
				$val['importe_otros_no_suj_iva'] . $separador .
				$val['exportacion_excentas'] . $separador .
				$val['ventas_tasa_cero'] . $separador .

				$subtotal . $separador .
				$val['descuento_rebaja_suj_iva'] . $separador .
				$importe_debito . $separador .
				$debito_fiscal . $separador .
				$codigo_control . "\r\n");
			//}
			/*if(in_array($row_counter/34,$lista)){
                fwrite ($file,  "------------------------------------------------------------------------------------------------------------------------------------"."\r\n\n\n\n\n");
                $row_counter = 38;
            }

            if(in_array($row_counter/38,$lista)){

                fwrite ($file,  "BOLIVIANA DE AVIACION (BoA)".$this->tabulacion(5).
                    'LIBRO DE VENTAS'.$this->tabulacion(5).
                    'Pagina: '."\r\n");

                fwrite ($file,  "                           ".$this->tabulacion(5).
                    "   ESTANDAR"."\r\n");

                fwrite ($file,  "------------------------------------------------------------------------------------------------------------------------------------"."\r\n");

                fwrite ($file,  "              Periodo: ".$this->tabulacion(2).$dataPeriodoArray['gestion']."\t".$dataPeriodoArray['periodo']."\r\n");

                fwrite ($file,  "Nombre o Razón Social: \t".$dataEntidadArray['nombre'].$this->tabulacion(1)."NIT: ".$dataEntidadArray['nit'].$this->tabulacion(2)."EXPRESADO EN BOLIVIANOS"."\r\n");

                fwrite ($file,  "------------------------------------------------------------------------------------------------------------------------------------"."\r\n");

                //fwrite($file, html_entity_decode())

                fwrite ($file,  "Nro. ".$this->tabulacion(0).
                    "FECHA DE LA ".$this->tabulacion(0).
                    "Nro. DE LA ".$this->tabulacion(0).
                    "Nro. DE ".$this->tabulacion(0).
                    "ESTADO ".$this->tabulacion(0).
                    "NOMBRE O RAZON SOCIAL ".$this->tabulacion(0).
                    "IMPORTE TOTAL ".$this->tabulacion(0).
                    "IMPORTE ICE/IEHD ".$this->tabulacion(0).
                    "EXPORT. ".$this->tabulacion(0).
                    "V-GRAVADAS ".$this->tabulacion(0).
                    "\r\n");

                $row_counter = 1;

            }*/
			$ctd = $ctd + 1;
			$row_counter ++;
		} //end for



		fclose($file);
		return $fileName;
	}

	function reporteIata () {

	$this->objFun=$this->create('MODDocCompraVenta');
	$this->res = $this->objFun->getDataIata();

	if($this->res->getTipo()=='ERROR'){
		$this->res->imprimirRespuesta($this->res->generarJson());
		exit;
	}

	$nombreArchivo = $this->crearArchivoExportacionIata($this->res->getDatos(), $this->objParam, $this->objParam->getParametro('nit_linea_aerea'), $this->objParam->getParametro('cod_iata'));

	$this->mensajeExito=new Mensaje();
	$this->mensajeExito->setMensaje('EXITO','Reporte.php','Se genero con exito el archivo'.$nombreArchivo,
									'Se genero con exito el archivo'.$nombreArchivo,'control');
	$this->mensajeExito->setArchivoGenerado($nombreArchivo);

	$this->res->imprimirRespuesta($this->mensajeExito->generarJson());
}

function crearArchivoExportacionIata($datos, $parm, $nit_linea_aerea, $cod_iata) {
	$separador = '|';
	$data = json_decode($datos[0]['jsondata']);
	if($this->objParam->getParametro('filtro_sql')=='fechas'){
		$fecha = $this->objParam->getParametro('fecha_ini');
		$anio = substr($fecha, 6, 4);
		$mes = substr($fecha, 3, 2);
	}else{
		$dataPeriodo = $this->recuperarDatosPeriodo();
		$dataPeriodoArray = $dataPeriodo->getDatos();
		$anio = $dataPeriodoArray['gestion'];
		$mes = $dataPeriodoArray['periodo'];
	}

	if (count($data->data) <= 0) {
		throw new \Exception("No se tiene registros para el reporte", 1);
	}

	if($this->objParam->getParametro('formato_reporte_iata') =='txt'){
		$separador = "|";
		$fileName = 'IATA_'.$nit_linea_aerea.$mes.$anio;
	}else{
		$separador = "|";
		$fileName = 'IATA_'.$nit_linea_aerea.$mes.$anio.'.csv';

	}


	$file = fopen("../../../reportes_generados/$fileName", 'w');

	 // var_dump($data->data);exit;
	foreach ($data->data as $val) {
				fwrite ($file,
				$val->presentacion.$separador.
				$val->tipo_transaccion.$separador.
				$val->nro_factura.$separador.
				$val->origen_servicio.$separador.
				$val->fecha_transaccion.$separador.
				$nit_linea_aerea.$separador.
				$cod_iata.$separador.
				$val->nombre_pasajero.$separador.
				$val->t_iva.$separador.
				$val->moneda.$separador.
				$val->nit_ci_beneficiario.$separador."0".
				"\r\n"
			);
		}

	 return $fileName;
	}



}

?>
