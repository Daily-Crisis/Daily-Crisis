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
                    <th>Nº</th>
                    <th>CÓDIGO</th>
                    <th>TOTAL ELEMENTOS</th>
                    <th>MONTO TOTAL</th>
                    <th>MONTO CANCELADO</th>
                    <th>SALDO</th>
                    <th>MODO DE PAGO</th>
                    <th>ENCARGADO</th>
                    <th>CLIENTE</th>
                    <th>FECHA</th>
                    <th>ANULAR RESERVA</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($allReservations as $get): ?>
                 <?php if($get->quantity >= "1"):?>
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
                    <td class="text-center deleteSupplier " >
                            <?php if($get->quantity < "1"): ?>
                            <a class="fa  deleteSupplier pointer">Reserva Anulada</a>
                            <?php else: ?>
                            <i class="fa fa-trash pointer"></i>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php $sn++; ?>
                <?php endif;?>
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