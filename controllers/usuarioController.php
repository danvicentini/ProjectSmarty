<?php

class usuario extends controller {
  
  public function __construct() {
    parent::__construct();

    $permission = new permission();
    if (!$permission->authenticate(USUARIOS)) {
      header('Location:/home');
      die;
    }
  }

  public function index_action() {
    $this->template->fetchCSS('/files/js/toastmessage/resources/css/jquery.toastmessage.css');
    $this->template->fetchJS('/files/js/toastmessage/javascript/jquery.toastmessage.js');
    $this->template->fetchJS('/files/js/usuario/index.js');

    $user = $_SESSION['user']['usu_nick'];
    
    if ($user == "Ligeane_CPFL") {
    	exit();
    }
    
    $dataUsuarios = new usuariosModel();
    $lista_usuarios = $dataUsuarios->getUsuarios();
    $this->smarty->assign('lista_usuarios', $lista_usuarios);

    $dataMenus = new menusModel();
    $this->smarty->assign('menu_ini', $dataMenus->getMenusToUrl('2,3'));
    $this->template->setTitle('Vitális Energia Eficiente - Lista de usuários');
    $this->template->run(); 
    $this->smarty->display('usuario/index.html');
  }

  public function cadastro() {
    
    $this->template->fetchCSS('/files/js/toastmessage/resources/css/jquery.toastmessage.css');
    $this->template->fetchJS('/files/js/toastmessage/javascript/jquery.toastmessage.js');
    $this->template->fetchJS('/files/js/usuario/cadastro.js');

    $dataUsuarios = new usuariosModel();
    $arrPerfis = $dataUsuarios->getPerfis();
    foreach ($arrPerfis as $perfil) {
      $lista_perfis[$perfil['per_id']] = $perfil['per_nome'];
    }
    $this->smarty->assign('lista_perfis', $lista_perfis);
    
    $dataProjetos = new projetosModel();
    foreach($dataProjetos->getProjetos() as $projetos){
      $arrProjetos[$projetos['pro_id']] = $projetos['projeto'];
    }
    $this->smarty->assign('arrProjetos', $arrProjetos);
    
    if($this->getParam('usu_id')!=''){
      $usu_id = $this->getParam('usu_id');
      $arrUsuario = $dataUsuarios->getUsuarios($usu_id);
      $this->smarty->assign('arrUsuario', $arrUsuario);
      $this->smarty->assign('perfil_selected', $arrUsuario[0]['per_id']);
      
      $arrProjetosUser = $dataProjetos->getProjetos(null, $arrUsuario[0]['usu_id']);
      $arrProjetosUserChecked = array();
      foreach($dataProjetos->getProjetos(null, $arrUsuario[0]['usu_id']) as $value){
        $arrProjetosUserChecked[] = $value['pro_id'];
      }
      $this->smarty->assign('projetos_checked', $arrProjetosUserChecked);
    }else{
      $this->smarty->assign('projetos_checked', '');
      $this->smarty->assign('perfil_selected', '');
    }

    $dataMenus = new menusModel();
    $this->smarty->assign('menu_ini', $dataMenus->getMenusToUrl('2,3'));
    
    $this->template->setTitle('Vitális Energia Eficiente - Cadastro de usuários');
    $this->template->run();
    $this->smarty->display('usuario/cadastro.html');
    
  }
  
  public function gravar_usuario(){
    $arrUsuario['per_id'] = $_POST['perfil'];
    $arrUsuario['usu_nome'] = $_POST['nome'];
    $arrUsuario['usu_nick'] = $_POST['nick'];
    $arrUsuario['usu_email'] = $_POST['email'];
    
    if($_POST['senha']){
      $arrUsuario['usu_senha'] = md5($_POST['senha']);
    }
    
    $usu_id = $_POST['usu_id'];
    
    $dataUsuarios = new usuariosModel();
    
    if($usu_id){  //atualizando usuario
      $dataUsuarios->updUsuario($arrUsuario, $usu_id);
      $this->update_projeto($usu_id, $_POST['projetos']);
      $arrReturn['text'] = "Usuario atualizado com sucesso!";
      $arrReturn['time'] = 1500;
      $arrReturn['status'] = "success";
      $arrReturn['redirect'] = "/usuario";
    }elseif($dataUsuarios->getUsuarios(null,$arrUsuario['usu_nick'])){  //verifico se usuario ja existe
      $arrReturn['text'] = "Esse usuário já existe no sistema";
      $arrReturn['time'] = 1500;
      $arrReturn['status'] = "error";
      $arrReturn['redirect'] = null;
    }else{
      $usu_id = $dataUsuarios->setUsuario($arrUsuario);
      $this->update_projeto($usu_id, $_POST['projetos']);
      $arrReturn['text'] = "Usuário gravado com sucesso!";
      $arrReturn['time'] = 1500;
      $arrReturn['status'] = "success";
      $arrReturn['redirect'] = '/usuario';
    }
    
    echo json_encode($arrReturn);
    die;
  }
  
  private function update_projeto($usu_id, $projetos){
    $dataProjeto = new projetosModel();
    $dataProjeto->delUsuariosProjetos($usu_id);
    
    $array['usu_id'] = $usu_id;
    foreach($projetos as $value){
      $array['pro_id'] = $value;
      $dataProjeto->setUsuariosProjetos($array);
    }
  }
  
  public function update_status(){
    
    $status = $this->getParam('sta_id');
    $usu_id = $this->getParam('usu_id');
    
    if($usu_id){
      $dataUsuarios = new usuariosModel();
      $arrUsuario['usu_status'] = $status;
      $dataUsuarios->updUsuario($arrUsuario, $usu_id);
      echo 1;
    }else{
      echo 0;
    }
    
  }

}

?>
