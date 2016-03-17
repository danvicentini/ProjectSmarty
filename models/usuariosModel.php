<?php

class usuariosModel extends model {
  
  public function getLoginUsuario($login, $senha){
    $where = "a.usu_nick='{$login}' and a.usu_senha='{$senha}'";
    $where .= " and a.per_id = b.per_id";
    return $this->read('cad_usuarios as a, cad_perfis as b', array('*'), $where, NULL, NULL, NULL, "");
  }
  
  public function getUsuarios($usu_id = null, $usu_nick = null){
    $tables = 'cad_usuarios as a, cad_perfis as b';  
    $where = "a.per_id=b.per_id and a.usu_status in (1,2)";
    if($usu_id){
      $where .= " and a.usu_id={$usu_id}";
    }
    if($usu_nick){
      $where .= " and a.usu_nick='$usu_nick'";
    }
    return $this->read($tables, array('*'), $where, NULL, NULL, NULL, "");
  }
    
  public function getPerfis(){
    return $this->read('cad_perfis', array('*'), NULL, NULL, NULL, NULL, "");
  }
  
  public function setUsuario($data){
     return $this->insert('cad_usuarios', $data, true);
  }
  
  public function updUsuario($data, $usu_id){
     return $this->update('cad_usuarios', $data, 'usu_id='.$usu_id, true);
  }
  
}


?>
