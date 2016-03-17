<?php

class orcamentoModel extends model {

    public function create($data){
        return $this->insert('orcamento', $data, true);
    }
    public function insertComponente($data) {
        return $this->insert('cad_componente_orcamento', $data, true);
    }
    public function insertServico($data) {
        return $this->insert('servico_orcamento', $data, true);
    }
    public function insertStatus($data){
        return $this->insert('rel_status_orcamento', $data, true);
    }
		public function insertStatusOS($data){
        return $this->insert('rel_status_os_orcamento', $data, true);
    }

    public function upd($data, $orcId){
        return $this->update('orcamento', $data, 'idorcamento=' . $orcId, true);
    }

    public function updateComponente($data, $compOrcId){
        return $this->update('cad_componente_orcamento', $data, 'cod_componente_orcamento=' . $compOrcId, true);
    }
		public function getStatusOrcamento($cod_comercial = null){
			$fields = array('cod_tipo_comercial, nom_tipo, sgl_tipo');
			$tables = 'cad_tipo_comercial';
			if(!is_null($cod_comercial)){
				$where = 'cod_tipo_comercial = '. $cod_comercial;
			} else {
				$where = null;
			}
			
			
			return $this->read($tables, $fields, $where, NULL, NULL, NULL, "");
		}
		public function getStatusOS($codStatus = null)
    {
        $fields = array('*');
        $tables = 'cad_status_os';
				if(!is_null($codStatus)){
					$where = ' cod_status_os = ' . $codStatus;
				} else{
					$where = '';
				}
				
        return $this->read($tables, $fields, $where, NULL, NULL, NULL, '');
    }
		
		public function getComponentes ($orcId){
			
			$fields = array('comp.cod_componente_orcamento, comp.qtd_itens, comp.cod_item, prod.descricao');
			$tables = 'cad_componente_orcamento comp, produto prod ';
			$where  = 'comp.cod_item = prod.idproduto AND comp.cod_orcamento = ' . $orcId; 
			return $this->read($tables, $fields, $where, null, null, null);
		}
		public function getComponentesAll($descricao){
			$fields = array('prod.idproduto, prod.descricao');
			$tables = 'produto prod ';
			$where  = 'prod.descricao LIKE "%' . $descricao . '%" '; 
			return $this->read($tables, $fields, $where, null, null, null, "");
		}
		
		public function getServicosAll($orcId = null){
			
			if(!is_null($orcId)){
				$fields = array('serv_orc.cod_servico_orcamento, serv.cod_servico, serv.nom_servico ');
				$tables = 'servico_orcamento AS serv_orc, cad_servico AS serv , orcamento AS orc';
				$where  .= 'serv_orc.cod_servico = serv.cod_servico AND orc.idorcamento = serv_orc.cod_orcamento '; 
				$where  .= 'AND orc.idorcamento = ' . $orcId; 
			} else {
				$fields = array('cod_servico, nom_servico');
				$tables = 'cad_servico ';
			}
			
			return $this->read($tables, $fields, $where, null, null, null, "");
		}
		
		public function getCondicoes($tipo, $cod_condicao = null){
			/*
			   1 - Devolucao
				 2 - REcebimento
			*/
			$fields = array('cod_condicao, nom_condicao, sgl_condicao');
			$tables = 'cad_condicoes';
			$where  .= ' tipo = ' . $tipo;
			if(!is_null($cod_condicao)){
				$where .= " AND  cod_condicao = " . $cod_condicao;
			}
			
			return $this->read($tables, $fields, $where, NULL, NULL, Null, "");
		}
		
		public function getEmpresa($cod_empresa = null){
			
			$fields = array('cod_empresa, nom_razao_social, nom_nome_fantasia');
			$tables = 'cad_empresa';
			if(!is_null($cod_empresa)){
				$where  = ' cod_empresa = ' . $cod_empresa;
			}
			
			
			return $this->read($tables, $fields, $where, NULL, NULL, Null, "");
		}
		public function getTecnico($cod_tecnico = null){
			
			$fields = array('cod_funcionario, nom_funcionario');
			$tables = 'pessoas';
			if(!is_null($cod_tecnico)){
				$where  = ' cod_funcionario = ' . $cod_tecnico;
			}
			
			
			return $this->read($tables, $fields, $where, NULL, NULL, Null, "");
		}
		public function getLab($cod_lab = null){
			$fields = array('cod_lab, nom_laboratorio , descricao');
			$tables = 'cad_laboratorio';
			if(!is_null($cod_lab)){
				$where  = ' cod_lab = ' . $cod_lab;
			}
			return $this->read($tables, $fields, $where, NULL, NULL, Null, "");
		}
		

    public function getOrcamento($orc_id = null, $filtro = null)
    { 
				
        $arrWhere = array();
        if(!is_null($orc_id))
            $arrWhere[] = " and orc.idorcamento = $orc_id";
				
        //Verifica se tem filtro 
				if(is_array($filtro) && (isset($filtro['pesquisar'])) || isset($filtro['exportar'])){
					if(!empty($filtro) OR is_null($filtro)){
						foreach($filtro AS $key => $value ){
							if($key != "pesquisar" && $value != '' ){
								if(is_null($orc_id)){
									$arrWhere[] = " AND  $key LIKE  '%$value%' ";
								} else {
									$arrWhere[] = "  $key LIKE  '%$value%' ";
								}
							}
						}
					}
        }
				$fields = array('orc.*, rec.*');
        $tables = 'orcamento orc, recebimento rec';
        $where = 'orc.idorcamento = rec.idrecebimento';
        $where .= implode(" and ", $arrWhere);
				
				return $this->read($tables, $fields, $where, NULL, NULL, NULL, "", null, 1);
    }

    

    public function getMunicipios()
    {
        $fields = array('DISTINCT municipio AS "nome"');
        $tables = 'cad_cliente';
        $where = 'municipio IS NOT NULL AND municipio <> ""';
        return $this->read($tables, $fields, $where, NULL, NULL, NULL, 'municipio');
    }

    public function getBairros()
    {
        $fields = array('DISTINCT bairro AS "nome"');
        $tables = 'cad_cliente';
        $where = 'bairro IS NOT NULL AND bairro <> ""';
        return $this->read($tables, $fields, $where, NULL, NULL, NULL, 'bairro');
    }

    public function getStatus()
    {
        $fields = array('DISTINCT status_cliente AS "nome"');
        $tables = 'cad_cliente';
        $where = 'status_cliente IS NOT NULL AND status_cliente <> ""';
        return $this->read($tables, $fields, $where, NULL, NULL, NULL, 'status_cliente');
    }
    

    public function del($con_id) {
        $where = "con_id = $con_id";
        return $this->delete('cad_cliente', $where, true);
    }
    public function delComponente($comp_id) {
        $where = "cod_componente_orcamento = $comp_id";
        return $this->delete('cad_componente_orcamento', $where, true);
    }
    public function delServico($service_id) {
        $where = "cod_servico_orcamento = $service_id";
        return $this->delete('servico_orcamento', $where, true);
    }

}
