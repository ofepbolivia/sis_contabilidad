<?php
/**
 *@package pXP
 *@file IntComprobanteRubroAprobar.php
 *@author franklin.espinoza
 *@date 10-05-2021
 *@description  Vista para generar Calculo Over Comison
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
    Phx.vista.IntComprobanteRubroAprobar=Ext.extend(Phx.gridInterfaz,{
        viewConfig: {
            stripeRows: false,
            getRowClass: function(record) {
                return "x-selectable";
            }
        },
        btest:false,
        constructor: function(config) {
            this.maestro = config;

            this.initButtons = [this.cmbDepto, this.cmbGestion];
            Phx.vista.IntComprobanteRubroAprobar.superclass.constructor.call(this,config);

            this.store.baseParams.estado_cbte = 'verificado_aprobado';

            this.addButton('btnImprimir', {
                text: 'Imprimir',
                grupo: [0, 1, 2, 3, 4],
                iconCls: 'bprint',
                disabled: true,
                handler: this.imprimirCbte,
                tooltip: '<b>Imprimir Comprobante</b><br/>Imprime el Comprobante en el formato oficial'
            });

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
                grupo: [0, 1, 2, 3],
                iconCls : 'badelante',
                disabled : true,
                handler : this.sigEstado,
                tooltip: '<b>Pasar al Siguiente Estado</b>'
            });

            this.iniciarEventos();
            this.init();

            this.bbar.insert(12,'-');
            this.bbar.insert(13,'-');
            this.bbar.insert(14,this.estado);
            this.bbar.insert(15,'-');
            this.bbar.insert(16,'-');
            this.sm.on('rowselect', this.selectRecord,this);
            this.sm.on('rowdeselect', this.deselectRecord,this);
            //this.load({params: {start: 0, limit: this.tam_pag}});
        },

        selectRecord : function(grid, rowIndex, rec) {
            var record = this.getSelectedData();
            this.estado.setText(record.estado_reg.toUpperCase());
        },

        deselectRecord : function(grid, rowIndex, rec) {
            this.estado.setText('');
        },

        estado : new Ext.form.Label({
            name: 'estado_cbte_cont',
            grupo: [0,1,2,3,4],
            fieldLabel: 'Estado',
            text: '',
            allowBlank: false,
            anchor: '60%',
            gwidth: 100,
            format: 'd/m/Y',
            hidden : false,
            readOnly:true,
            style: 'font-size: 15pt; font-weight: bold; background-image: none; color: #ff4040;'
        }),

        cmbDepto: new Ext.form.ComboBox({
            name: 'id_depto_rubro',
            grupo: [0, 1, 2],
            fieldLabel: 'Depto',
            typeAhead: false,
            forceSelection: true,
            allowBlank: false,
            disableSearchButton: true,
            emptyText: 'Departamento Contable ...',
            editable: false,
            msgTarget: 'side',
            style : {fontWeight : 'bolder', color : '#00B167'},
            store: new Ext.data.JsonStore({
                url: '../../sis_parametros/control/Depto/listarDeptoFiltradoPrioridadEXT',
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
            listWidth: 178,
            width : 178,
            resizable: true,
            minChars: 2
        }),


        cmbGestion: new Ext.form.ComboBox({
            fieldLabel: 'Gestión',
            grupo: [0, 1, 2],
            allowBlank: false,
            blankText: 'Mandatorio',
            emptyText: 'Gestión ...',
            name: 'id_gestion_rubro',
            msgTarget: 'side',
            editable: false,
            style : {fontWeight : 'bolder', color : '#00B167'},
            store: new Ext.data.JsonStore(
                {
                    url: '../../sis_parametros/control/Gestion/listarGestion',
                    id: 'id_gestion',
                    root: 'datos',
                    sortInfo: {
                        field: 'gestion',
                        direction: 'DESC'
                    },
                    totalProperty: 'total',
                    fields: ['id_gestion', 'gestion'],
                    // turn on remote sorting
                    remoteSort: true,
                    baseParams: {par_filtro: 'gestion'}
                }),
            valueField: 'id_gestion',
            triggerAction: 'all',
            displayField: 'gestion',
            hiddenName: 'id_gestion',
            mode: 'remote',
            pageSize: 50,
            queryDelay: 500,
            listWidth: 80,
            width: 80
        }),

        bactGroups:[0,1,2,3,4],
        bexcelGroups:[0,1,2,3,4],
        beditGroups:[0,1,2,3,4],
        bdelGroups:[0,1,2,3,4],
        bnewGroups:[0,1,2,3,4],
        gruposBarraTareas: [
            {name: 'normal', title: '<h1 style="text-align: center; color: #00B167;"><i class="fa fa-file-o fa-2x" aria-hidden="true"></i> NORMAL</h1>', grupo: 0, height: 1},
            {name: 'reversion', title: '<h1 style="text-align: center; color: #FF8F85;"><i class="fa fa-file-o fa-2x" aria-hidden="true"></i> REVERSIÓN</h1>', grupo: 1, height: 1}
        ],

        actualizarSegunTab: function(name, indice){
            this.store.baseParams.tipo_comprobante = name;
            if( this.cmbDepto.getValue() != '' ) {
                this.load({params: {start: 0, limit: 50}});
            }
        },

        imprimirCbte: function () {
            var rec = this.sm.getSelected();
            var data = rec.data;
            if (data) {
                Phx.CP.loadingShow();
                Ext.Ajax.request({
                    url: '../../sis_contabilidad/control/IntComprobante/reporteCbte',
                    params: {
                        'id_proceso_wf': data.id_proceso_wf
                    },
                    success: this.successExport,
                    failure: this.conexionFailure,
                    timeout: this.timeout,
                    scope: this
                });
            }

        },

        preparaMenu : function(n) {
            var tb = Phx.vista.IntComprobanteRubroAprobar.superclass.preparaMenu.call(this,n);
            var rec=this.sm.getSelected();

            if ( rec.data.estado_reg == 'verificado' ){
                this.getBoton('ant_estado').enable();
                this.getBoton('sig_estado').enable();
            }else if ( rec.data.estado_reg == 'aprobado' ){
                this.getBoton('ant_estado').disable();
                this.getBoton('sig_estado').disable();
            }
            this.getBoton('btnImprimir').enable();
            this.getBoton('ant_estado').enable();
            return tb;
        },
        liberaMenu : function() {
            var tb = Phx.vista.IntComprobanteRubroAprobar.superclass.liberaMenu.call(this);
            this.getBoton('sig_estado').disable();
            this.getBoton('ant_estado').disable();
            this.getBoton('btnImprimir').disable();
        },

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
            var rec = this.getSelectedData();
            //console.log('resp',resp);
            /*if (rec.c31 != '' && rec.fecha_c31 != '' && rec.estado == 'borrador') {
                Phx.CP.loadingShow();
                Ext.Ajax.request({
                    url: '../../sis_contabilidad/control/IntComprobante/siguienteEstado',
                    params: {

                        id_proceso_wf_act: resp.id_proceso_wf_act,
                        id_estado_wf_act: resp.id_estado_wf_act,
                        id_tipo_estado: resp.id_tipo_estado,
                        id_funcionario_wf: resp.id_funcionario_wf,
                        id_depto_wf: resp.id_depto_wf,
                        obs: resp.obs,
                        json_procesos: Ext.util.JSON.encode(resp.procesos)
                    },
                    success: this.successWizard,
                    failure: this.conexionFailure,
                    argument: {wizard: wizard},
                    timeout: this.timeout,
                    scope: this
                });
            }else{*/
            this.mandarDatosWizard(wizard, resp, true);
            //}
        },

        mandarDatosWizard: function (wizard, resp, validar_doc) {
            var record = this.getSelectedData();


            if(record.estado_reg == 'verificado'){
                this.onEgaAprobarCIP21(wizard,resp);
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

        onEgaAprobarCIP21: function(wizard, response){

            let record = this.getSelectedData();
            console.log('onEgaAprobarCIP21 record', record, 'wizard', wizard, 'response', response);
            Phx.CP.loadingShow();
            Ext.Ajax.request({
                url:'../../sis_sigep/control/SigepAdq/readyProcesoSigepC21',
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

                        Ext.Ajax.request({
                            url: '../../sis_sigep/control/SigepAdq/StatusPassC31',
                            params: {
                                id_service_request: record.id_service_request,
                                id_entrega : record.id_int_comprobante,
                                tipo : 'comprobante'
                            },
                            success: function (resp) {
                                var reg = Ext.decode(Ext.util.Format.trim(resp.responseText));
                                var datos = reg.ROOT.datos;

                                if( datos.error && datos.error != undefined){
                                    Phx.CP.loadingHide();
                                    wizard.panel.destroy();
                                    var error = datos.error;

                                    Ext.Msg.show({
                                        title: 'ERROR SIGEP',
                                        msg: error,
                                        icon: Ext.Msg.ERROR,
                                        width:500,
                                        buttons: Ext.Msg.OK
                                    });

                                    this.reload();
                                }else{
                                    Phx.CP.loadingHide();
                                    Ext.Ajax.request({
                                        url:'../../sis_contabilidad/control/IntComprobante/siguienteEstado',
                                        params:{
                                            id_int_comprobante: record.id_int_comprobante,
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
                                }
                            },
                            failure: this.failureC, //chequea si esta en verificacion presupeusto para enviar correo de transferencia
                            argument: {wizard : wizard, resp : resp},
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
            Phx.CP.loadingHide();
            resp.argument.wizard.panel.destroy();
            this.reload();
        },


        onRegistroConFlujoC21 : function(wizard, resp, momento){

            var rec = this.getSelectedData();
            console.log('wizardSIGP ENTREGA:',wizard,'respSIGP:',resp, rec);
            Phx.CP.loadingShow();
            Ext.Ajax.request({
                url: '../../sis_sigep/control/SigepAdqDet/cargarConFlujoC21',
                params: {
                    id_proceso_wf : rec.id_proceso_wf,
                    momento : momento,
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
            //Phx.CP.loadingShow();
            var reg = (Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText))).ROOT.datos;
            var porciones = reg.id_sigep.split(',');
            if( opt.argument.momento == 'REG_CON_FLUJO_C21' ){
                for (let i=0 ; i < porciones.length ; i++) {
                    console.log('identify:', porciones[i]);
                    /*Phx.CP.loadingHide();
                    Phx.CP.loadingShow();*/
                    Ext.Ajax.request({
                        url: '../../sis_sigep/control/SigepAdq/getParametrosC21',
                        params: {
                            id_sigep_adq: porciones[i],
                            ids: reg.id_sigep
                        },
                        success: this.successReg,//successReg
                        failure: this.failureC, //chequea si esta en verificacion presupeusto para enviar correo de transferencia
                        argument: {wizard : opt.argument.wizard, resp : opt.argument.resp, momento : opt.argument.momento},
                        timeout: this.timeout,
                        scope: this
                    });
                }
            }
            /*else{
                Ext.Ajax.request({
                    url: '../../sis_sigep/control/SigepAdq/getParametrosC21',
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
            }*/
        },

        successReg:function(resp, opt){
            var record = this.getSelectedData();
            var reg = (Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText))).datos;
            console.log('successReg',reg);
            let service_code = '';
            if(opt.argument.momento == 'REG_CON_FLUJO_C21') {
                service_code = 'REG_CON_FLUJO_C21';
            }
            /*Phx.CP.loadingHide();
            Phx.CP.loadingShow();*/
            Ext.Ajax.request({
                url: '../../sis_sigep/control/SigepAdq/registrarServicesC21',
                params: {
                    list : JSON.stringify(reg),
                    service_code : service_code,
                    estado_c21 : record.estado_reg,
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

            var reg = (Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText))).ROOT.datos;

            /*Phx.CP.loadingHide();
            Phx.CP.loadingShow();*/

            Ext.Ajax.request({
                url: '../../sis_sigep/control/SigepAdq/StatusC21',
                params: {
                    id_service_request: reg.id_service_request,
                    id_sigep_adq: reg.id_sigep_adq,
                    service_code: reg.service_code,
                    id_int_comprobante : record.id_int_comprobante,
                    tipo : 'comprobante'
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
            var record = this.getSelectedData();
            console.log('successSta 7777: ==========================',rest, 'getSelectedData', record);

            //Phx.CP.loadingHide();
            if(reg.ROOT.datos.error || reg.ROOT.datos.error == ''){
                Phx.CP.loadingHide();
                var error = reg.ROOT.datos.error;
                Ext.Msg.show({
                    title: 'ERROR SIGEP!',
                    msg: error,
                    icon: Ext.Msg.ERROR,
                    width:500,
                    buttons: Ext.Msg.OK,
                    fn: this.callback
                });
                this.reload();
            }else {
                if( rest.service_code == 'REG_CON_FLUJO_C21'){
                    //Phx.CP.loadingHide();
                    Ext.Ajax.request({
                        url: '../../sis_sigep/control/SigepAdq/registrarResultado',
                        params: {
                            id_sigep_adq: rest.id_sigep_adq,
                            nro_preventivo: rest.docDevengado,
                            nro_comprometido: rest.docPercibido,
                            nro_devengado: rest.secuenciaDoc
                        },
                        success: this.successP,
                        failure: this.failureC, //chequea si esta en verificacion presupeusto para enviar correo de transferencia
                        argument: {wizard : opt.argument.wizard, resp : opt.argument.resp, momento: opt.argument.momento, id_sigep_adq : rest.id_sigep_adq},
                        timeout: this.timeout,
                        scope: this
                    });
                }
            }
            //this.reload();
        },

        successP:function(resp, opt){
            Phx.CP.loadingHide();
            var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
            console.log('ResultadosP 7777: ==============================================',reg,'opt',opt);

            Ext.Ajax.request({
                url: '../../sis_sigep/control/SigepAdq/resultadoMsg',
                params: {
                    ids: opt.argument.id_sigep_adq
                },
                success: this.successResult,
                failure: this.failureC, //chequea si esta en verificacion presupeusto para enviar correo de transferencia
                argument: {wizard : opt.argument.wizard, resp : opt.argument.resp, momento: opt.argument.momento},
                timeout: this.timeout,
                scope: this
            });
            //this.reload();
        },

        successResult:function(resp, opt){

            var record =  this.getSelectedData();

            //Phx.CP.loadingHide();
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
                url:'../../sis_contabilidad/control/IntComprobante/siguienteEstado',
                params:{
                    id_int_comprobante: record.id_int_comprobante,
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
                title: 'REGISTRO C21 EXITOSO!', //<- el título del diálogo
                msg: '<p><font color="blue"><b>Los C21 Registrados fueron los siguientes: </font>' + '</br>'+ aux ,//<- El mensaje
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

            //this.revertirProcesoSigep(wizard,resp);

            Ext.Ajax.request({
                url:'../../sis_contabilidad/control/IntComprobante/anteriorEstado',
                params:{
                    id_proceso_wf: resp.id_proceso_wf,
                    id_estado_wf:  resp.id_estado_wf,
                    obs: resp.obs,
                    estado_destino: resp.estado_destino
                },
                argument:{wizard:wizard},
                success:this.successWizard,
                failure: this.conexionFailure,
                timeout:this.timeout,
                scope:this
            });
        },

        successWizard:function(resp){
            Phx.CP.loadingHide();
            resp.argument.wizard.panel.destroy();
            this.reload();
        },

        Grupos: [{
            layout: 'column',
            border: false,
            defaults: {
                border: false
            },
            items: [
                {
                    bodyStyle: 'padding-right:10px;',
                    columnWidth: .33,
                    items: [{
                        xtype: 'fieldset',
                        title: '<b style="color: green;">DATOS PRINCIPALES</b>',
                        autoHeight: true,
                        columns: 1,
                        items: [],
                        id_grupo: 0
                    }]
                },
                {
                    bodyStyle: 'padding-right:10px;',
                    columnWidth: .33,
                    items: [{
                        xtype: 'fieldset',
                        columns: 2,
                        title: '<b style="color: green;">TIPO DE CAMBIO</b>',
                        autoHeight: true,
                        items: [],
                        id_grupo: 2
                    }]
                },
                {
                    bodyStyle: 'padding-right:10px;',
                    columnWidth: .33,
                    items: [{
                        xtype: 'fieldset',
                        columns: 2,
                        title: '<b style="color: green;">TIPO COMPROBANTE</b>',
                        autoHeight: true,
                        items: [],
                        id_grupo: 1
                    }]
                }/*,
                {
                    bodyStyle: 'padding-left:5px;',
                    items: [{
                        xtype: 'fieldset',
                        columns: 2,
                        title: '<b style="color: green;">PERIODO DEL COSTO</b>',
                        autoHeight: true,
                        items: [],
                        id_grupo: 3
                    }]
                }*/
            ]
        }],


        /*iniciarEventos: function(){
            this.Cmp.forma_cambio.on('select', function (combo,rec,index) {
                this.getConfigCambiaria('si')
            }, this);

        },*/

        iniciarEventos: function () {

            this.cmpFecha = this.getComponente('fecha');

            this.Cmp.id_moneda.on('select', function () {
                this.getConfigCambiaria('si');
                this.Cmp.id_int_comprobante_fks.reset();
                this.Cmp.id_int_comprobante_fks.modificado = true;
            }, this);

            this.Cmp.id_moneda.on('expand', function (combo) {
                if ( this.Cmp.id_depto.getValue() == '' ) {
                    Ext.Msg.show({
                        title: 'Información',
                        msg: '<b>Estimado Usuario:<br>Es necesario elegir el <span style="color:red;">Departamento Contable</span> para definir la Moneda del C21.</b>',
                        buttons: Ext.Msg.OK,
                        width: 512,
                        icon: Ext.Msg.WARNING
                    });
                    this.Cmp.id_moneda.collapse();
                }
            },this);

            this.Cmp.forma_cambio.on('expand', function (combo) {
                if ( this.Cmp.id_moneda.getValue() == '' ) {
                    Ext.Msg.show({
                        title: 'Información',
                        msg: '<b>Estimado Usuario:<br>Es necesario elegir la <span style="color:red;">moneda</span> para definir el tipo de cambio para el C21.</b>',
                        buttons: Ext.Msg.OK,
                        width: 512,
                        icon: Ext.Msg.WARNING
                    });
                    this.Cmp.forma_cambio.collapse();
                }
            },this);

            /*this.Cmp.fecha_c21.on('enable', function (combo) {
                if ( this.Cmp.id_depto.getValue() == '' ) {
                    Ext.Msg.show({
                        title: 'Información',
                        msg: '<b>Estimado Usuario:<br>Es necesario elegir el Departamento Contable para definir la fecha del C21.</b>',
                        buttons: Ext.Msg.OK,
                        width: 512,
                        icon: Ext.Msg.WARNING
                    });
                    this.Cmp.fecha_c21.hide();
                }
            },this);*/

            this.Cmp.id_depto_libro.on('expand', function (combo) {
                if ( this.Cmp.id_depto.getValue() == '' ) {
                    Ext.Msg.show({
                        title: 'Información',
                        msg: '<b>Estimado Usuario:<br>Es necesario elegir el <span style="color:red;">Departamento Contable</span> para definir el Libro de Bancos.</b>',
                        buttons: Ext.Msg.OK,
                        width: 512,
                        icon: Ext.Msg.WARNING
                    });
                    this.Cmp.id_depto_libro.collapse();
                }
            },this);

            this.Cmp.id_cuenta_bancaria.on('expand', function (combo) {
                if ( this.Cmp.id_depto_libro.getValue() == '' ) {
                    Ext.Msg.show({
                        title: 'Información',
                        msg: '<b>Estimado Usuario:<br>Es necesario elegir el <span style="color:red;">Libro de Bancos</span> para definir las cuentas bancarias.</b>',
                        buttons: Ext.Msg.OK,
                        width: 512,
                        icon: Ext.Msg.WARNING
                    });
                    this.Cmp.id_cuenta_bancaria.collapse();
                }
            },this);

            /*this.Cmp.fecha_c21.on('select', function (value, date) {
                this.getConfigCambiaria('si');
            }, this);*/

            this.Cmp.forma_cambio.on('select', function () {
                this.getConfigCambiaria('si');
            }, this);

            this.Cmp.id_clase_comprobante.on('select', this.habilitaMomentos, this);

            this.Cmp.id_depto.on('select', function (cmp, rec, indice) {
                this.Cmp.id_moneda.reset();
                this.Cmp.id_moneda.store.baseParams.id_depto = rec.data.id_depto;
                this.Cmp.id_moneda.modificado = true;

                this.Cmp.id_depto_libro.reset();
                this.Cmp.id_depto_libro.modificado = true;

                this.Cmp.id_cuenta_bancaria.reset();
                this.Cmp.id_cuenta_bancaria.modificado = true;
            }, this);

            this.Cmp.id_depto_libro.on('select',function(a,b,c){
                this.Cmp.id_cuenta_bancaria.setValue('');
                this.Cmp.id_cuenta_bancaria.store.baseParams.id_depto_lb = this.Cmp.id_depto_libro.getValue();
                this.Cmp.id_cuenta_bancaria.store.baseParams.permiso = 'todos';
                this.Cmp.id_cuenta_bancaria.modificado=true;
            },this);

            this.cmbDepto.on('select', function () {
                if ( this.cmbGestion.getValue() != '' ) {
                    this.store.baseParams.id_deptos = this.cmbDepto.getValue();
                    this.store.baseParams.id_gestion = this.cmbGestion.getValue();
                    this.load();
                }
            }, this);

            this.cmbGestion.on('expand', function () {
                if ( this.cmbDepto.getValue() == '' ) {
                    Ext.Msg.show({
                        title: 'Información',
                        msg: '<b>Estimado Usuario:<br>Es necesario elegir el <span style="color:red;">Departamento Contable</span>.</b>',
                        buttons: Ext.Msg.OK,
                        width: 512,
                        icon: Ext.Msg.WARNING
                    });
                    this.cmbGestion.collapse();
                }
            }, this);

            this.cmbGestion.on('select', function () {
                this.store.baseParams.id_deptos = this.cmbDepto.getValue();
                this.store.baseParams.id_gestion = this.cmbGestion.getValue();
                this.store.baseParams.gestion = this.cmbGestion.getRawValue();
                this.load();
            }, this);
        },

        getConfigCambiaria : function(sw_valores) {

            var localidad = 'nacional';

            /*if (this.swButton == 'EDIT') {
                var rec = this.sm.getSelected();
                localidad = rec.data.localidad;

            }*/

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
                console.log('params', this.Cmp.fecha.getValue(),this.Cmp.id_moneda.getValue(),localidad,sw_valores,forma_cambio);
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

        /*actualizarSegunTab: function(name, indice){
            console.log('name', name);

            this.store.baseParams.tipo_rep = name;
            if ( (this.fecha_ini.getValue() != '' && this.fecha_ini.getValue() != undefined) || (this.fecha_fin.getValue() != '' && this.fecha_fin.getValue() != undefined) ) {


                fecha_desde = this.fecha_ini.getValue();
                dia = fecha_desde.getDate();
                dia = dia < 10 ? "0" + dia : dia;
                mes = fecha_desde.getMonth() + 1;
                mes = mes < 10 ? "0" + mes : mes;
                anio = fecha_desde.getFullYear();

                this.store.baseParams.fecha_desde = dia + "/" + mes + "/" + anio;

                fecha_hasta = this.fecha_fin.getValue();
                dia = fecha_hasta.getDate();
                dia = dia < 10 ? "0" + dia : dia;
                mes = fecha_hasta.getMonth() + 1;
                mes = mes < 10 ? "0" + mes : mes;
                anio = fecha_hasta.getFullYear();
                this.store.baseParams.fecha_hasta = dia + "/" + mes + "/" + anio;

                this.load({params: {start: 0, limit: 50}});
            }
        },*/

        Atributos: [
            {
                //configuracion del componente
                config: {
                    labelSeparator: '',
                    inputType: 'hidden',
                    name: 'id_int_comprobante'
                },
                type: 'Field',
                form: true,
                grid: false
            },
            {
                //configuracion del componente
                config: {
                    labelSeparator: '',
                    inputType: 'hidden',
                    name: 'id_periodo'
                },
                type: 'Field',
                id_grupo: 0,
                form: true
            },
            {
                //configuracion del componente
                config: {
                    labelSeparator: '',
                    inputType: 'hidden',
                    name: 'id_subsistema'
                },
                type: 'Field',
                id_grupo: 0,
                form: true
            },
            {
                //configuracion del componente
                config: {
                    fieldLabel: 'ID.',
                    gwidth: 50,
                    name: 'id_int_comprobante'
                },
                type: 'Field',
                bottom_filtro: true,
                filters: {
                    pfiltro: 'incbte.id_int_comprobante',
                    type: 'string'
                },
                form: false,
                grid: true
            },
            {
                //configuracion del componente
                config: {
                    labelSeparator: '',
                    inputType: 'hidden',
                    name: 'id_config_cambiaria'
                },
                type: 'Field',
                id_grupo: 0,
                form: true
            },


            {
                config: {
                    name: 'manual',
                    fieldLabel: 'Manual',
                    gwidth: 50,
                    renderer: function (value, p, record) {
                        if (value == 'si') {
                            return String.format('<b><font color="green">{0}</font></b>', value);
                        } else {
                            return String.format('<b><font color="orange">{0}</font></b>', value);
                        }
                    }
                },
                type: 'Field',
                id_grupo: 0,
                filters: {
                    pfiltro: 'incbte.manual',
                    type: 'string'
                },
                grid: true,
                form: false
            },
            {
                config: {
                    name: 'nro_cbte',
                    fieldLabel: 'Nro. Cbte.',
                    gwidth: 135,
                    emptyText: 'Nro. de Cbte.',
                    renderer: function (value, p, record) {
                        if (record.data.c31 && record.data.c31 != '') {
                            return String.format('<font color="#0000FF">{0}</font><br>{1}', value, record.data.c31);
                        }
                        return String.format('{0}', value);

                    }
                },
                type: 'Field',
                filters: {
                    pfiltro: 'incbte.nro_cbte#incbte.C31',
                    type: 'string'
                },
                id_grupo: 0,
                bottom_filtro: true,
                grid: true,
                form: false
            },
            {
                config: {
                    name: 'c21',
                    fieldLabel: 'C21',
                    gwidth: 150,
                    emptyText: 'c21',
                    renderer: function (value, p, record) {
                        return String.format('{0}', record.data['c21']);

                    }
                },
                type: 'Field',
                filters: {
                    pfiltro: 'incbte.C21',
                    type: 'string'
                },
                id_grupo: 0,
                bottom_filtro: true,
                grid: true,
                form: false
            },
            {
                config: {
                    msgTarget: 'side',
                    name: 'fecha',
                    fieldLabel: 'Fecha',
                    allowBlank: false,
                    width:177,
                    maxValue: new Date(),
                    gwidth: 100,
                    format: 'd/m/Y',
                    // renderer : function(value, p, record) {
                    //     return value ? value.dateFormat('d/m/Y') : ''
                    // }
                    renderer: function (value, p, record) {
                        return value ? value.dateFormat('d/m/Y') : ''
                    }
                },
                type: 'DateField',
                filters: {
                    pfiltro: 'incbte.fecha',
                    type: 'date'
                },
                id_grupo: 2,
                grid: true,
                form: true
            },
            {
                config: {
                    msgTarget: 'side',
                    name: 'id_depto',
                    hiddenName: 'id_depto',
                    url: '../../sis_parametros/control/Depto/listarDeptoFiltradoXUsuario',
                    origen: 'DEPTO',
                    allowBlank: false,
                    fieldLabel: 'Depto',
                    gdisplayField: 'desc_depto', //dibuja el campo extra de la consulta al hacer un inner join con orra tabla
                    width: 290,
                    listWidth: 290,
                    gwidth: 180,
                    baseParams: {
                        estado: 'activo',
                        codigo_subsistema: 'CONTA'
                    }, //parametros adicionales que se le pasan al store
                    renderer: function (value, p, record) {
                        return String.format('{0}', record.data['desc_depto']);
                    }
                },
                //type:'TrigguerCombo',
                type: 'ComboRec',
                id_grupo: 0,
                filters: {
                    pfiltro: 'incbte.desc_depto',
                    type: 'string'
                },
                grid: false,
                form: true
            },

            {
                config: {
                    name: 'id_depto_libro',
                    hiddenName: 'id_depto_libro',
                    //url: '../../sis_parametros/control/Depto/listarDepto',
                    origen: 'DEPTO',
                    allowBlank: false,
                    fieldLabel: 'Libro de bancos destino',
                    disabled: false,
                    listWidth: 290,
                    baseParams: {estado: 'activo', codigo_subsistema: 'TES', modulo: 'LB', tipo_filtro: 'DEPTO_UO'},
                    gdisplayField: 'desc_depto_lb',
                    gwidth: 120,
                    msgTarget: 'side',
                    width: 290
                },
                //type:'TrigguerCombo',
                filters: {pfiltro: 'depto.nombre', type: 'string'},
                type: 'ComboRec',
                id_grupo: 0,
                form: true,
                grid: true
            },

            {
                config: {
                    name: 'id_cuenta_bancaria',
                    fieldLabel: 'Cuenta Bancaria Ingreso (BOA)',
                    allowBlank: false,
                    resizable: true,
                    emptyText: 'Elija una Cuenta...',
                    store: new Ext.data.JsonStore(
                        {
                            url: '../../sis_tesoreria/control/CuentaBancaria/listarCuentaBancariaUsuario',
                            id: 'id_cuenta_bancaria',
                            root: 'datos',
                            sortInfo: {
                                field: 'id_cuenta_bancaria',
                                direction: 'ASC'
                            },
                            totalProperty: 'total',
                            fields: ['id_cuenta_bancaria', 'nro_cuenta', 'nombre_institucion', 'codigo_moneda', 'centro', 'denominacion'],
                            remoteSort: true,
                            baseParams: {
                                par_filtro: 'ctaban.nro_cuenta', centro: 'exterior'
                            }
                        }),
                    tpl: '<tpl for="."><div class="x-combo-list-item"><p><b>{nro_cuenta}</b></p><p>Moneda: {codigo_moneda}, {nombre_institucion}</p><p>{denominacion}, Centro: {centro}</p></div></tpl>',
                    valueField: 'id_cuenta_bancaria',
                    hiddenValue: 'id_cuenta_bancaria',
                    displayField: 'nro_cuenta',
                    gdisplayField: 'desc_cuenta_bancaria',

                    forceSelection: true,
                    typeAhead: false,
                    triggerAction: 'all',
                    lazyRender: true,
                    mode: 'remote',
                    pageSize: 20,
                    queryDelay: 500,
                    gwidth: 250,
                    width: 290,
                    listWidth: 290,
                    minChars: 2,
                    msgTarget: 'side',
                    renderer: function (value, p, record) {
                        return String.format('{0}', record.data['desc_cuenta_bancaria']);
                    }
                },
                type: 'ComboBox',
                filters: {pfiltro: 'ctaban.nro_cuenta', type: 'string'},
                id_grupo: 0,
                grid: true,
                form: true
            },

            {
                config: {
                    msgTarget: 'side',
                    name: 'id_clase_comprobante',
                    fieldLabel: 'Tipo Cbte.',
                    allowBlank: false,
                    emptyText: 'Elija una opción...',
                    store: new Ext.data.JsonStore({
                        url: '../../sis_contabilidad/control/ClaseComprobante/listarClaseComprobante',
                        id: 'id_clase_comprobante',
                        root: 'datos',
                        sortInfo: {
                            field: 'id_clase_comprobante',
                            direction: 'ASC'
                        },
                        totalProperty: 'total',
                        fields: ['id_clase_comprobante', 'tipo_comprobante', 'descripcion', 'codigo', 'momento_comprometido', 'momento_ejecutado', 'momento_pagado'],
                        remoteSort: true,
                        baseParams: {
                            par_filtro: 'ccom.tipo_comprobante#ccom.descripcion', clase: 'c21'
                        }
                    }),
                    valueField: 'id_clase_comprobante',
                    displayField: 'descripcion',
                    gdisplayField: 'desc_clase_comprobante',
                    hiddenName: 'id_clase_comprobante',
                    forceSelection: true,
                    typeAhead: false,
                    triggerAction: 'all',
                    lazyRender: true,
                    mode: 'remote',
                    pageSize: 15,
                    queryDelay: 1000,
                    width: 240,
                    listWidth: 240,

                    gwidth: 150,
                    minChars: 2,
                    renderer: function (value, p, record) {
                        return String.format('{0}', record.data['desc_clase_comprobante']);
                    }
                },
                type: 'ComboBox',
                id_grupo: 1,
                filters: {
                    pfiltro: 'incbte.desc_clase_comprobante',
                    type: 'string'
                },
                grid: true,
                form: true
            },

            {
                config: {
                    name: 'momento',
                    fieldLabel: 'Tipo',
                    qtip: 'Si el comprobante es presupuestario es encesario especificar los momentos que utiliza',
                    allowBlank: false,
                    gwidth: 100,
                    width: 250,
                    typeAhead: true,
                    triggerAction: 'all',
                    lazyRender: true,
                    mode: 'local',
                    valueField: 'inicio',
                    store: ['contable', 'presupuestario']
                },
                type: 'ComboBox',
                id_grupo: 1,
                filters: {
                    type: 'list',
                    pfiltro: 'incbte.momento',
                    options: ['contable', 'presupuestario'],
                },
                grid: true,
                form: false
            },

            {
                config: {
                    name: 'momento_devengado',
                    fieldLabel: 'Devengado',
                    renderer: function (value, p, record) {
                        return record.data['momento_comprometido'] == 'true' ? 'si' : 'no';
                    },
                    gwidth: 50,

                },
                type: 'Checkbox',
                id_grupo: 1,
                grid: true,
                form: true
            },

            {
                config: {
                    name: 'momento_percibido',
                    fieldLabel: 'Percibido',
                    renderer: function (value, p, record) {
                        return record.data['momento_ejecutado'] == 'true' ? 'si' : 'no';
                    },
                    gwidth: 50,

                },
                type: 'Checkbox',
                id_grupo: 1,
                grid: true,
                form: true
            },

            {
                config: {
                    name: 'reversion',
                    fieldLabel: 'Reversión',
                    renderer: function (value, p, record) {
                        return record.data['reversion'] == 'true' ? 'si' : 'no';
                    },
                    gwidth: 50,

                },
                type: 'Checkbox',
                id_grupo: 1,
                grid: true,
                form: true
            },

            {
                config: {
                    msgTarget: 'side',
                    name: 'id_moneda',
                    origen: 'MONEDA',
                    allowBlank: false,
                    fieldLabel: 'Moneda',
                    gdisplayField: 'desc_moneda', //mapea al store del grid
                    gwidth: 100,
                    width: 240,
                    listWidth:240,
                    renderer: function (value, p, record) {
                        return String.format('{0}', record.data['desc_moneda']);
                    }
                },
                type: 'ComboRec',
                id_grupo: 2,
                filters: {
                    pfiltro: 'incbte.desc_moneda',
                    type: 'string'
                },
                grid: true,
                form: true
            },

            {
                config: {
                    msgTarget: 'side',
                    name: 'forma_cambio',
                    fieldLabel: 'Cambio',
                    qtip: 'Tipo cambio oficial, compra, venta o convenido',
                    allowBlank: false,
                    gwidth: 100,
                    width: 240,
                    typeAhead: true,
                    triggerAction: 'all',
                    lazyRender: true,
                    mode: 'local',
                    valueField: 'oficial',
                    store: ['oficial', 'compra', 'venta', 'convenido']
                },
                type: 'ComboBox',
                id_grupo: 2,
                filters: {
                    type: 'list',
                    pfiltro: 'incbte.forma_cambio',
                    options: ['oficial', 'compra', 'venta', 'convenido'],
                },
                grid: true,
                form: true
            },
            {
                config: {
                    msgTarget: 'side',
                    name: 'tipo_cambio',
                    readOnly: true,
                    fieldLabel: 'TC',
                    allowBlank: false,
                    anchor: '80%',
                    gwidth: 70,
                    maxLength: 20,
                    decimalPrecision: 10
                },
                type: 'NumberField',
                filters: {
                    pfiltro: 'incbte.tipo_cambio',
                    type: 'numeric'
                },
                id_grupo: 2,
                grid: true,
                form: true
            },
            {
                config: {
                    msgTarget: 'side',
                    name: 'tipo_cambio_2',
                    fieldLabel: '(TC)',
                    allowBlank: false,
                    readOnly: true,
                    anchor: '80%',
                    gwidth: 70,
                    maxLength: 20,
                    decimalPrecision: 6
                },
                type: 'NumberField',
                filters: {
                    pfiltro: 'incbte.tipo_cambio_2',
                    type: 'numeric'
                },
                id_grupo: 2,
                grid: true,
                form: true
            },
            {
                config: {
                    msgTarget: 'side',
                    name: 'tipo_cambio_3',
                    fieldLabel: '(TC)',
                    allowBlank: false,
                    readOnly: true,
                    anchor: '80%',
                    gwidth: 70,
                    maxLength: 20,
                    decimalPrecision: 6
                },
                type: 'NumberField',
                filters: {
                    pfiltro: 'incbte.tipo_cambio_3',
                    type: 'numeric'
                },
                id_grupo: 2,
                grid: true,
                form: true
            },
            {
                config: {
                    name: 'nro_tramite',
                    gwidth: 150,
                    fieldLabel: 'Nro. Trámite',
                    renderer: function (value, p, record) {
                        if (record.data.cbte_reversion == 'si') {
                            return String.format('<div title="Cbte de Reversión"><b><font color="#0000FF">{0}</font></b></div>', value);
                        }
                        if (record.data.volcado == 'si') {
                            return String.format('<div title="Cbte Revertido/Volcado"><b><font color="red">{0}</font></b></div>', value);
                        }
                        return String.format('{0}', value);

                    }
                },
                type: 'Field',
                id_grupo: 0,
                filters: {
                    pfiltro: 'incbte.nro_tramite',
                    type: 'string'
                },
                grid: true,
                bottom_filtro: true,
                form: false,
                grid: true,
            },
            {
                config: {
                    msgTarget: 'side',
                    name: 'glosa1',
                    fieldLabel: 'Glosa',
                    allowBlank: false,
                    width: '290',
                    gwidth: 250,
                    maxLength: 1500
                },
                type: 'TextArea',
                filters: {
                    pfiltro: 'incbte.glosa1',
                    type: 'string'
                },
                id_grupo: 0,
                bottom_filtro: true,
                //egrid: true,
                grid: true,
                form: true
            },
            {
                config: {
                    name: 'estado_reg',
                    fieldLabel: 'Estado',
                    emptyText: 'Estado Reg.'
                },
                type: 'Field',
                filters: {
                    pfiltro: 'incbte.estado_reg',
                    type: 'string'
                },
                grid: true,
                form: false
            },
            {
                config: {
                    name: 'usr_reg',
                    fieldLabel: 'Creado por',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength: 4
                },
                type: 'Field',
                filters: {
                    pfiltro: 'incbte.usr_reg',
                    type: 'string'
                },
                id_grupo: 0,
                grid: true,
                form: false
            },
            {
                config: {
                    name: 'fecha_reg',
                    fieldLabel: 'Fecha creación',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    format: 'd/m/Y',
                    renderer: function (value, p, record) {
                        return value ? value.dateFormat('d/m/Y H:i:s') : ''
                    }
                },
                type: 'DateField',
                filters: {
                    pfiltro: 'incbte.fecha_reg',
                    type: 'date'
                },
                id_grupo: 0,
                grid: true,
                form: false
            },
            {
                config: {
                    name: 'usr_mod',
                    fieldLabel: 'Modificado por',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength: 4
                },
                type: 'Field',
                filters: {
                    pfiltro: 'incbte.usr_mod',
                    type: 'string'
                },
                id_grupo: 0,
                grid: true,
                form: false
            },
            {
                config: {
                    name: 'fecha_mod',
                    fieldLabel: 'Fecha Modif.',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    format: 'd/m/Y',
                    renderer: function (value, p, record) {
                        return value ? value.dateFormat('d/m/Y H:i:s') : ''
                    }
                },
                type: 'DateField',
                filters: {
                    pfiltro: 'incbte.fecha_mod',
                    type: 'date'
                },
                id_grupo: 0,
                grid: true,
                form: false
            },
            {
                config: {
                    name: 'id_service_request',
                    fieldLabel: 'Id. Service Request',
                    allowBlank: true,
                    readOnly: true,
                    anchor: '90%',
                    gwidth: 70,
                    maxLength: 20,
                    decimalPrecision: 6
                },
                type: 'NumberField',
                filters: {
                    pfiltro: 'incbte.id_service_request',
                    type: 'numeric'
                },
                id_grupo: 0,
                grid: true,
                form: false
            }
        ],

        title:'Registro C21',
        ActSave: '../../sis_contabilidad/control/IntComprobante/insertarIntComprobanteC21',
        ActDel: '../../sis_contabilidad/control/IntComprobante/eliminarIntComprobanteC21',
        ActList: '../../sis_contabilidad/control/IntComprobante/listarIntComprobanteC21',
        id_store: 'id_int_comprobante',
        fields: [
            { name: 'id_int_comprobante', type: 'numeric' },

            { name: 'id_depto', type: 'numeric' },
            { name: 'desc_depto', type: 'string' },
            { name: 'id_depto_libro', type: 'numeric' },
            { name: 'desc_depto_lb', type: 'string' },
            { name: 'id_cuenta_bancaria', type: 'numeric' },
            { name: 'desc_cuenta_bancaria', type: 'string' },
            { name: 'glosa1', type: 'string' },
            { name: 'fecha', type: 'date', dateFormat: 'Y-m-d' },
            { name: 'id_moneda', type: 'numeric' },
            { name: 'desc_moneda', type: 'string' },
            { name: 'forma_cambio', type: 'string' },
            { name: 'tipo_cambio', type: 'numeric' },
            { name: 'tipo_cambio_2', type: 'numeric' },
            { name: 'tipo_cambio_3', type: 'numeric' },
            { name: 'id_clase_comprobante', type: 'numeric' },
            { name: 'desc_clase_comprobante', type: 'string' },
            { name: 'momento_devengado', type: 'string' },
            { name: 'momento_percibido', type: 'string' },
            { name: 'reversion', type: 'string' },

            { name: 'id_subsistema', type: 'numeric' },
            { name: 'desc_subsistema', type: 'string'},
            { name: 'id_periodo', type: 'numeric' },
            { name: 'nro_cbte', type: 'string' },
            { name: 'nro_tramite', type: 'string' },
            { name: 'momento', type: 'string' },

            { name: 'id_moneda_base', type: 'numeric' },
            { name: 'id_proceso_wf', type: 'numeric' },
            { name: 'id_estado_wf', type: 'numeric' },
            { name: 'manual', type: 'string' },
            { name: 'id_moneda_tri', type: 'numeric' },
            { name: 'id_moneda_act', type: 'numeric' },
            { name: 'sw_tipo_cambio', type: 'string' },
            { name: 'id_config_cambiaria', type: 'numeric' },
            { name: 'localidad', type: 'string' },
            { name: 'sw_editable', type: 'string' },
            { name: 'cbte_reversion', type: 'string' },
            { name: 'volcado', type: 'string' },
            { name: 'c21', type: 'string' },
            { name: 'fecha_c31', type: 'date', dateFormat: 'Y-m-d' },
            { name: 'id_service_request', type: 'numeric' },
            { name: 'nro_preventivo', type: 'string' },

            /********************************* INFORMACION REGISTRO *********************************/
            { name: 'estado_reg', type: 'string' },
            { name: 'id_usuario_reg', type: 'numeric' },
            { name: 'fecha_reg', type: 'date', dateFormat: 'Y-m-d H:i:s.u' },
            { name: 'id_usuario_mod', type: 'numeric' },
            { name: 'fecha_mod', type: 'date', dateFormat: 'Y-m-d H:i:s.u' },
            { name: 'usr_reg', type: 'string' },
            { name: 'usr_mod', type: 'string' },
            /********************************* INFORMACION REGISTRO *********************************/

            { name: 'tipo_cbte', type: 'string' }
        ],
        sortInfo:{
            field: 'id_int_comprobante',
            direction: 'asc'
        },
        bedit:false,
        bnew:false,
        bdel:false,
        bsave:false,
        fwidth: '90%',
        fheight: '50%',
        south: {
            url: '../../../sis_contabilidad/vista/int_transaccion/IntTransaccionRubro.php',
            title: 'Imputaciones',
            height: '50%', //altura de la ventana hijo
            cls: 'IntTransaccionRubro'
        }
    });
</script>
