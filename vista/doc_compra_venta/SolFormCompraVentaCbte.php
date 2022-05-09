<?php
/**
 * @package pXP
 * @file FormRendicionCD.php
 * @author  Maylee Perez
 * @date 26-03-2019
 * @description Archivo con la interfaz de usuario que permite
 *ingresar el documento a rendir
 *
 */
header("content-type: text/javascript; charset=UTF-8");
?>
<script>
    Phx.vista.SolFormCompraVentaCbte = {
        // require: '../../../sis_contabilidad/vista/doc_compra_venta/FormCompraVenta.php',
        //require: '../../../sis_contabilidad/vista/doc_compra_venta/SolFormCompraVenta.php',
        require: '../../../sis_contabilidad/vista/doc_compra_venta/FormCompraVentaEXT.php',
        //requireclase: 'Phx.vista.SolFormCompraVenta',
        requireclase: 'Phx.vista.FormCompraVentaEXT',
        mostrarFormaPago: false,
        heightHeader: 245,
        constructor: function (config) {
            Phx.vista.SolFormCompraVentaCbte.superclass.constructor.call(this, config);
            console.log('feeeeeeeeeeeeeeee', config);
        },

        extraAtributos: [
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
                //configuracion del componente
                config: {
                    labelSeparator: '',
                    inputType: 'hidden',
                    name: 'desc_clase_comprobante'
                },
                type: 'Field',
                form: true
            },
            {
                //configuracion del componente
                config: {
                    labelSeparator: '',
                    inputType: 'hidden',
                    name: 'id_plan_pago'
                },
                type: 'Field',
                form: true
            }
        ],

        onNew: function () {
            Phx.vista.SolFormCompraVentaCbte.superclass.onNew.call(this);

            this.Cmp.id_plan_pago.setValue(this.data.objPadre.id_plan_pago);
            this.Cmp.id_int_comprobante.setValue(this.data.id_int_comprobante);


            // this.Cmp.id_plan_pago.setValue(this.data.id_plan_pago);
            // this.Cmp.id_int_comprobante.setValue(this.data.id_int_comprobante);
            this.Cmp.desc_clase_comprobante.setValue(this.data.objPadre.desc_clase_comprobante);
            console.log('datos.....', this.data);
        },

        onEdit: function () {
            Phx.vista.SolFormCompraVentaCbte.superclass.onEdit.call(this);
            this.Cmp.id_int_comprobante.setValue(this.data.id_int_comprobante);
            this.Cmp.id_plan_pago.setValue(this.objPadre.getSelected());
            this.cargarPeriodo();
        },

        iniciarEventos: function (config) {

            Phx.vista.SolFormCompraVentaCbte.superclass.iniciarEventos.call(this, config);

            this.Cmp.dia.hide();
            this.Cmp.fecha.setReadOnly(false);
            this.Cmp.fecha_vencimiento.setReadOnly(false);
            this.Cmp.fecha.on('change', this.cargarPeriodo, this);


        },
        preparaMenu:function(tb){
            Phx.vista.SolFormCompraVenta.superclass.preparaMenu.call(this,tb);
            console.log('this',this.getBoton('edit'));
            var data = this.getSelectedData();
            if(this.Cmp.id_plantilla.getValue()==  42 ||  this.Cmp.id_plantilla.getValue()==  41){
                this.getBoton('edit').disable();
                //this.getBoton('del').disable();
            }
            else{
                this.getBoton('edit').enable();
                //this.getBoton('del').enable();

            }
            //this.Cmp.id_plantilla.getValue();
            //console.log('plantilla ', this.data['id_plantilla']);

        },

        cargarPeriodo: function (obj) {
            //Busca en la base de datos la razon social en funci�n del NIT digitado. Si Razon social no esta vac�o, entonces no hace nada
            if (this.getComponente('fecha').getValue() != '') {
                Phx.CP.loadingShow();
                Ext.Ajax.request({
                    url: '../../sis_parametros/control/Periodo/listarPeriodo',
                    params: {start: 0, limit: 30, 'fecha': this.getComponente('fecha').getValue().format('d-m-Y')},
                    success: function (resp) {
                        Phx.CP.loadingHide();
                        var objRes = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
                        var idGestion = objRes.datos[0].id_gestion;
                        this.Cmp.id_gestion.setValue(idGestion);
                        this.Cmp.dia.setValue(this.getComponente('fecha').getValue().getDate());
                    },
                    failure: this.conexionFailure,
                    timeout: this.timeout,
                    scope: this
                });
            }
        },
        successSave: function (resp) {
            Phx.CP.loadingHide();
            Phx.CP.getPagina(this.idContenedorPadre).reload();


            this.panel.close();
        },
    };
</script>
