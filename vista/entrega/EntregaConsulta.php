<?php
/**
 *@package pXP
 *@file gen-EntregaConsulta.php
 *@author  (admin)
 *@date 17-11-2016 19:50:19
 *@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
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
    .regularizacion_una_cg {
        background-color: #bdffb2;//#e2ffe2
    color: #090;
    }

    .regularizacion_mas_cg{
        background-color: #EAA8A8;//#ffe2e2
    color: #900;
    }

    .normal_una_cg {
        background-color: #bdffb2;//#e2ffe2
    color: #090;
    }

    .normal_mas_cg{
        background-color: #EAA8A8;//#ffe2e2
    color: #900;
    }

</style>
<script>
    Phx.vista.EntregaConsulta=Ext.extend(Phx.gridInterfaz,{

        viewConfig: {

            autoFill: true,
            getRowClass: function (record) {

                if (record.data.tipo == 'normal_una_cg') {
                    return 'normal_una_cg';

                } else if (record.data.tipo == 'normal_mas_cg') {
                    return 'normal_mas_cg';

                } else if (record.data.tipo == 'regularizacion_una_cg') {
                    return 'regularizacion_una_cg';

                } else if (record.data.tipo == 'regularizacion_mas_cg') {
                    return 'regularizacion_mas_cg';

                } else {
                    return '';
                }
            }

        },

        nombreVista: 'EntregaConsulta',

        constructor:function(config){

            this.maestro=config.maestro;
            //llama al constructor de la clase padre
            this.initButtons = [this.cmbDepto/*, this.cmbGestion*/];
            this.tbarItems = ['-',
                this.cmbGestion,'-'
            ];
            Phx.vista.EntregaConsulta.superclass.constructor.call(this,config);
            this.store.baseParams.pes_estado = ' ';

            Ext.Ajax.request({
                url:'../../sis_reclamo/control/Reclamo/getDatosOficina',
                params:{id_usuario:0},
                success:function(resp){
                    var reg =  Ext.decode(Ext.util.Format.trim(resp.responseText));

                    this.cmbGestion.setValue(reg.ROOT.datos.id_gestion);
                    this.cmbGestion.setRawValue(reg.ROOT.datos.gestion);
                    this.store.baseParams.gestion = reg.ROOT.datos.gestion;
                    //this.load({params:{start:0, limit:this.tam_pag}});
                },
                failure: this.conexionFailure,
                timeout:this.timeout,
                scope:this
            });


            this.addBotonesGantt();
            this.cmbGestion.on('select',this.capturaGestion, this);
            this.addButton('btnChequeoDocumentosWf',{
                text: 'Documentos',
                grupo: [0,1,2,3,4],
                iconCls: 'bchecklist',
                disabled: true,
                handler: this.loadCheckDocumentosRecWf,
                tooltip: '<b>Documentos del Reclamo</b><br/>Subir los documetos requeridos en el Reclamo seleccionado.'
            });


            //Botón para Imprimir el Comprobante
            this.addButton('btnImprimir', {
                text : 'Imprimir',
                grupo: [0,1,2,3,4],
                iconCls : 'bprint',
                disabled : true,
                handler : this.imprimirCbte,
                tooltip : '<b>Imprimir Reporte de Entrega</b><br/>Imprime un detalle de las factidas presupeustarias relacioandas a la entrega'
            });

            if (this.unidad == 'contabilidad') {

                this.addButtonIndex(7, 'erp_ext_entrega',
                    {
                        iconCls: 'bball_green',
                        disabled: true,
                        xtype: 'splitbutton',
                        grupo: [0, 1,2,3,4],
                        tooltip: '<b>Acciones para validar y desvalidar, comprobante ERP.</b>',
                        text: 'ACCIÓN ERP',
                        //handler: this.onButtonExcel,
                        argument: {
                            'news': true,
                            def: 'reset'
                        },
                        scope: this,
                        menu: [
                            {
                                text: '<b style="color: red">Validar CBTES</b>',
                                iconCls: 'bver-sigep',
                                argument: {
                                    'news': true,
                                    def: 'csv'
                                },
                                handler: this.onValidar,
                                scope: this
                            },
                            {
                                text: '<b style="color: red">Desvalidar CBTES</b>',
                                iconCls: 'bdes-sigep',
                                argument: {
                                    'news': true,
                                    def: 'csv'
                                },
                                handler: this.onDesvalidar,
                                scope: this
                            },
                            {
                                text: '<b style="color: red">Revertir ENTREGA</b>',
                                iconCls: 'brever-sigep',
                                argument: {
                                    'news': true,
                                    def: 'csv'
                                },
                                handler: this.onRevertir,
                                scope: this
                            },
                            {
                                text: '<b style="color: red">Clonar ENTREGA</b>',
                                iconCls: 'bnew-sigep',
                                argument: {
                                    'news': true,
                                    def: 'csv'
                                },
                                handler: this.onClonar,
                                scope: this
                            }
                        ]
                    }
                );

                this.addButtonIndex(8,'sigep_ext_entrega',
                    {
                        iconCls: 'bball_green',
                        xtype: 'splitbutton',
                        grupo: [0, 1,2,3,4],
                        tooltip: '<b>Acciones para procesar SIGEP</b>',
                        text: 'ACCIÓN SIGEP',
                        //handler: this.onButtonExcel,
                        argument: {
                            'news': true,
                            def: 'reset'
                        },
                        scope: this,
                        menu: [
                            {
                                text: 'Desverificar C31',
                                iconCls: 'bdes-sigep',
                                argument: {
                                    'news': true,
                                    def: 'csv'
                                },
                                handler: this.antEstado,
                                scope: this
                            },
                            {
                                text: 'Estado C31',
                                iconCls: 'bdes-sigep',
                                argument: {
                                    'news': true,
                                    def: 'csv'
                                },
                                handler: this.estadoSigep,
                                scope: this
                            }
                        ]
                    }
                );
            }

            this.addButtonIndex(6,'consulta', {
                text: 'Consulta CBT',
                grupo: [0, 1, 2, 3, 4],
                iconCls: 'brenew',
                disabled: false,
                handler: this.consultaCBTE,
                tooltip: '<b>Consulta de Comprobantes</b>'
            });

            /*this.addButton('ant_estado',{
                grupo: [0,1,2,3,4,5],
                argument: {estado: 'anterior'},
                text: 'Anterior',
                iconCls: 'batras',
                disabled: false,
                handler: this.antEstadoA,
                tooltip: '<b>Volver al Anterior Estado</b>'
            });*/


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

        capturaGestion: function (){
            this.store.baseParams.gestion=this.cmbGestion.getRawValue();
            this.load({params:{start:0, limit:this.tam_pag}});
        },

        gruposBarraTareas: [
            {name:  'borrador', title: '<h1 style="text-align:center; color:#4682B4;"><i class="fa fa-file-o fa-2x" aria-hidden="true"></i> BORRADOR</h1>',grupo: 0, height: 1} ,
            {name: 'elaborado', title: '<h1 style="text-align: center; color: #586E7E ;"><i class="fa fa-file-o fa-2x" aria-hidden="true"></i> ELABORADO</h1>', grupo: 1, height: 1},
            {name: 'verificado', title: '<h1 style="text-align: center; color: #00B167;"><i class="fa fa-file-o fa-2x" aria-hidden="true"></i> VERIFICADO</h1>', grupo: 2, height: 1},
            {name: 'aprobado', title: '<h1 style="text-align: center; color: #B066BB;"><i class="fa fa-file-o fa-2x" aria-hidden="true"></i> APROBADO</h1>', grupo: 3, height: 1},
            {name: 'finalizado', title: '<h1 style="text-align: center; color: #FF8F85;"><i class="fa fa-file-o fa-2x" aria-hidden="true"></i> FINALIZADO</h1>', grupo: 4, height: 1}
        ],

        bactGroups:[0,1,2,3,4],
        bexcelGroups:[0,1,2,3,4],

        actualizarSegunTab: function(name, indice){

            this.store.baseParams.estado_entrega = name;
            if(this.cmbDepto.getValue() != '' && this.cmbDepto.getValue() != undefined) {
                this.load({params: {start: 0, limit: 50}});
            }
        },

        antEstadoA:function(res){
            var rec=this.sm.getSelected();
            Phx.CP.loadWindows('../../../sis_workflow/vista/estado_wf/AntFormEstadoWf.php',
                'Estado de Wf',
                {
                    modal:true,
                    width:450,
                    height:250
                }, { data:rec.data}, this.idContenedor,'AntFormEstadoWf',
                {
                    config:[{
                        event:'beforesave',
                        delegate: this.onAntEstadoA,
                    }
                    ],
                    scope:this
                })
        },
        onAntEstadoA: function(wizard,resp){
            Phx.CP.loadingShow();
            Ext.Ajax.request({
                url:'../../sis_contabilidad/control/Entrega/retrosederEstado',
                params:{
                    id_proceso_wf: resp.id_proceso_wf,
                    id_estado_wf:  resp.id_estado_wf,
                    obs: resp.obs,
                    estado_destino: resp.estado_destino
                },
                argument:{wizard:wizard},
                success:this.successEstadoSincA,
                failure: this.conexionFailure,
                timeout:this.timeout,
                scope:this
            });
        },

        successEstadoSincA:function(resp){
            Phx.CP.loadingHide();
            resp.argument.wizard.panel.destroy();
            this.reload();
        },
        /*===================================================BEGIN REVERTIR ESTADO ANTERIOR======================================================*/
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
            var record = this.getSelectedData();
            console.log('onAntEstado', record.estado);
            Phx.CP.loadingShow();
            if(record.estado == 'verificado'){
                this.desverificaProcesoSigep(wizard,resp);
            }else {
                /*Ext.Ajax.request({
                    url:'../../sis_contabilidad/control/Entrega/retrosederEstado',
                    params:{
                        id_proceso_wf: resp.id_proceso_wf,
                        id_estado_wf:  resp.id_estado_wf,
                        obs: resp.obs,
                        estado_destino: resp.estado_destino
                    },
                    argument:{wizard:wizard},
                    success:this.successEstadoSinc,
                    failure: this.conexionFailure,
                    timeout:this.timeout,
                    scope:this
                });*/
            }
        },

        desverificaProcesoSigep : function (wizard,response){
            var record = this.getSelectedData();
            console.log('record Desverifica', record);
            Ext.Ajax.request({
                url:'../../sis_sigep/control/SigepAdq/readyProcesoSigep',
                params:{
                    id_service_request : record.id_service_request,
                    estado_reg : record.estado,
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
                            url:'../../sis_contabilidad/control/Entrega/retrosederEstado',
                            params:{
                                id_proceso_wf: response.id_proceso_wf,
                                id_estado_wf:  response.id_estado_wf,
                                obs: response.obs,
                                estado_destino: response.estado_destino
                            },
                            argument:{wizard:wizard},
                            success:this.successEstadoSinc,
                            failure: this.conexionFailure,
                            timeout:this.timeout,
                            scope:this
                        });
                    }else{
                        Phx.CP.loadingHide();
                        wizard.panel.destroy();
                        this.reload();
                    }
                },
                failure: this.conexionFailure,
                timeout: this.timeout,
                scope:this
            });
        },
        /*===================================================END REVERTIR ESTADO ANTERIOR======================================================*/

        successEstadoSinc:function(resp){
            Phx.CP.loadingHide();
            resp.argument.wizard.panel.destroy()
            this.reload();
        },

        onRevertir: function(){

            Ext.Msg.show({
                title: 'REVERSIÓN ENTREGA',
                msg: '<b style="color: red;">Esta seguro de Revertir la Entreta ERP.</b>',
                fn: function (btn){
                    if(btn == 'ok'){

                                var rec = this.getSelectedData();
                                Phx.CP.loadingShow();
                                Ext.Ajax.request({
                                    url: '../../sis_contabilidad/control/Entrega/volcarEntrega',
                                    params: {
                                        id_entrega : rec.id_entrega
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
                },
                buttons: Ext.Msg.OKCANCEL,
                width: 350,
                maxWidth:500,
                icon: Ext.Msg.WARNING,
                scope:this
            });

        },
        onClonar: function(){
            Ext.Msg.show({
                title: 'CLONAR ENTREGA',
                msg: '<b style="color: red;">Esta seguro de Clonar la entrega ERP.</b>',
                fn: function (btn){
                    if(btn == 'ok'){
                        var record = this.getSelectedData();
                        Phx.CP.loadingShow();

                        Ext.Ajax.request({
                            url: '../../sis_contabilidad/control/Entrega/clonarEntrega',
                            params: {
                                id_entrega : record.id_entrega
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
                },
                buttons: Ext.Msg.OKCANCEL,
                width: 350,
                maxWidth:500,
                icon: Ext.Msg.WARNING,
                scope:this
            });
        },

        cmbDepto : new Ext.form.AwesomeCombo({
            id: 'id_depto_ent_ext_con',
            name : 'id_depto_ent_ext_con',
            grupo: [0, 1, 2, 3, 4],
            fieldLabel : 'Depto',
            typeAhead : false,
            forceSelection : true,
            allowBlank : false,
            disableSearchButton : true,
            emptyText : 'Depto Contable',
            msgTarget: 'side',
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

        /*cmbDepto: new Ext.form.AwesomeCombo({
            name: 'id_depto',
            grupo: [0, 1, 2, 3, 4],
            fieldLabel: 'Depto',
            typeAhead: false,
            forceSelection: true,
            allowBlank: false,
            disableSearchButton: true,
            msgTarget: 'side',
            emptyText: 'Depto Contable',
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
            enableMultiSelect: false,
            triggerAction: 'all',
            lazyRender: true,
            mode: 'remote',
            pageSize: 20,
            queryDelay: 200,
            anchor: '80%',
            listWidth: '280',
            resizable: true,
            minChars: 2
        }),*/


        cmbGestion: new Ext.form.ComboBox({
            fieldLabel: 'Gestión',
            grupo: [0, 1, 2, 3, 4],
            allowBlank: false,
            blankText: 'Seleccione gestión',
            emptyText: 'Gestión',
            name: 'id_gestion',
            msgTarget: 'side',
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
            listWidth: '280',
            width: 80
        }),

        consultaCBTE: function(){

            if( this.cmbDepto.getValue() != '' && this.cmbDepto.getValue() != undefined/*&& this.cmbGestion.getValue() != '' */) { //console.log('combos',this.cmbDepto.getValue(), this.cmbGestion.getValue());
                var rec = {maestro: this};
                rec.id_depto = this.cmbDepto.getValue();
                rec.id_gestion = this.cmbGestion.getValue();

                Phx.CP.loadWindows('../../../sis_contabilidad/vista/int_comprobante/IntComprobanteConsulta.php',
                    'Consulta de Comprobantes',
                    {
                        width: '90%',
                        height: '85%'
                    },
                    rec,
                    this.idContenedor,
                    'IntComprobanteConsulta'
                );
            }else{
                Ext.Msg.show({
                    title: 'Información',
                    msg: '<b>Estimado Funcionario: '+'\n'+' Debe seleccionar el departamento correspiente.</b>',
                    buttons: Ext.Msg.OK,
                    width: 512,
                    icon: Ext.Msg.INFO
                });
            }
        },



        /*revertirProcesoSigep : function (){
            var record = this.getSelectedData();
            Phx.CP.loadingShow();
            Ext.Ajax.request({
                url:'../../sis_sigep/control/SigepAdq/readyProcesoSigep',
                params:{
                    id_service_request : record.id_service_request,
                    estado_reg : record.estado,
                    momento : 'pass',
                    direction : 'previous'
                },
                success: function (resp) {
                    var reg =  Ext.decode(Ext.util.Format.trim(resp.responseText));
                    var datos = reg.ROOT.datos;
                    console.log('revertirProcesoEntregaSigep Contador',datos);
                    if(datos.process){
                        Ext.Ajax.request({
                            url:'../../sis_contabilidad/control/Entrega/retrosederEstado',
                            params:{
                                id_proceso_wf: record.id_proceso_wf,
                                id_estado_wf:  record.id_estado_wf,
                                obs: 'Retroceso Realizada por el Contador Responsable.'
                            },
                            argument:{ wizard : wizard },
                            success:this.successEstadoSinc,
                            failure: this.conexionFailure,
                            timeout:this.timeout,
                            scope:this
                        });
                    }else{
                        Ext.Msg.show({
                            title: 'Reversión de proceso ERP-SIGEP',
                            msg: '<b>Estimado Funcionario: '+'\n'+'No se pudo revertir el proceso ERP-SIGEP, comunicarse con el siguiente numero().</b>',
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
        },*/

        /*successEstadoSinc:function(resp){
            Phx.CP.loadingHide();
            this.reload();
        },*/

        onValidar : function(){

            /*Phx.CP.loadingShow();
            let record = this.getSelectedData();*/

            Ext.Msg.show({
                title: 'COMPROBANTE ERP',
                msg: '<b style="color: red;">Esta seguro de validar los comprobantes asociados a esta Entrega.</b>',
                fn: function (btn){
                    if(btn == 'ok'){
                        Phx.CP.loadingShow();
                        let record = this.getSelectedData();

                        Ext.Ajax.request({
                            url:'../../sis_contabilidad/control/Entrega/validarGrupoComprobantes',
                            params:{
                                id_entrega : record.id_entrega
                            },
                            success: function (resp) {
                                var reg =  Ext.decode(Ext.util.Format.trim(resp.responseText));
                                var datos = reg.ROOT.datos;
                                Phx.CP.loadingHide();
                                console.log('datos fuera',datos);
                                if(!reg.ROOT.error){
                                    console.log('datos',datos);
                                    Ext.Msg.show({
                                        title: 'Estado SIGEP',
                                        msg: '<b>Estimado Funcionario: '+'\n'+'Los comprobantes de la entrega (ID: '+record.id_entrega+') se validaron correctamente en el ERP.</b>',
                                        buttons: Ext.Msg.OK,
                                        width: 512,
                                        icon: Ext.Msg.INFO
                                    });
                                }else{
                                    Phx.CP.loadingHide();

                                    Ext.Msg.show({
                                        title: 'Estado SIGEP',
                                        msg: '<b>Estimado Funcionario: '+'\n'+'Hubo algunos inconvenientes al validar los comprobantes de la entrega (ID: '+record.id_entrega+')</b>',
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
                    }
                },
                buttons: Ext.Msg.OKCANCEL,
                width: 350,
                maxWidth:500,
                icon: Ext.Msg.WARNING,
                scope:this
            });
        },

        onDesvalidar : function(){
            /*Phx.CP.loadingShow();
            let record = this.getSelectedData();*/

            Ext.Msg.show({
                title: 'COMPROBANTE ERP',
                msg: '<b style="color: red;">Esta seguro de desvalidar los comprobantes asociados a esta Entrega.</b>',
                fn: function (btn){
                    if(btn == 'ok'){

                        Phx.CP.loadingShow();
                        let record = this.getSelectedData();

                        Ext.Ajax.request({
                            url:'../../sis_contabilidad/control/Entrega/desvalidarGrupoComprobantes',
                            params:{
                                id_entrega : record.id_entrega
                            },
                            success: function (resp) {
                                var reg =  Ext.decode(Ext.util.Format.trim(resp.responseText));
                                var datos = reg.ROOT.datos;
                                Phx.CP.loadingHide();
                                console.log('datos fuera',datos);
                                if(!reg.ROOT.error){
                                    console.log('datos',datos);
                                    Ext.Msg.show({
                                        title: 'Estado SIGEP',
                                        msg: '<b>Estimado Funcionario: '+'\n'+'Los comprobantes de la entrega (ID: '+record.id_entrega+') se desvalidaron correctamente en el ERP.</b>',
                                        buttons: Ext.Msg.OK,
                                        width: 512,
                                        icon: Ext.Msg.INFO
                                    });
                                }else{
                                    Phx.CP.loadingHide();

                                    Ext.Msg.show({
                                        title: 'Estado SIGEP',
                                        msg: '<b>Estimado Funcionario: '+'\n'+'Hubo algunos inconvenientes al desvalidar los comprobantes de la entrega (ID: '+record.id_entrega+')</b>',
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
                    }
                },
                buttons: Ext.Msg.OKCANCEL,
                width: 350,
                maxWidth:500,
                icon: Ext.Msg.WARNING,
                scope:this
            });
        },

        /*cmbDepto : new Ext.form.AwesomeCombo({
            name : 'id_depto_consulta',
            fieldLabel : 'Depto',
            typeAhead : false,
            forceSelection : true,
            allowBlank : false,
            disableSearchButton : true,
            emptyText : 'Depto Contable',
            store : new Ext.data.JsonStore({
                url : '../../sis_parametros/control/Depto/listarDeptoFiltradoDeptoUsuario',
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
        }),*/

        Atributos:[
            {

                config:{
                    name: 'id_entrega',
                    fieldLabel: 'Id. Entrega',
                    allowBlank: false,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength:200
                },
                type:'TextField',
                filters:{pfiltro:'ent.id_entrega',type:'string'},
                id_grupo:1,
                grid:true,
                form:true,
                bottom_filter:true
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
                form:true,
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
                form:true
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
                form:true,
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
                filters:{pfiltro:'monto',type:'numeric'},
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
        title:'EntregaConsulta',
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
            title : 'Detalle Comprobantes',
            height : '50%', //altura de la ventana hijo
            cls : 'EntregaDet',
            nombreVista: this.nombreVista
        },


        preparaMenu : function(n) {
            var tb = Phx.vista.EntregaConsulta.superclass.preparaMenu.call(this,n);
            var rec=this.sm.getSelected();

            this.getBoton('btnImprimir').enable();
            this.getBoton('diagrama_gantt').enable();
            this.getBoton('btnChequeoDocumentosWf').enable();
            if (this.unidad == 'contabilidad') {
                this.getBoton('sigep_ext_entrega').enable();
                this.getBoton('erp_ext_entrega').enable();
            }
            //this.getBoton('ant_estado').enable();
            return tb;
        },
        liberaMenu : function() {
            var tb = Phx.vista.EntregaConsulta.superclass.liberaMenu.call(this);
            this.getBoton('btnImprimir').disable();
            this.getBoton('btnChequeoDocumentosWf').disable();
            this.getBoton('diagrama_gantt').disable();
            if (this.unidad == 'contabilidad') {
                this.getBoton('sigep_ext_entrega').disable();
                this.getBoton('erp_ext_entrega').disable();
            }
            //this.getBoton('ant_estado').disable();
        },
        capturaFiltros : function(combo, record, index) {

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
                grupo:[0,1,2,3,4],
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




        bdel: false,
        bsave: false,
        bnew: false,
        bedit: false
    })
</script>