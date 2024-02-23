<?php
// Extend the TCPDF class to create custom MultiRow
class RLibroVentasNCDPDF extends  ReportePDF {
    var $datos_titulo;
    var $datos_detalle;
    var $ancho_hoja;
    var $gerencia;
    var $numeracion;
    var $ancho_sin_totales;
    var $cantidad_columnas_estaticas;

    var $categoria;
    var $modalidad;
    var $lugar;

    var $bandera_header;

    function setDatos($datos) {
        $this->datos = $datos; //var_dump($datos);exit;
    }

    function Header() {

        $this->SetMargins(3, 40, 2);

        $this->Image(dirname(__FILE__).'/../../lib'.$_SESSION['_DIR_LOGO'], 16, 5, 30, 10);
        $this->ln(2);
        $this->SetFont('', 'B', 12);
        $this->Cell(0, 5, "LIBRO DE VENTAS", 0, 1, 'C');
        $this->Cell(0, 5, "NOTAS DE CREDITO-DEBITO", 0, 1, 'C');
        $this->SetFont('', 'B', 9);
        $this->Cell(0, 3, "(Expresado en Bolivianos)", 0, 1, 'C');


        $tbl_head = '<table border="0" style="font-size: 9pt;">
                        <tr><td width="5%" style="text-align: center"></td><td width="60%" style="text-align: left">Año: '.$this->datos[0]['gestion'].'</td> <td width="20%" style="text-align: left">Mes:'.$this->datos[0]['periodo'].'</td></tr>
                        <tr><td width="5%" style="text-align: center"></td><td width="60%" style="text-align: left">Nombre o Razón Social: './*$this->datos[0]['razon_empresa']*/'Boliviana de Aviación BOA (Av. Simón López Nro. 1582)'.'</td><td width="20%" style="text-align: left">NIT: ' .$this->datos[0]['nit_empresa'] . '</td></tr>
                        </table>
                        ';
        $this->writeHTML($tbl_head);


        $this->tablewidths = array(10, 16, 16, 25, 17, 48, 19, 19, 25, 19, 17, 22, 22);
        $this->tablealigns = array('C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C');
        $this->tablenumbers = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);


        $this->SetFont('', 'B', 5);


        $RowArray = array(
            's0'  => "Nº",
            's1'  => "FECHA NOTA\nDE CRÉDITO-\nDÉBITO",
            's2'  => "N° DE NOTA\nDE CRÉDITO-\nDÉBITO",
            's3'  => "N° DE\nAUTORIZACIÓN",
            's4'  => "NIT PROVEEDOR",
            's5'  => "NOMBRE O\nRAZÓN SOCIAL\nPROVEEDOR",
            's6'  => "IMPORTE TOTAL\nDE LA DEVOLUCIÓN\nO RESCISIÓN\nEFECTUADA\nA",
            's7'  => "\nDÉBITO FISCAL\n\n\nB = A * 13%",
            's8'  => "CODIGO DE CONTROL DE LA\nNOTA DE CRÉDITO-DÉBITO",
            's9'  => "FECHA FACTURA\nORIGINAL",
            's10' => "N° FACTURA ORIGINAL",
            's11' => "N° DE AUTORIZACIÓN\nFACTURA ORIGINAL",
            's12' => "IMPORTE TOTAL FACTURA\nORIGINAL"

        );

        $this-> MultiRow($RowArray,false,1);




        $columnas = 0;

        $this->ancho_hoja = $this->getPageWidth() - PDF_MARGIN_LEFT - PDF_MARGIN_RIGHT - 10;
        //$this->SetMargins(3, 40, 2);
    }

    function generarReporte(){

        $this->setFontSubsetting(false);
        $this->SetMargins(3, 40, 2);
        $this->AddPage();
        //$this->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        //iniciacion de datos

        $this->numeracion = 1;


        $total_devolucion_par = 0;
        $total_rciva_par = 0;
        $total_importe_par = 0;

        $total_devolucion_gen = 0;
        $total_rciva_gen = 0;
        $total_importe_gen = 0;

        $this->tablewidths = array(10, 16, 16, 25, 17, 48, 19, 19, 25, 19, 17, 22, 22);
        $this->tablealigns = array('C', 'C', 'C', 'C', 'C', 'L', 'R', 'R', 'L', 'C', 'C', 'C', 'R');
        $this->tablenumbers = array(0, 0, 0, 0, 0, 0, 2, 2, 0, 0, 0, 0, 2);



        $this->Ln(7);
        $contador_reg = 0;
        foreach ($this->datos as $key => $value) {

            if ($value['codigo_control']== null || $value['codigo_control'] == '') {
                $codigo_control = '0';
            }else{
                $codigo_control = $value['codigo_control'];
            }

            $contador_reg++;

            $this->SetFont('','',5);
            if (strlen($value['razon_social']) < 35){
                $this->UniRow(array(
                    $this->numeracion, date("d/m/Y", strtotime( $value['fecha_nota'])) , $value['num_nota'], $value['num_autorizacion'], /*$value['estado'],*/ $value['nit'], $value['razon_social'],
                    $value['total_devuelto'], $value['rc_iva'], $codigo_control, date("d/m/Y", strtotime( $value['fecha_original'])), $value['num_factura'], $value['num_autorizacion_original'],
                    $value['monto_total_fac']
                ),false,0);
            }else{
                $this->MultiRow(array(
                    $this->numeracion, date("d/m/Y", strtotime( $value['fecha_nota'])), $value['num_nota'], $value['num_autorizacion'], /*$value['estado'],*/ $value['nit'], $value['razon_social'],
                    $value['total_devuelto'], $value['rc_iva'], $codigo_control, date("d/m/Y", strtotime( $value['fecha_original'])), $value['num_factura'], $value['num_autorizacion_original'],
                    $value['monto_total_fac']
                ),false,0);
            }

            if ( $contador_reg < 37 ) {
                //suma totales
                $total_devolucion_par += $value['total_devuelto'];
                $total_rciva_par += $value['rc_iva'];
                $total_importe_par += $value['monto_total_fac'];
            }
            //var_dump($this->getY());
            if ( $contador_reg == 36 ) {

                $this->SetFont('','B',7);
                $this->Cell($this->tablewidths[0],3,'','T',0,'R');
                $this->Cell($this->tablewidths[1],3,'','T',0,'R');
                $this->Cell($this->tablewidths[2],3,'','T',0,'R');
                $this->Cell($this->tablewidths[3],3,'','T',0,'R');
                //$this->Cell($this->tablewidths[4],3,'','T',0,'R');
                $this->Cell($this->tablewidths[4],3,'','T',0,'R');
                $this->Cell($this->tablewidths[5],3,'TOTAL PARCIALES','T',0,'R');
                $this->Cell($this->tablewidths[6],3,number_format($total_devolucion_par,2),'T',0,'R');
                $this->Cell($this->tablewidths[7],3,number_format($total_rciva_par,2),'T',0,'R');
                $this->Cell($this->tablewidths[8],3,'','T',0,'R');
                $this->Cell($this->tablewidths[9],3,'','T',0,'R');
                $this->Cell($this->tablewidths[10],3,'','T',0,'R');
                $this->Cell($this->tablewidths[11],3,'','T',0,'R');
                $this->Cell($this->tablewidths[12],3,number_format($total_importe_par,2),'T',1,'R');

                $total_devolucion_gen += $total_devolucion_par;
                $total_rciva_gen += $total_rciva_par;
                $total_importe_gen += $total_importe_par;

                $this->SetFont('','B',7);
                $this->Cell($this->tablewidths[0],3,'','B',0,'R');
                $this->Cell($this->tablewidths[1],3,'','B',0,'R');
                $this->Cell($this->tablewidths[2],3,'','B',0,'R');
                $this->Cell($this->tablewidths[3],3,'','B',0,'R');
                //$this->Cell($this->tablewidths[4],3,'','B',0,'R');
                $this->Cell($this->tablewidths[4],3,'','B',0,'R');
                $this->Cell($this->tablewidths[5],3,'TOTAL GENERALES','B',0,'R');
                $this->Cell($this->tablewidths[6],3,number_format($total_devolucion_gen,2),'B',0,'R');
                $this->Cell($this->tablewidths[7],3,number_format($total_rciva_gen,2),'B',0,'R');
                $this->Cell($this->tablewidths[8],3,'','B',0,'R');
                $this->Cell($this->tablewidths[9],3,'','B',0,'R');
                $this->Cell($this->tablewidths[10],3,'','B',0,'R');
                $this->Cell($this->tablewidths[11],3,'','B',0,'R');
                $this->Cell($this->tablewidths[12],3,number_format($total_importe_gen,2),'B',0,'R');

                $total_devolucion_par = 0;
                $total_rciva_par = 0;
                $total_importe_par = 0;

                $contador_reg = 1;
                $this->setFontSubsetting(false);
                $this->SetMargins(3, 40, 2);
                $this->AddPage();
            }

            $this->numeracion++;
        }//exit;

        $this->SetFont('','B',7);
        $this->Cell($this->tablewidths[0],3,'','T',0,'R');
        $this->Cell($this->tablewidths[1],3,'','T',0,'R');
        $this->Cell($this->tablewidths[2],3,'','T',0,'R');
        $this->Cell($this->tablewidths[3],3,'','T',0,'R');
        //$this->Cell($this->tablewidths[4],3,'','T',0,'R');
        $this->Cell($this->tablewidths[4],3,'','T',0,'R');
        $this->Cell($this->tablewidths[5],3,'TOTAL PARCIALES','T',0,'R');
        $this->Cell($this->tablewidths[6],3,number_format($total_devolucion_par,2),'T',0,'R');
        $this->Cell($this->tablewidths[7],3,number_format($total_rciva_par,2),'T',0,'R');
        $this->Cell($this->tablewidths[8],3,'','T',0,'R');
        $this->Cell($this->tablewidths[9],3,'','T',0,'R');
        $this->Cell($this->tablewidths[10],3,'','T',0,'R');
        $this->Cell($this->tablewidths[11],3,'','T',0,'R');
        $this->Cell($this->tablewidths[12],3,number_format($total_importe_par,2),'T',1,'R');

        $total_devolucion_gen += $total_devolucion_par;
        $total_rciva_gen += $total_rciva_par;
        $total_importe_gen += $total_importe_par;

        $this->SetFont('','B',7);
        $this->Cell($this->tablewidths[0],3,'','B',0,'R');
        $this->Cell($this->tablewidths[1],3,'','B',0,'R');
        $this->Cell($this->tablewidths[2],3,'','B',0,'R');
        $this->Cell($this->tablewidths[3],3,'','B',0,'R');
        //$this->Cell($this->tablewidths[4],3,'','B',0,'R');
        $this->Cell($this->tablewidths[4],3,'','B',0,'R');
        $this->Cell($this->tablewidths[5],3,'TOTAL GENERALES','B',0,'R');
        $this->Cell($this->tablewidths[6],3,number_format($total_devolucion_gen,2),'B',0,'R');
        $this->Cell($this->tablewidths[7],3,number_format($total_rciva_gen,2),'B',0,'R');
        $this->Cell($this->tablewidths[8],3,'','B',0,'R');
        $this->Cell($this->tablewidths[9],3,'','B',0,'R');
        $this->Cell($this->tablewidths[10],3,'','B',0,'R');
        $this->Cell($this->tablewidths[11],3,'','B',0,'R');
        $this->Cell($this->tablewidths[12],3,number_format($total_importe_gen,2),'B',0,'R');
    }
}
?>