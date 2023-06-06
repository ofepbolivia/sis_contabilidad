<?php
//fRnk: adicionado para mostrar la lista de auxiliares en comprobante diario
header("content-type: text/javascript; charset=UTF-8");
?>
<script>
    Phx.vista.GridAuxiliares=Ext.extend(Phx.gridInterfaz,{

        constructor:function(config){
            this.maestro=config.maestro;
            //llama al constructor de la clase padre
            Phx.vista.GridAuxiliares.superclass.constructor.call(this,config);
            this.init();
            this.load({params:{start:0, limit:50}});
            this.iniciarEventos();
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
                    name: 'codigo_auxiliar',
                    fieldLabel: 'Codigo Auxiliar',
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
                    name: 'nombre_auxiliar',
                    fieldLabel: 'Nombre Auxiliar',
                    allowBlank: true,
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
                    emptyText:'Tipo...',
                    typeAhead: true,
                    triggerAction: 'all',
                    lazyRender:true,
                    mode: 'local',
                    //readOnly: true,
                    gwidth: 100,
                    store:['si','no']
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
                    emptyText:'Tipo...',
                    typeAhead: true,
                    triggerAction: 'all',
                    lazyRender:true,
                    mode: 'local',
                    gwidth: 100,
                    store:['Agencia No IATA', 'Corporativo','Carga', 'Grupo', 'Intercambio de Servicios']
                },
                type:'ComboBox',
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
                    gwidth: 100,
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

        title:'Auxiliares de Cuenta',
        ActList:'../../sis_contabilidad/control/Auxiliar/listarAuxiliar',
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
            {name:'usr_mod', type: 'string'},'corriente','tipo'

        ],
        sortInfo:{
            field: 'id_auxiliar',
            direction: 'ASC'
        },
        bnew:false,
        bedit:false,
        bdel:false,
        bsave:false,

        iniciarEventos: function () {
            //this.ocultarComponente(this.Cmp.corriente);
        }

    })
</script>
