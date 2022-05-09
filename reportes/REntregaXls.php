<?php
class REntregaXls
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

    var $datos_titulo;
    var $datos_detalle;
    var $ancho_hoja;
    var $gerencia;
    var $numeracion;
    var $ancho_sin_totales;
    var $cantidad_columnas_estaticas;
    var $s1;
    var $t1;
    var $tg1;
    var $total;
    var $datos_entidad;
    var $datos_periodo;
    var $ult_codigo_partida;
    var $ult_concepto;



    function __construct(CTParametro $objParam){
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

        $this->docexcel->setActiveSheetIndex(0);

        $this->docexcel->getActiveSheet()->setTitle($this->objParam->getParametro('titulo_archivo'));

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

    function datosHeader ( $detalle, $id_entrega) {

        $this->datos_detalle = $detalle;
        $this->id_entrega = $id_entrega;

    }

    function ImprimeCabera(){

    }

    function imprimeDatos(){
        $datos = $this->datos_detalle;
        $config = $this->objParam->getParametro('config');
        $columnas = 0;

        $styleTitulos = array(
            'font'  => array(
                'bold'  => true,
                'size'  => 9,
                'name'  => 'Arial'
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'c5d9f1')
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ));


        $inicio_filas = 7;
        $this->docexcel->getActiveSheet()->getStyle('A7:L7')->applyFromArray($styleTitulos);

        $this->docexcel->getActiveSheet()->getStyle('E:F')->getNumberFormat()->setFormatCode('#,##0.00');

        //*************************************Cabecera*****************************************
        $this->docexcel->getActiveSheet()->getColumnDimension($this->equivalencias[0])->setWidth(20);
        $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(0,$inicio_filas,'Cuenta Bancaria BoA');
        $this->docexcel->getActiveSheet()->getColumnDimension($this->equivalencias[1])->setWidth(20);
        $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1,$inicio_filas,'Clase de Gasto');
        $this->docexcel->getActiveSheet()->getColumnDimension($this->equivalencias[2])->setWidth(25);
        $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(2,$inicio_filas,'Categoria Prog.');
        $this->docexcel->getActiveSheet()->getColumnDimension($this->equivalencias[3])->setWidth(40);
        $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(3,$inicio_filas,'Partida');
        $this->docexcel->getActiveSheet()->getColumnDimension($this->equivalencias[4])->setWidth(20);
        $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(4,$inicio_filas,'Importe');
        $this->docexcel->getActiveSheet()->getColumnDimension($this->equivalencias[5])->setWidth(20);
        $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(5,$inicio_filas,'Importe Doc.');
        $this->docexcel->getActiveSheet()->getColumnDimension($this->equivalencias[6])->setWidth(20);
        $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(6,$inicio_filas,'Moneda');

        $this->docexcel->getActiveSheet()->getColumnDimension($this->equivalencias[7])->setWidth(20);
        $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(7,$inicio_filas,'Inporte Original');
        $this->docexcel->getActiveSheet()->getColumnDimension($this->equivalencias[8])->setWidth(20);
        $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(8,$inicio_filas,'Moneda Original');

        $this->docexcel->getActiveSheet()->getColumnDimension($this->equivalencias[9])->setWidth(10);
        $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(9,$inicio_filas,'ID Cbte');
        $this->docexcel->getActiveSheet()->getColumnDimension($this->equivalencias[10])->setWidth(30);
        $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(10,$inicio_filas,'Concepto');
        $this->docexcel->getActiveSheet()->getColumnDimension($this->equivalencias[11])->setWidth(20);
        $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(11,$inicio_filas,'Beneficiario');

        //*************************************Fin Cabecera*****************************************

        $fila = $inicio_filas+1;
        $contador = 1;
        $sw = true;

        $fila_ini = $fila;
        $fila_fin = $fila;
        $fila_ini_par = $fila;
        $fila_fin_par = $fila;
        $fila_ini_cg = $fila;
        $fila_fin_cg = $fila;
        $fila_ini_cb = $fila;
        $fila_fin_cb = $fila;


        $sumatoria = 0;
        $sumatoria_neto = 0;
        $sumatoria_par = 0;
        $sumatoria_neto_par = 0;
        $sumatoria_cg = 0;
        $sumatoria_neto_cg = 0;
        $sumatoria_cb = 0;
        $sumatoria_neto_cb = 0;
        $sumatoria_neto_gral = 0;
        $sumatoria_gral = 0;


        $sumatoria_par_ori = 0;
        $sumatoria_ori = 0;
        $sumatoria_cg_ori = 0;
        $sumatoria_cb_ori = 0;
        $sumatoria_gral_ori = 0;


        //EStilos para categorias programaticas

        $styleArrayGroup = array(
            'font'  => array('bold'  => true),
            'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER),
            'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb' => 'FFCCFF')),
            'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN))

        );

        $styleArrayTotal = array(
            'font'  => array('bold'  => true),
            'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'FFCCFF')),
            'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN))
        );

        //Estilos para partidas
        $styleArrayGroupPar = array(
            'font'  => array('bold'  => true),
            'alignment' => array('vertical' => PHPExcel_Style_Alignment::VERTICAL_TOP),
            'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb' => '33FF66')),
            'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN))
        );

        $styleArrayTotalPar = array(
            'font'  => array('bold'  => true),
            'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => '33FF66')),
            'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN))
        );

        //Estilos para CLASES DE GASTO
        $styleArrayGroupCg = array(
            'font'  => array('bold'  => true),
            'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER),
            'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb' => 'FFFF99')),
            'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN))
        );

        $styleArrayTotalCg = array(
            'font'  => array('bold'  => true),
            'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'FFFF99')),
            'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN))
        );

        $styleArrayAccount = array(
            'font'  => array('bold'  => true),
            'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER),
            'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb' => 'AA90C1')),
            'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN))

        );

        $styleArrayTotalAccount = array(
            'font'  => array('bold'  => true),
            'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'AA90C1')),
            'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN))
        );
        $styleArrayTotalGral = array(
            'font'  => array('bold'  => true),
            'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'AEB6BF')),
            'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN))
        );

        /////////////////////***********************************Detalle***********************************************


        $tmp_rec = $datos[0];
        for ($fi = 0; $fi <= count($datos); $fi++) {
            $value = $datos[$fi];
            if($value['importe_gasto_mb'] > 0){
                $importe_neto = $value['importe_gasto_mb'];
                $importe = $value['importe_debe_mb_completo'];
                $importe_original = $value['importe_debe'];
            }
            else{
                $importe_neto = $value['importe_recurso_mb'];
                $importe = $value['importe_haber_mb_completo'];
                $importe_original = $value['importe_haber'];
            }
            $importe_neto = round ($importe_neto,2);
            $importe = round ($importe,2);
            $importe_original = round ($importe_original,2);


            if(($tmp_rec['codigo'] != $value['codigo']) or ($tmp_rec['nro_cuenta'] != $value['nro_cuenta'])){
                $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(4,$fila,$sumatoria_neto_par);
                $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(5,$fila,$sumatoria_par);
                $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(7,$fila,$sumatoria_par_ori);
                $fila_fin_par = $fila;
                $this->docexcel->setActiveSheetIndex(0)->mergeCells("D".($fila_ini_par).":D".($fila_fin_par));
                $this->docexcel->setActiveSheetIndex(0)->getStyle("D".($fila_ini_par).":D".($fila_fin_par))->applyFromArray($styleArrayGroupPar);
                $this->docexcel->setActiveSheetIndex(0)->getStyle("D".($fila_fin_par).":L".($fila_fin_par))->applyFromArray($styleArrayTotalPar);
                $this->docexcel->setActiveSheetIndex(0)->getStyle("D".($fila_ini_par).":D".($fila_fin_par))->getAlignment()->setWrapText(true);
                for ($row = $fila_ini_par; $row <= $fila_fin_par-1; ++$row) {
                    $this->docexcel->setActiveSheetIndex(0)->getRowDimension($row)->setOutlineLevel(1)->setVisible(false)->setCollapsed(true);
                }
                $fila++;
                $fila_ini_par = $fila;
                $sumatoria_par = 0;
                $sumatoria_neto_par = 0;
                $sumatoria_par_ori = 0;

            }

            if(($tmp_rec['codigo_categoria'] != $value['codigo_categoria']) or ($tmp_rec['nro_cuenta'] != $value['nro_cuenta'])){
                $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(2,$fila,'TOTAL CATEGORIA');
                $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(4,$fila,$sumatoria_neto);
                $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(5,$fila,$sumatoria);
                $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(7,$fila,$sumatoria_ori);
                $fila_fin = $fila;
                $this->docexcel->setActiveSheetIndex(0)->mergeCells("C".($fila_ini).":C".($fila_fin-1));
                $this->docexcel->setActiveSheetIndex(0)->getStyle("C".($fila_ini).":C".($fila_fin))->applyFromArray($styleArrayGroup);
                $this->docexcel->setActiveSheetIndex(0)->getStyle("D".($fila_fin).":L".($fila_fin))->applyFromArray($styleArrayTotal);

                $fila++;
                $fila_ini = $fila;
                $sumatoria = 0;
                $sumatoria_neto = 0;

                $fila_ini_par = $fila;
                $sumatoria_par = 0;
                $sumatoria_neto_par = 0;

                $sumatoria_par_ori = 0;
                $sumatoria_ori = 0;
            }

            if(($tmp_rec['codigo_cg'] != $value['codigo_cg']) or ($tmp_rec['nro_cuenta'] != $value['nro_cuenta'])){
                $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1,$fila,'TOTAL GASTO');
                $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(4,$fila,$sumatoria_neto_cg);
                $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(5,$fila,$sumatoria_cg);
                $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(7,$fila,$sumatoria_cg_ori);
                $fila_fin_cg = $fila;
                $this->docexcel->setActiveSheetIndex(0)->mergeCells("B".($fila_ini_cg).":B".($fila_fin_cg-1));
                $this->docexcel->setActiveSheetIndex(0)->getStyle("B".($fila_ini_cg).":B".($fila_fin_cg))->applyFromArray($styleArrayGroupCg);
                $this->docexcel->setActiveSheetIndex(0)->getStyle("B".($fila_fin_cg).":L".($fila_fin_cg))->applyFromArray($styleArrayTotalCg);
                $fila++;
                $fila_ini_cg = $fila;
                $sumatoria_cg = 0;
                $sumatoria_neto_cg = 0;

                $fila_ini_par = $fila;
                $sumatoria_par = 0;
                $sumatoria_neto_par = 0;
                $fila_ini = $fila;
                $sumatoria = 0;
                $sumatoria_neto = 0;

                $sumatoria_par_ori = 0;
                $sumatoria_ori = 0;
                $sumatoria_cg_ori = 0;
            }

            if($tmp_rec['nro_cuenta'] != $value['nro_cuenta']){
                $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(0,$fila,'TOTAL CUENTA');
                $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(4,$fila,$sumatoria_neto_cb);
                $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(5,$fila,$sumatoria_cb);
                $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(7,$fila,$sumatoria_cb_ori);
                $fila_fin_cb = $fila;
                $this->docexcel->setActiveSheetIndex(0)->mergeCells("A".($fila_ini_cb).":A".($fila_fin_cb-1));
                $this->docexcel->setActiveSheetIndex(0)->getStyle("A".($fila_ini_cb).":A".($fila_fin_cb))->applyFromArray($styleArrayAccount);
                $this->docexcel->setActiveSheetIndex(0)->getStyle("A".($fila_fin_cb).":L".($fila_fin_cb))->applyFromArray($styleArrayTotalAccount);
                $fila++;
                $fila_ini_cg = $fila;
                $sumatoria_cg = 0;
                $sumatoria_neto_cg = 0;
                $sumatoria_cb = 0;
                $sumatoria_neto_cb = 0;

                $fila_ini_par = $fila;
                $sumatoria_par = 0;
                $sumatoria_neto_par = 0;
                $fila_ini = $fila;
                $sumatoria = 0;
                $sumatoria_neto = 0;

                $fila_ini_cb = $fila;

                $sumatoria_par_ori = 0;
                $sumatoria_ori = 0;
                $sumatoria_cg_ori = 0;
                $sumatoria_cb_ori = 0;
            }

            $sumatoria = $sumatoria + $importe;
            $sumatoria_neto = $sumatoria_neto + $importe_neto;
            $sumatoria_par = $sumatoria_par + $importe;
            $sumatoria_neto_par = $sumatoria_neto_par + $importe_neto;
            $sumatoria_cg = $sumatoria_cg + $importe;
            $sumatoria_neto_cg = $sumatoria_neto_cg + $importe_neto;
            $sumatoria_cb = $sumatoria_cb + $importe;
            $sumatoria_neto_cb = $sumatoria_neto_cb + $importe_neto;
            $sumatoria_gral = $sumatoria_gral + $importe;
            $sumatoria_neto_gral = $sumatoria_neto_gral + $importe_neto;

            $sumatoria_par_ori = $sumatoria_par_ori + $importe_original;
            $sumatoria_ori = $sumatoria_ori + $importe_original;
            $sumatoria_cg_ori = $sumatoria_cg_ori + $importe_original;
            $sumatoria_cb_ori = $sumatoria_cb_ori + $importe_original;
            $sumatoria_gral_ori = $sumatoria_gral_ori + $importe_original;

            if($fi != count($datos)){
                if($value['nro_cuenta'] == 'SIN CUENTA'){
                    $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(0,$fila,$value['nro_cuenta']);
                }else{
                    $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(0,$fila,$value['nro_cuenta'].'-'.$value['nombre_institucion']);
                }
                $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1,$fila,$value['codigo_cg'].'-'.$value['nombre_cg']);
                $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(2,$fila,$value['codigo_categoria']);
                $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(3,$fila,$value['codigo'].'-'.$value['nombre_partida']);
                $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(4,$fila,$importe_neto);
                $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(5,$fila,$importe);
                $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(6,$fila,'Bolivianos');
                $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(7,$fila,$importe_original);
                $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(8,$fila,$value['moneda_original']);
                $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(9,$fila,$value['id_int_comprobante_dev']);
                $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(10,$fila,$value['glosa1']);
                $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(11,$fila,$value['beneficiario']);
            }

            $tmp_rec = $value;
            $fila++;
        }
        $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(0,($fila-1),'TOTAL GENERAL');
        $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(4,($fila-1),$sumatoria_neto_gral);
        $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(5,($fila-1),$sumatoria_gral);
        $this->docexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(7,($fila-1),$sumatoria_gral_ori);
        $this->docexcel->setActiveSheetIndex(0)->getStyle("A".(($fila-1)).":L".(($fila-1)))->applyFromArray($styleArrayTotalGral);

        //ajustar testo en beneficiario y glosa
        $this->docexcel->setActiveSheetIndex(0)->getStyle("K".($inicio_filas).":L".($fila+1))->getAlignment()->setWrapText(true);

    }

    function imprimeTitulo($sheet){
        $titulo = "REporte de Entrega";
        $fechas = 'Del '.$this->objParam->getParametro('fecha_c31');



        //$sheet->setCellValueByColumnAndRow(0,1,$this->objParam->getParametro('titulo_rep'));
        $sheet->getStyle('A1')->getFont()->applyFromArray(array('bold'=>true,
            'size'=>12,
            'name'=>Arial));

        $sheet->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValueByColumnAndRow(0,1,strtoupper($titulo));
        $sheet->mergeCells('A1:D1');

        //DEPTOS TITLE
        $sheet->getStyle('A2')->getFont()->applyFromArray(array(
            'bold'=>true,
            'size'=>10,
            'name'=>Arial));

        $sheet->getStyle('A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $sheet->setCellValueByColumnAndRow(0,2,strtoupper("ID :      ".$this->objParam->getParametro('id_entrega')));
        $sheet->mergeCells('A2:D2');

        //FECHAS
        $sheet->getStyle('A3')->getFont()->applyFromArray(array(
            'bold'=>true,
            'size'=>10,
            'name'=>Arial));

        $sheet->getStyle('A3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $sheet->setCellValueByColumnAndRow(0,3,strtoupper("NÚMERO TRÁMITE :    ".$this->objParam->getParametro('nro_tramite')));
        //$sheet->setCellValueByColumnAndRow(0,3,$this->objParam->getParametro('nro_tramite'));
        $sheet->mergeCells('A3:D3');

        $sheet->getStyle('A4')->getFont()->applyFromArray(array(
            'bold'=>true,
            'size'=>10,
            'name'=>Arial));

        $sheet->getStyle('A4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $sheet->setCellValueByColumnAndRow(0,4,"FECHA :    ".$this->objParam->getParametro('fecha'));
        $sheet->mergeCells('A4:D4');


        $sheet->getStyle('A5')->getFont()->applyFromArray(array(
            'bold'=>true,
            'size'=>10,
            'name'=>Arial));

        $sheet->getStyle('A5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $sheet->setCellValueByColumnAndRow(0,5,"TIPO DE CAMBIO :    ".$this->objParam->getParametro('tipo_cambio_2'));
        $sheet->mergeCells('A5:D5');



    }



    function generarReporte(){


        $this->imprimeTitulo($this->docexcel->setActiveSheetIndex(0));
        $this->imprimeDatos();

        //echo $this->nombre_archivo; exit;
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $this->docexcel->setActiveSheetIndex(0);
        $this->objWriter = PHPExcel_IOFactory::createWriter($this->docexcel, 'Excel5');
        $this->objWriter->save($this->url_archivo);


    }


}

?>