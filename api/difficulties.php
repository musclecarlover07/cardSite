<?php
require_once "../core/db.php";

$pdo = getPDO('Ashes'); 

header('Content-Type: application/json');

$mode = $_GET['mode'] ?? '';

try {

    $sql = "
        SELECT 
            d.difficultyId,
            d.difficultyName,
            t.typeName
        FROM tblDifficulty d
        JOIN tblEnvironmentType t 
            ON t.environmentTypeId = d.environmentTypeId
        WHERE t.typeName = ?
        ORDER BY d.difficultyId
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$mode]);

    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));

} catch (Exception $e) {

    http_response_code(500);
    echo json_encode([
        'error' => $e->getMessage()
    ]);
}
