// ============================================
// Dashboard JavaScript
// ============================================

document.addEventListener('DOMContentLoaded', function() {
    // Initialize charts if Chart.js is available
    if (typeof Chart !== 'undefined') {
        initCharts();
    }
    
    // Refresh stats
    refreshStats();
});

// ============================================
// Charts
// ============================================

function initCharts() {
    // Enrollment chart
    const enrollmentCtx = document.getElementById('enrollmentChart');
    if (enrollmentCtx) {
        new Chart(enrollmentCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'Enrollments',
                    data: [12, 19, 15, 25, 22, 30],
                    borderColor: '#667eea',
                    backgroundColor: 'rgba(102, 126, 234, 0.1)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }
    
    // Revenue chart
    const revenueCtx = document.getElementById('revenueChart');
    if (revenueCtx) {
        new Chart(revenueCtx, {
            type: 'bar',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'Revenue',
                    data: [500, 800, 600, 1200, 900, 1500],
                    backgroundColor: 'rgba(102, 126, 234, 0.8)',
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }
}

// ============================================
// Refresh Stats
// ============================================

function refreshStats() {
    const statsContainer = document.getElementById('statsContainer');
    if (!statsContainer) return;
    
    ajaxRequest('api/stats.php')
        .then(data => {
            document.getElementById('totalStudents').textContent = data.totalStudents;
            document.getElementById('totalMentors').textContent = data.totalMentors;
            document.getElementById('totalCourses').textContent = data.totalCourses;
            document.getElementById('totalRevenue').textContent = '$' + data.totalRevenue;
        })
        .catch(() => {
            console.error('Failed to refresh stats');
        });
}

// ============================================
// Activity Feed
// ============================================

function loadActivityFeed() {
    const feedContainer = document.getElementById('activityFeed');
    if (!feedContainer) return;
    
    ajaxRequest('api/activity.php')
        .then(data => {
            feedContainer.innerHTML = data.html;
        })
        .catch(() => {
            feedContainer.innerHTML = '<p class="text-muted">Failed to load activity</p>';
        });
}

// Auto-refresh activity feed every 30 seconds
setInterval(loadActivityFeed, 30000);