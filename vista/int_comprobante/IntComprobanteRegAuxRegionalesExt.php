<?php
/**
 * @package pXP
 * @file    IntComprobanteRegAuxRegionalesExt.php
 * @author  franklin.espinoza
 * @date    07-09-2020 00:28:30
 * @description Archivo con la interfaz de usuario que permite
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
    Phx.vista.IntComprobanteRegAuxRegionalesExt = {
        bsave: false,

        require: '../../../sis_contabilidad/vista/int_comprobante/IntComprobanteRegionalesExt.php',
        requireclase: 'Phx.vista.IntComprobanteRegionalesExt',
        title: 'Libro Diario',
        nombreVista: 'IntComprobanteRegAuxExt',

        viewConfig: {
            stripeRows: false,
            getRowClass: function(record) {
                return "x-selectable";
            }
        },
        constructor: function (config) {
            var me = this;

           /* (this.Grupos[0]['items']).splice(1,0,{
                bodyStyle: 'padding-left:5px;',
                items: [{
                    xtype: 'fieldset',
                    columns: 2,
                    title: 'Información Cuenta Bancaria',
                    autoHeight: true,
                    items: [],
                    id_grupo: 4
                }]
            });*/

            /*this.Atributos.splice(40,0, {
                config: {
                    name: 'id_depto_libro',
                    hiddenName: 'id_depto_libro',
                    //url: '../../sis_parametros/control/Depto/listarDepto',
                    origen: 'DEPTO',
                    allowBlank: false,
                    fieldLabel: 'Libro de bancos destino',
                    disabled: false,
                    width: '80%',
                    baseParams: {estado: 'activo', codigo_subsistema: 'TES', modulo: 'LB', tipo_filtro: 'DEPTO_UO'},
                    gdisplayField: 'desc_depto_lb',
                    gwidth: 120,
                    width: 250
                },
                //type:'TrigguerCombo',
                filters: {pfiltro: 'depto.nombre', type: 'string'},
                type: 'ComboRec',
                id_grupo: 0,
                form: true,
                grid: true
            });

            this.Atributos.splice(41,0,{
                config: {
                    name: 'id_cuenta_bancaria',
                    fieldLabel: 'Cuenta Bancaria Pago (BOA)',
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
                                par_filtro: 'nro_cuenta', centro: 'exterior'
                            }
                        }),
                    tpl: '<tpl for="."><div class="x-combo-list-item"><p><b>{nro_cuenta}</b></p><p>Moneda: {codigo_moneda}, {nombre_institucion}</p><p>{denominacion}, Centro: {centro}</p></div></tpl>',
                    valueField: 'id_cuenta_bancaria',
                    hiddenValue: 'id_cuenta_bancaria',
                    displayField: 'nro_cuenta',
                    gdisplayField: 'desc_cuenta_bancaria',
                    listWidth: '280',
                    forceSelection: true,
                    typeAhead: false,
                    triggerAction: 'all',
                    lazyRender: true,
                    mode: 'remote',
                    pageSize: 20,
                    queryDelay: 500,
                    gwidth: 250,
                    width: 250,
                    minChars: 2,
                    renderer: function (value, p, record) {
                        return String.format('{0}', record.data['desc_cuenta_bancaria']);
                    }
                },
                type: 'ComboBox',
                filters: {pfiltro: 'cb.nro_cuenta', type: 'string'},
                id_grupo: 0,
                grid: true,
                form: true
            });*/

            Phx.vista.IntComprobanteRegAuxRegionalesExt.superclass.constructor.call(this, config);



            /*this.addButtonIndex(6,'sig_estado', {
                text: 'Aprobar',
                grupo: [0, 1, 2, 3],
                iconCls: 'badelante',
                disabled: true,
                handler: this.sigEstado,
                tooltip: '<b>Pasar al Siguiente Estado</b>'
            });*/

            this.addButtonIndex(6,'chkEntregasSigep',{	text:'Entregas SIGEP',
                iconCls: 'blist',
                grupo: [0, 1, 2, 3],
                disabled: true,
                handler: this.crearEntregaSigep,
                tooltip: '<b>Crear Entregas Sigep</b><p>Las entregas permiten asociar con cbte en otros subsistema (por ejemplo SIGMA o SIGEP)</p>'
            });

            /*this.addButtonIndex(6,'ant_estado',{
                grupo: [0,1,2,3,4,5],
                argument: {estado: 'anterior'},
                text: 'Anterior',
                iconCls: 'batras',
                hidden: false,
                handler: this.antEstado,
                tooltip: '<b>Volver al Anterior Estado</b>'
            });*/

            this.addButtonIndex(6,'consulta', {
                text: 'Consulta CBT',
                grupo: [0, 1, 2, 3, 4],
                iconCls: 'brenew',
                disabled: false,
                handler: this.consultaCBTE,
                tooltip: '<b>Consulta de Comprobantes</b>'
            });

            /*this.addButtonIndex(7,'sigep_ext_verificado_pago',
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
                        text: 'Editar C31',
                        iconCls: 'bnew-sigep',
                        argument: {
                            'news': true,
                            def: 'csv'
                        },
                        handler: this.onEditC31,
                        scope: this
                    }/!*, {
                        text: 'Eliminar C31',
                        iconCls: 'bdel-sigep',
                        argument: {
                            'news': true,
                            def: 'pdf'
                        },
                        handler: this.onEliminarC31,
                        scope: this
                    }*!/]
                }
            );*/

            this.addButton('btnWizard', {
                text: 'Plantilla',
                grupo: [0, 1, 2, 3],
                iconCls: 'bgear',
                disabled: false,
                handler: this.loadWizard,
                tooltip: '<b>Plantilla de Comprobantes</b><br/>Seleccione una plantilla y genere comprobantes preconfigurados'
            });

            this.addButton('btnIgualarCbte', {
                text: 'Igualar',
                grupo: [0, 1, 2, 3],
                iconCls: 'bengineadd',
                disabled: true,
                handler: this.igualarCbte,
                tooltip: '<b>Igualar comprobante</b><br/>Si existe diferencia por redondeo o por tipo de cambio inserta una transacción para igualar'
            });

            this.addButton('btnSwEditble', {
                text: 'Editable',
                grupo: [0, 1, 2, 3],
                iconCls: 'balert',
                disabled: true,
                handler: this.swEditable,
                tooltip: '<b>Hacer editable</b><br/>Si la edición esta deshabilitada toma un backup y la habilita'
            });

            this.addButton('chkEntregas',{	text:'Entregas',
                iconCls: 'blist',
                grupo: [0, 1, 2, 3],
                disabled: true,
                handler: this.crearEntrega,
                tooltip: '<b>Crear Entregas </b><p>Las entregas permiten asociar con cbte en otros subsistema (por ejemplo SIGMA o SIGEP)</p>'
            });



            this.addBotonesAjusteIgualar();

            this.bbar.insert(12,'-');
            this.bbar.insert(13,'-');
            this.bbar.insert(14,this.estado);
            this.store.baseParams.estado_cbte = 'borrador_elaborado';

            this.init();

            this.sm.on('rowselect', this.selectRecord,this);
            this.sm.on('rowdeselect', this.deselectRecord,this);
            this.cambio_estado = null;
            this.wizard_estado = null;
        },

        onEditC31 : function(){
            Phx.CP.loadingShow();
            let record = this.getSelectedData();
            //console.log('record', record, 'wizard', wizard, 'response', response);
            Ext.Ajax.request({
                url:'../../sis_sigep/control/SigepAdq/setupSigepProcess',
                params:{
                    id_service_request : record.id_service_request,
                    estado_reg : record.estado_reg,
                    json_data : record.glosa1,
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

        crearEntregaSigep: function(){
            var filas=this.sm.getSelections(),
                total= 0,tmp='',me = this;

            for(var i=0;i<this.sm.getCount();i++){
                aux={};
                if(total == 0){
                    tmp = filas[i].data[this.id_store];
                }
                else{
                    tmp = tmp + ','+ filas[i].data[this.id_store];
                }
                total = total + 1;
            }
            if(total != 0){
                if(confirm("¿Esta  seguro de Crear esta entrega?") ){
                    Phx.CP.loadingShow();
                    Ext.Ajax.request({
                        url : '../../sis_contabilidad/control/Entrega/crearEntregaSigep',
                        params : {
                            id_int_comprobantes : tmp,
                            id_depto_conta: me.cmbDepto.getValue(),
                            total_cbte: total,
                            tipo: 'regularizacion'
                        },
                        success : function(resp) {
                            Phx.CP.loadingHide();
                            alert('La entrega fue creada con exito, incluye cbte(s): '+ total);
                            this.reload();

                        },
                        failure : this.conexionFailure,
                        timeout : this.timeout,
                        scope : this
                    });
                }
            }
            else{
                alert ('No selecciono ningun comprobante');
            }
        },

        consultaCBTE: function(){

            if( this.cmbDepto.getValue() != '' && this.cmbGestion.getValue() != '' ) { //console.log('combos',this.cmbDepto.getValue(), this.cmbGestion.getValue());
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
                    msg: '<b>Estimado Funcionario: '+'\n'+' Debe seleccionar el departamento y la gestión correspiente.</b>',
                    buttons: Ext.Msg.OK,
                    width: 512,
                    icon: Ext.Msg.INFO
                });
            }
        },

        estado : new Ext.form.Label({
            name: 'estado_ext',
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

        /*gruposBarraTareas: [
            {name: 'borrador', title: '<h1 style="text-align: center; color: #FF8F85;">BORRADOR</h1>',grupo: 0, height: 0} ,
            {name: 'elaborado', title: '<h1 style="text-align: center; color: #4682B4;">ELABORADO</h1>', grupo: 1, height: 0}
        ],*/
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

            /*Ext.Ajax.request({
                url:'../../sis_contabilidad/control/IntComprobante/anteriorEstado',
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
        },

        revertirProcesoSigep : function (wizard,response){
            var record = this.getSelectedData();
            console.log('record', record);
            Ext.Ajax.request({
                url:'../../sis_sigep/control/SigepAdq/revertirProcesoSigep',
                params:{
                    id_service_request : record.id_service_request,
                    estado_reg : record.estado_reg
                },
                success: function (resp) {
                    var reg =  Ext.decode(Ext.util.Format.trim(resp.responseText));
                    var datos = reg.ROOT.datos;
                    console.log('revertirProcesoSigep',datos);
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

        selectRecord : function(grid, rowIndex, rec) {
            var record = this.getSelectedData();
            this.estado.setText(record.estado_reg.toUpperCase());
            if(record.estado_reg != 'borrador'){
                //this.getBoton('ant_estado').setVisible(true);
            }
        },

        deselectRecord : function(grid, rowIndex, rec) {
            this.estado.setText('');
            //this.getBoton('ant_estado').setVisible(false);
        },

        /*=================================BEGIN VERIFICAR=======================================*/
        verificarProcesoSigep : function (wizard, response){
            var record = this.getSelectedData();
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
                    console.log('verificarProcesoSigep',datos);
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
        /*=================================END VERIFICAR=======================================*/


        /*=================================BEGIN WORKFLOW=======================================*/
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

        mandarDatosWizard: function (wizard, resp, validar_doc) {
            var record = this.getSelectedData();
            Phx.CP.loadingShow();

            if(record.estado_reg == 'borrador'){
                this.onSigepWizard(wizard, resp);
            }else if(record.estado_reg == 'elaborado'){
                this.verificarProcesoSigep(wizard, resp);
            }
            /*Ext.Ajax.request({
                url: '../../sis_contabilidad/control/IntComprobante/siguienteEstado',
                params: {
                    id_int_comprobante: wizard.data.id_int_comprobante,
                    id_proceso_wf_act: resp.id_proceso_wf_act,
                    id_estado_wf_act: resp.id_estado_wf_act,
                    id_tipo_estado: resp.id_tipo_estado,
                    id_funcionario_wf: resp.id_funcionario_wf,
                    id_depto_wf: resp.id_depto_wf,
                    obs: resp.obs,
                    instruc_rpc: resp.instruc_rpc,
                    json_procesos: Ext.util.JSON.encode(resp.procesos),
                    validar_doc: validar_doc

                },
                success: this.successWizard,
                failure: this.conexionFailure,
                argument: {wizard: wizard, id_proceso_wf: resp.id_proceso_wf_act, resp: resp},
                timeout: this.timeout,
                scope: this
            });*/
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
                if(rec.data.estado_reg == 'aprobado' || rec.data.estado_reg == 'borrador'){
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
        /*=================================END WORKFLOW=======================================*/

        /*==========================================================*/
        onSigepWizard : function(wizard,resp){

            var rec = this.getSelectedData();

            if(rec.estado_reg == 'borrador') {
                if (rec.id_clase_comprobante == 5) {
                    if (rec.localidad == 'internacional'){console.log('onSigepReguS');
                        this.onSigepReguS(wizard, resp, 'REGULARIZAS');
                    }else{
                        this.onSigepSip(wizard, resp);
                    }
                } else if (rec.id_clase_comprobante == 1){
                    if (rec.localidad == 'internacional'){
                        this.onSigepReguC(wizard, resp, 'REGULARIZAC'); console.log('onSigepReguC');
                    }else{
                        console.log('onSigepCIP');
                        this.onSigepCIP(wizard, resp);
                    }
                } /*else if (rec.id_clase_comprobante == 1 && rec.id_subsistema == 13){
                    this.onSigepPLANI(wizard, resp);
                }*/
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

        onSigepReguS:function(wizard, resp, momento){

            var rec = this.getSelectedData();
            console.log('wizardSIGP:',wizard,'respSIGP:',resp, rec);

            //resp.momento='REGULARIZAS';
            Phx.CP.loadingShow();

            Ext.Ajax.request({
                url: '../../sis_sigep/control/SigepAdqDet/cargarSigepReguSip',
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

        successConsu:function(resp, opt){
            Phx.CP.loadingShow();
            var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
            console.log('successConsu:',reg.ROOT.datos, 'option', opt.argument );
            rest = reg.ROOT.datos;
            var id = reg.ROOT.datos.id_sigep;
            this.ids = id;
            var porciones = id.split(',');
            if( opt.argument.momento == 'REGULARIZAC' || opt.argument.momento == 'REGULARIZAS' ){
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
                    id_int_comprobante : record.id_int_comprobante
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
                }else if(opt.argument.momento == 'REGULARIZAC' || opt.argument.momento == 'REGULARIZAS'){
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
                url: '../../sis_contabilidad/control/IntComprobante/siguienteEstado',
                params: {
                    id_int_comprobante: record.id_int_comprobante,
                    id_proceso_wf_act: opt.argument.resp.id_proceso_wf_act,
                    id_estado_wf_act: opt.argument.resp.id_estado_wf_act,
                    id_tipo_estado: opt.argument.resp.id_tipo_estado,
                    id_funcionario_wf: opt.argument.resp.id_funcionario_wf,
                    id_depto_wf: opt.argument.resp.id_depto_wf,
                    obs: opt.argument.resp.obs,
                    instruc_rpc: opt.argument.resp.instruc_rpc,
                    json_procesos: Ext.util.JSON.encode(opt.argument.resp.procesos),
                    validar_doc: true

                },
                success: this.successWizard,
                failure: this.conexionFailure,
                argument: {wizard : opt.argument.wizard, resp : opt.argument.resp, momento: opt.argument.momento},
                timeout: this.timeout,
                scope: this
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

        onSigepReguC:function(wizard,resp, momento){

            var rec = this.sm.getSelected().data;
            console.log('wizardSIGP:',wizard,'respSIGP:',resp, rec);
            resp.sigep_adq='vbsigepcontaregu';
            resp.momento='REGULARIZACION';
            Phx.CP.loadingShow();

            Ext.Ajax.request({
                url: '../../sis_sigep/control/SigepAdqDet/cargarSigepReguCip',
                params: {
                    id_proceso_wf: rec.id_proceso_wf,
                    momento: momento,
                    sigep_adq: resp.sigep_adq,
                    localidad: rec.localidad,
                },
                success: this.successConsu,
                failure: this.failureC, //chequea si esta en verificacion presupeusto para enviar correo de transferencia
                argument: {wizard: wizard, resp : resp, momento: momento},
                timeout: this.timeout,
                scope: this
            });
        },

        onSigepCIP:function(wizard,resp){
            var rec = this.sm.getSelected().data;
            console.log('wizardSIGP:',wizard,'respSIGP:',resp, rec);
            resp.sigep_adq='vbsigepconta';
            resp.momento='CON_IMPUTACION_V';
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

        //may
        cbtePerdida: function (sw_validar) {

            if (confirm("Esta seguro de generar un nuevo comprobante, este proceso iguala importes por cuestion del Tipo de Cambio en distintas fechas  ")) {
                if (confirm("¿Esta realmente seguro?")) {
                    var rec = this.sm.getSelected().data;
                    Phx.CP.loadingShow();
                    Ext.Ajax.request({
                        url: '../../sis_contabilidad/control/IntComprobante/cbtePerdidaCbte',
                        params: {
                            id_int_comprobante: rec.id_int_comprobante,
                            sw_validar: (sw_validar == 'si') ? 'si' : 'no'
                        },
                        success: function (resp) {
                            Phx.CP.loadingHide();
                            var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
                            if (reg.ROOT.error) {
                                Ext.Msg.alert('Error', 'Al generar el cbte: ' + reg.ROOT.error)
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
        cbteIncremento: function (sw_validar) {

            if (confirm("Esta seguro de generar un nuevo comprobante, este proceso iguala importes por cuestion del Tipo de Cambio en distintas fechas  ")) {
                if (confirm("¿Esta realmente seguro?")) {
                    var rec = this.sm.getSelected().data;
                    Phx.CP.loadingShow();
                    Ext.Ajax.request({
                        url: '../../sis_contabilidad/control/IntComprobante/cbteIncrementoCbte',
                        params: {
                            id_int_comprobante: rec.id_int_comprobante,
                            sw_validar: (sw_validar == 'si') ? 'si' : 'no'
                        },
                        success: function (resp) {
                            Phx.CP.loadingHide();
                            var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
                            if (reg.ROOT.error) {
                                Ext.Msg.alert('Error', 'Al generar el cbte: ' + reg.ROOT.error)
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


        addBotonesAjusteIgualar: function () {
            this.menuAjusteIgualar = new Ext.Toolbar.SplitButton({
                id: 'b-btnVolcar-' + this.idContenedor,
                text: 'Generar Cbte. Tipo de Cambio',
                disabled: true,
                grupo: [0, 1, 2, 3],
                iconCls: 'balert',
                scope: this,
                menu: {
                    items: [{
                        id: 'b-volb-' + this.idContenedor,
                        text: 'Cbte Pérdida',
                        tooltip: '<b>Cbte de Perdida para procesos internacionales que no igualan por el tipo de cambio</b>',
                        handler: function () {
                            this.cbtePerdida('no')
                        },
                        scope: this
                    }, {
                        id: 'b-vol-' + this.idContenedor,
                        //text: 'Reversión Total (Validado)',
                        text: 'Cbte Incremento',
                        tooltip: '<b>Cbte de Incremento para procesos internacionales que no igualan por el tipo de cambio</b>',
                        handler: function () {
                            this.cbteIncremento('si')
                        },
                        scope: this
                    }
                    ]
                }
            });
            this.tbar.add(this.menuAjusteIgualar);
        },


        onButtonEdit: function () {
            this.swButton = 'EDIT';

            //var record = this.sm.getSelected().data;
            var rec = this.getSelectedData();

            if (this.swButton == 'EDIT') {
                localidad = rec.localidad;
            }


            Phx.vista.IntComprobanteRegAuxRegionalesExt.superclass.onButtonEdit.call(this);
            this.cmpFecha.enable();
            this.Cmp.id_moneda.setReadOnly(true);
            //para que se puede modificar bolivia de sus comprobantes de ñas estaciones internacionales
            /*if (rec.localidad == 'internacional') {
                this.Cmp.fecha.setReadOnly(true);
            }*/
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
            Phx.vista.IntComprobanteRegAuxRegionalesExt.superclass.onButtonNew.call(this);
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
            var tb = Phx.vista.IntComprobanteRegAuxRegionalesExt.superclass.preparaMenu.call(this);
            var rec = this.sm.getSelected();
            if (rec.data.tipo_reg == 'summary') {
                this.getBoton('btnSwEditble').disable();
                //this.getBoton('sig_estado').disable();
                this.getBoton('btnImprimir').disable();
                this.getBoton('btnRelDev').disable();
                this.getBoton('btnIgualarCbte').disable();
                this.getBoton('btnDocCmpVnt').disable();

                this.getBoton('chkEntregas').disable();
                this.getBoton('chkEntregasSigep').disable();
                this.getBoton('btnVolcar').disable();
            } else {
                if (rec.data.sw_editable == 'no') {
                    this.getBoton('btnSwEditble').setDisabled(false);
                } else {
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

                this.getBoton('chkEntregas').enable();
                this.getBoton('chkEntregasSigep').enable();
                this.getBoton('btnVolcar').enable();
            }
            if (rec.data.momento == 'presupuestario') {
                this.getBoton('btnDocCmpVnt').enable();
            } else {
                this.getBoton('btnDocCmpVnt').disable();
            }

            /*if(rec.data.estado_reg != 'borrador'){
                this.getBoton('ant_estado').enable();
            }*/

            //this.getBoton('sigep_ext_verificado_pago').enable();

            return tb;
        },
        liberaMenu: function () {
            var tb = Phx.vista.IntComprobanteRegAuxRegionalesExt.superclass.liberaMenu.call(this);

            //this.getBoton('sig_estado').disable();
            this.getBoton('btnImprimir').disable();
            this.getBoton('btnRelDev').disable();
            this.getBoton('btnIgualarCbte').disable();
            this.getBoton('btnDocCmpVnt').disable();
            this.getBoton('chkpresupuesto').disable();
            this.getBoton('btnChequeoDocumentosWf').disable();
            this.getBoton('diagrama_gantt').disable();
            this.getBoton('btnObs').disable();

            this.getBoton('chkEntregas').disable();
            this.getBoton('chkEntregasSigep').disable();
            this.getBoton('btnVolcar').disable();

            //this.getBoton('sigep_ext_verificado_pago').disable();
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

            var rec = this.getSelectedData();
            var localidad = 'nacional';

            if (this.swButton == 'EDIT') {
                localidad = 'nacional';
            }
            //console.log('getConfigCambiaria',localidad, 'a', this.Cmp.fecha.getValue(), 'b', this.Cmp.id_moneda.getValue(), 'c', this.Cmp.forma_cambio.getValue());

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


        crearEntrega: function(){
            var filas=this.sm.getSelections(),
                total= 0,tmp='',me = this;

            for(var i=0;i<this.sm.getCount();i++){
                aux={};
                if(total == 0){
                    tmp = filas[i].data[this.id_store];
                }
                else{
                    tmp = tmp + ','+ filas[i].data[this.id_store];
                }
                total = total + 1;
            }
            if(total != 0){
                if(confirm("¿Esta  seguro de Crear esta entrega?") ){
                    Phx.CP.loadingShow();
                    Ext.Ajax.request({
                        url : '../../sis_contabilidad/control/Entrega/crearEntrega',
                        params : {
                            id_int_comprobantes : tmp,
                            id_depto_conta: me.cmbDepto.getValue(),
                            total_cbte: total
                        },
                        success : function(resp) {
                            Phx.CP.loadingHide();
                            alert('La entrega fue creada con exito, incluye cbte(s): '+ total);
                            this.reload();

                        },
                        failure : this.conexionFailure,
                        timeout : this.timeout,
                        scope : this
                    });
                }
            }
            else{
                alert ('No selecciono ningun comprobante');
            }
        },

        // south: {
        //     url: '../../../sis_contabilidad/vista/int_transaccion/IntTransaccionAux.php',
        //     title: 'Transacciones',
        //     height: '50%', //altura de la ventana hijo
        //     cls: 'IntTransaccionAux'
        // },

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