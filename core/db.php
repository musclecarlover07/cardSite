<?php

function getPDO($database = 'Ashes') {

    $host = 'localhost';
    $username = 'root';
    $password = 'M@nutd07M@nutd07';

    switch ($database) {

        case 'Ashes':
            $dbName = 'Ashes';
            break;

        case 'SWU':
            $dbName = 'SWUCardDB';
            break;

        default:
            throw new Exception("Unknown database: " . $database);
    }

    try {

        return new PDO(
            "mysql:host=$host;dbname=$dbName;charset=utf8mb4",
            $username,
            $password,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]
        );

    } catch (PDOException $e) {

        die("DB Connection failed: " . $e->getMessage());
    }
}
