<?php require_once "../core/header.php"; ?>



<main class="site-main">

<section class="card">

  <h2>Record New Play</h2>

  <form id="playForm" class="play-form">

    <!-- MODE -->
    <div class="form-row">
      <label>Environment Type</label>

      <div class="mode-toggle">
        <button type="button" class="mode-btn active" data-mode="Chimera">Chimera</button>
        <button type="button" class="mode-btn" data-mode="Dragonborn">Dragonborn</button>
      </div>

      <input type="hidden" id="mode" name="mode" value="Chimera">
    </div>

    <!-- ENVIRONMENT -->
    <div class="form-row">
      <label>Environment</label>

      <select id="environmentId" name="environmentId">
        <option value="">-- Select Environment --</option>
      </select>
    </div>

    <!-- DIFFICULTY -->
    <div class="form-row">
      <label>Difficulty</label>

      <select id="difficultyId" name="difficultyId">
        <option value="">-- Select Difficulty --</option>
      </select>
    </div>

    <hr style="border-color:#333; margin:16px 0;">

    <!-- PLAYERS -->
    <div class="players-wrapper">

      <!-- PLAYER 1 -->
      <section class="player-section player1">

        <h3>
          Player 1
          <span class="player-badge player-badge-required">
            Required
          </span>
        </h3>

        <div class="form-row">
          <label>Deck</label>

          <select name="player1DeckId" class="deck-select">
            <option value="">-- Select Deck --</option>
          </select>
        </div>

        <div class="form-row pilot-wrapper" style="display:none;">
          <label>Pilot</label>

          <select name="player1PhoenixbornId">
            <option value="">-- Select Pilot --</option>
          </select>
        </div>

      </section>

      <!-- PLAYER 2 -->
      <section class="player-section player2">

        <h3>
          Player 2
          <span class="player-badge player-badge-optional">
            Optional
          </span>
        </h3>

        <div class="form-row">
          <label>Deck</label>

          <select name="player2DeckId" class="deck-select">
            <option value="">-- Select Deck --</option>
          </select>
        </div>

        <div class="form-row pilot-wrapper" style="display:none;">
          <label>Pilot</label>

          <select name="player2PhoenixbornId">
            <option value="">-- Select Pilot --</option>
          </select>
        </div>

      </section>

    </div>

    <hr style="border-color:#333; margin:16px 0;">

    <!-- RESULT -->
    <div class="form-row">

      <label>Result</label>

      <select name="didYouWin">
        <option value="">-- Select Result --</option>
        <option value="Win">Win</option>
        <option value="Loss">Loss</option>
      </select>

    </div>

    <div class="form-actions">
      <button type="submit">
        Submit Play
      </button>
    </div>

  </form>

</section>

</main>

<script>

let currentMode = 'Chimera';

/* =========================
   MODE SWITCH
========================= */
document.querySelectorAll('.mode-btn').forEach(btn => {

  btn.addEventListener('click', async () => {

    document.querySelectorAll('.mode-btn')
      .forEach(b => b.classList.remove('active'));

    btn.classList.add('active');

    currentMode = btn.dataset.mode;

    document.getElementById('mode').value =
      currentMode;

    await loadEnvironments();
    await loadDifficulties();

  });

});

/* =========================
   ENVIRONMENTS
========================= */
async function loadEnvironments() {

  const select =
    document.getElementById('environmentId');

  select.innerHTML =
    `<option value="">-- Select Environment --</option>`;

  const res = await fetch(
    `/cardSite/api/environments.php?mode=${currentMode}`
  );

  const data = await res.json();

  console.log('ENVIRONMENT DATA:', data);

  data.forEach(item => {

    console.log(
      item.environmentName,
      item.environmentId,
      item.expansionId
    );

    const opt = document.createElement('option');

    opt.value = item.environmentId;

    opt.textContent =
      `${item.environmentName} (${item.expansionName})`;

    select.appendChild(opt);

  });

  select.addEventListener('change', () => {

    console.log(
      'SELECTED VALUE:',
      select.value
    );

  });

}

/* =========================
   DIFFICULTY
========================= */
async function loadDifficulties() {

  const select =
    document.getElementById('difficultyId');

  select.innerHTML =
    `<option value="">-- Select Difficulty --</option>`;

  const res = await fetch(
    `/cardSite/api/difficulties.php?mode=${currentMode}`
  );

  const data = await res.json();

  data.forEach(item => {

    const opt = document.createElement('option');

    opt.value = item.difficultyId;

    opt.textContent = item.difficultyName;

    select.appendChild(opt);

  });

}

/* =========================
   DECKS
========================= */
async function loadDecks() {

  const res = await fetch(
    `/cardSite/api/decks.php?action=list`
  );

  const data = await res.json();

  document.querySelectorAll('.deck-select')
    .forEach(select => {

      select.innerHTML =
        `<option value="">-- Select Deck --</option>`;

      data.forEach(deck => {

        const opt = document.createElement('option');

        opt.value = deck.deckId;
        opt.textContent = deck.deckName;

        select.appendChild(opt);

      });

  });

}

/* =========================
   PILOT LOGIC
========================= */
function bindDeckEvents() {

  document.querySelectorAll('.deck-select')
    .forEach(select => {

      select.addEventListener('change', async (e) => {

        const deckId =
          parseInt(e.target.value);

        const section =
          e.target.closest('.player-section');

        const pilotWrapper =
          section.querySelector('.pilot-wrapper');

        const pilotSelect =
          section.querySelector('select[name$="PhoenixbornId"]');

        pilotSelect.innerHTML =
          `<option value="">-- Select Pilot --</option>`;

        pilotWrapper.style.display = 'none';

        if (!deckId || deckId < 29 || deckId > 35) {
          return;
        }

        try {

          const res = await fetch(
            `/cardSite/api/decks.php?action=pilots&deckId=${deckId}`
          );

          const data = await res.json();

          if (!data.phoenixbornNames) {
            return;
          }

          const names = data.phoenixbornNames
            .split(',')
            .map(n => n.trim())
            .filter(Boolean);

          names.forEach(name => {

            const opt = document.createElement('option');

            opt.value = name;
            opt.textContent = name;

            pilotSelect.appendChild(opt);

          });

          pilotWrapper.style.display = 'block';

        } catch (err) {

          console.error(err);

        }

      });

  });

}

/* =========================
   FORM SUBMIT
========================= */
document.getElementById('playForm')
  .addEventListener('submit', async (e) => {

    e.preventDefault();

    try {

      /* =========================
         BASIC VALUES
      ========================= */

      const environmentId =
        document.getElementById('environmentId').value;

      const difficultyId =
        document.getElementById('difficultyId').value;

      const result =
        document.querySelector('[name="didYouWin"]').value;

      const win = result === 'Win' ? 1 : 0;

      /* =========================
         PLAYER 1
      ========================= */

      const player1DeckId =
        document.querySelector('[name="player1DeckId"]').value;

      const player1PilotName =
        document.querySelector('[name="player1PhoenixbornId"]').value || null;

      /* =========================
         PLAYER 2
      ========================= */

      const player2DeckId =
        document.querySelector('[name="player2DeckId"]').value;

      const player2PilotName =
        document.querySelector('[name="player2PhoenixbornId"]').value || null;

      /* =========================
         BUILD PAYLOAD
      ========================= */

      const payload = {
        environmentId,
        difficultyId,
        win,
        player1: player1DeckId ? {
          deckId: player1DeckId,
          pilotName: player1PilotName
        } : null,
        player2: player2DeckId ? {
          deckId: player2DeckId,
          pilotName: player2PilotName
        } : null
      };

      console.log("SENDING PAYLOAD:", payload);

      /* =========================
         SEND TO BACKEND
      ========================= */

      const res = await fetch('/cardSite/api/createPlay.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(payload)
      });

      const data = await res.json();

      console.log("SERVER RESPONSE:", data);

      if (!res.ok) {
        throw new Error(data.error || "Insert failed");
      }

      alert("Play saved successfully!");

    } catch (err) {

      console.error("SUBMIT ERROR:", err);
      alert("Error saving play: " + err.message);
    }
    
    window.location.href = "/cardSite/ashes/allPlays.php";

  });

/* =========================
   INIT
========================= */
document.addEventListener('DOMContentLoaded', async () => {

  try {

    await loadEnvironments();
    await loadDifficulties();
    await loadDecks();

    bindDeckEvents();

    console.log("INIT COMPLETE");

  } catch (err) {

    console.error("INIT FAILED:", err);

  }

});

</script>

<?php require_once "../core/footer.php"; ?>
