<?php
/**
 *@package      pXP
 *@file         EntregaRegionalesExt.php
 *@author       (franklin.espinoza)
 *@date         21-09-2020 10:50:19
 *@description  Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
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

    /*tipo entrega*/
    .sigep_una_cg {
        background-color: #bdffb2;//#e2ffe2
        color: #090;
    }

    .sigep_mas_cg{
        background-color: #EAA8A8;//#ffe2e2
        color: #900;
    }


</style>

<script>
    Phx.vista.EntregaRegionalesExt=Ext.extend(Phx.gridInterfaz,{


        viewConfig: {

            autoFill: true,
            getRowClass: function (record) {

                if (record.data.tipo == 'sigep_una_cg') {
                    return 'sigep_una_cg';

                } else if (record.data.tipo == 'sigep_mas_cg') {
                        return 'sigep_mas_cg';

                } else {
                    return '';
                }
            }

        },

        constructor:function(config){

            this.maestro=config.maestro;
            //llama al constructor de la clase padre
            this.initButtons = [this.cmbDepto];
            Phx.vista.EntregaRegionalesExt.superclass.constructor.call(this,config);
            this.store.baseParams.pes_estado = ' ';
            this.store.baseParams.estado_entrega = 'borrador_elaborado';
            this.addButton('ant_estado',{
                grupo: [0,1,2,3,4,5],
                argument: {estado: 'anterior'},
                text: 'Anterior',
                iconCls: 'batras',
                disabled: true,
                /*hidden:true,*/
                handler: this.antEstado,
                tooltip: '<b>Volver al Anterior Estado</b>'
            });

            this.addButton('sig_estado', {
                text : 'Siguiente',
                iconCls : 'badelante',
                disabled : true,
                handler : this.sigEstado,
                tooltip: '<b>Pasar al Siguiente Estado</b>'
            });

            this.addButtonIndex(7,'sigep_ext_entrega',
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
                    menu: [
                        {
                            text: 'Editar C31',
                            iconCls: 'bnew-sigep',
                            argument: {
                                'news': true,
                                def: 'csv'
                            },
                            handler: this.onEditC31,
                            scope: this
                        }
                    ]
                }
            );

            this.addBotonesGantt();

            this.addButton('btnChequeoDocumentosWf',{
                text: 'Documentos',
                grupo: [0,1,2,3,4,5],
                iconCls: 'bchecklist',
                disabled: true,
                handler: this.loadCheckDocumentosRecWf,
                tooltip: '<b>Documentos del Reclamo</b><br/>Subir los documetos requeridos en el Reclamo seleccionado.'
            });
            this.addButton('fin_entrega', {
                text : 'Registrar Entrega',
                iconCls : 'btag_accept',
                disabled : true,
                handler : this.cambiarEstado,
                tooltip: '<b>Finaliza la entrega, defini el nro de Cbte relacionado en SIGMA/SIGEP/OTRO</b>'
            });

            //Botón para Imprimir el Comprobante
            this.addButton('btnImprimir', {
                text : 'Imprimir',
                iconCls : 'bprint',
                disabled : true,
                handler : this.imprimirCbte,
                tooltip : '<b>Imprimir Reporte de Entrega</b><br/>Imprime un detalle de las factidas presupeustarias relacioandas a la entrega'
            });

            this.addButton('btnObs', {
                text: 'Obs Wf',
                grupo: [0, 1, 2, 3],
                iconCls: 'bchecklist',
                disabled: true,
                handler: this.onOpenObs,
                tooltip: '<b>Observaciones</b><br/><b>Observaciones del WF</b>'
            });



            this.init();

            this.bloquearOrdenamientoGrid();
            this.cmbDepto.on('clearcmb', function() {
                this.DisableSelect();
                this.store.removeAll();
            }, this);

            this.cmbDepto.on('valid', function() {
                this.capturaFiltros();
            }, this);

        },

        onEditC31 : function(){
            Phx.CP.loadingShow();
            let record = this.getSelectedData();
            //console.log('record', record, 'wizard', wizard, 'response', response);
            Ext.Ajax.request({
                url:'../../sis_sigep/control/SigepAdq/setupSigepProcess',
                params:{
                    id_service_request : record.id_service_request,
                    estado_reg : record.estado,
                    json_data : record.glosa,
                    clase_comprobante : record.id_clase_comprobante
                },
                success: function (resp) {
                    var reg =  Ext.decode(Ext.util.Format.trim(resp.responseText));
                    var datos = reg.ROOT.datos;
                    Phx.CP.loadingHide();
                    console.log('datos fuera',datos);
                    if(datos.process){
                        console.log('datos',datos);
                        Ext.Msg.show({
                            title: 'Estado SIGEP',
                            msg: '<b>Estimado Funcionario: '+'\n'+'La información se guardo satisfactoriamente en el SIGEP.</b>',
                            buttons: Ext.Msg.OK,
                            width: 512,
                            icon: Ext.Msg.INFO
                        });
                    }else{
                        Phx.CP.loadingHide();

                        Ext.Msg.show({
                            title: 'Estado SIGEP',
                            msg: '<b>Estimado Funcionario: '+'\n'+'Hubo algunos inconvenientes al guardar información en el SIGEP.</b>',
                            buttons: Ext.Msg.OK,
                            width: 512,
                            icon: Ext.Msg.INFO
                        });
                    }
                },
                failure: this.conexionFailure,
                timeout: this.timeout,
                scope:this
            });

        },



        cmbDepto : new Ext.form.AwesomeCombo({
            name : 'id_depto_ent_ext',
            fieldLabel : 'Depto',
            typeAhead : false,
            forceSelection : true,
            allowBlank : false,
            disableSearchButton : true,
            emptyText : 'Depto Contable',
            store : new Ext.data.JsonStore({
                url : '../../sis_parametros/control/Depto/listarDeptoFiltradoDeptoUsuario',//../../sis_parametros/control/Depto/listarDeptoFiltradoPrioridadEXT
                id : 'id_depto',
                root : 'datos',
                sortInfo : {
                    field : 'deppto.nombre',
                    direction : 'ASC'
                },
                totalProperty : 'total',
                fields : ['id_depto', 'nombre', 'codigo'],
                // turn on remote sorting
                remoteSort : true,
                baseParams : {
                    par_filtro : 'deppto.nombre#deppto.codigo',
                    estado : 'activo',
                    codigo_subsistema : 'CONTA'
                }
            }),
            valueField : 'id_depto',
            displayField : 'nombre',
            hiddenName : 'id_depto',
            enableMultiSelect : false,
            triggerAction : 'all',
            lazyRender : true,
            mode : 'remote',
            pageSize : 20,
            queryDelay : 200,
            anchor : '80%',
            listWidth : '280',
            resizable : true,
            minChars : 2
        }),

        Atributos:[
            {

                config:{
                    name: 'id_entrega',
                    fieldLabel: 'Nro',
                    allowBlank: false,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength:200,
                    inputType: 'hidden'
                },
                type:'TextField',
                filters:{pfiltro:'ent.id_entrega',type:'string'},
                id_grupo:1,
                grid:true,
                form:true
            },
            {
                config:{
                    name: 'nro_tramite',
                    fieldLabel: 'Nro. Tramite',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 150,
                    maxLength:100

                },
                type:'TextField',
                filters:{pfiltro:'com.nro_tramite',type:'string'},
                id_grupo:1,
                grid:true,
                form:false,
                bottom_filter:true
            },
            {
                config:{
                    name: 'c31',
                    fieldLabel: 'Nro C31',
                    allowBlank: false,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength:200
                },
                type:'TextField',
                filters:{pfiltro:'ent.c31',type:'string'},
                id_grupo:1,
                grid:true,
                form:false,//true
                bottom_filter:true
            },
            {
                config:{
                    name: 'fecha_c31',
                    fieldLabel: 'Fecha C31',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    format: 'd/m/Y',
                    renderer:function (value,p,record){return value?value.dateFormat('d/m/Y'):''}
                },
                type:'DateField',
                filters:{pfiltro:'ent.fecha_c31',type:'date'},
                id_grupo:1,
                grid:true,
                form:false//true
            },

            {
                config: {
                    name: 'glosa',
                    fieldLabel: 'Glosa',
                    allowBlank: false,
                    anchor: '90%',
                    gwidth: 250,
                    maxLength: 1500,
                    msgTarget: 'side'
                },
                type: 'TextArea',
                filters: {
                    pfiltro: 'ent.glosa',
                    type: 'string'
                },
                id_grupo: 0,
                bottom_filtro: true,
                egrid: true,
                grid: true,
                form: true
            },

            {
                config:{
                    name: 'estado',
                    fieldLabel: 'Estado',
                    allowBlank: false,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength:20
                },
                type:'TextField',
                filters:{pfiltro:'ent.estado',type:'string'},
                id_grupo:1,
                grid:true,
                form:false,//true
                bottom_filter:true
            },
            {
                config:{
                    name: 'desc_moneda',
                    fieldLabel: 'Moneda',
                    gwidth: 70
                },
                type:'Field',
                filters:{pfiltro:'com.desc_moneda',type:'string'},
                id_grupo:1,
                grid:true,
                form:false
            },
            {
                config:{
                    name: 'monto',
                    fieldLabel: 'Monto',
                    gwidth: 70,
                    renderer:function (value,p,record){
                        return  String.format(Ext.util.Format.number(value,'0.000,00/i'));
                    }
                },
                type:'Field',
                filters:{pfiltro:'pp.monto',type:'numeric'},
                id_grupo:1,
                grid:true,
                form:false
            },


            {
                config:{
                    name: 'estado_reg',
                    fieldLabel: 'Estado Reg.',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength:10
                },
                type:'TextField',
                filters:{pfiltro:'ent.estado_reg',type:'string'},
                id_grupo:1,
                grid:true,
                form:false
            },
            {
                config:{
                    name: 'id_usuario_ai',
                    fieldLabel: '',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength:4
                },
                type:'Field',
                filters:{pfiltro:'ent.id_usuario_ai',type:'numeric'},
                id_grupo:1,
                grid:false,
                form:false
            },
            {
                config:{
                    name: 'usuario_ai',
                    fieldLabel: 'Funcionaro AI',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength:300
                },
                type:'TextField',
                filters:{pfiltro:'ent.usuario_ai',type:'string'},
                id_grupo:1,
                grid:true,
                form:false
            },
            {
                config:{
                    name: 'fecha_reg',
                    fieldLabel: 'Fecha creación',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    format: 'd/m/Y',
                    renderer:function (value,p,record){return value?value.dateFormat('d/m/Y H:i:s'):''}
                },
                type:'DateField',
                filters:{pfiltro:'ent.fecha_reg',type:'date'},
                id_grupo:1,
                grid:true,
                form:false
            },
            {
                config:{
                    name: 'usr_reg',
                    fieldLabel: 'Creado por',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength:4
                },
                type:'Field',
                filters:{pfiltro:'usu1.cuenta',type:'string'},
                id_grupo:1,
                grid:true,
                form:false
            },
            {
                config:{
                    name: 'fecha_mod',
                    fieldLabel: 'Fecha Modif.',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    format: 'd/m/Y',
                    renderer:function (value,p,record){return value?value.dateFormat('d/m/Y H:i:s'):''}
                },
                type:'DateField',
                filters:{pfiltro:'ent.fecha_mod',type:'date'},
                id_grupo:1,
                grid:true,
                form:false
            },
            {
                config:{
                    name: 'usr_mod',
                    fieldLabel: 'Modificado por',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength:4
                },
                type:'Field',
                filters:{pfiltro:'usu2.cuenta',type:'string'},
                id_grupo:1,
                grid:true,
                form:false
            }

        ],
        tam_pag:50,
        title:'Entrega Regionales Ext',
        ActSave:'../../sis_contabilidad/control/Entrega/insertarEntrega',
        ActDel:'../../sis_contabilidad/control/Entrega/eliminarEntrega',
        ActList:'../../sis_contabilidad/control/Entrega/listarEntrega',
        id_store:'id_entrega',
        fields: [
            {name:'id_entrega', type: 'numeric'},
            {name:'fecha_c31', type: 'date',dateFormat:'Y-m-d'},
            {name:'c31', type: 'string'},
            {name:'estado', type: 'string'},
            {name:'estado_reg', type: 'string'},
            {name:'id_usuario_ai', type: 'numeric'},
            {name:'usuario_ai', type: 'string'},
            {name:'fecha_reg', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
            {name:'id_usuario_reg', type: 'numeric'},
            {name:'fecha_mod', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
            {name:'id_usuario_mod', type: 'numeric'},
            {name:'usr_reg', type: 'string'},
            {name:'usr_mod', type: 'string'},'id_depto_conta',
            {name:'id_estado_wf', type: 'numeric'},
            {name:'id_proceso_wf', type: 'numeric'},
            {name:'nro_tramite', type: 'string'},
            'desc_moneda', 'monto',
            'tipo_cambio_2',
            'fecha',
            'id_clase_comprobante',
            'id_service_request',
            'localidad',
            'glosa',
            'tipo',
            'validado'

        ],
        sortInfo:{
            field: 'id_entrega',
            direction: 'DESC'
        },
        south : {
            url : '../../../sis_contabilidad/vista/entrega_det/EntregaDet.php',
            title : 'Detalle',
            height : '50%', //altura de la ventana hijo
            cls : 'EntregaDet'
        },
        /*================================= BEGIN SIGUIENTE ESTADO =======================================*/
        sigEstado : function () {
            var rec = this.sm.getSelected();
            console.log('rec',rec.data);
            if(rec.data.validado == 'no'){
                this.validarComprobantesERP();
            }else{
                this.mostrarWizard(rec, true);
            }
        },

        validarComprobantesERP : function (){

            var record = this.getSelectedData();

            Ext.Ajax.request({
                url:'../../sis_contabilidad/control/Entrega/validarComprobantesERP',
                params:{
                    id_entrega : record.id_entrega
                },
                success: this.successVerificarCBTES,
                failure: this.conexionFailure,
                timeout: this.timeout,
                scope:this
            });
        },

        successVerificarCBTES: function(resp){
            var rec = this.sm.getSelected();
            this.mostrarWizard(rec, true);
        },

        mostrarWizard : function (rec, validar_doc) {
            var configExtra = [], obsValorInicial;

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

        /*=================================BEGIN VERIFICAR=======================================*/
        verificarProcesoSigep : function (wizard, response){
            var record = this.getSelectedData();
            console.log('record', record, 'wizard', wizard, 'response', response);
            Ext.Ajax.request({
                url:'../../sis_sigep/control/SigepAdq/readyProcesoSigep',
                params:{
                    id_service_request : record.id_service_request,
                    estado_reg : record.estado,
                    direction : 'next',
                    momento : 'pass'
                },
                success: function (resp) {
                    var reg =  Ext.decode(Ext.util.Format.trim(resp.responseText));
                    var datos = reg.ROOT.datos;
                    console.log('verificarProcesoSigep',datos);
                    if(datos.process){

                        Phx.CP.loadingHide();
                        Ext.Ajax.request({
                            url:'../../sis_contabilidad/control/Entrega/siguienteEstado',
                            params:{

                                id_proceso_wf_act:  response.id_proceso_wf_act,
                                id_estado_wf_act:   response.id_estado_wf_act,
                                id_tipo_estado:     response.id_tipo_estado,
                                id_funcionario_wf:  response.id_funcionario_wf,
                                id_depto_wf:        response.id_depto_wf,
                                obs:                response.obs,
                                json_procesos:      Ext.util.JSON.encode(response.procesos)
                            },
                            success:this.successWizard,
                            failure: this.conexionFailure,
                            argument: {wizard: wizard, id_proceso_wf: response.id_proceso_wf_act, resp: response},
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
        /*=================================END VERIFICAR=======================================*/

        mandarDatosWizard: function (wizard, resp, validar_doc) {
            var record = this.getSelectedData();
            Phx.CP.loadingShow();

            if(record.estado == 'borrador'){
                this.onSigepWizard(wizard, resp);
            }else if(record.estado == 'elaborado'){
                this.verificarProcesoSigep(wizard, resp);
            }

            /*Ext.Ajax.request({
                url:'../../sis_contabilidad/control/Entrega/siguienteEstado',
                params:{

                    id_proceso_wf_act:  resp.id_proceso_wf_act,
                    id_estado_wf_act:   resp.id_estado_wf_act,
                    id_tipo_estado:     resp.id_tipo_estado,
                    id_funcionario_wf:  resp.id_funcionario_wf,
                    id_depto_wf:        resp.id_depto_wf,
                    obs:                resp.obs,
                    json_procesos:      Ext.util.JSON.encode(resp.procesos)
                },
                success:this.successWizard,
                failure: this.conexionFailure,
                argument: {wizard: wizard, id_proceso_wf: resp.id_proceso_wf_act, resp: resp},
                timeout:this.timeout,
                scope:this
            });*/

        },

        successWizard:function(resp){
            Phx.CP.loadingHide();
            resp.argument.wizard.panel.destroy();
            this.reload();
        },

        onSigepWizard : function(wizard,resp){

            var rec = this.getSelectedData();

            if(rec.estado == 'borrador') {
                if (rec.id_clase_comprobante == 5) {
                    if (rec.localidad == 'internacional'){
                        this.onSigepReguS(wizard, resp, 'REGULARIZAS');
                    }else{
                        this.onSigepSip(wizard, resp);
                    }
                }else if (rec.id_clase_comprobante == 3){
                    if (rec.localidad == 'internacional'){
                        this.onSigepReguC(wizard, resp, 'REGULARIZAC');
                    }else{
                        console.log('onSigepCIP');
                        this.onSigepCIP(wizard, resp, 'CON_IMPUTACION');
                    }
                }else if (rec.id_clase_comprobante == 1){
                    if (rec.localidad == 'internacional'){
                        this.onSigepReguC(wizard, resp, 'REGULARIZAC');
                    }else{
                        console.log('onSigepCIP');
                        this.onSigepCIP(wizard, resp, 'CON_IMPUTACION');
                    }
                }
            }
        },

        /*================================= END SIGUIENTE ESTADO =======================================*/

        /*================================= BEGIN PROCESOS CIP =======================================*/
        onSigepCIP:function(wizard,resp, momento){
            var rec = this.sm.getSelected().data;
            console.log('ENTREGA SIGEP:',wizard,'respSIGP:',resp, rec, 'MOMENTO', momento);
            resp.sigep_adq='vbsigepconta';
            Phx.CP.loadingShow();

            Ext.Ajax.request({
                url: '../../sis_sigep/control/SigepAdqDet/cargarEntregaSigepCip',
                params: {
                    id_proceso_wf: rec.id_proceso_wf,
                    momento: momento,
                    sigep_adq: resp.sigep_adq,
                    localidad: rec.localidad
                },
                success: this.successConsu, //successConsu
                failure: this.failureCheck, //chequea si esta en verificacion presupeusto para enviar correo de transferencia
                argument: {wizard: wizard, resp : resp, momento: momento},
                timeout: this.timeout,
                scope: this
            });
        },
        /*================================= END PROCESOS CIP =======================================*/

        /*================================= BEGIN FUNCIONES REGULARICACION PROCESO SIGEP =======================================*/

        onSigepReguS:function(wizard, resp, momento){

            var rec = this.getSelectedData();
            console.log('wizardSIGP:',wizard,'respSIGP:',resp, rec);

            //resp.momento='REGULARIZAS';
            Phx.CP.loadingShow();

            Ext.Ajax.request({
                url: '../../sis_sigep/control/SigepAdqDet/cargarEntregaSigepReguSip',
                params: {
                    id_proceso_wf: rec.id_proceso_wf,
                    momento: momento,
                    localidad: rec.localidad,
                },
                success: this.successConsu,
                failure: this.failureC, //chequea si esta en verificacion presupeusto para enviar correo de transferencia
                argument: {wizard: wizard, resp : resp, momento: momento},
                timeout: this.timeout,
                scope: this
            });
        },

        onSigepReguC:function(wizard, resp, momento){

            var rec = this.getSelectedData();
            console.log('wizardSIGP ENTREGA:',wizard,'respSIGP:',resp, rec);
            resp.sigep_adq='vbsigepcontaregu';
            resp.momento = momento;
            Phx.CP.loadingShow();

            Ext.Ajax.request({
                url: '../../sis_sigep/control/SigepAdqDet/cargarEntregaSigepReguCip',
                params: {
                    id_proceso_wf : rec.id_proceso_wf,
                    momento : resp.momento,
                    sigep_adq : resp.sigep_adq,
                    localidad : rec.localidad
                },
                success: this.successConsu,
                failure: this.failureC, //chequea si esta en verificacion presupeusto para enviar correo de transferencia
                argument: {wizard: wizard, resp : resp, momento: momento},
                timeout: this.timeout,
                scope: this
            });
        },

        successConsu:function(resp, opt){
            Phx.CP.loadingShow();
            var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
            console.log('successConsu:',reg.ROOT.datos, 'option', opt.argument );
            rest = reg.ROOT.datos;
            var id = reg.ROOT.datos.id_sigep;
            this.ids = id;
            var porciones = id.split(',');
            if( opt.argument.momento == 'REGULARIZAC' || opt.argument.momento == 'REGULARIZAS' || opt.argument.momento == 'CON_IMPUTACION' ){
                for (let i=0 ; i < porciones.length ; i++) {
                    console.log('identify:', porciones[i]);
                    Ext.Ajax.request({
                        url: '../../sis_sigep/control/SigepAdq/consultaMonSigep',
                        params: {
                            id_sigep_adq: porciones[i],
                            ids: id
                        },
                        success: this.successReg,//successReg
                        failure: this.failureC, //chequea si esta en verificacion presupeusto para enviar correo de transferencia
                        argument: {wizard : opt.argument.wizard, resp : opt.argument.resp, momento : opt.argument.momento},
                        timeout: this.timeout,
                        scope: this
                    });
                }
            }else{
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

        successReg:function(resp, opt){

            var record = this.getSelectedData();
            Phx.CP.loadingShow();
            var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
            var data = reg.datos;
            let service_code = '';
            console.log('successReg 2020:===================================',data, '========================== OPT', opt);
            if(opt.argument.momento == 'REGULARIZAC') {
                service_code = 'REGULARIZAC';
            }else if( opt.argument.momento == 'REGULARIZAS' ){
                service_code = 'REGULARIZAS';
            }else if( opt.argument.momento == 'CON_IMPUTACION' ){
                service_code = 'CON_IMPUTACION';
            }
            /*else{
                service_code = 'CON_IMPUTACION_V';
            }
            //console.log('successReg: DATOS',data[0],'momento',data.momento,  'service_code', service_code);*/
            Ext.Ajax.request({
                url: '../../sis_sigep/control/SigepAdq/registrarService',
                params: {
                    list : JSON.stringify(data),
                    service_code : service_code,
                    estado_c31 : record.estado_reg,
                    momento : 'new'
                },
                success: this.successProc,
                failure: this.failureC, //chequea si esta en verificacion presupeusto para enviar correo de transferencia
                argument: {wizard : opt.argument.wizard, resp : opt.argument.resp, momento: opt.argument.momento},
                timeout: this.timeout,
                scope: this
            });
            this.reload();
        },

        successProc:function(resp, opt){

            var record = this.getSelectedData();

            Phx.CP.loadingShow();
            var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));

            var rest = reg.ROOT.datos;
            this.nro_preventivo = reg.ROOT.datos.nroPreventivo;
            var service = 'COMPRDEVEN';

            console.log('successProc 2020:===================================',rest, '========================== OPT', opt);
            Ext.Ajax.request({
                url: '../../sis_sigep/control/SigepAdq/StatusC31',
                params: {
                    id_service_request: rest.id_service_request,
                    id_sigep_adq: rest.id_sigep_adq,
                    service_code: rest.service_code,
                    id_entrega : record.id_entrega,
                    tipo : 'entrega'
                },
                success: this.successSta,
                failure: this.failureC, //chequea si esta en verificacion presupeusto para enviar correo de transferencia
                argument: {wizard : opt.argument.wizard, resp : opt.argument.resp, momento: opt.argument.momento},
                timeout: this.timeout,
                scope: this
            });
            if(!reg.ROOT.error){
                this.reload();
            }
        },

        successSta:function(resp, opt){

            var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));

            var rest = reg.ROOT.datos;

            console.log('successSta 2020: ==========================',rest);

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
                }else if( opt.argument.momento == 'REGULARIZAC' || opt.argument.momento == 'REGULARIZAS' || opt.argument.momento == 'CON_IMPUTACION' ){
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
                        argument: {wizard : opt.argument.wizard, resp : opt.argument.resp, momento: opt.argument.momento},
                        timeout: this.timeout,
                        scope: this
                    });

                }
            }
            this.reload();
        },

        successP:function(resp, opt){
            Phx.CP.loadingHide();
            var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
            console.log('ResultadosP 2020: ==============================================',reg, this.ids);
            //console.log('test:', id);
            Ext.Ajax.request({
                url: '../../sis_sigep/control/SigepAdq/resultadoMsg',
                params: {
                    ids: this.ids
                },
                success: this.successResult,
                failure: this.failureC, //chequea si esta en verificacion presupeusto para enviar correo de transferencia
                argument: {wizard : opt.argument.wizard, resp : opt.argument.resp, momento: opt.argument.momento},
                timeout: this.timeout,
                scope: this
            });
            this.reload();
        },

        successResult:function(resp, opt){

            var record =  this.getSelectedData();

            Phx.CP.loadingHide();
            var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
            console.log('ResultadosR: ==============================',reg);
            var matriz= reg.ROOT.datos.matriz_result;
            matriz = matriz.replace('{',"");
            matriz = matriz.replace('}',"");
            console.log('arraylist: =============================',matriz);
            var porciones = matriz.split(',').map(line=>line.split(','));
            var aux ='';
            console.log('array: ===================================',porciones);
            for (i=0;i<porciones.length;i++) {
                aux = aux + porciones[i] + '</br>';
            }

            if(aux != ''){

                //this.mandarDatosWizard(this.wizardfn, this.respfn, true);
            }

            /*====================================BEGIN CAMBIO ESTADO====================================*/
            console.log('CAMBIO DE ESTADO');

            Ext.Ajax.request({
                url:'../../sis_contabilidad/control/Entrega/siguienteEstado',
                params:{

                    id_proceso_wf_act:  opt.argument.resp.id_proceso_wf_act,
                    id_estado_wf_act:   opt.argument.resp.id_estado_wf_act,
                    id_tipo_estado:     opt.argument.resp.id_tipo_estado,
                    id_funcionario_wf:  opt.argument.resp.id_funcionario_wf,
                    id_depto_wf:        opt.argument.resp.id_depto_wf,
                    obs:                opt.argument.resp.obs,
                    json_procesos:      Ext.util.JSON.encode(opt.argument.resp.procesos)
                },
                success:this.successWizard,
                failure: this.conexionFailure,
                argument: {wizard: opt.argument.wizard, id_proceso_wf: opt.argument.resp.id_proceso_wf_act, resp: opt.argument.resp},
                timeout:this.timeout,
                scope:this
            });


            /*====================================END CAMBIO ESTADO====================================*/

            console.log('string: =====================',aux);

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

        /*================================= END FUNCIONES REGULARICACION PROCESO SIGEP =======================================*/

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

        onAntEstado: function(wizard,resp){

            Phx.CP.loadingShow();

            this.revertirProcesoSigep(wizard,resp);
        },

        revertirProcesoSigep : function (wizard,response){
            var record = this.getSelectedData();
            console.log('record', record);
            Ext.Ajax.request({
                url:'../../sis_sigep/control/SigepAdq/revertirProcesoSigep',
                params:{
                    id_service_request : record.id_service_request,
                    estado_reg : record.estado
                },
                success: function (resp) {
                    var reg =  Ext.decode(Ext.util.Format.trim(resp.responseText));
                    var datos = reg.ROOT.datos;
                    console.log('revertirProcesoEntregaSigep',datos);
                    if(datos.process){

                        Phx.CP.loadingHide();
                        Ext.Ajax.request({
                            url:'../../sis_contabilidad/control/Entrega/retrosederEstado',
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



        successEstadoSinc:function(resp){
            Phx.CP.loadingHide();
            resp.argument.wizard.panel.destroy();
            this.reload();
        },

        /*===================================================END ESTADO ANTERIOR======================================================*/

        //para retroceder de estado
        cambiarEstado:function(res){
            var rec=this.sm.getSelected(),
                obsValorInicial;
            Phx.CP.loadWindows('../../../sis_contabilidad/vista/entrega/EntregaForm.php',
                'Registro Entrega',
                {   modal: true,
                    width: '60%',
                    height: '30%'
                },
                {    data: rec.data }, this.idContenedor,'EntregaForm',
                {
                    config:[{
                        event:'beforesave',
                        delegate: this.onCambiartEstado,
                    }
                    ],
                    scope:this
                });
        },

        onCambiartEstado: function(wizard,resp){
            Phx.CP.loadingShow();
            var operacion = 'cambiar';
            Ext.Ajax.request({
                url:'../../sis_contabilidad/control/Entrega/cambiarEstado',
                params:{
                    id_entrega: resp.id_entrega,
                    c31:  resp.c31,
                    fecha_c31:  resp.fecha_c31,
                    id_tipo_relacion_comprobante:  resp.id_tipo_relacion_comprobante,
                    obs: resp.obs
                },
                argument: { wizard: wizard },
                success: this.successEstadoSinc,
                failure: this.conexionFailure,
                timeout: this.timeout,
                scope: this
            });

        },
        successEstadoSinc:function(resp){
            Phx.CP.loadingHide();
            resp.argument.wizard.panel.destroy()
            this.reload();
        },
        onOpenObs: function () {
            var rec = this.sm.getSelected();

            var data = {
                id_proceso_wf: rec.data.id_proceso_wf,
                id_estado_wf: rec.data.id_estado_wf,
                num_tramite: rec.data.num_tramite
            }

            Phx.CP.loadWindows('../../../sis_workflow/vista/obs/Obs.php',
                'Observaciones del WF',
                {
                    width: '80%',
                    height: '70%'
                },
                data,
                this.idContenedor,
                'Obs'
            )
        },

        preparaMenu : function(n) {
            var tb = Phx.vista.EntregaRegionalesExt.superclass.preparaMenu.call(this,n);
            var rec=this.sm.getSelected();

            if(rec.data.estado == 'finalizado'){
                this.getBoton('sig_estado').disable();

            } else{
                this.getBoton('sig_estado').enable();
                this.getBoton('ant_estado').enable();
            }

            this.getBoton('btnImprimir').enable();
            this.getBoton('diagrama_gantt').enable();
            this.getBoton('btnChequeoDocumentosWf').enable();
            this.getBoton('fin_entrega').enable();
            this.getBoton('btnObs').enable();

            this.getBoton('sigep_ext_entrega').enable();
            return tb;
        },
        liberaMenu : function() {
            var tb = Phx.vista.EntregaRegionalesExt.superclass.liberaMenu.call(this);
            this.getBoton('sig_estado').disable();
            this.getBoton('btnImprimir').disable();
            this.getBoton('fin_entrega').disable();
            this.getBoton('btnChequeoDocumentosWf').disable();
            this.getBoton('ant_estado').disable();
            this.getBoton('diagrama_gantt').disable();
            this.getBoton('btnObs').disable();
            this.getBoton('sigep_ext_entrega').disable();

        },
        capturaFiltros : function(combo, record, index) {
            this.desbloquearOrdenamientoGrid();
            this.store.baseParams.id_depto = this.cmbDepto.getValue();
            this.store.baseParams.nombreVista = this.nombreVista
            this.load();
        },

        validarFiltros : function() {
            if (this.cmbDepto.getValue() != '' ) {
                return true;
            } else {
                return false;
            }
        },
        onButtonAct : function() {
            if (!this.validarFiltros()) {
                alert('Especifique los filtros antes')
            }
            else{
                this.capturaFiltros();
            }
        },
        imprimirCbte : function() {
            var rec = this.sm.getSelected();
            var data = rec.data;
            console.log('llgeam', data);
            if (data) {
                Phx.CP.loadingShow();
                Ext.Ajax.request({
                    url : '../../sis_contabilidad/control/Entrega/reporteEntrega',
                    params : {
                        'id_entrega' : data.id_entrega,
                        'nro_tramite' : data.nro_tramite,
                        'tipo_cambio_2' : data.tipo_cambio_2,
                        'fecha' : data.fecha
                    },
                    success : this.successExport,
                    failure : this.conexionFailure,
                    timeout : this.timeout,
                    scope : this
                });
            }

        },
        loadCheckDocumentosRecWf:function() {
            var rec=this.sm.getSelected();
            rec.data.nombreVista = this.nombreVista;
            Phx.CP.loadWindows('../../../sis_workflow/vista/documento_wf/DocumentoWf.php',
                'Chequear documento del WF',
                {
                    width:'90%',
                    height:500
                },
                rec.data,
                this.idContenedor,
                'DocumentoWf'
            )
        },
        addBotonesGantt: function() {
            this.menuAdqGantt = new Ext.Toolbar.SplitButton({
                id: 'b-diagrama_gantt-' + this.idContenedor,
                text: 'Gantt',
                disabled: true,
                grupo:[0,1,2,3],
                iconCls : 'bgantt',
                handler:this.diagramGanttDinamico,
                scope: this,
                menu:{
                    items: [{
                        id:'b-gantti-' + this.idContenedor,
                        text: 'Gantt Imagen',
                        tooltip: '<b>Muestra un reporte gantt en formato de imagen</b>',
                        handler:this.diagramGantt,
                        scope: this
                    }, {
                        id:'b-ganttd-' + this.idContenedor,
                        text: 'Gantt Dinámico',
                        tooltip: '<b>Muestra el reporte gantt facil de entender</b>',
                        handler:this.diagramGanttDinamico,
                        scope: this
                    }
                    ]}
            });
            this.tbar.add(this.menuAdqGantt);
        },
        diagramGantt: function (){
            var data=this.sm.getSelected().data.id_proceso_wf;
            Phx.CP.loadingShow();
            Ext.Ajax.request({
                url:'../../sis_workflow/control/ProcesoWf/diagramaGanttTramite',
                params:{'id_proceso_wf':data},
                success: this.successExport,
                failure: this.conexionFailure,
                timeout: this.timeout,
                scope: this
            });
        },
        diagramGanttDinamico: function (){
            var data=this.sm.getSelected().data.id_proceso_wf;
            window.open('../../../sis_workflow/reportes/gantt/gantt_dinamico.html?id_proceso_wf='+data)
        },




        bdel: true,
        bsave: false,
        bnew: false,
        bedit: true
    })
</script>