<?php

class RLibroComprasNCDXLS{
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
        set_time_limit(400);
        $cacheMethod = PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
        $cacheSettings = array('memoryCacheSize'  => '10MB');
        PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);

        $this->docexcel = new PHPExcel();
        $this->docexcel->getProperties()->setCreator("PXP")
            ->setLastModifiedBy("PXP")
            ->setTitle($this->objParam->getParametro('titulo_archivo'))
            ->setSubject($this->objParam->getParametro('titulo_archivo'))
            ->setDescription('Reporte "'.$this->objParam->getParametro('titulo_archivo').'", generado por el framework PXP')
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

    function generarDatos(){

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
        //print_r($datos);exit;
        $tipo = $this->objParam->getParametro('tipo');

        $numberFormat = '#,##0.00';

        $index = 0;
        $color_pestana = array('ff0000','1100ff','55ff00','3ba3ff','ff4747','697dff','78edff','ba8cff',
            'ff80bb','ff792b','ffff5e','52ff97','bae3ff','ffaf9c','bfffc6','b370ff','ffa8b4','7583ff','9aff17','ff30c8');


        /*PAGOS QUE ESTAN EN ATC Y RET*/

        $this->addHoja('LIBRO COMPRAS NOTAS C-D',$index);

        $color_pestana = array('ff0000','1100ff','55ff00','3ba3ff','ff4747','697dff','78edff','ba8cff',
            'ff80bb','ff792b','ffff5e','52ff97','bae3ff','ffaf9c','bfffc6','b370ff','ffa8b4','7583ff','9aff17','ff30c8');

        $this->docexcel->getActiveSheet()->freezePaneByColumnAndRow(0,7);
        $this->docexcel->getActiveSheet()->getTabColor()->setRGB($color_pestana[$index]);

        $this->docexcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
        $this->docexcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('E')->setWidth(10);
        $this->docexcel->getActiveSheet()->getColumnDimension('F')->setWidth(13);
        $this->docexcel->getActiveSheet()->getColumnDimension('G')->setWidth(50);
        $this->docexcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('J')->setWidth(30);
        $this->docexcel->getActiveSheet()->getColumnDimension('K')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('L')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('M')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('N')->setWidth(20);


        /*logo*/
        $objDrawing = new PHPExcel_Worksheet_Drawing();
        $objDrawing->setName('BoA ERP');
        $objDrawing->setDescription('BoA ERP');
        $objDrawing->setPath('../../lib/imagenes/logos/logo.jpg');
        $objDrawing->setCoordinates('A1');
        $objDrawing->setOffsetX(0);
        $objDrawing->setOffsetY(0);
        $objDrawing->setWidth(105);
        $objDrawing->setHeight(75);
        $objDrawing->setWorksheet($this->docexcel->getActiveSheet());
        /*logo*/

        $this->docexcel->getActiveSheet()->getStyle('A1:N3')->applyFromArray($styleTitulos);
        $this->docexcel->getActiveSheet()->getStyle('A1:N6')->getAlignment()->setWrapText(true);

        $this->docexcel->getActiveSheet()->mergeCells('A1:N1');
        $this->docexcel->getActiveSheet()->setCellValue('A1','LIBRO DE COMPRAS');
        $this->docexcel->getActiveSheet()->mergeCells('A2:N2');
        $this->docexcel->getActiveSheet()->setCellValue('A2','NOTAS DE CREDITO-DEBITO');
        $this->docexcel->getActiveSheet()->mergeCells('A3:N3');
        $this->docexcel->getActiveSheet()->setCellValue('A3','(EXPRESADO EN BOLIVIANOS)');

        $styleTitulos['alignment']['horizontal'] = PHPExcel_Style_Alignment::HORIZONTAL_LEFT;
        $this->docexcel->getActiveSheet()->getStyle('A4:N5')->applyFromArray($styleTitulos);

        $this->docexcel->getActiveSheet()->mergeCells('C4:N4');
        $this->docexcel->getActiveSheet()->setCellValue('C4','Periodo: '.$datos[0]['periodo']." ".$datos[0]['gestion']);
        $this->docexcel->getActiveSheet()->mergeCells('C5:E5');
        $this->docexcel->getActiveSheet()->setCellValue('C5','Nombre o Razón Social: '.$datos[0]['razon_empresa']);
        $this->docexcel->getActiveSheet()->mergeCells('G5:N5');
        $this->docexcel->getActiveSheet()->setCellValue('G5','NIT: ' .$datos[0]['nit_empresa']);


        $this->docexcel->getActiveSheet()->getStyle('A6:N6')->applyFromArray($styleTitulos1);

        $this->docexcel->getActiveSheet()->setCellValue('A6','Nro.');
        $this->docexcel->getActiveSheet()->setCellValue('B6','FECHA NOTA');
        $this->docexcel->getActiveSheet()->setCellValue('C6','Nro. DE NOTA');
        $this->docexcel->getActiveSheet()->setCellValue('D6','Nro. AUTORIZACIÓN');
        $this->docexcel->getActiveSheet()->setCellValue('E6','ESTADO');
        $this->docexcel->getActiveSheet()->setCellValue('F6','NIT/CI CLIENTE');
        $this->docexcel->getActiveSheet()->setCellValue('G6','NOMBRE O RAZON SOCIAL CLIENTE');
        $this->docexcel->getActiveSheet()->setCellValue('H6','IMPORTE TOTAL DEVOLUCIÓN');
        $this->docexcel->getActiveSheet()->setCellValue('I6','CREDITO FISCAL');
        $this->docexcel->getActiveSheet()->setCellValue('J6','CODIGO DE CONTROL');
        $this->docexcel->getActiveSheet()->setCellValue('K6','FECHA FACTURA ORIGINAL');
        $this->docexcel->getActiveSheet()->setCellValue('L6','Nro. FACTURA ORIGINAL');
        $this->docexcel->getActiveSheet()->setCellValue('M6','Nro. AUTORIZACIÓN FACTURA ORIGINAL');
        $this->docexcel->getActiveSheet()->setCellValue('N6','IMPORTE TOTAL FACTURA ORIGINAL');



        $fila = 7;

        $color_cell = array('b4c6e7','d9e1f2','ffc7ce','9bbb59');


        $fila_total = $fila;

        $flag_left = true;
        $index_color = 0;

        $total_devolucion_par = 0;
        $total_rciva_par = 0;
        $total_importe_par = 0;

        $total_devolucion_gen = 0;
        $total_rciva_gen = 0;
        $total_importe_gen = 0;

        $numeracion = 1;
        $contador_reg = 0;
        foreach ($datos as $key => $rec) {
            $contador_reg++;


            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $fila, $numeracion);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1, $fila, DateTime::createFromFormat('Y-m-d', $rec['fecha_nota'])->format('d/m/Y'));
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2, $fila, $rec['num_nota']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3, $fila, $rec['num_autorizacion']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4, $fila, $rec['estado']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5, $fila, $rec['nit']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6, $fila, $rec['razon_social']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(7, $fila, number_format($rec['total_devuelto'], 2, '.', ''));
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8, $fila, number_format($rec['rc_iva'], 2, '.', ''));
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(9, $fila, $rec['codigo_control']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(10, $fila, DateTime::createFromFormat('Y-m-d', $rec['fecha_original'])->format('d/m/Y'));
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(11, $fila, $rec['num_factura']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(12, $fila, $rec['nroaut_anterior']);
            $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(13, $fila, number_format($rec['importe_total'], 2, '.', ''));

            if ( $contador_reg < 37 ) {
                //suma totales
                $total_devolucion_par += $rec['total_devuelto'];
                $total_rciva_par += $rec['rc_iva'];
                $total_importe_par += $rec['importe_total'];
            }
            if ( $contador_reg == 36 ) {

                $fila++;
                $this->docexcel->getActiveSheet()->setCellValue('G' . $fila, 'TOTAL PARCIALES: ');
                //$this->docexcel->getActiveSheet()->mergeCells('A'.$fila.':G'.$fila);
                $this->docexcel->getActiveSheet()->getStyle('A'.$fila.':N'.$fila)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                $this->docexcel->getActiveSheet()->getStyle('A'.$fila.':N'.$fila)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB($color_cell[2]);
                $this->docexcel->getActiveSheet()->getStyle('A'.$fila.':N'.$fila)->getFont()->setBold(true);

                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(7, $fila, '=SUM(H'.$fila_total.':H'.($fila-1).')');
                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8, $fila, '=SUM(I'.$fila_total.':I'.($fila-1).')');
                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(13, $fila, '=SUM(N'.$fila_total.':N'.($fila-1).')');

                $fila++;

                $total_devolucion_gen += number_format($total_devolucion_par, 2, '.', '') ;
                $total_rciva_gen += number_format($total_rciva_par, 2, '.', '');
                $total_importe_gen += number_format($total_importe_par, 2, '.', '');

                $this->docexcel->getActiveSheet()->setCellValue('G' . $fila, 'TOTAL GENERALES: ');
                //$this->docexcel->getActiveSheet()->mergeCells('A'.$fila.':G'.$fila);
                $this->docexcel->getActiveSheet()->getStyle('A'.$fila.':N'.$fila)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                $this->docexcel->getActiveSheet()->getStyle('A'.$fila.':N'.$fila)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB($color_cell[2]);
                $this->docexcel->getActiveSheet()->getStyle('A'.$fila.':N'.$fila)->getFont()->setBold(true);

                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(7, $fila, $total_devolucion_gen);
                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8, $fila, $total_rciva_gen);
                $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(13, $fila, $total_importe_gen);

                $fila_total = $fila + 1;
                $total_devolucion_par = 0;
                $total_rciva_par = 0;
                $total_importe_par = 0;
                $contador_reg = 1;
            }


            $fila++;
            $numeracion++;
        }

        $this->docexcel->getActiveSheet()->setCellValue('G' . $fila, 'TOTAL PARCIALES: ');
        //$this->docexcel->getActiveSheet()->mergeCells('A'.$fila.':G'.$fila);
        $this->docexcel->getActiveSheet()->getStyle('A'.$fila.':N'.$fila)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $this->docexcel->getActiveSheet()->getStyle('A'.$fila.':N'.$fila)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB($color_cell[2]);
        $this->docexcel->getActiveSheet()->getStyle('A'.$fila.':N'.$fila)->getFont()->setBold(true);

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(7, $fila, '=SUM(H'.$fila_total.':H'.($fila-1).')');
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8, $fila, '=SUM(I'.$fila_total.':I'.($fila-1).')');
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(13, $fila, '=SUM(N'.$fila_total.':N'.($fila-1).')');

        $fila++;

        $total_devolucion_gen += number_format($total_devolucion_par, 2, '.', '') ;
        $total_rciva_gen += number_format($total_rciva_par, 2, '.', '');
        $total_importe_gen += number_format($total_importe_par, 2, '.', '');

        $this->docexcel->getActiveSheet()->setCellValue('G' . $fila, 'TOTAL GENERALES: ');
        //$this->docexcel->getActiveSheet()->mergeCells('A'.$fila.':G'.$fila);
        $this->docexcel->getActiveSheet()->getStyle('A'.$fila.':N'.$fila)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $this->docexcel->getActiveSheet()->getStyle('A'.$fila.':N'.$fila)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB($color_cell[2]);
        $this->docexcel->getActiveSheet()->getStyle('A'.$fila.':N'.$fila)->getFont()->setBold(true);

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(7, $fila, $total_devolucion_gen);
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8, $fila, $total_rciva_gen);
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(13, $fila, $total_importe_gen);
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
        $this->objWriter = PHPExcel_IOFactory::createWriter($this->docexcel, 'Excel5');
        $this->objWriter->save($this->url_archivo);
    }

}
?>