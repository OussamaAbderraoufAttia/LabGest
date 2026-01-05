<?php
require_once("View/Components/TableView.php");
require_once("View/adminDashboardView.php");

class adminUsersView extends adminDashboardView {
    
    public function afficherGestionUtilisateurs($users) {
        $this->header();
        ?>
        <body>
            <div class="admin-container">
                <?php $this->sidebar('users'); ?>
                
                <main class="admin-content">
                    <?php $this->topBar('Gestion des Utilisateurs'); ?>
                    
                    <div class="admin-actions" style="margin-bottom: 20px; display: flex; justify-content: space-between;">
                        <div class="search-box" style="position: relative;">
                            <input type="text" id="userAdminSearch" placeholder="Rechercher un utilisateur..." style="padding: 10px 35px 10px 15px; border-radius: 8px; border: 1px solid #e2e8f0; width: 300px;">
                            <i class="fa-solid fa-search" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); color: #a0aec0;"></i>
                        </div>
                        <button class="btn btn-primary" onclick="openUserModal()">
                            <i class="fa-solid fa-plus"></i> Ajouter un Utilisateur
                        </button>
                    </div>
                    
                    <div class="admin-card" style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
                        <?php
                        // Table Columns Configuration
                        $columns = [
                            'id_user' => ['label' => 'ID', 'sortable' => true],
                            'photo' => ['label' => 'Photo', 'renderer' => function($row) {
                                $img = !empty($row['photo']) ? htmlspecialchars($row['photo']) : 'View/assets/default_avatar.png';
                                return "<img src='$img' style='width: 35px; height: 35px; border-radius: 50%; object-fit: cover;'>";
                            }],
                            'nom_complet' => ['label' => 'Nom Complet', 'renderer' => function($row) {
                                return '<strong>' . htmlspecialchars($row['nom'] . ' ' . $row['prenom']) . '</strong><br><small style="color:#718096">@'.htmlspecialchars($row['username']).'</small>';
                            }],
                            'email' => ['label' => 'Email', 'sortable' => true],
                            'role' => ['label' => 'Rôle', 'sortable' => true, 'renderer' => function($row) {
                                $badges = [
                                    'admin' => 'background:#e53e3e; color:white;',
                                    'enseignant-chercheur' => 'background:#667eea; color:white;',
                                    'doctorant' => 'background:#f6ad55; color:white;',
                                    'etudiant' => 'background:#4fd1c5; color:white;',
                                    'invite' => 'background:#cbd5e0; color:#2d3748;'
                                ];
                                $style = $badges[$row['role']] ?? 'background:#cbd5e0; color:#2d3748;';
                                return "<span style='padding: 4px 8px; border-radius: 4px; font-size: 0.8rem; font-weight: 600; $style'>" . ucfirst($row['role']) . "</span>";
                            }],
                            'actions' => ['label' => 'Actions', 'renderer' => function($row) {
                                return '
                                <button class="btn-icon" style="color: #667eea; margin-right: 10px;" onclick="editUser('.$row['id_user'].')" title="Modifier">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                                <button class="btn-icon" style="color: #e53e3e;" onclick="deleteUser('.$row['id_user'].')" title="Supprimer">
                                    <i class="fa-solid fa-trash"></i>
                                </button>';
                            }, 'sortType' => 'none']
                        ];
                        
                        TableView::render($columns, $users, 'usersTable', 'admin-table');
                        ?>
                    </div>
                </main>
            </div>
            
            <!-- Modal (Simplified Placeholder) -->
            <div id="userModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:2000; justify-content:center; align-items:center;">
                <div style="background:white; padding:30px; border-radius:12px; width:500px; max-width:90%;">
                    <h3 style="margin-top:0;">Gérer Utilisateur</h3>
                    <p>Fonctionnalité complète a venir dans la prochaine étape (CRUD).</p>
                    <div style="text-align:right; margin-top:20px;">
                        <button onclick="document.getElementById('userModal').style.display='none'" class="btn btn-secondary">Fermer</button>
                    </div>
                </div>
            </div>
            
            <script>
                function openUserModal() {
                    document.getElementById('userModal').style.display = 'flex';
                }
                function editUser(id) {
                    alert('Edit user ' + id);
                }
                function deleteUser(id) {
                    if(confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')) {
                        alert('Delete user ' + id);
                    }
                }
                
                // Client-side search for the table
                document.getElementById('userAdminSearch').addEventListener('keyup', function() {
                    var value = this.value.toLowerCase();
                    var rows = document.querySelectorAll('#usersTable tbody tr');
                    
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
