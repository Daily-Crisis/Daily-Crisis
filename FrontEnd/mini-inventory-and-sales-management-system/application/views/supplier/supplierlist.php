<?php
defined('BASEPATH') OR exit('');
?>

<?php echo isset($range) && !empty($range) ? "Mostrando ".$range : ""?>
<div class="panel panel-primary">
    <div class="panel-heading">PROVEEDORES</div>
    <?php if($allSuppliers):?>
    <div class="table table-responsive">
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>Nº</th>
                    <th>PROVEEDOR</th>
                    <th>TIPO DE PROVEEDOR</th>
                    <th>INSUMO</th>
                    <th>CORREO</th>
                    <th>TELÉFONO</th>
                    <th>TELÉFONO 2</th>
                    <th>FECHA DE CREACIÓN</th>
                    <th>EDITAR</th>
                    <th>ELIMINAR</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($allSuppliers as $get):?>
                <?php if($get->deleted == "0"):?>
                    <tr>
                        <th class="suppSN"><?=$sn?>.</th>
                        <td class="supplierName"><?=$get->first_name?></td>
                        <td class="supplierRole"><?=$get->role?></td>
                        <td class="supplierRole"><?=$get->last_name?></td>
                        <td class="hidden firstName"><?=$get->first_name?></td>
                        <td class="hidden lastName"><?=$get->last_name?></td>
                        <td class="supplierEmail"><?=mailto($get->email)?></td>
                        <td class="supplierMobile1"><?=$get->mobile1?></td>
                        <td class="supplierMobile2"><?=$get->mobile2?></td>
                        <td><?=date('jS M, Y h:i:sa', strtotime($get->created_on))?></td>
                        <td class="text-center editSupplier" id="edit-<?=$get->id?>">
                            <i class="fa fa-pencil pointer"></i>
                        </td>

                        <td class="text-center text-danger deleteSupplier" id="del-<?=$get->id?>">
                            <?php if($get->deleted === "1"): ?>
                            <a class="fa fa-trash deleteSupplier pointer">Deshacer eliminación</a>
                            <?php else: ?>
                            <i class="fa fa-trash pointer"></i>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php $sn++;?>
                    <?php endif;?>
                <?php endforeach;?>
            </tbody>
        </table>
    </div>
    <?php else:?>
        Sin cuentas proveedores
    <?php endif; ?>
</div>
<!-- Pagination -->
<div class="row text-center">
    <?php echo isset($links) ? $links : ""?>
</div>
<!-- Pagination ends -->