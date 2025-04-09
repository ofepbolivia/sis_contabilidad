<?php

//fRnk: nuevo reporte HR01014
class RBalanceOrdenesCostosDI extends ReportePDF
{
    private $datos_titulo;
    private $datos_detalle;
    private $desde;
    private $hasta;
    private $nivel;
    private $ancho_hoja;
    private $codigos;
    private $total_ordenes;
    private $transaccional;
    private $incluir_cierre;

    function datosHeader($detalle, $nivel, $desde, $hasta, $codigos, $transaccional, $incluir_cierre)
    {
        $this->ancho_hoja = $this->getPageWidth() - PDF_MARGIN_LEFT - PDF_MARGIN_RIGHT - 10;
        $this->datos_detalle = $detalle;
        $this->nivel = $nivel;
        $this->desde = $desde;
        $this->hasta = $hasta;
        $this->codigos = $codigos;
        $this->incluir_cierre = $incluir_cierre;
        $this->transaccional = $transaccional;
        //$this->SetMargins(5, 22.5, 5);
        $this->SetMargins(5, 40);
    }

    function Header()
    {
        $content = '<table border="0" cellpadding="1" style="font-size: 10px;">
            <tr>
                <td style="width: 23%; color: #222;" rowspan="2">
                    &nbsp;<img  style="width: 120px;" src="./../../../lib/' . $_SESSION['_DIR_LOGO'] . '" alt="Logo">
                </td>		
                <td style="width: 54%; color: #222;text-align: center" rowspan="2">
                   <h4 style="font-size: 12px">ESTADO AUXILIAR DE COSTOS POR ÓRDENES</h4>
                   <b style="font-size: 10px">Del ' . $this->desde . ' al ' . $this->hasta . '</b><br/>
                   (Expresado en Bolivianos)
                </td>
                <td style="width: 23%; color: #444444; text-align: left;"><br><br>&nbsp;&nbsp;<b>Depto:</b> (' . $this->codigos . ')</td>
            </tr>
            <tr>
                <td style="width: 23%; color: #444444; text-align: left;">&nbsp;&nbsp;<b>Incluye Cierres:</b> ' . $this->incluir_cierre . '</td>
            </tr>
        </table>';
        $this->writeHTMLCell(0, 10, 5, 4, $content, 0, 0, 0, true, 'L', true);
        $this->Ln(27);
        $this->SetFont('', 'B', 10);
        if ($this->nivel == 1 || $this->nivel > 3) {
            $this->Cell(160, 3.5, 'Órdenes', '', 0, 'C');
            $this->Cell(40, 3.5, 'Importes', '', 0, 'C');
            $this->ln();
        }
    }

    function generarReporte()
    {
        $this->total_ordenes = 0;
        $this->setFontSubsetting(false);
        $this->AddPage();
        $html = '<table style="font-size: 12px">';
        foreach ($this->datos_detalle as $val) {
            $this->definirTotales($val);
            $style = '';
            $bold = '';
            if ($val['monto'] * 1 < 0) {
                $style .= 'color:red;';
            }
            if ($val['nivel'] == 1) {
                $style .= 'font-size:13px;text-decoration:underline;font-weight:bold;';
                if ($this->transaccional == 'no')
                    $bold = 'style="font-weight:bold;"';
            } elseif ($val['nivel'] == 2) {
                $style .= 'font-size:12px;text-decoration:underline;font-weight:bold;';
            }
            $html .= '<tr>';
            $html .= '<td width="70%" ' . $bold . '>(' . $val['codigo'] . ') ' . $val['desc_orden'] . '</td>';
            $html .= '<td width="15%"></td>';
            $html .= '<td width="15%" style="text-align: right;' . $style . '">' . number_format($val['monto'], 2, '.', ',') . '</td>';
            $html .= '</tr>';
            $costo_directo = empty($val['costo_directo']) ? 0 : $val['costo_directo'];
            $costo_indirecto = empty($val['costo_indirecto']) ? 0 : $val['costo_indirecto'];
            if ($this->transaccional == 'no' && $val['nivel'] == 1) {
                $html .= '';
            } else {
                /*if ($costo_directo + $costo_indirecto != $val['monto']) {
                    if ($costo_directo > $val['monto']) {
                        $costo_directo = $val['monto'];
                    }
                    $costo_indirecto = $val['monto'] - $costo_directo;
                }*/

                $html .= '<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;Costos Directos</td><td style="text-align: right">' . number_format($costo_directo, 2, '.', ',') . '</td><td></td></tr>';
                $html .= '<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;Costos Indirectos</td><td style="text-align: right">' . number_format($costo_indirecto, 2, '.', ',') . '</td><td></td></tr>';
            }
        }
        $html .= '</table>';
        $this->writeHTML($html, false, false, false, false, '');
        $this->ln();
        $this->SetFont('times', 'BI', 17);
        $total_ordenes = number_format($this->total_ordenes, 2, '.', ',');
        $formula = "TOTAL ÓRDENES";
        $this->Write(0, $formula, '', 0, 'C', true, 0, false, false, 0);
        $formula = "$total_ordenes";
        $this->Write(0, $formula, '', 0, 'C', true, 0, false, false, 0);
    }

    function definirTotales($val)
    {
        if ($val ["nivel"] == 1) {
            $this->total_ordenes = $this->total_ordenes + $val['monto'];
        }
    }
}

?>