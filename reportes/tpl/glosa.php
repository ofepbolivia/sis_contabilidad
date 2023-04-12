<font size="8">
<table width="100%" cellpadding="5px"  rules="cols" border="1">
<tbody>	
		<tr>
            <td width="60%" style="text-align: justify"><b>Glosa:</b>&nbsp;&nbsp;&nbsp;&nbsp; <?php  echo trim($this->cabecera[0]['glosa1']).'<BR/>'.trim($this->cabecera[0]['glosa2']); ?></td>
            <td width="40%" style="text-align: left;">
                <b>Nro Trámite:</b>&nbsp;&nbsp;&nbsp;&nbsp;  <?php  echo $this->cabecera[0]['nro_tramite']; ?><br>
                <!--fRnk: añadido la fecha en comprobante de diario-->
                <b>Fecha:</b>&nbsp;&nbsp;&nbsp;&nbsp;  <?php  echo date("d/m/Y", strtotime($this->cabecera[0]['fecha'])); ?>
                <?php  
                if($this->cabecera[0]['c31'] !=''){ 
                ?>
                    <br/>&nbsp;&nbsp;<b>Cbte Rel.:</b>&nbsp;&nbsp;&nbsp;&nbsp;  <?php  echo $this->cabecera[0]['c31']; ?>
                <?php
                }
                ?>
                    <br/>&nbsp;&nbsp;<b>Dctos:</b>   <?php echo $this->cabecera[0]['documentos']; ?>
                <?php
                if ($this->cabecera[0]['id_moneda'] != $this->cabecera[0]['id_moneda_base']){
                ?>
                <br/>&nbsp;&nbsp;<b>T/C:</b> &nbsp;&nbsp;&nbsp;&nbsp;<?php if($this->cabecera[0]['localidad']=='internacional'){echo $this->cabecera[0]['tipo_cambio'];}else{if($this->cabecera[0]['sw_tipo_cambio']=='no'){echo $this->cabecera[0]['tipo_cambio'];}else{echo 'Por detalle';}}  ?>
                <?php
                } 
                ?>
                <br/>&nbsp;&nbsp;<b>Periodo de Costo:</b>   <?php echo $this->cabecera[0]['fecha_costo_ini'] .'  - '. $this->cabecera[0]['fecha_costo_fin']; ?> 
			</td>
		</tr>
</tbody>
</table></font>