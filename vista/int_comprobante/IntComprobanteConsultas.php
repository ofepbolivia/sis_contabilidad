<?php
/**
 * fRnk: nueva vista req. b. HR00903
 */
header("content-type: text/javascript; charset=UTF-8");
?>
<script>
    Phx.vista.IntComprobanteConsultas = {
        bsave: false,
        bnew: false,
        bdel: false,
        bedit: false,
        btnWizard: false,
        require: '../../../sis_contabilidad/vista/int_comprobante/IntComprobante.php',
        requireclase: 'Phx.vista.IntComprobante',
        title: 'Libro Diario',
        nombreVista: 'IntComprobanteConsultas',

        constructor: function (config) {
            var me = this;
            Phx.vista.IntComprobanteConsultas.superclass.constructor.call(this, config);
            this.getBoton('diagrama_gantt').hide();

            this.addButton('btnWizard', {
                text: 'Plantilla',
                iconCls: 'bgear',
                disabled: false,
                hidden: true,
                handler: this.loadWizard,
                tooltip: '<b>Plantilla de Comprobantes</b><br/>Seleccione una plantilla y genere comprobantes preconfigurados'
            });

            this.store.baseParams.estado_cbte = this.pes_estado;
            this.init();
        },

        cmbDepto: new Ext.form.AwesomeCombo({
            name: 'id_depto',
            fieldLabel: 'Depto',
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
                remoteSort: true,
                baseParams: {
                    par_filtro: 'deppto.nombre#deppto.codigo',
                    estado: 'activo',
                    codigo_subsistema: 'CONTA'
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

        successEstadoSinc: function (resp) {
            Phx.CP.loadingHide();
            resp.argument.wizard.panel.destroy();
            this.reload();
        },

        selectButton: function (grid, rowIndex, rec) {
            let record = this.getSelectedData();
            if (record.estado_reg == 'vbconta') {
                this.getBoton('sigep_vb').setText('APROBAR SIGEP');
            } else if (record.estado_reg == 'vbfin') {
                this.getBoton('sigep_vb').setText('FIRMAR SIGEP');
            }
        },

        deselectButton: function (grid, rowIndex, rec) {
            this.getBoton('sigep_vb').setText('ENVIAR SIGEP');
        },

        preparaMenu: function (n) {
            var tb = Phx.vista.IntComprobanteConsultas.superclass.preparaMenu.call(this);
            var rec = this.sm.getSelected();
            if (rec.data.tipo_reg == 'summary') {
                this.getBoton('btnImprimir').disable();
                this.getBoton('btnRelDev').disable();
                this.getBoton('btnDocCmpVnt').disable();
            } else {
                this.getBoton('btnImprimir').enable();
                this.getBoton('btnRelDev').enable();
                this.getBoton('btnDocCmpVnt').enable();
                this.getBoton('chkpresupuesto').enable();
                this.getBoton('btnChequeoDocumentosWf').enable();
                this.getBoton('diagrama_gantt').hide();
                this.getBoton('btnObs').enable();
            }
            if (rec.data.momento == 'presupuestario') {
                this.getBoton('btnDocCmpVnt').enable();
            } else {
                this.getBoton('btnDocCmpVnt').disable();
            }
            return tb;
        },
        liberaMenu: function () {
            var tb = Phx.vista.IntComprobanteConsultas.superclass.liberaMenu.call(this);
            this.getBoton('btnImprimir').disable();
            this.getBoton('btnRelDev').disable();
            this.getBoton('btnDocCmpVnt').disable();
            this.getBoton('chkpresupuesto').disable();
            this.getBoton('btnChequeoDocumentosWf').disable();
            this.getBoton('btnObs').disable()
        },

        tabsouth: [
            {
                url: '../../../sis_contabilidad/vista/int_transaccion/IntTransaccionAux.php',
                title: 'Transacciones',
                height: '50%',
                cls: 'IntTransaccionAux'
            },
            {
                url: '../../../sis_contabilidad/vista/int_beneficiario/IntBeneficiarioAux.php',
                title: 'Beneficiario',
                height: '50%',
                cls: 'IntBeneficiarioAux'
            }
        ]

    };
</script>