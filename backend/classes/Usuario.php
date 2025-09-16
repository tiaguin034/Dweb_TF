<?php
require_once '../config/database.php';

class Usuario {
    private $conn;
    private $table_name = "usuarios";

    public $id;
    public $nome;
    public $email;
    public $senha;
    public $perfil;
    public $telefone;
    public $endereco;
    public $cidade;
    public $estado;
    public $cep;
    public $data_cadastro;
    public $ativo;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Criar usuário
    public function criar() {
        $query = "INSERT INTO " . $this->table_name . " 
                  (nome, email, senha, perfil, telefone, endereco, cidade, estado, cep) 
                  VALUES (:nome, :email, :senha, :perfil, :telefone, :endereco, :cidade, :estado, :cep)";

        $stmt = $this->conn->prepare($query);

        // Hash da senha
        $this->senha = password_hash($this->senha, PASSWORD_DEFAULT);

        $stmt->bindParam(':nome', $this->nome);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':senha', $this->senha);
        $stmt->bindParam(':perfil', $this->perfil);
        $stmt->bindParam(':telefone', $this->telefone);
        $stmt->bindParam(':endereco', $this->endereco);
        $stmt->bindParam(':cidade', $this->cidade);
        $stmt->bindParam(':estado', $this->estado);
        $stmt->bindParam(':cep', $this->cep);

        if($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    // Verificar login
    public function login($email, $senha) {
        $query = "SELECT id, nome, email, senha, perfil, telefone, endereco, cidade, estado, cep, data_cadastro, ativo 
                  FROM " . $this->table_name . " 
                  WHERE email = :email AND ativo = 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if(password_verify($senha, $row['senha'])) {
                $this->id = $row['id'];
                $this->nome = $row['nome'];
                $this->email = $row['email'];
                $this->perfil = $row['perfil'];
                $this->telefone = $row['telefone'];
                $this->endereco = $row['endereco'];
                $this->cidade = $row['cidade'];
                $this->estado = $row['estado'];
                $this->cep = $row['cep'];
                $this->data_cadastro = $row['data_cadastro'];
                $this->ativo = $row['ativo'];
                return true;
            }
        }
        return false;
    }

    // Buscar usuário por ID
    public function buscarPorId($id) {
        $query = "SELECT id, nome, email, perfil, telefone, endereco, cidade, estado, cep, data_cadastro, ativo 
                  FROM " . $this->table_name . " 
                  WHERE id = :id AND ativo = 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        if($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->id = $row['id'];
            $this->nome = $row['nome'];
            $this->email = $row['email'];
            $this->perfil = $row['perfil'];
            $this->telefone = $row['telefone'];
            $this->endereco = $row['endereco'];
            $this->cidade = $row['cidade'];
            $this->estado = $row['estado'];
            $this->cep = $row['cep'];
            $this->data_cadastro = $row['data_cadastro'];
            $this->ativo = $row['ativo'];
            return true;
        }
        return false;
    }

    // Atualizar usuário
    public function atualizar() {
        $query = "UPDATE " . $this->table_name . " 
                  SET nome = :nome, telefone = :telefone, endereco = :endereco, 
                      cidade = :cidade, estado = :estado, cep = :cep 
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':nome', $this->nome);
        $stmt->bindParam(':telefone', $this->telefone);
        $stmt->bindParam(':endereco', $this->endereco);
        $stmt->bindParam(':cidade', $this->cidade);
        $stmt->bindParam(':estado', $this->estado);
        $stmt->bindParam(':cep', $this->cep);
        $stmt->bindParam(':id', $this->id);

        return $stmt->execute();
    }

    // Alterar senha
    public function alterarSenha($nova_senha) {
        $query = "UPDATE " . $this->table_name . " SET senha = :senha WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        
        $senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);
        $stmt->bindParam(':senha', $senha_hash);
        $stmt->bindParam(':id', $this->id);

        return $stmt->execute();
    }

    // Excluir usuário (soft delete)
    public function excluir() {
        $query = "UPDATE " . $this->table_name . " SET ativo = 0 WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        return $stmt->execute();
    }

    // Listar todos os usuários (admin)
    public function listarTodos() {
        $query = "SELECT id, nome, email, perfil, telefone, cidade, estado, data_cadastro, ativo 
                  FROM " . $this->table_name . " 
                  ORDER BY data_cadastro DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Verificar se email já existe
    public function emailExiste($email, $id_excluir = null) {
        $query = "SELECT id FROM " . $this->table_name . " WHERE email = :email";
        if($id_excluir) {
            $query .= " AND id != :id";
        }

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        if($id_excluir) {
            $stmt->bindParam(':id', $id_excluir);
        }
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }
}
?>
