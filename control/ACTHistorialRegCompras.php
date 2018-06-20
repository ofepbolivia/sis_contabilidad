<?php
/**
*@package pXP
*@file gen-ACTHistorialRegCompras.php
*@author  (franklin.espinoza)
*@date 07-06-2018 15:14:54
*@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
*/

class ACTHistorialRegCompras extends ACTbase{    
			
	function listarHistorialRegCompras(){
		$this->objParam->defecto('ordenacion','id_historial_reg_compras');

		$this->objParam->defecto('dir_ordenacion','asc');

        if ($this->objParam->getParametro('id_doc_compra_venta') != '') {
            $this->objParam->addFiltro(" hrc.id_doc_compra_venta = " . $this->objParam->getParametro('id_doc_compra_venta'));
        }

		if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
			$this->objReporte = new Reporte($this->objParam,$this);
			$this->res = $this->objReporte->generarReporteListado('MODHistorialRegCompras','listarHistorialRegCompras');
		} else{
			$this->objFunc=$this->create('MODHistorialRegCompras');
			
			$this->res=$this->objFunc->listarHistorialRegCompras($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
				
	function insertarHistorialRegCompras(){
		$this->objFunc=$this->create('MODHistorialRegCompras');	
		if($this->objParam->insertar('id_historial_reg_compras')){
			$this->res=$this->objFunc->insertarHistorialRegCompras($this->objParam);			
		} else{			
			$this->res=$this->objFunc->modificarHistorialRegCompras($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
						
	function eliminarHistorialRegCompras(){
			$this->objFunc=$this->create('MODHistorialRegCompras');	
		$this->res=$this->objFunc->eliminarHistorialRegCompras($this->objParam);
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
			
}

?>