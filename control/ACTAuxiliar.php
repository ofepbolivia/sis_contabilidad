<?php
/**
*@package pXP
*@file ACTAuxiliar.php
*@author  Gonzalo Sarmiento Sejas
*@date 21-02-2013 20:44:52
*@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
*/

class ACTAuxiliar extends ACTbase{

	function listarAuxiliar(){
		$this->objParam->defecto('ordenacion','id_auxiliar');

		$this->objParam->defecto('dir_ordenacion','asc');
		if($this->objParam->getParametro('id_cuenta')!=''){
            $this->objParam->addFiltro("auxcta.id_auxiliar IN (select id_auxiliar
            							from conta.tcuenta_auxiliar where estado_reg = ''activo'' and id_cuenta = ".$this->objParam->getParametro('id_cuenta') . ") ");
        }

		/*Aumentando para listar los Auxilaires que tengan el tipo*/
		if($this->objParam->getParametro('grupos')!=''){
            $this->objParam->addFiltro("auxcta.tipo is not null");
    }
		/**********************************************************/

		if($this->objParam->getParametro('corriente')!=''){
					if ($this->objParam->getParametro('ro_activo')=='si'){
							// $this->objParam->addFiltro("  auxcta.corriente = ''no'' and auxcta.tipo = ''Grupo''");
							$this->objParam->addFiltro(" (auxcta.corriente=''si'' or  auxcta.tipo = ''Grupo'') ");
					}else{
            $this->objParam->addFiltro("auxcta.corriente = ''".$this->objParam->getParametro('corriente')."''");
					}
        }

		if($this->objParam->getParametro('id_auxiliar')!=''){
            $this->objParam->addFiltro("auxcta.id_auxiliar = ".$this->objParam->getParametro('id_auxiliar'));
        }
		if($this->objParam->getParametro('estado_reg')!=''){
					  $this->objParam->addFiltro("auxcta.estado_reg = ''".$this->objParam->getParametro('estado_reg')."''");
		}
		if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
			$this->objReporte = new Reporte($this->objParam,$this);
			$this->res = $this->objReporte->generarReporteListado('MODAuxiliar','listarAuxiliar');
		} else{
			$this->objFunc=$this->create('MODAuxiliar');
			$this->res=$this->objFunc->listarAuxiliar($this->objParam);
		}

		/*Aqui para poner todos los puntos de ventas*/
		if($this->objParam->getParametro('_adicionar')!=''){

			$respuesta = $this->res->getDatos();


			array_unshift ( $respuesta, array(  'id_auxiliar'=>'0',
				  'codigo_auxiliar'=>'Todos',
					'nombre_auxiliar'=>'Todos') );
			//var_dump($respuesta);
			$this->res->setDatos($respuesta);
		}
		/********************************************/



		$this->res->imprimirRespuesta($this->res->generarJson());
	}

	function insertarAuxiliar(){
		$this->objFunc=$this->create('MODAuxiliar');
		if($this->objParam->insertar('id_auxiliar')){
			$this->res=$this->objFunc->insertarAuxiliar($this->objParam);
		} else{
			$this->res=$this->objFunc->modificarAuxiliar($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}

	function eliminarAuxiliar(){
			$this->objFunc=$this->create('MODAuxiliar');
		$this->res=$this->objFunc->eliminarAuxiliar($this->objParam);
		$this->res->imprimirRespuesta($this->res->generarJson());
	}

    // Realizamos la conexion a la base de datos sql Server  para poder llamar al procedimiento de replicacion.
    function conectar(){
        //var_dump('conectar');exit;
        $conexion = mssql_connect('172.17.45.133', 'Test', 'Boa.2017');

        if (!$conexion) {
            die('Algo fue mal mientras se conectaba a MSSQL');
        }else{
            //echo ('Conectado Correctamente');
            $p_cadena = 'Ejecucion Diaria ERP';
            mssql_select_db('msdb', $conexion);

            $stmt = mssql_init('dbo.sp_start_job', $conexion);
            mssql_bind($stmt, '@job_name', $p_cadena, SQLVARCHAR, false, false, 50);

            mssql_execute($stmt);
            //mssql_free_statement($stmt);
            //echo 'Mensaje:: ';

        }

        mssql_close($conexion);
    }

    function validarAuxiliar(){
        $this->objFunc=$this->create('MODAuxiliar');
        $this->res=$this->objFunc->validarAuxiliar($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

	function listarAuxCuentaCorriente(){
		$this->objParam->defecto('ordenacion','id_auxiliar');

		$this->objParam->defecto('dir_ordenacion','asc');
		if($this->objParam->getParametro('id_cuenta')!=''){
			$this->objParam->addFiltro("auxcta.id_auxiliar IN (select id_auxiliar
            							from conta.tcuenta_auxiliar where estado_reg = ''activo'' and id_cuenta = ".$this->objParam->getParametro('id_cuenta') . ") ");
		}
		// {mod: bvasquez, date: 08/04/2021, desc: filstro por tipo interface}
		if($this->objParam->getParametro('tipo_interfaz') == 'auxiliar_cc'){
			$this->objParam->addFiltro("auxcta.corriente =  ''si''");
		}else if ($this->objParam->getParametro('tipo_interfaz') == 'auxiliar_cc_grupos' || $this->objParam->getParametro('tipo_interfaz') == 'auxiliar_cc_grupo_ro') {
			$this->objParam->addFiltro("auxcta.corriente = ''no'' and auxcta.tipo = ''Grupo''");
		}

		if($this->objParam->getParametro('corriente')!=''){
			$this->objParam->addFiltro("auxcta.corriente = ''".$this->objParam->getParametro('corriente')."''");
		}

		if($this->objParam->getParametro('id_auxiliar')!=''){
			$this->objParam->addFiltro("auxcta.id_auxiliar = ".$this->objParam->getParametro('id_auxiliar'));
		}
		if($this->objParam->getParametro('estado_reg')!=''){
			$this->objParam->addFiltro(" auxcta.estado_reg = ''".$this->objParam->getParametro('estado_reg')."''");
		}
		if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
			$this->objReporte = new Reporte($this->objParam,$this);
			$this->res = $this->objReporte->generarReporteListado('MODAuxiliar','listarAuxCuentaCorriente');
		} else{
			$this->objFunc=$this->create('MODAuxiliar');
			$this->res=$this->objFunc->listarAuxCuentaCorriente($this->objParam);
		}

		/*Aqui para poner todos los puntos de ventas*/
		if($this->objParam->getParametro('_adicionar')!=''){

			$respuesta = $this->res->getDatos();


			array_unshift ( $respuesta, array(  'id_auxiliar'=>'0',
				'codigo_auxiliar'=>'Todos',
				'nombre_auxiliar'=>'Todos') );
			//var_dump($respuesta);
			$this->res->setDatos($respuesta);
		}
		/********************************************/



		$this->res->imprimirRespuesta($this->res->generarJson());
	}

	function insertarAuxCuentaCorriente(){
		$this->objFunc=$this->create('MODAuxiliar');
		if($this->objParam->insertar('id_auxiliar')){
			$this->res=$this->objFunc->insertarAuxCuentaCorriente($this->objParam);
		} else{
			$this->res=$this->objFunc->modificarAuxCuentaCorriente($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}

	function eliminarAuxCuentaCorriente(){
		$this->objFunc=$this->create('MODAuxiliar');
		$this->res=$this->objFunc->eliminarAuxCuentaCorriente($this->objParam);
		$this->res->imprimirRespuesta($this->res->generarJson());
	}

}

?>
