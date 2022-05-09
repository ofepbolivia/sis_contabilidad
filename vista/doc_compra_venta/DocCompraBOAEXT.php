<?php
/**
 *@package pXP
 *@file gen-SistemaDist.php
 *@author  Maylee Perez Pastor
 *@date 26-02-2020 10:22:05
 *@description Archivo con la interfaz de documentos de compra
 *
 */
header("content-type: text/javascript; charset=UTF-8");
?>
<script>
    Phx.vista.DocCompraBOAEXT = {

        require: '../../../sis_contabilidad/vista/doc_compra_venta/DocCompraVentaEXT.php',
        //ActList:'../../sis_contabilidad/control/DocCompraVenta/listarDocCompraCajero',
        requireclase: 'Phx.vista.DocCompraVentaEXT',
        title: 'Libro de Compras',
        nombreVista: 'DocCompra',
        tipoDoc: 'compra',
        formTitulo: 'Formulario de Documento Compra',

        constructor: function(config) {
            Phx.vista.DocCompraBOAEXT.superclass.constructor.call(this,config);
        },
        modificarAtributos: function(){
            this.Atributos[this.getIndAtributo('estacion')].grid=true;
            this.Atributos[this.getIndAtributo('codigo_noiata')].grid=true;
            this.Atributos[this.getIndAtributo('nombre')].grid=true;

        },

        loadValoresIniciales: function() {
            Phx.vista.DocCompraBOAEXT.superclass.loadValoresIniciales.call(this);


        },
        capturaFiltros:function(combo, record, index){
            this.store.baseParams.tipo = this.tipoDoc;
            this.store.baseParams.nombreVista = 'DocCompra';
            Phx.vista.DocCompraBOAEXT.superclass.capturaFiltros.call(this,combo, record, index);
        },

        south:{
            url: '../../../sis_contabilidad/vista/historial_reg_compras/HistorialRegCompras.php',
            title: 'Historial Validación Compras',
            width: 400,
            height:'40%',
            collapsed:true,
            cls: 'HistorialRegCompras'
        },




    };
</script>
