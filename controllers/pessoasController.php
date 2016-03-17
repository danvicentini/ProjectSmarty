<?php

class pessoas extends controller
{

    public function index_action()
    {

        $this->smarty->assign('lat_default', $_SESSION['projeto']['latitude']);
        $this->smarty->assign('lng_default', $_SESSION['projeto']['longitude']);
        $this->smarty->assign('zoom', 5);
        $this->smarty->assign('api_key', GEOCODING_APIKEY);
        $this->smarty->assign('geo_url', GEOCODING_URL);
    }

    public function listar(){
			$dataPessoas = new pessoasModel();
			$this->smarty->assign('mensagem', null);



			$this->template->fetchJS('/files/js/jquery_ui/js/jquery-ui-1.8.22.custom.min.js');
			$this->template->fetchCSS('/files/js/jquery_ui/css/blitzer/jquery-ui-1.8.22.custom.css');
			$this->template->fetchCSS('/files/js/toastmessage/resources/css/jquery.toastmessage.css');
			$this->template->fetchJS('/files/js/toastmessage/javascript/jquery.toastmessage.js');
			$this->template->fetchJS('/files/js/pessoas/listar.js');
			$this->template->fetchJS('/files/js/jquery.mask.js');

			
			//$local = !empty($_POST['endereco']) ? $_POST['endereco'] : null;
			$pagina = !empty($_POST['pagina']) ? $_POST['pagina'] : 1;
			$offset = PAGINACAO * ($pagina - 1);
			
			//echo "$pagina";
			
			$arrPessoas = $dataPessoas->getPessoas(null, $_POST, PAGINACAO, $offset);
			$total_linhas = count($dataPessoas->getPessoas(null));

			$this->smarty->assign('user', $_SESSION['user']['usu_nick']);
			$this->smarty->assign('perfilUser', $_SESSION['user']['per_id']);

			$this->smarty->assign('arrPessoas', $arrPessoas);


			$this->smarty->assign('total_linhas', $total_linhas);
			$this->smarty->assign('paginacao', $this->template->paginacao($total_linhas));
			$this->template->setTitle('Qualibras - Lista de Contatos');
			$this->template->run();
			//$html_lista_cliente = $this->smarty->fetch('solar_clientes/listar.html');
			//echo $html_lista_cliente;
			
			$this->template->run();
			$this->smarty->display('pessoas/listar.html');
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

    public function cadastro(){
			$this->template->fetchJS('/files/js/jquery_ui/js/jquery-ui-1.8.22.custom.min.js');
			$this->template->fetchCSS('/files/js/jquery_ui/css/blitzer/jquery-ui-1.8.22.custom.css');
			$this->template->fetchCSS('/files/js/toastmessage/resources/css/jquery.toastmessage.css');
			$this->template->fetchJS('/files/js/toastmessage/javascript/jquery.toastmessage.js');
			$this->template->fetchCSS('/files/js/fancybox/jquery.fancybox.css');
			$this->template->fetchJS('/files/js/fancybox/jquery.fancybox.js');
			$this->template->fetchJS('/files/js/jquery.numeric.js');
			$this->template->fetchJS('/files/js/jquery.mask.js');

			$vis = $this->getParam("vis");
			if(!$vis){
				$this->template->fetchJS('/files/js/pessoas/cadastro.js');
			}
		 
			//editando consumidor
			$pes_id = $this->getParam("pes_id");
			$dataPessoas = new pessoasModel();
			$arrEmpresas = $dataPessoas->getClienteProprietario();
			foreach($arrEmpresas AS $key => $value){
				$listaEmpresas[$value['cod_empresa']] = $value['nom_nome_fantasia'];
			}
			$this->smarty->assign('listaEmpresas', $listaEmpresas);
			if(@$pes_id){
				
				$arrPessoas = array();
				
				
				$arrPessoas = $dataPessoas->getPessoas($pes_id,null);
				$this->smarty->assign('arrPessoa', $arrPessoas[0]);
				$this->smarty->assign('descricao', $arrPessoas[0]['descricao']);
					
			}
			else{
				$this->smarty->assign('select_cidade', null);
				$this->smarty->assign('lat_default', $_SESSION['projeto']['latitude']);
				$this->smarty->assign('lng_default', $_SESSION['projeto']['longitude']);
				$this->smarty->assign('zoom', 3);
					
			}

			
			$this->template->setTitle('Qualibr&aacute;s - Cadastro de Pessoas');
			$this->template->run();

			if($vis)
			{
					$this->smarty->display('pessoas/visualizar.html');
			}
			else
			{
					$this->smarty->display('pessoas/cadastro.html');
			}
    }

    public function gravar_dados()
    {
			$dadosPessoas['nom_funcionario'] = addslashes(@$_POST['nom_funcionario']);
			$dadosPessoas['cpf'] = @$_POST['cpf'];
			$dadosPessoas['endereco'] = addslashes(@$_POST['endereco']);
			$dadosPessoas['telefone'] = @$_POST['telefone'];
			$dadosPessoas['celular'] = @$_POST['celular'];
			$dadosPessoas['email'] = addslashes(@$_POST['email']);
			$dadosPessoas['cod_empresa'] = @$_POST['cod_empresa'];
			
		 	
			$pessoasModel = new pessoasModel();

			//UPDATE
			if(isset($_POST['cod_funcionario']) && !empty($_POST['cod_funcionario'])){
					$pessoasModel->upd($dadosPessoas, $_POST['cod_funcionario']);
					$arrReturn['msg'] = 'Dados atualizados com sucesso!';
			}
			//INSERT
			else{
					$_POST['cod_funcionario'] = $pessoasModel->create($dadosPessoas);
					$arrReturn['msg'] = 'Dados gravados com sucesso!';
					$this->listar();
			}

			
			$arrReturn['msg'] = 'Dados gravados com sucesso!';
			$arrReturn['time'] = 2200;
			$arrReturn['status'] = 'success';
			$arrReturn['redirect'] = '/pessoas/listar';

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
        $pes_id = $_POST['pessoaId'];
        $pessoasModel = new pessoasModel();
        $pessoasModel->del($pes_id);

        $arrReturn['msg'] = "Dados excluídos com sucesso";
        $arrReturn['time'] = 1500;
        $arrReturn['status'] = "success";
        $arrReturn['redirect'] = '/pessoas/listar';
        echo json_encode($arrReturn);
    }

}