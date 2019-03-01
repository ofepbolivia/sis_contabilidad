<?php
header("content-type: text/javascript; charset=UTF-8");
?>
<script>
    Phx.vista.ConsolidacionFondosNoFinalizados = Ext.extend(Phx.frmInterfaz, {
        Atributos : [


            {
                config:{
                    name: 'fecha_ini',
                    fieldLabel: 'Fecha Inicio',
                    allowBlank: false,
                    disabled: false,
                    gwidth: 180,
                    qtip: 'Fecha Inicio Comprobante',
                    format: 'd/m/Y'

                },
                type:'DateField',
                id_grupo:0,
                form:true
            },
            {
                config:{
                    name: 'fecha_fin',
                    fieldLabel: 'Fecha Fin',
                    allowBlank: false,
                    disabled: false,
                    gwidth: 180,
                    qtip: 'Fecha Fin Comprobante',
                    format: 'd/m/Y'

                },
                type:'DateField',
                id_grupo:0,
                form:true
            }
        ],
        title : 'Generar Reporte',
        ActSave : '../../sis_contabilidad/control/ConsolidacionFondos/reporteConsolidacionFondosNoFinalizados',
        topBar : true,
        botones : false,
        labelSubmit : 'Imprimir',
        tooltipSubmit : '<b>Generar Reporte</b>',
        constructor : function(config) {
            Phx.vista.ConsolidacionFondosNoFinalizados.superclass.constructor.call(this, config);
            this.init();
        },
        tipo : 'reporte',
        clsSubmit : 'bprint'
    })
</script>