<?php
session_start();
if(!isset($_SESSION['user']) || $_SESSION['user']['tipo'] != 'admin'){
    header("Location: ../acesso.php"); exit;
}
include("../conexao.php");
require("../fpdf/fpdf.php");

$sql = "SELECT vendas.id, usuarios.nome AS cliente, produtos.nome AS produto,
    produtos.preco, vendas.quantidade,
    (vendas.quantidade * produtos.preco) AS total, vendas.data_venda
FROM vendas
INNER JOIN usuarios ON vendas.cliente_id = usuarios.id
INNER JOIN produtos ON vendas.produto_id = produtos.id
ORDER BY vendas.data_venda DESC";

$result = $conn->query($sql);

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetMargins(15,15,15);

// Cabeçalho
$pdf->SetFont('Arial','B',18);
$pdf->Cell(0,10,'AlphaSolutions',0,1,'C');
$pdf->SetFont('Arial','',11);
$pdf->Cell(0,7,'Relatorio de Vendas',0,1,'C');
$pdf->SetFont('Arial','',9);
$pdf->Cell(0,6,'Gerado em: '.date('d/m/Y H:i:s'),0,1,'C');
$pdf->Ln(6);

// Linha separadora
$pdf->SetDrawColor(30,111,232);
$pdf->SetLineWidth(0.5);
$pdf->Line(15,$pdf->GetY(),195,$pdf->GetY());
$pdf->Ln(6);

// Cabeçalho tabela
$pdf->SetFillColor(30,75,150);
$pdf->SetTextColor(255,255,255);
$pdf->SetFont('Arial','B',9);
$pdf->Cell(12,9,'ID',1,0,'C',true);
$pdf->Cell(42,9,'Cliente',1,0,'C',true);
$pdf->Cell(42,9,'Produto',1,0,'C',true);
$pdf->Cell(25,9,'Preco (MTS)',1,0,'C',true);
$pdf->Cell(14,9,'Qtd',1,0,'C',true);
$pdf->Cell(28,9,'Total (MTS)',1,0,'C',true);
$pdf->Cell(17,9,'Data',1,1,'C',true);

// Dados
$pdf->SetTextColor(30,30,30);
$pdf->SetFont('Arial','',8);
$total_geral = 0;
$fill = false;

if($result && $result->num_rows > 0){
    while($row=$result->fetch_assoc()){
        $total_geral += $row['total'];
        $pdf->SetFillColor($fill ? 240 : 255, $fill ? 245 : 255, $fill ? 255 : 255);
        $pdf->Cell(12,8,$row['id'],1,0,'C',$fill);
        $pdf->Cell(42,8,utf8_decode($row['cliente']),1,0,'L',$fill);
        $pdf->Cell(42,8,utf8_decode($row['produto']),1,0,'L',$fill);
        $pdf->Cell(25,8,number_format($row['preco'],2,',','.'),1,0,'R',$fill);
        $pdf->Cell(14,8,$row['quantidade'],1,0,'C',$fill);
        $pdf->Cell(28,8,number_format($row['total'],2,',','.'),1,0,'R',$fill);
        $pdf->Cell(17,8,date('d/m/Y',strtotime($row['data_venda'])),1,1,'C',$fill);
        $fill = !$fill;
    }
}

// Total geral
$pdf->SetFont('Arial','B',10);
$pdf->SetFillColor(30,75,150);
$pdf->SetTextColor(255,255,255);
$pdf->Cell(135,9,'TOTAL GERAL',1,0,'R',true);
$pdf->Cell(45,9,'MTS '.number_format($total_geral,2,',','.'),1,1,'C',true);

// Guardar e redirecionar
$dir = __DIR__.'/../relatorios/';
if(!file_exists($dir)) mkdir($dir,0777,true);
$pdf->Output('F',$dir.'relatorio_vendas.pdf');
header("Location: ../relatorios/relatorio_vendas.pdf");
exit;
