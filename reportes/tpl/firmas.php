<!--fRnk: se añadió la firmas-->
<font size="9"><table width="100%" cellspacing="0" cellpadding="0" border="1">
	<tbody>	
	<tr>		
		<td width="34%" class="td_label"><span><b style="font-size: 10px">&nbsp;&nbsp;&nbsp;&nbsp;Firma 1:</b></span></td>
		<td width="33%" class="td_label"><span><b style="font-size: 10px">&nbsp;&nbsp;&nbsp;&nbsp;Firma 2:</b></span></td>
		<td width="33%" class="td_label"><span><b style="font-size: 10px">&nbsp;&nbsp;&nbsp;&nbsp;Firma 3:</b></span></td>
	</tr>
	<tr>
		<td width="34%" class="td_label"><br><br><br><br></td>
		<td width="33%" class="td_label"><br><br><br><br></td>
		<td width="33%" class="td_label"><br><br><br><br></td>
		
	</tr>
	<tr>
        <?php
        //fRnk: fechas en las firmas
        $fecha=empty($this->cabecera[0]['fecha_reg'])?'':date("d/m/Y H:i:s", strtotime($this->cabecera[0]['fecha_reg']));
        ?>
		<td width="34%" class="td_label"><span>&nbsp;&nbsp;<font size="7"><?php  echo $this->cabecera[0]['desc_firma1'].'<br>&nbsp;&nbsp;&nbsp;'.$fecha; ?></font></span></td>
        <?php
        $fecha=empty($this->cabecera[0]['fec_validado'])?'':date("d/m/Y H:i:s", strtotime($this->cabecera[0]['fec_validado']));
        ?>
        <td width="33%" class="td_label"><span>&nbsp;&nbsp;<font size="7"><?php  echo $this->cabecera[0]['desc_firma2'].'<br>&nbsp;&nbsp;&nbsp;'.$fecha; ?></font></span></td>
        <?php
        $fecha=empty($this->cabecera[0]['fec_aprobado'])?'':date("d/m/Y H:i:s", strtotime($this->cabecera[0]['fec_aprobado']));
        ?>
        <td width="33%" class="td_label"><span>&nbsp;&nbsp;<font size="7"><?php  echo $this->cabecera[0]['desc_firma3'].'<br>&nbsp;&nbsp;&nbsp;'.$fecha; ?></font></span></td>
		
	</tr>
</tbody></table>
</font>