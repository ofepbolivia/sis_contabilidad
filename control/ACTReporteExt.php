<?php
/**
 * @package pXP
 * @file gen-ACTReporteExt.php
 * @author  Maylee Perez Pastor
 * @date 20-08-2020 15:57:09
 * @description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
 */
require_once(dirname(__FILE__) . '/../../pxp/pxpReport/DataSource.php');
require_once dirname(__FILE__) . '/../../pxp/lib/lib_reporte/ReportePDFFormulario.php';

require_once(dirname(__FILE__) . '/../reportes/RepDocDetalleGastos.php');

class ACTReporteExt extends ACTbase
{
    function reporteDetalleGastos()
    {

        $this->objFunc = $this->create('MODReporteExt');
        $this->res = $this->objFunc->reporteDetalleGastos($this->objParam);
        //var_dump( $this->res);exit;
        //obtener titulo del reporte
        $titulo = 'RepDocCompraVentaExt';

        //Genera el nombre del archivo (aleatorio + titulo)
        $nombreArchivo = uniqid(md5(session_id()) . $titulo);
        $nombreArchivo .= '.xls';
        $this->objParam->addParametro('nombre_archivo', $nombreArchivo);

        $this->objParam->addParametro('datos', $this->res->datos);
        //Instancia la clase de excel
        $this->objReporteFormato = new RepDocDetalleGastos($this->objParam);
//        var_dump('llegaaa',$this->res);exit;
        $this->objReporteFormato->generarDatos();
        $this->objReporteFormato->generarReporte();
        $this->mensajeExito = new Mensaje();
        $this->mensajeExito->setMensaje('EXITO', 'Reporte.php', 'Reporte generado',
            'Se generó con éxito el reporte: ' . $nombreArchivo, 'control');

        $this->mensajeExito->setArchivoGenerado($nombreArchivo);
        $this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());
    }

}
?>