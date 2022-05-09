<script>


    Ext.define('Phx.vista.PanelLogComisionistas',{
        extend: 'Ext.util.Observable',

        constructor: function(config) {

            var me = this;
            Ext.apply(this, config);
            var me = this;

            this.callParent(arguments);
            this.panel = Ext.getCmp(this.idContenedor);
            this.reportPanel = new Ext.Panel({
                id: 'panelComisionistas',
                width: '100%',
                height: '100%',
                /*renderTo: Ext.get('principal'),*/
                region:'center',
                margins: '5 0 5 5',
                layout: 'fit',
                autoScroll : true,
                items: [{
                    xtype: 'box',
                    width: '100%',
                    height: '100%',
                    autoEl: {
                        tag: 'iframe',
                        src: '../../../sis_contabilidad/vista/periodo_compra_venta/DetalleLog.html?id_periodo_compra_venta='+me.id_periodo_compra_venta,
                    }}]
            });


            this.Border = new Ext.Container({
                layout:'border',
                id:'principal',
                items:[this.reportPanel]
            });

            this.panel.add(this.Border);
            this.panel.doLayout();
            this.addEvents('init');

        }

    });
</script>
