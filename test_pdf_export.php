<?php
// Simulate PDF export route test
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "=== PDF Export Route Simulation ===\n\n";

// Simulate admin session
$_SESSION['role'] = 'admin';
$_SESSION['user_id'] = 1;
$_GET['year'] = '2024';

echo "Session: admin\n";
echo "User ID: 1\n";

// Load necessary files
require_once(__DIR__ . '/Model/projectModel.php');
require_once(__DIR__ . '/Utility/ReportGenerator.php');

try {
    echo "\n1. Checking admin role... ";
    if ($_SESSION['role'] !== 'admin') {
        throw new Exception("Not admin");
    }
    echo "✅ OK\n";
    
    echo "2. Loading project model... ";
    $projectModel = new projectModel();
    echo "✅ OK\n";
    
    echo "3. Getting projects... ";
    $projects = $projectModel->getAllProjects();
    echo "✅ OK (" . count($projects) . " projects)\n";
    
    echo "4. Creating report generator... ";
    $generator = new ReportGenerator('Laboratoire de Recherche ESI');
    echo "✅ OK\n";
    
    echo "5. Generating project report... ";
    $generator->generateProjectReport($projects, ['year' => '2024']);
    echo "✅ OK\n";
    
    echo "6. Getting report content... ";
    $content = $generator->getContent();
    echo "✅ OK (" . strlen($content) . " bytes)\n";
    
    echo "\n✅ PDF GENERATION SUCCESSFUL!\n";
    echo "Report would be downloaded as: Rapport_Projets_" . date('Y-m-d') . ".pdf\n";
    
} catch (Exception $e) {
    echo "❌ ERROR\n";
    echo "Message: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
?>
