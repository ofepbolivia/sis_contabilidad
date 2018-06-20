<?php
/**
 *@package pXP
 *@file gen-HistorialRegCompras.php
 *@author  (franklin.espinoza)
 *@date 07-06-2018 15:14:54
 *@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
 */

header("content-type: text/javascript; charset=UTF-8");
?>
<script>
    Phx.vista.HistorialRegCompras=Ext.extend(Phx.gridInterfaz,{

        constructor:function(config){
            this.maestro=config.maestro;
            //llama al constructor de la clase padre
            Phx.vista.HistorialRegCompras.superclass.constructor.call(this,config);
            this.init();
            //this.load({params:{start:0, limit:this.tam_pag}})
        },

        Atributos:[
            {
                //configuracion del componente
                config:{
                    labelSeparator:'',
                    inputType:'hidden',
                    name: 'id_historial_reg_compras'
                },
                type:'Field',
                form:true
            },
            {
                //configuracion del componente
                config:{
                    labelSeparator:'',
                    inputType:'hidden',
                    name: 'id_doc_compra_venta,'
                },
                type:'Field',
                form:true
            },

            {
                config:{
                    name: 'id_funcionario',
                    fieldLabel: 'Funcionario',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 250,
                    maxLength:25,
                    renderer: function (value, p, record) {
                        return String.format('<p style="color: green;">{0}</p>', record.data['desc_func']);
                    }
                },
                type:'TextField',
                filters:{pfiltro:'hrc.nit',type:'string'},
                id_grupo:1,
                grid:true,
                form:true
            },

            {
                config:{
                    name: 'nit',
                    fieldLabel: 'NIT',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength:25
                },
                type:'TextField',
                filters:{pfiltro:'tdc.nit',type:'string'},
                id_grupo:1,
                grid:true,
                form:true,
                bottom_filter : true
            },

            {
                config:{
                    name: 'nro_documento',
                    fieldLabel: 'N° Factura',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength:25
                },
                type:'TextField',
                filters:{pfiltro:'tdc.nro_documento',type:'string'},
                id_grupo:1,
                grid:true,
                form:true,
                bottom_filter : true
            },

            {
                config:{
                    name: 'nro_autorizacion',
                    fieldLabel: 'N° Autorización',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 150,
                    maxLength:25
                },
                type:'TextField',
                filters:{pfiltro:'tdc.nro_autorizacion',type:'string'},
                id_grupo:1,
                grid:true,
                form:true,
                bottom_filter : true
            },

            {
                config:{
                    name: 'razon_social',
                    fieldLabel: 'Razón Social',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 250,
                    maxLength:25
                },
                type:'TextField',
                filters:{pfiltro:'tdc.razon_social',type:'string'},
                id_grupo:1,
                grid:true,
                form:true,
                bottom_filter : true
            },

            {
                config:{
                    name: 'codigo_control',
                    fieldLabel: 'Codigo Control',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 150,
                    maxLength:25
                },
                type:'TextField',
                filters:{pfiltro:'tdc.codigo_control',type:'string'},
                id_grupo:1,
                grid:true,
                form:true,
                bottom_filter : true
            },

            {
                config:{
                    name: 'importe_neto',
                    fieldLabel: 'Importe',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength:25
                },
                type:'NumberField',
                filters:{pfiltro:'tdc.importe_neto',type:'numeric'},
                id_grupo:1,
                grid:true,
                form:true,
                bottom_filter : true
            },

            {
                config:{
                    name: 'nro_tramite',
                    fieldLabel: 'Nro Tramite',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 150,
                    maxLength:25
                },
                type:'TextField',
                filters:{pfiltro:'tdc.nro_tramite',type:'string'},
                id_grupo:1,
                grid:true,
                form:true,
                bottom_filter : true
            },



            {
                config:{
                    name: 'fecha_cambio',
                    fieldLabel: 'Fecha Modificación',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 150,
                    format: 'd/m/Y',
                    renderer:function (value,p,record){
                        console.log('feclas',value, record.data);
                        return value?value.dateFormat('d/m/Y H:i:s'):''
                    }
                },
                type:'DateField',
                filters:{pfiltro:'hrc.fecha_cambio',type:'date'},
                id_grupo:1,
                grid:true,
                form:true
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
                filters:{pfiltro:'hrc.estado_reg',type:'string'},
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
                filters:{pfiltro:'hrc.fecha_reg',type:'date'},
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
                filters:{pfiltro:'hrc.fecha_mod',type:'date'},
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
        title:'HistorialRegCompras',
        ActSave:'../../sis_contabilidad/control/HistorialRegCompras/insertarHistorialRegCompras',
        ActDel:'../../sis_contabilidad/control/HistorialRegCompras/eliminarHistorialRegCompras',
        ActList:'../../sis_contabilidad/control/HistorialRegCompras/listarHistorialRegCompras',
        id_store:'id_historial_reg_compras',
        fields: [
            {name:'id_historial_reg_compras', type: 'numeric'},
            {name:'id_doc_compra_venta,', type: 'numeric'},
            {name:'nit', type: 'string'},
            {name:'fecha_cambio', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
            {name:'nro_tramite', type: 'string'},
            {name:'nro_documento', type: 'string'},
            {name:'codigo_control', type: 'string'},
            {name:'nro_autorizacion', type: 'string'},
            {name:'id_funcionario', type: 'numeric'},
            {name:'estado_reg', type: 'string'},
            {name:'razon_social', type: 'string'},
            {name:'id_usuario_ai', type: 'numeric'},
            {name:'usuario_ai', type: 'string'},
            {name:'fecha_reg', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
            {name:'id_usuario_reg', type: 'numeric'},
            {name:'fecha_mod', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
            {name:'id_usuario_mod', type: 'numeric'},
            {name:'usr_reg', type: 'string'},
            {name:'usr_mod', type: 'string'},
            {name:'desc_func', type: 'string'},
            {name:'importe_neto', type: 'numeric'},

        ],

        arrayDefaultColumHidden:[
            'fecha_reg','fecha_mod','usr_reg','usr_mod', 'estado_reg'
        ],

        sortInfo:{
            field: 'razon_social',
            direction: 'ASC'
        },

        onReloadPage:function(param){
            this.maestro = param;
            this.store.baseParams = {id_doc_compra_venta: this.maestro.id_doc_compra_venta};
            this.load({params: {start: 0, limit: 50}});
        },

        /*loadValoresIniciales: function(){
            this.Cmp.id_reclamo.setValue(this.maestro.id_reclamo);
            Phx.vista.Informe.superclass.loadValoresIniciales.call(this);
        },*/

        bdel:false,
        bnew:false,
        bedit:false,
        bsave:false,
        btest:false
    })
</script>
		
		