
// Force filter button colors in light mode
function fixFilterButtonColors() {
    const theme = document.documentElement.getAttribute('data-theme');
    const filterTabs = document.querySelectorAll('.filter-tab');
    
    filterTabs.forEach(function(tab) {
        if (theme === 'light') {
            if (tab.classList.contains('active')) {
                tab.style.backgroundColor = '#e63946';
                tab.style.color = '#ffffff';
                tab.style.borderColor = '#e63946';
            } else {
                tab.style.backgroundColor = '#ffffff';
                tab.style.color = '#1f2937';
                tab.style.borderColor = '#d1d5db';
            }
        } else {
            tab.style.backgroundColor = '';
            tab.style.color = '';
            tab.style.borderColor = '';
        }
    });
}

// Run on page load
document.addEventListener('DOMContentLoaded', fixFilterButtonColors);

// Run when theme changes
const originalToggleTheme = window.toggleTheme;
window.toggleTheme = function() {
    if (originalToggleTheme) originalToggleTheme();
    setTimeout(fixFilterButtonColors, 100);
};

// Re-run when filter tabs are clicked
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('filter-tab')) {
        setTimeout(fixFilterButtonColors, 50);
    }
});
