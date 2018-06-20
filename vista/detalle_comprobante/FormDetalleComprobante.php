<?php

header("content-type: text/javascript; charset=UTF-8");
?>

<script>
    Phx.vista.FormDetalleComprobante = Ext.extend(Phx.frmInterfaz, {
        constructor: function (config) {
            this.panelResumen = new Ext.Panel({html: ''});
            this.Grupos = [{

                xtype: 'fieldset',
                border: false,
                autoScroll: true,
                layout: 'form',
                items: [],
                id_grupo: 0

            },
                this.panelResumen
            ];

            Phx.vista.FormDetalleComprobante.superclass.constructor.call(this, config);
            this.init();

            this.iniciarEventos();
        },

        Atributos: [

            {
                //configuracion del componente
                config: {
                    labelSeparator: '',
                    inputType: 'hidden',
                    name: 'concepto'
                },
                type: 'Field',
                form: true
            },
            // {
            //     config: {
            //         name: 'id_gestion',
            //         origen: 'GESTION',
            //         fieldLabel: 'Gestion',
            //         allowBlank: false,
            //         width: 150
            //     },
            //     type: 'ComboRec',
            //     id_grupo: 0,
            //     form: true
            // },

            /*{
                config: {
                    name: 'nro_tramite',
                    allowBlank: true,
                    fieldLabel: 'Nro. de Trámite',
                    width: 150
                },
                type: 'Field',
                id_grupo: 0,
                form: true
            },*/

            {
                config: {
                    name: 'desde',
                    fieldLabel: 'Desde',
                    allowBlank: true,
                    format: 'd/m/Y',
                    qtip: 'Filtra por Fecha de Comprobante',
                    width: 150
                },
                type: 'DateField',
                id_grupo: 0,
                form: true
            },

            {
                config: {
                    name: 'hasta',
                    fieldLabel: 'Hasta',
                    allowBlank: true,
                    format: 'd/m/Y',
                    qtip: 'Filtra por Fecha de Comprobante',
                    width: 150
                },
                type: 'DateField',
                id_grupo: 0,
                form: true
            },

            {
                config: {
                    name: 'id_depto_conta',
                    fieldLabel: 'Depto.',
                    allowBlank: true,
                    emptyText: 'Depto Contable',
                    store: new Ext.data.JsonStore({
                        //url : '../../sis_parametros/control/Depto/listarDeptoFiltradoDeptoUsuario',
                        url : '../../sis_parametros/control/Depto/listarDepto',
                        id: 'id_depto_conta',
                        root: 'datos',
                        sortInfo: {
                            //field: 'nombre',
                            field: 'deppto.nombre',
                            direction: 'ASC'
                        },
                        totalProperty: 'total',
                        fields: ['id_depto', 'nombre', 'codigo'],
                        remoteSort: true,
                        baseParams: {par_filtro: 'deppto.nombre#deppto.codigo',
                            estado : 'activo',
                            codigo_subsistema : 'CONTA'
                        },

                    }),
                    valueField: 'id_depto',
                    displayField: 'nombre',
                    gdisplayField: 'id_depto',
                    hiddenName: 'id_depto',
                    //forceSelection: true,
                    //typeAhead: false,
                    triggerAction: 'all',
                    lazyRender: true,
                    mode: 'remote',
                    pageSize: 20,
                    queryDelay: 1000,
                    anchor: '90%',


                    // renderer: function (value, p, record) {
                    //     return String.format('{0}', record.data['desc_entidad_tranferencia']);
                    // },
                    // tpl: new Ext.XTemplate([
                    //     '<tpl for=".">',
                    //     '<div class="x-combo-list-item">',
                    //     '<div class="awesomecombo-item {checked}">',
                    //     '<p><b>Codigo: {codigo}</b></p>',
                    //     '</div><p><b>Nombre:</b> <span style="color: green;">{nombre}</span></p>',
                    //     '</div></tpl>'
                    // ])
                },
                type: 'AwesomeCombo',
                id_grupo: 0,
                //bottom_filter: true,
                filters: {pfiltro: 'deppto.nombre#deppto.codigo', type: 'string'}
                //grid: true,
                //form: true
            },


            {
                config: {
                    name: 'id_clase_comprobante',
                    fieldLabel: 'Tipo Comprobante',
                    allowBlank: true,
                    emptyText: 'Tipo',
                    store: new Ext.data.JsonStore({
                       url : '../../sis_contabilidad/control/ClaseComprobante/listarClaseComprobante',
                        id: 'id_clase_comprobante',
                        root: 'datos',
                        sortInfo: {
                            //field: 'nombre',
                            field: 'ccom.descripcion',
                            direction: 'ASC'
                        },
                        totalProperty: 'total',
                        fields: ['id_clase_comprobante', 'descripcion'],
                        remoteSort: true,
                        baseParams: {par_filtro: 'ccom.descripcion',
                            estado : 'activo',
                            //codigo_subsistema : 'CONTA'
                        },

                    }),
                    valueField: 'id_clase_comprobante',
                    displayField: 'descripcion',
                    gdisplayField: 'id_clase_comprobante',
                    hiddenName: 'id_clase_comprobante',
                    //forceSelection: true,
                    //typeAhead: false,
                    triggerAction: 'all',
                    lazyRender: true,
                    mode: 'remote',
                    pageSize: 20,
                    queryDelay: 1000,
                    anchor: '90%',


                },
                type: 'AwesomeCombo',
                id_grupo: 0,
                //bottom_filter: true,
                filters: {pfiltro: 'ccom.descripcion', type: 'string'}
                //grid: true,
                //form: true
            },


        ],
        labelSubmit: '<i class="fa fa-check"></i> Aplicar Filtro',

        east: {
            url: '../../../sis_contabilidad/vista/detalle_comprobante/DetalleComprobante.php',
            title: 'Detalle Comprobante',
            width: '70%',
            cls: 'DetalleComprobante'
        },

        title: 'Filtros para el Reporte de Comprobante ERP BOA vs SIGEP',
        // Funcion guardar del formulario
        onSubmit: function () {
            var me = this;
            if (me.form.getForm().isValid()) {

                var parametros = me.getValForm()

                //console.log('parametros ....', parametros);

                this.onEnablePanel(this.idContenedor + '-east', parametros)
            }

            //console.log('datos de lo paramtros',o,x,y)
            Phx.vista.FormDetalleComprobante.superclass.onSubmit.call(this, o, x);
            //Phx.vista.FormFiltro.superclass.onSubmit.call(this, o, x, force);

        },
        iniciarEventos: function () {


               },


    })
</script>