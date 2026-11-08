
<script>
(function () {
    function setupMobileMenu() {
        const button = document.getElementById('mobileMenuToggle');
        const sidebar = document.getElementById('adminSidebar');
        const overlay = document.getElementById('sidebarOverlay');

        if (!button || !sidebar || !overlay) {
            console.error('Missing mobile menu element.');
            return;
        }

        // Prevent duplicate click handlers
        if (button.dataset.menuInitialized === 'true') {
            return;
        }
        button.dataset.menuInitialized = 'true';

        function setMenuOpen(open) {
            sidebar.classList.toggle('is-open', open);
            overlay.classList.toggle('is-open', open);
            button.setAttribute('aria-expanded', String(open));
        }

        button.addEventListener('click', function (event) {
            event.preventDefault();

            const isOpen = sidebar.classList.contains('is-open');
            setMenuOpen(!isOpen);
        });

        overlay.addEventListener('click', function () {
            setMenuOpen(false);
        });

        sidebar.querySelectorAll('a').forEach(function (link) {
            link.addEventListener('click', function () {
                setMenuOpen(false);
            });
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', setupMobileMenu);
    } else {
        setupMobileMenu();
    }
})();
</script>

</body>
</html>
