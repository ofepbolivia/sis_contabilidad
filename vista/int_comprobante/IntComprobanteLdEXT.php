<?php
/**
 * @package pXP
 * @file gen-IntComprobanteLdEXT.php
 * @author  Maylee Perez Pastor
 * @date 2-04-2020 10:22:05
 * @description Archivo con la interfaz de usuario que permite
 *dar el visto a solicitudes de compra
 *
 */
header("content-type: text/javascript; charset=UTF-8");
?>
<script>
    Phx.vista.IntComprobanteLdEXT = {
        bedit: false,
        bnew: false,
        bsave: false,
        bdel: true,
        require: '../../../sis_contabilidad/vista/int_comprobante/IntComprobanteIniEXT.php',
        requireclase: 'Phx.vista.IntComprobanteIniEXT',
        title: 'Libro Diario',
        nombreVista: 'IntComprobanteLd',
        ActSave: '../../sis_contabilidad/control/IntComprobante/modificarFechasCostosCbte',
        constructor: function (config) {
            Phx.vista.IntComprobanteLdEXT.superclass.constructor.call(this, config);

            this.addBotonesVolcar();
            this.addBotonesClonar();


            this.addButton('btnWizard', {
                text: 'Plantilla',
                iconCls: 'bgear',
                disabled: true,
                handler: this.loadWizard,
                scope: this,
                tooltip: '<b>Plantilla de Comprobantes</b><br/>Seleccione una plantilla y genere comprobantes preconfigurados'
            });

            this.addButton('bregcbte', {
                text: 'Regularizar Cbte',
                iconCls: 'bgear',
                disabled: true,
                handler: this.regcbte,
                scope: this,
                tooltip: '<b>Regularizar Comprobantes</b><br/>Seleccione un comprobante y valida comprobante a la central'
            });


            this.init();
        },

        volcarCbte: function (sw_validar) {

            if (confirm("Esta seguro de volcar el cbte,  este proceso no puede revertirse (Si tiene presupuesto será revertido)!")) {
                if (confirm("¿Esta realmente seguro?")) {
                    var rec = this.sm.getSelected().data;
                    Phx.CP.loadingShow();
                    Ext.Ajax.request({
                        url: '../../sis_contabilidad/control/IntComprobante/volcarCbte',
                        params: {
                            id_int_comprobante: rec.id_int_comprobante,
                            sw_validar: (sw_validar == 'si') ? 'si' : 'no'
                        },
                        success: function (resp) {
                            Phx.CP.loadingHide();
                            var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
                            if (reg.ROOT.error) {
                                Ext.Msg.alert('Error', 'Al volcar el cbte: ' + reg.ROOT.error)
                            } else {
                                this.reload()
                            }
                        },
                        failure: this.conexionFailure,
                        timeout: this.timeout,
                        scope: this
                    });
                }
            }

        },
        //may
        volcarCbteContable: function (sw_validar) {

            if (confirm("Esta seguro de volcar el cbte,  este proceso no puede revertirse (Si tiene presupuesto será revertido)!")) {
                if (confirm("¿Esta realmente seguro?")) {
                    var rec = this.sm.getSelected().data;
                    Phx.CP.loadingShow();
                    Ext.Ajax.request({
                        url: '../../sis_contabilidad/control/IntComprobante/volcarCbteContable',
                        params: {
                            id_int_comprobante: rec.id_int_comprobante,
                            sw_validar: (sw_validar == 'si') ? 'si' : 'no'
                        },
                        success: function (resp) {
                            Phx.CP.loadingHide();
                            var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
                            if (reg.ROOT.error) {
                                Ext.Msg.alert('Error', 'Al volcar el cbte: ' + reg.ROOT.error)
                            } else {
                                this.reload()
                            }
                        },
                        failure: this.conexionFailure,
                        timeout: this.timeout,
                        scope: this
                    });
                }
            }

        },

        clonarCbteSt: function () {
            this.clonarCbte('si');
        },
        clonarCbte: function (sw_tramite) {

            if (confirm("Esta seguro de clonar  el cbte")) {
                if (confirm("¿Esta realmente seguro?")) {
                    var rec = this.sm.getSelected().data;
                    Phx.CP.loadingShow();
                    Ext.Ajax.request({
                        url: '../../sis_contabilidad/control/IntComprobante/clonarCbte',
                        params: {
                            id_int_comprobante: rec.id_int_comprobante,
                            sw_tramite: sw_tramite == 'si' ? 'si' : 'no'
                        },
                        success: function (resp) {
                            Phx.CP.loadingHide();
                            var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
                            if (reg.ROOT.error) {
                                Ext.Msg.alert('Error', 'Al clonar el cbte: ' + reg.ROOT.error)
                            } else {
                                this.reload();
                            }
                        },
                        failure: this.conexionFailure,
                        timeout: this.timeout,
                        scope: this
                    });
                }
            }

        },

        south: {
            url: '../../../sis_contabilidad/vista/int_transaccion/IntTransaccionLd.php',
            title: 'Transacciones',
            height: '50%',	//altura de la ventana hijo
            cls: 'IntTransaccionLd'
        },
        preparaMenu: function (n) {
            var tb = Phx.vista.IntComprobanteLdEXT.superclass.preparaMenu.call(this);
            var rec = this.sm.getSelected();
            this.getBoton('btnImprimir').enable();
            this.getBoton('btnRelDev').enable();
            this.getBoton('btnDocCmpVnt').enable();
            this.getBoton('chkpresupuesto').enable();
            this.getBoton('btnVolcar').enable();
            this.getBoton('btnClonar').enable();
            this.getBoton('chkdep').enable();
            this.getBoton('btnChequeoDocumentosWf').enable();
            this.getBoton('diagrama_gantt').enable();
            this.getBoton('btnObs').enable();
            this.getBoton('btnWizard').enable()

            this.getBoton('bregcbte').enable()

            // 02-09-2018 se comenta para que muestre la opcion de registro de documentos
            /*if(rec.data.momento =='presupuestario'){
                this.getBoton('btnDocCmpVnt').enable();
            }else{
                this.getBoton('btnDocCmpVnt').disable();
            }*/


            return tb;
        },
        liberaMenu: function () {
            var tb = Phx.vista.IntComprobanteLdEXT.superclass.liberaMenu.call(this);
            this.getBoton('btnImprimir').disable();
            this.getBoton('btnRelDev').disable();
            //this.getBoton('btnDocCmpVnt').disable();
            this.getBoton('chkpresupuesto').disable();
            this.getBoton('btnVolcar').disable();
            this.getBoton('btnClonar').disable();
            this.getBoton('chkdep').disable();
            this.getBoton('btnChequeoDocumentosWf').disable();
            this.getBoton('diagrama_gantt').disable();
            this.getBoton('btnObs').disable();
            this.getBoton('btnWizard').disable();

            this.getBoton('bregcbte').disable();


        },

        addBotonesClonar: function () {
            this.menuClonar = new Ext.Toolbar.SplitButton({
                id: 'b-btnClonar-' + this.idContenedor,
                text: 'Clonar',
                disabled: true,
                grupo: [0, 1, 2, 3],
                iconCls: 'balert',
                scope: this,
                menu: {
                    items: [{
                        id: 'b-sint-' + this.idContenedor,
                        text: 'Clonar sin  Trámite',
                        tooltip: '<b>Clonar el cbte sin mantener el nro de trámite</b>',
                        handler: function () {
                            this.clonarCbteSt()
                        },
                        scope: this
                    }, {
                        id: 'b-cont-' + this.idContenedor,
                        text: 'Clonar con Trámite',
                        tooltip: '<b>Clonar el cbte manteniendo el nro de trámite</b>',
                        handler: function () {
                            this.clonarCbte()
                        },
                        scope: this
                    }
                    ]
                }
            });
            this.tbar.add(this.menuClonar);
        },

        addBotonesVolcar: function () {
            this.menuClonar = new Ext.Toolbar.SplitButton({
                id: 'b-btnVolcar-' + this.idContenedor,
                text: 'Revertir',
                disabled: true,
                grupo: [0, 1, 2, 3],
                iconCls: 'balert',
                scope: this,
                menu: {
                    items: [{
                        id: 'b-volb-' + this.idContenedor,
                        text: 'Reversión Parcial (Borrador)',
                        tooltip: '<b>Al volcar en borrador tiene la opción de revertir parcialmente</b>',
                        handler: function () {
                            this.volcarCbte('no')
                        },
                        scope: this
                    }, {
                        id: 'b-vol-' + this.idContenedor,
                        //text: 'Reversión Total (Validado)',
                        text: 'Reversión Total Presupuestario (Validado)',
                        tooltip: '<b>Al volcar y validar se revierte el 100%</b>',
                        handler: function () {
                            this.volcarCbte('si')
                        },
                        scope: this
                    }, {
                        //23/01/2020 (may) nueva reversion para cbtes contables y no ejecuten presupuesto
                        id: 'b-volc-' + this.idContenedor,
                        text: 'Reversión Total Contable (Validado)',
                        tooltip: '<b>Volcar y validar el cbte Contable </b>',
                        handler: function () {
                            this.volcarCbteContable('si')
                        },
                        scope: this
                    }
                    ]
                }
            });
            this.tbar.add(this.menuClonar);
        },


        loadWizard: function () {

            var rec = this.sm.getSelected();
            Phx.CP.loadWindows('../../../sis_contabilidad/vista/int_comprobante/WizardCbteDiario.php', 'Generar comprobante desde plantilla ...', {
                width: '40%',
                height: 300
            }, {
                'id_int_comprobante': rec.data.id_int_comprobante,
                'id_depto': this.cmbDepto.getValue()
            }, this.idContenedor, 'WizardCbteDiario')
        },

    };
</script>
