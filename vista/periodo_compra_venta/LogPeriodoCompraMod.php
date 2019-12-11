
<script>    
    Phx.vista.LogPeriodoCompraMod=Ext.extend(Phx.gridInterfaz,{        
        constructor: function(config) {
            this.maestro = config;                                    
            
            Phx.vista.LogPeriodoCompraMod.superclass.constructor.call(this,config);            
            this.init();            
        },
        bactGroups:[],
        bexcelGroups:[],
        gruposBarraTareas: [
            {name:  'cerrado', title: '<h1 style="text-align: center; color: red ;"><i class="fa fa-circle-o" aria-hidden="true"></i>CERRADO</h1>',grupo: 0, height: 0} ,
            {name: 'abierto', title: '<h1 style="text-align: center; color: green;"><i  class="fa fa-circle" aria-hidden="true"></i>ABIERTO</h1>', grupo: 1, height: 1},
            {name: 'cerrado_parcial', title: '<h1 style="text-align: center; color: brown;"><i class="fa fa-dot-circle-o" aria-hidden="true"></i>CIERRE PARCIAL</h1>', grupo: 2, height: 2}
        ],        
        actualizarSegunTab: function(name, indice){
            this.store.baseParams.estado = name;                        
            this.load({params:{start:0, limit:this.tam_pag,id_periodo_compra_venta:this.maestro.id_periodo_compra_venta}});
        },        
        Atributos:[
            {
                //configuracion del componente
                config:{
                    labelSeparator:'',
                    inputType:'hidden',
                    name: 'id_log_periodo_compra'
                },
                type:'Field',
                form:true
            },
            {
                config:{
                    labelSeparator:'',
                    name: 'id_periodo_compra_venta',                    
                    inputType:'hidden'
                },
                type:'Field',
                form:true
            },            
            {
                config:{
                    name: 'estado',
                    fieldLabel: 'Estado',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
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
                filters:{pfiltro:'lgp.estado',type:'string'},
                bottom_filter: true,
                id_grupo:1,
                grid:true,
                form:true
            },
            {
                config:{
                    name: 'fecha_reg',
                    fieldLabel: 'Fecha creación',
                    allowBlank: true,
                    anchor: '100%',
                    gwidth: 120,
                    format: 'd/m/Y',
                    renderer:function (value,p,record){return value?value.dateFormat('d/m/Y H:i:s'):''}
                },
                type:'DateField',
                filters:{pfiltro:'lgp.fecha_reg',type:'date'},
                id_grupo:1,
                grid:true,
                form:false
            },
            {
                config:{
                    name: 'persona_reg',
                    fieldLabel: 'Creado por',
                    allowBlank: true,
                    anchor: '100%',
                    gwidth: 200                    
                },
                type:'TextField',
                filters:{pfiltro:'usu1.persona_reg',type:'string'},
                bottom_filter:true,
                id_grupo:1,
                grid:true,
                form:false
            },            
            {
                config:{
                    name: 'usr_reg',
                    fieldLabel: 'Cuenta Creador',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100                    
                },
                type:'TextField',
                filters:{pfiltro:'usu1.cuenta',type:'string'},
                bottom_filter:true,
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
                filters:{pfiltro:'lgp.fecha_mod',type:'date'},
                id_grupo:1,
                grid:true,
                form:false
            },
            {
                config:{
                    name: 'persona_mod',
                    fieldLabel: 'Modificado por',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100                    
                },
                type:'TextField',
                filters:{pfiltro:'usu2.persona_mod',type:'string'},
                id_grupo:1,
                grid:true,
                form:false
            },            
            {
                config:{
                    name: 'usr_mod',
                    fieldLabel: 'Cuenta Modificador',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100                    
                },
                type:'TextField',
                filters:{pfiltro:'usu2.cuenta',type:'string'},
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
                    gwidth: 100                    
                },
                type:'TextField',
                filters:{pfiltro:'lgp.estado_reg',type:'string'},
                id_grupo:1,
                grid:true,
                form:false
            },                                                      
        ],
        tam_pag:50,
        title:'Detalle',                        
        ActList:'../../sis_contabilidad/control/PeriodoCompraVenta/listarHistorialPeriodoCompra',
        id_store:'id_log_periodo_compra',
        fields: [
            {name:'id_log_periodo_compra', type: 'numeric'},
            {name:'id_perido_compra_venta', type: 'numeric'},
            {name:'estado', type: 'string'},            
            {name:'estado_reg', type: 'string'},
            {name:'id_usuario_ai', type: 'string'},
            {name:'fecha_reg', type: 'date',dateFormat:'Y-m-d H:i:s.u'},            
            {name:'fecha_mod', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
            {name:'usuario_ai', type: 'string'},                        
            {name:'id_usuario_reg', type: 'numeric'},                        
            {name:'id_usuario_mod', type: 'numeric'},
            {name:'usr_reg', type: 'string'},
            {name:'usr_mod', type: 'string'},
            {name:'persona_reg', type: 'string'},
            {name:'persona_mod', type: 'string'}            

        ],
        sortInfo:{
            field: 'id_log_periodo_compra',
            direction: 'ASC'
        }, 
        bedit: false,
        bnew:  false,
        bdel:  false,
        bsave: false,        
        btest: false,                
        fwidth: '90%',
        fheight: '95%',        
        loadValoresIniciales:function()
        {	                                    
            Phx.vista.LogPeriodoCompraMod.superclass.loadValoresIniciales.call(this);
        }        

    });
</script>
