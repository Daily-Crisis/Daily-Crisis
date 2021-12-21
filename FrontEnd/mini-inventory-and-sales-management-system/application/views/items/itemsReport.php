<?php
defined('BASEPATH') OR exit('');

$total_earned = 0;

?>
<!DOCTYPE HTML>
<html>
    <head>
        <title>Reporte de Inventario</title>
		
        <!-- Favicon -->
        <link rel="shortcut icon" href="<?=base_url()?>public/images/icon.ico">
        <!-- favicon ends --->
        
        <!--- LOAD FILES -->
        <?php if((stristr($_SERVER['HTTP_HOST'], "localhost") !== FALSE) || (stristr($_SERVER['HTTP_HOST'], "192.168.") !== FALSE)|| (stristr($_SERVER['HTTP_HOST'], "127.0.0.") !== FALSE)): ?>
        <link rel="stylesheet" href="<?=base_url()?>public/bootstrap/css/bootstrap.min.css">

        <?php else: ?>
        
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">

        <?php endif; ?>
        
        <!-- custom CSS -->
        <link rel="stylesheet" href="<?= base_url() ?>public/css/main.css">
    </head>

    <body>
        <div class="container margin-top-5">
            <div class="row">
                <div class="col-xs-12 text-right hidden-print">
                    <button class="btn btn-primary btn-sm" onclick="window.print()">Print Report</button>
                </div>
            </div>

            <div class="row">
                <div class="col-xs-12 text-center">
                    <h4>Reporte Inventario</h4>
                 </div>
            </div>

            
            <div class="row margin-top-5">
                <div class="col-xs-12 table-responsive">
                    <div class="panel panel-primary">
                        <!-- Default panel contents -->
                        <?php if($allItems): ?>
                        <div class="table table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Nº</th>
                                        <th>NOMBRE</th>
                                        <th>CÓDIGO</th>
                                        <th>DESCRIPCIÓN</th>
                                        <th>EN STOCK</th>
                                        <th>PRECIO UNITARIO</th>
                                        <th>VENTA TOTAL</th>
                                        <th>GANANCIA TOTAL EN EL ELEMENTO</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $sn = 1;?>
                                    <?php foreach($allItems as $get): ?>
                                    <tr>
                                      <th><?=$sn?>.</th>
                                      <td><?=$get->name?></td>
                                      <td><?=$get->code?></td>
                                      <td> <?=word_limiter($get->description, 15)?></td>
                                      <td><?=$get->quantity?> </td>
                                      <td>Bs. <?=number_format($get->unitPrice, 2)?></td>
                                      <td><?=$this->genmod->gettablecol('transactions', 'SUM(quantity)', 'itemCode', $get->code)?></td>
                                      <td> Bs. <?=number_format($this->genmod->gettablecol('transactions', 'SUM(totalPrice)', 'itemCode', $get->code), 2)?></td>
                                    <?php $sn++; ?>
                                    <!--?php $total_earned += $get->cum_total; ?-->
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <!-- table div end--->
                        <?php else: ?>
                            <ul><li>No se encontraron transacciones dentro de las fechas especificadas</li></ul>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="row" style="margin-bottom: 10px">
                <div class="col-xs-6">
                    <button class="btn btn-primary btn-sm hidden-print" onclick="window.print()">Imprimir Reportes</button>
                </div>
                
                <!--div class="col-xs-6 text-right">
                    <h4>Total Recaudado: &#8369;<?=number_format($total_earned, 2)?></h4>
                </div-->
            </div>
        </div>
        <!--- panel end-->
    </body>
</html>