<?php

header('Content-Type: application/json');
require_once __DIR__ . '/../core/db.php';

$pdo = getPDO('Ashes'); 

try {

    $data = json_decode(file_get_contents("php://input"), true);

    if (!$data) {
        throw new Exception("Invalid JSON payload");
    }

    $pdo->beginTransaction();

    /* =========================
       1. INSERT tblPlay
    ========================= */

    $stmt = $pdo->prepare("
        INSERT INTO tblPlay (environmentId, difficultyId, win)
        VALUES (:env, :diff, :win)
    ");

    $stmt->execute([
        ':env' => $data['environmentId'],
        ':diff' => $data['difficultyId'],
        ':win' => $data['win']
    ]);

    $playId = $pdo->lastInsertId();

    /* =========================
       2. RESOLVE PILOT → deckId
       (tblDeck is source of truth)
    ========================= */

    function resolvePilotDeckId($pdo, $pilotName) {

        if (!$pilotName) return null;

        $stmt = $pdo->prepare("
            SELECT deckId
            FROM tblDeck
            WHERE deckName = :name
            LIMIT 1
        ");

        $stmt->execute([':name' => $pilotName]);

        $row = $stmt->fetch();

        return $row['deckId'] ?? null;
    }

    /* =========================
       3. INSERT PLAYER
    ========================= */

    function insertPlayer($pdo, $playId, $playerNum, $playerData) {

        if (!$playerData || !$playerData['deckId']) return;

        $pilotId = resolvePilotDeckId($pdo, $playerData['pilotName']);

        $stmt = $pdo->prepare("
            INSERT INTO tblPlayDeck (playId, deckId, pilotId, player)
            VALUES (:playId, :deckId, :pilotId, :player)
        ");

        $stmt->execute([
            ':playId'  => $playId,
            ':deckId'  => $playerData['deckId'],
            ':pilotId' => $pilotId,
            ':player'  => $playerNum
        ]);
    }

    /* =========================
       4. PLAYER INSERTS
    ========================= */

    insertPlayer($pdo, $playId, 1, $data['player1'] ?? null);
    insertPlayer($pdo, $playId, 2, $data['player2'] ?? null);

    /* =========================
       5. COMMIT
    ========================= */

    $pdo->commit();

    echo json_encode([
        "success" => true,
        "playId" => $playId
    ]);

} catch (Exception $e) {

    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    http_response_code(500);

    echo json_encode([
        "error" => $e->getMessage()
    ]);
}
