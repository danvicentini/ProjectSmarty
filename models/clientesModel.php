<?php

class clientesModel extends model
{

    public function create($data)
    {
        return $this->insert('cliente', $data, true);
    }

    public function upd($data, $cliId)
    {
        return $this->update('cliente', $data, 'idcliente=' . $conId, true);
    }

    public function getCliente($cli_id = null, $filtro = null)
    {
        $arrWhere = array();
        if(!is_null($cli_id))
            $arrWhere[] = "cli.idcliente = $cli_id";

        //Verifica se tem filtro
        if(is_array($filtro) && (isset($filtro['pesquisar'])) || isset($filtro['exportar']))
        {
            if($filtro['descricao'] !== 'Descricao') $arrWhere[] = 'cli.nome_fantasia = "' . $filtro['descricao'] . '"';
            
        }

        $fields = array('cli.*');
        $tables = 'cliente cli';
        $where = implode(" and ", $arrWhere);
        return $this->read($tables, $fields, $where, NULL, NULL, NULL, "");
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

    public function del($con_id)
    {
        $where = "con_id = $con_id";
        return $this->delete('cad_cliente', $where, true);
    }

}
