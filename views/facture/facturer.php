<?php
require_once __DIR__ . '/../../vendor/autoload.php';
use Dompdf\Dompdf;
use Dompdf\Options;

// Initialize DomPDF
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isPhpEnabled', true);
$dompdf = new Dompdf($options);

// HTML content (your HTML code as a string)
$html = file_get_contents('affiche.html'); // or you can paste your HTML content here directly

// Load HTML content
$dompdf->loadHtml($html);

// (Optional) Set paper size and orientation
$dompdf->setPaper('A4', 'portrait');

// Render PDF (first pass)
$dompdf->render();

// Output the generated PDF (download it)
$dompdf->stream("devis.pdf", array("Attachment" => 0)); // 0 for display inline, 1 for download

?>
