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
        //ob_start();
        //include(dirname(__FILE__) . '/../reportes/tpl/totales.php');
        //$content = ob_get_clean();
        //fRnk: se trajo el código de totales.php, debido a que generaba error al crear el PDF, SOP00124
        $content='<table width="100%" cellpadding="1" rules="cols" border="1" style="font-size: 10px">
                <tr>
                <td width="'.$this->with_col.'" align="right"><b>'.$titulo.'&nbsp;&nbsp;&nbsp;&nbsp;</b></td>';
        if ($this->cabecera[0]['id_moneda'] == $this->cabecera[0]['id_moneda_base']){
            $content.='<td width="15%" align="right"><b>&nbsp;</b></td>
                       <td width="15%" align="right" class="td_currency"><span><b>';
            if ($this->tot_debe>0) { $content.= number_format($this->tot_debe, 2, '.', ',');}
            $content.='</b></span></td>';
            $content.= '<td width="15%" align="right" class="td_currency"><span><b>';
            if ($this->tot_haber>0) { $content.= number_format($this->tot_haber, 2, '.', ',');}
            $content.='</b></span></td>';
        }
        else {
            $content.='<td width="11%" align="right"><b>&nbsp;</b></td>
                       <td width="11%" align="right"><span><b>';
            if ($this->tot_debe > 0) { $content.= number_format($this->tot_debe, 2, '.', ',');}
            $content.='</b></span></td>
                       <td width="11%" align="right"><span><b>';
            if ($this->tot_haber > 0) { $content.= number_format($this->tot_haber, 2, '.', ',');}
            $content.='</b></span></td>
                       <td width="11%" align="right"><span><b>';
            if ($this->tot_debe_mb > 0) { $content.= number_format($this->tot_debe_mb, 2, '.', ',');}
            $content.='</b></span></td>
                       <td width="11%" align="right"><span><b>';
            if ($this->tot_haber_mb > 0) { $content.= number_format($this->tot_haber_mb, 2, '.', ',');}
            $content.='</b></span></td>';
        }
        $content.='</tr>
                   </table>
                   <div style="height: 1px;font-size:0px;">&</div>
                   <div style="border: 1px solid #000000;margin-top: 0;width:100%;height:60px;font-size:10px;">
                        <br/><b>&nbsp;&nbsp;Son: ';
        $content.= convertir($this->tot_haber).$this->cabecera[0]['moneda'].'</b><br/></div>';
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

    function unidad($numuero){
        switch ($numuero)
        {
            case 9:
            {
                $numu = "NUEVE";
                break;
            }
            case 8:
            {
                $numu = "OCHO";
                break;
            }
            case 7:
            {
                $numu = "SIETE";
                break;
            }
            case 6:
            {
                $numu = "SEIS";
                break;
            }
            case 5:
            {
                $numu = "CINCO";
                break;
            }
            case 4:
            {
                $numu = "CUATRO";
                break;
            }
            case 3:
            {
                $numu = "TRES";
                break;
            }
            case 2:
            {
                $numu = "DOS";
                break;
            }
            case 1:
            {
                $numu = "UNO";
                break;
            }
            case 0:
            {
                $numu = "";
                break;
            }
        }
        return $numu;
    }

    function decena($numdero){

        if ($numdero >= 90 && $numdero <= 99)
        {
            $numd = "NOVENTA ";
            if ($numdero > 90)
                $numd = $numd."Y ".(unidad($numdero - 90));
        }
        else if ($numdero >= 80 && $numdero <= 89)
        {
            $numd = "OCHENTA ";
            if ($numdero > 80)
                $numd = $numd."Y ".(unidad($numdero - 80));
        }
        else if ($numdero >= 70 && $numdero <= 79)
        {
            $numd = "SETENTA ";
            if ($numdero > 70)
                $numd = $numd."Y ".(unidad($numdero - 70));
        }
        else if ($numdero >= 60 && $numdero <= 69)
        {
            $numd = "SESENTA ";
            if ($numdero > 60)
                $numd = $numd."Y ".(unidad($numdero - 60));
        }
        else if ($numdero >= 50 && $numdero <= 59)
        {
            $numd = "CINCUENTA ";
            if ($numdero > 50)
                $numd = $numd."Y ".(unidad($numdero - 50));
        }
        else if ($numdero >= 40 && $numdero <= 49)
        {
            $numd = "CUARENTA ";
            if ($numdero > 40)
                $numd = $numd."Y ".(unidad($numdero - 40));
        }
        else if ($numdero >= 30 && $numdero <= 39)
        {
            $numd = "TREINTA ";
            if ($numdero > 30)
                $numd = $numd."Y ".(unidad($numdero - 30));
        }
        else if ($numdero >= 20 && $numdero <= 29)
        {
            if ($numdero == 20)
                $numd = "VEINTE ";
            else
                $numd = "VEINTI".(unidad($numdero - 20));
        }
        else if ($numdero >= 10 && $numdero <= 19)
        {
            switch ($numdero){
                case 10:
                {
                    $numd = "DIEZ ";
                    break;
                }
                case 11:
                {
                    $numd = "ONCE ";
                    break;
                }
                case 12:
                {
                    $numd = "DOCE ";
                    break;
                }
                case 13:
                {
                    $numd = "TRECE ";
                    break;
                }
                case 14:
                {
                    $numd = "CATORCE ";
                    break;
                }
                case 15:
                {
                    $numd = "QUINCE ";
                    break;
                }
                case 16:
                {
                    $numd = "DIECISEIS ";
                    break;
                }
                case 17:
                {
                    $numd = "DIECISIETE ";
                    break;
                }
                case 18:
                {
                    $numd = "DIECIOCHO ";
                    break;
                }
                case 19:
                {
                    $numd = "DIECINUEVE ";
                    break;
                }
            }
        }
        else
            $numd = unidad($numdero);
        return $numd;
    }

    function centena($numc){
        if ($numc >= 100)
        {
            if ($numc >= 900 && $numc <= 999)
            {
                $numce = "NOVECIENTOS ";
                if ($numc > 900)
                    $numce = $numce.(decena($numc - 900));
            }
            else if ($numc >= 800 && $numc <= 899)
            {
                $numce = "OCHOCIENTOS ";
                if ($numc > 800)
                    $numce = $numce.(decena($numc - 800));
            }
            else if ($numc >= 700 && $numc <= 799)
            {
                $numce = "SETECIENTOS ";
                if ($numc > 700)
                    $numce = $numce.(decena($numc - 700));
            }
            else if ($numc >= 600 && $numc <= 699)
            {
                $numce = "SEISCIENTOS ";
                if ($numc > 600)
                    $numce = $numce.(decena($numc - 600));
            }
            else if ($numc >= 500 && $numc <= 599)
            {
                $numce = "QUINIENTOS ";
                if ($numc > 500)
                    $numce = $numce.(decena($numc - 500));
            }
            else if ($numc >= 400 && $numc <= 499)
            {
                $numce = "CUATROCIENTOS ";
                if ($numc > 400)
                    $numce = $numce.(decena($numc - 400));
            }
            else if ($numc >= 300 && $numc <= 399)
            {
                $numce = "TRESCIENTOS ";
                if ($numc > 300)
                    $numce = $numce.(decena($numc - 300));
            }
            else if ($numc >= 200 && $numc <= 299)
            {
                $numce = "DOSCIENTOS ";
                if ($numc > 200)
                    $numce = $numce.(decena($numc - 200));
            }
            else if ($numc >= 100 && $numc <= 199)
            {
                if ($numc == 100)
                    $numce = "CIEN ";
                else
                    $numce = "CIENTO ".(decena($numc - 100));
            }
        }
        else
            $numce = decena($numc);

        return $numce;
    }

    function miles($nummero){
        if ($nummero >= 1000 && $nummero < 2000){
            $numm = "MIL ".(centena($nummero%1000));
        }
        if ($nummero >= 2000 && $nummero <10000){
            $numm = unidad(Floor($nummero/1000))." MIL ".(centena($nummero%1000));
        }
        if ($nummero < 1000)
            $numm = centena($nummero);

        return $numm;
    }

    function decmiles($numdmero){
        if ($numdmero == 10000)
            $numde = "DIEZ MIL";
        if ($numdmero > 10000 && $numdmero <20000){
            $numde = decena(Floor($numdmero/1000))."MIL ".(centena($numdmero%1000));
        }
        if ($numdmero >= 20000 && $numdmero <100000){
            $numde = decena(Floor($numdmero/1000))." MIL ".(miles($numdmero%1000));
        }
        if ($numdmero < 10000)
            $numde = miles($numdmero);

        return $numde;
    }

    function cienmiles($numcmero){
        if ($numcmero == 100000)
            $num_letracm = "CIEN MIL";
        if ($numcmero >= 100000 && $numcmero <1000000){
            $num_letracm = centena(Floor($numcmero/1000))." MIL ".(centena($numcmero%1000));
        }
        if ($numcmero < 100000)
            $num_letracm = decmiles($numcmero);
        return $num_letracm;
    }

    function millon($nummiero){
        if ($nummiero >= 1000000 && $nummiero <2000000){
            $num_letramm = "UN MILLON ".(cienmiles($nummiero%1000000));
        }
        if ($nummiero >= 2000000 && $nummiero <10000000){
            $num_letramm = unidad(Floor($nummiero/1000000))." MILLONES ".(cienmiles($nummiero%1000000));
        }
        if ($nummiero < 1000000)
            $num_letramm = cienmiles($nummiero);

        return $num_letramm;
    }

    function decmillon($numerodm){
        if ($numerodm == 10000000)
            $num_letradmm = "DIEZ MILLONES";
        if ($numerodm > 10000000 && $numerodm <20000000){
            $num_letradmm = decena(Floor($numerodm/1000000))."MILLONES ".(cienmiles($numerodm%1000000));
        }
        if ($numerodm >= 20000000 && $numerodm <100000000){
            $num_letradmm = decena(Floor($numerodm/1000000))." MILLONES ".(millon($numerodm%1000000));
        }
        if ($numerodm < 10000000)
            $num_letradmm = millon($numerodm);

        return $num_letradmm;
    }

    function cienmillon($numcmeros){
        if ($numcmeros == 100000000)
            $num_letracms = "CIEN MILLONES";
        if ($numcmeros >= 100000000 && $numcmeros <1000000000){
            $num_letracms = centena(Floor($numcmeros/1000000))." MILLONES ".(millon($numcmeros%1000000));
        }
        if ($numcmeros < 100000000)
            $num_letracms = decmillon($numcmeros);
        return $num_letracms;
    }

    function milmillon($nummierod){
        if ($nummierod >= 1000000000 && $nummierod <2000000000){
            $num_letrammd = "MIL ".(cienmillon($nummierod%1000000000));
        }
        if ($nummierod >= 2000000000 && $nummierod <10000000000){
            $num_letrammd = unidad(Floor($nummierod/1000000000))." MIL ".(cienmillon($nummierod%1000000000));
        }
        if ($nummierod < 1000000000)
            $num_letrammd = cienmillon($nummierod);

        return $num_letrammd;
    }

    function convertir($numero){
        $tempnum = explode('.',$numero);

        if ($tempnum[0] !== ""){
            $numf = milmillon($tempnum[0]);
            if ($numf == "UNO")
            {
                $numf = substr($numf, 0, -1);
                $Ps = " ";
            }
            else
            {
                $Ps = " ";
            }
            $TextEnd = $numf;
            $TextEnd .= $Ps;
        }
        if ($tempnum[1] == "" || $tempnum[1] >=  100)
        {
            $tempnum[1] = "00" ;
        }
        $TextEnd .= $tempnum[1] ;
        $TextEnd .= "/100 ";
        return $TextEnd;
    }
}

?>