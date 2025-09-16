<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/database.php';
require_once '../classes/Campanha.php';

$database = new Database();
$db = $database->getConnection();
$campanha = new Campanha($db);

$method = $_SERVER['REQUEST_METHOD'];

// Verificar autenticação para operações que requerem login
function verificarAuth() {
    session_start();
    return isset($_SESSION['user_id']);
}

if($method == 'GET') {
    if(isset($_GET['id'])) {
        // Buscar campanha específica
        if($campanha->buscarPorId($_GET['id'])) {
            echo json_encode([
                'success' => true,
                'campanha' => [
                    'id' => $campanha->id,
                    'nome' => $campanha->nome,
                    'descricao' => $campanha->descricao,
                    'tipo' => $campanha->tipo,
                    'data_evento' => $campanha->data_evento,
                    'hora_inicio' => $campanha->hora_inicio,
                    'hora_fim' => $campanha->hora_fim,
                    'cidade' => $campanha->cidade,
                    'estado' => $campanha->estado,
                    'endereco' => $campanha->endereco,
                    'vagas_disponiveis' => $campanha->vagas_disponiveis,
                    'vagas_preenchidas' => $campanha->vagas_preenchidas,
                    'ativa' => $campanha->ativa,
                    'data_criacao' => $campanha->data_criacao
                ]
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Campanha não encontrada'
            ]);
        }
    } else if(isset($_GET['search'])) {
        // Buscar campanhas
        $resultados = $campanha->buscar($_GET['search']);
        echo json_encode([
            'success' => true,
            'campanhas' => $resultados
        ]);
    } else {
        // Listar campanhas ativas
        $filtros = [];
        if(isset($_GET['cidade'])) $filtros['cidade'] = $_GET['cidade'];
        if(isset($_GET['data'])) $filtros['data'] = $_GET['data'];
        if(isset($_GET['tipo'])) $filtros['tipo'] = $_GET['tipo'];
        if(isset($_GET['busca'])) $filtros['busca'] = $_GET['busca'];
        
        $campanhas = $campanha->listarAtivas($filtros);
        echo json_encode([
            'success' => true,
            'campanhas' => $campanhas
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
                $campanha->nome = $data->nome;
                $campanha->descricao = $data->descricao;
                $campanha->tipo = $data->tipo;
                $campanha->data_evento = $data->data_evento;
                $campanha->hora_inicio = $data->hora_inicio ?? null;
                $campanha->hora_fim = $data->hora_fim ?? null;
                $campanha->cidade = $data->cidade;
                $campanha->estado = $data->estado;
                $campanha->endereco = $data->endereco ?? '';
                $campanha->usuario_id = $_SESSION['user_id'];
                $campanha->vagas_disponiveis = $data->vagas_disponiveis ?? 0;
                
                if($campanha->criar()) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Campanha criada com sucesso',
                        'campanha_id' => $campanha->id
                    ]);
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Erro ao criar campanha'
                    ]);
                }
                break;
                
            case 'participar':
                if($campanha->buscarPorId($data->campanha_id)) {
                    if($campanha->temVagasDisponiveis()) {
                        if($campanha->incrementarVagasPreenchidas()) {
                            echo json_encode([
                                'success' => true,
                                'message' => 'Inscrição realizada com sucesso'
                            ]);
                        } else {
                            echo json_encode([
                                'success' => false,
                                'message' => 'Erro ao realizar inscrição'
                            ]);
                        }
                    } else {
                        echo json_encode([
                            'success' => false,
                            'message' => 'Não há vagas disponíveis'
                        ]);
                    }
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Campanha não encontrada'
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
    
    $campanha->id = $data->id;
    $campanha->nome = $data->nome;
    $campanha->descricao = $data->descricao;
    $campanha->tipo = $data->tipo;
    $campanha->data_evento = $data->data_evento;
    $campanha->hora_inicio = $data->hora_inicio ?? null;
    $campanha->hora_fim = $data->hora_fim ?? null;
    $campanha->cidade = $data->cidade;
    $campanha->estado = $data->estado;
    $campanha->endereco = $data->endereco ?? '';
    $campanha->usuario_id = $_SESSION['user_id'];
    $campanha->vagas_disponiveis = $data->vagas_disponiveis ?? 0;
    
    if($campanha->atualizar()) {
        echo json_encode([
            'success' => true,
            'message' => 'Campanha atualizada com sucesso'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Erro ao atualizar campanha'
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
    
    $campanha_id = $_GET['id'] ?? null;
    
    if($campanha_id) {
        $campanha->id = $campanha_id;
        if($campanha->excluir($_SESSION['user_id'])) {
            echo json_encode([
                'success' => true,
                'message' => 'Campanha excluída com sucesso'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Erro ao excluir campanha'
            ]);
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'ID da campanha não fornecido'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Método não permitido'
    ]);
}
?>
