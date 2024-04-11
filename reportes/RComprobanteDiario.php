<?php
// Extend the TCPDF class to create custom MultiRow
class RComprobanteDiario extends ReportePDF {

	var $datos_detalle;
	var $ancho_hoja;
	var $total;
	
	function datosHeader ($detalle,$resultado,$hasta,$desde) {
		$this->SetHeaderMargin(10);
		$this->SetAutoPageBreak(TRUE, 10);
		$this->ancho_hoja = $this->getPageWidth()-PDF_MARGIN_LEFT-PDF_MARGIN_RIGHT-10;
		$this->datos_detalle = $detalle;
		$this->SetMargins(20, 15, 5,10);
	}
	
	function Header() {		
	}
	//	
	function generarCabecera(){
		$this->tablewidths=array(7,25,20,15,15,50,28,15,15);
		$this->tablealigns=array('C','C','C','C','C','C','C','C','C');
		$this->tablenumbers=array(0,0,0,0,0,0,0,0,0);
		$this->tableborders=array('LRTB','LRTB','LRTB','LRTB','LRTB','LRTB','LRTB','LRTB','LRTB');
		$this->tabletextcolor=array();

		$RowArray = array (
			's0' => 'Nº',				
			's1' => 'Nro DE COMPROBANTE',
			's2' => 'Nro TRAMITE',
			's3' => 'DEBE',
			's4' => 'HABER',
			's5' => 'DESCRIPCIÓN',
			's6' => 'CTA CONTABLE',
			's7' => 'FECHA'
			//'s8' => 'FECHA'
		);
		$this->MultiRow($RowArray, false, 1);
	}
	//
	function generarReporte() {
		$this->setFontSubsetting(false);
		$this->AddPage();
		$this->cab();
		$this->generarCuerpo($this->datos_detalle);
	}
	//		
	function generarCuerpo($detalle){		
		$count = 1;
		$fill = 0;
		$this->total = count($detalle);
		foreach ($detalle as $val) {			
			$this->imprimirLinea($val,$count,$fill);
			$fill = !$fill;
			$count = $count + 1;
			$this->total = $this->total -1;
			$this->revisarfinPagina();
		}
	}
	//desde 
	function imprimirLinea($val,$count,$fill){
		$this->SetFillColor(224, 235, 255);
		$this->SetTextColor(0);
		$this->SetFont('','',6);
	
		$RowArray = array(  's0' => $count,
							's1' => $val['nro_cbte'],
                            's2' => $val['nro_tramite'],
                            's3' => $val['importe_debe'],
                            's4' => $val['importe_haber'],
                            's5' => $val['glosa1'],
                            's6' => $val['nro_cuenta'],
                            's7' => substr($val['fecha_reg'], 0, 10)
                         );
											
		$this-> MultiRow($RowArray,$fill,0);
	} 
	//desde generarcuerpo
	function revisarfinPagina(){
		$startY = $this->GetY();

		if ($startY > 230) {
			if($this->total!= 0){
				$this->AddPage();
				$this->generarCabecera();
			}
			
		}
	}
	//
	function Footer() {		
		$this->setY(-15);
		$ormargins = $this->getOriginalMargins();
		$this->SetTextColor(0, 0, 0);
		$line_width = 0.85 / $this->getScaleFactor();
		$this->SetLineStyle(array('width' => $line_width, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
		$ancho = round(($this->getPageWidth() - $ormargins['left'] - $ormargins['right']) / 3);
		$this->Ln(2);
		$this->Cell($ancho, 0, '', '', 0, 'L');
		$pagenumtxt = 'Página'.' '.$this->getAliasNumPage().' de '.$this->getAliasNbPages();
		$this->Cell($ancho, 0, $pagenumtxt, '', 0, 'C');
		$this->Cell($ancho, 0, '', '', 0, 'R');
		$this->Ln();
		$this->Cell($ancho, 0, '', '', 0, 'L');
		$this->Ln($line_width);
	}
	
	function cab() {
		//cabecera del reporte
		$this->Image(dirname(__FILE__).'/../../lib'.$_SESSION['_DIR_LOGO'], 18, 15, 40, 10);
		$html='<br><br><table cellpadding="0" border="0" style="font-size: 9px">
			<tr><td><b>Gestión:</b> '.date("Y").'</td></tr>
			<tr><td><b>Fecha:</b> '.date("d-m-Y").'</td></tr>
			<tr><td><b>Usuario:</b> '.$_SESSION["_LOGIN"].'</td></tr>
			</table>';
		$this->writeHTMLCell(0, 0, $this->ancho_hoja-10, 5, $html, 0, 0, 0, true, 'L', false);
		$this->ln(5);
		$this->SetFont('','B',12);
		$this->Cell(0,5,'Libro Diario',0,1,'C');

		$this->SetFont('','B',8);		
		$this->Cell(0,5,'Del  '.substr($this->objParam->getParametro('fecIni'),0,10).'  al  '.substr($this->objParam->getParametro('fecFin'),0,10),0,1,'C');
		$this->SetFont('','B',8);
		$this->Cell(0,5,'(Expresado en Bolivianos)',0,1,'C');
	
		$this->generarCabecera();
	}	
}
?>