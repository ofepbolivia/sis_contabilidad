<?php
/**
 * @package pXP
 * @file gen-MODReporteExt.php
 * @author  Maylee Perez Pastor
 * @date 13-08-2020 15:57:09
 * @description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
 */
class MODReporteExt extends MODbase
{
    function reporteDetalleGastos()
    {
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento = 'conta.ft_reporte_ext_sel';
        $this->transaccion = 'CONTA_REPDETGASTO_SEL';
        $this->tipo_procedimiento = 'SEL';//tipo de transaccion
        $this->setCount(false);

        $this->setParametro('fecha_ini', 'fecha_ini', 'date');
        $this->setParametro('fecha_fin', 'fecha_fin', 'date');
        $this->setParametro('id_proveedor', 'id_proveedor', 'int4');
        $this->setParametro('id_funcionario', 'id_funcionario', 'int4');

        //Definicion de la lista del resultado del query
        $this->captura('nro_cbte', 'varchar');
        $this->captura('fecha', 'varchar');
        $this->captura('tipo_cambio_2', 'numeric');
        $this->captura('desc_proveedor', 'varchar');
        $this->captura('desc_plantilla', 'varchar');
        $this->captura('nro_documento', 'varchar');
        $this->captura('concepto_gasto', 'varchar');
        $this->captura('observaciones', 'varchar');
        $this->captura('codigo_cc', 'varchar');
        $this->captura('partida', 'varchar');
        $this->captura('desc_orden', 'varchar');
        $this->captura('fecha_costo_ini', 'varchar');
        $this->captura('fecha_costo_fin', 'varchar');
        $this->captura('detalle_periodo', 'varchar');
        $this->captura('detalle_gasto', 'varchar');
        $this->captura('nro_cheque', 'varchar');
        $this->captura('monto_dolares', 'numeric');
        $this->captura('monto', 'numeric');
        $this->captura('cuenta_bancaria', 'varchar');
        $this->captura('glosa', 'varchar');


        //Ejecuta la instruccion
        $this->armarConsulta();
        //echo $this->consulta;exit;
        $this->ejecutarConsulta();
        //var_dump('llegaDet2',$this->respuesta );exit;
        //Devuelve la respuesta
        return $this->respuesta;
    }

}

?>
