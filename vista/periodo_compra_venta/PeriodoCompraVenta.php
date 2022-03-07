<?php
/**
*@package pXP
*@file gen-PeriodoCompraVenta.php
*@author  (admin)
*@date 24-08-2015 14:16:54
*@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
*/

header("content-type: text/javascript; charset=UTF-8");
?>

<script>
Phx.vista.PeriodoCompraVenta=Ext.extend(Phx.gridInterfaz,{

	constructor:function(config){
		this.maestro=config.maestro;
		this.initButtons=[this.cmbGestion];
    	//llama al constructor de la clase padre
		Phx.vista.PeriodoCompraVenta.superclass.constructor.call(this,config);
		this.bloquearOrdenamientoGrid();
		this.cmbGestion.on('select', function(){
		    if(this.validarFiltros()){
                  this.capturaFiltros();
            }
		},this);


		this.init();
        this.iniciarEventos();
		this.addButton('btnGenPer',
            {
                text: 'Generar Periodos',
                iconCls: 'bchecklist',
                disabled: false,
                handler: this.generarPeriodos,
                tooltip: '<b>Generar Periodos</b><br/>GEnerar periodos de compra y venta para el depto y gestion selecionados'
            }
        );

        this.addButton('btnCerrarPeriodo', {
				text : 'Cerrar',
				iconCls : 'block',
				disabled : true,
				handler : this.onCerrarPeriodo,
				tooltip : '<b>Cerrar</b> Cerrar el periodo , nadie puede insertar ni modificar documentos'
			});

		this.addButton('btnCerrarParPeriodo', {
				text : 'Cerrar Parcial',
				iconCls : 'block',
				disabled : true,
				handler : this.onCerrarParPeriodo,
				tooltip : '<b>Cerrar Parcialmente</b> Cierra para todos pero permite al personal de contabilidad realizar registros'
			});

		this.addButton('btnAbrirPeriodo', {
				text : 'Abrir',
				iconCls : 'bunlock',
				disabled : true,
				handler : this.onAbrirPeriodo,
				tooltip : '<b>Abrir</b>Abrir periodo para permitir registros de documentos'
			});

        this.addButton('btnLogPeriodo', {
            text : 'Historial de cambios.',
            iconCls : 'blist',
            disabled : true,
            handler : this.logPeriodo,
            tooltip : '<b>Historial</b> de cierre, apertura y apertura temporal del periodo.'
        });


		/*Aumentando para los botones de comisionistas (Ismael Valdivia 11/02/2022)*/
				this.addButton('btn1', {
					text : '               ',
					//iconCls : 'block',
					disabled : true,
					//handler : this.onCerrarPeriodoComisionistas,
					//tooltip : '<b>Cerrar</b> Cerrar el periodo , nadie puede insertar ni modificar documentos'
				});

			this.addButton('btnCerrarPeriodoComisionistas', {
				text : 'Cerrar Comisionistas',
				iconCls : 'block',
				disabled : true,
				id : 'botonComisionistas',
				handler : this.onCerrarPeriodoComisionistas,
				tooltip : '<b>Cerrar</b> Cerrar el periodo para comisionistas'
			});


			this.addButton('btnAbrirPeriodoComisionistas', {
				text : 'Abrir Comisionistas',
				iconCls : 'bunlock',
				disabled : true,
				id : 'botonComisionistasAbierto',
				handler : this.onAbrirPeriodoComisionistas,
				tooltip : '<b>Abrir</b> Abrir el periodo para comisionistas'
			});

			this.addButton('btnLogPeriodoComisionistas', {
					text : 'Historial de cambios Comisionistas.',
					iconCls : 'blist',
					disabled : true,
					id : 'logComisionistas',
					handler : this.logPeriodoComisionistas,
					tooltip : '<b>Historial</b> de cierre, apertura y apertura temporal del periodo de Comisionistas.'
			});
		/***************************************************************************/



		this.bloquearMenus();
		//this.load({params:{start:0, limit:this.tam_pag}})
	},




	cmbGestion: new Ext.form.ComboBox({
				fieldLabel: 'Gestion',
				allowBlank: false,
				emptyText:'Gestion...',
				blankText: 'Año',
				store:new Ext.data.JsonStore(
				{
					url: '../../sis_parametros/control/Gestion/listarGestion',
					id: 'id_gestion',
					root: 'datos',
					sortInfo:{
						field: 'gestion',
						direction: 'DESC'
					},
					totalProperty: 'total',
					fields: ['id_gestion','gestion'],
					// turn on remote sorting
					remoteSort: true,
					baseParams:{par_filtro:'gestion'}
				}),
				valueField: 'id_gestion',
				triggerAction: 'all',
				displayField: 'gestion',
			    hiddenName: 'id_gestion',
    			mode:'remote',
				pageSize:5,
				queryDelay:500,
				listWidth:'250',
				width:80
			}),

	Atributos:[
		{
			//configuracion del componente
			config:{
					labelSeparator:'',
					inputType:'hidden',
					name: 'id_periodo_compra_venta'
			},
			type:'Field',
			form:true
		},
		{
			//configuracion del componente
			config:{
					labelSeparator:'',
					inputType:'hidden',
					name: 'id_periodo'
			},
			type:'Field',
			form:true
		},
		{
			//configuracion del componente
			config:{
					labelSeparator:'',
					inputType:'hidden',
					name: 'id_depto'
			},
			type:'Field',
			form:true
		},
		{
			config:{
				name: 'mes',
				fieldLabel: 'Periodo',
				allowBlank: false,
				anchor: '80%',
				gwidth: 100,
				renderer:function(value, cmb, i){
                    return String.format('<b>{0}</b>',value);
                }
			},
				type:'TextField',
				filters:{pfiltro:'per.periodo',type:'numeric'},
				id_grupo:1,
				grid:true,
				form:false
		},
		{
			config:{
				name: 'estado',
				fieldLabel: 'estado',
				allowBlank: false,
				anchor: '80%',
				gwidth: 100,
				maxLength:20,
                //breydi.vasquez 10/12/2019
                renderer:function (value, rec, i ){
                        var llave = '';
                        switch(value){
                            case 'cerrado': llave = 'Cerrado'
                            break;
                            case 'abierto': llave = 'Abierto'
                            break;
                            case 'cerrado_parcial': llave = 'Cierre Parcial'
                            break;
                        }
                        return String.format('<b>{0}</b>', llave);
                    }
			},
				type:'TextField',
				filters:{pfiltro:'pcv.estado',type:'string'},
				id_grupo:1,
				grid:true,
				form:false
		},
		{
			config:{
				name: 'fecha_ini',
				fieldLabel: 'Inicio',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
							format: 'd/m/Y',
							renderer:function (value,p,record){return value?value.dateFormat('d/m/Y'):''}
			},
				type:'DateField',
				filters:{pfiltro:'per.fecha_ini',type:'date'},
				id_grupo:1,
				grid:true,
				form:false
		},
		{
			config:{
				name: 'fecha_fin',
				fieldLabel: 'Fin',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
							format: 'd/m/Y',
							renderer:function (value,p,record){return value?value.dateFormat('d/m/Y'):''}
			},
				type:'DateField',
				filters:{pfiltro:'per.fecha_fin',type:'date'},
				id_grupo:1,
				grid:true,
				form:false
		},
		{
			config:{
				name: 'cantidad_cerrado',
				fieldLabel: 'Cant Cerrados',
				allowBlank: false,
				anchor: '80%',
				gwidth: 110,
                renderer: function(value, rec, i){
                    return String.format('<div style="text-align: center; font-size:12px; font-weight:bold;">{0}</div>', value);
                }
			},
				type:'NumberField',
				id_grupo:1,
				grid:true,
				form:false
		},
		{
			config:{
				name: 'cantidad_abierto',
				fieldLabel: 'Cant. Abiertos',
				allowBlank: false,
				anchor: '80%',
				gwidth: 110,
                renderer: function(value, rec, i){
                    return String.format('<div style="text-align: center; font-size:12px; font-weight:bold;">{0}</div>', value);
                }
			},
				type:'NumberField',
				id_grupo:1,
				grid:true,
				form:false
		},
		{
			config:{
				name: 'cantidad_cerrado_parcial',
				fieldLabel: 'Cant. Cerrados Parcial',
				allowBlank: false,
				anchor: '100%',
				gwidth: 110,
                renderer: function(value, rec, i){
                    return String.format('<div style="text-align: center; font-size:12px; font-weight:bold;">{0}</div>', value);
                }
			},
				type:'NumberField',
				id_grupo:1,
				grid:true,
				form:false
		},

		/*Aqui Para comisionistas 24/02/2022 (Ismael Valdivia)*/
		{
			config:{
				name: 'estado_comisionistas',
				fieldLabel: 'Estado Comisionistas',
				allowBlank: false,
				anchor: '80%',
				gwidth: 150,
				maxLength:20,
                //breydi.vasquez 10/12/2019
                renderer:function (value, rec, i ){
                        var llave = '';
                        switch(value){
                            case 'cerrado': llave = 'Cerrado'
                            break;
                            case 'abierto': llave = 'Abierto'
                            break;
                        }
                        return String.format('<b style="color:blue;">{0}</b>', llave);
                    }
			},
				type:'TextField',
				filters:{pfiltro:'pcv.estado',type:'string'},
				id_grupo:1,
				grid:true,
				form:false
		},
		{
			config:{
				name: 'cantidad_abierto_comisionistas',
				fieldLabel: 'Cant Abiertos Comisionistas',
				allowBlank: false,
				anchor: '80%',
				gwidth: 170,
                renderer: function(value, rec, i){
                    return String.format('<div style="color:blue; text-align: center; font-size:12px; font-weight:bold;">{0}</div>', value);
                }
			},
				type:'NumberField',
				id_grupo:1,
				grid:true,
				form:false
		},
		{
			config:{
				name: 'cantidad_cerrado_comisionistas',
				fieldLabel: 'Cant. CerradosComisionistas',
				allowBlank: false,
				anchor: '80%',
				gwidth: 170,
                renderer: function(value, rec, i){
                    return String.format('<div style="color:blue; text-align: center; font-size:12px; font-weight:bold;">{0}</div>', value);
                }
			},
				type:'NumberField',
				id_grupo:1,
				grid:true,
				form:false
		},
		/*********************************************************/
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
				filters:{pfiltro:'pcv.fecha_reg',type:'date'},
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
				type:'Field',
				filters:{pfiltro:'usu1.cuenta',type:'string'},
				id_grupo:1,
				grid:true,
				form:false
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
				filters:{pfiltro:'pcv.estado_reg',type:'string'},
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
				filters:{pfiltro:'pcv.fecha_mod',type:'date'},
				id_grupo:1,
				grid:true,
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
				filters:{pfiltro:'pcv.usuario_ai',type:'string'},
				id_grupo:1,
				grid:true,
				form:false
		},

		{
			config:{
				name: 'id_usuario_ai',
				fieldLabel: '',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:4
			},
				type:'Field',
				filters:{pfiltro:'pcv.id_usuario_ai',type:'numeric'},
				id_grupo:1,
				grid:false,
				form:false
		}
	],
	tam_pag:50,
	title:'PCV',
	ActSave:'../../sis_contabilidad/control/PeriodoCompraVenta/insertarPeriodoCompraVenta',
	ActDel:'../../sis_contabilidad/control/PeriodoCompraVenta/eliminarPeriodoCompraVenta',
	ActList:'../../sis_contabilidad/control/PeriodoCompraVenta/listarPeriodoCompraVenta',
	id_store:'id_periodo_compra_venta',
	fields: [
		{name:'id_periodo_compra_venta', type: 'numeric'},
		{name:'estado', type: 'string'},
		{name:'id_periodo', type: 'numeric'},
		{name:'estado_reg', type: 'string'},
		{name:'id_depto', type: 'numeric'},
		{name:'id_usuario_ai', type: 'numeric'},
		{name:'fecha_reg', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'usuario_ai', type: 'string'},
		{name:'id_usuario_reg', type: 'numeric'},
		{name:'id_usuario_mod', type: 'numeric'},
		{name:'fecha_mod', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'usr_reg', type: 'string'},
		{name:'usr_mod', type: 'string'},'periodo','id_gestion',
		{name:'fecha_ini', type: 'date',dateFormat:'Y-m-d'},
		{name:'fecha_fin', type: 'date',dateFormat:'Y-m-d'},
        {name:'mes', type:'string'},
        {name:'cantidad_cerrado', type:'string'},
		{name:'cantidad_abierto', type:'string'},
		{name:'cantidad_cerrado_parcial', type:'string'},
		{name:'cantidad_abierto_comisionistas', type:'string'},
		{name:'cantidad_cerrado_comisionistas', type:'string'},
        {name:'estado_comisionistas', type:'string'},
	],
	sortInfo:{
		field: 'per.periodo',
		direction: 'ASC'
	},

	validarFiltros:function(){
        if(this.cmbGestion.isValid()){
            return true;
        }
        else{
            return false;
        }

    },

    capturaFiltros:function(combo, record, index){
        //this.desbloquearOrdenamientoGrid();
        this.store.baseParams.id_gestion=this.cmbGestion.getValue();
        this.load();


    },
    onButtonAct:function(){

        if(!this.validarFiltros()){
            alert('Especifique el año antes')
         }
        else{
            this.store.baseParams.id_gestion=this.cmbGestion.getValue();
            Phx.vista.PeriodoCompraVenta.superclass.onButtonAct.call(this);
        }
    },

	onReloadPage:function(m){

		this.maestro=m;
		this.store.baseParams={id_depto: this.maestro.id_depto};
        Ext.Ajax.request({
                    url:'../../sis_parametros/control/Gestion/obtenerGestionByFecha',
                    params:{fecha:new Date()},
                    success:function(resp){
                        var reg =  Ext.decode(Ext.util.Format.trim(resp.responseText));
                        this.cmbGestion.setValue(reg.ROOT.datos.id_gestion);
                        this.cmbGestion.setRawValue(reg.ROOT.datos.anho);
                        this.store.baseParams.id_gestion=reg.ROOT.datos.id_gestion;
                        this.load({params:{start:0, limit:this.tam_pag}});
                    },
                    failure: this.conexionFailure,
                    timeout:this.timeout,
                    scope:this
                });
		/*if(!this.validarFiltros()){
            alert('Especifique el año antes')
         }
        else{*/
           this.store.baseParams.id_gestion=this.cmbGestion.getValue();
           this.load({params:{start:0, limit:50}})
        //}

	},

	generarPeriodos: function(){
		   var id_gestion = this.cmbGestion.getValue(),
		       id_depto = this.maestro.id_depto;

		   if(!this.validarFiltros()){
	            alert('Especifique el año antes');
	            return;
	        }

	        if(!id_depto){
	        	alert('selecione un departamento antes');
	        	return;
	        }

		    Phx.CP.loadingShow();
			Ext.Ajax.request({
				url:'../../sis_contabilidad/control/PeriodoCompraVenta/generarPeriodosCompraVenta',
				params:{
					id_gestion: id_gestion,
					id_depto: this.maestro.id_depto
				},
				success: function(resp){
					Phx.CP.loadingHide();
					var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
					if (reg.ROOT.error) {
						Ext.Msg.alert('Error','Validación no realizada: '+reg.ROOT.error)
					} else {
						this.reload();
						Ext.Msg.alert('Mensaje','Proceso ejecutado con éxito')
					}
				},
				failure: this.conexionFailure,
				timeout: this.timeout,
				scope:this
			});

	},


	onCerrarPeriodo: function(){this.cerrarAbrirPeriodo('cerrar');},
	onCerrarParPeriodo: function(){this.cerrarAbrirPeriodo('cerrar_parcial')},
	onAbrirPeriodo: function(){this.cerrarAbrirPeriodo('abrir')},

	/*Aumentando para comisionistas Ismael Valdivia (14/02/2022)*/
	onCerrarPeriodoComisionistas: function(){this.cerrarAbrirPeriodoComisionistas('cerrar');},
	onAbrirPeriodoComisionistas: function(){this.cerrarAbrirPeriodoComisionistas('abrir')},
	/***********************************************************/

	onSubmitCierre: function (data) {
			var rec = this.sm.getSelected();
			var obs = this.formObs.getForm().findField('observacion').getValue();
			if ((obs=='') || (obs.length<3)) {
				alert('Registre su observacion previamente')
			}else {
				  this.windowObser.hide();
				if(rec){
				 Phx.CP.loadingShow();
			 Ext.Ajax.request({
					 url : '../../sis_contabilidad/control/PeriodoCompraVenta/cerrarAbrirPeriodo',
					 params : {
						 id_periodo_compra_venta : rec.data.id_periodo_compra_venta,
						 tipo: data.tipoEvent,
						 observacion: obs
					 },
					 success : function(resp){
						 Phx.CP.loadingHide();
						 var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
						 if (reg.ROOT.error) {
							 Ext.Msg.alert('Error','no se pudo proceder: '+reg.ROOT.error)
						 } else {
							 this.reload();
							 Ext.Msg.alert('Mensaje','Proceso ejecutado con éxito')
						 }
					 },
					 failure: this.conexionFailure,
					 timeout: this.timeout,
					 scope:this
				 });
			 }
			}
	},

	onSubmitCierreComisionistas: function (data) {
			var rec = this.sm.getSelected();
			var obs = this.formObsComisionistas.getForm().findField('observacion').getValue();
			if ((obs=='') || (obs.length<3)) {
				alert('Registre su observacion previamente')
			}else {
				  this.windowObserComisionistas.hide();
				if(rec){
				 Phx.CP.loadingShow();
			 Ext.Ajax.request({
					 url : '../../sis_contabilidad/control/PeriodoCompraVenta/cerrarAbrirPeriodoComisionistas',
					 params : {
						 id_periodo_compra_venta : rec.data.id_periodo_compra_venta,
						 tipo: data.tipoEvent,
						 observacion: obs
					 },
					 success : function(resp){
						 Phx.CP.loadingHide();
						 var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
						 if (reg.ROOT.error) {
							 Ext.Msg.alert('Error','no se pudo proceder: '+reg.ROOT.error)
						 } else {
							 this.reload();
							 Ext.Msg.alert('Mensaje','Proceso ejecutado con éxito')
						 }
					 },
					 failure: this.conexionFailure,
					 timeout: this.timeout,
					 scope:this
				 });
			 }
			}
	},

	/*Aumentando para los comisionistas 14/02/2022*/
	cerrarAbrirPeriodoComisionistas: function(estado){
		this.creaFormObservacionComisionistas(estado);
		this.windowObserComisionistas.show();
		},


	creaFormObservacionComisionistas: function (estado) {

				this.formObsComisionistas = new Ext.form.FormPanel({
						id: this.idContenedor + '_OBSERVA',
						bodyStyle: 'padding: 10px;',
						items: [
								new Ext.form.TextArea({
										fieldLabel: 'Observacion',
										name: 'observacion',
										height: 60,
										allowBlank: false,
										width: '95%',
								})
						],
						autoScroll: false,
						autoDestroy: true,
						autoScroll: true
				});


				// Definicion de la ventana que contiene al formulario
				this.windowObserComisionistas = new Ext.Window({
						// id:this.idContenedor+'_W',
						title: '<h6 style="font-size:11pt;">Registre previamente su observacion</h6>',
						modal: true,
						width: 400,
						height: 200,
						bodyStyle: 'padding: 10px;',
						layout: 'fit',
						hidden: true,
						autoScroll: false,
						maximizable: true,
						buttons: [{
								text: 'Guardar',
								arrowAlign: 'bottom',
								handler: this.onSubmitCierreComisionistas,
								argument: {
										'news': false
								},
								scope: this,
								tipoEvent: estado

						},
								{
										text: 'Declinar',
										handler: this.onDeclinarCierreComisionistas,
										scope: this
								}],
						items: this.formObsComisionistas,
						autoDestroy: true,
						closeAction: 'hide'
				});
		},

  /**********************************************/

	cerrarAbrirPeriodo: function(estado){
		this.creaFormObservacion(estado);
		this.windowObser.show();
	},

        creaFormObservacion: function (estado) {

            this.formObs = new Ext.form.FormPanel({
                id: this.idContenedor + '_OBSERVA',
								bodyStyle: 'padding: 10px;',
                items: [
                    new Ext.form.TextArea({
                        fieldLabel: 'Observacion',
                        name: 'observacion',
                        height: 60,
                        allowBlank: false,
                        width: '95%',
                    })
                ],
                autoScroll: false,
                autoDestroy: true,
                autoScroll: true
            });


            // Definicion de la ventana que contiene al formulario
            this.windowObser = new Ext.Window({
                // id:this.idContenedor+'_W',
                title: '<h6 style="font-size:11pt;">Registre previamente su observacion</h6>',
                modal: true,
                width: 400,
                height: 200,
                bodyStyle: 'padding: 10px;',
                layout: 'fit',
                hidden: true,
                autoScroll: false,
                maximizable: true,
                buttons: [{
                    text: 'Guardar',
                    arrowAlign: 'bottom',
                    handler: this.onSubmitCierre,
                    argument: {
                        'news': false
                    },
                    scope: this,
										tipoEvent: estado

                },
                    {
                        text: 'Declinar',
                        handler: this.onDeclinarCierre,
                        scope: this
                    }],
                items: this.formObs,
                autoDestroy: true,
                closeAction: 'hide'
            });
        },

	onDeclinarCierre: function () {
          this.windowObser.hide();
  },

	onDeclinarCierreComisionistas: function () {
          this.windowObserComisionistas.hide();
  },
	preparaMenu: function(n) {
		var tb = Phx.vista.PeriodoCompraVenta.superclass.preparaMenu.call(this);
        // modificado breydi.vasquez 10/12/2019
        var rec = this.getSelectedData();

        switch (rec.estado) {
            case 'cerrado':
                this.getBoton('btnCerrarPeriodo').setDisabled(true);
                this.getBoton('btnCerrarParPeriodo').setDisabled(false);
                this.getBoton('btnAbrirPeriodo').setDisabled(false);
                break;
            case 'abierto':
                this.getBoton('btnCerrarPeriodo').setDisabled(false);
                this.getBoton('btnCerrarParPeriodo').setDisabled(false);
                this.getBoton('btnAbrirPeriodo').setDisabled(true);
                break;
            case 'cerrado_parcial':
                this.getBoton('btnCerrarPeriodo').setDisabled(false);
                this.getBoton('btnCerrarParPeriodo').setDisabled(true);
                this.getBoton('btnAbrirPeriodo').setDisabled(false);
                break;
        }

				if (rec.estado == 'cerrado') {
					switch (rec.estado_comisionistas) {
	            case 'cerrado':
									Ext.getCmp('botonComisionistas').setDisabled(true);
									Ext.getCmp('botonComisionistasAbierto').setDisabled(false);
	                break;
	            case 'abierto':
	                Ext.getCmp('botonComisionistas').setDisabled(false);
	                Ext.getCmp('botonComisionistasAbierto').setDisabled(true);
	                break;
	        }
				}
				this.getBoton('btnLogPeriodo').setDisabled(false);
				Ext.getCmp('logComisionistas').setDisabled(false);
				//this.getBoton('btnCerrarPeriodoComisionistas').setDisabled(false);
				//this.getBoton('btnAbrirPeriodoComisionistas').setDisabled(false);
		   	this.getBoton('btn1').setDisabled(true);

  		return tb;
	},
	liberaMenu: function() {
		var tb = Phx.vista.PeriodoCompraVenta.superclass.liberaMenu.call(this);
		this.getBoton('btnCerrarParPeriodo').setDisabled(true);
	   	this.getBoton('btnCerrarPeriodo').setDisabled(true);
	   	this.getBoton('btnAbrirPeriodo').setDisabled(true);
			this.getBoton('btnLogPeriodo').setDisabled(true);
			Ext.getCmp('logComisionistas').setDisabled(true);
			//this.getBoton('btnCerrarPeriodoComisionistas').setDisabled(true);
			Ext.getCmp('botonComisionistas').setDisabled(true);
			Ext.getCmp('botonComisionistasAbierto').setDisabled(true);
      //this.getBoton('btnAbrirPeriodoComisionistas').setDisabled(true);
			this.getBoton('btn1').setDisabled(true);

			Ext.getCmp('botonComisionistas').el.dom.onmouseover = function () {
				Ext.getCmp('botonComisionistas').btnEl.dom.style.backgroundColor = '#0EB9B2';
				Ext.getCmp('botonComisionistas').btnEl.dom.style.color = '#ffffff';
				Ext.getCmp('botonComisionistas').btnEl.dom.style.fontWeight = 'bold';

			};

			Ext.getCmp('botonComisionistas').el.dom.onmouseout = function () {
				Ext.getCmp('botonComisionistas').btnEl.dom.style.backgroundColor = '';
				Ext.getCmp('botonComisionistas').btnEl.dom.style.color = '';
				Ext.getCmp('botonComisionistas').btnEl.dom.style.fontWeight = '';
			};

			Ext.getCmp('botonComisionistasAbierto').el.dom.onmouseover = function () {
				Ext.getCmp('botonComisionistasAbierto').btnEl.dom.style.backgroundColor = '#0EB9B2';
				Ext.getCmp('botonComisionistasAbierto').btnEl.dom.style.color = '#ffffff';
				Ext.getCmp('botonComisionistasAbierto').btnEl.dom.style.fontWeight = 'bold';

			};

			Ext.getCmp('botonComisionistasAbierto').el.dom.onmouseout = function () {
				Ext.getCmp('botonComisionistasAbierto').btnEl.dom.style.backgroundColor = '';
				Ext.getCmp('botonComisionistasAbierto').btnEl.dom.style.color = '';
				Ext.getCmp('botonComisionistasAbierto').btnEl.dom.style.fontWeight = '';
			};

			Ext.getCmp('logComisionistas').el.dom.onmouseover = function () {
				Ext.getCmp('logComisionistas').btnEl.dom.style.backgroundColor = '#0EB9B2';
				Ext.getCmp('logComisionistas').btnEl.dom.style.color = '#ffffff';
				Ext.getCmp('logComisionistas').btnEl.dom.style.fontWeight = 'bold';

			};

			Ext.getCmp('logComisionistas').el.dom.onmouseout = function () {
				Ext.getCmp('logComisionistas').btnEl.dom.style.backgroundColor = '';
				Ext.getCmp('logComisionistas').btnEl.dom.style.color = '';
				Ext.getCmp('logComisionistas').btnEl.dom.style.fontWeight = '';
			};


	},


	bdel: false,
	bsave: false,
	bnew:  false,
	bedit:  false,

    //ini: add breydi.vasquez 10/12/2019, log de cambios de estado.

    logPeriodo: function(){
            var rec = this.getSelectedData();
            var NumSelect=this.sm.getCount();
            if (NumSelect != 0 ){
                Phx.CP.loadWindows('../../../sis_contabilidad/vista/periodo_compra_venta/LogPeriodoCompraMod.php',
                    `<h4 style="font-weight:bold;font-size:15;color:#15428b;">DETALLE MODIFICACIONES: ${rec.mes.toLocaleUpperCase()}</h4>`,
                    {
                        width: '70%',
                        height: '70%'
                    }, rec, this.idContenedor, 'LogPeriodoCompraMod');
            }else{
                Ext.MessageBox.alert('Alerta', 'Antes debe seleccionar un item.');
            }
    },
    //fin

		/*Interfaz para mostrar el log de los cambios en botonComisionistas*/
		logPeriodoComisionistas: function(){
            var rec = this.getSelectedData();
            var NumSelect=this.sm.getCount();
						console.log("aqui llega los datos",rec);
            if (NumSelect != 0 ){
							Phx.CP.loadWindows('../../../sis_contabilidad/vista/periodo_compra_venta/PanelLogComisionistas.php',
							'', {
								width:'90%',
								height:'90%'
								}, {
									id_periodo_compra_venta: rec.id_periodo_compra_venta,
									link: true
								},
								this.idContenedor,
								'PanelLogComisionistas'
							);
            }else{
                Ext.MessageBox.alert('Alerta', 'Antes debe seleccionar un item.');
            }
    }
		/*******************************************************************/


})
</script>
