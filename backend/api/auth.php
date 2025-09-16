<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/database.php';
require_once '../classes/Usuario.php';

$database = new Database();
$db = $database->getConnection();
$usuario = new Usuario($db);

$method = $_SERVER['REQUEST_METHOD'];

if($method == 'POST') {
    $data = json_decode(file_get_contents("php://input"));
    
    if(isset($data->action)) {
        switch($data->action) {
            case 'login':
                if(isset($data->email) && isset($data->senha)) {
                    if($usuario->login($data->email, $data->senha)) {
                        session_start();
                        $_SESSION['user_id'] = $usuario->id;
                        $_SESSION['user_nome'] = $usuario->nome;
                        $_SESSION['user_email'] = $usuario->email;
                        $_SESSION['user_perfil'] = $usuario->perfil;
                        
                        echo json_encode([
                            'success' => true,
                            'message' => 'Login realizado com sucesso',
                            'user' => [
                                'id' => $usuario->id,
                                'nome' => $usuario->nome,
                                'email' => $usuario->email,
                                'perfil' => $usuario->perfil,
                                'cidade' => $usuario->cidade,
                                'estado' => $usuario->estado
                            ]
                        ]);
                    } else {
                        echo json_encode([
                            'success' => false,
                            'message' => 'Email ou senha incorretos'
                        ]);
                    }
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Email e senha são obrigatórios'
                    ]);
                }
                break;
                
            case 'register':
                if(isset($data->nome) && isset($data->email) && isset($data->senha) && isset($data->perfil)) {
                    // Verificar se email já existe
                    if($usuario->emailExiste($data->email)) {
                        echo json_encode([
                            'success' => false,
                            'message' => 'Este email já está cadastrado'
                        ]);
                        break;
                    }
                    
                    $usuario->nome = $data->nome;
                    $usuario->email = $data->email;
                    $usuario->senha = $data->senha;
                    $usuario->perfil = $data->perfil;
                    $usuario->telefone = $data->telefone ?? '';
                    $usuario->endereco = $data->endereco ?? '';
                    $usuario->cidade = $data->cidade ?? '';
                    $usuario->estado = $data->estado ?? '';
                    $usuario->cep = $data->cep ?? '';
                    
                    if($usuario->criar()) {
                        echo json_encode([
                            'success' => true,
                            'message' => 'Usuário cadastrado com sucesso',
                            'user_id' => $usuario->id
                        ]);
                    } else {
                        echo json_encode([
                            'success' => false,
                            'message' => 'Erro ao cadastrar usuário'
                        ]);
                    }
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Dados obrigatórios não fornecidos'
                    ]);
                }
                break;
                
            case 'logout':
                session_start();
                session_destroy();
                echo json_encode([
                    'success' => true,
                    'message' => 'Logout realizado com sucesso'
                ]);
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
} else if($method == 'GET') {
    // Verificar se usuário está logado
    session_start();
    if(isset($_SESSION['user_id'])) {
        if($usuario->buscarPorId($_SESSION['user_id'])) {
            echo json_encode([
                'success' => true,
                'logged_in' => true,
                'user' => [
                    'id' => $usuario->id,
                    'nome' => $usuario->nome,
                    'email' => $usuario->email,
                    'perfil' => $usuario->perfil,
                    'cidade' => $usuario->cidade,
                    'estado' => $usuario->estado,
                    'telefone' => $usuario->telefone,
                    'endereco' => $usuario->endereco,
                    'cep' => $usuario->cep
                ]
            ]);
        } else {
            session_destroy();
            echo json_encode([
                'success' => false,
                'logged_in' => false,
                'message' => 'Usuário não encontrado'
            ]);
        }
    } else {
        echo json_encode([
            'success' => false,
            'logged_in' => false,
            'message' => 'Usuário não logado'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Método não permitido'
    ]);
}
?>
