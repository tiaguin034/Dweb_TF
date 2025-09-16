<?php
// Teste específico para Railway
echo "<h2>🚂 Teste Railway + Vercel</h2>";

echo "<h3>📋 Variáveis de Ambiente:</h3>";
echo "DB_HOST: " . ($_ENV['DB_HOST'] ?? 'NÃO DEFINIDA') . "<br>";
echo "DB_NAME: " . ($_ENV['DB_NAME'] ?? 'NÃO DEFINIDA') . "<br>";
echo "DB_USER: " . ($_ENV['DB_USER'] ?? 'NÃO DEFINIDA') . "<br>";
echo "DB_PASS: " . (isset($_ENV['DB_PASS']) ? 'DEFINIDA ✅' : 'NÃO DEFINIDA ❌') . "<br>";

echo "<h3>🔗 Teste de Conexão:</h3>";
try {
    require_once 'backend/config/database.php';
    $database = new Database();
    $db = $database->getConnection();
    
    if($db) {
        echo "✅ <strong>Conexão com Railway MySQL: SUCESSO!</strong><br>";
        
        // Testar se tabelas existem
        $tables = ['usuarios', 'animais', 'campanhas', 'solicitacoes_adocao'];
        foreach($tables as $table) {
            $stmt = $db->query("SHOW TABLES LIKE '$table'");
            if($stmt->rowCount() > 0) {
                echo "✅ Tabela '$table': EXISTE<br>";
            } else {
                echo "❌ Tabela '$table': NÃO EXISTE<br>";
            }
        }
        
        // Testar dados
        $stmt = $db->query("SELECT COUNT(*) as total FROM usuarios");
        $result = $stmt->fetch();
        echo "✅ Usuários cadastrados: " . $result['total'] . "<br>";
        
        $stmt = $db->query("SELECT COUNT(*) as total FROM animais");
        $result = $stmt->fetch();
        echo "✅ Animais cadastrados: " . $result['total'] . "<br>";
        
    } else {
        echo "❌ <strong>Conexão com Railway MySQL: FALHOU!</strong><br>";
    }
} catch(Exception $e) {
    echo "❌ <strong>Erro:</strong> " . $e->getMessage() . "<br>";
    echo "<p><strong>Dica:</strong> Verifique se as variáveis de ambiente estão configuradas corretamente no Vercel.</p>";
}

echo "<h3>🌐 Informações do Servidor:</h3>";
echo "Servidor: " . $_SERVER['SERVER_NAME'] . "<br>";
echo "Ambiente: " . ($_ENV['VERCEL'] ? 'Vercel ✅' : 'Local') . "<br>";
echo "Railway: " . (isset($_ENV['DB_HOST']) && strpos($_ENV['DB_HOST'], 'railway') !== false ? 'Detectado ✅' : 'Não detectado') . "<br>";

echo "<h3>📝 Próximos Passos:</h3>";
echo "<ol>";
echo "<li>Se a conexão falhou, verifique as variáveis no Vercel</li>";
echo "<li>Se as tabelas não existem, execute o script SQL no Railway</li>";
echo "<li>Se tudo funcionou, acesse <a href='index.html'>o site</a></li>";
echo "</ol>";
?>
