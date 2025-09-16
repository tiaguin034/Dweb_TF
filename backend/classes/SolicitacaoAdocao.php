<?php
require_once '../config/database.php';

class SolicitacaoAdocao {
    private $conn;
    private $table_name = "solicitacoes_adocao";

    public $id;
    public $animal_id;
    public $solicitante_nome;
    public $solicitante_email;
    public $solicitante_telefone;
    public $mensagem;
    public $status;
    public $data_solicitacao;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Criar solicitação de adoção
    public function criar() {
        $query = "INSERT INTO " . $this->table_name . " 
                  (animal_id, solicitante_nome, solicitante_email, solicitante_telefone, mensagem) 
                  VALUES (:animal_id, :solicitante_nome, :solicitante_email, :solicitante_telefone, :mensagem)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':animal_id', $this->animal_id);
        $stmt->bindParam(':solicitante_nome', $this->solicitante_nome);
        $stmt->bindParam(':solicitante_email', $this->solicitante_email);
        $stmt->bindParam(':solicitante_telefone', $this->solicitante_telefone);
        $stmt->bindParam(':mensagem', $this->mensagem);

        if($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    // Listar solicitações por animal
    public function listarPorAnimal($animal_id) {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE animal_id = :animal_id 
                  ORDER BY data_solicitacao DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':animal_id', $animal_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Listar todas as solicitações (admin)
    public function listarTodas() {
        $query = "SELECT s.*, a.nome as animal_nome, a.especie, u.nome as responsavel_nome 
                  FROM " . $this->table_name . " s 
                  INNER JOIN animais a ON s.animal_id = a.id 
                  INNER JOIN usuarios u ON a.usuario_id = u.id 
                  ORDER BY s.data_solicitacao DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Atualizar status da solicitação
    public function atualizarStatus($status) {
        $query = "UPDATE " . $this->table_name . " SET status = :status WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $this->id);
        return $stmt->execute();
    }

    // Buscar solicitação por ID
    public function buscarPorId($id) {
        $query = "SELECT s.*, a.nome as animal_nome, a.especie, u.nome as responsavel_nome 
                  FROM " . $this->table_name . " s 
                  INNER JOIN animais a ON s.animal_id = a.id 
                  INNER JOIN usuarios u ON a.usuario_id = u.id 
                  WHERE s.id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        if($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->id = $row['id'];
            $this->animal_id = $row['animal_id'];
            $this->solicitante_nome = $row['solicitante_nome'];
            $this->solicitante_email = $row['solicitante_email'];
            $this->solicitante_telefone = $row['solicitante_telefone'];
            $this->mensagem = $row['mensagem'];
            $this->status = $row['status'];
            $this->data_solicitacao = $row['data_solicitacao'];
            return true;
        }
        return false;
    }

    // Excluir solicitação
    public function excluir() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        return $stmt->execute();
    }

    // Contar solicitações pendentes
    public function contarPendentes($usuario_id = null) {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " s 
                  INNER JOIN animais a ON s.animal_id = a.id 
                  WHERE s.status = 'pendente'";
        
        if($usuario_id) {
            $query .= " AND a.usuario_id = :usuario_id";
        }

        $stmt = $this->conn->prepare($query);
        if($usuario_id) {
            $stmt->bindParam(':usuario_id', $usuario_id);
        }
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }
}
?>
