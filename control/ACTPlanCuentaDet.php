<?php
/**
*@package pXP
*@file gen-ACTPlanCuentaDet.php
*@author  (alan.felipez)
*@date 25-11-2019 22:17:20
*@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
*/

class ACTPlanCuentaDet extends ACTbase{    
			
	function listarPlanCuentaDet(){
		$this->objParam->defecto('ordenacion','id_plan_cuenta_det');

		$this->objParam->defecto('dir_ordenacion','asc');
		if($this->objParam->getParametro('id_plan_cuenta')!=''){
            $this->objParam->addFiltro('ipcd.id_plan_cuenta ='.$this->objParam->getParametro('id_plan_cuenta'));
        }
		if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
			$this->objReporte = new Reporte($this->objParam,$this);
			$this->res = $this->objReporte->generarReporteListado('MODPlanCuentaDet','listarPlanCuentaDet');
		} else{
			$this->objFunc=$this->create('MODPlanCuentaDet');
			
			$this->res=$this->objFunc->listarPlanCuentaDet($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
				
	function insertarPlanCuentaDet(){
		$this->objFunc=$this->create('MODPlanCuentaDet');	
		if($this->objParam->insertar('id_plan_cuenta_det')){
			$this->res=$this->objFunc->insertarPlanCuentaDet($this->objParam);			
		} else{			
			$this->res=$this->objFunc->modificarPlanCuentaDet($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
						
	function eliminarPlanCuentaDet(){
			$this->objFunc=$this->create('MODPlanCuentaDet');	
		$this->res=$this->objFunc->eliminarPlanCuentaDet($this->objParam);
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
    function generarPlanCuenta(){
        $this->objFunc=$this->create('MODPlanCuentaDet');
        $this->res=$this->objFunc->generarPlanCuenta($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

}

?>