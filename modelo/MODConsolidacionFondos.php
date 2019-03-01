<?php

class MODConsolidacionFondos extends MODbase
{

    function __construct(CTParametro $pParam)
    {
        parent::__construct($pParam);
    }

    function reporteConsolidacionFondosNoFinalizados()
    {
        //Definicion de variables para ejecucion del procedimientp
        $this->procedimiento = 'conta.f_consolidacion_fondos_sel';
        $this->transaccion = 'CONTA_RCONFONFIN_SEL';
        $this->tipo_procedimiento = 'SEL';//tipo de transaccion

        $this->setParametro('fecha_ini', 'fecha_ini', 'date');
        $this->setParametro('fecha_fin', 'fecha_fin', 'date');
        //$this->setParametro('tipo', 'tipo', 'varchar');
        //$this->setParametro('monto_mayor', 'monto_mayor', 'varchar');

        //Definicion de la lista del resultado del query
        $this->captura('nro_tramite', 'varchar');
        $this->captura('beneficiario', 'varchar');
        $this->captura('nro_cheque', 'varchar');
        $this->captura('codigo_categoria', 'varchar');
        $this->captura('partida', 'varchar');
        $this->captura('importe', 'numeric');

        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();
        //Devuelve la respuesta

//        var_dump($this->respuesta);exit;
        return $this->respuesta;
    }



}

?>