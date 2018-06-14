<?php
/**
*@package pXP
*@file gen-RevisarComisionistas.php
*@author  (miguel.mamani)
*@date 28-12-2017 21:31:21
*@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
*/

header("content-type: text/javascript; charset=UTF-8");
?>
<script>

Phx.vista.RevisarComisionistas=Ext.extend(Phx.gridInterfaz, {
        constructor: function (config) {
            this.maestro = config;
            this.idContenedor = config.idContenedor;

            console.log(  this.periodo);
            //llama al constructor de la clase padre
            Phx.vista.RevisarComisionistas.superclass.constructor.call(this, config);
            this.grid.addListener('cellclick', this.oncellclick,this);
            this.init();
            this.store.baseParams = {
                id_periodo: this.maestro.data.id_periodo,
                id_depto_conta: this.maestro.data.id_depto_conta
            };
            this.load({params: {start: 0, limit: this.tam_pag}});
            this.addButton('reporte',{
                text: 'Reporte de inconsistencias',
                iconCls: 'bpdf32',
                disabled: false,
                handler: this.reporteGeneral,
                tooltip: '<b>Reporte de inconsistencias</b>',
                scope:this
            });

        },

        Atributos: [
            {
                //configuracion del componente
                config: {
                    labelSeparator: '',
                    inputType: 'hidden',
                    name: 'id_comisionista_rev'
                },
                type: 'Field',
                form: true
            },
            {
                //configuracion del componente
                config: {
                    labelSeparator: '',
                    inputType: 'hidden',
                    name: 'id_periodo'
                },
                type: 'Field',
                form: true
            }, {
                //configuracion del componente
                config: {
                    labelSeparator: '',
                    inputType: 'hidden',
                    name: 'id_gestion'
                },
                type: 'Field',
                form: true
            },
            {
                config: {
                    name: 'revisado',
                    fieldLabel: 'Revisado',
                    allowBlank: true,
                    anchor: '50%',
                    gwidth: 80,
                    maxLength: 3,
                    renderer: function (value) {
                        //check or un check row
                        var checked = '',
                            momento = 'no';
                        if (value == 'si') {
                            checked = 'checked';
                            ;
                        }
                        return String.format('<div style="vertical-align:middle;text-align:center;"><input style="height:35px;width:35px;" type="checkbox"  {0}></div>', checked);

                    }
                },
                type: 'TextField',
                id_grupo: 0,
                grid: true,
                form: false
            },
            {
                config: {
                    name: 'id_agencia',
                    fieldLabel: 'Agencia',
                    allowBlank: true,
                    emptyText: 'Agencia...',
                    store: new Ext.data.JsonStore({
                        url: '../../sis_obingresos/control/Agencia/listarAgencia',
                        id: 'id_agencia',
                        root: 'datos',
                        sortInfo: {
                            field: 'nombre',
                            direction: 'ASC'
                        },
                        totalProperty: 'total',
                        fields: ['id_agencia', 'nombre', 'codigo_int','tipo_agencia','codigo'],
                        remoteSort: true,
                        baseParams: {par_filtro: 'age.nombre',comision :'si'}
                    }),
                    valueField: 'id_agencia',
                    displayField: 'nombre',
                    gdisplayField: 'id_agencia',
                    hiddenName: 'id_agencia',
                    anchor: '70%',
                    tpl:'<tpl for="."><div class="x-combo-list-item"><p><b>{nombre}</b></p><b><p>Codigo:<font color="green">{codigo_int}</font></b></p></div></tpl>',
                    forceSelection: true,
                    typeAhead: false,
                    triggerAction: 'all',
                    lazyRender: true,
                    mode: 'remote',
                    pageSize: 15,
                    queryDelay: 1000,
                    gwidth: 150,
                    listWidth:350,
                    resizable:true,
                    minChars: 2
                },
                type: 'ComboBox',
                grid: false,
                form: true
            },
            {
                config: {
                    name: 'nombre_agencia',
                    fieldLabel: 'Nombre Agencia',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 200
                },
                type: 'TextField',
                filters: {pfiltro: 'rca.nombre_agencia', type: 'string'},
                id_grupo: 1,
                grid: true,
                form: false,
                bottom_filter: true
            },
            {
                config: {
                    name: 'nit_comisionista',
                    fieldLabel: 'Nit',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 200
                },
                type: 'TextField',
                filters: {pfiltro: 'rca.nit_comisionista', type: 'string'},
                id_grupo: 1,
                grid: true,
                form: true,
                egrid:true,
                bottom_filter: true
            },
            {
                config: {
                    name: 'nro_contrato',
                    fieldLabel: 'Nro. Contrato',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 200
                },
                type: 'TextField',
                filters: {pfiltro: 'rca.nro_contrato', type: 'string'},
                id_grupo: 1,
                grid: true,
                form: true,
                egrid:true,
                bottom_filter: true
            },

            {
                config: {
                    name: 'precio_unitario',
                    fieldLabel: 'Precio Unitario',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    galign: 'right ',
                    renderer: function (value, p, record) {
                        return String.format('<b><font size=2 >{0}</font><b>', Ext.util.Format.number(value, '0,000.00'));

                    }
                },
                type: 'NumberField',
                filters: {pfiltro: 'rca.precio_unitario', type: 'numeric'},
                id_grupo: 1,
                grid: true,
                form: false
            },
            {
                config: {
                    name: 'monto_total',
                    fieldLabel: 'Monto Total',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    galign: 'right ',
                    renderer: function (value, p, record) {
                        return String.format('<b><font size=2 >{0}</font><b>', Ext.util.Format.number(value, '0,000.00'));

                    }
                },
                type: 'NumberField',
                filters: {pfiltro: 'rca.monto_total', type: 'numeric'},
                id_grupo: 1,
                grid: true,
                form: false
            },

            {
                config: {
                    name: 'monto_total_comision',
                    fieldLabel: 'Total Comision',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    galign: 'right ',
                    renderer: function (value, p, record) {
                        return String.format('<b><font size=2 >{0}</font><b>', Ext.util.Format.number(value, '0,000.00'));

                    }
                },
                type: 'NumberField',
                filters: {pfiltro: 'rca.monto_total_comision', type: 'numeric'},
                id_grupo: 1,
                grid: true,
                form: false
            },
            {
                config: {
                    name: 'estado_reg',
                    fieldLabel: 'Estado Reg.',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength: 10
                },
                type: 'TextField',
                filters: {pfiltro: 'rca.estado_reg', type: 'string'},
                id_grupo: 1,
                grid: true,
                form: false
            },
            {
                config: {
                    name: 'id_usuario_ai',
                    fieldLabel: '',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength: 4
                },
                type: 'Field',
                filters: {pfiltro: 'rca.id_usuario_ai', type: 'numeric'},
                id_grupo: 1,
                grid: false,
                form: false
            },
            {
                config: {
                    name: 'fecha_reg',
                    fieldLabel: 'Fecha creación',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    format: 'd/m/Y',
                    renderer: function (value, p, record) {
                        return value ? value.dateFormat('d/m/Y H:i:s') : ''
                    }
                },
                type: 'DateField',
                filters: {pfiltro: 'rca.fecha_reg', type: 'date'},
                id_grupo: 1,
                grid: true,
                form: false
            },
            {
                config: {
                    name: 'usuario_ai',
                    fieldLabel: 'Funcionaro AI',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength: 300
                },
                type: 'TextField',
                filters: {pfiltro: 'rca.usuario_ai', type: 'string'},
                id_grupo: 1,
                grid: true,
                form: false
            },
            {
                config: {
                    name: 'usr_reg',
                    fieldLabel: 'Creado por',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength: 4
                },
                type: 'Field',
                filters: {pfiltro: 'usu1.cuenta', type: 'string'},
                id_grupo: 1,
                grid: true,
                form: false
            },
            {
                config: {
                    name: 'usr_mod',
                    fieldLabel: 'Modificado por',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength: 4
                },
                type: 'Field',
                filters: {pfiltro: 'usu2.cuenta', type: 'string'},
                id_grupo: 1,
                grid: true,
                form: false
            },
            {
                config: {
                    name: 'fecha_mod',
                    fieldLabel: 'Fecha Modif.',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    format: 'd/m/Y',
                    renderer: function (value, p, record) {
                        return value ? value.dateFormat('d/m/Y H:i:s') : ''
                    }
                },
                type: 'DateField',
                filters: {pfiltro: 'rca.fecha_mod', type: 'date'},
                id_grupo: 1,
                grid: true,
                form: false
            }
        ],
        tam_pag: 50,
        title: 'Revisar Comisionista ',
        ActList: '../../sis_contabilidad/control/Comisionistas/listarRevisarComisionistas',
        ActSave:'../../sis_contabilidad/control/Comisionistas/insertarRevisarComisionistas',
        id_store: 'id_comisionista_rev',
        fields: [
            {name: 'id_comisionista_rev', type: 'numeric'},
            {name: 'nit_comisionista', type: 'string'},
            {name: 'nro_contrato', type: 'string'},
            {name: 'nombre_agencia', type: 'string'},
            {name: 'precio_unitario', type: 'numeric'},
            {name: 'id_periodo', type: 'numeric'},
            {name: 'monto_total', type: 'numeric'},
            {name: 'estado_reg', type: 'string'},
            {name: 'monto_total_comision', type: 'numeric'},
            {name: 'revisado', type: 'string'},
            {name: 'id_depto_conta', type: 'numeric'},
            {name: 'id_usuario_ai', type: 'numeric'},
            {name: 'fecha_reg', type: 'date', dateFormat: 'Y-m-d H:i:s.u'},
            {name: 'usuario_ai', type: 'string'},
            {name: 'id_usuario_reg', type: 'numeric'},
            {name: 'id_usuario_mod', type: 'numeric'},
            {name: 'fecha_mod', type: 'date', dateFormat: 'Y-m-d H:i:s.u'},
            {name: 'usr_reg', type: 'string'},
            {name: 'usr_mod', type: 'string'}

        ],
        sortInfo: {
            field: 'id_comisionista_rev',
            direction: 'ASC'
        },
        bdel: false,
        bsave: true,
        bedit: true,
        bnew: true,

        oncellclick : function(grid, rowIndex, columnIndex, e) {
            var record = this.store.getAt(rowIndex),
                fieldName = grid.getColumnModel().getDataIndex(columnIndex); // Get field name

            if(fieldName == 'revisado') {
                this.cambiarRevision(record);
            }
        },
        cambiarRevision: function(record){
            Phx.CP.loadingShow();
            var d = record.data;
            Ext.Ajax.request({
                url:'../../sis_contabilidad/control/Comisionistas/cambiarRevisionCat',
                params:{ id_comisionista_rev: d.id_comisionista_rev,
                    revisado: d.revisado
                },
                success: this.successRevision,
                failure: this.conexionFailure,
                timeout: this.timeout,
                scope: this
            });
            this.reload();
        },
        successRevision: function(resp){
            Phx.CP.loadingHide();
            var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
            Phx.CP.getPagina(this.idContenedorPadre).reload();
        },
        reporteGeneral : function(){
            var id_periodo = this.maestro.data.id_periodo;
            Phx.CP.loadingShow();
            Ext.Ajax.request({
                url:'../../sis_contabilidad/control/Comisionistas/reporteValidar',
                params:{'id_periodo':id_periodo},
                success: this.successExport,
                failure: this.conexionFailure,
                timeout:this.timeout,
                scope:this
            });
        },
    successSave:function(resp){
        Phx.vista.RevisarComisionistas.superclass.successSave.call(this,resp);
        Phx.CP.getPagina(this.idContenedorPadre).reload();
    },
    successEdit:function(resp){
        Phx.vista.RevisarComisionistas.superclass.successEdit.call(this,resp);
        Phx.CP.getPagina(this.idContenedorPadre).reload();
    },
    onButtonNew:function(){
        Phx.vista.RevisarComisionistas.superclass.onButtonNew.call(this);

         this.getComponente('id_periodo').setValue(this.maestro.data.id_periodo);
        this.ocultarComponente(this.Cmp.nit_comisionista);
        this.ocultarComponente(this.Cmp.nro_contrato);
    },
    onButtonEdit:function(){
        Phx.vista.RevisarComisionistas.superclass.onButtonEdit.call(this);
        this.ocultarComponente(this.Cmp.id_agencia);
    }
    }
)
</script>
		
		