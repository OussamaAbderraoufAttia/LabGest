<?php
require_once("View/Components/TableView.php");
require_once("View/adminDashboardView.php");

class adminTeamsView extends adminDashboardView {
    
    public function afficherGestionEquipes($teams) {
        $this->header();
        ?>
        <body>
            <div class="admin-container">
                <?php $this->sidebar('teams'); ?>
                
                <main class="admin-content">
                    <?php $this->topBar('Gestion des Équipes'); ?>
                    
                    <div class="admin-actions" style="margin-bottom: 20px; display: flex; justify-content: flex-end;">
                        <button class="btn btn-primary">
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
                                <button class="btn-icon" style="color: #667eea; margin: 0 10px;" title="Modifier">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                                <button class="btn-icon" style="color: #e53e3e;" title="Supprimer">
                                    <i class="fa-solid fa-trash"></i>
                                </button>';
                            }, 'sortType' => 'none']
                        ];
                        
                        TableView::render($columns, $teams, 'teamsTable', 'admin-table');
                        ?>
                    </div>
                </main>
            </div>
        </body>
        </html>
        <?php
    }
}
?>
