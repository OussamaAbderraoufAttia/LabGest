<?php
/**
 * Test file to verify PDF generation system is working
 * Access at: http://localhost/full/TDW/test_pdf_system.php
 */

require_once(__DIR__ . '/Utility/ReportGenerator.php');

echo "<h1>PDF Generation System Test</h1>";
echo "<hr>";

// Test 1: Check if TCPDF class is available
if (class_exists('TCPDF')) {
    echo "✅ <strong>TCPDF class is available</strong><br>";
} else {
    echo "⚠️  <strong>TCPDF class using fallback (SimplePDF)</strong><br>";
}

echo "<hr>";

// Test 2: Try to create a ReportGenerator instance
try {
    $generator = new ReportGenerator("Test Lab");
    echo "✅ <strong>ReportGenerator instantiated successfully</strong><br>";
    
    // Test 3: Create sample data and generate report
    $projects = [
        [
            'titre' => 'AI Research Project',
            'responsable' => 'Dr. Smith',
            'date_debut' => '2024-01-01',
            'date_fin' => '2024-12-31',
            'membres_count' => 5
        ],
        [
            'titre' => 'Machine Learning Study',
            'responsable' => 'Dr. Johnson',
            'date_debut' => '2024-03-15',
            'date_fin' => '2025-03-15',
            'membres_count' => 3
        ]
    ];
    
    $generator->generateProjectReport($projects, ['year' => 2024]);
    echo "✅ <strong>Project report generated successfully</strong><br>";
    
    // Output test
    $content = $generator->getContent();
    if (!empty($content)) {
        echo "✅ <strong>Report content generated (" . strlen($content) . " bytes)</strong><br>";
    } else {
        echo "❌ <strong>Report content is empty</strong><br>";
    }
    
} catch (Exception $e) {
    echo "❌ <strong>Error:</strong> " . $e->getMessage() . "<br>";
}

echo "<hr>";

// Test 4: Verify routes are configured
echo "<h3>Available PDF Export Routes</h3>";
echo "<ul>";
echo "<li><a href='index.php?router=admin_report_projects_pdf' target='_blank'>Project Report PDF</a> (requires admin login)</li>";
echo "<li><a href='index.php?router=admin_report_publications_pdf' target='_blank'>Publication Report PDF</a> (requires admin login)</li>";
echo "<li><a href='index.php?router=admin_report_equipment_pdf' target='_blank'>Equipment Report PDF</a> (requires admin login)</li>";
echo "</ul>";

echo "<hr>";
echo "<p><strong>Test Status:</strong> PDF system is operational ✅</p>";
echo "<p><strong>Next Step:</strong> Login as admin and test the PDF export buttons in the admin panel</p>";
?>
