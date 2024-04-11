<?php

class RComprobanteDiarioXls
{
    private $docexcel;
    private $objWriter;
    private $objParam;
    public $url_archivo;
    var $datos_detalle;
    var $datos_contenido;


    function __construct(CTParametro $objParam)
    {
        $this->objParam = $objParam;
        $this->url_archivo = "../../../reportes_generados/" . $this->objParam->getParametro('nombre_archivo');

        $cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
        $cacheSettings = array('memoryCacheSize' => '10MB');
        PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
        PHPExcel_Settings::setPdfRendererPath('/var/www/html/kerp/pxp/lib/tcpdf');

        $this->docexcel = new PHPExcel();
        $this->docexcel->getProperties()
            ->setTitle($this->objParam->getParametro('titulo_archivo'))
            ->setSubject($this->objParam->getParametro('titulo_archivo'))
            ->setDescription('Reporte "' . $this->objParam->getParametro('titulo_archivo') . '", generado por el framework OFEP')
            ->setCategory("Report File");
    }

    //function datosHeader ($contenido,$saldo_anterior,$cabecera) {
    function datosHeader($detalle, $contenido, $cabecera)
    {
        $this->datos_contenido = $contenido;
        $this->recueprar_cabecera = $cabecera;
        $this->datos_detalle = $detalle;
    }

    function imprimeDatos()
    {
        $datos = $this->datos_detalle;
        $this->desc = $datos[0]['fecha'];

    }

    function imprimeCabecera($gestion)
    {
        $this->docexcel->createSheet();
        $this->docexcel->getActiveSheet()->setTitle('Libro Diario');
        $this->docexcel->setActiveSheetIndex(0);

        $styleTitulos = array(
            'font' => array(
                'bold' => true,
                'size' => 18,
                'name' => 'Calibri'
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                    'rgb' => 'FFFFFF'
                )
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
        );
        $styleTitulos_principal = array(
            'font' => array(
                'bold' => true,
                'size' => 38,
                'name' => 'Calibri',
                'color' => array(
                    'rgb' => 'A0FCD8'
                ),
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
        );
        $styleTitulosSubCabezera = array(
            'font' => array(
                'bold' => true,
                'size' => 8,
                'name' => 'Calibri'
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                    'rgb' => 'FFFFFF'
                )
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
        );

        $bordes = array(
            'borders' => array(
                'left' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
                'right' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
                'bottom' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
                'top' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
            ),

        );
        $bordes_titulo_infe = array(
            'borders' => array(
                'bottom' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),

            ),

        );
        //titulos
        $logo_ = dirname(__FILE__) . '/../../lib' . $_SESSION['_DIR_LOGO']; //fRnk
        if (strpos($logo_, 'png') !== false)
            $gdImage = imagecreatefrompng($logo_);
        else
            $gdImage = imagecreatefromjpeg($logo_);
        // Add a drawing to the worksheetecho date('H:i:s') . " Add a drawing to the worksheet\n";
        $objDrawing = new PHPExcel_Worksheet_MemoryDrawing();
        $objDrawing->setImageResource($gdImage);
        $objDrawing->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_JPEG);
        $objDrawing->setMimeType(PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_DEFAULT);
        $objDrawing->setHeight(50);
        $objDrawing->setCoordinates('A1');
        $objDrawing->setWorksheet($this->docexcel->getActiveSheet());

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, 1, 'REPORTE LIBRO DIARIO');
        $this->docexcel->getActiveSheet()->mergeCells('A1:H1');  
        $this->docexcel->getActiveSheet()->getStyle('A1')->applyFromArray($styleTitulos);              

        $this->docexcel->getActiveSheet()->getStyle('A3:H3')->applyFromArray($bordes_titulo_infe);
        $this->docexcel->getActiveSheet()->mergeCells('A2:H2');
        $this->docexcel->getActiveSheet()->mergeCells('A3:H3');

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8, 1, 'Desde: ' . substr($this->objParam->getParametro('fecIni'), 0, 10));
        $this->docexcel->getActiveSheet()->getStyle('I1')->applyFromArray($styleTitulosSubCabezera);
        $this->docexcel->getActiveSheet()->getStyle('I1')->applyFromArray($bordes);

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8, 2, 'Hasta: ' . substr($this->objParam->getParametro('fecFin'), 0, 10));
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8, 3, 'Gestión: ' . $this->objParam->getParametro('anio_gestion'));

        $this->docexcel->getActiveSheet()->getStyle('A3:I3')->applyFromArray($styleTitulosSubCabezera);
        $this->docexcel->getActiveSheet()->getStyle('I2')->applyFromArray($styleTitulosSubCabezera);
        $this->docexcel->getActiveSheet()->getStyle('I2')->applyFromArray($bordes);
        $this->docexcel->getActiveSheet()->getStyle('I3')->applyFromArray($bordes);

        $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(0, 4, 'ID')->getColumnDimension('A')->setWidth(5);
        $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1, 4, 'Nro')->getColumnDimension('B')->setWidth(5);
        $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(2, 4, 'Nro Comprobante')->getColumnDimension('C')->setWidth(10);
        $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(3, 4, 'Nro Tramite')->getColumnDimension('D')->setWidth(15);
        $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(4, 4, 'Fecha')->getColumnDimension('E')->setWidth(20);
        $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(5, 4, 'Nro. Cta. Contable')->getColumnDimension('F')->setWidth(20);
        $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(6, 4, 'Descripción')->getColumnDimension('G')->setWidth(50);
        $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(7, 4, 'Debe')->getColumnDimension('H')->setWidth(15);
        $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(8, 4, 'Haber')->getColumnDimension('I')->setWidth(15);
    }

    function generarDatos() {
        $styleArrayGroup = array(
            'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb' => 'e0ebff')),
        ); 
        $styleDefault = array(
            //'alignment' => array('vertical'=> PHPExcel_Style_Alignment::VERTICAL_TOP,'wrap'=> TRUE),
        );         
        
        $datos = $this->datos_detalle;
        $gestion = $datos[0]['gestion'] ?? date('Y');
        $this->imprimeCabecera($gestion);

        $fila = 5;
        $contador = 0;
        $idReg = $datos[0]['id_int_comprobante'] ?? 0;
        $fill = false;
        foreach ($datos as $value) {
            if ($idReg !== $value['id_int_comprobante']) {
                $fill = !$fill;
                $contador = 1;
            } else {
                $contador++;
            }
            if ($fill) {
                $this->docexcel->getActiveSheet()->getStyle('A'.$fila.':I'.$fila)->applyFromArray($styleArrayGroup);
            } else {
                $this->docexcel->getActiveSheet()->getStyle('A'.$fila.':I'.$fila)->applyFromArray($styleDefault);
            }

            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $fila, $value['id_int_comprobante']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1, $fila, $contador);            
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2, $fila, $value['nro_cbte']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3, $fila, $value['nro_tramite']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4, $fila, date("Y-m-d", strtotime($value["fecha"])));
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5, $fila, $value['nro_cuenta']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6, $fila, $value['nombre_cuenta']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(7, $fila, $value['importe_debe']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8, $fila, $value['importe_haber']);

            
            $fila++;
            $idReg = $value['id_int_comprobante'] ?? 0;
        }
    }
    function generarReporte()    {
  
        $this->generarDatos();
        $this->docexcel->setActiveSheetIndex(0);
        $this->objWriter = PHPExcel_IOFactory::createWriter($this->docexcel, 'Excel5');
        $this->objWriter->save($this->url_archivo);
        //$pd = new PHPExcel_Writer_PDF_tcPDF($this->docexcel);
        //$pd->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
        //$pd->save($this->url_archivo);
    }
    function generarReportePDF()    {
  
        $this->generarDatos();
        
        $pd = new PHPExcel_Writer_PDF_tcPDF($this->docexcel);

        $pd->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
        $pd->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
        $pd->save($this->url_archivo);
    }    
}
?>
