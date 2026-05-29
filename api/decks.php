<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

require_once __DIR__ . '/../core/db.php';

$pdo = getPDO('Ashes'); 

$action = $_GET['action'] ?? 'list';

try {

    /* =========================
       GET ALL DECKS
    ========================= */

    if ($action === 'list') {

        $stmt = $pdo->query("
            SELECT deckId, deckName 
            FROM tblDeck 
            ORDER BY deckName
        ");

        echo json_encode($stmt->fetchAll());
        exit;
    }

    /* =========================
       GET PILOTS FOR DECK
    ========================= */

    if ($action === 'pilots') {

        $deckId = (int)($_GET['deckId'] ?? 0);

        $stmt = $pdo->prepare("
            SELECT 
                d.deckId,
                d.deckName,
                GROUP_CONCAT(p.name ORDER BY p.name SEPARATOR ', ') AS phoenixbornNames
            FROM tblDeck d
            JOIN tblDeckPilot dp ON dp.deckId = d.deckId
            JOIN tblPhoenixborn p ON p.id = dp.pbId
            WHERE d.deckId = :deckId
            GROUP BY d.deckId, d.deckName
        ");

        $stmt->execute([
            ':deckId' => $deckId
        ]);

        echo json_encode($stmt->fetch() ?: []);
        exit;
    }

    echo json_encode([
        "error" => "Invalid action"
    ]);

} catch (Exception $e) {

    http_response_code(500);

    echo json_encode([
        "error" => $e->getMessage()
    ]);
}
