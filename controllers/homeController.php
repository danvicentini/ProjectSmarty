<?php

class home extends controller {

  public function __construct() {
    parent::__construct();

    //criar sessao com conexao para o projeto
    $_SESSION['projeto']['db_name'] = DBNAME;
    $_SESSION['projeto']['db_user'] = DBUSER;
    $_SESSION['projeto']['db_password'] = DBPASS;

    $permission = new permission();
    if (!$permission->authenticate(HOME)) {
      header('Location:/');
      die;
    }
  }

  public function index_action() {

    $this->template->fetchCSS('/files/js/toastmessage/resources/css/jquery.toastmessage.css');
    $this->template->fetchJS('/files/js/toastmessage/javascript/jquery.toastmessage.js');
    $this->template->fetchJS('/files/js/home/home.js');

    $dataProjetos = new projetosModel();
    $arrProjetos = array();
    foreach ($dataProjetos->getProjetos(null, $_SESSION['user']['usu_id']) as $projetos) {
      $arrProjetos[$projetos['pro_id']] = $projetos['projeto'];
    }
    $this->smarty->assign('arrProjetos', $arrProjetos);

    unset($_SESSION['menus']);

    $dataMenus = new menusModel();
    if($_SESSION['user']['per_id']==1)
      $this->smarty->assign('menu_ini', $dataMenus->getMenusToUrl('2,3'));
    else
      $this->smarty->assign('menu_ini', $dataMenus->getMenusToUrl('3'));
    $this->template->setTitle('Qualibras - Home');
    $this->template->run();
    $this->smarty->display('home/index.html');
  }

  public function seleciona_projeto() {

    //verificar se o usuario tem permissao de ver o projeto
    $dataProjetos = new projetosModel();
    $pro_id = $this->getParam('pro_id');
    $arrProjeto = $dataProjetos->getProjetos($pro_id, $_SESSION['user']['usu_id']);
    if (!count($arrProjeto)) {
      $arrReturn['text'] = "VocÃª nÃ£o possui permissÃ£o para visualizar esse projeto.";
      $arrReturn['time'] = 2500;
      $arrReturn['status'] = "error";
      $arrReturn['redirect'] = null;
      echo json_encode($arrReturn);
      die;
    }


    //buscar o primeiro menu que o usuario tem permissao
    //no banco de dados, tabela cad_menus, olhar a sequencia da coluna menu_ordem,
    //esta coluna, diz a ordem que o sistema irá carregar o serviço.
    $dataPerfil = new perfisModel();
    $arrPerfis = $dataPerfil->getPerfis($_SESSION['user']['per_id']);
    $dataMenus = new menusModel();

     $lista_menus = $dataMenus->getMenusToUrl('1,3');
    
    
    $lista_menus_ok = array();

    foreach ($lista_menus as $menus) {

      if ((intval($arrPerfis[0]['per_valor']) & intval($menus['men_valor']))) {
        if(!isset($arrReturn['redirect']) && $menus['men_url'] != '/home')
          $arrReturn['redirect'] = $menus['men_url'];

        $lista_menus_ok[] = $menus;
      }
    }

    //criar sessao com conexao para o projeto selecionado
    unset($_SESSION['projeto']);
    unset($_SESSION['menus']);
    $_SESSION['projeto'] = $arrProjeto[0];
    $_SESSION['menus'] = $lista_menus_ok;

    $arrReturn['text'] = "Redirecionando para o projeto...";
    $arrReturn['time'] = 1000;
    $arrReturn['status'] = "success";

    echo json_encode($arrReturn);
    die;
  }

}

?>
