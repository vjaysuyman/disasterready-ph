<?php
// includes/footer.php
?>
    <?php if (is_logged_in() && is_admin()): ?>
                </div> <!-- Close container -->
            </div> <!-- Close main-content -->
        </div> <!-- Close admin-layout -->
    <?php else: ?>
        </div> <!-- Close container -->
    <?php endif; ?>

    <footer class="footer">
        <p>&copy; <?= date('Y') ?> DisasterReady PH. Educational Project.</p>
        <p><strong>Disclaimer:</strong> Always follow official Philippine government emergency advisories. This system is not an official government service.</p>
    </footer>

    <!-- Scripts (Ibutang diri ang Chart.js sa Phase 5) -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</body>
</html>