<?php

class index extends controller {

  public function __construct() {
    parent::__construct();

    //criar sessao com conexao para o projeto
    $_SESSION['projeto']['db_name'] = DBNAME;
    $_SESSION['projeto']['db_user'] = DBUSER;
    $_SESSION['projeto']['db_password'] = DBPASS;
  }

  public function index_action() {

    $permission = new permission();
    if ($permission->accessValidate()) {
      header('Location:/home');
    }

    $this->template->fetchCSS('/files/js/toastmessage/resources/css/jquery.toastmessage.css');
    $this->template->fetchJS('/files/js/toastmessage/javascript/jquery.toastmessage.js');
    $this->template->fetchJS('/files/js/index/index.js');

    $this->template->setTitle('Qualibras');
    $this->template->run();
    $this->smarty->display('index.html');
  }

}

?>
