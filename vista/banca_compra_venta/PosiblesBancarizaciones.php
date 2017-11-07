<?php
/**
 *@package pXP
 *@file gen-Pais.php
 *@author  (favio figueroa)
 *@date 16-11-2015 16:56:32
 *@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
 */

header("content-type: text/javascript; charset=UTF-8");
?>
<script>
    Phx.vista.PosiblesBancarizaciones=Ext.extend(Phx.gridInterfaz,{




            constructor:function(config){


                this.maestro=config.maestro;
                //llama al constructor de la clase padre
                console.log(config)
                Phx.vista.PosiblesBancarizaciones.superclass.constructor.call(this,config);

                console.log('this',this);

                this.init();
                console.log('tipo monto',config);

                this.load({params:{start:0, limit:this.tam_pag,id_gestion:config.id_gestion}})






            },

            Atributos:[
                {
                    //configuracion del componente
                    config:{
                        labelSeparator:'',
                        inputType:'hidden',
                        name: 'id_banca_compra_venta',

                    },
                    type:'Field',
                    form:true
                },
                {
                    //configuracion del componente
                    config:{
                        labelSeparator:'',
                        inputType:'hidden',
                        name: 'id_plan_pago_pagado',

                    },
                    type:'Field',
                    form:true
                },
                {
                    //configuracion del componente
                    config:{
                        labelSeparator:'',
                        inputType:'hidden',
                        name: 'id_plan_pago_devengado',

                    },
                    type:'Field',
                    form:true
                },
                {
                    //configuracion del componente
                    config:{
                        labelSeparator:'',
                        inputType:'hidden',
                        name: 'id_libro_bancos',

                    },
                    type:'Field',
                    form:true
                },
                {
                    //configuracion del componente
                    config:{
                        labelSeparator:'',
                        inputType:'hidden',
                        name: 'id_documento',

                    },
                    type:'Field',
                    form:true
                },
                {
                    //configuracion del componente
                    config:{
                        labelSeparator:'',
                        inputType:'hidden',
                        name: 'id_cuenta_bancaria',

                    },
                    type:'Field',
                    form:true
                },

                {
                    //configuracion del componente
                    config:{
                        labelSeparator:'',
                        inputType:'hidden',
                        name: 'id_proveedor',

                    },
                    type:'Field',
                    form:true
                },
                {
                    //configuracion del componente
                    config:{
                        labelSeparator:'',
                        inputType:'hidden',
                        name: 'id_contrato',

                    },
                    type:'Field',
                    form:true
                },
                {
                    //configuracion del componente
                    config:{
                        labelSeparator:'',
                        inputType:'hidden',
                        name: 'id_cuenta_bancaria_plan_pago',

                    },
                    type:'Field',
                    form:true
                },

                {
                    //configuracion del componente
                    config:{
                        labelSeparator:'',
                        inputType:'hidden',
                        name: 'id_proceso_wf',

                    },
                    type:'Field',
                    form:true
                },


                {
                    config:{
                        name: 'comprobante_sigma',
                        fieldLabel: 'comprobante_sigma',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength:300
                    },
                    type:'TextField',
                    filters:{pfiltro:'banca.comprobante_sigma',type:'string'},
                    id_grupo:2,
                    grid:true,
                    form:false
                },
                {
                    config:{
                        name: 'tipo',
                        fieldLabel: 'tipo',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength:300
                    },
                    type:'TextField',
                    filters:{pfiltro:'banca.tipo',type:'string'},
                    id_grupo:2,
                    grid:true,
                    form:false
                },
                {
                    config:{
                        name: 'razon_social',
                        fieldLabel: 'razon_social',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength:300
                    },
                    type:'TextField',
                    filters:{pfiltro:'banca.razon_social',type:'string'},
                    id_grupo:2,
                    grid:true,
                    form:false
                },


                {
                    config:{
                        name: 'fecha_documento',
                        fieldLabel: 'fecha_documento.',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        format: 'd/m/Y',
                        renderer:function (value,p,record){return value?value.dateFormat('d/m/Y H:i:s'):''}
                    },
                    type:'DateField',
                    filters:{pfiltro:'banca.fecha_documento',type:'date'},
                    id_grupo:2,
                    grid:true,
                    form:false
                },
                {
                    config:{
                        name: 'nro_documento',
                        fieldLabel: 'nro_documento',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength:300
                    },
                    type:'TextField',
                    filters:{pfiltro:'banca.nro_documento',type:'string'},
                    id_grupo:2,
                    grid:true,
                    form:false
                },
                {
                    config:{
                        name: 'nro_autorizacion',
                        fieldLabel: 'nro_autorizacion',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength:300
                    },
                    type:'TextField',
                    filters:{pfiltro:'banca.nro_autorizacion',type:'string'},
                    id_grupo:2,
                    grid:true,
                    form:false
                },

                {
                    config:{
                        name: 'importe_total',
                        fieldLabel: 'importe_total',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength:300
                    },
                    type:'TextField',
                    filters:{pfiltro:'banca.importe_total',type:'string'},
                    id_grupo:2,
                    grid:true,
                    form:false
                },

                {
                    config:{
                        name: 'nro_nit',
                        fieldLabel: 'nro_nit',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength:300
                    },
                    type:'TextField',
                    filters:{pfiltro:'banca.nro_nit',type:'string'},
                    id_grupo:2,
                    grid:true,
                    form:false
                },
                {
                    config:{
                        name: 'tipo_informe',
                        fieldLabel: 'tipo_informe',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength:300
                    },
                    type:'TextField',
                    filters:{pfiltro:'banca.tipo_informe',type:'string'},
                    id_grupo:2,
                    grid:true,
                    form:false
                },

                {
                    config:{
                        name: 'tipo_plantilla',
                        fieldLabel: 'tipo_plantilla',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength:300
                    },
                    type:'TextField',
                    filters:{pfiltro:'banca.tipo_plantilla',type:'string'},
                    id_grupo:2,
                    grid:true,
                    form:false
                },
                {
                    config:{
                        name: 'fecha_dev',
                        fieldLabel: 'fecha_dev.',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        format: 'd/m/Y',
                        renderer:function (value,p,record){return value?value.dateFormat('d/m/Y H:i:s'):''}
                    },
                    type:'DateField',
                    filters:{pfiltro:'banca.fecha_dev',type:'date'},
                    id_grupo:2,
                    grid:true,
                    form:false
                },
                {
                    config:{
                        name: 'fecha_pag',
                        fieldLabel: 'fecha_pag.',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        format: 'd/m/Y',
                        renderer:function (value,p,record){return value?value.dateFormat('d/m/Y H:i:s'):''}
                    },
                    type:'DateField',
                    filters:{pfiltro:'banca.fecha_pag',type:'date'},
                    id_grupo:2,
                    grid:true,
                    form:false
                },
                {
                    config:{
                        name: 'fecha_costo_ini',
                        fieldLabel: 'fecha_costo_ini.',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        format: 'd/m/Y',
                        renderer:function (value,p,record){return value?value.dateFormat('d/m/Y H:i:s'):''}
                    },
                    type:'DateField',
                    filters:{pfiltro:'banca.fecha_costo_ini',type:'date'},
                    id_grupo:2,
                    grid:true,
                    form:false
                },
                {
                    config:{
                        name: 'fecha_costo_fin',
                        fieldLabel: 'fecha_costo_fin.',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        format: 'd/m/Y',
                        renderer:function (value,p,record){return value?value.dateFormat('d/m/Y H:i:s'):''}
                    },
                    type:'DateField',
                    filters:{pfiltro:'banca.fecha_costo_fin',type:'date'},
                    id_grupo:2,
                    grid:true,
                    form:false
                },
                {
                    config:{
                        name: 'fecha_pago',
                        fieldLabel: 'fecha_pago.',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        format: 'd/m/Y',
                        renderer:function (value,p,record){return value?value.dateFormat('d/m/Y H:i:s'):''}
                    },
                    type:'DateField',
                    filters:{pfiltro:'banca.fecha_pago',type:'date'},
                    id_grupo:2,
                    grid:true,
                    form:false
                },
                {
                    config:{
                        name: 'denominacion',
                        fieldLabel: 'denominacion',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength:300
                    },
                    type:'TextField',
                    filters:{pfiltro:'banca.denominacion',type:'string'},
                    id_grupo:2,
                    grid:true,
                    form:false
                },
                {
                    config:{
                        name: 'nro_cuenta',
                        fieldLabel: 'nro_cuenta',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength:300
                    },
                    type:'TextField',
                    filters:{pfiltro:'banca.nro_cuenta',type:'string'},
                    id_grupo:2,
                    grid:true,
                    form:false
                },
                {
                    config:{
                        name: 'numero_contrato',
                        fieldLabel: 'numero_contrato',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength:300
                    },
                    type:'TextField',
                    filters:{pfiltro:'banca.numero_contrato',type:'string'},
                    id_grupo:2,
                    grid:true,
                    form:false
                },
                {
                    config:{
                        name: 'monto_contrato',
                        fieldLabel: 'monto_contrato',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength:300
                    },
                    type:'TextField',
                    filters:{pfiltro:'banca.monto_contrato',type:'string'},
                    id_grupo:2,
                    grid:true,
                    form:false
                },
                {
                    config:{
                        name: 'bancarizacion',
                        fieldLabel: 'bancarizacion',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength:300
                    },
                    type:'TextField',
                    filters:{pfiltro:'banca.bancarizacion',type:'string'},
                    id_grupo:2,
                    grid:true,
                    form:false
                },
                {
                    config:{
                        name: 'num_tramite',
                        fieldLabel: 'num_tramite',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength:300
                    },
                    type:'TextField',
                    filters:{pfiltro:'banca.num_tramite',type:'string'},
                    id_grupo:2,
                    grid:true,
                    form:false
                },
                {
                    config:{
                        name: 'nro_cuota',
                        fieldLabel: 'nro_cuota',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength:300
                    },
                    type:'TextField',
                    filters:{pfiltro:'banca.nro_cuota',type:'string'},
                    id_grupo:2,
                    grid:true,
                    form:false
                },
                {
                    config:{
                        name: 'forma_pago',
                        fieldLabel: 'forma_pago',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength:300
                    },
                    type:'TextField',
                    filters:{pfiltro:'banca.forma_pago',type:'string'},
                    id_grupo:2,
                    grid:true,
                    form:false
                },

                {
                    config:{
                        name: 'comprobante_c31',
                        fieldLabel: 'comprobante_c31',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength:300
                    },
                    type:'TextField',
                    filters:{pfiltro:'banca.comprobante_c31',type:'string'},
                    id_grupo:2,
                    grid:true,
                    form:false
                },
                {
                    config:{
                        name: 'fecha_entrega',
                        fieldLabel: 'fecha_entrega.',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        format: 'd/m/Y',
                        renderer:function (value,p,record){return value?value.dateFormat('d/m/Y H:i:s'):''}
                    },
                    type:'DateField',
                    filters:{pfiltro:'banca.fecha_entrega',type:'date'},
                    id_grupo:2,
                    grid:true,
                    form:false
                },
                {
                    config:{
                        name: 'nro_cheque',
                        fieldLabel: 'nro_cheque',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength:300
                    },
                    type:'TextField',
                    filters:{pfiltro:'banca.nro_cheque',type:'string'},
                    id_grupo:2,
                    grid:true,
                    form:false
                },
                {
                    config:{
                        name: 'resolucion_bancarizacion',
                        fieldLabel: 'resolucion_bancarizacion',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength:300
                    },
                    type:'TextField',
                    filters:{pfiltro:'banca.resolucion_bancarizacion',type:'string'},
                    id_grupo:2,
                    grid:true,
                    form:false
                },
                {
                    config:{
                        name: 'monto_retgar_mo',
                        fieldLabel: 'monto_retgar_mo',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength:300
                    },
                    type:'TextField',
                    filters:{pfiltro:'banca.monto_retgar_mo',type:'string'},
                    id_grupo:2,
                    grid:true,
                    form:false
                },
                {
                    config:{
                        name: 'liquido_pagable',
                        fieldLabel: 'liquido_pagable',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength:300
                    },
                    type:'TextField',
                    filters:{pfiltro:'banca.liquido_pagable',type:'string'},
                    id_grupo:2,
                    grid:true,
                    form:false
                },
                {
                    config:{
                        name: 'monto_pago',
                        fieldLabel: 'monto_pago',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength:300
                    },
                    type:'TextField',
                    filters:{pfiltro:'banca.monto_pago',type:'string'},
                    id_grupo:2,
                    grid:true,
                    form:false
                },
                {
                    config:{
                        name: 'otros_descuentos',
                        fieldLabel: 'otros_descuentos',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength:300
                    },
                    type:'TextField',
                    filters:{pfiltro:'banca.otros_descuentos',type:'string'},
                    id_grupo:2,
                    grid:true,
                    form:false
                },
                {
                    config:{
                        name: 'descuento_inter_serv',
                        fieldLabel: 'descuento_inter_serv',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength:300
                    },
                    type:'TextField',
                    filters:{pfiltro:'banca.descuento_inter_serv',type:'string'},
                    id_grupo:2,
                    grid:true,
                    form:false
                },
                {
                    config:{
                        name: 'estado_libro',
                        fieldLabel: 'estado_libro',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength:300
                    },
                    type:'TextField',
                    filters:{pfiltro:'banca.estado_libro',type:'string'},
                    id_grupo:2,
                    grid:true,
                    form:false
                },
                {
                    config:{
                        name: 'importe_cheque',
                        fieldLabel: 'importe_cheque',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength:300
                    },
                    type:'TextField',
                    filters:{pfiltro:'banca.importe_cheque',type:'string'},
                    id_grupo:2,
                    grid:true,
                    form:false
                },
                {
                    config:{
                        name: 'importe_debe',
                        fieldLabel: 'importe_debe',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength:300
                    },
                    type:'TextField',
                    filters:{pfiltro:'banca.importe_debe',type:'string'},
                    id_grupo:2,
                    grid:true,
                    form:false
                },

                {
                    config:{
                        name: 'importe_gasto',
                        fieldLabel: 'importe_gasto',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength:300
                    },
                    type:'TextField',
                    filters:{pfiltro:'banca.importe_gasto',type:'string'},
                    id_grupo:2,
                    grid:true,
                    form:false
                },
                {
                    config:{
                        name: 'importe_recurso',
                        fieldLabel: 'importe_recurso',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength:300
                    },
                    type:'TextField',
                    filters:{pfiltro:'banca.importe_recurso',type:'string'},
                    id_grupo:2,
                    grid:true,
                    form:false
                },
                {
                    config:{
                        name: 'importe_haber',
                        fieldLabel: 'importe_haber',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength:300
                    },
                    type:'TextField',
                    filters:{pfiltro:'banca.importe_haber',type:'string'},
                    id_grupo:2,
                    grid:true,
                    form:false
                },
                {
                    config:{
                        name: 'tipo_monto',
                        fieldLabel: 'tipo_monto',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength:300
                    },
                    type:'TextField',
                    filters:{pfiltro:'banca.tipo_monto',type:'string'},
                    id_grupo:2,
                    grid:true,
                    form:false
                },


            ],
            tam_pag:1000,
            title:'PosiblesBancarizaciones',
            ActList:'../../sis_contabilidad/control/BancaCompraVenta/listarPosiblesBancarizaciones',
            id_store:'id_documento',
            fields: [





                {name:'id_banca_compra_venta', type: 'numeric'},
                {name:'num_cuenta_pago', type: 'string'},
                {name:'tipo_documento_pago', type: 'numeric'},
                {name:'num_documento', type: 'string'},
                {name:'monto_PosiblesBancarizaciones', type: 'numeric'},
                {name:'estado_reg', type: 'string'},
                {name:'nit_ci', type: 'string'},
                {name:'importe_documento', type: 'numeric'},
                {name:'fecha_documento', type: 'date',dateFormat:'Y-m-d'},
                {name:'modalidad_transaccion', type: 'numeric'},
                {name:'tipo_transaccion', type: 'numeric'},
                {name:'autorizacion', type: 'numeric'},
                {name:'monto_pagado', type: 'numeric'},
                {name:'fecha_de_pago', type: 'date',dateFormat:'Y-m-d'},
                {name:'razon', type: 'string'},
                {name:'tipo', type: 'string'},
                {name:'num_documento_pago', type: 'string'},
                {name:'num_contrato', type: 'string'},
                {name:'nit_entidad', type: 'numeric'},
                {name:'fecha_reg', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
                {name:'usuario_ai', type: 'string'},
                {name:'id_usuario_reg', type: 'numeric'},
                {name:'id_usuario_ai', type: 'numeric'},
                {name:'id_usuario_mod', type: 'numeric'},
                {name:'fecha_mod', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
                {name:'usr_reg', type: 'string'},
                {name:'usr_mod', type: 'string'},

                {name:'desc_modalidad_transaccion', type: 'string'},
                {name:'desc_tipo_transaccion', type: 'string'},
                {name:'desc_tipo_documento_pago', type: 'string'},
                {name:'revisado', type: 'string'},

                {name:'id_contrato', type: 'numeric'},
                {name:'id_proveedor', type: 'numeric'},
                {name:'id_cuenta_bancaria', type: 'numeric'},

                {name:'desc_proveedor2', type: 'string'},
                {name:'desc_contrato', type: 'string'},
                {name:'desc_cuenta_bancaria', type: 'string'},


                {name:'id_documento', type: 'numeric'},
                {name:'desc_documento', type: 'string'},
                {name:'periodo', type: 'string'},
                {name:'saldo', type: 'numeric'},
                'monto_contrato'
                ,'gestion','banca_seleccionada',
                'numero_cuota',
                'tramite_cuota',

                'id_plan_pago_pagado',
                'id_plan_pago_devengado',
                'comprobante_sigma',
                'id_libro_bancos',
                'tipo',
                'id_documento',
                'razon_social',
                {name:'fecha_documento', type: 'date',dateFormat:'Y-m-d H:i:s.u'},

                'nro_documento',
                'nro_autorizacion',
                'importe_total',
                'nro_nit',
                'tipo_informe',
                'tipo_plantilla',
                {name:'fecha_dev', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
                {name:'fecha_pag', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
                {name:'fecha_costo_ini', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
                {name:'fecha_costo_fin', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
                {name:'fecha_pago', type: 'date',dateFormat:'Y-m-d H:i:s.u'},


                'id_cuenta_bancaria',
                'denominacion',
                'nro_cuenta',
                'id_proveedor',
                'numero_contrato',
                'id_contrato',
                'monto_contrato',
                'bancarizacion',
                'num_tramite',
                'nro_cuota',
                'forma_pago',
                'comprobante_c31',
                {name:'fecha_entrega', type: 'date',dateFormat:'Y-m-d H:i:s.u'},


                'id_cuenta_bancaria_plan_pago',
                'nro_cheque',
                'id_proceso_wf',
                'resolucion_bancarizacion',
                'monto_retgar_mo',
                'liquido_pagable',
                'monto_pago',
                'otros_descuentos',
                'descuento_inter_serv',
                'estado_libro',
                'importe_cheque',
                'importe_debe',
                'importe_gasto',
                'importe_recurso',
                'importe_haber',
                'tipo_monto',


            ],
            sortInfo:{
                field: 'id_documento',
                direction: 'DESC'
            },
            bdel:true,
            bsave:true,
            bnew:false,
            bedit:false,
            bdel:false,
            holaMundo:function(){
                console.log('das')
            }


        }
    )
</script>
		
		