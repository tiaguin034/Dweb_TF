<?php
require_once '../config/database.php';

class Animal {
    private $conn;
    private $table_name = "animais";

    public $id;
    public $nome;
    public $especie;
    public $raca;
    public $idade;
    public $sexo;
    public $tamanho;
    public $cor;
    public $descricao;
    public $foto_url;
    public $cidade;
    public $estado;
    public $usuario_id;
    public $data_publicacao;
    public $adotado;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Criar animal
    public function criar() {
        $query = "INSERT INTO " . $this->table_name . " 
                  (nome, especie, raca, idade, sexo, tamanho, cor, descricao, foto_url, cidade, estado, usuario_id) 
                  VALUES (:nome, :especie, :raca, :idade, :sexo, :tamanho, :cor, :descricao, :foto_url, :cidade, :estado, :usuario_id)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':nome', $this->nome);
        $stmt->bindParam(':especie', $this->especie);
        $stmt->bindParam(':raca', $this->raca);
        $stmt->bindParam(':idade', $this->idade);
        $stmt->bindParam(':sexo', $this->sexo);
        $stmt->bindParam(':tamanho', $this->tamanho);
        $stmt->bindParam(':cor', $this->cor);
        $stmt->bindParam(':descricao', $this->descricao);
        $stmt->bindParam(':foto_url', $this->foto_url);
        $stmt->bindParam(':cidade', $this->cidade);
        $stmt->bindParam(':estado', $this->estado);
        $stmt->bindParam(':usuario_id', $this->usuario_id);

        if($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    // Listar animais para adoção
    public function listarParaAdocao($filtros = []) {
        $query = "SELECT a.*, u.nome as usuario_nome, u.telefone as usuario_telefone 
                  FROM " . $this->table_name . " a 
                  INNER JOIN usuarios u ON a.usuario_id = u.id 
                  WHERE a.adotado = 0 AND u.ativo = 1";

        $params = [];

        // Filtros
        if(!empty($filtros['especie'])) {
            $query .= " AND a.especie = :especie";
            $params[':especie'] = $filtros['especie'];
        }

        if(!empty($filtros['cidade'])) {
            $query .= " AND a.cidade LIKE :cidade";
            $params[':cidade'] = '%' . $filtros['cidade'] . '%';
        }

        if(!empty($filtros['busca'])) {
            $query .= " AND (a.nome LIKE :busca OR a.descricao LIKE :busca OR a.cidade LIKE :busca)";
            $params[':busca'] = '%' . $filtros['busca'] . '%';
        }

        $query .= " ORDER BY a.data_publicacao DESC";

        $stmt = $this->conn->prepare($query);
        foreach($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Buscar animal por ID
    public function buscarPorId($id) {
        $query = "SELECT a.*, u.nome as usuario_nome, u.telefone as usuario_telefone, u.email as usuario_email 
                  FROM " . $this->table_name . " a 
                  INNER JOIN usuarios u ON a.usuario_id = u.id 
                  WHERE a.id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        if($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->id = $row['id'];
            $this->nome = $row['nome'];
            $this->especie = $row['especie'];
            $this->raca = $row['raca'];
            $this->idade = $row['idade'];
            $this->sexo = $row['sexo'];
            $this->tamanho = $row['tamanho'];
            $this->cor = $row['cor'];
            $this->descricao = $row['descricao'];
            $this->foto_url = $row['foto_url'];
            $this->cidade = $row['cidade'];
            $this->estado = $row['estado'];
            $this->usuario_id = $row['usuario_id'];
            $this->data_publicacao = $row['data_publicacao'];
            $this->adotado = $row['adotado'];
            return true;
        }
        return false;
    }

    // Atualizar animal
    public function atualizar() {
        $query = "UPDATE " . $this->table_name . " 
                  SET nome = :nome, especie = :especie, raca = :raca, idade = :idade, 
                      sexo = :sexo, tamanho = :tamanho, cor = :cor, descricao = :descricao, 
                      foto_url = :foto_url, cidade = :cidade, estado = :estado 
                  WHERE id = :id AND usuario_id = :usuario_id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':nome', $this->nome);
        $stmt->bindParam(':especie', $this->especie);
        $stmt->bindParam(':raca', $this->raca);
        $stmt->bindParam(':idade', $this->idade);
        $stmt->bindParam(':sexo', $this->sexo);
        $stmt->bindParam(':tamanho', $this->tamanho);
        $stmt->bindParam(':cor', $this->cor);
        $stmt->bindParam(':descricao', $this->descricao);
        $stmt->bindParam(':foto_url', $this->foto_url);
        $stmt->bindParam(':cidade', $this->cidade);
        $stmt->bindParam(':estado', $this->estado);
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':usuario_id', $this->usuario_id);

        return $stmt->execute();
    }

    // Excluir animal
    public function excluir($usuario_id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id AND usuario_id = :usuario_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':usuario_id', $usuario_id);
        return $stmt->execute();
    }

    // Marcar como adotado
    public function marcarAdotado() {
        $query = "UPDATE " . $this->table_name . " SET adotado = 1 WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        return $stmt->execute();
    }

    // Listar animais do usuário
    public function listarPorUsuario($usuario_id) {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE usuario_id = :usuario_id 
                  ORDER BY data_publicacao DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':usuario_id', $usuario_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Buscar animais (para funcionalidade de busca)
    public function buscar($termo) {
        $query = "SELECT a.*, u.nome as usuario_nome 
                  FROM " . $this->table_name . " a 
                  INNER JOIN usuarios u ON a.usuario_id = u.id 
                  WHERE a.adotado = 0 AND u.ativo = 1 
                  AND (a.nome LIKE :termo OR a.descricao LIKE :termo OR a.cidade LIKE :termo) 
                  ORDER BY a.data_publicacao DESC 
                  LIMIT 10";

        $stmt = $this->conn->prepare($query);
        $termo = '%' . $termo . '%';
        $stmt->bindParam(':termo', $termo);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
