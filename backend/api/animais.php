<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/database.php';
require_once '../classes/Animal.php';
require_once '../classes/SolicitacaoAdocao.php';

$database = new Database();
$db = $database->getConnection();
$animal = new Animal($db);
$solicitacao = new SolicitacaoAdocao($db);

$method = $_SERVER['REQUEST_METHOD'];

// Verificar autenticação para operações que requerem login
function verificarAuth() {
    session_start();
    return isset($_SESSION['user_id']);
}

if($method == 'GET') {
    if(isset($_GET['id'])) {
        // Buscar animal específico
        if($animal->buscarPorId($_GET['id'])) {
            echo json_encode([
                'success' => true,
                'animal' => [
                    'id' => $animal->id,
                    'nome' => $animal->nome,
                    'especie' => $animal->especie,
                    'raca' => $animal->raca,
                    'idade' => $animal->idade,
                    'sexo' => $animal->sexo,
                    'tamanho' => $animal->tamanho,
                    'cor' => $animal->cor,
                    'descricao' => $animal->descricao,
                    'foto_url' => $animal->foto_url,
                    'cidade' => $animal->cidade,
                    'estado' => $animal->estado,
                    'data_publicacao' => $animal->data_publicacao,
                    'adotado' => $animal->adotado
                ]
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Animal não encontrado'
            ]);
        }
    } else if(isset($_GET['search'])) {
        // Buscar animais
        $resultados = $animal->buscar($_GET['search']);
        echo json_encode([
            'success' => true,
            'animais' => $resultados
        ]);
    } else {
        // Listar animais para adoção
        $filtros = [];
        if(isset($_GET['especie'])) $filtros['especie'] = $_GET['especie'];
        if(isset($_GET['cidade'])) $filtros['cidade'] = $_GET['cidade'];
        if(isset($_GET['busca'])) $filtros['busca'] = $_GET['busca'];
        
        $animais = $animal->listarParaAdocao($filtros);
        echo json_encode([
            'success' => true,
            'animais' => $animais
        ]);
    }
} else if($method == 'POST') {
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
            case 'create':
                $animal->nome = $data->nome;
                $animal->especie = $data->especie;
                $animal->raca = $data->raca ?? '';
                $animal->idade = $data->idade ?? null;
                $animal->sexo = $data->sexo;
                $animal->tamanho = $data->tamanho ?? '';
                $animal->cor = $data->cor ?? '';
                $animal->descricao = $data->descricao ?? '';
                $animal->foto_url = $data->foto_url ?? '';
                $animal->cidade = $data->cidade;
                $animal->estado = $data->estado;
                $animal->usuario_id = $_SESSION['user_id'];
                
                if($animal->criar()) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Animal cadastrado com sucesso',
                        'animal_id' => $animal->id
                    ]);
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Erro ao cadastrar animal'
                    ]);
                }
                break;
                
            case 'solicitar_adocao':
                $solicitacao->animal_id = $data->animal_id;
                $solicitacao->solicitante_nome = $data->solicitante_nome;
                $solicitacao->solicitante_email = $data->solicitante_email;
                $solicitacao->solicitante_telefone = $data->solicitante_telefone ?? '';
                $solicitacao->mensagem = $data->mensagem ?? '';
                
                if($solicitacao->criar()) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Solicitação de adoção enviada com sucesso'
                    ]);
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Erro ao enviar solicitação'
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
} else if($method == 'PUT') {
    if(!verificarAuth()) {
        echo json_encode([
            'success' => false,
            'message' => 'Login necessário'
        ]);
        exit;
    }
    
    $data = json_decode(file_get_contents("php://input"));
    
    $animal->id = $data->id;
    $animal->nome = $data->nome;
    $animal->especie = $data->especie;
    $animal->raca = $data->raca ?? '';
    $animal->idade = $data->idade ?? null;
    $animal->sexo = $data->sexo;
    $animal->tamanho = $data->tamanho ?? '';
    $animal->cor = $data->cor ?? '';
    $animal->descricao = $data->descricao ?? '';
    $animal->foto_url = $data->foto_url ?? '';
    $animal->cidade = $data->cidade;
    $animal->estado = $data->estado;
    $animal->usuario_id = $_SESSION['user_id'];
    
    if($animal->atualizar()) {
        echo json_encode([
            'success' => true,
            'message' => 'Animal atualizado com sucesso'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Erro ao atualizar animal'
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
    
    $animal_id = $_GET['id'] ?? null;
    
    if($animal_id) {
        $animal->id = $animal_id;
        if($animal->excluir($_SESSION['user_id'])) {
            echo json_encode([
                'success' => true,
                'message' => 'Animal excluído com sucesso'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Erro ao excluir animal'
            ]);
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'ID do animal não fornecido'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Método não permitido'
    ]);
}
?>
