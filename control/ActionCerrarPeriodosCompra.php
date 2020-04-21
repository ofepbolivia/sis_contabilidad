<?php
/***
Nombre: Intermediario.php
Proposito: Invocar a la funcion conta.ft_periodo_compra_venta_ime/CONTA_ABRCERPER_IME
 *          esta estara encargada de cerrar los periodos de compra cada fecha 20 de cada mes y 10 para un cierre parcial
Autor:	YMR
Fecha:	11/12/2019
 */

include_once(dirname(__FILE__)."/../../lib/lib_control/CTSesion.php");
session_start();
$_SESSION["_SESION"]= new CTSesion();

include(dirname(__FILE__).'/../../lib/DatosGenerales.php');
include_once(dirname(__FILE__).'/../../lib/lib_general/Errores.php');
include_once(dirname(__FILE__).'/../../lib/rest/PxpRestClient.php');


ob_start();


//estable aprametros ce la cookie de sesion
$_SESSION["_CANTIDAD_ERRORES"]=0;//inicia control


//echo dirname(__FILE__).'LLEGA';
register_shutdown_function('fatalErrorShutdownHandler');
set_exception_handler('exception_handler');
set_error_handler('error_handler');;
include_once(dirname(__FILE__).'/../../lib/lib_control/CTincludes.php');

$pxpRestClient = PxpRestClient::connect('127.0.0.1',substr($_SESSION["_FOLDER"], 1) .'pxp/lib/rest/')
    ->setCredentialsPxp($_GET['user'],$_GET['pw']);
$fecha = new DateTime();
$res = $pxpRestClient->doPost('contabilidad/PeriodoCompraVenta/cerrarPeriodosCompra',
    array());
$res_json = json_decode($res);

var_dump($res_json);
exit;
?>