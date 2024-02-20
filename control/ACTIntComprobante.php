<?php
/**
*@package pXP
*@file gen-ACTIntComprobante.php
*@author  (admin)
*@date 29-08-2013 00:28:30
*@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
*/
//require_once(dirname(__FILE__).'/../../lib/lib_reporte/ReportePDF2.php');
// convert to PDF
require_once(dirname(__FILE__).'/../../pxp/pxpReport/DataSource.php');
require_once(dirname(__FILE__).'/../../lib/lib_reporte/PlantillasHTML.php');
require_once(dirname(__FILE__).'/../../lib/lib_reporte/smarty/ksmarty.php');
require_once(dirname(__FILE__).'/../reportes/RIntCbte.php');

require_once(dirname(__FILE__).'/../reportes/RComprobanteDiario.php');
require_once(dirname(__FILE__).'/../reportes/RComprobanteDiarioDet.php');
require_once(dirname(__FILE__).'/../reportes/RComprobanteDiarioXls.php');
//
class ACTIntComprobante extends ACTbase{
	
	private $objPlantHtml;

    function listarIntComprobante(){
        $this->objParam->defecto('ordenacion','id_int_comprobante');
        $this->objParam->defecto('dir_ordenacion','asc');
        $this->objParam->addFiltro("(incbte.temporal = ''no'' or (incbte.temporal = ''si'' and incbte.vbregional = ''si''))");

        //begin(franklin.espinoza) 20/08/2020
        //var_dump($this->objParam->getParametro('estado_cbte'));exit;
        if($this->objParam->getParametro('estado_cbte') == 'borrador'){
            $this->objParam->addFiltro("incbte.estado_reg in (''borrador'')");
        }else if($this->objParam->getParametro('estado_cbte') == 'verificado'){
            $this->objParam->addFiltro("incbte.estado_reg in (''verificado'')");
        }else if($this->objParam->getParametro('estado_cbte') == 'elaborado'){
            $this->objParam->addFiltro("incbte.estado_reg in (''elaborado'')");
        }else if($this->objParam->getParametro('estado_cbte') == 'aprobado'){
            $this->objParam->addFiltro("incbte.estado_reg in (''aprobado'')");
        }else if($this->objParam->getParametro('estado_cbte') == 'validado'){
            $this->objParam->addFiltro("incbte.estado_reg in (''validado'')");
        }else if($this->objParam->getParametro('estado_cbte') == 'borrador_elaborado'){
            $this->objParam->addFiltro("incbte.estado_reg in (''borrador'',''elaborado'')");
        }
        //end(franklin.espinoza) 20/08/2020

        if($this->objParam->getParametro('tipo')=='diario'){
            $this->objParam->addFiltro("incbte.id_clase_comprobante in (''3'',''4'')");
        }

        if($this->objParam->getParametro('tipo')=='pago'){
            $this->objParam->addFiltro("incbte.id_clase_comprobante in (''1'',''5'')");
        }


        if($this->objParam->getParametro('id_deptos')!=''){
            $this->objParam->addFiltro("incbte.id_depto in (".$this->objParam->getParametro('id_deptos').")");
        }

        if($this->objParam->getParametro('id_gestion')!=''){
            $this->objParam->addFiltro("incbte.id_gestion in (".$this->objParam->getParametro('id_gestion').")");
        }

        if($this->objParam->getParametro('id_clase_comprobante')!=''){
            $this->objParam->addFiltro("incbte.id_clase_comprobante in (".$this->objParam->getParametro('id_clase_comprobante').")");
        }

        if($this->objParam->getParametro('nombreVista') == 'IntComprobanteLd'  || $this->objParam->getParametro('nombreVista') == 'IntComprobanteLdEntrega'){
            $this->objParam->addFiltro("incbte.estado_reg = ''validado''");
        }else{
            //(may) vb de los comprobantes en estado vbfin y vbconta
            if($this->objParam->getParametro('nombreVista') == 'VbIntComprobante'){
                //$this->objParam->addFiltro(" (incbte.estado_reg in (''verificado'',''aprobado''))" );

            }else{
                //(may)25-09-2019 para que enliste el nuevo estado vbfin y vbconta
                // $this->objParam->addFiltro("incbte.estado_reg in (''borrador'', ''edicion'')");
                if( $this->objParam->getParametro('nombreVista') == 'IntComprobantezConsulta'){
                    $this->objParam->addFiltro("incbte.estado_reg in (''borrador'', ''validado'')");
                }else if( $this->objParam->getParametro('nombreVista') != 'IntComprobanteConsultas'){ //fRnk: adicionado b. HR00903
                    $this->objParam->addFiltro("incbte.estado_reg in (''borrador'', ''edicion'')");
                }

            }

        }



        if($this->objParam->getParametro('nombreVista') == 'IntComprobanteLdEntrega'){
            $this->objParam->addFiltro(" (incbte.c31 = '''' or incbte.c31 is null )" );
        }


        if($this->objParam->getParametro('momento')!= ''){
            $this->objParam->addFiltro("incbte.momento = ''".$this->objParam->getParametro('momento')."''");
        }

        //RCM 01/09/2017
        if($this->objParam->getParametro('id_int_comprobante')!= ''){
            $this->objParam->addFiltro("incbte.id_int_comprobante = ".$this->objParam->getParametro('id_int_comprobante'));
        }

        if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
            $this->objReporte = new Reporte($this->objParam,$this);
            $this->objParam->addParametro('es_reporte', 'si'); //fRnk: adicionado para HR00903
            $this->res = $this->objReporte->generarReporteListado('MODIntComprobante','listarIntComprobante');
        } else{
            $this->objParam->addParametro('es_reporte', 'no');
            $this->objFunc=$this->create('MODIntComprobante');

            $this->res=$this->objFunc->listarIntComprobante($this->objParam);
        }

        //echo dirname(__FILE__).'/../../lib/lib_reporte/ReportePDF2.php';exit;
        $this->res->imprimirRespuesta($this->res->generarJson());

    }

    function listarIntComprobanteWF(){
		$this->objParam->defecto('ordenacion','id_int_comprobante');
		$this->objParam->defecto('dir_ordenacion','asc');
		$this->objParam->addFiltro("(incbte.temporal = ''no'' or (incbte.temporal = ''si'' and vbregional = ''si''))");    
		
		if($this->objParam->getParametro('id_deptos')!=''){
            $this->objParam->addFiltro("incbte.id_depto in (".$this->objParam->getParametro('id_deptos').")");    
        }
		
		
		if($this->objParam->getParametro('momento')!= ''){
			$this->objParam->addFiltro("incbte.momento = ''".$this->objParam->getParametro('momento')."''");    
		}
		
		$this->objParam->addParametro('id_funcionario_usu',$_SESSION["ss_id_funcionario"]); 
		
		
		if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
			$this->objReporte = new Reporte($this->objParam,$this);
			$this->res = $this->objReporte->generarReporteListado('MODIntComprobante','listarIntComprobanteWF');
		} else{
			$this->objFunc=$this->create('MODIntComprobante');
			
			$this->res=$this->objFunc->listarIntComprobanteWF($this->objParam);
		}
		
		//echo dirname(__FILE__).'/../../lib/lib_reporte/ReportePDF2.php';exit;
		$this->res->imprimirRespuesta($this->res->generarJson());
	}





	
	function listarSimpleIntComprobante(){
		$this->objParam->defecto('ordenacion','id_int_comprobante');
		$this->objParam->defecto('dir_ordenacion','asc');
		
		$this->objParam->addFiltro("inc.estado_reg = ''validado''");
		
		if($this->objParam->getParametro('id_deptos')!=''){
            $this->objParam->addFiltro("inc.id_depto in (".$this->objParam->getParametro('id_deptos').")");    
        }
        
        if($this->objParam->getParametro('id_gestion')!=''){
            $this->objParam->addFiltro("per.id_gestion = ".$this->objParam->getParametro('id_gestion'));    
        }
		
		 if($this->objParam->getParametro('id_moneda')!=''){
            $this->objParam->addFiltro("inc.id_moneda = ".$this->objParam->getParametro('id_moneda'));    
        }
        
		
		
		if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
			$this->objReporte = new Reporte($this->objParam,$this);
			$this->res = $this->objReporte->generarReporteListado('MODIntComprobante','listarSimpleIntComprobante');
		} else{
			$this->objFunc=$this->create('MODIntComprobante');
			
			$this->res=$this->objFunc->listarSimpleIntComprobante($this->objParam);
		}
		
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
	
				
	function insertarIntComprobante(){
		$this->objFunc=$this->create('MODIntComprobante');	
		if($this->objParam->insertar('id_int_comprobante')){
			$this->res=$this->objFunc->insertarIntComprobante($this->objParam);			
		} else{			
			$this->res=$this->objFunc->modificarIntComprobante($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
						
	function eliminarIntComprobante(){
		$this->objFunc=$this->create('MODIntComprobante');	
		$this->res=$this->objFunc->eliminarIntComprobante($this->objParam);
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
	
	function generarDesdePlantilla(){
		$this->objFunc=$this->create('MODIntComprobante');	
		$this->res=$this->objFunc->generarDesdePlantilla($this->objParam);
		$this->res->imprimirRespuesta($this->res->generarJson());
	}

    function validarIntComprobante(){
		$this->objFunc=$this->create('MODIntComprobante');	
		$this->res=$this->objFunc->validarIntComprobante($this->objParam);
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
	
	//Cabecera reporte comprobante
	function listarCbteCabecera(){
		$this->objParam->defecto('ordenacion','id_int_comprobante');
		$this->objParam->defecto('dir_ordenacion','asc');
		
		if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
			$this->objReporte = new Reporte($this->objParam,$this);
			$this->res = $this->objReporte->generarReporteListado('MODIntComprobante','listarCbteCabecera');
		} else{
			$this->objFunc=$this->create('MODIntComprobante');
			$this->res=$this->objFunc->listarCbteCabecera($this->objParam);
		}

		$this->res->imprimirRespuesta($this->res->generarJson());
	}
	
	//Detalle reporte comprobante
	function listarCbteDetalle(){
		$this->objParam->defecto('ordenacion','id_int_comprobante');
		$this->objParam->defecto('dir_ordenacion','asc');
		
		if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
			$this->objReporte = new Reporte($this->objParam,$this);
			$this->res = $this->objReporte->generarReporteListado('MODIntComprobante','listarCbteDetalle');
		} else{
			$this->objFunc=$this->create('MODIntComprobante');
			$this->res=$this->objFunc->listarCbteDetalle($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
	
	function reporteComprobante1(){
		$this->objFunc=$this->create('MODIntComprobante');
		
		//
		//Seteo de los parámetros generales del reporte
		//
		//Configuración
		$this->objParam->addParametro('orientation','P');
		$this->objParam->addParametro('unit','mm');
		$this->objParam->addParametro('format','Letter');
		$this->objParam->addParametro('unicode',true);
		$this->objParam->addParametro('encoding','UTF-8');
		$this->objParam->addParametro('diskcache',false);
		$this->objParam->addParametro('pdfa',false);
		//Archivo
		$this->objParam->addParametro('nombre_archivo','pxp_conta_comprobante');
		$this->objParam->addParametro('title1','REGISTRO');
		$this->objParam->addParametro('title2','Comprobante');
		
		$this->objParam->addParametro('header_key_right1','Cbte.');
		$this->objParam->addParametro('header_key_right2','Rev.');
		$this->objParam->addParametro('header_key_right3','Fecha.');
		$this->objParam->addParametro('header_key_right4','Pagina.');
		$this->objParam->addParametro('header_value_right1','DCC-CD-090001/2013');
		$this->objParam->addParametro('header_value_right2','1.0');
		$this->objParam->addParametro('header_value_right3','10/09/2013');
		$this->objParam->addParametro('header_value_right4','12');
		
		//Instancia de las plantillas
		$this->objPlantHtml=new PlantillasHTML($this->objParam);
		$this->objPlantHtml->setSeleccionarPlantilla('header',0);
		$header=$this->objPlantHtml->getPlantilla();
		$this->objPlantHtml->setSeleccionarPlantilla('footer',0);
		$footer=$this->objPlantHtml->getPlantilla();
			
	
		//Instancia la clase de reportes
		$this->objReporte = new ReportePDF2($this->objParam);
		$this->objReporte->setHeaderHtml($header);
		$this->objReporte->setFooterHtml($footer);

		//Genera el reporte		
		$this->objReporte->generarReporte();
		
		//Salida
		$mensajeExito = new Mensaje();
		$mensajeExito->setMensaje('EXITO','Reporte.php','Reporte generado','Se generó con éxito el reporte: '.$this->objParam->getParametro('nombre_archivo'),'control');
		$mensajeExito->setArchivoGenerado($this->objReporte->getNombreArchivo());
		$this->res = $mensajeExito;
		$this->res->imprimirRespuesta($this->res->generarJson());
	}

	function reporteComprobante(){
		/////////////////////
		//Obtención de datos
		/////////////////////
		//Cabecera (firmas)
		$this->objFunc=$this->create('MODIntComprobante');
		$cbteHeader = $this->objFunc->listarCbteCabecera($this->objParam);
		$cbteHeaderData=$cbteHeader->getDatos();
		
		//Detalle transacciones
		$this->objFunc=$this->create('MODIntComprobante');
		$cbteTrans = $this->objFunc->listarCbteDetalle($this->objParam);
		$cbteTransData=$cbteTrans->getDatos();
		
		//Se obtienen la suma de debe y haber
		$arrTotalesCbte= array('tot_ejec'=>0,'tot_debe'=>0,'tot_haber'=>0,'tot_debe1'=>0,'tot_haber1'=>0);
		
		foreach($cbteTransData as $key=>$val){
			$arrTotalesCbte['tot_debe']+=$val['importe_debe'];
			$arrTotalesCbte['tot_haber']+=$val['importe_haber'];
			$arrTotalesCbte['tot_debe1']+=$val['importe_debe1'];
			$arrTotalesCbte['tot_haber1']+=$val['importe_haber1'];
		}
		
		/*echo '<pre>';
		print_r($cbteTransData);
		echo '</pre>';
		exit;*/
		
		
		//Reporte
		$repCbte = new ksmarty();
		
		//////////
		//Header
		//////////
		$repCbte->assign('main_title1','COMPROBANTE DIARIO'); //dinámico
		$repCbte->assign('main_title2','MOMENTO PRESUPUESTARIO: DEVENGADO');//dinámico
		$repCbte->assign('header_key_right1','Depto.');
		$repCbte->assign('header_key_right2','N°');
		$repCbte->assign('header_key_right3','Fecha');
		$repCbte->assign('header_value_right1',$cbteHeaderData[0]['cod_depto']);//dinámico
		$repCbte->assign('header_value_right2',$cbteHeaderData[0]['nro_cbte']);//dinámico
		$repCbte->assign('header_value_right3',$cbteHeaderData[0]['fecha']);//dinámico
		$header = $repCbte->fetch($repCbte->getTplHeader());
		
		//echo $header;exit;
		
		/////////
		//Labels
		/////////
		$repCbte->setTemplateDir(dirname(__FILE__).'/../reportes/tpl_comprobante/');
		$labels=$repCbte->fetch('labels.tpl');
		
		
		/////////
		//Footer
		/////////
		$repCbte->setTemplateDir(dirname(__FILE__).'/../reportes/tpl_comprobante/');
		$repCbte->assign('etiqueta1','Centro Responsable');
		$repCbte->assign('etiqueta2','Elaborado por');
		$repCbte->assign('etiqueta3','Beneficiario');
		$repCbte->assign('etiqueta4','VoBo');
		$repCbte->assign('firma1',$cbteHeaderData[0]['firma1']);
		$repCbte->assign('firma2',$cbteHeaderData[0]['firma2']);
		$repCbte->assign('firma3',$cbteHeaderData[0]['firma3']);
		$repCbte->assign('firma4',$cbteHeaderData[0]['firma4']);
		$repCbte->assign('cargo1',$cbteHeaderData[0]['firma1_cargo']);
		$repCbte->assign('cargo2',$cbteHeaderData[0]['firma2_cargo']);
		$repCbte->assign('cargo3',$cbteHeaderData[0]['firma3_cargo']);
		$repCbte->assign('cargo4',$cbteHeaderData[0]['firma4_cargo']);
		$footer = $repCbte->fetch('footer.tpl');
		
		
		//////////
		//Master
		//////////
		$repCbte->setTemplateDir(dirname(__FILE__).'/../reportes/tpl_comprobante/');
		$repCbte->assign('acreedor',$cbteHeaderData[0]['beneficiario']); //dinámico
		$repCbte->assign('conformidad',$cbteHeaderData[0]['glosa1']); //dinámico
		$repCbte->assign('operacion',$cbteHeaderData[0]['glosa2']); //dinámico
		$repCbte->assign('tipo_cambio',$cbteHeaderData[0]['tipo_cambio']); //dinámico
		$repCbte->assign('facturas','Facturas 1 2'); //dinámico
		$repCbte->assign('pedido','Pedido G'); //dinámico
		$repCbte->assign('aprobacion','Aprobación V'); //dinámico
		$master=$repCbte->fetch('master.tpl');
		
		
		/////////
		//Detail
		/////////
		$repCbte->assign('transac',$cbteTransData); //dinámico
		$repCbte->assign('tot_ejecucion_bs',$arrTotalesCbte['tot_ejec']); //dinámico
		$repCbte->assign('tot_importe_debe1',$arrTotalesCbte['tot_debe1']); //dinámico
		$repCbte->assign('tot_importe_haber1',$arrTotalesCbte['tot_haber1']); //dinámico
		$repCbte->assign('tot_importe_debe',$arrTotalesCbte['tot_debe']); //dinámico
		$repCbte->assign('tot_importe_haber',$arrTotalesCbte['tot_haber']); //dinámico
		$detail=$repCbte->fetch('comprobante.tpl');

		
		////////////////////////////
		//Creación del archivo html
		////////////////////////////
		//$html = $header.'<br>'.$master.'<br>'.$labels.'<br>'.$detail.'<br>'.$footer;
		$html = $header.$master.$labels.$detail.$footer;
		//echo 'resp:'.$html; exit;
		
		$repCbte->generarArchivo($html,'pxp_comprobante');
		
		$mensajeExito = new Mensaje();
		$mensajeExito->setMensaje('EXITO',dirname(__FILE__),'Salida generada','Se generó la salida HTML con éxito','control','reporteComprobante');
		$mensajeExito->setArchivoGenerado($repCbte->getFileName());
		$this->res = $mensajeExito;
		$this->res->imprimirRespuesta($this->res->generarJson());
		
		//Se obtienen la suma de debe y haber
		$arrTotalesCbte= array('tot_ejec'=>0,'tot_debe'=>0,'tot_haber'=>0,'tot_debe1'=>0,'tot_haber1'=>0);
		
		foreach($cbteTransData as $key=>$val){
			$arrTotalesCbte['tot_debe']+=$val['importe_debe'];
			$arrTotalesCbte['tot_haber']+=$val['importe_haber'];
			$arrTotalesCbte['tot_debe1']+=$val['importe_debe1'];
			$arrTotalesCbte['tot_haber1']+=$val['importe_haber1'];
		}
		
	}

    function recuperarDatosCbte(){
    	$dataSource = new DataSource();	
		$this->objFunc = $this->create('MODIntComprobante');
		$cbteHeader = $this->objFunc->listarCbteCabecera($this->objParam);
		if($cbteHeader->getTipo() == 'EXITO'){
				 	
				$dataSource->putParameter('cabecera',$cbteHeader->getDatos());
						
				$this->objFunc=$this->create('MODIntComprobante');
				$cbteTrans = $this->objFunc->listarCbteDetalle($this->objParam);
				if($cbteTrans->getTipo()=='EXITO'){
					$dataSource->putParameter('detalleCbte', $cbteTrans->getDatos());

                    $this->objFunc=$this->create('MODIntComprobante');
                    $beneTrans = $this->objFunc->listarBeneficiarios($this->objParam);

                    if($beneTrans->getTipo()=='EXITO'){
                        $dataSource->putParameter('listadoBeneficiarios', $beneTrans->getDatos());
                    }
                    else{
                        $beneTrans->imprimirRespuesta($beneTrans->generarJson());
                    }
				}
		        else{
		            $cbteTrans->imprimirRespuesta($cbteTrans->generarJson());
				}
			return $dataSource;
		}
        else{
		    $cbteHeader->imprimirRespuesta($cbteHeader->generarJson());
		}              
		
    }

   function reporteCbte(){
			
		$nombreArchivo = uniqid(md5(session_id()).'-Cbte') . '.pdf'; 
		$dataSource = $this->recuperarDatosCbte();

		//parametros basicos
		$tamano = 'LETTER';
		$orientacion = 'p';
        $titulo='Reporte';
		$this->objParam->addParametro('orientacion',$orientacion);
		$this->objParam->addParametro('tamano',$tamano);		
		$this->objParam->addParametro('titulo_archivo',$titulo);        
		$this->objParam->addParametro('nombre_archivo',$nombreArchivo);

        //fRnk: fragmento para limpiar archivos antigüos
        /*$fileList = glob(dirname(__FILE__).'/../../reportes_generados/*.pdf');
        foreach($fileList as $file) {
           if(is_file($file) && date("m-d-Y", filemtime($file)) != date("m-d-Y")) {
               unlink($file);
           }
        }*/

        //Instancia la clase de pdf
        $reporte = new RIntCbte($this->objParam);
		$reporte->datosHeader($dataSource);
		$reporte->generarReporte();
        $reporte->output($reporte->url_archivo,'F');

        $this->mensajeExito=new Mensaje();
		$this->mensajeExito->setMensaje('EXITO','Reporte.php','Reporte generado','Se generó con éxito el reporte: '.$nombreArchivo,'control');
		$this->mensajeExito->setArchivoGenerado($nombreArchivo);
		$this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());
		
	}

    function reporteCbte_bk(){
   	    	
   	    $dataSource = $this->recuperarDatosCbte(); 
   	   	
   	    // get the HTML
	    ob_start();
	    include(dirname(__FILE__).'/../reportes/tpl/intCbte.php');
        $content = ob_get_clean();
	    try
	    {
	    	
			//$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
			$pdf = new TCPDF();
			
			
			$pdf->SetDisplayMode('fullpage');
			
            // set document information
            $pdf->SetCreator(PDF_CREATOR);
			// set default header data
			//$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 061', PDF_HEADER_STRING);
			
			// set default monospaced font
			$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
			
			// set margins
			$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
			$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
			$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
			
			// set auto page breaks
			$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
			
			// set font
			$pdf->SetFont('helvetica', '', 10);
			// add a page
            $pdf->AddPage();
			$pdf->writeHTML($content, true, false, true, false, '');
			$nombreArchivo = 'IntComprobante.pdf';
			$pdf->Output(dirname(__FILE__).'/../../reportes_generados/'.$nombreArchivo, 'F');
			
			$mensajeExito = new Mensaje();
            $mensajeExito->setMensaje('EXITO','Reporte.php','Reporte generado', 'Se generó con éxito el reporte: '.$nombreArchivo,'control');
            $mensajeExito->setArchivoGenerado($nombreArchivo);
            $this->res = $mensajeExito;
            $this->res->imprimirRespuesta($this->res->generarJson());
			
			
			
			
	    }
	    catch(exception $e) {
	        echo $e;
	        exit;
	    }
    }	


    function igualarComprobante(){
		$this->objFunc=$this->create('MODIntComprobante');	
		$this->res=$this->objFunc->igualarComprobante($this->objParam);
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
	
	function swEditable(){
		$this->objFunc=$this->create('MODIntComprobante');	
		$this->res=$this->objFunc->swEditable($this->objParam);
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
	
	function volcarCbte(){
		$this->objFunc=$this->create('MODIntComprobante');	
		$this->res=$this->objFunc->volcarCbte($this->objParam);
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
	
	function listarCbteDependencias(){
        
        //obtiene el parametro nodo enviado por la vista
        $node=$this->objParam->getParametro('node');

        $id_cuenta=$this->objParam->getParametro('id_int_comprobante');
        $tipo_nodo=$this->objParam->getParametro('tipo_nodo');
        
                   
        if($node=='id'){
            $this->objParam->addParametro('id_padre','%');
        }
        else {
            $this->objParam->addParametro('id_padre',$id_cuenta);
        }
        
		$this->objFunc=$this->create('MODIntComprobante');
        $this->res=$this->objFunc->listarCbteDependencias();
        
        $this->res->setTipoRespuestaArbol();
        
        $arreglo=array();
        
        array_push($arreglo,array('nombre'=>'id','valor'=>'id_int_comprobante'));
        array_push($arreglo,array('nombre'=>'id_p','valor'=>'id_int_comprobante_padre'));
        
        
        array_push($arreglo,array('nombre'=>'text','valores'=>'<b> (#id_int_comprobante#) - #nro_cbte# </b>'));
        array_push($arreglo,array('nombre'=>'cls','valor'=>'nombre_cuenta'));
        array_push($arreglo,array('nombre'=>'qtip','valores'=>'<b> #nro_cbte#</b><br/>#glosa1#'));
        
        
        $this->res->addNivelArbol('tipo_nodo','raiz',array('leaf'=>false,
                                                        'allowDelete'=>true,
                                                        'allowEdit'=>true,
                                                        'cls'=>'folder',
                                                        'tipo_nodo'=>'raiz',
                                                        'icon'=>'../../../lib/imagenes/a_form.png'),
                                                        $arreglo);
         
        /*se añade un nivel al arbol incluyendo con tido de nivel carpeta con su arreglo de equivalencias
          es importante que entre los resultados devueltos por la base exista la variable\
          tipo_dato que tenga el valor en texto = 'hoja' */
                                                                

         $this->res->addNivelArbol('tipo_nodo','hijo',array(
                                                        'leaf'=>false,
                                                        'allowDelete'=>true,
                                                        'allowEdit'=>true,
                                                        'tipo_nodo'=>'hijo',
                                                        'icon'=>'../../../lib/imagenes/a_form.png'),
                                                        $arreglo);
													
														

        $this->res->imprimirRespuesta($this->res->generarJson());         

   }

   function siguienteEstado(){
        $this->objFunc=$this->create('MODIntComprobante');  
        $this->res=$this->objFunc->siguienteEstado($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

   function anteriorEstado(){
        $this->objFunc=$this->create('MODIntComprobante');  
        $this->res=$this->objFunc->anteriorEstado($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }
   
   function clonarCbte(){
		$this->objFunc=$this->create('MODIntComprobante');	
		$this->res=$this->objFunc->clonarCbte($this->objParam);
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
   
   function modificarFechasCostosCbte(){
		$this->objFunc=$this->create('MODIntComprobante');	
		$this->res=$this->objFunc->modificarFechasCostosCbte($this->objParam);
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
   
   

   function listarVerPresCbte(){
		$this->objParam->defecto('ordenacion','id_int_comprobante');
		$this->objParam->defecto('dir_ordenacion','asc');
		$this->objParam->addFiltro("(incbte.temporal = ''no'' or (incbte.temporal = ''si'' and vbregional = ''si''))");    
		
		if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
			$this->objReporte = new Reporte($this->objParam,$this);
			$this->res = $this->objReporte->generarReporteListado('MODIntComprobante','listarVerPresCbte');
		} else{
			$this->objFunc=$this->create('MODIntComprobante');
			
			$this->res=$this->objFunc->listarVerPresCbte($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
	//
	function listarRepIntComprobante(){

		$this->objParam->addFiltro("(incbte.temporal = ''no'' or (incbte.temporal = ''si'' and vbregional = ''si''))");    		
		if($this->objParam->getParametro('id_deptos')!=''){
			$this->objParam->addFiltro("incbte.id_depto in (".$this->objParam->getParametro('id_deptos').")");    
		}		
		if($this->objParam->getParametro('id_gestion')!=''){
			$this->objParam->addFiltro("incbte.id_gestion in (".$this->objParam->getParametro('id_gestion').")");    
		}		
		if($this->objParam->getParametro('id_clase_comprobante')!=''){
		    $this->objParam->addFiltro("incbte.id_clase_comprobante in (".$this->objParam->getParametro('id_clase_comprobante').")");    
		}		
		if($this->objParam->getParametro('nombreVista') == 'IntComprobanteLd'  || $this->objParam->getParametro('nombreVista') == 'IntComprobanteLdEntrega'){
			$this->objParam->addFiltro("incbte.estado_reg = ''validado''");    
		}else{
			$this->objParam->addFiltro("incbte.estado_reg in (''borrador'', ''edicion'')");
		}		
		if($this->objParam->getParametro('nombreVista') == 'IntComprobanteLdEntrega'){
			$this->objParam->addFiltro(" (incbte.c31 = '''' or incbte.c31 is null )" );      
		}		
		if($this->objParam->getParametro('momento')!= ''){
			$this->objParam->addFiltro("incbte.momento = ''".$this->objParam->getParametro('momento')."''");    
		}
		if($this->objParam->getParametro('id_int_comprobante')!= ''){
			$this->objParam->addFiltro("incbte.id_int_comprobante = ".$this->objParam->getParametro('id_int_comprobante'));    
		}

		$this->objFunc=$this->create('MODIntComprobante');		
		$cbteHeader = $this->objFunc->listarRepIntComprobanteDiario($this->objParam);		
		if($cbteHeader->getTipo() == 'EXITO'){
			return $cbteHeader;										
			
		}
		else{
			$cbteHeader->imprimirRespuesta($cbteHeader->generarJson());
			exit;
		}

	}
	//mp
	function impReporteDiario() {
		if($this->objParam->getParametro('tipo_formato')=='pdf' and $this->objParam->getParametro('tipo_diario')=='dia'){
			$nombreArchivo = uniqid(md5(session_id()).'LibroDiario').'.pdf';			
			$dataSource = $this->listarRepIntComprobante();
			$dataEntidad = "";
			$dataPeriodo = "";
			$orientacion = 'P';
			$tamano = 'LETTER';
			$titulo = 'Consolidado';
			$this->objParam->addParametro('orientacion',$orientacion);
			$this->objParam->addParametro('tamano',$tamano);
			$this->objParam->addParametro('titulo_archivo',$titulo);
			$this->objParam->addParametro('nombre_archivo',$nombreArchivo);
			$reporte = new RComprobanteDiario($this->objParam);
            $reporte->datosHeader(
                $dataSource, $this->objParam->getParametro('detalle'),
                $this->objParam->getParametro('desde'),
                $this->objParam->getParametro('hasta'),
                $this->objParam->getParametro('tipo_moneda'));
               // $this->objParam->getParametro('codigos'),
               // $this->objParam->getParametro('tipo_balance'));
               // $this->objParam->getParametro('incluir_cierre'),
               // $this->objParam->getParametro('formato_reporte'));
			$reporte->datosHeader($dataSource->getDatos(),$dataSource->extraData, '' , '');		
			$reporte->generarReporte();
			$reporte->output($reporte->url_archivo,'F');
			$this->mensajeExito=new Mensaje();
			$this->mensajeExito->setMensaje('EXITO','Reporte.php','Reporte generado','Se genera con exito el reporte: '.$nombreArchivo,'control');
			$this->mensajeExito->setArchivoGenerado($nombreArchivo);
			$this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());		
		}

		elseif($this->objParam->getParametro('tipo_formato')=='pdf' and $this->objParam->getParametro('tipo_diario')=='det'){
			$nombreArchivo = uniqid(md5(session_id()).'LibroDiarioDet').'.pdf';
			$dataSource = $this->listarRepIntComprobante();
			//$dataSourceBeneficiarios = $this->listarBeneficiarios();
			$dataEntidad = "";
			$dataPeriodo = "";
			$orientacion = 'P';
			$tamano = 'LETTER';
			$titulo = 'Detallado';
			$this->objParam->addParametro('orientacion',$orientacion);
			$this->objParam->addParametro('tamano',$tamano);
			$this->objParam->addParametro('titulo_archivo',$titulo);
			$this->objParam->addParametro('nombre_archivo',$nombreArchivo);
			//$reporte = new RIntCbte($this->objParam);
			$reporte = new RComprobanteDiarioDet($this->objParam);
			$reporte->datosHeader($dataSource->getDatos(),$dataSource->extraData, '' , '');
			$reporte->generarReporte();
			$reporte->output($reporte->url_archivo,'F');
			$this->mensajeExito=new Mensaje();
			$this->mensajeExito->setMensaje('EXITO','Reporte.php','Reporte generado','Se genera con exito el reporte: '.$nombreArchivo,'control');
			$this->mensajeExito->setArchivoGenerado($nombreArchivo);
			$this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());
		}
      
		elseif($this->objParam->getParametro('tipo_formato')=='xls' and $this->objParam->getParametro('tipo_diario')=='dia'){

				$dataSource = $this->contenidoLibroDiario();
				//$saldo_anterior = $this->calcularSaldoAnteriorLibroDiario();
				//$recuperar_cabecera =$this->recuperarCabecera();

				$titulo ='Libro Diario Consolidado';
		        //$nombreArchivo=uniqid(md5(session_id()).$titulo);
				$nombreArchivo = uniqid(md5(session_id()).'$titulo').'.xls';
				$this->objParam->addParametro('nombre_archivo',$nombreArchivo);
				$reporte = new RComprobanteDiarioXls($this->objParam);
				//$reporte->datosHeader($dataSource->getDatos(),$recuperar_cabecera->getDatos());
				$reporte->datosHeader($dataSource->getDatos(), '', '');
				//$reporte->datosHeader($dataSource->getDatos(),$saldo_anterior->getDatos(),$recuperar_cabecera->getDatos());
				$reporte->generarReporte();
				$this->mensajeExito=new Mensaje();
				$this->mensajeExito->setMensaje('EXITO','Reporte.php','Reporte generado','Se generó con éxito el reporte: '.$nombreArchivo,'control');
				$this->mensajeExito->setArchivoGenerado($nombreArchivo);
				$this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());

		}				
	}

		function listarIntComprobanteTCCCuenta(){
		$this->objParam->defecto('ordenacion','id_int_comprobante');
		$this->objParam->defecto('dir_ordenacion','asc');
		
		if($this->objParam->getParametro('id_tipo_cc')!=''){
            $this->objParam->addFiltro("cc.id_tipo_cc =".$this->objParam->getParametro('id_tipo_cc'));    
        }

        if($this->objParam->getParametro('nro_cuenta')!=''){
            $this->objParam->addFiltro("cue.nro_cuenta = ''".$this->objParam->getParametro('nro_cuenta')."''");
        }
		
		if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
			$this->objReporte = new Reporte($this->objParam,$this);
			$this->res = $this->objReporte->generarReporteListado('MODIntComprobante','listarIntComprobanteTCCCuenta');
		} else{
			$this->objFunc=$this->create('MODIntComprobante');
			
			$this->res=$this->objFunc->listarIntComprobanteTCCCuenta($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}

    //filtro listado grilla de comprobantes para las estaciones internacionales en Registro de Comprobantes(contador)EXT
    function listarIntComprobanteEXT(){
        $this->objParam->defecto('ordenacion','id_int_comprobante');
        $this->objParam->defecto('dir_ordenacion','asc');
        $this->objParam->addFiltro("(incbte.temporal = ''no'' or (incbte.temporal = ''si'' and vbregional = ''si''))");

        if($this->objParam->getParametro('id_deptos')!=''){
            $this->objParam->addFiltro("incbte.id_depto in (".$this->objParam->getParametro('id_deptos').")");
        }

        if($this->objParam->getParametro('id_gestion')!=''){
            $this->objParam->addFiltro("incbte.id_gestion in (".$this->objParam->getParametro('id_gestion').")");
        }

        if($this->objParam->getParametro('id_clase_comprobante')!=''){
            $this->objParam->addFiltro("incbte.id_clase_comprobante in (".$this->objParam->getParametro('id_clase_comprobante').")");
        }

        if($this->objParam->getParametro('nombreVista') == 'IntComprobanteLd'  || $this->objParam->getParametro('nombreVista') == 'IntComprobanteLdEntrega'){
            $this->objParam->addFiltro("incbte.estado_reg = ''validado''");
        }else{
            $this->objParam->addFiltro("incbte.estado_reg in (''borrador'', ''edicion'')");
        }

        if($this->objParam->getParametro('nombreVista') == 'IntComprobanteLdEntrega'){
            $this->objParam->addFiltro(" (incbte.c31 = '''' or incbte.c31 is null )" );
        }

        if($this->objParam->getParametro('momento')!= ''){
            $this->objParam->addFiltro("incbte.momento = ''".$this->objParam->getParametro('momento')."''");
        }

        //RCM 01/09/2017
        if($this->objParam->getParametro('id_int_comprobante')!= ''){
            $this->objParam->addFiltro("incbte.id_int_comprobante = ".$this->objParam->getParametro('id_int_comprobante'));
        }

        if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
            $this->objReporte = new Reporte($this->objParam,$this);
            $this->res = $this->objReporte->generarReporteListado('MODIntComprobante','listarIntComprobanteEXT');
        } else{
            $this->objFunc=$this->create('MODIntComprobante');

            $this->res=$this->objFunc->listarIntComprobanteEXT($this->objParam);
        }

        //echo dirname(__FILE__).'/../../lib/lib_reporte/ReportePDF2.php';exit;
        $this->res->imprimirRespuesta($this->res->generarJson());
    }


    //may regularizacion de comprobantes
    function cbteRegularizacion(){
        $this->objFunc=$this->create('MODIntComprobante');
        $this->res=$this->objFunc->cbteRegularizacion($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    //may
    function volcarCbteContable(){
        $this->objFunc=$this->create('MODIntComprobante');
        $this->res=$this->objFunc->volcarCbteContable($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    //may
    function cbtePerdidaCbte(){
        $this->objFunc=$this->create('MODIntComprobante');
        $this->res=$this->objFunc->cbtePerdidaCbte($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    //may
    function cbteIncrementoCbte(){
        $this->objFunc=$this->create('MODIntComprobante');
        $this->res=$this->objFunc->cbteIncrementoCbte($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    //{develop: franklin.espinoza date: 12/10/2020, description: Guarda Preventivo,Compromiso,Devengado para procesos con Preventivo}
    function guardarDocumentoSigep(){
        $this->objFunc=$this->create('MODIntComprobante');
        $this->res=$this->objFunc->guardarDocumentoSigep($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function desvalidarCBTE(){
        $this->objFunc=$this->create('MODIntComprobante');
        $this->res=$this->objFunc->desvalidarCBTE($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    //{develop: franklin.espinoza date: 14/01/2022, description: Lista los comprobantes C21}
    function listarIntComprobanteC21(){
        $this->objParam->defecto('ordenacion','id_int_comprobante');
        $this->objParam->defecto('dir_ordenacion','asc');

        //begin(franklin.espinoza) 20/01/2022
        if($this->objParam->getParametro('estado_cbte') == 'borrador'){
            $this->objParam->addFiltro("incbte.estado_reg in (''borrador'')");
        }else if($this->objParam->getParametro('estado_cbte') == 'verificado'){
            $this->objParam->addFiltro("incbte.estado_reg in (''verificado'')");
        }else if($this->objParam->getParametro('estado_cbte') == 'elaborado'){
            $this->objParam->addFiltro("incbte.estado_reg in (''elaborado'')");
        }else if($this->objParam->getParametro('estado_cbte') == 'aprobado'){
            $this->objParam->addFiltro("incbte.estado_reg in (''aprobado'')");
        }else if($this->objParam->getParametro('estado_cbte') == 'validado'){
            $this->objParam->addFiltro("incbte.estado_reg in (''validado'')");
        }else if($this->objParam->getParametro('estado_cbte') == 'borrador_elaborado'){
            $this->objParam->addFiltro("incbte.estado_reg in (''borrador'',''elaborado'')");
        }else if($this->objParam->getParametro('estado_cbte') == 'verificado_aprobado'){
            $this->objParam->addFiltro("incbte.estado_reg in (''verificado'',''aprobado'')");
        }
        //end(franklin.espinoza)  20/01/2022

        $this->objParam->addFiltro("incbte.id_clase_comprobante in (''6'',''7'')");
        if($this->objParam->getParametro('id_deptos')!=''){
            $this->objParam->addFiltro("incbte.id_depto in (".$this->objParam->getParametro('id_deptos').")");
        }
        if($this->objParam->getParametro('gestion')!=''){
            $this->objParam->addFiltro("incbte.fecha between ''01/01/".$this->objParam->getParametro('gestion')."''::date and ''31/12/".$this->objParam->getParametro('gestion')."''::date");
        }
        if($this->objParam->getParametro('tipo_comprobante') == 'normal'){
            $this->objParam->addFiltro("coalesce(tic.reversion,''no'') in (''no'')");
        }else if($this->objParam->getParametro('tipo_comprobante') == 'reversion'){
            $this->objParam->addFiltro("coalesce(tic.reversion,''no'') in (''si'')");
        }
        if($this->objParam->getParametro('estado_entrega') == 'borrador_elaborado'){
            $this->objParam->addFiltro("ent.estado in (''borrador'',''elaborado'') and ent.tipo in (''normal_una_cg'',''normal_mas_cg'',''regularizacion_una_cg'',''regularizacion_mas_cg'') and (ent.id_usuario_reg = ".$_SESSION["ss_id_usuario"]." or 612 = ".$_SESSION["ss_id_usuario"].")");
        }

        if($this->objParam->getParametro('id_int_comprobante')!= ''){
            $this->objParam->addFiltro("incbte.id_int_comprobante = ".$this->objParam->getParametro('id_int_comprobante'));
        }

        if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
            $this->objReporte = new Reporte($this->objParam,$this);
            $this->res = $this->objReporte->generarReporteListado('MODIntComprobante','listarIntComprobanteC21');
        } else{
            $this->objFunc=$this->create('MODIntComprobante');
            $this->res=$this->objFunc->listarIntComprobanteC21($this->objParam);
        }
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    //{develop: franklin.espinoza date: 14/01/2022, description: Insertar, Modificar Documentos C21}
    function insertarIntComprobanteC21(){
        $this->objFunc=$this->create('MODIntComprobante');
        if($this->objParam->insertar('id_int_comprobante')){
            $this->res=$this->objFunc->insertarIntComprobanteC21($this->objParam);
        } else{
            $this->res=$this->objFunc->modificarIntComprobanteC21($this->objParam);
        }
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

//ngll
	function contenidoLibroDiario(){

		//var_dump("esta llegando el filtro para poner",$this->objParam->getParametro('filtro_reporte'));
  
		//if ($this->objParam->getParametro('filtro_reporte') != '') {
		//	$this->objParam->addFiltro("((icbte.nro_tramite::varchar ILIKE ''%".$this->objParam->getParametro('filtro_reporte')."%'') OR (icbte.c31::varchar ILIKE ''%".$this->objParam->getParametro('filtro_reporte')."%'') OR (transa.glosa::varchar ILIKE ''%".$this->objParam->getParametro('filtro_reporte')."%''))");
		//}
  
		  $this->objFunc=$this->create('MODIntComprobante');
		  $cbteHeader = $this->objFunc->listarRepIntComprobanteDiario($this->objParam);
  
		  if($cbteHeader->getTipo() == 'EXITO'){
			  return $cbteHeader;
		  }
		  else{
			  $cbteHeader->imprimirRespuesta($cbteHeader->generarJson());
			  exit;
		  }
	  }
}

?>