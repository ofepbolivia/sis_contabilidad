<?php
/**
 *@package pXP
 *@file CorreccionDocVentaLog.php
 *@author franklin.espinoza
 *@date 25-01-2021
 *@description  Vista para modificar Nit, Razon Social
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
    Phx.vista.CorreccionDocVentaLog=Ext.extend(Phx.gridInterfaz,{
        viewConfig: {
            stripeRows: false,
            getRowClass: function(record) {
                return "x-selectable";
            }
        },
        constructor: function(config) {

            Phx.vista.CorreccionDocVentaLog.superclass.constructor.call(this,config);

            this.maestro = config;
            this.store.baseParams.id_factura = this.maestro.maestro.id_factura;

            this.init();
            this.load({params: {start: 0, limit: 50}});
        },


        Atributos:[
            {
                // configuracion del componente
                config:{
                    labelSeparator:'',
                    inputType:'hidden',
                    name: 'id_correcion_doc'
                },
                type:'Field',
                form:true

            },

            {
                config:{
                    name: 'fecha_reg',
                    fieldLabel: 'Fecha Modificacion',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 120,
                    format: 'd/m/Y',
                    renderer:function (value,p,record){
                        return String.format('<div style="color: #00B167; font-weight: bold;">{0}</div>', value)?
                               String.format('<div style="color: #00B167; font-weight: bold;">{0}</div>', value.dateFormat('d/m/Y H:i:s')):
                               ''
                    }

                },
                type:'DateField',
                filters:{pfiltro:'tcd.fecha_reg',type:'date'},
                id_grupo:1,
                grid:true,
                form:false
            },

            {
                config:{
                    name: 'nit_ci_cli',
                    fieldLabel: 'NIT/CI',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength:4,
                    renderer: function (value, p, record){
                        return String.format('<div style="color: #00B167; font-weight: bold;">{0}</div>', value);
                    }
                },
                type:'TextField',
                filters:{pfiltro:'tcd.nit_ci_cli',type:'string'},
                id_grupo:1,
                grid:true,
                form:false
            },

            {
                config:{
                    name: 'razon_social_cli',
                    fieldLabel: 'Razon Social',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 270,
                    maxLength:4,
                    renderer: function (value, p, record){
                        return String.format('<div style="color: #00B167; font-weight: bold;">{0}</div>', value);
                    }
                },
                type:'TextField',
                filters:{pfiltro:'tcd.razon_social_cli',type:'string'},
                id_grupo:1,
                grid:true,
                form:false
            },
            {
                config:{
                    name: 'usr_reg',
                    fieldLabel: 'Modificado Por',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 218,
                    maxLength:4,
                    renderer: function (value, p, record){
                        return String.format('<div style="color: #00B167; font-weight: bold;">{0}</div>', value);
                    }
                },
                type:'TextField',
                filters:{pfiltro:'usu1.cuenta',type:'string'},
                id_grupo:1,
                grid:true,
                form:false
            }/*,
            {
                config:{
                    name: 'estado_reg',
                    fieldLabel: 'Estado Reg.',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength:10,
                    renderer: function (value, p, record){
                        return String.format('<div style="color: #00B167; font-weight: bold;">{0}</div>', value);
                    }
                },
                type:'TextField',
                filters:{pfiltro:'tcd.estado_reg',type:'string'},
                id_grupo:1,
                grid:true,
                form:false
            }*/
        ],
        title:'Log Correcciones',
        ActList:'../../sis_contabilidad/control/DocCompraVenta/listaCorreccionVenta',
        id_store:'id_correcion_doc',
        fields: [
            {name:'id_correcion_doc'},
            {name:'fecha_reg', type: 'date', dateFormat: 'Y-m-d H:i:s.u'},
            {name:'nit_ci_cli', type: 'string'},
            {name:'razon_social_cli', type: 'string'},
            {name:'estado_reg', type: 'string'},
            {name:'usr_reg', type: 'string'}
        ],


        bedit:false,
        bnew:false,
        bdel:false,
        bsave:false,
        fwidth: '90%',
        fheight: '95%'
    });
</script>
