<?php
/**
*@package pXP
*@file gen-PlanCuentaDet.php
*@author  (alan.felipez)
*@date 25-11-2019 22:17:20
*@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
*/

header("content-type: text/javascript; charset=UTF-8");
?>
<script>
Phx.vista.PlanCuentaDet=Ext.extend(Phx.gridInterfaz,{

	constructor:function(config){
		this.maestro=config.maestro;
		console.log('config_cuenta_det',this.maestro);
    	//llama al constructor de la clase padre
		Phx.vista.PlanCuentaDet.superclass.constructor.call(this,config);
		this.init();
		//this.load({params:{start:0, limit:this.tam_pag}})
	},
    onReloadPage: function(m){
        this.maestro=m;
        console.log('plan_cuenta',this.maestro.id_plan_cuenta);
        this.Cmp.id_plan_cuenta.store.baseParams.id_plan_cuenta=this.maestro.id_plan_cuenta;
        this.store.baseParams={id_plan_cuenta:this.maestro.id_plan_cuenta};
        this.load({params:{start:0, limit:50}});
        this.bloquearMenus();
    },
    loadValoresIniciales: function(){
        Phx.vista.PlanCuentaDet.superclass.loadValoresIniciales.call(this);
        console.log('plan_cuenta',this.maestro.id_plan_cuenta);
        this.Cmp.id_plan_cuenta.setValue(this.maestro.id_plan_cuenta);


    },
	Atributos:[
		{
			//configuracion del componente
			config:{
					labelSeparator:'',
					inputType:'hidden',
					name: 'id_plan_cuenta_det'
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
				filters:{pfiltro:'ipcd.estado_reg',type:'string'},
				id_grupo:1,
				grid:false,
				form:false
		},
		{
			config: {
				name: 'id_plan_cuenta',
				fieldLabel: 'Plan de Cuenta',
				allowBlank: true,
				emptyText: 'Plan de cuenta...',
				store: new Ext.data.JsonStore({
					url: '../../sis_contabilidad/control/PlanCuenta/listarPlanCuenta',
					id: 'id_plan_cuenta',
					root: 'datos',
					sortInfo: {
						field: 'id_plan_cuenta',
						direction: 'ASC'
					},
					totalProperty: 'total',
					fields: ['id_plan_cuenta', 'nombre', 'codigo'],
					remoteSort: true,
					baseParams: {par_filtro: 'ipcd.id_plan_cuenta#ipcd.nombre'}
				}),
				valueField: 'id_plan_cuenta',
				displayField: 'nombre',
				gdisplayField: 'id_plan_cuenta',
				hiddenName: 'id_plan_cuenta',
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
					return String.format('{0}', record.data['id_plan_cuenta']);
				}
			},
			type: 'ComboBox',
			id_grupo: 0,
			filters: {pfiltro: 'ipcd.nombre',type: 'string'},
			grid: false,
			form: true
		},
        {
            config:{
                name: 'numero',
                fieldLabel: 'Número',
                allowBlank: true,
                anchor: '80%',
                gwidth: 100,
                maxLength:50
            },
            type:'NumberField',
            filters:{pfiltro:'ipcd.numero',type:'numeric'},
            id_grupo:1,
            grid:true,
            form:true
        },
		{
			config:{
				name: 'nivel',
				fieldLabel: 'Nivel',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:50
			},
				type:'TextField',
				filters:{pfiltro:'ipcd.nivel',type:'string'},
				id_grupo:1,
				grid:true,
				form:true
		},
		{
			config:{
				name: 'rubro',
				fieldLabel: 'Rubro',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:50
			},
				type:'TextField',
				filters:{pfiltro:'ipcd.rubro',type:'string'},
				id_grupo:1,
				grid:true,
				form:true
		},
		{
			config:{
				name: 'grupo',
				fieldLabel: 'Grupo',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:50
			},
				type:'TextField',
				filters:{pfiltro:'ipcd.grupo',type:'string'},
				id_grupo:1,
				grid:true,
				form:true
		},
		{
			config:{
				name: 'sub_grupo',
				fieldLabel: 'Sub Grupo',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:20
			},
				type:'TextField',
				filters:{pfiltro:'ipcd.sub_grupo',type:'string'},
				id_grupo:1,
				grid:true,
				form:true
		},
		{
			config:{
				name: 'cuenta',
				fieldLabel: 'Cuenta',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:50
			},
				type:'TextField',
				filters:{pfiltro:'ipcd.cuenta',type:'string'},
				id_grupo:1,
				grid:true,
				form:true
		},
		{
			config:{
				name: 'codigo_cuenta',
				fieldLabel: 'Codigo Cuenta',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:50
			},
				type:'TextField',
				filters:{pfiltro:'ipcd.codigo_cuenta',type:'string'},
				id_grupo:1,
				grid:true,
				form:true
		},
		{
			config:{
				name: 'sub_cuenta',
				fieldLabel: 'Sub Cuenta',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:50
			},
				type:'TextField',
				filters:{pfiltro:'ipcd.sub_cuenta',type:'string'},
				id_grupo:1,
				grid:true,
				form:true
		},
        {
            config:{
                name: 'sub_sub_cuenta',
                fieldLabel: 'Sub-Sub Cuenta',
                allowBlank: true,
                anchor: '80%',
                gwidth: 100,
                maxLength:50
            },
            type:'TextField',
            filters:{pfiltro:'ipcd.sub_sub_cuenta',type:'string'},
            id_grupo:1,
            grid:true,
            form:true
        },
        {
            config:{
                name: 'sub_sub_sub_cuenta',
                fieldLabel: 'Sub-Sub-Sub Cuenta',
                allowBlank: true,
                anchor: '80%',
                gwidth: 100,
                maxLength:50
            },
            type:'TextField',
            filters:{pfiltro:'ipcd.sub_sub_sub_cuenta',type:'string'},
            id_grupo:1,
            grid:true,
            form:true
        },
		{
			config:{
				name: 'auxiliar',
				fieldLabel: 'Auxiliar',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:50
			},
				type:'TextField',
				filters:{pfiltro:'ipcd.auxiliar',type:'string'},
				id_grupo:1,
				grid:true,
				form:true
		},
		{
			config:{
				name: 'nombre_cuenta',
				fieldLabel: 'Nombre Cuenta',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:150
			},
				type:'TextField',
				filters:{pfiltro:'ipcd.nombre_cuenta',type:'string'},
				id_grupo:1,
				grid:true,
				form:true
		},
		{
			config:{
				name: 'ajuste',
				fieldLabel: 'Ajuste',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:20
			},
				type:'TextField',
				filters:{pfiltro:'ipcd.ajuste',type:'string'},
				id_grupo:1,
				grid:true,
				form:true
		},
		{
			config:{
				name: 'moneda_ajuste',
				fieldLabel: 'Moneda Ajuste',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:20
			},
				type:'TextField',
				filters:{pfiltro:'ipcd.moneda_ajuste',type:'string'},
				id_grupo:1,
				grid:true,
				form:true
		},
		{
			config:{
				name: 'tipo_cuenta',
				fieldLabel: 'Tipo de Cuenta',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:50
			},
				type:'TextField',
				filters:{pfiltro:'ipcd.tipo_cuenta',type:'string'},
				id_grupo:1,
				grid:true,
				form:true
		},
		{
			config:{
				name: 'moneda',
				fieldLabel: 'Moneda',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:20
			},
				type:'TextField',
				filters:{pfiltro:'ipcd.moneda',type:'string'},
				id_grupo:1,
				grid:true,
				form:true
		},
		{
			config:{
				name: 'tip_cuenta',
				fieldLabel: 'Tipo Cuenta',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:50
			},
				type:'TextField',
				filters:{pfiltro:'ipcd.tip_cuenta',type:'string'},
				id_grupo:1,
				grid:true,
				form:true
		},
		{
			config:{
				name: 'permite_auxiliar',
				fieldLabel: 'Permite Auxiliar?',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:20
			},
				type:'TextField',
				filters:{pfiltro:'ipcd.permite_auxiliar',type:'string'},
				id_grupo:1,
				grid:true,
				form:true
		},
		{
			config:{
				name: 'cuenta_sigep',
				fieldLabel: 'Cuenta Sigep',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:4
			},
				type:'NumberField',
				filters:{pfiltro:'ipcd.cuenta_sigep',type:'numeric'},
				id_grupo:1,
				grid:true,
				form:true
		},
		{
			config:{
				name: 'partida_sigep_debe',
				fieldLabel: 'Partida Sigep Debe',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:150
			},
				type:'TextField',
				filters:{pfiltro:'ipcd.partida_sigep_debe',type:'string'},
				id_grupo:1,
				grid:true,
				form:true
		},
		{
			config:{
				name: 'partida_sigep_haber',
				fieldLabel: 'Partida Sigep Haber',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:200
			},
				type:'TextField',
				filters:{pfiltro:'ipcd.partida_sigep_haber',type:'string'},
				id_grupo:1,
				grid:true,
				form:true
		},
		{
			config:{
				name: 'observaciones',
				fieldLabel: 'Observaciones',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:200
			},
				type:'TextField',
				filters:{pfiltro:'ipcd.observaciones',type:'string'},
				id_grupo:1,
				grid:true,
				form:true
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
				filters:{pfiltro:'ipcd.fecha_reg',type:'date'},
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
				maxLength:4
			},
				type:'Field',
				filters:{pfiltro:'ipcd.id_usuario_ai',type:'numeric'},
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
				filters:{pfiltro:'ipcd.usuario_ai',type:'string'},
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
				maxLength:4
			},
				type:'Field',
				filters:{pfiltro:'usu2.cuenta',type:'string'},
				id_grupo:1,
				grid:false,
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
				filters:{pfiltro:'ipcd.fecha_mod',type:'date'},
				id_grupo:1,
				grid:false,
				form:false
		}
	],
	tam_pag:50,	
	title:'ImportarPlanCuentaDet',
	ActSave:'../../sis_contabilidad/control/PlanCuentaDet/insertarPlanCuentaDet',
	ActDel:'../../sis_contabilidad/control/PlanCuentaDet/eliminarPlanCuentaDet',
	ActList:'../../sis_contabilidad/control/PlanCuentaDet/listarPlanCuentaDet',
	id_store:'id_plan_cuenta_det',
	fields: [
		{name:'id_plan_cuenta_det', type: 'numeric'},
		{name:'estado_reg', type: 'string'},
		{name:'id_plan_cuenta', type: 'numeric'},
        {name:'numero', type: 'numeric'},
		{name:'nivel', type: 'string'},
		{name:'rubro', type: 'string'},
		{name:'grupo', type: 'string'},
		{name:'sub_grupo', type: 'string'},
		{name:'cuenta', type: 'string'},
        {name:'codigo_cuenta', type: 'string'},
		{name:'sub_cuenta', type: 'string'},
        {name:'sub_sub_cuenta', type: 'string'},
        {name:'sub_sub_sub_cuenta', type: 'string'},
		{name:'auxiliar', type: 'string'},
		{name:'nombre_cuenta', type: 'string'},
		{name:'ajuste', type: 'string'},
		{name:'moneda_ajuste', type: 'string'},
		{name:'tipo_cuenta', type: 'string'},
		{name:'moneda', type: 'string'},
		{name:'tip_cuenta', type: 'string'},
		{name:'permite_auxiliar', type: 'string'},
		{name:'cuenta_sigep', type: 'numeric'},
		{name:'partida_sigep_debe', type: 'string'},
		{name:'partida_sigep_haber', type: 'string'},
		{name:'observaciones', type: 'string'},
		{name:'id_usuario_reg', type: 'numeric'},
		{name:'fecha_reg', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'id_usuario_ai', type: 'numeric'},
		{name:'usuario_ai', type: 'string'},
		{name:'id_usuario_mod', type: 'numeric'},
		{name:'fecha_mod', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'usr_reg', type: 'string'},
		{name:'usr_mod', type: 'string'},
		
	],
	sortInfo:{
		field: 'id_plan_cuenta_det',
		direction: 'ASC'
	},
	bdel:true,
	bsave:false,
    bnew:false,
    bedit:false

	}
)
</script>
		
		