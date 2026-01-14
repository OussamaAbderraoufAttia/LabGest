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
            
            <!-- Modal -->
            <div id="userModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:2000; justify-content:center; align-items:center;">
                <div style="background:white; padding:30px; border-radius:12px; width:600px; max-width:90%; position:relative;">
                    <button onclick="closeUserModal()" style="position:absolute; top:20px; right:20px; background:none; border:none; font-size:1.5rem; cursor:pointer;">&times;</button>
                    
                    <h3 id="modalTitle" style="margin-top:0; margin-bottom:20px; color:#2d3748;">Ajouter un Utilisateur</h3>
                    
                    <form id="userForm" method="POST" action="index.php?router=admin-users">
                        <input type="hidden" name="action" id="userAction" value="add">
                        <input type="hidden" name="id_user" id="userId">
                        
                        <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                            <div class="form-group">
                                <label style="display:block; margin-bottom:5px; font-weight:600;">Nom</label>
                                <input type="text" name="nom" id="nom" required style="width:100%; padding:10px; border:1px solid #e2e8f0; border-radius:6px;">
                            </div>
                            <div class="form-group">
                                <label style="display:block; margin-bottom:5px; font-weight:600;">Prénom</label>
                                <input type="text" name="prenom" id="prenom" required style="width:100%; padding:10px; border:1px solid #e2e8f0; border-radius:6px;">
                            </div>
                            <div class="form-group">
                                <label style="display:block; margin-bottom:5px; font-weight:600;">Email</label>
                                <input type="email" name="email" id="email" required style="width:100%; padding:10px; border:1px solid #e2e8f0; border-radius:6px;">
                            </div>
                            <div class="form-group">
                                <label style="display:block; margin-bottom:5px; font-weight:600;">Username</label>
                                <input type="text" name="username" id="username" style="width:100%; padding:10px; border:1px solid #e2e8f0; border-radius:6px;">
                            </div>
                            <div class="form-group">
                                <label style="display:block; margin-bottom:5px; font-weight:600;">Role</label>
                                <select name="role" id="role" style="width:100%; padding:10px; border:1px solid #e2e8f0; border-radius:6px;">
                                    <option value="enseignant-chercheur">Enseignant-Chercheur</option>
                                    <option value="doctorant">Doctorant</option>
                                    <option value="etudiant">Etudiant</option>
                                    <option value="admin">Admin</option>
                                    <option value="invite">Invité</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label style="display:block; margin-bottom:5px; font-weight:600;">Grade</label>
                                <input type="text" name="grade" id="grade" placeholder="ex: Professeur" style="width:100%; padding:10px; border:1px solid #e2e8f0; border-radius:6px;">
                            </div>
                            <div class="form-group">
                                <label style="display:block; margin-bottom:5px; font-weight:600;">Poste</label>
                                <input type="text" name="poste" id="poste" placeholder="ex: Directeur" style="width:100%; padding:10px; border:1px solid #e2e8f0; border-radius:6px;">
                            </div>
                            <div class="form-group" id="pwdGroup">
                                <label style="display:block; margin-bottom:5px; font-weight:600;">Mot de Passe</label>
                                <input type="password" name="password" id="password" style="width:100%; padding:10px; border:1px solid #e2e8f0; border-radius:6px;">
                            </div>
                        </div>
                        
                        <div style="margin-top:25px; text-align:right;">
                            <button type="button" onclick="closeUserModal()" class="btn btn-secondary" style="background:#edf2f7; color:#4a5568; margin-right:10px;">Annuler</button>
                            <button type="submit" class="btn btn-primary">Enregistrer</button>
                        </div>
                    </form>
                </div>
            </div>
            
            <script>
                const usersData = <?php echo json_encode($users); ?>;

                function openUserModal() {
                    document.getElementById('userModal').style.display = 'flex';
                    document.getElementById('userAction').value = 'add';
                    document.getElementById('modalTitle').textContent = 'Ajouter un Utilisateur';
                    document.getElementById('userForm').reset();
                    document.getElementById('username').required = true;
                    document.getElementById('password').required = true;
                    document.getElementById('pwdGroup').style.display = 'block';
                    document.getElementById('username').disabled = false;
                }
                
                function closeUserModal() {
                    document.getElementById('userModal').style.display = 'none';
                }
                
                function editUser(id) {
                    const user = usersData.find(u => u.id_user == id);
                    if (!user) return;
                    
                    document.getElementById('userModal').style.display = 'flex';
                    document.getElementById('userAction').value = 'edit';
                    document.getElementById('modalTitle').textContent = 'Modifier Utilisateur';
                    document.getElementById('userId').value = user.id_user;
                    
                    document.getElementById('nom').value = user.nom;
                    document.getElementById('prenom').value = user.prenom;
                    document.getElementById('email').value = user.email;
                    document.getElementById('username').value = user.username;
                    document.getElementById('username').disabled = true; // Prevent changing username
                    document.getElementById('role').value = user.role;
                    document.getElementById('grade').value = user.grade;
                    document.getElementById('poste').value = user.poste;
                    
                    // Hide password field for edit (or make optional)
                    document.getElementById('pwdGroup').style.display = 'none';
                    document.getElementById('password').required = false;
                }
                
                function deleteUser(id) {
                    if(confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')) {
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = 'index.php?router=admin-users';
                        
                        const inputAction = document.createElement('input');
                        inputAction.type = 'hidden';
                        inputAction.name = 'action';
                        inputAction.value = 'delete';
                        
                        const inputId = document.createElement('input');
                        inputId.type = 'hidden';
                        inputId.name = 'id_user';
                        inputId.value = id;
                        
                        form.appendChild(inputAction);
                        form.appendChild(inputId);
                        document.body.appendChild(form);
                        form.submit();
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
