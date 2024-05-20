<?php
/**
 * @package pXP
 * @file gen-DocCompraVenta.php
 * @author  (admin)
 * @date 18-08-2015 15:57:09
 * @description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
 */
header("content-type: text/javascript; charset=UTF-8");
?>
<script>
    Phx.vista.DocCompraVenta = Ext.extend(Phx.gridInterfaz, {
        fheight: '80%',
        fwidth: '70%',
        tabEnter: true,
        tipoDoc: 'venta',
        regitrarDetalle: 'si',
        constructor: function (config) {
            this.initButtons = [this.cmbDepto, this.cmbGestion, this.cmbPeriodo];
            var me = this;
            this.Atributos = [

                {
                    config: {
                        labelSeparator: '',
                        inputType: 'hidden',
                        name: 'id_doc_compra_venta'
                    },
                    type: 'Field',
                    form: true
                },
                {
                    config: {
                        labelSeparator: '',
                        inputType: 'hidden',
                        name: 'tipo'
                    },
                    type: 'Field',
                    form: true
                },
                {
                    
                    config: {
                        labelSeparator: '',
                        inputType: 'hidden',
                        name: 'porc_descuento_ley',
                        allowDecimals: true,
                        decimalPrecision: 10
                    },
                    type: 'NumberField',
                    form: true
                },
                {
                    
                    config: {
                        labelSeparator: '',
                        inputType: 'hidden',
                        name: 'porc_iva_cf',
                        allowDecimals: true,
                        decimalPrecision: 10
                    },
                    type: 'NumberField',
                    form: true
                },
                {
                    
                    config: {
                        labelSeparator: '',
                        inputType: 'hidden',
                        name: 'porc_iva_df',
                        allowDecimals: true,
                        decimalPrecision: 10
                    },
                    type: 'NumberField',
                    form: true
                },
                {
                    
                    config: {
                        labelSeparator: '',
                        inputType: 'hidden',
                        name: 'porc_it',
                        allowDecimals: true,
                        decimalPrecision: 10
                    },
                    type: 'NumberField',
                    form: true
                },
 
                {
                    
                    config: {
                        labelSeparator: '',
                        inputType: 'hidden',
                        name: 'porc_ice',
                        allowDecimals: true,
                        decimalPrecision: 10
                    },
                    type: 'NumberField',
                    form: true
                },
                {
                    
                    config: {
                        labelSeparator: '',
                        inputType: 'hidden',
                        name: 'id_depto_conta'
                    },
                    type: 'Field',
                    form: true
                },
                {
                    config: {
                        name: 'revisado',
                        fieldLabel: 'Revisado',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 80,
                        maxLength: 3,
                        renderer: function (value, p, record, rowIndex, colIndex) {
                            //check or un check row
                            var checked = '',
                                state = '',
                                momento = 'no';
                            if (value == 'si') {
                                checked = 'checked';
                            }
                            /*if(record.data.id_int_comprobante){
                             state = 'disabled';
                             }*/
                            if (record.data.tipo_reg != 'summary') {
                                return String.format('<div style="vertical-align:middle;text-align:center;"><input style="height:37px;width:37px;" type="checkbox"  {0} {1}></div>', checked, state);
                            }
                            else {
                                return '';
                            }
                        }
                    },
                    type: 'TextField',
                    filters: {pfiltro: 'dcv.revisado', type: 'string'},
                    id_grupo: 1,
                    grid: true,
                    form: false
                },

                {
                    config: {
                        name: 'nit',
                        fieldLabel: 'NIT',
                        qtip: 'Número de indentificación del proveedor',
                        allowBlank: false,
                        emptyText: 'nit ...',
                        store: new Ext.data.JsonStore(
                            {
                                url: '../../sis_contabilidad/control/DocCompraVenta/listarNroNit',
                                id: 'nit',
                                root: 'datos',
                                sortInfo: {
                                    field: 'nit',
                                    direction: 'ASC'
                                },
                                totalProperty: 'total',
                                fields: ['nit', 'razon_social'],
                                remoteSort: true
                            }),
                        valueField: 'nit',
                        hiddenValue: 'nit',
                        displayField: 'nit',
                        gdisplayField: 'nit',
                        queryParam: 'nit',
                        listWidth: '280',
                        forceSelection: false,
                        autoSelect: false,
                        typeAhead: false,
                        typeAheadDelay: 75,
                        hideTrigger: true,
                        triggerAction: 'query',
                        lazyRender: false,
                        mode: 'remote',
                        pageSize: 20,
                        queryDelay: 500,
                        gwidth: 100,
                        minChars: 1
                    },
                    type: 'ComboBox',
                    filters: {pfiltro: 'dcv.nit', type: 'string'},
                    id_grupo: 0,
                    grid: true,
                    bottom_filter: true,
                    form: false
                },

                {
                    config: {
                        name: 'razon_social',
                        fieldLabel: 'Razón Social',
                        allowBlank: false,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength: 100
                    },
                    type: 'TextField',
                    filters: {pfiltro: 'dcv.razon_social', type: 'string'},
                    id_grupo: 0,
                    grid: true,
                    bottom_filter: true,
                    form: false
                },                
                {
                    config: {
                        name: 'nro_autorizacion',
                        fieldLabel: 'Autorización',
                        allowBlank: false,
                        emptyText: 'autorización ...',
                        store: new Ext.data.JsonStore(
                            {
                                url: '../../sis_contabilidad/control/DocCompraVenta/listarNroAutorizacion',
                                id: 'nro_autorizacion',
                                root: 'datos',
                                sortInfo: {
                                    field: 'nro_autorizacion',
                                    direction: 'ASC'
                                },
                                totalProperty: 'total',
                                fields: ['nro_autorizacion', 'nit', 'razon_social'],
                                remoteSort: true
                            }),
                        valueField: 'nro_autorizacion',
                        hiddenValue: 'nro_autorizacion',
                        displayField: 'nro_autorizacion',
                        gdisplayField: 'nro_autorizacion',
                        queryParam: 'nro_autorizacion',
                        listWidth: '280',
                        forceSelection: false,
                        autoSelect: false,
                        hideTrigger: true,
                        typeAhead: false,
                        typeAheadDelay: 75,
                        //triggerAction: 'query',
                        lazyRender: false,
                        mode: 'remote',
                        pageSize: 20,
                        queryDelay: 500,
                        gwidth: 150,
                        minChars: 1
                    },
                    type: 'ComboBox',
                    filters: {pfiltro: 'dcv.nro_autorizacion', type: 'string'},
                    id_grupo: 0,
                    grid: true,
                    bottom_filter: true,
                    form: false
                },

                {
                    config: {
                        name: 'nro_documento',
                        fieldLabel: 'Nro Doc.',
                        allowBlank: false,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength: 100
                    },
                    type: 'TextField',
                    filters: {pfiltro: 'dcv.nro_documento', type: 'string'},
                    id_grupo: 0,
                    grid: true,
                    bottom_filter: true,
                    form: false
                },
                {
                    config: {
                        name: 'nro_dui',
                        fieldLabel: 'DUI',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength: 16,
                        minLength: 9
                    },
                    type: 'TextField',
                    filters: {pfiltro: 'dcv.nro_dui', type: 'string'},
                    id_grupo: 0,
                    grid: true,
                    form: false
                },
                {
                    config: {
                        name: 'fecha',
                        fieldLabel: 'Fecha',
                        allowBlank: false,
                        anchor: '80%',
                        gwidth: 100,
                        format: 'd/m/Y',
                        readOnly: true,
                        renderer: function (value, p, record) {
                            return value ? value.dateFormat('d/m/Y') : ''
                        }
                    },
                    type: 'DateField',
                    filters: {pfiltro: 'dcv.fecha', type: 'date'},
                    id_grupo: 0,
                    grid: true,
                    form: false
                },

                {
                    config: {
                        name: 'importe_pago_liquido',
                        fieldLabel: 'Líquido Pagado',
                        allowBlank: true,
                        readOnly: true,
                        anchor: '80%',
                        gwidth: 100,
                        galign: 'right ',
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
                    filters: {pfiltro: 'dcv.importe_pago_liquido', type: 'numeric'},
                    id_grupo: 1,
                    grid: true,
                    form: false
                },

                {
                    config: {
                        name: 'importe_ice',
                        fieldLabel: 'ICE',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        galign: 'right ',
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
                    filters: {pfiltro: 'dcv.importe_ice', type: 'numeric'},
                    id_grupo: 1,
                    grid: true,
                    form: false
                },                

                {
                    config: {
                        name: 'importe_iehd',
                        fieldLabel: 'importe IEHD',
                        allowBlank: false,
                        anchor: '80%',
                        gwidth: 80,
                        galign: 'right ',
                        maxLength: 1179650,
                        renderer: function (value, p, record) {
                            if (record.data.tipo_reg != 'summary') {
                                return String.format('{0}', Ext.util.Format.number(value, '0,000.00'));
                            }
                            else {
                                Ext.util.Format.usMoney
                                return String.format('<b><font size=2 >{0}</font><b>', Ext.util.Format.number(value, '0,000.00'));
                            }
                        }
                    },
                    type: 'NumberField',
                    filters: {pfiltro: 'dcv.importe_iehd', type: 'numeric'},
                    id_grupo: 1,
                    grid: true,
                    form: false
                },                

                {
                    config: {
                        name: 'importe_ipj',
                        fieldLabel: 'Importe IPJ',
                        allowBlank: false,
                        anchor: '80%',
                        gwidth: 80,
                        galign: 'right ',
                        maxLength: 1179650,
                        renderer: function (value, p, record) {
                            if (record.data.tipo_reg != 'summary') {
                                return String.format('{0}', Ext.util.Format.number(value, '0,000.00'));
                            }
                            else {
                                Ext.util.Format.usMoney
                                return String.format('<b><font size=2 >{0}</font><b>', Ext.util.Format.number(value, '0,000.00'));
                            }
                        }
                    },
                    type: 'NumberField',
                    filters: {pfiltro: 'dcv.importe_ipj', type: 'numeric'},
                    id_grupo: 1,
                    grid: true,
                    form: false
                },


                {
                    config: {
                        name: 'fecha_vencimiento',
                        fieldLabel: 'Fecha de Vencimiento de la Deuda',
                        allowBlank: false,
                        anchor: '80%',
                        gwidth: 100,
                        format: 'd/m/Y',
                        readOnly: true,
                        renderer: function (value, p, record) {
                            return value ? value.dateFormat('d/m/Y') : ''
                        }
                    },
                    type: 'DateField',
                    filters: {pfiltro: 'dcv.fecha_vencimiento', type: 'date'},
                    id_grupo: 0,
                    grid: true,
                    form: true
                },

                {
                    config: {
                        name: 'importe_doc',
                        fieldLabel: 'Monto',
                        allowBlank: false,
                        anchor: '80%',
                        gwidth: 80,
                        galign: 'right ',
                        maxLength: 1179650,
                        renderer: function (value, p, record) {
                            if (record.data.tipo_reg != 'summary') {
                                return String.format('{0}', Ext.util.Format.number(value, '0,000.00'));
                            }
                            else {
                                Ext.util.Format.usMoney
                                return String.format('<b><font size=2 >{0}</font><b>', Ext.util.Format.number(value, '0,000.00'));
                            }
                        }
                    },
                    type: 'NumberField',
                    filters: {pfiltro: 'dcv.importe_doc', type: 'numeric'},
                    id_grupo: 1,
                    grid: true,
                    form: false
                },

                {
                    config: {
                        name: 'importe_tasas',
                        fieldLabel: 'Importe Tasas',
                        allowBlank: false,
                        anchor: '80%',
                        gwidth: 80,
                        galign: 'right ',
                        maxLength: 1179650,
                        renderer: function (value, p, record) {
                            if (record.data.tipo_reg != 'summary') {
                                return String.format('{0}', Ext.util.Format.number(value, '0,000.00'));
                            }
                            else {
                                Ext.util.Format.usMoney
                                return String.format('<b><font size=2 >{0}</font><b>', Ext.util.Format.number(value, '0,000.00'));
                            }
                        }
                    },
                    type: 'NumberField',
                    filters: {pfiltro: 'dcv.importe_tasas', type: 'numeric'},
                    id_grupo: 1,
                    grid: true,
                    form: false
                },           
                
                {
                    config: {
                        name: 'otro_no_sujeto_credito_fiscal',
                        fieldLabel: 'Otro No Sujeto A Credito Fiscal',
                        allowBlank: false,
                        anchor: '80%',
                        gwidth: 80,
                        galign: 'right ',
                        maxLength: 1179650,
                        renderer: function (value, p, record) {
                            if (record.data.tipo_reg != 'summary') {
                                return String.format('{0}', Ext.util.Format.number(value, '0,000.00'));
                            }
                            else {
                                Ext.util.Format.usMoney
                                return String.format('<b><font size=2 >{0}</font><b>', Ext.util.Format.number(value, '0,000.00'));
                            }
                        }
                    },
                    type: 'NumberField',
                    filters: {pfiltro: 'dcv.otro_no_sujeto_credito_fiscal', type: 'numeric'},
                    id_grupo: 1,
                    grid: true,
                    form: false
                },                
                {
                    config: {
                        name: 'importe_excento',
                        fieldLabel: 'Exento',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        galign: 'right ',
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
                    filters: {pfiltro: 'dcv.importe_excento', type: 'numeric'},
                    id_grupo: 1,
                    grid: true,
                    form: false
                },

                {
                    config: {
                        name: 'importe_compras_gravadas_tasa_cero',
                        fieldLabel: 'Importe Compras Gravadas Tasa Cero',
                        allowBlank: false,
                        anchor: '80%',
                        gwidth: 80,
                        galign: 'right ',
                        maxLength: 1179650,
                        renderer: function (value, p, record) {
                            if (record.data.tipo_reg != 'summary') {
                                return String.format('{0}', Ext.util.Format.number(value, '0,000.00'));
                            }
                            else {
                                Ext.util.Format.usMoney
                                return String.format('<b><font size=2 >{0}</font><b>', Ext.util.Format.number(value, '0,000.00'));
                            }
                        }
                    },
                    type: 'NumberField',
                    filters: {pfiltro: 'dcv.importe_compras_gravadas_tasa_cero', type: 'numeric'},
                    id_grupo: 1,
                    grid: true,
                    form: false
                },

                {
                    config: {
                        name: 'subtotal',
                        fieldLabel: 'SubTotal',
                        allowBlank: false,
                        anchor: '80%',
                        gwidth: 80,
                        galign: 'right',
                    },
                    type: 'NumberField',
                    filters: {pfiltro: 'dcv.subtotal', type: 'numeric'},
                    id_grupo: 1,
                    grid: true,
                    form: false
                },

                {
                    config: {
                        name: 'importe_descuento_ley',
                        fieldLabel: 'Descuentos de Ley',
                        allowBlank: true,
                        readOnly: true,
                        anchor: '80%',
                        gwidth: 100,
                        galign: 'right ',
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
                    filters: {pfiltro: 'dcv.importe_descuento_ley', type: 'numeric'},
                    id_grupo: 1,
                    grid: true,
                    form: false
                },                

                {
                    config: {
                        name: 'importe_gift_card',
                        fieldLabel: 'Importe Gift Card',
                        allowBlank: false,
                        anchor: '80%',
                        gwidth: 80,
                        galign: 'right ',
                        maxLength: 1179650,
                        renderer: function (value, p, record) {
                            if (record.data.tipo_reg != 'summary') {
                                return String.format('{0}', Ext.util.Format.number(value, '0,000.00'));
                            }
                            else {
                                Ext.util.Format.usMoney
                                return String.format('<b><font size=2 >{0}</font><b>', Ext.util.Format.number(value, '0,000.00'));
                            }
                        }
                    },
                    type: 'NumberField',
                    filters: {pfiltro: 'dcv.importe_gift_card', type: 'numeric'},
                    id_grupo: 1,
                    grid: true,
                    form: false
                },


                {
                    config: {
                        name: 'importe_neto',
                        fieldLabel: 'Importe c/d',
                        allowBlank: false,
                        anchor: '80%',
                        gwidth: 100,
                        galign: 'right ',
                        maxLength: 1179650,
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
                    filters: {pfiltro: 'dcv.importe_doc', type: 'numeric'},
                    id_grupo: 1,
                    grid: true,
                    form: false
                },                

                {
                    config: {
                        name: 'importe_iva',
                        fieldLabel: 'IVA',
                        allowBlank: true,
                        readOnly: true,
                        anchor: '80%',
                        gwidth: 100,
                        galign: 'right ',
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
                    filters: {pfiltro: 'dcv.importe_iva', type: 'numeric'},
                    id_grupo: 1,
                    grid: true,
                    form: false
                },

                {
                    config: {
                        name: 'codigo_control',
                        fieldLabel: 'Código de Control',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength: 200
                    },
                    type: 'TextField',
                    filters: {pfiltro: 'dcv.codigo_control', type: 'string'},
                    id_grupo: 0,
                    grid: true,
                    form: false
                },

  
                {
                    config: {
                        name: 'c31',
                        fieldLabel: 'C31-SIGEP',
                        allowBlank: false,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength: 100
                    },
                    type: 'TextField',
                    id_grupo: 0,
                    grid: true,
                    form: false
                },
                {
                    config: {
                        name: 'id_int_comprobante',
                        fieldLabel: 'Id Int Comprobante',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength: 100
                    },
                    type: 'TextField',
                    id_grupo: 0,
                    grid: true,
                    form: false
                },
                {
                    config: {
                        name: 'id_int_transaccion',
                        fieldLabel: 'Id Transaccion',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength: 100
                    },
                    type: 'TextField',
                    id_grupo: 0,
                    grid: true,
                    form: false
                },
                {
                    config: {
                        name: 'estado',
                        fieldLabel: 'Estado',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength: 30
                    },
                    type: 'TextField',
                    filters: {pfiltro: 'dcv.estado', type: 'string'},
                    id_grupo: 1,
                    grid: true,
                    form: false
                },
            ],
                //Esta funcion se sobre carga para la version de BOA
                this.modificarAtributos();
            //llama al constructor de la clase padre
            Phx.vista.DocCompraVenta.superclass.constructor.call(this, config);
            this.bloquearOrdenamientoGrid();
            this.cmbGestion.on('select', function (combo, record, index) {
                this.tmpGestion = record.data.gestion;
                this.cmbPeriodo.enable();
                this.cmbPeriodo.reset();
                this.store.removeAll();
                this.cmbPeriodo.store.baseParams = Ext.apply(this.cmbPeriodo.store.baseParams, {id_gestion: this.cmbGestion.getValue()});
                this.cmbPeriodo.modificado = true;
            }, this);
            this.cmbPeriodo.on('select', function (combo, record, index) {
                this.tmpPeriodo = record.data.periodo;
                this.capturaFiltros();
            }, this);
            this.cmbDepto.on('select', function (combo, record, index) {
                this.capturaFiltros();
            }, this);
            this.addButton('btnWizard',
                {
                    text: 'Generar Cbte',
                    iconCls: 'bchecklist',
                    disabled: true,
                    handler: this.loadWizard,
                    tooltip: '<b>Generar Comprobante</b><br/>Genera cbte de  para el dato seleccionado'
                }
            );
            //Botón para Imprimir el Comprobante
            this.addButton('btnImprimir', {
                text: 'Imprimir',
                iconCls: 'bprint',
                disabled: false,
                handler: this.imprimirLCV,
                tooltip: '<b>Imprimir LCV en PDF</b><br/>Imprime el LCV en formato PDF para archivo'
            });
            this.addButton('btnExpTxt',
                {
                    text: 'Exportar TXT',
                    iconCls: 'bchecklist',
                    disabled: false,
                    handler: this.expTxt,
                    tooltip: '<b>Exportar</b><br/>Exporta a archivo TXT para LCV'
                }
            );
			this.addButton('btnImportarXlsSIN', {
					text : 'Importar SIN',
					iconCls : 'bgear',
					disabled : false,
					handler : this.importarXlsSIN,
					tooltip : '<b>Archivo Servicio de Impuestos</b><br/>Seleccione un archivo que haya descargado de Impuestos Nacionales.'
			});			            
            //this.iniciarEventos();
            this.init();
            this.grid.addListener('cellclick', this.oncellclick, this);
            this.obtenerVariableGlobal();
        },
        modificarAtributos: function () {
        },
        obtenerVariableGlobal: function () {
            //Verifica que la fecha y la moneda hayan sido elegidos
            Phx.CP.loadingShow();
            Ext.Ajax.request({
                url: '../../sis_seguridad/control/Subsistema/obtenerVariableGlobal',
                params: {
                    codigo: 'conta_libro_compras_detallado'
                },
                success: function (resp) {
                    Phx.CP.loadingHide();
                    var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
                    if (reg.ROOT.error) {
                        Ext.Msg.alert('Error', 'Error a recuperar la variable global')
                    } else {
                        if (reg.ROOT.datos.valor == 'no') {
                            this.regitrarDetalle = 'no';
                        }
                    }
                },
                failure: this.conexionFailure,
                timeout: this.timeout,
                scope: this
            });
        },
        capturaFiltros: function (combo, record, index) {
            this.desbloquearOrdenamientoGrid();
            if (this.validarFiltros()) {
                this.store.baseParams.id_gestion = this.cmbGestion.getValue();
                this.store.baseParams.id_periodo = this.cmbPeriodo.getValue();
                this.store.baseParams.id_depto = this.cmbDepto.getValue();
                this.load();
            }
        },
        validarFiltros: function () {
            if (this.cmbDepto.getValue() && this.cmbGestion.validate() && this.cmbPeriodo.validate()) {
                this.desbloquearOrdenamientoGrid();
                return true;
            }
            else {
                this.bloquearOrdenamientoGrid();
                return false;
            }
        },
        onButtonAct: function () {
            if (!this.validarFiltros()) {
                alert('Especifique los filtros antes')
            }
        },
        cmbDepto: new Ext.form.ComboBox({
            name: 'id_depto',
            fieldLabel: 'Depto',
            blankText: 'Depto',
            typeAhead: false,
            forceSelection: true,
            allowBlank: false,
            disableSearchButton: true,
            emptyText: 'Depto Contable',
            store: new Ext.data.JsonStore({
                url: '../../sis_parametros/control/Depto/listarDeptoFiltradoDeptoUsuario',
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
                    codigo_subsistema: 'CONTA',
                    _adicionar: 'si'
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
            anchor: '80%',
            listWidth: '280',
            resizable: true,
            minChars: 2
        }),
        cmbGestion: new Ext.form.ComboBox({
            fieldLabel: 'Gestion',
            allowBlank: false,
            emptyText: 'Gestion...',
            blankText: 'Año',
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
        cmbPeriodo: new Ext.form.ComboBox({
            fieldLabel: 'Periodo',
            allowBlank: false,
            blankText: 'Mes',
            emptyText: 'Periodo...',
            store: new Ext.data.JsonStore(
                {
                    url: '../../sis_parametros/control/Periodo/listarPeriodo',
                    id: 'id_periodo',
                    root: 'datos',
                    sortInfo: {
                        field: 'periodo',
                        direction: 'ASC'
                    },
                    totalProperty: 'total',
                    fields: ['id_periodo', 'periodo', 'id_gestion', 'literal'],
                    // turn on remote sorting
                    remoteSort: true,
                    baseParams: {par_filtro: 'gestion'}
                }),
            valueField: 'id_periodo',
            triggerAction: 'all',
            displayField: 'literal',
            hiddenName: 'id_periodo',
            mode: 'remote',
            pageSize: 50,
            disabled: true,
            queryDelay: 500,
            listWidth: '280',
            width: 80
        }),
        tam_pag: 50,
        title: 'Documentos Compra/Venta',
        ActSave: '../../sis_contabilidad/control/DocCompraVenta/modificarBasico',
        ActDel: '../../sis_contabilidad/control/DocCompraVenta/eliminarDocCompraVenta',
        ActList: '../../sis_contabilidad/control/DocCompraVenta/listarDocCompraVenta',
        id_store: 'id_doc_compra_venta',
        fields: [
            {name: 'id_doc_compra_venta', type: 'string'},
            {name: 'revisado', type: 'string'},
            {name: 'movil', type: 'string'},
            {name: 'tipo', type: 'string'},
            {name: 'importe_excento', type: 'numeric'},
            {name: 'id_plantilla', type: 'numeric'},
            {name: 'fecha', type: 'date', dateFormat: 'Y-m-d'},
            {name: 'nro_documento', type: 'string'},
            {name: 'nit', type: 'string'},
            {name: 'importe_ice', type: 'numeric'},
            {name: 'nro_autorizacion', type: 'string'},
            {name: 'importe_iva', type: 'numeric'},
            {name: 'importe_descuento', type: 'numeric'},
            {name: 'importe_doc', type: 'numeric'},
            {name: 'sw_contabilizar', type: 'string'},
            {name: 'tabla_origen', type: 'string'},
            {name: 'estado', type: 'string'},
            {name: 'id_depto_conta', type: 'numeric'},
            {name: 'id_origen', type: 'numeric'},
            {name: 'obs', type: 'string'},
            {name: 'estado_reg', type: 'string'},
            {name: 'codigo_control', type: 'string'},
            {name: 'importe_it', type: 'numeric'},
            {name: 'razon_social', type: 'string'},
            {name: 'id_usuario_ai', type: 'numeric'},
            {name: 'id_usuario_reg', type: 'numeric'},
            {name: 'fecha_reg', type: 'date', dateFormat: 'Y-m-d H:i:s.u'},
            {name: 'usuario_ai', type: 'string'},
            {name: 'id_usuario_mod', type: 'numeric'},
            {name: 'fecha_mod', type: 'date', dateFormat: 'Y-m-d H:i:s.u'},
            {name: 'usr_reg', type: 'string'},
            {name: 'usr_mod', type: 'string'},
            {name: 'importe_pendiente', type: 'numeric'},
            {name: 'importe_anticipo', type: 'numeric'},
            {name: 'importe_retgar', type: 'numeric'},
            {name: 'importe_neto', type: 'numeric'},
            'desc_depto', 'desc_plantilla',
            'importe_descuento_ley', 'importe_aux_neto',
            'importe_pago_liquido', 'nro_dui', 'id_moneda', 'desc_moneda',
            'desc_tipo_doc_compra_venta', 'id_tipo_doc_compra_venta', 'nro_tramite',
            'desc_comprobante', 'id_int_comprobante','id_int_transaccion', 'id_auxiliar', 'codigo_auxiliar', 'nombre_auxiliar', 'tipo_reg',
            'estacion', 'id_punto_venta', 'nombre', 'id_agencia', 'codigo_noiata', 'codigo_int', 'c31',
            {name: 'fecha_vencimiento', type: 'date', dateFormat: 'Y-m-d'}, 'tipo_cambio',
            {name: 'importe_iehd', type: 'numeric'},
            {name: 'importe_ipj', type: 'numeric'},
            {name: 'importe_tasas', type: 'numeric'},
            {name: 'importe_gift_card', type: 'numeric'},
            {name: 'otro_no_sujeto_credito_fiscal', type: 'numeric'},
            {name: 'importe_compras_gravadas_tasa_cero', type: 'numeric'},
        ],
        sortInfo: {
            field: 'id_doc_compra_venta',
            direction: 'ASC'
        },
        arrayDefaultColumHidden: ['codigo_noiata'],
        bdel: true,
        bsave: true,
        onButtonAct: function () {
            if (!this.validarFiltros()) {
                alert('Especifique el año y el mes antes')
            }
            else {
                this.store.baseParams.id_gestion = this.cmbGestion.getValue();
                this.store.baseParams.id_periodo = this.cmbPeriodo.getValue();
                this.store.baseParams.id_depto = this.cmbDepto.getValue();
                Phx.vista.DocCompraVenta.superclass.onButtonAct.call(this);
            }
        },
        formTitulo: 'Registro de Documento Compra',
        abrirFormulario: function (tipo, record) {
            var me = this;
            me.objSolForm = Phx.CP.loadWindows('../../../sis_contabilidad/vista/doc_compra_venta/FormCompraVenta.php',
                me.formTitulo,
                {
                    modal: true,
                    width: '90%',
                    height: (me.regitrarDetalle == 'si') ? '100%' : '80%',
                }, {
                    data: {
                        objPadre: me,
                        tipoDoc: me.tipoDoc,
                        id_gestion: me.cmbGestion.getValue(),
                        id_periodo: me.cmbPeriodo.getValue(),
                        id_depto: me.cmbDepto.getValue(),
                        tmpPeriodo: me.tmpPeriodo,
                        tmpGestion: me.tmpGestion,
                        tipo_form: tipo,
                        datosOriginales: record
                    },
                    regitrarDetalle: me.regitrarDetalle
                },
                this.idContenedor,
                'FormCompraVenta',
                {
                    config: [{
                        event: 'successsave',
                        delegate: this.onSaveForm,
                    }],
                    scope: this
                });
        },
        onButtonNew: function () {
            //abrir formulario de solicitud
            if (!this.validarFiltros()) {
                alert('Especifique el año y el mes antes')
            }
            else {
                this.abrirFormulario('new')
            }
        },
        onButtonEdit: function () {
            if (!this.validarFiltros()) {
                alert('Especifique el año y el mes antes')
            }
            else {
                this.abrirFormulario('edit', this.sm.getSelected())
            }
        },
        oncellclick: function (grid, rowIndex, columnIndex, e) {
            var record = this.store.getAt(rowIndex),
                fieldName = grid.getColumnModel().getDataIndex(columnIndex); // Get field name
            if (fieldName == 'revisado') {
                if (!record.data['id_int_comprobante'] || record.data['id_int_comprobante']) {
                    if (record.data.tipo_reg != 'summary' || record.data.tipo_reg == 'summary') {
                        this.cambiarRevision(record);
                    }
                }
            }
        },
        cambiarRevision: function (record) {
            Phx.CP.loadingShow();
            var d = record.data
            Ext.Ajax.request({
                url: '../../sis_contabilidad/control/DocCompraVenta/cambiarRevision',
                params: {id_doc_compra_venta: d.id_doc_compra_venta},
                success: this.successRevision,
                failure: this.conexionFailure,
                timeout: this.timeout,
                scope: this
            });
        },
        successRevision: function (resp) {
            Phx.CP.loadingHide();
            var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
            if (!reg.ROOT.error) {
                this.reload();
            }
        },
        preparaMenu: function (tb) {
            Phx.vista.DocCompraVenta.superclass.preparaMenu.call(this, tb)
            var data = this.getSelectedData();
            //if(data['revisado'] ==  'si' || data['id_int_comprobante'] > 0 || data.tipo_reg == 'summary' || data.tabla_origen !='ninguno'){
            if (data['revisado'] == 'si' || data.tipo_reg == 'summary') {
                this.getBoton('edit').disable();
                this.getBoton('del').disable();
            }
            else {
                this.getBoton('edit').enable();
                this.getBoton('del').enable();
            }
            if (this.regitrarDetalle == 'si') {
                this.getBoton('btnWizard').enable();
            }
            else {
                this.getBoton('btnWizard').enable   ();
            }
        },
        liberaMenu: function (tb) {
            Phx.vista.DocCompraVenta.superclass.liberaMenu.call(this, tb);
            if (this.regitrarDetalle == 'si') {
                this.getBoton('btnWizard').enable();
            }
            else {
                this.getBoton('btnWizard').disable();
            }
        },
        loadWizard: function () {
            if (!this.validarFiltros()) {
                alert('Especifique el año y el mes antes')
            }
            else {
                Phx.CP.loadWindows('../../../sis_contabilidad/vista/agrupador/WizardAgrupador.php',
                    'Generar comprobante',
                    {
                        width: '40%',
                        height: 300
                    },
                    {
                        id_gestion: this.cmbGestion.getValue(),
                        id_periodo: this.cmbPeriodo.getValue(),
                        id_depto_conta: this.cmbDepto.getValue(),
                        gestion: this.tmpGestion,
                        tipoDoc: this.tipoDoc
                    },
                    this.idContenedor,
                    'WizardAgrupador')
            }
        },
        imprimirLCV: function () {
            if (this.validarFiltros) {
                var me = this;
                Phx.CP.loadingShow();
                Ext.Ajax.request({
                    //url : '../../sis_contabilidad/control/IntComprobante/reporteComprobante',
                    url: '../../sis_contabilidad/control/DocCompraVenta/reporteLCV',
                    params: {
                        'id_periodo': me.cmbPeriodo.getValue(),
                        'id_depto': me.cmbDepto.getValue(),
                        'tipo': me.tipoDoc
                    },
                    success: me.successExport,
                    failure: me.conexionFailure,
                    timeout: me.timeout,
                    scope: me
                });
            }
        },
        expTxt: function (resp) {
            if (this.validarFiltros) {
                var me = this;
                Phx.CP.loadingShow();
                Ext.Ajax.request({
                    url: '../../sis_contabilidad/control/DocCompraVenta/exportarTxtLcvLCV',
                    params: {
                        'id_periodo': me.cmbPeriodo.getValue(),
                        'id_depto': me.cmbDepto.getValue(),
                        'tipo': me.tipoDoc
                    },
                    success: me.successExport,
                    failure: me.conexionFailure,
                    timeout: me.timeout,
                    scope: me
                });
            }
        },
        successExport: function (resp) {
            Phx.CP.loadingHide();
            var objRes = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
            var nomRep = objRes.ROOT.detalle.archivo_generado;
            if (Phx.CP.config_ini.x == 1) {
                nomRep = Phx.CP.CRIPT.Encriptar(nomRep);
            }
            window.open('../../../reportes_generados/' + nomRep + '?t=' + new Date().toLocaleTimeString())
        },
        importarXlsSIN: function () {
            //var rec = this.sm.getSelected();
            Phx.CP.loadWindows('../../../sis_contabilidad/vista/archivo_airbp/FormArchivoAIRBP.php',
                'Subir Archivo',
                {
                    modal: true,
                    width: 450,
                    height: 200
                }, { codigo: 'XLSSIN' }, this.idContenedor, 'FormArchivoAIRBP')
        },

    })
</script>
