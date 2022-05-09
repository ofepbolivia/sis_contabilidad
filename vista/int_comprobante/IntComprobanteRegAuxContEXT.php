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
    Phx.vista.IntComprobanteRegAuxContEXT = {
        bsave:true,

        require: '../../../sis_contabilidad/vista/int_comprobante/IntComprobanteIniEXT.php',
        requireclase: 'Phx.vista.IntComprobanteIniEXT',
        title: 'Libro Diario',
        nombreVista: 'IntComprobanteRegAuxContExt',


        constructor: function(config) {
            var me = this;
            me.bMedios = [];
            me.addButtonCustom(config.idContenedor, 'sig_estado', { text: 'Aprobar', iconCls: 'badelante', disabled: true, handler: this.sigEstado, tooltip: '<b>Pasar al Siguiente Estado</b>' });

            Phx.vista.IntComprobanteRegAuxContEXT.superclass.constructor.call(this,config);
            this.addButton('btnWizard', {
                text : 'Plantilla',
                iconCls : 'bgear',
                disabled : false,
                handler : this.loadWizard,
                tooltip : '<b>Plantilla de Comprobantes</b><br/>Seleccione una plantilla y genere comprobantes preconfigurados'
            });

            this.addButton('btnIgualarCbte', {
                text : 'Igualar',
                iconCls : 'bengineadd',
                disabled : true,
                handler : this.igualarCbte,
                tooltip : '<b>Igualar comprobante</b><br/>Si existe diferencia por redondeo o por tipo de cambio inserta una transacción para igualar'
            });

            this.addButton('btnSwEditble', {
                text : 'Editable',
                iconCls : 'balert',
                disabled : true,
                handler : this.swEditable,
                tooltip : '<b>Hacer editable</b><br/>Si la edición esta deshabilitada toma un backup y la habilita'
            });

            this.init();

        },


        onButtonEdit:function(){
            this.swButton = 'EDIT';
            var rec = this.sm.getSelected().data;

            this.cmpFecha.enable();

            Phx.vista.IntComprobanteRegAuxContEXT.superclass.onButtonEdit.call(this);
            this.Cmp.id_clase_comprobante.store.baseParams.tipo='diario';
            this.Cmp.id_moneda.setReadOnly(true);
            if(rec.localidad == 'internacional'){
                this.Cmp.fecha.setReadOnly(true);
            }
            //si el tic vari en lastransacciones ..
            if(rec.sw_tipo_cambio == 'si'){
                this.ocultarComponente(this.Cmp.tipo_cambio);
                this.ocultarComponente(this.Cmp.tipo_cambio_2);
                this.ocultarComponente(this.Cmp.tipo_cambio_3);
            }
            else{
                this.mostrarComponente(this.Cmp.tipo_cambio);
                this.mostrarComponente(this.Cmp.tipo_cambio_2);
                this.mostrarComponente(this.Cmp.tipo_cambio_3);

                //RAC 1/12/2016 valor origal en no
                //cambio para que al editar se peuda cambiar la forma de pago y se recalcule el tipo de cambio ...
                // hay que ver que implicaciones va tener esto ....
                // si despues queire editar el combo de forma de pago estan en si va recalcular los tipo o permitir editar
                // si selecciona convenido
                this.getConfigCambiaria('no');

            }

        },

        onButtonNew:function(){
            this.swButton = 'NEW';

            this.Cmp.id_clase_comprobante.store.baseParams.tipo = 'diario';

            Phx.vista.IntComprobanteRegAuxContEXT.superclass.onButtonNew.call(this);
            this.Cmp.id_moneda.setReadOnly(false);
            //this.Cmp.fecha.setReadOnly(false);
            this.cmpFecha.enable();
            //this.cmpFecha.disable();
            this.cmpFecha.setValue(new Date());
            this.cmpFecha.fireEvent('change');
            this.mostrarComponente(this.Cmp.tipo_cambio);
            this.mostrarComponente(this.Cmp.tipo_cambio_2);
            this.mostrarComponente(this.Cmp.tipo_cambio_3);
        },

        igualarCbte: function() {

            var rec = this.sm.getSelected().data;
            Phx.CP.loadingShow();
            Ext.Ajax.request({
                url : '../../sis_contabilidad/control/IntComprobante/igualarComprobante',
                params : {
                    id_int_comprobante : rec.id_int_comprobante
                },
                success : function(resp) {
                    Phx.CP.loadingHide();
                    var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
                    if (reg.ROOT.error) {
                        Ext.Msg.alert('Error', 'No se pudo igualar el cbte: ' + reg.ROOT.error)
                    } else {
                        this.reload();
                    }
                },
                failure : this.conexionFailure,
                timeout : this.timeout,
                scope : this
            });


        },


        swEditable: function() {

            var rec = this.sm.getSelected().data;
            Phx.CP.loadingShow();
            Ext.Ajax.request({
                url : '../../sis_contabilidad/control/IntComprobante/swEditable',
                params : {
                    id_int_comprobante : rec.id_int_comprobante
                },
                success : function(resp) {
                    Phx.CP.loadingHide();
                    var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
                    if (reg.ROOT.error) {
                        Ext.Msg.alert('Error', 'Al  cambiar el modo de edición: ' + reg.ROOT.error)
                    } else {
                        this.reload();
                    }
                },
                failure : this.conexionFailure,
                timeout : this.timeout,
                scope : this
            });
        },
        preparaMenu : function(n) {
            var tb = Phx.vista.IntComprobanteRegAuxContEXT.superclass.preparaMenu.call(this);
            var rec = this.sm.getSelected();
            this.getBoton('sig_estado').enable();
            if(rec.data.tipo_reg == 'summary'){
                this.getBoton('btnSwEditble').disable();
                //this.getBoton('sig_estado').disable();
                this.getBoton('btnImprimir').disable();
                this.getBoton('btnRelDev').disable();
                this.getBoton('btnIgualarCbte').disable();
                this.getBoton('btnDocCmpVnt').disable();
            }
            else{
                if(rec.data.sw_editable == 'no'){
                    this.getBoton('btnSwEditble').setDisabled(false);
                }
                else{
                    this.getBoton('btnSwEditble').setDisabled(true);
                }
                //this.getBoton('sig_estado').enable();
                this.getBoton('btnImprimir').enable();
                this.getBoton('btnRelDev').enable();
                this.getBoton('btnIgualarCbte').enable();
                this.getBoton('btnDocCmpVnt').enable();
                this.getBoton('chkpresupuesto').enable();
                this.getBoton('btnChequeoDocumentosWf').enable();
                this.getBoton('diagrama_gantt').enable();
                this.getBoton('btnObs').enable();
            }
            if(rec.data.momento =='presupuestario'){
                this.getBoton('btnDocCmpVnt').enable();
            }else{
                this.getBoton('btnDocCmpVnt').disable();
            }

            //para bloquear estadosvbconta y vbfin solo para el visto bueno
            if (rec.data.estado_reg == 'vbconta' || rec.data.estado_reg =='vbfin') {
                //this.getBoton('sig_estado').disable();

            }
            //



            return tb;
        },
        liberaMenu : function() {
            var tb = Phx.vista.IntComprobanteRegAuxContEXT.superclass.liberaMenu.call(this);

            this.getBoton('sig_estado').disable();
            this.getBoton('btnImprimir').disable();
            this.getBoton('btnRelDev').disable();
            this.getBoton('btnIgualarCbte').disable();
            this.getBoton('btnDocCmpVnt').disable();
            this.getBoton('chkpresupuesto').disable();
            this.getBoton('btnChequeoDocumentosWf').disable();
            this.getBoton('diagrama_gantt').disable();
            this.getBoton('btnObs').disable()



        },
        /*
        capturaFiltros : function(combo, record, index) {
            this.desbloquearOrdenamientoGrid();
            this.store.baseParams.id_deptos = this.cmbDepto.getValue();
            this.store.baseParams.nombreVista = this.nombreVista;
            this.load();
        },*/

        getTipoCambio : function() {
            //Verifica que la fecha y la moneda hayan sido elegidos
            if (this.Cmp.fecha.getValue() && this.Cmp.id_moneda.getValue()) {
                Ext.Ajax.request({
                    url : '../../sis_parametros/control/TipoCambio/obtenerTipoCambio',
                    params : {
                        fecha : this.Cmp.fecha.getValue(),
                        id_moneda : this.Cmp.id_moneda.getValue(),
                        tipo : 'O'
                    },
                    success : function(resp) {
                        Phx.CP.loadingHide();
                        var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
                        if (reg.ROOT.error) {
                            Ext.Msg.alert('Error', 'Validación no realizada: ' + reg.ROOT.error)
                        } else {
                            this.Cmp.tipo_cambio.setValue(reg.ROOT.datos.tipo_cambio);
                        }
                    },
                    failure : this.conexionFailure,
                    timeout : this.timeout,
                    scope : this
                });
            }

        },
        getConfigCambiaria : function(sw_valores) {

            var localidad = 'nacional';

            if (this.swButton == 'EDIT') {
                var rec = this.sm.getSelected();
                localidad = rec.data.localidad;

            }

            //Verifica que la fecha y la moneda hayan sido elegidos
            if (this.Cmp.fecha.getValue() && this.Cmp.id_moneda.getValue() && this.Cmp.forma_cambio.getValue()) {
                Phx.CP.loadingShow();
                var forma_cambio = this.Cmp.forma_cambio.getValue();
                if(forma_cambio=='convenido'){
                    this.Cmp.tipo_cambio.setReadOnly(false);
                    this.Cmp.tipo_cambio_2.setReadOnly(false);
                }
                else{
                    this.Cmp.tipo_cambio.setReadOnly(true);
                    this.Cmp.tipo_cambio_2.setReadOnly(true);
                }

                this.Cmp.tipo_cambio_3.setReadOnly(true);

                Ext.Ajax.request({
                    url:'../../sis_contabilidad/control/ConfigCambiaria/getConfigCambiaria',
                    params:{
                        fecha: this.Cmp.fecha.getValue(),
                        id_moneda: this.Cmp.id_moneda.getValue(),
                        localidad: localidad,
                        sw_valores: sw_valores,
                        forma_cambio: forma_cambio
                    }, success: function(resp) {
                        Phx.CP.loadingHide();
                        var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
                        if (reg.ROOT.error) {
                            this.Cmp.tipo_cambio.reset();
                            this.Cmp.tipo_cambio_2.reset();
                            this.Cmp.tipo_cambio_3.reset();
                            Ext.Msg.alert('Error', 'Validación no realizada: ' + reg.ROOT.error)
                        } else {

                            //cambia labels

                            this.Cmp.tipo_cambio.label.update(reg.ROOT.datos.v_tc1 +' (tc)');
                            this.Cmp.tipo_cambio_2.label.update(reg.ROOT.datos.v_tc2 +' (tc)');
                            this.Cmp.tipo_cambio_3.label.update(reg.ROOT.datos.v_tc3 +' (tc)');
                            if (sw_valores == 'si'){
                                //poner valores por defecto
                                this.Cmp.tipo_cambio.setValue(reg.ROOT.datos.v_valor_tc1);
                                this.Cmp.tipo_cambio_2.setValue(reg.ROOT.datos.v_valor_tc2);
                                this.Cmp.tipo_cambio_3.setValue(reg.ROOT.datos.v_valor_tc3);
                            }


                            this.Cmp.id_config_cambiaria.setValue(reg.ROOT.datos.id_config_cambiaria);
                        }


                    }, failure: function(a,b,c,d){
                        this.Cmp.tipo_cambio.reset();
                        this.Cmp.tipo_cambio_2.reset();
                        this.Cmp.tipo_cambio_3.reset();
                        this.conexionFailure(a,b,c,d)
                    },
                    timeout: this.timeout,
                    scope:this
                });
            }

        },
        validarCbte : function() {
            Ext.Msg.confirm('Confirmación', '¿Está seguro de Validar el Comprobante?', function(btn, x, c) {
                if (btn == 'yes') {
                    var rec = this.sm.getSelected();
                    Phx.CP.loadingShow();
                    Ext.Ajax.request({
                        url : '../../sis_contabilidad/control/IntComprobante/validarIntComprobante',
                        params : {
                            id_int_comprobante : rec.data.id_int_comprobante,
                            igualar : 'no'
                        },
                        success : function(resp) {
                            Phx.CP.loadingHide();
                            var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
                            if (reg.ROOT.error) {
                                Ext.Msg.alert('Error', 'Validación no realizada: ' + reg.ROOT.error)
                            } else {
                                this.reload();
                                Ext.Msg.alert('Mensaje', 'Proceso ejecutado con éxito')
                            }
                        },
                        failure : this.conexionFailure,
                        timeout : this.timeout,
                        scope : this
                    });
                }
            }, this);
        },
        loadWizard : function() {
            var rec = this.sm.getSelected();
            Phx.CP.loadWindows('../../../sis_contabilidad/vista/int_comprobante/WizardCbte.php', 'Generar comprobante desde plantilla ...', {
                width : '40%',
                height : 300
            }, rec, this.idContenedor, 'WizardCbte')
        },
        south : {
            url : '../../../sis_contabilidad/vista/int_transaccion/IntTransaccionAux.php',
            title : 'Transacciones',
            height : '50%', //altura de la ventana hijo
            cls : 'IntTransaccionAux'
        },


    };
</script>