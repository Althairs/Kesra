<!-- File: komponen/alert-messages.php -->
<?php if (isset($_SESSION['success_message'])): ?>
    <div class="alert alert-success">
        <div class="alert-content">
            <i class="fas fa-check-circle"></i>
            <span><?php echo $_SESSION['success_message']; ?></span>
            <button class="alert-close">&times;</button>
        </div>
    </div>
    <?php unset($_SESSION['success_message']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['error_messages'])): ?>
    <div class="alert alert-error">
        <div class="alert-content">
            <i class="fas fa-exclamation-triangle"></i>
            <div style="flex: 1;">
                <strong>Terjadi kesalahan:</strong>
                <ul>
                    <?php foreach ($_SESSION['error_messages'] as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <button class="alert-close">&times;</button>
        </div>
    </div>
    <?php unset($_SESSION['error_messages']); ?>
<?php endif; ?>