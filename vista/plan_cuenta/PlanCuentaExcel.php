<?php
/**
 *@package pXP
 *@file    SubirArchivo.php
 *@author  Alan Felipez
 *@date    22-03-2012
 *@description permite subir archivos csv con el detalle de planes decuenta en la tabla de conta.tplan_cuenta_det
 */
header("content-type: text/javascript; charset=UTF-8");
?>
<script>
    Phx.vista.ConsumoCuentaDet=Ext.extend(Phx.frmInterfaz,{

            constructor:function(config)
            {
                Phx.vista.ConsumoCuentaDet.superclass.constructor.call(this,config);
                console.log('config',config);
                this.init();
                this.loadValoresIniciales();
            },

            loadValoresIniciales:function()
            {
                Phx.vista.ConsumoCuentaDet.superclass.loadValoresIniciales.call(this);
                this.getComponente('id_plan_cuenta').setValue(this.id_plan_cuenta);
            },

            successSave:function(resp)
            {
                Phx.CP.loadingHide();
                Phx.CP.getPagina(this.idContenedorPadre).reload();
                this.panel.close();
            },


            Atributos:[
                {
                    config:{
                        labelSeparator:'',
                        inputType:'hidden',
                        name: 'id_plan_cuenta'

                    },
                    type:'Field',
                    form:true

                },
                {
                    config:{
                        name:'codigo',
                        fieldLabel:'Codigo Archivo',
                        allowBlank:false,
                        emptyText:'Codigo Archivo...',
                        store: new Ext.data.JsonStore({
                            url: '../../sis_contabilidad/control/PlanCuenta/listarPlantillaArchivoExcel',
                            id: 'id_plantilla_archivo_excel',
                            root: 'datos',
                            sortInfo:{
                                field: 'codigo',
                                direction: 'ASC'
                            },
                            totalProperty: 'total',
                            fields: ['id_plantilla_archivo_excel','nombre','codigo'],
                            //turn on remote sorting
                            remoteSort: true,
                            baseParams:{par_filtro:'codigo', vista:'vista', archivoAcm: 'EXTPC'}
                        }),
                        valueField: 'codigo',
                        displayField: 'codigo',
                        hiddenName: 'codigo',
                        forceSelection:true,
                        typeAhead: false,
                        triggerAction: 'all',
                        lazyRender:true,
                        mode:'remote',
                        pageSize:10,
                        queryDelay:1000,
                        listWidth:260,
                        resizable:true,
                        anchor:'90%',
                        tpl: new Ext.XTemplate([
                            '<tpl for=".">',
                            '<div class="x-combo-list-item">',
                            '<p><b>Nombre:</b> <span style="color: blue; font-weight: bold;">{nombre}</span></p>',
                            '<p><b>Codigo:</b> <span style="color: green; font-weight: bold;">{codigo}</span></p>',
                            '</div></tpl>'
                        ])
                    },
                    type:'ComboBox',
                    id_grupo:0,
                    grid:true,
                    form:true
                },
                {
                    config:{
                        fieldLabel: "Documento",
                        gwidth: 130,
                        inputType:'file',
                        name: 'archivo',
                        buttonText: '',
                        maxLength:150,
                        anchor:'100%'
                    },
                    type:'Field',
                    form:true
                }
            ],
            title:'Subir Archivo',
            fileUpload:true,
            ActSave:'../../sis_contabilidad/control/PlanCuenta/cargarArchivoExcel'
        }
    )
</script>