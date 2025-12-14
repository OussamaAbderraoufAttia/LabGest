<?php
require_once("commonViews.php");

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
                    
                    <div class="equipments-grid">
                        <?php if (empty($equipments)): ?>
                            <p class="no-results">Aucun équipement disponible.</p>
                        <?php else: ?>
                            <?php foreach ($equipments as $equip): ?>
                                <div class="equipment-card fade-in-up">
                                    <div class="equip-icon">
                                        <i class="fa-solid fa-laptop"></i>
                                    </div>
                                    
                                    <h3><?= htmlspecialchars($equip['nom']) ?></h3>
                                    
                                    <div class="equip-details">
                                        <p><strong>Type:</strong> <?= htmlspecialchars($equip['type']) ?></p>
                                        <p><strong>Localisation:</strong> <?= htmlspecialchars($equip['localisation']) ?></p>
                                        <p class="equip-status status-<?= $equip['etat'] ?>">
                                            <i class="fa-solid fa-circle"></i> 
                                            <?= ucfirst($equip['etat']) ?>
                                        </p>
                                    </div>
                                    
                                    <?php if ($equip['description']): ?>
                                        <p class="equip-description"><?= htmlspecialchars($equip['description']) ?></p>
                                    <?php endif; ?>
                                    
                                    <?php if ($equip['etat'] === 'disponible'): ?>
                                        <button class="btn-primary" onclick="openReservationModal(<?= $equip['id_equip'] ?>, '<?= htmlspecialchars($equip['nom']) ?>')">
                                            <i class="fa-solid fa-calendar-check"></i> Réserver
                                        </button>
                                    <?php else: ?>
                                        <button class="btn-secondary" disabled>
                                            <i class="fa-solid fa-ban"></i> Indisponible
                                        </button>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
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
                    
                    <div class="reservations-list">
                        <?php if (empty($reservations)): ?>
                            <p class="no-results">Aucune réservation.</p>
                        <?php else: ?>
                            <?php foreach ($reservations as $res): ?>
                                <div class="reservation-card">
                                    <h3><?= htmlspecialchars($res['equip_nom']) ?></h3>
                                    <p><strong>Type:</strong> <?= htmlspecialchars($res['equip_type']) ?></p>
                                    <p><strong>Du:</strong> <?= date('d/m/Y H:i', strtotime($res['date_debut'])) ?></p>
                                    <p><strong>Au:</strong> <?= date('d/m/Y H:i', strtotime($res['date_fin'])) ?></p>
                                    <span class="status-badge status-<?= $res['status'] ?>"><?= ucfirst($res['status']) ?></span>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
                
                <?php $common->footer(); ?>
            </body>
        </html>
        <?php
    }
}
?>
