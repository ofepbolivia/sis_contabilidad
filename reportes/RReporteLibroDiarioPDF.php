<?php
//require_once dirname(__FILE__).'/../../pxp/lib/lib_reporte/ReportePDF.php';
//require_once dirname(__FILE__).'/../../lib/tcpdf/tcpdf_barcodes_2d.php';
set_time_limit(400);

class RReporteLibroDiarioPDF extends  ReportePDF{
    var $datos ;
    var $ancho_hoja;
    var $gerencia;
    var $numeracion;
    var $ancho_sin_totales;
    var $cantidad_columnas_estaticas;
    var $total = 0;
//$fecha = date("d/m/Y", strtotime($record["fecha"]));

    function Header() {

      $this->setPrintFooter(false);
      //$this->setFooterData(array(0,64,0), array(0,64,128));
      //$this->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
      //$this->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);

      $this->SetXY(5,5);


      if ($this->objParam->getParametro('desde') == '' && $this->objParam->getParametro('hasta') != '') {
        $cabecera_datos = ' <td style="width: 200px; text-align: center; vertical-align: middle; height: 42px;"><strong>Departamento:</strong> Contabilidad <br /><strong>Desde: </strong>01/01/'.$this->objParam->getParametro('gestion').' <strong>Hasta:</strong> '.$this->objParam->getParametro('hasta').'<br /><strong>Gesti&oacute;n:</strong>'.$this->objParam->getParametro('gestion').'</td>
                          ';
      } elseif ($this->objParam->getParametro('hasta') == '' && $this->objParam->getParametro('desde') != '') {
        $cabecera_datos = '
                            <td style="width: 200px; text-align: center; vertical-align: middle; height: 42px;"><strong>Departamento:</strong> Contabilidad <br /><strong>Desde: </strong>'.$this->objParam->getParametro('desde').' <strong>Hasta:</strong> 31/12/'.$this->objParam->getParametro('gestion').'<br /><strong>Gesti&oacute;n:</strong> '.$this->objParam->getParametro('gestion').'</td>

                          ';
      } elseif ($this->objParam->getParametro('hasta') != '' && $this->objParam->getParametro('desde') != '') {
        $cabecera_datos = '
                            <td style="width: 200px; text-align: center; vertical-align: middle; height: 42px;"><strong>Departamento:</strong> Contabilidad <br /><strong>Desde: </strong>'.$this->objParam->getParametro('desde').' <strong>Hasta:</strong> '.$this->objParam->getParametro('hasta').'<br /><strong>Gesti&oacute;n:</strong> '.$this->objParam->getParametro('gestion').'</td>
                          ';
      } elseif ($this->objParam->getParametro('hasta') == '' && $this->objParam->getParametro('desde') == '') {
        $cabecera_datos = '
                            <td style="width: 200px; text-align: center; vertical-align: middle; height: 42px;"><strong>Departamento:</strong> Contabilidad <br /><strong>Desde: </strong> 01/01/'.$this->objParam->getParametro('gestion').' <strong>Hasta:</strong> 31/12/'.$this->objParam->getParametro('gestion').'<br /><strong>Gesti&oacute;n:</strong> '.$this->objParam->getParametro('gestion').'</td>
                          ';
      }


      $cabecera = '<font size="8">
                <table  style="height: 20px;" border = "1" cellspacing="0" cellpadding="2" >
                  <tbody>
                    <tr style="height: 40px;">
                      <td style="width: 100px; height: 42px;">&nbsp;<img  style="width: 80px;" align="middle" src="'.dirname(__FILE__).'/../../lib'.$_SESSION['_DIR_LOGO'].'" alt="Logo"></td>
                      <td style="width: 655px; text-align: center; vertical-align: middle; height: 42px;"><h1>REPORTE <br>LIBRO DIARIO</h1></td>
                      '.$cabecera_datos.'
                    </tr>
                  </tbody>
                </table>
              </font>
              ';

              $this->writeHTML($cabecera, true, 0, true, 0);

              //$this->SetAutoPageBreak(true, 2);

    }

    //function setDatos($datos,$saldo_anterior,$recuperar_cabecera) {
    function setDatos($datos,$recuperar_cabecera) {

        $this->datos = $datos;
        //$this->saldo_anterior = $saldo_anterior;
        $this->cabezera = $recuperar_cabecera;
        //var_dump( $this->datos);
    }

    function  generarReporte()
    {

    $this->AddPage();
    $this->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
    $this->SetXY(5,20);

        /*******************************************************************************************************************************************/
          $this->SetX(7);
          $this->SetMargins(7, 23, 0);

        /************************************Creamos la estructura para el detalle Ismael Valdivia (04/12/2019)****************************************/
        $tabla_datos = '
                          <table style="text-align: center; font-size: 9px;" border="0" cellspacing="0" cellpadding="2">
                            <thead>
                                <tr style="font-size: 9px;">
                                    <td style="border:1px solid black; width: 30px;"><strong>#</strong></td>
                                    <td style="border:1px solid black; width: 100px;"><strong>Nro Comprobante</strong></td>
                                    <td style="border:1px solid black; width: 60px;"><strong>Nro Tramite</strong></td>
                                    <td style="border:1px solid black; width: 52px;"><strong>Fecha</strong></td>
                                    <td style="border:1px solid black; width: 60px;"><strong>Nro Cuenta</strong></td>
                                    <td style="border:1px solid black; width: 410px;"><strong>Glosa</strong></td>
                                    <td style="border:1px solid black; width: 80px;"><strong>Importe Debe</strong></td>
                                    <td style="border:1px solid black; width: 80px;"><strong>Importe Haber</strong></td>
                                    <td style="border:1px solid black; width: 80px;"><strong>Saldo</strong></td>
                                </tr>
                            </thead> ';


          /**************************************************************************/
        //        foreach( $this->saldo_anterior as $anterior){
        //          $tabla_datos.= '<tr style="font-size: 9px;">
        //                          <td style="width:712px; border:1px solid black; text-align: center; vertical-align: middle;"><strong>SALDO ANTERIOR</strong></td>
        //                          <td style="width:80px; border:1px solid black; text-align: right; vertical-align: middle;">'.number_format($anterior["total_debe_anterior"], 2, ',', '.').'</td>
        //                          <td style="width:80px; border:1px solid black; text-align: right; vertical-align: middle;">'.number_format($anterior["total_haber_anterior"], 2, ',', '.').'</td>
        //                          <td style="width:80px; border:1px solid black; text-align: right; vertical-align: middle;">'.number_format($anterior["saldo_anterior"], 2, ',', '.').'</td>
        //                          </tr>';
        //          $total_debe = $anterior["total_debe_anterior"];
        //          $total_haber = $anterior["total_haber_anterior"];
        //          $saldo_anterior = $anterior["saldo_anterior"];
        //        }


          /***************************************************************************************************************************************************/
              $contador=1;
              foreach( $this->datos as $record){
                        $tabla_datos .='<tbody>
                        <tr nobr="true">
                        <td style="border:1px solid black; width: 30px; text-align: left; vertical-align: middle;">'.$contador.'</td>
                        <td style="border:1px solid black; width: 100px; text-align: left; vertical-align: middle;">'.$record["nro_cbte"].'</td>
                        <td style="border:1px solid black; width: 60px; text-align: left; vertical-align: middle;">'.$record["nro_tramite"].'</td>
                        <td style="border:1px solid black; width: 52px; text-align: left; vertical-align: middle;">'.date("d/m/Y", strtotime($record["fecha"])).'</td>
                        <td style="border:1px solid black; width: 60px; text-align: left; vertical-align: middle;">'.$record["nro_cuenta"].'</td>
                        <td style="border:1px solid black; width: 410px; text-align: left; vertical-align: middle;">'.$record["glosa1"].'</td>
                        <td style="border:1px solid black; width: 80px; text-align: right; vertical-align: middle;">'.number_format($record["importe_debe_mb"], 2, ',', '.').'</td>
                        <td style="border:1px solid black; width: 80px; text-align: right; vertical-align: middle;">'.number_format($record["importe_haber_mb"], 2, ',', '.').'</td>
                        <td style="border:1px solid black; width: 80px; text-align: right; vertical-align: middle;">'.number_format($record["importe_saldo_mb"], 2, ',', '.').'</td>
                        </tr>
                        </tbody>
                        ';
                  $total_debe += $record["importe_debe_mb"];
                  $total_haber += $record["importe_haber_mb"];
                  $contador++;

            }
          /****************************************************************************************************************************************/
          $numero_cuenta = substr($cabezera['desc_cuenta'], 0, 3);
          $inicial_cuenta = substr($cabezera['desc_cuenta'], 0, 0);
          $cuenta_acreedora = substr($cabezera['desc_cuenta'], 0, 2);

          if ($inicial_cuenta = '1') {
            if ($numero_cuenta = '124' OR  $numero_cuenta = '114') {
                  $comportamiento = 'pasivo';
              }else{
                    $comportamiento = 'activo';
                   }
          }
          if ($inicial_cuenta = '4' or $inicial_cuenta = '6') {
            $comportamiento = 'activo';
          }
           /*Si la cuenta inicia con 2 o 3 o 5 pertenece a un pasivo*/
            if ($inicial_cuenta = '2' or $inicial_cuenta = '3' or $inicial_cuenta = '5') {
              $comportamiento = 'pasivo';
            }

            if ($inicial_cuenta = '8') {
              if ($cuenta_acreedora = '81') {
                $comportamiento = 'activo';
              } else if ($cuenta_acreedora = '82') {
                $comportamiento = 'pasivo';
              }
            }

            if ($comportamiento == 'activo') {
            $saldo_total = $total_debe-$total_haber;
          } elseif ($comportamiento == 'pasivo') {
            $saldo_total = $total_haber-$total_debe;
          }

            $tabla_datos .= '</table>
                          ';
        //                  <tr style="font-size: 9px;">
        //                              <td colspan="5"  style=" border:1px solid black; text-align: right; vertical-align: middle;"><strong>TOTALES</strong></td>
        //                              <td style="border:1px solid black; text-align: right; vertical-align: middle;">'.number_format($total_debe, 2, ',', '.').'</td>
        //                              <td style="border:1px solid black; text-align: right; vertical-align: middle;">'.number_format($total_haber, 2, ',', '.').'</td>
        //                              <td style="border:1px solid black; text-align: right; vertical-align: middle;">'.number_format($saldo_total, 2, ',', '.').'</td>
        //                          </tr>
                          
                          //  <td style="border-right:2px solid white; border-bottom: 2px solid white;">'.$total.'</td>

          //$this->writeHTML($tabla_datos);
          //$this->writeHTMLCell('', '', '', '', $tabla_datos, 0, 0, 0, true, '', true);
          $this->writeHTML($tabla_datos, true, false, true, false, '');
    //
    }

}
?>
