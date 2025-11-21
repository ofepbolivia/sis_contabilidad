<?php

//fRnk: nuevo reporte HR00586-2025
class RBancarizacionComprasVentasExpSINXls
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
            $sheet->setCellValue('A' . $first, 'N°')
                ->setCellValue('B' . $first, 'TIPO DE TRANSACCIÓN')
                ->setCellValue('C' . $first, 'FORMA DE PAGO')
                ->setCellValue('D' . $first, 'NIT/CI PROVEEDOR')
                ->setCellValue('E' . $first, 'COMPLEMENTO')
                ->setCellValue('F' . $first, 'RAZÓN SOCIAL PROVEEDOR')
                ->setCellValue('G' . $first, 'CÓDIGO DE AUTORIZACIÓN')
                ->setCellValue('H' . $first, 'NÚMERO FACTURA')
                ->setCellValue('I' . $first, 'TIPO DE DOCUMENTO DE RESPALDO')
                ->setCellValue('J' . $first, 'NÚMERO DE DOCUMENTO DE RESPALDO')
                ->setCellValue('K' . $first, 'FECHA DE LA FACTURA O DOCUMENTO DE RESPALDO')
                ->setCellValue('L' . $first, 'MONTO FACTURADO COMPRA (BS)')
                ->setCellValue('M' . $first, 'NÚMERO DE CONTRATO O ACUERDO')
                ->setCellValue('N' . $first, 'TIPO DE DOCUMENTO DE PAGO')
                ->setCellValue('O' . $first, 'FECHA DEL DOCUMENTO DE LA TRANSACCIÓN FINANCIERA')
                ->setCellValue('P' . $first, 'NÚMERO DE CUENTA DEL COMPRADOR (DEBITO)')
                ->setCellValue('Q' . $first, 'NÚMERO DE CUENTA DEL PROVEEDOR/VENDEDOR (ABONO)')
                ->setCellValue('R' . $first, 'NIT DE LA ENTIDAD FINANCIERA DE DÉBITO')
                ->setCellValue('S' . $first, 'NIT DE LA ENTIDAD FINANCIERA DE ABONO')
                ->setCellValue('T' . $first, 'NÚMERO DE TRANSACCIÓN O NÚMERO DE OPERACIÓN DEL PAGO ABONADO')
                ->setCellValue('U' . $first, 'MONTO PAGADO')
                ->getStyle('A' . $first . ':U' . $first)->getFont()->setBold(true);
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
            $i = 1;
            foreach ($datos as $row) {
                $ar[] = array($i,
                    $row['tipo_transaccion'],
                    $row['modalidad_transaccion'],
                    $row['nit_ci'],
                    $row['complemento'],
                    $row['razon'],
                    $row['autorizacion'],
                    $row['num_documento'],
                    $row['tipo_documento_respaldo'],
                    $row['nro_documento_respaldo'],
                    implode('/', array_reverse(explode('-', $row['fecha_documento']))),
                    $row['importe_documento'],
                    $row['num_contrato'],
                    $row['tipo_documento_pago'],
                    implode('/', array_reverse(explode('-', $row['fecha_de_pago']))),
                    $row['num_cuenta_compra'],//$row['id_cuenta_bancaria'],
                    $row['num_cuenta_pago'],
                    $row['nit_entidad'],
                    $row['nit_financiera_abono'],
                    $row['num_documento_pago'],
                    $row['monto_pagado'],
                );
                $i++;
            }
            $sheet->fromArray($ar, null, 'A' . ($first + 1));
            $sheet->getStyle('A1:A2')->applyFromArray($styleTitle);
            $sheet->getStyle('A' . $first . ':U' . ($first))->applyFromArray($styleHeaderTable);
        } else {
            $sheet->setCellValue('A' . $first, 'N°')
                ->setCellValue('B' . $first, 'TIPO DE TRANSACCIÓN')
                ->setCellValue('C' . $first, 'FORMA DE PAGO')
                ->setCellValue('D' . $first, 'NIT / CI CLIENTE')
                ->setCellValue('E' . $first, 'COMPLEMENTO')
                ->setCellValue('F' . $first, 'NOMBRE O RAZÓN SOCIAL')
                ->setCellValue('G' . $first, 'CÓDIGO DE AUTORIZACIÓN')
                ->setCellValue('H' . $first, 'NÚMERO DE LA FACTURA')
                ->setCellValue('I' . $first, 'TIPO DE DOCUMENTO DE RESPALDO')
                ->setCellValue('J' . $first, 'NÚMERO DE DOCUMENTO DE RESPALDO')
                ->setCellValue('K' . $first, 'FECHA DE LA FACTURA O DOCUMENTO DE RESPALDO')
                ->setCellValue('L' . $first, 'MONTO FACTURADO VENTA (BS)')
                ->setCellValue('M' . $first, 'NÚMERO DE CONTRATO O ACUERDO')
                ->setCellValue('N' . $first, 'TIPO DE DOCUMENTO DE PAGO')
                ->setCellValue('O' . $first, 'FECHA DEL DOCUMENTO DE PAGO')
                ->setCellValue('P' . $first, 'NÚMERO DE CUENTA DEL PROVEEDOR/VENDEDOR (ABONO)')
                ->setCellValue('Q' . $first, 'NIT DE LA ENTIDAD FINANCIERA DE ABONO')
                ->setCellValue('R' . $first, 'NÚMERO DE TRANSACCIÓN O NÚMERO DE OPERACIÓN DEL PAGO ABONADO')
                ->setCellValue('S' . $first, 'MONTO PERCIBIDO')
                ->getStyle('A' . $first . ':S' . $first)->getFont()->setBold(true);
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
            $i = 1;
            foreach ($datos as $row) {
                $ar[] = array($i,
                    $row['tipo_transaccion'],
                    $row['modalidad_transaccion'],
                    $row['nit_ci'],
                    $row['complemento'],
                    $row['razon'],
                    $row['autorizacion'],
                    $row['num_documento'],
                    $row['tipo_documento_respaldo'],
                    $row['nro_documento_respaldo'],
                    implode('/', array_reverse(explode('-', $row['fecha_documento']))),
                    $row['importe_documento'],
                    $row['num_contrato'],
                    $row['tipo_documento_pago'],
                    implode('/', array_reverse(explode('-', $row['fecha_de_pago']))),
                    $row['num_cuenta_pago'],
                    $row['nit_financiera_abono'],
                    $row['num_documento_pago'],
                    $row['monto_pagado'],
                );
                $i++;
            }
            $sheet->fromArray($ar, null, 'A' . ($first + 1));
            $sheet->getStyle('A1:A2')->applyFromArray($styleTitle);
            $sheet->getStyle('A' . $first . ':S' . ($first))->applyFromArray($styleHeaderTable);
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