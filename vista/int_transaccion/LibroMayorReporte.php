<?php
/**
 * @package pXP
 * @file gen-LibroMayorReporte.php
 * @author  (Ismael Valdivia)
 * @date 04-12-2019 08:30:00
 * @description Archivo con la interfaz de usuario que permite generar el reporte del libro mayor con el formato de SIGEP.
 */
header("content-type: text/javascript; charset=UTF-8");
?>
<script>
    var ini = null;
    var fin = null;
    var id_auxiliar = null;
    var id_centro_costo;
    var id_config_subtipo_cuenta = null;
    var id_config_tipo_cuenta = null;
    var id_cuenta = null;
    var id_depto = null;
    var id_gestion = null;
    var id_orden_trabajo = null;
    var id_partida = null;
    var id_suborden = null;
    var id_tipo_cc = null;
    var nro_tramite = null;

    var fec = null;


    Phx.vista.LibroMayorReporte = Ext.extend(Phx.gridInterfaz, {
        title: 'Mayor',
        constructor: function (config) {
            var me = this;
            this.maestro = config.maestro;

          //Agrega combo de moneda

            this.Atributos = [
                {
                    //configuracion del componente
                    config: {
                        labelSeparator: '',
                        inputType: 'hidden',
                        name: 'id_int_transaccion'
                    },
                    type: 'Field',
                    form: true
                },
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
                        name: 'desc_centro_costo',
                        fieldLabel: 'Centro Costo',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 300,
                        maxLength: 1000,
                        renderer: function (value, metaData, record, rowIndex, colIndex, store) {
                            //metaData.css = 'multilineColumn';
                            return String.format('<b style="color: green">{0}</b>', record.data['desc_centro_costo']);
                        }
                    },
                    type: 'TextField',
                    filters: {pfiltro: 'cc.codigo_cc', type: 'string'},
                    id_grupo: 1,
                    grid: true,
                    form: false
                },
                {
                    config: {
                        sysorigen: 'sis_contabilidad',
                        name: 'id_cuenta',
                        origen: 'CUENTA',
                        allowBlank: false,
                        fieldLabel: 'Cuenta',
                        gdisplayField: 'desc_cuenta',//mapea al store del grid
                        gwidth: 500,
                        width: 350,
                        listWidth: 350,
                        scope: this,
                        renderer: function (value, p, record) {
                            var color = 'green';
                            if (record.data["tipo_reg"] != 'summary') {
                                if (record.data["tipo_partida"] == 'flujo') {
                                    color = 'red';
                                }


                                var retorno = String.format('<b>Cta.:</b> <font color="blue">{3}</font><br><b>Aux.:</b> <font color="#CC3B00">{4}</font><br><b>Ptda.:</b> <font color="{1}">{2}</font><br><b>CC:</b> {0}', record.data['desc_centro_costo'], color, record.data['desc_partida'],
                                    record.data['desc_cuenta'], record.data['desc_auxiliar']);


                                if (record.data['desc_orden']) {
                                    retorno = retorno + '<br><b>Ord.:</b> ' + record.data['desc_orden'];
                                }
                                if (record.data['desc_suborden']) {
                                    retorno = retorno + '<br><b>Sub.:</b> ' + record.data['desc_suborden'];
                                }
                                return retorno;

                             }
                        }
                    },
                    type: 'ComboRec',
                    id_grupo: 0,
                    filters: {
                        pfiltro: 'cue.nombre_cuenta#cue.nro_cuenta#cc.codigo_cc#cue.nro_cuenta#cue.nombre_cuenta#aux.codigo_auxiliar#aux.nombre_auxiliar#par.codigo#par.nombre_partida#ot.desc_orden',
                        type: 'string'
                    },
                    bottom_filter: true,
                    grid: true,
                    form: true
                },
                {
                    config: {
                        name: 'id_orden_trabajo',
                        fieldLabel: 'Orden Trabajo',
                        sysorigen: 'sis_contabilidad',
                        origen: 'OT',
                        allowBlank: true,
                        gwidth: 200,
                        width: 350,
                        listWidth: 350,
                        gdisplayField: 'desc_orden',
                        renderer: function (value, p, record) {
                            return String.format('{0}', record.data['desc_orden']);
                        }

                    },
                    type: 'ComboRec',
                    id_grupo: 0,
                    filters: {pfiltro: 'ot.motivo_orden#ot.desc_orden', type: 'string'},
                    grid: false,
                    form: true
                },
                {
                    config: {
                        name: 'nro_tramite',
                        fieldLabel: 'Nro Trámite',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 200,
                        maxLength: 1000
                    },
                    type: 'TextField',
                    bottom_filter: true,
                    id_grupo: 1,
                    grid: true,
                    form: true
                },
                {
                    config: {
                        name: 'nro_factura',
                        fieldLabel: 'Nro Fáctura',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 200,
                        maxLength: 1000
                    },
                    type: 'TextField',
                    bottom_filter: true,
                    id_grupo: 1,
                    grid: true,
                    form: true
                },
                {
                    config: {
                        name: 'codigo',
                        fieldLabel: 'Nro Partida',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 200,
                        maxLength: 1000
                    },
                    type: 'TextField',
                    bottom_filter: true,
                    id_grupo: 1,
                    grid: true,
                    form: true
                },
                {
                    config: {
                        name: 'c31',
                        fieldLabel: 'C-31',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 200,
                        maxLength: 1000
                    },
                    type: 'TextField',
                    bottom_filter: true,
                    id_grupo: 1,
                    grid: true,
                    form: true
                },
                {
                    config: {
                        name: 'fecha_costo_ini',
                        fieldLabel: 'Fecha Inicio',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        format: 'd/m/Y',
                        renderer: function (value, p, record) {
                            return value ? value.dateFormat('d/m/Y') : ''
                        }
                    },
                    type: 'DateField',
                    filters: {pfiltro: 'icbte.fecha_costo_ini', type: 'date'},
                    id_grupo: 1,
                    grid: true,
                    form: false,
                    bottom_filter: true
                },
                {
                    config: {
                        name: 'fecha_costo_fin',
                        fieldLabel: 'Fecha Fin',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        format: 'd/m/Y',
                        renderer: function (value, p, record) {
                            return value ? value.dateFormat('d/m/Y') : ''
                        }
                    },
                    type: 'DateField',
                    filters: {pfiltro: 'icbte.fecha_costo_ini', type: 'date'},
                    id_grupo: 1,
                    grid: true,
                    form: false,
                    bottom_filter: true
                },
                {
                    config: {
                        sysorigen: 'sis_contabilidad',
                        name: 'id_auxiliar',
                        origen: 'AUXILIAR',
                        allowBlank: true,
                        fieldLabel: 'Auxiliar',
                        gdisplayField: 'desc_auxiliar',//mapea al store del grid
                        gwidth: 200,
                        width: 350,
                        listWidth: 350,
                        //anchor: '80%',
                        renderer: function (value, p, record) {
                            return String.format('{0}', record.data['desc_auxiliar']);
                        }
                    },
                    type: 'ComboRec',
                    id_grupo: 0,
                    filters: {
                        pfiltro: 'au.codigo_auxiliar#au.nombre_auxiliar',
                        type: 'string'
                    },

                    grid: false,
                    form: true
                },
                {
                    config: {
                        sysorigen: 'sis_presupuestos',
                        name: 'id_partida',
                        origen: 'PARTIDA',
                        allowBlank: false,
                        fieldLabel: 'Partida',
                        gdisplayField: 'desc_partida',//mapea al store del grid
                        gwidth: 200,
                        width: 350,
                        listWidth: 350,
                        renderer: function (value, p, record) {
                            return String.format('{0}', record.data['desc_partida']);
                        }
                    },
                    type: 'ComboRec',
                    id_grupo: 0,
                    filters: {
                        pfiltro: 'par.codigo_partida#au.nombre_partida',
                        type: 'string'
                    },

                    grid: false,
                    form: true
                },
                {
                    config: {
                        name: 'id_centro_costo',
                        fieldLabel: 'Centro Costo',
                        allowBlank: false,
                        tinit: false,
                        origen: 'CENTROCOSTO',
                        gdisplayField: 'desc_centro_costo',
                        width: 350,
                        listWidth: 350,
                        gwidth: 300,
                        renderer: function (value, p, record) {
                            return String.format('{0}', record.data['desc_centro_costo']);
                        }
                    },
                    type: 'ComboRec',
                    filters: {pfiltro: 'cc.codigo_cc', type: 'string'},
                    id_grupo: 1,
                    grid: false,
                    form: true
                },
                {
                    config: {
                        name: 'glosa',
                        fieldLabel: 'Glosa',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 300,
                        maxLength: 1000,
                        renderer: function (value, metaData, record, rowIndex, colIndex, store) {
                            metaData.css = 'multilineColumn';
                            return String.format('{0} <br> {1}', record.data['glosa1'], value);
                        }
                    },
                    type: 'TextArea',
                    filters: {pfiltro: 'transa.glosa', type: 'string'},
                    id_grupo: 1,
                    grid: true,
                    form: true
                },


                {
                    config: {
                        name: 'importe_debe_mb',
                        fieldLabel: 'Debe MB',
                        allowBlank: true,
                        width: '100%',
                        gwidth: 110,
                        galign: 'right ',
                        maxLength: 100,
                        renderer: function (value, p, record) {
                            if (record.data.tipo_reg != 'summary') {
                                return String.format('{0}', Ext.util.Format.number(value, '0,000.00'));
                            } else {
                                return String.format('<b><font size=2 >{0}</font><b>', Ext.util.Format.number(value, '0,000.00'));
                            }
                        }
                    },
                    type: 'NumberField',
                    filters: {pfiltro: 'transa.importe_debe_mb', type: 'numeric'},
                    id_grupo: 1,
                    grid: true,
                    form: true
                },
                {
                    config: {
                        name: 'importe_haber_mb',
                        fieldLabel: 'Haber MB',
                        allowBlank: true,
                        width: '100%',
                        gwidth: 110,
                        galign: 'right ',
                        maxLength: 100,
                        renderer: function (value, p, record) {
                            if (record.data.tipo_reg != 'summary') {
                                return String.format('{0}', Ext.util.Format.number(value, '0,000.00'));
                            } else {
                                return String.format('<b><font size=2 >{0}</font><b>', Ext.util.Format.number(value, '0,000.00'));
                            }
                        }
                    },
                    type: 'NumberField',
                    filters: {pfiltro: 'transa.importe_haber_mb', type: 'numeric'},
                    id_grupo: 1,
                    grid: true,
                    form: true
                },
                {
                    config: {
                        name: 'importe_saldo_mb',
                        fieldLabel: 'Saldo MB',
                        allowBlank: true,
                        width: '100%',
                        gwidth: 110,
                        galign: 'right ',
                        maxLength: 100,
                        renderer: function (value, p, record) {
                            if (record.data.tipo_reg != 'summary') {
                                return String.format('{0}', Ext.util.Format.number(value, '0,000.00'));
                            } else {
                                return String.format('<b><font size=2 >{0}</font><b>', Ext.util.Format.number(value, '0,000.00'));
                            }
                        }
                    },
                    type: 'NumberField',
                    id_grupo: 1,
                    grid: true,
                    form: true
                },
                {
                    config: {
                        name: 'estado_reg',
                        fieldLabel: 'Estado Reg.',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength: 10
                    },
                    type: 'Field',
                    filters: {pfiltro: 'transa.estado_reg', type: 'string'},
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
                    form: false
                },
                {
                    config: {
                        name: 'fecha_reg',
                        fieldLabel: 'Fecha creación',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        format: 'd/m/Y',
                        renderer: function (value, p, record) {
                            return value ? value.dateFormat('d/m/Y H:i:s') : ''
                        }
                    },
                    type: 'DateField',
                    filters: {pfiltro: 'transa.fecha_reg', type: 'date'},
                    id_grupo: 1,
                    grid: true,
                    form: false
                },
                {
                    config: {
                        name: 'usr_mod',
                        fieldLabel: 'Modificado por',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength: 4
                    },
                    type: 'Field',
                    filters: {pfiltro: 'usu2.cuenta', type: 'string'},
                    id_grupo: 1,
                    grid: true,
                    form: false
                },
                {
                    config: {
                        name: 'fecha_mod',
                        fieldLabel: 'Fecha Modif.',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        format: 'd/m/Y',
                        renderer: function (value, p, record) {
                            return value ? value.dateFormat('d/m/Y H:i:s') : ''
                        }
                    },
                    type: 'DateField',
                    filters: {pfiltro: 'transa.fecha_mod', type: 'date'},
                    id_grupo: 1,
                    grid: true,
                    form: false
                }
            ];


            //llama al constructor de la clase padre
            Phx.vista.LibroMayorReporte.superclass.constructor.call(this, config);

            /********************Aumentando boton para sacar reporte libro mayor*******************************/
            //Ismael Valdivia (3/12/2019)
            this.addButton('btnImprimirReportePDF', {
              text: '<center>Reporte PDF<br>Libro Mayor</center>',
              iconCls: 'bpdf',
              disabled: false,
              handler: this.libroMayorPDF,
              tooltip: '<b>Generar Reporte</b><br/>Genera reporte del Libro Mayor'
            });

            this.addButton('btnImprimirReporteExcel', {
              text: '<center>Reporte XLS<br>Libro Mayor</center>',
              iconCls: 'bexcel',
              disabled: false,
              handler: this.libroMayorEXCEL,
              tooltip: '<b>Generar Reporte</b><br/>Genera reporte del Libro Mayor'
            });
            /***********************************************************************************************/


            this.grid.getTopToolbar().disable();
            this.grid.getBottomToolbar().disable();
            this.init();

        		this.grid.body.dom.firstChild.firstChild.lastChild.style.background='#F1F7FF';
        		this.grid.body.dom.firstChild.firstChild.firstChild.firstChild.style.background='#dfe8f6';
            this.iniciarEventos();


        },


        tam_pag: 50,

        ActList: '../../sis_contabilidad/control/IntTransaccion/listarReporteLibroMayorPDF',
        id_store: 'id_int_transaccion',
        fields: [
            {name: 'id_int_transaccion', type: 'numeric'},
            {name: 'id_partida', type: 'numeric'},
            {name: 'id_centro_costo', type: 'numeric'},
            {name: 'id_partida_ejecucion', type: 'numeric'},
            {name: 'estado_reg', type: 'string'},
            {name: 'id_int_transaccion_fk', type: 'numeric'},
            {name: 'id_cuenta', type: 'numeric'},
            {name: 'glosa', type: 'string'},
            {name: 'id_int_comprobante', type: 'numeric'},
            {name: 'id_auxiliar', type: 'numeric'},
            {name: 'id_usuario_reg', type: 'numeric'},
            {name: 'fecha_reg', type: 'date', dateFormat: 'Y-m-d H:i:s.u'},
            {name: 'id_usuario_mod', type: 'numeric'},
            {name: 'fecha_mod', type: 'date', dateFormat: 'Y-m-d H:i:s.u'},
            {name: 'usr_reg', type: 'string'},
            {name: 'usr_mod', type: 'string'},
            {name: 'importe_debe', type: 'numeric'},
            {name: 'importe_haber', type: 'numeric'},
            {name: 'importe_gasto', type: 'numeric'},
            {name: 'importe_recurso', type: 'numeric'},
            {name: 'importe_debe_mb', type: 'numeric'},
            {name: 'importe_haber_mb', type: 'numeric'},
            {name: 'importe_saldo_mb', type: 'numeric'},
            {name: 'codigo', type: 'string'},
            {name: 'c31', type: 'string'},
            { name:'fecha_costo_ini', type: 'date',dateFormat:'Y-m-d'},
            { name:'fecha_costo_fin', type: 'date',dateFormat:'Y-m-d'},

            {name: 'desc_cuenta', type: 'string'},
            {name: 'desc_auxiliar', type: 'string'},
            {name: 'desc_partida', type: 'string'},
            {name: 'desc_centro_costo', type: 'string'},
            'cbte_relacional',
            'cbte_relacional',
            'tipo_partida', 'id_orden_trabajo', 'desc_orden',
            'tipo_reg', 'nro_cbte', 'nro_tramite','nro_factura','nombre_corto', 'fecha', 'glosa1',
            'id_suborden', 'desc_suborden',

        ],


        rowExpander: new Ext.ux.grid.RowExpander({
            tpl: new Ext.Template(
                '<br>',
                '<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Cbte:&nbsp;&nbsp;</b> {nro_cbte} - {nombre_corto}</p>',
                '<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Trámite:&nbsp;&nbsp;</b> {nro_tramite} &nbsp; {fecha:date("d/m/Y")}</p>',
                '<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Creado por:&nbsp;&nbsp;</b> {usr_reg}</p>',
                '<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Estado Registro:&nbsp;&nbsp;</b> {estado_reg}</p><br>'
            )
        }),

        arrayDefaultColumHidden: ['fecha_mod', 'usr_reg', 'usr_mod', 'estado_reg', 'fecha_reg', 'desc_centro_costo'],

        sortInfo: {
            field: 'id_int_transaccion',
            direction: 'ASC'
        },
        bdel: true,
        bsave: false,
        loadValoresIniciales: function () {
            Phx.vista.LibroMayorReporte.superclass.loadValoresIniciales.call(this);
            this.getComponente('id_int_comprobante').setValue(this.maestro.id_int_comprobante);
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

        /*Aumentando funcion para sacar el reporte del libro mayor Ismael Valdivia (3/12/2019) */
        libroMayorPDF: function () {
              Phx.CP.loadingShow();
              console.log("la fecha es",this);
              Ext.Ajax.request({
                  url: '../../sis_contabilidad/control/IntTransaccion/GenerarLibroMayor',
                  params: {
                      gestion: this.store.baseParams.gest,
                      cuenta: this.store.baseParams.cuenta,
                      auxiliar: this.store.baseParams.auxiliar,
                      partida: this.store.baseParams.partida,
                      centro_costo: this.store.baseParams.cc,
                      /*Parametros que enviaremos a la base de datos Ismael Valdivia (06/12/2019)*/
                      id_cuenta:this.store.baseParams.id_cuenta,
                      id_auxiliar:this.store.baseParams.id_auxiliar,
                      id_gestion:this.store.baseParams.id_gestion,
                      id_centro_costo:this.store.baseParams.id_centro_costo,
                      id_partida:this.store.baseParams.id_partida,
                      desde: this.store.baseParams.desde,
                      hasta: this.store.baseParams.hasta,
                      id_orden_trabajo: this.store.baseParams.id_orden_trabajo
                      /***************************************************************************/
                  },
                  success: this.successExport,
                  failure: this.conexionFailure,
                  timeout: this.timeout,
                  scope: this
              });

        },

        libroMayorEXCEL: function () {
              Phx.CP.loadingShow();
              console.log("El dato es el siguiente",this);
              Ext.Ajax.request({
                  url: '../../sis_contabilidad/control/IntTransaccion/GenerarReporteLibroMayorExcel',
                  params: {
                      gestion: this.store.baseParams.gest,
                      cuenta: this.store.baseParams.cuenta,
                      auxiliar: this.store.baseParams.auxiliar,
                      partida: this.store.baseParams.partida,
                      centro_costo: this.store.baseParams.cc,
                      /*Parametros que enviaremos a la base de datos Ismael Valdivia (06/12/2019)*/
                      id_cuenta:this.store.baseParams.id_cuenta,
                      id_auxiliar:this.store.baseParams.id_auxiliar,
                      id_gestion:this.store.baseParams.id_gestion,
                      id_centro_costo:this.store.baseParams.id_centro_costo,
                      id_partida:this.store.baseParams.id_partida,
                      desde: this.store.baseParams.desde,
                      hasta: this.store.baseParams.hasta,
                      id_orden_trabajo: this.store.baseParams.id_orden_trabajo
                      /***************************************************************************/
                  },
                  success: this.successExport,
                  failure: this.conexionFailure,
                  timeout: this.timeout,
                  scope: this
              });

        },
        /***************************************************************************************/
        ExtraColumExportDet: [{
            label: 'Partida',
            name: 'desc_partida',
            width: '200',
            type: 'string',
            gdisplayField: 'desc_partida',
            value: 'desc_partida'
        },
            {
                label: 'Cbte',
                name: 'nro_cbte',
                width: '100',
                type: 'string',
                gdisplayField: 'nro_cbte',
                value: 'nro_cbte'
            }],
        //mpmpmp
        postReloadPage: function (data) {
            console.log(data);
            ini = data.desde;
            fin = data.hasta;
            aux = data.auxiliar;
            gest = data.gest;
            cuenta = data.cuenta;
            partida = data.partida;
            centro_costo = data.cc;
            orden_trabajo = data.ot;
            id_auxiliar = data.id_auxiliar;
            id_centro_costo = data.id_centro_costo;
            id_cuenta = data.id_cuenta;
            id_gestion = data.id_gestion;
            id_partida = data.id_partida;
            id_orden_trabajo = data.id_orden_trabajo;

        },

        bnew: false,
        bedit: false,
        bdel: false,
        bexcel:false,
      	btest:false
    })
</script>
