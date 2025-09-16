<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/database.php';
require_once '../classes/Usuario.php';

$database = new Database();
$db = $database->getConnection();
$usuario = new Usuario($db);

$method = $_SERVER['REQUEST_METHOD'];

// Verificar autenticação
function verificarAuth() {
    session_start();
    return isset($_SESSION['user_id']);
}

// Verificar se é admin
function verificarAdmin() {
    session_start();
    return isset($_SESSION['user_id']) && isset($_SESSION['user_perfil']) && $_SESSION['user_perfil'] === 'org';
}

if($method == 'GET') {
    if(!verificarAuth()) {
        echo json_encode([
            'success' => false,
            'message' => 'Login necessário'
        ]);
        exit;
    }
    
    if(isset($_GET['id'])) {
        // Buscar usuário específico
        if($usuario->buscarPorId($_GET['id'])) {
            echo json_encode([
                'success' => true,
                'usuario' => [
                    'id' => $usuario->id,
                    'nome' => $usuario->nome,
                    'email' => $usuario->email,
                    'perfil' => $usuario->perfil,
                    'telefone' => $usuario->telefone,
                    'endereco' => $usuario->endereco,
                    'cidade' => $usuario->cidade,
                    'estado' => $usuario->estado,
                    'cep' => $usuario->cep,
                    'data_cadastro' => $usuario->data_cadastro,
                    'ativo' => $usuario->ativo
                ]
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Usuário não encontrado'
            ]);
        }
    } else if(isset($_GET['list_all']) && verificarAdmin()) {
        // Listar todos os usuários (apenas admin)
        $usuarios = $usuario->listarTodos();
        echo json_encode([
            'success' => true,
            'usuarios' => $usuarios
        ]);
    } else {
        // Buscar dados do usuário logado
        if($usuario->buscarPorId($_SESSION['user_id'])) {
            echo json_encode([
                'success' => true,
                'usuario' => [
                    'id' => $usuario->id,
                    'nome' => $usuario->nome,
                    'email' => $usuario->email,
                    'perfil' => $usuario->perfil,
                    'telefone' => $usuario->telefone,
                    'endereco' => $usuario->endereco,
                    'cidade' => $usuario->cidade,
                    'estado' => $usuario->estado,
                    'cep' => $usuario->cep,
                    'data_cadastro' => $usuario->data_cadastro,
                    'ativo' => $usuario->ativo
                ]
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Usuário não encontrado'
            ]);
        }
    }
} else if($method == 'PUT') {
    if(!verificarAuth()) {
        echo json_encode([
            'success' => false,
            'message' => 'Login necessário'
        ]);
        exit;
    }
    
    $data = json_decode(file_get_contents("php://input"));
    
    if(isset($data->action)) {
        switch($data->action) {
            case 'update_profile':
                $usuario->id = $_SESSION['user_id'];
                $usuario->nome = $data->nome;
                $usuario->telefone = $data->telefone ?? '';
                $usuario->endereco = $data->endereco ?? '';
                $usuario->cidade = $data->cidade ?? '';
                $usuario->estado = $data->estado ?? '';
                $usuario->cep = $data->cep ?? '';
                
                if($usuario->atualizar()) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Perfil atualizado com sucesso'
                    ]);
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Erro ao atualizar perfil'
                    ]);
                }
                break;
                
            case 'change_password':
                if(isset($data->nova_senha)) {
                    $usuario->id = $_SESSION['user_id'];
                    if($usuario->alterarSenha($data->nova_senha)) {
                        echo json_encode([
                            'success' => true,
                            'message' => 'Senha alterada com sucesso'
                        ]);
                    } else {
                        echo json_encode([
                            'success' => false,
                            'message' => 'Erro ao alterar senha'
                        ]);
                    }
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Nova senha não fornecida'
                    ]);
                }
                break;
                
            default:
                echo json_encode([
                    'success' => false,
                    'message' => 'Ação não reconhecida'
                ]);
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Ação não especificada'
        ]);
    }
} else if($method == 'DELETE') {
    if(!verificarAuth()) {
        echo json_encode([
            'success' => false,
            'message' => 'Login necessário'
        ]);
        exit;
    }
    
    $usuario_id = $_GET['id'] ?? null;
    
    if($usuario_id) {
        // Verificar se pode excluir (próprio usuário ou admin)
        if($usuario_id == $_SESSION['user_id'] || verificarAdmin()) {
            $usuario->id = $usuario_id;
            if($usuario->excluir()) {
                if($usuario_id == $_SESSION['user_id']) {
                    // Se excluiu próprio usuário, fazer logout
                    session_destroy();
                }
                echo json_encode([
                    'success' => true,
                    'message' => 'Usuário excluído com sucesso'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Erro ao excluir usuário'
                ]);
            }
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Sem permissão para excluir este usuário'
            ]);
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'ID do usuário não fornecido'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Método não permitido'
    ]);
}
?>
