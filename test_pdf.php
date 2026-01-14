<?php
// Test PDF generation
chdir(dirname(__FILE__));
require_once(__DIR__ . '/Utility/ReportGenerator.php');

try {
    $generator = new ReportGenerator('ESI Lab', null);
    
    // Generate a test report
    $generator->addProjectReport([], 'Test PDF');
    
    // Output as PDF to test
    ob_start();
    $generator->output('test_report.pdf');
    $output = ob_get_clean();
    
    echo "PDF Generated successfully!<br>";
    echo "Output headers would be set to: application/pdf<br>";
    echo "Filename: test_report.pdf<br>";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>
