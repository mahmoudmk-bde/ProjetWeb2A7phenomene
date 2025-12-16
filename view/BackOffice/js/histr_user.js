document.addEventListener('DOMContentLoaded', function() {
    console.log('histr_user.js chargé');
    
    // Gestion du menu déroulant de la sidebar
    const dropdownToggle = document.querySelector('.dropdown-toggle');
    const gestionSubmenu = document.getElementById('gestion-submenu');
    
    if (dropdownToggle && gestionSubmenu) {
        console.log('Menu dropdown trouvé');
        dropdownToggle.addEventListener('click', function(e) {
            e.preventDefault();
            const isOpen = gestionSubmenu.classList.contains('show');
            
            if (isOpen) {
                gestionSubmenu.classList.remove('show');
            } else {
                gestionSubmenu.classList.add('show');
            }
            
            // Mettre à jour l'icône
            const icon = dropdownToggle.querySelector('i.fas');
            if (icon) {
                icon.className = isOpen ? 'fas fa-chevron-right' : 'fas fa-chevron-down';
            }
        });
    }
    
    // Filtre par score - Si vous avez des filtres dans le HTML
    const scoreFilter = document.getElementById('scoreFilter');
    if (scoreFilter) {
        console.log('Filtre score trouvé');
        scoreFilter.addEventListener('change', function() {
            filterTableByScore(this.value);
        });
    }
    
    // Filtre par date - Si vous avez des filtres dans le HTML
    const dateFilter = document.getElementById('dateFilter');
    if (dateFilter) {
        console.log('Filtre date trouvé');
        dateFilter.addEventListener('change', function() {
            filterTableByDate(this.value);
        });
    }
    
    // Fonction de filtrage par score
    function filterTableByScore(scoreRange) {
        console.log('Filtrage par score:', scoreRange);
        const rows = document.querySelectorAll('.data-table tbody tr');
        let visibleCount = 0;
        
        rows.forEach(row => {
            // Sauter la ligne d'état vide
            if (row.querySelector('.empty-state')) {
                return;
            }
            
            const scoreCell = row.querySelector('.score-cell .score-badge');
            if (!scoreCell) {
                row.style.display = 'none';
                return;
            }
            
            const scoreText = scoreCell.textContent.trim();
            const scoreMatch = scoreText.match(/(\d+)\s*pts/);
            let showRow = true;
            
            if (scoreMatch) {
                const score = parseInt(scoreMatch[1]);
                
                switch(scoreRange) {
                    case 'high':
                        showRow = score >= 80;
                        break;
                    case 'medium':
                        showRow = score >= 50 && score < 80;
                        break;
                    case 'low':
                        showRow = score < 50;
                        break;
                    default:
                        showRow = true;
                }
            } else {
                showRow = scoreRange === 'all';
            }
            
            row.style.display = showRow ? '' : 'none';
            if (showRow) visibleCount++;
        });
        
        console.log('Lignes visibles après filtrage:', visibleCount);
    }
    
    // Fonction de filtrage par date
    function filterTableByDate(dateRange) {
        console.log('Filtrage par date:', dateRange);
        const rows = document.querySelectorAll('.data-table tbody tr');
        const now = new Date();
        let visibleCount = 0;
        
        rows.forEach(row => {
            // Sauter la ligne d'état vide
            if (row.querySelector('.empty-state')) {
                return;
            }
            
            const dateCell = row.querySelector('.date-cell');
            if (!dateCell) {
                row.style.display = 'none';
                return;
            }
            
            const dateStr = dateCell.textContent.trim();
            if (dateStr === 'Date non disponible') {
                row.style.display = dateRange === 'all' ? '' : 'none';
                return;
            }
            
            const dateParts = dateStr.split(' ')[0].split('/');
            if (dateParts.length !== 3) {
                row.style.display = 'none';
                return;
            }
            
            const rowDate = new Date(dateParts[2], dateParts[1] - 1, dateParts[0]);
            const diffTime = now - rowDate;
            const diffDays = diffTime / (1000 * 60 * 60 * 24);
            
            let showRow = true;
            
            switch(dateRange) {
                case 'today':
                    showRow = diffDays < 1;
                    break;
                case 'week':
                    showRow = diffDays < 7;
                    break;
                case 'month':
                    showRow = diffDays < 30;
                    break;
                default:
                    showRow = true;
            }
            
            row.style.display = showRow ? '' : 'none';
            if (showRow) visibleCount++;
        });
        
        console.log('Lignes visibles après filtrage:', visibleCount);
    }
    
    // Tri des colonnes
    const headers = document.querySelectorAll('.data-table th[data-sort]');
    console.log('En-têtes trouvés:', headers.length);
    
    headers.forEach(header => {
        header.style.cursor = 'pointer';
        header.title = 'Cliquer pour trier';
        
        header.addEventListener('click', function() {
            const column = this.getAttribute('data-sort');
            const isAsc = this.classList.contains('asc');
            console.log('Tri de la colonne:', column, 'asc:', !isAsc);
            
            sortTable(column, isAsc);
            
            // Mettre à jour les classes d'indicateur de tri
            headers.forEach(h => {
                h.classList.remove('asc', 'desc');
                h.style.color = '';
            });
            
            this.classList.toggle('asc', !isAsc);
            this.classList.toggle('desc', isAsc);
            this.style.color = 'var(--primary-color)';
        });
    });
    
    function sortTable(column, reverse) {
        console.log('Tri en cours, colonne:', column, 'reverse:', reverse);
        const tbody = document.querySelector('.data-table tbody');
        const rows = Array.from(tbody.querySelectorAll('tr'));
        
        // Supprimer la ligne d'état vide du tri
        const filteredRows = rows.filter(row => !row.querySelector('.empty-state'));
        
        if (filteredRows.length === 0) {
            console.log('Aucune donnée à trier');
            return;
        }
        
        filteredRows.sort((a, b) => {
            const columnIndex = getColumnIndex(column);
            let aValue = a.querySelector(`td:nth-child(${columnIndex})`).textContent.trim();
            let bValue = b.querySelector(`td:nth-child(${columnIndex})`).textContent.trim();
            
            console.log('Valeurs à comparer:', aValue, 'vs', bValue);
            
            // Conversion pour les scores
            if (column === 'score') {
                const aMatch = aValue.match(/(\d+)\s*pts/);
                const bMatch = bValue.match(/(\d+)\s*pts/);
                
                aValue = aMatch ? parseInt(aMatch[1]) : -1;
                bValue = bMatch ? parseInt(bMatch[1]) : -1;
                
                console.log('Scores extraits:', aValue, bValue);
            } 
            // Conversion pour les dates
            else if (column === 'date') {
                if (aValue.includes('/') && !aValue.includes('Date non disponible')) {
                    const aParts = aValue.split(' ')[0].split('/');
                    aValue = new Date(aParts[2], aParts[1] - 1, aParts[0]).getTime();
                } else {
                    aValue = 0;
                }
                
                if (bValue.includes('/') && !bValue.includes('Date non disponible')) {
                    const bParts = bValue.split(' ')[0].split('/');
                    bValue = new Date(bParts[2], bParts[1] - 1, bParts[0]).getTime();
                } else {
                    bValue = 0;
                }
                
                console.log('Dates converties:', aValue, bValue);
            }
            // Tri pour les utilisateurs (par nom)
            else if (column === 'user') {
                // Extraire le nom sans l'ID
                const aName = aValue.split('#')[0].trim();
                const bName = bValue.split('#')[0].trim();
                aValue = aName.toLowerCase();
                bValue = bName.toLowerCase();
                
                console.log('Noms extraits:', aValue, bValue);
            }
            
            // Comparaison
            if (aValue < bValue) return reverse ? 1 : -1;
            if (aValue > bValue) return reverse ? -1 : 1;
            return 0;
        });
        
        // Réorganiser les lignes
        tbody.innerHTML = '';
        filteredRows.forEach(row => tbody.appendChild(row));
        
        // Réinsérer la ligne d'état vide s'il n'y a pas de données
        if (filteredRows.length === 0) {
            const emptyRow = document.createElement('tr');
            emptyRow.innerHTML = `
                <td colspan="3">
                    <div class="empty-state">
                        <i class="fas fa-user-slash"></i>
                        <h3>Aucun utilisateur trouvé</h3>
                        <p>Aucun utilisateur n'a encore passé le quiz de cet article.</p>
                    </div>
                </td>
            `;
            tbody.appendChild(emptyRow);
        }
        
        console.log('Tri terminé, lignes réorganisées:', filteredRows.length);
    }
    
    function getColumnIndex(column) {
        const headers = document.querySelectorAll('.data-table th[data-sort]');
        for (let i = 0; i < headers.length; i++) {
            if (headers[i].getAttribute('data-sort') === column) {
                return i + 1; // +1 car nth-child commence à 1
            }
        }
        return 1;
    }
    
    // Export CSV
    const exportBtn = document.getElementById('exportBtn');
    if (exportBtn) {
        console.log('Bouton export trouvé');
        exportBtn.addEventListener('click', function() {
            exportToCSV();
        });
    }
    
    function exportToCSV() {
        console.log('Export CSV en cours...');
        const rows = document.querySelectorAll('.data-table tr:not(:has(.empty-state))');
        
        if (rows.length <= 1) {
            alert('Aucune donnée à exporter.');
            return;
        }
        
        let csv = [];
        
        // En-têtes
        const headerRow = [];
        document.querySelectorAll('.data-table th').forEach(th => {
            headerRow.push(`"${th.textContent.replace(/"/g, '""')}"`);
        });
        csv.push(headerRow.join(','));
        
        // Données
        rows.forEach(row => {
            if (row.querySelector('th')) return; // Sauter l'en-tête
            
            const rowData = [];
            const cols = row.querySelectorAll('td');
            
            cols.forEach(col => {
                rowData.push(`"${col.textContent.replace(/"/g, '""')}"`);
            });
            
            csv.push(rowData.join(','));
        });
        
        const csvContent = csv.join('\n');
        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        
        const articleId = document.getElementById('articleId')?.value || 'unknown';
        const date = new Date().toISOString().split('T')[0];
        
        a.href = url;
        a.download = `utilisateurs-article-${articleId}-${date}.csv`;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        window.URL.revokeObjectURL(url);
        
        console.log('Export CSV terminé, fichier téléchargé');
    }
    
    // Menu mobile
    const sidebarToggle = document.querySelector('.sidebar-toggle');
    const sidebar = document.getElementById('sidebar');
    
    if (sidebarToggle && sidebar) {
        console.log('Bouton sidebar mobile trouvé');
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('active');
            console.log('Sidebar toggled, active:', sidebar.classList.contains('active'));
        });
    }
    
    // Ajouter un bouton export si nécessaire
    const sectionHeader = document.querySelector('.section-header');
    if (sectionHeader && !document.getElementById('exportBtn')) {
        const exportButton = document.createElement('button');
        exportButton.id = 'exportBtn';
        exportButton.className = 'btn btn-primary';
        exportButton.innerHTML = '<i class="fas fa-download"></i> Exporter CSV';
        exportButton.style.cssText = `
            background: var(--primary-color);
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            color: white;
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 8px;
        `;
        
        exportButton.addEventListener('mouseover', function() {
            this.style.opacity = '0.9';
            this.style.transform = 'translateY(-2px)';
        });
        
        exportButton.addEventListener('mouseout', function() {
            this.style.opacity = '1';
            this.style.transform = 'translateY(0)';
        });
        
        sectionHeader.appendChild(exportButton);
        console.log('Bouton export ajouté dynamiquement');
    }
});