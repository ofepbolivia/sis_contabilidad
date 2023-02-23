<?php 
    date_default_timezone_set('America/La_Paz');
    //fRnk: se modificó el label N°, por N° Cbte.
?>
<font size="8"><table width="100%" style="width: 100%; text-align: center;" cellspacing="0" cellpadding="1" border="1">	
<tbody>
	<tr>
		<td style="width: 23%; color: #444444;" rowspan="5">
			&nbsp;<br><img  style="width: 150px;" src="./../../../lib/<?php echo $_SESSION['_DIR_LOGO'];?>" alt="Logo">
		</td>		
		<td style="width: 54%; color: #444444;" rowspan="5">
            <h1><?php  echo $this->cabecera[0]['desc_clase_comprobante']; ?> </h1>
            <h4>(EXPRESADO EN <?php  echo $this->cabecera[0]['moneda']; ?>)</h4>
        </td>
		<td style="width: 23%; color: #444444; text-align: left;">&nbsp;&nbsp;<b>Gestión:</b> <?php  echo date('Y'); ?> </td>
	</tr>
    <tr>
        <td style="width: 23%; color: #444444; text-align: left;">&nbsp;&nbsp;<b>Fecha:</b> <?php  echo date('d/m/y h:i:s A'); ?></td>
	</tr>
    <tr>
        <td style="width: 23%; color: #444444; text-align: left;">&nbsp;&nbsp;<b>Depto.:</b> <?php  echo $this->cabecera[0]['codigo_depto']; ?></td>
	</tr>
	<tr>
		<td style="width: 23%; color: #444444; text-align: left;">&nbsp;&nbsp;<b>N° Cbte.:</b> <?php  echo $this->cabecera[0]['nro_cbte']; ?> </td>
	</tr>
	<tr>
		<td style="width: 23%; color: #444444; text-align: left;">&nbsp;&nbsp;<b>Usuario:</b> <?php  echo $this->cabecera[0]['usuario']; ?> </td>
	</tr>
</tbody>
</table></font>
