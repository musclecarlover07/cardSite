<?php

require_once "../core/header.php";
require_once "../core/db.php";

$pdo = getPDO('Ashes'); 

$sql = "SELECT * FROM vwEnvironmentStats ORDER BY cycle, expansionId, environmentId";

$stmt = $pdo->prepare($sql);
$stmt->execute();

$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* =========================
   GROUP DATA
========================= */

$grouped = [];

foreach ($rows as $row) {

    $cycle = $row['cycle'];
    $expansionId = $row['expansionId'];
    $expansion = $row['expansionName'];

    if (!isset($grouped[$cycle])) {
        $grouped[$cycle] = [];
    }

    if (!isset($grouped[$cycle][$expansionId])) {
        $grouped[$cycle][$expansionId] = [
            'expansionName' => $expansion,
            'rows' => []
        ];
    }

    $grouped[$cycle][$expansionId]['rows'][] = $row;
}

/* =========================
   CYCLE ORDER
========================= */

$cycleOrder = [
    'Red Rains',
    'Ascendancy'
];

?>

<main class="site-main">

<section class="card">

<h2>Environment Statistics</h2>

<?php foreach ($cycleOrder as $cycleName): ?>
<?php if (!isset($grouped[$cycleName])) continue; ?>

<div class="environment-cycle-block">

<h2 class="environment-cycle-title">
    <?= htmlspecialchars($cycleName) ?>
</h2>

<?php ksort($grouped[$cycleName]); ?>

<?php foreach ($grouped[$cycleName] as $expansionId => $expansionData): ?>

<div class="environment-expansion-block">

<h3 class="environment-expansion-title">
    <?= htmlspecialchars($expansionData['expansionName']) ?>
</h3>

<table class="data-table striped environment-table">

<thead>
<tr>
    <th rowspan="2">Environment</th>

    <th colspan="3">Standard</th>
    <th colspan="3">Heroic</th>

    <th rowspan="2">Plays</th>
    <th rowspan="2">Wins</th>
    <th rowspan="2">Losses</th>
    <th rowspan="2">Win %</th>
</tr>
<tr>
    <th>1</th><th>2</th><th>3</th>
    <th>1</th><th>2</th><th>3</th>
</tr>
</thead>

<tbody>

<?php foreach ($expansionData['rows'] as $row): ?>

<?php
$completed = $row['Difficulties Completed'] ?? '';

$difficultyChecks = [
    'Standard 1' => str_contains($completed, 'Standard 1'),
    'Standard 2' => str_contains($completed, 'Standard 2'),
    'Standard 3' => str_contains($completed, 'Standard 3'),
    'Heroic 1' => str_contains($completed, 'Heroic 1'),
    'Heroic 2' => str_contains($completed, 'Heroic 2'),
    'Heroic 3' => str_contains($completed, 'Heroic 3'),
];
?>

<tr class="environment-row"
    data-environment-id="<?= htmlspecialchars($row['environmentId']) ?>"
    data-environment-name="<?= htmlspecialchars($row['environmentName']) ?>">

    <td><?= htmlspecialchars($row['environmentName']) ?></td>

    <td class="center"><?= $difficultyChecks['Standard 1'] ? '✓' : '' ?></td>
    <td class="center"><?= $difficultyChecks['Standard 2'] ? '✓' : '' ?></td>
    <td class="center"><?= $difficultyChecks['Standard 3'] ? '✓' : '' ?></td>

    <td class="center"><?= $difficultyChecks['Heroic 1'] ? '✓' : '' ?></td>
    <td class="center"><?= $difficultyChecks['Heroic 2'] ? '✓' : '' ?></td>
    <td class="center"><?= $difficultyChecks['Heroic 3'] ? '✓' : '' ?></td>

    <td class="center"><?= $row['totalPlays'] ?></td>
    <td class="center"><?= $row['wins'] ?></td>
    <td class="center"><?= $row['losses'] ?></td>
    <td class="center"><?= $row['winRatePercent'] ?>%</td>

</tr>

<?php endforeach; ?>

</tbody>
</table>

</div>

<?php endforeach; ?>

</div>

<?php endforeach; ?>

</section>

</main>

<!-- =========================
     MODAL
========================= -->

<div id="environment-modal" class="modal hidden">

    <div class="modal-backdrop"></div>

    <div class="modal-content">

        <button id="environment-modal-close" class="modal-close">×</button>

        <h2 id="environment-modal-title"></h2>

        <table class="data-table striped">
            <thead>
                <tr>
                    <th>Difficulty</th>
                    <th>Mike</th>
                    <th>Lana</th>
                    <th>Result</th>
                </tr>
            </thead>
            <tbody id="environment-modal-table-body"></tbody>
        </table>

    </div>
</div>

<script>

const modal = document.getElementById('environment-modal');
const title = document.getElementById('environment-modal-title');
const body = document.getElementById('environment-modal-table-body');

document.getElementById('environment-modal-close')
    .addEventListener('click', closeModal);

modal.querySelector('.modal-backdrop')
    .addEventListener('click', closeModal);

function openModal() {
    modal.classList.remove('hidden');
    document.body.classList.add('modal-open');
}

function closeModal() {
    modal.classList.add('hidden');
    document.body.classList.remove('modal-open');
    body.innerHTML = '';
}

document.querySelectorAll('.environment-row').forEach(row => {

    row.addEventListener('click', async () => {

        const id = row.dataset.environmentId;
        const name = row.dataset.environmentName;

        title.textContent = name;
        body.innerHTML = `<tr><td colspan="4">Loading...</td></tr>`;

        openModal();

        try {

            const res = await fetch(`/cardSite/api/environments.php?environmentId=${id}`);

            const data = await res.json();

            if (!Array.isArray(data) || data.length === 0) {
                body.innerHTML = `<tr><td colspan="4">No plays found</td></tr>`;
                return;
            }

            body.innerHTML = '';

            data.forEach(p => {

                const tr = document.createElement('tr');

                tr.innerHTML = `
                    <td>${p.difficulty}</td>
                    <td>${p.Mike}</td>
                    <td>${p.Lana}</td>
                    <td>${p.result}</td>
                `;

                body.appendChild(tr);
            });

        } catch (err) {
            console.error(err);
            body.innerHTML = `<tr><td colspan="4">Error loading data</td></tr>`;
        }
    });

});

</script>
