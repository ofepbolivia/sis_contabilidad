<?php

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
	

// Extend the TCPDF class to create custom MultiRow
class RComprobanteDiarioDet extends  ReportePDF {
	var $datos_titulo;
	var $datos_detalle;
	var $ancho_hoja;
	var $gerencia;
	var $numeracion;
	var $ancho_sin_totales;
	var $cantidad_columnas_estaticas;
	var $s1;
	var $t1;
	var $tg1;
	var $total;
	var $datos_entidad;
	var $datos_periodo;
	var $ult_codigo_partida;
	var $ult_concepto;
	var $importe;
	var $importe_haber;
	var$importe_total;

	function datosHeader ($detalle, $totales, $gestion, $dataEmpresa) {
        $this->SetHeaderMargin(8);
        $this->SetAutoPageBreak(TRUE, 12);
		$this->ancho_hoja = $this->getPageWidth()-PDF_MARGIN_LEFT-PDF_MARGIN_RIGHT-10;
		$this->datos_detalle = $detalle;
		$this->datos_titulo = $totales;
		$this->datos_entidad = $dataEmpresa;
		$this->datos_gestion = $gestion;
		$this->subtotal = 0;
		$this->SetMargins(7, 45, 5);
	}

	function generarCuerpo($detalle) {

		$count = 1;
		$sw = 0;
		$sw1 = 0;
		$this->ult_codigo_partida = '';
		$this->ult_concepto = '';
		$fill = 0;

		$this->total = count($detalle);

		$this->s1 = 0;
		$this->t1 = 0;
		$this->tg1 = 0;
		$this->SetMargins(7, 50, 10);

		foreach ($detalle as $val) {


			if($sw == 0){
				$fill = 0;
				$this->imprimircabecera1($val["glosa1"]." - ".$val["glosa1"],$fill);
				$fill = !$fill;
				$sw = 1;
				$this->ult_codigo_glosa1 = $val["glosa1"];
			}

			$this->imprimirInicio($val,$count,$fill);
			$fill = !$fill;
			$count = $count + 1;
			$this->total = $this->total -1;

			$this->imprimirLinea($val,$count,$fill);
			$fill = !$fill;
			$count = $count + 1;
			$this->total = $this->total -1;
			//$this->revisarfinPagina();

			
		//	$this->imprimirLineaUno($val,$count,$fill);
		//	$fill = !$fill;
		//	$count = $count + 1;
		//	$this->total = $this->total -1;

			$this->imprimirLineabk($val,$count,$fill);
			$fill = !$fill;
			$count = $count + 1;
			$this->total = $this->total -1;

			$this->imprimirLineabkUno($val,$count,$fill);
			$fill = !$fill;
			$count = $count + 1;
			$this->total = $this->total -1;

			
			$this->imprimirLineaDos($val,$count,$fill);
			$fill = !$fill;
			$count = $count + 1;
			$this->total = $this->total -1;


			$this->imprimirLineabk($val,$count,$fill);
			$fill = !$fill;
			$count = $count + 1;
			$this->total = $this->total -1;


			$this->imprimirLineaTres($val,$count,$fill);
			$fill = !$fill;
			$count = $count + 1;
			$this->total = $this->total -1;

			
			$this->imprimirLineaCuatro($val,$count,$fill);
			$fill = !$fill;
			$count = $count + 1;
			$this->total = $this->total -1;

			$this->imprimirLineaCinco($val,$count,$fill);
			$fill = !$fill;
			$count = $count + 1;
			$this->total = $this->total -1;

			$this->imprimirLineaSeis($val,$count,$fill);
			$fill = !$fill;
			$count = $count + 1;
			$this->total = $this->total -1;

			$this->imprimirLineabk($val,$count,$fill);
			$fill = !$fill;
			$count = $count + 1;
			$this->total = $this->total -1;


			$this->imprimirLineaSiete($val,$count,$fill);
			$fill = !$fill;
			$count = $count + 1;
			$this->total = $this->total -1;

			$this->imprimirLineabk($val,$count,$fill);
			$fill = !$fill;
			$count = $count + 1;
			$this->total = $this->total -1;

			$this->imprimirLineaOcho($val,$count,$fill);
			$fill = !$fill;
			$count = $count + 1;
			$this->total = $this->total -1;

			$this->imprimirLineaOchoNueve($val,$count,$fill);
			$fill = !$fill;
			$count = $count + 1;
			$this->total = $this->total -1;

			$this->imprimirLineaNueve($val,$count,$fill);
			$fill = !$fill;
			$count = $count + 1;
			$this->total = $this->total -1;

			$this->imprimirLineabk($val,$count,$fill);
			$fill = !$fill;
			$count = $count + 1;
			$this->total = $this->total -1;
	
		}
	}
	
		function Header(){
		
		$white = array('LTRB' =>array('width' => 0.3, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(255, 255, 255)));
        //$black = array('T' =>array('width' => 0.3, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));

		$this->Ln(1);
		$this->Cell(200,25,'','LRBT','C',1,0);
		//cabecera del reporte
		$this->Image(dirname(__FILE__).'/../../lib'.$_SESSION['_DIR_LOGO'], 10, 10, 40, 20);
		
		$this->ln(5);

	    // $html='<br><br>
		//    <table cellpadding="0" border="1" style="font-size: 9px" width="91%">
		//	<tr><td><b>Gestión:</b> '.date("Y").'</td></tr>
		//	<tr><td><b>Fecha:</b> '.date("d-m-Y").'</td></tr>
        //    <tr><td><b>Depto:</b> '.$this->objParam->getParametro('fecIni').' </td></tr>
		//	<tr><td><b>Nro. Cbte:</b> '.$val['nro_cbte'].' </td></tr>
		//	<tr><td><b>Estado:</b>  '.$this->objParam->getParametro('estado_reg').' </td></tr>
		//	<tr><td><b>Usuario:</b> '.$_SESSION["_USUARIO"].'</td></tr>
		//	</table>';
		$this->writeHTMLCell(0, 0, $this->ancho_hoja-10, 5, $html, 0, 0, 0, true, 'L', false);
		
		$this->ln(5);
		$this->SetFont('','B',12);
		$this->Cell(0,5,'Comprobante de Diario Presupuestario',0,1,'C');

		$this->SetFont('','B',8);
		//$this->MultiCell(0,5,'','L','C',1,0); 
		$this->Cell(0,5, 'Del  '.substr($this->objParam->getParametro('fecIni'),0,10).'  al  '.substr($this->objParam->getParametro('fecFin'),0,10),0,1,'C');

		$this->SetFont('','B',8);

		$this->Cell(0,5,'(Expresado en Bolivianos)',0,1,'C');
	}

   function generarReporte() {
		//$this->setFontSubsetting(false);
		$this->AddPage();
		
		
		//$this->SetXY(6,30);
		//$this->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
		//$sw = false;
		//$concepto = '';

		$this->generarCuerpo($this->datos_detalle);
		//$this->cerrarCuadro();
		//$this->Ln(4);
		//$this->cerrarConcepto();
		//$this->Ln(4);
		//$this->cerrarCuadroTotal();
		//$this->Ln(4);
	}
   // function generarCabecera(){
		//armca caecera de la tabla
//		$conf_par_tablewidths=array(10,40,30,30,20,20,20,30);
//        $conf_par_tablealigns=array('C','C','C','C','C','C','C','C');
//        $conf_par_tablenumbers=array(0,0,0,0,0,0,0,0);
//        $conf_tableborders=array();
//        $conf_tabletextcolor=array();

//		$this->tablewidths=$conf_par_tablewidths;
//        $this->tablealigns=$conf_par_tablealigns;
//        $this->tablenumbers=$conf_par_tablenumbers;
//        $this->tableborders=$conf_tableborders;
//        $this->tabletextcolor=$conf_tabletextcolor;

//		$RowArray = array(
//            			's0' => 'Nº',
//						's1' => 'DEPARTAMENTO',
//                        's2' => 'CONCEPTO DE GASTO',
//                        's3' => 'JUSTIFICACION',
//                        's4' => 'UNIDAD DE MEDIDA',
//                        's5' => 'COSTO UNITARIO',
//                        's6' => 'CANT. REQ.',
//                        's7' => 'TOTAL ESTACIONALIDAD');

        //$this-> MultiRow($RowArray,false,1);
 //   }

	
			function imprimirInicio($val,$count,$fill){

				$this->SetFillColor(232, 232, 232);
				$this->SetTextColor(0);
				$this->SetFont('','B',7);

				$conf_par_tablewidths=array(68,66,66);
				$conf_par_tablealigns=array('L','L','L');
				$conf_par_tablenumbers=array(0,0,0);
				$conf_tableborders=array('LRBT','LRBT','LRBT');

				$this->tablewidths=$conf_par_tablewidths;
				$this->tablealigns=$conf_par_tablealigns;
				$this->tablenumbers=$conf_par_tablenumbers;
				$this->tableborders=$conf_tableborders;
				$this->tabletextcolor=$conf_tabletextcolor;

				$this->caclularMontos($val);

				$newDate = date("d/m/Y", strtotime( $val['fecha_reg']));

				$RowArray = array(
								's0' => 'Gestión: '.date("Y").'
								Fecha de Registro: '.substr($val['fecha_reg'],0,10),
								's1' => ' Depto: '.$val['desc_depto'].'
										 Cbte: '.$val['nro_cbte'],
								's2' =>	'Usuario: '.$val['usr_reg'].'
										 Estado: '.$val['estado_reg'],
							);

				$this-> MultiRow($RowArray,$fill,1);
			}
 
	function imprimirLinea($val,$count,$fill){

		$this->SetFillColor(255, 255, 255);
        $this->SetTextColor(0);
        $this->SetFont('','',7);

		$conf_par_tablewidths=array(100,100);
        $conf_par_tablealigns=array('L','L');
        $conf_par_tablenumbers=array(0,0);
		$conf_tableborders=array('LRBT','LRBT');

		$this->tablewidths=$conf_par_tablewidths;
        $this->tablealigns=$conf_par_tablealigns;
        $this->tablenumbers=$conf_par_tablenumbers;
        $this->tableborders=$conf_tableborders;
        $this->tabletextcolor=$conf_tabletextcolor;

		$this->caclularMontos($val);

		$newDate = date("d/m/Y", strtotime( $val['fecha_reg']));

		$RowArray = array(
						's0' => 'Glosa: '.$val['glosa1'],
                        's1' => 'Tramite: '.$val['nro_tramite'].'
						         Fecha: '.substr($val['fecha_reg'],0,10).'
						         Dctos: '.$val['dctos'].'
						         Periodo de Costo: '.$val['periodo']
					);

		$this-> MultiRow($RowArray,$fill,1);
	}
	
	function imprimirlineabk($titulo,$fill){

		$this->SetFont('','B',9);
		$this->tablewidths=array(10+40+30+30+20+20+20+30);
        $this->tablealigns=array('L');
        $this->tablenumbers=array(0);
        $this->tableborders=array('');
        //$this->tabletextcolor=$conf_tabletextcolor;

		$RowArray = array(
            			's0' => '');
        $this-> MultiRow($RowArray,$fill,2);
	}
	//function imprimirLineaUno($val,$count,$fill){

	//	$this->SetFillColor(224, 235, 255);
    //    $this->SetTextColor(0);
    //    $this->SetFont('','',7);

	//	$conf_par_tablewidths=array(10,40,30,30,20,20,20,30);
    //    $conf_par_tablealigns=array('C','L','L','L','L','R','R','R');
    //    $conf_par_tablenumbers=array(0,0,0,0,0,2,2,2);
	//	$conf_tableborders=array('T','T','T','T','T','T','T','T');

	//	$this->tablewidths=$conf_par_tablewidths;
    //    $this->tablealigns=$conf_par_tablealigns;
    //    $this->tablenumbers=$conf_par_tablenumbers;
    //    $this->tableborders=$conf_tableborders;
    //    $this->tabletextcolor=$conf_tabletextcolor;

	//	$this->caclularMontos($val);

	//	$newDate = date("d/m/Y", strtotime( $val['fecha_reg']));

	//	$RowArray = array(
    //        			's0' => $count,
	//					's1' => $val['nro_cuenta'],
    //                    's2' => $val['nro_cbte'],
    //                    's3' => $val['nro_tramite'],
    //                    's4' => $val['glosa1'],
    //                    's5' => $val['importe_debe'],
	//					's6' => $val['importe_haber'],
    //                    's7' => substr($val['fecha_reg'], 0, 10));

	//	$this-> MultiRow($RowArray,$fill,1);
	//}


	function imprimirLineaDos($val,$count,$fill){

		$this->SetFillColor(255, 255, 255);
        $this->SetTextColor(0);
        $this->SetFont('','B',7);

		$conf_par_tablewidths=array(20,30,20,40,30,30,30);
        $conf_par_tablealigns=array('L','L','L','L','L','L','L');
        $conf_par_tablenumbers=array(0,0,0,0,0,0,0);
		$conf_tableborders=array('LRBT','LRBT','LRBT','LRBT','LRBT','LRBT','LRBT');

		$this->tablewidths=$conf_par_tablewidths;
        $this->tablealigns=$conf_par_tablealigns;
        $this->tablenumbers=$conf_par_tablenumbers;
        $this->tableborders=$conf_tableborders;
        $this->tabletextcolor=$conf_tabletextcolor;

		//$this->caclularMontos($val);

		$newDate = date("d/m/Y", strtotime( $val['fecha_reg']));

		$RowArray = array(
						's0' => 'Tipo Doc: 
						NIT',
                        's1' => 'Nro Documento: '
						.$val['nombre'],
						's2' => 'Expedido: 
						LP',
						's3' => 'Nombre o Razón Social: '
						.$val['banco'],
						's4' => 'Banco: 
						'.$val['banco'],
						's5' => 'Cuenta: 
						'.$val['nro_cuenta_bancaria_sigma'],
						's6' => 'Importe: 
						$'.$val['importe']
					);
	$this-> MultiRow($RowArray,$fill,3);
	}

	function imprimirlineabkUno($titulo,$fill){

		$this->SetFont('','B',9);
		$this->tablewidths=array(10+40+30+30+20+20+20+30);
        $this->tablealigns=array('L');
        $this->tablenumbers=array(0);
        $this->tableborders=array('LRBT');
        //$this->tabletextcolor=$conf_tabletextcolor;

		$RowArray = array(
            			's0' => 'Beneficiarios');
        $this-> MultiRow($RowArray,$fill,2);
	}


	function imprimirLineaTres($val,$count,$fill){

		$this->SetFillColor(255, 255, 255);
        $this->SetTextColor(0);
        $this->SetFont('','B',8);

		$conf_par_tablewidths=array(110,90);
        $conf_par_tablealigns=array('C','C');
        $conf_par_tablenumbers=array(0,0);
		$conf_tableborders=array('LRBT','LRBT');

		$this->tablewidths=$conf_par_tablewidths;
        $this->tablealigns=$conf_par_tablealigns;
        $this->tablenumbers=$conf_par_tablenumbers;
        $this->tableborders=$conf_tableborders;
        $this->tabletextcolor=$conf_tabletextcolor;

		//$this->caclularMontos($val);

		$newDate = date("d/m/Y", strtotime( $val['fecha_reg']));

		$RowArray = array(

						's0' => '
						DETALLE: ',
                        's1' => '
						IMPORTE: ' 
					);

		$this-> MultiRow($RowArray,$fill,3);
	}

	function imprimirLineaCuatro($val,$count,$fill){

		$this->SetFillColor(255, 255, 255);
        $this->SetTextColor(0);
        $this->SetFont('','B',8);

		$conf_par_tablewidths=array(110,30,30,30);
        $conf_par_tablealigns=array('C','C','C','C');
        $conf_par_tablenumbers=array(0,0,0,0);
		$conf_tableborders=array('LRBT','LRBT','LRBT','LRBT');

		$this->tablewidths=$conf_par_tablewidths;
        $this->tablealigns=$conf_par_tablealigns;
        $this->tablenumbers=$conf_par_tablenumbers;
        $this->tableborders=$conf_tableborders;
        $this->tabletextcolor=$conf_tabletextcolor;

		//$this->caclularMontos($val);

		$newDate = date("d/m/Y", strtotime( $val['fecha_reg']));

		$RowArray = array(
						's0' => '',
                        's1' => '
						Ejecución',
						's2' => 
						'
						Debe',
						's3' => '
						Haber'
					);

		$this-> MultiRow($RowArray,$fill,3);
	}

	function imprimirLineaCinco($val,$count,$fill){

		$this->SetFillColor(255, 255, 255);
        $this->SetTextColor(0);
        $this->SetFont('','',7);

		$conf_par_tablewidths=array(110,30,30,30);
        $conf_par_tablealigns=array('L','R','R','R');
        $conf_par_tablenumbers=array(0,0,0,0);
		$conf_tableborders=array('LRBT','LRBT','LRBT','LRBT');

		$this->tablewidths=$conf_par_tablewidths;
        $this->tablealigns=$conf_par_tablealigns;
        $this->tablenumbers=$conf_par_tablenumbers;
        $this->tableborders=$conf_tableborders;
        $this->tabletextcolor=$conf_tabletextcolor;

		//$this->caclularMontos($val);

		$newDate = date("d/m/Y", strtotime( $val['fecha_reg']));

		if($val['importe_debe']==0){

		$RowArray = array(
			's0' =>  'CC: '.$val['nro_tramite'].'
					 Ptda: '.$val['nombre_partida'].'
					 Cta: '.$val['nro_cuenta'].'
					 C31: '.$val['c31'].'
					 Tipo Cambio: '.$val['tipo_cambio'],
			's1' => number_format($val['importe_haber'],2),
			's2' => number_format($val['importe_debe'],2),
			's3' => number_format($val['importe_haber'],2)
			);
		$this-> MultiRow($RowArray,$fill,3);
	}else{
		$RowArray = array(
			's0' =>  'CC: '.$val['nro_tramite'].'
					 Ptda: '.$val['nombre_partida'].'
					 Cta: '.$val['nro_cuenta'].'
					 c31: '.$val['c31'].'
					 Tipo Cambio: '.$val['tipo_cambio'],
					 's1' => number_format($val['importe_debe'],2),
					 's2' => number_format($val['importe_debe'],2),
					 's3' => number_format($val['importe_haber'],2)
			);
		$this-> MultiRow($RowArray,$fill,3);
	}
	}

	function imprimirLineaSeis($val,$count,$fill){

		$this->SetFillColor(255, 255, 255);
        $this->SetTextColor(0);
        $this->SetFont('','',7);

		$conf_par_tablewidths=array(110,30,30,30);
        $conf_par_tablealigns=array('R','R','R','R');
        $conf_par_tablenumbers=array(0,0,0,0);
		$conf_tableborders=array('LRBT','LRBT','LRBT','LRBT');

		$this->tablewidths=$conf_par_tablewidths;
        $this->tablealigns=$conf_par_tablealigns;
        $this->tablenumbers=$conf_par_tablenumbers;
        $this->tableborders=$conf_tableborders;
        $this->tabletextcolor=$conf_tabletextcolor;

		//$this->caclularMontos($val);

		$newDate = date("d/m/Y", strtotime( $val['fecha_reg']));

		$this->s1 = $val['importe_debe'];
		$this->s2 = $val['importe_haber'];
		//$this->s1 = $this->s1 + $val['importe_debe'];
		//$this->s2 = $this->s2 + $val['importe_haber'];

		$RowArray = array(
						's0' => 'TOTALES',
                        's1' => '',
						's2' => number_format($this->s1,2),
						's3' => number_format($this->s2,2)
					);

		$this-> MultiRow($RowArray,$fill,3);
	}

	function imprimirLineaSiete($val,$count,$fill){

		$this->SetFillColor(255, 255, 255);
        $this->SetTextColor(0);
        $this->SetFont('','',8);

		$conf_par_tablewidths=array(200);
        $conf_par_tablealigns=array('L');
        $conf_par_tablenumbers=array(0);
		$conf_tableborders=array('LRBT');

		$this->tablewidths=$conf_par_tablewidths;
        $this->tablealigns=$conf_par_tablealigns;
        $this->tablenumbers=$conf_par_tablenumbers;
        $this->tableborders=$conf_tableborders;
        $this->tabletextcolor=$conf_tabletextcolor;

		//$this->caclularMontos($val);
		if($val['importe_haber']==0 or $val['importe_debe']==0){
			$this->s1 = $val['importe_debe'];
			//$this->s1 = $this->s1 + $val['importe_debe'];
		}else{
			//$this->s1 = $this->s1 + $val['importe_haber'];
			$this->s1 = $val['importe_haber'];
		}
		
		$RowArray = array(
						's0' => '
						 Son: '.convertir($this->s1) .' 
						'
					);

		$this-> MultiRow($RowArray,$fill,3);
	}


	function imprimirLineaOcho($val,$count,$fill){

		$this->SetFillColor(255, 255, 255);
        $this->SetTextColor(0);
        $this->SetFont('','',8);

		$conf_par_tablewidths=array(67,67,66);
        $conf_par_tablealigns=array('L','L','L');
        $conf_par_tablenumbers=array(0,0,0);
		$conf_tableborders=array('LRBT','LRBT','LRBT');

		$this->tablewidths=$conf_par_tablewidths;
        $this->tablealigns=$conf_par_tablealigns;
        $this->tablenumbers=$conf_par_tablenumbers;
        $this->tableborders=$conf_tableborders;
        $this->tabletextcolor=$conf_tabletextcolor;

		//$this->caclularMontos($val);

		$newDate = date("d/m/Y", strtotime( $val['fecha_reg']));

		$RowArray = array(
						's0' => 'Firma 1',
                        's1' => 'Firma 2',
						's2' => 'Firma 3'
					);

		$this-> MultiRow($RowArray,$fill,3);
	}

	function imprimirLineaOchoNueve($val,$count,$fill){

		$this->SetFillColor(255, 255, 255);
        $this->SetTextColor(0);
        $this->SetFont('','',8);

		$conf_par_tablewidths=array(67,67,66);
        $conf_par_tablealigns=array('L','L','L');
        $conf_par_tablenumbers=array(0,0,0);
		$conf_tableborders=array('LRBT','LRBT','LRBT');

		$this->tablewidths=$conf_par_tablewidths;
        $this->tablealigns=$conf_par_tablealigns;
        $this->tablenumbers=$conf_par_tablenumbers;
        $this->tableborders=$conf_tableborders;
        $this->tabletextcolor=$conf_tabletextcolor;

		//$this->caclularMontos($val);

		$newDate = date("d/m/Y", strtotime( $val['fecha_reg']));

		$RowArray = array(
						's0' => ' 
						
						',
                        's1' => '
						
						',
						's2' => '
						
						'
					);

		$this-> MultiRow($RowArray,$fill,3);
	}

	function imprimirLineaNueve($val,$count,$fill){

		$this->SetFillColor(255, 255, 255);
        $this->SetTextColor(0);
        $this->SetFont('','',8);

		$conf_par_tablewidths=array(67,67,66);
        $conf_par_tablealigns=array('L','L','L');
        $conf_par_tablenumbers=array(0,0,0);
		$conf_tableborders=array('LRBT','LRBT','LRBT');

		$this->tablewidths=$conf_par_tablewidths;
        $this->tablealigns=$conf_par_tablealigns;
        $this->tablenumbers=$conf_par_tablenumbers;
        $this->tableborders=$conf_tableborders;
        $this->tabletextcolor=$conf_tabletextcolor;

		//$this->caclularMontos($val);

		$newDate = date("d/m/Y", strtotime( $val['fecha_reg']));

		$RowArray = array(
						's0' => $val['desc_firma1'],
                        's1' => $val['desc_firma2'],
						's2' => $val['desc_firma3']
					);

		$this-> MultiRow($RowArray,$fill,3);
	}

    function revisarfinPagina(){
		$dimensions = $this->getPageDimensions();
		$hasBorder = false; //flag for fringe case

		$startY = $this->GetY();
		$this->getNumLines($row['cell1data'], 80);

		if ($startY > 180) {

			$k = 	($startY + 4 * 6) + $dimensions['bm'] - ($dimensions['hk']);
			/*
			for($i=0;$i<=k;$i++){
				$this->ln();
				$this->ln();
				$this->ln();
				$this->ln();
				$this->ln();
				$this->ln();
			}*/

			if($this->total!= 0){
				$this->AddPage();
			}
		}
	}

	function imprimircabecera1($titulo,$fill){

		$this->SetFont('','B',9);
		$this->tablewidths=array(10+40+30+30+20+20+20+30);
        $this->tablealigns=array('L');
        $this->tablenumbers=array(0);
        $this->tableborders=array('LRBT');
        $this->tabletextcolor=$conf_tabletextcolor;

		//$RowArray = array(
        //    			's0' => 'Datos');
        //$this-> MultiRow($RowArray,$fill,1);
	}
	function imprimirPartida($titulo,$fill){

	//	$this->SetFont('','B',9);
	//	$this->tablewidths=array(10+40+30+30+20+20+20+30);
    //    $this->tablealigns=array('L');
    //    $this->tablenumbers=array(0);
    //    $this->tableborders=array('B');
    //    $this->tabletextcolor=$conf_tabletextcolor;

	//	$RowArray = array(
    //        			'casa' => 'Beneficiario');
    //    $this-> MultiRow($RowArray,$fill,1);
	}

	function imprimirConcepto($titulo,$fill){
		$conf_par_tablewidths=array(10+40+30+30+20+20+20+30);
        $conf_par_tablealigns=array('L');
        $conf_par_tablenumbers=array(0);
		$conf_tableborders=array('LRBT');
		$this->SetFont('','B',11);


		$this->tablewidths=$conf_par_tablewidths;
        $this->tablealigns=$conf_par_tablealigns;
        $this->tablenumbers=$conf_par_tablenumbers;
        $this->tableborders=$conf_tableborders;
        $this->tabletextcolor=$conf_tabletextcolor;

		$RowArray = array(
            			'casa' => $titulo);

        $this-> MultiRow($RowArray,$fill,1);

	}
	

   function caclularMontos($val){

		$this->s1 = $this->s1 + $val['importe'];
		$this->t1 = $this->t1 + $val['importe'];
		$this->tg1 = $this->tg1 + $val['importe'];
   }

   function cerrarCuadro(){

	   	    //si noes inicio termina el cuardro anterior

			$this->tablewidths=array(66,66,66);
            $this->tablealigns=array('R','R','R');
	        $this->tablenumbers=array(0,0,0);
	        $this->tableborders=array('LRBT','LRBT','LRBT');
			$this->SetFont('','B',8);

	        $RowArray = array(
	                    's0' => $val['desc_firma1'],
						's1' => $val['desc_firma2'],
						's2' => $val['desc_firma3']
	                  );

	        $this-> MultiRow($RowArray,false,1);

			//$this->s1 = 0;

  }

  function cerrarConcepto(){


	   	    //si noes inicio termina el cuardro anterior

			$this->tablewidths=array(10+30+30+30+20+20+20+30);
            $this->tablealigns=array('R','R');
	        $this->tablenumbers=array(0,2,);
	        $this->tableborders=array('LRBT','LRTB');
			$this->SetFont('','B',8);

	        $RowArray = array(
	                    'espacio' => 'TOTAL '.$this->ult_concepto.':',
	                    's1' => $this->t1
	                  );

	        $this-> MultiRow($RowArray,false,1);

			$this->t1 = 0;

  }

  function cerrarCuadroTotal(){

   	    //si noes inicio termina el cuardro anterior
		$this->tablewidths=array(10+30+30+30+20+20+20+30);
        $this->tablealigns=array('C','R');
        $this->tablenumbers=array(0,2);
        $this->tableborders=array('TLRBT','LRTB');
        $this->SetFont('','B',9);
        
       // $this-> MultiRow($RowArray,false,1);

  }
}
?>
