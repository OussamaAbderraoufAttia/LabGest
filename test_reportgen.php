<?php
// Quick test of ReportGenerator
require_once(__DIR__ . '/Utility/ReportGenerator.php');

echo "Testing ReportGenerator...\n";

try {
    $gen = new ReportGenerator('Test Lab');
    echo "✅ SUCCESS: ReportGenerator created successfully\n";
    
    // Try generating a simple report
    $projects = [
        ['titre' => 'Test', 'responsable' => 'Smith', 'date_debut' => '2024-01-01', 'date_fin' => '2024-12-31', 'membres_count' => 5]
    ];
    
    $gen->generateProjectReport($projects);
    echo "✅ SUCCESS: Project report generated\n";
    
    $content = $gen->getContent();
    echo "✅ SUCCESS: Report content retrieved (" . strlen($content) . " bytes)\n";
    
} catch (Throwable $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
?>
