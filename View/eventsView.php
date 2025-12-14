<?php
require_once("commonViews.php");

class eventsView {
    
    public function entetePage() {
        ?>
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Événements - LRE</title>
            <link rel="stylesheet" href="View/css/commonStyles.css">
            <link rel="stylesheet" href="View/css/eventsStyle.css">
            <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />
        </head>
        <?php
    }
    
    public function afficherEvenements($events, $offers) {
        $common = new commonViews();
        ?>
        <!DOCTYPE html>
        <html lang="fr">
            <?php $this->entetePage(); ?>
            <body>
                <?php $common->navBar(); ?>
                
                <div class="events-container">
                    <h1 class="page-title">Événements et Opportunités</h1>
                    
                    <!-- Events Section -->
                    <section class="events-section">
                        <h2><i class="fa-solid fa-calendar-days"></i> Événements à venir</h2>
                        
                        <div class="events-grid">
                            <?php if (empty($events)): ?>
                                <p class="no-results">Aucun événement pour le moment.</p>
                            <?php else: ?>
                                <?php foreach ($events as $event): ?>
                                    <div class="event-card fade-in-up">
                                        <div class="event-date-badge">
                                            <div class="day"><?= date('d', strtotime($event['date_event'])) ?></div>
                                            <div class="month"><?= strtoupper(date('M', strtotime($event['date_event']))) ?></div>
                                        </div>
                                        
                                        <div class="event-content">
                                            <h3><?= htmlspecialchars($event['titre']) ?></h3>
                                            
                                            <div class="event-meta">
                                                <span><i class="fa-solid fa-clock"></i> <?= date('H:i', strtotime($event['date_event'])) ?></span>
                                                <?php if ($event['lieu']): ?>
                                                    <span><i class="fa-solid fa-location-dot"></i> <?= htmlspecialchars($event['lieu']) ?></span>
                                                <?php endif; ?>
                                                <?php if ($event['type']): ?>
                                                    <span class="event-type"><?= ucfirst($event['type']) ?></span>
                                                <?php endif; ?>
                                            </div>
                                            
                                            <?php if ($event['description']): ?>
                                                <p class="event-description"><?= htmlspecialchars(substr($event['description'], 0, 150)) ?>...</p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </section>
                    
                    <!-- Opportunities Section -->
                    <section class="offers-section">
                        <h2><i class="fa-solid fa-briefcase"></i> Opportunités</h2>
                        
                        <div class="offers-grid">
                            <?php if (empty($offers)): ?>
                                <p class="no-results">Aucune opportunité disponible.</p>
                            <?php else: ?>
                                <?php foreach ($offers as $offer): ?>
                                    <div class="offer-card fade-in-up">
                                        <div class="offer-type-badge"><?= ucfirst($offer['type']) ?></div>
                                        
                                        <h3><?= htmlspecialchars($offer['titre']) ?></h3>
                                        
                                        <?php if ($offer['description']): ?>
                                            <p><?= htmlspecialchars(substr($offer['description'], 0, 120)) ?>...</p>
                                        <?php endif; ?>
                                        
                                        <?php if ($offer['date_limite']): ?>
                                            <p class="deadline">
                                                <i class="fa-solid fa-hourglass-half"></i> 
                                                Date limite: <?= date('d/m/Y', strtotime($offer['date_limite'])) ?>
                                            </p>
                                        <?php endif; ?>
                                        
                                        <?php if ($offer['fichier_pdf']): ?>
                                            <a href="<?= htmlspecialchars($offer['fichier_pdf']) ?>" class="btn-primary" target="_blank">
                                                <i class="fa-solid fa-file-pdf"></i> Plus d'infos
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </section>
                </div>
                
                <?php $common->footer(); ?>
            </body>
        </html>
        <?php
    }
}
?>
