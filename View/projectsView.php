<?php
require_once("commonViews.php");
require_once("View/Components/CardView.php");

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
                    
                    <!-- Projects Grid using CardView -->
                    <?php
                    $statusLabels = [
                        'en_cours' => 'En Cours',
                        'termine' => 'Terminé',
                        'soumis' => 'Soumis'
                    ];
                    
                    CardView::render($projects, function($project) use ($statusLabels) {
                        $statusLabel = $statusLabels[$project['statut']] ?? ucfirst(str_replace('_', ' ', $project['statut']));
                        return '
                            <div class="project-card">
                                <div class="project-header">
                                    <span class="project-theme">' . htmlspecialchars($project['thematique']) . '</span>
                                    <span class="project-status status-' . $project['statut'] . '">' . $statusLabel . '</span>
                                </div>
                                
                                <h3>' . htmlspecialchars($project['titre']) . '</h3>
                                
                                <p class="project-description">
                                    ' . htmlspecialchars(substr($project['description'], 0, 150)) . '...
                                </p>
                                
                                <div class="project-meta">
                                    <p><i class="fa-solid fa-user"></i> ' . htmlspecialchars($project['responsable_prenom'] . ' ' . $project['responsable_nom']) . '</p>
                                    <p><i class="fa-solid fa-coins"></i> ' . htmlspecialchars($project['type_financement']) . '</p>
                                </div>
                                
                                <a href="index.php?router=projet-details&id=' . $project['id_project'] . '" class="btn-primary">
                                    En savoir plus <i class="fa-solid fa-arrow-right"></i>
                                </a>
                            </div>
                        ';
                    }, 'projectsGrid', 'projects-grid');
                    ?>
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
    
    public function afficherDetails($project, $members, $publications, $partners, $isLoggedIn = false, $isMember = false, $isResponsable = false) {
        $common = new commonViews();
        
        // Get current user info
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $currentUserId = $_SESSION['admin']['id_user'] ?? $_SESSION['user']['id_user'] ?? null;
        $currentUserRole = $_SESSION['admin']['role'] ?? $_SESSION['user']['role'] ?? null;
        $isAdmin = $currentUserRole === 'admin';
        
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
                        <div class="header-top">
                            <div>
                                <h1><?= htmlspecialchars($project['titre']) ?></h1>
                                <div class="project-badges">
                                    <span class="badge theme-badge"><?= $project['thematique'] ?></span>
                                    <span class="badge status-<?= $project['statut'] ?>">
                                        <?= ucfirst(str_replace('_', ' ', $project['statut'])) ?>
                                    </span>
                                </div>
                            </div>
                            <div class="header-actions">
                                <?php if ($isLoggedIn && $isMember): ?>
                                    <button class="btn-primary" onclick="openPublicationModal()">
                                        <i class="fa-solid fa-plus"></i> Ajouter Publication
                                    </button>
                                <?php endif; ?>
                                <?php if ($isResponsable): ?>
                                    <button class="btn-secondary" onclick="openMemberModal()">
                                        <i class="fa-solid fa-user-plus"></i> Gérer Membres
                                    </button>
                                <?php endif; ?>
                                <?php if ($isAdmin): ?>
                                    <button class="btn-danger" onclick="closeProjectConfirm()">
                                        <i class="fa-solid fa-xmark"></i> Fermer Projet
                                    </button>
                                <?php endif; ?>
                                <?php if ($isLoggedIn && $isMember && !$isResponsable): ?>
                                    <button class="btn-secondary" onclick="quitProjectConfirm()">
                                        <i class="fa-solid fa-right-from-bracket"></i> Quitter
                                    </button>
                                <?php endif; ?>
                            </div>
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
                                    <div class="members-header">
                                        <h3><i class="fa-solid fa-users"></i> Membres de l'équipe (<?= count($members) ?>)</h3>
                                    </div>
                                    <ul class="members-list">
                                        <?php foreach ($members as $member): ?>
                                            <li class="member-item">
                                                <div class="member-info">
                                                    <span class="member-name"><?= htmlspecialchars($member['prenom'] . ' ' . $member['nom']) ?></span>
                                                    <span class="role-tag"><?= htmlspecialchars($member['role_projet']) ?></span>
                                                </div>
                                                <?php if ($isResponsable && $member['id_user'] != $project['responsable_id'] && $member['id_user'] != $currentUserId): ?>
                                                    <button class="btn-remove" title="Retirer du projet" onclick="removeMemberConfirm(<?= $member['id_user'] ?>, '<?= htmlspecialchars($member['prenom'] . ' ' . $member['nom']) ?>')">
                                                        <i class="fa-solid fa-trash"></i>
                                                    </button>
                                                <?php endif; ?>
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
                
                <!-- Publication Modal -->
                <?php if ($isLoggedIn && $isMember): ?>
                <div id="publicationModal" class="modal" style="display:none;">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h2>Ajouter une Publication</h2>
                            <button class="modal-close" onclick="closePublicationModal()">&times;</button>
                        </div>
                        <form id="publicationForm" onsubmit="submitPublication(event)">
                            <div class="form-group">
                                <label for="pubTitre">Titre de la publication *</label>
                                <input type="text" id="pubTitre" name="titre" required placeholder="Titre de la publication">
                            </div>
                            
                            <div class="form-group">
                                <label for="pubResume">Résumé</label>
                                <textarea id="pubResume" name="resume" placeholder="Résumé ou description" rows="3"></textarea>
                            </div>
                            
                            <div class="form-group">
                                <label for="pubType">Type *</label>
                                <select id="pubType" name="type" required>
                                    <option value="">-- Sélectionner --</option>
                                    <option value="article">Article</option>
                                    <option value="conference">Conférence</option>
                                    <option value="these">Thèse</option>
                                    <option value="memoire">Mémoire</option>
                                    <option value="rapport">Rapport</option>
                                    <option value="cours">Cours</option>
                                    <option value="livre">Livre</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="pubDate">Date de publication *</label>
                                <input type="date" id="pubDate" name="date_publication" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="pubConf">Conférence / Revue</label>
                                <input type="text" id="pubConf" name="conference" placeholder="Ex: IEEE ICPR 2024">
                            </div>
                            
                            <div class="form-group">
                                <label for="pubDOI">DOI</label>
                                <input type="text" id="pubDOI" name="doi" placeholder="Ex: 10.1109/ICPR.2024.12345">
                            </div>
                            
                            <div class="form-group">
                                <label>Co-auteurs</label>
                                <div id="authorsContainer" class="authors-list">
                                    <p class="info-text">Vous serez automatiquement ajouté comme auteur principal. Sélectionnez d'autres co-auteurs ci-dessous:</p>
                                    <div id="availableAuthors" class="available-authors">
                                        <!-- Populated by JavaScript -->
                                    </div>
                                </div>
                            </div>
                            
                            <div class="modal-actions">
                                <button type="submit" class="btn-primary">
                                    <i class="fa-solid fa-paper-plane"></i> Publier
                                </button>
                                <button type="button" class="btn-secondary" onclick="closePublicationModal()">
                                    Annuler
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Member Management Modal -->
                <?php if ($isResponsable): ?>
                <div id="memberModal" class="modal" style="display:none;">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h2>Ajouter un Membre</h2>
                            <button class="modal-close" onclick="closeMemberModal()">&times;</button>
                        </div>
                        <form id="memberForm" onsubmit="submitMember(event)">
                            <div class="form-group">
                                <label for="memberSelect">Sélectionner un utilisateur *</label>
                                <select id="memberSelect" name="user_id" required>
                                    <option value="">-- Sélectionner --</option>
                                    <!-- Populated by JavaScript -->
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="memberRole">Rôle *</label>
                                <select id="memberRole" name="role_projet" required>
                                    <option value="Etudiant">Étudiant</option>
                                    <option value="Chercheur">Chercheur</option>
                                    <option value="Développeur">Développeur</option>
                                    <option value="Co-responsable">Co-responsable</option>
                                </select>
                            </div>
                            
                            <div class="modal-actions">
                                <button type="submit" class="btn-primary">
                                    <i class="fa-solid fa-plus"></i> Ajouter
                                </button>
                                <button type="button" class="btn-secondary" onclick="closeMemberModal()">
                                    Annuler
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                <?php endif; ?>
                
                <script>
                    const projectId = <?= $project['id_project'] ?>;
                    const currentUserId = <?= $currentUserId ?? 'null' ?>;
                    const projectResponsableId = <?= $project['responsable_id'] ?>;
                    const members = <?= json_encode($members) ?>;
                    
                    // Publication Modal
                    function openPublicationModal() {
                        document.getElementById('publicationModal').style.display = 'flex';
                        // Populate co-authors list
                        const authorsContainer = document.getElementById('availableAuthors');
                        authorsContainer.innerHTML = '';
                        
                        members.forEach(member => {
                            if (member.id_user != currentUserId) {
                                const div = document.createElement('div');
                                div.className = 'author-checkbox';
                                div.innerHTML = `
                                    <input type="checkbox" id="author_${member.id_user}" name="author_ids" value="${member.id_user}">
                                    <label for="author_${member.id_user}">
                                        ${member.prenom} ${member.nom} <span class="role-info">${member.role_projet}</span>
                                    </label>
                                `;
                                authorsContainer.appendChild(div);
                            }
                        });
                    }
                    
                    function closePublicationModal() {
                        document.getElementById('publicationModal').style.display = 'none';
                        document.getElementById('publicationForm').reset();
                    }
                    
                    function submitPublication(event) {
                        event.preventDefault();
                        
                        const formData = new FormData(document.getElementById('publicationForm'));
                        const authorIds = Array.from(document.querySelectorAll('input[name="author_ids"]:checked')).map(el => el.value);
                        
                        // Create request data
                        const data = new FormData();
                        data.append('action', 'add-publication');
                        data.append('project_id', projectId);
                        data.append('titre', formData.get('titre'));
                        data.append('resume', formData.get('resume'));
                        data.append('type', formData.get('type'));
                        data.append('date_publication', formData.get('date_publication'));
                        data.append('conference', formData.get('conference'));
                        data.append('doi', formData.get('doi'));
                        data.append('author_ids', JSON.stringify(authorIds));
                        
                        fetch('index.php?router=project-add-publication', {
                            method: 'POST',
                            body: data
                        })
                        .then(response => response.json())
                        .then(result => {
                            if (result.success) {
                                alert('Publication ajoutée avec succès!');
                                closePublicationModal();
                                location.reload();
                            } else {
                                alert('Erreur: ' + (result.message || 'Impossible d\'ajouter la publication'));
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Erreur lors de l\'ajout de la publication');
                        });
                    }
                    
                    // Member Modal
                    function openMemberModal() {
                        document.getElementById('memberModal').style.display = 'flex';
                        populateMemberSelect();
                    }
                    
                    function closeMemberModal() {
                        document.getElementById('memberModal').style.display = 'none';
                        document.getElementById('memberForm').reset();
                    }
                    
                    function populateMemberSelect() {
                        // This would normally fetch available users from backend
                        // For now, we'll handle this in the form submission
                        const select = document.getElementById('memberSelect');
                        if (select.options.length <= 1) {
                            // Load available members
                            fetch('index.php?router=project-members&id=' + projectId)
                            .then(response => response.json())
                            .then(data => {
                                select.innerHTML = '<option value="">-- Sélectionner --</option>';
                                data.eligible.forEach(user => {
                                    const option = document.createElement('option');
                                    option.value = user.id_user;
                                    option.textContent = user.prenom + ' ' + user.nom + ' (' + user.role + ')';
                                    select.appendChild(option);
                                });
                            })
                            .catch(error => console.error('Error:', error));
                        }
                    }
                    
                    function submitMember(event) {
                        event.preventDefault();
                        
                        const formData = new FormData(document.getElementById('memberForm'));
                        const data = new FormData();
                        data.append('action', 'add-member');
                        data.append('project_id', projectId);
                        data.append('user_id', formData.get('user_id'));
                        data.append('role_projet', formData.get('role_projet'));
                        
                        fetch('index.php?router=project-add-member', {
                            method: 'POST',
                            body: data
                        })
                        .then(response => response.json())
                        .then(result => {
                            if (result.success) {
                                alert('Membre ajouté avec succès!');
                                closeMemberModal();
                                location.reload();
                            } else {
                                alert('Erreur: ' + (result.message || 'Impossible d\'ajouter le membre'));
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Erreur lors de l\'ajout du membre');
                        });
                    }
                    
                    function removeMemberConfirm(userId, userName) {
                        if (confirm('Êtes-vous sûr de vouloir retirer ' + userName + ' du projet?')) {
                            removeMember(userId);
                        }
                    }
                    
                    function removeMember(userId) {
                        const data = new FormData();
                        data.append('action', 'remove-member');
                        data.append('project_id', projectId);
                        data.append('user_id', userId);
                        
                        fetch('index.php?router=project-remove-member', {
                            method: 'POST',
                            body: data
                        })
                        .then(response => response.json())
                        .then(result => {
                            if (result.success) {
                                alert('Membre retiré avec succès!');
                                location.reload();
                            } else {
                                alert('Erreur: ' + (result.message || 'Impossible de retirer le membre'));
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Erreur lors du retrait du membre');
                        });
                    }
                    
                    function quitProjectConfirm() {
                        if (confirm('Êtes-vous sûr de vouloir quitter ce projet?')) {
                            quitProject();
                        }
                    }
                    
                    function quitProject() {
                        const data = new FormData();
                        data.append('action', 'quit');
                        data.append('project_id', projectId);
                        
                        fetch('index.php?router=project-quit', {
                            method: 'POST',
                            body: data
                        })
                        .then(response => response.json())
                        .then(result => {
                            if (result.success) {
                                alert('Vous avez quitté le projet');
                                window.location.href = 'index.php?router=projets';
                            } else {
                                alert('Erreur: ' + (result.message || 'Impossible de quitter le projet'));
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Erreur lors de la tentative de quitter le projet');
                        });
                    }
                    
                    function closeProjectConfirm() {
                        if (confirm('Êtes-vous sûr de vouloir fermer ce projet? Cette action ne peut pas être annulée.')) {
                            closeProject();
                        }
                    }
                    
                    function closeProject() {
                        const data = new FormData();
                        data.append('action', 'close');
                        data.append('project_id', projectId);
                        
                        fetch('index.php?router=project-close', {
                            method: 'POST',
                            body: data
                        })
                        .then(response => response.json())
                        .then(result => {
                            if (result.success) {
                                alert('Le projet a été fermé');
                                location.reload();
                            } else {
                                alert('Erreur: ' + (result.message || 'Impossible de fermer le projet'));
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Erreur lors de la fermeture du projet');
                        });
                    }
                    
                    // Close modals when clicking outside
                    window.onclick = function(event) {
                        const pubModal = document.getElementById('publicationModal');
                        const memberModal = document.getElementById('memberModal');
                        
                        if (pubModal && event.target === pubModal) {
                            closePublicationModal();
                        }
                        if (memberModal && event.target === memberModal) {
                            closeMemberModal();
                        }
                    }
                </script>
                
                <style>
                    .header-top {
                        display: flex;
                        justify-content: space-between;
                        align-items: flex-start;
                        gap: 20px;
                        margin-bottom: 20px;
                    }
                    
                    .header-actions {
                        display: flex;
                        gap: 10px;
                        flex-wrap: wrap;
                    }
                    
                    .member-item {
                        display: flex;
                        justify-content: space-between;
                        align-items: center;
                        padding: 10px;
                        background: #f5f5f5;
                        border-radius: 5px;
                        margin-bottom: 8px;
                    }
                    
                    .member-info {
                        flex: 1;
                        display: flex;
                        flex-direction: column;
                        gap: 3px;
                    }
                    
                    .member-name {
                        font-weight: 600;
                        color: #333;
                    }
                    
                    .btn-remove {
                        background: #ff4444;
                        color: white;
                        border: none;
                        padding: 5px 10px;
                        border-radius: 3px;
                        cursor: pointer;
                        font-size: 12px;
                    }
                    
                    .btn-remove:hover {
                        background: #dd0000;
                    }
                    
                    .btn-danger {
                        background: #ff4444;
                        color: white;
                        padding: 8px 16px;
                        border: none;
                        border-radius: 5px;
                        cursor: pointer;
                    }
                    
                    .btn-danger:hover {
                        background: #dd0000;
                    }
                    
                    /* Modal Styles */
                    .modal {
                        display: none;
                        position: fixed;
                        z-index: 1000;
                        left: 0;
                        top: 0;
                        width: 100%;
                        height: 100%;
                        background-color: rgba(0,0,0,0.4);
                        align-items: center;
                        justify-content: center;
                        padding: 20px;
                    }
                    
                    .modal-content {
                        background-color: white;
                        padding: 30px;
                        border-radius: 10px;
                        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
                        max-width: 500px;
                        width: 100%;
                        max-height: 80vh;
                        overflow-y: auto;
                    }
                    
                    .modal-header {
                        display: flex;
                        justify-content: space-between;
                        align-items: center;
                        margin-bottom: 20px;
                        border-bottom: 2px solid #f0f0f0;
                        padding-bottom: 15px;
                    }
                    
                    .modal-header h2 {
                        margin: 0;
                        font-size: 20px;
                        color: #333;
                    }
                    
                    .modal-close {
                        background: none;
                        border: none;
                        font-size: 28px;
                        cursor: pointer;
                        color: #999;
                    }
                    
                    .modal-close:hover {
                        color: #333;
                    }
                    
                    .form-group {
                        margin-bottom: 15px;
                    }
                    
                    .form-group label {
                        display: block;
                        margin-bottom: 5px;
                        font-weight: 600;
                        color: #333;
                    }
                    
                    .form-group input,
                    .form-group textarea,
                    .form-group select {
                        width: 100%;
                        padding: 8px 12px;
                        border: 1px solid #ddd;
                        border-radius: 5px;
                        font-family: inherit;
                    }
                    
                    .form-group textarea {
                        resize: vertical;
                        font-family: inherit;
                    }
                    
                    .available-authors {
                        display: flex;
                        flex-direction: column;
                        gap: 8px;
                        max-height: 250px;
                        overflow-y: auto;
                        margin-top: 10px;
                    }
                    
                    .author-checkbox {
                        display: flex;
                        align-items: center;
                        gap: 8px;
                    }
                    
                    .author-checkbox input[type="checkbox"] {
                        width: auto;
                        cursor: pointer;
                    }
                    
                    .author-checkbox label {
                        margin: 0;
                        cursor: pointer;
                        display: flex;
                        align-items: center;
                        gap: 5px;
                    }
                    
                    .role-info {
                        font-size: 12px;
                        color: #999;
                        font-style: italic;
                    }
                    
                    .info-text {
                        font-size: 12px;
                        color: #666;
                        margin-bottom: 10px;
                    }
                    
                    .modal-actions {
                        display: flex;
                        gap: 10px;
                        margin-top: 20px;
                    }
                    
                    .modal-actions button {
                        flex: 1;
                        padding: 10px;
                        border: none;
                        border-radius: 5px;
                        font-weight: 600;
                        cursor: pointer;
                    }
                </style>
                
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
