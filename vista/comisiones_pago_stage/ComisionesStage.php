<script>
Phx.vista.ComisionesStage=Ext.extend(Phx.gridInterfaz,{

	constructor:function(config){
		this.maestro=config.maestro;
    	//llama al constructor de la clase padre
		Phx.vista.ComisionesStage.superclass.constructor.call(this,config);
		this.init();
		this.tbar.items.items[4].setVisible(false)
		this.load({params:{start:0, limit:this.tam_pag}})
	},
	tam_pag:50,

	Atributos:[
		{
			//configuracion del componente
			config:{
					labelSeparator:'',
					inputType:'hidden',
					name: 'id_comision_stage'
			},
			type:'Field',
			form:true
		},
    {
        config:{
            name: 'codigo_empresa',
            fieldLabel: 'Codigo Compañia',
            allowBlank: false,
            anchor: '80%'
        },
        type:'TextField',
				bottom_filter: true,
        id_grupo:1,
        grid:true,
        form:true
    },
    {
        config:{
            name: 'nombre_empresa',
            fieldLabel: 'Nombre Compañia',
            allowBlank: false,
            anchor: '80%',
						gwidth: 150
        },
        type:'TextField',
				bottom_filter: true,
        id_grupo:1,
        grid:true,
        form:true
    },
		{
			config:{
				name: 'fecha_ini',
				fieldLabel: 'Fecha Inicio',
				allowBlank: false,
				anchor: '80%',
				gwidth: 100
			},
			type:'DateField',
			id_grupo:1,
			grid:true,
			form:true
		},
    {
			config:{
				name: 'fecha_fin',
				fieldLabel: 'Fecha Fin',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100
			},
			type:'DateField',
			id_grupo:1,
			grid:true,
			form:true
		},
		{
			config:{
				name: 'porcentaje_comision',
				fieldLabel: 'Porcentaje Comison',
				allowBlank: false,
				anchor: '80%',
				gwidth: 150,
				decimalPrecision:6
			},
			type:'NumberField',
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
				gwidth: 250
			},
			type:'TextArea',
			bottom_filter: true,
			id_grupo:1,
			grid:true,
			form:true
		},
		{
			config:{
				name: 'estado',
				fieldLabel: 'Estado',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100
			},
			type:'TextField',
			id_grupo:1,
			grid:true,
			form:true
		}
	],

	title:'Comisiones Stage',
  ActSave:'../../sis_contabilidad/control/ComisionesStage/insertarComision',
  ActDel:'../../sis_contabilidad/control/ComisionesStage/eliminarComision',
  ActList:'../../sis_contabilidad/control/ComisionesStage/listarComisiones',
	id_store:'id_comision_stage',
	fields: [
		{name:'id_comision_stage', type: 'numeric'},
    {name:'codigo_empresa', type: 'varchar'},
    {name:'nombre_empresa', type: 'varchar'},
		{name:'fecha_fin', type: 'varchar'},
    {name:'fecha_ini', type: 'varchar'},
    {name:'porcentaje_comision', type: 'numeric'},
		{name:'observaciones', type: 'string'},
		{name:'estado', type: 'string'},

	],
	sortInfo:{
		field: 'id_comision_stage',
		direction: 'ASC'
	},
	bdel:true,
	bsave:false,
	bexport:false,
	btest:false,
	}
)
</script>
