<?php defined('BASEPATH') OR exit('') ?>

<?= isset($range) && !empty($range) ? $range : ""; ?>
<div class="panel panel-primary">
    <!-- Default panel contents -->
    <div class="panel-heading">RESERVAS</div>
    <?php if($allReservations): ?>
    <div class="table table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>NIT</th>
                    <th>Código</th>
                    <th>Total de Elementos</th>
                    <th>Monto Total</th>
                    <th>Monto Cancelado</th>
                    <th>Saldo</th>
                    <th>Modo de Pago</th>
                    <th>Encargado</th>
                    <th>Cliente</th>
                    <th>Fecha</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($allReservations as $get): ?>
                <tr>
                    <th><?= $sn ?>.</th>
                    <td><a class="pointer vtr" title="Pulsa Para Ver el Recibo"><?= $get->ref ?></a></td>
                    <td><?= $get->quantity ?></td>
                    <td>Bs. <?= number_format($get->totalMoneySpent, 2) ?></td>
                    <td>Bs. <?= number_format($get->amountTendered, 2) ?></td>
                    <td>Bs. <?= number_format($get->changeDue, 2) ?></td>
                    <td><?=  str_replace("_", " ", $get->modeOfPayment)?></td>
                    <td><?=$get->staffName?></td>
                    <td><?=$get->cust_name?> - <?=$get->cust_phone?> - <?=$get->cust_email?></td>
                    <td><?= date('jS M, Y h:ia', strtotime($get->resDate)) ?></td>
                </tr>
                <?php $sn++; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<!-- table div end-->
    <?php else: ?>
        <ul><li>No existen Registros</li></ul>
    <?php endif; ?>
    
    <!--Pagination div-->
    <div class="col-sm-12 text-center">
        <ul class="pagination">
            <?= isset($links) ? $links : "" ?>
        </ul>
    </div>
</div>
<!-- panel end-->