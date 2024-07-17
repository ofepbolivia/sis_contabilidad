<?php
header("content-type: text/javascript; charset=UTF-8");
//fRnk: HR00690 b.
?>
<script>
    Phx.vista.CuentaGrid = Ext.extend(Phx.gridInterfaz, {
            constructor: function (config) {
                this.maestro = config.maestro;
                Phx.vista.CuentaGrid.superclass.constructor.call(this, config);
                this.init();
                this.store.baseParams = {
                    id_gestion: config.id_gestion,
                    bottom_filter_value: ''
                };
                this.load({params: {start: 0, limit: this.tam_pag}});
            },

            Atributos: [
                {
                    config: {
                        labelSeparator: '',
                        inputType: 'hidden',
                        name: 'id_cuenta'
                    },
                    type: 'Field',
                    form: true,
                    grid: false
                },
                {
                    config: {
                        name: 'nro_cuenta',
                        fieldLabel: 'Cuenta',
                        allowBlank: true,
                        anchor: '90%',
                        gwidth: 190
                    },
                    type: 'TextField',
                    filters: {pfiltro: 'nro_cuenta', type: 'string'},
                    id_grupo: 0,
                    grid: true,
                    form: false,
                    bottom_filter: true
                },
                {
                    config: {
                        name: 'nombre_cuenta',
                        fieldLabel: 'Descripción Cuenta',
                        allowBlank: true,
                        anchor: '100%',
                        gwidth: 350,
                    },
                    type: 'TextField',
                    filters: {pfiltro: 'nombre_cuenta', type: 'string'},
                    id_grupo: 0,
                    grid: true,
                    form: false
                }
            ],
            tam_pag: 10000,
            title: 'Plan de Cuentas',
            ActList: '../../sis_contabilidad/control/Cuenta/getListaCuentas',
            id_store: 'nro_cuenta',
            fields: [
                {name: 'id_cuenta', type: 'numeric'},
                {name: 'nro_cuenta', type: 'string'},
                {name: 'nombre_cuenta', type: 'string'},
                {name: 'id_cuenta_padre', type: 'numeric'}
            ],

            preparaMenu: function (n) {
                var tb = this.tbar;
                Phx.vista.CuentaGrid.superclass.preparaMenu.call(this, n);
                return tb
            },
            liberaMenu: function () {
                var tb = Phx.vista.CuentaGrid.superclass.liberaMenu.call(this);
                return tb
            },

            successObs: function (resp) {
                Phx.CP.loadingHide();
                var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
                if (!reg.ROOT.error) {
                    this.reload();
                }
            },

            sortInfo: {
                field: 'nro_cuenta',
                direction: 'ASC'
            },
            bnew: false,
            bedit: false,
            bdel: false,
            bsave: false,
            bexcel: false
        }
    );

</script>
