<?php
require_once("commonViews.php");

class projectsView {
    
    public function entetePage() {
        ?>
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Projets de Recherche - LRE</title>
            <link rel="stylesheet" href="View/css/commonStyles.css">
            <link rel="stylesheet" href="View/css/projectsStyle.css">
            <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />
            <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        </head>
        <?php
    }
    
    public function afficherCatalogue($projects, $themes, $currentFilters) {
        $common = new commonViews();
        ?>
        <!DOCTYPE html>
        <html lang="fr">
            <?php $this->entetePage(); ?>
            <body>
                <?php $common->navBar(); ?>
                
                <div class="projects-container">
                    <h1 class="page-title">Catalogue des Projets de Recherche</h1>
                    
                    <!-- Filters -->
                    <div class="filters-section">
                        <div class="filter-group">
                            <label>Thématique:</label>
                            <select id="filterTheme" class="filter-select">
                                <option value="">Toutes les thématiques</option>
                                <?php foreach ($themes as $theme): ?>
                                    <option value="<?= $theme ?>" <?= (isset($currentFilters['thematique']) && $currentFilters['thematique'] === $theme) ? 'selected' : '' ?>>
                                        <?= $theme ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="filter-group">
                            <label>Statut:</label>
                            <select id="filterStatus" class="filter-select">
                                <option value="">Tous les statuts</option>
                                <option value="en_cours" <?= (isset($currentFilters['statut']) && $currentFilters['statut'] === 'en_cours') ? 'selected' : '' ?>>En cours</option>
                                <option value="termine" <?= (isset($currentFilters['statut']) && $currentFilters['statut'] === 'termine') ? 'selected' : '' ?>>Terminé</option>
                                <option value="soumis" <?= (isset($currentFilters['statut']) && $currentFilters['statut'] === 'soumis') ? 'selected' : '' ?>>Soumis</option>
                            </select>
                        </div>
                        
                        <button class="btn-primary" onclick="filterProjects()">
                            <i class="fa-solid fa-filter"></i> Filtrer
                        </button>
                        
                        <button class="btn-secondary" onclick="resetFilters()">
                            <i class="fa-solid fa-rotate-left"></i> Réinitialiser
                        </button>
                    </div>
                    
                    <!-- Projects Grid -->
                    <div class="projects-grid">
                        <?php if (empty($projects)): ?>
                            <p class="no-results">Aucun projet trouvé.</p>
                        <?php else: ?>
                            <?php foreach ($projects as $project): ?>
                                <div class="project-card">
                                    <div class="project-header">
                                        <span class="project-theme"><?= $project['thematique'] ?></span>
                                        <span class="project-status status-<?= $project['statut'] ?>">
                                            <?= ucfirst(str_replace('_', ' ', $project['statut'])) ?>
                                        </span>
                                    </div>
                                    
                                    <h3><?= htmlspecialchars($project['titre']) ?></h3>
                                    
                                    <p class="project-description">
                                        <?= htmlspecialchars(substr($project['description'], 0, 150)) ?>...
                                    </p>
                                    
                                    <div class="project-meta">
                                        <p><i class="fa-solid fa-user"></i> 
                                            <?= htmlspecialchars($project['responsable_prenom'] . ' ' . $project['responsable_nom']) ?>
                                        </p>
                                        <p><i class="fa-solid fa-coins"></i> <?= htmlspecialchars($project['type_financement']) ?></p>
                                    </div>
                                    
                                    <a href="index.php?router=projet-details&id=<?= $project['id_project'] ?>" class="btn-primary">
                                        En savoir plus <i class="fa-solid fa-arrow-right"></i>
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
                
                <script>
                    function filterProjects() {
                        const theme = $('#filterTheme').val();
                        const status = $('#filterStatus').val();
                        
                        let url = 'index.php?router=projets';
                        const params = [];
                        
                        if (theme) params.push('thematique=' + encodeURIComponent(theme));
                        if (status) params.push('statut=' + encodeURIComponent(status));
                        
                        if (params.length > 0) {
                            url += '&' + params.join('&');
                        }
                        
                        window.location.href = url;
                    }
                    
                    function resetFilters() {
                        window.location.href = 'index.php?router=projets';
                    }
                </script>
                
                <?php $common->footer(); ?>
            </body>
        </html>
        <?php
    }
    
    public function afficherDetails($project, $members, $publications, $partners) {
        $common = new commonViews();
        ?>
        <!DOCTYPE html>
        <html lang="fr">
            <?php $this->entetePage(); ?>
            <body>
                <?php $common->navBar(); ?>
                
                <div class="project-detail-container">
                    <a href="index.php?router=projets" class="back-link">
                        <i class="fa-solid fa-arrow-left"></i> Retour au catalogue
                    </a>
                    
                    <div class="project-detail-header">
                        <h1><?= htmlspecialchars($project['titre']) ?></h1>
                        <div class="project-badges">
                            <span class="badge theme-badge"><?= $project['thematique'] ?></span>
                            <span class="badge status-<?= $project['statut'] ?>">
                                <?= ucfirst(str_replace('_', ' ', $project['statut'])) ?>
                            </span>
                        </div>
                    </div>
                    
                    <div class="project-detail-grid">
                        <!-- Main Info -->
                        <div class="project-main">
                            <section class="detail-section">
                                <h2><i class="fa-solid fa-info-circle"></i> Description</h2>
                                <p><?= nl2br(htmlspecialchars($project['description'])) ?></p>
                            </section>
                            
                            <section class="detail-section">
                                <h2><i class="fa-solid fa-calendar"></i> Période</h2>
                                <p>
                                    <strong>Début:</strong> <?= date('d/m/Y', strtotime($project['date_debut'])) ?><br>
                                    <?php if ($project['date_fin']): ?>
                                        <strong>Fin:</strong> <?= date('d/m/Y', strtotime($project['date_fin'])) ?>
                                    <?php endif; ?>
                                </p>
                            </section>
                            
                            <!-- Publications -->
                            <?php if (!empty($publications)): ?>
                                <section class="detail-section">
                                    <h2><i class="fa-solid fa-book"></i> Publications Associées</h2>
                                    <div class="publications-list">
                                        <?php foreach ($publications as $pub): ?>
                                            <div class="pub-item">
                                                <h4><?= htmlspecialchars($pub['titre']) ?></h4>
                                                <p><?= $pub['type'] ?> - <?= date('Y', strtotime($pub['date_publication'])) ?></p>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </section>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Sidebar -->
                        <div class="project-sidebar">
                            <div class="sidebar-card">
                                <h3><i class="fa-solid fa-user-tie"></i> Responsable</h3>
                                <p><strong><?= htmlspecialchars($project['responsable_prenom'] . ' ' . $project['responsable_nom']) ?></strong></p>
                                <p><i class="fa-solid fa-envelope"></i> <?= htmlspecialchars($project['responsable_email']) ?></p>
                            </div>
                            
                            <div class="sidebar-card">
                                <h3><i class="fa-solid fa-coins"></i> Financement</h3>
                                <p><?= htmlspecialchars($project['type_financement']) ?></p>
                            </div>
                            
                            <!-- Members -->
                            <?php if (!empty($members)): ?>
                                <div class="sidebar-card">
                                    <h3><i class="fa-solid fa-users"></i> Membres de l'équipe</h3>
                                    <ul class="members-list">
                                        <?php foreach ($members as $member): ?>
                                            <li>
                                                <?= htmlspecialchars($member['prenom'] . ' ' . $member['nom']) ?>
                                                <span class="role-tag"><?= htmlspecialchars($member['role_projet']) ?></span>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Partners -->
                            <?php if (!empty($partners)): ?>
                                <div class="sidebar-card">
                                    <h3><i class="fa-solid fa-handshake"></i> Partenaires</h3>
                                    <ul>
                                        <?php foreach ($partners as $partner): ?>
                                            <li><?= htmlspecialchars($partner['nom']) ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <?php $common->footer(); ?>
            </body>
        </html>
        <?php
    }
    
    public function afficherMesProjets($projects) {
        // This is shown in user profile, basic implementation
        echo "Mes Projets - voir profil utilisateur";
    }
}
?>
