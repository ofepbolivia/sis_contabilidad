<?php
/**
 *@package pXP
 *@file    FormFiltroDiario.php
 *@author  manuel guerra
 *@date    09-10-2017
 *@description muestra un formulario que muestra la cuenta y el monto de la transferencia
 */
header("content-type: text/javascript; charset=UTF-8");
?>
<script>
	Phx.vista.FormFiltroDiario = Ext.extend(Phx.frmInterfaz, {

		layout: 'fit',
		maxCount: 0,
		constructor: function (config) {

			Phx.vista.FormFiltroDiario.superclass.constructor.call(this, config);
			this.init();

			this.iniciarEventos();
			this.loadValoresIniciales();
		},

		Atributos: [
			{
				config: {
					name: 'id_gestion',
					fieldLabel: 'Gestion',
					allowBlank: false,
					emptyText: 'Gestion...',
					typeAhead: true,
					lazyRender: true,
					gwidth: 100,
					store: new Ext.data.JsonStore(
						{
							url: '../../sis_parametros/control/Gestion/listarGestion',
							id: 'id_gestion',
							root: 'datos',
							sortInfo: {
								field: 'gestion',
								direction: 'DESC'
							},
							fields: ['id_gestion', 'gestion'],
							// turn on remote sorting
							remoteSort: true,
							baseParams: { par_filtro: 'gestion' }
						}),
					valueField: 'id_gestion',
					triggerAction: 'all',
					displayField: 'gestion',
					hiddenName: 'id_gestion',
					mode: 'remote',
					pageSize: 50,
					queryDelay: 500,
					listWidth: '280',
					width: 80,
				},
				type: 'ComboBox',
				form: true
			},
			{
				config: {
					name: 'tipo_moneda',
					fieldLabel: 'Tipo de Moneda',
					allowBlank: false,
					emptyText: 'Tipo...',
					typeAhead: true,
					triggerAction: 'all',
					lazyRender: true,
					mode: 'local',
					valueField: 'tipo_moneda',
					gwidth: 100,
					enableMultiSelect: true,
					store: new Ext.data.ArrayStore({
						fields: ['variable', 'valor'],
						data: [
							['1', 'Moneda Base'],
							['2', 'Moneda Triangulacion'],
							['3', 'Moneda Actualizacion'],
						]
					}),
					valueField: 'variable',
					displayField: 'valor'
				},
				type: 'ComboBox',
				form: true
			},
			{
				config: {
					name: 'tipo_diario',
					fieldLabel: 'Tipo Diario',
					allowBlank: false,
					emptyText: 'Tipo...',
					typeAhead: true,
					triggerAction: 'all',
					lazyRender: true,
					mode: 'local',
					valueField: 'tipo_diario',
					gwidth: 100,
					store: new Ext.data.ArrayStore({
						fields: ['variable', 'valor'],
						data: [
							['dia', 'Libro Diario'],
							['det', 'Libro Diario Detallado']
						]
					}),
					valueField: 'variable',
					displayField: 'valor'
				},
				type: 'ComboBox',
				form: true
			},
			{
				config: {
					name: 'desde',
					fieldLabel: 'Fecha Inicio',
					xtype: 'datefield',
					allowBlank: true,
					triggerAction: 'all',
					lazyRender: true,
					format: 'd/m/Y',
					width: 200
				},
				type: 'DateField',
				form: true
			}, {
				config: {
					name: 'hasta',
					xtype: 'datefield',
					fieldLabel: 'Fecha Final',
					allowBlank: true,
					triggerAction: 'all',
					lazyRender: true,
					format: 'd/m/Y',
					width: 200
				},
				type: 'DateField',
				form: true
			},
			{
				config: {
					fieldLabel: "Nro Comprobante",
					gwidth: 130,
					name: 'nro_cbte',
					valueField: 'nro_cbte',
					triggerAction: 'all',
					lazyRender: true,
					mode: 'local',
					allowBlank: true,
					maxLength: 50,
					anchor: '100%',
					validator: function (v) {
                            return (/^(?!.*\%.*\%)(?!\%$).*$/.test(v) && /(\S){5,}/g.test(v)) || (v.trim() === '')  ? true : 'Formato inválido,(al menos 5 caracteres sin espacios)';
                    },
                    
				},
				type: 'TextField',
				form: true
			},
			{
                config: {
                    name: 'id_clase_comprobante',
                    fieldLabel: 'Tipo Cbte.',
                    allowBlank: true,
                    emptyText: 'Elija una opción...',
                    store: new Ext.data.JsonStore({
                        url: '../../sis_contabilidad/control/ClaseComprobante/listarClaseComprobante',
                        id: 'id_clase_comprobante',
                        root: 'datos',
                        sortInfo: {
                            field: 'id_clase_comprobante',
                            direction: 'ASC'
                        },
                        totalProperty: 'total',
                        fields: ['id_clase_comprobante', 'tipo_comprobante', 'descripcion', 'codigo', 'momento_comprometido', 'momento_ejecutado', 'momento_pagado'],
                        remoteSort: true,
                        baseParams: {
                            par_filtro: 'ccom.tipo_comprobante#ccom.descripcion'
                        }
                    }),
                    valueField: 'id_clase_comprobante',
                    displayField: 'descripcion',
                    gdisplayField: 'desc_clase_comprobante',
                    hiddenName: 'id_clase_comprobante',
                    forceSelection: true,
                    typeAhead: false,
                    triggerAction: 'all',
                    lazyRender: true,
                    mode: 'remote',
                    pageSize: 15,
                    queryDelay: 1000,
                    anchor: '100%',
                    gwidth: 150,
                    minChars: 2,
                    renderer: function (value, p, record) {
                        return String.format('{0}', record.data['desc_clase_comprobante']);
                    }
                },
                type: 'ComboBox',
                filters: {
                    pfiltro: 'incbte.desc_clase_comprobante',
                    type: 'string'
                },
                form: true
            }, 			
			{
				config: {
					name: 'estado_reg',
					fieldLabel: 'Estado comprobante',
					allowBlank: true,
					emptyText: 'Estado...',
					typeAhead: true,
					triggerAction: 'all',
					lazyRender: true,
					mode: 'local',
					valueField: 'estado_reg',
					gwidth: 100,
					enableMultiSelect: true,
					store: new Ext.data.ArrayStore({
						fields: ['variable', 'valor'],
						data: [
							['anulado','Anulado'],
							['aprobado','Aprobado'],
							['borrador','Borrador'],
							['elaborado','Elaborado'],
							['eliminado','Eliminado'],
							['finalizado','Finalizado'],
							['registrado','Registrado'],
							['supconta','Supconta'],
							['validado','Validado'],
							['vbcbte','Vb Cpte'],
							['vbconta','Vb contabilidad'],
							['verificado','Verificado'],
						]
					}),
					valueField: 'variable',
					displayField: 'valor'
				},
				type: 'AwesomeCombo',
				form: true
			},			
			{
				config: {
					name: 'tipo_formato',
					fieldLabel: 'Tipo de Reporte',
					allowBlank: false,
					emptyText: 'Tipo...',
					typeAhead: true,
					triggerAction: 'all',
					lazyRender: true,
					mode: 'local',
					valueField: 'tipo_formato',
					gwidth: 100,
					store: new Ext.data.ArrayStore({
						fields: ['variable', 'valor'],
						data: [
							['pdf', 'PDF'],
							['xls', 'EXCEL']
						]
					}),
					valueField: 'variable',
					displayField: 'valor'
				},
				type: 'ComboBox',
				form: true

			},
		],
		title: 'Filtro',
		onSubmit: function () {
			//TODO passar los datos obtenidos del wizard y pasar  el evento save		
			if (this.form.getForm().isValid()) {
				this.fireEvent('beforesave', this, this.getValues());
			}
		},

		getValues: function () {
			var data = 
			{
				tipo_formato: this.Cmp.tipo_formato.getValue(),
				tipo_diario: this.Cmp.tipo_diario.getValue(),				
				query_filter: [
				{ name: 'id_gestion', type: 'numeric', field: 'incbte.id_gestion', value: this.Cmp.id_gestion.getValue(), comparison: 'eq' },
				{ name: 'tipo_moneda', type: 'numeric', field: 'incbte.id_moneda', value: this.Cmp.tipo_moneda.getValue(), comparison: 'eq'  },
				{ name: 'desde', type: 'date', field: 'incbte.fecha', value: this.Cmp.desde.getValue() ? this.Cmp.desde.getValue().dateFormat('Y-m-d') : '', comparison: 'gte'  },
				{ name: 'hasta', type: 'date', field: 'incbte.fecha', value: this.Cmp.hasta.getValue() ? this.Cmp.hasta.getValue().dateFormat('Y-m-d') : '', comparison: 'lte'  },
				{ name: 'nro_cbte', type: 'string', field: 'incbte.nro_cbte', value: this.Cmp.nro_cbte.getValue(), comparison: 'like'  },
				{ name: 'id_clase_comprobante', type: 'numeric', field: 'incbte.id_clase_comprobante', value: this.Cmp.id_clase_comprobante.getValue(), comparison: 'eq' },
				{ name: 'estado_reg', type: 'list', field: 'incbte.estado_reg', value: this.Cmp.estado_reg.getValue(), comparison: 'in' }
				]
			};

			return data;
		},
	})
</script>