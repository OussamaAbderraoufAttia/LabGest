<?php
require_once("commonViews.php");

class publicationsView {
    
    public function entetePage() {
        ?>
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Publications - LRE</title>
            <link rel="stylesheet" href="View/css/commonStyles.css">
            <link rel="stylesheet" href="View/css/publicationsStyle.css">
            <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />
        </head>
        <?php
    }
    
    public function afficherBase($publications, $years, $types, $filters) {
        $common = new commonViews();
        ?>
        <!DOCTYPE html>
        <html lang="fr">
            <?php $this->entetePage(); ?>
            <body>
                <?php $common->navBar(); ?>
                
                <div class="publications-container">
                    <h1 class="page-title">Base Documentaire et Publications</h1>
                    
                    <!-- Search and Filters -->
                    <div class="search-section">
                        <div class="search-bar">
                            <i class="fa-solid fa-search"></i>
                            <input type="text" id="searchInput" placeholder="Rechercher par titre ou résumé..." value="<?= htmlspecialchars($filters['search'] ?? '') ?>">
                        </div>
                        
                        <div class="filters-row">
                            <select id="filterYear" class="filter-select">
                                <option value="">Toutes les années</option>
                                <?php foreach ($years as $year): ?>
                                    <option value="<?= $year ?>" <?= (isset($filters['year']) && $filters['year'] == $year) ? 'selected' : '' ?>>
                                        <?= $year ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            
                            <select id="filterType" class="filter-select">
                                <option value="">Tous les types</option>
                                <?php foreach ($types as $type): ?>
                                    <option value="<?= $type ?>" <?= (isset($filters['type']) && $filters['type'] === $type) ? 'selected' : '' ?>>
                                        <?= ucfirst($type) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            
                            <button class="btn-primary" onclick="applyFilters()">
                                <i class="fa-solid fa-filter"></i> Filtrer
                            </button>
                            
                            <button class="btn-secondary" onclick="resetFilters()">
                                <i class="fa-solid fa-rotate-left"></i> Réinitialiser
                            </button>
                        </div>
                    </div>
                    
                    <!-- Publications List -->
                    <div class="publications-list">
                        <?php if (empty($publications)): ?>
                            <p class="no-results">Aucune publication trouvée.</p>
                        <?php else: ?>
                            <?php foreach ($publications as $pub): ?>
                                <div class="publication-item fade-in-up">
                                    <div class="pub-header">
                                        <h3><?= htmlspecialchars($pub['titre']) ?></h3>
                                        <span class="pub-type"><?= ucfirst($pub['type']) ?></span>
                                    </div>
                                    
                                    <div class="pub-meta">
                                        <span><i class="fa-solid fa-calendar"></i> <?= date('Y', strtotime($pub['date_publication'])) ?></span>
                                        <?php if ($pub['conference']): ?>
                                            <span><i class="fa-solid fa-building"></i> <?= htmlspecialchars($pub['conference']) ?></span>
                                        <?php endif; ?>
                                        <?php if ($pub['doi']): ?>
                                            <span><i class="fa-solid fa-link"></i> DOI: <?= htmlspecialchars($pub['doi']) ?></span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <?php if ($pub['resume']): ?>
                                        <p class="pub-resume"><?= htmlspecialchars(substr($pub['resume'], 0, 200)) ?>...</p>
                                    <?php endif; ?>
                                    
                                    <?php if ($pub['fichier_pdf']): ?>
                                        <a href="<?= htmlspecialchars($pub['fichier_pdf']) ?>" class="btn-download" target="_blank">
                                            <i class="fa-solid fa-file-pdf"></i> Télécharger PDF
                                        </a>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
                
                <script>
                    function applyFilters() {
                        const search = document.getElementById('searchInput').value;
                        const year = document.getElementById('filterYear').value;
                        const type = document.getElementById('filterType').value;
                        
                        let url = 'index.php?router=publications';
                        const params = [];
                        
                        if (search) params.push('search=' + encodeURIComponent(search));
                        if (year) params.push('year=' + encodeURIComponent(year));
                        if (type) params.push('type=' + encodeURIComponent(type));
                        
                        if (params.length > 0) {
                            url += '&' + params.join('&');
                        }
                        
                        window.location.href = url;
                    }
                    
                    function resetFilters() {
                        window.location.href = 'index.php?router=publications';
                    }
                    
                    // Allow Enter key to trigger search
                    document.getElementById('searchInput').addEventListener('keypress', function(e) {
                        if (e.key === 'Enter') {
                            applyFilters();
                        }
                    });
                </script>
                
                <?php $common->footer(); ?>
            </body>
        </html>
        <?php
    }
}
?>
