<?php defined('BASEPATH') OR exit('') ?>

<div class='col-sm-6'>
    <?= isset($range) && !empty($range) ? $range : ""; ?>
</div>

<div class='col-sm-6 text-right'><b>Valor/precio total de los elementos:</b>Bs. <?=$cum_total ? number_format($cum_total, 2) : '0.00'?></div>

<div class='col-xs-12'>
    <div class="panel panel-primary">
        <!-- Default panel contents -->
        <div class="panel-heading">Elementos</div>
        <?php if($allProcesses): ?>
            <div class="table table-responsive">
                <table class="table table-bordered table-striped" style="background-color: #f5f5f5">
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
                        <th>ACTUALIZAR CANTIDAD</th>
                        <th>EDITAR</th>
                        <th>ELIMINAR</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach($allProcesses as $get): ?>
                        <tr>
                            <input type="hidden" value="<?=$get->id?>" class="curProcessId">
                            <th class="processSN"><?=$sn?>.</th>
                            <td><span id="processName-<?=$get->id?>"><?=$get->name?></span></td>
                            <td><span id="processCode-<?=$get->id?>"><?=$get->code?></td>
                            <td>
                            <span id="processDesc-<?=$get->id?>" data-toggle="tooltip" title="<?=$get->description?>" data-placement="auto">
                                <?=word_limiter($get->description, 15)?>
                            </span>
                            </td>
                            <td class="<?=$get->quantity <= 10 ? 'bg-danger' : ($get->quantity <= 25 ? 'bg-warning' : '')?>">
                                <span id="processQuantity-<?=$get->id?>"><?=$get->quantity?></span>
                            </td>
                            <td>Bs. <span id="processPrice-<?=$get->id?>"><?=number_format($get->unitPrice, 2)?></span></td>
                            <td><?=$this->genmod->gettablecol('transactions', 'SUM(quantity)', 'processCode', $get->code)?></td>
                            <td>
                                Bs. <?=number_format($this->genmod->gettablecol('transactions', 'SUM(totalPrice)', 'processCode', $get->code), 2)?>
                            </td>
                            <td><a class="pointer updateStock" id="stock-<?=$get->id?>">Actualizar cantidad</a></td>
                            <td class="text-center text-primary">
                                <span class="editProcess" id="edit-<?=$get->id?>"><i class="fa fa-pencil pointer"></i> </span>
                            </td>
                            <td class="text-center"><i class="fa fa-trash text-danger delProcesspointer"></i></td>
                        </tr>
                        <?php $sn++; ?>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <!-- table div end-->
        <?php else: ?>
            <ul><li>Sin elementos</li></ul>
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
