<?php
require_once("commonViews.php");

class teamsView {
    
    public function entetePage() {
        ?>
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Équipes - LRE</title>
            <link rel="stylesheet" href="View/css/commonStyles.css">
            <link rel="stylesheet" href="View/css/teamsStyle.css">
            <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />
            <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        </head>
        <?php
    }
    
    public function afficherEquipes($teams, $director) {
        $common = new commonViews();
        
        // Get all members for filtering
        require_once("Model/teamModel.php");
        $teamModel = new teamModel();
        
        ?>
        <!DOCTYPE html>
        <html lang="fr">
            <?php $this->entetePage(); ?>
            <body>
                <?php $common->navBar(); ?>
                
                <div class="teams-container">
                    <!-- 1. PRESENTATION DU LABORATOIRE -->
                    <section class="lab-presentation">
                        <h1 class="page-title">Laboratoire de Recherche ESI (LRE)</h1>
                        
                        <div class="presentation-content">
                            <h2>À Propos du Laboratoire</h2>
                            <p>
                                Le Laboratoire de Recherche ESI (LRE) est un pôle d'excellence dédié à la recherche 
                                en informatique avancée. Créé pour promouvoir l'innovation et la recherche scientifique 
                                de haut niveau, le LRE regroupe plus de 15 chercheurs permanents et accueille une 
                                trentaine de doctorants chaque année.
                            </p>
                            
                            <h3>Thèmes de Recherche</h3>
                            <div class="research-themes">
                                <div class="theme-card">
                                    <i class="fa-solid fa-brain"></i>
                                    <h4>Intelligence Artificielle</h4>
                                    <p>Machine Learning, Deep Learning, Computer Vision, NLP</p>
                                </div>
                                <div class="theme-card">
                                    <i class="fa-solid fa-shield-halved"></i>
                                    <h4>Sécurité Informatique</h4>
                                    <p>Cybersécurité, Cryptographie, Sécurité IoT, Blockchain</p>
                                </div>
                                <div class="theme-card">
                                    <i class="fa-solid fa-cloud"></i>
                                    <h4>Cloud Computing</h4>
                                    <p>Architectures cloud, Virtualisation, Conteneurisation</p>
                                </div>
                                <div class="theme-card">
                                    <i class="fa-solid fa-code"></i>
                                    <h4>Ingénierie Web</h4>
                                    <p>Services Web, Big Data, Systèmes d'Information</p>
                                </div>
                            </div>
                        </div>
                    </section>
                    
                    <!-- 2. ORGANIGRAMME -->
                    <?php if ($director): ?>
                        <section class="organigramme-section">
                            <h2><i class="fa-solid fa-sitemap"></i> Organigramme du Laboratoire</h2>
                            
                            <div class="director-card">
                                <div class="director-photo">
                                    <?php if (!empty($director['photo']) && file_exists($director['photo'])): ?>
                                        <img src="<?= $director['photo'] ?>" alt="<?= htmlspecialchars($director['prenom'] . ' ' . $director['nom']) ?>">
                                    <?php else: ?>
                                        <img src="View/assets/default_avatar.png" alt="<?= htmlspecialchars($director['prenom'] . ' ' . $director['nom']) ?>">
                                    <?php endif; ?>
                                </div>
                                <div class="director-info">
                                    <h3><?= htmlspecialchars($director['prenom'] . ' ' . $director['nom']) ?></h3>
                                    <p class="position"><i class="fa-solid fa-user-tie"></i> Directeur du Laboratoire</p>
                                    <p class="grade"><i class="fa-solid fa-graduation-cap"></i> <?= htmlspecialchars($director['grade']) ?></p>
                                    <p class="email"><i class="fa-solid fa-envelope"></i> <?= htmlspecialchars($director['email']) ?></p>
                                    <?php if ($director['domaine_recherche']): ?>
                                        <p class="domain"><i class="fa-solid fa-flask"></i> <?= htmlspecialchars($director['domaine_recherche']) ?></p>
                                    <?php endif; ?>
                                    <div class="director-links">
                                        <a href="index.php?router=membre-profil&id=<?= $director['id_user'] ?>" class="btn-sm btn-primary">
                                            <i class="fa-solid fa-user"></i> Biographie
                                        </a>
                                        <a href="index.php?router=membre-publications&id=<?= $director['id_user'] ?>" class="btn-sm btn-secondary">
                                            <i class="fa-solid fa-book"></i> Publications
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </section>
                    <?php endif; ?>
                    
                    <!-- 3. EQUIPES DE RECHERCHE -->
                    <section class="teams-section">
                        <h2><i class="fa-solid fa-users"></i> Équipes de Recherche</h2>
                        
                        <?php if (empty($teams)): ?>
                            <p class="no-results">Aucune équipe disponible.</p>
                        <?php else: ?>
                            <?php foreach ($teams as $team): ?>
                                <?php 
                                $members = $teamModel->getTeamMembers($team['id_team']);
                                $teamPublications = $teamModel->getTeamPublications($team['id_team']);
                                ?>
                                
                                <div class="team-block">
                                    <div class="team-header">
                                        <div>
                                            <h3><?= htmlspecialchars($team['nom']) ?></h3>
                                            <?php if ($team['description']): ?>
                                                <p class="team-description"><?= htmlspecialchars($team['description']) ?></p>
                                            <?php endif; ?>
                                        </div>
                                        <a href="index.php?router=equipe-publications&id=<?= $team['id_team'] ?>" class="btn-primary">
                                            <i class="fa-solid fa-book-open"></i> Publications de l'équipe (<?= count($teamPublications) ?>)
                                        </a>
                                    </div>
                                    
                                    <!-- Members Table -->
                                    <table class="members-table">
                                        <thead>
                                            <tr>
                                                <th>Photo</th>
                                                <th>Nom Complet</th>
                                                <th>Grade</th>
                                                <th>Poste/Rôle</th>
                                                <th>Spécialité</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (empty($members)): ?>
                                                <tr><td colspan="6" class="no-members">Aucun membre dans cette équipe</td></tr>
                                            <?php else: ?>
                                                <?php foreach ($members as $member): ?>
                                                    <tr>
                                                        <td>
                                                            <div class="member-photo-sm">
                                                                <?php if (!empty($member['photo']) && file_exists($member['photo'])): ?>
                                                                    <img src="<?= $member['photo'] ?>" alt="<?= htmlspecialchars($member['prenom'] . ' ' . $member['nom']) ?>">
                                                                <?php else: ?>
                                                                    <img src="View/assets/default_avatar.png" alt="<?= htmlspecialchars($member['prenom'] . ' ' . $member['nom']) ?>">
                                                                <?php endif; ?>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <strong><?= htmlspecialchars($member['prenom'] . ' ' . $member['nom']) ?></strong>
                                                        </td>
                                                        <td>
                                                            <span class="grade-badge"><?= htmlspecialchars($member['grade']) ?></span>
                                                        </td>
                                                        <td>
                                                            <?php if ($member['role_dans_equipe']): ?>
                                                                <span class="role-badge"><?= htmlspecialchars($member['role_dans_equipe']) ?></span>
                                                            <?php else: ?>
                                                                <span class="role-badge">Membre</span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td><?= htmlspecialchars($member['specialite'] ?? '-') ?></td>
                                                        <td class="actions-cell">
                                                            <a href="index.php?router=membre-profil&id=<?= $member['id_user'] ?>" class="btn-sm btn-primary" title="Biographie">
                                                                <i class="fa-solid fa-user"></i>
                                                            </a>
                                                            <a href="index.php?router=membre-publications&id=<?= $member['id_user'] ?>" class="btn-sm btn-secondary" title="Publications">
                                                                <i class="fa-solid fa-book"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </section>
                    
                    <!-- 4. TOUS LES MEMBRES (avec filtrage) -->
                    <section class="all-members-section">
                        <h2><i class="fa-solid fa-user-group"></i> Tous les Membres du Laboratoire</h2>
                        
                        <!-- Filters -->
                        <div class="filters-bar">
                            <select id="filterGrade" class="filter-select">
                                <option value="">Tous les grades</option>
                                <option value="Professeur">Professeur</option>
                                <option value="MCA">MCA</option>
                                <option value="MCB">MCB</option>
                                <option value="MAA">MAA</option>
                                <option value="MAB">MAB</option>
                                <option value="Doctorant">Doctorant</option>
                            </select>
                            
                            <select id="filterRole" class="filter-select">
                                <option value="">Tous les rôles</option>
                                <option value="admin">Admin</option>
                                <option value="enseignant-chercheur">Enseignant-Chercheur</option>
                                <option value="doctorant">Doctorant</option>
                            </select>
                            
                            <input type="text" id="searchMember" class="search-input" placeholder="Rechercher par nom ou spécialité...">
                            
                            <button onclick="applyMemberFilters()" class="btn-primary">
                                <i class="fa-solid fa-filter"></i> Filtrer
                            </button>
                            <button onclick="resetMemberFilters()" class="btn-secondary">
                                <i class="fa-solid fa-rotate-left"></i> Réinitialiser
                            </button>
                        </div>
                        
                        <!-- Members Grid -->
                        <div class="members-grid" id="membersGrid">
                            <!-- Will be populated by JavaScript or PHP -->
                        </div>
                    </section>
                </div>
                
                <script>
                    function applyMemberFilters() {
                        const grade = $('#filterGrade').val();
                        const role = $('#filterRole').val();
                        const search = $('#searchMember').val();
                        
                        let url = 'index.php?router=equipes';
                        const params = [];
                        
                        if (grade) params.push('grade=' + encodeURIComponent(grade));
                        if (role) params.push('role=' + encodeURIComponent(role));
                        if (search) params.push('search=' + encodeURIComponent(search));
                        
                        if (params.length > 0) {
                            url += '&' + params.join('&');
                        }
                        
                        window.location.href = url;
                    }
                    
                    function resetMemberFilters() {
                        window.location.href = 'index.php?router=equipes';
                    }
                    
                    // Allow Enter key in search
                    $('#searchMember').on('keypress', function(e) {
                        if (e.which === 13) {
                            applyMemberFilters();
                        }
                    });
                </script>
                
                <?php $common->footer(); ?>
            </body>
        </html>
        <?php
    }
    
    // Individual member profile page
    public function afficherMembreProfil($member, $publications, $projects) {
        $common = new commonViews();
        ?>
        <!DOCTYPE html>
        <html lang="fr">
            <?php $this->entetePage(); ?>
            <body>
                <?php $common->navBar(); ?>
                
                <div class="member-profile-container">
                    <a href="index.php?router=equipes" class="back-link">
                        <i class="fa-solid fa-arrow-left"></i> Retour aux équipes
                    </a>
                    
                    <div class="profile-header">
                        <div class="profile-photo-large">
                            <?php if (!empty($member['photo']) && file_exists($member['photo'])): ?>
                                <img src="<?= $member['photo'] ?>" alt="<?= htmlspecialchars($member['prenom'] . ' ' . $member['nom']) ?>">
                            <?php else: ?>
                                <img src="View/assets/default_avatar.png" alt="<?= htmlspecialchars($member['prenom'] . ' ' . $member['nom']) ?>">
                            <?php endif; ?>
                        </div>
                        
                        <div class="profile-info">
                            <h1><?= htmlspecialchars($member['prenom'] . ' ' . $member['nom']) ?></h1>
                            <p class="profile-grade"><i class="fa-solid fa-graduation-cap"></i> <?= htmlspecialchars($member['grade']) ?></p>
                            <?php if ($member['poste']): ?>
                                <p class="profile-position"><i class="fa-solid fa-briefcase"></i> <?= htmlspecialchars($member['poste']) ?></p>
                            <?php endif; ?>
                            <p class="profile-email"><i class="fa-solid fa-envelope"></i> <?= htmlspecialchars($member['email']) ?></p>
                            <?php if ($member['specialite']): ?>
                                <p class="profile-specialty"><i class="fa-solid fa-tag"></i> <?= htmlspecialchars($member['specialite']) ?></p>
                            <?php endif; ?>
                            <?php if ($member['domaine_recherche']): ?>
                                <p class="profile-domain"><i class="fa-solid fa-flask"></i> <?= htmlspecialchars($member['domaine_recherche']) ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <?php if ($member['biographie']): ?>
                        <div class="biography-section">
                            <h2><i class="fa-solid fa-user-circle"></i> Biographie</h2>
                            <p><?= nl2br(htmlspecialchars($member['biographie'])) ?></p>
                        </div>
                    <?php endif; ?>
                    
                    <div class="member-publications">
                        <h2><i class="fa-solid fa-book-open"></i> Publications (<?= count($publications) ?>)</h2>
                        <?php if (empty($publications)): ?>
                            <p class="no-results">Aucune publication</p>
                        <?php else: ?>
                            <div class="publications-list">
                                <?php foreach ($publications as $pub): ?>
                                    <div class="pub-item">
                                        <h3><?= htmlspecialchars($pub['titre']) ?></h3>
                                        <p class="pub-meta">
                                            <span class="pub-type"><?= ucfirst($pub['type']) ?></span>
                                            <span><?= date('Y', strtotime($pub['date_publication'])) ?></span>
                                            <?php if ($pub['conference']): ?>
                                                <span><?= htmlspecialchars($pub['conference']) ?></span>
                                            <?php endif; ?>
                                        </p>
                                        <?php if ($pub['fichier_pdf']): ?>
                                            <a href="<?= $pub['fichier_pdf'] ?>" class="btn-sm btn-primary" target="_blank">
                                                <i class="fa-solid fa-file-pdf"></i> PDF
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <?php if (!empty($projects)): ?>
                        <div class="member-projects">
                            <h2><i class="fa-solid fa-diagram-project"></i> Projets (<?= count($projects) ?>)</h2>
                            <div class="projects-list">
                                <?php foreach ($projects as $project): ?>
                                    <div class="project-item">
                                        <h3><?= htmlspecialchars($project['titre']) ?></h3>
                                        <p class="project-role"><?= htmlspecialchars($project['role_projet']) ?></p>
                                        <a href="index.php?router=projet-details&id=<?= $project['id_project'] ?>" class="btn-sm btn-primary">
                                            Voir le projet
                                        </a>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                
                <?php $common->footer(); ?>
            </body>
        </html>
        <?php
    }
}
?>
