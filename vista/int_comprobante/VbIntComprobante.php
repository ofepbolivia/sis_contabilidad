<?php
/**
 * @package pXP
 * @file gen-SistemaDist.php
 * @author  Maylee Perez Pastor
 * @date 25-09-2019 10:22:05
 * @description Archivo con la interfaz de usuario que permite
 *dar el visto a los comprobantes
 *
 */
header("content-type: text/javascript; charset=UTF-8");
?>
<script>
    Phx.vista.VbIntComprobante = {
        bsave: false,
        bnew: false,
        bdel: false,
        btnWizard: false,
        btnIgualarCbte: false,
        btnAIRBP: false,


        require: '../../../sis_contabilidad/vista/int_comprobante/IntComprobante.php',
        requireclase: 'Phx.vista.IntComprobante',
        title: 'Libro Diario',
        nombreVista: 'VbIntComprobante',


        constructor: function (config) {
            var me = this;
            //me.bMedios = [];
            /*me.addButtonCustom(config.idContenedor, 'ant_estado', {
                text: 'Anterior',
                iconCls: 'batras',
                disabled: true,
                handler: this.antEstado,
                tooltip: '<b>Pasar al Anterior Estado</b>'
            });

            me.addButtonCustom(config.idContenedor, 'sig_estado', {
                text: 'Aprobar',
                iconCls: 'badelante',
                disabled: true,
                handler: this.sigEstado,
                tooltip: '<b>Pasar al Siguiente Estado</b>'
            });*/

            /*this.addButtonCustom(config.idContenedor, 'sigep_vb', {
                text : 'ENVIAR SIGEP',
                iconCls : 'bball_green',
                disabled : true,
                handler : this.onSigepWizard,
                tooltip: '<b>Procesar en el Sigep</b>'
            });*/

            Phx.vista.VbIntComprobante.superclass.constructor.call(this, config);

            //Siguiente
            this.addButtonIndex(5,'sig_estado', {
                text: 'Aprobar',
                grupo: [0, 1, 2, 3],
                iconCls: 'badelante',
                disabled: true,
                handler: this.sigEstado,
                tooltip: '<b>Pasar al Siguiente Estado</b>'
            });
            //Anterior
            this.addButtonIndex(5,'ant_estado',{
                grupo: [0,1,2,3,4,5],
                argument: {estado: 'anterior'},
                text: 'Anterior',
                iconCls: 'batras',
                hidden: false,
                handler: this.antEstado,
                tooltip: '<b>Volver al Anterior Estado</b>'
            });



            this.addButton('btnAIRBP',
                {
                    text: 'Subir AIRBP',
                    iconCls: 'blist',
                    disabled: false,
                    hidden: true,
                    handler: this.onButtonAIRBP,
                    tooltip: 'Subir archivo facturas AIRBP'
                }
            );

            this.addButton('btnWizard', {
                text: 'Plantilla',
                iconCls: 'bgear',
                disabled: false,
                hidden: true,
                handler: this.loadWizard,
                tooltip: '<b>Plantilla de Comprobantes</b><br/>Seleccione una plantilla y genere comprobantes preconfigurados'
            });

            this.addButton('btnIgualarCbte', {
                text: 'Igualar',
                iconCls: 'bengineadd',
                disabled: false,
                hidden: false,
                handler: this.igualarCbte,
                tooltip: '<b>Igualar comprobante</b><br/>Si existe diferencia por redondeo o por tipo de cambio inserta una transacción para igualar'
            });

            this.addButton('btnSwEditble', {
                text: 'Editable',
                iconCls: 'balert',
                disabled: false,
                hidden: true,
                handler: this.swEditable,
                tooltip: '<b>Hacer editable</b><br/>Si la edición esta deshabilitada toma un backup y la habilita'
            });

            this.store.baseParams.estado_cbte = this.pes_estado;
            this.init();

            //this.sm.on('rowselect', this.getPlazoSelect,this);
            //this.sm.on('rowdeselect', this.getPlazoDeselect,this);
        },

        cmbDepto: new Ext.form.AwesomeCombo({
            name: 'id_depto',
            fieldLabel: 'Depto',
            typeAhead: false,
            forceSelection: true,
            allowBlank: false,
            disableSearchButton: true,
            emptyText: 'Depto Contable',
            store: new Ext.data.JsonStore({
                url: '../../sis_parametros/control/Depto/listarDeptoFiltradoPrioridadEXT', //fRnk: corregido, anterior valor listarDeptoFiltradoDeptoUsuario listarDeptoFiltradoPrioridadEXT
                id: 'id_depto',
                root: 'datos',
                sortInfo: {
                    field: 'deppto.nombre',
                    direction: 'ASC'
                },
                totalProperty: 'total',
                fields: ['id_depto', 'nombre', 'codigo'],
                // turn on remote sorting
                remoteSort: true,
                baseParams: {
                    par_filtro: 'deppto.nombre#deppto.codigo',
                    estado: 'activo',
                    codigo_subsistema: 'CONTA'
                }
            }),
            valueField: 'id_depto',
            displayField: 'nombre',
            hiddenName: 'id_depto',
            enableMultiSelect: true,
            triggerAction: 'all',
            lazyRender: true,
            mode: 'remote',
            pageSize: 20,
            queryDelay: 200,
            anchor: '80%',
            listWidth: '280',
            resizable: true,
            minChars: 2
        }),

        /*===================================================BEGIN ESTADO ANTERIOR======================================================*/
        antEstado:function(res){
            var rec=this.sm.getSelected();
            Phx.CP.loadWindows('../../../sis_workflow/vista/estado_wf/AntFormEstadoWf.php',
                'Estado de Wf',
                {
                    modal:true,
                    width:450,
                    height:250
                }, {
                    data:rec.data,
                    estado_destino: res.argument.estado
                },
                this.idContenedor,'AntFormEstadoWf',
                {
                    config:[{
                        event:'beforesave',
                        delegate: this.onAntEstado,
                    }
                    ],
                    scope:this
                })
        },

        desverificaProcesoSigep : function (wizard,response){
            var record = this.getSelectedData();
            console.log('record Desverifica', record);
            Ext.Ajax.request({
                url:'../../sis_sigep/control/SigepAdq/readyProcesoSigep',
                params:{
                    id_service_request : record.id_service_request,
                    estado_reg : record.estado_reg,
                    momento : 'pass',
                    direction : 'previous'
                },
                success: function (resp) {
                    var reg =  Ext.decode(Ext.util.Format.trim(resp.responseText));
                    var datos = reg.ROOT.datos;
                    console.log('desverificaProcesoSigep',datos);
                    if(datos.process){

                        Phx.CP.loadingHide();
                        Ext.Ajax.request({
                            url:'../../sis_contabilidad/control/IntComprobante/anteriorEstado',
                            params:{
                                id_proceso_wf: response.id_proceso_wf,
                                id_estado_wf:  response.id_estado_wf,
                                obs: response.obs,
                                estado_destino: response.estado_destino
                            },
                            argument:{ wizard : wizard },
                            success:this.successEstadoSinc,
                            failure: this.conexionFailure,
                            timeout:this.timeout,
                            scope:this
                        });
                    }else{
                        Phx.CP.loadingHide();
                    }
                },
                failure: this.conexionFailure,
                timeout: this.timeout,
                scope:this
            });
        },

        onAntEstado: function(wizard,resp){
            var record = this.getSelectedData();
            console.log('onAntEstado', record.estado_reg);
            Phx.CP.loadingShow();
            if(record.estado_reg == 'verificado'){
                this.desverificaProcesoSigep(wizard,resp);
            }else {
                Ext.Ajax.request({
                    url: '../../sis_contabilidad/control/IntComprobante/anteriorEstado',
                    params: {
                        id_proceso_wf: resp.id_proceso_wf,
                        id_estado_wf: resp.id_estado_wf,
                        obs: resp.obs,
                        estado_destino: resp.estado_destino
                    },
                    argument: {wizard: wizard},
                    success: this.successEstadoSinc,
                    failure: this.conexionFailure,
                    timeout: this.timeout,
                    scope: this
                });

            }
        },

        successEstadoSinc:function(resp){
            Phx.CP.loadingHide();
            resp.argument.wizard.panel.destroy();
            this.reload();
        },

        /*===================================================END ESTADO ANTERIOR======================================================*/

        selectButton : function(grid, rowIndex, rec) {
            let record = this.getSelectedData();

            if(record.estado_reg == 'vbconta'){
                this.getBoton('sigep_vb').setText('APROBAR SIGEP');
            }else if(record.estado_reg == 'vbfin'){
                this.getBoton('sigep_vb').setText('FIRMAR SIGEP');
            }
        },

        deselectButton : function(grid, rowIndex, rec) {
            this.getBoton('sigep_vb').setText('ENVIAR SIGEP');
        },

        /*==========================================================*/
        /*=================================BEGIN WORKFLOW APROBAR=======================================*/
        sigEstado: function () {
            var rec = this.sm.getSelected();
            this.mostrarWizard(rec, true);


        },

        mostrarWizard: function (rec, validar_doc) {
            var configExtra = [],
                obsValorInicial;

            this.objWizard = Phx.CP.loadWindows('../../../sis_workflow/vista/estado_wf/FormEstadoWf.php',
                'Estado de Wf',
                {
                    modal: true,
                    width: 700,
                    height: 450
                },
                {
                    configExtra: configExtra,
                    eventosExtra: this.eventosExtra,
                    data: {
                        id_estado_wf: rec.data.id_estado_wf,
                        id_proceso_wf: rec.data.id_proceso_wf,
                        id_int_comprobante: rec.data.id_int_comprobante,
                        fecha_ini: rec.data.fecha
                    },
                    obsValorInicial: obsValorInicial,
                }, this.idContenedor, 'FormEstadoWf',
                {
                    config: [{
                        event: 'beforesave',
                        delegate: this.onSaveWizard,

                    },
                        {
                            event: 'requirefields',
                            delegate: function () {
                                this.onButtonEdit();
                                this.window.setTitle('Registre los campos antes de pasar al siguiente estado');
                                this.formulario_wizard = 'si';
                            }

                        }],

                    scope: this
                });
        },
        onSaveWizard: function (wizard, resp) {
            this.mandarDatosWizard(wizard, resp, true);
        },

        mandarDatosWizard:function(wizard,resp, validar_doc){
            var rec = this.getSelectedData();

            console.log('wizardSIGP:',wizard,'respSIGP:',resp, rec);
            Phx.CP.loadingShow();
            if(rec.estado_reg == 'verificado'){
                //if (rec.id_clase_comprobante == 3){
                    this.onEgaAprobarCIP(wizard,resp);
                //}

            }else if(rec.estado_reg == 'aprobado'){
                //if (rec.id_clase_comprobante == 3){
                    this.onEgaFirmarCIP(wizard,resp);
                //}
            }

        },

        onEgaAprobarCIP: function(wizard, response){
            /*let record = this.getSelectedData();
            Phx.CP.loadingShow();
            Ext.Ajax.request({
                url: '../../sis_sigep/control/SigepAdq/aprobarC31',
                params: {
                    id_service_request: record.id_service_request,
                    service_code: 'C31_APROBAR'
                },
                success: function(resp){

                },
                failure: this.failureC, //chequea si esta en verificacion presupeusto para enviar correo de transferencia
                //argument: {wizard: wizard},
                timeout: this.timeout,
                scope: this
            });*/

            let record = this.getSelectedData();
            console.log('record', record, 'wizard', wizard, 'response', response);
            Ext.Ajax.request({
                url:'../../sis_sigep/control/SigepAdq/readyProcesoSigep',
                params:{
                    id_service_request : record.id_service_request,
                    estado_reg : record.estado_reg,
                    direction : 'next',
                    momento : 'pass'
                },
                success: function (resp) {
                    var reg =  Ext.decode(Ext.util.Format.trim(resp.responseText));
                    var datos = reg.ROOT.datos;
                    console.log('aprobarProcesoSigep',datos);
                    if(datos.process){

                        Phx.CP.loadingHide();
                        Ext.Ajax.request({
                            url: '../../sis_contabilidad/control/IntComprobante/siguienteEstado',
                            params: {
                                id_int_comprobante: record.id_int_comprobante,
                                id_proceso_wf_act: response.id_proceso_wf_act,
                                id_estado_wf_act: response.id_estado_wf_act,
                                id_tipo_estado: response.id_tipo_estado,
                                id_funcionario_wf: response.id_funcionario_wf,
                                id_depto_wf: response.id_depto_wf,
                                obs: response.obs,
                                instruc_rpc: response.instruc_rpc,
                                json_procesos: Ext.util.JSON.encode(response.procesos),
                                validar_doc: true

                            },
                            success: this.successWizard,
                            failure: this.conexionFailure,
                            argument: {wizard: wizard, id_proceso_wf: response.id_proceso_wf_act, resp: response},
                            timeout: this.timeout,
                            scope: this
                        });
                    }else{
                        Phx.CP.loadingHide();
                    }
                },
                failure: this.conexionFailure,
                timeout: this.timeout,
                scope:this
            });
        },

        successWizard: function (resp) {
            var rec = this.sm.getSelected();
            Phx.CP.loadingHide();

            var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));

            if (reg.ROOT.datos.operacion == 'falla') {


                reg.ROOT.datos.desc_falla
                if (confirm(reg.ROOT.datos.desc_falla + "\n¿Desea continuar de todas formas?")) {
                    this.mandarDatosWizard(resp.argument.wizard, resp.argument.resp, false);
                }
                else {
                    resp.argument.wizard.panel.destroy();
                    this.reload();
                }

            }else {
                resp.argument.wizard.panel.destroy();
                this.reload();
                if(rec.data.estado_reg == 'aprobado'){
                    if (resp.argument.id_proceso_wf) {
                        Phx.CP.loadingShow();
                        Ext.Ajax.request({
                            url: '../../sis_contabilidad/control/IntComprobante/reporteCbte',
                            params: {
                                'id_proceso_wf': resp.argument.id_proceso_wf
                            },
                            success: this.successExport,
                            failure: this.conexionFailure,
                            timeout: this.timeout,
                            scope: this
                        });
                    }
                }
            }
        },

        /*=================================END WORKFLOW APROBAR=======================================*/

        onEgaFirmarCIP: function(wizard,resp){
            let record = this.getSelectedData();

            Phx.CP.loadingShow();
            Ext.Ajax.request({
                url: '../../sis_contabilidad/control/IntComprobante/siguienteEstado',
                params: {
                    id_int_comprobante: record.id_int_comprobante,
                    id_proceso_wf_act: resp.id_proceso_wf_act,
                    id_estado_wf_act: resp.id_estado_wf_act,
                    id_tipo_estado: resp.id_tipo_estado,
                    id_funcionario_wf: resp.id_funcionario_wf,
                    id_depto_wf: resp.id_depto_wf,
                    obs: resp.obs,
                    instruc_rpc: resp.instruc_rpc,
                    json_procesos: Ext.util.JSON.encode(resp.procesos),
                    validar_doc: true

                },
                success: this.successWizard,
                failure: this.conexionFailure,
                argument: {wizard: wizard, id_proceso_wf: resp.id_proceso_wf_act, resp: resp},
                timeout: this.timeout,
                scope: this
            });
        },

        /*==========================================================*/

        onButtonEdit: function () {
            this.swButton = 'EDIT';
            var rec = this.sm.getSelected().data;

            this.cmpFecha.enable();

            Phx.vista.VbIntComprobante.superclass.onButtonEdit.call(this);
            this.Cmp.id_moneda.setReadOnly(true);
            if (rec.localidad == 'internacional') {
                this.Cmp.fecha.setReadOnly(true);
            }
            //si el tic vari en lastransacciones ..
            if (rec.sw_tipo_cambio == 'si') {
                this.ocultarComponente(this.Cmp.tipo_cambio);
                this.ocultarComponente(this.Cmp.tipo_cambio_2);
                this.ocultarComponente(this.Cmp.tipo_cambio_3);
            } else {
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

        onButtonNew: function () {
            this.swButton = 'NEW';
            Phx.vista.VbIntComprobante.superclass.onButtonNew.call(this);
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

        igualarCbte: function () {

            var rec = this.sm.getSelected().data;
            Phx.CP.loadingShow();
            Ext.Ajax.request({
                url: '../../sis_contabilidad/control/IntComprobante/igualarComprobante',
                params: {
                    id_int_comprobante: rec.id_int_comprobante
                },
                success: function (resp) {
                    Phx.CP.loadingHide();
                    var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
                    if (reg.ROOT.error) {
                        Ext.Msg.alert('Error', 'No se pudo igualar el cbte: ' + reg.ROOT.error)
                    } else {
                        this.reload();
                    }
                },
                failure: this.conexionFailure,
                timeout: this.timeout,
                scope: this
            });


        },


        swEditable: function () {

            var rec = this.sm.getSelected().data;
            Phx.CP.loadingShow();
            Ext.Ajax.request({
                url: '../../sis_contabilidad/control/IntComprobante/swEditable',
                params: {
                    id_int_comprobante: rec.id_int_comprobante
                },
                success: function (resp) {
                    Phx.CP.loadingHide();
                    var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
                    if (reg.ROOT.error) {
                        Ext.Msg.alert('Error', 'Al  cambiar el modo de edición: ' + reg.ROOT.error)
                    } else {
                        this.reload();
                    }
                },
                failure: this.conexionFailure,
                timeout: this.timeout,
                scope: this
            });
        },
        preparaMenu: function (n) {
            var tb = Phx.vista.VbIntComprobante.superclass.preparaMenu.call(this);

            var rec = this.sm.getSelected();

            if (rec.data.tipo_reg == 'summary') {
                this.getBoton('btnSwEditble').disable();
                this.getBoton('sig_estado').disable();
                this.getBoton('btnImprimir').disable();
                this.getBoton('btnRelDev').disable();
                this.getBoton('btnIgualarCbte').disable();
                this.getBoton('btnDocCmpVnt').disable();
                this.getBoton('ant_estado').disable();
            } else {
                if (rec.data.sw_editable == 'no') {
                    this.getBoton('btnSwEditble').setDisabled(false);
                } else {
                    this.getBoton('btnSwEditble').setDisabled(true);
                }
                this.getBoton('sig_estado').enable();
                this.getBoton('btnImprimir').enable();
                this.getBoton('btnRelDev').enable();
                this.getBoton('btnIgualarCbte').enable();
                this.getBoton('btnDocCmpVnt').enable();
                this.getBoton('chkpresupuesto').enable();
                this.getBoton('btnChequeoDocumentosWf').enable();
                this.getBoton('diagrama_gantt').enable();
                this.getBoton('btnObs').enable();
                this.getBoton('ant_estado').enable();
            }
            if (rec.data.momento == 'presupuestario') {
                this.getBoton('btnDocCmpVnt').enable();
            } else {
                this.getBoton('btnDocCmpVnt').disable();
            }
            /*if('vbfin' == rec.data.estado_reg){
                this.getBoton('sigep').enable();
            }*/

            return tb;
        },
        liberaMenu: function () {
            var tb = Phx.vista.VbIntComprobante.superclass.liberaMenu.call(this);

            this.getBoton('sig_estado').disable();
            this.getBoton('btnImprimir').disable();
            this.getBoton('btnRelDev').disable();
            this.getBoton('btnIgualarCbte').disable();
            this.getBoton('btnDocCmpVnt').disable();
            this.getBoton('chkpresupuesto').disable();
            this.getBoton('btnChequeoDocumentosWf').disable();
            this.getBoton('diagrama_gantt').disable();
            this.getBoton('btnObs').disable()
            this.getBoton('ant_estado').disable()
            //this.getBoton('sigep').disable();

        },
        /*
        capturaFiltros : function(combo, record, index) {
            this.desbloquearOrdenamientoGrid();
            this.store.baseParams.id_deptos = this.cmbDepto.getValue();
            this.store.baseParams.nombreVista = this.nombreVista;
            this.load();
        },*/

        getTipoCambio: function () {
            //Verifica que la fecha y la moneda hayan sido elegidos
            if (this.Cmp.fecha.getValue() && this.Cmp.id_moneda.getValue()) {
                Ext.Ajax.request({
                    url: '../../sis_parametros/control/TipoCambio/obtenerTipoCambio',
                    params: {
                        fecha: this.Cmp.fecha.getValue(),
                        id_moneda: this.Cmp.id_moneda.getValue(),
                        tipo: 'O'
                    },
                    success: function (resp) {
                        Phx.CP.loadingHide();
                        var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
                        if (reg.ROOT.error) {
                            Ext.Msg.alert('Error', 'Validación no realizada: ' + reg.ROOT.error)
                        } else {
                            this.Cmp.tipo_cambio.setValue(reg.ROOT.datos.tipo_cambio);
                        }
                    },
                    failure: this.conexionFailure,
                    timeout: this.timeout,
                    scope: this
                });
            }

        },
        getConfigCambiaria: function (sw_valores) {

            var localidad = 'nacional';

            if (this.swButton == 'EDIT') {
                var rec = this.sm.getSelected();
                localidad = rec.data.localidad;

            }

            //Verifica que la fecha y la moneda hayan sido elegidos
            if (this.Cmp.fecha.getValue() && this.Cmp.id_moneda.getValue() && this.Cmp.forma_cambio.getValue()) {
                Phx.CP.loadingShow();
                var forma_cambio = this.Cmp.forma_cambio.getValue();
                if (forma_cambio == 'convenido') {
                    this.Cmp.tipo_cambio.setReadOnly(false);
                    this.Cmp.tipo_cambio_2.setReadOnly(false);
                } else {
                    this.Cmp.tipo_cambio.setReadOnly(true);
                    this.Cmp.tipo_cambio_2.setReadOnly(true);
                }

                this.Cmp.tipo_cambio_3.setReadOnly(true);

                Ext.Ajax.request({
                    url: '../../sis_contabilidad/control/ConfigCambiaria/getConfigCambiaria',
                    params: {
                        fecha: this.Cmp.fecha.getValue(),
                        id_moneda: this.Cmp.id_moneda.getValue(),
                        localidad: localidad,
                        sw_valores: sw_valores,
                        forma_cambio: forma_cambio
                    }, success: function (resp) {
                        Phx.CP.loadingHide();
                        var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
                        if (reg.ROOT.error) {
                            this.Cmp.tipo_cambio.reset();
                            this.Cmp.tipo_cambio_2.reset();
                            this.Cmp.tipo_cambio_3.reset();
                            Ext.Msg.alert('Error', 'Validación no realizada: ' + reg.ROOT.error)
                        } else {

                            //cambia labels

                            this.Cmp.tipo_cambio.label.update(reg.ROOT.datos.v_tc1 + ' (tc)');
                            this.Cmp.tipo_cambio_2.label.update(reg.ROOT.datos.v_tc2 + ' (tc)');
                            this.Cmp.tipo_cambio_3.label.update(reg.ROOT.datos.v_tc3 + ' (tc)');
                            if (sw_valores == 'si') {
                                //poner valores por defecto
                                this.Cmp.tipo_cambio.setValue(reg.ROOT.datos.v_valor_tc1);
                                this.Cmp.tipo_cambio_2.setValue(reg.ROOT.datos.v_valor_tc2);
                                this.Cmp.tipo_cambio_3.setValue(reg.ROOT.datos.v_valor_tc3);
                            }


                            this.Cmp.id_config_cambiaria.setValue(reg.ROOT.datos.id_config_cambiaria);
                        }


                    }, failure: function (a, b, c, d) {
                        this.Cmp.tipo_cambio.reset();
                        this.Cmp.tipo_cambio_2.reset();
                        this.Cmp.tipo_cambio_3.reset();
                        this.conexionFailure(a, b, c, d)
                    },
                    timeout: this.timeout,
                    scope: this
                });
            }

        },
        validarCbte: function () {
            Ext.Msg.confirm('Confirmación', '¿Está seguro de Validar el Comprobante?', function (btn, x, c) {
                if (btn == 'yes') {
                    var rec = this.sm.getSelected();
                    Phx.CP.loadingShow();
                    Ext.Ajax.request({
                        url: '../../sis_contabilidad/control/IntComprobante/validarIntComprobante',
                        params: {
                            id_int_comprobante: rec.data.id_int_comprobante,
                            igualar: 'no'
                        },
                        success: function (resp) {
                            Phx.CP.loadingHide();
                            var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
                            if (reg.ROOT.error) {
                                Ext.Msg.alert('Error', 'Validación no realizada: ' + reg.ROOT.error)
                            } else {
                                this.reload();
                                Ext.Msg.alert('Mensaje', 'Proceso ejecutado con éxito')
                            }
                        },
                        failure: this.conexionFailure,
                        timeout: this.timeout,
                        scope: this
                    });
                }
            }, this);
        },
        loadWizard: function () {
            var rec = this.sm.getSelected();
            Phx.CP.loadWindows('../../../sis_contabilidad/vista/int_comprobante/WizardCbte.php', 'Generar comprobante desde plantilla ...', {
                width: '40%',
                height: 300
            }, rec, this.idContenedor, 'WizardCbte')
        },
        // south: {
        //     url: '../../../sis_contabilidad/vista/int_transaccion/IntTransaccionAux.php',
        //     title: 'Transacciones',
        //     height: '50%', //altura de la ventana hijo
        //     cls: 'IntTransaccionAux'
        // }
        // 27-10-2022 ANPM Adicion de tab Beneficiario
		tabsouth : [
            {
                url : '../../../sis_contabilidad/vista/int_transaccion/IntTransaccionAux.php',
                title : 'Transacciones',
                height : '50%', //altura de la ventana hijo
                cls : 'IntTransaccionAux'
		    },
            {
                url : '../../../sis_contabilidad/vista/int_beneficiario/IntBeneficiarioAux.php',
                title : 'Beneficiario',
                height : '50%', //altura de la ventana hijo
                cls : 'IntBeneficiarioAux'
		    }
        ]

    };
</script>