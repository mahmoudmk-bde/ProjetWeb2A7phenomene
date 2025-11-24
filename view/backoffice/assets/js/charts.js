// charts.js - Initialisation des graphiques du dashboard
document.addEventListener('DOMContentLoaded', function() {
    if (typeof window.dashboardData !== 'undefined') {
        initMissionsThemeChart();
        initCandidaturesMissionChart();
        initNiveauExpChart();
    }
});

function initMissionsThemeChart() {
    const ctx = document.getElementById('missionsThemeChart').getContext('2d');
    
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: window.dashboardData.themesLabels,
            datasets: [{
                label: 'Nombre de missions',
                data: window.dashboardData.themesValues,
                backgroundColor: 'rgba(255, 74, 87, 0.8)',
                borderColor: 'rgba(255, 74, 87, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        color: '#ffffff'
                    },
                    grid: {
                        color: 'rgba(255, 255, 255, 0.1)'
                    }
                },
                x: {
                    ticks: {
                        color: '#ffffff'
                    },
                    grid: {
                        color: 'rgba(255, 255, 255, 0.1)'
                    }
                }
            }
        }
    });
}

function initCandidaturesMissionChart() {
    const ctx = document.getElementById('candidaturesMissionChart').getContext('2d');
    
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: window.dashboardData.candidLabels,
            datasets: [{
                label: 'Nombre de candidatures',
                data: window.dashboardData.candidValues,
                backgroundColor: 'rgba(0, 123, 255, 0.8)',
                borderColor: 'rgba(0, 123, 255, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        color: '#ffffff'
                    },
                    grid: {
                        color: 'rgba(255, 255, 255, 0.1)'
                    }
                },
                x: {
                    ticks: {
                        color: '#ffffff'
                    },
                    grid: {
                        color: 'rgba(255, 255, 255, 0.1)'
                    }
                }
            }
        }
    });
}

function initNiveauExpChart() {
    const ctx = document.getElementById('niveauExpChart').getContext('2d');
    
    // Mapping des couleurs par niveau d'expérience
    const levelColors = {
        'débutant': '#FF6384',      // Rouge
        'novice': '##4BC0C0',         // Rouge
        'intermédiaire': '#36A2EB',  // Bleu
        'moyen': '#36A2EB',          // Bleu
        'avancé': '#FFCE56',         // Jaune
        'expert': '#4BC0C0',         // Turquoise
        'professionnel': '#9966FF',  // Violet
        'maître': '#FF9F40'          // Orange
    };

    const backgroundColors = window.dashboardData.expLabels.map(label => {
        const normalizedLabel = label.toLowerCase().trim();
        return levelColors[normalizedLabel] || '#C9CBCF';
    });

    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: window.dashboardData.expLabels,
            datasets: [{
                data: window.dashboardData.expValues,
                backgroundColor: backgroundColors,
                borderColor: '#1a1a1a',
                borderWidth: 2,
                hoverOffset: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right',
                    labels: {
                        color: '#ffffff',
                        font: {
                            size: 12
                        },
                        padding: 15
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.raw || 0;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = Math.round((value / total) * 100);
                            return `${label}: ${value} (${percentage}%)`;
                        }
                    },
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleColor: '#ffffff',
                    bodyColor: '#ffffff'
                }
            },
            animation: {
                animateScale: true,
                animateRotate: true
            }
        }
    });
}