<?php

class orcamento extends controller
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
				
        $dataOrcamento = new orcamentoModel();
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
        
				if($_POST['pesquisar']){
					$filtro['idorcamento'] = $_POST['idorcamento'];
					$filtro['descricao'] = $_POST['descricao'];
					$filtro['modelo'] = $_POST['modelo'];
					$filtro['pesquisar'] = $_POST['pesquisar'];
				}
        $arrOrcamento = $dataOrcamento->getOrcamento(null, $filtro, PAGINACAO, $offset);
				
        $total_linhas = count($dataOrcamento->getOrcamento(null, $filtro));

        $this->smarty->assign('user', $_SESSION['user']['usu_nick']);
        $this->smarty->assign('perfilUser', $_SESSION['user']['per_id']);

        $this->smarty->assign('arrOrcamento', $arrOrcamento);


      
        //$html_lista_cliente = $this->smarty->fetch('solar_clientes/listar.html');
		//echo $html_lista_cliente;
        
				if($_POST['exportar']){
					$this->exportar($arrOrcamento);
				} else {
					$this->smarty->assign('total_linhas', $total_linhas);
					$this->smarty->assign('paginacao', $this->template->paginacao($total_linhas));
					$this->template->setTitle('Qualibras - Or&ccedil;amentos');
					$this->template->run();
					$this->template->run();
					$this->smarty->display('orcamento/listar.html');
				}
        
    }
		
		public function visualizar(){
			$dataOrcamento = new orcamentoModel();
        $this->smarty->assign('mensagem', null);
				$prd_id = $this->getParam("prd_id"); 
				$boolPDF = $this->getParam("pdf"); 
				if(!empty($boolPDF)){
					$this->smarty->assign('boolPDF', 1);
				} else {
					$this->smarty->assign('boolPDF', 0);
				}
				
				//echo $prd_id;
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
        
        $arrOrcamento = $dataOrcamento->getOrcamento($prd_id);

				
				$arrComponentes = $dataOrcamento->getComponentes(@$prd_id);

				$arrServicoOrcamento = $dataOrcamento->getServicosAll(@$prd_id);
				//arruma os selects
				$arrDevolvido = $dataOrcamento->getCondicoes(1, $arrOrcamento[0]['cod_condicao_devolucao']);
				$arrOrcamento[0]['cod_condicao_devolucao'] = $arrDevolvido[0]['nom_condicao'];
				
				$arrComercial = $dataOrcamento->getStatusOrcamento($arrOrcamento[0]['cod_comercial']);
				$arrOrcamento[0]['cod_comercial'] = $arrComercial[0]['nom_tipo'];
				
				$arrEmpresas = $dataOrcamento->getEmpresa($arrOrcamento[0]['cod_empresa_envio']);
				$arrOrcamento[0]['cod_empresa_envio'] = $arrEmpresas[0]['nom_nome_fantasia'];
				$arrLabs = $dataOrcamento->getLab($arrOrcamento[0]['cod_lab']);
				$arrOrcamento[0]['cod_lab'] = $arrLabs[0]['nom_laboratorio'];
				$arrLabs = $dataOrcamento->getLab($arrOrcamento[0]['cod_lab2']);
				$arrOrcamento[0]['cod_lab2'] = $arrLabs[0]['nom_laboratorio'];
				$arrLabs = $dataOrcamento->getLab($arrOrcamento[0]['cod_lab3']);
				$arrOrcamento[0]['cod_lab3'] = $arrLabs[0]['nom_laboratorio'];
				$arrStatOS = $dataOrcamento->getStatusOS($arrOrcamento[0]['status_os']);
				$arrOrcamento[0]['status_os'] = $arrLabs[0]['nom_status_os'];
				
				//$arrDevolvido = $dataOrcamento->getCondicoes(1, $arrOrcamento[0]['cond_dev']);
				//$arrOrcamento[0]['cond_dev'] = $arrEmpresas[0]['nom_condicao'];
				$arrTecnico = $dataOrcamento->getTecnico($arrOrcamento[0]['cod_tecnico_executa']);
				$arrOrcamento[0]['cod_tecnico_executa'] = $arrTecnico[0]['nom_nome_fantasia'];
				
				//end arruma os selects		
        $this->smarty->assign('arrComponentes', $arrComponentes);
				$this->smarty->assign('arrServicoOrcamento', $arrServicoOrcamento);
				$this->smarty->assign('user', $_SESSION['user']['usu_nick']);
        $this->smarty->assign('perfilUser', $_SESSION['user']['per_id']);

        $this->smarty->assign('arrOrcamento', $arrOrcamento);


        $this->smarty->assign('total_linhas', $total_linhas);
    	$this->smarty->assign('paginacao', $this->template->paginacao($total_linhas));
        $this->template->setTitle('Qualibras - Or&ccedil;amentos');
        $this->template->run();
        //$html_lista_cliente = $this->smarty->fetch('solar_clientes/listar.html');
		//echo $html_lista_cliente;
        
        $this->template->run();
       	$this->smarty->display('orcamento/visualizar.html');
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
        header('Content-Disposition: attachment; filename="orcamentosQualibras' . date('YmdHm') . '.xls"');
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

        	
        #$dataClientes = new clientesModel();
        #$arrClientes = $dataClientes->getCliente();
    #		foreach ($arrClientes as $cliente) {
    #  	$listaClientes[$cliente['idcliente']] = $cliente['razao_social']." - ".$cliente['nome_fantasia'];
   # 	}
   # 	$this->smarty->assign('lista_clientes', $listaClientes);
   # 	$this->smarty->assign('select_cliente', $_SESSION['projeto']['est_id']);  //iniciando com o estado de SP
        
        
        
        
        $vis = $this->getParam("vis");
        if(!$vis)
        {
            $this->template->fetchJS('/files/js/orcamento/cadastro.js');
        }
       
        //editando consumidor
        $prd_id = $this->getParam("prd_id");
				$dataOrcamento = new orcamentoModel();
				$arrComercial = $dataOrcamento->getStatusOrcamento();
				
				foreach($arrComercial AS $key => $value){
					$listaComercial[$value['cod_tipo_comercial']] = $value['nom_tipo']; 
				}
				//Combo de Lista Devolvidos
				$arrDevolvido = $dataOrcamento->getCondicoes(1);
				foreach($arrDevolvido AS $key => $value){
					$listaDevolvido[$value['cod_condicao']] = $value['nom_condicao']; 
				}	//Combo de Laboratorios
				$arrlabs = $dataOrcamento->getLab();
				foreach($arrlabs AS $key => $value){
					$listaLabs[$value['cod_lab']] = $value['nom_laboratorio']; 
				}	
				//Combo de Empresas
				$arrEmpresas = $dataOrcamento->getEmpresa();
				 
				$listaEmpresas[0] = ' - Selecione -';
				foreach($arrEmpresas AS $key => $value){
					$listaEmpresas[$value['cod_empresa']] = $value['nom_razao_social']; 
				}	
				//Combo de Status OS
				$arrStatusOS = $dataOrcamento->getStatusOS();
				 
				$listaStatusOs[0] = ' - Selecione -';
				foreach($arrStatusOS AS $key => $value){
					$listaStatusOs[$value['cod_status_os']] = $value['nom_status_os']; 
				}	
				//Combo de Tecnico
				$arrTecnico = $dataOrcamento->getTecnico();
				 
				$listaTecnicos[0] = ' - Selecione -';
				foreach($arrTecnico AS $key => $value){
					$listaTecnicos[$value['cod_funcionario']] = $value['nom_funcionario']; 
				}	
				
				$listaCondDevolvido['ok'] = 'OK';
        $listaCondDevolvido['nok'] = 'Nao OK';
				$arrServicos = $dataOrcamento->getServicosAll();
        if(@$prd_id){
        	
					$arrComponentes = $dataOrcamento->getComponentes(@$prd_id);

        	$arrServicoOrcamento = $dataOrcamento->getServicosAll(@$prd_id);
        	
        	
        	$arrOrcamento = $dataOrcamento->getOrcamento($prd_id,null,null, null);
					$arrOrcamento[0]['dat_aprovacao_orcamento'] = (!is_null($arrOrcamento[0]['dat_aprovacao_orcamento'])? date( "d/m/Y", strtotime( $arrOrcamento[0]['dat_aprovacao_orcamento'] ) ) : null);
					$arrOrcamento[0]['dat_enc_rnc'] = (!is_null($arrOrcamento[0]['dat_enc_rnc'])? date( "d/m/Y", strtotime( $arrOrcamento[0]['dat_enc_rnc'] ) ) : null);
					$arrOrcamento[0]['dat_liberacao_expedicao'] = (!is_null($arrOrcamento[0]['dat_liberacao_expedicao'])? date( "d/m/Y", strtotime( $arrOrcamento[0]['dat_liberacao_expedicao'] ) ) : null);
					$arrOrcamento[0]['dat_liberacao_documento'] = (!is_null($arrOrcamento[0]['dat_liberacao_documento'])? date( "d/m/Y", strtotime( $arrOrcamento[0]['dat_liberacao_documento'] ) ) : null);
					$arrOrcamento[0]['dat_nf_devolucao'] = (!is_null($arrOrcamento[0]['dat_nf_devolucao'])? date( "d/m/Y", strtotime( $arrOrcamento[0]['dat_nf_devolucao'] ) ) : null);
					$arrOrcamento[0]['dat_nf_envio_fornecedor'] = (!is_null($arrOrcamento[0]['dat_nf_envio_fornecedor'])? date( "d/m/Y", strtotime( $arrOrcamento[0]['dat_nf_envio_fornecedor'] ) ) : null);
					$arrOrcamento[0]['dat_nf_recebimento'] = (!is_null($arrOrcamento[0]['dat_nf_recebimento'])? date( "d/m/Y", strtotime( $arrOrcamento[0]['dat_nf_recebimento'] ) ) : null);
					$arrOrcamento[0]['dat_os'] = (!is_null($arrOrcamento[0]['dat_os'])? date( "d/m/Y", strtotime( $arrOrcamento[0]['dat_os'] ) ) : null);
					$arrOrcamento[0]['dat_encerramento_os'] = (!is_null($arrOrcamento[0]['dat_encerramento_os'])? date( "d/m/Y", strtotime( $arrOrcamento[0]['dat_encerramento_os'] ) ) : null);
					$arrOrcamento[0]['dat_entrada_lab'] = (!is_null($arrOrcamento[0]['dat_entrada_lab'])? date( "d/m/Y", strtotime( $arrOrcamento[0]['dat_entrada_lab'] ) ) : null);
					$arrOrcamento[0]['dat_entrada_lab2'] = (!is_null($arrOrcamento[0]['dat_entrada_lab2'])? date( "d/m/Y", strtotime( $arrOrcamento[0]['dat_entrada_lab2'] ) ) : null);
					$arrOrcamento[0]['dat_entrada_lab3'] = (!is_null($arrOrcamento[0]['dat_entrada_lab3'])? date( "d/m/Y", strtotime( $arrOrcamento[0]['dat_entrada_lab3'] ) ) : null);
					$arrOrcamento[0]['data_criacao'] = (!is_null($arrOrcamento[0]['data_criacao'])? date( "d/m/Y", strtotime( $arrOrcamento[0]['data_criacao'] ) ) : null);
					$arrOrcamento[0]['data_alteracao'] = (!is_null($arrOrcamento[0]['data_alteracao'])? date( "d/m/Y", strtotime( $arrOrcamento[0]['data_alteracao'] ) ) : null);
					$arrOrcamento[0]['data_recebimento'] = (!is_null($arrOrcamento[0]['data_recebimento'])? date( "d/m/Y", strtotime( $arrOrcamento[0]['data_recebimento'] ) ) : null);
					$arrOrcamento[0]['data_os'] = (!is_null($arrOrcamento[0]['data_os'])? date( "d/m/Y", strtotime( $arrOrcamento[0]['data_os'] ) ) : null);
					$arrOrcamento[0]['dat_recebimento'] = (!is_null($arrOrcamento[0]['dat_recebimento'])? date( "d/m/Y", strtotime( $arrOrcamento[0]['dat_recebimento'] ) ) : null);
					$arrOrcamento[0]['dat_prazo_os'] = (!is_null($arrOrcamento[0]['dat_prazo_os'])? date( "d/m/Y", strtotime( $arrOrcamento[0]['dat_prazo_os'] ) ) : null);
					$arrOrcamento[0]['dat_proxima_calibragem'] = (!is_null($arrOrcamento[0]['dat_proxima_calibragem'])? date( "d/m/Y", strtotime( $arrOrcamento[0]['dat_proxima_calibragem'] ) ) : null);
					
		    //echo $arrOrcamento[0][idrecebimento];        
            $this->smarty->assign('arrOrcamento', $arrOrcamento);
            
            
						$this->smarty->assign('arrComponentes', $arrComponentes);
						$this->smarty->assign('listaLabs', $listaLabs);
						$this->smarty->assign('arrServicoOrcamento', $arrServicoOrcamento);
            
         }
        else
        {
            $this->smarty->assign('select_cidade', null);
            $this->smarty->assign('lat_default', $_SESSION['projeto']['latitude']);
            $this->smarty->assign('lng_default', $_SESSION['projeto']['longitude']);
            $this->smarty->assign('zoom', 3);
            
        }
				$this->smarty->assign('listaComercial', $listaComercial);
				$this->smarty->assign('listaDevolvido', $listaDevolvido);
				$this->smarty->assign('listaEmpresas', $listaEmpresas);
				$this->smarty->assign('listaStatusOs', $listaStatusOs);
        $this->smarty->assign('listaCondDevolvido', $listaCondDevolvido);    
        $this->smarty->assign('listaTecnicos', $listaTecnicos);    
        $this->smarty->assign('listaServicos', $arrServicos);    
        
        $this->template->setTitle('Qualibr&aacute;s - Cadastro de Orcamento');
        $this->template->run();

        if($vis)
        {
            $this->smarty->display('orcamento/visualizar.html');
        }
        else
        {
            $this->smarty->display('orcamento/cadastro.html');
        }
    }
		public function recuperar_servicos(){
			$dataOrcamento = new orcamentoModel();
			$arrServicos = $dataOrcamento->getServicosAll();
			foreach($arrServicos AS $key => $value){ 
				$option .= "<option value = ".$value['cod_servico'].">".$value['nom_servico']."</option>";
			}	
			echo $option;
			
		}
		public function excluirComponente(){
			$codComponente = $_POST['idComponente'];
			$dataOrcamento = new orcamentoModel();
			$dataOrcamento->delComponente($codComponente); 
			echo '1';
		}
		
		public function excluirServico(){
			$codServico = $_POST['idServico'];
			$dataOrcamento = new orcamentoModel();
			$dataOrcamento->delServico($codServico); 
			echo '1';
		}
		
    public function gravar_dados(){
			$dadosOrcamento['idorcamento'] = @$_POST['idorcamento'];
			$dadosRecebimento['orcamento'] = @$_POST['orcamento'];
			$dadosRecebimento['cod_fabricante'] = @$_POST['cod_fabricante'];
			$dadosRecebimento['cod_sap'] = @$_POST['cod_sap'];
			$dadosRecebimento['descricao'] = @$_POST['descricao'];
			$dadosRecebimento['modelo'] = @$_POST['modelo'];
			$dadosRecebimento['patrimonio'] = @$_POST['patrimonio'];
			$dadosRecebimento['cliente_prop'] = @$_POST['cliente_prop'];
			$dadosRecebimento['fabricante'] = @$_POST['fabricante'];
			$dadosRecebimento['n_serie'] = @$_POST['n_serie'];
			$dadosRecebimento['data_os'] = @$_POST['data_os'];
			$dadosOrcamento['cod_comercial'] = @$_POST['lista_comercial'];
			$dadosOrcamento['status_os'] = @$_POST['lista_status_os'];
			$dadosOrcamento['cod_condicao_devolucao'] = @$_POST['lista_dev'];
			$dadosOrcamento['cond_dev'] = @$_POST['lista_cond_dev'];
			$dadosOrcamento['cod_empresa_envio'] = @$_POST['lista_empresa'];
			$dadosOrcamento['cod_tecnico_executa'] = @$_POST['lista_tec'];
			$dadosOrcamento['cod_lab'] = @$_POST['cod_lab'];
			$dadosOrcamento['cod_lab2'] = @$_POST['cod_lab2'];
			$dadosOrcamento['cod_lab3'] = @$_POST['cod_lab3'];
			$dadosOrcamento['obs'] = addslashes(@$_POST['obs']);
			$dadosOrcamento['dat_aprovacao_orcamento'] = ((!is_null(@$_POST['dat_aprovacao_orcamento']) OR !empty(@$_POST['dat_aprovacao_orcamento'])) ? date( "Y-m-d", strtotime( @$_POST['dat_aprovacao_orcamento'] ) ) : null );
			$dadosOrcamento['dat_enc_rnc'] = ((!is_null(@$_POST['dat_enc_rnc']) OR !empty(@$_POST['dat_enc_rnc'])) ? date( "Y-m-d", strtotime( @$_POST['dat_enc_rnc'] ) ) : null );
			$dadosOrcamento['dat_liberacao_expedicao'] = ((!is_null(@$_POST['dat_liberacao_expedicao']) OR !empty(@$_POST['dat_liberacao_expedicao'])) ? date( "Y-m-d", strtotime( @$_POST['dat_liberacao_expedicao'] ) ) : null );
			$dadosOrcamento['dat_liberacao_documento'] = ((!is_null(@$_POST['dat_liberacao_documento']) OR !empty(@$_POST['dat_liberacao_documento'])) ? date( "Y-m-d", strtotime( @$_POST['dat_liberacao_documento'] ) ) : null );
			$dadosOrcamento['dat_entrada_lab'] = ((!is_null(@$_POST['dat_entrada_lab']) OR !empty(@$_POST['dat_entrada_lab'])) ? date( "Y-m-d", strtotime( @$_POST['dat_entrada_lab'] ) ) : null );
			$dadosOrcamento['dat_entrada_lab2'] = ((!is_null(@$_POST['dat_entrada_lab2']) OR !empty(@$_POST['dat_entrada_lab2'])) ? date( "Y-m-d", strtotime( @$_POST['dat_entrada_lab2'] ) ) : null );
			$dadosOrcamento['dat_entrada_lab3'] = ((!is_null(@$_POST['dat_entrada_lab3']) OR !empty(@$_POST['dat_entrada_lab3'])) ? date( "Y-m-d", strtotime( @$_POST['dat_entrada_lab3'] ) ) : null );
			$dadosOrcamento['dat_os'] = ((!is_null(@$_POST['dat_os']) OR !empty(@$_POST['dat_os'])) ? date( "Y-m-d", strtotime( @$_POST['dat_os'] ) ) : null );
			$dadosOrcamento['dat_recebimento'] = ((!is_null(@$_POST['dat_recebimento']) OR !empty(@$_POST['dat_recebimento'])) ? date( "Y-m-d", strtotime( @$_POST['dat_recebimento'] ) ) : null );
			$dadosOrcamento['dat_prazo_os'] = ((!is_null(@$_POST['dat_prazo_os']) OR !empty(@$_POST['dat_prazo_os'])) ? date( "Y-m-d", strtotime( @$_POST['dat_prazo_os'] ) ) : null );
			$dadosOrcamento['dat_proxima_calibragem'] = ((!is_null(@$_POST['dat_proxima_calibragem']) OR !empty(@$_POST['dat_proxima_calibragem'])) ? date( "Y-m-d", strtotime( @$_POST['dat_proxima_calibragem'] ) ) : null );
			$dadosOrcamento['historico'] = addslashes(@$_POST['historico']);
			$dadosOrcamento['laudo_tecnico'] = addslashes(@$_POST['laudo_tecnico']);
			$dadosOrcamento['num_certificado'] = @$_POST['num_certificado'];
			$dadosOrcamento['num_cartao_defeito'] = @$_POST['num_cartao_defeito'];
			$dadosOrcamento['num_rnc'] = @$_POST['operacao'];
			$dadosOrcamento['num_nf_saida'] = @$_POST['num_nf_saida'];
			$dadosOrcamento['dsc_os'] = @$_POST['dsc_os'];
			$dadosOrcamento['num_os'] = @$_POST['num_os'];
			$dadosOrcamento['num_os_ext'] = @$_POST['num_os_ext'];
			$dadosOrcamento['valor_calibracao'] = str_replace(',', '.', @$_POST['valor_calibracao']);
			$dadosOrcamento['valor_orc_ext'] = str_replace(',', '.', @$_POST['valor_orc_ext']);
			$dadosOrcamento['valor_pecas'] = str_replace(',', '.', @$_POST['valor_pecas']);
			$dadosOrcamento['valor_reparo'] = str_replace(',', '.', @$_POST['valor_reparo']);
			$dadosOrcamento['valor_total'] = str_replace(',', '.', @$_POST['valor_total']);
			$dadosOrcamento['ind_faturado'] = ((@$_POST['chk_faturado'] == 'on')? 1 : 0);
			
			// DADOS DE REL STATUS 
			$dadosStatus['cod_orcamento'] = @$_POST['idorcamento'];
			$dadosStatus['cod_status'] = @$_POST['lista_comercial'];
			$dadosStatus['data'] = date('Y-m-d h:m:i');
			
			// DADOS DE REL STATUS 
			$dadosStatusOS['cod_orcamento'] = @$_POST['idorcamento'];
			$dadosStatusOS['cod_status'] = @$_POST['lista_status_os'];
			$dadosStatusOS['data'] = date('Y-m-d h:m:i');
			
			$OrcamentoModel = new orcamentoModel();
			
			//UPDATE
			if(isset($_POST['idorcamento']) && !empty($_POST['idorcamento'])){
					
					if(isset($_POST['newQtd'])){
						$dadosComponente = array();
						foreach($_POST['newQtd'] as $key => $value){
							$dadosComponente['cod_orcamento'] = $dadosOrcamento['idorcamento'];
							$dadosComponente['qtd_itens'] = $value;
							$dadosComponente['cod_item'] = $OrcamentoModel->getComponentesAll($_POST['newCod'][$key])[0]['idproduto'];
							$OrcamentoModel->insertComponente($dadosComponente);
						}
					}
					if(isset($_POST['cod_item'])){
						$dadosComponente = array();
						foreach($_POST['cod_item'] as $key => $value){
							$dadosComponente['cod_orcamento'] = $dadosOrcamento['idorcamento'];
							$dadosComponente['qtd_itens'] = $_POST['qtd_itens'][$key];
							$dadosComponente['cod_item'] = $OrcamentoModel->getComponentesAll($value)[0]['idproduto'];
							
							$componenteId = $_POST['compID'][$key];
							
							$OrcamentoModel->updateComponente($dadosComponente, $componenteId);
							
						}
					}  
					if(isset($_POST['newCodServ'])){
						
						foreach($_POST['newCodServ'] as $key => $value){
							$dadosServico['cod_orcamento'] = $dadosOrcamento['idorcamento'];
							$dadosServico['cod_servico'] = $value;
							
							$OrcamentoModel->insertServico($dadosServico);
						}
					}  
					
					$OrcamentoModel->upd($dadosOrcamento, $_POST['idorcamento']);
					
					$OrcamentoModel->insertStatus($dadosStatus);
					$OrcamentoModel->insertStatusOS($dadosStatusOS);
					
					$arrReturn['msg'] = 'Dados gravados com sucesso!';
					$arrReturn['time'] = 2200;
					$arrReturn['status'] = 'success';
					$arrReturn['redirect'] = '/orcamento/listar';

					
			}
			//INSERT
			else{
					$_POST['idorcamento'] = $OrcamentoModel->create($dadosOrcamento);
					$arrReturn['msg'] = 'Dados gravados com sucesso!';
					$this->listar();
			}
			
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
		
    public function buscaComponente(){
			$dataOrcamento = new orcamentoModel();
			$descricao = $_POST['descricao'];
			$arrReturn = $dataOrcamento->getComponentesAll($descricao);
			
			foreach($arrReturn AS $key => $value){
				$listaProdutos[] =  $value['descricao']; 
			}
			echo json_encode($listaProdutos);
    }

}