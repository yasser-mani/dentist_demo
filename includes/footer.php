<?php $pageScripts = $pageScripts ?? []; ?>
            </div> <!-- .content-wrapper -->
        </main>
    </div> <!-- .app-container -->

    <div id="toastContainer" class="toast-container"></div>
    <div id="modalContainer" class="modal-container"></div>

    <script src="assets/js/app.js"></script>
<?php foreach ($pageScripts as $script): ?>
    <script src="<?= e($script) ?>"></script>
<?php endforeach; ?>
</body>
</html>