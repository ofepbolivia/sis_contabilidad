<?php

//fRnk: reporte EXCEL Balance General - Estado de Recursos y Gastos APS
class RBalanceGeneralAPSXls
{
    private $docexcel;
    private $objWriter;
    private $objParam;
    private $url_archivo;
    private $total_ingreso = 0;
    private $total_egreso = 0;
    private $tipo_balance;
    private $titulo_inst;
    private $hasta;

    function __construct(CTParametro $objParam)
    {
        $this->objParam = $objParam;
        $this->titulo_inst = mb_strtoupper($_SESSION['_TITULO_SIS_LARGO'], 'UTF-8');
        $this->url_archivo = "../../../reportes_generados/" . $this->objParam->getParametro('nombre_archivo');
        set_time_limit(400);
        $cacheMethod = PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
        $cacheSettings = array('memoryCacheSize' => '10MB');
        PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
        PHPExcel_Shared_Font::setAutoSizeMethod(PHPExcel_Shared_Font::AUTOSIZE_METHOD_EXACT);
        $this->docexcel = new PHPExcel();
        $this->docexcel->getProperties()->setCreator($_SESSION['_TITULO_SIS_CORTO'])
            ->setLastModifiedBy($_SESSION['_TITULO_SIS_CORTO'])
            ->setTitle($this->objParam->getParametro('titulo_archivo'))
            ->setSubject($this->objParam->getParametro('titulo_archivo'));
        $this->docexcel->setActiveSheetIndex(0);
        $this->docexcel->getActiveSheet()->setTitle($this->objParam->getParametro('titulo_archivo'));
        $this->tipo_balance = $this->objParam->getParametro('tipo_balance');
        $this->hasta = $this->objParam->getParametro('hasta');
    }

    function imprimeDatos()
    {
        $sheet = $this->docexcel->getActiveSheet();
        $datos = $this->objParam->getParametro('datos');
        $ar = array();
        $ar_exptext = array();
        $subtotal = 0;
        $total_pasivo_activo = 0;
        $monto_sub = '';
        $cuenta_ant = $this->tipo_balance == 'resultado' ? '4' : '1';
        $total_n1 = '';
        $ar[] = array('', '', 'NOTAS', '', '');
        $i = 7;
        $row_bold = array();
        $row_cuentas = 1;
        foreach ($datos as $val) {
            if ($val['nivel'] == 2) {
                $monto_sub = empty($val['monto']) ? 0 : $val['monto'];
                $subtotal += $monto_sub;
                $monto_sub = number_format($monto_sub, 2, '.', ',');
            }
            $cuenta_act = explode('.', $val['nro_cuenta']);
            if (count($cuenta_act) > 0) {
                $this->definirTotales($val, $cuenta_act[0]);
                if ($cuenta_act[0] != $cuenta_ant) {
                    $i++;
                    $ar[] = array('', 'TOTAL ' . $total_n1, '', '', number_format($subtotal, 2, '.', ','));
                    $row_bold[] = $i;
                    if ($cuenta_ant == '2' || $cuenta_ant == '3') {
                        $total_pasivo_activo += $subtotal;
                    }
                    $subtotal = 0;

                    if ($cuenta_act[0] == '6' && $val['nivel'] == 1) {
                        $i++;
                        $ar[] = array('', 'TOTAL PASIVO Y PATRIMONIO', '', '', number_format($total_pasivo_activo, 2, '.', ','));
                        $row_bold[] = $i;
                        $row_cuentas = $i;
                        $ar[] = array('', '', '', '', '');
                        $ar[] = array($this->titulo_inst, '', '', '', '');
                        $ar[] = array('CONSOLIDADO', '', '', '', '');
                        $ar[] = array('CUENTAS DE ORDEN', '', '', '', '');
                        $ar[] = array('AL ' . $this->getFecha(implode("-", array_reverse(explode("/", $this->hasta)))), '', '', '', '');
                        $ar[] = array('(EXPRESADO EN BOLIVIANOS)', '', '', '', '');
                        $i += 6;
                    }
                }
                $cuenta_ant = $cuenta_act[0];
            }
            $monto_p = $val['nivel'] != 2 ? number_format($monto, 2, '.', ',') : '';
            if ($val['nivel'] == 1) {
                $monto_p = '';
                $total_n1 = $val['nombre_cuenta'];
            }
            $monto = empty($val['monto']) ? 0 : $val['monto'];
            $ar[] = array($val['nro_cuenta'], $val['nombre_cuenta'], '', $monto_p, $monto_sub);
            $monto_sub = '';
            $i++;
            if (strlen($val['nro_cuenta']) == 3) {
                $ar_exptext[] = array($i, $val['nro_cuenta']);
            }
        }
        $i++;
        $ar[] = array('', 'TOTAL ' . $total_n1, '', '', number_format($subtotal, 2, '.', ','));
        $row_bold[] = $i;
        if ($this->tipo_balance == 'resultado') {
            $i++;
            $row_bold[] = $i;
            $resultado = $this->total_ingreso - $this->total_egreso;
            $ar[] = array('', 'RESULTADO DEL EJERCICIO ', '', '', number_format($resultado, 2, '.', ','));
        }
        if ($this->tipo_balance != 'resultado' && $datos[0]['cuenta_orden']=='no'){ //fRnk: añadido para mostrar total cuando 6 y 7 no tiene movimiento
            $total_pasivo_activo += $subtotal;
            $ar[] = array('', 'TOTAL PASIVO Y PATRIMONIO', '', '', number_format($total_pasivo_activo, 2, '.', ','));
            $row_bold[] = $i+1;
        }
        $rows = count($ar) + 6;
        $sheet->getStyle('A1:E' . $rows)
            ->getNumberFormat()
            ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
        $sharedStyle1 = new PHPExcel_Style();
        $sharedStyle1->applyFromArray(
            array('fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('argb' => 'FFFFFFFF')//FFCCFFCC
            ),
                'borders' => array(
                    'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                    'right' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)
                )
            ));

        $sheet->setSharedStyle($sharedStyle1, "A1:E" . $rows);
        foreach ($row_bold as $item) {
            $sheet->getStyle("A" . $item . ":E" . $item)->getFont()->setBold(true);
        }
        if ($this->tipo_balance != 'resultado') {
            for ($j = 1; $j <= 6; $j++) {
                $sheet->mergeCells('A' . ($row_cuentas + $j) . ':E' . ($row_cuentas + $j));
                $sheet->getStyle('A' . ($row_cuentas + $j))->getFont()->setBold(true);
                $sheet->getStyle('A' . ($row_cuentas + $j))->getAlignment()->applyFromArray(
                    array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
                );
            }
        }
        $titulo = $this->tipo_balance == 'resultado' ? 'ESTADO DE RECURSOS Y GASTOS' : 'BALANCE GENERAL';
        $sheet->getColumnDimension("A")->setWidth(15);
        $sheet->getColumnDimension("B")->setWidth(80);
        $sheet->getColumnDimension("C")->setWidth(15);
        $sheet->getColumnDimension("D")->setWidth(15);
        $sheet->getColumnDimension("E")->setWidth(15);

        $sheet->setCellValue('A1', $this->titulo_inst)
            ->setCellValue('A2', 'CONSOLIDADO')
            ->setCellValue('A3', $titulo)
            ->setCellValue('A4', 'AL ' . $this->getFecha(implode("-", array_reverse(explode("/", $this->hasta)))))
            ->setCellValue('A5', '(EXPRESADO EN BOLIVIANOS)')
            ->getStyle("A1:B5")->getFont()->setBold(true);
        $sheet->mergeCells('A1:E1')->mergeCells('A2:E2')->mergeCells('A3:E3')->mergeCells('A4:E4')->mergeCells('A5:E5');
        $sheet->getStyle("A1:B5")->getAlignment()->applyFromArray(
            array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
        );
        $sheet->getStyle('A7:A' . $rows)
            ->getAlignment()
            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle('D1:E' . $rows)
            ->getAlignment()
            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $sheet->fromArray($ar, null, 'A7');
        foreach ($ar_exptext as $d) {
            $sheet->getcell('A' . $d[0])->setValueExplicit($d[1], PHPExcel_Cell_DataType::TYPE_STRING);
        }
    }

    function getFecha($fecha)
    {
        $dia = date("d", strtotime($fecha));
        $anio = date("Y", strtotime($fecha));
        $mes = array('ENERO', 'FEBRERO', 'MARZO', 'ABRIL', 'MAYO', 'JUNIO', 'JULIO', 'AGOSTO', 'SEPTIEMBRE', 'OCTUBRE', 'NOVIEMBRE', 'DICIEMBRE');
        $mes = $mes[(date('m', strtotime($fecha)) * 1) - 1];
        return $dia . ' DE ' . $mes . ' DE ' . $anio;
    }

    function definirTotales($val, $rcuenta)
    {
        if ($val["nivel"] == 1 && $rcuenta == '4') {
            $this->total_ingreso += $val['monto'];
        }
        if ($val["nivel"] == 1 && $rcuenta == '5') {
            $this->total_egreso += $val['monto'];
        }
    }


    function generarReporte()
    {
        $this->docexcel->setActiveSheetIndex(0);
        $this->objWriter = PHPExcel_IOFactory::createWriter($this->docexcel, 'Excel5');
        $this->objWriter->save($this->url_archivo);
    }

}

?>