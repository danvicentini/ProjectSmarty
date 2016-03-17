<?php

class pessoasModel extends model
{

    public function create($data)
    {
        return $this->insert('pessoas', $data, true);
    }

    public function upd($data, $pesId)
    {
        return $this->update('pessoas', $data, 'cod_funcionario=' . $pesId, true);
    }
		
    public function getClienteProprietario()
    {
        $fields = array('*');
        $tables = 'cad_empresa';
        $where = null;
        return $this->read($tables, $fields, $where, NULL, NULL, NULL, null);
    }
    public function getPessoas($pessoa_id = null, $filtro = null)
    {
			
			$arrWhere = array();
			$arrWhere[] = " pes.cod_empresa = emp.cod_empresa";
			if(!is_null($pessoa_id))
					$arrWhere[] = "pes.cod_funcionario = $pessoa_id";
			//Verifica se tem filtro
			if(is_array($filtro) && (isset($filtro['pesquisar'])) || isset($filtro['exportar']))
			{
				foreach($filtro AS $key => $value){
					if(!empty($value) AND $value != 'Pesquisar'){
						if($key == 'cod_funcionario'){
							$arrWhere[] = " $key = '$value' ";
						} else {
							$arrWhere[] = " $key LIKE '%$value%' ";
						}
						
					}
				} 
					
			}
			$fields = array('pes.*, emp.*');
			$tables = 'pessoas pes, cad_empresa emp';
			$where .= implode(" and ", $arrWhere);
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
        $tipoEndereco['praca'] = 'Pra&ccedil;a';
        $tipoEndereco['rua'] = 'Rua';
        $tipoEndereco['trecho'] = 'Trecho';
        $tipoEndereco['vale'] = 'Vale';
        $tipoEndereco['via'] = 'Via';

        $tipoBairro['aeroporto'] = 'Aeroporto';
        $tipoBairro['area'] = '&Aacute;rea';
        $tipoBairro['campo'] = 'Campo';
        $tipoBairro['chacara'] = 'Ch&aacute;cara';
        $tipoBairro['colonia'] = 'Col&ocirc;nia';
        $tipoBairro['condominio'] = 'Condom&iacute;nio';
        $tipoBairro['conjunto'] = 'Conjunto';
        $tipoBairro['distrito'] = 'Distrito';
        $tipoBairro['esplanada'] = 'Esplanada';
        $tipoBairro['estacao'] = 'Esta&ccedil;&aacute;o';
        $tipoBairro['favela'] = 'Favela';
        $tipoBairro['feira'] = 'Feira';
        $tipoBairro['jardim'] = 'Jardim';
        $tipoBairro['lagoa'] = 'Lagoa';
        $tipoBairro['loteamento'] = 'Loteamento';
        $tipoBairro['morro'] = 'Morro';
        $tipoBairro['nucleo'] = 'N&uacute;cleo';
        $tipoBairro['parque'] = 'Parque';
        $tipoBairro['patio'] = 'P&aacute;tio';
        $tipoBairro['praca'] = 'Pra&ccedil;a';
        $tipoBairro['quadra'] = 'Quadra';
        $tipoBairro['residencial'] = 'Residencial';
        $tipoBairro['setor'] = 'Setor';
        $tipoBairro['sitio'] = 'S&iacute;tio';
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

    public function del($pes_id)
    {
        $where = "cod_funcionario = $pes_id";
        return $this->delete('pessoas', $where, true);
    }

}
