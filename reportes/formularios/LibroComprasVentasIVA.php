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
      					labelSeparator:'',
      					inputType:'hidden',
      					name: 'nit_linea_aerea'
      			},
      			type:'Field',
      			form:true
      		},
          {
      			config:{
      					labelSeparator:'',
      					inputType:'hidden',
      					name: 'cod_iata'
      			},
      			type:'Field',
      			form:true
      		},
            {
                config:{
                    name: 'id_entidad',
                    fieldLabel: 'Entidad',
                    qtip: 'entidad a la que pertenese el depto, ',
                    allowBlank: false,
                    emptyText:'Entidad...',
                    msgTarget: 'side',
                    editable: false,
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
                            fields: ['id_entidad','nit','nombre','cod_iata_linea_aerea'],
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
                    //anchor:"90%",
                    width: 280,
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
                    msgTarget: 'side',
                    editable:false,
                    store:new Ext.data.ArrayStore({
                        fields: ['ID', 'valor'],
                        data :	[
                            //['endesis_erp','Libro de Compras Estandar'],
                            ['lcv_compras','Libro de Compras Estandar'],
                            ['lcv_ventas','Libro de Ventas Estandar'],
                            ['lcncd','Libro de Compras Notas Credito-Debito'],
                            ['lvncd','Libro de Ventas Notas Credito-Debito'],
                            ['repo_iata', 'Iata'],
                            ['repo_ing_gravado', 'Reporte Ingresos Gravados (IT)'],
                            ['lce_siat', '<b style="color : #00B167;">Registro de Compras Estandar SIAT</b>'],
                            ['lve_siat', '<b style="color : #00B167;">Registro de Ventas Estandar SIAT</b>'],
                            ['lc_on_siat', '<b style="color : #FF8F85;">Registro de Compras Online SIAT</b>'],
                            ['lc_es_on_siat', '<b style="color : #4682B4;">Registro de Compras Estandar,Online SIAT</b>']
                        ]
                    }),
                    valueField:'ID',
                    displayField:'valor',
                    width:280,

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
                    editable:false,
                    store:new Ext.data.ArrayStore({
                        fields: ['ID', 'valor'],
                        data :	[['periodo','Gestión y Periodo'],
                            ['fechas','Rango de Fechas']]
                    }),
                    msgTarget: 'side',
                    valueField:'ID',
                    displayField:'valor',
                    width:280,

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
                    editable: false,
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
                    msgTarget: 'side',
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
                    listWidth:280,
                    resizable:true,
                    width: 280
                    //anchor:'100%'

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
                    msgTarget: 'side',
                    editable: false,
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
                    listWidth:280,
                    resizable:true,
                    width: 280
                    //anchor:'100%'

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
                    //anchor: '80%',
                    width: 177,
                    gwidth: 100,
                    format: 'd/m/Y',
                    editable: false,
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
                    //anchor: '80%',
                    width: 177,
                    gwidth: 100,
                    format: 'd/m/Y',
                    editable: false,
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
                    msgTarget: 'side',
                    editable: false,
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
                        baseParams:{par_filtro:'PERSON.nombre_completo2#USUARI.cuenta',_adicionar:'si'}
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
                    width:280,
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
                    msgTarget: 'side',
                    editable: false,
                    store:new Ext.data.ArrayStore({
                        fields: ['ID', 'valor'],
                        data :	[['txt','TXT'],
                            ['pdf','PDF'],
                            ['csv','CSV'],
                            ['xls','XLS']]
                    }),
                    valueField:'ID',
                    displayField:'valor',
                    width:280,

                },
                type:'ComboBox',
                id_grupo:1,
                form:true
            },
            {
                config:{
                    name:'formato_reporte_iata',
                    fieldLabel:'Formato del Reporte',
                    typeAhead: true,
                    allowBlank:true,
                    triggerAction: 'all',
                    emptyText:'Formato...',
                    mode:'local',
                    msgTarget: 'side',
                    store:new Ext.data.ArrayStore({
                        fields: ['ID', 'valor'],
                        data :	[['txt','TXT'],
                                ['csv','CSV']]
                    }),
                    valueField:'ID',
                    displayField:'valor',
                    width:280,

                },
                type:'ComboBox',
                id_grupo:1,
                form:true
            }
          ],


        title : 'Reporte Libro Compras Ventas IVA',
        ActSave : '../../sis_contabilidad/control/TsLibroBancos/reporteLibroBancos',

        topBar : true,
        botones : false,
        labelSubmit : 'Generar',
        tooltipSubmit : '<b>Reporte LCV - IVA</b>',
        timeout: 3000000,

        constructor : function(config) {
            Phx.vista.ReporteLibroComprasVentasIVA.superclass.constructor.call(this, config);
            this.init();

            this.ocultarComponente(this.Cmp.fecha_fin);
            this.ocultarComponente(this.Cmp.fecha_ini);
            this.ocultarComponente(this.Cmp.id_gestion);
            this.ocultarComponente(this.Cmp.id_periodo);
            this.ocultarComponente(this.Cmp.formato_reporte_iata);
            this.iniciarEventos();
        },

        iniciarEventos:function(){

            this.Cmp.id_entidad.store.load({params:{start:0, limit:10}, scope:this, callback: function (param,op,suc) {
                    this.Cmp.nit_linea_aerea.setValue(param[0].data.nit);
                    this.Cmp.cod_iata.setValue(param[0].data.cod_iata_linea_aerea);
                    this.Cmp.id_entidad.setValue(param[0].data.id_entidad);
                    this.Cmp.id_entidad.collapse();
                    this.Cmp.tipo_lcv.focus(false,  5);
                }});

            this.Cmp.id_entidad.on('select', function(combo,record,index){
              this.Cmp.nit_linea_aerea.setValue(record.data.nit);
              this.Cmp.cod_iata.setValue(record.data.cod_iata_linea_aerea);
              this.Cmp.id_entidad.setValue(record.data.id_entidad);
            },this)

            this.Cmp.tipo_lcv.on('select', function (combo,record,index){

                if( 'lcncd' == record.data.ID ){

                    this.Cmp.id_usuario.setVisible(false);
                    this.Cmp.id_usuario.reset();
                    this.Cmp.id_usuario.modificado = true;
                    this.Cmp.id_usuario.allowBlank = true;
                    this.ocultarComponente(this.Cmp.formato_reporte_iata);
                    this.mostrarComponente(this.Cmp.formato_reporte);
                }else if ('repo_iata' == record.data.ID){
                    this.Cmp.id_usuario.setVisible(false);
                    this.Cmp.id_usuario.reset();
                    this.Cmp.id_usuario.modificado = true;
                    this.Cmp.id_usuario.allowBlank = true;
                    this.mostrarComponente(this.Cmp.formato_reporte_iata);
                    this.ocultarComponente(this.Cmp.formato_reporte);
                }else{
                    this.mostrarComponente(this.Cmp.id_usuario);
                    this.Cmp.id_usuario.setVisible(true);
                    this.Cmp.id_usuario.allowBlank = false;
                    this.ocultarComponente(this.Cmp.formato_reporte_iata);
                    this.mostrarComponente(this.Cmp.formato_reporte);
                }
            },this);


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
                    this.Cmp.id_gestion.allowBlank = false;
                    this.Cmp.id_periodo.allowBlank = false;
                    this.Cmp.fecha_ini.allowBlank = true;
                    this.Cmp.fecha_fin.allowBlank = true;

                    this.Cmp.fecha_ini.reset();
                    this.Cmp.fecha_ini.modificado = true;
                    this.Cmp.fecha_fin.reset();
                    this.Cmp.fecha_fin.modificado = true;

                } else{
                    this.mostrarComponente(this.Cmp.fecha_fin);
                    this.mostrarComponente(this.Cmp.fecha_ini);
                    this.ocultarComponente(this.Cmp.id_gestion);
                    this.ocultarComponente(this.Cmp.id_periodo);
                    this.Cmp.id_gestion.allowBlank = true;
                    this.Cmp.id_periodo.allowBlank = true;
                    this.Cmp.fecha_ini.allowBlank = false;
                    this.Cmp.fecha_fin.allowBlank = false;

                    this.Cmp.id_gestion.reset();
                    this.Cmp.id_gestion.modificado = true;
                    this.Cmp.id_periodo.reset();
                    this.Cmp.id_periodo.modificado = true;
                }

            }, this);

            this.current_date = new Date();
            this.diasMes = [31, new Date(this.current_date.getFullYear(), 2, 0).getDate(), 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];

            this.Cmp.fecha_ini.on('select', function (rec, date) {
                let fecha_max = new Date(date.getFullYear() ,date.getMonth(), this.diasMes[date.getMonth()])
                this.Cmp.fecha_fin.setMaxValue(fecha_max);
                this.Cmp.fecha_fin.setMinValue(this.Cmp.fecha_ini.getValue());
            },this);
        },



        tipo : 'reporte',
        clsSubmit : 'bprint',

        Grupos : [{
            layout : 'column',
            labelAlign: 'top',
            border : false,
            autoScroll: true,
            items : [
                {
                    columnWidth: .31,
                    border: false,
                    //split: true,
                    layout: 'anchor',
                    autoScroll: true,
                    autoHeight: true,
                    collapseFirst : false,
                    collapsible: false,
                    anchor: '100%',
                    items:[
                        {
                            anchor: '100%',
                            bodyStyle: 'padding-right:5px;',
                            autoHeight: true,
                            border: false,
                            items:[
                                {
                                    xtype: 'fieldset',
                                    layout: 'form',
                                    border: true,
                                    title: 'Datos para el Reporte',
                                    //bodyStyle: 'padding: 5px 10px 10px 10px;',

                                    items: [],
                                    id_grupo: 0
                                }
                            ]
                        }
                    ]
                }
            ]
        }],

        ActSave:'../../sis_contabilidad/control/DocCompraVentaForm/reporteLCV',

        successSave :function(resp){
            Phx.CP.loadingHide();

            if ( ( this.Cmp.tipo_lcv.getValue() == 'lcv_ventas' || this.Cmp.tipo_lcv.getValue() == 'repo_ing_gravado' || this.Cmp.tipo_lcv.getValue() == 'lve_siat' ) && (this.Cmp.formato_reporte.getValue() == 'pdf' || this.Cmp.formato_reporte.getValue() == 'xls') ) {
                Ext.Msg.show({
                    title: 'Información',
                    msg: '<b>Estimado Funcionario: ' + '\n' + ' El Reporte se esta Generando..........</b>',
                    //msg: '<b>Actualmente se esta evaluando la generación del reporte, le confirmaremos para que pueda generar el reporte.</b>',
                    buttons: Ext.Msg.OK,
                    width: 512,
                    icon: Ext.Msg.INFO
                });
            }

            var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));

            /*let raiz = reg.ROOT;
            console.log('raiz',raiz);*/

            if (reg.ROOT.error) {
                alert('error al procesar');
                return
            }

            var nomRep = reg.ROOT.detalle.archivo_generado;
            if(Phx.CP.config_ini.x==1){
                nomRep = Phx.CP.CRIPT.Encriptar(nomRep);
            }



            if (this.Cmp.formato_reporte.getValue() == 'pdf') {
                window.open('../../../lib/lib_control/Intermediario.php?r=' + nomRep + '&t=' + new Date().toLocaleTimeString())
            } else if (this.Cmp.formato_reporte_iata.getValue() == 'txt' && this.Cmp.tipo_lcv.getValue() == 'repo_iata') {
                var data = "&extension=txt";
                data += "&name_file=" + nomRep;
                data += "&url=../../../reportes_generados/" + nomRep;
                window.open('../../../lib/lib_control/CTOpenFile.php?' + data);
            } else {
                if ( ( this.Cmp.tipo_lcv.getValue() != 'lve_siat' && (this.Cmp.formato_reporte.getValue() == 'xls' || this.Cmp.formato_reporte.getValue() == 'txt') ) || (this.Cmp.tipo_lcv.getValue() == 'lve_siat' && this.Cmp.formato_reporte.getValue() == 'txt') ) {
                    window.open('../../../reportes_generados/' + nomRep + '?t=' + new Date().toLocaleTimeString());
                }
            }

            if ( this.Cmp.tipo_lcv.getValue() == 'lve_siat' && this.Cmp.formato_reporte.getValue() == 'xls'  ) {

                let raiz = reg.ROOT.datos;
                let partition = 30000;//30000
                let number_sheets = Math.ceil(raiz.total / partition);
                let index = 0;
                //console.log('root', raiz);

                let min_inc = 8;
                let min_con = 60000;//1 min
                let min_var = min_con*min_inc;//8 min
                for (var ind = 1; ind <= number_sheets; ind++) { console.log('ind', ind, 'number_sheets', number_sheets);

                    let root = {
                        file_name     : 'RegistroVentasEstandarSiat_' + raiz.periodo + '_' + raiz.gestion + '_' + ind + '_de_' + number_sheets + '_',
                        index         : index,
                        partition     : partition,
                        dataEntidad   : JSON.stringify(raiz.dataEntidad.datos),
                        dataPeriodo   : JSON.stringify(raiz.dataPeriodo.datos),
                        filtro_sql    : raiz.filtro_sql,
                        tipo_lcv      : raiz.tipo_lcv,
                        id_gestion    : raiz.id_gestion,
                        id_periodo    : raiz.id_periodo,
                        fecha_ini     : raiz.fecha_ini,
                        fecha_fin     : raiz.fecha_fin,
                        number_sheets : number_sheets,
                        contador      : ind
                    };

                    setTimeout(this.crearListadoArchivoExcelSIAT, min_var, root);
                    console.log('minutos', min_var);

                    min_inc += 8;
                    min_var = min_con * min_inc;
                    index += partition;
                }
            }

        },
        crearListadoArchivoExcelSIAT:  function(root){
            Ext.Ajax.request({
                url: '../../sis_contabilidad/control/DocCompraVentaForm/crearListadoArchivoExcelSIAT',
                params: {
                    file_name     : root.file_name,
                    index         : root.index,
                    partition     : root.partition,
                    dataEntidad   : root.dataEntidad,
                    dataPeriodo   : root.dataPeriodo,
                    filtro_sql    : root.filtro_sql,
                    tipo_lcv      : root.tipo_lcv,
                    id_gestion    : root.id_gestion,
                    id_periodo    : root.id_periodo,
                    fecha_ini     : root.fecha_ini,
                    fecha_fin     : root.fecha_fin,
                    number_sheets : root.number_sheets,
                    contador      : root.contador

                },
                success: function(response){
                    let root = (Ext.util.JSON.decode(Ext.util.Format.trim(response.responseText))).ROOT;
                    console.log('root', root)
                },
                failure: this.conexionFailure,
                timeout: this.timeout,
                scope: this
            });
        }
    })
</script>
