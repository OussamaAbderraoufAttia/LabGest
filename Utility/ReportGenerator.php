<?php
/**
 * ReportGenerator - PDF Report Generation using TCPDF
 * Generates various lab reports (projects, publications, equipment)
 */

// Load TCPDF setup (handles installation fallbacks)
require_once(__DIR__ . '/TCPDFSetup.php');

class ReportGenerator {
    
    private $pdf;
    private $labName;
    private $labLogo;
    private $labInfo;
    
    public function __construct($labName = 'Laboratoire de Recherche ESI', $logoPath = null) {
        $this->labName = $labName;
        $this->labLogo = $logoPath;
        $this->initializePDF();
    }
    
    /**
     * Initialize TCPDF with lab branding
     */
    private function initializePDF() {
        // Create PDF object - use direct parameters instead of constants
        $this->pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
        
        // Set margins
        $this->pdf->SetMargins(15, 20, 15);
        
        // Set font
        $this->pdf->SetFont('helvetica', '', 10);
        
        // Set header/footer
        $this->pdf->SetHeaderData('', 0, $this->labName, 'Rapport Généré le ' . date('d/m/Y'));
    }
    
    /**
     * Generate Project Report PDF
     * @param array $projects Array of projects with data
     * @param array $filters Optional filters (year, theme, responsible)
     */
    public function generateProjectReport($projects, $filters = []) {
        $this->pdf->AddPage();
        
        // Title
        $this->pdf->SetFont('helvetica', 'B', 16);
        $this->pdf->Cell(0, 10, 'Rapport des Projets de Recherche', 0, 1, 'C');
        
        // Filters info
        $this->pdf->SetFont('helvetica', '', 9);
        $filterText = 'Tous les projets';
        if (!empty($filters)) {
            $parts = [];
            if (!empty($filters['year'])) $parts[] = 'Année: ' . $filters['year'];
            if (!empty($filters['theme'])) $parts[] = 'Thème: ' . $filters['theme'];
            if (!empty($filters['responsible'])) $parts[] = 'Responsable: ' . $filters['responsible'];
            $filterText = implode(' | ', $parts);
        }
        $this->pdf->Cell(0, 5, $filterText, 0, 1, 'C');
        $this->pdf->Cell(0, 5, 'Date: ' . date('d/m/Y H:i'), 0, 1, 'C');
        $this->pdf->Ln(5);
        
        // Table header
        $this->pdf->SetFont('helvetica', 'B', 10);
        $this->pdf->SetFillColor(66, 139, 202);
        $this->pdf->SetTextColor(255, 255, 255);
        
        $this->pdf->Cell(40, 8, 'Titre', 1, 0, 'C', true);
        $this->pdf->Cell(35, 8, 'Responsable', 1, 0, 'C', true);
        $this->pdf->Cell(25, 8, 'Date Début', 1, 0, 'C', true);
        $this->pdf->Cell(25, 8, 'Date Fin', 1, 0, 'C', true);
        $this->pdf->Cell(20, 8, 'Membres', 1, 1, 'C', true);
        
        // Table content
        $this->pdf->SetFont('helvetica', '', 9);
        $this->pdf->SetTextColor(0, 0, 0);
        
        foreach ($projects as $project) {
            $this->pdf->SetFillColor(240, 240, 240);
            $this->pdf->MultiCell(40, 8, substr($project['titre'] ?? 'N/A', 0, 20), 1, 'L', true);
            
            // Get current Y position
            $y = $this->pdf->GetY() - 8;
            $this->pdf->SetXY(55, $y);
            $this->pdf->MultiCell(35, 8, substr($project['responsable'] ?? 'N/A', 0, 15), 1, 'L', true);
            
            $this->pdf->SetXY(90, $y);
            $this->pdf->MultiCell(25, 8, $project['date_debut'] ?? 'N/A', 1, 'L', true);
            
            $this->pdf->SetXY(115, $y);
            $this->pdf->MultiCell(25, 8, $project['date_fin'] ?? 'N/A', 1, 'L', true);
            
            $this->pdf->SetXY(140, $y);
            $this->pdf->MultiCell(20, 8, $project['membres_count'] ?? '0', 1, 'C', true);
        }
        
        return $this;
    }
    
    /**
     * Generate Publication Report PDF (Bibliography)
     * @param array $publications Array of publications with author info
     * @param array $filters Optional filters (year, author, team)
     */
    public function generatePublicationReport($publications, $filters = []) {
        $this->pdf->AddPage();
        
        // Title
        $this->pdf->SetFont('helvetica', 'B', 16);
        $this->pdf->Cell(0, 10, 'Rapport Bibliographique', 0, 1, 'C');
        
        // Filters info
        $this->pdf->SetFont('helvetica', '', 9);
        $filterText = 'Tous les publications';
        if (!empty($filters)) {
            $parts = [];
            if (!empty($filters['year'])) $parts[] = 'Année: ' . $filters['year'];
            if (!empty($filters['author'])) $parts[] = 'Auteur: ' . $filters['author'];
            if (!empty($filters['team'])) $parts[] = 'Équipe: ' . $filters['team'];
            $filterText = implode(' | ', $parts);
        }
        $this->pdf->Cell(0, 5, $filterText, 0, 1, 'C');
        $this->pdf->Cell(0, 5, 'Date: ' . date('d/m/Y H:i'), 0, 1, 'C');
        $this->pdf->Ln(5);
        
        // Table header
        $this->pdf->SetFont('helvetica', 'B', 10);
        $this->pdf->SetFillColor(66, 139, 202);
        $this->pdf->SetTextColor(255, 255, 255);
        
        $this->pdf->Cell(60, 8, 'Titre', 1, 0, 'C', true);
        $this->pdf->Cell(40, 8, 'Auteurs', 1, 0, 'C', true);
        $this->pdf->Cell(20, 8, 'Année', 1, 0, 'C', true);
        $this->pdf->Cell(30, 8, 'Type', 1, 1, 'C', true);
        
        // Table content
        $this->pdf->SetFont('helvetica', '', 9);
        $this->pdf->SetTextColor(0, 0, 0);
        
        foreach ($publications as $pub) {
            $this->pdf->SetFillColor(240, 240, 240);
            $authors = is_array($pub['authors'] ?? null) ? implode(', ', array_slice($pub['authors'], 0, 2)) : ($pub['authors'] ?? 'N/A');
            if (strlen($authors) > 35) $authors = substr($authors, 0, 32) . '...';
            
            $this->pdf->MultiCell(60, 8, substr($pub['titre'] ?? 'N/A', 0, 30), 1, 'L', true);
            
            $y = $this->pdf->GetY() - 8;
            $this->pdf->SetXY(60, $y);
            $this->pdf->MultiCell(40, 8, $authors, 1, 'L', true);
            
            $this->pdf->SetXY(100, $y);
            $this->pdf->MultiCell(20, 8, substr($pub['annee'] ?? date('Y'), 0, 4), 1, 'C', true);
            
            $this->pdf->SetXY(120, $y);
            $this->pdf->MultiCell(30, 8, $pub['type'] ?? 'Article', 1, 'C', true);
        }
        
        return $this;
    }
    
    /**
     * Generate Equipment Report PDF (Usage Statistics)
     * @param array $equipment Array of equipment with reservation data
     * @param array $stats Optional statistics array
     */
    public function generateEquipmentReport($equipment, $stats = []) {
        $this->pdf->AddPage();
        
        // Title
        $this->pdf->SetFont('helvetica', 'B', 16);
        $this->pdf->Cell(0, 10, 'Rapport d\'Utilisation des Équipements', 0, 1, 'C');
        
        // Date
        $this->pdf->SetFont('helvetica', '', 9);
        $this->pdf->Cell(0, 5, 'Date du rapport: ' . date('d/m/Y H:i'), 0, 1, 'C');
        $this->pdf->Ln(3);
        
        // Statistics Summary
        if (!empty($stats)) {
            $this->pdf->SetFont('helvetica', 'B', 11);
            $this->pdf->Cell(0, 7, 'Résumé Statistique', 0, 1);
            $this->pdf->SetFont('helvetica', '', 9);
            $this->pdf->SetFillColor(230, 240, 250);
            
            foreach ($stats as $key => $value) {
                $label = ucfirst(str_replace('_', ' ', $key));
                $this->pdf->Cell(80, 6, $label . ': ', 0, 0);
                $this->pdf->Cell(30, 6, $value, 0, 1, 'R');
            }
            $this->pdf->Ln(3);
        }
        
        // Equipment Table
        $this->pdf->SetFont('helvetica', 'B', 10);
        $this->pdf->SetFillColor(66, 139, 202);
        $this->pdf->SetTextColor(255, 255, 255);
        
        $this->pdf->Cell(45, 8, 'Équipement', 1, 0, 'C', true);
        $this->pdf->Cell(25, 8, 'État', 1, 0, 'C', true);
        $this->pdf->Cell(25, 8, 'Réservations', 1, 0, 'C', true);
        $this->pdf->Cell(30, 8, 'Utilisation %', 1, 0, 'C', true);
        $this->pdf->Cell(25, 8, 'Maintenance', 1, 1, 'C', true);
        
        // Equipment content
        $this->pdf->SetFont('helvetica', '', 9);
        $this->pdf->SetTextColor(0, 0, 0);
        
        foreach ($equipment as $item) {
            $this->pdf->SetFillColor(240, 240, 240);
            
            // State color
            $state = $item['etat'] ?? 'N/A';
            if ($state === 'disponible') {
                $this->pdf->SetFillColor(200, 255, 200);
            } elseif ($state === 'réservé') {
                $this->pdf->SetFillColor(255, 255, 200);
            } elseif ($state === 'maintenance') {
                $this->pdf->SetFillColor(255, 200, 200);
            }
            
            $this->pdf->Cell(45, 8, substr($item['nom'] ?? 'N/A', 0, 20), 1, 0, 'L', true);
            $this->pdf->Cell(25, 8, ucfirst($state), 1, 0, 'C', true);
            $this->pdf->Cell(25, 8, $item['reservations_count'] ?? '0', 1, 0, 'C', true);
            $utilization = $item['utilization_percent'] ?? '0';
            $this->pdf->Cell(30, 8, $utilization . '%', 1, 0, 'C', true);
            $this->pdf->Cell(25, 8, $item['maintenance_date'] ?? 'N/A', 1, 1, 'C', true);
        }
        
        return $this;
    }
    
    /**
     * Output PDF to browser for download
     * @param string $filename Filename for download
     */
    public function output($filename = 'report.pdf') {
        $this->pdf->Output($filename, 'D');
    }
    
    /**
     * Save PDF to file
     * @param string $filepath Full path where to save the PDF
     */
    public function save($filepath) {
        $this->pdf->Output($filepath, 'F');
        return file_exists($filepath);
    }
    
    /**
     * Get PDF content as string
     */
    public function getContent() {
        return $this->pdf->Output('', 'S');
    }
}
?>
