<?php
require_once("View/Components/TableView.php");
require_once("View/adminDashboardView.php");

class adminPublicationsView extends adminDashboardView {
    
    public function afficherGestionPublications($pending, $allPubs, $projects) {
        $this->header();
        ?>
        <body>
            <div class="admin-container">
                <?php $this->sidebar('publications'); ?>
                
                <main class="admin-content">
                    <?php $this->topBar('Gestion des Publications'); ?>
                    
                    <!-- PENDING VALIDATION SECTION -->
                    <?php if(!empty($pending)): ?>
                    <div class="admin-card" style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); margin-bottom:30px; border-left: 5px solid #ecc94b;">
                        <h3 style="color:#d69e2e; margin-top:0;"><i class="fa-solid fa-clock"></i> En attente de validation</h3>
                        <?php
                        $colsPending = [
                            'titre' => ['label' => 'Titre', 'sortable' => true],
                            'author_nom' => ['label' => 'Auteur', 'renderer' => function($row) {
                                return $row['author_nom'] . ' ' . $row['author_prenom'];
                            }],
                            'date_soumission' => ['label' => 'Date', 'sortable' => true],
                            'actions' => ['label' => 'Actions', 'renderer' => function($row) {
                                return '
                                <form method="post" action="index.php?router=admin-publications" style="display:inline;">
                                    <input type="hidden" name="action" value="validate">
                                    <input type="hidden" name="id_pub" value="'.$row['id_pub'].'">
                                    <button type="submit" class="btn btn-success" style="padding:5px 10px; font-size:0.9rem;">
                                        <i class="fa-solid fa-check"></i> Valider
                                    </button>
                                </form>
                                <form method="post" action="index.php?router=admin-publications" style="display:inline; margin-left:5px;">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id_pub" value="'.$row['id_pub'].'">
                                    <button type="submit" class="btn btn-danger" style="padding:5px 10px; font-size:0.9rem;">
                                        <i class="fa-solid fa-trash"></i> Rejeter
                                    </button>
                                </form>';
                            }]
                        ];
                        TableView::render($colsPending, $pending, 'pendingTable', 'admin-table');
                        ?>
                    </div>
                    <?php endif; ?>

                    <!-- ALL PUBLICATIONS SECTION -->
                    <div class="admin-actions" style="margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
                        <div style="display: flex; gap: 10px;">
                            <a href="index.php?router=admin_report_publications_pdf" class="btn btn-success" style="background: #48bb78; color: white; padding: 10px 15px; border-radius: 8px; text-decoration: none; display: flex; align-items: center; gap: 8px;">
                                <i class="fa-solid fa-file-pdf"></i> Rapport Bibliographique
                            </a>
                        </div>
                        <button class="btn btn-primary" onclick="openPubModal()">
                            <i class="fa-solid fa-plus"></i> Ajouter une Publication
                        </button>
                    </div>
                    
                    <div class="admin-card" style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
                        <h3>Publications Validées</h3>
                        <?php
                        $colsAll = [
                            'id_pub' => ['label' => 'ID', 'sortable' => true],
                            'titre' => ['label' => 'Titre', 'sortable' => true],
                            'type' => ['label' => 'Type', 'sortable' => true, 'renderer' => function($row) {
                                return "<span class='badge'>".$row['type']."</span>";
                            }],
                            'date_publication' => ['label' => 'Date', 'sortable' => true],
                            'actions' => ['label' => 'Actions', 'renderer' => function($row) {
                                return '
                                <form method="post" action="index.php?router=admin-publications" style="display:inline;" onsubmit="return confirm(\'Supprimer cette publication ?\')">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id_pub" value="'.$row['id_pub'].'">
                                    <button type="submit" class="btn-icon" style="color: #e53e3e;" title="Supprimer">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>';
                            }]
                        ];
                        TableView::render($colsAll, $allPubs, 'allPubsTable', 'admin-table');
                        ?>
                    </div>
                </main>
            </div>
            
            <!-- Modal -->
            <div id="pubModal" class="modal-overlay" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:2000; justify-content:center; align-items:center;">
                <div style="background:white; padding:30px; border-radius:12px; width:600px; max-width:90%;">
                    <h3 style="margin-top:0;">Ajouter une Publication</h3>
                    <form method="post" action="index.php?router=admin-publications">
                        <input type="hidden" name="action" value="add">
                        
                        <div class="form-group">
                            <label>Titre</label>
                            <input type="text" name="titre" required class="form-control" style="width:100%;">
                        </div>
                        
                        <div class="form-group">
                            <label>Résumé</label>
                            <textarea name="resume" class="form-control" style="width:100%; height:80px;"></textarea>
                        </div>
                        
                        <div class="form-row" style="display:flex; gap:15px;">
                            <div style="flex:1;">
                                <label>Date</label>
                                <input type="date" name="date_publication" required class="form-control" style="width:100%;">
                            </div>
                            <div style="flex:1;">
                                <label>Type</label>
                                <select name="type" class="form-control" style="width:100%;">
                                    <option value="article">Article</option>
                                    <option value="conference">Conférence</option>
                                    <option value="these">Thèse</option>
                                    <option value="memoire">Mémoire</option>
                                    <option value="rapport">Rapport</option>
                                    <option value="cours">Cours</option>
                                    <option value="livre">Livre</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label>Projet Associé</label>
                            <select name="project_id" class="form-control" style="width:100%;">
                                <option value="">Aucun</option>
                                <?php foreach($projects as $p): ?>
                                    <option value="<?= $p['id_project'] ?>"><?= htmlspecialchars($p['titre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label>Lien Externe / DOI</label>
                            <input type="text" name="lien_externe" class="form-control" style="width:100%;">
                        </div>
                        
                        <div style="text-align:right; margin-top:20px;">
                            <button type="button" onclick="document.getElementById('pubModal').style.display='none'" class="btn btn-secondary">Annuler</button>
                            <button type="submit" class="btn btn-primary">Ajouter</button>
                        </div>
                    </form>
                </div>
            </div>
            
            <script>
                function openPubModal() {
                    document.getElementById('pubModal').style.display = 'flex';
                }
            </script>
        </body>
        </html>
        <?php
    }
}
?>
