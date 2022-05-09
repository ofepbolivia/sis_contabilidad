<?php

header("content-type: text/javascript; charset=UTF-8");
?>
<script>
    Phx.vista.ReporteCompraExt = Ext.extend(Phx.frmInterfaz, {
        Atributos: [

            {
                config: {
                    name: 'fecha_ini',
                    fieldLabel: 'Fecha Inicio',
                    allowBlank: false,
                    disabled: false,
                    gwidth: 100,
                    qtip: 'Fecha Inicio del Pago',
                    format: 'd/m/Y'

                },
                type: 'DateField',
                id_grupo: 0,
                form: true
            },
            {
                config: {
                    name: 'fecha_fin',
                    fieldLabel: 'Fecha Fin',
                    allowBlank: false,
                    disabled: false,
                    gwidth: 100,
                    qtip: 'Fecha Fin del Pago',
                    format: 'd/m/Y'

                },
                type: 'DateField',
                id_grupo: 0,
                form: true
            }
        ],
        title: 'Generar Reporte',
        ActSave: '../../sis_contabilidad/control/DocCompraVenta/reporteCompraExt',
        topBar: true,
        botones: false,
        labelSubmit: 'Imprimir',
        tooltipSubmit: '<b>Generar Reporte</b>',
        constructor: function (config) {
            Phx.vista.ReporteCompraExt.superclass.constructor.call(this, config);
            this.init();
        },
        tipo: 'reporte',
        clsSubmit: 'bprint'
    })
</script>