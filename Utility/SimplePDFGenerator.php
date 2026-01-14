<?php
/**
 * Simple PDF Generator - Creates actual PDF files without TCPDF
 * Uses mPDF-like syntax for compatibility with existing code
 */

class SimplePDFGenerator {
    private $content = [];
    private $title = '';
    private $font_size = 12;
    private $margin_left = 15;
    private $margin_top = 15;
    private $margin_right = 15;
    private $current_y = 15;
    private $page_width = 210;
    private $page_height = 297;
    private $orientation = 'P';
    private $fill_color = [255, 255, 255];
    private $text_color = [0, 0, 0];
    private $draw_color = [0, 0, 0];
    private $line_height = 7;
    private $current_font_size = 12;
    private $current_font_bold = false;
    private $current_font_family = 'Helvetica';
    private $objects = [];
    private $page_count = 0;
    private $current_page_content = '';
    private $current_x = 15;
    private $y_position = 20;
    private $page_break_margin = 260;
    
    public function __construct($orientation = 'P', $unit = 'mm', $format = 'A4', $unicode = true, $encoding = 'UTF-8') {
        $this->orientation = $orientation;
        $this->setupPageSize($format);
        $this->addNewPage();
    }
    
    private function setupPageSize($format) {
        switch (strtoupper($format)) {
            case 'A4':
                if ($this->orientation === 'P') {
                    $this->page_width = 210;
                    $this->page_height = 297;
                } else {
                    $this->page_width = 297;
                    $this->page_height = 210;
                }
                break;
            default:
                $this->page_width = 210;
                $this->page_height = 297;
        }
        $this->current_y = $this->margin_top;
        $this->y_position = 20;
    }
    
    private function addNewPage() {
        $this->page_count++;
        $this->y_position = 20;
        $this->current_page_content = '';
        $this->current_x = $this->margin_left;
    }
    
    public function AddPage() {
        $this->addNewPage();
    }
    
    public function SetFont($family, $style = '', $size = null) {
        $this->current_font_family = $family;
        $this->current_font_bold = (strpos($style, 'B') !== false);
        if ($size !== null) {
            $this->current_font_size = $size;
            $this->font_size = $size;
        }
    }
    
    public function SetMargins($left, $top, $right) {
        $this->margin_left = $left;
        $this->margin_top = $top;
        $this->margin_right = $right;
        $this->current_x = $left;
        $this->y_position = $top;
    }
    
    public function SetHeaderData($logo, $logo_width, $title, $subtitle) {
        $this->title = $title;
        // Add title to page
        $this->y_position = 20;
        $this->current_page_content .= $this->createTextCommand($title, 14, true, 20, 'C');
        $this->y_position += 10;
    }
    
    public function SetFillColor($r, $g = null, $b = null) {
        if ($g === null && $b === null) {
            $this->fill_color = [$r, $r, $r];
        } else {
            $this->fill_color = [$r, $g, $b];
        }
    }
    
    public function SetTextColor($r, $g = null, $b = null) {
        if ($g === null && $b === null) {
            $this->text_color = [$r, $r, $r];
        } else {
            $this->text_color = [$r, $g, $b];
        }
    }
    
    public function SetDrawColor($r, $g = null, $b = null) {
        if ($g === null && $b === null) {
            $this->draw_color = [$r, $r, $r];
        } else {
            $this->draw_color = [$r, $g, $b];
        }
    }
    
    public function Cell($w, $h, $txt = '', $border = 0, $ln = 0, $align = 'L', $fill = false) {
        // Add cell content with better formatting
        $this->y_position += $h;
        
        if (!empty($txt)) {
            $text_cmd = $this->createCellCommand($txt, $w, $h, $border, $align, $fill);
            $this->current_page_content .= $text_cmd;
        }
        
        if ($ln === 1) {
            $this->current_x = $this->margin_left;
        }
    }
    
    public function MultiCell($w, $h, $txt = '', $border = 0, $align = 'L', $fill = false) {
        if (empty($txt)) {
            return;
        }
        
        // Word wrap text
        $wrapped = $this->wrapText($txt, $w);
        $lines = explode("\n", $wrapped);
        
        foreach ($lines as $line) {
            $this->Cell($w, $h, $line, $border, 1, $align, $fill);
        }
    }
    
    private function wrapText($text, $max_width) {
        $words = explode(' ', $text);
        $lines = [];
        $current_line = '';
        
        foreach ($words as $word) {
            if (strlen($current_line . ' ' . $word) > $max_width / 2.5) {
                if (!empty($current_line)) {
                    $lines[] = $current_line;
                }
                $current_line = $word;
            } else {
                $current_line .= ($current_line ? ' ' : '') . $word;
            }
        }
        
        if (!empty($current_line)) {
            $lines[] = $current_line;
        }
        
        return implode("\n", $lines);
    }
    
    public function Ln($h = 5) {
        $this->y_position += $h;
        $this->current_page_content .= "% Line break\n";
    }
    
    public function SetLineWidth($width) {
        // Set line width in PDF
    }
    
    public function SetXY($x, $y) {
        $this->current_x = $x;
        $this->y_position = $y;
    }
    
    public function GetY() {
        return $this->y_position;
    }
    
    private function createTextCommand($text, $size, $bold, $y, $align = 'L') {
        $weight = $bold ? ' bold' : '';
        $text_escaped = addslashes(substr($text, 0, 100)); // Limit text length
        $x = $this->margin_left;
        
        if ($align === 'C') {
            $x = $this->page_width / 2;
        } elseif ($align === 'R') {
            $x = $this->page_width - $this->margin_right;
        }
        
        $color_r = isset($this->text_color[0]) ? $this->text_color[0] : 0;
        $color_g = isset($this->text_color[1]) ? $this->text_color[1] : 0;
        $color_b = isset($this->text_color[2]) ? $this->text_color[2] : 0;
        
        $cmd = "BT\n";
        $cmd .= "/F" . ($bold ? "2" : "1") . " " . (int)$size . " Tf\n";
        $cmd .= (($color_r / 255) . " " . ($color_g / 255) . " " . ($color_b / 255) . " rg\n");
        $cmd .= $x . " " . (841.89 - $y) . " Td\n";
        $cmd .= "(" . $text_escaped . ") Tj\n";
        $cmd .= "ET\n";
        
        return $cmd;
    }
    
    private function createCellCommand($text, $w, $h, $border, $align, $fill) {
        $text_escaped = addslashes(substr($text, 0, 150));
        $x = $this->current_x;
        $y = 841.89 - $this->y_position;
        
        $cmd = '';
        
        // Add background color if fill
        if ($fill) {
            $fill_r = isset($this->fill_color[0]) ? $this->fill_color[0] / 255 : 1;
            $fill_g = isset($this->fill_color[1]) ? $this->fill_color[1] / 255 : 1;
            $fill_b = isset($this->fill_color[2]) ? $this->fill_color[2] / 255 : 1;
            
            $cmd .= "q\n";
            $cmd .= $fill_r . " " . $fill_g . " " . $fill_b . " rg\n";
            $cmd .= $x . " " . ($y - $h) . " " . $w . " " . $h . " re\n";
            $cmd .= "f\n";
            $cmd .= "Q\n";
        }
        
        // Add border if needed
        if ($border) {
            $cmd .= "q\n";
            $cmd .= "0.5 w\n";
            $cmd .= $x . " " . ($y - $h) . " " . $w . " " . $h . " re\n";
            $cmd .= "S\n";
            $cmd .= "Q\n";
        }
        
        // Add text
        $color_r = isset($this->text_color[0]) ? $this->text_color[0] / 255 : 0;
        $color_g = isset($this->text_color[1]) ? $this->text_color[1] / 255 : 0;
        $color_b = isset($this->text_color[2]) ? $this->text_color[2] / 255 : 0;
        
        $cmd .= "BT\n";
        $cmd .= "/F" . ($this->current_font_bold ? "2" : "1") . " " . (int)$this->current_font_size . " Tf\n";
        $cmd .= $color_r . " " . $color_g . " " . $color_b . " rg\n";
        $cmd .= ($x + 2) . " " . ($y - $h + 2) . " Td\n";
        $cmd .= "(" . $text_escaped . ") Tj\n";
        $cmd .= "ET\n";
        
        return $cmd;
    }
    
    /**
     * Generate PDF output - creates a proper PDF with better formatting
     */
    private function generatePDF() {
        $pdf = "%PDF-1.4\n";
        $offsets = [];
        
        // Object 1: Catalog
        $offsets[1] = strlen($pdf);
        $pdf .= "1 0 obj\n<</Type/Catalog/Pages 2 0 R>>\nendobj\n";
        
        // Object 2: Pages
        $offsets[2] = strlen($pdf);
        $pdf .= "2 0 obj\n<</Type/Pages/Kids[3 0 R]/Count 1>>\nendobj\n";
        
        // Object 3: Page
        $offsets[3] = strlen($pdf);
        $pdf .= "3 0 obj\n<</Type/Page/Parent 2 0 R/MediaBox[0 0 595 842]/Contents 4 0 R/Resources<</Font<</F1 5 0 R/F2 6 0 R>>>>/Rotate 0>>\nendobj\n";
        
        // Object 4: Content Stream
        $content = "BT\n/F2 14 Tf\n50 800 Td\n(" . addslashes(substr($this->title, 0, 50)) . ") Tj\nET\n";
        $content .= $this->current_page_content;
        
        $offsets[4] = strlen($pdf);
        $pdf .= "4 0 obj\n<</Length " . strlen($content) . ">>\nstream\n" . $content . "\nendstream\nendobj\n";
        
        // Object 5: Font Helvetica
        $offsets[5] = strlen($pdf);
        $pdf .= "5 0 obj\n<</Type/Font/Subtype/Type1/BaseFont/Helvetica>>\nendobj\n";
        
        // Object 6: Font Helvetica-Bold
        $offsets[6] = strlen($pdf);
        $pdf .= "6 0 obj\n<</Type/Font/Subtype/Type1/BaseFont/Helvetica-Bold>>\nendobj\n";
        
        // xref table
        $xref_offset = strlen($pdf);
        $pdf .= "xref\n0 7\n";
        $pdf .= "0000000000 65535 f \n";
        
        for ($i = 1; $i <= 6; $i++) {
            $pdf .= sprintf("%010d 00000 n \n", $offsets[$i]);
        }
        
        // Trailer
        $pdf .= "trailer\n<</Size 7/Root 1 0 R>>\n";
        $pdf .= "startxref\n" . $xref_offset . "\n%%EOF\n";
        
        return $pdf;
    }
    
    public function Output($filename = 'document.pdf', $dest = 'I') {
        $pdf_content = $this->generatePDF();
        
        if ($dest === 'D') {
            // Download
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="' . basename($filename) . '"');
            header('Content-Length: ' . strlen($pdf_content));
            echo $pdf_content;
        } elseif ($dest === 'F') {
            // Save to file
            file_put_contents($filename, $pdf_content);
        } elseif ($dest === 'S') {
            // Return as string
            return $pdf_content;
        } else {
            // Display inline
            header('Content-Type: application/pdf');
            echo $pdf_content;
        }
    }
}

// Alias for compatibility
if (!class_exists('TCPDF')) {
    class TCPDF extends SimplePDFGenerator {}
}

?>
