<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "../core/header.php";
require_once "../core/db.php";

$pdo = getPDO('Ashes'); 

// Query
$sql = "SELECT * FROM vwAllPlays ORDER BY playId";

$stmt = $pdo->prepare($sql);
$stmt->execute();

$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<main class="site-main site-main--all-plays">

    <section class="card">

        <h2>All Plays</h2>

        <table class="data-table striped all-plays-table">

            <thead>
                <tr>

                    <?php
                    if (!empty($rows)) {

                        foreach (array_keys($rows[0]) as $column) {

                            if ($column == "environmentId") {
                                continue;
                            }

                            echo "<th>" . htmlspecialchars($column) . "</th>";
                        }
                    }
                    ?>

                </tr>
            </thead>

            <tbody>

                <?php foreach ($rows as $row): ?>

                    <tr>

                        <?php foreach ($row as $key => $value): ?>

                            <?php if ($key == "environmentId") continue; ?>

                            <td>
                                <?php echo htmlspecialchars($value); ?>
                            </td>

                        <?php endforeach; ?>

                    </tr>

                <?php endforeach; ?>

            </tbody>

        </table>

    </section>

</main>

<?php require_once "../core/footer.php"; ?>
