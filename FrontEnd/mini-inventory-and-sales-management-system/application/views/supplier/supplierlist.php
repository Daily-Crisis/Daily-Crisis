<?php
defined('BASEPATH') OR exit('');
?>

<?php echo isset($range) && !empty($range) ? "Showing ".$range : ""?>
<div class="panel panel-primary">
    <div class="panel-heading">PROVEEDORES</div>
    <?php if($allSuppliers):?>
    <div class="table table-responsive">
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>CI</th>
                    <th>NOMBRE</th>
                    <th>CORREO</th>
                    <th>TELÉFONO</th>
                    <th>OBSERVACIONES</th>
                    <th>EMPRESA</th>
                    <th>FECHA DE CREACIÓN</th>
                    <th>EDITAR</th>
                    <th>ESTADO DE PERFIL</th>
                    <th>ELIMINAR</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($allSuppliers as $get):?>
                    <tr>
                        <th><?=$sn?>.</th>
                        <td class="supplierName"><?=$get->first_name?></td>
                        <td class="hidden firstName"><?=$get->first_name?></td>
                        <td class="hidden lastName"><?=$get->last_name?></td>
                        <td class="supplierEmail"><?=mailto($get->email)?></td>
                        <td class="supplierMobile1"><?=$get->mobile1?></td>
                        <td class="supplierMobile2"><?=$get->mobile2?></td>
                        <td class="supplierRole"><?=$get->last_name?></td>
                        <td><?=date('jS M, Y h:i:sa', strtotime($get->created_on))?></td>
                        <td class="text-center editSupplier" id="edit-<?=$get->id?>">
                            <i class="fa fa-pencil pointer"></i>
                        </td>
                        <td class="text-center suspendSupplier text-success" id="sus-<?=$get->id?>">
                            <?php if($get->account_status === "1"): ?>
                            <i class="fa fa-toggle-on pointer"></i>
                            <?php else: ?>
                            <i class="fa fa-toggle-off pointer"></i>
                            <?php endif; ?>
                        </td>
                        <td class="text-center text-danger deleteSupplier" id="del-<?=$get->id?>">
                            <?php if($get->deleted === "1"): ?>
                            <a class="pointer">Deshacer eliminación</a>
                            <?php else: ?>
                            <i class="fa fa-trash pointer"></i>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php $sn++;?>
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