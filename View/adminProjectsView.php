<?php
require_once("View/Components/TableView.php");
require_once("View/adminDashboardView.php");

class adminProjectsView extends adminDashboardView {
    
    public function afficherGestionProjets($projects, $researchers) {
        $this->header();
        ?>
        <body>
            <div class="admin-container">
                <?php $this->sidebar('projects'); ?>
                
                <main class="admin-content">
                    <?php $this->topBar('Gestion des Projets de Recherche'); ?>
                    
                    <div class="admin-actions" style="margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
                        <form method="get" action="index.php" class="search-box" style="position: relative;">
                            <input type="hidden" name="router" value="admin-projects">
                            <input type="text" id="projectAdminSearch" placeholder="Rechercher un projet..." style="padding: 10px 35px 10px 15px; border-radius: 8px; border: 1px solid #e2e8f0; width: 300px;">
                            <i class="fa-solid fa-search" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); color: #a0aec0;"></i>
                        </form>
                        <div style="display: flex; gap: 10px;">
                            <a href="index.php?router=admin_report_projects_pdf" class="btn btn-success" style="background: #48bb78; color: white; padding: 10px 15px; border-radius: 8px; text-decoration: none; display: flex; align-items: center; gap: 8px;">
                                <i class="fa-solid fa-file-pdf"></i> Exporter PDF
                            </a>
                            <button class="btn btn-primary" onclick="openProjectModal('add')">
                                <i class="fa-solid fa-plus"></i> Nouveau Projet
                            </button>
                        </div>
                    </div>
                    
                    <div class="admin-card" style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
                        <?php
                        $columns = [
                            'id_project' => ['label' => 'ID', 'sortable' => true],
                            'titre' => ['label' => 'Titre', 'sortable' => true],
                            'thematique' => ['label' => 'Thématique', 'sortable' => true, 'renderer' => function($row) {
                                return "<span class='badge'>".htmlspecialchars($row['thematique'])."</span>";
                            }],
                            'responsable' => ['label' => 'Responsable', 'renderer' => function($row) {
                                return htmlspecialchars($row['responsable_nom'] . ' ' . $row['responsable_prenom']);
                            }],
                            'dates' => ['label' => 'Période', 'renderer' => function($row) {
                                return "<small>" . $row['date_debut'] . " <br> " . $row['date_fin'] . "</small>";
                            }],
                            'statut' => ['label' => 'Statut', 'renderer' => function($row) {
                                $colors = [
                                    'en_cours' => '#48bb78', 
                                    'termine' => '#a0aec0', 
                                    'soumis' => '#ecc94b'
                                ];
                                $labels = [
                                    'en_cours' => 'En Cours',
                                    'termine' => 'Terminé',
                                    'soumis' => 'Soumis'
                                ];
                                $color = $colors[$row['statut']] ?? '#cbd5e0';
                                $label = $labels[$row['statut']] ?? ucfirst($row['statut']);
                                return "<span style='color:$color; font-weight:bold'>" . $label . "</span>";
                            }],
                            'actions' => ['label' => 'Actions', 'renderer' => function($row) {
                                $rowJson = htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8');
                                return '
                                <button class="btn-icon" style="color: #667eea; margin-right: 10px;" onclick=\'editProject('.$rowJson.')\' title="Modifier">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                                <form method="post" action="index.php?router=admin-projects" style="display:inline;" onsubmit="return confirm(\'Supprimer ce projet ?\')">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id_project" value="'.$row['id_project'].'">
                                    <button type="submit" class="btn-icon" style="color: #e53e3e;" title="Supprimer">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>';
                            }]
                        ];
                        
                        TableView::render($columns, $projects, 'projectsTable', 'admin-table');
                        ?>
                    </div>
                </main>
            </div>
            
            <!-- Modal -->
            <div id="projectModal" class="modal-overlay" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:2000; justify-content:center; align-items:center;">
                <div style="background:white; padding:30px; border-radius:12px; width:600px; max-width:95%; max-height:90vh; overflow-y:auto;">
                    <h3 id="modalTitle" style="margin-top:0;">Nouveau Projet</h3>
                    <form id="projectForm" method="post" action="index.php?router=admin-projects" enctype="multipart/form-data">
                        <input type="hidden" name="action" id="formAction" value="add">
                        <input type="hidden" name="id_project" id="projectId">
                        
                        <div class="form-group" style="margin-bottom:15px;">
                            <label>Titre</label>
                            <input type="text" name="titre" id="titre" required class="form-control" style="width:100%; padding:8px; margin-top:5px;">
                        </div>
                        
                        <div class="form-group" style="margin-bottom:15px;">
                            <label>Description</label>
                            <textarea name="description" id="description" required class="form-control" style="width:100%; padding:8px; margin-top:5px; height:100px;"></textarea>
                        </div>
                        
                        <div class="form-row" style="display:flex; gap:15px; margin-bottom:15px;">
                            <div style="flex:1;">
                                <label>Thématique</label>
                                <input type="text" name="thematique" id="thematique" required class="form-control" style="width:100%; padding:8px; margin-top:5px;">
                            </div>
                            <div style="flex:1;">
                                <label>Statut</label>
                                <select name="statut" id="statut" class="form-control" style="width:100%; padding:8px; margin-top:5px;">
                                    <option value="en_cours">En Cours</option>
                                    <option value="termine">Terminé</option>
                                    <option value="soumis">Soumis</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-row" style="display:flex; gap:15px; margin-bottom:15px;">
                            <div style="flex:1;">
                                <label>Date Début</label>
                                <input type="date" name="date_debut" id="date_debut" required class="form-control" style="width:100%; padding:8px; margin-top:5px;">
                            </div>
                            <div style="flex:1;">
                                <label>Date Fin</label>
                                <input type="date" name="date_fin" id="date_fin" class="form-control" style="width:100%; padding:8px; margin-top:5px;">
                            </div>
                        </div>
                        
                        <div class="form-group" style="margin-bottom:15px;">
                            <label>Responsable</label>
                            <select name="responsable_id" id="responsable_id" required class="form-control" style="width:100%; padding:8px; margin-top:5px;">
                                <option value="">Choisir un responsable...</option>
                                <?php foreach($researchers as $r): ?>
                                    <option value="<?= $r['id_user'] ?>"><?= htmlspecialchars($r['nom'] . ' ' . $r['prenom']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div style="text-align:right;">
                            <button type="button" onclick="closeProjectModal()" class="btn btn-secondary" style="margin-right:10px;">Annuler</button>
                            <button type="submit" class="btn btn-primary">Enregistrer</button>
                        </div>
                    </form>
                </div>
            </div>
            
            <script>
                function openProjectModal(mode) {
                    document.getElementById('projectModal').style.display = 'flex';
                    if(mode === 'add') {
                        document.getElementById('modalTitle').innerText = 'Nouveau Projet';
                        document.getElementById('formAction').value = 'add';
                        document.getElementById('projectForm').reset();
                        document.getElementById('projectId').value = '';
                    }
                }
                
                function closeProjectModal() {
                    document.getElementById('projectModal').style.display = 'none';
                }

                function editProject(project) {
                    openProjectModal('edit');
                    document.getElementById('modalTitle').innerText = 'Modifier Projet';
                    document.getElementById('formAction').value = 'edit';
                    document.getElementById('projectId').value = project.id_project;
                    
                    document.getElementById('titre').value = project.titre;
                    document.getElementById('description').value = project.description;
                    document.getElementById('thematique').value = project.thematique;
                    document.getElementById('statut').value = project.statut;
                    document.getElementById('date_debut').value = project.date_debut;
                    document.getElementById('date_fin').value = project.date_fin;
                    document.getElementById('responsable_id').value = project.responsable_id;
                }
                
                // Search
                document.getElementById('projectAdminSearch').addEventListener('keyup', function() {
                    var value = this.value.toLowerCase();
                    var rows = document.querySelectorAll('#projectsTable tbody tr');
                    rows.forEach(function(row) {
                        var text = row.textContent.toLowerCase();
                        row.style.display = text.indexOf(value) > -1 ? '' : 'none';
                    });
                });
            </script>
        </body>
        </html>
        <?php
    }
}
?>
