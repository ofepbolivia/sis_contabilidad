<?php
/**
*@package pXP
*@file gen-MonedaPais.php
*@author  (ivaldivia)
*@date 07-08-2019 14:05:50
*@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
*/

header("content-type: text/javascript; charset=UTF-8");
?>
<script>
Phx.vista.MonedaPais=Ext.extend(Phx.gridInterfaz,{

	constructor:function(config){
		this.maestro=config.maestro;
    	//llama al constructor de la clase padre
		Phx.vista.MonedaPais.superclass.constructor.call(this,config);
		this.init();
    this.tbar.addField(this.lugar);

    this.lugar.on('select', function( combo, record, index){
                this.capturaFiltros();
								this.id_lugar=this.lugar.getValue();
                this.id_sql=record.data['id_sql_server'];
            },this);

    this.tbar.items.items[0].disable();
    this.tbar.items.items[3].disable();

		this.bbar.el.dom.style.background='#A6C2ED';
		this.tbar.el.dom.style.background='#A6C2ED';
		this.grid.body.dom.firstChild.firstChild.lastChild.style.background='#FAFAFA';

	},


  capturaFiltros:function(combo, record, index){
            this.desbloquearOrdenamientoGrid();
                this.load({
                    params : {
                        start: 0,
                        limit: 1000,
                        id_lugar:this.lugar.getValue()
                    }
                });
  },

  onButtonNew : function () {
	    Phx.vista.MonedaPais.superclass.onButtonNew.call(this);
			this.Cmp.id_lugar.setValue(this.id_lugar);
      this.Cmp.id_sql_server.setValue(this.id_sql);
			this.form.el.dom.firstChild.childNodes[0].style.background = '#A6C2ED';
    },


	Atributos:[
		{
			//configuracion del componente
			config:{
					labelSeparator:'',
					inputType:'hidden',
					name: 'id_moneda_pais'
			},
			type:'Field',
			form:true
		},
		{
			//configuracion del componente
			config:{
					labelSeparator:'',
					inputType:'hidden',
					name: 'id_sql_server'
			},
			type:'Field',
			form:true
		},
    {
			config:{
				name: 'id_lugar',
				fieldLabel: 'Lugar',
				allowBlank: true,
        inputType:'hidden',
				anchor: '80%',
				gwidth: 100,
				maxLength:10
			},
				type:'TextField',
				filters:{pfiltro:'monpa.id_lugar',type:'string'},
				id_grupo:1,
				grid:false,
				form:true
		},
		{
			config:{
				name: 'desc_moneda',
				fieldLabel: 'Moneda',
				allowBlank: true,
        inputType:'hidden',
				anchor: '80%',
				gwidth: 200,
				maxLength:10
			},
				type:'TextField',
				filters:{pfiltro:'monpa.moneda',type:'string'},
				id_grupo:1,
				grid:true,
				form:false
		},
    {
				config:{
					name: 'id_moneda',
					fieldLabel: 'Moneda',
					allowBlank: false,
					emptyText:'Moneda...',
					store:new Ext.data.JsonStore(
					{
						url: '../../sis_parametros/control/Moneda/listarMoneda',
						id: 'id_moneda',
						root: 'datos',
						sortInfo:{
							field: 'moneda',
							direction: 'ASC'
						},
						totalProperty: 'total',
						fields: ['id_moneda','moneda','codigo','codigo_internacional'],
						// turn on remote sorting
						remoteSort: true,
						baseParams:{par_filtro:'moneda.codigo_internacional#moneda.moneda',tipo:'listar_todo'}
					}),
					valueField: 'id_moneda',
					displayField: 'moneda',
					gdisplayField:'moneda',
					hiddenName: 'id_moneda',
					tpl:'<tpl for="."><div class="x-combo-list-item"><p><b>{moneda}</b></p><b><p>Codigo:<font color="green">{codigo_internacional}</font></b></p></div></tpl>',
						triggerAction: 'all',
						lazyRender:true,
					mode:'remote',
					pageSize:50,
					queryDelay:500,
					anchor: '80%',
					gwidth:150,
					minChars:2,
					renderer:function (value, p, record){return String.format('{0}', record.data['id_moneda']);}
				},
				type:'ComboBox',
				filters:{pfiltro:'moneda.codigo_internacional',type:'string'},
				id_grupo:0,
				grid:false,
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
			config: {
				name: 'origen',
				fieldLabel: 'Origen',
				anchor: '80%',
				tinit: false,
				allowBlank: false,
				origen: 'CATALOGO',
				gdisplayField: 'origen',
				gwidth: 100,
				baseParams:{
						cod_subsistema:'PARAM',
						catalogo_tipo:'tmoneda__origen'
				},
				renderer:function (value, p, record){return String.format('{0}', record.data['origen']);}
			},
			type: 'ComboRec',
			id_grupo: 1,
			filters:{pfiltro:'monpa.origen',type:'string'},
			grid: true,
			form: true
		},
		{
			config:{
				name: 'prioridad',
				fieldLabel: 'Prioridad',
				allowBlank: false,
				anchor: '80%',
				gwidth: 100,
				maxLength:4
			},
				type:'NumberField',
				filters:{pfiltro:'monpa.prioridad',type:'numeric'},
				id_grupo:1,
				grid:true,
				form:true
		},
    {
			config: {
				name: 'tipo_actualizacion',
				fieldLabel: 'Tipo Actualización',
				anchor: '80%',
				tinit: false,
				allowBlank: false,
				origen: 'CATALOGO',
				gdisplayField: 'tipo_actualizacion',
				gwidth: 100,
				baseParams:{
						cod_subsistema:'PARAM',
						catalogo_tipo:'tmoneda__tipo_actualizacion'
				},
				renderer:function (value, p, record){return String.format('{0}', record.data['tipo_actualizacion']);}
			},
			type: 'ComboRec',
			id_grupo: 1,
			filters:{pfiltro:'monpa.tipo_actualizacion',type:'string'},
			grid: true,
			form: true
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
				filters:{pfiltro:'monpa.fecha_reg',type:'date'},
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
				filters:{pfiltro:'monpa.id_usuario_ai',type:'numeric'},
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
				filters:{pfiltro:'monpa.usuario_ai',type:'string'},
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
				filters:{pfiltro:'monpa.fecha_mod',type:'date'},
				id_grupo:1,
				grid:true,
				form:false
		}
	],
	tam_pag:50,
	title:'Moneda Pais',
	ActSave:'../../sis_contabilidad/control/MonedaPais/insertarMonedaPais',
	ActDel:'../../sis_contabilidad/control/MonedaPais/eliminarMonedaPais',
	ActList:'../../sis_contabilidad/control/MonedaPais/listarMonedaPais',
	id_store:'id_moneda_pais',
	fields: [
		{name:'id_moneda_pais', type: 'numeric'},
		{name:'estado_reg', type: 'string'},
		{name:'id_moneda', type: 'numeric'},
		{name:'estado', type: 'string'},
		{name:'origen', type: 'string'},
		{name:'prioridad', type: 'numeric'},
		{name:'tipo_actualizacion', type: 'string'},
		{name:'id_lugar', type: 'numeric'},
		{name:'id_usuario_reg', type: 'numeric'},
		{name:'fecha_reg', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'id_usuario_ai', type: 'numeric'},
		{name:'usuario_ai', type: 'string'},
		{name:'id_usuario_mod', type: 'numeric'},
		{name:'fecha_mod', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'usr_reg', type: 'string'},
		{name:'usr_mod', type: 'string'},
		{name:'desc_moneda', type: 'string'},

	],
	sortInfo:{
		field: 'id_moneda_pais',
		direction: 'ASC'
	},
	bdel:true,
	bsave:false,
  bexcel:false,
	btest:false,
  fwidth : 420,
  fheight : 250,

  /**************************************Detalle TipoCambio*****************************************/
  tabsouth:[
            {
                url:'../../../sis_contabilidad/vista/moneda_pais/TipoCambioPais.php',
                title:'Detalle Tipo Cambio Pais',
                height: '50%',
                cls:'TipoCambioPais'
            }
        ],
  /************************************************************************************************/

  lugar : new Ext.form.ComboBox({
            name: 'id_lugar',
            fieldLabel: 'Lugar',
            emptyText:'Lugar...',
            store:new Ext.data.JsonStore(
                {
                    url: '../../sis_parametros/control/Lugar/listarLugar',
                    id: 'id_lugar',
                    root: 'datos',
                    sortInfo:{
                        field: 'nombre',
                        direction: 'ASC'
                    },
                    totalProperty: 'total',
                    fields: ['id_lugar','id_lugar_fk','codigo','nombre','tipo','sw_municipio','sw_impuesto','codigo_largo','id_sql_server'],
                    // turn on remote sorting
                    remoteSort: true,
                    baseParams:{par_filtro:'lug.nombre',pais:'pais'}
                }),
            valueField: 'id_lugar',
            displayField: 'nombre',
            hiddenName: 'id_lugar',
            triggerAction: 'all',
            lazyRender:true,
            mode:'remote',
            gwidth: 100,
            pageSize:50,
            queryDelay:500,
            anchor:"35%",
            minChars:2,
        }),



	}
)
</script>
