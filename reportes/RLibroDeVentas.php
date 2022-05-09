<?php
// Extend the TCPDF class to create custom MultiRow
set_time_limit(0);//avoid timeout
ini_set('memory_limit','-1');
class RLibroDeVentas extends  ReportePDF {
    var $datos_titulo;
    var $datos_detalle;
    var $ancho_hoja;
    var $gerencia;
    var $numeracion;
    var $ancho_sin_totales;
    var $cantidad_columnas_estaticas;
    var $s1;
    var $s2;
    var $s3;
    var $s4;
    var $s5;
    var $s6;
    var $s7;
    var $s8;
    var $t1;
    var $t2;
    var $t3;
    var $t4;
    var $t5;
    var $t6;
    var $t7;
    var $t8;
    var $total;
    var $datos_entidad;
    var $datos_periodo;



    function datosHeader ( $detalle, $totales,$entidad, $periodo) {
        $this->ancho_hoja = $this->getPageWidth()-PDF_MARGIN_LEFT-PDF_MARGIN_RIGHT-10;
        $this->datos_detalle = $detalle;
        $this->datos_titulo = $totales;
        $this->datos_entidad = $entidad;
        $this->datos_periodo = $periodo;
        $this->subtotal = 0;
        $this->SetMargins(7, 48, 5);
    }

    function Header() {

        $white = array('LTRB' =>array('width' => 0.3, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(255, 255, 255)));
        $black = array('T' =>array('width' => 0.3, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));


        $this->Ln(3);
        //formato de fecha
        $newDate = date("d-m-Y", strtotime($this->objParam->getParametro('hasta')));

        //cabecera del reporte
        $this->Image(dirname(__FILE__).'/../../lib/imagenes/logos/logo.jpg', 10,5,40,20);
        $this->ln(5);





        $this->SetFont('','BU',12);
        $this->Cell(0,5,"LIBRO DE VENTAS ESTANDAR",0,1,'C');
        $this->SetFont('','BU',7);
        $this->Cell(0,5,"(Expresado en Bolivianos)",0,1,'C');
        $this->Ln(2);


        $this->SetFont('','',8);

        $height = 5;
        $width1 = 5;
        $esp_width = 10;
        $width_c1= 55;
        $width_c2= 92;
        $width3 = 40;
        $width4 = 75;


        if($this->objParam->getParametro('filtro_sql') == 'fechas'){

            $fecha_ini =$this->objParam->getParametro('fecha_ini');
            $fecha_fin = $this->objParam->getParametro('fecha_fin');


            $this->Cell($width1, $height, '', 0, 0, 'L', false, '', 0, false, 'T', 'C');
            $this->Cell($width_c1, $height, 'DESDE:', 0, 0, 'L', false, '', 0, false, 'T', 'C');
            $this->SetFont('', '');
            //$this->SetFillColor(192,192,192, true);
            $this->Cell($width_c2, $height, $fecha_ini, 0, 0, 'L', false, '', 0, false, 'T', 'C');

            $this->Cell($esp_width, $height, '', 0, 0, 'L', false, '', 0, false, 'T', 'C');
            $this->Cell(20, $height,'HASTA:', 0, 0, 'L', false, '', 0, false, 'T', 'C');
            $this->SetFont('', '');
            //$this->SetFillColor(192,192,192, true);
            $this->Cell(50, $height, $fecha_fin, 0, 0, 'L', false, '', 0, false, 'T', 'C');
        }
        else{
            $this->Cell($width1, $height, '', 0, 0, 'L', false, '', 0, false, 'T', 'C');
            $this->Cell($width_c1, $height, 'AÑO:', 0, 0, 'L', false, '', 0, false, 'T', 'C');
            $this->SetFont('', '');
            //$this->SetFillColor(192,192,192, true);
            $this->Cell($width_c2, $height, $this->datos_periodo['gestion'], 0, 0, 'L', false, '', 0, false, 'T', 'C');

            $this->Cell($esp_width, $height, '', 0, 0, 'L', false, '', 0, false, 'T', 'C');
            $this->Cell(20, $height,'MES:', 0, 0, 'L', false, '', 0, false, 'T', 'C');
            $this->SetFont('', '');
            //$this->SetFillColor(192,192,192, true);
            $this->Cell(50, $height, $this->datos_periodo['literal_periodo'], 0, 0, 'L', false, '', 0, false, 'T', 'C');
        }


        $this->Ln();

        $this->Cell($width1, $height, '', 0, 0, 'L', false, '', 0, false, 'T', 'C');
        $this->Cell($width_c1, $height, 'NOMBRE O RAZON SOCIAL:', 0, 0, 'L', false, '', 0, false, 'T', 'C');
        $this->SetFont('', '');
        //$this->SetFillColor(192,192,192, true);
        $this->Cell($width_c2, $height, $this->datos_entidad['nombre'].' ('.$this->datos_entidad['direccion_matriz'].')', 0, 0, 'L', false, '', 0, false, 'T', 'C');

        $this->Cell($esp_width, $height, '', 0, 0, 'L', false, '', 0, false, 'T', 'C');
        $this->Cell(20, $height,'NIT:', 0, 0, 'L', false, '', 0, false, 'T', 'C');
        $this->SetFont('', '');
        //$this->SetFillColor(192,192,192, true);
        $this->Cell(50, $height, $this->datos_entidad['nit'], 0, 0, 'L', false, '', 0, false, 'T', 'C');



        $this->Ln(6);

        $this->SetFont('','B',4);
        $this->generarCabecera();


    }

    function generarReporte() {

        $this->setFontSubsetting(false);
        $this->AddPage();



        $sw = false;
        $concepto = '';




        $this->generarCuerpo($this->datos_detalle);

        if($this->s1 != 0){
            $this->cerrarCuadro();
            $this->cerrarCuadroTotal();
        }

        $this->Ln(2);


    }
    function generarCabecera(){



        //armca caecera de la tabla
        $conf_par_tablewidths=array(10,13,18,22,8,13,45,15,15,15,15,15,15,15,14,20);
        $conf_par_tablealigns=array('C','C','C','C','C','C','C','C','C','C','C','C','C','C','C','C');
        $conf_par_tablenumbers=array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
        $conf_tableborders=array();
        $conf_tabletextcolor=array();

        $this->tablewidths=$conf_par_tablewidths;
        $this->tablealigns=$conf_par_tablealigns;
        $this->tablenumbers=$conf_par_tablenumbers;
        $this->tableborders=$conf_tableborders;
        $this->tabletextcolor=$conf_tabletextcolor;

        $RowArray = array(
            's0'  => 'Nº',
            's1' => 'FECHA DE LA FACTURA',
            's2' => 'Nº de LA FACTURA',
            's3' => 'Nº de AUTORIZACION',
            's4' => 'ESTADO',
            's5' => 'NIT/CI CLIENTE',
            's6' => 'NOMBRE O RAZON SOCIAL',
            's7' => "\nIMPORTE TOTAL \nDE LA VENTA\n\nA",
            's8' => "IMPORTE ICE/IEHD\n/IPJ/TASAS/OTROS \n NO SUJETOS AL IVA\n\nB",
            's9' => "EXPORTACIONES \nY OPERACIONES EXENTAS\n\nC",
            's10' => "\nVENTAS GRAVADAS \nA TASA CERO\n\nD",
            's11' => "\nSUBTOTAL\n\n\nE = A-B-C-D",
            's12' => "DESCUENTOS, BONIFICACIONES Y REBAJAS SUJETAS AL IVA\n F",
            's13' => "IMPORTE BASE \nPARA DÉBITO \nFISCAL\n\nG = E-F",
            's14' => "\nDÉBITO FISCAL\n\n\nH = G*13%",
            's15' => 'CÓDIGO DE CONTROL'

        );

        $this-> MultiRow($RowArray,false,1);


    }

    function generarCuerpo($detalle){

        $count = 1;
        $sw = 0;
        $ult_region = '';
        $fill = 0;

        $this->total = count($detalle);

        $this->s1 = 0;
        $this->s2 = 0;
        $this->s3 = 0;
        $this->s4 = 0;
        $this->s5 = 0;
        $this->s6 = 0;
        //var_dump('$detalle 22222', $detalle);exit;
        foreach ($detalle as $val) {

            $this->imprimirLinea($val,$count,$fill);
            $fill = !$fill;
            $count = $count + 1;
            $this->total = $this->total -1;
            $this->revisarfinPagina();

        }



    }

    function imprimirLinea($val,$count,$fill){

        $this->SetFillColor(224, 235, 255);
        $this->SetTextColor(0);
        $this->SetFont('','',5);

        $conf_par_tablewidths=array(10,13,18,22,8,13,45,15,15,15,15,15,15,15,14,20);
        $conf_par_tablealigns=array('C','C','L','C','C','L','L','R','R','R','R','R','R','R','R','L');
        $conf_par_tablenumbers=array(0,0,0,0,0,0,0,2,2,2,2,2,2,2,2,0);
        $conf_tableborders=array();//array('LR','LR','LR','LR','LR','LR','LR','LR','LR','LR','LR','LR','LR','LR','LR','LR','LR','LR');

        $this->tablewidths=$conf_par_tablewidths;
        $this->tablealigns=$conf_par_tablealigns;
        $this->tablenumbers=$conf_par_tablenumbers;
        $this->tableborders=$conf_tableborders;
        $this->tabletextcolor=$conf_tabletextcolor;

        $this->caclularMontos($val);

        $newDate = date("d/m/Y", strtotime( $val['fecha']));

        $subtotal = $val['importe_total_venta'] - $val['importe_otros_no_suj_iva'] - $val['exportacion_excentas'] - $val['ventas_tasa_cero'];
        $debito_fiscal = $subtotal - $val['descuento_rebaja_suj_iva'];

        $RowArray = array(
            's0'  => $count,
            's1' => date("d/m/Y", strtotime($val['fecha_factura'])),
            's2' => $val['nro_factura'],
            's3' => $val['nro_autorizacion'],
            's4' => $val['estado'],
            's5' => $val['nit_ci_cli'],
            's6' => $val['razon_social_cli'],
            's7' => $val['importe_total_venta'],
            's8' => $val['importe_otros_no_suj_iva'],
            's9' => $val['exportacion_excentas'],
            's10' => $val['ventas_tasa_cero'],
            's11' => $subtotal,
            's12' => $val['descuento_rebaja_suj_iva'],
            's13' => $debito_fiscal,
            's14' => $debito_fiscal * 0.13,
            's15' => $val['codigo_control']);

        $this-> MultiRow($RowArray,false,0);

    }


    function revisarfinPagina(){
        $dimensions = $this->getPageDimensions();
        $hasBorder = false; //flag for fringe case

        $startY = $this->GetY();
        $this->getNumLines($row['cell1data'], 80);

        if (($startY + 9) + $dimensions['bm'] > ($dimensions['hk'])) {

            $this->cerrarCuadro();
            $this->cerrarCuadroTotal();
            $k = 	($startY + 9) + $dimensions['bm'] - ($dimensions['hk']);


            if($this->total!= 0){
                $this->AddPage();
            }



        }


    }



    function caclularMontos($val){

        $subtotal = $val['importe_total_venta'] - $val['importe_otros_no_suj_iva'] - $val['exportacion_excentas'] - $val['ventas_tasa_cero'];
        $debito_fiscal = $subtotal - $val['descuento_rebaja_suj_iva'];

        $this->s1 = $this->s1 + $val['importe_total_venta'];
        $this->s2 = $this->s2 + $val['importe_otros_no_suj_iva'];
        $this->s3 = $this->s3 + $val['exportacion_excentas'];
        $this->s4 = $this->s4 + $val['ventas_tasa_cero'];

        $this->s5 = $this->s5 + $subtotal;
        $this->s6 = $this->s6 + $val['descuento_rebaja_suj_iva'];
        $this->s7 = $this->s7 + $debito_fiscal;
        $this->s8 = $this->s8 + $debito_fiscal * 0.13;


        $this->t1 = $this->t1 + $val['importe_total_venta'];
        $this->t2 = $this->t2 + $val['importe_otros_no_suj_iva'];
        $this->t3 = $this->t3 + $val['exportacion_excentas'];
        $this->t4 = $this->t4 + $val['ventas_tasa_cero'];

        $this->t5 = $this->t5 + $subtotal;
        $this->t6 = $this->t6 + $val['descuento_rebaja_suj_iva'];
        $this->t7 = $this->t7 + $debito_fiscal;
        $this->t8 = $this->t8 + $debito_fiscal * 0.13;



    }
    function cerrarCuadro(){


        //si noes inicio termina el cuardro anterior
        $conf_par_tablewidths=array(6+15+15+24+13+19+29,17,16,16,16,16,16,14,16);
        $conf_par_tablealigns=array('R','R','R','R','R','R','R','R','R');
        $conf_par_tablenumbers=array(0,2,2,2,2,2,2,2,2);
        $conf_par_tableborders=array('T','LRTB','LRTB','LRTB','LRTB','LRTB','LRTB','LRTB','LRTB');


        //coloca el total de egresos
        //coloca el total de la partida
        $this->tablewidths=$conf_par_tablewidths;
        $this->tablealigns=$conf_par_tablealigns;
        $this->tablenumbers=$conf_par_tablenumbers;
        $this->tableborders=$conf_par_tableborders;

        $RowArray = array(
            'espacio' => 'Subtotal: ',
            's1' => $this->s1,
            's2' => $this->s2,
            's3' => $this->s3,
            's4' => $this->s4,
            's5' => $this->s5,
            's6' => $this->s6,
            's7' => $this->s7,
            's8' => $this->s8
        );

        $this-> MultiRow($RowArray,false,1);

        $this->s1 = 0;
        $this->s2 = 0;
        $this->s3 = 0;
        $this->s4 = 0;
        $this->s5 = 0;
        $this->s6 = 0;
        $this->s7 = 0;
        $this->s8 = 0;


    }

    function cerrarCuadroTotal(){


        //si noes inicio termina el cuardro anterior


        $conf_par_tablewidths=array(6+15+15+24+13+19+29,17,16,16,16,16,16,14,16);
        $conf_par_tablealigns=array('R','R','R','R','R','R','R','R','R');
        $conf_par_tablenumbers=array(0,2,2,2,2,2,2,2,2);
        $conf_par_tableborders=array('T','LRTB','LRTB','LRTB','LRTB','LRTB','LRTB','LRTB','LRTB');

        //coloca el total de egresos
        //coloca el total de la partida
        $this->tablewidths=$conf_par_tablewidths;
        $this->tablealigns=$conf_par_tablealigns;
        $this->tablenumbers=$conf_par_tablenumbers;
        $this->tableborders=$conf_par_tableborders;

        $RowArray = array(
            'espacio' => 'TOTAL: ',
            't1' => $this->t1,
            't2' => $this->t2,
            't3' => $this->t3,
            't4' => $this->t4,
            't5' => $this->t5,
            't6' => $this->t6,
            't7' => $this->t7,
            't8' => $this->t8
        );

        $this-> MultiRow($RowArray,false,1);

    }


}
?>