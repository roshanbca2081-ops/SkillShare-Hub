/* =============================================
   SkillShare Hub - Dashboard JS
   ============================================= */

document.addEventListener('DOMContentLoaded', function () {

    // Sidebar active state handled by navbar.js

    // Responsive sidebar off-canvas for mobile
    const sidebar = document.querySelector('.dash-sidebar');
    const sidebarToggle = document.querySelector('.sidebar-toggle');
    const sidebarOverlay = document.querySelector('.sidebar-overlay');
    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function () {
            sidebar.classList.toggle('open');
            if (sidebarOverlay) sidebarOverlay.classList.toggle('show');
        });
    }
    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', function () {
            sidebar.classList.remove('open');
            sidebarOverlay.classList.remove('show');
        });
    }

    // Dashboard stats counter
    const counters = document.querySelectorAll('.dash-counter[data-target]');
    if (counters.length) {
        const observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    const el = entry.target;
                    const target = parseInt(el.getAttribute('data-target'), 10);
                    const duration = 1500;
                    const startTime = performance.now();
                    function update(now) {
                        const progress = Math.min((now - startTime) / duration, 1);
                        const eased = 1 - Math.pow(1 - progress, 3);
                        el.textContent = Math.floor(eased * target).toLocaleString();
                        if (progress < 1) requestAnimationFrame(update);
                    }
                    requestAnimationFrame(update);
                    observer.unobserve(el);
                }
            });
        }, { threshold: 0.4 });
        counters.forEach(function (el) { observer.observe(el); });
    }

    // Data table search filter
    const tableSearch = document.getElementById('tableSearch');
    if (tableSearch) {
        tableSearch.addEventListener('input', function () {
            const query = this.value.toLowerCase();
            document.querySelectorAll('.dash-table tbody tr').forEach(function (row) {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(query) ? '' : 'none';
            });
        });
    }

    // Row action dropdowns
    document.querySelectorAll('.action-btn').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            const menu = this.parentElement.querySelector('.action-menu');
            if (menu) menu.classList.toggle('show');
        });
    });
    document.addEventListener('click', function () {
        document.querySelectorAll('.action-menu.show').forEach(function (m) {
            m.classList.remove('show');
        });
    });

    // Mass checkbox select
    const checkAll = document.getElementById('checkAll');
    if (checkAll) {
        checkAll.addEventListener('change', function () {
            document.querySelectorAll('.row-check').forEach(function (cb) {
                cb.checked = checkAll.checked;
            });
        });
    }

    // Chart initialization (demo bars)
    const chart = document.getElementById('dashChart');
    if (chart) {
        const values = [42, 58, 65, 48, 75, 82, 60, 90, 72, 68, 85, 95];
        const bars = chart.querySelectorAll('.chart-bar');
        bars.forEach(function (bar, i) {
            bar.style.height = values[i] + '%';
        });
    }

    // Tabs
    document.querySelectorAll('.dash-tabs .tab-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const wrap = this.closest('.dash-tabs');
            wrap.querySelectorAll('.tab-btn').forEach(function (b) { b.classList.remove('active'); });
            this.classList.add('active');
            const targetId = this.getAttribute('data-tab');
            document.querySelectorAll('.tab-pane').forEach(function (pane) {
                pane.classList.remove('active');
                if (pane.id === targetId) pane.classList.add('active');
            });
        });
    });
});
