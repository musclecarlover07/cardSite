<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

require_once __DIR__ . '/../core/db.php';

$pdo = getPDO('SWU'); 

try {

    $sql = "
        SELECT
            setId,
            setName,
            setCode,
            setCardCount
        FROM tblSets
        ORDER BY setId
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    echo json_encode(
        $stmt->fetchAll(PDO::FETCH_ASSOC)
    );

} catch (Exception $e) {

    http_response_code(500);

    echo json_encode([
        'error' => $e->getMessage()
    ]);
}
