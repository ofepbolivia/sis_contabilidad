<?php
/**
 *@package pXP
 *@file    GenerarLibroBancos.php
 *@author  Gonzalo Sarmiento Sejas
 *@date    01-12-2014
 *@description Archivo con la interfaz para generaci�n de reporte
 */
header("content-type: text/javascript; charset=UTF-8");
?>
<script>
	Phx.vista.ReporteLibroComprasVentasIVA = Ext.extend(Phx.frmInterfaz, {
		
		Atributos : [
		{
			config:{
				name: 'id_entidad',
				fieldLabel: 'Entidad',
				qtip: 'entidad a la que pertenese el depto, ',
				allowBlank: false,
				emptyText:'Entidad...',
				store:new Ext.data.JsonStore(
				{
					url: '../../sis_parametros/control/Entidad/listarEntidad',
					id: 'id_entidad',
					root: 'datos',
					sortInfo:{
						field: 'nombre',
						direction: 'ASC'
					},
					totalProperty: 'total',
					fields: ['id_entidad','nit','nombre'],
					// turn on remote sorting
					remoteSort: true,
					baseParams: { par_filtro:'nit#nombre' }
				}),
				valueField: 'id_entidad',
				displayField: 'nombre',
				gdisplayField:'desc_entidad',
				hiddenName: 'id_entidad',
    			triggerAction: 'all',
    			lazyRender:true,
				mode:'remote',
				pageSize:50,
				queryDelay:500,
				anchor:"90%",
				listWidth:280,
				gwidth:150,
				minChars:2,
				renderer:function (value, p, record){return String.format('{0}', record.data['desc_entidad']);}

       		},
       		type:'ComboBox',
			filters:{pfiltro:'ENT.nombre',type:'string'},
			id_grupo:0,
			egrid: true,
			grid:true,
			form:true
		},
		
		
		
		{
			config:{
				name:'tipo_lcv',
				fieldLabel:'Tipo de Reporte',
				typeAhead: true,
				allowBlank:false,
	    		triggerAction: 'all',
	    		emptyText:'Tipo...',
	    		selectOnFocus:true,
				mode:'local',
				store:new Ext.data.ArrayStore({
	        	fields: ['ID', 'valor'],
	        	data :	[
						['endesis_erp','Libro de Compras Estandar'],
	        	        ['lcv_compras','Libro de Compras Estandar (nueva versión)'],
	        	        ['lcv_ventas','Libro de Ventas Estandar'],	
						['LCNCD','Libro de Compras NCD']					
						]	        				
	    		}),
				valueField:'ID',
				displayField:'valor',
				width:250,			
				
			},
			type:'ComboBox',
			id_grupo:1,
			form:true
		},
		{
			config:{
				name:'filtro_sql',
				fieldLabel:'Filtrar Por',
				typeAhead: true,
				allowBlank:false,
	    		triggerAction: 'all',
	    		emptyText:'Filtro...',
	    		selectOnFocus:true,
				mode:'local',
				store:new Ext.data.ArrayStore({
	        	fields: ['ID', 'valor'],
	        	data :	[['periodo','Año y Mes'],	
						['fechas','Rango de Fechas']]	        				
	    		}),
				valueField:'ID',
				displayField:'valor',
				width:250,			
				
			},
			type:'ComboBox',
			id_grupo:1,
			form:true
		},
		
		{
            config:{
                name:'id_gestion',
                fieldLabel:'Gestión',
                allowBlank:true,
                emptyText:'Gestión...',
                store: new Ext.data.JsonStore({
                         url: '../../sis_parametros/control/Gestion/listarGestion',
                         id: 'id_gestion',
                         root: 'datos',
                         sortInfo:{
                            field: 'gestion',
                            direction: 'DESC'
                    },
                    totalProperty: 'total',
                    fields: ['id_gestion','gestion','moneda','codigo_moneda'],
                    // turn on remote sorting
                    remoteSort: true,
                    baseParams:{par_filtro:'gestion'}
                    }),
                valueField: 'id_gestion',
                displayField: 'gestion',
                //tpl:'<tpl for="."><div class="x-combo-list-item"><p><b>{nro_cuenta}</b></p><p>{denominacion}</p></div></tpl>',
                hiddenName: 'id_gestion',
                forceSelection:true,
                typeAhead: false,
                triggerAction: 'all',
                lazyRender:true,
                mode:'remote',
                pageSize:10,
                queryDelay:1000,
                listWidth:600,
                resizable:true,
                anchor:'100%'
                
            },
            type:'ComboBox',
            id_grupo:0,
            filters:{   
                        pfiltro:'gestion',
                        type:'string'
                    },
            grid:true,
            form:true
        },
		{
            config:{
                name:'id_periodo',
                fieldLabel:'Periodo',
                allowBlank:true,
                emptyText:'Periodo...',
                store: new Ext.data.JsonStore({
                         url: '../../sis_parametros/control/Periodo/listarPeriodo',
                         id: 'id_periodo',
                         root: 'datos',
                         sortInfo:{
                            field: 'id_periodo',
                            direction: 'ASC'
                    },
                    totalProperty: 'total',
                    fields: ['id_periodo','literal','periodo','fecha_ini','fecha_fin'],
                    // turn on remote sorting
                    remoteSort: true,
                    baseParams:{par_filtro:'periodo#literal'}
                    }),
                valueField: 'id_periodo',
                displayField: 'literal',
                //tpl:'<tpl for="."><div class="x-combo-list-item"><p><b>{nro_cuenta}</b></p><p>{denominacion}</p></div></tpl>',
                hiddenName: 'id_periodo',
                forceSelection:true,
                typeAhead: false,
                triggerAction: 'all',
                lazyRender:true,
                mode:'remote',
                pageSize:12,
                queryDelay:1000,
                listWidth:600,
                resizable:true,
                anchor:'100%'
                
            },
            type:'ComboBox',
            id_grupo:0,
            filters:{   
                        pfiltro:'literal',
                        type:'string'
                    },
            grid:true,
            form:true
        },
		{
			config:{
				name: 'fecha_ini',
				fieldLabel: 'Fecha Inicio',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
						format: 'd/m/Y', 
						renderer:function (value,p,record){return value?value.dateFormat('d/m/Y'):''}
			},
			type:'DateField',
			filters:{pfiltro:'fecha_ini',type:'date'},
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
				gwidth: 100,
						format: 'd/m/Y', 
						renderer:function (value,p,record){return value?value.dateFormat('d/m/Y'):''}
			},
			type:'DateField',
			filters:{pfiltro:'fecha_fin',type:'date'},
			id_grupo:1,
			grid:true,
			form:true
		},
		{
			config:{
				name:'id_usuario',
				fieldLabel:'Usuario',
				allowBlank:false,
				emptyText:'Usuario...',
				store: new Ext.data.JsonStore({

					url: '../../sis_seguridad/control/Usuario/listarUsuario',
					id: 'id_persona',
					root: 'datos',
					sortInfo:{
						field: 'desc_person',
						direction: 'ASC'
					},
					totalProperty: 'total',
					fields: ['id_usuario','desc_person','cuenta'],
					// turn on remote sorting
					remoteSort: true,
					baseParams:{par_filtro:'PERSON.nombre_completo2#cuenta',_adicionar:'si'}
				}),
				valueField: 'id_usuario',
				displayField: 'desc_person',
				gdisplayField:'desc_usuario',//dibuja el campo extra de la consulta al hacer un inner join con orra tabla
				tpl:'<tpl for="."><div class="x-combo-list-item"><p>{desc_person}</p></div></tpl>',
				hiddenName: 'id_usuario',
				forceSelection:true,
				typeAhead: true,
				triggerAction: 'all',
				lazyRender:true,
				mode:'remote',
				pageSize:10,
				queryDelay:1000,
				width:250,
				gwidth:280,
				minChars:2
			},
			type:'ComboBox',
			id_grupo:0,
			form:true
		},
		{
			config:{
				name:'formato_reporte',
				fieldLabel:'Formato del Reporte',
				typeAhead: true,
				allowBlank:false,
	    		triggerAction: 'all',
	    		emptyText:'Formato...',
	    		selectOnFocus:true,
				mode:'local',
				store:new Ext.data.ArrayStore({
	        	fields: ['ID', 'valor'],
	        	data :	[['txt','TXT'],
						['pdf','PDF'],	
						['csv','CSV'],
                        ['xls','XLS']]
	    		}),
				valueField:'ID',
				displayField:'valor',
				width:250,			
				
			},
			type:'ComboBox',
			id_grupo:1,
			form:true
		}],
		
		
		title : 'Reporte Libro Compras Ventas IVA',		
		ActSave : '../../sis_tesoreria/control/TsLibroBancos/reporteLibroBancos',//modificacion de la ruta sis_contabilidad a sis_tesoreria
		
		topBar : true,
		botones : false,
		labelSubmit : 'Generar',
		tooltipSubmit : '<b>Reporte LCV - IVA</b>',
		
		constructor : function(config) {
			Phx.vista.ReporteLibroComprasVentasIVA.superclass.constructor.call(this, config);
			this.init();
			
			this.ocultarComponente(this.Cmp.fecha_fin);
			this.ocultarComponente(this.Cmp.fecha_ini);
			this.ocultarComponente(this.Cmp.id_gestion);
			this.ocultarComponente(this.Cmp.id_periodo);
						
			this.iniciarEventos();
		},
		
		iniciarEventos:function(){        
			
			this.Cmp.id_gestion.on('select',function(c,r,n){
				
				this.Cmp.id_periodo.reset();
				this.Cmp.id_periodo.store.baseParams={id_gestion:c.value, vista: 'reporte'};				
				this.Cmp.id_periodo.modificado=true;
				
			},this);
			
			
			this.Cmp.filtro_sql.on('select',function(combo, record, index){
				
				if(index == 0){
					this.ocultarComponente(this.Cmp.fecha_fin);
					this.ocultarComponente(this.Cmp.fecha_ini);
					this.mostrarComponente(this.Cmp.id_gestion);
					this.mostrarComponente(this.Cmp.id_periodo);
				}
				else{
					this.mostrarComponente(this.Cmp.fecha_fin);
					this.mostrarComponente(this.Cmp.fecha_ini);
					this.ocultarComponente(this.Cmp.id_gestion);
					this.ocultarComponente(this.Cmp.id_periodo);
				}
				
			}, this);
		},
		
		
		
		tipo : 'reporte',
		clsSubmit : 'bprint',
		
		Grupos : [{
			layout : 'column',
			items : [{
				xtype : 'fieldset',
				layout : 'form',
				border : true,
				title : 'Datos para el reporte',
				bodyStyle : 'padding:0 10px 0;',
				columnWidth : '500px',
				items : [],
				id_grupo : 0,
				collapsible : true
			}]
		}],
		
	ActSave:'../../sis_contabilidad/control/DocCompraVentaForm/reporteLCV',
	
    successSave :function(resp){
       Phx.CP.loadingHide();
       var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
        if (reg.ROOT.error) {
            alert('error al procesar');
            return
       } 
       
       var nomRep = reg.ROOT.detalle.archivo_generado;
        if(Phx.CP.config_ini.x==1){  			
        	nomRep = Phx.CP.CRIPT.Encriptar(nomRep);
        }
       
        if(this.Cmp.formato_reporte.getValue()=='pdf'){
        	window.open('../../../lib/lib_control/Intermediario.php?r='+nomRep+'&t='+new Date().toLocaleTimeString())
        }
        else{
        	window.open('../../../reportes_generados/'+nomRep+'?t='+new Date().toLocaleTimeString())
        }
       
	}
})
</script>