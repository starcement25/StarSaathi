<?php
ob_start();

require_once('tcpdf/tcpdf.php');

$QrCode1 = "Qk2yiwAAAAAAAD4AAAAoAAAAIAIAAA0CAAABAAEAAAAAAHSLAABGXAAARlwAAAIAAAACAAAA/////wAAAP//////4Pg//4AAAAA";
$QrCode1 = preg_replace('#^data:image/\w+;base64,#i', '', $QrCode1);

// Decode and save as temp BMP file
$imgData = base64_decode($QrCode1);
$tmpFile = sys_get_temp_dir() . '/qr_' . uniqid() . '.bmp';
file_put_contents($tmpFile, $imgData);

$pdf = new TCPDF();
$pdf->AddPage();

$pdf->SetFont('helvetica', 'B', 14);
$pdf->Cell(0, 10, 'QR Code', 0, 1);

// TCPDF handles BMP internally — no GD needed
$pdf->Image($tmpFile, 15, 30, 40, 40, 'BMP');

ob_end_clean();

@unlink($tmpFile);

$pdf->Output('qr_test.pdf', 'I');
exit;