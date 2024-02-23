<?php
class RReporteLibDiaExcel
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
        //set_time_limit(0);
        $this->objParam = $objParam;
        $this->url_archivo = "../../../reportes_generados/".$this->objParam->getParametro('nombre_archivo');
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
  function datosHeader ($contenido) {
        $this->datos_contenido = $contenido;
        //$this->datos_contenido = json_decode($contenido[0]['datos_json']);
        // $this->resumen = $resumen;
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

        $logo_=dirname(__FILE__).'/../../lib'.$_SESSION['_DIR_LOGO']; //fRnk
        if(strpos($logo_, '.png') !== false)
            $gdImage = imagecreatefrompng($logo_);
        else
            $gdImage = imagecreatefromjpeg($logo_);
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
        $this->docexcel->getActiveSheet()->mergeCells('A1:A3');
        $this->docexcel->getActiveSheet()->getStyle('A2:H2')->applyFromArray($styleTitulos);
        $this->docexcel->getActiveSheet()->getStyle('A1:A3')->applyFromArray($bordes);

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1,2,'LIBRO DIARIO' );
        $this->docexcel->getActiveSheet()->getStyle('A2:H2')->applyFromArray($styleTitulos);
        $this->docexcel->getActiveSheet()->getStyle('B1:M1')->applyFromArray($bordes_titulo_supe);
        $this->docexcel->getActiveSheet()->getStyle('B3:M3')->applyFromArray($bordes_titulo_infe);
        $this->docexcel->getActiveSheet()->mergeCells('B2:M2');
        $this->docexcel->getActiveSheet()->getStyle('B2:M2')->applyFromArray($styleTitulos_principal);
        $this->docexcel->getActiveSheet()->mergeCells('B3:M3');
        $this->docexcel->getActiveSheet()->mergeCells('B1:M1');


        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(18,1,'Desde: '.$this->objParam->getParametro('desde'));
        $this->docexcel->getActiveSheet()->getStyle('A1:S1')->applyFromArray($styleTitulosSubCabezera);
        $this->docexcel->getActiveSheet()->getStyle('S1')->applyFromArray($bordes);

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(18,2,'Hasta: '.$this->objParam->getParametro('hasta'));
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(18,3,'Gestión: '.$this->objParam->getParametro('gest'));
        $this->docexcel->getActiveSheet()->getStyle('A3:S3')->applyFromArray($styleTitulosSubCabezera);
        $this->docexcel->getActiveSheet()->getStyle('S2')->applyFromArray($styleTitulosSubCabezera);
        $this->docexcel->getActiveSheet()->getStyle('S2')->applyFromArray($bordes);
        $this->docexcel->getActiveSheet()->getStyle('S3')->applyFromArray($bordes);
        $this->docexcel->getActiveSheet()->getStyle('A4:V4')->applyFromArray($styleTitulosSubCabezera);





        //*************************************Cabecera*****************************************

        $this->docexcel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('B')->setWidth(80);
        $this->docexcel->getActiveSheet()->getColumnDimension('C')->setWidth(80);
        $this->docexcel->getActiveSheet()->getColumnDimension('D')->setWidth(80);
        $this->docexcel->getActiveSheet()->getColumnDimension('E')->setWidth(80);
        $this->docexcel->getActiveSheet()->getColumnDimension('F')->setWidth(25);
        $this->docexcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('K')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('L')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('M')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('N')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('O')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('P')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('Q')->setWidth(25);
        $this->docexcel->getActiveSheet()->getColumnDimension('R')->setWidth(30);
        $this->docexcel->getActiveSheet()->getColumnDimension('S')->setWidth(30);
        $this->docexcel->getActiveSheet()->getColumnDimension('T')->setWidth(45);
        $this->docexcel->getActiveSheet()->getColumnDimension('U')->setWidth(40);
        $this->docexcel->getActiveSheet()->getColumnDimension('V')->setWidth(135);

        $this->docexcel->getActiveSheet()->setCellValue('A4','FECHA');
        $this->docexcel->getActiveSheet()->setCellValue('B4','CUENTA');
        $this->docexcel->getActiveSheet()->setCellValue('C4','PARTIDA');
        $this->docexcel->getActiveSheet()->setCellValue('D4','ORDEN DE TRABAJO');
        $this->docexcel->getActiveSheet()->setCellValue('E4','GLOSA');

        $this->docexcel->getActiveSheet()->setCellValue('F4','CBTE');
        $this->docexcel->getActiveSheet()->setCellValue('G4','DEBE');
        $this->docexcel->getActiveSheet()->setCellValue('H4','HABER');
        $this->docexcel->getActiveSheet()->setCellValue('I4','DEBE MO');
        $this->docexcel->getActiveSheet()->setCellValue('J4','HABER MO');
        $this->docexcel->getActiveSheet()->setCellValue('K4','TIPO DE CAMBIO');
        $this->docexcel->getActiveSheet()->setCellValue('L4','DEBE MT');
        $this->docexcel->getActiveSheet()->setCellValue('M4','HABER MT');

        $this->docexcel->getActiveSheet()->setCellValue('N4','DEBE MA');
        $this->docexcel->getActiveSheet()->setCellValue('O4','HABER MA');
        $this->docexcel->getActiveSheet()->setCellValue('P4','NRO DE TRAMITE');
        $this->docexcel->getActiveSheet()->setCellValue('Q4','C-31');
        $this->docexcel->getActiveSheet()->setCellValue('R4','NRO FACTURA');
        $this->docexcel->getActiveSheet()->setCellValue('S4','DEPARTAMENTO');
        $this->docexcel->getActiveSheet()->setCellValue('T4','GLOSA TRAMITE');
        $this->docexcel->getActiveSheet()->setCellValue('U4','CENTRO DE COSTO');
        $this->docexcel->getActiveSheet()->setCellValue('V4','CATEGORIA PROGRAMATICA');
        $this->docexcel->getActiveSheet()->getStyle('A4:V4')->getAlignment()->setWrapText(true);
        $this->docexcel->getActiveSheet()->getStyle('A4:V4')->applyFromArray($styleTitulos1);
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

        $totales = array(
          'alignment' => array(
              'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT ,
              'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
          ),
          'fill' => array(
              'type' => PHPExcel_Style_Fill::FILL_SOLID,
              'color' => array(
                  'rgb' => 'FFBA68'
              )
          ),
          'borders' => array(
               'allborders' => array(
                   'style' => PHPExcel_Style_Border::BORDER_THIN
               )
           ),
           'font'  => array(
               'bold'  => true,
               'size'  => 12,
               'name'  => 'Calibri',
           ),
        );


        $style_todos_bordes = array(
            'borders' => array(
                 'allborders' => array(
                     'style' => PHPExcel_Style_Border::BORDER_THIN
                 )
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

      /*Aqui el contenido del reporte*/
        $fila = 5;
        $inicio = 5;
        $datos = $this->datos_contenido;
        // var_dump();
        // $tamano = count($datos);
        // for ($i=0; $i<$tamano; $i++) {
        //   $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $fila, $datos[$i]['desc_cuenta']);
        //   $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1, $fila, $datos[$i]['desc_partida']);
        //   $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2, $fila, $datos[$i]['desc_orden']);
        //   $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3, $fila, $datos[$i]['glosa1']);
        //
        //   $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4, $fila, $datos[$i]['nro_cbte']);
        //   $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5, $fila, $datos[$i]['importe_debe_mb']);
        //   $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6, $fila, $datos[$i]['importe_haber_mb']);
        //   $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(7, $fila, $datos[$i]['importe_debe_mt']);
        //   $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8, $fila, $datos[$i]['importe_haber_mt']);
        //
        //   $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(9, $fila, $datos[$i]['importe_debe_ma']);
        //   $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(10, $fila, $datos[$i]['importe_haber_ma']);
        //   $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(11, $fila, $datos[$i]['nro_tramite']);
        //   $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(12, $fila, $datos[$i]['c31']);
        //   //$this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(13, $fila, $datos[$i]['nro_documentos']);
        //
        //   $fila ++;
        //   //var_dump("aqui llega el FOR",$datos[$i]->id_int_transaccion);
        // }
        //
        // /*Aqui aplicamos los estilos*/
        // for ($j=5; $j<$fila; $j++) {
        //   $this->docexcel->getActiveSheet()->getStyle("A$j:N$j")->applyFromArray($style_todos_bordes);
        //   $this->docexcel->getActiveSheet()->getStyle("M$j:N$j")->getAlignment()->setWrapText(true);
        //   $this->docexcel->getActiveSheet()->getStyle("F$j:K$j")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
        //   $this->docexcel->getActiveSheet()->getStyle("F$j:K$j")->applyFromArray($style_datos_2);
        // }
        // // /**************************/
        // //
        // $fila_final = $fila - 1;
        //
        // /*Aqui calculamos los totales*/
        // $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $fila, 'Totales:');
        // $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5, $fila, "=SUM((F$inicio:F$fila_final))");
        // $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6, $fila, "=SUM((G$inicio:G$fila_final))");
        // $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(7, $fila, "=SUM((H$inicio:H$fila_final))");
        // $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8, $fila, "=SUM((H$inicio:H$fila_final))");
        // $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(9, $fila, "=SUM((H$inicio:H$fila_final))");
        // $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(10, $fila, "=SUM((H$inicio:H$fila_final))");
        // $this->docexcel->getActiveSheet()->mergeCells("A$fila:E$fila");
        // $this->docexcel->getActiveSheet()->getStyle("F$fila:K$fila")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
        //
        // $this->docexcel->getActiveSheet()->getStyle("A$fila:K$fila")->applyFromArray($totales);
        //
        //
        //


        /*****************************/


      foreach ($datos as $value) {
           $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $fila, date("d/m/Y", strtotime($value['fecha'])));
           $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1, $fila, $value['desc_cuenta']);
           $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2, $fila, $value['desc_partida']);
           $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3, $fila, $value['desc_orden']);
           $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4, $fila, $value['glosa1']);

           $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5, $fila, $value['nro_cbte']);
           $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6, $fila, $value['importe_debe_mb']);
           $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(7, $fila, $value['importe_haber_mb']);
           $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8, $fila, $value['importe_debe']);
           $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(9, $fila, $value['importe_haber']);
           $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(10, $fila, $value['tipo_cambio']);
           $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(11, $fila, $value['importe_debe_mt']);
           $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(12, $fila, $value['importe_haber_mt']);

           $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(13, $fila, $value['importe_debe_ma']);
           $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(14, $fila, $value['importe_haber_ma']);
           $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(15, $fila, $value['nro_tramite']);
           $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(16, $fila, $value['c31']);
           $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(17, $fila, $value['nro_documentos']);
           $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(18, $fila, $value['nombre_corto']);
           $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(19, $fila, $value['glosa']);
           $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(20, $fila, $value['desc_centro_costo']);
           $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(21, $fila, $value['codigo_categoria'].' '.' '.' '.' '.$value['desc_catergori_prog']);
           //
          /*Mandamos los estilos*/
           // $this->docexcel->getActiveSheet()->getStyle("A$fila:N$fila")->applyFromArray($style_todos_bordes);


           // $this->docexcel->getActiveSheet()->getStyle("A$fila:N$fila")->applyFromArray($styledatos3);
           //
           // $this->docexcel->getActiveSheet()->getStyle("K$fila:M$fila")->applyFromArray($style_datos_2);
           // $this->docexcel->getActiveSheet()->getStyle("A$fila")->applyFromArray($bordes);
           // $this->docexcel->getActiveSheet()->getStyle("B$fila")->applyFromArray($bordes);
           // $this->docexcel->getActiveSheet()->getStyle("C$fila")->applyFromArray($bordes);
           // $this->docexcel->getActiveSheet()->getStyle("D$fila")->applyFromArray($bordes);
           // $this->docexcel->getActiveSheet()->getStyle("E$fila")->applyFromArray($bordes);
           // $this->docexcel->getActiveSheet()->getStyle("F$fila")->applyFromArray($bordes);
           // $this->docexcel->getActiveSheet()->getStyle("G$fila")->applyFromArray($bordes);
           // $this->docexcel->getActiveSheet()->getStyle("H$fila")->applyFromArray($bordes);
           // $this->docexcel->getActiveSheet()->getStyle("I$fila")->applyFromArray($bordes);
           // $this->docexcel->getActiveSheet()->getStyle("J$fila")->applyFromArray($bordes);
           // $this->docexcel->getActiveSheet()->getStyle("K$fila")->applyFromArray($bordes);
           // $this->docexcel->getActiveSheet()->getStyle("L$fila")->applyFromArray($bordes);
           // $this->docexcel->getActiveSheet()->getStyle("M$fila")->applyFromArray($bordes);
           // $this->docexcel->getActiveSheet()->getStyle("N$fila")->applyFromArray($bordes);
           // $this->docexcel->getActiveSheet()->getStyle("F$fila:K$fila")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
           // $this->docexcel->getActiveSheet()->getStyle("M$fila:N$fila")->getAlignment()->setWrapText(true);
           //
           $fila++;


      }
      $fila_final = $fila - 1;
      /*Mandamos los estilos*/
      $this->docexcel->getActiveSheet()->getStyle("A$inicio:A$fila_final")->applyFromArray($style_todos_bordes);
      $this->docexcel->getActiveSheet()->getStyle("B$inicio:B$fila_final")->applyFromArray($style_todos_bordes);
      $this->docexcel->getActiveSheet()->getStyle("C$inicio:C$fila_final")->applyFromArray($style_todos_bordes);
      $this->docexcel->getActiveSheet()->getStyle("D$inicio:D$fila_final")->applyFromArray($style_todos_bordes);
      $this->docexcel->getActiveSheet()->getStyle("E$inicio:E$fila_final")->applyFromArray($style_todos_bordes);
      $this->docexcel->getActiveSheet()->getStyle("F$inicio:F$fila_final")->applyFromArray($style_todos_bordes);
      $this->docexcel->getActiveSheet()->getStyle("G$inicio:G$fila_final")->applyFromArray($style_todos_bordes);
      $this->docexcel->getActiveSheet()->getStyle("H$inicio:H$fila_final")->applyFromArray($style_todos_bordes);
      $this->docexcel->getActiveSheet()->getStyle("I$inicio:I$fila_final")->applyFromArray($style_todos_bordes);
      $this->docexcel->getActiveSheet()->getStyle("J$inicio:J$fila_final")->applyFromArray($style_todos_bordes);
      $this->docexcel->getActiveSheet()->getStyle("K$inicio:K$fila_final")->applyFromArray($style_todos_bordes);
      $this->docexcel->getActiveSheet()->getStyle("L$inicio:L$fila_final")->applyFromArray($style_todos_bordes);
      $this->docexcel->getActiveSheet()->getStyle("M$inicio:M$fila_final")->applyFromArray($style_todos_bordes);
      $this->docexcel->getActiveSheet()->getStyle("N$inicio:N$fila_final")->applyFromArray($style_todos_bordes);
      $this->docexcel->getActiveSheet()->getStyle("O$inicio:O$fila_final")->applyFromArray($style_todos_bordes);
      $this->docexcel->getActiveSheet()->getStyle("P$inicio:P$fila_final")->applyFromArray($style_todos_bordes);
      $this->docexcel->getActiveSheet()->getStyle("Q$inicio:Q$fila_final")->applyFromArray($style_todos_bordes);
      $this->docexcel->getActiveSheet()->getStyle("R$inicio:R$fila_final")->applyFromArray($style_todos_bordes);
      $this->docexcel->getActiveSheet()->getStyle("S$inicio:S$fila_final")->applyFromArray($style_todos_bordes);
      $this->docexcel->getActiveSheet()->getStyle("T$inicio:T$fila_final")->applyFromArray($style_todos_bordes);
      $this->docexcel->getActiveSheet()->getStyle("U$inicio:U$fila_final")->applyFromArray($style_todos_bordes);
      $this->docexcel->getActiveSheet()->getStyle("V$inicio:V$fila_final")->applyFromArray($style_todos_bordes);

      $this->docexcel->getActiveSheet()->getStyle("Q$inicio:Q$fila_final")->getAlignment()->setWrapText(true);
      $this->docexcel->getActiveSheet()->getStyle("R$inicio:R$fila_final")->getAlignment()->setWrapText(true);
                //
                //  $fila_final = $fila - 1;
                //
    /*Aqui calculamos los totales*/
    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $fila, 'Totales:');
    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6, $fila, "=SUM((G$inicio:G$fila_final))");
    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(7, $fila, "=SUM((H$inicio:H$fila_final))");
    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8, $fila, "=SUM((I$inicio:I$fila_final))");
    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(9, $fila, "=SUM((J$inicio:J$fila_final))");
    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(11, $fila, "=SUM((L$inicio:L$fila_final))");
    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(12, $fila, "=SUM((M$inicio:M$fila_final))");
    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(13, $fila, "=SUM((N$inicio:N$fila_final))");
    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(14, $fila, "=SUM((O$inicio:O$fila_final))");
    $this->docexcel->getActiveSheet()->mergeCells("A$fila:E$fila");
    $this->docexcel->getActiveSheet()->getStyle("G$fila:O$fila")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
    $this->docexcel->getActiveSheet()->getStyle("A$fila:O$fila")->applyFromArray($totales);
                //
                // $this->docexcel->getActiveSheet()->getStyle("A$fila:K$fila")->applyFromArray($totales);
      //   $numero_cuenta = substr($cabecera_datos['desc_cuenta'], 0, 3);
      //   $inicial_cuenta = substr($cabecera_datos['desc_cuenta'], 0, 0);
      //   $cuenta_acreedora = substr($cabecera_datos['desc_cuenta'], 0, 2);
      //
      //   if ($inicial_cuenta = '1') {
      //     if ($numero_cuenta = '124' OR  $numero_cuenta = '114') {
      //           $comportamiento = 'pasivo';
      //       }else{
      //             $comportamiento = 'activo';
      //            }
      //   }
      //   if ($inicial_cuenta = '4' or $inicial_cuenta = '6') {
      //     $comportamiento = 'activo';
      //   }
      //    /*Si la cuenta inicia con 2 o 3 o 5 pertenece a un pasivo*/
      //     if ($inicial_cuenta = '2' or $inicial_cuenta = '3' or $inicial_cuenta = '5') {
      //       $comportamiento = 'pasivo';
      //     }
      //
      //     if ($inicial_cuenta = '8') {
      //       if ($cuenta_acreedora = '81') {
      //         $comportamiento = 'activo';
      //       } else if ($cuenta_acreedora = '82') {
      //         $comportamiento = 'pasivo';
      //       }
      //     }
      //
      //   $anterior = 7;
      //   $this->docexcel->getActiveSheet()->setCellValue("J$fila",'TOTALES:');
      //   for ($i=7; $i < $fila; $i++) {
      //     $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(10, $fila, "=SUM((K$anterior:K$i))");
      //     $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(11, $fila, "=SUM((L$anterior:L$i))");
      //     if ($comportamiento == 'activo') {
      //     $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(12, $fila, "=SUM((K$fila-L$fila))");
      //   } elseif ($comportamiento == 'pasivo') {
      //     $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(12, $fila, "=SUM((L$fila-K$fila))");
      //   }
      //
      //     $this->docexcel->getActiveSheet()->getStyle("K$fila")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
      //     $this->docexcel->getActiveSheet()->getStyle("L$fila")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
      //     $this->docexcel->getActiveSheet()->getStyle("M$fila")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
      //   }
      //
      //   $this->docexcel->getActiveSheet()->getStyle("J$fila:M$fila")->applyFromArray($styleTotales);
      //   $this->docexcel->getActiveSheet()->getStyle("J$fila")->applyFromArray($bordes);
      //   $this->docexcel->getActiveSheet()->getStyle("K$fila")->applyFromArray($bordes);
      //   $this->docexcel->getActiveSheet()->getStyle("L$fila")->applyFromArray($bordes);
      //   $this->docexcel->getActiveSheet()->getStyle("M$fila")->applyFromArray($bordes);
      //
      //   $saldo_anterior = $this->saldo_anterior;
      //
      //   foreach ($saldo_anterior as $value3){
      //    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $anterior, 'SALDO ANTERIOR: ');
      //    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(10, $anterior, $value3['total_debe_anterior']);
      //    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(11, $anterior, $value3['total_haber_anterior']);
      //    $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(12, $anterior, $value3['saldo_anterior']);
      //    $this->docexcel->getActiveSheet()->getStyle("A$anterior:L$anterior")->applyFromArray($bordes);
      //    $this->docexcel->getActiveSheet()->getStyle("A$anterior:L$anterior")->applyFromArray($styleTotales);
      //    $this->docexcel->getActiveSheet()->getStyle("M$anterior")->applyFromArray($bordes);
      //    $this->docexcel->getActiveSheet()->getStyle("K$anterior")->applyFromArray($bordes);
      //    $this->docexcel->getActiveSheet()->getStyle("L$anterior")->applyFromArray($bordes);
      //    $this->docexcel->getActiveSheet()->getStyle("K$anterior:M$anterior")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat :: FORMAT_NUMBER_COMMA_SEPARATED1);
      //
      //    $this->docexcel->getActiveSheet()->mergeCells("A$anterior:J$anterior");
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
