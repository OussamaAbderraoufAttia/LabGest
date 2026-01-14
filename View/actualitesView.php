<?php
require_once("commonViews.php");

class actualitesView {
    
    public function afficher_page($actualites) {
        $common = new commonViews();
        ?>
        <!DOCTYPE html>
        <html lang="fr">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Actualités - LRE</title>
            <link rel="stylesheet" href="View/css/commonStyles.css">
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
            <style>
                .actualites-section {
                    padding: 40px 20px;
                    background-color: #f8f9fa;
                }
                .actualites-container {
                    max-width: 1000px;
                    margin: 0 auto;
                }
                .actualites-container h1 {
                    margin-bottom: 40px;
                    text-align: center;
                    color: #333;
                }
                .actualite-card {
                    background: white;
                    border-radius: 10px;
                    overflow: hidden;
                    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
                    margin-bottom: 30px;
                    transition: transform 0.3s, box-shadow 0.3s;
                    display: flex;
                    flex-direction: row;
                }
                .actualite-card:hover {
                    transform: translateY(-5px);
                    box-shadow: 0 5px 20px rgba(0,0,0,0.15);
                }
                .actualite-image {
                    width: 250px;
                    height: 200px;
                    object-fit: cover;
                    flex-shrink: 0;
                }
                .actualite-content {
                    padding: 20px;
                    flex: 1;
                    display: flex;
                    flex-direction: column;
                }
                .actualite-meta {
                    display: flex;
                    gap: 15px;
                    margin-bottom: 10px;
                    font-size: 12px;
                    color: #666;
                }
                .actualite-category {
                    display: inline-block;
                    background-color: #667eea;
                    color: white;
                    padding: 3px 10px;
                    border-radius: 15px;
                    font-size: 11px;
                }
                .actualite-content h3 {
                    margin: 10px 0;
                    color: #333;
                    font-size: 1.3em;
                }
                .actualite-content p {
                    color: #666;
                    margin: 10px 0;
                    line-height: 1.5;
                    flex: 1;
                }
                .actualite-footer {
                    margin-top: 15px;
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                }
                .read-more {
                    color: #667eea;
                    text-decoration: none;
                    font-weight: 600;
                    transition: color 0.3s;
                }
                .read-more:hover {
                    color: #764ba2;
                }
                .author-info {
                    font-size: 12px;
                    color: #999;
                }
                @media (max-width: 768px) {
                    .actualite-card {
                        flex-direction: column;
                    }
                    .actualite-image {
                        width: 100%;
                        height: 200px;
                    }
                }
            </style>
        </head>
        <body>
            <?php $common->navBar(); ?>
            
            <section class="actualites-section" style="padding-top: 120px;">
                <div class="actualites-container">
                    <h1>Actualités</h1>
                    
                    <?php if (count($actualites) > 0): ?>
                        <?php foreach ($actualites as $actualite): ?>
                            <div class="actualite-card">
                                <img src="<?php echo htmlspecialchars($actualite['image_url'] ?? 'View/assets/default_news.png'); ?>" 
                                     alt="<?php echo htmlspecialchars($actualite['titre']); ?>" 
                                     class="actualite-image">
                                <div class="actualite-content">
                                    <div class="actualite-meta">
                                        <span class="actualite-category"><?php echo htmlspecialchars($actualite['categorie']); ?></span>
                                        <span><?php echo date('d/m/Y', strtotime($actualite['date_publication'])); ?></span>
                                    </div>
                                    <h3><?php echo htmlspecialchars($actualite['titre']); ?></h3>
                                    <p><?php echo htmlspecialchars(substr($actualite['description'], 0, 150)) . '...'; ?></p>
                                    <div class="actualite-footer">
                                        <a href="index.php?router=actualite_detail&id=<?php echo $actualite['id_actualite']; ?>" class="read-more">
                                            Lire la suite →
                                        </a>
                                        <div class="author-info">
                                            <?php if ($actualite['prenom'] && $actualite['nom']): ?>
                                                Par <?php echo htmlspecialchars($actualite['prenom'] . ' ' . $actualite['nom']); ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div style="text-align: center; padding: 60px 20px;">
                            <i class="fas fa-inbox" style="font-size: 48px; color: #ccc; margin-bottom: 20px;"></i>
                            <p style="color: #666; font-size: 18px;">Aucune actualité pour le moment.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </section>

            <?php $common->footer(); ?>
        </body>
        </html>
        <?php
    }

    public function afficher_detail($actualite) {
        $common = new commonViews();
        ?>
        <!DOCTYPE html>
        <html lang="fr">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title><?php echo htmlspecialchars($actualite['titre']); ?></title>
            <link rel="stylesheet" href="View/css/commonStyles.css">
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
            <style>
                .detail-container {
                    max-width: 800px;
                    margin: 100px auto 40px;
                    padding: 0 20px;
                }
                .detail-header {
                    margin-bottom: 40px;
                }
                .detail-header h1 {
                    font-size: 2.5em;
                    margin-bottom: 20px;
                    color: #333;
                }
                .detail-meta {
                    display: flex;
                    gap: 20px;
                    font-size: 14px;
                    color: #666;
                    margin-bottom: 30px;
                }
                .detail-category {
                    display: inline-block;
                    background-color: #667eea;
                    color: white;
                    padding: 5px 15px;
                    border-radius: 20px;
                }
                .detail-image {
                    width: 100%;
                    height: 400px;
                    object-fit: cover;
                    border-radius: 10px;
                    margin-bottom: 40px;
                }
                .detail-content {
                    background: white;
                    padding: 40px;
                    border-radius: 10px;
                    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
                    line-height: 1.8;
                    color: #333;
                }
                .detail-content p {
                    margin-bottom: 20px;
                }
                .back-link {
                    margin-bottom: 20px;
                }
                .back-link a {
                    color: #667eea;
                    text-decoration: none;
                }
                .back-link a:hover {
                    text-decoration: underline;
                }
            </style>
        </head>
        <body>
            <?php $common->navBar(); ?>
            
            <div class="detail-container">
                <div class="back-link">
                    <a href="index.php?router=actualites"><i class="fas fa-arrow-left"></i> Retour aux actualités</a>
                </div>

                <div class="detail-header">
                    <h1><?php echo htmlspecialchars($actualite['titre']); ?></h1>
                    <div class="detail-meta">
                        <span class="detail-category"><?php echo htmlspecialchars($actualite['categorie']); ?></span>
                        <span><i class="fas fa-calendar"></i> <?php echo date('d F Y', strtotime($actualite['date_publication'])); ?></span>
                        <?php if ($actualite['prenom'] && $actualite['nom']): ?>
                            <span><i class="fas fa-user"></i> <?php echo htmlspecialchars($actualite['prenom'] . ' ' . $actualite['nom']); ?></span>
                        <?php endif; ?>
                    </div>
                </div>

                <img src="<?php echo htmlspecialchars($actualite['image_url'] ?? 'View/assets/default_news.png'); ?>" 
                     alt="<?php echo htmlspecialchars($actualite['titre']); ?>" 
                     class="detail-image">

                <div class="detail-content">
                    <?php echo nl2br(htmlspecialchars($actualite['contenu_complet'] ?? $actualite['description'])); ?>
                </div>
            </div>

            <?php $common->footer(); ?>
        </body>
        </html>
        <?php
    }

    public function afficher_admin($actualites, $page, $itemsPerPage, $total, $totalPages) {
        $common = new commonViews();
        ?>
        <!DOCTYPE html>
        <html lang="fr">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Gestion des Actualités - Admin</title>
            <link rel="stylesheet" href="View/css/commonStyles.css">
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
            <style>
                .admin-container {
                    max-width: 1200px;
                    margin: 100px auto 40px;
                    padding: 0 20px;
                }
                .admin-header {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    margin-bottom: 30px;
                }
                .admin-header h1 {
                    margin: 0;
                }
                .btn-add {
                    background-color: #28a745;
                    color: white;
                    padding: 10px 20px;
                    border-radius: 5px;
                    text-decoration: none;
                    transition: background-color 0.3s;
                }
                .btn-add:hover {
                    background-color: #218838;
                }
                .controls {
                    display: flex;
                    gap: 15px;
                    margin-bottom: 30px;
                    align-items: center;
                }
                .items-selector {
                    display: flex;
                    align-items: center;
                    gap: 10px;
                }
                .items-selector select {
                    padding: 8px;
                    border: 1px solid #ddd;
                    border-radius: 5px;
                }
                .actualites-table {
                    width: 100%;
                    border-collapse: collapse;
                    background: white;
                    border-radius: 10px;
                    overflow: hidden;
                    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
                }
                .actualites-table thead {
                    background-color: #f8f9fa;
                    border-bottom: 2px solid #dee2e6;
                }
                .actualites-table th {
                    padding: 15px;
                    text-align: left;
                    font-weight: 600;
                    color: #333;
                }
                .actualites-table td {
                    padding: 15px;
                    border-bottom: 1px solid #dee2e6;
                }
                .actualites-table tbody tr:hover {
                    background-color: #f8f9fa;
                }
                .actions {
                    display: flex;
                    gap: 10px;
                }
                .btn-small {
                    padding: 6px 12px;
                    border-radius: 5px;
                    text-decoration: none;
                    font-size: 12px;
                    border: none;
                    cursor: pointer;
                    transition: all 0.3s;
                }
                .btn-edit {
                    background-color: #007bff;
                    color: white;
                }
                .btn-edit:hover {
                    background-color: #0056b3;
                }
                .btn-delete {
                    background-color: #dc3545;
                    color: white;
                }
                .btn-delete:hover {
                    background-color: #c82333;
                }
                .btn-archive {
                    background-color: #ffc107;
                    color: #333;
                }
                .btn-archive:hover {
                    background-color: #e0a800;
                }
                .pagination {
                    display: flex;
                    gap: 5px;
                    margin-top: 30px;
                    justify-content: center;
                }
                .pagination a, .pagination span {
                    padding: 8px 12px;
                    border: 1px solid #ddd;
                    border-radius: 5px;
                    text-decoration: none;
                    color: #667eea;
                }
                .pagination a:hover {
                    background-color: #f8f9fa;
                }
                .pagination .active {
                    background-color: #667eea;
                    color: white;
                    border-color: #667eea;
                }
                .success-message {
                    background-color: #d4edda;
                    color: #155724;
                    padding: 15px;
                    border-radius: 5px;
                    margin-bottom: 20px;
                }
            </style>
        </head>
        <body>
            <?php $common->navBar(); ?>
            
            <div class="admin-container">
                <?php if (isset($_GET['success'])): ?>
                    <div class="success-message">
                        <i class="fas fa-check-circle"></i> Opération réussie !
                    </div>
                <?php endif; ?>

                <div class="admin-header">
                    <h1>Gestion des Actualités</h1>
                    <a href="index.php?router=actualite_add" class="btn-add">
                        <i class="fas fa-plus"></i> Ajouter une actualité
                    </a>
                </div>

                <div class="controls">
                    <div class="items-selector">
                        <label for="itemsPerPage">Afficher par page:</label>
                        <select id="itemsPerPage" onchange="window.location.href = 'index.php?router=actualites_admin&page=1&items=' + this.value">
                            <option value="5" <?php echo $itemsPerPage === 5 ? 'selected' : ''; ?>>5</option>
                            <option value="10" <?php echo $itemsPerPage === 10 ? 'selected' : ''; ?>>10</option>
                            <option value="20" <?php echo $itemsPerPage === 20 ? 'selected' : ''; ?>>20</option>
                            <option value="50" <?php echo $itemsPerPage === 50 ? 'selected' : ''; ?>>50</option>
                        </select>
                    </div>
                    <span style="color: #666;">Total: <?php echo $total; ?> actualité(s)</span>
                </div>

                <?php if (count($actualites) > 0): ?>
                    <table class="actualites-table">
                        <thead>
                            <tr>
                                <th>Titre</th>
                                <th>Catégorie</th>
                                <th>Date de publication</th>
                                <th>Auteur</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($actualites as $actualite): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars(substr($actualite['titre'], 0, 50)); ?></td>
                                    <td><?php echo htmlspecialchars($actualite['categorie']); ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($actualite['date_publication'])); ?></td>
                                    <td><?php echo htmlspecialchars($actualite['prenom'] . ' ' . $actualite['nom']); ?></td>
                                    <td>
                                        <div class="actions">
                                            <a href="index.php?router=actualite_edit&id=<?php echo $actualite['id_actualite']; ?>" class="btn-small btn-edit">
                                                <i class="fas fa-edit"></i> Modifier
                                            </a>
                                            <a href="index.php?router=actualite_archive&id=<?php echo $actualite['id_actualite']; ?>" class="btn-small btn-archive">
                                                <i class="fas fa-archive"></i> Archiver
                                            </a>
                                            <a href="index.php?router=actualite_delete&id=<?php echo $actualite['id_actualite']; ?>" class="btn-small btn-delete" onclick="return confirm('Êtes-vous sûr?')">
                                                <i class="fas fa-trash"></i> Supprimer
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <?php if ($totalPages > 1): ?>
                        <div class="pagination">
                            <?php if ($page > 1): ?>
                                <a href="index.php?router=actualites_admin&page=1&items=<?php echo $itemsPerPage; ?>">« Première</a>
                                <a href="index.php?router=actualites_admin&page=<?php echo $page - 1; ?>&items=<?php echo $itemsPerPage; ?>">‹ Précédente</a>
                            <?php endif; ?>

                            <?php
                            $startPage = max(1, $page - 2);
                            $endPage = min($totalPages, $page + 2);
                            
                            for ($i = $startPage; $i <= $endPage; $i++):
                            ?>
                                <?php if ($i === $page): ?>
                                    <span class="active"><?php echo $i; ?></span>
                                <?php else: ?>
                                    <a href="index.php?router=actualites_admin&page=<?php echo $i; ?>&items=<?php echo $itemsPerPage; ?>"><?php echo $i; ?></a>
                                <?php endif; ?>
                            <?php endfor; ?>

                            <?php if ($page < $totalPages): ?>
                                <a href="index.php?router=actualites_admin&page=<?php echo $page + 1; ?>&items=<?php echo $itemsPerPage; ?>">Suivante ›</a>
                                <a href="index.php?router=actualites_admin&page=<?php echo $totalPages; ?>&items=<?php echo $itemsPerPage; ?>">Dernière »</a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div style="text-align: center; padding: 60px 20px; background: white; border-radius: 10px;">
                        <i class="fas fa-inbox" style="font-size: 48px; color: #ccc; margin-bottom: 20px;"></i>
                        <p style="color: #666; font-size: 18px;">Aucune actualité trouvée.</p>
                    </div>
                <?php endif; ?>
            </div>

            <?php $common->footer(); ?>
        </body>
        </html>
        <?php
    }

    public function afficher_form_add() {
        $common = new commonViews();
        ?>
        <!DOCTYPE html>
        <html lang="fr">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Ajouter une Actualité</title>
            <link rel="stylesheet" href="View/css/commonStyles.css">
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
            <style>
                .form-container {
                    max-width: 800px;
                    margin: 100px auto 40px;
                    padding: 0 20px;
                }
                .form-container h1 {
                    margin-bottom: 30px;
                }
                .form-group {
                    margin-bottom: 20px;
                }
                .form-group label {
                    display: block;
                    margin-bottom: 8px;
                    font-weight: 600;
                    color: #333;
                }
                .form-group input, .form-group textarea, .form-group select {
                    width: 100%;
                    padding: 10px;
                    border: 1px solid #ddd;
                    border-radius: 5px;
                    font-family: inherit;
                    font-size: 14px;
                }
                .form-group textarea {
                    resize: vertical;
                    min-height: 120px;
                }
                .form-actions {
                    display: flex;
                    gap: 15px;
                    margin-top: 30px;
                }
                .btn-submit {
                    background-color: #28a745;
                    color: white;
                    padding: 12px 30px;
                    border: none;
                    border-radius: 5px;
                    cursor: pointer;
                    font-size: 16px;
                    transition: background-color 0.3s;
                }
                .btn-submit:hover {
                    background-color: #218838;
                }
                .btn-cancel {
                    background-color: #6c757d;
                    color: white;
                    padding: 12px 30px;
                    border: none;
                    border-radius: 5px;
                    cursor: pointer;
                    font-size: 16px;
                    text-decoration: none;
                    display: inline-block;
                    transition: background-color 0.3s;
                }
                .btn-cancel:hover {
                    background-color: #5a6268;
                }
            </style>
        </head>
        <body>
            <?php $common->navBar(); ?>
            
            <div class="form-container">
                <h1>Ajouter une nouvelle actualité</h1>
                
                <form method="POST" action="index.php?router=actualite_add">
                    <div class="form-group">
                        <label for="titre">Titre *</label>
                        <input type="text" id="titre" name="titre" required>
                    </div>

                    <div class="form-group">
                        <label for="categorie">Catégorie</label>
                        <select id="categorie" name="categorie">
                            <option value="Général">Général</option>
                            <option value="Projets">Projets</option>
                            <option value="Événements">Événements</option>
                            <option value="Partenariats">Partenariats</option>
                            <option value="Reconnaissances">Reconnaissances</option>
                            <option value="Infrastructure">Infrastructure</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="description">Description courte *</label>
                        <textarea id="description" name="description" required placeholder="Résumé de l'actualité (visible sur la liste)"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="contenu_complet">Contenu complet</label>
                        <textarea id="contenu_complet" name="contenu_complet" placeholder="Article complet (visible sur la page de détail)"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="image_url">URL de l'image</label>
                        <input type="url" id="image_url" name="image_url" placeholder="https://exemple.com/image.jpg">
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-submit">
                            <i class="fas fa-save"></i> Publier l'actualité
                        </button>
                        <a href="index.php?router=actualites_admin" class="btn-cancel">
                            <i class="fas fa-times"></i> Annuler
                        </a>
                    </div>
                </form>
            </div>

            <?php $common->footer(); ?>
        </body>
        </html>
        <?php
    }

    public function afficher_form_edit($actualite) {
        $common = new commonViews();
        ?>
        <!DOCTYPE html>
        <html lang="fr">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Modifier l'Actualité</title>
            <link rel="stylesheet" href="View/css/commonStyles.css">
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
            <style>
                .form-container {
                    max-width: 800px;
                    margin: 100px auto 40px;
                    padding: 0 20px;
                }
                .form-container h1 {
                    margin-bottom: 30px;
                }
                .form-group {
                    margin-bottom: 20px;
                }
                .form-group label {
                    display: block;
                    margin-bottom: 8px;
                    font-weight: 600;
                    color: #333;
                }
                .form-group input, .form-group textarea, .form-group select {
                    width: 100%;
                    padding: 10px;
                    border: 1px solid #ddd;
                    border-radius: 5px;
                    font-family: inherit;
                    font-size: 14px;
                }
                .form-group textarea {
                    resize: vertical;
                    min-height: 120px;
                }
                .form-actions {
                    display: flex;
                    gap: 15px;
                    margin-top: 30px;
                }
                .btn-submit {
                    background-color: #007bff;
                    color: white;
                    padding: 12px 30px;
                    border: none;
                    border-radius: 5px;
                    cursor: pointer;
                    font-size: 16px;
                    transition: background-color 0.3s;
                }
                .btn-submit:hover {
                    background-color: #0056b3;
                }
                .btn-cancel {
                    background-color: #6c757d;
                    color: white;
                    padding: 12px 30px;
                    border: none;
                    border-radius: 5px;
                    cursor: pointer;
                    font-size: 16px;
                    text-decoration: none;
                    display: inline-block;
                    transition: background-color 0.3s;
                }
                .btn-cancel:hover {
                    background-color: #5a6268;
                }
            </style>
        </head>
        <body>
            <?php $common->navBar(); ?>
            
            <div class="form-container">
                <h1>Modifier l'actualité</h1>
                
                <form method="POST" action="index.php?router=actualite_edit&id=<?php echo $actualite['id_actualite']; ?>">
                    <div class="form-group">
                        <label for="titre">Titre *</label>
                        <input type="text" id="titre" name="titre" value="<?php echo htmlspecialchars($actualite['titre']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="categorie">Catégorie</label>
                        <select id="categorie" name="categorie">
                            <option value="Général" <?php echo $actualite['categorie'] === 'Général' ? 'selected' : ''; ?>>Général</option>
                            <option value="Projets" <?php echo $actualite['categorie'] === 'Projets' ? 'selected' : ''; ?>>Projets</option>
                            <option value="Événements" <?php echo $actualite['categorie'] === 'Événements' ? 'selected' : ''; ?>>Événements</option>
                            <option value="Partenariats" <?php echo $actualite['categorie'] === 'Partenariats' ? 'selected' : ''; ?>>Partenariats</option>
                            <option value="Reconnaissances" <?php echo $actualite['categorie'] === 'Reconnaissances' ? 'selected' : ''; ?>>Reconnaissances</option>
                            <option value="Infrastructure" <?php echo $actualite['categorie'] === 'Infrastructure' ? 'selected' : ''; ?>>Infrastructure</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="description">Description courte *</label>
                        <textarea id="description" name="description" required><?php echo htmlspecialchars($actualite['description']); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="contenu_complet">Contenu complet</label>
                        <textarea id="contenu_complet" name="contenu_complet"><?php echo htmlspecialchars($actualite['contenu_complet']); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="image_url">URL de l'image</label>
                        <input type="url" id="image_url" name="image_url" value="<?php echo htmlspecialchars($actualite['image_url']); ?>">
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-submit">
                            <i class="fas fa-save"></i> Mettre à jour
                        </button>
                        <a href="index.php?router=actualites_admin" class="btn-cancel">
                            <i class="fas fa-times"></i> Annuler
                        </a>
                    </div>
                </form>
            </div>

            <?php $common->footer(); ?>
        </body>
        </html>
        <?php
    }
}
?>
