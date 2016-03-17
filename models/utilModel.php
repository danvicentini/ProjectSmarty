<?php

class utilModel extends model {
  
  public function getCidadeById($cid_id){
    return $this->read('cad_cidades', array('*'), "cid_id={$cid_id}", NULL, NULL, NULL, "");
  }
  
  public function getEstadoById($est_id){
    return $this->read('cad_estados', array('*'), "est_id={$est_id}", NULL, NULL, NULL, "");
  }
  
  
}
?>
