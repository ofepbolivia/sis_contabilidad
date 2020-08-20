<?php

/**
 * @package pXP
 * @file gen-ReporteCompraExt.php
 * @author  Maylee Perez Pastor
 * @date 27-02-2020 12:55:30
 * @description interfaz reporte de los documentos de compra (factura)
 */

header("content-type: text/javascript; charset=UTF-8");
?>
<script>
    Phx.vista.RepDetalleGastos = Ext.extend(Phx.frmInterfaz, {
        Atributos: [

            {
                config: {
                    name: 'fecha_ini',
                    fieldLabel: 'Fecha Inicio',
                    allowBlank: false,
                    disabled: false,
                    width:220,
                    qtip: 'Fecha de Pago',
                    format: 'd/m/Y'

                },
                type: 'DateField',
                qtip: '2',
                id_grupo: 0,
                form: true
            },
            {
                config: {
                    name: 'fecha_fin',
                    fieldLabel: 'Fecha Fin',
                    allowBlank: false,
                    disabled: false,
                    width:220,
                    qtip: 'Fecha de Pago',
                    format: 'd/m/Y'

                },
                type: 'DateField',
                id_grupo: 0,
                form: true
            },
            {
                config:{
                    name: 'id_proveedor',
                    fieldLabel: 'Proveedor',
                    allowBlank: true,
                    emptyText: 'Proveedor ...',
                    store: new Ext.data.JsonStore({
                        url: '../../sis_parametros/control/Proveedor/listarProveedorCombos',
                        id: 'id_proveedor',
                        root: 'datos',
                        sortInfo:{
                            field: 'desc_proveedor',
                            direction: 'ASC'
                        },
                        totalProperty: 'total',
                        fields: ['id_proveedor','codigo','desc_proveedor'],
                        // turn on remote sorting
                        remoteSort: true,
                        baseParams:{par_filtro:'codigo#desc_proveedor'}
                    }),
                    valueField: 'id_proveedor',
                    displayField: 'desc_proveedor',
                    gdisplayField: 'desc_proveedor',
                    hiddenName: 'id_proveedor',
                    triggerAction: 'all',
                    pageSize:10,
                    forceSelection: true,
                    typeAhead: true,
                    listWidth:'280',
                    gwidth: 200,
                    width:220,
                    mode: 'remote'
                },

                type:'ComboBox',
                id_grupo:1,
                form:true
            }
        ],
        title: 'Generar Reporte',
        ActSave: '../../sis_contabilidad/control/ReporteExt/reporteDetalleGastos',
        topBar: true,
        botones: false,
        labelSubmit: 'Imprimir',
        tooltipSubmit: '<b>Generar Reporte</b>',
        constructor: function (config) {
            Phx.vista.RepDetalleGastos.superclass.constructor.call(this, config);
            this.init();
        },
        tipo: 'reporte',
        clsSubmit: 'bprint'
    })
</script>