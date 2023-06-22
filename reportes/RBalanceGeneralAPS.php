<?php

//fRnk: reporte PDF Balance General - Estado de Recursos y Gastos APS
class RBalanceGeneralAPS extends ReportePDF
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
    var $total_activo;
    var $total_pasigo;
    var $total_patrimonio;
    private $total_ingreso = 0;
    private $total_egreso = 0;
    var $tipo_balance;
    var $incluir_cierre;
    var $formato_reporte;
    private $titulo_inst;

    function datosHeader($detalle, $nivel, $desde, $hasta, $codigos, $tipo_balance, $incluir_cierre, $formato_reporte)
    {
        $this->titulo_inst = mb_strtoupper($_SESSION['_TITULO_SIS_LARGO'], 'UTF-8');
        $this->ancho_hoja = $this->getPageWidth() - PDF_MARGIN_LEFT - PDF_MARGIN_RIGHT - 10;
        $this->datos_detalle = $detalle;//var_dump($detalle);exit();
        $this->nivel = $nivel;
        $this->desde = $desde;
        $this->hasta = $hasta;
        $this->codigos = $codigos;
        $this->incluir_cierre = $incluir_cierre;
        $this->tipo_balance = $tipo_balance;
        $this->formato_reporte = $formato_reporte;
        $this->SetMargins(5, 10);
    }

    function Header()
    {
        if ($this->page == 1) {
            $this->Image(dirname(__FILE__) . '/../../lib' . $_SESSION['_DIR_LOGO'], $this->ancho_hoja, 5, 30, 10);
            $this->SetMargins(5, 25);
            $titulo = $this->tipo_balance == 'resultado' ? 'ESTADO DE RECURSOS Y GASTOS' : 'BALANCE GENERAL';
            $html = '<table style="text-align: center;font-size: 10px; font-weight: bold">';
            $html .= '<tr><td>' . $this->titulo_inst . '</td></tr>';
            $html .= '<tr><td>CONSOLIDADO</td></tr>';
            $html .= '<tr><td>' . $titulo . '</td></tr>';
            $html .= '<tr><td>AL ' . $this->getFecha(implode("-", array_reverse(explode("/", $this->hasta)))) . '</td></tr>';
            $html .= '<tr><td style="font-size: 8px">(EXPRESADO EN BOLIVIANOS)</td></tr>';
            $html .= '</table>';
            $this->writeHTML($html, false, false, false, false, '');
        }
    }

    function getFecha($fecha)
    {
        $dia = date("d", strtotime($fecha));
        $anio = date("Y", strtotime($fecha));
        $mes = array('ENERO', 'FEBRERO', 'MARZO', 'ABRIL', 'MAYO', 'JUNIO', 'JULIO', 'AGOSTO', 'SEPTIEMBRE', 'OCTUBRE', 'NOVIEMBRE', 'DICIEMBRE');
        $mes = $mes[(date('m', strtotime($fecha)) * 1) - 1];
        return $dia . ' DE ' . $mes . ' DE ' . $anio;
    }

    function generarReporte()
    {
        $this->SetFontSize(8);
        $this->AddPage();
        $this->SetMargins(5, 10);
        $html = '<table>';
        $subtotal = 0;
        $total_pasivo_activo = 0;
        $monto_sub = '';
        $cuenta_ant = $this->tipo_balance == 'resultado' ? '4' : '1';
        $total_n1 = '';
        foreach ($this->datos_detalle as $val) {
            if ($val['nivel'] == 2) {
                $monto_sub = empty($val['monto']) ? 0 : $val['monto'];
                $subtotal += $monto_sub;
                $monto_sub = number_format($monto_sub, 2, ',', '.');
            }
            $cuenta_act = explode('.', $val['nro_cuenta']);
            if (count($cuenta_act) > 0) {
                $this->definirTotales($val, $cuenta_act[0]);
                if ($cuenta_act[0] != $cuenta_ant) {
                    $html .= '<tr><td></td><td style="text-align: center;font-weight: bold">TOTAL ' . $total_n1 . '</td>
                            <td style="border-top: 1px solid #000; border-bottom: 1px solid #000"></td>
                            <td style="text-align: right;font-weight: bold;border-top: 1px solid #000; border-bottom:  1px solid #000">' . number_format($subtotal, 2, ',', '.') . '</td></tr>';
                    if ($cuenta_ant == '2' || $cuenta_ant == '3') {
                        $total_pasivo_activo += $subtotal;
                    }
                    $subtotal = 0;

                    if ($cuenta_act[0] == '6' && $val['nivel'] == 1) {
                        $html .= '<tr><td></td><td style="text-align: center;font-weight: bold">TOTAL PASIVO Y PATRIMONIO</td>
                            <td style="border-bottom: 1px solid #000"></td>
                            <td style="text-align: right;font-weight: bold;border-bottom:  1px solid #000">' . number_format($total_pasivo_activo, 2, ',', '.') . '</td></tr>';
                        $html .= '<tr><td colspan="4"><br/><br/><br/><br/></td></tr>';
                        $html .= '<tr><td colspan="4" style="text-align: center;font-size: 10px; font-weight: bold">' . $this->titulo_inst . '</td></tr>';
                        $html .= '<tr><td colspan="4" style="text-align: center;font-size: 10px; font-weight: bold">CONSOLIDADO</td></tr>';
                        $html .= '<tr><td colspan="4" style="text-align: center;font-size: 10px; font-weight: bold">CUENTAS DE ORDEN</td></tr>';
                        $html .= '<tr><td colspan="4" style="text-align: center;font-size: 10px; font-weight: bold">AL ' . $this->getFecha(implode("-", array_reverse(explode("/", $this->hasta)))) . '</td></tr>';
                        $html .= '<tr><td colspan="4" style="text-align: center;font-size: 8px; font-weight: bold">(EXPRESADO EN BOLIVIANOS)</td></tr>';
                    }
                }
                $cuenta_ant = $cuenta_act[0];
            }
            $bold = $val['nivel'] <= 6 ? 'font-weight: bold;' : '';
            $monto = empty($val['monto']) ? 0 : $val['monto'];
            $monto_p = $val['nivel'] != 2 ? number_format($monto, 2, ',', '.') : '';
            if ($val['nivel'] == 1) {
                $monto_p = '';
                $total_n1 = $val['nombre_cuenta'];
            }

            $html .= '<tr><td style="width: 12%;' . $bold . '">' . $val['nro_cuenta'] . '</td>';
            $html .= '<td style="width: 64%;' . $bold . '">' . $val['nombre_cuenta'] . '</td>';
            $html .= '<td style="width: 12%;text-align: right;' . $bold . '">' . $monto_p . '</td>';
            $html .= '<td style="width: 12%;text-align: right;' . $bold . '">' . $monto_sub . '</td></tr>';
            $monto_sub = '';
        }
        $html .= '<tr><td></td><td style="text-align: center;font-weight: bold">TOTAL ' . $total_n1 . '</td>
                            <td style="border-top: 1px solid #000; border-bottom: 1px solid #000"></td>
                            <td style="text-align: right;font-weight: bold;border-top: 1px solid #000; border-bottom:  1px solid #000">' . number_format($subtotal, 2, ',', '.') . '</td></tr>';
        if ($this->tipo_balance == 'resultado') {
            $resultado = $this->total_ingreso - $this->total_egreso;
            $html .= '<tr><td></td><td style="text-align: center;font-weight: bold">RESULTADO DEL EJERCICIO</td>
                            <td style="border-bottom: 1px solid #000"></td>
                            <td style="text-align: right;font-weight: bold;border-bottom:  1px solid #000">' . number_format($resultado, 2, ',', '.') . '</td></tr>';
        }
        if ($this->tipo_balance != 'resultado' && $this->datos_detalle[0]['cuenta_orden']=='no'){ //fRnk: añadido para mostrar total cuando 6 y 7 no tiene movimiento
            $total_pasivo_activo += $subtotal;
            $html .= '<tr><td></td><td style="text-align: center;font-weight: bold">TOTAL PASIVO Y PATRIMONIO</td>
                            <td style="border-bottom: 1px solid #000"></td>
                            <td style="text-align: right;font-weight: bold;border-bottom:  1px solid #000">' . number_format($total_pasivo_activo, 2, ',', '.') . '</td></tr>';
        }
        $html .= '</table>';
        $this->writeHTML($html, false, false, false, false, '');
    }

    function definirTotales($val, $rcuenta)
    {
        if ($val["nivel"] == 1 && $rcuenta == '4') {
            $this->total_ingreso += $val['monto'];
        }
        if ($val["nivel"] == 1 && $rcuenta == '5') {
            $this->total_egreso += $val['monto'];
        }
    }
}

?>

