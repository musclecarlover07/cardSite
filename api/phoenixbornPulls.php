<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

require_once __DIR__ . '/../core/db.php';

$pdo = getPDO('Ashes'); 

try {

    $phoenixbornSql = "
        SELECT *
        FROM vwPhoenixbornStats
        ORDER BY phoenixbornId
    ";

    $phoenixbornStmt = $pdo->prepare($phoenixbornSql);
    $phoenixbornStmt->execute();

    $phoenixbornStats = $phoenixbornStmt->fetchAll(PDO::FETCH_ASSOC);

    $monoDeckSql = "
        SELECT *
        FROM vwMonoDeckPilotStats
        ORDER BY monoDeckId, pilotId
    ";

    $monoDeckStmt = $pdo->prepare($monoDeckSql);
    $monoDeckStmt->execute();

    $monoDeckStats = $monoDeckStmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'phoenixbornStats' => $phoenixbornStats,
        'monoDeckStats' => $monoDeckStats
    ]);

} catch (Exception $e) {

    http_response_code(500);

    echo json_encode([
        'error' => $e->getMessage()
    ]);
}
