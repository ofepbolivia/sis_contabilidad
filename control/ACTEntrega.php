<?php
/**
 * @package pXP
 * @file gen-ACTEntrega.php
 * @author  (admin)
 * @date 17-11-2016 19:50:19
 * @description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
 */
require_once(dirname(__FILE__) . '/../../pxp/pxpReport/DataSource.php');
require_once(dirname(__FILE__) . '/../reportes/REntregaXls.php');

class ACTEntrega extends ACTbase
{

    function listarEntrega()
    {
        $this->objParam->defecto('ordenacion', 'id_entrega');
        $this->objParam->defecto('dir_ordenacion', 'asc');


        if ($this->objParam->getParametro('id_depto') != '') {
            $this->objParam->addFiltro("ent.id_depto_conta = " . $this->objParam->getParametro('id_depto'));
        }
        if ($this->objParam->getParametro('pes_estado') == 'EntregaConsulta') {
            $this->objParam->addFiltro("ent.estado  in (''vbconta'')");
        }

        //begin(franklin.espinoza) 20/08/2020
        if($this->objParam->getParametro('estado_entrega') == 'borrador'){
            $this->objParam->addFiltro("ent.estado in (''borrador'')");
        }else if($this->objParam->getParametro('estado_entrega') == 'elaborado'){
            $this->objParam->addFiltro("ent.estado in (''elaborado'')");
        }else if($this->objParam->getParametro('estado_entrega') == 'verificado'){
            $this->objParam->addFiltro("ent.estado in (''verificado'')");
        }else if($this->objParam->getParametro('estado_entrega') == 'aprobado'){
            $this->objParam->addFiltro("ent.estado in (''aprobado'')");
        }else if($this->objParam->getParametro('estado_entrega') == 'finalizado'){
            $this->objParam->addFiltro("ent.estado in (''finalizado'')");
        }else if($this->objParam->getParametro('estado_entrega') == 'borrador_elaborado'){
            $this->objParam->addFiltro("ent.estado in (''borrador'',''elaborado'')");
        }

        if($this->objParam->getParametro('tipo_entrega') == 'normal'){
            $this->objParam->addFiltro("coalesce(tic.reversion,''no'') in (''no'')");
        }else if($this->objParam->getParametro('tipo_entrega') == 'reversion'){
            $this->objParam->addFiltro("coalesce(tic.reversion,''no'') in (''si'')");
        }


        if ($this->objParam->getParametro('tipoReporte') == 'excel_grid' || $this->objParam->getParametro('tipoReporte') == 'pdf_grid') {
            $this->objReporte = new Reporte($this->objParam, $this);
            $this->res = $this->objReporte->generarReporteListado('MODEntrega', 'listarEntrega');
        } else {
            $this->objFunc = $this->create('MODEntrega');

            $this->res = $this->objFunc->listarEntrega($this->objParam);
        }
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function insertarEntrega()
    {
        $this->objFunc = $this->create('MODEntrega');
        if ($this->objParam->insertar('id_entrega')) {
            $this->res = $this->objFunc->insertarEntrega($this->objParam);
        } else {
            $this->res = $this->objFunc->modificarEntrega($this->objParam);
        }
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function eliminarEntrega()
    {
        $this->objFunc = $this->create('MODEntrega');
        $this->res = $this->objFunc->eliminarEntrega($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function crearEntrega()
    {
        $this->objFunc = $this->create('MODEntrega');
        $this->res = $this->objFunc->crearEntrega($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    //{develop:franklin.espinoza date:28/09/2020}
    function crearEntregaSigep(){
        $this->objFunc = $this->create('MODEntrega');
        $this->res = $this->objFunc->crearEntregaSigep($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function cambiarEstado()
    {
        $this->objFunc = $this->create('MODEntrega');
        $this->res = $this->objFunc->cambiarEstado($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function recuperarEntrega()
    {
        $this->objFunc = $this->create('MODEntrega');
        $cbteHeader = $this->objFunc->recuperarEntrega($this->objParam);
        if ($cbteHeader->getTipo() == 'EXITO') {
            return $cbteHeader;
        } else {
            $cbteHeader->imprimirRespuesta($cbteHeader->generarJson());
            exit;
        }
    }

    function reporteEntrega()
    {


        $nombreArchivo = uniqid(md5(session_id()) . 'Entrega') . '.xls';
        $dataSource = $this->recuperarEntrega();

        //parametros basicos
        $tamano = 'LETTER';
        $orientacion = 'L';
        $titulo = 'Consolidado';

        $this->objParam->addParametro('orientacion', $orientacion);
        $this->objParam->addParametro('tamano', $tamano);
        $this->objParam->addParametro('titulo_archivo', $titulo);
        $this->objParam->addParametro('nombre_archivo', $nombreArchivo);

        $reporte = new REntregaXls($this->objParam);
        $reporte->datosHeader($dataSource->getDatos(), $this->objParam->getParametro('id_entrega'));
        $reporte->generarReporte();


        $this->mensajeExito = new Mensaje();
        $this->mensajeExito->setMensaje('EXITO', 'Reporte.php', 'Reporte generado', 'Se generó con éxito el reporte: ' . $nombreArchivo, 'control');
        $this->mensajeExito->setArchivoGenerado($nombreArchivo);
        $this->mensajeExito->imprimirRespuesta($this->mensajeExito->generarJson());

    }

    function siguienteEstado()
    {
        $this->objFunc = $this->create('MODEntrega');

        $this->objParam->addParametro('id_funcionario_usu', $_SESSION["id_usuario_reg"]);

        $this->res = $this->objFunc->ListarSiguienteEstado($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function retrosederEstado()
    {
        $this->objFunc = $this->create('MODEntrega');
        $this->objParam->addParametro('id_funcionario_usu', $_SESSION["ss_id_funcionario"]);
        $this->res = $this->objFunc->ListarAnteriorEstado($this->objParam);

        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function listarDetalleComprobante()
    {
        $this->objParam->defecto('ordenacion', 'id_int_comprobante');
        $this->objParam->defecto('dir_ordenacion', 'asc');


//        if($this->objParam->getParametro('id_depto')!=''){
//            $this->objParam->addFiltro("ent.id_depto_conta = ".$this->objParam->getParametro('id_depto'));
//        }
        if ($this->objParam->getParametro('pes_estado') == 'EntregaConsulta') {
            $this->objParam->addFiltro("ent.estado  in (''vbconta'')");
        }

        if ($this->objParam->getParametro('desde') != '' && $this->objParam->getParametro('hasta') != '') {
            $this->objParam->addFiltro("(com.fecha::date  BETWEEN ''%" . $this->objParam->getParametro('desde') . "%''::date  and ''%" . $this->objParam->getParametro('hasta') . "%''::date)");
        }

        if ($this->objParam->getParametro('desde') != '' && $this->objParam->getParametro('hasta') == '') {
            $this->objParam->addFiltro("(com.fecha::date  >= ''%" . $this->objParam->getParametro('desde') . "%''::date)");
        }

        if ($this->objParam->getParametro('desde') == '' && $this->objParam->getParametro('hasta') != '') {
            $this->objParam->addFiltro("(com.fecha::date  <= ''%" . $this->objParam->getParametro('hasta') . "%''::date)");
        }

        if ($this->objParam->getParametro('id_depto_conta') != '') {
            $this->objParam->addFiltro("com.id_depto = " . $this->objParam->getParametro('id_depto_conta'));
        }
        if ($this->objParam->getParametro('id_clase_comprobante') != '') {
            $this->objParam->addFiltro("ccom.id_clase_comprobante = " . $this->objParam->getParametro('id_clase_comprobante'));
        }
        if ($this->objParam->getParametro('desc_tipo_relacion_comprobante')=="true" ){

            $this->objParam->addFiltro("(com.id_tipo_relacion_comprobante is Null or com.id_tipo_relacion_comprobante = 2)");
        }
//         para discriminar un dato segun su ID en el check
//        if ($this->objParam->getParametro('desc_tipo_relacion_comprobante')=="true") {
//            $this->objParam->addFiltro("com.id_tipo_relacion_comprobante <> 1 " );
//        }

        if ($this->objParam->getParametro('tipoReporte') == 'excel_grid' || $this->objParam->getParametro('tipoReporte') == 'pdf_grid') {
            $this->objReporte = new Reporte($this->objParam, $this);
            $this->res = $this->objReporte->generarReporteListado('MODEntrega', 'listarDetalleComprobante');
        } else {
            $this->objFunc = $this->create('MODEntrega');

            $this->res = $this->objFunc->listarDetalleComprobante($this->objParam);
        }
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    //{develop:franklin.espinoza date:20/10/2020}
    function validarComprobantesERP(){
        $this->objFunc = $this->create('MODEntrega');
        $this->res = $this->objFunc->validarComprobantesERP($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    //{develop:franklin.espinoza date:30/10/2020}
    function validarGrupoComprobantes(){
        $this->objFunc = $this->create('MODEntrega');
        $this->res = $this->objFunc->validarComprobantesERP($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    //{develop:franklin.espinoza date:30/10/2020}
    function desvalidarGrupoComprobantes(){
        $this->objFunc = $this->create('MODEntrega');
        $this->res = $this->objFunc->desvalidarGrupoComprobantes($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }
    
    //{develop:franklin.espinoza date:20/11/2020}
    function volcarEntrega(){
        $this->objFunc=$this->create('MODEntrega');
        $this->res=$this->objFunc->volcarEntrega($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    //{develop:franklin.espinoza date:20/11/2020}
    function clonarEntrega(){
        $this->objFunc=$this->create('MODEntrega');
        $this->res=$this->objFunc->clonarEntrega($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

}

?>