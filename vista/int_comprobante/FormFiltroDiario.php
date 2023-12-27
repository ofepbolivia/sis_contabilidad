<?php
/**
*@package pXP
*@file    FormFiltroDiario.php
*@author  manuel guerra
*@date    09-10-2017
*@description muestra un formulario que muestra la cuenta y el monto de la transferencia
*/
header("content-type: text/javascript; charset=UTF-8");
?>
<script>
Phx.vista.FormFiltroDiario=Ext.extend(Phx.frmInterfaz,{
		
	layout:'fit',
	maxCount:0,
	constructor:function(config){   
		Phx.vista.FormFiltroDiario.superclass.constructor.call(this,config);
		this.init(); 
		this.iniciarEventos();		
		this.loadValoresIniciales();	
	},

	Atributos:[
		{
			config:{
				name:'tipo_moneda',
				fieldLabel:'Tipo de Moneda',
				allowBlank:false,
				emptyText:'Tipo...',
				typeAhead: true,
				triggerAction: 'all',
				lazyRender:true,
				mode: 'local',
				valueField: 'tipo_moneda',
				gwidth: 100,
				store:new Ext.data.ArrayStore({
					fields: ['variable', 'valor'],
					data : [
								['1','Moneda Base'],
								['2','Moneda Triangulacion'],
								['3','Moneda Actualizacion'],
				           ]
				}),
				valueField: 'variable',
				displayField: 'valor'
			},
			type:'ComboBox',
			form:true

		},{

 			config:{
 				sysorigen: 'sis_contabilidad',
     		    name: 'id_cuenta',
 				origen: 'CUENTA',
 				allowBlank: false,
 				fieldLabel: 'Cuenta',
 				gdisplayField: 'desc_cuenta',
 				baseParams: { sw_transaccional: 'movimiento' },
 				width: 200
     	     },
 			type: 'ComboRec',
 			id_grupo: 1,
 			form: true
   	},

	//{
	//   config:{
 	//			sysorigen: 'sis_contabilidad',
    // 		    name: 'id_int_comprobante',
 	//			origen: 'COMPROBANTE',
 	//			allowBlank: false,
 	//			fieldLabel: 'Cuenta',
 	//			gdisplayField: 'id_int_comprobante',
 	//			baseParams: { sw_transaccional: 'movimiento' },
 	//			width: 200
    // 	     },
 	//		type: 'ComboRec',
 	//		id_grupo: 1,
 	//		form: true
   	//},


     //   {
    //            config:{
    //                name : 'tipo_filtro',
    //                fieldLabel : 'Libros',
    //                items: [
    //                    {boxLabel: 'Libro Diario', name: 'tipo_filtro', inputValue: 'libro_diario', checked: true},
    //                    {boxLabel: 'Libro Diario Detallado', name: 'tipo_filtro', inputValue: 'libro_diario_detallado'}
    //                ],
    //            },
    //            type : 'RadioGroupField',
    //            id_grupo : 0,
    //            form : false
    //        },
        {
                config:{
                    name : 'tipo_filtro_f',
                    fieldLabel : 'Seleccione por:',
                    items: [
                        {boxLabel: 'Fecha', name: 'tipo_filtro_f', inputValue: 'fecha', checked: true},
                        {boxLabel: 'Número de Comprobante', name: 'tipo_filtro_f', inputValue: 'num_comprobante'}
                    ],
                },
                type : 'RadioGroupField',
                id_grupo : 0,
                form : false
            },

	

    
//         {
//			config:{
//				name: 'libro_diario',
//				fieldLabel: 'Libro Diario',
//				allowBlank: false,
//				anchor: '80%',
//				gwidth: 50,				
//			},
//			type:'Checkbox',		
//			form:true

//		},
        
//        {
//			config:{
//				name: 'libro_diario_det',
//				fieldLabel: 'Libro Diario Detallado',
//				allowBlank: false,
//				anchor: '80%',
//				gwidth: 50,				
//			},
//			type:'Checkbox',		
//			form:true
//		},
        {
                config:{
                    name: 'desde',
                    fieldLabel: 'Fecha Inicio',
                    allowBlank: true,
                    format: 'd/m/Y',
                    width: 200
                },
                type: 'DateField',
                form: true
        },{
                config:{
                    name: 'hasta',
                    fieldLabel: 'Fecha Final',
                    allowBlank: true,
                    format: 'd/m/Y',
                    width: 200
                },
                type: 'DateField',
                form: true
        },{
			
			config:{
				name:'tipo_diario',
				fieldLabel:'Tipo Diario',
				allowBlank:false,
				emptyText:'Tipo...',
				typeAhead: true,
				triggerAction: 'all',
				lazyRender:true,
				mode: 'local',
				valueField:'tipo_diario',
				gwidth: 100,
				store:new Ext.data.ArrayStore({
					fields : ['variable', 'valor'],
					data : [
								['dia','Libro Diario'],
								['det','Libro Diario Detallado']
						]
				}),
				valueField: 'variable',
				displayField: 'valor'
			},
			type:'ComboBox',
			form:true

			},{

			config:{
				name:'tipo_formato',
				fieldLabel:'Tipo de Reporte',
				allowBlank:false,
				emptyText:'Tipo...',
				typeAhead: true,
				triggerAction: 'all',
				lazyRender:true,
				mode: 'local',
				valueField: 'tipo_formato',
				gwidth: 100,
				store:new Ext.data.ArrayStore({
					fields: ['variable', 'valor'],
					data : [ 
								['pdf','PDF'],
								['xls','EXCEL']
							]
				}),
				valueField: 'variable',
				displayField: 'valor'
			},
			type:'ComboBox',
			form:true

		},{

			config:{
				name: 'cc',
				fieldLabel: 'Centro Costo',
				allowBlank: false,
				anchor: '80%',
				gwidth: 50,				
			},
			type:'Checkbox',		
			form:false

		},{
			config:{
				name: 'partida',
				fieldLabel: 'Partida',
				allowBlank: true,
				anchor: '80%',
				gwidth: 50,				
			},
			type:'Checkbox',		
			form:false	
		},{
			config:{
				name: 'auxiliar',
				fieldLabel: 'Auxiliar',
				allowBlank: true,
				anchor: '80%',
				gwidth: 50,				
			},
			type:'Checkbox',		
			form:false	
		},{
			config:{
				name: 'ordenes',
				fieldLabel: 'Ordenes',
				allowBlank: true,
				anchor: '80%',
				gwidth: 50,				
			},
			type:'Checkbox',			
			form:false	
		},{
			config:{
				name: 'relacional',
				fieldLabel: 'Comprobante Relacional',
				allowBlank: true,
				anchor: '80%',
				gwidth: 50,			
			},
			type:'Checkbox',			
			form:false	
		},{
			config:{
				name: 'nro_tramite',
				fieldLabel: 'Nro de Tramite',
				allowBlank: true,
				anchor: '80%',
				gwidth: 50,
				checked:false		
			},
			type:'Checkbox',
			form:false
		},
    
      //  {
	//		config:{
	//			name: 'fecIni',
	//			fieldLabel: 'Fecha Inicio',
	//			allowBlank: true,
	//			anchor: '80%',
	//			gwidth: 50,
	//			checked:true		
	//		},
	//		type:'Checkbox',
	//		form:true,		
	//	},{
	//		config:{
	//			name: 'fecFin',
	//			fieldLabel: 'Fecha Final',
	//			allowBlank: true,
	//			anchor: '80%',
	//			gwidth: 50,
	//			checked:true		
	//		},
	//		type:'Checkbox',
	//		form:true,		
	//	},
	],
	title:'Filtro',
	onSubmit:function(){
		//TODO passar los datos obtenidos del wizard y pasar  el evento save		
		if (this.form.getForm().isValid()) {
			this.fireEvent('beforesave',this,this.getValues());
			this.getValues();
		}
	},
	
	getValues:function(){		
		var resp = {			
			tipo_moneda:this.Cmp.tipo_moneda.getValue(),
			id_cuenta:this.Cmp.id_cuenta.getValue(),
			tipo_formato:this.Cmp.tipo_formato.getValue(),	
			tipo_diario:this.Cmp.tipo_diario.getValue(),	
			//desde:this.Cmp.desde.getValue(),
			nro_cuenta:this.Cmp.id_cuenta.getValue(),
			//hasta:this.Cmp.hasta.getValue()
			fecIni:this.Cmp.desde.getValue(),
			fecFin:this.Cmp.hasta.getValue(),
			//id_int_comprobante:this.Cmp.id_int_comprobante.getValue(),
			
			
			//id_proceso_wf:this.Cmp.id_proceso_wf.getValue(),
			//cc:this.Cmp.cc.getValue(),
			//partida:this.Cmp.partida.getValue(),
			//auxiliar:this.Cmp.auxiliar.getValue(),
			//ordenes:this.Cmp.ordenes.getValue(),
			//relacional:this.Cmp.relacional.getValue(),
			//nro_tramite:this.Cmp.nro_tramite.getValue(),
			//nro_cuenta:this.Cmp.desc_cuenta.getValue(),
			//libro_diario:this.Cmp.libro_diario.getValue(),
			//libro_diario_det:this.Cmp.libro_diario_det.getValue(),
		}
		return resp;
	}
})
</script>