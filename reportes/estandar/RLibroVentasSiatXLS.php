<?php
/*set_time_limit(0);//avoid timeout
ini_set('memory_limit','-1');*/
class RLibroVentasSiatXLS
{
    private $docexcel;
    private $objWriter;
    private $numero;
    private $equivalencias=array();
    private $objParam;
    var $datos_detalle;
    var $datos_titulo;
    public  $url_archivo;
    function __construct(CTParametro $objParam)
    {
        $this->objParam = $objParam;
        $this->url_archivo = "../../../reportes_generados/".$this->objParam->getParametro('nombre_archivo');
        //ini_set('memory_limit','512M');
        //set_time_limit(420);
        $cacheMethod = PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
        $cacheSettings = array('memoryCacheSize'  => '32MB');
        PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);

        $this->docexcel = new PHPExcel();
        $this->docexcel->getProperties()->setCreator("PXP")
            ->setLastModifiedBy("PXP")
            ->setTitle($this->objParam->getParametro('nombre_archivo'))
            ->setSubject($this->objParam->getParametro('nombre_archivo'))
            ->setDescription('Reporte "'.$this->objParam->getParametro('nombre_archivo').'", generado por el framework PXP')
            ->setKeywords("office 2007 openxml php")
            ->setCategory("Report File");


        $this->equivalencias=array( 0=>'A',1=>'B',2=>'C',3=>'D',4=>'E',5=>'F',6=>'G',7=>'H',8=>'I',
            9=>'J',10=>'K',11=>'L',12=>'M',13=>'N',14=>'O',15=>'P',16=>'Q',17=>'R',
            18=>'S',19=>'T',20=>'U',21=>'V',22=>'W',23=>'X',24=>'Y',25=>'Z',
            26=>'AA',27=>'AB',28=>'AC',29=>'AD',30=>'AE',31=>'AF',32=>'AG',33=>'AH',
            34=>'AI',35=>'AJ',36=>'AK',37=>'AL',38=>'AM',39=>'AN',40=>'AO',41=>'AP',
            42=>'AQ',43=>'AR',44=>'AS',45=>'AT',46=>'AU',47=>'AV',48=>'AW',49=>'AX',
            50=>'AY',51=>'AZ',
            52=>'BA',53=>'BB',54=>'BC',55=>'BD',56=>'BE',57=>'BF',58=>'BG',59=>'BH',
            60=>'BI',61=>'BJ',62=>'BK',63=>'BL',64=>'BM',65=>'BN',66=>'BO',67=>'BP',
            68=>'BQ',69=>'BR',70=>'BS',71=>'BT',72=>'BU',73=>'BV',74=>'BW',75=>'BX',
            76=>'BY',77=>'BZ');

    }

    public function addHoja($name,$index){

        $this->docexcel->createSheet($index)->setTitle($name);
        $this->docexcel->setActiveSheetIndex($index);
        return $this->docexcel;
    }

    function array_sort_by(&$arrIni, $col, $order = SORT_ASC){
        $arrAux = array();
        foreach ($arrIni as $key=> $row)
        {
            $arrAux[$key] = is_object($row) ? $arrAux[$key] = $row->$col : $row[$col];
            $arrAux[$key] = strtolower($arrAux[$key]);
        }
        array_multisort($arrAux, $order, $arrIni);
    }

    function hiddenString($str, $start = 1, $end = 1){
        $len = strlen($str);
        return substr($str, 0, $start) . str_repeat('X', $len - ($start + $end)) . substr($str, $len - $end, $end);
    }

    function imprimeDatos(){


        $styleTitulos = array(
            'font'  => array(
                'bold'  => true,
                'size'  => 9,
                'name'  => 'Arial',
                'color' => array(
                    'rgb' => '000000'
                )
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'ffffff')
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_NONE
                )
            )
        );

        $styleTitulos1 = array(
            'font'  => array(
                'bold'  => true,
                'size'  => 9,
                'name'  => 'Arial',
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
                    'rgb' => '4682b4'
                )
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_NONE
                )
            ));

        $styleLeft = array(
            'font'  => array(
                'bold'  => true,
                'size'  => 9,
                'name'  => 'Arial',
                'color' => array(
                    'rgb' => '000000'
                )
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'ffffff')
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_NONE
                )
            )
        );

        $this->styleVacio = array(
            'font'  => array(
                'bold'  => true,
                'size'  => 8,
                'name'  => 'Arial'
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                    'rgb' => 'FA8072'
                )
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            )
        );


        $datos = $this->objParam->getParametro('datos');//print_r($datos);exit;
        $dataEntidad = $this->objParam->getParametro('dataEntidad');
        $dataPeriodo = $this->objParam->getParametro('dataPeriodo');
        $filtro_sql = $this->objParam->getParametro('filtro_sql');



        $fecha_desde = $this->objParam->getParametro('fecha_ini');
        $fecha_hasta = $this->objParam->getParametro('fecha_fin');

        $fecha = date('d/m/Y');
        $numberFormat = '#,##0.00';

        $index = 0;

        /*PAGOS DE ATC*/
        $this->addHoja('LIBRO VENTAS',$index);

        $color_pestana = array('ff0000','1100ff','55ff00','3ba3ff','ff4747','697dff','78edff','ba8cff',
            'ff80bb','ff792b','ffff5e','52ff97','bae3ff','ffaf9c','bfffc6','b370ff','ffa8b4','7583ff','9aff17','ff30c8');

        //$this->docexcel->getActiveSheet()->freezePaneByColumnAndRow(0,7);
        $this->docexcel->getActiveSheet()->freezePaneByColumnAndRow(0,2);
        $this->docexcel->getActiveSheet()->getTabColor()->setRGB($color_pestana[$index]);

        $this->docexcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
        $this->docexcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('H')->setWidth(35);
        $this->docexcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);

        $this->docexcel->getActiveSheet()->getColumnDimension('K')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('L')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('M')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('N')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('O')->setWidth(30);
        $this->docexcel->getActiveSheet()->getColumnDimension('P')->setWidth(25);
        $this->docexcel->getActiveSheet()->getColumnDimension('Q')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('R')->setWidth(30);
        $this->docexcel->getActiveSheet()->getColumnDimension('S')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('T')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('U')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('V')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('W')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('X')->setWidth(15);

        /*logo*/
        /*$objDrawing = new PHPExcel_Worksheet_Drawing();
        $objDrawing->setName('BoA ERP');
        $objDrawing->setDescription('BoA ERP');
        $objDrawing->setPath('../../lib/imagenes/logos/logo.jpg');
        $objDrawing->setCoordinates('A1');
        $objDrawing->setOffsetX(0);
        $objDrawing->setOffsetY(0);
        $objDrawing->setWidth(105);
        $objDrawing->setHeight(75);
        $objDrawing->setWorksheet($this->docexcel->getActiveSheet());*/
        /*logo*/


        /*$this->docexcel->getActiveSheet()->getStyle('A1:X4')->applyFromArray($styleTitulos);

        $this->docexcel->getActiveSheet()->getStyle('A1:X2')->getAlignment()->setWrapText(true);
        $this->docexcel->getActiveSheet()->mergeCells('A1:W2');
        $this->docexcel->getActiveSheet()->setCellValue('A1',"LIBRO DE VENTAS ESTANDAR\n (Expresado en Bolivianos)");

        $this->docexcel->getActiveSheet()->getStyle('A3:X4')->getAlignment()->setWrapText(true);


        $this->docexcel->getActiveSheet()->getStyle('F3:G3')->applyFromArray($styleLeft);
        $this->docexcel->getActiveSheet()->mergeCells('F3:G3');
        $this->docexcel->getActiveSheet()->getStyle('H3:L3')->applyFromArray($styleLeft);
        $this->docexcel->getActiveSheet()->mergeCells('H3:L3');

        $this->docexcel->getActiveSheet()->getStyle('O3:S3')->applyFromArray($styleLeft);
        $this->docexcel->getActiveSheet()->getStyle('O4:S4')->applyFromArray($styleLeft);
        $this->docexcel->getActiveSheet()->getStyle('N3:N3')->applyFromArray($styleLeft);
        $this->docexcel->getActiveSheet()->getStyle('N4:N4')->applyFromArray($styleLeft);
        $this->docexcel->getActiveSheet()->mergeCells('O3:S3');
        $this->docexcel->getActiveSheet()->mergeCells('O4:S4');
        if ($filtro_sql == 'fechas') {
            $this->docexcel->getActiveSheet()->setCellValue('F3', 'DESDE: ');
            $this->docexcel->getActiveSheet()->setCellValue('H3', $fecha_desde);
            $this->docexcel->getActiveSheet()->setCellValue('N3', 'HASTA: ');
            $this->docexcel->getActiveSheet()->setCellValue('O3', $fecha_hasta);
        }else{
            $this->docexcel->getActiveSheet()->setCellValue('F3', 'AÑO: ');
            $this->docexcel->getActiveSheet()->setCellValue('H3', $dataPeriodo['gestion']);
            $this->docexcel->getActiveSheet()->setCellValue('N3', 'MES: ');
            $this->docexcel->getActiveSheet()->setCellValue('O3', $dataPeriodo['literal_periodo']);
        }

        $this->docexcel->getActiveSheet()->getStyle('F4:G4')->applyFromArray($styleLeft);
        $this->docexcel->getActiveSheet()->mergeCells('F4:G4');
        $this->docexcel->getActiveSheet()->getStyle('H4:L4')->applyFromArray($styleLeft);
        $this->docexcel->getActiveSheet()->mergeCells('H4:L4');
        $this->docexcel->getActiveSheet()->setCellValue('F4','NOMBRE O RAZÓN SOCIAL: ');
        $this->docexcel->getActiveSheet()->setCellValue('H4',$dataEntidad['nombre'].' - ('.$dataEntidad['direccion_matriz'].')');

        $this->docexcel->getActiveSheet()->setCellValue('N4','NIT: ');
        $this->docexcel->getActiveSheet()->setCellValue('O4',$dataEntidad['nit']);

        $this->docexcel->getActiveSheet()->setCellValue('X1', 'Fecha');
        $this->docexcel->getActiveSheet()->setCellValue('X2', $fecha);*/

        $row = 1;
        $this->docexcel->getActiveSheet()->getStyle('A'.$row.':X'.$row)->applyFromArray($styleTitulos1);

        $this->docexcel->getActiveSheet()->mergeCells('A'.$row.':A'.$row);
        $this->docexcel->getActiveSheet()->mergeCells('B'.$row.':B'.$row);
        $this->docexcel->getActiveSheet()->mergeCells('C'.$row.':C'.$row);
        $this->docexcel->getActiveSheet()->mergeCells('D'.$row.':D'.$row);
        $this->docexcel->getActiveSheet()->mergeCells('E'.$row.':E'.$row);
        $this->docexcel->getActiveSheet()->mergeCells('F'.$row.':F'.$row);
        $this->docexcel->getActiveSheet()->mergeCells('G'.$row.':G'.$row);
        $this->docexcel->getActiveSheet()->mergeCells('H'.$row.':H'.$row);
        $this->docexcel->getActiveSheet()->mergeCells('I'.$row.':I'.$row);
        $this->docexcel->getActiveSheet()->mergeCells('J'.$row.':J'.$row);
        $this->docexcel->getActiveSheet()->mergeCells('K'.$row.':K'.$row);
        $this->docexcel->getActiveSheet()->mergeCells('L'.$row.':L'.$row);
        $this->docexcel->getActiveSheet()->mergeCells('M'.$row.':M'.$row);
        $this->docexcel->getActiveSheet()->mergeCells('N'.$row.':N'.$row);
        $this->docexcel->getActiveSheet()->mergeCells('O'.$row.':O'.$row);
        $this->docexcel->getActiveSheet()->mergeCells('P'.$row.':P'.$row);
        $this->docexcel->getActiveSheet()->mergeCells('Q'.$row.':Q'.$row);
        $this->docexcel->getActiveSheet()->mergeCells('R'.$row.':R'.$row);
        $this->docexcel->getActiveSheet()->mergeCells('S'.$row.':S'.$row);
        $this->docexcel->getActiveSheet()->mergeCells('T'.$row.':T'.$row);
        $this->docexcel->getActiveSheet()->mergeCells('U'.$row.':U'.$row);
        $this->docexcel->getActiveSheet()->mergeCells('V'.$row.':V'.$row);
        $this->docexcel->getActiveSheet()->mergeCells('W'.$row.':W'.$row);
        $this->docexcel->getActiveSheet()->mergeCells('X'.$row.':X'.$row);

        $this->docexcel->getActiveSheet()->getStyle('A'.$row.':X'.$row)->getAlignment()->setWrapText(true);

        $this->docexcel->getActiveSheet()->setCellValue('A'.$row,'N°');
        $this->docexcel->getActiveSheet()->setCellValue('B'.$row,'ESPECIFICACIÓN');
        $this->docexcel->getActiveSheet()->setCellValue('C'.$row,"FECHA DE \nLA FACTURA");
        $this->docexcel->getActiveSheet()->setCellValue('D'.$row,"N° DE \nLA FACTURA");
        $this->docexcel->getActiveSheet()->setCellValue('E'.$row,"CODIGO DE \nAUTORIZACION");
        $this->docexcel->getActiveSheet()->setCellValue('F'.$row,"NIT / CI CLIENTE");
        $this->docexcel->getActiveSheet()->setCellValue('G'.$row,"COMPLEMENTO");
        $this->docexcel->getActiveSheet()->setCellValue('H'.$row,"NOMBRE O RAZÓN SOCIAL");
        $this->docexcel->getActiveSheet()->setCellValue('I'.$row,"IMPORTE TOTAL \n VENTA");

        $this->docexcel->getActiveSheet()->setCellValue('J'.$row,"IMPORTE ICE");
        $this->docexcel->getActiveSheet()->setCellValue('K'.$row,"IMPORTE IEHD");
        $this->docexcel->getActiveSheet()->setCellValue('L'.$row,"IMPORTE IPJ");
        $this->docexcel->getActiveSheet()->setCellValue('M'.$row,"TASAS");
        $this->docexcel->getActiveSheet()->setCellValue('N'.$row,"OTRO NO SUJETO \n AL IVA");
        $this->docexcel->getActiveSheet()->setCellValue('O'.$row,"EXPORTACIONES Y\n OPERACIONES EXENTAS");
        $this->docexcel->getActiveSheet()->setCellValue('P'.$row,"VENTAS GRAVADAS\n A TASA CERO");
        $this->docexcel->getActiveSheet()->setCellValue('Q'.$row,"SUBTOTAL");

        $this->docexcel->getActiveSheet()->setCellValue('R'.$row,"DESCUENTOS, BONIFICACIONES Y\n REBAJAS SUJETAS AL IVA");
        $this->docexcel->getActiveSheet()->setCellValue('S'.$row,"IMPORTE GIFT CARD");
        $this->docexcel->getActiveSheet()->setCellValue('T'.$row,"IMPORTE BASE PARA\n DÉBITO FISCAL");
        $this->docexcel->getActiveSheet()->setCellValue('U'.$row,"DEBITO FISCAL");
        $this->docexcel->getActiveSheet()->setCellValue('V'.$row,"ESTADO");
        $this->docexcel->getActiveSheet()->setCellValue('W'.$row,"CÓDIGO DE CONTROL");
        $this->docexcel->getActiveSheet()->setCellValue('X'.$row,"TIPO DE VENTA");


        $fila = 2;

        $color_cell = array('b4c6e7','d9e1f2','ffc7ce','9bbb59');

        $monto_pagado = 0;
        $fila_total = $fila;
        $flag_left = true;
        $index_color = 0;

        $point_sale = '';
        $index_total = 0;
        $currency = '';
        $mount_admin = 0; $total_pagado = 0; $total_vendido = 0;
        $establishment_code = '';
        $payment_ammount = 0;

        $fila_contador = 1;
        //DateTime::createFromFormat('M j Y g:i:s:a', $rec->PaymentDate)->format('d/m/Y')
        foreach ($datos as $key => $rec){
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $fila, $key+1);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1, $fila, '2');
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2, $fila, DateTime::createFromFormat('Y-m-d', $rec['fecha_factura'])->format('d/m/Y'));
            //$this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3, $fila, $rec['nro_factura']);
            $this->docexcel->getActiveSheet()->setCellValueExplicitByColumnAndRow(3, $fila, trim($rec['nro_factura']), PHPExcel_Cell_DataType::TYPE_NUMERIC);
            //$this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4, $fila, $rec['nro_autorizacion']);
            $this->docexcel->getActiveSheet()->setCellValueExplicitByColumnAndRow(4, $fila, trim($rec['nro_autorizacion']), PHPExcel_Cell_DataType::TYPE_NUMERIC);//TYPE_STRING
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5, $fila, $rec['nit_ci_cli']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6, $fila, $rec['complemento_nit']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(7, $fila, $rec['razon_social_cli']);

            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8, $fila, $rec['importe_total_venta']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(9, $fila, $rec['importe_ice']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(10, $fila, $rec['importe_iehd']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(11, $fila, $rec['importe_ipj']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(12, $fila, $rec['importe_otros_no_suj_iva']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(13, $fila, $rec['otros_no_sujetos_al_iva']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(14, $fila, $rec['exportacion_excentas']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(15, $fila, $rec['ventas_tasa_cero']);
            $subtotal = $rec['importe_total_venta']-$rec['importe_ice']-$rec['importe_iehd']-$rec['importe_ipj']-$rec['importe_otros_no_suj_iva']-
                $rec['otros_no_sujetos_al_iva']-$rec['exportacion_excentas']-$rec['ventas_tasa_cero'];
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(16, $fila, $subtotal);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(17, $fila, $rec['descuento_rebaja_suj_iva']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(18, $fila, $rec['importe_gift_card']);
            $importe_base_df = $subtotal - $rec['descuento_rebaja_suj_iva']  - $rec['importe_gift_card'];
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(19, $fila, $importe_base_df);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(20, $fila, $importe_base_df*0.13);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(21, $fila, $rec['estado']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(22, $fila, $rec['codigo_control']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(23, $fila, $rec['tipo_de_venta']);
            $fila++;
            //$fila_contador++;
        }
//
//        $this->docexcel->getActiveSheet()->setCellValue('A' . $fila, 'TOTAL MONEDA: ' . $currency);
//        $this->docexcel->getActiveSheet()->mergeCells('A'.$fila.':G'.$fila);
//        $this->docexcel->getActiveSheet()->getStyle('A'.$fila.':G'.$fila)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//        $this->docexcel->getActiveSheet()->getStyle('A'.$fila.':J'.$fila)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB($color_cell[2]);
//        $this->docexcel->getActiveSheet()->getStyle('A'.$fila.':J'.$fila)->getFont()->setBold(true);
//
//        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(9, $fila, '=SUM(J'.$index_total.':J'.($fila-1).')');
        //FIN PAGO ATC
    }

    function obtenerFechaEnLetra($fecha){
        setlocale(LC_ALL,"es_ES@euro","es_ES","esp");
        $dia= date("d", strtotime($fecha));
        $anno = date("Y", strtotime($fecha));
        // var_dump()
        $mes = array('Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');
        $mes = $mes[(date('m', strtotime($fecha))*1)-1];
        return $dia.' de '.$mes.' del '.$anno;
    }
    function generarReporte(){
        //$this->imprimeDatos();
        $this->docexcel->setActiveSheetIndex(0);
        $this->objWriter = PHPExcel_IOFactory::createWriter($this->docexcel, 'Excel2007');
        //$this->objWriter = PHPExcel_IOFactory::createWriter($this->docexcel, 'Excel5');
        $this->objWriter->save($this->url_archivo);
        return $this->url_archivo;
    }

}
?>