<?php
class RComprobanteDiarioXls
{
    private $docexcel;
    private $objWriter;
    private $equivalencias=array();
    private $aux=0;
    private $aux2=0;
    private $objParam;
    public  $url_archivo;
    public  $fila = 0;
    public  $filaAux = 0;
    public  $fnum =array();
    public  $fnumA =0;
    public  $garantia =0;
    public  $array =array();
    public  $array2 =array();
    public  $sinboleta =array();
    public  $sb2 =array();
    public  $saldoanterior =array();
    public  $boletaGarantia =array();
    public  $depositosTotal =array();
    public  $comision =array();
    public  $boletos =array();
    var $datos_detalle;
    var $datos;
    var $contenido;
    var $datos_contenido;
    var $datos_titulo;
    var $saldo_anterior;


    function __construct(CTParametro $objParam){
        $this->objParam = $objParam;
        $this->url_archivo = "../../../reportes_generados/".$this->objParam->getParametro('nombre_archivo');
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

        $this->equivalencias=array( 0=>'A',1=>'B',2=>'C',3=>'D',4=>'E',5=>'F',6=>'G',7=>'H',8=>'I',9=>'J',10=>'K');
            //11=>'L',12=>'M',13=>'N',14=>'O',15=>'P',16=>'Q',17=>'R',
            //18=>'S',19=>'T',20=>'U',21=>'V',22=>'W',23=>'X',24=>'Y',25=>'Z',
            //26=>'AA',27=>'AB',28=>'AC',29=>'AD',30=>'AE',31=>'AF',32=>'AG',33=>'AH',
            //34=>'AI',35=>'AJ',36=>'AK',37=>'AL',38=>'AM',39=>'AN',40=>'AO',41=>'AP',
            //42=>'AQ',43=>'AR',44=>'AS',45=>'AT',46=>'AU',47=>'AV',48=>'AW',49=>'AX',
            //50=>'AY',51=>'AZ',
            //52=>'BA',53=>'BB',54=>'BC',55=>'BD',56=>'BE',57=>'BF',58=>'BG',59=>'BH',
            //60=>'BI',61=>'BJ',62=>'BK',63=>'BL',64=>'BM',65=>'BN',66=>'BO',67=>'BP',
            //68=>'BQ',69=>'BR',70=>'BS',71=>'BT',72=>'BU',73=>'BV',74=>'BW',75=>'BX',
            //76=>'BY',77=>'BZ');

    }

    //function datosHeader ($contenido,$saldo_anterior,$cabecera) {
        function datosHeader ($detalle, $contenido, $cabecera) {
        $this->datos_contenido = $contenido;
        // $this->resumen = $resumen;
        // $this->saldo_anterior = $saldo_anterior;
        $this->recueprar_cabecera = $cabecera;
        

        $this->datos_detalle = $detalle;
		$this->datos_titulo = $totales;
		$this->datos_entidad = $dataEmpresa;
		$this->datos_gestion = $gestion;
    }
    
    function imprimeDatos(){
		$datos = $this->datos_detalle;
		$this->desc = $datos[0]['fecha'];
        $config = $this->objParam->getParametro('config');
        $subtitulo = $this->objParam->getParametro('subtitulo'); 
        $concepto= $this->objParam->getParametro('concepto');
    }
    
    function imprimeCabecera() {
        $this->docexcel->createSheet();
        $this->docexcel->getActiveSheet()->setTitle('Libro Diario');
        $this->docexcel->setActiveSheetIndex(0);

        $styleTitulos1 = array(
            'font'  => array(
                'bold'  => true,
                'size'  => 18,
                'name'  => 'Calibri',
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                    'rgb' => 'EDEDED'
                )
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            )
        );

        $styleTitulos = array(
            'font'  => array(
                'bold'  => true,
                'size'  => 18,
                'name'  => 'Calibri'
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
        $styleTitulos_principal= array(
            'font'  => array(
                'bold'  => true,
                'size'  => 18,
                'name'  => 'Calibri'
            ),

        );
        $styleTitulosSubCabezera = array(
            'font'  => array(
                'bold'  => true,
                'size'  => 11,
                'name'  => 'Calibri'
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                    'rgb' => 'FFFFFF'
                )
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT ,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
        );

        $styleTituloPrincipal = array(
            'font'  => array(
                'bold'  => true,
                'size'  => 15,
                'name'  => 'Calibri'
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
        $bordes_titulo_supe = array(
            'borders' => array(
                'top' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
            ),

        );
        
        //titulos
        //  $this->docexcel->getActiveSheet()->mergeCells('A1:A3');
        $gdImage = imagecreatefromjpeg('../../../lib/imagenes/logos/logo.jpg');
        // Add a drawing to the worksheetecho date('H:i:s') . " Add a drawing to the worksheet\n";
        $objDrawing = new PHPExcel_Worksheet_MemoryDrawing();
        $objDrawing->setName('Sample image');
        $objDrawing->setDescription('Sample image');
        $objDrawing->setImageResource($gdImage);
        $objDrawing->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_JPEG);
        $objDrawing->setMimeType(PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_DEFAULT);
        $objDrawing->setHeight(50);
        $objDrawing->setCoordinates('A1');
        $objDrawing->setWorksheet($this->docexcel->getActiveSheet());
        
          $this->docexcel->getActiveSheet()->getStyle('A1:G1')->applyFromArray($styleTitulos);
          //$this->docexcel->getActiveSheet()->getStyle('A1:G1')->applyFromArray($bordes);

          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3,1,'REPORTE LIBRO DIARIO' );
          $this->docexcel->getActiveSheet()->getStyle('A1:G1')->applyFromArray($styleTitulos);
          
          //$this->docexcel->getActiveSheet()->getStyle('A2:G2')->applyFromArray($bordes_titulo_supe);
          $this->docexcel->getActiveSheet()->getStyle('A3:G3')->applyFromArray($bordes_titulo_infe);
          $this->docexcel->getActiveSheet()->mergeCells('A2:G2');
          $this->docexcel->getActiveSheet()->getStyle('A1:G1')->applyFromArray($styleTitulos_principal);
          // $this->docexcel->getActiveSheet()->mergeCells('B1:H1');
          $this->docexcel->getActiveSheet()->mergeCells('A3:G3');

          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(7,1,'Desde: '.substr($this->objParam->getParametro('fecIni'),0,10));
          $this->docexcel->getActiveSheet()->getStyle('A1:H1')->applyFromArray($styleTitulosSubCabezera);
          $this->docexcel->getActiveSheet()->getStyle('H1')->applyFromArray($bordes);

          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(7,2,'Hasta: '.substr($this->objParam->getParametro('fecFin'),0,10));
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(7,3,'Gestión: '. date('Y'));
          //$this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(7,3,'Gestión: '.$this->objParam->getParametro('gestion'));
          $this->docexcel->getActiveSheet()->getStyle('A3:H3')->applyFromArray($styleTitulosSubCabezera);
          $this->docexcel->getActiveSheet()->getStyle('H2')->applyFromArray($styleTitulosSubCabezera);
          $this->docexcel->getActiveSheet()->getStyle('H2')->applyFromArray($bordes);
          $this->docexcel->getActiveSheet()->getStyle('H3')->applyFromArray($bordes);
          //$this->docexcel->getActiveSheet()->getStyle('A4:H4')->applyFromArray($styleTitulosSubCabezera);

        //*************************************Cabecera*****************************************

      //  $this->docexcel->getActiveSheet()->getColumnDimension('A')->setWidth(17);
    //    $this->docexcel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
    //    $this->docexcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
    //    $this->docexcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
    //    $this->docexcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
    //    $this->docexcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
    //    $this->docexcel->getActiveSheet()->getColumnDimension('G')->setWidth(22);
    //    $this->docexcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
    //    $this->docexcel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
    //    $this->docexcel->getActiveSheet()->getColumnDimension('J')->setWidth(50);
    //    $this->docexcel->getActiveSheet()->getColumnDimension('K')->setWidth(20);
    //    $this->docexcel->getActiveSheet()->getColumnDimension('L')->setWidth(20);
    //    $this->docexcel->getActiveSheet()->getColumnDimension('M')->setWidth(20);

        $this->docexcel->getActiveSheet()->getColumnDimension($this->equivalencias[0])->setWidth(10);
        $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(0,4,'Nro');
        $this->docexcel->getActiveSheet()->getColumnDimension($this->equivalencias[1])->setWidth(20);
        $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1,4,'Nro Comprobante');
        $this->docexcel->getActiveSheet()->getColumnDimension($this->equivalencias[2])->setWidth(25);
        $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(2,4,'Nro Tramite');
        $this->docexcel->getActiveSheet()->getColumnDimension($this->equivalencias[3])->setWidth(20);
        $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(3,4,'Fecha');
        $this->docexcel->getActiveSheet()->getColumnDimension($this->equivalencias[4])->setWidth(20);
        $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(4,4,'Cuenta Contable');
        $this->docexcel->getActiveSheet()->getColumnDimension($this->equivalencias[5])->setWidth(50);
        $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(5,4,'Descripción');
        $this->docexcel->getActiveSheet()->getColumnDimension($this->equivalencias[6])->setWidth(20);
        $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(6,4,'Debe');
        $this->docexcel->getActiveSheet()->getColumnDimension($this->equivalencias[7])->setWidth(20);
        $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(7,4,'Haber');
        $this->docexcel->getActiveSheet()->getColumnDimension($this->equivalencias[8])->setWidth(20);
        $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(8,4,'');
       // $this->docexcel->getActiveSheet()->getColumnDimension($this->equivalencias[9])->setWidth(15);
       //$this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(9,1,'');
        

    //    $this->docexcel->getActiveSheet()->setCellValue('A5','FECHA');
    //    $this->docexcel->getActiveSheet()->setCellValue('B5','NRO COMPROBANTE');
    //    $this->docexcel->getActiveSheet()->setCellValue('C5','NRO FÁCTURA');
    //    $this->docexcel->getActiveSheet()->setCellValue('D5','NRO PARTIDA');

    //    $this->docexcel->getActiveSheet()->setCellValue('E5','NRO TRÁMITE');
    //    $this->docexcel->getActiveSheet()->setCellValue('F5','NRO C-31');
    //    $this->docexcel->getActiveSheet()->setCellValue('G5','ORDEN DE TRABAJO');
    //    $this->docexcel->getActiveSheet()->setCellValue('H5','FECHA INICIO');
    //    $this->docexcel->getActiveSheet()->setCellValue('I5','FECHA FIN');

    //    $this->docexcel->getActiveSheet()->setCellValue('J5','GLOSA');
    //    $this->docexcel->getActiveSheet()->setCellValue('K5','IMPORTE DEBE');
    //    $this->docexcel->getActiveSheet()->setCellValue('L5','IMPORTE HABER');
    //    $this->docexcel->getActiveSheet()->setCellValue('M5','SALDO');
       // $this->docexcel->getActiveSheet()->getStyle('A6:M6')->getAlignment()->setWrapText(true);
    //    $this->docexcel->getActiveSheet()->getStyle('A6:M6')->applyFromArray($styleTitulos1);
        $this->docexcel->getActiveSheet()->freezePaneByColumnAndRow(0,5);
    }
    function generarDatos(){
        $this->imprimeCabecera();

        $style_datos = array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
        );
        $style_datos_2 = array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
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

        $styleTotales = array(
            'font'  => array(
                'bold'  => true,
                'name'  => 'Calibri'
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                    'rgb' => 'FFFFFF'
                )
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
        );

        $styleTitulosSubCabezera = array(
            'font'  => array(
                'bold'  => true,
                'size'  => 11,
                'name'  => 'Calibri'
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                    'rgb' => 'FFFFFF'
                )
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT ,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
        );

        $styleTitulos = array(
            'font'  => array(
                'bold'  => true,
                'size'  => 11,
                'name'  => 'Calibri'
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


        
        
        // if ($value["fecha_costo_ini"]=='' || $value["fecha_costo_ini"] == null) {
        //  $fecha_costo_ini = '';
        // } else {
        //    $fecha_costo_ini = date("d/m/Y", strtotime($value['fecha_costo_fin']));
        //  }

        //  if ($value["fecha_costo_fin"]=='' || $value["fecha_costo_fin"] == null) {
        //    $fecha_costo_fin = '';
        //  } else {
        //    $fecha_costo_fin = date("d/m/Y", strtotime($value['fecha_costo_fin']));
        //  }

        $cabecera_recup = $this->recueprar_cabecera;
        $fila = 5;

        $inicio = 5;
        $datos = $this->datos_detalle;

    

          $fila = 5;
          $contador = 1;

          foreach ($datos as $value){
    
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $fila, $contador);
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1, $fila, $value['nro_tramite']);
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2, $fila, $value['nro_cbte']);
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3, $fila, date("Y-m-d", strtotime($value["fecha"])));

          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4, $fila, $value['nro_cuenta']);
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5, $fila, $value['glosa1']);
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6, $fila, $value['importe_debe']);
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(7, $fila, $value['importe_haber']);
        
        //  $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(9, $fila, $value['glosa1']);
        //  $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(10, $fila, $value['debe']);
        //  $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(11, $fila, $value['haber']);
        //  $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(12, $fila, $value['importe_saldo_mb']);

          $contador++;
          $fila++;
       } // final foreach

        //    $numero_cuenta = substr($cabecera_datos['desc_cuenta'], 0, 3);
    ////    $inicial_cuenta = substr($cabecera_datos['desc_cuenta'], 0, 0);
    //    $cuenta_acreedora = substr($cabecera_datos['desc_cuenta'], 0, 2);

    //    if ($inicial_cuenta = '1') {
    //      if ($numero_cuenta = '124' OR  $numero_cuenta = '114') {
    //            $comportamiento = 'pasivo';
    //        }else{
    //              $comportamiento = 'activo';
    //             }
    //    }
    //    if ($inicial_cuenta = '4' or $inicial_cuenta = '6') {
    //      $comportamiento = 'activo';
    //    }
    //     /*Si la cuenta inicia con 2 o 3 o 5 pertenece a un pasivo*/
    //      if ($inicial_cuenta = '2' or $inicial_cuenta = '3' or $inicial_cuenta = '5') {
    //        $comportamiento = 'pasivo';
    //      }

    //      if ($inicial_cuenta = '8') {
    //        if ($cuenta_acreedora = '81') {
    //          $comportamiento = 'activo';
    //        } else if ($cuenta_acreedora = '82') {
    //          $comportamiento = 'pasivo';
    //        }
    //      }

    //    $anterior = 7;
    //    $this->docexcel->getActiveSheet()->setCellValue("J$fila",'TOTALES:');
    //    for ($i=7; $i < $fila; $i++) {
    //      $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(10, $fila, "=SUM((K$anterior:K$i))");
    //      $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(11, $fila, "=SUM((L$anterior:L$i))");
    //      if ($comportamiento == 'activo') {
    //      $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(12, $fila, "=SUM((K$fila-L$fila))");
    //    } elseif ($comportamiento == 'pasivo') {
    //      $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(12, $fila, "=SUM((L$fila-K$fila))");
    //    }

    //      $this->docexcel->getActiveSheet()->getStyle("K$fila")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
    //      $this->docexcel->getActiveSheet()->getStyle("L$fila")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
    //      $this->docexcel->getActiveSheet()->getStyle("M$fila")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
    //    }

    //    $this->docexcel->getActiveSheet()->getStyle("J$fila:M$fila")->applyFromArray($styleTotales);
    //    $this->docexcel->getActiveSheet()->getStyle("J$fila")->applyFromArray($bordes);
    //    $this->docexcel->getActiveSheet()->getStyle("K$fila")->applyFromArray($bordes);
    //    $this->docexcel->getActiveSheet()->getStyle("L$fila")->applyFromArray($bordes);
    //    $this->docexcel->getActiveSheet()->getStyle("M$fila")->applyFromArray($bordes);

    //    $saldo_anterior = $this->saldo_anterior;

       // foreach ($saldo_anterior as $value3){
        // $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $anterior, 'SALDO ANTERIOR: ');
        // $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(10, $anterior, $value3['total_debe_anterior']);
        // $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(11, $anterior, $value3['total_haber_anterior']);
        // $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(12, $anterior, $value3['saldo_anterior']);
        // $this->docexcel->getActiveSheet()->getStyle("A$anterior:L$anterior")->applyFromArray($bordes);
        // $this->docexcel->getActiveSheet()->getStyle("A$anterior:L$anterior")->applyFromArray($styleTotales);
        // $this->docexcel->getActiveSheet()->getStyle("M$anterior")->applyFromArray($bordes);
        // $this->docexcel->getActiveSheet()->getStyle("K$anterior")->applyFromArray($bordes);
        // $this->docexcel->getActiveSheet()->getStyle("L$anterior")->applyFromArray($bordes);
        // $this->docexcel->getActiveSheet()->getStyle("K$anterior:M$anterior")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);

        // $this->docexcel->getActiveSheet()->mergeCells("A$anterior:J$anterior");
        // }
    }
    function generarReporte(){
        $this->generarDatos();
        $this->docexcel->setActiveSheetIndex(0);
        $this->objWriter = PHPExcel_IOFactory::createWriter($this->docexcel, 'Excel5');
        $this->objWriter->save($this->url_archivo);
    }
}
?>