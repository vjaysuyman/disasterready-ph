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
          <script>
        document.addEventListener('DOMContentLoaded', function() {
            const menuToggle = document.getElementById('menu-toggle');
            const sidebar = document.getElementById('admin-sidebar'); // Gigamit ang ID

            if (menuToggle && sidebar) {
                menuToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('active');
                    
                    // I-update ang aria-expanded para sa accessibility
                    const isExpanded = sidebar.classList.contains('active');
                    menuToggle.setAttribute('aria-expanded', isExpanded);
                });
            }

                        // Resident Hamburger Menu Logic
            const residentToggle = document.getElementById('resident-menu-toggle');
            const residentNavLinks = document.getElementById('resident-nav-links');

            if (residentToggle && residentNavLinks) {
                residentToggle.addEventListener('click', function() {
                    residentNavLinks.classList.toggle('active');
                });
            }
        });
    </script>
</body>
</html>