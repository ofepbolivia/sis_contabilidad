<?php
// Extend the TCPDF class to create custom MultiRow
class RComprobanteDiario extends ReportePDF {
	var $datos_titulo;
	var $datos_detalle;
	var $ancho_hoja;
	var $gerencia;
	var $numeracion;
	var $ancho_sin_totales;
	var $cantidad_columnas_estaticas;
	var $s1;
	var $s2;
	var $s3;
	var $s4;
	var $s5;
	var $s6;
	var $s7;
	var $t1;
	var $t2;
	var $t3;
	var $t4;
	var $t5;
	var $t6;
	var $total;
	var $datos_entidad;
	var $datos_periodo;
	var $datos_tpoestado;
	var $datos_auxiliar;
	var $cant;
	var $valor;
	var $tipo_moneda;
	var $tipo_formato;
	var $desde;
	var $hasta;

	
	function datosHeader ($detalle,$resultado,$hasta,$desde) {
		$this->SetHeaderMargin(10);
		$this->SetAutoPageBreak(TRUE, 10);
		$this->ancho_hoja = $this->getPageWidth()-PDF_MARGIN_LEFT-PDF_MARGIN_RIGHT-10;
		$this->datos_detalle = $detalle;
		$this->datos_titulo = $resultado;
		//$this->datos_tpoestado = $tpoestado;
		//$this->datos_auxiliar = $auxiliar;
        $this->tipo_moneda = $tipo_moneda;
        $this->tipo_diario = $tipo_diario;
        $this->desde = $desde;
		$this->hasta = $hasta;
        $this->subtotal = 0;
		$this->SetMargins(20, 15, 5,10);
	}
	
	function Header() {		
	}
	//	
	function generarCabecera(){
		$conf_par_tablewidths=array(7,25,20,15,15,50,28,15,15);
		$conf_par_tablealigns=array('C','C','C','C','C','C','C','C','C');
		$conf_par_tablenumbers=array(0,0,0,0,0,0,0,0,0);
		$conf_tableborders=array();
		$conf_tabletextcolor=array();
		
		$this->tablewidths=$conf_par_tablewidths;
		$this->tablealigns=$conf_par_tablealigns;
		$this->tablenumbers=$conf_par_tablenumbers;
		$this->tableborders=$conf_tableborders;
		$this->tabletextcolor=$conf_tabletextcolor;

		$RowArray = array
		(
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
		$sw = false;
		$concepto = '';		
		$this->generarCuerpo($this->datos_detalle);
		if($this->s1 != 0){
			$this->SetFont('','B',6);
			$this->cerrarCuadro();	
			$this->cerrarCuadroTotal();
		}
	}
	//		
	function generarCuerpo($detalle){		
		//function
		$this->cab();
		//
		$count = 1;
		$sw = 0;
		$ult_region = '';
		$fill = 0;
		$this->total = count($detalle);
		$this->s1 = 0;
		$this->s2 = 0;
		$this->s3 = '';
		$this->s4 = '';
		$this->s5 = 0;
		$this->s6 = 0;
		$this->s7 = 0;
		$this->s8 = 0;
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

		$conf_par_tablewidths=array(7,25,20,15,15,50,28,15,15);
		$conf_par_tablealigns=array('C','L','L','R','R','R','R','R','R');		
		$conf_par_tablenumbers=array(0,0,0,2,2,0,0,0,0,0,0);
		$conf_tableborders=array('LR','LR','LR','LR','LR','LR','LR','LR','LR');		
		
		switch ($this->objParam->getParametro('tipo_moneda')) {
			case 'MA':
				$debe=$val['importe_debe_ma'];
				$haber=$val['importe_haber_ma'];		
				break;
			case 'MT':			
				$debe=$val['importe_debe_mt'];
				$haber=$val['importe_haber_mt'];
				break;
			case 'MB':
				$debe=$val['importe_debe_mb'];
				$haber=$val['importe_haber_mb'];			
				break;		
			default:			
				break;
		}
				
		$cc= (int)($this->objParam->getParametro('cc') === 'true');		
		$partida = (int)($this->objParam->getParametro('partida')=== 'true');
		$auxiliar = (int)($this->objParam->getParametro('auxiliar')=== 'true');
		$ordenes = (int)($this->objParam->getParametro('ordenes')=== 'true');
		$tramite = (int)($this->objParam->getParametro('tramite')=== 'true');
		$crel = (int)($this->objParam->getParametro('relacional')=== 'true');			
		$nro_comprobante = (int)($this->objParam->getParametro('nro_cbte')=== 'true');
		$fec = (int)($this->objParam->getParametro('fec')=== 'true');
        $glosa1 = (int)($this->objParam->getParametro('glosa1')=== 'true');
        $fecha = (int)($this->objParam->getParametro('fecha_reg')=== 'true');
        $importe_debe = (int)($this->objParam->getParametro('$importe_debe')=== 'true');
        $tipo_moneda = (int)($this->objParam->getParametro('$tipo_moneda')=== 'true');
        $importe_haber = (int)($this->objParam->getParametro('$importe_haber')=== 'true');
        $nro_cuenta = (int)($this->objParam->getParametro('$nro_cuenta')=== 'true');
        $desde = (int)($this->objParam->getParametro('desde')=== 'true');
        $hasta = (int)($this->objParam->getParametro('hasta')=== 'true');
        $tipo_moneda = (int)($this->objParam->getParametro('tipo_moneda')=== 'true');
        $desc_moneda = (int)($this->objParam->getParametro('desc_moneda')=== 'true');
		
		$aux='';		
		if($cc == 1){
			$aux=$aux.'CC:'.trim($val['desc_centro_costo'])."\r\n";			
		}else{			
			$aux=$aux.'';
		}
		if($partida == 1){
			$aux=$aux.'Ptda:'.trim($val['desc_partida'])."\r\n";
		}else{
			$aux=$aux.'';
		}
		if($auxiliar == 1){
			$aux=$aux.'Aux:'.trim($val['desc_auxiliar'])."\r\n";
		}else{
			$aux=$aux.'';
		}
		if($ordenes == 1){
			$aux=$aux.'Ptda:'.trim($val['desc_partida'])."\r\n";
		}else{
			$aux=$aux.'';
		}
		if($tramite == 1){
			$aux=$aux.'Tramite:'.strval($val['nro_tramite'])."\r\n";
		}else{
			$aux=$aux.'';
		}
		if($crel == 1 ){
			$aux=$aux.'Cbte Relacional:'.$val['cbte_relacional']."\r\n";
		}else{
			$aux=$aux.'';
		}		
		if($nro_cbte == 1){
			$aux=$aux.'Nro Cbte.:'.trim($val['nro_cbte'])."\r\n";
		}else{
			$aux=$aux.'';
		}	
		if($fec == 1){
			$arr = explode('-', $val['fecha']);
			$newDate = $arr[2].'-'.$arr[1].'-'.$arr[0];
			$aux=$aux.'Fecha:'.$newDate."\r\n";
		}else{
			$aux=$aux.'';
		}
        if($glosa1 == 1){
			$aux=$aux.'Glosa:'.trim($val['glosa1'])."\r\n";
		}else{
			$aux=$aux.'';
		}
        if($debe == 1){
			$aux=$aux.'Debe:'.trim($val['importe_debe'])."\r\n";
		}else{
			$aux=$aux.'';
		}
        if($haber== 1){
			$aux=$aux.'Haber:'.trim($val['importe_haber'])."\r\n";
		}else{
			$aux=$aux.'';
		}
        //if($fec == 1){
		//	$aux=$aux.'Fecha:'.trim($val['fecha_reg'])."\r\n";
		//}else{
		//	$aux=$aux.'';
		//}
        if($fecha_reg == 1){
			$arr = explode('-', $val['fecha_reg']);
			$newDate = $arr[2].'-'.$arr[1].'-'.$arr[0];
			$aux=$aux.'Fecha:'.$newDate."\r\n";
		}else{
			$aux=$aux.'';
		}
        
        if($fecha_reg == 1){
			$fechareg=strtotime($fecha_reg);
            $fecha_reg = date("d/m/Y", $fechareg);
			$aux=$aux.'Fecha:'.$fecha_reg."\r\n";
		}else{
			$aux=$aux.'';
		}
        if($nro_cuenta == 1){
			$aux=$aux.'Nro Cuenta:'.strval(trim($val['nro_cuenta']))."\r\n";
		}else{
			$aux=$aux.'';
		}
        if($tipo_moneda== 1){
			$aux=$aux.'Tipo Moneda:'.trim($val['tipo_moneda'])."\r\n";
		}else{
			$aux=$aux.'';
		}
        if($desde== 1){
			$aux=$aux.'Desde:'.trim($val['desde'])."\r\n";
		}else{
			$aux=$aux.'';
		}
        if($hasta== 1){
			$aux=$aux.'Hasta:'.trim($val['hasta'])."\r\n";
		}else{
			$aux=$aux.'';
		}
        
        $newDate = date("d/m/Y", strtotime( $val['fecha_reg']));
		//			
		//alert('Hola');
		$RowArray = array(  's0' => $count,
							's1' => $val['nro_cbte'],
                            's2' => $val['nro_tramite'],
                            's3' => $val['importe_debe'],
                            's4' => $val['importe_haber'],
                            's5' => $val['glosa1'],
                            's6' => $val['nro_cuenta'],
                            's7' => substr($val['fecha_reg'], 0, 10)
                            //'s8' => substr($val['fecha_reg'], 0, 10)
                         );
											
		$this->tablewidths=$conf_par_tablewidths;
		$this->tablealigns=$conf_par_tablealigns;
		$this->tablenumbers=$conf_par_tablenumbers;
		$this->tableborders=$conf_tableborders;
		$this->tabletextcolor=$conf_tabletextcolor;
		//$this->calcularMontos($val);
		$this-> MultiRow($RowArray,$fill,0);
	} 
	//desde generarcuerpo
	function revisarfinPagina(){
		$dimensions = $this->getPageDimensions();
		$hasBorder = false;
		$startY = $this->GetY();
		$x=0;
		$this->getNumLines($row['cell1data'], 90);

		if ($startY > 230) {
			//$this->cerrarCuadro();
			//$this->cerrarCuadroTotal();
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
		$cur_y = $this->GetY();
		$this->Cell($ancho, 0, '', '', 0, 'L');
		$pagenumtxt = 'Página'.' '.$this->getAliasNumPage().' de '.$this->getAliasNbPages();
		$this->Cell($ancho, 0, $pagenumtxt, '', 0, 'C');
		$this->Cell($ancho, 0, '', '', 0, 'R');
		$this->Ln();
		$fecha_rep = date("d-m-Y H:i:s");
		$this->Cell($ancho, 0, '', '', 0, 'L');
		$this->Ln($line_width);
	}
	//
	//imprimirLinea suma filas
	function calcularMontos($val){
		switch ($this->objParam->getParametro('tipo_moneda')) {
			case 'MA':
				$this->s1 = $this->s1 + $val['importe_debe_ma'];
				$this->s2 = $this->s2 + $val['importe_haber_ma'];		
				break;
			case 'MT':			
				$this->s1 = $this->s1 + $val['importe_debe_mt'];
				$this->s2 = $this->s2 + $val['importe_haber_mt'];
				break;
			case 'MB':
				$this->s1 = $this->s1 + $val['importe_debe_mb'];
				$this->s2 = $this->s2 + $val['importe_haber_mb'];			
				break;		
			default:			
				break;
		}
		
		switch ($this->objParam->getParametro('tipo_moneda')) {
			case 'MA':
				$this->t1 = $this->t1 + $val['importe_debe_ma'];
				$this->t2 = $this->t2 + $val['importe_haber_ma'];		
				break;
			case 'MT':			
				$this->t1 = $this->t1 + $val['importe_debe_mt'];
				$this->t2 = $this->t2 + $val['importe_haber_mt'];
				break;
			case 'MB':
				$this->t1 = $this->t1 + $val['importe_debe_mb'];
				$this->t2 = $this->t2 + $val['importe_haber_mb'];			
				break;		
			default:			
				break;
		}			
	}	
	//revisarfinPagina pie
	function cerrarCuadro(){
		//si noes inicio termina el cuardro anterior
		$conf_par_tablewidths=array(7,80,15,15,15);				
		$this->tablealigns=array('R','R','R','R','R');		
		$this->tablenumbers=array(0,0,0,2,2);
		$this->tableborders=array('T','T','T','LRTB','LRTB');						
		$RowArray = array(  's1' => '',
							's2' => '',
	        				's3' => '',
	        				's4' => '',
	        				's5' => '',
	        				's6' => '',
							's7' => ''
						);		
		$this-> MultiRow($RowArray,false,1);
		$this->s1 = 0;
		$this->s2 = 0;
		$this->s3 = 0;
		$this->s4 = 0;
	}
	//revisarfinPagina pie
	function cerrarCuadroTotal(){
		$conf_par_tablewidths=array(7,80,15,15,15);				
		$this->tablealigns=array('R','R','R','R','R');		
		$this->tablenumbers=array(0,0,0,2,2);
		$this->tableborders=array('','','','LRTB','LRTB');		
							
		$RowArray = array( 
					't1' => '',
					't2' => '',
					't3' => '',
					't4' => '',
					't5' => '',
					't6' => '',
					't7' => ''
				);
		$this-> MultiRow($RowArray,false,1);
	}
	
	function cab() {
		//cabecera del reporte
		$this->Image(dirname(__FILE__).'/../../lib'.$_SESSION['_DIR_LOGO'], 18, 15, 40, 10);
		$html='<br><br><table cellpadding="0" border="0" style="font-size: 9px">
			<tr><td><b>Gestión:</b> '.date("Y").'</td></tr>
			<tr><td><b>Fecha:</b> '.date("d-m-Y").'</td></tr>
            <tr><td><b>Depto:</b> Contabilidad </td></tr>
			<tr><td><b>Usuario:</b> '.$_SESSION["_USUARIO"].'</td></tr>
			</table>';
		$this->writeHTMLCell(0, 0, $this->ancho_hoja-10, 5, $html, 0, 0, 0, true, 'L', false);
		$this->ln(5);
		$this->SetFont('','B',12);
		$this->Cell(0,5,'Libro Diario',0,1,'C');

		//$this->SetFont('','BU',11);
		//$this->Cell(0,5,'',0,1,'C');
		$this->SetFont('','B',8);		
		$this->Cell(0,5,'Del  '.substr($this->objParam->getParametro('fecIni'),0,10).'  al  '.substr($this->objParam->getParametro('fecFin'),0,10),0,1,'C');
		//$this->SetFont('','BU',8);
		//$this->Cell(0,5,'',0,1,'C');
		$this->SetFont('','B',8);
		$this->Cell(0,5,'(Expresado en Bolivianos)',0,1,'C');


		
		$this->Ln(3);
		$this->SetFont('','B',10);		
		
		if($this->objParam->getParametro('fecIni')!=null){
			$desde = $this->objParam->getParametro('fecIni');
			$cant++;	
		}
		if($this->objParam->getParametro('fecFin')!=null){
			$hasta = $this->objParam->getParametro('fecFin');
			$cant++;	
		}
		if($this->objParam->getParametro('aux')!=null){
			$aux = $this->objParam->getParametro('aux');
			$cant++;	
		} 
		if($this->objParam->getParametro('gest')!=null){
			$gest = $this->objParam->getParametro('gest');
			$cant++;	
		}
        
		if($this->objParam->getParametro('depto')!=null){
			$depto = $this->objParam->getParametro('depto');
			$cant++;	
		}						
		if($this->objParam->getParametro('config_tipo_cuenta')!=null){
			$config_tipo_cuenta = $this->objParam->getParametro('config_tipo_cuenta');
			$cant++;	
		}			
		if($this->objParam->getParametro('config_subtipo_cuenta')!=null){
			$config_subtipo_cuenta = $this->objParam->getParametro('config_subtipo_cuenta');
			$cant++;	
		}				
		if($this->objParam->getParametro('cuenta')!=null){
			$cuenta = $this->objParam->getParametro('cuenta');
			$cant++;	
		}
		if($this->objParam->getParametro('partidas')!=null){
			$partidas = $this->objParam->getParametro('partidas');
			$cant++;	
		}
		if($this->objParam->getParametro('tipo_cc')!=null){
			$tipo_cc = $this->objParam->getParametro('tipo_cc');
			$cant++;	
		}			
		if($this->objParam->getParametro('centro_costo')!=null){
			$centro_costo = $this->objParam->getParametro('centro_costo');
			$cant++;	
		}		
		if($this->objParam->getParametro('orden_trabajo')!=null){
			$orden_trabajo = $this->objParam->getParametro('orden_trabajo');
			$cant++;	
		}
		if($this->objParam->getParametro('suborden')!=null){
			$suborden = $this->objParam->getParametro('suborden');
			$cant++;	
		}
		if($this->objParam->getParametro('nro_tram')!=null){
			$nro_tram = $this->objParam->getParametro('nro_tram');
			$cant++;	
		}
        if($this->objParam->getParametro('fecha_reg')!=null){
			$fecha_reg = $this->objParam->getParametro('fecha_reg');
			$cant++;	
		}
        if($this->objParam->getParametro('glosa1')!=null){
			$glosa1 = $this->objParam->getParametro('glosa1');
			$cant++;	
		}
        if($this->objParam->getParametro('importe_debe')!=null){
			$importe_debe = $this->objParam->getParametro('importe_debe');
			$cant++;	
		}
        if($this->objParam->getParametro('importe_haber')!=null){
			$importe_haber = $this->objParam->getParametro('importe_haber');
			$cant++;	
		}
        if($this->objParam->getParametro('nro_cuenta')!=null){
			$nro_cuenta = $this->objParam->getParametro('nro_cuenta');
			$cant++;	
		}
		
		$valor =$cant;	
		if($this->objParam->getParametro('gest')!=null){			
			$gest=$this->objParam->getParametro('gest');
			$this->SetFont('', 'B',6);
			$this->Cell($width1, $height, '', 0, 0, 'L', false, '', 0, false, 'T', 'C');
			$this->SetFont('', '',6);
			$this->Cell($width_c1, $height, 'Gestion:', 0, 0, 'L', false, '', 0, false, 'T', 'C');
			$this->SetFillColor(192,192,192, true);			
			$this->Cell($width_c2, $height, $gest, 0, 1, 'L', true, '', 0, false, 'T', 'C');
			$this->Ln();		
		}
							
		if($this->objParam->getParametro('desde')!=null){			
			$fecha_ini =$this->objParam->getParametro('desde');
			$this->SetFont('', 'B',6);
			$this->Cell($width1, $height, '', 0, 0, 'L', false, '', 0, false, 'T', 'C');
			$this->Cell($width_c1, $height, 'Del:', 0, 0, 'L', false, '', 0, false, 'T', 'C');
			$this->SetFont('', '',6);
			$this->SetFillColor(192,192,192, true);			
			$this->Cell($width_c2, $height, $fecha_ini, 0, 1, 'L', true, '', 0, false, 'T', 'C');	
			$this->Ln();		
		}
		if($this->objParam->getParametro('hasta')!=null){		
			$fecha_fin = $this->objParam->getParametro('hasta');
			$this->SetFont('', 'B',6);		
			$this->Cell($width1, $height, '', 0, 0, 'L', false, '', 0, false, 'T', 'C');
			$this->Cell($width_c1, $height,'Hasta:', 0, 0, 'L', false, '', 0, false, 'T', 'C');
			$this->SetFont('', '',6);
			$this->SetFillColor(192,192,192, true);			
			$this->Cell($width_c2, $height, $fecha_fin, 0, 1, 'L', true, '', 0, false, 'T', 'C');
			$this->Ln();			
		}
				
		if($this->objParam->getParametro('aux')!=null){		
			$aux = $this->objParam->getParametro('aux');
			$this->SetFont('', 'B',6);					
			$this->Cell($width1, $height, '', 0, 0, 'L', false, '', 0, false, 'T', 'C');
			$this->Cell($width_c1, $height,'Auxiliar:', 0, 0, 'L', false, '', 0, false, 'T', 'C');
			$this->SetFont('', '',6);
			$this->SetFillColor(192,192,192, true);			
			$this->Cell($width_c2, $height, $aux, 0, 1, 'L', true, '', 0, false, 'T', 'C');
			$this->Ln();		
		}
		
		if($this->objParam->getParametro('depto')!=null){		
			$depto= $this->objParam->getParametro('depto');
			$this->SetFont('', 'B',6);					
			$this->Cell($width1, $height, '', 0, 0, 'L', false, '', 0, false, 'T', 'C');
			$this->Cell($width_c1, $height,'Departamento:', 0, 0, 'L', false, '', 0, false, 'T', 'C');
			$this->SetFont('', '',6);
			$this->SetFillColor(192,192,192, true);			
			$this->Cell($width_c2, $height, $depto, 0, 1, 'L', true, '', 0, false, 'T', 'C');
			$this->Ln();		
		}
		
		if($this->objParam->getParametro('config_tipo_cuenta')!=null){		
			$config_tipo_cuenta= $this->objParam->getParametro('config_tipo_cuenta');
			$this->SetFont('', 'B',6);					
			$this->Cell($width1, $height, '', 0, 0, 'L', false, '', 0, false, 'T', 'C');
			$this->Cell($width_c1, $height,'Tipo de Cuenta:', 0, 0, 'L', false, '', 0, false, 'T', 'C');
			$this->SetFont('', '',6);
			$this->SetFillColor(192,192,192, true);			
			$this->Cell($width_c2, $height, $config_tipo_cuenta, 0, 1, 'L', true, '', 0, false, 'T', 'C');
			$this->Ln();		
		}
		
		if($this->objParam->getParametro('config_subtipo_cuenta')!=null){
			$this->SetFont('', 'B',6);		
			$config_subtipo_cuenta= $this->objParam->getParametro('config_subtipo_cuenta');					
			$this->Cell($width1, $height, '', 0, 0, 'L', false, '', 0, false, 'T', 'C');
			$this->Cell($width_c1, $height,'Sub Tipo de Cuenta:', 0, 0, 'L', false, '', 0, false, 'T', 'C');
			$this->SetFont('', '',6);
			$this->SetFillColor(192,192,192, true);			
			$this->Cell($width_c2, $height, $config_subtipo_cuenta, 0, 1, 'L', true, '', 0, false, 'T', 'C');
			$this->Ln();		
		}
		
		if($this->objParam->getParametro('config_subtipo_cuenta')!=null){
			$this->SetFont('', 'B',6);		
			$config_subtipo_cuenta= $this->objParam->getParametro('config_subtipo_cuenta');					
			$this->Cell($width1, $height, '', 0, 0, 'L', false, '', 0, false, 'T', 'C');
			$this->Cell($width_c1, $height,'Sub Tipo de Cuenta:', 0, 0, 'L', false, '', 0, false, 'T', 'C');
			$this->SetFont('', '',6);
			$this->SetFillColor(192,192,192, true);			
			$this->Cell($width_c2, $height, $config_subtipo_cuenta, 0, 1, 'L', true, '', 0, false, 'T', 'C');
			$this->Ln();		
		}
		
		if($this->objParam->getParametro('cuenta')!=null){
			$this->SetFont('', 'B',6);		
			$cuenta= $this->objParam->getParametro('cuenta');					
			$this->Cell($width1, $height, '', 0, 0, 'L', false, '', 0, false, 'T', 'C');
			$this->Cell($width_c1, $height,'Cuenta:', 0, 0, 'L', false, '', 0, false, 'T', 'C');
			$this->SetFont('', '',6);
			$this->SetFillColor(192,192,192, true);			
			$this->Cell($width_c2, $height, $cuenta, 0, 1, 'L', true, '', 0, false, 'T', 'C');
			$this->Ln();		
		}

		if($this->objParam->getParametro('partidas')!=null){		
			$partidas= $this->objParam->getParametro('partidas');
			$this->SetFont('', 'B',6);					
			$this->Cell($width1, $height, '', 0, 0, 'L', false, '', 0, false, 'T', 'C');
			$this->Cell($width_c1, $height,'Partida:', 0, 0, 'L', false, '', 0, false, 'T', 'C');
			$this->SetFont('', '',6);
			$this->SetFillColor(192,192,192, true);			
			$this->Cell($width_c2, $height, $partidas, 0, 1, 'L', true, '', 0, false, 'T', 'C');
			$this->Ln();		
		}

		if($this->objParam->getParametro('tipo_cc')!=null){		
			$tipo_cc = $this->objParam->getParametro('tipo_cc');
			$this->SetFont('', 'B',6);					
			$this->Cell($width1, $height, '', 0, 0, 'L', false, '', 0, false, 'T', 'C');
			$this->Cell($width_c1, $height,'Tipo de Centro de Costo :', 0, 0, 'L', false, '', 0, false, 'T', 'C');
			$this->SetFont('', '',6);
			$this->SetFillColor(192,192,192, true);			
			$this->Cell($width_c2, $height, $tipo_cc, 0, 1, 'L', true, '', 0, false, 'T', 'C');
			$this->Ln();		
		}
						
		if($this->objParam->getParametro('centro_costo')!=null){		
			$centro_costo= $this->objParam->getParametro('centro_costo');
			$this->SetFont('', 'B',6);					
			$this->Cell($width1, $height, '', 0, 0, 'L', false, '', 0, false, 'T', 'C');
			$this->Cell($width_c1, $height,'Centro de Costo:', 0, 0, 'L', false, '', 0, false, 'T', 'C');
			$this->SetFont('', '',6);
			$this->SetFillColor(192,192,192, true);			
			$this->Cell($width_c2, $height, $centro_costo, 0, 1, 'L', true, '', 0, false, 'T', 'C');
			$this->Ln();		
		}	
		
		if($this->objParam->getParametro('orden_trabajo')!=null){		
			$orden_trabajo= $this->objParam->getParametro('orden_trabajo');	
			$this->SetFont('', 'B',6);				
			$this->Cell($width1, $height, '', 0, 0, 'L', false, '', 0, false, 'T', 'C');
			$this->Cell($width_c1, $height,'Orden de Trabajo:', 0, 0, 'L', false, '', 0, false, 'T', 'C');
			$this->SetFont('', '',6);
			$this->SetFillColor(192,192,192, true);			
			$this->Cell($width_c2, $height, $orden_trabajo, 0, 1, 'L', true, '', 0, false, 'T', 'C');
			$this->Ln();		
		}				
		
		if($this->objParam->getParametro('suborden')!=null){		
			$suborden= $this->objParam->getParametro('suborden');
			$this->SetFont('', 'B',6);					
			$this->Cell($width1, $height, '', 0, 0, 'L', false, '', 0, false, 'T', 'C');
			$this->Cell($width_c1, $height,'Sub Orden:', 0, 0, 'L', false, '', 0, false, 'T', 'C');
			$this->SetFont('', '',6);
			$this->SetFillColor(192,192,192, true);			
			$this->Cell($width_c2, $height, $suborden, 0, 1, 'L', true, '', 0, false, 'T', 'C');
			$this->Ln();		
		}	
		
		if($this->objParam->getParametro('nro_tram')!=null){		
			$nro_tram= $this->objParam->getParametro('v');
			$this->SetFont('', 'B',6);					
			$this->Cell($width1, $height, '', 0, 0, 'L', false, '', 0, false, 'T', 'C');
			$this->Cell($width_c1, $height,'Nro de Tramite:', 0, 0, 'L', false, '', 0, false, 'T', 'C');
			$this->SetFont('', '',6);
			$this->SetFillColor(192,192,192, true);			
			$this->Cell($width_c2, $height, $nro_tram, 0, 1, 'L', true, '', 0, false, 'T', 'C');
			$this->Ln();		
		}
        if($this->objParam->getParametro('fecha_reg')!=null){		
			$fecha_reg= $this->objParam->getParametro('v');
			$this->SetFont('', 'B',6);					
			$this->Cell($width1, $height, '', 0, 0, 'L', false, '', 0, false, 'T', 'C');
			$this->Cell($width_c1, $height,'Fecha Registro:', 0, 0, 'L', false, '', 0, false, 'T', 'C');
			$this->SetFont('', '',6);
			$this->SetFillColor(192,192,192, true);			
			$this->Cell($width_c2, $height, $fecha_reg, 0, 1, 'L', true, '', 0, false, 'T', 'C');
			$this->Ln();		
		}
        
        if($this->objParam->getParametro('glosa1')!=null){		
			$glosa1= $this->objParam->getParametro('glosa1');
			$this->SetFont('', 'B',6);					
			$this->Cell($width1, $height, '', 0, 0, 'L', false, '', 0, false, 'T', 'C');
			$this->Cell($width_c1, $height,'Glosa:', 0, 0, 'L', false, '', 0, false, 'T', 'C');
			$this->SetFont('', '',6);
			$this->SetFillColor(192,192,192, true);			
			$this->Cell($width_c2, $height, $glosa1, 0, 1, 'L', true, '', 0, false, 'T', 'C');
			$this->Ln();		
		}
        if($this->objParam->getParametro('importe_debe')!=null){		
			$importe_debe= $this->objParam->getParametro('glosa1');
			$this->SetFont('', 'B',6);					
			$this->Cell($width1, $height, '', 0, 0, 'L', false, '', 0, false, 'T', 'C');
			$this->Cell($width_c1, $height,'Debe:', 0, 0, 'L', false, '', 0, false, 'T', 'C');
			$this->SetFont('', '',6);
			$this->SetFillColor(192,192,192, true);			
			$this->Cell($width_c2, $height, $importe_debe, 0, 1, 'L', true, '', 0, false, 'T', 'C');
			$this->Ln();		
		}
        if($this->objParam->getParametro('importe_haber')!=null){		
			$importe_haber= $this->objParam->getParametro('glosa1');
			$this->SetFont('', 'B',6);					
			$this->Cell($width1, $height, '', 0, 0, 'L', false, '', 0, false, 'T', 'C');
			$this->Cell($width_c1, $height,'Haber:', 0, 0, 'L', false, '', 0, false, 'T', 'C');
			$this->SetFont('', '',6);
			$this->SetFillColor(192,192,192, true);			
			$this->Cell($width_c2, $height, $importe_haber, 0, 1, 'L', true, '', 0, false, 'T', 'C');
			$this->Ln();		
		}
        if($this->objParam->getParametro('nro_cuenta')!=null){		
			$nro_cuenta= $this->objParam->getParametro('nro_cuenta');
			$this->SetFont('', 'B',6);					
			$this->Cell($width1, $height, '', 0, 0, 'L', false, '', 0, false, 'T', 'C');
			$this->Cell($width_c1, $height,'Nro Cuenta:', 0, 0, 'L', false, '', 0, false, 'T', 'C');
			$this->SetFont('', '',6);
			$this->SetFillColor(192,192,192, true);			
			$this->Cell($width_c2, $height, $nro_cuenta, 0, 1, 'L', true, '', 0, false, 'T', 'C');
			$this->Ln();		
		}
		
		$this->Ln(4);
		$this->SetFont('','B',6);
		$this->generarCabecera();
	}	
}
?>