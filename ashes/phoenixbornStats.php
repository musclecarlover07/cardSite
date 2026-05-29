<?php require_once "../core/header.php"; ?>

<main class="site-main">

<section class="card">

    <h2>Phoenixborn Statistics</h2>

    <div id="phoenixborn-stats-container"></div>

</section>

<section class="card" style="margin-top:24px;">

    <h2>Mono Deck Pilot Statistics</h2>

    <div id="mono-deck-container"></div>

</section>

</main>


<div id="pb-modal" class="modal hidden">

    <div class="modal-backdrop"></div>

    <div class="modal-content pb-modal-content">

        <button id="pb-modal-close"
                class="modal-close"
                type="button">
            ×
        </button>

        <h2 id="pb-modal-title"></h2>

        <h3>First Five</h3>

        <div id="first-five-container"
             class="first-five-wrapper">
        </div>

        <div id="pb-modal-body"></div>

    </div>

</div>


<script>

async function loadPhoenixbornStats() {

    try {

        const res = await fetch('/cardSite/api/phoenixbornPulls.php');

        const data = await res.json();

        renderPhoenixbornStats(data.phoenixbornStats);

        renderMonoDeckStats(data.monoDeckStats);

    } catch (err) {

        console.error(err);
    }
}

/* =========================
   PHOENIXBORN TABLE
========================= */

function renderPhoenixbornStats(rows) {

    const container = document.getElementById('phoenixborn-stats-container');

    let html = `
        <table class="data-table striped">

            <thead>
                <tr>
                    <th>ID</th>
                    <th>Phoenixborn</th>
                    <th>Games</th>
                    <th>Wins</th>
                    <th>Win %</th>
                </tr>
            </thead>

            <tbody>
    `;

    rows.forEach(row => {

        html += `
            <tr class="pb-row"
                data-name="${row.phoenixbornName}">

                <td>${row.phoenixbornId}</td>
                <td>${row.phoenixbornName}</td>
                <td>${row.gamesPlayed}</td>
                <td>${row.wins}</td>
                <td>${row.winPct}%</td>

            </tr>
        `;
    });

    html += `
            </tbody>
        </table>
    `;

    container.innerHTML = html;

    bindPhoenixbornRows();
}

/* =========================
   MONO DECK TABLES
========================= */

function renderMonoDeckStats(rows) {

    const container = document.getElementById('mono-deck-container');

    container.innerHTML = '';

    const grouped = {};

    rows.forEach(row => {

        const monoDeckId = row.monoDeckId;

        if (!grouped[monoDeckId]) {

            grouped[monoDeckId] = {
                monoDeckName: row.monoDeckName,
                rows: []
            };
        }

        grouped[monoDeckId].rows.push(row);
    });

    Object.values(grouped).forEach(group => {

        const section = document.createElement('div');

        let html = `
            <h3>${group.monoDeckName}</h3>

            <table class="data-table striped">

                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Phoenixborn</th>
                        <th>Games</th>
                        <th>Wins</th>
                        <th>Win %</th>
                    </tr>
                </thead>

                <tbody>
        `;

        group.rows.forEach(row => {

            html += `
                <tr class="pb-row"
                    data-name="${row.pilotName}">

                    <td>${row.pilotId}</td>
                    <td>${row.pilotName}</td>
                    <td>${row.gamesPlayed}</td>
                    <td>${row.wins}</td>
                    <td>${row.winPct}%</td>

                </tr>
            `;
        });

        html += `
                </tbody>
            </table>
        `;

        section.innerHTML = html;

        container.appendChild(section);
    });

    bindPhoenixbornRows();
}

/* =========================
   MODAL
========================= */

const pbModal = document.getElementById('pb-modal');

const pbModalTitle = document.getElementById('pb-modal-title');

const pbModalBody = document.getElementById('pb-modal-body');

const firstFiveContainer = document.getElementById('first-five-container');

document
    .getElementById('pb-modal-close')
    .addEventListener('click', closePbModal);

pbModal
    .querySelector('.modal-backdrop')
    .addEventListener('click', closePbModal);

function closePbModal() {

    pbModal.classList.add('hidden');

    document.body.classList.remove('modal-open');
}

function bindPhoenixbornRows() {

    document.querySelectorAll('.pb-row').forEach(row => {

        row.addEventListener('click', async () => {

            const phoenixbornName = row.dataset.name;

            pbModalTitle.textContent = phoenixbornName;

            pbModal.classList.remove('hidden');

            document.body.classList.add('modal-open');

            loadPhoenixbornModal(phoenixbornName);
        });
    });
}

async function loadPhoenixbornModal(phoenixbornName) {

    const res = await fetch('/cardSite/api/phoenixbornModal.php?name=' + encodeURIComponent(phoenixbornName));

    const data = await res.json();

    /* =========================
       FIRST FIVE
    ========================= */

    firstFiveContainer.innerHTML = '';

    data.firstFive.forEach(card => {

        const img = document.createElement('img');

        img.className = 'first-five-card';

        img.src = '/cardSite/assets/images/ashes/FirstFive/' + card.cardName + '.jpg';

        img.alt = card.cardName;

        firstFiveContainer.appendChild(img);
    });

    /* =========================
       NON PILOTED
    ========================= */

    let html = `
        <div class="pb-play-section">

            <h3>Non Piloted Decks</h3>

            <table class="data-table striped">

                <thead>
                    <tr>
                        <th>Play ID</th>
                        <th>Environment</th>
                        <th>Difficulty</th>
                        <th>Mike</th>
                        <th>Lana</th>
                        <th>Result</th>
                    </tr>
                </thead>

                <tbody>
    `;

    data.nonPiloted.forEach(play => {

        html += `
            <tr>
                <td>${play.playId}</td>
                <td>${play.environment}</td>
                <td>${play.difficulty}</td>
                <td>${play.Mike}</td>
                <td>${play.Lana}</td>
                <td>${play.result}</td>
            </tr>
        `;
    });

    html += `
                </tbody>
            </table>

        </div>
    `;

    /* =========================
       PILOTED
    ========================= */

    html += `
        <div class="pb-play-section">

            <h3>Piloted Decks</h3>

            <table class="data-table striped">

                <thead>
                    <tr>
                        <th>Play ID</th>
                        <th>Environment</th>
                        <th>Difficulty</th>
                        <th>Mike</th>
                        <th>Lana</th>
                        <th>Result</th>
                    </tr>
                </thead>

                <tbody>
    `;

    data.piloted.forEach(play => {

        html += `
            <tr>
                <td>${play.playId}</td>
                <td>${play.environment}</td>
                <td>${play.difficulty}</td>
                <td>${play.Mike}</td>
                <td>${play.Lana}</td>
                <td>${play.result}</td>
            </tr>
        `;
    });

    html += `
                </tbody>
            </table>

        </div>
    `;

    pbModalBody.innerHTML = html;
}

loadPhoenixbornStats();

</script>
