<?php
/**
*@package pXP
*@file gen-ACTClaseComprobante.php
*@author  (admin)
*@date 27-05-2013 16:07:00
*@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
*/

class ACTClaseComprobante extends ACTbase{    
			
	function listarClaseComprobante(){

        $this->objParam->defecto('ordenacion','id_clase_comprobante');
        $this->objParam->defecto('dir_ordenacion','asc');
        
        if($this->objParam->getParametro('tipo')=='diario'){
            $this->objParam->addFiltro("ccom.id_clase_comprobante in (''3'',''4'')");
        }

        if($this->objParam->getParametro('tipo')=='pago'){
            $this->objParam->addFiltro("ccom.id_clase_comprobante in (''1'',''5'')");
        }

        if( $this->objParam->getParametro('clase') != '' ){
            $this->objParam->addFiltro("ccom.clase in (''".$this->objParam->getParametro('clase')."'')");
        }

		if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
			$this->objReporte = new Reporte($this->objParam,$this);
			$this->res = $this->objReporte->generarReporteListado('MODClaseComprobante','listarClaseComprobante');
		} else{
			$this->objFunc=$this->create('MODClaseComprobante');
			
			$this->res=$this->objFunc->listarClaseComprobante($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
    function listarClase($filtro){
        $this->objParam->defecto('ordenacion','id_clase_comprobante');
        $this->objParam->defecto('dir_ordenacion','asc');
        $this->objParam->addFiltro("id_clase_comprobante in (".$filtro.")");
        if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
            $this->objReporte = new Reporte($this->objParam,$this);
            $this->res = $this->objReporte->generarReporteListado('MODClaseComprobante','listarClaseComprobante');
        } else{
            $this->objFunc=$this->create('MODClaseComprobante');

            $this->res=$this->objFunc->listarClaseComprobante($this->objParam);
        }
        $this->res->imprimirRespuesta($this->res->generarJson());
    }
				
	function insertarClaseComprobante(){
		$this->objFunc=$this->create('MODClaseComprobante');	
		if($this->objParam->insertar('id_clase_comprobante')){
			$this->res=$this->objFunc->insertarClaseComprobante($this->objParam);			
		} else{			
			$this->res=$this->objFunc->modificarClaseComprobante($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
						
	function eliminarClaseComprobante(){
			$this->objFunc=$this->create('MODClaseComprobante');	
		$this->res=$this->objFunc->eliminarClaseComprobante($this->objParam);
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
			
}

?>