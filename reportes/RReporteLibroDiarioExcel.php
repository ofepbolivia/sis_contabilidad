<?php
class RReporteLibroDiarioExcel
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
  //function datosHeader ($contenido,$saldo_anterior,$cabecera) {
  function datosHeader ($contenido,$cabecera) {
        $this->datos_contenido = $contenido;
        // $this->resumen = $resumen;
       // $this->saldo_anterior = $saldo_anterior;
        $this->recueprar_cabecera = $cabecera;
    }
    function imprimeCabecera() {
        $this->docexcel->createSheet();
        $this->docexcel->getActiveSheet()->setTitle('Libro Diario');
        $this->docexcel->setActiveSheetIndex(0);

        $styleTitulos1 = array(
            'font'  => array(
                'bold'  => true,
                'size'  => 11,
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
        //$this->docexcel->getActiveSheet()->mergeCells('A1:C1');

        //titulos
        $this->docexcel->getActiveSheet()->mergeCells('A1:A4');
        $this->docexcel->getActiveSheet()->getStyle('A2:H2')->applyFromArray($styleTitulos);
        $this->docexcel->getActiveSheet()->getStyle('A1:A4')->applyFromArray($bordes);

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1,2,'REPORTE LIBRO DIARIO');
        $this->docexcel->getActiveSheet()->getStyle('A2:H2')->applyFromArray($styleTitulos);
        $this->docexcel->getActiveSheet()->getStyle('B1:H1')->applyFromArray($bordes_titulo_supe);
        $this->docexcel->getActiveSheet()->getStyle('B4:H4')->applyFromArray($bordes_titulo_infe);
        $this->docexcel->getActiveSheet()->mergeCells('B2:H2');
        $this->docexcel->getActiveSheet()->getStyle('B2:H2')->applyFromArray($styleTitulos_principal);
        $this->docexcel->getActiveSheet()->mergeCells('B4:H4');
        $this->docexcel->getActiveSheet()->mergeCells('B1:H1');


        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8,1,'Desde: '.$this->objParam->getParametro('desde'));
        $this->docexcel->getActiveSheet()->getStyle('A1:I1')->applyFromArray($styleTitulosSubCabezera);
        $this->docexcel->getActiveSheet()->getStyle('I1')->applyFromArray($bordes);

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8,2,'Hasta: '.$this->objParam->getParametro('hasta'));
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8,3,'Gestión: '.$this->objParam->getParametro('gestion'));
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8,4,'Depto: Contabilidad ');
        $this->docexcel->getActiveSheet()->getStyle('A4:I4')->applyFromArray($styleTitulosSubCabezera);
        $this->docexcel->getActiveSheet()->getStyle('I2')->applyFromArray($styleTitulosSubCabezera);
        $this->docexcel->getActiveSheet()->getStyle('I2')->applyFromArray($bordes);
        $this->docexcel->getActiveSheet()->getStyle('I3')->applyFromArray($bordes);
        $this->docexcel->getActiveSheet()->getStyle('I4')->applyFromArray($bordes);
        $this->docexcel->getActiveSheet()->getStyle('A4:I4')->applyFromArray($styleTitulosSubCabezera);





        //*************************************Cabecera*****************************************

        $this->docexcel->getActiveSheet()->getColumnDimension('A')->setWidth(17);
        $this->docexcel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
        $this->docexcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('G')->setWidth(22);
        $this->docexcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
      //$this->docexcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
      //$this->docexcel->getActiveSheet()->getColumnDimension('K')->setWidth(20);
      //$this->docexcel->getActiveSheet()->getColumnDimension('L')->setWidth(20);
      //$this->docexcel->getActiveSheet()->getColumnDimension('M')->setWidth(20);

        
        $this->docexcel->getActiveSheet()->setCellValue('A6','#');    
        $this->docexcel->getActiveSheet()->setCellValue('B6','NRO COMPROBANTE');
        $this->docexcel->getActiveSheet()->setCellValue('C6','NRO TRÁMITE');
        $this->docexcel->getActiveSheet()->setCellValue('D6','FECHA');
        $this->docexcel->getActiveSheet()->setCellValue('E6','NRO CUENTA');
       // $this->docexcel->getActiveSheet()->setCellValue('F6','NRO C-31');
       // $this->docexcel->getActiveSheet()->setCellValue('G6','ORDEN DE TRABAJO');
       // $this->docexcel->getActiveSheet()->setCellValue('H6','FECHA INICIO');
       // $this->docexcel->getActiveSheet()->setCellValue('I6','FECHA FIN');

        $this->docexcel->getActiveSheet()->setCellValue('F6','GLOSA');
        $this->docexcel->getActiveSheet()->setCellValue('G6','IMPORTE DEBE');
        $this->docexcel->getActiveSheet()->setCellValue('H6','IMPORTE HABER');
        $this->docexcel->getActiveSheet()->setCellValue('I6','SALDO');
        $this->docexcel->getActiveSheet()->getStyle('A6:I6')->getAlignment()->setWrapText(true);
        $this->docexcel->getActiveSheet()->getStyle('A6:I6')->applyFromArray($styleTitulos1);
        $this->docexcel->getActiveSheet()->freezePaneByColumnAndRow(0,7);





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


        $cabecera_recup = $this->recueprar_cabecera;
        $fila = 7;
  //      foreach ($cabecera_recup as $cabecera_datos) {

//          $cuenta_negrita = new PHPExcel_RichText();
//          $texto_negrita_cuenta = $cuenta_negrita->createTextRun('CUENTA: ');
//          $texto_negrita_cuenta->getFont()->setBold(true);
//          $cuenta_negrita->createText($cabecera_datos['desc_cuenta']);

//          $partida_negrita = new PHPExcel_RichText();
//          $texto_negrita_partida = $partida_negrita->createTextRun('PARTIDA: ');
//          $texto_negrita_partida->getFont()->setBold(true);
//          $partida_negrita->createText($cabecera_datos['desc_partida']);

//          $auxiliar_negrita = new PHPExcel_RichText();
//          $texto_negrita_auxiliar = $auxiliar_negrita->createTextRun('AUXILIAR: ');
//          $texto_negrita_auxiliar->getFont()->setBold(true);
//          $auxiliar_negrita->createText($cabecera_datos['desc_auxiliar']);

//          $centro_costo_negrita = new PHPExcel_RichText();
//          $texto_negrita_cuenta = $centro_costo_negrita->createTextRun('CENTRO COSTO: ');
//          $texto_negrita_cuenta->getFont()->setBold(true);
//          $centro_costo_negrita->createText($cabecera_datos['desc_centro_costo']);

        /*Datos cabecera cuenta*/
//        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0,4,$cuenta_negrita);
//        $this->docexcel->getActiveSheet()->getStyle('A4:F4')->applyFromArray($bordes);
//        $this->docexcel->getActiveSheet()->mergeCells('A4:F4');
        //$this->docexcel->getActiveSheet()->getStyle('A4')->getAlignment()->setWrapText(true);
        /*********************/

        /*Datos cabecera partida*/
//        if ($cabecera_datos['desc_partida'] != '') {
//          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0,5,$partida_negrita);
//        }
//        $this->docexcel->getActiveSheet()->getStyle('A5:F5')->applyFromArray($bordes);
//        $this->docexcel->getActiveSheet()->mergeCells('A5:F5');
        //$this->docexcel->getActiveSheet()->getStyle('A5')->getAlignment()->setWrapText(true);
        /************************/

        /*Datos cabecera auxiliar*/
//        if ($cabecera_datos['desc_auxiliar'] != '') {
//          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6,4,$auxiliar_negrita);
  //      }
//        $this->docexcel->getActiveSheet()->getStyle('G4:M4')->applyFromArray($bordes);
  //      $this->docexcel->getActiveSheet()->mergeCells('G4:M4');
        //$this->docexcel->getActiveSheet()->getStyle('E4')->getAlignment()->setWrapText(true);
        /************************/

        /*Datos cabecera centro de costo*/
//        if ($cabecera_datos['desc_centro_costo'] != '') {
      //    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6,5,$centro_costo_negrita);
    //    }
    //    $this->docexcel->getActiveSheet()->getStyle('G5:M5')->applyFromArray($bordes);
      //  $this->docexcel->getActiveSheet()->mergeCells('G5:M5');
    //    //$this->docexcel->getActiveSheet()->getStyle('E5')->getAlignment()->setWrapText(true);
    //    /********************************/
        //$this->docexcel->getActiveSheet()->getStyle('A6:M6')->applyFromArray($styleTitulos);

      //}

        $fila = 7;
        $inicio = 7;
        $datos = $this->datos_contenido;
        $contador=1;
        foreach ($datos as $value) {

          if ($value["fecha_costo_ini"]=='' || $value["fecha_costo_ini"] == null) {
            $fecha_costo_ini = '';
          } else {
            $fecha_costo_ini = date("d/m/Y", strtotime($value['fecha_costo_fin']));
          }

          if ($value["fecha_costo_fin"]=='' || $value["fecha_costo_fin"] == null) {
            $fecha_costo_fin = '';
          } else {
            $fecha_costo_fin = date("d/m/Y", strtotime($value['fecha_costo_fin']));
          }

          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $fila, $contador);
          
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1, $fila, $value['nro_cbte']);
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2, $fila, $value['nro_tramite']);
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3, $fila, date("d/m/Y", strtotime($value["fecha"])));
          //$this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4, $fila, $value['codigo']);

          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4, $fila, $value['nro_cuenta']);
         // $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5, $fila, $value['c31']);
        //  $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6, $fila, $value['desc_orden']);
        //  $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(7, $fila,  $fecha_costo_ini);
        //  $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8, $fila,  $fecha_costo_fin);

          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5, $fila, $value['glosa1']);
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6, $fila, $value['importe_debe_mb']);
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(7, $fila, $value['importe_haber_mb']);
          $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8, $fila, $value['importe_saldo_mb']);

          /*Mandamos los estilos*/
          $this->docexcel->getActiveSheet()->getStyle("A$fila:J$fila")->applyFromArray($style_datos);
          $this->docexcel->getActiveSheet()->getStyle("K$fila:M$fila")->applyFromArray($style_datos_2);
          $this->docexcel->getActiveSheet()->getStyle("A$fila")->applyFromArray($bordes);
          $this->docexcel->getActiveSheet()->getStyle("B$fila")->applyFromArray($bordes);
          $this->docexcel->getActiveSheet()->getStyle("C$fila")->applyFromArray($bordes);
          $this->docexcel->getActiveSheet()->getStyle("D$fila")->applyFromArray($bordes);
          $this->docexcel->getActiveSheet()->getStyle("E$fila")->applyFromArray($bordes);
          $this->docexcel->getActiveSheet()->getStyle("F$fila")->applyFromArray($bordes);
          $this->docexcel->getActiveSheet()->getStyle("G$fila")->applyFromArray($bordes);
          $this->docexcel->getActiveSheet()->getStyle("H$fila")->applyFromArray($bordes);
          $this->docexcel->getActiveSheet()->getStyle("I$fila")->applyFromArray($bordes);
         // $this->docexcel->getActiveSheet()->getStyle("J$fila")->applyFromArray($bordes);
        //  $this->docexcel->getActiveSheet()->getStyle("K$fila")->applyFromArray($bordes);
        //  $this->docexcel->getActiveSheet()->getStyle("L$fila")->applyFromArray($bordes);
        //  $this->docexcel->getActiveSheet()->getStyle("M$fila")->applyFromArray($bordes);
        //  $this->docexcel->getActiveSheet()->getStyle("K$fila:M$fila")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
        //  $this->docexcel->getActiveSheet()->getStyle("J$fila")->getAlignment()->setWrapText(true);
        //  $this->docexcel->getActiveSheet()->getStyle("G$fila")->getAlignment()->setWrapText(true);
          $contador++;
          $fila++;


        }
        $numero_cuenta = substr($cabecera_datos['desc_cuenta'], 0, 3);
        $inicial_cuenta = substr($cabecera_datos['desc_cuenta'], 0, 0);
        $cuenta_acreedora = substr($cabecera_datos['desc_cuenta'], 0, 2);

        if ($inicial_cuenta = '1') {
          if ($numero_cuenta = '124' OR  $numero_cuenta = '114') {
                $comportamiento = 'pasivo';
            }else{
                  $comportamiento = 'activo';
                 }
        }
        if ($inicial_cuenta = '4' or $inicial_cuenta = '6') {
          $comportamiento = 'activo';
        }
         /*Si la cuenta inicia con 2 o 3 o 5 pertenece a un pasivo*/
          if ($inicial_cuenta = '2' or $inicial_cuenta = '3' or $inicial_cuenta = '5') {
            $comportamiento = 'pasivo';
          }

          if ($inicial_cuenta = '8') {
            if ($cuenta_acreedora = '81') {
              $comportamiento = 'activo';
            } else if ($cuenta_acreedora = '82') {
              $comportamiento = 'pasivo';
            }
          }

        $anterior = 7;
    //    $this->docexcel->getActiveSheet()->setCellValue("F$fila",'TOTALES:');
    //    for ($i=7; $i < $fila; $i++) {
    //      $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(10, $fila, "=SUM((G$anterior:G$i))");
    //      $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(11, $fila, "=SUM((H$anterior:H$i))");
    //      if ($comportamiento == 'activo') {
    //      $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(12, $fila, "=SUM((G$fila-G$fila))");
    //    } elseif ($comportamiento == 'pasivo') {
    //      $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(12, $fila, "=SUM((H$fila-H$fila))");
    //    }

    //      $this->docexcel->getActiveSheet()->getStyle("G$fila")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
    //      $this->docexcel->getActiveSheet()->getStyle("H$fila")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
    //      $this->docexcel->getActiveSheet()->getStyle("I$fila")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
    //    }

    //    $this->docexcel->getActiveSheet()->getStyle("F$fila:I$fila")->applyFromArray($styleTotales);
    //    $this->docexcel->getActiveSheet()->getStyle("F$fila")->applyFromArray($bordes);
    //    $this->docexcel->getActiveSheet()->getStyle("G$fila")->applyFromArray($bordes);
    //    $this->docexcel->getActiveSheet()->getStyle("H$fila")->applyFromArray($bordes);
    //    $this->docexcel->getActiveSheet()->getStyle("I$fila")->applyFromArray($bordes);

    //    $saldo_anterior = $this->saldo_anterior;

    //    foreach ($saldo_anterior as $value3){
    //     $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $anterior, 'SALDO ANTERIOR: ');
    //     $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(10, $anterior, $value3['total_debe_anterior']);
    //     $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(11, $anterior, $value3['total_haber_anterior']);
    //     $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(12, $anterior, $value3['saldo_anterior']);
    //     $this->docexcel->getActiveSheet()->getStyle("A$anterior:L$anterior")->applyFromArray($bordes);
    //     $this->docexcel->getActiveSheet()->getStyle("A$anterior:L$anterior")->applyFromArray($styleTotales);
    //     $this->docexcel->getActiveSheet()->getStyle("M$anterior")->applyFromArray($bordes);
    //     $this->docexcel->getActiveSheet()->getStyle("K$anterior")->applyFromArray($bordes);
    //     $this->docexcel->getActiveSheet()->getStyle("L$anterior")->applyFromArray($bordes);
      //   $this->docexcel->getActiveSheet()->getStyle("K$anterior:M$anterior")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);

    //     $this->docexcel->getActiveSheet()->mergeCells("A$anterior:J$anterior");
     //    }


    }

    function generarReporte(){
        $this->generarDatos();
        $this->docexcel->setActiveSheetIndex(0);
        $this->objWriter = PHPExcel_IOFactory::createWriter($this->docexcel, 'Excel5');
        $this->objWriter->save($this->url_archivo);
    }

}
?>
