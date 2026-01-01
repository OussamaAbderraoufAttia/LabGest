<?php
require_once("commonViews.php");
require_once("View/Components/TableView.php");
require_once("View/Components/CardView.php");
require_once("View/Components/OrganigramView.php");
require_once("Model/teamModel.php");

class teamsView {
    
    public function entetePage() {
        ?>
        <head>
            <meta charset="UTF-8">
            <title>Équipes - LRE</title>
            <link rel="stylesheet" href="View/css/commonStyles.css">
            <link rel="stylesheet" href="View/css/teamsStyle.css">
            <link rel="stylesheet" href="View/css/components.css">
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
            <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
            <script>
            // Global Sorting Function
            function sortTable(tableId, n, type) {
                var table, rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
                table = document.getElementById(tableId);
                if(!table) return;
                switching = true;
                dir = "asc"; 
                
                // Icon management
                $(table).find('th i').removeClass('fa-sort-up fa-sort-down').addClass('fa-sort');
                var th = table.getElementsByTagName("TH")[n];
                
                while (switching) {
                    switching = false;
                    rows = table.rows;
                    
                    for (i = 1; i < (rows.length - 1); i++) {
                        shouldSwitch = false;
                        x = rows[i].getElementsByTagName("TD")[n];
                        y = rows[i + 1].getElementsByTagName("TD")[n];
                        
                        var xVal = $(x).text().trim();
                        var yVal = $(y).text().trim();
                        
                        // Extract custom values if present
                        if($(x).find('[data-value]').length > 0) xVal = $(x).find('[data-value]').data('value');
                        if($(y).find('[data-value]').length > 0) yVal = $(y).find('[data-value]').data('value');

                        if (type == 'grade') {
                            xVal = getGradeRank(xVal);
                            yVal = getGradeRank(yVal);
                        } else if (type == 'role') {
                            xVal = getRoleRank(xVal);
                            yVal = getRoleRank(yVal);
                        } else if (type == 'number') {
                            xVal = parseFloat(xVal) || 0;
                            yVal = parseFloat(yVal) || 0;
                        } else if (type == 'none') {
                            continue;
                        } else {
                            xVal = xVal.toLowerCase();
                            yVal = yVal.toLowerCase();
                        }

                        if (dir == "asc") {
                            if (xVal > yVal) { shouldSwitch = true; break; }
                        } else if (dir == "desc") {
                            if (xVal < yVal) { shouldSwitch = true; break; }
                        }
                    }
                    
                    if (shouldSwitch) {
                        rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
                        switching = true;
                        switchcount ++; 
                    } else {
                        if (switchcount == 0 && dir == "asc") {
                            dir = "desc";
                            switching = true;
                        }
                    }
                }
                
                // Update Icon
                var icon = $(th).find('i');
                icon.removeClass('fa-sort');
                if(dir == "asc") icon.addClass('fa-sort-up');
                else icon.addClass('fa-sort-down');
            }

            function getGradeRank(grade) {
                if(!grade) return 99;
                grade = grade.toLowerCase();
                if (grade.includes('prof')) return 1;
                if (grade.includes('mca')) return 2;
                if (grade.includes('mcb')) return 3;
                if (grade.includes('maa')) return 4;
                if (grade.includes('mab')) return 5;
                if (grade.includes('doctorant')) return 6;
                return 99;
            }

            function getRoleRank(role) {
                if(!role) return 99;
                role = role.toLowerCase();
                if (role.includes('chef')) return 1;
                if (role.includes('admin')) return 2;
                if (role.includes('enseignant')) return 3;
                if (role.includes('chercheur')) return 4;
                if (role.includes('membre')) return 5;
                if (role.includes('doctorant')) return 6;
                if (role.includes('etudiant')) return 7;
                return 99;
            }
            </script>
        </head>
        <?php
    }
    
    // MASTER VIEW: List of Teams
    public function afficherEquipes($teams, $director) {
        $common = new commonViews();
        ?>
        <!DOCTYPE html>
        <html lang="fr">
            <?php $this->entetePage(); ?>
            <body>
                <?php $common->navBar(); ?>
                
                <div class="teams-container">
                    <h1 class="page-title">Nos Équipes de Recherche</h1>
                    
                    <!-- Director / Lab Organigram Link could go here if global -->
                    <?php if ($director): ?>
                        <div class="lab-director-summary" style="text-align: center; margin-bottom: 3rem;">
                            <h2>Directeur du Laboratoire</h2>
                            <div class="director-card-mini" style="display: inline-block; background: white; padding: 1rem; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                                <img src="<?= $director['photo'] ?? 'View/assets/default_avatar.png' ?>" style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover;">
                                <h3><?= htmlspecialchars($director['prenom'] . ' ' . $director['nom']) ?></h3>
                                <p><?= htmlspecialchars($director['grade']) ?></p>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Filters Section -->
                    <div class="filters-section" style="margin-bottom: 2rem;">
                        <input type="text" id="statsSearch" placeholder="Rechercher une équipe..." class="form-input" style="max-width: 300px;">
                    </div>
                    
                    <!-- TEAMS TABLE -->
                    <?php
                    $columns = [
                        'nom' => ['label' => 'Nom de l\'équipe', 'renderer' => function($row) {
                            return '<strong>' . htmlspecialchars($row['nom']) . '</strong>';
                        }],
                        'chef' => ['label' => 'Chef d\'équipe', 'renderer' => function($row) {
                            return htmlspecialchars($row['chef_prenom'] . ' ' . $row['chef_nom']);
                        }],
                        'member_count' => ['label' => 'Membres', 'renderer' => function($row) {
                            return '<span class="badge">' . ($row['member_count'] ?? 0) . '</span>';
                        }],
                        'pub_count' => ['label' => 'Publications', 'renderer' => function($row) {
                            return '<span class="badge">' . ($row['pub_count'] ?? 0) . '</span>';
                        }],
                        'actions' => ['label' => 'Actions', 'renderer' => function($row) {
                            return '<a href="index.php?router=equipe-details&id='.$row['id_team'].'" class="btn-sm btn-primary">
                                        <i class="fa-solid fa-eye"></i> Voir Détails & Organigramme
                                    </a>';
                        }]
                    ];
                    
                    TableView::render($columns, $teams, 'teamsTable');
                    ?>
                </div>
                
                <script>
                    // Simple JS for search (since we want it "done properly" we'd normally do AJAX, but client-side fine for small data)
                    $('#statsSearch').on('keyup', function() {
                        var value = $(this).val().toLowerCase();
                        $("#teamsTable tbody tr").filter(function() {
                            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                        });
                    });
                </script>
                
                <?php $common->footer(); ?>
            </body>
        </html>
        <?php
    }
    
    // DETAIL VIEW: Organigram + Members Table
    public function afficherDetailsEquipe($team, $members, $publications) {
        $common = new commonViews();
        
        // Prepare Organigram Data
        // Root: Chef
        $root = [
            'prenom' => $team['chef_prenom'],
            'nom' => $team['chef_nom'],
            'photo' => $team['chef_photo'],
            'role' => 'Chef d\'équipe (' . $team['chef_grade'] . ')',
            'id_user' => $team['chef_id']
        ];
        
        // Group Members by Role/Grade for the tree
        $groups = ['Enseignants-Chercheurs' => [], 'Doctorants' => []];
        foreach ($members as $m) {
            if ($m['id_user'] == $team['chef_id']) continue; // Skip chef if in list
            
            if (stripos($m['grade'], 'Doctorant') !== false || stripos($m['role_dans_equipe'], 'Doctorant') !== false) {
                $groups['Doctorants'][] = $m;
            } else {
                $groups['Enseignants-Chercheurs'][] = $m;
            }
        }
        
        ?>
        <!DOCTYPE html>
        <html lang="fr">
            <?php $this->entetePage(); ?>
            <body>
                <?php $common->navBar(); ?>
                
                <div class="teams-container">
                    <a href="index.php?router=equipes" class="back-link"><i class="fa-solid fa-arrow-left"></i> Retour aux équipes</a>
                    
                    <h1 class="page-title"><?= htmlspecialchars($team['nom']) ?></h1>
                    <?php if ($team['description']): ?>
                        <p class="team-desc-large" style="text-align: center; max-width: 800px; margin: 0 auto 3rem; color: #666;">
                            <?= htmlspecialchars($team['description']) ?>
                        </p>
                    <?php endif; ?>
                    
                    <!-- VISUAL ORGANIGRAM -->
                    <section class="organigram-section">
                        <h2 class="section-heading"><i class="fa-solid fa-sitemap"></i> Organigramme de l'équipe</h2>
                        <?php
                        OrganigramView::renderTree($root, $groups, function($node) {
                            $photo = !empty($node['photo']) ? $node['photo'] : 'View/assets/default_avatar.png';
                            $role = $node['role'] ?? ($node['grade'] ?? 'Membre');
                            $id = $node['id_user'];
                            
                            return '
                                <a href="index.php?router=membre-profil&id='.$id.'" style="text-decoration:none; color:inherit;">
                                    <img src="'.$photo.'" class="org-photo">
                                    <div class="org-name">'.htmlspecialchars($node['prenom'].' '.$node['nom']).'</div>
                                    <div class="org-role">'.htmlspecialchars($role).'</div>
                                </a>
                            ';
                        });
                        ?>
                    </section>
                    
                    <hr style="margin: 4rem 0; border: 0; border-top: 1px solid #eee;">
                    
                    <!-- MEMBERS TABLE -->
                    <section class="members-list-section">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
                            <h2 class="section-heading" style="margin:0;"><i class="fa-solid fa-users"></i> Liste des Membres</h2>
                            <input type="text" id="memberSearch" placeholder="Filtrer les membres..." class="form-input" style="width: auto;">
                        </div>
                        
                        <?php
                        $memColumns = [
                            'photo' => ['label' => 'Photo', 'renderer' => function($row) {
                                $src = !empty($row['photo']) ? $row['photo'] : 'View/assets/default_avatar.png';
                                return '<img src="'.$src.'" style="width:40px; height:40px; border-radius:50%; object-fit:cover;">';
                            }, 'sortType' => 'none'],
                            'nom' => ['label' => 'Nom Complet', 'renderer' => function($row) {
                                return '<strong>'.htmlspecialchars($row['prenom'].' '.$row['nom']).'</strong>';
                            }, 'sortType' => 'string'],
                            'grade' => ['label' => 'Grade', 'renderer' => function($row) {
                                return '<span class="grade-badge" data-value="'.htmlspecialchars($row['grade']).'">'.htmlspecialchars($row['grade']).'</span>';
                            }, 'sortType' => 'grade'],
                            'role' => ['label' => 'Rôle', 'renderer' => function($row) {
                                return '<span data-value="'.htmlspecialchars($row['role_dans_equipe'] ?? 'Membre').'">'.htmlspecialchars($row['role_dans_equipe'] ?? 'Membre').'</span>';
                            }, 'sortType' => 'role'],
                            'specialite' => ['label' => 'Spécialité', 'renderer' => function($row) {
                                return htmlspecialchars($row['specialite'] ?? '-');
                            }, 'sortType' => 'string'],
                            'actions' => ['label' => 'Profil', 'renderer' => function($row) {
                                return '<a href="index.php?router=membre-profil&id='.$row['id_user'].'" class="btn-sm btn-secondary" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                            <i class="fa-solid fa-user"></i>
                                        </a>';
                            }, 'sortType' => 'none']
                        ];
                        
                        TableView::render($memColumns, $members, 'membersTable');
                        ?>
                    </section>
                </div>
                
                <script>
                    $('#memberSearch').on('keyup', function() {
                        var value = $(this).val().toLowerCase();
                        $("#membersTable tbody tr").filter(function() {
                            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                        });
                    });

                    // Search for teams
                    $('#statsSearch').on('keyup', function() {
                        var value = $(this).val().toLowerCase();
                        $("#teamsTable tbody tr").filter(function() {
                            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                        });
                    });

                    // Custom Sorting Function
                    function sortTable(tableId, n, type) {
                        var table, rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
                        table = document.getElementById(tableId);
                        switching = true;
                        dir = "asc"; 
                        
                        // Icon management
                        $(table).find('th i').removeClass('fa-sort-up fa-sort-down').addClass('fa-sort');
                        var th = table.getElementsByTagName("TH")[n];
                        
                        while (switching) {
                            switching = false;
                            rows = table.rows;
                            
                            for (i = 1; i < (rows.length - 1); i++) {
                                shouldSwitch = false;
                                x = rows[i].getElementsByTagName("TD")[n];
                                y = rows[i + 1].getElementsByTagName("TD")[n];
                                
                                var xVal = $(x).text().trim();
                                var yVal = $(y).text().trim();
                                
                                // Extract custom values if present (data-value)
                                if($(x).find('[data-value]').length > 0) xVal = $(x).find('[data-value]').data('value');
                                if($(y).find('[data-value]').length > 0) yVal = $(y).find('[data-value]').data('value');

                                if (type == 'grade') {
                                    xVal = getGradeRank(xVal);
                                    yVal = getGradeRank(yVal);
                                } else if (type == 'role') {
                                    xVal = getRoleRank(xVal);
                                    yVal = getRoleRank(yVal);
                                } else if (type == 'number') {
                                    xVal = parseFloat(xVal) || 0;
                                    yVal = parseFloat(yVal) || 0;
                                } else if (type == 'none') {
                                    continue;
                                } else {
                                    xVal = xVal.toLowerCase();
                                    yVal = yVal.toLowerCase();
                                }

                                if (dir == "asc") {
                                    if (xVal > yVal) { shouldSwitch = true; break; }
                                } else if (dir == "desc") {
                                    if (xVal < yVal) { shouldSwitch = true; break; }
                                }
                            }
                            
                            if (shouldSwitch) {
                                rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
                                switching = true;
                                switchcount ++; 
                            } else {
                                if (switchcount == 0 && dir == "asc") {
                                    dir = "desc";
                                    switching = true;
                                }
                            }
                        }
                        
                        // Update Icon
                        var icon = $(th).find('i');
                        icon.removeClass('fa-sort');
                        if(dir == "asc") icon.addClass('fa-sort-up');
                        else icon.addClass('fa-sort-down');
                    }

                    // Helper: Get Rank for Grades (Lower number = Higher Importance)
                    function getGradeRank(grade) {
                        grade = grade.toLowerCase();
                        if (grade.includes('prof')) return 1;
                        if (grade.includes('mca')) return 2;
                        if (grade.includes('mcb')) return 3;
                        if (grade.includes('maa')) return 4;
                        if (grade.includes('mab')) return 5;
                        if (grade.includes('doctorant')) return 6;
                        return 99; // Unknown
                    }

                    // Helper: Get Rank for Roles
                    function getRoleRank(role) {
                        role = role.toLowerCase();
                        if (role.includes('chef')) return 1;
                        if (role.includes('admin')) return 2;
                        if (role.includes('enseignant')) return 3;
                        if (role.includes('chercheur')) return 4;
                        if (role.includes('membre')) return 5;
                        if (role.includes('doctorant')) return 6;
                        if (role.includes('etudiant')) return 7;
                        return 99;
                    }
                </script>
                
                <?php $common->footer(); ?>
            </body>
        </html>
        <?php
    }
    
    public function afficherMembreProfil($member, $publications, $projects) {
        $common = new commonViews();
        
        // Privacy Check Simulation 
        // In a real implementation: if (!$member['is_public'] && !isActiveUser($member['id'])) ...
        $isPublic = true; // Defaulting to true as requested
        
        ?>
        <!DOCTYPE html>
        <html lang="fr">
            <?php $this->entetePage(); ?>
            <body>
                <?php $common->navBar(); ?>
                
                <?php if ($isPublic): ?>
                    <div class="member-profile-container" style="max-width: 1000px; margin: 40px auto; padding: 20px;">
                        <a href="javascript:history.back()" class="back-link btn-sm btn-secondary" style="margin-bottom: 20px; display:inline-block;">
                            <i class="fa-solid fa-arrow-left"></i> Retour
                        </a>
                        
                        <div class="profile-card" style="display:flex; gap:30px; background:white; padding:30px; border-radius:15px; box-shadow:0 10px 25px rgba(0,0,0,0.05);">
                            <div class="profile-left" style="flex:0 0 250px; text-align:center;">
                                <div class="profile-photo-wrapper" style="width:200px; height:200px; margin:0 auto 20px; position:relative;">
                                    <img src="<?= !empty($member['photo']) ? $member['photo'] : 'View/assets/default_avatar.png' ?>" 
                                         style="width:100%; height:100%; object-fit:cover; border-radius:50%; border:5px solid #fff; box-shadow:0 5px 15px rgba(0,0,0,0.1);">
                                </div>
                                <h2 style="margin:0; color:#2d3748;"><?= htmlspecialchars($member['prenom'] . ' ' . $member['nom']) ?></h2>
                                <p style="color:#718096; font-weight:500; margin-top:5px;"><?= htmlspecialchars($member['grade']) ?></p>
                                
                                <div class="contact-mini" style="margin-top:20px; text-align:left; background:#f7fafc; padding:15px; border-radius:8px;">
                                    <p style="margin:5px 0;"><i class="fa-solid fa-envelope" style="color:#667eea; width:20px;"></i> <?= htmlspecialchars($member['email']) ?></p>
                                    <?php if(!empty($member['telephone'])): ?>
                                        <p style="margin:5px 0;"><i class="fa-solid fa-phone" style="color:#667eea; width:20px;"></i> <?= htmlspecialchars($member['telephone']) ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="profile-right" style="flex:1;">
                                <?php if (!empty($member['biographie'])): ?>
                                    <div class="bio-section" style="margin-bottom:30px;">
                                        <h3 style="border-bottom:2px solid #e2e8f0; padding-bottom:10px; margin-bottom:15px; color:#4a5568;">Biographie</h3>
                                        <p style="line-height:1.6; color:#4a5568;"><?= nl2br(htmlspecialchars($member['biographie'])) ?></p>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="publications-section">
                                    <h3 style="border-bottom:2px solid #e2e8f0; padding-bottom:10px; margin-bottom:15px; color:#4a5568;">
                                        Publications Récentes
                                    </h3>
                                    <?php
                                    if (!empty($publications)) {
                                         $pubCols = [
                                            'titre' => ['label' => 'Titre', 'renderer' => function($r) {
                                                return '<strong>'.htmlspecialchars($r['titre']).'</strong><br><small style="color:#718096;">'.htmlspecialchars($r['type']).'</small>';
                                            }],
                                            'date' => ['label' => 'Année', 'renderer' => function($r) { return date('Y', strtotime($r['date_publication'])); }]
                                         ];
                                         TableView::render($pubCols, $publications, 'pubTable', 'generic-table simple-table');
                                    } else {
                                        echo '<p style="color:#a0aec0; font-style:italic;">Aucune publication publique disponible.</p>';
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="container" style="text-align:center; padding:100px 20px;">
                        <i class="fa-solid fa-lock" style="font-size:50px; color:#cbd5e0; margin-bottom:20px;"></i>
                        <h2>Profil Privé</h2>
                        <p>Ce profil n'est pas accessible publiquement.</p>
                        <a href="index.php?router=equipes" class="btn btn-primary">Retour aux équipes</a>
                    </div>
                <?php endif; ?>
                
                <?php $common->footer(); ?>
            </body>
        </html>
        <?php
    }
}
?>
