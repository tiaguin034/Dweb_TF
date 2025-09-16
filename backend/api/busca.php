<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/database.php';
require_once '../classes/Animal.php';
require_once '../classes/Campanha.php';

$database = new Database();
$db = $database->getConnection();
$animal = new Animal($db);
$campanha = new Campanha($db);

$method = $_SERVER['REQUEST_METHOD'];

if($method == 'GET') {
    if(isset($_GET['q']) && !empty($_GET['q'])) {
        $termo = $_GET['q'];
        $tipo = $_GET['tipo'] ?? 'all'; // all, animais, campanhas
        
        $resultados = [
            'success' => true,
            'termo' => $termo,
            'animais' => [],
            'campanhas' => []
        ];
        
        if($tipo === 'all' || $tipo === 'animais') {
            $resultados['animais'] = $animal->buscar($termo);
        }
        
        if($tipo === 'all' || $tipo === 'campanhas') {
            $resultados['campanhas'] = $campanha->buscar($termo);
        }
        
        echo json_encode($resultados);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Termo de busca não fornecido'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Método não permitido'
    ]);
}
?>
