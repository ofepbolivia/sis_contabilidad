<?php

class RepDocCompraVentaExt
{
    private $docexcel;
    private $objWriter;
    private $nombre_archivo;
    private $hoja;
    private $columnas=array();
    private $fila;
    private $equivalencias=array();

    private $indice, $m_fila, $titulo;
    private $swEncabezado=0; //variable que define si ya se imprimi� el encabezado
    private $objParam;
    public  $url_archivo;
    private $resumen = array();
    private $resumen_regional = array();

    function __construct(CTParametro $objParam){

        //reducido menos 23,24,26,27,29,30
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

        $this->equivalencias=array(0=>'A',1=>'B',2=>'C',3=>'D',4=>'E',5=>'F',6=>'G',7=>'H',8=>'I',
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

    //function imprimeIniciados(){
    function imprimeCabecera(){
        $this->docexcel->getActiveSheet()->setTitle('Procesos Adjudicados');
        $this->docexcel->setActiveSheetIndex(0);

        //*************************************TITULO*****************************************

        $styleTitulos1 = array(
            'font' => array(
                'bold' => true,
                'size' => 12,
                'name' => 'Arial'
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
        );

        $styleTitulos3 = array(
            'font' => array(
                'bold' => true,
                'size' => 11,
                'name' => 'Arial'
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),

        );

        //titulos

        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, 2, 'PLANILLA MENSUAL');
        $this->docexcel->getActiveSheet()->getStyle('A2:AH2')->applyFromArray($styleTitulos1);
        $this->docexcel->getActiveSheet()->mergeCells('A2:AH2');
        $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, 3, 'Del: ' . $this->objParam->getParametro('fecha_ini') . '   Al: ' . $this->objParam->getParametro('fecha_fin'));
        $this->docexcel->getActiveSheet()->getStyle('A3:AH3')->applyFromArray($styleTitulos3);
        $this->docexcel->getActiveSheet()->mergeCells('A3:AH3');

        //*************************************FIN TITULO*****************************************


        $this->docexcel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('B')->setWidth(50);
        $this->docexcel->getActiveSheet()->getColumnDimension('C')->setWidth(18);
        $this->docexcel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
        $this->docexcel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
        $this->docexcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('G')->setWidth(50);
        $this->docexcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('I')->setWidth(30);
        $this->docexcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
        $this->docexcel->getActiveSheet()->getColumnDimension('K')->setWidth(10);
        $this->docexcel->getActiveSheet()->getColumnDimension('L')->setWidth(10);
        $this->docexcel->getActiveSheet()->getColumnDimension('M')->setWidth(18);
        $this->docexcel->getActiveSheet()->getColumnDimension('N')->setWidth(20);
        $this->docexcel->getActiveSheet()->getColumnDimension('O')->setWidth(18);
        $this->docexcel->getActiveSheet()->getColumnDimension('P')->setWidth(18);
        $this->docexcel->getActiveSheet()->getColumnDimension('Q')->setWidth(18);
        $this->docexcel->getActiveSheet()->getColumnDimension('R')->setWidth(18);
        $this->docexcel->getActiveSheet()->getColumnDimension('S')->setWidth(18);
        $this->docexcel->getActiveSheet()->getColumnDimension('T')->setWidth(18);
        $this->docexcel->getActiveSheet()->getColumnDimension('U')->setWidth(18);
        $this->docexcel->getActiveSheet()->getColumnDimension('V')->setWidth(18);
        $this->docexcel->getActiveSheet()->getColumnDimension('W')->setWidth(18);
        $this->docexcel->getActiveSheet()->getColumnDimension('X')->setWidth(18);
        $this->docexcel->getActiveSheet()->getColumnDimension('Y')->setWidth(18);
        $this->docexcel->getActiveSheet()->getColumnDimension('Z')->setWidth(18);
        $this->docexcel->getActiveSheet()->getColumnDimension('AA')->setWidth(18);
        $this->docexcel->getActiveSheet()->getColumnDimension('AB')->setWidth(18);
        $this->docexcel->getActiveSheet()->getColumnDimension('AC')->setWidth(18);
        $this->docexcel->getActiveSheet()->getColumnDimension('AD')->setWidth(18);
        $this->docexcel->getActiveSheet()->getColumnDimension('AE')->setWidth(18);
        $this->docexcel->getActiveSheet()->getColumnDimension('AF')->setWidth(18);
        $this->docexcel->getActiveSheet()->getColumnDimension('AG')->setWidth(18);
        $this->docexcel->getActiveSheet()->getColumnDimension('AH')->setWidth(25);


        $styleTitulos = array(
            'font'  => array(
                'bold'  => true,
                'size'  => 8,
                'name'  => 'Arial',
                'color' => array(
                    'rgb' => 'FEFFFF'
                )
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                    'rgb' => '172673'
                )
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ));

        $styleTitulosRed = array(
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
                    'rgb' => 'DC0502'
                )
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ));
        $this->docexcel->getActiveSheet()->getStyle('A4:AH4')->getAlignment()->setWrapText(true);

        $this->docexcel->getActiveSheet()->getStyle('A4:AH4')->applyFromArray($styleTitulos);

        $this->docexcel->getActiveSheet()->getStyle('A4')->applyFromArray($styleTitulosRed);
        $this->docexcel->getActiveSheet()->getStyle('H4:T4')->applyFromArray($styleTitulosRed);
        $this->docexcel->getActiveSheet()->getStyle('Y4:AG4')->applyFromArray($styleTitulosRed);

        //*************************************Cabecera*****************************************
        $this->docexcel->getActiveSheet()->setCellValue('A4','NRO PROVEEDOR');
        $this->docexcel->getActiveSheet()->setCellValue('B4','RAZON SOCIAL');
        $this->docexcel->getActiveSheet()->setCellValue('C4','CUIT');
        $this->docexcel->getActiveSheet()->setCellValue('D4','CONDICION IVA');
        $this->docexcel->getActiveSheet()->setCellValue('E4','ACTIVIDAD');
        $this->docexcel->getActiveSheet()->setCellValue('F4','COSTO DIRECTO');
        $this->docexcel->getActiveSheet()->setCellValue('G4','DESCRIPCION DEL BIEN O SERVICIO');
        $this->docexcel->getActiveSheet()->setCellValue('H4','FECHA DE PAGO');
        $this->docexcel->getActiveSheet()->setCellValue('I4','FORMA DE PAGO');
        $this->docexcel->getActiveSheet()->setCellValue('J4','FECHA FACTURA');
        $this->docexcel->getActiveSheet()->setCellValue('K4','TIPO');
        $this->docexcel->getActiveSheet()->setCellValue('L4','LETRA');
        $this->docexcel->getActiveSheet()->setCellValue('M4','C. EMISOR');
        $this->docexcel->getActiveSheet()->setCellValue('N4','NRO COMPROBANTE');
        $this->docexcel->getActiveSheet()->setCellValue('O4','EXENTO');
        $this->docexcel->getActiveSheet()->setCellValue('P4','NO GRAVADO');
        $this->docexcel->getActiveSheet()->setCellValue('Q4','BASE 21%');
        $this->docexcel->getActiveSheet()->setCellValue('R4','BASE 27%');
        $this->docexcel->getActiveSheet()->setCellValue('S4','BASE 10,5%');
        $this->docexcel->getActiveSheet()->setCellValue('T4','BASE 2,5%');
        $this->docexcel->getActiveSheet()->setCellValue('U4','IVA 21%');
        $this->docexcel->getActiveSheet()->setCellValue('V4','IVA 27%');
        $this->docexcel->getActiveSheet()->setCellValue('W4','IVA 10,5%');
        $this->docexcel->getActiveSheet()->setCellValue('X4','IVA 2,5%');
        $this->docexcel->getActiveSheet()->setCellValue('Y4','PERCEPCION IIBB CABA');
        $this->docexcel->getActiveSheet()->setCellValue('Z4','PERCEPCION IIBB BUE');
        $this->docexcel->getActiveSheet()->setCellValue('AA4','PERCEPCION IIBB IVA');
        $this->docexcel->getActiveSheet()->setCellValue('AB4','PERCEPCION IIBB SALTA');
        $this->docexcel->getActiveSheet()->setCellValue('AC4','IMP. INTERNOS');
        $this->docexcel->getActiveSheet()->setCellValue('AD4','PERCEPCION IIBB TUCUMAN');
        $this->docexcel->getActiveSheet()->setCellValue('AE4','PERCEPCION IIBB CORRIENTES');
        $this->docexcel->getActiveSheet()->setCellValue('AF4','OTROS IMPUESTOS');
        $this->docexcel->getActiveSheet()->setCellValue('AG4','PERCEPCION IIBB NEUQUEN');
        $this->docexcel->getActiveSheet()->setCellValue('AH4','TOTAL');


    }

    function generarDatos()
    {
        //*************************************Detalle*****************************************
        $columna = 0;
        //$fila = 2;

        //$this->numero = 1;
        $fila = 5;
        $datos = $this->objParam->getParametro('datos');
        $this->imprimeCabecera(0);

        foreach($datos as $value) {

            foreach ($value as $key => $val) {

                $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($columna,$fila,$val);
                $columna++;
            }
            $fila++;
            $columna = 0;
        }


        //************************************************Fin Detalle***********************************************

    }


    function generarReporte() {
        $this->objWriter = PHPExcel_IOFactory::createWriter($this->docexcel, 'Excel5');
        $this->objWriter->save($this->url_archivo);

    }


}

?>