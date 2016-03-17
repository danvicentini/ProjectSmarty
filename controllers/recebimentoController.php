<?php
ini_set('memory_limit', '512M');

class recebimento extends controller
{

    public function index_action()
    {

        $this->smarty->assign('lat_default', $_SESSION['projeto']['latitude']);
        $this->smarty->assign('lng_default', $_SESSION['projeto']['longitude']);
        $this->smarty->assign('zoom', 5);
        $this->smarty->assign('api_key', GEOCODING_APIKEY);
        $this->smarty->assign('geo_url', GEOCODING_URL);
    }

    public function listar() {
        $dataConsumidor = new recebimentoModel();
        $this->smarty->assign('mensagem', null);

        if(isset($_FILES['importar']) && $_FILES['importar']['size'] > 0) {
        	
        	//$dataConsumidor->del();
        	
            $conteudo = file_get_contents($_FILES['importar']['tmp_name']);
            $conteudo = explode(PHP_EOL, $conteudo);

            $interador = 0;
            foreach($conteudo as $linha)
            {
                if(!empty($linha))
                {
                    if($interador == 0)
                    {
                    	$linha = str_replace('.http','',$linha);
                    	$linha = str_replace("_ID","",$linha);
                        $coluna = explode(';', $linha);
                    }
                    else
                    {
                        $dadosCliente = array();
                        foreach(explode(';', $linha) as $key => $eachLinha)
                        {
                            $linhaFinal = str_replace(array('"', "'"), array('', ''), $eachLinha);
                            $dadosCliente[trim($coluna[$key])] = utf8_encode($linhaFinal);
                        }

  
                        
                        $cliente="";
                        $id_orc=""; 
                        
                        $dataHora = date("d/m/Y h:i:s");
                        $dadosCliente[data_os] = $dataHora;
                        //$con_id = $dadosCliente['MEMENTO'];
                        //$cliente = $dataConsumidor->get($con_id);
                        //echo "OI2";
                        //echo $cliente;
                        //if (!$cliente) { 
                            
                        	unset($id_orc);
                        	
                        	$id_orc = $dataConsumidor->create($dadosCliente);
                        	if(!$id_orc) {
                            	$errors[] = '<span style="color:red">Erro na linha: <strong>' . $interador . '</strong></span>';
                        	}else {
                        		
                        		$dataOrc = array();
                        		$dataOrc[idorcamento] = $id_orc;
                        		$dataOrc[idcliente] = 0;
                        		$teste = $dataConsumidor->create_orc($dataOrc);
                        		$errors[] = '<span style="color:red">ID inserido: <strong>' . $id_orc . '</strong></span>';
                        	}      
                    }

                    $interador++;
                }
            }

            $mensagem = 'Importacao feita com sucesso!';

            if(count($errors) > 0)
                $mensagem = $mensagem . '<br />' . join($errors, '<br />');

            $this->smarty->assign('mensagem', $mensagem);
        }

        $this->template->fetchJS('/files/js/jquery_ui/js/jquery-ui-1.8.22.custom.min.js');
        $this->template->fetchCSS('/files/js/jquery_ui/css/blitzer/jquery-ui-1.8.22.custom.css');
        $this->template->fetchCSS('/files/js/toastmessage/resources/css/jquery.toastmessage.css');
        $this->template->fetchJS('/files/js/toastmessage/javascript/jquery.toastmessage.js');
        $this->template->fetchJS('/files/js/recebimento/listar.js');
        $this->template->fetchJS('/files/js/jquery.mask.js');
        $this->template->fetchJS('/files/js/jquery.dataTables.min.js');
  		$this->template->fetchCSS('/files/css/jquery.dataTables.min.css');
        
        

        //Combo de Status
        $listaStatusCliente['0'] = 'Status';
        $listaStatusCliente['VIABILIZADO'] = 'Viabilizado';
        $listaStatusCliente['REJEITADO'] = 'Rejeitado';
        $this->smarty->assign('lista_status_cliente', $listaStatusCliente);
        $this->smarty->assign('select_status_cliente', (isset($_POST['status_cliente']) ? $_POST['status_cliente'] : ''));

        $this->smarty->assign('user', $_SESSION['user']['usu_nick']);
        $this->smarty->assign('perfilUser', $_SESSION['user']['per_id']);

        $arrConsumidor = $dataConsumidor->getRecebimentos(null, $_POST);
        foreach($arrConsumidor as $keyConsumidor => $consumidor)
            $arrConsumidor[$keyConsumidor]['status_cliente'] = (isset($consumidor['status_cliente']) && !empty($consumidor['status_cliente'])) ? $listaStatusCliente[$arrConsumidor[$keyConsumidor]['status_cliente']] : '-';
        $this->smarty->assign('arrConsumidor', $arrConsumidor);
        $_SESSION['filtro_paginacao'] = $_POST;


        //Filtro
        $filtro = null;
        if(count($_POST) < 1)
            $filtro = array('municipio' => '', 'bairro' => '', 'status' => '', 'CPF' => '', 'NIS' => '');
        elseif(isset($_POST['pesquisar']))
            $filtro = $_POST;
        $this->smarty->assign('filtro', $filtro);

        if(isset($_POST['exportar']))
        {
            $this->exportar($arrConsumidor);
            exit();
        }

        $this->template->setTitle('Qualibras - Recebimento de Produtos');
        $this->template->run();
        $this->smarty->display('recebimento/listar.html');
    }

    public function find_dropdown($find, $objects)
    {
        $newObjects = array();
        foreach($objects as $key => $object)
            $newObjects[$key] = strtoupper(preg_replace('/[`^~\'"]/', null, iconv('UTF-8', 'ASCII//TRANSLIT', $object)));

        $newFind = strtoupper(preg_replace('/[`^~\'"]/', null, iconv('UTF-8', 'ASCII//TRANSLIT', $find)));

        return array_search($newFind, $newObjects);
    }

    public function exportar($dados)
    {
        header("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
        header("Cache-Control: no-cache, must-revalidate");
        header("Pragma: no-cache");
        header("Content-type: application/x-msexcel");
        header('Content-Disposition: attachment; filename="INSTAMAN_relatorio_clientes_' . date('YmdHm') . '.xls"');
        header("Content-Description: PHP Generated Data");

        $tabela = array();
        $tabela[] = '<table>';
        foreach($dados as $key => $linhas)
        {
            if($key === 0)
            {
                $tabela[] = '<tr>';

                $headers = array_keys($linhas);

                foreach($headers as $header)
                    $tabela[] = '<td><strong>' . $header . '</strong></td>';

                $tabela[] = '</tr>';
            }

            $tabela[] = '<tr>';

            foreach($linhas as $keyLinha => $linha)
            {

				$linha = utf8_decode($linha);

                $tabela[] = '<td>' . $linha . '</td>';
            }

            $tabela[] = '</tr>';
        }
        $tabela[] = '</table>';

        echo join(PHP_EOL, $tabela);
    }

    public function cadastro()
    {
        $this->template->fetchJS('/files/js/jquery_ui/js/jquery-ui-1.8.22.custom.min.js');
        $this->template->fetchCSS('/files/js/jquery_ui/css/blitzer/jquery-ui-1.8.22.custom.css');
        $this->template->fetchCSS('/files/js/toastmessage/resources/css/jquery.toastmessage.css');
        $this->template->fetchJS('/files/js/toastmessage/javascript/jquery.toastmessage.js');
        $this->template->fetchCSS('/files/js/fancybox/jquery.fancybox.css');
        $this->template->fetchJS('/files/js/fancybox/jquery.fancybox.js');
        $this->template->fetchJS('/files/js/jquery.numeric.js');
        $this->template->fetchJS('/files/js/jquery.mask.js');

        $vis = $this->getParam("vis");
        if(!$vis)
        {
            $this->template->fetchJS('/files/js/led_clientes/cadastro.js');
        }

        $listaCidades = $listaTipoBeneficio = $listaSituacaoCasa = $listaTensao = $listaTipoRede = $listaStatusCpfl = array();
        $listaTipoTelhado = $listaMatTelhado = $listaCondTelhado = $listaPosTelhado = $listaStatusCliente = $listaCidadeBase = array();
        $listaBeneficiarioFatura = $listaTrocarTitularidade = $listaCaixaDagua = $listaMedicaoEletrica = $listaPosteInstalado = array();

        $dataLocalidade = new localidadeModel();

        $arrCidades = $dataLocalidade->getCidades(26); //SP
        $listaCidades[0] = 'Selecione...';
        foreach($arrCidades as $cidades)
        {
            $listaCidades[$cidades['cid_id']] = $cidades['cid_nome'];
        }
        $this->smarty->assign('lista_cidades', $listaCidades);

        //$arrCidadesBase = $dataLocalidade->getCidadesBase(26);
        //$listaCidadesBase[''] = 'Selecione...';
        //foreach($arrCidadesBase as $cidadesBase)
        //    $listaCidadesBase[$cidadesBase['cid_base_id']] = $cidadesBase['cid_nome'];
        //$this->smarty->assign('lista_cid_base_id', $listaCidadesBase);

        
        
                
        $listaTipoBeneficio['0'] = 'Selecione...';
        $listaTipoBeneficio['aposentadoria'] = 'Aposentadoria';
        $listaTipoBeneficio['deficiente'] = 'BPC - Deficiente';
        $listaTipoBeneficio['bolsa_familia'] = 'Bolsa FamÃ­lia';
        $listaTipoBeneficio['aposentado'] = 'BPC - Aposentado';
        $listaTipoBeneficio['pensao'] = 'PensÃ£o INSS';
        $listaTipoBeneficio['sem'] = 'Sem BenefÃ­cio';
        $this->smarty->assign('lista_tipo_beneficio', $listaTipoBeneficio);

        $listaBeneficiarioFatura[''] = 'Selecione...';
        $listaBeneficiarioFatura['1'] = 'Sim';
        $listaBeneficiarioFatura['0'] = 'NÃ£o';
        $this->smarty->assign('lista_beneficiario_fatura', $listaBeneficiarioFatura);

        $listaTrocarTitularidade[''] = 'Selecione...';
        $listaTrocarTitularidade['1'] = 'SIM';
        $listaTrocarTitularidade['0'] = 'NÃƒÆ’O';
        $this->smarty->assign('lista_trocar_titularidade', $listaTrocarTitularidade);

        $listaCaixaDagua[''] = 'Selecione...';
        $listaCaixaDagua['1'] = 'Sim';
        $listaCaixaDagua['0'] = 'NÃ£o';
        $this->smarty->assign('lista_caixa_dagua', $listaCaixaDagua);

        $listaMedicaoEletrica[''] = 'Selecione...';
        $listaMedicaoEletrica['1'] = 'Sim';
        $listaMedicaoEletrica['0'] = 'NÃ£o';
        $this->smarty->assign('lista_medicao_eletrica', $listaMedicaoEletrica);

        $listaPosteInstalado[''] = 'Selecione...';
        $listaPosteInstalado['1'] = 'Sim';
        $listaPosteInstalado['0'] = 'NÃ£o';
        $this->smarty->assign('lista_poste_instalado', $listaPosteInstalado);





        //editando consumidor
        $con_id = $this->getParam("con_id");

        if(@$con_id)
        {
            $consumidorModel = new ledConsumidorModel();
            $arrConsumidor = $consumidorModel->getConsumidor($con_id);

            $arrConsumidor[0]['data_visita'] = (!empty($arrConsumidor[0]['data_visita'])) ? date('d/m/Y', strtotime($arrConsumidor[0]['data_visita'])) : '';
            $arrConsumidor[0]['data_nascimento'] = (!empty($arrConsumidor[0]['data_nascimento'])) ? date('d/m/Y', strtotime($arrConsumidor[0]['data_nascimento'])) : '';
            $arrConsumidor[0]['nasc_responsavel'] = (!empty($arrConsumidor[0]['nasc_responsavel'])) ? date('d/m/Y', strtotime($arrConsumidor[0]['nasc_responsavel'])) : '';
            $arrConsumidor[0]['data_entrega'] = (!empty($arrConsumidor[0]['data_entrega'])) ? date('d/m/Y', strtotime($arrConsumidor[0]['data_entrega'])) : '';
            $arrConsumidor[0]['data_instalacao'] = (!empty($arrConsumidor[0]['data_instalacao'])) ? date('d/m/Y', strtotime($arrConsumidor[0]['data_instalacao'])) : '';
            $arrConsumidor[0]['periodo_medicao_inicio'] = (!empty($arrConsumidor[0]['periodo_medicao_inicio'])) ? date('d/m/Y', strtotime($arrConsumidor[0]['periodo_medicao_inicio'])) : '';
            $arrConsumidor[0]['periodo_medicao_fim'] = (!empty($arrConsumidor[0]['periodo_medicao_fim'])) ? date('d/m/Y', strtotime($arrConsumidor[0]['periodo_medicao_fim'])) : '';

            $this->smarty->assign('arrConsumidor', $arrConsumidor[0]);

            //busco id do estado
            //$arrCidade = $dataLocalidade->getCidades(null, $arrConsumidor[0]['cid_id']);

            $latitude = !empty($arrConsumidor[0]['latitude']) ? $arrConsumidor[0]['latitude'] : $_SESSION['projeto']['latitude'];
            $longitude = !empty($arrConsumidor[0]['longitude']) ? $arrConsumidor[0]['longitude'] : $_SESSION['projeto']['longitude'];

            $this->smarty->assign('select_tipo_beneficio', $arrConsumidor[0]['tipo_beneficio']);
            
            
            $this->smarty->assign('lat_default', $latitude);
            $this->smarty->assign('lng_default', $longitude);
            $this->smarty->assign('zoom', 18);
        }
        else
        {
            $this->smarty->assign('select_cidade', null);
            $this->smarty->assign('lat_default', $_SESSION['projeto']['latitude']);
            $this->smarty->assign('lng_default', $_SESSION['projeto']['longitude']);
            $this->smarty->assign('zoom', 3);
        }

        //Geolocalizacao
        if(isset($arrConsumidor[0]))
            $geoEndereco = $tipoEndereco[$arrConsumidor[0]['tipo_endereco']] . ' ' . $arrConsumidor[0]['rua'] . ', ' . $arrConsumidor[0]['numero'] . ' - ' . $tipoBairro[$arrConsumidor[0]['tipo_bairro']] . ' ' . $arrConsumidor[0]['bairro'] . ' - ' . $arrConsumidor[0]['municipio'] . ' - Brasil';
        else
            $geoEndereco = null;

        $this->smarty->assign('geo_endereco', $geoEndereco);
        $latlon = $arrConsumidor[0]['posicao_gps'];
        $this->smarty->assign('latlon', $latlon);

        $this->smarty->assign('api_key', GEOCODING_APIKEY);
        $this->smarty->assign('geo_url', GEOCODING_URL);

        $this->template->setTitle('Instaman - Cadastro de Clientes');
        $this->template->run();

        if($vis)
        {
            $this->smarty->display('solar_clientes/visualizar.html');
        }
        else
        {
            $this->smarty->display('solar_clientes/cadastro.html');
        }
    }

    public function gravar_dados()
    {
        if((!isset($_POST['uc']) || empty($_POST['uc'])) && (!isset($_POST['nome']) || empty($_POST['nome'])) && (!isset($_POST['rg']) || empty($_POST['rg'])))
        {
            $arrReturn['msg'] = 'Campos obrigatorios vazios';
            $arrReturn['time'] = 2200;
            $arrReturn['status'] = 'success';
            $arrReturn['redirect'] = '/led_clientes/listar';

            $this->listar();

            return false;
        }

        $dadosConsumidor['uc'] = @$_POST['uc'];
        $dadosConsumidor['data_visita'] = implode("-", array_reverse(explode("/", @$_POST['data_visita'])));
        $dadosConsumidor['medidor'] = @$_POST['medidor'];
        $dadosConsumidor['cpf'] = @$_POST['cpf'];
        $dadosConsumidor['rg'] = @$_POST['rg'];
        $dadosConsumidor['data_nascimento'] = implode("-", array_reverse(explode("/", @$_POST['data_nascimento'])));
        $dadosConsumidor['nome'] = @$_POST['nome'];
        $dadosConsumidor['numero_nis'] = @$_POST['numero_nis'];
        $dadosConsumidor['tipo_beneficio'] = @$_POST['tipo_beneficio'];
        $dadosConsumidor['beneficiario_fatura'] = @$_POST['beneficiario_fatura'];
        $dadosConsumidor['nome_responsavel'] = @$_POST['nome_responsavel'];
        $dadosConsumidor['rg_responsavel'] = @$_POST['rg_responsavel'];
        $dadosConsumidor['cpf_responsavel'] = @$_POST['cpf_responsavel'];
        $dadosConsumidor['nasc_responsavel'] = implode("-", array_reverse(explode("/", @$_POST['nasc_responsavel'])));
        $dadosConsumidor['parentesco'] = @$_POST['parentesco'];
        $dadosConsumidor['rua'] = @$_POST['rua'];
        $dadosConsumidor['numero'] = @$_POST['numero'];
        $dadosConsumidor['bairro'] = @$_POST['bairro'];
        $dadosConsumidor['municipio'] = @$_POST['municipio'];
        $dadosConsumidor['complemento'] = @$_POST['complemento'];
        $dadosConsumidor['telefone'] = @$_POST['telefone'];
        $dadosConsumidor['celular'] = @$_POST['celular'];
        $dadosConsumidor['email'] = @$_POST['email'];
        $dadosConsumidor['melhor_dia'] = @$_POST['melhor_dia'];
        $dadosConsumidor['periodo'] = @$_POST['periodo'];
        $dadosConsumidor['tensao'] = @$_POST['tensao'];
        $dadosConsumidor['tensao_chuveiro'] = @$_POST['tensao_chuveiro'];
        $dadosConsumidor['tipo_telhado'] = @$_POST['tipo_telhado'];
        $dadosConsumidor['mat_telhado'] = @$_POST['mat_telhado'];
        $dadosConsumidor['caixa_dagua'] = @$_POST['caixa_dagua'];
        $dadosConsumidor['cond_telhado'] = @$_POST['cond_telhado'];
        $dadosConsumidor['pos_telhado'] = @$_POST['pos_telhado'];
        $dadosConsumidor['obs_tecnica'] = @$_POST['obs_tecnica'];
        $dadosConsumidor['status_cliente'] = @$_POST['status_cliente'];
        $dadosConsumidor['just_reprovacao'] = @$_POST['just_reprovacao'];
        $dadosConsumidor['status_cpfl'] = @$_POST['status_cpfl'];
        //$dadosConsumidor['cid_base_id'] = @$_POST['cid_base_id'];
        $dadosConsumidor['material_entregue'] = @$_POST['material_entregue'];
        $dadosConsumidor['data_entrega'] = implode("-", array_reverse(explode("/", @$_POST['data_entrega'])));
        $dadosConsumidor['just_n_entrega'] = @$_POST['just_n_entrega'];
        $dadosConsumidor['obs_n_entrega'] = @$_POST['obs_n_entrega'];
        $dadosConsumidor['num_serie_coletor'] = @$_POST['num_serie_coletor'];
        $dadosConsumidor['num_serie_reservatorio'] = @$_POST['num_serie_reservatorio'];
        $dadosConsumidor['num_serie_dispositivo'] = @$_POST['num_serie_dispositivo'];
        $dadosConsumidor['num_serie_kitled'] = @$_POST['num_serie_kitled'];
        $dadosConsumidor['instalacao_realizada'] = @$_POST['instalacao_realizada'];
        $dadosConsumidor['data_instalacao'] = implode("-", array_reverse(explode("/", @$_POST['data_instalacao'])));
        $dadosConsumidor['just_n_instalacao'] = @$_POST['just_n_instalacao'];
        $dadosConsumidor['obs_n_instalacao'] = @$_POST['obs_n_instalacao'];
        $dadosConsumidor['plano_acao'] = @$_POST['plano_acao'];
        $dadosConsumidor['resp_instalacao'] = @$_POST['resp_instalacao'];
        $dadosConsumidor['poste_instalado'] = @$_POST['poste_instalado'];
        $dadosConsumidor['tipo_endereco'] = @$_POST['tipo_endereco'];
        $dadosConsumidor['tipo_bairro'] = @$_POST['tipo_bairro'];
        $dadosConsumidor['medicao_eletrica'] = @$_POST['medicao_eletrica'];
        $dadosConsumidor['periodo_medicao_inicio'] = implode("-", array_reverse(explode("/", @$_POST['periodo_medicao_inicio'])));
        $dadosConsumidor['periodo_medicao_fim'] = implode("-", array_reverse(explode("/", @$_POST['periodo_medicao_fim'])));
        $dadosConsumidor['consumo_antes'] = @$_POST['consumo_antes'];
        $dadosConsumidor['demanda_antes'] = @$_POST['demanda_antes'];
        $dadosConsumidor['consumo_depois'] = @$_POST['consumo_depois'];
        $dadosConsumidor['demanda_depois'] = @$_POST['demanda_depois'];
        $dadosConsumidor['obs_referencial'] = @$_POST['obs_referencial'];
        $dadosConsumidor['lampada_1'] = @$_POST['lampada_1'];
        $dadosConsumidor['potencia_1'] = @$_POST['potencia_1'];
        $dadosConsumidor['lampada_2'] = @$_POST['lampada_2'];
        $dadosConsumidor['potencia_2'] = @$_POST['potencia_2'];
        $dadosConsumidor['lampada_3'] = @$_POST['lampada_3'];
        $dadosConsumidor['potencia_3'] = @$_POST['potencia_3'];

        $consumidorModel = new ledConsumidorModel();

        //UPDATE
        if(isset($_POST['con_id']) && !empty($_POST['con_id']))
        {
            //$consumidorModel->upd($dadosConsumidor, $_POST['con_id']);
            $arrReturn['msg'] = 'Dados atualizados com sucesso!';
        }
        //INSERT
        else
        {
            $_POST['con_id'] = $consumidorModel->create($dadosConsumidor);
            $arrReturn['msg'] = 'Dados gravados com sucesso!';
        }

        //UPLOAD
        $uploaddir = './files/documentos/';
        if(!is_dir($uploaddir))
            mkdir($uploaddir);

        foreach($_FILES as $key => $files)
        {
            if($files['size'] > 0)
            {
                $filename = md5(uniqid(time())) . strrchr($files['name'], ".");
                if(move_uploaded_file($files['tmp_name'], $uploaddir . $filename))
                    $dadosConsumidor[$key] = $filename;
            }
        }

        //UPDATE PHOTO
        $consumidorModel->upd($dadosConsumidor, $_POST['con_id']);

        $arrReturn['msg'] = 'Dados gravados com sucesso!';
        $arrReturn['time'] = 2200;
        $arrReturn['status'] = 'success';
        $arrReturn['redirect'] = '/led_clientes/listar';

        $this->listar();
    }

    public function apagar_arquivo()
    {
        $arquivo = $this->getParam("arquivo");
        $con_id = $this->getParam("con_id");
        $campo = $this->getParam("campo");

        $arrConsumidor[$campo] = null;
        $consumidorModel = new ledConsumidorModel();
        $consumidorModel->upd($arrConsumidor, $con_id);

        if(file_exists('./files/documentos/' . $arquivo))
            unlink('./files/documentos/' . $arquivo);

        $arrReturn['msg'] = 'Arquivo excluÃ­do com sucesso!';
        $arrReturn['time'] = 2200;
        $arrReturn['status'] = 'success';
        echo json_encode($arrReturn);
    }

    public function excluir()
    {
        $con_id = $this->getParam("con_id");
        $consumidorModel = new ledConsumidorModel();
        $consumidorModel->del($con_id);

        $arrReturn['msg'] = "Dados excluÃ­dos com sucesso";
        $arrReturn['time'] = 1500;
        $arrReturn['status'] = "success";
        $arrReturn['redirect'] = '/led_clientes/listar';
        echo json_encode($arrReturn);
    }

    public function paginar()
    {
        $consumidorModel = new recebimentoModel();
        $_SESSION['total_registro'] = NULL;

        $filtro = (isset($_SESSION['filtro_paginacao']))?$_SESSION['filtro_paginacao']:NULL;

        $param = $this->getParamsDirtURL();
        $results = $consumidorModel->getRecebimentos(null, $filtro, $param['iDisplayLength'], $param['iDisplayStart']);

        if($results) {
            $retorno = array(
                'draw' => $param['sEcho'],
                'recordsTotal' => $_SESSION['total_registro'],
                'recordsFiltered' => $_SESSION['total_registro']
            );

            $perfilUser = $_SESSION['user']['per_id'];

            foreach($results as $result) {
                    $botao = "<center>
                                <a title='Editar' href='/led_clientes/cadastro/con_id/{$result['con_id']}'><img title='Editar' src='/files/images/edit-6.png' /></a>&nbsp;
                                <!--<a title='Visualizar' href='/led_clientes/cadastro/con_id/{$result['con_id']}/vis/1'><img title='Visualizar' src='/files/images/map-magnify.png' /></a>&nbsp;-->
                                <!--<a title='Excluir' href='javascript:excluir({$result['con_id']})'><img title='Excluir' src='/files/images/dialog-cancel-3.png' /></a>&nbsp;-->
                              </center>";
                


                $retorno['data'][] = array(
                    $result['fabricante'],
                    $result['cliente'],
                    $result['descricao'],
                    $result['modelo'],
                    $result['cod_sap'],
                    $botao
                );
            }

            echo json_encode($retorno);
        } else {
            echo json_encode(array(
                'draw' => 1,
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => array()
            ));
        }
    }
}