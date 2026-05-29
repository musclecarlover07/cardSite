<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

require_once __DIR__ . "/../core/db.php";

$pdo = getPDO('Ashes'); 

$mode = $_GET['mode'] ?? '';
$environmentId = $_GET['environmentId'] ?? '';

try {

    /**
     * =========================
     * MODE 1: ENVIRONMENT LIST
     * =========================
     */
    if (empty($environmentId)) {

        $sql = "
            SELECT 
                e.environmentId,
                e.environmentName,
                t.typeName,
                x.expansionName,
                x.expansionId
            FROM tblEnvironment e
            JOIN tblEnvironmentType t 
                ON t.environmentTypeId = e.environmentTypeId
            JOIN tblExpansion x 
                ON x.expansionId = e.expansionId
        ";

        $params = [];

        if (!empty($mode)) {
            $sql .= " WHERE t.typeName = :mode";
            $params['mode'] = $mode;
        }

        $sql .= " ORDER BY x.expansionName, e.environmentName";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        exit;
    }

    /**
     * =========================
     * MODE 2: MODAL (ALL PLAYS)
     * =========================
     */
    $sql = "
        SELECT *
        FROM vwAllPlays
        WHERE environmentId = :environmentId
        ORDER BY playId
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'environmentId' => $environmentId
    ]);

    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));

} catch (Exception $e) {

    http_response_code(500);

    echo json_encode([
        "error" => $e->getMessage()
    ]);
}
