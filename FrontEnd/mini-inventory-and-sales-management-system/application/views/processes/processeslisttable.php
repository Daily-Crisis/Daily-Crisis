<?php defined('BASEPATH') OR exit('') ?>

<div class='col-sm-6'>
    <?= isset($range) && !empty($range) ? $range : ""; ?>
</div>

<div class='col-sm-6 text-right'><b>Gasto total de los procesos:</b>Bs. <?=$cum_total ? number_format($cum_total, 2) : '0.00'?></div>

<div class='col-xs-12'>
    <div class="panel panel-primary">
        <!-- Default panel contents -->
        <div class="panel-heading">PROCESOS</div>
        <?php if($allProcesses): ?>
            <div class="table table-responsive">
                <table class="table table-bordered table-striped" style="background-color: #f5f5f5">
                    <thead>
                    <tr>
                        <th>Nº</th>
                        <th>TIPO</th>
                        <th>CÓDIGO</th>
                        <th>DESCRIPCIÓN</th>
                        <th>LOTES TRAB.</th>
                        <th>PRECIO POR LOTE</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach($allProcesses as $get): ?>
                        <tr>
                            <input type="hidden" value="<?=$get->id?>" class="curItemId">
                            <th class="itemSN"><?=$sn?>.</th>
                            <td><span id="itemName-<?=$get->id?>"><?=$get->name?></span></td>
                            <td><span id="itemCode-<?=$get->id?>"><?=$get->code?></td>
                            <td>
                            <span id="itemDesc-<?=$get->id?>" data-toggle="tooltip" title="<?=$get->description?>" data-placement="auto">
                                <?=word_limiter($get->description, 15)?>
                            </span>
                            </td>
                            <td class="<?=$get->quantity <= 10 ? 'bg-danger' : ($get->quantity <= 25 ? 'bg-warning' : '')?>">
                                <span id="itemQuantity-<?=$get->id?>"><?=$get->quantity?></span>
                            </td>
                            <td>Bs. <span id="itemPrice-<?=$get->id?>"><?=number_format($get->unitPrice, 2)?></span></td>

                            </tr>
                        <?php $sn++; ?>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <!-- table div end-->
        <?php else: ?>
            <ul><li>Sin procesos</li></ul>
        <?php endif; ?>
    </div>
    <!--- panel end-->
</div>

<!---Pagination div-->
<div class="col-sm-12 text-center">
    <ul class="pagination">
        <?= isset($links) ? $links : "" ?>
    </ul>
</div>