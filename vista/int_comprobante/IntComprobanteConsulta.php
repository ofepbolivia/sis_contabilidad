<?php
/**
 *@package pXP
 *@file IntCBTDiarioRegAuxSigep.php
 *@author  (franklin.espinoza)
 *@date 20-09-2011 10:22:05
 *@description Archivo con la interfaz de usuario que permite
 *dar el visto a solicitudes de compra
 *
 */
header("content-type: text/javascript; charset=UTF-8");
?>
<style type="text/css" rel="stylesheet">
    .x-selectable,
    .x-selectable * {
        -moz-user-select: text !important;
        -khtml-user-select: text !important;
        -webkit-user-select: text !important;
    }

    .x-grid-row td,
    .x-grid-summary-row td,
    .x-grid-cell-text,
    .x-grid-hd-text,
    .x-grid-hd,
    .x-grid-row,

    .x-grid-row,
    .x-grid-cell,
    .x-unselectable
    {
        -moz-user-select: text !important;
        -khtml-user-select: text !important;
        -webkit-user-select: text !important;
    }
</style>
<script>
    Phx.vista.IntComprobanteConsulta = {
        bsave:true,

        require: '../../../sis_contabilidad/vista/int_comprobante/IntComprobante.php',
        requireclase: 'Phx.vista.IntComprobante',
        title: 'Consulta Comprobantes',
        nombreVista: 'IntComprobanteConsulta',


        viewConfig: {
            stripeRows: false,
            getRowClass: function(record) {
                return "x-selectable";
            }
        },

        constructor: function(config) {


            Phx.vista.IntComprobanteConsulta.superclass.constructor.call(this,config);
            this.maestro =  config; console.log('maestro',this.maestro);
            //this.store.baseParams.tipo = 'diario';
            this.store.baseParams.estado_cbte = 'borrador';



            /*this.addButton('btnWizard', {
                grupo: [0],
                text : 'Plantilla',
                iconCls : 'bgear',
                disabled : false,
                handler : this.loadWizard,
                tooltip : '<b>Plantilla de Comprobantes</b><br/>Seleccione una plantilla y genere comprobantes preconfigurados'
            });*/

            /*this.addButton('btnIgualarCbte', {
                grupo: [0],
                text : 'Igualar',
                iconCls : 'bengineadd',
                disabled : true,
                handler : this.igualarCbte,
                tooltip : '<b>Igualar comprobante</b><br/>Si existe diferencia por redondeo o por tipo de cambio inserta una transacción para igualar'
            });*/

            /*this.addButton('btnSwEditble', {
                grupo: [0],
                text : 'Editable',
                iconCls : 'balert',
                disabled : true,
                handler : this.swEditable,
                tooltip : '<b>Hacer editable</b><br/>Si la edición esta deshabilitada toma un backup y la habilita'
            });*/


            this.cmbDepto.store.load({params:{start:0, limit:this.tam_pag}, scope:this, callback: function (param,op,suc) {
                    this.cmbDepto.setValue(this.maestro.id_depto);
                    this.cmbDepto.collapse();
                    //this.Cmp.id_tipo_columna.focus(false,  5);
                }}
            );

            this.cmbGestion.store.load({params:{start:0, limit:this.tam_pag}, scope:this, callback: function (param,op,suc) {
                    this.cmbGestion.setValue(this.maestro.id_gestion);
                    this.cmbGestion.collapse();
                    //this.Cmp.id_tipo_columna.focus(false,  5);
                }}
            );

            this.store.baseParams.id_deptos = this.maestro.id_depto;
            this.store.baseParams.id_gestion = this.maestro.id_gestion;
            this.store.baseParams.nombreVista = this.nombreVista;

            this.getBoton('btnAIRBP').setVisible(false);
            this.init();
            this.getBoton('btnRelDev').setVisible(false);
            this.getBoton('btnAIRBP').setVisible(false);
            this.getBoton('chkdep').setVisible(false);
            this.getBoton('btnObs').setVisible(false);

            //this.getBoton('b-libro_diario-' + this.idContenedor).setVisible(false);
            //this.getBoton('b-chkpresupuesto-' + this.idContenedor).setVisible(false);
            //this.getBoton('btnAIRBP').setVisible(false);
            this.sm.on('rowselect', this.selectButton,this);
            this.sm.on('rowdeselect', this.deselectButton,this);

            this.load({params: {start: 0, limit: this.tam_pag}});
            //this.reload();
        },

        selectButton : function(grid, rowIndex, rec) {
            let record = this.getSelectedData();
            if(record.estado_reg == 'borrador'){
                //this.getBoton('sigep_verificado').setText('VERIFICAR SIGEP');
            }
        },

        deselectButton : function(grid, rowIndex, rec) {
            //this.getBoton('sigep_verificado').setText('ENVIAR SIGEP');
        },


        gruposBarraTareas: [
            {name:  'borrador', title: '<h1 style="text-align:center; color:#4682B4;"><i class="fa fa-user fa-2x" aria-hidden="true"></i> BORRADOR</h1>',grupo: 0, height: 1} ,
            //{name: 'elaborado', title: '<h1 style="text-align: center; color: #586E7E ;"><i class="fa fa-user fa-2x" aria-hidden="true"></i> ELABORADO</h1>', grupo: 1, height: 1},
            //{name: 'verificado', title: '<h1 style="text-align: center; color: #00B167;"><i class="fa fa-user fa-2x" aria-hidden="true"></i> VERIFICADO</h1>', grupo: 2, height: 1},
            //{name: 'aprobado', title: '<h1 style="text-align: center; color: #B066BB;"><i class="fa fa-user fa-2x" aria-hidden="true"></i> APROBADO</h1>', grupo: 3, height: 1},
            {name: 'validado', title: '<h1 style="text-align: center; color: #FF8F85;"><i class="fa fa-user fa-2x" aria-hidden="true"></i> VALIDADO</h1>', grupo: 4, height: 1}
        ],

        bnewGroups:[],
        bsaveGroups:[],
        beditGroups:[],
        bdelGroups:[],
        bactGroups:[0,1,2,3,4],
        bexcelGroups:[0,1,2,4],

        actualizarSegunTab: function(name, indice){



            this.store.baseParams.estado_cbte = name;
            this.store.baseParams.id_deptos = this.cmbDepto.getValue();
            this.store.baseParams.id_gestion = this.cmbGestion.getValue();
            this.store.baseParams.nombreVista = this.nombreVista;

            console.log('estado_cbte',name);
            this.load({params: {start: 0, limit: 50}});


        },


        onButtonEdit:function(){
            this.swButton = 'EDIT';
            var rec = this.sm.getSelected().data;

            this.cmpFecha.enable();

            Phx.vista.IntComprobanteConsulta.superclass.onButtonEdit.call(this);
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

            Phx.vista.IntComprobanteConsulta.superclass.onButtonNew.call(this);
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
            var tb = Phx.vista.IntComprobanteConsulta.superclass.preparaMenu.call(this);
            var rec = this.sm.getSelected();
            if(rec.data.tipo_reg == 'summary'){
                //this.getBoton('btnSwEditble').disable();
                //this.getBoton('sig_estado').disable();
                this.getBoton('btnImprimir').disable();
                //this.getBoton('btnRelDev').disable();
                //this.getBoton('btnIgualarCbte').disable();
                this.getBoton('btnDocCmpVnt').disable();
            }
            else{
                if(rec.data.sw_editable == 'no'){
                    //this.getBoton('btnSwEditble').setDisabled(false);
                }
                else{
                    //this.getBoton('btnSwEditble').setDisabled(true);
                }
                //this.getBoton('sig_estado').enable();
                this.getBoton('btnImprimir').enable();
                //this.getBoton('btnRelDev').enable();
                //this.getBoton('btnIgualarCbte').enable();
                this.getBoton('btnDocCmpVnt').enable();
                this.getBoton('chkpresupuesto').enable();
                this.getBoton('btnChequeoDocumentosWf').enable();
                this.getBoton('diagrama_gantt').enable();
                //this.getBoton('btnObs').enable();
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

            //this.getBoton('sigep_verificado').enable();

            return tb;
        },
        liberaMenu : function() {
            var tb = Phx.vista.IntComprobanteConsulta.superclass.liberaMenu.call(this);

            //this.getBoton('sig_estado').disable();
            this.getBoton('btnImprimir').disable();
            //this.getBoton('btnRelDev').disable();
            //this.getBoton('btnIgualarCbte').disable();
            this.getBoton('btnDocCmpVnt').disable();
            this.getBoton('chkpresupuesto').disable();
            this.getBoton('btnChequeoDocumentosWf').disable();
            this.getBoton('diagrama_gantt').disable();
            //this.getBoton('btnObs').disable()
            //this.getBoton('sigep_verificado').disable();


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