<?php
require_once("commonViews.php");
require_once("View/Components/CardView.php");
require_once("View/Components/TableView.php");

class equipmentsView {
    
    public function entetePage() {
        ?>
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Équipements - LRE</title>
            <link rel="stylesheet" href="View/css/commonStyles.css">
            <link rel="stylesheet" href="View/css/equipmentsStyle.css">
            <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />
        </head>
        <?php
    }
    
    public function afficherListe($equipments) {
        $common = new commonViews();
        ?>
        <!DOCTYPE html>
        <html lang="fr">
            <?php $this->entetePage(); ?>
            <body>
                <?php $common->navBar(); ?>
                
                <div class="equipments-container">
                    <h1 class="page-title">Équipements et Ressources</h1>
                    
                    <?php
                    $statusLabels = [
                        'disponible' => 'Disponible',
                        'reserve' => 'Réservé',
                        'en_maintenance' => 'En Maintenance',
                        'hors_service' => 'Hors Service'
                    ];
                    
                    CardView::render($equipments, function($equip) use ($statusLabels) {
                        $statusLabel = $statusLabels[$equip['etat']] ?? ucfirst($equip['etat']);
                        $reserveBtn = ($equip['etat'] === 'disponible') 
                            ? '<button class="btn-primary" onclick="openReservationModal(' . $equip['id_equip'] . ', \'' . htmlspecialchars($equip['nom'], ENT_QUOTES) . '\')">
                                    <i class="fa-solid fa-calendar-check"></i> Réserver
                               </button>'
                            : '<button class="btn-secondary" disabled>
                                    <i class="fa-solid fa-ban"></i> Indisponible
                               </button>';
                        
                        return '
                            <div class="equipment-card fade-in-up">
                                <div class="equip-icon">
                                    <i class="fa-solid fa-laptop"></i>
                                </div>
                                
                                <h3>' . htmlspecialchars($equip['nom']) . '</h3>
                                
                                <div class="equip-details">
                                    <p><strong>Type:</strong> ' . htmlspecialchars($equip['type']) . '</p>
                                    <p><strong>Localisation:</strong> ' . htmlspecialchars($equip['localisation']) . '</p>
                                    <p class="equip-status status-' . $equip['etat'] . '">
                                        <i class="fa-solid fa-circle"></i> ' . $statusLabel . '
                                    </p>
                                </div>
                                
                                ' . (!empty($equip['description']) ? '<p class="equip-description">' . htmlspecialchars($equip['description']) . '</p>' : '') . '
                                
                                ' . $reserveBtn . '
                            </div>
                        ';
                    }, 'equipmentsGrid', 'equipments-grid');
                    ?>
                </div>
                
                <!-- Reservation Modal -->
                <div id="reservationModal" class="modal">
                    <div class="modal-content">
                        <span class="close" onclick="closeReservationModal()">&times;</span>
                        <h2>Réserver un équipement</h2>
                        <p id="equipmentName" style="margin-bottom: 1.5rem; color: #667eea; font-weight: 600;"></p>
                        
                        <form method="POST" action="index.php?router=reserver-equipement">
                            <input type="hidden" id="equipId" name="equip_id">
                            
                            <div class="form-group">
                                <label>Date de début:</label>
                                <input type="datetime-local" name="date_debut" required>
                            </div>
                            
                            <div class="form-group">
                                <label>Date de fin:</label>
                                <input type="datetime-local" name="date_fin" required>
                            </div>
                            
                            <button type="submit" class="btn-primary">
                                <i class="fa-solid fa-check"></i> Confirmer la réservation
                            </button>
                        </form>
                    </div>
                </div>
                
                <script>
                    function openReservationModal(equipId, equipName) {
                        document.getElementById('equipId').value = equipId;
                        document.getElementById('equipmentName').textContent = equipName;
                        document.getElementById('reservationModal').style.display = 'block';
                    }
                    
                    function closeReservationModal() {
                        document.getElementById('reservationModal').style.display = 'none';
                    }
                    
                    // Close modal when clicking outside
                    window.onclick = function(event) {
                        const modal = document.getElementById('reservationModal');
                        if (event.target === modal) {
                            modal.style.display = 'none';
                        }
                    }
                </script>
                
                <?php $common->footer(); ?>
            </body>
        </html>
        <?php
    }
    
    public function afficherMesReservations($reservations) {
        $common = new commonViews();
        ?>
        <!DOCTYPE html>
        <html lang="fr">
            <?php $this->entetePage(); ?>
            <body>
                <?php $common->navBar(); ?>
                
                <div class="reservations-container">
                    <h1 class="page-title">Mes Réservations</h1>
                    
                    <?php
                    $statusLabels = [
                        'confirme' => 'Confirmé',
                        'annule' => 'Annulé',
                        'en_attente' => 'En Attente',
                        'refuse' => 'Refusé',
                        'termine' => 'Terminé'
                    ];
                    
                    $columns = [
                        'equip_nom' => ['label' => 'Équipement', 'renderer' => function($row) {
                            return '<strong>' . htmlspecialchars($row['equip_nom']) . '</strong><br><small style="color:#718096;">' . htmlspecialchars($row['equip_type']) . '</small>';
                        }],
                        'date_debut' => ['label' => 'Du', 'renderer' => function($row) {
                            return date('d/m/Y H:i', strtotime($row['date_debut']));
                        }],
                        'date_fin' => ['label' => 'Au', 'renderer' => function($row) {
                            return date('d/m/Y H:i', strtotime($row['date_fin']));
                        }],
                        'status' => ['label' => 'Statut', 'renderer' => function($row) use ($statusLabels) {
                            $statusColors = [
                                'confirme' => '#48bb78',
                                'annule' => '#e53e3e',
                                'en_attente' => '#ecc94b',
                                'refuse' => '#f6ad55',
                                'termine' => '#a0aec0'
                            ];
                            $statusLabel = $statusLabels[$row['status']] ?? ucfirst($row['status']);
                            $color = $statusColors[$row['status']] ?? '#cbd5e0';
                            return '<span class="status-badge" style="background:' . $color . '; color:white; padding:4px 8px; border-radius:4px;">' . $statusLabel . '</span>';
                        }]
                    ];
                    
                    TableView::render($columns, $reservations, 'reservationsTable', 'reservations-list');
                    ?>
                </div>
                
                <?php $common->footer(); ?>
            </body>
        </html>
        <?php
    }
}
?>
