<?php

// Extend the TCPDF class to create custom MultiRow
class RIntCbte extends ReportePDF
{
    var $cabecera;
    var $detalleCbte;
    var $ancho_hoja;
    var $gerencia;
    var $numeracion;
    var $ancho_sin_totales;
    var $cantidad_columnas_estaticas;
    var $total;
    var $with_col;
    var $tot_debe;
    var $tot_haber;
    var $tot_debe_mb;
    var $tot_haber_mb;

    function datosHeader($detalle)
    {


        $this->cabecera = $detalle->getParameter('cabecera');
        $this->detalleCbte = $detalle->getParameter('detalleCbte');
        $this->datosBeneficiarios = $detalle->getParameter('listadoBeneficiarios');
        $this->ancho_hoja = $this->getPageWidth() - PDF_MARGIN_LEFT - PDF_MARGIN_RIGHT - 10;
        $this->datos_detalle = $detalle;
        $this->SetMargins(15, 30, 5);


    }

    function Header()
    {

        if ($this->page == 1) {
            // $this->SetMargins(15, 40, 5);
            $newDate = date("d/m/Y", strtotime($this->cabecera[0]['fecha']));
            $dataSource = $this->datos_detalle;
            ob_start();
            include(dirname(__FILE__) . '/../reportes/tpl/cabecera.php');
            $content = ob_get_clean();
            $this->writeHTML($content, true, false, true, false, '');
        } else {
            $this->SetMargins(15, 48, 5);
            ob_start();
            include(dirname(__FILE__) . '/../reportes/tpl/cabecera.php');
            $content = ob_get_clean();
            $this->writeHTML($content, true, false, true, false, '');
            ob_start();
            include(dirname(__FILE__) . '/../reportes/tpl/cabeceraDetalle.php');
            $content = ob_get_clean();
            $this->writeHTML($content, false, false, true, false, '');
        }
    }

    function generarReporte()
    {

        $this->AddPage();

        $dataSource = $this->datos_detalle;
        $tot_debe = 0;
        $tot_haber = 0;
        if ($this->cabecera[0]['id_moneda'] == $this->cabecera[0]['id_moneda_base']) {
            $this->with_col = '55%';
        } else {
            $this->with_col = '45%';
        }

        $with_col = $this->with_col;


        //adiciona glosa
        ob_start();
        include(dirname(__FILE__) . '/../reportes/tpl/glosa.php');
        $content = ob_get_clean();
        $this->writeHTML($content, false, false, true, false, '');

        //linea en blanco
        ob_start();
        $content = '<table width="100%"><tr><td style="font-size: 4px;">&nbsp;</td></tr></table>';
        $this->writeHTML($content, false, false, true, false, '');

        //cabecera de los beneficiarios
        $importe = 0;
        ob_start();
        $content3 = '   <table rules="cols" border="1">
                            <tbody>
                                <tr>
                                    <td colspan="7" style="font-size: 13px;font-weight: bold;">Beneficiarios</td>
                                </tr>
                                <tr style="font-size: 10px;font-weight: bold;text-align: center;">
                                    <th width="7%">Tipo Doc.</th>
                                    <th width="15%">Nro. Documento</th>
                                    <th width="8%">Expedido</th>
                                    <th width="30%">Nombre o Razón Social</th>
                                    <th width="13%">Banco</th>
                                    <th width="15%">Cuenta</th>
                                    <th width="12%">Importe</th>
                                </tr>';
        foreach ($this->datosBeneficiarios as $key => $val) {
            $importe = $importe + $val['importe'];

            $content3 = $content3 . '<tr style="font-size: 10px">
                                    <td width="7%" style="text-align: center;">' . $val['tipo_doc'] . '</td>
                                    <td width="15%" style="text-align: center;">' . $val['ci'] . '</td>
                                    <td width="8%" style="text-align: center;">' . $val['expedicion'] . '</td>
                                    <td width="30%" style="text-align: left;">' . $val['nombre_razon_social'] . '</td>
                                    <td width="13%" style="text-align: center;">' . $val['banco'] . '</td>
                                    <td width="15%" style="text-align: center;">' . $val['nro_cuenta_bancaria_sigma'] . '</td>
                                    <td width="12%" style="text-align: rigth;">' . number_format($val['importe'], 2) . '</td>
                                </tr>';
        }
        $content3 = $content3 . '<tr style="font-size: 10px;font-weight: bold;">
                                    <td style="text-align: right;" width="88%" colspan="6">TOTALES</td>
                                    <td style="text-align: right;" width="12%">' . number_format($importe, 2) . '</td>
                                </tr>
                            </tbody>
                        </table>';
        $this->writeHTML($content3, false, false, true, false, '');

        //linea en blanco
        ob_start();
        $content = '<table width="100%"><tr><td style="font-size: 4px;">&nbsp;</td></tr></table>';
        $this->writeHTML($content, false, false, true, false, '');

        //cabecera del detalle del reporte
        ob_start();
        include(dirname(__FILE__) . '/../reportes/tpl/cabeceraDetalle.php');
        $content2 = ob_get_clean();
        // $this->writeHTML($content.$content2, false, false, true, false, '');
        $this->writeHTML($content2, false, false, true, false, '');

        $this->SetFont('helvetica', '', 5, '', 'default', true);

        //fRnk: modificado porque no funcionaba la impresión de reportes - SOP01
        $htmlc = '';
        foreach ($this->detalleCbte as $key => $val) {
            $sw = 1;
            if ($this->cabecera[0]['id_moneda'] == $this->cabecera[0]['id_moneda_base'] && $val['importe_debe'] == 0 && $val['importe_haber'] == 0) {
                $sw = 0;
            }

            if ($sw == 1) {
                ob_start();
                include(dirname(__FILE__) . '/../reportes/tpl/transaccion.php');
                $content = ob_get_clean();
                $htmlc .= $content;
                $this->tot_debe += $val['importe_debe'];
                $this->tot_haber += $val['importe_haber'];
                $this->tot_debe_mb += $val['importe_debe_mb'];
                $this->tot_haber_mb += $val['importe_haber_mb'];
            }
        }
        $this->writeHTML($htmlc, false, false, true, false, '');

        //$this->Ln();
        //$this->revisarfinPagina($content); //fRnk: se quitó esta opción porque generaba error de impresión en algunos reportes
        $this->subtotales('TOTALES');

        $this->Ln(2);
        $this->Firmas();

        $this->Cell(196, 3.5, 'Reg: ' . $this->cabecera[0]['usr_reg'], '', 0, 'R');
        //fRnk: se quitó el ID, para evitar confusión
        //$this->Cell(10, 3.5, 'ID: ' . $this->cabecera[0]['id_int_comprobante'], '', 0, 'R');

    }

    function Firmas()
    {


        $newDate = date("d/m/Y", strtotime($this->cabecera[0]['fecha']));
        $dataSource = $this->datos_detalle;
        ob_start();
        include(dirname(__FILE__) . '/../reportes/tpl/firmas.php');
        $content = ob_get_clean();
        $this->writeHTML($content, true, false, true, false, '');


    }

    function subtotales($titulo)
    {
        ob_start();
        include(dirname(__FILE__) . '/../reportes/tpl/totales.php');
        $content = ob_get_clean();
        $this->writeHTML($content, false, false, true, false, '');

    }


    function revisarfinPagina($content)
    {
        $dimensions = $this->getPageDimensions();
        $hasBorder = false; //flag for fringe case

        $startY = $this->GetY();
        $test = $this->getNumLines($content, 80);

        //if (($startY + 10 * 6) + $dimensions['bm'] > ($dimensions['hk'])) {

        //if ($startY +  $test > 250) {
        $auxiliar = 250;
        //if($this->page==1){
        //	$auxiliar = 250;
        //}
        if ($startY + $test > $auxiliar) {
            //$this->Ln();
            //$this->subtotales('Pasa a la siguiente página. '.$startY);
            $this->subtotales('Pasa a la siguiente página');
            $startY = $this->GetY();
            if ($startY < 70) {
                //$this->AddPage();
            } else {
                $this->AddPage();
            }


            //$this->writeHTML('<p>text'.$startY.'</p>', false, false, true, false, '');
        }
    }
}

?>