<?php

class menusModel extends model {

  public function getMenus(){
    return $this->read('cad_menus', array('*'), 'men_status=1', NULL, NULL, NULL, "men_ordem");
  }

  public function getMenusToUrl($tipo = null){
    $where = "men_status=1";
    if(!is_null($tipo)) $where .= " and tipo IN ($tipo)";
    return $this->read('cad_menus', array('*'), $where, NULL, NULL, NULL, "men_ordem");
  }

}
?>
