<?php
/**
 *@package      pXP
 *@file         IntTransaccionRubro.php
 *@author       (frankli.espinoza)
 *@date         01-09-2013 18:10:12
 *@description  Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
 */
header("content-type: text/javascript; charset=UTF-8");
?>
<script>
    Phx.vista.IntTransaccionRubro=Ext.extend(Phx.gridInterfaz,{
        fheight : '70%',
        fwidth : '60%',
        btest:false,
        constructor:function(config){

            this.maestro=config.maestro;
            //llama al constructor de la clase padre
            Phx.vista.IntTransaccionRubro.superclass.constructor.call(this,config);
            this.grid.getTopToolbar().disable();
            this.grid.getBottomToolbar().disable();
            this.init();

            this.obtenerVariableGlobal('conta_partidas');
            this.iniciarEvento();

            this.Cmp.importe_gasto.on('change',function(cmp,value){
                this.Cmp.importe_haber.suspendEvents();
                this.Cmp.importe_haber.setValue(0);
                this.Cmp.importe_haber.resumeEvents();
                this.Cmp.importe_recurso.suspendEvents();
                this.Cmp.importe_recurso.setValue(0);
                this.Cmp.importe_recurso.resumeEvents();
                this.Cmp.importe_debe.suspendEvents();
                this.Cmp.importe_debe.setValue(value);
                this.Cmp.importe_debe.resumeEvents();
            },this);

            this.Cmp.importe_recurso.on('change',
                function(cmp,value){
                    this.Cmp.importe_debe.suspendEvents();
                    this.Cmp.importe_debe.setValue(0);
                    this.Cmp.importe_debe.resumeEvents();
                    this.Cmp.importe_haber.suspendEvents();
                    this.Cmp.importe_haber.setValue(value);
                    this.Cmp.importe_haber.resumeEvents();
                    this.Cmp.importe_gasto.suspendEvents();
                    this.Cmp.importe_gasto.setValue(0);
                    this.Cmp.importe_gasto.resumeEvents();
                },this);


            this.Cmp.importe_debe.on('change',function(cmp){
                this.Cmp.importe_haber.suspendEvents();
                this.Cmp.importe_haber.setValue(0);
                this.Cmp.importe_haber.resumeEvents();
                this.Cmp.importe_recurso.suspendEvents();
                this.Cmp.importe_recurso.setValue(0);
                this.Cmp.importe_recurso.resumeEvents();
            },this);

            this.Cmp.importe_haber.on('change',
                function(cmp){
                    this.Cmp.importe_debe.suspendEvents();
                    this.Cmp.importe_debe.setValue(0);
                    this.Cmp.importe_debe.resumeEvents();
                    this.Cmp.importe_gasto.suspendEvents();
                    this.Cmp.importe_gasto.setValue(0);
                    this.Cmp.importe_gasto.resumeEvents();
                },this);
        },
        iniciarEvento:function() {
            this.Cmp.id_concepto_ingas.on('select', function (cmb, record, index) {
                this.Cmp.id_subtipo_incidente.reset();
                this.Cmp.id_subtipo_incidente.modificado = true;
                this.Cmp.id_subtipo_incidente.setDisabled(false);
                this.Cmp.id_subtipo_incidente.store.setBaseParam('fk_tipo_incidente', record.data.id_tipo_incidente);

                this.Cmp.id_partida.store.setBaseParam('fk_tipo_incidente', record.data.id_tipo_incidente);
                this.Cmp.id_cuenta.store.setBaseParam('fk_tipo_incidente', record.data.id_tipo_incidente);

            }, this);
        },
        Grupos: [
            {
                layout: 'column',
                border: false,
                labelAlign: 'top',
                defaults: {
                    border: false
                },
                items: [
                    /*{
                        bodyStyle: 'padding-right:10px;',
                        columnWidth: .50,
                        items: [{
                            xtype: 'fieldset',
                            title: '<b style="color: green;">IMPUTACIONES</b>',
                            autoHeight: true,

                            items: [],
                            id_grupo: 0
                        }]
                    },*/
                    {
                        columnWidth: .50,
                        border: false,
                        layout: 'fit',
                        bodyStyle: 'padding-right:10px;',
                        items: [
                            {
                                xtype: 'fieldset',
                                title: '<b style="color: green;">IMPUTACIONES</b>',

                                autoHeight: true,
                                items: [
                                    {
                                        layout: 'form',
                                        anchor: '100%',
                                        border: false,
                                        padding: '0 5 0 5',
                                        id_grupo: 0,
                                        items: []
                                    }
                                ]
                            }
                        ]
                    },
                    {
                        columnWidth: .50,
                        border: false,
                        layout: 'fit',
                        bodyStyle: 'padding-right:10px;',
                        items: [
                            {
                                xtype: 'fieldset',
                                title: '<b style="color: green;">MONTOS</b>',

                                autoHeight: true,
                                items: [
                                    {
                                        layout: 'form',
                                        anchor: '100%',
                                        border: false,
                                        padding: '0 5 0 5',
                                        id_grupo: 1,
                                        items: []
                                    }
                                ]
                            }
                        ]
                    }
                    /*{
                        bodyStyle: 'padding-right:10px;',
                        columnWidth: .50,
                        items: [{
                            xtype: 'fieldset',
                            title: '<b style="color: green;">MONTOS</b>',
                            autoHeight: true,
                            items: [],
                            id_grupo: 1
                        }]
                    }*/
                ]
            }
        ],

        Atributos:[
            {
                //configuracion del componente
                config:{
                    labelSeparator:'',
                    inputType:'hidden',
                    name: 'id_int_transaccion'
                },
                type:'Field',
                form:true
            },
            {
                config: {
                    labelSeparator:'',
                    inputType:'hidden',
                    name: 'id_int_comprobante'
                },
                type:'Field',
                form:true
            },
            {
                config:{
                    name: 'id_centro_costo',
                    msgTarget: 'side',
                    fieldLabel: 'Centro Costo',
                    allowBlank: false,
                    tinit:false,
                    editable: false,
                    origen:'CENTROCOSTO',
                    url: '../../sis_parametros/control/CentroCosto/listarCentroCostoFiltradoXDepto',
                    gdisplayField: 'desc_centro_costo',
                    /*width: 380,
                    listWidth: 380,*/
                    anchor:'97%',
                    gwidth:600,
                    tipo_pres:"gasto,administrativo,recurso,ingreso_egreso",
                    baseParams: { tipo_pres: "recurso"},
                    renderer:function (value, p, record){
                        var color = 'green';//console.log('record.data[desc_orden]', record.data);
                        if(record.data["tipo_reg"] != 'summary'){
                            if(record.data["tipo_partida"] == 'flujo'){
                                color = 'red';
                            }

                            var retorno =  String.format('<b>CC:</b> {0} <br><b>Cta.:</b>{1}<br>',record.data['desc_centro_costo'],record.data['desc_cuenta']);

                            if(record.data['desc_auxiliar']){
                                retorno = retorno + String.format('<b>Aux.:</b> {0}</br>', record.data['desc_auxiliar']);
                            }
                            if(record.data['desc_partida']){
                                retorno = retorno + String.format('<b>Ptda.:</b> <font color="{0}">{1}</font><br>',color, record.data['desc_partida']);
                            }
                            if(record.data['desc_orden']){
                                if(record.data.planilla == 'si')
                                    retorno = retorno + String.format('<b>Ord.:</b> <font> {0} </font><br>', record.data['desc_orden']);
                                else
                                    retorno = retorno + String.format('<b>Ord.:</b> <font> {0} {1}</font><br>', record.data['codigo_ot'],record.data['desc_orden']);
                            }
                            if(record.data['desc_suborden']){
                                retorno = retorno + '<b>Sub.:</b> '+record.data['desc_suborden'];
                            }
                            return String.format('<div class="gridmultiline">{0}</div>',retorno);
                        }
                        else{
                            return '<b><p align="right">Total: &nbsp;&nbsp; </p></b>';
                        }

                    }
                },
                type:'ComboRec',
                filters:{
                    pfiltro:'cue.nombre_cuenta#cue.nro_cuenta#cc.codigo_cc#cue.nro_cuenta#cue.nombre_cuenta#aux.codigo_auxiliar#aux.nombre_auxiliar#par.codigo#par.nombre_partida#ot.desc_orden#suo.codigo#suo.nombre#ot.codigo',
                    type:'string'},
                id_grupo:0,
                grid:true,
                bottom_filter: true,
                form:true
            },

            {
                config:{
                    name:'id_concepto_ingas',
                    fieldLabel:'Concepto Ingreso',
                    allowBlank:true,
                    emptyText:'Concepto Ingreso :qqq...',
                    store: new Ext.data.JsonStore({
                        url: '../../sis_parametros/control/ConceptoIngas/listarConceptoIngasMasPartida',
                        id: 'id_concepto_ingas',
                        root: 'datos',
                        sortInfo:{
                            field: 'desc_ingas',
                            direction: 'ASC'
                        },
                        totalProperty: 'total',
                        fields: ['id_concepto_ingas','tipo','desc_ingas','movimiento','desc_partida','id_grupo_ots','filtro_ot','requiere_ot'],
                        // turn on remote sorting
                        remoteSort: true,
                        baseParams:{par_filtro:'desc_ingas#par.codigo#par.nombre_partida#par.id_partida',movimiento:'recurso' ,autorizacion_nulos: 'no'}
                    }),
                    valueField: 'id_concepto_ingas',
                    displayField: 'desc_ingas',
                    gdisplayField:'desc_ingas',
                    tpl:'<tpl for="."><div class="x-combo-list-item"><p><b>{desc_ingas}</b></p><p>TIPO:{tipo}</p><p>MOVIMIENTO:{movimiento}</p> <p>PARTIDA:{desc_partida}</p></div></tpl>',
                    hiddenName: 'id_concepto_ingas',
                    forceSelection:true,
                    typeAhead: false,
                    triggerAction: 'all',
                    lazyRender:true,
                    mode:'remote',
                    pageSize:30,
                    queryDelay:1000,
                    resizable:true,
                    gwidth: 800,
                    /*width: 380,
                    listWidth:380,*/
                    anchor:'97%',
                    renderer:function(value, p, record){return String.format('{0}', record.data['desc_ingas']);}
                },
                type:'ComboBox',
                id_grupo:0,
                filters:{
                    pfiltro:'conig.desc_ingas',
                    type:'string'
                },
                grid:true,
                form:true
            },

            {
                config:{
                    sysorigen:'sis_presupuestos',
                    name:'id_partida',
                    msgTarget: 'side',
                    origen:'PARTIDA',
                    allowBlank:false,
                    fieldLabel:'Partida',
                    gdisplayField:'desc_partida',//mapea al store del grid
                    gwidth:200,
                    anchor:'97%',
                    /*width: 380,
                    listWidth: 380,*/
                    baseParams: {par_filtro:'par.codigo#par.nombre_partida#par.id_gestion',sw_transaccional:'movimiento',tipo:'recurso'}
                },
                type:'ComboRec',
                id_grupo:0,
                filters:{
                    pfiltro: 'par.codigo_partida#par.nombre_partida',
                    type: 'string'
                },

                grid:true,

                form:true
            },
            {
                config:{
                    sysorigen:'sis_contabilidad',
                    name:'id_cuenta',
                    msgTarget: 'side',
                    origen:'CUENTA',
                    allowBlank:false,
                    fieldLabel:'Cuenta',
                    gdisplayField:'desc_cuenta',//mapea al store del grid
                    gwidth:600,
                    anchor:'97%',
                    /*width: 380,
                    listWidth: 380*/
                },
                type:'ComboRec',
                id_grupo:0,
                filters:{
                    pfiltro:'cue.nombre_cuenta#cue.nro_cuenta',
                    type:'string'
                },
                grid:true,
                form:true
            },
            {
                config:{
                    sysorigen:'sis_contabilidad',
                    name:'id_auxiliar',
                    origen:'AUXILIAR',
                    allowBlank:true,
                    fieldLabel:'Auxiliar',
                    gdisplayField:'desc_auxiliar',//mapea al store del grid
                    gwidth:200,
                    anchor:'97%',
                    /*width: 380,
                    listWidth: 380,*/
                    renderer:function (value, p, record){return String.format('{0}', record.data['desc_auxiliar']);}
                },
                type:'ComboRec',
                id_grupo:0,
                filters:{
                    pfiltro:'au.codigo_auxiliar#au.nombre_auxiliar',
                    type:'string'
                },

                grid:false,
                form:true
            },


            {
                config:{
                    name:'id_orden_trabajo',
                    msgTarget: 'side',
                    fieldLabel: 'Orden',
                    sysorigen:'sis_contabilidad',
                    origen:'OT',
                    allowBlank:true,
                    gwidth:200,
                    anchor:'97%',
                    /*width: 380,
                    listWidth: 380*/

                },
                type:'ComboRec',
                id_grupo:0,
                filters:{pfiltro:'ot.motivo_orden#ot.desc_orden',type:'string'},
                grid:false,
                form:true
            },
            {
                config:{
                    name:'id_suborden',
                    fieldLabel: 'Suborden',
                    sysorigen:'sis_contabilidad',
                    origen:'SUBORDEN',
                    allowBlank:true,
                    gwidth:200,
                    anchor:'97%',
                    /*width: 380,
                    listWidth: 380,*/
                    hidden: true
                },
                type:'ComboRec',
                id_grupo:0,
                filters:{pfiltro:'suo.codigo#suo.nombre',type:'string'},
                grid:false,
                form:true
            },
            {
                config: {
                    name: 'importe_gasto',
                    fieldLabel: 'Gasto',
                    qtip:'Monto para ejecutar el gasto  presupuestario',
                    allowBlank: true,
                    //width: '100%',
                    anchor:'97%',
                    gwidth: 100,
                    //width: 380,
                    galign: 'right ',
                    maxLength: 100,
                    renderer:function (value,p,record){
                        if(record.data.tipo_reg != 'summary'){
                            return  String.format('{0}', Ext.util.Format.number(value,'0,000.00'));
                        }
                        else{
                            return  String.format('<b><font size=2 >{0}</font><b>', Ext.util.Format.number(value,'0,000.00'));
                        }
                    }
                },
                type: 'NumberField',
                filters: {pfiltro: 'transa.importe_gasto',type: 'numeric'},
                id_grupo: 1,
                grid: true,
                bottom_filter: true,
                form: true
            },
            {
                config: {
                    name: 'importe_recurso',
                    fieldLabel: 'Recurso',
                    qtip:'Monto para ejecutar el recurso presupeustario',
                    allowBlank: true,
                    //width: 380,
                    anchor:'97%',
                    gwidth: 100,
                    galign: 'right ',
                    maxLength: 100,
                    renderer:function (value,p,record){
                        if(record.data.tipo_reg != 'summary'){
                            return  String.format('{0}', Ext.util.Format.number(value,'0,000.00'));
                        }
                        else{
                            return  String.format('<b><font size=2 >{0}</font><b>', Ext.util.Format.number(value,'0,000.00'));
                        }
                    }
                },
                type: 'NumberField',
                filters: {pfiltro: 'transa.importe_recurso',type: 'numeric'},
                id_grupo: 1,
                grid: true,
                bottom_filter: true,
                form: true
            },

            {
                config: {
                    name: 'importe_debe',
                    fieldLabel: 'Debe',
                    allowBlank: true,
                    //width: 380,
                    anchor:'97%',
                    gwidth: 100,
                    galign: 'right ',
                    maxLength: 100,
                    renderer:function (value,p,record){
                        if(record.data.tipo_reg != 'summary'){
                            return  String.format('{0}', Ext.util.Format.number(value,'0,000.00'));
                        }
                        else{
                            return  String.format('<b><font size=2 >{0}</font><b>', Ext.util.Format.number(value,'0,000.00'));
                        }
                    }
                },
                type: 'NumberField',
                filters: {pfiltro: 'transa.importe_debe',type: 'numeric'},
                id_grupo: 1,
                grid: true,
                bottom_filter: true,
                form: true
            },
            {
                config: {
                    name: 'importe_haber',
                    fieldLabel: 'Haber',
                    allowBlank: true,
                    //width: 380,
                    anchor:'97%',
                    gwidth: 100,
                    galign: 'right ',
                    maxLength: 100,
                    renderer:function (value,p,record){
                        if(record.data.tipo_reg != 'summary'){
                            return  String.format('{0}', Ext.util.Format.number(value,'0,000.00'));
                        }
                        else{
                            return  String.format('<b><font size=2 >{0}</font><b>', Ext.util.Format.number(value,'0,000.00'));
                        }
                    }
                },
                type: 'NumberField',
                filters: {pfiltro: 'transa.importe_haber',type: 'numeric'},
                id_grupo: 1,
                grid: true,
                bottom_filter: true,
                form: true
            },
            {
                config: {
                    name: 'importe_debe_mb',
                    fieldLabel: 'Debe MB',
                    allowBlank: true,
                    width: 380,
                    gwidth: 100,
                    galign: 'right ',
                    maxLength: 100,
                    renderer:function (value,p,record){
                        if(record.data.tipo_reg != 'summary'){
                            return  String.format('{0}', Ext.util.Format.number(value,'0,000.00'));
                        }
                        else{
                            return  String.format('<b><font size=2 >{0}</font><b>', Ext.util.Format.number(value,'0,000.00'));
                        }
                    }
                },
                type: 'NumberField',
                filters: {pfiltro: 'transa.importe_debe_mb',type: 'numeric'},
                id_grupo: 1,
                grid: true,
                form: false
            },
            {
                config: {
                    name: 'importe_haber_mb',
                    fieldLabel: 'Haber MB',
                    allowBlank: true,
                    width: 380,
                    gwidth: 100,
                    galign: 'right ',
                    maxLength: 100,
                    renderer:function (value,p,record){
                        if(record.data.tipo_reg != 'summary'){
                            return  String.format('{0}', Ext.util.Format.number(value,'0,000.00'));
                        }
                        else{
                            return  String.format('<b><font size=2 >{0}</font><b>', Ext.util.Format.number(value,'0,000.00'));
                        }
                    }
                },
                type: 'NumberField',
                filters: {pfiltro: 'transa.importe_haber_mb',type: 'numeric'},
                id_grupo: 1,
                grid: true,
                form: false
            },

            {
                config: {
                    name: 'importe_debe_mt',
                    fieldLabel: 'Debe MT',
                    allowBlank: true,
                    width: 380,
                    gwidth: 100,
                    galign: 'right ',
                    maxLength: 100,
                    renderer:function (value,p,record){
                        if(record.data.tipo_reg != 'summary'){
                            return  String.format('{0}', Ext.util.Format.number(value,'0,000.00'));
                        }
                        else{
                            return  String.format('<b><font size=2 >{0}</font><b>', Ext.util.Format.number(value,'0,000.00'));
                        }
                    }
                },
                type: 'NumberField',
                filters: {pfiltro: 'transa.importe_debe_mt', type: 'numeric'},
                id_grupo: 1,
                grid: true,
                form: false
            },
            {
                config: {
                    name: 'importe_haber_mt',
                    fieldLabel: 'Haber MT',
                    allowBlank: true,
                    width: 380,
                    gwidth: 100,
                    galign: 'right ',
                    maxLength: 100,
                    renderer:function (value,p,record){
                        if(record.data.tipo_reg != 'summary'){
                            return  String.format('{0}', Ext.util.Format.number(value,'0,000.00'));
                        }
                        else{
                            return  String.format('<b><font size=2 >{0}</font><b>', Ext.util.Format.number(value,'0,000.00'));
                        }
                    }
                },
                type: 'NumberField',
                filters: {pfiltro: 'transa.importe_haber_mt',type: 'numeric'},
                id_grupo: 1,
                grid: true,
                form: false
            },

            {
                config: {
                    name: 'importe_debe_ma',
                    fieldLabel: 'Debe MA',
                    allowBlank: true,
                    width: 380,
                    gwidth: 100,
                    galign: 'right ',
                    maxLength: 100,
                    renderer:function (value,p,record){
                        if(record.data.tipo_reg != 'summary'){
                            return  String.format('{0}', Ext.util.Format.number(value,'0,000.00'));
                        }
                        else{
                            return  String.format('<b><font size=2 >{0}</font><b>', Ext.util.Format.number(value,'0,000.00'));
                        }
                    }
                },
                type: 'NumberField',
                filters: {pfiltro: 'transa.importe_debe_ma', type: 'numeric'},
                id_grupo: 1,
                grid: true,
                form: false
            },
            {
                config: {
                    name: 'importe_haber_ma',
                    fieldLabel: 'Haber MA',
                    allowBlank: true,
                    width: 380,
                    gwidth: 100,
                    galign: 'right ',
                    maxLength: 100,
                    renderer:function (value,p,record){
                        if(record.data.tipo_reg != 'summary'){
                            return  String.format('{0}', Ext.util.Format.number(value,'0,000.00'));
                        }
                        else{
                            return  String.format('<b><font size=2 >{0}</font><b>', Ext.util.Format.number(value,'0,000.00'));
                        }
                    }
                },
                type: 'NumberField',
                filters: {pfiltro: 'transa.importe_haber_ma',type: 'numeric'},
                id_grupo: 1,
                grid: true,
                form: false
            },

            {
                config : {
                    name : 'tipo_cambio',
                    fieldLabel : 'TC 1',
                    allowBlank : false,
                    //width: 380,
                    width:'97%',
                    gwidth : 70,
                    galign: 'right ',
                    maxLength : 20,
                    decimalPrecision : 6
                },
                type : 'NumberField',
                filters : {
                    pfiltro : 'incbte.tipo_cambio',
                    type : 'numeric'
                },
                id_grupo : 1,
                grid : true,
                form : true
            }, {
                config : {
                    name : 'tipo_cambio_2',
                    fieldLabel : 'TC 2',
                    allowBlank : false,
                    //width: 380,
                    width:'97%',
                    galign: 'right ',
                    gwidth : 70,
                    maxLength : 20,
                    decimalPrecision : 6
                },
                type : 'NumberField',
                filters : {
                    pfiltro : 'incbte.tipo_cambio_2',
                    type : 'numeric'
                },
                id_grupo : 1,
                grid : true,
                form : true
            }, {
                config : {
                    name : 'tipo_cambio_3',
                    fieldLabel : 'TC 3',
                    allowBlank : false,
                    //width: 380,
                    width : '97%',
                    galign: 'right ',
                    gwidth : 70,
                    maxLength : 20,
                    decimalPrecision : 6
                },
                type : 'NumberField',
                filters : {
                    pfiltro : 'incbte.tipo_cambio_3',
                    type : 'numeric'
                },
                id_grupo : 1,
                grid : true,
                form : true
            },

            {
                config:{
                    name: 'glosa',
                    fieldLabel: 'Glosa',
                    allowBlank: true,
                    //width: 380,
                    anchor:'97%',
                    gwidth: 300,
                    maxLength:1000,
                    renderer: function(val){if (val != ''){return '<div class="gridmultiline">'+val+'</div>';}}
                },
                type:'TextArea',
                filters:{pfiltro:'transa.glosa',type:'string'},
                id_grupo:1,
                bottom_filter: true,
                grid:true,
                form:true
            },


            {
                config:{
                    name: 'estado_reg',
                    fieldLabel: 'Estado Reg.',
                    allowBlank: true,
                    width: 380,
                    gwidth: 100,
                    maxLength:10
                },
                type:'Field',
                filters:{pfiltro:'transa.estado_reg',type:'string'},
                id_grupo:1,
                grid:true,
                form:false
            },

            {
                config:{
                    name: 'usr_reg',
                    fieldLabel: 'Creado por',
                    allowBlank: true,
                    width: 380,
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
                    name: 'fecha_reg',
                    fieldLabel: 'Fecha creación',
                    allowBlank: true,
                    width: 380,
                    gwidth: 100,
                    format: 'd/m/Y',
                    renderer:function (value,p,record){return value?value.dateFormat('d/m/Y H:i:s'):''}
                },
                type:'DateField',
                filters:{pfiltro:'transa.fecha_reg',type:'date'},
                id_grupo:1,
                grid:true,
                form:false
            },
            {
                config:{
                    name: 'usr_mod',
                    fieldLabel: 'Modificado por',
                    allowBlank: true,
                    width: 380,
                    gwidth: 100,
                    maxLength:4
                },
                type:'Field',
                filters:{pfiltro:'usu2.cuenta',type:'string'},
                id_grupo:1,
                grid:true,
                form:false
            },
            {
                config:{
                    name: 'fecha_mod',
                    fieldLabel: 'Fecha Modif.',
                    allowBlank: true,
                    width: 380,
                    gwidth: 100,
                    format: 'd/m/Y',
                    renderer:function (value,p,record){return value?value.dateFormat('d/m/Y H:i:s'):''}
                },
                type:'DateField',
                filters:{pfiltro:'transa.fecha_mod',type:'date'},
                id_grupo:1,
                grid:true,
                form:false
            }
        ],
        tam_pag:50,
        title:'Imputaciones',
        ActSave:'../../sis_contabilidad/control/IntTransaccion/insertarIntTransaccion',
        ActDel:'../../sis_contabilidad/control/IntTransaccion/eliminarIntTransaccion',
        ActList:'../../sis_contabilidad/control/IntTransaccion/listarIntTransaccion',
        id_store:'id_int_transaccion',
        fields: [
            {name:'id_int_transaccion', type: 'numeric'},
            {name:'id_partida', type: 'numeric'},
            {name:'id_centro_costo', type: 'numeric'},
            {name:'id_partida_ejecucion', type: 'numeric'},
            {name:'estado_reg', type: 'string'},
            {name:'id_int_transaccion_fk', type: 'numeric'},
            {name:'id_cuenta', type: 'numeric'},
            {name:'glosa', type: 'string'},
            {name:'id_int_comprobante', type: 'numeric'},
            {name:'id_auxiliar', type: 'numeric'},
            {name:'id_usuario_reg', type: 'numeric'},
            {name:'fecha_reg', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
            {name:'id_usuario_mod', type: 'numeric'},
            {name:'fecha_mod', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
            {name:'usr_reg', type: 'string'},
            {name:'usr_mod', type: 'string'},
            {name:'importe_debe', type: 'numeric'},
            {name:'importe_haber', type: 'numeric'},
            {name:'importe_gasto', type: 'numeric'},
            {name:'importe_recurso', type: 'numeric'},
            {name:'importe_debe_mb', type: 'numeric'},
            {name:'importe_haber_mb', type: 'numeric'},
            {name:'importe_gasto_mb', type: 'numeric'},
            {name:'importe_recurso_mb', type: 'numeric'},
            {name:'desc_cuenta', type: 'string'},
            {name:'desc_auxiliar', type: 'string'},
            {name:'desc_partida', type: 'string'},
            {name:'desc_centro_costo', type: 'string'},'tipo_partida','id_orden_trabajo','desc_orden','tipo_reg',
            'banco', 'forma_pago', 'nombre_cheque_trans', 'nro_cuenta_bancaria_trans', 'nro_cheque',
            'importe_debe_mt',	'importe_haber_mt','importe_gasto_mt','importe_recurso_mt',
            'importe_debe_ma',	'importe_haber_ma','importe_gasto_ma','importe_recurso_ma',
            'id_moneda_tri','id_moneda_act','id_moneda', 'tipo_cambio','tipo_cambio_2','tipo_cambio_3',
            'codigo_categoria','actualizacion','triangulacion','id_suborden','desc_suborden','codigo_ot', 'planilla',
            {name:'id_concepto_ingas', type: 'numeric'},
            {name:'desc_ingas', type: 'string'}

        ],

        pdfOrientacion: 'L',
        title2:'Transacciones del Comprobante',

        ExtraColumExportDet:[{
            label:'Partida',
            name:'desc_partida',
            width:'200',
            type:'string',
            gdisplayField:'desc_partida',
            value:'desc_partida'
        },
            {
                label:'Cuenta',
                name:'desc_cuenta',
                width:'200',
                type:'string',
                gdisplayField:'desc_cuenta',
                value:'desc_cuenta'
            },
            {
                label:'Orden',
                name:'codigo_ot',
                width:'200',
                type:'string',
                gdisplayField:'codigo_ot',
                value:'codigo_ot'
            },
            {
                label:'Categoria',
                name:'codigo_categoria',
                width:'200',
                type:'string',
                gdisplayField:'codigo_categoria',
                value:'codigo_categoria'
            },



        ],

        rowExpander: new Ext.ux.grid.RowExpander({
            tpl : new Ext.Template(
                '<br>',
                '<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Glosa:&nbsp;&nbsp;</b> {glosa} </p><br>'
            )
        }),

        arrayDefaultColumHidden:['id_cuenta','id_partida','fecha_mod','usr_reg','usr_mod','glosa','estado_reg','fecha_reg'],



        sortInfo:{
            field: 'id_int_transaccion',
            direction: 'ASC'
        },
        bdel: true,
        bsave: false,
        loadValoresIniciales:function(){
            Phx.vista.IntTransaccionRubro.superclass.loadValoresIniciales.call(this);

            this.Cmp.id_int_comprobante.setValue(this.maestro.id_int_comprobante);


        },
        onReloadPage:function(m){
            this.maestro=m;


            //(f.e.a) para verificar si el proceso le correponde a planillas
            var bandera_planilla = '';
            if(this.maestro.desc_subsistema == 'Sistema de Planillas')
                bandera_planilla = 'true';
            else
                bandera_planilla = 'false';

            this.store.baseParams={id_int_comprobante:this.maestro.id_int_comprobante, id_moneda:this.maestro.id_moneda, planilla : bandera_planilla};
            this.Cmp.id_centro_costo.store.baseParams.id_depto = this.maestro.id_depto;
            this.load({params:{start:0, limit:this.tam_pag}});
            this.Cmp.id_centro_costo.store.baseParams.id_depto = this.maestro.id_depto;

            //Se obtiene la gestión en función de la fecha del comprobante para filtrar partidas, cuentas, etc.
            var fecha=new Date(this.maestro.fecha);
            this.maestro.id_gestion = this.getGestion(fecha);
            //Se setea el combo de moneda con el valor del padre



            this.setColumnHeader('importe_debe', this.Cmp.importe_debe.fieldLabel +' '+this.maestro.desc_moneda);
            this.setColumnHeader('importe_haber', this.Cmp.importe_haber.fieldLabel +' '+this.maestro.desc_moneda);

            this.mostrarColumnaByName('importe_debe_mt');
            this.mostrarColumnaByName('importe_haber_mt');
            //si a moneda del comprobate es base ocultamos la columnas duplicadas
            if(this.maestro.id_moneda_base == this.maestro.id_moneda){
                this.ocultarColumnaByName('importe_debe_mb');
                this.ocultarColumnaByName('importe_haber_mb');
            }
            else{
                this.mostrarColumnaByName('importe_debe_mb');
                this.mostrarColumnaByName('importe_haber_mb');
            }

            if(this.maestro.id_moneda_act == this.maestro.id_moneda  ||this.maestro.id_moneda_act == this.maestro.id_moneda_tri){
                this.ocultarColumnaByName('importe_debe_ma');
                this.ocultarColumnaByName('importe_haber_ma');
            }
            else{
                this.mostrarColumnaByName('importe_debe_ma');
                this.mostrarColumnaByName('importe_haber_ma');
            }
            this.getConfigCambiaria();

            if ( this.maestro.estado_reg == 'borrador' ) {
                this.getBoton('new').enable();
                this.getBoton('edit').enable();
                this.getBoton('del').enable();
            }else{
                this.getBoton('new').disable();
                this.getBoton('edit').disable();
                this.getBoton('del').disable();
            }
        },

        preparaMenu:function(){
            var rec = this.sm.getSelected();
            var tb = this.tbar;
            console.log('rec.data.tipo_reg',rec.data.tipo_reg);
            if(rec.data.tipo_reg != 'summary'){
                Phx.vista.IntTransaccionRubro.superclass.preparaMenu.call(this);
            }else{
                if (tb && this.bedit) {
                    this.getBoton('edit').disable();
                }
                if (tb && this.bdel) {
                    this.getBoton('del').disable();
                }
            }

            if(rec.data.actualizacion == 'si'){
                this.getBoton('edit').disable();
            }

            if(this.maestro.sw_editable == 'no'){
                if (tb && this.bedit) {
                    this.getBoton('edit').disable();
                }
                if (tb && this.bdel) {
                    this.getBoton('del').disable();
                }
                if (tb && this.bnew) {
                    this.getBoton('new').disable();
                }
            }

            if ( this.maestro.estado_reg == 'borrador' ) {
                this.getBoton('new').enable();
                this.getBoton('edit').enable();
                this.getBoton('del').enable();
            }else{
                this.getBoton('new').disable();
                this.getBoton('edit').disable();
                this.getBoton('del').disable();
            }
        },

        liberaMenu: function() {
            var tb = Phx.vista.IntTransaccionRubro.superclass.liberaMenu.call(this);

            if(this.maestro.sw_editable == 'no'){
                if (tb && this.bnew) {
                    this.getBoton('new').disable();
                }
            }
            if ( this.maestro.estado_reg != 'borrador' ) {
                this.getBoton('new').disable();
                this.getBoton('edit').disable();
                this.getBoton('del').disable();
            }
        },

        getGestion:function(x){
            if(Ext.isDate(x)){
                Ext.Ajax.request({
                    url:'../../sis_parametros/control/Gestion/obtenerGestionByFecha',
                    params:{fecha:x},
                    success:this.successGestion,
                    failure: this.conexionFailure,
                    timeout:this.timeout,
                    scope:this
                });
            } else{
                alert('Error al obtener gestión: fecha inválida')
            }
        },
        obtenerVariableGlobal: function(param){
            //Verifica que la fecha y la moneda hayan sido elegidos
            //Phx.CP.loadingShow();
            Ext.Ajax.request({
                url:'../../sis_seguridad/control/Subsistema/obtenerVariableGlobal',
                params:{
                    codigo: param
                },
                success: function(resp){
                    //Phx.CP.loadingHide();
                    var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));

                    if (reg.ROOT.error) {
                        Ext.Msg.alert('Error','Error a recuperar la variable global')
                    } else {
                        if (param == 'conta_partidas'){
                            if(reg.ROOT.datos.valor == 'no'){
                                this.ocultarComponente(this.Cmp.id_partida);
                            }

                            this.obtenerVariableGlobal('conta_ejecucion_igual_pres_conta');
                        }

                        if (param == 'conta_ejecucion_igual_pres_conta'){
                            if(reg.ROOT.datos.valor == 'si'){
                                this.ocultarComponente(this.Cmp.importe_gasto);
                                this.ocultarComponente(this.Cmp.importe_recurso);
                            }
                        }

                    }
                },
                failure: this.conexionFailure,
                timeout: this.timeout,
                scope:this
            });

        },
        successGestion: function(resp){
            var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
            if(!reg.ROOT.error){
                var id_gestion = reg.ROOT.datos.id_gestion;
                //Setea los stores de partida, cuenta, etc. con la gestion obtenida
                Ext.apply(this.Cmp.id_cuenta.store.baseParams,{id_gestion: id_gestion})
                Ext.apply(this.Cmp.id_partida.store.baseParams,{id_gestion: id_gestion})
                Ext.apply(this.Cmp.id_centro_costo.store.baseParams,{id_gestion: id_gestion})

            } else{
                alert('Error al obtener la gestión. Cierre y vuelva a intentarlo')
            }
        },

        getConfigCambiaria : function() {

            var localidad = this.maestro.localidad;


            Phx.CP.loadingShow();
            Ext.Ajax.request({
                url:'../../sis_contabilidad/control/ConfigCambiaria/getConfigCambiaria',
                params:{
                    fecha: this.maestro.fecha,
                    id_moneda: this.maestro.id_moneda,
                    localidad: localidad,
                    sw_valores: 'no',
                    forma_cambio: 'Oficial'
                }, success: function(resp) {



                    Phx.CP.loadingHide();
                    var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
                    if (reg.ROOT.error) {
                        Ext.Msg.alert('Error', 'Validación no realizada: ' + reg.ROOT.error)
                    } else {
                        //cambia labels
                        this.labeTc1 = reg.ROOT.datos.v_tc1 +' (tc)';
                        this.labeTc2 = reg.ROOT.datos.v_tc2 +' (tc)';
                        this.labeTc3 = reg.ROOT.datos.v_tc3 +' (tc)';

                        this.setColumnHeader('tipo_cambio', this.labeTc1);
                        this.setColumnHeader('tipo_cambio_2', this.labeTc2);
                        this.setColumnHeader('tipo_cambio_3', this.labeTc3);

                    }
                }, failure: function(a,b,c,d){
                    this.conexionFailure(a,b,c,d)
                },
                timeout: this.timeout,
                scope:this
            });
        },

        setLabelsTc: function(){
            this.Cmp.tipo_cambio.label.update(this.labeTc1);
            this.Cmp.tipo_cambio_2.label.update(this.labeTc2);
            this.Cmp.tipo_cambio_3.label.update(this.labeTc3);
        },

        onButtonEdit:function(){
            this.swButton = 'EDIT';
            var rec = this.sm.getSelected().data;
            Phx.vista.IntTransaccionRubro.superclass.onButtonEdit.call(this);
            this.setModificadoCombos();
            this.setLabelsTc();
        },

        onButtonNew: function() {
            this.swButton = 'NEW';
            this.sw_valores = 'si';
            Phx.vista.IntTransaccionRubro.superclass.onButtonNew.call(this);
            this.setModificadoCombos()
            this.Cmp.tipo_cambio.setValue(this.maestro.tipo_cambio);
            this.Cmp.tipo_cambio_2.setValue(this.maestro.tipo_cambio_2);
            this.Cmp.tipo_cambio_3.setValue(this.maestro.tipo_cambio_3);
            this.setLabelsTc();
        }


    })
</script>