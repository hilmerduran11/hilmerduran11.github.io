<?php
require('../fpdf17/fpdf.php');


require_once "../model/Pedido.php";
$objPedido = new Pedido();
$query_cli = $objPedido->GetVenta($_GET["id"]);
$reg_cli = $query_cli->fetch_object();

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial','B',14);

$pdf->SetXY(170,40);
$pdf->Cell(0,0,utf8_decode($reg_cli->serie_comprobante."-".$reg_cli->num_comprobante));

$pdf->SetFont('Arial','B',10);
$pdf->SetXY(35,60);
$pdf->Cell(0,0,utf8_decode($reg_cli->nombre));
$pdf->SetXY(35,69);
$pdf->Cell(0,0,utf8_decode($reg_cli->direccion_calle));
//***Parte de la derecha
$pdf->SetXY(180,60);
$pdf->Cell(0,0,utf8_decode($reg_cli->num_documento));
$pdf->SetXY(180,69);
$pdf->Cell(0,0,utf8_decode($reg_cli->fecha));
$total=0;
//***Detalles de la factura
$query_ped = $objPedido->ImprimirDetallePedido($_GET["id"]);

$y=89;
while ($reg = $query_ped->fetch_object()) {
$pdf->SetXY(20,$y);
$pdf->MultiCell(10,0,$reg->cantidad);

$pdf->SetXY(32,$y);
$pdf->MultiCell(120,0,utf8_decode($reg->articulo." Serie: ".$reg->serie));

$pdf->SetXY(162,$y);
$pdf->MultiCell(25,0,$reg->precio_venta);

$pdf->SetXY(187,$y);
$pdf->MultiCell(25,0,$reg->sub_total);

$total=$total+$reg->sub_total;
$y=$y+7;
 
}
require_once "../ajax/Letras.php";

 $V=new EnLetras(); 
 $con_letra=strtoupper($V->ValorEnLetras($total,"NUEVOS SOLES")); 
$pdf->SetXY(32,145);
$pdf->MultiCell(120,0,$con_letra);


require_once "../model/Configuracion.php";
$objConfiguracion = new Configuracion();
$query_global = $objConfiguracion->Listar();
$reg_igv = $query_global->fetch_object();

$pdf->SetXY(187,153);
$pdf->MultiCell(20,0,$reg_igv->simbolo_moneda." ".sprintf("%0.2F", $total));


$pdf->Output();
?>