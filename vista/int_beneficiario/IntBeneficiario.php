<?php
/**
*@package pXP
*@file gen-IntBeneficiario.php
*@author  (admin)
*@date 27-10-2022 14:42:32
*@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
*/

header("content-type: text/javascript; charset=UTF-8");
?>
<script>
Phx.vista.IntBeneficiario=Ext.extend(Phx.gridInterfaz,{
	fheight : '30%',
    fwidth : '33%',
	nombreVista: 'IntComprobante',
	constructor:function(config){
		this.maestro=config.maestro;
    	//llama al constructor de la clase padre
		Phx.vista.IntBeneficiario.superclass.constructor.call(this,config);
		this.init();
		if(Phx.CP.getPagina(this.idContenedorPadre)) {
			var dataMaestro=Phx.CP.getPagina(this.idContenedorPadre).getSelectedData();
	 	 	if(dataMaestro){ 
	 	 		this.onEnablePanel(this,dataMaestro)
	 	 	}
		}
		
	},
			
	Atributos:[
		{
			//configuracion del componente
			config:{
					labelSeparator:'',
					inputType:'hidden',
					name: 'id_beneficiario'
			},
			type:'Field',
			form:true 
		},
		{
			config: {
				name: 'id_funcionario_beneficiario',
				fieldLabel: 'Beneficiario',
				allowBlank: false,
				emptyText: 'Beneficiario...',
				store: new Ext.data.JsonStore({
					url: '../../sis_contabilidad/control/IntBeneficiario/listarIntBeneficiarioProvCombo',
					id: 'id_funcionario_beneficiario',
					root: 'datos',
					sortInfo: {
						field: 'nombre_razon_social',
						direction: 'ASC'
					},
					totalProperty: 'total',
					fields: ['id_funcionario_beneficiario', 'nombre_razon_social', 'nro_documento', 'banco_beneficiario', 'nro_cuenta'],
					remoteSort: true,
					baseParams: {par_filtro: 't.rotulo_comercial#t2.ci#t.nit'}
				}),
				valueField: 'id_funcionario_beneficiario',
				displayField: 'nombre_razon_social',
				tpl:'<tpl for="."><div class="x-combo-list-item"><p>{nombre_razon_social}</p><p>Nro. Documento: {nro_documento}</p><p>Banco: {banco_beneficiario}</p><p>Nro. Cuenta: {nro_cuenta}</p></div></tpl>',
				hiddenName: 'nombre_razon_social',
				forceSelection: true,
				typeAhead: false,
				triggerAction: 'all',
				lazyRender: true,
				mode: 'remote',
				pageSize: 30,
				queryDelay: 1000,
				listWidth:380,
                resizable:true,
                gwidth: 150,
                width: 380,
				renderer : function(value, p, record) {
					return String.format('{0}', record.data['nombre_razon_social']);
				}
			},
			type: 'ComboBox',
			id_grupo: 0,
			filters: {pfiltro: 'intbenef.id_funcionario_beneficiario',type: 'string'},
			grid: false,
			form: true,
			bottom_filter : true
		},
		{
			config:{
				name: 'tipo_doc',
				fieldLabel: 'Tipo Doc.',
				allowBlank: false,
				anchor: '80%',
				gwidth: 100,
				maxLength:30
			},
				type:'TextField',
				filters:{pfiltro:'intbenef.tipo_doc',type:'string'},
				id_grupo:1,
				grid:true,
				form:false
		},
		{
			config:{
				name: 'nro_documento',
				fieldLabel: 'Nro. Documento',
				allowBlank: false,
				anchor: '80%',
				gwidth: 100,
				maxLength:30
			},
				type:'TextField',
				filters:{pfiltro:'intbenef.nro_documento',type:'string'},
				id_grupo:1,
				grid:true,
				form:false
		},
		{
			config:{
				name: 'nombre_razon_social',
				fieldLabel: 'Nombre o Razon Social',
				allowBlank: false,
				anchor: '80%',
				gwidth: 300,
				maxLength:30
			},
				type:'TextField',
				filters:{pfiltro:'intbenef.nombre_razon_social',type:'string'},
				id_grupo:1,
				grid:true,
				form:false
		},
		{
			config:{
				name: 'banco',
				fieldLabel: 'Banco',
				allowBlank: false,
				anchor: '80%',
				gwidth: 100,
				maxLength:30,
				disabled:true
			},
				type:'TextField',
				filters:{pfiltro:'intbenef.banco',type:'string'},
				id_grupo:1,
				grid:true,
				form:true
		},
		{
			config:{
				name: 'nro_cuenta_bancaria_sigma',
				fieldLabel: 'Nro. Cuenta',
				allowBlank: false,
				anchor: '80%',
				gwidth: 100,
				maxLength:300,
				disabled:true
			},
				type:'TextField',
				filters:{pfiltro:'intbenef.nro_cuenta_bancaria_sigma',type:'string'},
				id_grupo:1,
				grid:true,
				form:true
		},
		{
            config: {
                    name: 'importe',
                    fieldLabel: 'Importe',
                    allowBlank: false,
                    width: '100%',
                    gwidth: 100,
                    width: 200,
                    galign: 'right ',
                    maxLength: 100,
                    renderer:function (value,p,record){
						if(record.data.tipo_reg != 'summary'){
                            return  String.format('{0}', Ext.util.Format.number(value,'0,000.00'));
                        }
                        else{
                            return  String.format('<b><font size=2 >{0}</font><b>', Ext.util.Format.number(value,'0,000.00'));
                        }
                    }
                },
                type: 'NumberField',
                filters: {pfiltro: 'intbenef.importe',type: 'numeric'},
                id_grupo: 1,
                grid: true,
                form: true
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
				filters:{pfiltro:'intbenef.estado_reg',type:'string'},
				id_grupo:1,
				grid:false,
				form:false
		},
		{
                config: {
                    labelSeparator:'',
                    inputType:'hidden',
                    name: 'id_int_comprobante'
                },
                type:'Field',
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
				grid:false,
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
				filters:{pfiltro:'intbenef.fecha_reg',type:'date'},
				id_grupo:1,
				grid:false,
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
				filters:{pfiltro:'intbenef.id_usuario_ai',type:'numeric'},
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
				filters:{pfiltro:'intbenef.usuario_ai',type:'string'},
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
				filters:{pfiltro:'intbenef.fecha_mod',type:'date'},
				id_grupo:1,
				grid:false,
				form:false
		}
	],
	tam_pag:50,	
	title:'intBeneficiario',
	ActSave:'../../sis_contabilidad/control/IntBeneficiario/insertarIntBeneficiario',
	ActDel:'../../sis_contabilidad/control/IntBeneficiario/eliminarIntBeneficiario',
	ActList:'../../sis_contabilidad/control/IntBeneficiario/listarIntBeneficiario',
	id_store:'id_beneficiario',
	fields: [
		{name:'id_beneficiario', type: 'numeric'},
		{name:'estado_reg', type: 'string'},
		{name:'id_funcionario_beneficiario', type: 'numeric'},
		{name:'id_int_comprobante', type: 'numeric'},
		{name:'banco', type: 'string'},
		{name:'nro_cuenta_bancaria_sigma', type: 'string'},
		{name:'importe', type: 'numeric'},
		{name:'id_usuario_reg', type: 'numeric'},
		{name:'fecha_reg', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'id_usuario_ai', type: 'numeric'},
		{name:'usuario_ai', type: 'string'},
		{name:'id_usuario_mod', type: 'numeric'},
		{name:'fecha_mod', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'usr_reg', type: 'string'},
		{name:'usr_mod', type: 'string'},
		{name:'tipo_doc', type: 'string'},
		{name:'nro_documento', type: 'string'},
		{name:'nombre_razon_social', type: 'string'},'tipo_reg'
		
	],
	sortInfo:{
		field: 'id_beneficiario',
		direction: 'ASC'
	},
	bdel:true,
	bsave:false,
    
	loadValoresIniciales:function(){
    
		Phx.vista.IntTransaccion.superclass.loadValoresIniciales.call(this);
        console.log(this.maestro.id_int_comprobante);
		this.Cmp.id_int_comprobante.setValue(this.maestro.id_int_comprobante);

    },

	onReloadPage:function(m){
            
		this.maestro=m;

        this.store.baseParams={id_int_comprobante:this.maestro.id_int_comprobante};
        
        this.load({params:{start:0, limit:this.tam_pag}});

    }

	}
)
</script>
		
		