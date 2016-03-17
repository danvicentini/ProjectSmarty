<?php

class login extends controller {
  
  public function __construct() {
    parent::__construct();

    //criar sessao com conexao para o projeto
    $_SESSION['projeto']['db_name'] = DBNAME;
    $_SESSION['projeto']['db_user'] = DBUSER;
    $_SESSION['projeto']['db_password'] = DBPASS;
  }

  public function index_action() {

    $util = new util();
    $dataUsuario = new usuariosModel();

    $login = $_POST['login'];
    $senha = md5($_POST['senha']);
  
    $arrUsuario = $dataUsuario->getLoginUsuario($login, $senha);

    if (count($arrUsuario)) {
      $_SESSION['user'] = $arrUsuario[0];
      echo 1;
    }else
      echo 0;
  }
  
  public function sair(){
    unset($_SESSION['user']);
    header("Location:/");
  }

}

?>
