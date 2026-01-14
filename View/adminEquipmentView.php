<?php
require_once("View/Components/TableView.php");
require_once("View/adminDashboardView.php");

class adminEquipmentView extends adminDashboardView {
    
    public function afficherGestionEquipements($equipments, $reservations) {
        $this->header();
        ?>
        <body>
            <div class="admin-container">
                <?php $this->sidebar('equipments'); ?>
                
                <main class="admin-content">
                    <?php $this->topBar('Gestion des Équipements & Ressources'); ?>
                    
                    <div class="admin-tabs" style="margin-bottom: 20px; border-bottom: 2px solid #e2e8f0;">
                        <button class="tab-btn active" onclick="switchTab('inventory')" style="padding: 10px 20px; border: none; background: none; border-bottom: 3px solid #667eea; color: #667eea; font-weight: bold; cursor: pointer;">Inventaire</button>
                        <button class="tab-btn" onclick="switchTab('reservations')" style="padding: 10px 20px; border: none; background: none; color: #718096; font-weight: bold; cursor: pointer;">Réservations</button>
                    </div>
                    
                    <!-- INVENTORY TAB -->
                    <div id="tab-inventory">
                        <div class="admin-actions" style="margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
                            <a href="index.php?router=admin_report_equipment_pdf" class="btn btn-success" style="background: #48bb78; color: white; padding: 10px 15px; border-radius: 8px; text-decoration: none; display: flex; align-items: center; gap: 8px;">
                                <i class="fa-solid fa-file-pdf"></i> Rapport d'Utilisation
                            </a>
                            <button class="btn btn-primary" onclick="openEquipModal('add')">
                                <i class="fa-solid fa-plus"></i> Ajouter un Équipement
                            </button>
                        </div>
                        
                        <div class="admin-card" style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
                            <?php
                            $columns = [
                                'id_equip' => ['label' => 'ID', 'sortable' => true],
                                'nom' => ['label' => 'Nom', 'sortable' => true],
                                'type' => ['label' => 'Type', 'sortable' => true],
                                'etat' => ['label' => 'État', 'renderer' => function($row) {
                                    $colors = [
                                        'disponible' => '#48bb78', 
                                        'reserve' => '#ecc94b', 
                                        'en_maintenance' => '#f6ad55',
                                        'hors_service' => '#e53e3e'
                                    ];
                                    $labels = [
                                        'disponible' => 'Disponible',
                                        'reserve' => 'Réservé',
                                        'en_maintenance' => 'En Maintenance',
                                        'hors_service' => 'Hors Service'
                                    ];
                                    $color = $colors[$row['etat']] ?? '#cbd5e0';
                                    $label = $labels[$row['etat']] ?? ucfirst($row['etat']);
                                    return "<span style='padding: 4px 8px; border-radius: 4px; color:white; font-size: 0.8rem; font-weight: 600; background:$color'>" . $label . "</span>";
                                }],
                                'actions' => ['label' => 'Actions', 'renderer' => function($row) {
                                    $rowJson = htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8');
                                    return '
                                    <button class="btn-icon" style="color: #667eea; margin-right: 10px;" onclick=\'editEquip('.$rowJson.')\' title="Modifier">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                    <form method="post" action="index.php?router=admin-equipments" style="display:inline;" onsubmit="return confirm(\'Supprimer cet équipement ?\')">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id_equip" value="'.$row['id_equip'].'">
                                        <button type="submit" class="btn-icon" style="color: #e53e3e;" title="Supprimer">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>';
                                }]
                            ];
                            TableView::render($columns, $equipments, 'equipTable', 'admin-table');
                            ?>
                        </div>
                    </div>

                    <!-- RESERVATIONS TAB -->
                    <div id="tab-reservations" style="display:none;">
                        <div class="admin-card" style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
                            <h3>Historique des Réservations</h3>
                            <?php
                            $resColumns = [
                                'id_reservation' => ['label' => 'Ref', 'sortable' => true],
                                'equip_nom' => ['label' => 'Équipement', 'sortable' => true],
                                'user' => ['label' => 'Utilisateur', 'renderer' => function($row) {
                                    return htmlspecialchars($row['user_nom'] . ' ' . $row['user_prenom']);
                                }],
                                'dates' => ['label' => 'Période', 'renderer' => function($row) {
                                    return "<small>" . $row['date_debut'] . " au " . $row['date_fin'] . "</small>";
                                }],
                                'status' => ['label' => 'Statut', 'renderer' => function($row) {
                                    $colors = [
                                        'confirme' => '#48bb78', 
                                        'annule' => '#e53e3e', 
                                        'en_attente' => '#ecc94b',
                                        'refuse' => '#f6ad55',
                                        'termine' => '#a0aec0'
                                    ];
                                    $labels = [
                                        'confirme' => 'Confirmé',
                                        'annule' => 'Annulé',
                                        'en_attente' => 'En Attente',
                                        'refuse' => 'Refusé',
                                        'termine' => 'Terminé'
                                    ];
                                    $status = $row['status'] ?? 'inconnu';
                                    $color = $colors[$status] ?? '#cbd5e0';
                                    $label = $labels[$status] ?? ucfirst(str_replace('_', ' ', $status));
                                    return "<span style='color:".$color."'>".$label."</span>";
                                }],
                                'actions' => ['label' => 'Actions', 'renderer' => function($row) {
                                    if ($row['status'] == 'en_attente' || $row['status'] == 'confirme') {
                                        return '
                                        <form method="post" action="index.php?router=admin-equipments" style="display:inline;">
                                            <input type="hidden" name="action" value="update_res">
                                            <input type="hidden" name="id_res" value="'.$row['id_reservation'].'">
                                            <input type="hidden" name="status" value="annule">
                                            <button type="submit" class="btn-icon" style="color: #e53e3e;" title="Annuler">
                                                <i class="fa-solid fa-xmark"></i>
                                            </button>
                                        </form>';
                                    }
                                    return '';
                                }]
                            ];
                            TableView::render($resColumns, $reservations, 'resTable', 'admin-table');
                            ?>
                        </div>
                    </div>
                </main>
            </div>
            
            <!-- Modal -->
            <div id="equipModal" class="modal-overlay" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:2000; justify-content:center; align-items:center;">
                <div style="background:white; padding:30px; border-radius:12px; width:500px; max-width:90%;">
                    <h3 id="modalTitle" style="margin-top:0;">Nouvel Équipement</h3>
                    <form id="equipForm" method="post" action="index.php?router=admin-equipments" enctype="multipart/form-data">
                        <input type="hidden" name="action" id="formAction" value="add">
                        <input type="hidden" name="id_equip" id="equipId">
                        
                        <div class="form-group" style="margin-bottom:15px;">
                            <label>Nom</label>
                            <input type="text" name="nom" id="nom" required class="form-control" style="width:100%; padding:8px; margin-top:5px;">
                        </div>
                        
                        <div class="form-group" style="margin-bottom:15px;">
                            <label>Type</label>
                            <input type="text" name="type" id="type" required class="form-control" style="width:100%; padding:8px; margin-top:5px;">
                        </div>
                        
                        <div class="form-group" style="margin-bottom:15px;">
                            <label>Description</label>
                            <textarea name="description" id="description" class="form-control" style="width:100%; padding:8px; margin-top:5px;"></textarea>
                        </div>
                        
                        <div class="form-group" style="margin-bottom:15px;">
                            <label>État</label>
                            <select name="etat" id="etat" class="form-control" style="width:100%; padding:8px; margin-top:5px;">
                                <option value="disponible">Disponible</option>
                                <option value="en_maintenance">En Maintenance</option>
                                <option value="hors_service">Hors Service</option>
                                <option value="reserve">Réservé</option>
                            </select>
                        </div>
                        
                        <div class="form-group" style="margin-bottom:15px;">
                            <label>Image</label>
                            <input type="file" name="image" id="image" class="form-control">
                        </div>
                        
                        <div style="text-align:right; margin-top:20px;">
                            <button type="button" onclick="document.getElementById('equipModal').style.display='none'" class="btn btn-secondary">Annuler</button>
                            <button type="submit" class="btn btn-primary">Enregistrer</button>
                        </div>
                    </form>
                </div>
            </div>
            
            <script>
                function switchTab(tab) {
                    document.querySelectorAll('.tab-btn').forEach(b => {
                        b.style.borderBottom = 'none';
                        b.style.color = '#718096';
                    });
                    document.getElementById('tab-inventory').style.display = 'none';
                    document.getElementById('tab-reservations').style.display = 'none';
                    
                    if(tab === 'inventory') {
                        document.getElementById('tab-inventory').style.display = 'block';
                        event.target.style.borderBottom = '3px solid #667eea';
                        event.target.style.color = '#667eea';
                    } else {
                        document.getElementById('tab-reservations').style.display = 'block';
                        event.target.style.borderBottom = '3px solid #667eea';
                        event.target.style.color = '#667eea';
                    }
                }
                
                function openEquipModal(mode) {
                    document.getElementById('equipModal').style.display = 'flex';
                    if(mode === 'add') {
                        document.getElementById('modalTitle').innerText = 'Nouvel Équipement';
                        document.getElementById('formAction').value = 'add';
                        document.getElementById('equipForm').reset();
                        document.getElementById('equipId').value = '';
                    }
                }
                
                function editEquip(equip) {
                    openEquipModal('edit');
                    document.getElementById('modalTitle').innerText = 'Modifier Équipement';
                    document.getElementById('formAction').value = 'edit';
                    document.getElementById('equipId').value = equip.id_equip;
                    document.getElementById('nom').value = equip.nom;
                    document.getElementById('type').value = equip.type;
                    document.getElementById('description').value = equip.description;
                    document.getElementById('etat').value = equip.etat;
                }
            </script>
        </body>
        </html>
        <?php
    }
}
?>
