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

                this.addButton('chkdep', {
                    text: 'Dependencias',
                    iconCls: 'blist',
                    disabled: true,
                    handler: this.checkDependencias,
                    tooltip: '<b>Revisar Dependencias </b><p>Revisar dependencias del comprobante</p>'
                });

                this.addButton('btnChequeoDocumentosWf',
                    {
                        text: 'Documentos',
                        grupo:[0,1,2,3],
                        iconCls: 'bchecklist',
                        disabled: true,
                        handler: this.loadCheckDocumentosWf,
                        tooltip: '<b>Documentos del Trámite</b><br/>Permite ver los documentos asociados al NRO de trámite.'
                    }
                );

                this.addButton('chkpresupuesto', {
                    text: 'Chk Presupuesto',
                    iconCls: 'blist',
                    disabled: true,
                    handler: this.checkPresupuesto,
                    tooltip: '<b>Revisar Presupuesto</b><p>Revisar estado de ejecución presupeustaria para el tramite</p>'
                });

                this.addButton('btnDocCmpVnt', {
                    text: 'Doc Cmp/Vnt',
                    iconCls: 'brenew',
                    disabled: true,
                    handler: this.loadDocCmpVnt,
                    tooltip: '<b>Documentos de compra/venta</b><br/>Muestras los docuemntos relacionados con el comprobante'
                });

            },
            checkDependencias: function () {
                var rec = this.sm.getSelected();
                var configExtra = [];
                this.objChkPres = Phx.CP.loadWindows('../../../sis_contabilidad/vista/int_comprobante/CbteDependencias.php',
                    'Dependencias',
                    {
                        modal: true,
                        width: '80%',
                        height: '80%'
                    },
                    {id_int_comprobante: rec.data.id_int_comprobante},
                    this.idContenedor,
                    'CbteDependencias');

            },
            checkPresupuesto: function () {
                var rec = this.sm.getSelected();
                var configExtra = [];
                this.objChkPres = Phx.CP.loadWindows('../../../sis_presupuestos/vista/presup_partida/ChkPresupuesto.php',
                    'Estado del Presupuesto',
                    {
                        modal: true,
                        width: 700,
                        height: 450
                    }, {
                        data: {
                            nro_tramite: rec.data.nro_tramite
                        }
                    }, this.idContenedor, 'ChkPresupuesto',
                    {
                        config: [{
                            event: 'onclose',
                            delegate: this.onCloseChk
                        }],

                        scope: this
                    });

            },

            loadCheckDocumentosWf: function () {
                var rec = this.sm.getSelected();
                rec.data.nombreVista = this.nombreVista;
                Phx.CP.loadWindows('../../../sis_workflow/vista/documento_wf/DocumentoWf.php',
                    'Chequear documento del WF',
                    {
                        width: '90%',
                        height: 500
                    },
                    rec.data,
                    this.idContenedor,
                    'DocumentoWf'
                )
            },

            loadDocCmpVnt: function () {
                var rec = this.sm.getSelected();
                Phx.CP.loadWindows('../../../sis_contabilidad/vista/doc_compra_venta/DocCompraVentaCbte.php', 'Documentos del Cbte', {
                    width: '80%',
                    height: '80%'
                }, rec.data, this.idContenedor, 'DocCompraVentaCbte');
            },

            Atributos: [
                {
                    //configuracion del componente
                    config: {
                        labelSeparator: '',
                        inputType: 'hidden',
                        name: 'id_int_comprobante'
                    },
                    type: 'Field',
                    form: true
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
                    form: true,
                    bottom_filter: true
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

                {
                    config: {
                        name: 'total_importe',
                        fieldLabel: 'Importe Total',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        //maxLength: 100
                        renderer: function (value, p, record) {
                            if (record.data.tipo_reg != 'summary') {
                                return String.format('{0}', Ext.util.Format.number(value, '0,000.00'));
                            }
                            else {
                                return String.format('<b><font size=2 >{0}</font><b>', Ext.util.Format.number(value, '0,000.00'));
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
                        name: 'id_tipo_relacion_comprobante',
                        fieldLabel: 'ID Tipo Rel.',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 200,
                        maxLength: 100

                    },
                    type: 'TextField',
                    filters: {pfiltro: 'com.id_tipo_relacion_comprobante', type: 'string'},
                    id_grupo: 1,
                    grid: true,
                    form: false,
                    bottom_filter: true
                },
                {
                    config: {
                        name: 'desc_tipo_relacion_comprobante',
                        fieldLabel: 'Tipo Rel.',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 200,
                        maxLength: 100

                    },
                    type: 'TextField',
                    filters: {pfiltro: 'com.desc_tipo_relacion_comprobante', type: 'string'},
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
                    grid: false,
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
                    grid: false,
                    form: false
                },
                // {
                //     config: {
                //         name: 'usr_reg',
                //         fieldLabel: 'Creado por',
                //         allowBlank: true,
                //         anchor: '80%',
                //         gwidth: 100,
                //         maxLength: 4
                //     },
                //     type: 'Field',
                //     filters: {pfiltro: 'usu1.cuenta', type: 'string'},
                //     id_grupo: 1,
                //     grid: true,
                //     form: false,
                //     bottom_filter: true
                // },

                {
                    config: {
                        name: 'usr_reg_comprobante',
                        fieldLabel: 'Creado por',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength: 4
                    },
                    type: 'Field',
                    filters: {pfiltro: 'com.usr_reg', type: 'string'},
                    id_grupo: 1,
                    grid: true,
                    form: false,
                    bottom_filter: true
                },

            ],
            tam_pag: 50,
            title: 'Detalle Comprobante',
            ActList: '../../sis_contabilidad/control/Entrega/listarDetalleComprobante',
            id_store: 'id_int_comprobante',
            //id_store: 'id_entrega',
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
                {name: 'id_int_comprobante', type: 'numeric'},
                {name: 'id_clase_comprobante', type: 'numeric'},
                {name: 'usr_reg_comprobante', type: 'string'},
                {name: 'total_importe', type: 'numeric'},
                {name: 'id_tipo_relacion_comprobante', type: 'numeric'},
                {name: 'desc_tipo_relacion_comprobante', type: 'string'},

            ],
            sortInfo: {
                //field: 'fecha',
                field: 'id_int_comprobante',
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

