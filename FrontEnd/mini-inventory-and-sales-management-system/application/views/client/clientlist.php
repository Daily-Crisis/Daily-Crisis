<?php
defined('BASEPATH') OR exit('');
?>

<?php echo isset($range) && !empty($range) ? "Mostrando ".$range : ""?>
<div class="panel panel-primary">
    <div class="panel-heading">PERFILES DE CLIENTES</div>
    <?php if($allClients):?>
    <div class="table table-responsive">
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>Nº</th>
                    <th>NOMBRE</th>
                    <th>CORREO</th>
                    <th>NIT/CI</th>
                    <th>TELÉFONO</th>
                    <th>TIPO DE CLIENTE</th>
                    <th>FECHA DE CREACIÓN</th>
                    <th>EDITAR</th>
                    <th>ELIMINAR</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($allClients as $get):?>
                <?php if($get->deleted == "0"):?>
                    <tr>
                        <th class="clientSN"><?=$sn?>.</th>
                        <td class="clientName"><?=$get->first_name ." ". $get->last_name?></td>
                        <td class="hidden firstName"><?=$get->first_name?></td>
                        <td class="hidden lastName"><?=$get->last_name?></td>
                        <td class="clientEmail"><?=mailto($get->email)?></td>
                        <td class="clientMobile1"><?=$get->mobile1?></td>
                        <td class="clientMobile2"><?=$get->mobile2?></td>
                        <td class="clientRole"><?=ucfirst($get->role)?></td>
                        <td><?=date('jS M, Y h:i:sa', strtotime($get->created_on))?></td>

                        <td class="text-center editClient" id="edit-<?=$get->id?>">
                            <i class="fa fa-pencil pointer"></i>
                        </td>
                        <td class="text-center text-danger deleteClient" id="del-<?=$get->id?>">
                            <?php if($get->deleted === "1"): ?>
                            <a class="fa fa-trash deleteClient pointer">Deshacer eliminación</a>
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
        Sin perfiles de clientes
    <?php endif; ?>
</div>
<!-- Pagination -->
<div class="row text-center">
    <?php echo isset($links) ? $links : ""?>
</div>
<!-- Pagination ends -->