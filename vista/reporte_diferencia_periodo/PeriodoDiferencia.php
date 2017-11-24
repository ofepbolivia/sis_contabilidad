<?php
/**
 *@package pXP
 *@file gen-SolicitudMayor500000.php
 *@author  (admin)
 *@date 05-09-2017 15:19:59
 *@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
 */

header("content-type: text/javascript; charset=UTF-8");
?>
<script>
    Phx.vista.PeriodoDiferencia=Ext.extend(Phx.gridInterfaz,{

            constructor:function(config){
                this.maestro=config.maestro;
                //llama al constructor de la clase padre
                Phx.vista.PeriodoDiferencia.superclass.constructor.call(this,config);
                this.init();

            },

            Atributos:[
                {
                    //configuracion del componente
                    config:{
                        labelSeparator:'',
                        inputType:'hidden',
                        name: 'id_doc_compra_venta'
                    },
                    type:'Field',
                    form:true
                },
                {
                    config:{
                        name: 'nro_tramite',
                        fieldLabel: 'Nro. Tramite',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 140,
                        maxLength:70
                    },
                    type:'TextField',
                    filters:{pfiltro:'dff.nro_tramite',type:'string'},
                    id_grupo:1,
                    grid:true,
                    form:false
                },
                {
                    config:{
                        name: 'desc_plantilla',
                        fieldLabel: 'Tipo Documento',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 150,
                        maxLength:255
                    },
                    type:'TextField',
                    filters:{pfiltro:'dff.desc_plantilla',type:'string'},
                    id_grupo:1,
                    grid:true,
                    form:false
                },
                {
                    config:{
                        name: 'fecha',
                        fieldLabel: 'Fecha',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        format: 'd/m/Y',
                        renderer:function (value,p,record){return value?value.dateFormat('d/m/Y'):''}
                    },
                    type:'DateField',
                    filters:{pfiltro:'dff.fecha',type:'date'},
                    id_grupo:1,
                    grid:true,
                    form:true
                },
                {
                    config:{
                        name: 'periodo_doc',
                        fieldLabel: 'Periodo Modificado ',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 120,
                        maxLength:8
                    },
                    type:'TextField',
                    filters:{pfiltro:'dff.periodo_doc',type:'string'},
                    id_grupo:1,
                    grid:true,
                    form:true
                },
                {
                    config:{
                        name: 'periodo',
                        fieldLabel: 'Periodo Actual',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 130,
                        maxLength:4
                    },
                    type:'NumberField',
                    filters:{pfiltro:'dff.periodo',type:'numeric'},
                    id_grupo:1,
                    grid:true,
                    form:true
                },
                {
                    config:{
                        name: 'razon_social',
                        fieldLabel: 'Razon Social',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength:-5
                    },
                    type:'TextField',
                    filters:{pfiltro:'dff.razon_social',type:'string'},
                    id_grupo:1,
                    grid:true,
                    form:true
                },
                {
                    config:{
                        name: 'nro_documento',
                        fieldLabel: 'Nro Documento',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength:100
                    },
                    type:'TextField',
                    filters:{pfiltro:'dff.nro_documento',type:'string'},
                    id_grupo:1,
                    grid:true,
                    form:true
                },
                {
                    config:{
                        name: 'nro_autorizacion',
                        fieldLabel: 'Nro. Autorizacion',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength:200
                    },
                    type:'TextField',
                    filters:{pfiltro:'dff.nro_autorizacion',type:'string'},
                    id_grupo:1,
                    grid:true,
                    form:true
                },
                {
                    config:{
                        name: 'nit',
                        fieldLabel: 'Nit',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength:100
                    },
                    type:'TextField',
                    filters:{pfiltro:'dff.nit',type:'string'},
                    id_grupo:1,
                    grid:true,
                    form:true
                },
                {
                    config:{
                        name: 'codigo_control',
                        fieldLabel: 'Codigo Control',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength:200
                    },
                    type:'TextField',
                    filters:{pfiltro:'dff.codigo_control',type:'string'},
                    id_grupo:1,
                    grid:true,
                    form:true
                },
                {
                    config:{
                        name: 'importe_doc',
                        fieldLabel: 'Monto',
                        allowBlank: false,
                        anchor: '80%',
                        gwidth: 80,
                        galign: 'right ',
                        maxLength:1179650,
                        renderer:function (value,p,record){

                                return  String.format('<b><font size=2 >{0}</font><b>', Ext.util.Format.number(value,'0,000.00'));


                        }
                    },
                    type:'NumberField',
                    filters:{pfiltro:'dff.importe_doc',type:'numeric'},
                    id_grupo:1,

                    grid:true,
                    form:false
                },
                {
                    config:{
                        name: 'importe_excento',
                        fieldLabel: 'Exento',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        galign: 'right ',
                        renderer:function (value,p,record){
                                return  String.format('<b><font size=2 >{0}</font><b>', Ext.util.Format.number(value,'0,000.00'));

                        }
                    },
                    type: 'NumberField',
                    filters: {pfiltro:'dff.importe_excento',type:'numeric'},
                    id_grupo:1,

                    grid: true,
                    form: false
                },
                {
                    config:{
                        name: 'importe_descuento',
                        fieldLabel: 'Descuento',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        galign: 'right ',
                        renderer:function (value,p,record){

                                return  String.format('<b><font size=2 >{0}</font><b>', Ext.util.Format.number(value,'0,000.00'));


                        }
                    },
                    type:'NumberField',
                    filters:{pfiltro:'dff.importe_descuento',type:'numeric'},
                    id_grupo:1,

                    grid:true,
                    form:false
                },
                {
                    config:{
                        name: 'importe_neto',
                        fieldLabel: 'Importe c/d',
                        allowBlank: false,
                        anchor: '80%',
                        gwidth: 100,
                        galign: 'right ',
                        maxLength:1179650,
                        renderer:function (value,p,record){

                                return  String.format('<b><font size=2 >{0}</font><b>', Ext.util.Format.number(value,'0,000.00'));


                        }
                    },
                    type:'NumberField',
                    filters:{pfiltro:'dff.importe_doc',type:'numeric'},
                    id_grupo:1,
                    grid:true,
                    form:false
                },
                {
                    config:{
                        name: 'importe_iva',
                        fieldLabel: 'IVA',
                        allowBlank: true,
                        readOnly:true,
                        anchor: '80%',
                        gwidth: 100,
                        galign: 'right ',
                        renderer:function (value,p,record){

                                return  String.format('<b><font size=2 >{0}</font><b>', Ext.util.Format.number(value,'0,000.00'));


                        }
                    },
                    type: 'NumberField',
                    filters: { pfiltro:'dff.importe_iva',type:'numeric'},
                    id_grupo: 1,

                    grid: true,
                    form: false
                },
                {
                    config:{
                        name: 'importe_pago_liquido',
                        fieldLabel: 'Liquido Pagado',
                        allowBlank: true,
                        readOnly:true,
                        anchor: '80%',
                        gwidth: 100,
                        galign: 'right ',
                        renderer:function (value,p,record){

                                return  String.format('<b><font size=2 >{0}</font><b>', Ext.util.Format.number(value,'0,000.00'));


                        }
                    },
                    type:'NumberField',
                    filters:{pfiltro:'dff.importe_pago_liquido',type:'numeric'},
                    id_grupo:1,

                    grid:true,
                    form: false
                },
                {
                    config:{
                        name: 'importe_ice',
                        fieldLabel: 'Importe Ice',
                        allowBlank: true,
                        readOnly:true,
                        anchor: '80%',
                        gwidth: 100,
                        galign: 'right ',
                        renderer:function (value,p,record){

                            return  String.format('<b><font size=2 >{0}</font><b>', Ext.util.Format.number(value,'0,000.00'));


                        }
                    },
                    type:'NumberField',
                    filters:{pfiltro:'dff.importe_ice',type:'numeric'},
                    id_grupo:1,

                    grid:false,
                    form: false
                },
                {
                    config:{
                        name: 'importe_it',
                        fieldLabel: 'Importe It',
                        allowBlank: true,
                        readOnly:true,
                        anchor: '80%',
                        gwidth: 100,
                        galign: 'right ',
                        renderer:function (value,p,record){

                            return  String.format('<b><font size=2 >{0}</font><b>', Ext.util.Format.number(value,'0,000.00'));


                        }
                    },
                    type:'NumberField',
                    filters:{pfiltro:'dff.importe_it',type:'numeric'},
                    id_grupo:1,

                    grid:true,
                    form: false
                },

                {
                    config:{
                        name: 'desc_persona',
                        fieldLabel: 'Usuario',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 200,
                        maxLength:-5
                    },
                    type:'TextField',
                    filters:{pfiltro:'dff.desc_persona',type:'string'},
                    id_grupo:1,
                    grid:true,
                    form:true
                },
                {
                    config:{
                        name: 'nombre',
                        fieldLabel: 'Departamento Contabolidad',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 200,
                        maxLength:-5
                    },
                    type:'TextField',
                    filters:{pfiltro:'dff.nombre',type:'string'},
                    id_grupo:1,
                    grid:true,
                    form:true
                },
                {
                    config:{
                        name: 'tipo',
                        fieldLabel: 'Tipo',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength:25
                    },
                    type:'TextField',
                    filters:{pfiltro:'dff.tipo',type:'string'},
                    id_grupo:1,
                    grid:true,
                    form:true
                },
                {
                    config:{
                        name: 'obs',
                        fieldLabel: 'obs',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength:-5
                    },
                    type:'TextField',
                    filters:{pfiltro:'dff.obs',type:'string'},
                    id_grupo:1,
                    grid:true,
                    form:true
                }
            ],
        tam_pag:50,
        title:'Diferencia periodo ',

        ActList:'../../sis_contabilidad/control/DocCompraVenta/listarDiferenciaPeriodo',
        id_store:'id_doc_compra_venta',
        fields: [
            {name:'id_doc_compra_venta', type: 'numeric'},
            {name:'nro_documento', type: 'string'},
            {name:'nro_autorizacion', type: 'string'},
            {name:'desc_persona', type: 'string'},
            {name:'importe_iva', type: 'numeric'},
            {name:'periodo_doc', type: 'string'},
            {name:'nro_tramite', type: 'string'},
            {name:'nombre', type: 'string'},
            {name:'codigo_control', type: 'string'},
            {name:'fecha', type: 'date',dateFormat:'Y-m-d'},
            {name:'importe_ice', type: 'numeric'},
            {name:'importe_pago_liquido', type: 'numeric'},
            {name:'tipo', type: 'string'},
            {name:'obs', type: 'string'},
            {name:'nit', type: 'string'},
            {name:'desc_plantilla', type: 'string'},
            {name:'razon_social', type: 'string'},
            {name:'importe_doc', type: 'numeric'},
            {name:'importe_excento', type: 'numeric'},
            {name:'periodo', type: 'numeric'},
            {name:'importe_neto', type: 'numeric'},
            {name:'importe_it', type: 'numeric'},
            {name:'importe_descuento_ley', type: 'numeric'},
            {name:'gestion', type: 'numeric'}


        ],
        sortInfo:{
            field: 'id_doc_compra_venta',
            direction: 'ASC'
        },
            loadValoresIniciales:function(){
                Phx.vista.SolicitudMayor500000.superclass.loadValoresIniciales.call(this);
                //this.getComponente('id_int_comprobante').setValue(this.maestro.id_int_comprobante);
            },
            onReloadPage:function(param){
                //Se obtiene la gestión en función de la fecha del comprobante para filtrar partidas, cuentas, etc.
                var me = this;
                this.initFiltro(param);
            },

            initFiltro: function(param){
                this.store.baseParams=param;
                this.load( { params: { start:0, limit: this.tam_pag } });
            },

            bdel:false,
            bsave:false,
            bnew:false,
            bedit:false
        }
    )
</script>

