<?php

class recebimentoModel extends model
{

    public function create($data)
    {
        return $this->insert('recebimento', $data, true);
    }
    
	public function create_orc($data)
    {
        return $this->insert('orcamento', $data, true);
    }

    public function upd($data, $prdId)
    {
        return $this->update('produto', $data, 'idproduto=' . $prdId, true);
    }

    public function getProduto($prd_id = null, $filtro = null)
    {
        $arrWhere = array();
        if(!is_null($prd_id))
            $arrWhere[] = "prd.idproduto = $prd_id";

        //Verifica se tem filtro
        if(is_array($filtro) && (isset($filtro['pesquisar'])) || isset($filtro['exportar']))
        {
            if($filtro['descricao'] !== 'Descricao') $arrWhere[] = 'prd.descricao = "' . $filtro['descricao'] . '"';
            
        }

        $fields = array('prd.*');
        $tables = 'produto prd';
        $where = implode(" and ", $arrWhere);
        return $this->read($tables, $fields, $where, NULL, NULL, NULL, "");
    }

    public function getConsumidorMapa()
    {
        $tipoEndereco['alameda'] = 'Alameda';
        $tipoEndereco['avenida'] = 'Avenida';
        $tipoEndereco['caminho'] = 'Caminho';
        $tipoEndereco['estrada'] = 'Estrada';
        $tipoEndereco['largo'] = 'Largo';
        $tipoEndereco['passarela'] = 'Passarela';
        $tipoEndereco['praca'] = 'PraÃ§a';
        $tipoEndereco['rua'] = 'Rua';
        $tipoEndereco['trecho'] = 'Trecho';
        $tipoEndereco['vale'] = 'Vale';
        $tipoEndereco['via'] = 'Via';

        $tipoBairro['aeroporto'] = 'Aeroporto';
        $tipoBairro['area'] = 'Ã�rea';
        $tipoBairro['campo'] = 'Campo';
        $tipoBairro['chacara'] = 'ChÃ¡cara';
        $tipoBairro['colonia'] = 'ColÃ´nia';
        $tipoBairro['condominio'] = 'CondomÃ­nio';
        $tipoBairro['conjunto'] = 'Conjunto';
        $tipoBairro['distrito'] = 'Distrito';
        $tipoBairro['esplanada'] = 'Esplanada';
        $tipoBairro['estacao'] = 'EstaÃ§Ã£o';
        $tipoBairro['favela'] = 'Favela';
        $tipoBairro['feira'] = 'Feira';
        $tipoBairro['jardim'] = 'Jardim';
        $tipoBairro['lagoa'] = 'Lagoa';
        $tipoBairro['loteamento'] = 'Loteamento';
        $tipoBairro['morro'] = 'Morro';
        $tipoBairro['nucleo'] = 'NÃºcleo';
        $tipoBairro['parque'] = 'Parque';
        $tipoBairro['patio'] = 'PÃ¡tio';
        $tipoBairro['praca'] = 'PraÃ§a';
        $tipoBairro['quadra'] = 'Quadra';
        $tipoBairro['residencial'] = 'Residencial';
        $tipoBairro['setor'] = 'Setor';
        $tipoBairro['sitio'] = 'SÃ­tio';
        $tipoBairro['vila'] = 'Vila';

        $clientes = $this->read('cad_cliente c', array('c.*'), 'status_cliente IN ("instalado", "aprovado")');

        $enderecos = array();
		$totalInstalado = 0;
		$totalAprovado = 0;
        foreach($clientes as $cliente)
        {
            $enderecos[] = array(
                'status_cliente' => $cliente['status_cliente'],
                'uc' => $cliente['uc'],
                'nome' => $cliente['nome'],
                'endereco' => $tipoEndereco[$cliente['tipo_endereco']] . ' ' . $cliente['rua'] . ', ' . $cliente['numero'] . ' - ' . $tipoBairro[$cliente['tipo_bairro']] . ' ' . $cliente['bairro'] . ' - ' . $cliente['municipio'] . ' - Brasil'
            );

			if($cliente['status_cliente'] == 'instalado')
				$totalInstalado++;
			elseif($cliente['status_cliente'] == 'aprovado')
				$totalAprovado++;
        }

        return array('enderecos' => $enderecos, 'totalInstalado' => $totalInstalado, 'totalAprovado' => $totalAprovado);
    }

    public function     getMunicipios()
    {
        $fields = array('DISTINCT municipio AS "nome"');
        $tables = 'cad_cliente';
        $where = 'municipio IS NOT NULL AND municipio <> ""';
        return $this->read($tables, $fields, $where, NULL, NULL, NULL, 'municipio');
    }

	public function getRecebimentos()
    {
        $tables = 'recebimento';
        return $this->read($tables, NULL, NULL, NULL, NULL, NULL, NULL);
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

    public function del($con_id)
    {
        $where = "con_id = $con_id";
        return $this->delete('cad_cliente', $where, true);
    }

}
