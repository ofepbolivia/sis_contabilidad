<?php
//fRnk: nuevo reporte HR00528-2024

class RBancarizacionComprasVentas extends ReportePDF
{
    private $data;
    private $tipo;
    private $ancho_hoja;

    function datosHeader($maestro, $tipo)
    {
        $this->ancho_hoja = $this->getPageWidth() - PDF_MARGIN_LEFT - PDF_MARGIN_RIGHT - 5;
        $this->SetMargins(5, 25, 5);
        $this->data = $maestro;
        $this->tipo = $tipo;
    }

    function Header()
    {
        $content = '<table border="0.5" cellpadding="1" style="font-size: 11px">
            <tr>
                <td style="width: 23%; color: #444444;" rowspan="2">
                    &nbsp;<img  style="width: 120px;" src="./../../../lib/' . $_SESSION['_DIR_LOGO'] . '" alt="Logo">
                </td>		
                <td style="width: 52%; color: #444444;text-align: center" rowspan="2">
                   <h1 style="font-size: 16px">Bancarización ' . $this->tipo . '</h1>
                </td>
                <td style="width: 25%; color: #444444; text-align: left;height: 30px">&nbsp;&nbsp;<b>Revisión:</b> 1</td>
            </tr>
            <tr>
                <td style="width: 25%; color: #444444; text-align: left;">&nbsp;&nbsp;<b>Página:</b> ' . $this->getAliasNumPage() . ' de ' . $this->getAliasNbPages() . '</td>
            </tr>
        </table>';
        $this->writeHTML($content, false, false, true, false, '');
    }

    function generarReporte()
    {
        $this->setFontSubsetting(false);
        $this->AddPage();
        $this->SetFontSize(6);
        $html = '<table border="0.5" cellpadding="2" cellspacing="0">';
        if ($this->tipo == 'Compras') {
            $html .= '<tr style="background-color: #cccccc;font-size: 7px;text-align: center">
                    <td width="6%"><b>Modalidad transacción</b></td>
                    <td width="6%"><b>Fecha factura/fecha documento</b></td>
                    <td width="6%"><b>Tipo de transacción</b></td>
                    <td width="6%"><b>NIT/CI proveedor</b></td>
                    <td width="10%"><b>Nombre/razón social proveedor</b></td>
                    <td width="6%"><b>N° de factura/ N° documento</b></td>
                    <td width="4%"><b>N° de contrato</b></td>
                    <td width="6%"><b>Importe factura/importe documento</b></td>
                    <td width="6%"><b>N° autorización factura/documento</b></td>
                    <td width="6%"><b>N° de cuenta del documento de pago</b></td>
                    <td width="6%"><b>Monto pagado en documento de pago</b></td>
                    <td width="6%"><b>Monto Acumulado</b></td>
                    <td width="6%"><b>NIT Entidad Financiera</b></td>
                    <td width="6%"><b>N° documento de pago (N° transacción u operación)</b></td>
                    <td width="8%"><b>Tipo de documento de pago</b></td>
                    <td width="6%"><b>Fecha del documento de pago</b></td></tr>';
            foreach ($this->data as $row) {
                $html .= '<tr>';
                $html .= '<td>' . $row['desc_modalidad_transaccion'] . '</td>';
                $html .= '<td style="text-align: center">' . implode('/', array_reverse(explode('-', $row['fecha_documento']))) . '</td>';
                $html .= '<td>' . $row['desc_tipo_transaccion'] . '</td>';
                $html .= '<td>' . $row['nit_ci'] . '</td>';
                $html .= '<td>' . $row['razon'] . '</td>';
                $html .= '<td style="text-align: center">' . $row['num_documento'] . '</td>';
                $html .= '<td style="text-align: center">' . $row['num_contrato'] . '</td>';
                $html .= '<td style="text-align: right">' . $row['importe_documento'] . '</td>';
                $html .= '<td>' . $row['autorizacion'] . '</td>';
                $html .= '<td>' . $row['num_cuenta_pago'] . '</td>';
                $html .= '<td style="text-align: right">' . $row['monto_pagado'] . '</td>';
                $html .= '<td style="text-align: right">' . $row['monto_acumulado'] . '</td>';
                $html .= '<td>' . $row['nit_entidad'] . '</td>';
                $html .= '<td style="text-align: center">' . $row['num_documento_pago'] . '</td>';
                $html .= '<td>' . $row['desc_tipo_documento_pago'] . '</td>';
                $html .= '<td style="text-align: center">' . implode('/', array_reverse(explode('-', $row['fecha_de_pago']))) . '</td>';
                $html .= '</tr>';
            }
        } else {
            $html .= '<tr style="background-color: #cccccc;font-size: 7px;text-align: center">
                    <td width="7%"><b>Modalidad de transacción</b></td>
                    <td width="6%"><b>Fecha factura/documento</b></td>
                    <td width="6%"><b>N° de factura/documento</b></td>
                    <td width="6%"><b>Importe factura/documento</b></td>
                    <td width="6%"><b>N° de contrato</b></td>
                    <td width="6%"><b>N° autorización factura</b></td>
                    <td width="6%"><b>NIT/CI cliente</b></td>
                    <td width="11%"><b>Nombre o razón social cliente</b></td>
                    <td width="6%"><b>N° de cuenta del documento de pago</b></td>
                    <td width="6%"><b>Monto pagado en documento de pago</b></td>
                    <td width="6%"><b>Monto acumulado de pagos parciales</b></td>
                    <td width="6%"><b>NIT Entidad Financiera</b></td>
                    <td width="6%"><b>N° de transacción u operación de pago</b></td>
                    <td width="10%"><b>Tipo de documento de pago</b></td>
                    <td width="6%"><b>Fecha del documento de pago</b></td></tr>';
            foreach ($this->data as $row) {
                $html .= '<tr>';
                $html .= '<td>' . $row['desc_modalidad_transaccion'] . '</td>';
                $html .= '<td style="text-align: center">' . implode('/', array_reverse(explode('-', $row['fecha_documento']))) . '</td>';
                $html .= '<td style="text-align: center">' . $row['num_documento'] . '</td>';
                $html .= '<td style="text-align: right">' . $row['importe_documento'] . '</td>';
                $html .= '<td style="text-align: center">' . $row['num_contrato'] . '</td>';
                $html .= '<td>' . $row['autorizacion'] . '</td>';
                $html .= '<td>' . $row['nit_ci'] . '</td>';
                $html .= '<td>' . $row['razon'] . '</td>';
                $html .= '<td>' . $row['num_cuenta_pago'] . '</td>';
                $html .= '<td style="text-align: right">' . $row['monto_pagado'] . '</td>';
                $html .= '<td style="text-align: right">' . $row['monto_acumulado'] . '</td>';
                $html .= '<td>' . $row['nit_entidad'] . '</td>';
                $html .= '<td style="text-align: center">' . $row['num_documento_pago'] . '</td>';
                $html .= '<td>' . $row['desc_tipo_documento_pago'] . '</td>';
                $html .= '<td style="text-align: center">' . implode('/', array_reverse(explode('-', $row['fecha_de_pago']))) . '</td>';
                $html .= '</tr>';
            }
        }
        $html .= '</table>';
        $this->writeHTML($html, false, false, true, false, '');
        $this->Ln(10);
    }

    function Footer()
    {
        $this->setY(-15);
        $ormargins = $this->getOriginalMargins();
        $this->SetTextColor(0, 0, 0);
        //set style for cell border
        $line_width = 0.85 / $this->getScaleFactor();
        $this->SetLineStyle(array('width' => $line_width, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
        $ancho = round(($this->getPageWidth() - $ormargins['left'] - $ormargins['right']) / 3);
        $this->Ln(2);
        $cur_y = $this->GetY();
        //$this->Cell($ancho, 0, 'Generado por XPHS', 'T', 0, 'L');
        $this->Cell($ancho, 0, 'Usuario: ' . $_SESSION['_LOGIN'], '', 0, 'L');
        $pagenumtxt = 'Página' . ' ' . $this->getAliasNumPage() . ' de ' . $this->getAliasNbPages();
        $this->Cell($ancho, 0, $pagenumtxt, '', 0, 'C');
        $this->Cell($ancho, 0, $_SESSION['_REP_NOMBRE_SISTEMA'], '', 0, 'R');
        $this->Ln();
        $fecha_rep = date("d-m-Y H:i:s");
        $this->Cell($ancho, 0, "Fecha : " . $fecha_rep, '', 0, 'L');
        $this->Ln($line_width);
        $this->Ln();
    }
}

?>