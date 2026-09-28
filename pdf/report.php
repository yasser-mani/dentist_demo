<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../lib/fpdf/fpdf.php';

$patientId = (int)($_GET['patient_id'] ?? 0);
if (!$patientId) die('Patient ID requis');

$db = getDB();

// Load patient
$stmt = $db->prepare("
    SELECT p.*, ps.label as status_label
    FROM patients p
    JOIN patient_statuses ps ON p.status_id = ps.id
    WHERE p.id = ?
");
$stmt->execute([$patientId]);
$patient = $stmt->fetch();

if (!$patient) die('Patient introuvable');

// Next appointment
$stmt = $db->prepare("
    SELECT * FROM appointments
    WHERE patient_id = ? AND status = 'scheduled' AND appointment_date >= NOW()
    ORDER BY appointment_date ASC LIMIT 1
");
$stmt->execute([$patientId]);
$nextAppt = $stmt->fetch();

// Teeth
$stmt = $db->prepare("SELECT * FROM teeth_records WHERE patient_id = ? ORDER BY tooth_number ASC");
$stmt->execute([$patientId]);
$teeth = $stmt->fetchAll();

// Notes
$stmt = $db->prepare("SELECT * FROM patient_notes WHERE patient_id = ? ORDER BY created_at DESC");
$stmt->execute([$patientId]);
$notes = $stmt->fetchAll();

// Images (jpg/png only for embedding)
$stmt = $db->prepare("
    SELECT * FROM attachments
    WHERE patient_id = ? AND file_type IN ('jpg','jpeg','png')
    ORDER BY uploaded_at DESC LIMIT 3
");
$stmt->execute([$patientId]);
$images = $stmt->fetchAll();

// Build PDF
class PDF extends FPDF {
    function Header() {
        $this->SetFont('Arial','B',16);
        $this->SetTextColor(37,99,235);
        $this->Cell(0,10,'DentaFlow - Rapport Patient',0,1,'C');
        $this->Ln(5);
    }
    
    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial','I',8);
        $this->SetTextColor(128);
        $this->Cell(0,10,'Page '.$this->PageNo(),0,0,'C');
    }
    
    function SectionTitle($title) {
        $this->SetFont('Arial','B',12);
        $this->SetTextColor(0);
        $this->Cell(0,8,$title,0,1);
        $this->SetDrawColor(200);
        $this->Line($this->GetX(), $this->GetY(), $this->GetX()+190, $this->GetY());
        $this->Ln(4);
    }
    
    function InfoRow($label, $value) {
        $this->SetFont('Arial','B',10);
        $this->SetTextColor(60);
        $this->Cell(50,6,$label.':',0,0);
        $this->SetFont('Arial','',10);
        $this->SetTextColor(0);
        $this->Cell(0,6,$value,0,1);
    }
}

$pdf = new PDF();
$pdf->AddPage();
$pdf->SetFont('Arial','',10);

// Patient info
$pdf->SectionTitle('Informations du patient');
$pdf->InfoRow('Nom complet', $patient['full_name']);
$pdf->InfoRow('Telephone', $patient['phone']);
$pdf->InfoRow('Assurance', $patient['insurance'] ?: 'Aucune');
$pdf->InfoRow('Statut', $patient['status_label']);
if ($nextAppt) {
    $pdf->InfoRow('Prochain RDV', date('d/m/Y H:i', strtotime($nextAppt['appointment_date'])));
}
$pdf->Ln(5);

// Dental chart (text representation)
$pdf->SectionTitle('Carte dentaire');

$stateLabels = [
    'healthy' => 'Saine',
    'needs_intervention' => 'Intervention necessaire',
    'in_progress' => 'Traitement en cours',
    'treated' => 'Traitee'
];

if (empty($teeth)) {
    $pdf->SetFont('Arial','I',10);
    $pdf->SetTextColor(128);
    $pdf->Cell(0,6,'Aucune donnee dentaire enregistree.',0,1);
} else {
    $pdf->SetFont('Arial','',9);
    foreach ($teeth as $t) {
        $line = 'Dent ' . $t['tooth_number'] . ' : ' . $stateLabels[$t['state']];
        if ($t['treatment']) $line .= ' | ' . $t['treatment'];
        $pdf->MultiCell(0,5,$line);
    }
}

$pdf->Ln(5);

// Notes
$pdf->SectionTitle('Notes cliniques');
if (empty($notes)) {
    $pdf->SetFont('Arial','I',10);
    $pdf->SetTextColor(128);
    $pdf->Cell(0,6,'Aucune note.',0,1);
} else {
    $pdf->SetFont('Arial','',9);
    foreach ($notes as $note) {
        $date = date('d/m/Y', strtotime($note['created_at']));
        $pdf->SetFont('Arial','B',9);
        $pdf->Cell(0,5,$date,0,1);
        $pdf->SetFont('Arial','',9);
        $pdf->MultiCell(0,5,$note['content']);
        $pdf->Ln(2);
    }
}

$pdf->Ln(5);

// Images
if (!empty($images)) {
    $pdf->SectionTitle('Images attachees');
    $x = $pdf->GetX();
    $y = $pdf->GetY();
    $imgWidth = 60;
    $col = 0;
    foreach ($images as $img) {
        $path = __DIR__ . '/../uploads/' . $img['file_path'];
        if (file_exists($path)) {
            if ($col > 0 && $col % 3 === 0) {
                $y += 50;
                $x = $pdf->GetX();
            }
            $pdf->Image($path, $x + ($col % 3) * 63, $y, $imgWidth);
            $col++;
        }
    }
    if ($col > 0) {
        $pdf->Ln(($col > 3 ? 100 : 50));
    }
}

// Output
$filename = 'Rapport_' . preg_replace('/[^A-Za-z0-9_-]/', '_', $patient['full_name']) . '.pdf';
$pdf->Output('D', $filename);