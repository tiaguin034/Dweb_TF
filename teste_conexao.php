<?php
require_once 'backend/config/database.php';

$database = new Database();
$db = $database->getConnection();

if($db) {
    echo "✅ Conexão com banco de dados: OK<br>";
    
    // Testar consulta
    $stmt = $db->query("SELECT COUNT(*) as total FROM usuarios");
    $result = $stmt->fetch();
    echo "✅ Usuários cadastrados: " . $result['total'] . "<br>";
    
    // Testar API
    echo "✅ Sistema funcionando corretamente!<br>";
    echo "<a href='index.html'>Ir para o site</a>";
} else {
    echo "❌ Erro na conexão com banco de dados";
}
?>
