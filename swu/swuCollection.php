<?php require_once "../core/header.php"; ?>

<main class="swu-layout">

    <!-- =========================
         SETS PANEL
    ========================= -->

    <aside class="swu-sidebar">

        <h2>Sets</h2>

        <div id="sets-container"></div>

    </aside>

    <!-- =========================
         CARDS PANEL
    ========================= -->

    <section class="swu-cards-panel">

        <!-- PINNED HEADER -->
        <div class="swu-cards-sticky-top">

            <div class="swu-panel-header">
                <h2 id="cards-title">Cards</h2>
            </div>

            <!-- SEARCH -->
            <div class="swu-search-wrapper">

                <input
                    type="text"
                    id="swu-card-search"
                    class="swu-search-input"
                    placeholder="Search cards..."
                >

            </div>

            <!-- PINNED COLUMN HEADERS -->
            <div class="swu-card-header-row">
                <div>ID</div>
                <div>Card Name</div>
            </div>

        </div>

        <!-- SCROLLING CONTENT -->
        <div id="cards-container"
             class="swu-card-list">
        </div>

    </section>

    <!-- =========================
         DETAILS PANEL
    ========================= -->

    <aside class="swu-details-panel">

        <h2>Card Details</h2>

        <div id="card-details-container">
            <div class="swu-placeholder">
                Select a card
            </div>
        </div>

    </aside>

</main>

<style>

/* =========================
   STICKY TOP SECTION
========================= */

.swu-cards-panel {
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

.swu-cards-sticky-top {
    position: sticky;
    top: 0;
    z-index: 10;
    background: #1b1b1b;
    padding-bottom: 8px;
}

/* =========================
   SEARCH
========================= */

.swu-search-wrapper {
    margin-bottom: 10px;
}

.swu-search-input {
    width: 100%;
    padding: 10px 12px;
    background: #222;
    border: 1px solid #333;
    border-radius: 8px;
    color: #fff;
    box-sizing: border-box;
}

.swu-search-input:focus {
    outline: none;
    border-color: #666;
}

/* =========================
   CARD LIST
========================= */

.swu-card-list {
    display: flex;
    flex-direction: column;
    gap: 6px;

    overflow-y: auto;
    min-height: 0;
}

/* =========================
   CARD ROWS
========================= */

.swu-card-row,
.swu-card-header-row {
    display: grid;
    grid-template-columns: 60px 1fr;
    gap: 10px;

    height: 56px;              /* 🔥 forces uniform height */
    padding: 8px 10px;

    border: 1px solid #333;
    border-radius: 8px;
    background: #222;

    cursor: pointer;
    box-sizing: border-box;
    align-items: center;
}

.swu-card-header-row {
    background: #1f1f1f;
    font-weight: bold;
    cursor: default;

    position: sticky;
    top: 0;
    z-index: 5;
}

.swu-card-row:hover {
    background: #2b2b2b;
}

.swu-card-id {
    color: #aaa;
    font-size: 0.85rem;
}

.swu-card-name {
    font-weight: 600;
    line-height: 1.2;
}

.swu-card-subname {
    font-size: 0.75rem;
    color: #9a9a9a;
    line-height: 1.2;

    display: block;   /* ensures it actually renders */
}


</style>

<script>

/* =========================
   GLOBAL
========================= */

let currentCards = [];

/* =========================
   INITIAL LOAD
========================= */

loadSets();

/* =========================
   LOAD SETS
========================= */

async function loadSets() {

    const res = await fetch('/cardSite/api/swuSets.php');

    const sets = await res.json();

    renderSets(sets);
}

/* =========================
   RENDER SETS
========================= */

function renderSets(sets) {

    const container = document.getElementById('sets-container');

    let html = '';

    sets.forEach(set => {

        html += `
            <div class="swu-set-item"
                 data-set-id="${set.setId}"
                 data-set-name="${set.setName}">

                <div class="swu-set-name">
                    ${set.setName}
                </div>

                <div class="swu-set-meta">
                    ${set.setCode || '---'} • ${set.setCardCount}
                </div>

            </div>
        `;
    });

    container.innerHTML = html;

    bindSetClicks();
}

/* =========================
   SET CLICK
========================= */

function bindSetClicks() {

    document.querySelectorAll('.swu-set-item')
        .forEach(item => {

            item.addEventListener('click', () => {

                const setId = item.dataset.setId;
                const setName = item.dataset.setName;

                document.getElementById('cards-title')
                    .textContent = setName;

                document.getElementById('swu-card-search')
                    .value = '';

                loadCards(setId);
            });
        });
}

/* =========================
   LOAD CARDS
========================= */

async function loadCards(setId) {

    const res = await fetch(
        '/cardSite/api/swuCards.php?setId=' + setId
    );

    const cards = await res.json();

    currentCards = cards;

    renderCards(cards);
}

/* =========================
   SEARCH
========================= */

document
    .getElementById('swu-card-search')
    .addEventListener('input', e => {

        const term = e.target.value.toLowerCase();

        const filtered = currentCards.filter(card => {

            const fullName = `
                ${card.cardName || ''}
                ${card.cardSubname || ''}
            `.toLowerCase();

            return fullName.includes(term);
        });

        renderCards(filtered);
    });

/* =========================
   RENDER CARDS
========================= */

function renderCards(cards) {

    const container = document.getElementById('cards-container');

    let html = '';

    cards.forEach(card => {

       html += `
    <div class="swu-card-row"
         data-card-id="${card.cardId}">

        <div class="swu-card-id">
            ${card.cardNumber}
        </div>

        <div>

            <div class="swu-card-name">
                ${card.cardName}
            </div>

            ${card.cardSubname ? `
                <div class="swu-card-subname">
                    ${card.cardSubname}
                </div>
            ` : ''}

        </div>

    </div>
`;
    });

    container.innerHTML = html;

    bindCardClicks(cards);
}

/* =========================
   CARD CLICK
========================= */

function bindCardClicks(cards) {

    document.querySelectorAll('.swu-card-row')
        .forEach(item => {

            item.addEventListener('click', () => {

                const cardId = item.dataset.cardId;

                const card = cards.find(
                    x => x.cardId == cardId
                );

                renderCardDetails(card);
            });
        });
}

/* =========================
   CARD DETAILS
========================= */

function renderCardDetails(card) {

    const container =
        document.getElementById(
            'card-details-container'
        );

    container.innerHTML = `
        <div class="swu-card-details">

            <h2>${card.cardName}</h2>

            ${card.cardSubname ? `
                <div class="swu-card-subname">
                    ${card.cardSubname}
                </div>
            ` : ''}

            <div class="swu-detail-row">
                <strong>Number:</strong>
                ${card.cardNumber}
            </div>

            <div class="swu-detail-row">
                <strong>Type:</strong>
                ${card.cardType || '-'}
            </div>

            <div class="swu-detail-row">
                <strong>Rarity:</strong>
                ${card.rarity || '-'}
            </div>

            <hr>

            <h3>Collection</h3>

            <div class="swu-collection-grid">

                <label>
                    Normal
                    <input type="number" value="0">
                </label>

                <label>
                    Foil
                    <input type="number" value="0">
                </label>

                <label>
                    Hyperspace
                    <input type="number" value="0">
                </label>

                <label>
                    Hyperspace Foil
                    <input type="number" value="0">
                </label>

            </div>

        </div>
    `;
}

</script>
