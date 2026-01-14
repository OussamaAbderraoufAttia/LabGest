<?php
/**
 * TCPDF Setup - Load TCPDF library for PDF generation
 */

// Try to load modern TCPDF (tc-lib-pdf) with better error handling
$tcpdf_loaded = false;

// Direct load without relying on Composer - load minimal required dependencies first
$tcpdf_base_path = __DIR__ . '/tc-lib-pdf-8.4.1/src/';

if (file_exists($tcpdf_base_path . 'Tcpdf.php')) {
    try {
        // Load minimal classes needed for TCPDF to work
        if (file_exists($tcpdf_base_path . 'Exception.php')) {
            @include_once($tcpdf_base_path . 'Exception.php');
        }
        
        // Try to include TCPDF - suppress namespace errors for missing dependencies
        @include_once($tcpdf_base_path . 'Tcpdf.php');
        
        // Check if class was loaded successfully
        if (class_exists('Com\Tecnick\Pdf\Tcpdf')) {
            $tcpdf_loaded = true;
            // Create a simple wrapper to TCPDF that uses basic PDF output
            if (!class_exists('TCPDF')) {
                class TCPDF {
                    private $tcpdf;
                    private $data = '';
                    
                    public function __construct($orientation = 'P', $unit = 'mm', $format = 'A4', $unicode = true, $encoding = 'UTF-8') {
                        try {
                            $this->tcpdf = new \Com\Tecnick\Pdf\Tcpdf($orientation, $unit, $format, $unicode, $encoding);
                        } catch (Exception $e) {
                            // If TCPDF fails, fall through to SimplePDF later
                            throw $e;
                        }
                    }
                    
                    public function AddPage() {
                        return $this->tcpdf->AddPage();
                    }
                    
                    public function SetFont($family, $style = '', $size = 0) {
                        return $this->tcpdf->SetFont($family, $style, $size);
                    }
                    
                    public function Cell($w, $h = 0, $txt = '', $border = 0, $ln = 0, $align = '', $fill = false, $link = '', $stretch = 0) {
                        return $this->tcpdf->Cell($w, $h, $txt, $border, $ln, $align, $fill, $link, $stretch);
                    }
                    
                    public function Ln($h = null) {
                        return $this->tcpdf->Ln($h);
                    }
                    
                    public function Output($filename = '', $dest = 'I') {
                        // Output as PDF binary
                        if ($dest === 'D') {
                            header('Content-Type: application/pdf; charset=utf-8');
                            header('Content-Disposition: attachment; filename="' . $filename . '"');
                            return $this->tcpdf->Output($filename, 'S');
                        } elseif ($dest === 'F') {
                            $content = $this->tcpdf->Output($filename, 'S');
                            file_put_contents($filename, $content);
                            return;
                        } elseif ($dest === 'S') {
                            return $this->tcpdf->Output($filename, 'S');
                        } else {
                            return $this->tcpdf->Output($filename, $dest);
                        }
                    }
                    
                    public function MultiCell($w, $h, $txt = '', $border = 0, $align = 'J', $fill = false) {
                        return $this->tcpdf->MultiCell($w, $h, $txt, $border, $align, $fill);
                    }
                    
                    public function SetDrawColor($r, $g = -1, $b = -1) {
                        return $this->tcpdf->SetDrawColor($r, $g, $b);
                    }
                    
                    public function SetFillColor($r, $g = -1, $b = -1) {
                        return $this->tcpdf->SetFillColor($r, $g, $b);
                    }
                    
                    public function SetTextColor($r, $g = -1, $b = -1) {
                        return $this->tcpdf->SetTextColor($r, $g, $b);
                    }
                    
                    public function __call($name, $arguments) {
                        return call_user_func_array([$this->tcpdf, $name], $arguments);
                    }
                }
            }
        }
    } catch (Exception $e) {
        // TCPDF has missing dependencies, fall through to SimplePDFGenerator
        $tcpdf_loaded = false;
    } catch (Error $e) {
        // Class not found errors - fall through to SimplePDFGenerator
        $tcpdf_loaded = false;
    }
}

// If modern TCPDF not loaded, try old TCPDF
if (!$tcpdf_loaded) {
    // Common composer autoload paths
    $composerPaths = [
        __DIR__ . '/../../vendor/autoload.php',
        __DIR__ . '/../vendor/autoload.php',
        '/vendor/autoload.php',
        dirname(__DIR__) . '/vendor/autoload.php',
        dirname(dirname(__DIR__)) . '/vendor/autoload.php'
    ];

    foreach ($composerPaths as $path) {
        if (file_exists($path)) {
            try {
                require_once($path);
                if (class_exists('TCPDF')) {
                    $tcpdf_loaded = true;
                    break;
                }
            } catch (Exception $e) {
                // Continue to next path
            }
        }
    }
}

// If TCPDF not found via composer, try direct include
if (!$tcpdf_loaded && !class_exists('TCPDF')) {
    // Try direct TCPDF path
    $tcpdfPaths = [
        __DIR__ . '/TCPDF/tcpdf.php',
        __DIR__ . '/../../TCPDF/tcpdf.php',
        __DIR__ . '/tcpdf/tcpdf.php',
    ];
    
    foreach ($tcpdfPaths as $path) {
        if (file_exists($path)) {
            try {
                require_once($path);
                $tcpdf_loaded = true;
                break;
            } catch (Exception $e) {
                // Continue to next
            }
        }
    }
}

// If still not found, use our SimplePDFGenerator
if (!$tcpdf_loaded && !class_exists('TCPDF')) {
    // Load SimplePDFGenerator as fallback
    if (file_exists(__DIR__ . '/SimplePDFGenerator.php')) {
        require_once(__DIR__ . '/SimplePDFGenerator.php');
    } else {
        // Fallback PDF wrapper
        class TCPDF {
            protected $width = 210;
            protected $height = 297;
            protected $margin_left = 15;
            protected $margin_top = 20;
            protected $margin_right = 15;
            protected $currentY = 30;
            protected $font_size = 10;
            protected $font_family = 'helvetica';
            protected $font_weight = 'normal';
            protected $text_color = '0,0,0';
            protected $fill_color = '255,255,255';
            protected $line_width = 0.1;
            protected $content = '';
            protected $pages = [];
            protected $page_count = 0;
            protected $header_data = '';
            protected $current_page = '';
            protected $line_height = 5;
            
            public function __construct($orientation = 'P', $unit = 'mm', $format = 'A4', $unicode = true, $encoding = 'UTF-8', $diskcache = false) {
                $this->initializePage();
            }
            
            protected function initializePage() {
                $this->page_count++;
                $this->currentY = $this->margin_top;
                $this->current_page = '';
            }
            
            public function SetMargins($left, $top, $right) {
                $this->margin_left = $left;
                $this->margin_top = $top;
                $this->margin_right = $right;
                $this->currentY = $top + 15;
            }
            
            public function SetFont($family, $style = '', $size = '') {
                $this->font_family = $family;
                $this->font_weight = $style;
                if ($size) $this->font_size = $size;
            }
            
            public function SetHeaderData($logo, $logo_width, $title, $subtitle) {
                $this->header_data = $title;
            }
            
            public function SetFillColor($r, $g = null, $b = null) {
                if ($g === null && $b === null) {
                    // Grayscale
                    $this->fill_color = "$r,$r,$r";
                } else {
                    $this->fill_color = "$r,$g,$b";
                }
            }
            
            public function SetTextColor($r, $g = null, $b = null) {
                if ($g === null && $b === null) {
                    $this->text_color = "$r,$r,$r";
                } else {
                    $this->text_color = "$r,$g,$b";
                }
            }
            
            public function AddPage($orientation = '') {
                $this->initializePage();
            }
            
            public function Cell($width, $height, $txt = '', $border = 0, $ln = 0, $align = '', $fill = false) {
                // Generate HTML for cell (will be converted to PDF)
                $html = "<table border='" . ($border ? 1 : 0) . "' width='100%' style='font-size:" . $this->font_size . "px'>";
                $html .= "<tr><td>" . htmlspecialchars($txt) . "</td></tr>";
                $html .= "</table>";
                $this->current_page .= $html;
                $this->currentY += $height;
            }
            
            public function MultiCell($width, $height, $txt = '', $border = 0, $align = '', $fill = false) {
                $this->Cell($width, $height, $txt, $border, 1, $align, $fill);
            }
            
            public function Ln($height = 5) {
                $this->currentY += $height;
            }
            
            public function GetY() {
                return $this->currentY;
            }
            
            public function SetXY($x, $y) {
                // Positioning logic for simple PDF
                $this->currentY = $y;
            }
            
            public function Output($filename = '', $dest = 'I') {
                // Output as HTML for browser rendering
                // In production, this would generate actual PDF
                // For now, we generate HTML that can be printed to PDF
                
                if ($dest === 'D') {
                    header('Content-Type: application/pdf; charset=utf-8');
                    header('Content-Disposition: attachment; filename="' . $filename . '"');
                    echo $this->generateHTML();
                } elseif ($dest === 'F') {
                    file_put_contents($filename, $this->generateHTML());
                } elseif ($dest === 'S') {
                    return $this->generateHTML();
                } else {
                    header('Content-Type: application/pdf; charset=utf-8');
                    echo $this->generateHTML();
                }
            }
            
            protected function generateHTML() {
                return '<html><head><meta charset="utf-8"><title>Report</title>' .
                       '<style>body{font-family:Arial;margin:20px;} table{border-collapse:collapse; width:100%; margin:10px 0;}' .
                       'th{background:#428ad1; color:white; padding:8px;} td{border:1px solid #ddd; padding:8px;}</style></head>' .
                       '<body>' . $this->current_page . '</body></html>';
            }
        }
    }
}

?>
