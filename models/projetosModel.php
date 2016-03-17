<?php

class projetosModel extends model {
  
  public function getProjetos($pro_id = null, $usu_id = null){
    $where = "a.pro_id = b.pro_id";
    if(!is_null($pro_id)){
      $where .= " and a.pro_id = $pro_id";
    }
    if(!is_null($usu_id)){
      $where .= " and b.usu_id = $usu_id";
    }
    
    return $this->read("cad_projetos as a, rel_usuarios_projetos as b", array('*'), $where);
  }
  
  public function delUsuariosProjetos($usu_id){
    $where = "usu_id = $usu_id";
    return $this->delete("rel_usuarios_projetos", $where);
  }
  
  public function setUsuariosProjetos($array){
    return $this->insert("rel_usuarios_projetos", $array);
  }
}

?>
