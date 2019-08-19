<?php
/**
*@package pXP
*@file gen-ACTMonedaPais.php
*@author  (ivaldivia)
*@date 07-08-2019 14:05:50
*@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
*/

class ACTMonedaPais extends ACTbase{

	function listarMonedaPais(){
		$this->objParam->defecto('ordenacion','id_moneda_pais');

		$this->objParam->defecto('dir_ordenacion','asc');

		/*Aumentando condicion para filtrar por pais seleccionado*/
		if($this->objParam->getParametro('id_lugar') != ''){
            $this->objParam->addFiltro(" monpa.id_lugar = ''".$this->objParam->getParametro('id_lugar')."''");
        }
		/*********************************************************/


		if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
			$this->objReporte = new Reporte($this->objParam,$this);
			$this->res = $this->objReporte->generarReporteListado('MODMonedaPais','listarMonedaPais');
		} else{
			$this->objFunc=$this->create('MODMonedaPais');

			$this->res=$this->objFunc->listarMonedaPais($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}

	function insertarMonedaPais(){
		$this->objFunc=$this->create('MODMonedaPais');
		if($this->objParam->insertar('id_moneda_pais')){
			$this->res=$this->objFunc->insertarMonedaPais($this->objParam);
		} else{
			$this->res=$this->objFunc->modificarMonedaPais($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}

	function eliminarMonedaPais(){
			$this->objFunc=$this->create('MODMonedaPais');
		$this->res=$this->objFunc->eliminarMonedaPais($this->objParam);
		$this->res->imprimirRespuesta($this->res->generarJson());
	}

}

?>
