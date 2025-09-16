<?php
require_once '../config/database.php';

class Campanha {
    private $conn;
    private $table_name = "campanhas";

    public $id;
    public $nome;
    public $descricao;
    public $tipo;
    public $data_evento;
    public $hora_inicio;
    public $hora_fim;
    public $cidade;
    public $estado;
    public $endereco;
    public $usuario_id;
    public $vagas_disponiveis;
    public $vagas_preenchidas;
    public $ativa;
    public $data_criacao;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Criar campanha
    public function criar() {
        $query = "INSERT INTO " . $this->table_name . " 
                  (nome, descricao, tipo, data_evento, hora_inicio, hora_fim, cidade, estado, endereco, usuario_id, vagas_disponiveis) 
                  VALUES (:nome, :descricao, :tipo, :data_evento, :hora_inicio, :hora_fim, :cidade, :estado, :endereco, :usuario_id, :vagas_disponiveis)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':nome', $this->nome);
        $stmt->bindParam(':descricao', $this->descricao);
        $stmt->bindParam(':tipo', $this->tipo);
        $stmt->bindParam(':data_evento', $this->data_evento);
        $stmt->bindParam(':hora_inicio', $this->hora_inicio);
        $stmt->bindParam(':hora_fim', $this->hora_fim);
        $stmt->bindParam(':cidade', $this->cidade);
        $stmt->bindParam(':estado', $this->estado);
        $stmt->bindParam(':endereco', $this->endereco);
        $stmt->bindParam(':usuario_id', $this->usuario_id);
        $stmt->bindParam(':vagas_disponiveis', $this->vagas_disponiveis);

        if($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    // Listar campanhas ativas
    public function listarAtivas($filtros = []) {
        $query = "SELECT c.*, u.nome as organizador_nome, u.telefone as organizador_telefone 
                  FROM " . $this->table_name . " c 
                  INNER JOIN usuarios u ON c.usuario_id = u.id 
                  WHERE c.ativa = 1 AND u.ativo = 1 AND c.data_evento >= CURDATE()";

        $params = [];

        // Filtros
        if(!empty($filtros['cidade'])) {
            $query .= " AND c.cidade LIKE :cidade";
            $params[':cidade'] = '%' . $filtros['cidade'] . '%';
        }

        if(!empty($filtros['data'])) {
            $query .= " AND c.data_evento = :data";
            $params[':data'] = $filtros['data'];
        }

        if(!empty($filtros['tipo'])) {
            $query .= " AND c.tipo = :tipo";
            $params[':tipo'] = $filtros['tipo'];
        }

        if(!empty($filtros['busca'])) {
            $query .= " AND (c.nome LIKE :busca OR c.descricao LIKE :busca OR c.cidade LIKE :busca)";
            $params[':busca'] = '%' . $filtros['busca'] . '%';
        }

        $query .= " ORDER BY c.data_evento ASC";

        $stmt = $this->conn->prepare($query);
        foreach($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Buscar campanha por ID
    public function buscarPorId($id) {
        $query = "SELECT c.*, u.nome as organizador_nome, u.telefone as organizador_telefone, u.email as organizador_email 
                  FROM " . $this->table_name . " c 
                  INNER JOIN usuarios u ON c.usuario_id = u.id 
                  WHERE c.id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        if($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->id = $row['id'];
            $this->nome = $row['nome'];
            $this->descricao = $row['descricao'];
            $this->tipo = $row['tipo'];
            $this->data_evento = $row['data_evento'];
            $this->hora_inicio = $row['hora_inicio'];
            $this->hora_fim = $row['hora_fim'];
            $this->cidade = $row['cidade'];
            $this->estado = $row['estado'];
            $this->endereco = $row['endereco'];
            $this->usuario_id = $row['usuario_id'];
            $this->vagas_disponiveis = $row['vagas_disponiveis'];
            $this->vagas_preenchidas = $row['vagas_preenchidas'];
            $this->ativa = $row['ativa'];
            $this->data_criacao = $row['data_criacao'];
            return true;
        }
        return false;
    }

    // Atualizar campanha
    public function atualizar() {
        $query = "UPDATE " . $this->table_name . " 
                  SET nome = :nome, descricao = :descricao, tipo = :tipo, data_evento = :data_evento, 
                      hora_inicio = :hora_inicio, hora_fim = :hora_fim, cidade = :cidade, estado = :estado, 
                      endereco = :endereco, vagas_disponiveis = :vagas_disponiveis 
                  WHERE id = :id AND usuario_id = :usuario_id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':nome', $this->nome);
        $stmt->bindParam(':descricao', $this->descricao);
        $stmt->bindParam(':tipo', $this->tipo);
        $stmt->bindParam(':data_evento', $this->data_evento);
        $stmt->bindParam(':hora_inicio', $this->hora_inicio);
        $stmt->bindParam(':hora_fim', $this->hora_fim);
        $stmt->bindParam(':cidade', $this->cidade);
        $stmt->bindParam(':estado', $this->estado);
        $stmt->bindParam(':endereco', $this->endereco);
        $stmt->bindParam(':vagas_disponiveis', $this->vagas_disponiveis);
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':usuario_id', $this->usuario_id);

        return $stmt->execute();
    }

    // Excluir campanha
    public function excluir($usuario_id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id AND usuario_id = :usuario_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':usuario_id', $usuario_id);
        return $stmt->execute();
    }

    // Desativar campanha
    public function desativar() {
        $query = "UPDATE " . $this->table_name . " SET ativa = 0 WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        return $stmt->execute();
    }

    // Listar campanhas do usuário
    public function listarPorUsuario($usuario_id) {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE usuario_id = :usuario_id 
                  ORDER BY data_criacao DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':usuario_id', $usuario_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Verificar se há vagas disponíveis
    public function temVagasDisponiveis() {
        return ($this->vagas_disponiveis - $this->vagas_preenchidas) > 0;
    }

    // Incrementar vagas preenchidas
    public function incrementarVagasPreenchidas() {
        $query = "UPDATE " . $this->table_name . " 
                  SET vagas_preenchidas = vagas_preenchidas + 1 
                  WHERE id = :id AND vagas_preenchidas < vagas_disponiveis";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        return $stmt->execute();
    }

    // Buscar campanhas (para funcionalidade de busca)
    public function buscar($termo) {
        $query = "SELECT c.*, u.nome as organizador_nome 
                  FROM " . $this->table_name . " c 
                  INNER JOIN usuarios u ON c.usuario_id = u.id 
                  WHERE c.ativa = 1 AND u.ativo = 1 AND c.data_evento >= CURDATE() 
                  AND (c.nome LIKE :termo OR c.descricao LIKE :termo OR c.cidade LIKE :termo) 
                  ORDER BY c.data_evento ASC 
                  LIMIT 10";

        $stmt = $this->conn->prepare($query);
        $termo = '%' . $termo . '%';
        $stmt->bindParam(':termo', $termo);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
