<?php

// fRnk: nuevo reporte HR01014
class RBalanceTipoCCostosDI extends ReportePDF
{
    var $datos_titulo;
    var $datos_detalle;
    var $desde;
    var $hasta;
    var $nivel;
    var $ancho_hoja;
    var $gerencia;
    var $numeracion;
    var $ancho_sin_totales;
    var $cantidad_columnas_estaticas;
    var $codigos;
    var $total_ordenes;
    var $tipo_balance;
    var $incluir_cierre;

    function datosHeader($detalle, $nivel, $desde, $hasta, $codigos, $tipo_balance, $incluir_cierre)
    {
        $this->ancho_hoja = $this->getPageWidth() - PDF_MARGIN_LEFT - PDF_MARGIN_RIGHT - 10;
        $this->datos_detalle = $detalle;
        $this->nivel = $nivel;
        $this->desde = $desde;
        $this->hasta = $hasta;
        $this->codigos = $codigos;
        $this->incluir_cierre = $incluir_cierre;
        $this->tipo_balance = $tipo_balance;
        $this->SetMargins(10, 50);
    }

    function Header()
    {
        $this->Image(dirname(__FILE__) . '/../../lib' . $_SESSION['_DIR_LOGO'], 10, 5, 30, 12);
        $this->SetMargins(10, 30);
        $html = '<table style="font-size: 10px"><tr><td width="17%"></td><td width="66%">';
        $html .= '<table style="text-align: center;font-size: 13px; font-weight: bold">';
        $html .= '<tr><td style="text-decoration: underline">ESTADO AUXILIAR DE CENTROS DE COSTOS</td></tr>';
        $html .= '<tr><td style="font-size: 12px">Del ' . $this->desde . ' al ' . $this->hasta . '</td></tr>';
        $html .= '<tr><td style="font-size: 10px">(Expresado en Bolivianos)</td></tr>';
        $html .= '</table>';
        $html .= '</td><td width="17%"><b>Incluye Cierres</b>: ' . $this->incluir_cierre . '<br><b>Importes:</b> ' . $this->objParam->getParametro('importe') . '</td></tr></table>';
        $this->writeHTML($html, false, false, false, false, '');
        $this->Ln(7);
        $this->SetFont('', 'B', 10);
        $this->Cell(160, 3.5, 'Centros', '', 0, 'C');
        $this->Cell(40, 3.5, 'Importes', '', 0, 'C');
        $this->ln();
    }

    function generarReporte()
    {
        $this->total_ordenes = 0;
        $this->setFontSubsetting(false);
        $this->AddPage();
        $html = '<table style="font-size: 12px">';
        foreach ($this->datos_detalle as $val) {
            if ($this->objParam->getParametro('moneda') == 'base') {
                $var_monto = $val['monto'];
            } else {
                $var_monto = $val['monto_mt'];
            }
            $this->definirTotales($val, $var_monto);
            $html .= '<tr>';
            $html .= '<td width="70%">'. substr('('.$val['codigo'].') '.$val['descripcion'], 0, 81) . '</td>';
            $html .= '<td width="15%"></td>';
            $style = '';
            if ($val['monto'] * 1 < 0) {
                $style .= 'color:red;';
            }
            if ($val['nivel'] == 1) {
                $style .= 'font-size:13px;text-decoration:underline;font-weight:bold;';
            } elseif ($val['nivel'] == 2) {
                $style .= 'font-size:12px;text-decoration:underline;font-weight:bold;';
            }
            $html .= '<td width="15%" style="text-align: right;' . $style . '">' . number_format($var_monto, 2, '.', ',') . '</td>';
            $html .= '</tr>';
            $costo_directo = empty($val['costo_directo']) ? 0 : $val['costo_directo'];
            $costo_indirecto = empty($val['costo_indirecto']) ? 0 : $val['costo_indirecto'];
            if($val['movimiento']=='si'){
                $html .= '<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;Costos Directos</td><td style="text-align: right">' . number_format($costo_directo, 2, '.', ',') . '</td><td></td></tr>';
                $html .= '<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;Costos Indirectos</td><td style="text-align: right">' . number_format($costo_indirecto, 2, '.', ',') . '</td><td></td></tr>';
            }
        }
        $html .= '</table>';
        $this->writeHTML($html, false, false, false, false, '');
        $this->SetFont('times', 'BI', 17);
        $total_ordenes = number_format($this->total_ordenes, 2, '.', ',');
        $formula = "TOTAL";
        $this->Write(0, $formula, '', 0, 'C', true, 0, false, false, 0);
        $formula = "$total_ordenes";
        $this->Write(0, $formula, '', 0, 'C', true, 0, false, false, 0);
    }

    function definirTotales($val, $var_monto)
    {
        if ($val ["nivel"] == 1) {
            $this->total_ordenes = $this->total_ordenes + $var_monto;
        }
    }
}

?>

