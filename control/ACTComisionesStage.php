<?php
/**
 *@package pXP
 *@file ACTCalculoOverComison.php
 *@author  (breydi.vasquez)
 *@date 23/07/2021
 *@description Clase que recibe los parametros enviados por la vista para mandar a la capa de servicios Stage
 */


class ACTComisionesStage extends ACTbase{

    function listarComisiones(){

      $c = curl_init();
      curl_setopt($c, CURLOPT_URL, 'http://sms.obairlines.bo/CommissionServices/ServiceComision.svc/GetPayCommissions');
      curl_setopt($c, CURLOPT_CUSTOMREQUEST, "GET");
      curl_setopt($c, CURLOPT_POSTFIELDS, "");
      curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($c, CURLOPT_CONNECTTIMEOUT, 20);
      curl_setopt($c, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

      $ex = curl_exec($c);
      $status = curl_getinfo($c, CURLINFO_HTTP_CODE);
      if (!$status) {
        throw new Exception("No se pudo conectar con el servicio");
      }
      curl_close($c);
      $res = json_decode($ex);
      $res = json_decode($res->GetPayCommissionsResult);
      $this->res = new Mensaje();
      $this->res->setMensaje(
          'EXITO',
          'driver.php',
          'Get Data Documents Comisiones pago',
          'Service Get List Comisiones pago',
          'control',
          'conta.ft_agrupador_doc_sel',
          'CONTA_CRDPYCO_SEL',
          'SEL'
      );

      $resp = array();
      $input = $this->objParam->arreglo_parametros['bottom_filtro_value']; // filtro ingresado en la vista
      if ($input!=""){
        // filtro php
        $result = array_filter($res->Data, function ($item) use ($input) {
            if (stripos(json_encode($item), $input) !== false) {
                return true;
            }
            return false;
        });
        // agregarlo en otro arreglo para que el indice comience de 0, caso contrario podria enpezar en otros indice y en la vista no lo reconoceria el arreglo enviado
        foreach ($result as  $value) {
          array_push($resp, $value);
        }
      }else{
        $resp = $res->Data;
      }

      // var_dump($resp);exit;
      $this->res->setTotal(count($resp));
      $this->res->datos = $resp;
      $this->res->imprimirRespuesta($this->res->generarJson());
    }


    function insertarComision(){
      $ff = '9999-12-31';
      if($this->objParam->getParametro('fecha_fin')!='' || $this->objParam->getParametro('fecha_fin') != null){
          $ff = date("Y-m-d", strtotime($this->objParam->getParametro('fecha_fin')));
      }

      $data = array(
          "id_pay_com" => 0,
          "cod_emp" => $this->objParam->getParametro('codigo_empresa'),
          "nom_emp" => $this->objParam->getParametro('nombre_empresa'),
          "f_ini" => date("Y-m-d", strtotime($this->objParam->getParametro('fecha_ini'))),
          "f_fin" => $ff,
          "comi_porcj" => $this->objParam->getParametro('porcentaje_comision'),
          "observ" => $this->objParam->getParametro('observaciones'),
          "transaccion" => ''
      );

      if($this->objParam->insertar('id_comision_stage')){
        $data["transaccion"] = 'INS';
      } else{
        $data["id_pay_com"] = $this->objParam->getParametro('id_comision_stage');
        $data["transaccion"] = 'MOD';
      }

      $jdata = json_encode($data);
      $c = curl_init();
      curl_setopt($c, CURLOPT_URL, 'http://sms.obairlines.bo/CommissionServices/ServiceComision.svc/ImePayCommissions');
      curl_setopt($c, CURLOPT_POST, true);
      curl_setopt($c, CURLOPT_POSTFIELDS, $jdata);
      curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($c, CURLOPT_CONNECTTIMEOUT, 20);
      curl_setopt($c, CURLOPT_HTTPHEADER, array(
              'Content-Type: application/json',
              'Content-Length: ' . strlen($jdata)));

      $ex = curl_exec($c);
      $status = curl_getinfo($c, CURLINFO_HTTP_CODE);
      if (!$status) {
        throw new Exception("No se pudo conectar con el servicio");
      }
      curl_close($c);
      $res = json_decode($ex);
      $res = json_decode($res->ImePayCommissionsResult);
      if($res->State){
        if($res->Data->Result==1){
          $this->res = new Mensaje();
          $this->res->setMensaje(
              'EXITO',
              'driver.php',
              'Insercion O Modificacion Comisiones pago',
              'Insert Mod Comisiones pago',
              'control',
              'conta.ft_agrupador_doc_ime',
              'CONTA_INSMPYCO_IME',
              'IME'
          );
          $this->res->setTotal(0);
          $this->res->datos = $res->Data;
          $this->res->imprimirRespuesta($this->res->generarJson());
        }else{
          throw new \Exception($res->Data->Message);
        }
      }else{
        throw new \Exception($res->Message);
      }
    }

    function eliminarComision(){

      $data = array(
          "id_pay_com" => $this->objParam->arreglo_parametros[0]['id_comision_stage'],
          "cod_emp" => "",
          "nom_emp" => "",
          "f_ini" => "",
          "f_fin" => "",
          "comi_porcj" => 0.00,
          "observ" => "",
          "transaccion" => "DEL"
      );

      $jdata = json_encode($data);
      $c = curl_init();
      curl_setopt($c, CURLOPT_URL, 'http://sms.obairlines.bo/CommissionServices/ServiceComision.svc/ImePayCommissions');
      curl_setopt($c, CURLOPT_POST, true);
      curl_setopt($c, CURLOPT_POSTFIELDS, $jdata);
      curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($c, CURLOPT_CONNECTTIMEOUT, 20);
      curl_setopt($c, CURLOPT_HTTPHEADER, array(
              'Content-Type: application/json',
              'Content-Length: ' . strlen($jdata)));

      $ex = curl_exec($c);
      $status = curl_getinfo($c, CURLINFO_HTTP_CODE);
      if (!$status) {
        throw new Exception("No se pudo conectar con el servicio");
      }
      curl_close($c);
      $res = json_decode($ex);
      $res = json_decode($res->ImePayCommissionsResult);
      if($res->State){
        if($res->Data->Result==1){
            $this->res = new Mensaje();
            $this->res->setMensaje(
                'EXITO',
                'driver.php',
                'Eliminacion Comisiones pago',
                'Delete Comisiones pago',
                'control',
                'conta.ft_agrupador_doc_ime',
                'CONTA_DELMPYCO_IME',
                'IME'
            );
            $this->res->setTotal(0);
            $this->res->datos = $res->Data;
            $this->res->imprimirRespuesta($this->res->generarJson());
        }else{
          throw new \Exception($res->Data->Message);
        }
      }else{
        throw new \Exception($res->Message);
      }

    }

}
