<?php
require('classes/fpdf/fpdf.php');

class PDF extends FPDF{
	function Header(){
	    $this->Image('img/alturaslogo.png',10,6,30);
	    $this->SetFont('Arial','',10);
	    $this->Cell(150);
	    $this->Cell(0,0,'Number',0,0,'L');
	    $this->Cell(-18);
	    $this->SetTextColor(225,0,0);
	    $this->Cell(0,0,'0001',0,0,'L');
	    
	    $this->SetFont('Arial','',12);
	    $this->SetTextColor(0,0,0);
	    $this->setX(50);
	    $this->Cell(10,10,'REQUEST FOR SETUP (RFS) FORM',0,0,'L');
	    $this->Ln(8);
	}
}
$pdf = new PDF('L','mm',array(215.9,139.7));
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Helvetica','',10);
$pdf->SetFillColor(230,230,230);
$pdf->Cell(0,0,'',1,'','',true);

$pdf->setXY(10,17);$pdf->Cell(0,15,'Company Name:');
$pdf->setXY(50,17);$pdf->Cell(0,15,'MARCELA FARMS INCORPORATED');

$pdf->setXY(10,22);$pdf->Cell(0,15,'Business Unit:');
$pdf->setXY(50,22);$pdf->Cell(0,15,'Prawn Farm');

$pdf->setXY(10,27);$pdf->Cell(0,15,'Contact Number:');
$pdf->setXY(50,27);$pdf->Cell(0,15,'0934234234');

$pdf->setXY(110,27);$pdf->Cell(0,15,'Date:');
$pdf->setXY(130,27);$pdf->Cell(0,15,'11/11/11');

$pdf->setXY(10,32);$pdf->Cell(0,15,'Address:');
$pdf->setXY(50,32);$pdf->Cell(0,15,'Cagayan, Inabanga, Bohol');

$pdf->setXY(10,37);$pdf->Cell(0,15,'Request Mode:');
$pdf->setXY(50,37);$pdf->Cell(0,15,'Add');

$pdf->setXY(110,37);$pdf->Cell(0,15,'Type of Request:');
$pdf->setXY(145,37);$pdf->Cell(0,15,'Customer/s');

$pdf->setXY(10,48);
$pdf->Cell(0,0,'',1,'','',true);
$pdf->setXY(10,52);
$pdf->Cell(0,0,'Details:');
$pdf->setXY(10,55);
$pdf->MultiCell(0,5,"Glenn Mejias => Inabanga asdfasdf asdfasdfa",0,'L',0);
$pdf->setXY(10,95);$pdf->Cell(0,0,'Purpose:');
$pdf->setXY(50,95);$pdf->Cell(0,0,'Add new customer');

//for($i=1;$i<=40;$i++)
//    $pdf->Cell(0,10,'Printing line number '.$i,0,1);
$pdf->Output();
?>