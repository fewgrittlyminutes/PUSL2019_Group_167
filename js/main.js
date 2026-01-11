// js/main.js
document.addEventListener('DOMContentLoaded', function() {
    console.log('UtilityPro System loaded');
    
    // Initialize Bootstrap components
    initTooltips();
    initPopovers();
    
    // Layout functionality
    initSidebar();
});

/* ===== LAYOUT FUNCTIONS ===== */
function initSidebar() {
    const sidebarToggle = document.querySelector('[data-bs-toggle="collapse"][data-bs-target="#sidebar"]');
    const sidebar = document.getElementById('sidebar');
    
    if (sidebarToggle && sidebar) {
        // Initial check for mobile
        if (window.innerWidth < 768) {
            sidebar.classList.remove('show');
        }
        
        // Handle collapse events to adjust main content margin
        sidebar.addEventListener('show.bs.collapse', function() {
            const mainContent = document.querySelector('.main-content');
            if (mainContent) mainContent.style.marginLeft = '250px';
        });
        
        sidebar.addEventListener('hide.bs.collapse', function() {
            const mainContent = document.querySelector('.main-content');
            if (mainContent) mainContent.style.marginLeft = '0';
        });
        
        // Handle window resize
        window.addEventListener('resize', function() {
            const mainContent = document.querySelector('.main-content');
            if (window.innerWidth >= 768) {
                sidebar.classList.add('show');
                if (mainContent) mainContent.style.marginLeft = '250px';
            } else {
                sidebar.classList.remove('show');
                if (mainContent) mainContent.style.marginLeft = '0';
            }
        });
    }
}

/* ===== BOOTSTRAP HELPERS ===== */
function initTooltips() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
}

function initPopovers() {
    const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    popoverTriggerList.map(function(popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });
}