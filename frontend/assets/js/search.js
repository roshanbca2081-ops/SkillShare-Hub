/* =============================================
   SkillShare Hub - Search Page JS
   ============================================= */

document.addEventListener('DOMContentLoaded', function () {

    // Filter / sort controls
    const filterSelects = document.querySelectorAll('.filter-select');
    filterSelects.forEach(function (select) {
        select.addEventListener('change', function () {
            // Auto-submit containing form
            const form = this.closest('form');
            if (form) form.submit();
        });
    });

    // Live search suggestions (demo)
    const searchInput = document.getElementById('liveSearch');
    const suggestions = document.getElementById('searchSuggestions');
    if (searchInput && suggestions) {
        const demoData = [
            'Web Development Courses', 'Data Science Mentors', 'Research Papers',
            'UI/UX Design', 'Medical Research', 'Business Analytics',
            'Machine Learning', 'Career Counseling'
        ];
        searchInput.addEventListener('input', function () {
            const val = this.value.trim().toLowerCase();
            suggestions.innerHTML = '';
            if (val.length === 0) {
                suggestions.classList.remove('show');
                return;
            }
            const matches = demoData.filter(function (item) {
                return item.toLowerCase().includes(val);
            });
            if (matches.length) {
                matches.forEach(function (item) {
                    const li = document.createElement('li');
                    li.innerHTML = '<i class="fa-solid fa-magnifying-glass"></i> ' + item;
                    li.addEventListener('click', function () {
                        searchInput.value = item;
                        suggestions.classList.remove('show');
                    });
                    suggestions.appendChild(li);
                });
                suggestions.classList.add('show');
            } else {
                suggestions.classList.remove('show');
            }
        });
        document.addEventListener('click', function (e) {
            if (!suggestions.contains(e.target) && e.target !== searchInput) {
                suggestions.classList.remove('show');
            }
        });
    }

    // Result count display
    const resultsFound = document.querySelector('.results-count span');
    const resultItems = document.querySelectorAll('.result-item');
    if (resultsFound && resultItems.length) {
        resultsFound.textContent = resultItems.length;
    }
});
