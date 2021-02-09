<?php
// Extend the TCPDF class to create custom MultiRow
class RLibroComprasNCDPDF extends  ReportePDF {
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

        $this->Image(dirname(__FILE__) . '/../../lib/imagenes/logos/logo.jpg', 16, 5, 30, 10);
        $this->ln(2);
        $this->SetFont('', 'B', 12);
        $this->Cell(0, 5, "LIBRO DE COMPRAS", 0, 1, 'C');
        $this->Cell(0, 5, "NOTAS DE CREDITO-DEBITO", 0, 1, 'C');
        $this->SetFont('', 'B', 9);
        $this->Cell(0, 3, "(EXPRESADO EN BOLIVIANOS)", 0, 1, 'C');


        $tbl_head = '<table border="0" style="font-size: 9pt;">
                        <tr><td width="5%" style="text-align: center"></td><td width="40%" style="text-align: left">Periodo: '.$this->datos[0]['periodo']." ".$this->datos[0]['gestion'].'</td> <td width="40%" style="text-align: center"></td></tr>
                        <tr><td width="5%" style="text-align: center"></td><td width="40%" style="text-align: left">Nombre o Razón Social: '.$this->datos[0]['razon_empresa'].'</td><td width="40%" style="text-align: left"> NIT: ' .$this->datos[0]['nit_empresa'] . '</td></tr>
                        </table>
                        ';
        $this->writeHTML($tbl_head);


        $this->tablewidths = array(10, 16, 16, 22, 16, 46, 22, 19, 26, 19, 19, 22, 22);
        $this->tablealigns = array('C', 'C', 'C', 'C', 'C', 'L', 'R', 'R', 'L', 'C', 'C', 'C', 'R');
        $this->tablenumbers = array(0, 0, 0, 0, 0, 0, 2, 2, 0, 0, 0, 0, 2);


        $this->SetFont('', 'B', 6);

        $this->Cell(10, 3.5, 'Nro.', 'LTR', 0, 'C');
        $this->Cell(16, 3.5, 'FECHA NOTA', 'TR', 0, 'C');
        $this->Cell(16, 3.5, 'Nro. DE NOTA', 'TR', 0, 'C');
        $this->Cell(22, 3.5, 'Nro. AUTORIZACIÓN', 'TR', 0, 'C');
        //$this->Cell(10, 3.5, '', 'TR', 0, 'C');//ESTADO

        $this->Cell(16, 3.5, 'NIT/CI', 'TR', 0, 'C');
        $this->Cell(46, 3.5, 'NOMBRE O RAZON SOCIAL CLIENTE', 'TR', 0, 'C');
        $this->Cell(22, 3.5, 'IMPORTE TOTAL', 'TR', 0, 'C');
        $this->Cell(19, 3.5, 'DEBITO FISCAL', 'TR', 0, 'C');//CREDITO FISCAL
        $this->Cell(26, 3.5, 'CODIGO DE CONTROL', 'TR', 0, 'C');

        $this->Cell(19, 3.5, 'FECHA FACTURA', 'TR', 0, 'C');
        $this->Cell(19, 3.5, 'Nro. FACTURA', 'TR', 0, 'C');
        $this->Cell(22, 3.5, 'Nro. AUTORIZACIÓN', 'TR', 0, 'C');
        $this->Cell(22, 3.5, 'IMPORTE TOTAL', 'TR', 0, 'C');

        $this->ln();

        $this->Cell(10, 3.5, '', 'LBR', 0, 'C');
        $this->Cell(16, 3.5, '', 'BR', 0, 'C');
        $this->Cell(16, 3.5, '', 'BR', 0, 'C');
        $this->Cell(22, 3.5, '', 'BR', 0, 'C');
        //$this->Cell(10, 3.5, '', 'BR', 0, 'C');

        $this->Cell(16, 3.5, 'PROVEEDOR', 'BR', 0, 'C');//CLIENTE
        $this->Cell(46, 3.5, '', 'BR', 0, 'C');
        $this->Cell(22, 3.5, 'DE DEVOLUCIÓN', 'BR', 0, 'C');
        $this->Cell(19, 3.5, '', 'BR', 0, 'C');
        $this->Cell(26, 3.5, '', 'BR', 0, 'C');

        $this->Cell(19, 3.5, 'ORIGINAL', 'BR', 0, 'C');
        $this->Cell(19, 3.5, 'ORIGINAL', 'BR', 0, 'C');
        $this->Cell(22, 3.5, 'FACTURA ORIGINAL', 'BR', 0, 'C');
        $this->Cell(22, 3.5, 'FACTURA ORIGINAL', 'BR', 0, 'C');

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

        //$this->ln();
        $contador_reg = 0;
        foreach ($this->datos as $key => $value) {

            $contador_reg++;

            $this->SetFont('','',6);
            if (strlen($value['razon_social']) < 35){
                $this->UniRow(array(
                    $this->numeracion, date("d/m/Y", strtotime( $value['fecha_nota'])) , $value['num_nota'], $value['num_autorizacion'], /*$value['estado'],*/ $value['nit'], $value['razon_social'],
                    $value['total_devuelto'], $value['rc_iva'], $value['codigo_control'], date("d/m/Y", strtotime( $value['fecha_original'])), $value['num_factura'], $value['nroaut_anterior'],
                    $value['importe_total']
                ),false,0);
            }else{
                $this->MultiRow(array(
                    $this->numeracion, date("d/m/Y", strtotime( $value['fecha_nota'])), $value['num_nota'], $value['num_autorizacion'], /*$value['estado'],*/ $value['nit'], $value['razon_social'],
                    $value['total_devuelto'], $value['rc_iva'], $value['codigo_control'], date("d/m/Y", strtotime( $value['fecha_original'])), $value['num_factura'], $value['nroaut_anterior'],
                    $value['importe_total']
                ),false,0);
            }

            if ( $contador_reg < 37 ) {
                //suma totales
                $total_devolucion_par += $value['total_devuelto'];
                $total_rciva_par += $value['rc_iva'];
                $total_importe_par += $value['importe_total'];
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