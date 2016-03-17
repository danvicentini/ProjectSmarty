<?php

class perfisModel extends model {
  
  public function getPerfis($per_id = null){
    
    $where = (!is_null($per_id)) ? "per_id=$per_id" : '';
    
    return $this->read('cad_perfis', array('*'), $where, NULL, NULL, NULL, "per_nome");
  }
  
}
?>
