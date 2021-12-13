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
    Phx.vista.IntCBTDiarioRegAuxSigep = {
        bsave:true,

        require: '../../../sis_contabilidad/vista/int_comprobante/IntComprobante.php',
        requireclase: 'Phx.vista.IntComprobante',
        title: 'Libro Diario',
        nombreVista: 'IntCBTDiarioRegAuxSigep',


        viewConfig: {
            stripeRows: false,
            getRowClass: function(record) {
                return "x-selectable";
            }
        },

        constructor: function(config) {
            var me = this;
            me.bMedios = [];
            me.addButtonCustom(config.idContenedor, 'sig_estado', { text: 'Aprobar', iconCls: 'badelante', disabled: true, handler: this.sigEstado, tooltip: '<b>Pasar al Siguiente Estado</b>' });
            /*me.addButtonCustom(config.idContenedor, 'sigep_verificado', {
                grupo: [0],
                text : 'ENVIAR SIGEP',
                iconCls : 'bball_green',
                disabled : true,
                handler : this.onSigepWizard,
                tooltip: '<b>Procesar Sigep Hasta Verificado</b>'
            });*/
            Phx.vista.IntCBTDiarioRegAuxSigep.superclass.constructor.call(this,config);
            this.store.baseParams.tipo = 'diario';
            this.store.baseParams.estado_cbte = 'borrador';

            this.addButtonIndex(9,'sigep_verificado',
                {
                    iconCls: 'bball_green',
                    xtype: 'splitbutton',
                    grupo: [0,4],
                    tooltip: '<b>Acciones para procesar SIGEP</b>',
                    text: 'ACTION SIGEP',
                    //handler: this.onButtonExcel,
                    argument: {
                        'news': true,
                        def: 'reset'
                    },
                    scope: this,
                    menu: [{
                        text: 'Crear C31',
                        iconCls: 'bnew-sigep',
                        argument: {
                            'news': true,
                            def: 'csv'
                        },
                        handler: this.onSigepWizard,
                        scope: this
                    }, {
                        text: 'Verificar C31',
                        iconCls: 'bver-sigep',
                        argument: {
                            'news': true,
                            def: 'pdf'
                        },
                        handler: this.onVerificarC31,
                        scope: this
                    }]
                }
            );

            this.addButtonIndex(6,'docCargaSigep',{
                text:'Doc. SIGEP',
                iconCls: 'brenew',
                disabled: true,
                handler: this.onCargarDocumentoSigep,
                tooltip: '<b>Cargar Documento SIGEP</b><br/>Información para los comprobantes que ya tienen Preventivo en el SIGEP.'
            });

            this.addButton('btnWizard', {
                grupo: [0],
                text : 'Plantilla',
                iconCls : 'bgear',
                disabled : false,
                handler : this.loadWizard,
                tooltip : '<b>Plantilla de Comprobantes</b><br/>Seleccione una plantilla y genere comprobantes preconfigurados'
            });

            this.addButton('btnIgualarCbte', {
                grupo: [0],
                text : 'Igualar',
                iconCls : 'bengineadd',
                disabled : true,
                handler : this.igualarCbte,
                tooltip : '<b>Igualar comprobante</b><br/>Si existe diferencia por redondeo o por tipo de cambio inserta una transacción para igualar'
            });

            this.addButton('btnSwEditble', {
                grupo: [0],
                text : 'Editable',
                iconCls : 'balert',
                disabled : true,
                handler : this.swEditable,
                tooltip : '<b>Hacer editable</b><br/>Si la edición esta deshabilitada toma un backup y la habilita'
            });

            this.init();
            //this.sm.on('rowselect', this.selectButton,this);
            //this.sm.on('rowdeselect', this.deselectButton,this);

        },

        onCargarDocumentoSigep:  function (){
            this.formDocumentoSigep();
            this.windowDocumento.show();
        },

        onSubmitDocumento: function () {
            var record = this.getSelectedData();
            Phx.CP.loadingShow();
            Ext.Ajax.request({
                url: '../../sis_contabilidad/control/IntComprobante/guardarDocumentoSigep',
                success: this.successDocumento,
                failure: this.failureDocumento,
                params: {
                    'id_int_comprobante' : record.id_int_comprobante,
                    'preventivo' : this.formDocumento.getForm().findField('preventivo').getValue()/*,
                    'compromiso' : this.formDocumento.getForm().findField('compromiso').getValue(),
                    'devengado' : this.formDocumento.getForm().findField('devengado').getValue()*/
                },
                timeout: this.timeout,
                scope: this
            });

        },

        successDocumento: function (resp) {
            this.windowDocumento.hide();
            Phx.vista.IntCBTDiarioRegAuxSigep.superclass.successDel.call(this, resp);

        },

        failureDocumento: function (resp) {
            Phx.CP.loadingHide();
            Phx.vista.IntCBTDiarioRegAuxSigep.superclass.conexionFailure.call(this, resp);

        },

        formDocumentoSigep: function () {

            this.formDocumento = new Ext.form.FormPanel({
                id: this.idContenedor + '_DOCSIGEP',
                items: [
                    new Ext.form.TextField({
                        fieldLabel: 'Nro. Preventivo',
                        name: 'preventivo',
                        //height: 150,
                        allowBlank: false,
                        width: '90%',
                        msgTarget : 'side'
                    })/*,
                    new Ext.form.TextField({
                        fieldLabel: 'Nro. Compromiso',
                        name: 'compromiso',
                        //height: 150,
                        allowBlank: true,
                        width: '90%',
                        msgTarget : 'side'

                    }),
                    new Ext.form.TextField({
                        fieldLabel: 'Nro. Devengado',
                        name: 'devengado',
                        //height: 150,
                        allowBlank: true,
                        width: '90%',
                        value: 1,
                        msgTarget : 'side'
                    }),
                    new Ext.form.TextField({
                        fieldLabel: 'Nro. Pago',
                        name: 'pago',
                        //height: 150,
                        allowBlank: true,
                        width: '90%',
                        value: 0,
                        msgTarget : 'side'
                    }),
                    new Ext.form.TextField({
                        fieldLabel: 'Nro. Secuencia',
                        name: 'secuencia',
                        //height: 150,
                        allowBlank: true,
                        width: '90%',
                        value: 0,
                        msgTarget : 'side'
                    }),*/
                ],
                autoScroll: false,
                //height: this.fheight,
                autoDestroy: true,
                autoScroll: true
            });


            // Definicion de la ventana que contiene al formulario
            this.windowDocumento = new Ext.Window({
                // id:this.idContenedor+'_W',
                title: 'Datos Documento Sigep',
                modal: true,
                width: 300,
                height: 200,
                bodyStyle: 'padding:5px;',
                layout: 'fit',
                hidden: true,
                autoScroll: false,
                maximizable: true,
                buttons: [{
                    text: 'Guardar',
                    arrowAlign: 'bottom',
                    handler: this.onSubmitDocumento,
                    argument: {
                        'news': false
                    },
                    scope: this

                },
                    {
                        text: 'Declinar',
                        handler: this.onDeclinarDocumento,
                        scope: this
                    }],
                items: this.formDocumento,
                // autoShow:true,
                autoDestroy: true,
                closeAction: 'hide'
            });
        },

        onDeclinarDocumento: function () {
            this.windowDocumento.hide();
        },

        selectButton : function(grid, rowIndex, rec) {
            let record = this.getSelectedData();
            if(record.estado_reg == 'borrador'){
                this.getBoton('sigep_verificado').setText('VERIFICAR SIGEP');
            }
        },

        deselectButton : function(grid, rowIndex, rec) {
            this.getBoton('sigep_verificado').setText('ENVIAR SIGEP');
        },

        /*==========================================================*/

        onCrearC31: function (){

        },

        onSigepWizard:function(wizard,resp){

            var rec = this.getSelectedData();

            if(rec.estado_reg == 'borrador') {
                if (rec.id_clase_comprobante == 5) {
                    if (rec.localidad == 'internacional'){
                        this.onSigepReguS(wizard, resp);
                    }else{
                        this.onSigepSip(wizard, resp);
                    }
                } else if (rec.id_clase_comprobante == 3){
                    if (rec.localidad == 'internacional'){
                        this.onSigepReguC(wizard, resp);
                    }else{
                        console.log('onSigepCIP');
                        this.onSigepCIP(wizard, resp);
                    }
                } else if (rec.id_clase_comprobante == 1 && rec.id_subsistema == 13){
                    this.onSigepPLANI(wizard, resp);
                }
            }
        },

        onSigepSip:function(wizard,resp){
            Phx.CP.loadingHide();
            var resp = this.sm.getSelected().data;
            resp.sigep_adq='vbsigepsip';
            console.log('Grid de Cbte:',resp);
            Ext.Ajax.request({
                url: '../../sis_sigep/control/SigepAdqDet/cargarSigepSip',
                params: {
                    id_int_comprobante: resp.id_int_comprobante,
                    momento: 'SIN_IMPUTACION',
                },
                success: this.successConsu,
                failure: this.failureC, //chequea si esta en verificacion presupeusto para enviar correo de transferencia
                //argument: {wizard: wizard},
                timeout: this.timeout,
                scope: this
            });
        },

        onSigepReguS:function(wizard,resp){

            var rec = this.sm.getSelected().data;
            console.log('wizardSIGP:',wizard,'respSIGP:',resp, rec);
            //if (rec.estado_reg == 'borrador') {
            //if (rec.estado_reg == 'chies') {
            resp.sigep_adq='vbsigepcontaregu';
            resp.momento='REGULARIZAS';
            Phx.CP.loadingShow();

            Ext.Ajax.request({
                url: '../../sis_sigep/control/SigepAdqDet/cargarSigepReguSip',
                params: {
                    id_proceso_wf: rec.id_proceso_wf,
                    momento: resp.momento,
                    sigep_adq: resp.sigep_adq,
                    localidad: rec.localidad,
                },
                success: this.successConsu,
                failure: this.failureC, //chequea si esta en verificacion presupeusto para enviar correo de transferencia
                argument: {wizard: wizard},
                timeout: this.timeout,
                scope: this
            });
        },

        successConsu:function(resp){
            Phx.CP.loadingShow();
            var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
            console.log('successConsu:',reg.ROOT.datos, 'rest.momento', reg.ROOT.datos.momento);
            rest = reg.ROOT.datos;
            var id = reg.ROOT.datos.id_sigep;
            this.ids = id;
            var porciones = id.split(',');
            if (rest.momento == 'REGULARIZACION' || rest.momento == 'CON_IMPUTACION' || rest.momento == 'CON_IMPUTACION_M' || rest.momento == 'SIN_IMPUTACION' || rest.momento == 'SIN_IMPUTACION_CP' || rest.momento == 'CON_IMPUTACION_V'){
                console.log('AAAAA');
                for (i=0;i<porciones.length;i++) {
                    console.log('id:', porciones[i]);
                    Ext.Ajax.request({
                        url: '../../sis_sigep/control/SigepAdq/consultaMonSigep',
                        params: {
                            id_sigep_adq: porciones[i],
                            ids: id
                        },
                        success: this.successReg,//successReg
                        failure: this.failureC, //chequea si esta en verificacion presupeusto para enviar correo de transferencia
                        //argument: {wizard: wizard},
                        timeout: this.timeout,
                        scope: this
                    });
                }
            }else{
                console.log('BBBBB');
                Ext.Ajax.request({
                    url: '../../sis_sigep/control/SigepAdq/consultaMonSigep',
                    params: {
                        id_sigep_adq: rest.id_sigep_cont
                    },
                    success: this.successReg,
                    failure: this.failureC, //chequea si esta en verificacion presupeusto para enviar correo de transferencia
                    //argument: {wizard: wizard},
                    timeout: this.timeout,
                    scope: this
                });
            }
            if(!reg.ROOT.error){
                this.reload();
            }
        },

        successReg:function(resp){
            var record = this.getSelectedData();
            Phx.CP.loadingShow();
            var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
            console.log('successReg:',reg.datos);
            var data = reg.datos;
            /*if(reg.datos.momento == 'REGULARIZACION'){
                var service_code = 'REGULARIZA';
            }else{*/
            var service_code = 'CON_IMPUTACION';
            //}
            console.log('successReg: DATOS',data, 'service_code', service_code);
            Ext.Ajax.request({
                url: '../../sis_sigep/control/SigepAdq/registrarService',
                params: {
                    list: JSON.stringify(data),
                    //id_sigep_adq: comprdevengado.id_sigep_adq,
                    service_code: service_code,
                    nro_preventivo: record.nro_preventivo
                },
                success: this.successProc,
                failure: this.failureC, //chequea si esta en verificacion presupeusto para enviar correo de transferencia
                //argument: {wizard: wizard},
                timeout: this.timeout,
                scope: this
            });
            this.reload();
        },

        successProc:function(resp){
            Phx.CP.loadingShow();
            var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
            console.log('successProc:',reg);
            var rest = reg.ROOT.datos;
            this.nro_preventivo = reg.ROOT.datos.nroPreventivo;
            var service = 'COMPRDEVEN';
            Ext.Ajax.request({
                url: '../../sis_sigep/control/SigepAdq/StatusC31',
                params: {
                    id_service_request: rest.id_service_request,
                    id_sigep_adq: rest.id_sigep_adq,
                    service_code: rest.service_code
                },
                success: this.successSta,
                failure: this.failureC, //chequea si esta en verificacion presupeusto para enviar correo de transferencia
                //argument: {wizard: wizard},
                timeout: this.timeout,
                scope: this
            });
            if(!reg.ROOT.error){
                this.reload();
            }
        },

        successSta:function(resp){

            var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
            console.log('successSta;',reg);
            var rest = reg.ROOT.datos;

            if(reg.ROOT.datos.error || reg.ROOT.datos.error == ''){
                Phx.CP.loadingHide();
                var error = reg.ROOT.datos.error;
                Ext.Msg.show({
                    title: 'ERROR SIGEP!', //<- el título del diálogo
                    msg: error, //<- El mensaje
                    icon: Ext.Msg.ERROR,// <- un ícono de error
                    width:500,// <- tamaño de ventana
                    buttons: Ext.Msg.OK, //<- Botones de SI y NO
                    fn: this.callback //<- la función que se ejecuta cuando se da clic
                });
                this.reload();
            }else {
                if(rest.service_code == 'COMPRDEVEN'){
                    Phx.CP.loadingHide();
                    Ext.Ajax.request({
                        url: '../../sis_sigep/control/SigepAdq/registrarComprometidoDevengado',
                        params: {
                            id_sigep_adq: rest.id_sigep_adq,
                            nro_comprometido: rest.nro_comprometido,
                            nro_devengado: rest.nro_devengado,
                        },
                        success: this.successWizard,
                        failure: this.failureC, //chequea si esta en verificacion presupeusto para enviar correo de transferencia
                        //argument: {wizard: wizard},
                        timeout: this.timeout,
                        scope: this
                    });

                    Ext.Msg.show({
                        title: 'REGISTRO SIGEP C31 EXITOSO!', //<- el título del diálogo
                        msg: '<p><font color="blue"><b>El Numero de Preventivo es: </font>' + this.nro_preventivo + '<p><font color="blue"><b>El Numero de Compromiso es:  </font>' + ((rest.nro_comprometido == 'undefined') ? '1' : rest.nro_comprometido) + '<p><font color="blue"><b>El numero de Devengado es: </font>' + ((rest.nro_devengado == 'undefined') ? '1' : rest.nro_devengado),//<- El mensaje
                        //msg: '<div class="x-combo-list-item"><p><b>Numero de Preventivo:</b><span style="color: red; font-weight: bold;">{'this.nro_preventivo'}</span></p></div><div class="x-combo-list-item"><p><b>Numero de Compromiso:</b><span style="color: red; font-weight: bold;">{'((rest.nro_comprometido == 'undefined')?'1':rest.nro_comprometido)'}</span></p></div><div class="x-combo-list-item"><p><b>Numero de Preventivo:</b><span style="color: red; font-weight: bold;">{'((rest.nro_devengado == 'undefined')?'1':rest.nro_devengado)'}</span></p></div>',
                        icon: Ext.Msg.INFO,// <- un ícono de error
                        width: 500,// <- tamaño de ventana
                        buttons: Ext.Msg.OK, //<- Botones de SI y NO
                        fn: this.callback //<- la función que se ejecuta cuando se da clic
                    });
                }else if(rest.service_code == 'CON_IMPUTACION_M' || rest.service_code == 'CON_IMPUTACION' || rest.service_code == 'SIN_IMPUTACION'|| rest.service_code == 'SIN_IMPUTACION_CP'|| rest.service_code == 'CON_IMPUTACION_V'){
                    if(rest.nro_preventivo == '' && rest.nro_comprometido == ''){rest.nro_preventivo = 0; rest.nro_comprometido = 0;}
                    Phx.CP.loadingHide();
                    Ext.Ajax.request({
                        url: '../../sis_sigep/control/SigepAdq/registrarResultado',
                        params: {
                            id_sigep_adq: rest.id_sigep_adq,
                            nro_preventivo: rest.nro_preventivo,
                            nro_comprometido: rest.nro_comprometido,
                            nro_devengado: rest.nro_devengado,
                        },
                        success: this.successP,
                        failure: this.failureC, //chequea si esta en verificacion presupeusto para enviar correo de transferencia
                        //argument: {wizard: wizard},
                        timeout: this.timeout,
                        scope: this
                    });

                }
            }
            this.reload();
        },

        successP:function(resp){
            Phx.CP.loadingHide();
            var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
            console.log('ResultadosP:',reg, this.ids);
            //console.log('test:', id);
            Ext.Ajax.request({
                url: '../../sis_sigep/control/SigepAdq/resultadoMsg',
                params: {
                    ids: this.ids
                },
                success: this.successResult,
                failure: this.failureC, //chequea si esta en verificacion presupeusto para enviar correo de transferencia
                //argument: {wizard: wizard},
                timeout: this.timeout,
                scope: this
            });
            this.reload();
        },

        successResult:function(resp){
            Phx.CP.loadingHide();
            var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
            console.log('ResultadosR:',reg);
            var matriz= reg.ROOT.datos.matriz_result;
            matriz = matriz.replace('{',"");
            matriz = matriz.replace('}',"");
            console.log('arraylist:',matriz);
            var porciones = matriz.split(',').map(line=>line.split(','));
            var aux ='';
            console.log('array:',porciones);
            for (i=0;i<porciones.length;i++) {
                aux = aux + porciones[i] + '</br>';
            }

            if(aux != ''){
                this.mandarDatosWizard(this.wizardfn, this.respfn, true);
            }
            console.log('string:',aux);

            Ext.Msg.show({
                title: 'REGISTRO C31 EXITOSO!', //<- el título del diálogo
                msg: '<p><font color="blue"><b>Los C31 Registrados fueron los siguientes: </font>' + '</br>'+ aux ,//<- El mensaje
                icon: Ext.Msg.INFO,// <- un ícono de error
                width: 500,// <- tamaño de ventana
                closable: false,
                modal: true,
                buttons: Ext.Msg.OK, //<- Botones de SI y NO
                fn: this.callback //<- la función que se ejecuta cuando se da clic
            });
            if(reg.ROOT.error) {
                Phx.CP.loadingHide();
                var error = reg.ROOT.datos.error;
                //Ext.Msg.alert('ERROR SIGEP!', reg.ROOT.datos.error);
                Ext.Msg.show({
                    title: 'ERROR SIGEP!', //<- el título del diálogo
                    msg: error, //<- El mensaje
                    icon: Ext.Msg.ERROR,// <- un ícono de error
                    width: 500,// <- tamaño de ventana
                    buttons: Ext.Msg.OK, //<- Botones de SI y NO
                    fn: this.callback //<- la función que se ejecuta cuando se da clic
                });
                //this.onSigepWindow();
                this.reload();
            }
        },

        onSigepReguC:function(wizard,resp){

            var rec = this.sm.getSelected().data;
            console.log('wizardSIGP:',wizard,'respSIGP:',resp, rec);
            resp.sigep_adq='vbsigepcontaregu';
            resp.momento='REGULARIZAC';
            Phx.CP.loadingShow();

            Ext.Ajax.request({
                url: '../../sis_sigep/control/SigepAdqDet/cargarSigepReguCip',
                params: {
                    id_proceso_wf: rec.id_proceso_wf,
                    momento: resp.momento,
                    sigep_adq: resp.sigep_adq,
                    localidad: rec.localidad,
                },
                success: this.successConsu,
                failure: this.failureC, //chequea si esta en verificacion presupeusto para enviar correo de transferencia
                argument: {wizard: wizard},
                timeout: this.timeout,
                scope: this
            });
        },

        onSigepCIP:function(wizard,resp){
            var rec = this.sm.getSelected().data;
            console.log('wizardSIGP:',wizard,'respSIGP:',resp, rec);
            resp.sigep_adq='vbsigepconta';
            resp.momento='CON_IMPUTACION';
            Phx.CP.loadingShow();

            Ext.Ajax.request({
                url: '../../sis_sigep/control/SigepAdqDet/cargarSigepCip',
                params: {
                    id_proceso_wf: rec.id_proceso_wf,
                    momento: resp.momento,
                    sigep_adq: resp.sigep_adq,
                },
                success: this.successConsu, //successConsu
                failure: this.failureCheck, //chequea si esta en verificacion presupeusto para enviar correo de transferencia
                argument: {wizard: wizard},
                timeout: this.timeout,
                scope: this
            });
        },

        successDel:function(resp){
            Phx.CP.loadingHide();
            var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
            console.log('successDel:',reg);
            rest = reg.datos;
            //var porciones = id.split(',');
            for (i=0;i<rest.length;i++) {
                console.log('id:', rest[i]);
            }
            if(!reg.ROOT.error){
                this.reload();
            }
        },

        onSigepPLANI:function(wizard,resp){

            var rec = this.sm.getSelected().data;
            console.log('wizardSIGP:',wizard,'respSIGP:',resp, rec);
            //if (rec.estado_reg == 'chies') {
            resp.sigep_adq='vbsigepconta';
            resp.momento='CON_IMPUTACION';
            Phx.CP.loadingShow();

            Ext.Ajax.request({
                url: '../../sis_sigep/control/SigepAdqDet/cargarSigepPlani',
                params: {
                    id_proceso_wf: rec.id_proceso_wf,
                    momento: resp.momento,
                    sigep_adq: resp.sigep_adq,
                },
                success: this.successConsu,
                failure: this.failureCheck, //chequea si esta en verificacion presupeusto para enviar correo de transferencia
                argument: {wizard: wizard},
                timeout: this.timeout,
                scope: this
            });
        },

        /*==========================================================*/

        /*gruposBarraTareas: [
            {name:  'borrador', title: '<h1 style="text-align: center; color: #FF8F85;">BORRADOR</h1>',grupo: 0, height: 0} ,
            {name: 'contabilidad', title: '<h1 style="text-align: center; color: #4682B4;">VB CONTABILIDAD</h1>', grupo: 1, height: 0},
            {name: 'finanzas', title: '<h1 style="text-align: center; color: #00B167;">VB FINANZAS</h1>', grupo: 2, height: 0}
        ],*/

        bnewGroups:[0],
        beditGroups:[0],
        bdelGroups:[0],
        bactGroups:[0,1,2],
        bexcelGroups:[0,1,2],

        actualizarSegunTab: function(name, indice){

            this.store.baseParams.estado_cbte = name;

            if(this.cmbGestion.getValue() != '' && this.cmbDepto.getValue() != '') {
                this.load({params: {start: 0, limit: 50}});
            }

        },


        onButtonEdit:function(){
            this.swButton = 'EDIT';
            var rec = this.sm.getSelected().data;

            this.cmpFecha.enable();

            Phx.vista.IntCBTDiarioRegAuxSigep.superclass.onButtonEdit.call(this);
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

            Phx.vista.IntCBTDiarioRegAuxSigep.superclass.onButtonNew.call(this);
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
            var tb = Phx.vista.IntCBTDiarioRegAuxSigep.superclass.preparaMenu.call(this);
            var rec = this.sm.getSelected();
            if(rec.data.tipo_reg == 'summary'){
                this.getBoton('btnSwEditble').disable();
                this.getBoton('sig_estado').disable();
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
                this.getBoton('sig_estado').enable();
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
                this.getBoton('sig_estado').disable();
            }
            //

            this.getBoton('sigep_verificado').enable();
            this.getBoton('docCargaSigep').enable();

            return tb;
        },
        liberaMenu : function() {
            var tb = Phx.vista.IntCBTDiarioRegAuxSigep.superclass.liberaMenu.call(this);

            this.getBoton('sig_estado').disable();
            this.getBoton('btnImprimir').disable();
            this.getBoton('btnRelDev').disable();
            this.getBoton('btnIgualarCbte').disable();
            this.getBoton('btnDocCmpVnt').disable();
            this.getBoton('chkpresupuesto').disable();
            this.getBoton('btnChequeoDocumentosWf').disable();
            this.getBoton('diagrama_gantt').disable();
            this.getBoton('btnObs').disable()
            this.getBoton('sigep_verificado').disable();
            this.getBoton('docCargaSigep').disable();


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