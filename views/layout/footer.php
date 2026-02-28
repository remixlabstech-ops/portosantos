        </main><!-- /.content -->
    </div><!-- /.main-wrapper -->
</div><!-- /#app -->

<!-- Notification container -->
<div id="notification-container" aria-live="polite"></div>

<!-- Confirm dialog -->
<div id="confirm-dialog" class="modal-backdrop" style="display:none">
    <div class="modal" style="max-width:400px">
        <div class="modal-header">
            <h3><i class="fas fa-question-circle"></i> Confirmação</h3>
        </div>
        <div class="modal-body">
            <p id="confirm-message"></p>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" id="confirm-cancel">Cancelar</button>
            <button class="btn btn-danger" id="confirm-ok">Confirmar</button>
        </div>
    </div>
</div>

<script src="assets/js/app.js"></script>
<?php if (isset($pageScript)): ?>
<script src="assets/js/<?= htmlspecialchars($pageScript) ?>"></script>
<?php endif; ?>
</body>
</html>
