<?php
require_once("View/eventsView.php");
require_once("Model/eventModel.php");

class eventController {
    public function afficherEvenements() {
        $model = new eventModel();
        $events = $model->getAllEvents();
        $offers = $model->getOffers();
        
        $view = new eventsView();
        $view->afficherEvenements($events, $offers);
    }
}
?>
