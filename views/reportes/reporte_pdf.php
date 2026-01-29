<?php
error_reporting(E_ERROR | E_PARSE);
require_once __DIR__ . '/../../doc/fpdf.php';

/* =============================
   Función para acentos
============================= */
function t($texto) {
    return iconv('UTF-8', 'ISO-8859-1//IGNORE', $texto);
}

/* =============================
   CLASE PDF
============================= */
class PDF extends FPDF {

    function Header() {
        // Logo
        $this->Image(__DIR__ . '/../../public/img/logo.png', 10, 8, 25);

        // Encabezado institucional
        $this->SetFont('Arial','B',12);
        $this->SetXY(40, 10);
        $this->Cell(0, 6, t('SISTEMA DE ADMINISTRACIÓN'), 0, 1, 'L');

        $this->SetFont('Arial','',10);
        $this->SetX(40);
        $this->Cell(0, 6, t('Bitácora de Movimientos del Sistema'), 0, 1, 'L');

        // Línea separadora
        $this->Ln(4);
        $this->SetDrawColor(180, 180, 180);
        $this->Line(10, 30, 287, 30);

        $this->Ln(10);
    }

    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial','',8);
        $this->SetTextColor(120);

        $this->Cell(0, 5, t('Generado el ') . date('d/m/Y H:i:s'), 0, 0, 'L');
        $this->Cell(0, 5, t('Página ') . $this->PageNo(), 0, 0, 'R');
    }

    // Fila con multicell
    function Row($data, $widths, $height = 6) {
        $nb = 0;
        for ($i = 0; $i < count($data); $i++) {
            $nb = max($nb, $this->NbLines($widths[$i], $data[$i]));
        }
        $h = $height * $nb;

        if ($this->GetY() + $h > $this->PageBreakTrigger) {
            $this->AddPage();
        }

        for ($i = 0; $i < count($data); $i++) {
            $x = $this->GetX();
            $y = $this->GetY();

            $this->Rect($x, $y, $widths[$i], $h);
            $this->MultiCell($widths[$i], $height, $data[$i], 0, 'L');
            $this->SetXY($x + $widths[$i], $y);
        }
        $this->Ln($h);
    }

    function NbLines($w, $txt) {
        $cw = &$this->CurrentFont['cw'];
        if ($w == 0)
            $w = $this->w - $this->rMargin - $this->x;
        $wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;
        $s = str_replace("\r", '', $txt);
        $nb = strlen($s);
        if ($nb > 0 && $s[$nb - 1] == "\n")
            $nb--;
        $sep = -1;
        $i = 0;
        $j = 0;
        $l = 0;
        $nl = 1;
        while ($i < $nb) {
            $c = $s[$i];
            if ($c == "\n") {
                $i++;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
                continue;
            }
            if ($c == ' ')
                $sep = $i;
            $l += $cw[$c];
            if ($l > $wmax) {
                if ($sep == -1) {
                    if ($i == $j)
                        $i++;
                } else
                    $i = $sep + 1;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
            } else
                $i++;
        }
        return $nl;
    }
}

/* =============================
   CREAR PDF
============================= */
$pdf = new PDF('P', 'mm', 'Letter');
$pdf->SetMargins(10,10,10);
$pdf->SetAutoPageBreak(true, 15);
$pdf->AddPage();

/* =============================
   PERIODO
============================= */
$pdf->SetFont('Arial','B',10);
$pdf->Cell(0, 8, t("Periodo consultado: $inicio al $fin"), 0, 1, 'C');
$pdf->Ln(5);

/* =============================
   ENCABEZADOS TABLA
============================= */
$pdf->SetFillColor(233, 30, 99); // rosa institucional
$pdf->SetTextColor(255);
$pdf->SetFont('Arial','B',9);

$w = [32, 85, 30, 25, 24];


$pdf->Cell($w[0], 8, t('Usuario'), 1, 0, 'C', true);
$pdf->Cell($w[1], 8, t('Acción realizada'), 1, 0, 'C', true);
$pdf->Cell($w[2], 8, t('Módulo'), 1, 0, 'C', true);
$pdf->Cell($w[3], 8, t('Fecha'), 1, 0, 'C', true);
$pdf->Cell($w[4], 8, t('Hora'), 1, 1, 'C', true);

$pdf->SetTextColor(0);
$pdf->SetFont('Arial','',8);

/* =============================
   REGISTROS
============================= */
if (!empty($registros)) {
    foreach ($registros as $r) {
        $pdf->Row([
            t($r['usuario_nombre']),
            t($r['accion']),
            t($r['modulo']),
            $r['fecha'],
            $r['hora']
        ], $w, 6);
    }
} else {
    $pdf->Cell(array_sum($w), 12, t('No hay registros en el periodo seleccionado'), 1, 1, 'C');
}

/* =============================
   SALIDA
============================= */
$pdf->Output('I', "bitacora_{$inicio}_a_{$fin}.pdf");
exit;

