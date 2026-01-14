<?php
require_once("commonViews.php");
require_once("View/Components/CardView.php");

class eventsView {
    
    public function afficher_page($events) {
        $common = new commonViews();
        ?>
        <!DOCTYPE html>
        <html lang="fr">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Événements - LRE</title>
            <link rel="stylesheet" href="View/css/commonStyles.css">
            <link rel="stylesheet" href="View/css/homeStyle.css"> <!-- Reuse event cards -->
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        </head>
        <body>
            <?php $common->navBar(); ?>
            
            <section class="events-section" style="padding-top: 100px;">
                <div class="container">
                    <h1>Tous les Événements</h1>
                    
                    <div class="filters" style="margin-bottom: 30px;">
                        <input type="text" id="eventSearch" placeholder="Rechercher un événement..." style="padding: 10px; width: 300px; border: 1px solid #ddd; border-radius: 5px;">
                        <select id="eventTypeFilter" style="padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                            <option value="all">Tous les types</option>
                            <option value="conference">Conférence</option>
                            <option value="atelier">Atelier</option>
                            <option value="seminaire">Séminaire</option>
                            <option value="soutenance">Soutenance</option>
                            <option value="reunion">Réunion</option>
                            <option value="hackathon">Hackathon</option>
                        </select>
                    </div>

                    <?php
                    $typeLabels = [
                        'conference' => 'Conférence',
                        'atelier' => 'Atelier',
                        'seminaire' => 'Séminaire',
                        'soutenance' => 'Soutenance',
                        'reunion' => 'Réunion',
                        'hackathon' => 'Hackathon'
                    ];
                    
                    CardView::render($events, function($event) use ($typeLabels) {
                        $typeLabel = $typeLabels[$event['type']] ?? ucfirst($event['type']);
                        $internalBadge = ($event['public_cible'] == 'interne') 
                            ? '<span class="badge-internal" style="background:#ffc107; color:#333; padding:2px 8px; border-radius:10px; font-size:0.8em; margin-left:5px;">Interne</span>'
                            : '';
                        
                        return '
                            <div class="event-card" data-type="' . $event['type'] . '">
                                <div class="event-date">
                                    <div class="day">' . date('d', strtotime($event['date_event'])) . '</div>
                                    <div class="month">' . strtoupper(date('M', strtotime($event['date_event']))) . '</div>
                                </div>
                                <div class="event-content">
                                    <span class="event-type">' . $typeLabel . '</span>
                                    ' . $internalBadge . '
                                    
                                    <h4>' . htmlspecialchars($event['titre']) . '</h4>
                                    <p><i class="fa-solid fa-location-dot"></i> ' . htmlspecialchars($event['lieu']) . '</p>
                                    
                                    <a href="index.php?router=event_details&id=' . $event['id_event'] . '" class="btn-primary" style="margin-top:10px; display:inline-block; font-size:0.9em; padding: 8px 15px;">Détails</a>
                                </div>
                            </div>
                        ';
                    }, 'eventsGrid', 'events-grid');
                    ?>
                </div>
            </section>

            <?php $common->footer(); ?>

            <script>
                document.getElementById('eventTypeFilter').addEventListener('change', function() {
                    let type = this.value;
                    let cards = document.querySelectorAll('.event-card');
                    cards.forEach(card => {
                        if (type === 'all' || card.dataset.type === type) card.style.display = 'block';
                        else card.style.display = 'none';
                    });
                });
            </script>
        </body>
        </html>
        <?php
    }

    public function afficher_details($event) {
        $common = new commonViews();
        $isLoggedIn = isset($_SESSION['user_id']);
        ?>
        <!DOCTYPE html>
        <html lang="fr">
        <head>
            <meta charset="UTF-8">
            <title><?php echo htmlspecialchars($event['titre']); ?> - Détails</title>
            <link rel="stylesheet" href="View/css/commonStyles.css">
            <style>
                .event-header {
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    color: white;
                    padding: 80px 0 40px;
                    text-align: center;
                }
                .event-container {
                    max-width: 800px;
                    margin: 40px auto;
                    padding: 20px;
                    background: white;
                    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
                    border-radius: 10px;
                }
                .modal {
                    display: none;
                    position: fixed;
                    top: 0; left: 0; width: 100%; height: 100%;
                    background: rgba(0,0,0,0.5);
                    justify-content: center;
                    align-items: center;
                }
                .modal-content {
                    background: white;
                    padding: 30px;
                    border-radius: 10px;
                    width: 400px;
                    text-align: center;
                }
            </style>
        </head>
        <body>
            <?php $common->navBar(); ?>
            
            <div class="event-header">
                <h1><?php echo htmlspecialchars($event['titre']); ?></h1>
                <p><i class="fa-solid fa-calendar"></i> <?php echo date('d F Y à H:i', strtotime($event['date_event'])); ?></p>
                <p><i class="fa-solid fa-map-marker-alt"></i> <?php echo htmlspecialchars($event['lieu']); ?></p>
            </div>

            <div class="event-container">
                <img src="<?php echo $event['image_url']; ?>" alt="Event" style="width:100%; height:300px; object-fit:cover; border-radius:10px; margin-bottom:20px;">
                <h3>Description</h3>
                <p><?php echo nl2br(htmlspecialchars($event['description'])); ?></p>
                
                <hr style="margin: 30px 0; border: 0; border-top: 1px solid #eee;">
                
                <h3 style="margin-bottom: 20px;">Inscription</h3>
                
                <?php if (isset($_GET['success'])): ?>
                    <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
                        Inscription réussie ! Vous recevrez une confirmation bientôt.
                    </div>
                <?php else: ?>
                    <button onclick="openModal()" class="btn-primary" style="width: 100%; font-size: 1.2em;">S'inscrire à l'événement</button>
                <?php endif; ?>

                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                    <div style="margin-top: 30px; padding-top: 30px; border-top: 1px solid #eee;">
                        <h3>Actions Admin</h3>
                        <a href="index.php?router=event_inscrits&id=<?php echo $event['id_event']; ?>" 
                           class="btn-primary" style="display: inline-block; padding: 10px 20px; background-color: #28a745; text-decoration: none; border-radius: 5px; color: white;">
                            <i class="fas fa-list"></i> Voir la liste des participants
                        </a>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Registration Modal -->
            <div id="regModal" class="modal">
                <div class="modal-content">
                    <span onclick="closeModal()" style="float:right; cursor:pointer; font-size:1.5em;">&times;</span>
                    <h2>Inscription</h2>
                    <form action="index.php?router=event_register" method="POST">
                        <input type="hidden" name="event_id" value="<?php echo $event['id_event']; ?>">
                        
                        <?php if (!$isLoggedIn): ?>
                            <input type="text" name="nom" placeholder="Nom complet" required style="width:100%; margin:10px 0; padding:10px;">
                            <input type="email" name="email" placeholder="Email" required style="width:100%; margin:10px 0; padding:10px;">
                            <input type="text" name="telephone" placeholder="Téléphone" style="width:100%; margin:10px 0; padding:10px;">
                        <?php else: ?>
                            <p>Connecté en tant que membre.</p>
                        <?php endif; ?>
                        
                        <textarea name="motivation" placeholder="Votre motivation (optionnel)" style="width:100%; margin:10px 0; padding:10px;"></textarea>
                        <button type="submit" class="btn-primary">Confirmer l'inscription</button>
                    </form>
                </div>
            </div>

            <?php $common->footer(); ?>

            <script>
                function openModal() { document.getElementById('regModal').style.display = 'flex'; }
                function closeModal() { document.getElementById('regModal').style.display = 'none'; }
                
                window.onclick = function(event) {
                    if (event.target == document.getElementById('regModal')) {
                        closeModal();
                    }
                }
            </script>
        </body>
        </html>
        <?php
    }

    public function afficher_inscrits($event, $registrations, $count) {
        $common = new commonViews();
        ?>
        <!DOCTYPE html>
        <html lang="fr">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Participants - <?php echo htmlspecialchars($event['titre']); ?></title>
            <link rel="stylesheet" href="View/css/commonStyles.css">
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
            <style>
                .registrations-container {
                    max-width: 1000px;
                    margin: 100px auto 40px;
                    padding: 20px;
                }
                .event-header {
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    color: white;
                    padding: 30px;
                    border-radius: 10px;
                    margin-bottom: 30px;
                }
                .event-header h1 {
                    margin: 0 0 10px 0;
                }
                .registration-count {
                    font-size: 14px;
                    opacity: 0.9;
                }
                .registrations-table {
                    width: 100%;
                    border-collapse: collapse;
                    background: white;
                    border-radius: 10px;
                    overflow: hidden;
                    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
                }
                .registrations-table thead {
                    background-color: #f8f9fa;
                    border-bottom: 2px solid #dee2e6;
                }
                .registrations-table th {
                    padding: 15px;
                    text-align: left;
                    font-weight: 600;
                    color: #333;
                }
                .registrations-table td {
                    padding: 15px;
                    border-bottom: 1px solid #dee2e6;
                }
                .registrations-table tbody tr:hover {
                    background-color: #f8f9fa;
                }
                .user-cell {
                    display: flex;
                    align-items: center;
                    gap: 10px;
                }
                .user-avatar {
                    width: 40px;
                    height: 40px;
                    border-radius: 50%;
                    object-fit: cover;
                }
                .user-info h4 {
                    margin: 0;
                    color: #333;
                }
                .user-info p {
                    margin: 3px 0 0 0;
                    font-size: 12px;
                    color: #666;
                }
                .status-badge {
                    display: inline-block;
                    padding: 4px 12px;
                    border-radius: 20px;
                    font-size: 12px;
                    font-weight: 600;
                }
                .status-confirmed {
                    background-color: #d4edda;
                    color: #155724;
                }
                .status-pending {
                    background-color: #fff3cd;
                    color: #856404;
                }
                .no-registrations {
                    text-align: center;
                    padding: 40px;
                    color: #666;
                }
                .back-link {
                    margin-bottom: 20px;
                }
                .back-link a {
                    color: #667eea;
                    text-decoration: none;
                    font-size: 14px;
                }
                .back-link a:hover {
                    text-decoration: underline;
                }
            </style>
        </head>
        <body>
            <?php $common->navBar(); ?>
            
            <div class="registrations-container">
                <div class="back-link">
                    <a href="index.php?router=event_details&id=<?php echo $event['id_event']; ?>"><i class="fas fa-arrow-left"></i> Retour à l'événement</a>
                </div>

                <div class="event-header">
                    <h1><?php echo htmlspecialchars($event['titre']); ?></h1>
                    <div class="registration-count">
                        <i class="fas fa-users"></i> 
                        <?php echo $count; ?> participant(s) inscrit(s)
                    </div>
                </div>

                <?php if (count($registrations) > 0): ?>
                    <table class="registrations-table">
                        <thead>
                            <tr>
                                <th>Participant</th>
                                <th>Email</th>
                                <th>Grade/Poste</th>
                                <th>Date d'inscription</th>
                                <th>Statut</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($registrations as $reg): ?>
                                <tr>
                                    <td>
                                        <div class="user-cell">
                                            <img src="<?php echo htmlspecialchars($reg['photo'] ?? 'View/assets/default_avatar.png'); ?>" 
                                                 alt="<?php echo htmlspecialchars($reg['prenom'] . ' ' . $reg['nom']); ?>" 
                                                 class="user-avatar">
                                            <div class="user-info">
                                                <h4><?php echo htmlspecialchars($reg['prenom'] . ' ' . $reg['nom']); ?></h4>
                                                <p>@<?php echo htmlspecialchars($reg['username']); ?></p>
                                            </div>
                                        </div>
                                    </td>
                                    <td><?php echo htmlspecialchars($reg['email']); ?></td>
                                    <td><?php echo htmlspecialchars($reg['grade'] ?? 'N/A'); ?></td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($reg['registration_date'])); ?></td>
                                    <td>
                                        <span class="status-badge status-<?php echo strtolower($reg['status']); ?>">
                                            <?php 
                                                echo ucfirst($reg['status']);
                                                if ($reg['status'] === 'confirmed') {
                                                    echo ' ✓';
                                                }
                                            ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="no-registrations">
                        <i class="fas fa-inbox" style="font-size: 48px; color: #ccc; margin-bottom: 20px;"></i>
                        <p>Aucun participant inscrit pour cet événement pour le moment.</p>
                    </div>
                <?php endif; ?>
            </div>

            <?php $common->footer(); ?>
        </body>
        </html>
        <?php
    }
}
?>
