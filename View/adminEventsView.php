<?php
require_once("View/Components/TableView.php");
require_once("View/adminDashboardView.php");

class adminEventsView extends adminDashboardView {
    
    public function afficherGestionEvenements($events, $offers) {
        $this->header();
        ?>
        <body>
            <div class="admin-container">
                <?php $this->sidebar('events'); ?>
                
                <main class="admin-content">
                    <?php $this->topBar('Gestion des Événements & Communications'); ?>
                    
                    <div class="admin-tabs" style="margin-bottom: 20px; border-bottom: 2px solid #e2e8f0;">
                        <button class="tab-btn active" onclick="switchTab('events')" style="padding: 10px 20px; border: none; background: none; border-bottom: 3px solid #667eea; color: #667eea; font-weight: bold; cursor: pointer;">Événements</button>
                        <button class="tab-btn" onclick="switchTab('offers')" style="padding: 10px 20px; border: none; background: none; color: #718096; font-weight: bold; cursor: pointer;">Offres & Opportunités</button>
                    </div>

                    <!-- EVENTS TAB -->
                    <div id="tab-events">
                        <div class="admin-actions" style="margin-bottom: 20px; display: flex; justify-content: flex-end;">
                            <button class="btn btn-primary" onclick="openEventModal()">
                                <i class="fa-solid fa-plus"></i> Publicer un Événement
                            </button>
                        </div>
                        
                        <div class="admin-card" style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
                            <?php
                            $colsEvents = [
                                'titre' => ['label' => 'Titre', 'sortable' => true],
                                'type' => ['label' => 'Type', 'renderer' => function($row) { return "<span class='badge'>".$row['type']."</span>"; }],
                                'date_event' => ['label' => 'Date', 'sortable' => true],
                                'lieu' => ['label' => 'Lieu'],
                                'actions' => ['label' => 'Actions', 'renderer' => function($row) {
                                    return '
                                    <form method="post" action="index.php?router=admin-events" style="display:inline;" onsubmit="return confirm(\'Supprimer cet événement ?\')">
                                        <input type="hidden" name="action" value="delete_event">
                                        <input type="hidden" name="id_event" value="'.$row['id_event'].'">
                                        <button type="submit" class="btn-icon" style="color: #e53e3e;" title="Supprimer">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>';
                                }]
                            ];
                            TableView::render($colsEvents, $events, 'eventsTable', 'admin-table');
                            ?>
                        </div>
                    </div>

                    <!-- OFFERS TAB -->
                    <div id="tab-offers" style="display:none;">
                        <div class="admin-actions" style="margin-bottom: 20px; display: flex; justify-content: flex-end;">
                            <button class="btn btn-primary" onclick="openOfferModal()">
                                <i class="fa-solid fa-briefcase"></i> Ajouter une Offre
                            </button>
                        </div>
                        
                        <div class="admin-card" style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
                            <?php
                            $colsOffers = [
                                'titre' => ['label' => 'Intitulé', 'sortable' => true],
                                'type' => ['label' => 'Type', 'sortable' => true],
                                'date_limite' => ['label' => 'Date Limite', 'sortable' => true],
                                'actions' => ['label' => 'Actions', 'renderer' => function($row) {
                                    return '
                                    <form method="post" action="index.php?router=admin-events" style="display:inline;">
                                        <input type="hidden" name="action" value="delete_offer">
                                        <input type="hidden" name="id_offer" value="'.$row['id_offer'].'">
                                        <button type="submit" class="btn-icon" style="color: #e53e3e;" title="Supprimer">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>';
                                }]
                            ];
                            TableView::render($colsOffers, $offers, 'offersTable', 'admin-table');
                            ?>
                        </div>
                    </div>
                </main>
            </div>
            
            <!-- Event Modal -->
            <div id="eventModal" class="modal-overlay" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:2000; justify-content:center; align-items:center;">
                <div style="background:white; padding:30px; border-radius:12px; width:500px; max-width:90%;">
                    <h3 style="margin-top:0;">Publier un Événement</h3>
                    <form method="post" action="index.php?router=admin-events" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="add_event">
                        <input type="text" name="titre" placeholder="Titre de l'événement" required class="form-control" style="width:100%; margin-bottom:10px;">
                        <textarea name="description" placeholder="Description..." required class="form-control" style="width:100%; height:100px; margin-bottom:10px;"></textarea>
                        
                        <div class="form-row" style="display:flex; gap:10px; margin-bottom:10px;">
                            <input type="date" name="date_event" required class="form-control" style="flex:1;">
                            <select name="type" class="form-control" style="flex:1;">
                                <option value="conference">Conférence</option>
                                <option value="atelier">Atelier</option>
                                <option value="seminaire">Séminaire</option>
                                <option value="soutenance">Soutenance</option>
                                <option value="reunion">Réunion</option>
                                <option value="hackathon">Hackathon</option>
                            </select>
                        </div>
                        
                        <input type="text" name="lieu" placeholder="Lieu (Salle, Amphi, Online)" required class="form-control" style="width:100%; margin-bottom:10px;">
                        
                        <label>Affiche / Image</label>
                        <input type="file" name="image" class="form-control" style="width:100%; margin-bottom:20px;">
                        
                        <div style="text-align:right;">
                            <button type="button" onclick="document.getElementById('eventModal').style.display='none'" class="btn btn-secondary">Annuler</button>
                            <button type="submit" class="btn btn-primary">Publier</button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Offer Modal -->
            <div id="offerModal" class="modal-overlay" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:2000; justify-content:center; align-items:center;">
                <div style="background:white; padding:30px; border-radius:12px; width:500px; max-width:90%;">
                    <h3 style="margin-top:0;">Ajouter une Offre</h3>
                    <form method="post" action="index.php?router=admin-events">
                        <input type="hidden" name="action" value="add_offer">
                        <input type="text" name="titre" placeholder="Titre de l'offre" required class="form-control" style="width:100%; margin-bottom:10px;">
                        <textarea name="description" placeholder="Description..." required class="form-control" style="width:100%; height:100px; margin-bottom:10px;"></textarea>
                        
                        <div class="form-row" style="display:flex; gap:10px; margin-bottom:10px;">
                            <input type="date" name="date_limite" required class="form-control" style="flex:1;">
                            <select name="type" class="form-control" style="flex:1;">
                                <option value="stage">Stage</option>
                                <option value="these">Thèse</option>
                                <option value="bourse">Bourse</option>
                                <option value="collaboration">Collaboration</option>
                            </select>
                        </div>
                        
                        <input type="text" name="lien_postuler" placeholder="Lien pour postuler (Email ou URL)" required class="form-control" style="width:100%; margin-bottom:20px;">
                        
                        <div style="text-align:right;">
                            <button type="button" onclick="document.getElementById('offerModal').style.display='none'" class="btn btn-secondary">Annuler</button>
                            <button type="submit" class="btn btn-primary">Ajouter</button>
                        </div>
                    </form>
                </div>
            </div>
            
            <script>
                function switchTab(tab) {
                    document.getElementById('tab-events').style.display = tab==='events'?'block':'none';
                    document.getElementById('tab-offers').style.display = tab==='offers'?'block':'none';
                    
                    document.querySelectorAll('.tab-btn').forEach(b => {
                        b.style.borderBottom = 'none';
                        b.style.color = '#718096';
                    });
                    event.target.style.borderBottom = '3px solid #667eea';
                    event.target.style.color = '#667eea';
                }
                function openEventModal() { document.getElementById('eventModal').style.display = 'flex'; }
                function openOfferModal() { document.getElementById('offerModal').style.display = 'flex'; }
            </script>
        </body>
        </html>
        <?php
    }
}
?>
