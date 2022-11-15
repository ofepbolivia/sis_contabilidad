<?php
/**
*@package pXP
*@file gen-ACTIntBeneficiario.php
*@author  (admin)
*@date 27-10-2022 14:42:32
*@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
*/

class ACTIntBeneficiario extends ACTbase{    
			
	function listarIntBeneficiario(){
		$this->objParam->defecto('ordenacion','id_beneficiario');

		$this->objParam->defecto('dir_ordenacion','asc');
        
        if($this->objParam->getParametro('id_int_comprobante')!=''){
            
            $this->objParam->addFiltro("intbenef.id_int_comprobante = ".$this->objParam->getParametro('id_int_comprobante'));
                 
        }

		if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
			$this->objReporte = new Reporte($this->objParam,$this);
			$this->res = $this->objReporte->generarReporteListado('MODIntBeneficiario','listarIntBeneficiario');
		} else{
			$this->objFunc=$this->create('MODIntBeneficiario');
			
			$this->res=$this->objFunc->listarIntBeneficiario($this->objParam);
		}

		$temp = Array();
		$temp['importe'] = $this->res->extraData['total_importe'];
		$temp['tipo_reg'] = 'summary';
		$temp['id_beneficiario'] = 0;

		$this->res->total++;

		$this->res->addLastRecDatos($temp);
		
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
				
	function insertarIntBeneficiario(){
		$this->objFunc=$this->create('MODIntBeneficiario');	
		if($this->objParam->insertar('id_beneficiario')){
			$this->res=$this->objFunc->insertarIntBeneficiario($this->objParam);			
		} else{			
			$this->res=$this->objFunc->modificarIntBeneficiario($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
						
	function eliminarIntBeneficiario(){
			$this->objFunc=$this->create('MODIntBeneficiario');	
		$this->res=$this->objFunc->eliminarIntBeneficiario($this->objParam);
		$this->res->imprimirRespuesta($this->res->generarJson());
	}

	function listarIntBeneficiarioProvCombo(){
		$this->objParam->defecto('ordenacion','id_funcionario_beneficiario');

		$this->objParam->defecto('dir_ordenacion','asc');
		if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
			$this->objReporte = new Reporte($this->objParam,$this);
			$this->res = $this->objReporte->generarReporteListado('MODIntBeneficiario','listarIntBeneficiarioProvCombo');
		} else{
			$this->objFunc=$this->create('MODIntBeneficiario');
			
			$this->res=$this->objFunc->listarIntBeneficiarioProvCombo($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
			
}

?>