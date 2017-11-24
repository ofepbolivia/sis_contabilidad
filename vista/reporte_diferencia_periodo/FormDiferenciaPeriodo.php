<?php
/**
 *@package pXP
 *@file    FormDiferenciaPeriodo.php
 *@author  Gonzalo Sarmiento Sejas
 *@date    24-02-2017
 *@description Archivo con la interfaz para generación de reporte
 */
header("content-type: text/javascript; charset=UTF-8");
?>
<script>
    Phx.vista.FormDiferenciaPeriodo = Ext.extend(Phx.frmInterfaz, {

        Atributos : [
            {
                config:{
                    name:'id_gestion',
                    fieldLabel:'Gestión',
                    allowBlank:false,
                    emptyText:'Gestión...',
                    store: new Ext.data.JsonStore({
                        url: '../../sis_parametros/control/Gestion/listarGestion',
                        id: 'id_gestion',
                        root: 'datos',
                        sortInfo:{
                            field: 'gestion',
                            direction: 'DESC'
                        },
                        totalProperty: 'total',
                        fields: ['id_gestion','gestion','moneda','codigo_moneda'],
                        // turn on remote sorting
                        remoteSort: true,
                        baseParams:{par_filtro:'gestion'}
                    }),
                    valueField: 'gestion',
                    displayField: 'gestion',
                    hiddenName: 'id_gestion',
                    forceSelection:true,
                    typeAhead: false,
                    triggerAction: 'all',
                    lazyRender:true,
                    mode:'remote',
                    pageSize:10,
                    queryDelay:1000,
                    listWidth:180,
                    resizable:true,
                    width:180

                },
                type:'ComboBox',
                id_grupo:0,
                filters:{
                    pfiltro:'gestion',
                    type:'string'
                },
                grid:true,
                form:true
            }],


        title : 'Reporte Diferencia Perido',

        topBar : true,
        botones : false,
        labelSubmit : 'Generar',
        tooltipSubmit : '<b>Reporte Diferencia Perido/b>',

        constructor : function(config) {
            Phx.vista.FormDiferenciaPeriodo.superclass.constructor.call(this, config);
            this.init();
        },

        tipo : 'reporte',
        clsSubmit : 'bprint',

        Grupos : [{
            layout : 'column',
            items : [{
                xtype : 'fieldset',
                layout : 'form',
                border : true,
                title : 'Datos para el reporte',
                bodyStyle : 'padding:0 10px 0;',
                columnWidth : '500px',
                items : [],
                id_grupo : 0,
                collapsible : true
            }]
        }],

        ActSave:'../../sis_contabilidad/control/DocCompraVenta/listarDiferenciaPeriodo',

        east: {
            url: '../../../sis_contabilidad/vista/reporte_diferencia_periodo/PeriodoDiferencia.php',
            title: 'Diferecia Periodo',
            width: '70%',
            cls: 'PeriodoDiferencia'
        },
        onSubmit: function(o) {
            var me = this;
            if (me.form.getForm().isValid()) {
                var parametros = me.getValForm();
                console.log('parametros ....', parametros);
                this.onEnablePanel(this.idContenedor + '-east', parametros)
            }
        }
    })
</script>