<?php
/**
*@package pXP
*@file gen-TipoCambioPais.php
*@author  (ivaldivia)
*@date 07-08-2019 14:12:25
*@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
*/

header("content-type: text/javascript; charset=UTF-8");
?>
<script>
Phx.vista.TipoCambioPais=Ext.extend(Phx.gridInterfaz,{

	constructor:function(config){
		this.maestro=config.maestro;
    	//llama al constructor de la clase padre
		Phx.vista.TipoCambioPais.superclass.constructor.call(this,config);
		this.init();

    this.tbar.items.items[0].disable();
    this.tbar.items.items[3].disable();

		this.bbar.el.dom.style.background='#A6C2ED';
		this.tbar.el.dom.style.background='#A6C2ED';
		this.grid.body.dom.firstChild.firstChild.lastChild.style.background='#FAFAFA';
		//this.load({params:{start:0, limit:this.tam_pag}})
	},

	Atributos:[
		{
			//configuracion del componente
			config:{
					labelSeparator:'',
					inputType:'hidden',
					name: 'id_tipo_cambio_pais'
			},
			type:'Field',
			form:true
		},
    {
			config:{
				name: 'id_moneda_pais',
				fieldLabel: 'id_moneda_pais',
				allowBlank: true,
        inputType:'hidden',
				anchor: '80%',
				gwidth: 100,
				maxLength:10
			},
				type:'TextField',
				filters:{pfiltro:'tcpa.id_moneda_pais',type:'string'},
				id_grupo:1,
				grid:false,
				form:true
		},
		{
			config:{
				name: 'id_lugar',
				fieldLabel: 'id_lugar',
				allowBlank: true,
        inputType:'hidden',
				anchor: '80%',
				gwidth: 100,
				maxLength:10
			},
				type:'TextField',
				filters:{pfiltro:'tcpa.id_lugar',type:'string'},
				id_grupo:1,
				grid:false,
				form:true
		},
		{
			config:{
				name: 'id_moneda',
				fieldLabel: 'id_moneda',
				allowBlank: true,
        inputType:'hidden',
				anchor: '80%',
				gwidth: 100,
				maxLength:10
			},
				type:'TextField',
				filters:{pfiltro:'tcpa.id_moneda',type:'string'},
				id_grupo:1,
				grid:false,
				form:true
		},
    {
			config:{
				name: 'fecha',
				fieldLabel: 'Fecha',
				allowBlank: false,
				anchor: '80%',
				gwidth: 100,
							format: 'd/m/Y',
							renderer:function (value,p,record){return value?value.dateFormat('d/m/Y'):''}
			},
				type:'DateField',
				filters:{pfiltro:'tcpa.fecha',type:'date'},
				id_grupo:1,
				grid:true,
				form:true
		},
		{
			config:{
				name: 'oficial',
				fieldLabel: 'Oficial',
				allowBlank: false,
				anchor: '80%',
				gwidth: 100,
				maxLength:1179655,
        decimalPrecision:7,
			},
				type:'NumberField',
				filters:{pfiltro:'tcpa.oficial',type:'numeric'},
				id_grupo:1,
				grid:true,
				form:true
		},
		{
			config:{
				name: 'compra',
				fieldLabel: 'Compra',
				allowBlank: false,
				anchor: '80%',
				gwidth: 100,
				maxLength:1179655,
        decimalPrecision:7,
			},
				type:'NumberField',
				filters:{pfiltro:'tcpa.compra',type:'numeric'},
				id_grupo:1,
				grid:true,
				form:true
		},
		{
			config:{
				name: 'venta',
				fieldLabel: 'Venta',
				allowBlank: false,
				anchor: '80%',
				gwidth: 100,
				maxLength:1179655,
        decimalPrecision:7,
			},
				type:'NumberField',
				filters:{pfiltro:'tcpa.venta',type:'numeric'},
				id_grupo:1,
				grid:true,
				form:true
		},
		{
			config:{
				name: 'estado_reg',
				fieldLabel: 'Estado',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:10
			},
				type:'TextField',
				filters:{pfiltro:'monpa.estado_reg',type:'string'},
				id_grupo:1,
				grid:true,
				form:false
		},
		{
			config:{
				name: 'observaciones',
				fieldLabel: 'Observaciones',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:300
			},
				type:'TextArea',
				filters:{pfiltro:'tcpa.observaciones',type:'string'},
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
				filters:{pfiltro:'tcpa.fecha_reg',type:'date'},
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
				filters:{pfiltro:'tcpa.id_usuario_ai',type:'numeric'},
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
				filters:{pfiltro:'tcpa.usuario_ai',type:'string'},
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
				filters:{pfiltro:'tcpa.fecha_mod',type:'date'},
				id_grupo:1,
				grid:true,
				form:false
		}
	],
	tam_pag:50,
	title:'Tipo Cambio Pais',
	ActSave:'../../sis_contabilidad/control/TipoCambioPais/insertarTipoCambioPais',
	ActDel:'../../sis_contabilidad/control/TipoCambioPais/eliminarTipoCambioPais',
	ActList:'../../sis_contabilidad/control/TipoCambioPais/listarTipoCambioPais',
	id_store:'id_tipo_cambio_pais',
	fields: [
		{name:'id_tipo_cambio_pais', type: 'numeric'},
		{name:'estado_reg', type: 'string'},
		{name:'fecha', type: 'date',dateFormat:'Y-m-d'},
		{name:'oficial', type: 'numeric'},
		{name:'compra', type: 'numeric'},
		{name:'venta', type: 'numeric'},
		{name:'observaciones', type: 'string'},
		{name:'id_reload_pais', type: 'numeric'},
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
		field: 'id_tipo_cambio_pais',
		direction: 'ASC'
	},
  bdel:true,
	bsave:false,
  bexcel:false,
	btest:false,
  fwidth : 420,
  fheight : 350,

  onReloadPage: function(m){
	    this.maestro=m;
      console.log("llega aqui el id",this);
	    this.store.baseParams={id_moneda_pais:this.maestro.id_moneda_pais};
	    this.load({params:{start:0, limit:50}});//this.bloquearMenus();
    },

    onButtonNew : function () {
  	    Phx.vista.TipoCambioPais.superclass.onButtonNew.call(this);
          this.Cmp.id_moneda_pais.setValue(this.maestro.id_moneda_pais);
					/*Recuperamos el id lugar y id_moenda para obtener el codigo pais para informix*/
					this.Cmp.id_lugar.setValue(this.maestro.id_lugar);
					this.Cmp.id_moneda.setValue(this.maestro.id_moneda);
					this.mostrarComponente(this.Cmp.fecha);
					this.form.el.dom.firstChild.childNodes[0].style.background = '#A6C2ED';
					console.log("recuperar el id_moneda",this);
					/*******************************************************************/

      },
			onButtonEdit : function () {
	  	    Phx.vista.TipoCambioPais.superclass.onButtonEdit.call(this);
	          this.Cmp.id_moneda_pais.setValue(this.maestro.id_moneda_pais);
						/*Recuperamos el id lugar y id_moenda para obtener el codigo pais para informix*/
						this.Cmp.id_lugar.setValue(this.maestro.id_lugar);
						this.Cmp.id_moneda.setValue(this.maestro.id_moneda);
						this.ocultarComponente(this.Cmp.fecha);
						this.form.el.dom.firstChild.childNodes[0].style.background = '#A6C2ED';
						/*******************************************************************/

	      },


	}
)
</script>
