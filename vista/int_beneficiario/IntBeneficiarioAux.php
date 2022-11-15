<?php
/**
*@package pXP
*@file gen-SistemaDist.php
*@author  (fprudencio)
*@date 20-09-2011 10:22:05
*@description Archivo con la interfaz de usuario que permite 
*dar el visto a solicitudes de compra
*
*/
header("content-type: text/javascript; charset=UTF-8");
?>
<script>
Phx.vista.IntBeneficiarioAux = {
    require: '../../../sis_contabilidad/vista/int_beneficiario/IntBeneficiario.php',
	requireclase: 'Phx.vista.IntBeneficiario',
	title: 'Beneficiario',
	nombreVista: 'IntBeneficiarioAux',
	
	constructor: function(config) {
	    Phx.vista.IntBeneficiarioAux.superclass.constructor.call(this,config);
	    this.iniciarEventos();
    
    },
    preparaMenu:function(){
		var rec = this.sm.getSelected();
		var tb = this.tbar;
		Phx.vista.IntBeneficiarioAux.superclass.preparaMenu.call(this);
	},
	
	liberaMenu: function() {
		var tb = Phx.vista.IntBeneficiarioAux.superclass.liberaMenu.call(this);
		
	},
	iniciarEventos: function(){
		
		 this.Cmp.id_funcionario_beneficiario.on('select',function(cmp,rec,ind){
            console.log('llego',rec.data.banco_beneficiario);
            this.getComponente('banco').setValue(rec.data.banco_beneficiario);
            this.getComponente('nro_cuenta_bancaria_sigma').setValue(rec.data.nro_cuenta);
		 	  
		 	  
		 }, this);

	},

	
	
};
</script>
