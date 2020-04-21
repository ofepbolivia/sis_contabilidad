<?php
/**
*@package pXP
*@file gen-PlanCuenta.php
*@author  (alan.felipez)
*@date 25-11-2019 22:15:53
*@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
*/

header("content-type: text/javascript; charset=UTF-8");
?>
<script>
Phx.vista.PlanCuenta=Ext.extend(Phx.gridInterfaz,{

	constructor:function(config){
		this.maestro=config.maestro;
    	//llama al constructor de la clase padre
		Phx.vista.PlanCuenta.superclass.constructor.call(this,config);
		this.init();

		this.load({params:{start:0, limit:this.tam_pag}})
        this.addButton('btnsubir_archivo',
            {
                //grupo: [0],
                text: 'Cargar Archivo',
                iconCls: 'blist',
                disabled: false,
                handler: this.onButtonUpload,
                tooltip: '<b>Cargar Archivo</b><br/>Carga un Archivo del tipo Excel.'
            }
        );
		this.addButton('btngenerar_plan_cuenta',
            {
                text:'Generar Plan de Cuentas',
                iconCls: 'bchecklist',
                disabled: true,
                handler: this.onButtonCargar,
                tooltip: '<b>Generar Plan de Cuentas</b><br/>Genera un plan de cuentas a partir del detalle de plan de cuentas.'
            }
        );
        this.addButton('btnregresar_estado',
            {
                text:'Revertir Estado',
                iconCls: 'batras',
                disabled: true,
                handler: this.onButtonRevertir,
                tooltip: '<b>Revertir Carga de Archivo</b><br/>Revierte la carga de un archivo Excel'
            }
        );
	},
    onButtonUpload: function () {
        var rec=this.sm.getSelected();
        Phx.CP.loadWindows('../../../sis_contabilidad/vista/plan_cuenta/PlanCuentaExcel.php',
            'Subir Archivo Plan Cuenta Excel',
            {
                modal:true,
                width:450,
                height:200
            },rec.data,this.idContenedor,'ConsumoCuentaDet')
    },
    successAnular:function(resp){
        Phx.CP.loadingHide();
        var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
        if(!reg.ROOT.error){
            this.reload();
        }
    },
    onButtonCargar: function () {
        if(confirm('¿Está seguro de generar el plan de Cuentas? ')){
            Phx.CP.loadingShow();
            var rec = this.sm.getSelected();
            console.log('datos_plan_cuenta',rec);

            Ext.Ajax.request({
                url: '../../sis_contabilidad/control/PlanCuentaDet/generarPlanCuenta',
                params: {
                    id_plan_cuenta: rec.data.id_plan_cuenta
                },
                success: this.successAnular,
                failure: this.conexionFailure,
                timeout: this.timeout,
                scope: this
            });

        }
    },
    onButtonRevertir: function () {
        Phx.CP.loadingShow();
	    var rec = this.sm.getSelected();
            //console.log('datos_plan_cuenta',rec);

            Ext.Ajax.request({
                url: '../../sis_contabilidad/control/PlanCuenta/revertirCargaArchivoExcel',
                params: {
                    id_plan_cuenta: rec.data.id_plan_cuenta
                },
                success: this.successAnular,
                failure: this.conexionFailure,
                timeout: this.timeout,
                scope: this
            });

    },
      preparaMenu: function () {
          Phx.vista.PlanCuenta.superclass.preparaMenu.call(this);
         var rec = this.sm.getSelected();
         console.log('estado',rec.data.estado);
         if(rec !== '') {
             if(rec.data.estado == 'borrador'){
                 this.getBoton('btnsubir_archivo').enable();
             }
            if(rec.data.estado == 'registrado'){
                this.getBoton('btngenerar_plan_cuenta').enable();
                this.getBoton('btnsubir_archivo').enable();//solo para prueba hay que eliminar despues esta linea
                this.getBoton('btnregresar_estado').enable();
            }
             if(rec.data.estado == 'finalizado'){
                 this.getBoton('btnregresar_estado').enable();
                 this.getBoton('btnsubir_archivo').enable();//solo para prueba hay que eliminar despues esta linea
             }
         }
     },
    liberaMenu : function(){
        Phx.vista.PlanCuenta.superclass.liberaMenu.call(this);
	    var rec = this.sm.getSelected.data;
         this.getBoton('btnsubir_archivo').disable();
         this.getBoton('btngenerar_plan_cuenta').disable();
        this.getBoton('btnregresar_estado').disable();
     },
	Atributos:[
		{
			//configuracion del componente
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
				name: 'estado_reg',
				fieldLabel: 'Estado Reg.',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:10
			},
				type:'TextField',
				filters:{pfiltro:'ipc.estado_reg',type:'string'},
				id_grupo:1,
				grid:false,
				form:false
		},
		{
			config:{
				name: 'nombre',
				fieldLabel: 'Nombre',
				allowBlank: true,
				anchor: '100%',
				gwidth: 150,
				maxLength:300
			},
				type:'TextField',
				filters:{pfiltro:'ipc.nombre',type:'string'},
				id_grupo:1,
				grid:true,
				form:true
		},


        {
            config: {
                name: 'id_gestion',
                fieldLabel: 'Gestion Destino',
                allowBlank: true,
                emptyText: 'Gestion',
                store: new Ext.data.JsonStore({
                    url: '../../sis_parametros/control/Gestion/listarGestion',
                    id: 'id_gestion',
                    root: 'datos',
                    sortInfo: {
                        field: 'gestion',
                        direction: 'ASC'
                    },
                    totalProperty: 'total',
                    fields: ['id_gestion', 'gestion'],
                    remoteSort: true,
                    baseParams: {par_filtro: 'ipc.id_gestion'}
                }),
                valueField: 'id_gestion',
                displayField: 'gestion',
                gdisplayField: 'desc_gestion',
                hiddenName: 'id_gestion',
                forceSelection: true,
                typeAhead: false,
                triggerAction: 'all',
                lazyRender: true,
                mode: 'remote',
                pageSize: 15,
                queryDelay: 1000,
                anchor: '100%',
                gwidth: 150,
                minChars: 2,
                renderer : function(value, p, record) {
                    return String.format('{0}', record.data['desc_gestion']);
                }
            },
            type: 'ComboBox',
            id_grupo: 0,
            filters: {pfiltro: 'ipc.id_gestion',type: 'numeric'},
            grid: true,
            form: true
        },



        {
			config:{
				name: 'estado',
				fieldLabel: 'estado',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:100
			},
				type:'TextField',
				filters:{pfiltro:'ipc.estado',type:'string'},
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
				maxLength:20
			},
				type:'Field',
				filters:{pfiltro:'usu1.cuenta',type:'string'},
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
				filters:{pfiltro:'ipc.fecha_reg',type:'date'},
				id_grupo:1,
				grid:true,
				form:false
		},
		{
			config:{
				name: 'id_usuario_ai',
				fieldLabel: 'Fecha creación',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:20
			},
				type:'Field',
				filters:{pfiltro:'ipc.id_usuario_ai',type:'numeric'},
				id_grupo:1,
				grid:false,
				form:false
		},
		{
			config:{
				name: 'usuario_ai',
				fieldLabel: 'Funcionaro AI',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:300
			},
				type:'TextField',
				filters:{pfiltro:'ipc.usuario_ai',type:'string'},
				id_grupo:1,
				grid:false,
				form:false
		},
		{
			config:{
				name: 'usr_mod',
				fieldLabel: 'Modificado por',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:20
			},
				type:'Field',
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
				filters:{pfiltro:'ipc.fecha_mod',type:'date'},
				id_grupo:1,
				grid:false,
				form:false
		}
	],
	tam_pag:50,	
	title:'ImportarPlanCuenta',
	ActSave:'../../sis_contabilidad/control/PlanCuenta/insertarPlanCuenta',
	ActDel:'../../sis_contabilidad/control/PlanCuenta/eliminarPlanCuenta',
	ActList:'../../sis_contabilidad/control/PlanCuenta/listarPlanCuenta',
	id_store:'id_plan_cuenta',
	fields: [
		{name:'id_plan_cuenta', type: 'numeric'},
		{name:'estado_reg', type: 'string'},
		{name:'nombre', type: 'string'},
		{name:'estado', type: 'string'},
		{name:'id_usuario_reg', type: 'numeric'},
		{name:'fecha_reg', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'id_usuario_ai', type: 'numeric'},
		{name:'usuario_ai', type: 'string'},
		{name:'id_usuario_mod', type: 'numeric'},
		{name:'fecha_mod', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
        {name:'id_gestion', type:'numeric'},
		{name:'usr_reg', type: 'string'},
		{name:'usr_mod', type: 'string'},
        {name:'desc_gestion', type:'numeric'},
		
	],
	sortInfo:{
		field: 'id_plan_cuenta',
		direction: 'ASC'
	},
	bdel:true,
    bsave:false,
    btest:false,

    tabsouth:[
        {
            url:'../../../sis_contabilidad/vista/plan_cuenta_det/PlanCuentaDet.php',
            title:'Detalle Plan de Cuentas',
            height: '50%',
            cls:'PlanCuentaDet'
        }
    ]

	}
)
</script>
		
		