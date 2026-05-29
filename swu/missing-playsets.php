<?php include '../includes/header.php'; ?>

<h1 class="page-title">Missing Playsets</h1>

<div id="missing-container"></div>

<button id="exportBtn" class="export-button">
    Export
</button>

<div id="setsContainer"></div>

<!-- Modal -->
<div id="exportModal" class="modal hidden">
    <div class="modal-content">

        <h2>Export Missing Cards</h2>

        <div id="setOptions"></div>

        <div class="modal-footer">
            <button id="cancelExport">Cancel</button>
            <button id="confirmExport">Export</button>
        </div>

    </div>
</div>

<link rel="stylesheet" href="/assets/css/missing-playsets.css">
<script src="/assets/js/missing-playsets.js"></script>

<?php include '../includes/footer.php'; ?>