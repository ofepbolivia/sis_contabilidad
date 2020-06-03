<?php
/**
*@package pXP
*@file    FormFiltroReporteLibroMayor.php
*@author  Ismael Valdivia
*@date    01-12-2019
*@description permite filtrar datos para sacar el reporte del libro mayor
*/
header("content-type: text/javascript; charset=UTF-8");
?>

<script>
Phx.vista.FormFiltroReporteLibroMayor=Ext.extend(Phx.frmInterfaz,{
    constructor:function(config)
    {

        Phx.vista.FormFiltroReporteLibroMayor.superclass.constructor.call(this,config);
        this.init();
        this.iniciarEventos();

        if(config.detalle){

  			//cargar los valores para el filtro
  			this.loadForm({data: config.detalle});
  			var me = this;
  			setTimeout(function(){
  				me.onSubmit()
  			}, 1500);

  		}
      /*Fondo de los filtros*/
      this.regiones[0].body.dom.style.background='#dfe8f6';


    },


    Grupos: [
  			{
  					layout: 'column',
            xtype: 'fieldset',
            region: 'north',
            collapseFirst : false,
            width: '100%',
            autoScroll:true,
            padding: '0 0 0 0',
  					items: [
              {
               bodyStyle: 'padding-right:0px;',
               autoHeight: true,
               border: false,
               items:[
                  {
                   xtype: 'fieldset',
                   frame: true,
                   border: false,
                   layout: 'form',
                   style: {
                          height:'100px',
                          width:'100px',
                          backgroundColor:'#dfe8f6'
                       },
                   padding: '0 0 0 0',
                   bodyStyle: 'padding-left:0px;',
                   id_grupo: 0,
                   items: [],
                }]
            },
            {
             bodyStyle: 'padding-right:0px;',
             autoHeight: true,
             border: false,
             items:[
                {
                 xtype: 'fieldset',
                 frame: true,
                 border: false,
                 layout: 'form',
                 style: {
                        height:'100px',
                        width:'100px',
                        backgroundColor:'#dfe8f6'
                     },
                 padding: '0 0 0 0',
                 bodyStyle: 'padding-left:0px;',
                 id_grupo: 1,
                 items: [],
              }]
          },
            {
             bodyStyle: 'padding-right:0px;',
             border: false,
             autoHeight: true,
             items: [{
                   xtype: 'fieldset',
                   frame: true,
                   layout: 'form',
                   style: {
                          height:'100px',
                          width:'100px',
                          backgroundColor:'#dfe8f6',
                         },
                   border: false,
                   padding: '0 0 0 0',
                   bodyStyle: 'padding-left:0px;',
                   id_grupo: 2,
                   items: [],
                }]
            },
  					]
  			}
  	],


    Atributos:[

           {
	   			config:{
	   				name : 'id_gestion',
	   				origen : 'GESTION',
	   				fieldLabel : 'Gestion',
	   				gdisplayField: 'desc_gestion',
	   				allowBlank : false,
	   				width: 150
	   			},
	   			type : 'ComboRec',
	   			id_grupo : 0,
	   			form : true
	   	   },
	   	   {
				config:{
					name: 'desde',
					fieldLabel: 'Desde',
					allowBlank: false,
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
					allowBlank: false,
					format: 'd/m/Y',
					width: 150
				},
				type: 'DateField',
				id_grupo: 0,
				form: true
		  },
    {
 			config:{
 				sysorigen: 'sis_contabilidad',
     		    name: 'id_cuenta',
 				origen: 'CUENTA',
 				allowBlank: false,
 				fieldLabel: 'Cuenta',
 				gdisplayField: 'desc_cuenta',
 				baseParams: { sw_transaccional: 'movimiento' },
 				width: 150
     	     },
 			type: 'ComboRec',
 			id_grupo: 1,
 			form: true
   	},
	   	{
   			config:{
   				sysorigen: 'sis_contabilidad',
       		    name: 'id_auxiliar',
   				origen: 'AUXILIAR',
   				allowBlank: true,
   				gdisplayField: 'desc_auxiliar',
   				fieldLabel: 'Auxiliar',
   				width: 150
       	     },
   			type:'ComboRec',
   			id_grupo: 1,
   			form: true
	   	},
	   	{
   			config:{
   				sysorigen: 'sis_presupuestos',
       		    name: 'id_partida',
   				origen: 'PARTIDA',
   				gdisplayField: 'desc_partida',
   				allowBlank: true,
   				fieldLabel: 'Partida',
   				width: 150
       	     },
   			type:'ComboRec',
   			id_grupo:1,
   			form:true
	   	},
	   		{
            config:{
                name: 'id_centro_costo',
                fieldLabel: 'Centro Costo',
                allowBlank: true,
                tinit: false,
                origen: 'CENTROCOSTO',
                gdisplayField: 'desc_centro_costo',
                width: 150
            },
            type: 'ComboRec',
            id_grupo: 2,
            form: true
        },
        {
            config:{
                    name: 'id_orden_trabajo',
                    fieldLabel: 'ORDEN DE TRABAJO',
                    tpl:'<tpl for="."><div class="x-combo-list-item"><p><b>{codigo}</b></p><p>{desc_orden}</p> <p>Tipo:{tipo}</p></div></tpl>',
                    sysorigen: 'sis_contabilidad',
	       		    origen: 'OT',
                    allowBlank: true,
                    gwidth: 200,
                    store : new Ext.data.JsonStore({
                            url:'../../sis_contabilidad/control/OrdenTrabajo/listarOrdenTrabajoAll',
                            id : 'id_orden_trabajo',
                            root: 'datos',
                            sortInfo:{
                                    field: 'motivo_orden',
                                    direction: 'ASC'
                            },
                            totalProperty: 'total',
                            fields: ['id_orden_trabajo','motivo_orden','desc_orden','motivo_orden','codigo','tipo'],
                            remoteSort: true,
                            baseParams:{par_filtro:'desc_orden#motivo_orden'}
                    }),
                    width: 150

            },
            type: 'ComboRec',
            id_grupo: 2,
            form: true
        },

	],
	labelSubmit: '<i class="fa fa-check"></i> Aplicar Filtro',
	south: {
		url: '../../../sis_contabilidad/vista/int_transaccion/LibroMayorReporte.php',
		title: 'Reporte Libro Mayor',
		height: '70%',
		cls: 'LibroMayorReporte'
	},
	title: 'Filtro de mayores',
	// Funcion guardar del formulario
	onSubmit: function(o) {
		var me = this;
		if (me.form.getForm().isValid()) {
			var parametros = me.getValForm();

			var gest=this.Cmp.id_gestion.lastSelectionText;

			var cuenta=this.Cmp.id_cuenta.lastSelectionText;
			var auxiliar=this.Cmp.id_auxiliar.lastSelectionText;
			var partida=this.Cmp.id_partida.lastSelectionText;

			var cc=this.Cmp.id_centro_costo.lastSelectionText;
      var ot=this.Cmp.id_orden_trabajo.lastSelectionText;

			this.onEnablePanel(this.idContenedor + '-south',
				Ext.apply(parametros,{	'gest': gest,
										'cuenta': cuenta,
										'auxiliar': auxiliar,
										'partida': partida,
										'cc' : cc,
                    'ot' : ot,
									 }));
        }
    },

    onReset:function(o){
      this.Cmp.id_cuenta.reset();
      this.Cmp.id_partida.reset();
      this.Cmp.id_centro_costo.reset();
      this.Cmp.id_auxiliar.reset();
      this.Cmp.id_orden_trabajo.reset();
	   },

	//
    iniciarEventos:function(){
    	this.Cmp.id_gestion.on('select', function(cmb, rec, ind){

    	 Ext.apply(this.Cmp.id_cuenta.store.baseParams,{id_gestion: rec.data.id_gestion})
			 Ext.apply(this.Cmp.id_partida.store.baseParams,{id_gestion: rec.data.id_gestion})
			 Ext.apply(this.Cmp.id_centro_costo.store.baseParams,{id_gestion: rec.data.id_gestion})
			 this.Cmp.id_cuenta.reset();
			 this.Cmp.id_partida.reset();
			 this.Cmp.id_centro_costo.reset();
			 this.Cmp.id_cuenta.modificado = true;
			 this.Cmp.id_partida.modificado = true;
			 this.Cmp.id_centro_costo.modificado = true;

       var gestion_año = this.Cmp.id_gestion.lastSelectionText;
       var inicio = '01/01/' + gestion_año;
       var fin = '31/12/' + gestion_año;

       this.Cmp.desde.setValue(inicio);
       this.Cmp.hasta.setValue(fin);

    	},this);




    },

    loadValoresIniciales: function(){
    	Phx.vista.FormFiltroReporteLibroMayor.superclass.loadValoresIniciales.call(this);

    	delete this.Cmp.id_cuenta.store.baseParams.id_gestion;
    	delete this.Cmp.id_cuenta.store.baseParams.tipo_cuenta;
    	this.Cmp.id_cuenta.modificado = true;




    }

})
</script>
