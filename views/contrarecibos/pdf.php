<?php
error_reporting(E_ERROR | E_PARSE);
require_once __DIR__ . '/../../doc/fpdf.php';

function t($s) {
    return iconv("UTF-8", "ISO-8859-1//IGNORE", $s);
}

class PDF extends FPDF {

    function Row($data, $widths, $height = 4) {
        $nb = 0;
        for ($i = 0; $i < count($data); $i++) {
            $nb = max($nb, $this->NbLines($widths[$i], $data[$i]));
        }
        $h = $height * $nb;

        if ($this->GetY() + $h > $this->PageBreakTrigger) {
            $this->AddPage($this->CurOrientation);
        }

        for ($i = 0; $i < count($data); $i++) {
            $x = $this->GetX();
            $y = $this->GetY();
            $this->MultiCell($widths[$i], $height, $data[$i], 1, 'L');
            $this->SetXY($x + $widths[$i], $y);
        }

        $this->Ln($h);
    }
}

$pdf = new PDF('P', 'mm', 'Letter');
$pdf->SetMargins(10,10,10);
$pdf->SetAutoPageBreak(true,12);

function imprimirFormato($pdf, $id, $rows, $tipoCopia)
{
    $pdf->AddPage();
    $pdf->SetFont('Arial','',8);

    // LOGO
    $pdf->Image(__DIR__ . '/../../public/img/logo.png', 10, 8, 28);

    // ENCABEZADO
    $pdf->SetFont('Arial','B',10);
    $pdf->SetXY(60, 10);
    $pdf->Cell(120, 5, t("MUNICIPIO DE HUETAMO MICHOACAN"), 0, 1);

    $pdf->SetFont('Arial','',9);
    $pdf->SetX(60); $pdf->Cell(120, 5, t("RFC: MHM8501016A8"), 0, 1);
    $pdf->SetX(60); $pdf->Cell(120, 5, t("AV. Madero Nte S/N"), 0, 1);
    $pdf->SetX(60); $pdf->Cell(120, 5, t("Col Centro Cp: 61940"), 0, 1);
    $pdf->SetX(60); $pdf->Cell(120, 5, t("Huetamo Mich. Teléfono: (435)5-56-02-92"), 0, 1);

    // FECHA Y FOLIO
    $pdf->SetFont('Arial','B',10);
    $pdf->SetXY(155, 15);
    $pdf->Cell(28, 6, t("FECHA:"), 1, 0, "L");

    $pdf->SetFont('Arial','',10);
    $pdf->Cell(28, 6, t($rows[0]['fecha_contrarecibo']), 1, 1, "C");

    $pdf->SetXY(155, 21);
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(28, 10, t("FOLIO:"), 1, 0, "L");

    $pdf->SetFont('Arial','B',14);
    $pdf->Cell(28, 10, t($id), 1, 1, "C");

    $pdf->Ln(8);

    // FRANJA AZUL
    $pdf->SetFillColor(0, 51, 102); 
    $pdf->SetTextColor(255,255,255);
    $pdf->SetFont('Arial','B',11);
    $pdf->Cell(196, 8, t("CONTRA RECIBO PARA PROVEEDORES"), 1, 1, "C", true);
    $pdf->SetTextColor(0,0,0);

    // RECIBIMOS DE
    $pdf->SetFont('Arial','B',9);
    $pdf->Cell(40, 8, t("RECIBIMOS DE"), 1, 0, "C");
    $pdf->SetFont('Arial','',9);
    $pdf->Cell(156, 8, t($rows[0]['proveedor']), 1, 1, "L");

    // SUBTITULO
    $pdf->SetFont('Arial','B',8);
    $pdf->Cell(196, 8, t("LOS SIGUIENTES DOCUMENTOS ORIGINALES A REVISION, Y DE SER PROCEDENTES REALIZAREMOS EL PAGO"), 1, 1, "C");

    $pdf->Ln(2);

    // TABLA
    $pdf->SetFont('Arial','B',8);
    $w = [8, 30, 28, 30, 100];
    $pdf->Cell($w[0], 7, t("#"), 1, 0, "C");
    $pdf->Cell($w[1], 7, t("# DOCUMENTO"), 1, 0, "C");
    $pdf->Cell($w[2], 7, t("FECHA"), 1, 0, "C");
    $pdf->Cell($w[3], 7, t("IMPORTE"), 1, 0, "C");
    $pdf->Cell($w[4], 7, t("OBSERVACIONES"), 1, 1, "C");

    $pdf->SetFont('Arial','',8);
    $total = 0;
    $i = 1;
    foreach ($rows as $r) {
        $total += floatval($r['cantidad_factura']);
        $row = [
            $i++,
            t($r['id_factura']),
            t($r['fecha_factura']),
            "$ ".number_format($r['cantidad_factura'],2),
            t($r['descripcion_factura'])
        ];
        $pdf->Row($row, $w, 4);
    }

    // TOTAL
    $pdf->SetFont('Arial','B',9);
    $pdf->Cell($w[0], 7, "", 1, 0);
    $pdf->Cell($w[1], 7, "", 1, 0);
    $pdf->Cell($w[2], 7, t("TOTAL:"), 1, 0, "C");
    $pdf->Cell($w[3], 7, "$ ".number_format($total,2), 1, 0, "R");
    $pdf->Cell($w[4], 7, "", 1, 1);

    // FIRMAS — RECUADROS GRANDES
    $hFirmas = 40;
    $pdf->Ln(10);
    $pdf->SetFont('Arial','',9);

    // Primer recuadro (TESORERIA MUNICIPAL + NOMBRE Y FIRMA)
    $pdf->Cell(65, $hFirmas, "", 1, 0);
    $x = $pdf->GetX() - 65;
    $y = $pdf->GetY();

    // TESORERIA MUNICIPAL arriba
    $pdf->SetXY($x, $y + 3);
    $pdf->Cell(65, 6, t("TESORERIA MUNICIPAL"), 0, 1, "C");

    // NOMBRE Y FIRMA abajo
    $pdf->SetXY($x, $y + $hFirmas - 10);
    $pdf->Cell(65, 6, t("NOMBRE Y FIRMA:"), 0, 1, "C");

    // Segundo recuadro (SELLO) arriba
    $pdf->SetXY($x + 65, $y + 3);
    $pdf->Cell(65, 6, t("SELLO"), 0, 1, "C");
    $pdf->SetXY($x + 65, $y);
    $pdf->Cell(65, $hFirmas, "", 1, 0);

    // Tercer recuadro (FECHA DE PAGO) arriba
    $pdf->SetXY($x + 130, $y + 3);
    $pdf->Cell(66, 6, t("FECHA DE PAGO:"), 0, 1, "C");
    $pdf->SetXY($x + 130, $y);
    $pdf->Cell(66, $hFirmas, "", 1, 1);

    // TEXTO DE COPIA justo debajo de los recuadros
    $pdf->Ln(2);
    $pdf->SetFont('Arial','B',8);
    $pdf->Cell(196, 6, "**" . $tipoCopia . "**", 0, 1, "R");
}

// GENERAR DOS COPIAS
imprimirFormato($pdf, $id, $rows, "COPIA PROVEEDOR");
imprimirFormato($pdf, $id, $rows, "COPIA TESORERIA");

$pdf->Output();


