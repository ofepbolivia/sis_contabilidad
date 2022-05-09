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
Phx.vista.IntTransaccionLd = {
    bedit:false,
    bnew:false,
    bsave:false,
    bdel:false,
	require: '../../../sis_contabilidad/vista/int_transaccion/IntTransaccion.php',
	requireclase: 'Phx.vista.IntTransaccion',
	title: 'Libro Diario',
	nombreVista: 'IntTransaccionLd',
	
	constructor: function(config) {
	    Phx.vista.IntTransaccionLd.superclass.constructor.call(this,config);

        this.addButtonIndex(4,'cbte_info',{
            text:'Información CBTE.',
            grupo:[0,1,2],
            iconCls: 'brenew',
            disabled: true,
            handler: this.onCargarInformacionAdicional,
            tooltip: '<b>Datos CBTE:</b><br/>Información complementaria de un comprobante.'
        });
    
    },

    /****************** {developer:franklin.espinoza, date: 16/03/2021, descripcion:Información Complementaria Comprobante validado.} ******************/
    onCargarInformacionAdicional:  function (){
        var record = this.getSelectedData();
        this.formInformacionCBTE();

        this.formCBTE.getForm().findField('id_orden_trabajo').setValue(record.codigo_ot);
        this.formCBTE.getForm().findField('id_orden_trabajo').setRawValue(record.id_orden_trabajo);

        this.windowCBTE.show();
    },
    formInformacionCBTE: function () {

        this.formCBTE = new Ext.form.FormPanel({
            id: this.idContenedor + '_CBTEINFO',
            items: [

                new Ext.form.ComboRec({
                    name: 'id_orden_trabajo',
                    msgTarget: 'title',
                    sysorigen: 'sis_contabilidad',
                    fieldLabel: 'Orden Trabajo',
                    origen: 'OT',
                    allowBlank: false,
                    width: '80%',
                    msgTarget : 'side',
                    resizable:true,
                    listWidth:'259',
                })
            ],
            autoScroll: false,
            //height: this.fheight,
            autoDestroy: true,
            autoScroll: true
        });


        // Definicion de la ventana que contiene al formulario
        this.windowCBTE = new Ext.Window({
            // id:this.idContenedor+'_W',
            title: 'Información CBTE',
            modal: true,
            width: 400,
            height: 200,
            bodyStyle: 'padding:5px;',
            layout: 'fit',
            hidden: true,
            autoScroll: false,
            maximizable: true,
            buttons: [{
                text: 'Guardar',
                arrowAlign: 'bottom',
                handler: this.onSubmitCBTE,
                argument: {
                    'news': false
                },
                scope: this

            },
                {
                    text: 'Declinar',
                    handler: this.onDeclinarCBTE,
                    scope: this
                }],
            items: this.formCBTE,
            // autoShow:true,
            autoDestroy: true,
            closeAction: 'hide'
        });
    },

    onSubmitCBTE: function () {
        var record = this.getSelectedData();
        Phx.CP.loadingShow();
        Ext.Ajax.request({
            url: '../../sis_contabilidad/control/IntTransaccion/guardarInformacionCBTE',
            success: this.successCBTE,
            failure: this.failureCBTE,
            params: {
                'id_int_transaccion' : record.id_int_transaccion,
                'id_orden_trabajo' : this.formCBTE.getForm().findField('id_orden_trabajo').getValue()
            },
            timeout: this.timeout,
            scope: this
        });

    },

    successCBTE: function (resp) {
        this.windowCBTE.hide();
        Phx.vista.IntTransaccionLd.superclass.successSave.call(this, resp);

    },

    failureCBTE: function (resp) {
        Phx.CP.loadingHide();
        Phx.vista.IntTransaccionLd.superclass.conexionFailure.call(this, resp);

    },

    onDeclinarCBTE: function () {
        this.windowCBTE.hide();
    },
    /****************** {developer:franklin.espinoza, date: 16/03/2021, descripcion:Información Complementaria Comprobante validado.} ******************/

    preparaMenu:function(){
		var rec = this.sm.getSelected();
		var tb = this.tbar;
		Phx.vista.IntTransaccionLd.superclass.preparaMenu.call(this);
		if(this.getBoton('btnBanco')){
			this.getBoton('btnBanco').disable();
		}		
	},
	
	liberaMenu: function() {
		var tb = Phx.vista.IntTransaccionLd.superclass.liberaMenu.call(this);
		if(this.getBoton('btnBanco')){
			this.getBoton('btnBanco').setDisabled(true);
		}		
	}
	
};
</script>
