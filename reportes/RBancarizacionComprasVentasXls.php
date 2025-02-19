<?php

//fRnk: nuevo reporte HR00528-2024
class RBancarizacionComprasVentasXls
{
    private $docexcel;
    private $objWriter;
    private $objParam;
    public $url_archivo;

    function __construct(CTParametro $objParam)
    {
        $this->objParam = $objParam;
        $this->url_archivo = "../../../reportes_generados/" . $this->objParam->getParametro('nombre_archivo');
        $cacheMethod = PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
        $cacheSettings = array('memoryCacheSize' => '10MB');
        PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
        $this->docexcel = new PHPExcel();
        $this->docexcel->getProperties()->setCreator($_SESSION['_TITULO_SIS_CORTO'])
            ->setLastModifiedBy($_SESSION['_TITULO_SIS_CORTO'])
            ->setTitle($this->objParam->getParametro('titulo_archivo'))
            ->setSubject($this->objParam->getParametro('titulo_archivo'))
            ->setDescription('Reporte "' . $this->objParam->getParametro('titulo_archivo'))
            ->setKeywords("office 2007 openxml php")
            ->setCategory("Report File");
        $this->docexcel->setActiveSheetIndex(0);
    }

    function imprimeDatos()
    {
        $this->docexcel->getActiveSheet()->setTitle('Bancarización ' . $this->objParam->getParametro('tipo'));
        $sheet = $this->docexcel->getActiveSheet();
        $datos = $this->objParam->getParametro('datos');
        $this->createSheet($sheet, $datos, $this->objParam->getParametro('tipo'));
    }

    function createSheet($sheet, $datos, $type)
    {
        $sharedStyle1 = new PHPExcel_Style();
        $sheet->setCellValue('A2', 'BANCARIZACIÓN ' . strtoupper($type));
        $first = 4;
        if ($type == 'Compras') {
            $sheet->setCellValue('A' . $first, 'Modalidad transacción')
                ->setCellValue('B' . $first, 'Fecha factura/fecha documento')
                ->setCellValue('C' . $first, 'Tipo de transacción')
                ->setCellValue('D' . $first, 'NIT/CI proveedor')
                ->setCellValue('E' . $first, 'Nombre/razón social proveedor')
                ->setCellValue('F' . $first, 'N° de factura/ N° documento')
                ->setCellValue('G' . $first, 'N° de contrato')
                ->setCellValue('H' . $first, 'Importe factura/importe documento')
                ->setCellValue('I' . $first, 'N° autorización factura/documento')
                ->setCellValue('J' . $first, 'N° de cuenta del documento de pago')
                ->setCellValue('K' . $first, 'Monto pagado en documento de pago')
                ->setCellValue('L' . $first, 'Monto Acumulado')
                ->setCellValue('M' . $first, 'NIT Entidad Financiera')
                ->setCellValue('N' . $first, 'N° documento de pago (N° transacción u operación)')
                ->setCellValue('O' . $first, 'Tipo de documento de pago')
                ->setCellValue('P' . $first, 'Fecha del documento de pago')
                ->getStyle('A' . $first . ':P' . $first)->getFont()->setBold(true);
            $sharedStyle1->applyFromArray(
                array('fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('argb' => 'FFFFFFFF')//FFCCFFCC
                ),
                    'borders' => array(
                        'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                        'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                        'right' => array('style' => PHPExcel_Style_Border::BORDER_THIN)
                    )
                ));
            $styleTitle = array(
                'font' => array(
                    'bold' => true,
                    'size' => 11,
                    'name' => 'Arial'
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                ),
            );
            $styleHeaderTable = array(
                'font' => array(
                    'bold' => true,
                    'color' => array(
                        'rgb' => 'ffffff'
                    )
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                ),
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array(
                        'rgb' => '2B579A'
                    )
                )
            );
            $ar = array();
            foreach ($datos as $row) {
                $ar[] = array($row['desc_modalidad_transaccion'],
                    implode('/', array_reverse(explode('-', $row['fecha_documento']))),
                    $row['desc_tipo_transaccion'],
                    $row['nit_ci'],
                    $row['razon'],
                    $row['num_documento'],
                    $row['num_contrato'],
                    $row['importe_documento'],
                    $row['autorizacion'],
                    $row['num_cuenta_pago'],
                    $row['monto_pagado'],
                    $row['monto_acumulado'],
                    $row['nit_entidad'],
                    $row['num_documento_pago'],
                    $row['desc_tipo_documento_pago'],
                    implode('/', array_reverse(explode('-', $row['fecha_de_pago']))));
            }
            $sheet->fromArray($ar, null, 'A' . ($first + 1));
            $sheet->getStyle('A1:A2')->applyFromArray($styleTitle);
            $sheet->getStyle('A' . $first . ':P' . ($first))->applyFromArray($styleHeaderTable);
        } else {
            $sheet->setCellValue('A' . $first, 'Modalidad de transacción')
                ->setCellValue('B' . $first, 'Fecha factura/documento')
                ->setCellValue('C' . $first, 'N° de factura/documento')
                ->setCellValue('D' . $first, 'Importe factura/documento')
                ->setCellValue('E' . $first, 'N° de contrato')
                ->setCellValue('F' . $first, 'N° autorización factura')
                ->setCellValue('G' . $first, 'NIT/CI cliente')
                ->setCellValue('H' . $first, 'Nombre o razón social cliente')
                ->setCellValue('I' . $first, 'N° de cuenta del documento de pago')
                ->setCellValue('J' . $first, 'Monto pagado en documento de pago')
                ->setCellValue('K' . $first, 'Monto acumulado de pagos parciales')
                ->setCellValue('L' . $first, 'NIT Entidad Financiera')
                ->setCellValue('M' . $first, 'N° de transacción u operación de pago')
                ->setCellValue('N' . $first, 'Tipo de documento de pago')
                ->setCellValue('O' . $first, 'Fecha del documento de pago')
                ->getStyle('A' . $first . ':O' . $first)->getFont()->setBold(true);
            $sharedStyle1->applyFromArray(
                array('fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('argb' => 'FFFFFFFF')//FFCCFFCC
                ),
                    'borders' => array(
                        'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                        'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                        'right' => array('style' => PHPExcel_Style_Border::BORDER_THIN)
                    )
                ));
            $styleTitle = array(
                'font' => array(
                    'bold' => true,
                    'size' => 11,
                    'name' => 'Arial'
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                ),
            );
            $styleHeaderTable = array(
                'font' => array(
                    'bold' => true,
                    'color' => array(
                        'rgb' => 'ffffff'
                    )
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                ),
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array(
                        'rgb' => '2B579A'
                    )
                )
            );
            $ar = array();
            foreach ($datos as $row) {
                $ar[] = array($row['desc_modalidad_transaccion'],
                    implode('/', array_reverse(explode('-', $row['fecha_documento']))),
                    $row['num_documento'],
                    $row['importe_documento'],
                    $row['num_contrato'],
                    $row['autorizacion'],
                    $row['nit_ci'],
                    $row['razon'],
                    $row['num_cuenta_pago'],
                    $row['monto_pagado'],
                    $row['monto_acumulado'],
                    $row['nit_entidad'],
                    $row['num_documento_pago'],
                    $row['desc_tipo_documento_pago'],
                    implode('/', array_reverse(explode('-', $row['fecha_de_pago']))));
            }
            $sheet->fromArray($ar, null, 'A' . ($first + 1));
            $sheet->getStyle('A1:A2')->applyFromArray($styleTitle);
            $sheet->getStyle('A' . $first . ':O' . ($first))->applyFromArray($styleHeaderTable);
        }
    }

    function generarReporte()
    {
        $this->docexcel->setActiveSheetIndex(0);
        $this->imprimeDatos();
        $this->objWriter = PHPExcel_IOFactory::createWriter($this->docexcel, 'Excel5');
        $this->objWriter->save($this->url_archivo);
    }
}

?>