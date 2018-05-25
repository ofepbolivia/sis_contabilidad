<?php

header("content-type: text/javascript; charset=UTF-8");
?>
<script>
    Phx.vista.DetalleComprobante = Ext.extend(Phx.gridInterfaz, {

            constructor: function (config) {
                this.maestro = config.maestro;

                //llama al constructor de la clase padre
                Phx.vista.DetalleComprobante.superclass.constructor.call(this, config);
                this.grid.getTopToolbar().disable();
                this.grid.getBottomToolbar().disable();
                this.init();
                //this.load({params:{start:0, limit:this.tam_pag}})


            },



            Atributos: [
                {

                    config: {
                        name: 'id_int_comprobante',
                        fieldLabel: 'id_comprobante',
                        allowBlank: false,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength: 200
                    },
                    type: 'TextField',
                    filters: {pfiltro: 'com.id_int_comprobante', type: 'string'},
                    id_grupo: 1,
                    grid: false,
                    form: false,
                    bottom_filter: true
                },
                {

                    config: {
                        name: 'id_entrega',
                        fieldLabel: 'Nro de Entrega',
                        allowBlank: false,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength: 200
                    },
                    type: 'TextField',
                    filters: {pfiltro: 'ent.id_entrega', type: 'string'},
                    id_grupo: 1,
                    grid: true,
                    form: true
                },
                {
                    config: {
                        name: 'c31',
                        fieldLabel: 'Nro C31 Entrega',
                        allowBlank: false,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength: 200
                    },
                    type: 'TextField',
                    filters: {pfiltro: 'ent.c31', type: 'string'},
                    id_grupo: 1,
                    grid: true,
                    form: true,
                    bottom_filter: true
                },
                {
                    config: {
                        name: 'fecha_c31',
                        fieldLabel: 'Fecha C31 Entrega',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        format: 'd/m/Y',
                        renderer: function (value, p, record) {
                            return value ? value.dateFormat('d/m/Y') : ''
                        }
                    },
                    type: 'DateField',
                    filters: {pfiltro: 'ent.fecha_c31', type: 'date'},
                    id_grupo: 1,
                    grid: true,
                    form: true,
                    bottom_filter: true
                },
                {
                    config: {
                        name: 'estado',
                        fieldLabel: 'Estado Entrega',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength: 20
                    },
                    type: 'TextField',
                    filters: {pfiltro: 'ent.estado', type: 'string'},
                    id_grupo: 1,
                    grid: true,
                    form: false,
                    bottom_filter: true
                },



                {
                    config: {
                        name: 'nro_cbte',
                        fieldLabel: 'Nro Cbte',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 150,
                        maxLength: 100

                    },
                    type: 'TextField',
                    filters: {pfiltro: 'com.beneficiario', type: 'string'},
                    id_grupo: 1,
                    grid: true,
                    form: false,
                    bottom_filter: true
                },
                {
                    config: {
                        name: 'fecha',
                        fieldLabel: 'Fecha Cbte',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        format: 'd/m/Y',
                        renderer: function (value, p, record) {
                            return value ? value.dateFormat('d/m/Y') : ''
                        }
                    },
                    type: 'DateField',
                    filters: {pfiltro: 'com.fecha', type: 'date'},
                    id_grupo: 1,
                    grid: true,
                    form: false
                },
                {
                    config: {
                        name: 'glosa1',
                        fieldLabel: 'Glosa',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 200,
                        maxLength: 100

                    },
                    type: 'TextField',
                    filters: {pfiltro: 'com.glosa1', type: 'string'},
                    id_grupo: 1,
                    grid: true,
                    form: false,
                    bottom_filter: true
                },
                // {
                //     config: {
                //         name: 'importe_haber',
                //         fieldLabel: 'Importe Total',
                //         allowBlank: true,
                //         anchor: '80%',
                //         gwidth: 100,
                //         //maxLength: 100
                //         renderer:function (value,p,record){
                //             if(record.data.tipo_reg != 'summary'){
                //                 return  String.format('{0}', Ext.util.Format.number(value,'0,000.00'));
                //             }
                //             else{
                //                 return  String.format('<b><font size=2 >{0}</font><b>', Ext.util.Format.number(value,'0,000.00'));
                //             }
                //         }
                //
                //     },
                //     type: 'NumberField',
                //     filters: {pfiltro: 'com.importe_haber', type: 'numeric'},
                //     id_grupo: 1,
                //     grid: true,
                //     form: false,
                //     //bottom_filter: true
                // },
                {
                    config: {
                        name: 'total_importe',
                        fieldLabel: 'Importe Total',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        //maxLength: 100
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
                    filters: {pfiltro: 'com.total_importe', type: 'numeric'},
                    id_grupo: 1,
                    grid: true,
                    form: false,
                    //bottom_filter: true
                },
                {
                    config: {
                        name: 'beneficiario',
                        fieldLabel: 'Beneficiario',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 230,
                        maxLength: 100

                    },
                    type: 'TextField',
                    filters: {pfiltro: 'com.beneficiario', type: 'string'},
                    id_grupo: 1,
                    grid: true,
                    form: false,
                    bottom_filter: true
                },
                {
                    config: {
                        name: 'nro_tramite',
                        fieldLabel: 'Nro. Tramite',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 150,
                        maxLength: 100

                    },
                    type: 'TextField',
                    filters: {pfiltro: 'com.nro_tramite', type: 'string'},
                    id_grupo: 1,
                    grid: true,
                    form: false,
                    bottom_filter: true
                },
                {
                    config: {
                        name: 'desc_clase_comprobante',
                        fieldLabel: 'Tipo',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 230,
                        maxLength: 100

                    },
                    type: 'TextField',
                    filters: {pfiltro: 'com.desc_clase_comprobante', type: 'string'},
                    id_grupo: 1,
                    grid: true,
                    form: false,
                    bottom_filter: true
                },

                {
                    config: {
                        name: 'desc_depto',
                        fieldLabel: 'Departamento Contable',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 300,
                        maxLength: 100

                    },
                    type: 'TextField',
                    filters: {pfiltro: 'com.desc_depto', type: 'string'},
                    id_grupo: 1,
                    grid: true,
                    form: false,
                    bottom_filter: true
                },


                {
                    config: {
                        name: 'c31comp',
                        fieldLabel: 'Nro c31-Comprobante',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 150,
                        maxLength: 100

                    },
                    type: 'TextField',
                    filters: {pfiltro: 'com.c31comp', type: 'string'},
                    id_grupo: 1,
                    grid: true,
                    form: false,
                    //bottom_filter: true
                },
                {
                    config: {
                        name: 'fecha_c31comp',
                        fieldLabel: 'Fecha c31-Comprobante',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 150,
                        format: 'd/m/Y',
                        renderer: function (value, p, record) {
                            return value ? value.dateFormat('d/m/Y') : ''
                        }
                    },
                    type: 'DateField',
                    filters: {pfiltro: 'com.fecha_c31comp', type: 'date'},
                    id_grupo: 1,
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
                    filters: {pfiltro: 'usu1.cuenta', type: 'string'},
                    id_grupo: 1,
                    grid: true,
                    form: false,
                    bottom_filter: true
                },


            ],
            tam_pag: 50,
            title: 'Detalle Comprobante',
            //ActSave: '../../sis_contabilidad/control/Entrega/insertarEntrega',
            // ActDel: '../../sis_contabilidad/control/Entrega/eliminarEntrega',
            ActList: '../../sis_contabilidad/control/Entrega/listarDetalleComprobante',
            id_store: 'id_entrega',
            fields: [
                {name: 'id_entrega', type: 'numeric'},
                {name: 'fecha_c31', type: 'date', dateFormat: 'Y-m-d'},
                {name: 'c31', type: 'string'},
                {name: 'estado', type: 'string'},
                {name: 'estado_reg', type: 'string'},
                {name: 'id_usuario_ai', type: 'numeric'},
                {name: 'usuario_ai', type: 'string'},
                {name: 'fecha', type: 'date', dateFormat: 'Y-m-d'},
                {name: 'id_usuario_reg', type: 'numeric'},
                {name: 'fecha_mod', type: 'date', dateFormat: 'Y-m-d H:i:s.u'},
                {name: 'id_usuario_mod', type: 'numeric'},
                {name: 'usr_reg', type: 'string'},
                {name: 'usr_mod', type: 'string'}, 'id_depto_conta',
                {name: 'id_estado_wf', type: 'numeric'},
                {name: 'id_proceso_wf', type: 'numeric'},
                {name: 'nro_tramite', type: 'string'},
                {name: 'beneficiario', type: 'string'},
                {name: 'nro_cbte', type: 'string'},
                {name: 'desc_clase_comprobante', type: 'string'},
                {name: 'glosa1', type: 'string'},
                {name: 'desc_depto', type: 'string'},
                {name: 'c31comp', type: 'string'},
                {name: 'fecha_c31comp', type: 'date', dateFormat: 'Y-m-d'},
                {name: 'importe_haber', type: 'numeric'},
                {name: 'id_int_comprobante', type: 'numeric'},
                {name: 'total_importe', type: 'numeric'},

            ],
            sortInfo: {
                field: 'id_entrega',
                direction: 'DESC'
            },

            loadValoresIniciales: function () {
                Phx.vista.DetalleComprobante.superclass.loadValoresIniciales.call(this);

            },
            onReloadPage: function (param) {
                //Se obtiene la gestión en función de la fecha del comprobante para filtrar partidas, cuentas, etc.
                var me = this;
                this.initFiltro(param);
            },

            initFiltro: function (param) {
                this.store.baseParams = param;
                this.load({params: {start: 0, limit: this.tam_pag}});
            },
            rowExpander: new Ext.ux.grid.RowExpander({
                tpl: new Ext.Template(
                    '<p>&nbsp;&nbsp;<b>Glosa:&nbsp;&nbsp;</b> {glosa1}</p>',
                )
            }),

            bdel: false,
            bsave: false,
            bedit: false,
            bnew: false
        }
    )
</script>

