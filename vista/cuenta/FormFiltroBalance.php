<?php
/**
*@package pXP
*@file    SolModPresupuesto.php
*@author  Rensi Arteaga Copari 
*@date    30-01-2014
*@description permites subir archivos a la tabla de documento_sol
*/
header("content-type: text/javascript; charset=UTF-8");
?>

<script>
Phx.vista.FormFiltroBalance=Ext.extend(Phx.frmInterfaz,{
    constructor:function(config)
    {   
    	this.panelResumen = new Ext.Panel({html:''});
    	this.Grupos = [{

	                    xtype: 'fieldset',
	                    border: false,
	                    autoScroll: true,
	                    layout: 'form',
	                    items: [],
	                    id_grupo: 0
				               
				    },
				     this.panelResumen
				    ];
				    
        Phx.vista.FormFiltroBalance.superclass.constructor.call(this,config);
        this.init(); 
        this.iniciarEventos();

        //Botón para Imprimir en formato APS
        this.addButton('btnSincronizar', {
            text : 'Reporte APS',
            iconCls : 'bdocuments',
            disabled : false,
            handler : this.reporteAPS,
            tooltip : '<b>Genera Reporte en formato APS</b>'
        });
        
    },
    
    Atributos:[
          
	   	   {
				config:{
					name: 'desde',
					fieldLabel: 'Desde',
					allowBlank: true,
					format: 'd/m/Y',
					width: 150
				},
				type: 'DateField',
				id_grupo: 0,
				form: true
		  },
		  {
				config:{
					name: 'hasta',
					fieldLabel: 'Hasta',
					allowBlank: true,
					format: 'd/m/Y',
					width: 150
				},
				type: 'DateField',
				id_grupo: 0,
				form: true
		  },
		  {
   			config:{
                name: 'id_deptos',
                fieldLabel: 'Depto',
                typeAhead: false,
                forceSelection: true,
                allowBlank: false,
                disableSearchButton: true,
                emptyText: 'Depto Contable',
                store: new Ext.data.JsonStore({
                    url: '../../sis_parametros/control/Depto/listarDeptoFiltradoDeptoUsuario',
                    id: 'id_depto',
					root: 'datos',
					sortInfo:{
						field: 'deppto.nombre',
						direction: 'ASC'
					},
					totalProperty: 'total',
					fields: ['id_depto','nombre','codigo'],
					// turn on remote sorting
					remoteSort: true,
					baseParams: { par_filtro:'deppto.nombre#deppto.codigo', estado:'activo', codigo_subsistema: 'CONTA'}
                }),
                valueField: 'id_depto',
   				displayField: 'codigo',
   				hiddenName: 'id_depto',
                enableMultiSelect: true,
                triggerAction: 'all',
                lazyRender: true,
                mode: 'remote',
                pageSize: 20,
                queryDelay: 200,
                anchor: '80%',
                listWidth:'280',
                resizable:true,
                minChars: 2
            },
   			//type:'TrigguerCombo',
   			type:'AwesomeCombo',
   			id_grupo:0,
   			form:true
         },
	     {
	       		config:{
	       			name:'nivel',
	       			fieldLabel:'Nivel',
	       			allowBlank:false,
	       			emptyText:'nivel...',
	       			typeAhead: true,
	       		    triggerAction: 'all',
	       		    lazyRender:true,
	       		    mode: 'local',
	       		    valueField: 'autentificacion',
	       		    store:[1,2,3,4,5,6,7,8]
	       		    
	       		},
	       		type:'ComboBox',
	       		id_grupo:0,
	       		form:true
	      },
	      {
	       		config:{
	       			name: 'incluir_cierre',
	       			qtip : 'Incluir los comprobantes de cierre en el balance',
	       			fieldLabel: 'Incluir cierre',
	       			allowBlank: false,
	       			emptyText:'Tipo...',
	       			typeAhead: true,
	       		    triggerAction: 'all',
	       		    lazyRender:true,
	       		    mode: 'local',
	       		    gwidth: 100,
	       		    store:['no','balance','resultado','todos']
	       		},
	       		type:'ComboBox',
	       		id_grupo:0,
	       		valorInicial: 'no',
	       		grid:true,
	       		form:true
	      },
	      {
	       		config:{
	       			name: 'incluir_sinmov',
	       			qtip : 'Incluir solo cuentas con movimiento?',
	       			fieldLabel: 'Solo con movimiento',
	       			allowBlank: false,
	       			emptyText:'Tipo...',
	       			typeAhead: true,
	       		    triggerAction: 'all',
	       		    lazyRender:true,
	       		    mode: 'local',
	       		    gwidth: 100,
	       		    store:['no','si']
	       		},
	       		type:'ComboBox',
	       		id_grupo:0,
	       		valorInicial: 'no',
	       		grid:true,
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
                    data :	[['pdf','PDF'],
                        ['csv','CSV']]
                }),
                valueField:'ID',
                displayField:'valor',
                gwidth: 100
            },
            type:'ComboBox',
            id_grupo:0,
            grid:true,
            form:true
        }
    ],
    topBar : true,
    botones : false,
    labelSubmit: 'Generar',
    clsSubmit: 'bprint',
    tooltipSubmit : '<b>Generar Balance General</b>',
    title: 'Filtro de mayores',
    // Funcion guardar del formulario
    onSubmit: function(o) {
    	var me = this;
    	if (me.form.getForm().isValid()) {
             var parametros = me.getValForm()
             Phx.CP.loadingShow();
             
             var deptos = this.Cmp.id_deptos.getValue('object');
             console.log('deptos',deptos)
             var sw = 0, codigos = ''
             deptos.forEach(function(entry) {
			    if(sw == 0){
			    	codigos = entry.codigo;
			    }
			    else{
			    	codigos = codigos + ', '+ entry.codigo;
			    }
			    sw = 1;
			});
             
             Ext.Ajax.request({
						url : '../../sis_contabilidad/control/Cuenta/reporteBalanceGeneral',
						params : Ext.apply(parametros,{'codigos': codigos, 'tipo_balance':'general'}),
						success : this.successExport,
						failure : this.conexionFailure,
						timeout : this.timeout,
						scope : this
					})
                    
        }

    },
    reporteAPS: function(){//fRnk: reporte APS, modificación de la cabecera de los reportes
        var me = this;
        if (me.form.getForm().isValid()) {
            var parametros = me.getValForm()
            Phx.CP.loadingShow();

            var deptos = this.Cmp.id_deptos.getValue('object');
            var sw = 0, codigos = '';
            deptos.forEach(function(entry) {
                if(sw == 0){
                    codigos = entry.codigo;
                }
                else{
                    codigos = codigos + ', '+ entry.codigo;
                }
                sw = 1;
            });

            Ext.Ajax.request({
                url : '../../sis_contabilidad/control/Cuenta/reporteBalanceGeneralAPS',
                params : Ext.apply(parametros,{'codigos': codigos, 'tipo_balance':'general'}),
                success : this.successExport,
                failure : this.conexionFailure,
                timeout : this.timeout,
                scope : this
            })
        }
    },
})    
</script>