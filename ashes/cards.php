<?php
require_once "../core/db.php";

$conn = getDB("Ashes"); // CHANGE if your DB name differs

$pdo = getPDO('Ashes'); 

$result = $conn->query("SELECT * FROM tblCards LIMIT 50");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Ashes Cards</title>

    <style>
        body {
            font-family: Arial;
            background: #111;
            color: white;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        th, td {
            border: 1px solid #444;
            padding: 8px;
        }

        th {
            background: #222;
        }
    </style>
</head>

<body>

<h1>Ashes Cards</h1>

<table>
    <tr>
        <th>ID</th>
        <th>Name</th>
    </tr>

    <?php while ($row = $result->fetch_assoc()): ?>

        <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo $row['name']; ?></td>
        </tr>

    <?php endwhile; ?>

</table>

</body>
</html>
