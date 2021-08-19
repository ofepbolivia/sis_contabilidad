<?php
/**
 *@package pXP
 *@file AuxiliarGrupoRo.php
 *@author  breydi.vasquez
 *@date 08-04-2021
 *@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
 */

header("content-type: text/javascript; charset=UTF-8");
?>
<script>
    Phx.vista.AuxiliarGrupoRo=Ext.extend(Phx.gridInterfaz,{
        nomvista:'auxiliar_cc_grupo_ro',
        constructor:function(config){
            this.maestro=config.maestro;
            //llama al constructor de la clase padre
            Phx.vista.AuxiliarGrupoRo.superclass.constructor.call(this,config);
            this.init();
            this.store.baseParams = {tipo_interfaz:this.nomvista};
            this.load({params:{start:0, limit:50}});
            this.momento = undefined;
        },

        Atributos:[
            {
                //configuracion del componente
                config:{
                    labelSeparator:'',
                    inputType:'hidden',
                    name: 'id_auxiliar'
                },
                type:'Field',
                form:true
            },
            {
              config:{
                inputType:'hidden',
                name:'tipo_interfaz'
              },
              type:'Field',
              form:true
            },
            {
                config:{
                    name: 'codigo_auxiliar',
                    fieldLabel: 'Código Auxiliar',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength:50
                },
                type:'TextField',
                filters:{pfiltro:'auxcta.codigo_auxiliar',type:'string'},
                bottom_filter : true,
                id_grupo:1,
                grid:true,
                form:true
            },
            {
                config:{
                    name: 'cod_antiguo',
                    fieldLabel: 'Código Antiguo',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength:50
                },
                type:'TextField',
                filters:{pfiltro:'auxcta.cod_antiguo',type:'string'},
                bottom_filter : true,
                id_grupo:1,
                grid:true,
                form:true
            },
            {
                config:{
                    name: 'nombre_auxiliar',
                    fieldLabel: 'Nombre Auxiliar',
                    allowBlank: false,
                    anchor: '80%',
                    gwidth: 230,
                    maxLength:300,
                    style:'text-transform:uppercase'
                },
                type:'TextField',
                filters:{pfiltro:'auxcta.nombre_auxiliar',type:'string'},
                bottom_filter : true,
                id_grupo:1,
                grid:true,
                form:true
            },
            {
                config:{
                    name:'corriente',
                    fieldLabel:'Corriente',
                    qtip: '¿Es cuenta corriente?',
                    allowBlank:true,
                    disabled:true,
                    emptyText:'Tipo...',
                    typeAhead: true,
                    triggerAction: 'all',
                    lazyRender:true,
                    mode: 'local',
                    gwidth: 100,
                    store:['si', 'no']
                },
                type:'ComboBox',
                valorInicial: 'no',
                id_grupo:0,
                grid:true,
                form:true
            },
            {
                config:{
                    name:'tipo',
                    fieldLabel:'Tipo',
                    allowBlank:true,
                    disabled:true,
                    emptyText:'Tipo...',
                    typeAhead: true,
                    triggerAction: 'all',
                    lazyRender:true,
                    mode: 'local',
                    gwidth: 100,
                    store:['Grupo']
                },
                type:'ComboBox',
                valorInicial:'Grupo',
                id_grupo:0,
                grid:true,
                form:true
            },
            {
                config:{
                    name: 'estado_reg',
                    fieldLabel: 'Estado Reg.',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength:10
                },
                type:'TextField',
                filters:{pfiltro:'auxcta.estado_reg',type:'string'},
                id_grupo:1,
                grid:true,
                form:false
            },
            {
                config:{
                    name: 'fecha_reg',
                    fieldLabel: 'Fecha creación',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 120,
                    format: 'd/m/Y',
                    renderer:function (value,p,record){return value?value.dateFormat('d/m/Y H:i:s'):''}
                },
                type:'DateField',
                filters:{pfiltro:'auxcta.fecha_reg',type:'date'},
                id_grupo:1,
                grid:true,
                form:false
            },
            {
                config:{
                    name: 'usr_reg',
                    fieldLabel: 'Creado por',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength:4
                },
                type:'NumberField',
                filters:{pfiltro:'usu1.cuenta',type:'string'},
                id_grupo:1,
                grid:true,
                form:false
            },
            {
                config:{
                    name: 'usr_mod',
                    fieldLabel: 'Modificado por',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength:4
                },
                type:'NumberField',
                filters:{pfiltro:'usu2.cuenta',type:'string'},
                id_grupo:1,
                grid:true,
                form:false
            },
            {
                config:{
                    name: 'fecha_mod',
                    fieldLabel: 'Fecha Modif.',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    format: 'd/m/Y',
                    renderer:function (value,p,record){return value?value.dateFormat('d/m/Y H:i:s'):''}
                },
                type:'DateField',
                filters:{pfiltro:'auxcta.fecha_mod',type:'date'},
                id_grupo:1,
                grid:true,
                form:false
            }
        ],

        title:'Auxiliares de Grupos',
        ActSave:'../../sis_contabilidad/control/Auxiliar/insertarAuxCuentaCorriente',
        ActDel:'../../sis_contabilidad/control/Auxiliar/eliminarAuxCuentaCorriente',
        ActList:'../../sis_contabilidad/control/Auxiliar/listarAuxCuentaCorriente',
        id_store:'id_auxiliar',
        fields: [
            {name:'id_auxiliar', type: 'numeric'},
            {name:'id_empresa', type: 'numeric'},
            {name:'nombre', type:'string'},
            {name:'estado_reg', type: 'string'},
            {name:'codigo_auxiliar', type: 'string'},
            {name:'nombre_auxiliar', type: 'string'},
            {name:'fecha_reg', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
            {name:'id_usuario_reg', type: 'numeric'},
            {name:'id_usuario_mod', type: 'numeric'},
            {name:'fecha_mod', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
            {name:'usr_reg', type: 'string'},
            {name:'usr_mod', type: 'string'},'corriente',
            {name:'tipo', type: 'string'},
            {name:'cod_antiguo', type: 'string'}

        ],
        sortInfo:{
            field: 'id_auxiliar',
            direction: 'ASC'
        },
        bdel:true,
        bsave:false,
        onButtonNew : function () {
            Phx.vista.AuxiliarGrupoRo.superclass.onButtonNew.call(this);
            this.momento = true;
            this.Cmp.tipo_interfaz.setValue(this.nomvista);
            this.Cmp.codigo_auxiliar.setVisible(false);
            this.Cmp.cod_antiguo.setVisible(false);
        },

        onButtonEdit : function () {
            Phx.vista.AuxiliarGrupoRo.superclass.onButtonEdit.call(this);
            this.momento = false;
            this.Cmp.tipo_interfaz.setValue(this.nomvista);
            this.Cmp.codigo_auxiliar.setVisible(false);
            this.Cmp.cod_antiguo.setVisible(false);
        },

        onSubmit: function (o,x, force) {

            if(this.momento) {
                Ext.Ajax.request({
                    url: '../../sis_contabilidad/control/Auxiliar/validarAuxiliar',
                    params: {
                        codigo_auxiliar: this.Cmp.codigo_auxiliar.getValue(),
                        nombre_auxiliar: this.Cmp.nombre_auxiliar.getValue(),
                        corriente: this.Cmp.corriente.getValue()
                    },
                    success: function (resp) {
                        var reg = Ext.decode(Ext.util.Format.trim(resp.responseText));
                        if (reg.ROOT.datos.v_valid == 'true')
                            Ext.Msg.alert('Alerta','Estimado usuario la Cuenta Auxiliar con codigo (<b>'+ this.Cmp.codigo_auxiliar.getValue()+'</b>)-nombre <b>'+ this.Cmp.nombre_auxiliar.getValue()+'</b> que intenta crear, ya se encuentra registrado en el sistema ERP. Por esta razon no es posible crearlo.');
                        else
                            Phx.vista.AuxiliarGrupoRo.superclass.onSubmit.call(this, o);

                    },
                    failure: this.conexionFailure,
                    timeout: this.timeout,
                    scope: this
                });
            }else{
                Phx.vista.AuxiliarGrupoRo.superclass.onSubmit.call(this, o);
            }
        }

    })
</script>
