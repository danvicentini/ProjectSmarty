<?php

class itens extends controller
{

    public function index_action()
    {

        $this->smarty->assign('lat_default', $_SESSION['projeto']['latitude']);
        $this->smarty->assign('lng_default', $_SESSION['projeto']['longitude']);
        $this->smarty->assign('zoom', 5);
        $this->smarty->assign('api_key', GEOCODING_APIKEY);
        $this->smarty->assign('geo_url', GEOCODING_URL);
    }

    public function listar()
    {
        $dataProdutos = new produtosModel();
        $this->smarty->assign('mensagem', null);



        $this->template->fetchJS('/files/js/jquery_ui/js/jquery-ui-1.8.22.custom.min.js');
        $this->template->fetchCSS('/files/js/jquery_ui/css/blitzer/jquery-ui-1.8.22.custom.css');
        $this->template->fetchCSS('/files/js/toastmessage/resources/css/jquery.toastmessage.css');
        $this->template->fetchJS('/files/js/toastmessage/javascript/jquery.toastmessage.js');
        $this->template->fetchJS('/files/js/produtos/listar.js');
        $this->template->fetchJS('/files/js/jquery.mask.js');

        
        //$local = !empty($_POST['endereco']) ? $_POST['endereco'] : null;
        $pagina = !empty($_POST['pagina']) ? $_POST['pagina'] : 1;
        $offset = PAGINACAO * ($pagina - 1);
        
        //echo "$pagina";
        
        $arrProduto = $dataProdutos->getProduto(null, $_POST, PAGINACAO, $offset);
        $total_linhas = count($dataProdutos->getProduto(null));

        $this->smarty->assign('user', $_SESSION['user']['usu_nick']);
        $this->smarty->assign('perfilUser', $_SESSION['user']['per_id']);

        $this->smarty->assign('arrProduto', $arrProduto);


        $this->smarty->assign('total_linhas', $total_linhas);
    	$this->smarty->assign('paginacao', $this->template->paginacao($total_linhas));
        $this->template->setTitle('Qualibras - Lista de Produtos');
        $this->template->run();
        //$html_lista_cliente = $this->smarty->fetch('solar_clientes/listar.html');
		//echo $html_lista_cliente;
        
        $this->template->run();
       	$this->smarty->display('produtos/listar.html');
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
        header('Content-Disposition: attachment; filename="solar_relatorio_clientes_' . date('YmdHm') . '.xls"');
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
                $linha = ($keyLinha == 'data_visita') ? ((!empty($linha)) ? implode("-", array_reverse(explode("/", $linha))) : null) : $linha;
                $linha = ($keyLinha == 'data_nascimento') ? ((!empty($linha)) ? implode("-", array_reverse(explode("/", $linha))) : null) : $linha;
                $linha = ($keyLinha == 'data_entrega') ? ((!empty($linha)) ? implode("-", array_reverse(explode("/", $linha))) : null) : $linha;
                $linha = ($keyLinha == 'data_instalacao') ? ((!empty($linha)) ? implode("-", array_reverse(explode("/", $linha))) : null) : $linha;
                $linha = ($keyLinha == 'periodo_medicao_inicio') ? ((!empty($linha)) ? implode("-", array_reverse(explode("/", $linha))) : null) : $linha;
                $linha = ($keyLinha == 'periodo_medicao_fim') ? ((!empty($linha)) ? implode("-", array_reverse(explode("/", $linha))) : null) : $linha;
                $linha = ($keyLinha == 'nasc_responsavel') ? ((!empty($linha)) ? implode("-", array_reverse(explode("/", $linha))) : null) : $linha;

                $simnao = array('1' => 'Sim', '0' => 'NÃ£o');
                $linha = ($keyLinha == 'beneficiario_fatura') ? $simnao[$linha] : $linha;
                $linha = ($keyLinha == 'caixa_dagua') ? $simnao[$linha] : $linha;
                $linha = ($keyLinha == 'material_entregue') ? $simnao[$linha] : $linha;
                $linha = ($keyLinha == 'instalacao_realizada') ? $simnao[$linha] : $linha;
                $linha = ($keyLinha == 'poste_instalado') ? $simnao[$linha] : $linha;
                $linha = ($keyLinha == 'medicao_eletrica') ? $simnao[$linha] : $linha;

                $beneficio = array('aposentadoria' => 'Aposentadoria', 'deficiente' => 'BPC - Deficiente', 'bolsa_familia' => 'Bolsa FamÃ­lia', 'aposentado' => 'BPC - Aposentado', 'pensao' => 'PensÃ£o INSS', 'sem' => 'Sem Beneficio');
                $linha = ($keyLinha == 'tipo_beneficio') ? $beneficio[$linha] : $linha;

                $parentesco = array('irmao' => 'IrmÃ£o (a)', 'avo' => 'AvÃ³ - AvÃ´', 'pai' => 'Pai - MÃ£e', 'filho' => 'Filho (a)', 'neto' => 'Neto (a)', 'tio' => 'Tio (a)', 'primo' => 'Primo (a)', 'vizinho' => 'Vizinho (a)', 'amigo' => 'Amigo (a)', 'locatario' => 'LocatÃ¡rio', 'esposa' => 'Esposa', 'esposo' => 'Esposo');
                $linha = ($keyLinha == 'parentesco') ? $parentesco[$linha] : $linha;

                $endereco = array('alameda' => 'Alameda', 'avenida' => 'Avenida', 'caminho' => 'Caminho', 'estrada' => 'Estrada', 'largo' => 'Largo', 'passarela' => 'Passarela', 'praca' => 'PraÃ§a', 'rua' => 'Rua', 'trecho' => 'Trecho', 'vale' => 'Vale', 'via' => 'Via');
                $linha = ($keyLinha == 'tipo_endereco') ? $endereco[$linha] : $linha;

                $bairro = array('aeroporto' => 'Aeroporto', 'area' => 'Ã�rea', 'campo' => 'Campo', 'chacara' => 'ChÃ¡cara', 'colonia' => 'ColÃ´nia', 'condominio' => 'CondomÃ­nio', 'conjunto' => 'Conjunto', 'distrito' => 'Distrito', 'esplanada' => 'Esplanada', 'estacao' => 'EstaÃ§Ã£o', 'favela' => 'Favela', 'feira' => 'Feira', 'jardim' => 'Jardim', 'lagoa' => 'Lagoa', 'loteamento' => 'Loteamento', 'morro' => 'Morro', 'nucleo' => 'NÃºcleo', 'parque' => 'Parque', 'patio' => 'PÃ¡tio', 'praca' => 'PraÃ§a', 'quadra' => 'Quadra', 'residencial' => 'Residencial', 'setor' => 'Setor', 'sitio' => 'SÃ­tio', 'vila' => 'Vila');
                $linha = ($keyLinha == 'tipo_bairro') ? $bairro[$linha] : $linha;

                $semana = array('qualquer' => 'Qualquer dia', 'seg' => 'Segunda-Feira', 'ter' => 'TerÃ§a-Feira', 'qua' => 'Quarta-Feira', 'qui' => 'Quinta-Feira', 'sex' => 'Sexta-Feira', 'sab' => 'SÃ¡bado', 'dom' => 'Domingo');
                $linha = ($keyLinha == 'melhor_dia') ? $semana[$linha] : $linha;

                $periodo = array('manha' => 'ManhÃ£', 'tarde' => 'Tarde');
                $linha = ($keyLinha == 'periodo') ? $periodo[$linha] : $linha;

                $tipoTelhado = array('1' => 'Duas Ã¡guas frontal', '2' => 'Duas Ã¡guas lateral', '3' => 'Quatro Ã¡guas', '4' => 'Uma Ã¡gua', '5' => 'Irregular', '6' => 'Somente laje');
                $linha = ($keyLinha == 'tipo_telhado') ? $tipoTelhado[$linha] : $linha;

                $matTelhado = array('brasilit' => 'Brasilit', 'ceramica' => 'CerÃ¢mica', 'outros' => 'Outros');
                $linha = ($keyLinha == 'mat_telhado') ? $matTelhado[$linha] : $linha;

                $condTelhado = array('funcional' => 'Funcional', 'deteriorado' => 'Deteriorado', 'nao_disponivel' => 'NÃ£o disponÃ­vel');
                $linha = ($keyLinha == 'cond_telhado') ? $condTelhado[$linha] : $linha;

                $posTelhado = array('n1' => 'Norte BÃºssola', 'n2' => '45Â°, sentido horÃ¡rio, a partir do norte da bÃºssola', 'n3' => 'Leste da bÃºssola', 'n4' => '45Â°, sentido horÃ¡rio, a partir do leste da bÃºssola', 'n5' => 'Sul da bÃºssola', 'n6' => '45Â°, sentido horÃ¡rio, a partir do sul da bÃºssola', 'n7' => 'Oeste da bÃºssola', 'n8' => '45Â° sentido horÃ¡rio, a partir do oeste da bÃºssola');
                $linha = ($keyLinha == 'pos_telhado') ? $posTelhado[$linha] : $linha;

                $justificativa = array('ausente' => 'Ausente', 'mudou' => 'Mudou-se', 'menor' => 'Menor de idade na residÃªncia', 'nao_localizado' => 'EndereÃ§o nÃ£o localizado', 'recusou' => 'Cliente recusou o material', 'extravio' => 'Material extraviado/ roubo');
                $linha = ($keyLinha == 'just_n_entrega') ? $justificativa[$linha] : $linha;
                $linha = ($keyLinha == 'just_n_instalacao') ? $justificativa[$linha] : $linha;

                $plano = array('retorno' => 'Retorno na residÃªncia', 'retirar' => 'Retirar Material', 'extravio' => 'Extravio de Material');
                $linha = ($keyLinha == 'plano_acao') ? $plano[$linha] : $linha;

                $respo = array('powersolar' => 'Power Solar', 'grotech' => 'Grotech', 'cpfl' => 'CPFL', 'vitalis' => 'Vitalis');
                $linha = ($keyLinha == 'resp_instalacao') ? $respo[$linha] : $linha;

                //$status = array('sem' => 'Cliente sem diagnÃ³stico', 'reprovado' => 'Reprovado no diagnÃ³stico', 'aprovado' => 'Aprovado no diagnÃ³stico', 'mat_nentregue' => 'Material nÃ£o entregue', 'mat_entregue' => 'Material entregue', 'nao_instalado' => 'NÃ£o instalado', 'instalado' => 'Instalado', 'nao_instalado_critico' => 'NÃ£o instalado crÃ­tico');
                //$linha = ($keyLinha == 'status_cliente') ? $status[$linha] : $linha;

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
            $this->template->fetchJS('/files/js/produtos/cadastro.js');
        }
       
        //editando consumidor
        $prd_id = $this->getParam("prd_id");
        
        //Pegando clientes do banco
        $dataClientes = new clientesModel();
        $arrClientes = $dataClientes->getCliente();
    		foreach ($arrClientes as $cliente) {
      	$listaClientes[$cliente['idcliente']] = $cliente['razao_social']." - ".$cliente['nome_fantasia'];
    	}
    	$this->smarty->assign('lista_clientes', $listaClientes);
    	$this->smarty->assign('select_cliente', $_SESSION['projeto']['est_id']);  //iniciando com o estado de SP
        

        if($prd_id>0)
        {
        	echo "OI1 $prd_id";
        	$dataProdutos = new produtosModel();
            
            $arrProduto = array();
            $arrProduto = $dataProdutos->getProduto($prd_id,null);
		    echo " OI2 ";        
            $this->smarty->assign('prd_id', $prd_id);
		    $this->smarty->assign('arrProduto', $arrProduto[0]);
            
            
         }
        else
        {
            $this->smarty->assign('select_cidade', null);
            $this->smarty->assign('lat_default', $_SESSION['projeto']['latitude']);
            $this->smarty->assign('lng_default', $_SESSION['projeto']['longitude']);
            $this->smarty->assign('zoom', 3);
            
        }

        
        $this->template->setTitle('Qualibrás - Cadastro de Produtos');
        $this->template->run();

        if($vis)
        {
            $this->smarty->display('produtos/visualizar.html');
        }
        else
        {
            $this->smarty->display('produtos/cadastro.html');
        }
    }

    public function gravar_dados()
    {
        
        $dadosProduto['acessorios'] = @$_POST['acessorios'];
        $dadosProduto['cod_fabricante'] = @$_POST['cod_fabricante'];
        $dadosProduto['idcliente'] = @$_POST['idcliente'];
        $dadosProduto['cod_sap'] = @$_POST['cod_sap'];
        $dadosProduto['patrimonio'] = @$_POST['patrimonio'];
        $dadosProduto['descricao'] = @$_POST['descricao'];
        $dadosProduto['modelo'] = @$_POST['modelo'];
        $dadosProduto['fabricante'] = @$_POST['fabricante'];
        $dadosProduto['cliente_prop'] = @$_POST['cliente_prop'];
        $dadosProduto['n_serie'] = @$_POST['n_serie'];
        $dadosProduto['obs'] = @$_POST['obs'];
        
        $produtosModel = new produtosModel();

        //UPDATE
        if(isset($_POST['prd_id']) && !empty($_POST['prd_id']))
        {
        	echo "OI";
            $produtosModel->upd($dadosProduto, $_POST['prd_id']);
            $arrReturn['msg'] = 'Dados atualizados com sucesso!';
        }
        //INSERT
        else
        {
            $_POST['prd_id'] = $produtosModel->create($dadosProduto);
            $arrReturn['msg'] = 'Dados gravados com sucesso!';
            $this->listar();
        }

        
        $arrReturn['msg'] = 'Dados gravados com sucesso!';
        $arrReturn['time'] = 2200;
        $arrReturn['status'] = 'success';
        $arrReturn['redirect'] = '/produtos/listar';

        $this->listar();
    }

    public function apagar_arquivo()
    {
        $arquivo = $this->getParam("arquivo");
        $con_id = $this->getParam("con_id");
        $campo = $this->getParam("campo");

        $arrConsumidor[$campo] = null;
        $consumidorModel = new solarConsumidorModel();
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
        $consumidorModel = new solarConsumidorModel();
        $consumidorModel->del($con_id);

        $arrReturn['msg'] = "Dados excluÃ­dos com sucesso";
        $arrReturn['time'] = 1500;
        $arrReturn['status'] = "success";
        $arrReturn['redirect'] = '/solar_clientes/listar';
        echo json_encode($arrReturn);
    }

}