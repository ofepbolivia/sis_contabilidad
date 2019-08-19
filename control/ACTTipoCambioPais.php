<?php
/**
*@package pXP
*@file gen-ACTTipoCambioPais.php
*@author  (ivaldivia)
*@date 07-08-2019 14:12:25
*@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
*/

class ACTTipoCambioPais extends ACTbase{

	function listarTipoCambioPais(){
		$this->objParam->defecto('ordenacion','id_tipo_cambio_pais');

		$this->objParam->defecto('dir_ordenacion','asc');

    /*Aumentando condicion para filtrar por pais seleccionado*/
		if($this->objParam->getParametro('id_moneda_pais') != ''){
            $this->objParam->addFiltro(" tcpa.id_moneda_pais = ".$this->objParam->getParametro('id_moneda_pais'));
        }
		/*********************************************************/




		if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
			$this->objReporte = new Reporte($this->objParam,$this);
			$this->res = $this->objReporte->generarReporteListado('MODTipoCambioPais','listarTipoCambioPais');
		} else{
			$this->objFunc=$this->create('MODTipoCambioPais');

			$this->res=$this->objFunc->listarTipoCambioPais($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}

	function insertarTipoCambioPais(){
		$this->objFunc=$this->create('MODTipoCambioPais');
		if($this->objParam->insertar('id_tipo_cambio_pais')){
			$this->res=$this->objFunc->insertarTipoCambioPais($this->objParam);
		} else{
			$this->res=$this->objFunc->modificarTipoCambioPais($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}

	function eliminarTipoCambioPais(){
			$this->objFunc=$this->create('MODTipoCambioPais');
		$this->res=$this->objFunc->eliminarTipoCambioPais($this->objParam);
		$this->res->imprimirRespuesta($this->res->generarJson());
	}

}

?>
