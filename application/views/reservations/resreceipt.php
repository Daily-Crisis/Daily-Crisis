<?php
defined('BASEPATH') OR exit('');
?>
<?php if($allResInfo):?>
    <?php $sn = 1; ?>
    <div id="resReceiptToPrint">
        <div class="row">
            <div class="col-xs-12 text-center text-uppercase">
                <center style='margin-bottom:5px'><img src="<?=base_url()?>public/images/receipt_logo.png" alt="logo" class="img-responsive" width="60px"></center>
                <b>Almacen 1410, Bloque 5, Estante 2</b>
                <div>+591 79408866, +252 558301</div>
            </div>
        </div>
        <div class="row text-center">
            <div class="col-sm-12">
                <b><?=isset($resDate) ? date('jS M, Y h:i:sa', strtotime($resDate)) : ""?></b>
            </div>
        </div>

        <div class="row" style="margin-top:2px">
            <div class="col-sm-12">
                <label>Factura No:</label>
                <span><?=isset($ref) ? $ref : ""?></span>
            </div>
        </div>

        <div class="row" style='font-weight:bold'>
            <div class="col-xs-4">Elemento</div>
            <div class="col-xs-4">Cntd x Precio</div>
            <div class="col-xs-4">Total(Bs. )</div>
        </div>
        <hr style='margin-top:2px; margin-bottom:0px'>
        <?php $init_total = 0; ?>
        <?php foreach($allResInfo as $get):?>
            <div class="row">
                <div class="col-xs-4"><?=ellipsize($get['itemName'], 10);?></div>
                <div class="col-xs-4"><?=$get['quantity'] . "x" .number_format($get['unitPrice'], 2)?></div>
                <div class="col-xs-4"><?=number_format($get['totalPrice'], 2)?></div>
            </div>
            <?php $init_total += $get['totalPrice'];?>
        <?php endforeach; ?>
        <hr style='margin-top:2px; margin-bottom:0px'>
        <div class="row">
            <div class="col-xs-12 text-right">
                <b>Total: Bs. <?=isset($init_total) ? number_format($init_total, 2) : 0?></b>
            </div>
        </div>
        <hr style='margin-top:2px; margin-bottom:0px'>
        <div class="row">
            <div class="col-xs-12 text-right">
                <b>Decuento(<?=$discountPercentage?>%): Bs. <?=isset($discountAmount) ? number_format($discountAmount, 2) : 0?></b>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12 text-right">
                <?php if($vatPercentage > 0): ?>
                    <b>Monto Cancelado (<?=$vatPercentage?>%): Bs. <?=isset($vatAmount) ? number_format($vatAmount, 2) : ""?></b>
                <?php else: ?>
                    Monto Cancelado
                <?php endif; ?>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12 text-right">
                <b>TOTAL: Bs. <?=isset($cumAmount) ? number_format($cumAmount, 2) : ""?></b>
            </div>
        </div>
        <hr style='margin-top:5px; margin-bottom:0px'>
        <div class="row margin-top-5">
            <div class="col-xs-12">
                <b>Modo de Pago: <?=isset($_mop) ? str_replace("_", " ", $_mop) : ""?></b>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <b>Monto Cancelado: Bs. <?=isset($amountTendered) ? number_format($amountTendered, 2) : ""?></b>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <b>Saldo: Bs. <?=isset($changeDue) ? number_format($changeDue, 2) : ""?></b>
            </div>
        </div>
        <hr style='margin-top:5px; margin-bottom:0px'>
        <div class="row margin-top-5">
            <div class="col-xs-12">
                <b>Nombre del Cliente: <?=$cust_name?></b>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <b>Teléfono del Cliente: <?=$cust_phone?></b>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <b>Correo Electrónico del Cliente: <?=$cust_email?></b>
            </div>
        </div>
        <br>
        <div class="row">
            <div class="col-xs-12 text-center">Gracias</div>
        </div>
    </div>
    <br class="hidden-print">
    <div class="row hidden-print">
        <div class="col-sm-12">
            <div class="text-center">
                <button type="button" class="btn btn-primary ptr">
                    <i class="fa fa-print"></i> Imprimir
                </button>

                <button type="button" data-dismiss='modal' class="btn btn-danger">
                    <i class="fa fa-close"></i> Cerrar
                </button>
            </div>
        </div>
    </div>
    <br class="hidden-print">
<?php endif;?>