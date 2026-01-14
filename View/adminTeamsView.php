<?php
require_once("View/Components/TableView.php");
require_once("View/adminDashboardView.php");

class adminTeamsView extends adminDashboardView {
    
    public function afficherGestionEquipes($teams, $potentialChefs) { // added arg
        $this->header();
        ?>
        <body>
            <div class="admin-container">
                <?php $this->sidebar('teams'); ?>
                
                <main class="admin-content">
                    <?php $this->topBar('Gestion des Équipes'); ?>
                    
                    <div class="admin-actions" style="margin-bottom: 20px; display: flex; justify-content: flex-end;">
                        <button class="btn btn-primary" onclick="openTeamModal()">
                            <i class="fa-solid fa-plus"></i> Nouvelle Équipe
                        </button>
                    </div>
                    
                    <div class="admin-card" style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
                        <?php
                        $columns = [
                            'id_team' => ['label' => 'ID', 'sortable' => true],
                            'nom' => ['label' => 'Nom de l\'Équipe', 'renderer' => function($row) {
                                return '<strong>' . htmlspecialchars($row['nom']) . '</strong>';
                            }],
                            'photo' => ['label' => 'Logo', 'renderer' => function($row) {
                                $img = !empty($row['photo']) ? htmlspecialchars($row['photo']) : 'View/assets/default_team.jpg';
                                return "<img src='$img' style='width: 35px; height: 35px; object-fit: cover; border-radius: 4px;'>";
                            }],
                            'chef' => ['label' => 'Chef d\'Équipe', 'renderer' => function($row) {
                                if (!empty($row['chef_nom'])) {
                                    return htmlspecialchars($row['chef_nom'] . ' ' . $row['chef_prenom']);
                                }
                                return '<span style="color:#a0aec0; font-style:italic;">Non assigné</span>';
                            }],
                            'description' => ['label' => 'Description', 'renderer' => function($row) {
                                $desc = htmlspecialchars($row['description']);
                                return strlen($desc) > 50 ? substr($desc, 0, 50) . '...' : $desc;
                            }],
                            'actions' => ['label' => 'Actions', 'renderer' => function($row) {
                                return '
                                <a href="index.php?router=equipes&id='.$row['id_team'].'" class="btn-icon" style="color: #4299e1;" target="_blank" title="Voir Public">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                <button class="btn-icon" style="color: #667eea; margin: 0 10px;" onclick="editTeam('.$row['id_team'].')" title="Modifier">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                                <button class="btn-icon" style="color: #e53e3e;" onclick="deleteTeam('.$row['id_team'].')" title="Supprimer">
                                    <i class="fa-solid fa-trash"></i>
                                </button>';
                            }, 'sortType' => 'none']
                        ];
                        
                        TableView::render($columns, $teams, 'teamsTable', 'admin-table');
                        ?>
                    </div>
                </main>
            </div>
            
            <!-- Modal -->
            <div id="teamModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:2000; justify-content:center; align-items:center;">
                <div style="background:white; padding:30px; border-radius:12px; width:600px; max-width:90%; position:relative;">
                    <button onclick="document.getElementById('teamModal').style.display='none'" style="position:absolute; top:20px; right:20px; background:none; border:none; font-size:1.5rem; cursor:pointer;">&times;</button>
                    
                    <h3 id="modalTitle" style="margin-top:0; margin-bottom:20px; color:#2d3748;">Ajouter une Équipe</h3>
                    
                    <form id="teamForm" method="POST" enctype="multipart/form-data" action="index.php?router=admin-teams">
                        <input type="hidden" name="action" id="teamAction" value="add">
                        <input type="hidden" name="id_team" id="teamId">
                        
                        <div class="form-group">
                            <label style="display:block; margin-bottom:5px; font-weight:600;">Nom de l'équipe</label>
                            <input type="text" name="nom" id="nom" required style="width:100%; padding:10px; border:1px solid #e2e8f0; border-radius:6px;">
                        </div>
                        
                        <div class="form-group">
                            <label style="display:block; margin-bottom:5px; font-weight:600;">Description</label>
                            <textarea name="description" id="description" rows="4" style="width:100%; padding:10px; border:1px solid #e2e8f0; border-radius:6px;"></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label style="display:block; margin-bottom:5px; font-weight:600;">Chef d'Équipe</label>
                            <select name="chef_id" id="chef_id" style="width:100%; padding:10px; border:1px solid #e2e8f0; border-radius:6px;">
                                <option value="">-- Sélectionner un Chef --</option>
                                <?php foreach ($potentialChefs as $chief): ?>
                                    <option value="<?= $chief['id_user'] ?>"><?= htmlspecialchars($chief['nom'] . ' ' . $chief['prenom']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label style="display:block; margin-bottom:5px; font-weight:600;">Logo/Photo</label>
                            <input type="file" name="image" id="image" accept="image/*" style="width:100%; padding:10px; border:1px solid #e2e8f0; border-radius:6px;">
                        </div>
                        
                        <div style="margin-top:25px; text-align:right;">
                            <button type="button" onclick="document.getElementById('teamModal').style.display='none'" class="btn btn-secondary" style="background:#edf2f7; color:#4a5568; margin-right:10px;">Annuler</button>
                            <button type="submit" class="btn btn-primary">Enregistrer</button>
                        </div>
                    </form>
                </div>
            </div>

            <script>
                const teamsData = <?php echo json_encode($teams); ?>;

                function openTeamModal() {
                    document.getElementById('teamModal').style.display = 'flex';
                    document.getElementById('teamAction').value = 'add';
                    document.getElementById('modalTitle').textContent = 'Ajouter une Équipe';
                    document.getElementById('teamForm').reset();
                }

                function editTeam(id) {
                    const team = teamsData.find(t => t.id_team == id);
                    if (!team) return;
                    
                    document.getElementById('teamModal').style.display = 'flex';
                    document.getElementById('teamAction').value = 'edit';
                    document.getElementById('modalTitle').textContent = 'Modifier Équipe';
                    document.getElementById('teamId').value = team.id_team;
                    
                    document.getElementById('nom').value = team.nom;
                    document.getElementById('description').value = team.description;
                    document.getElementById('chef_id').value = team.chef_id;
                }

                function deleteTeam(id) {
                    if(confirm('Êtes-vous sûr de vouloir supprimer cette équipe ?')) {
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = 'index.php?router=admin-teams';
                        
                        const inputAction = document.createElement('input');
                        inputAction.type = 'hidden';
                        inputAction.name = 'action';
                        inputAction.value = 'delete';
                        
                        const inputId = document.createElement('input');
                        inputId.type = 'hidden';
                        inputId.name = 'id_team';
                        inputId.value = id;
                        
                        form.appendChild(inputAction);
                        form.appendChild(inputId);
                        document.body.appendChild(form);
                        form.submit();
                    }
                }
            </script>
        </body>
        </html>
        <?php
    }
}
?>
