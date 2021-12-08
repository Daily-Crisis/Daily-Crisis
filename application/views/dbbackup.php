<?php
defined('BASEPATH') OR exit('');
?>

<div class="pwell hidden-print"> 
    <div class="row">
        <div class="col-sm-6">
            <a href="<?=base_url()?>misc/dldb" download="1410inventory.sqlite"><button class="btn btn-primary">Descargar archivos</button></a>
        </div>

        <br class="visible-xs">
        
        <div class="col-sm-6">
            <button class="btn btn-info" id="importdb">Importar archivos</button>
            <span class="help-block">El archivo debe ser de tipo .sqlite</span>
            <input type="file" id="selecteddbfile" class="hidden" accept=".sqlite">
            <span class="help-block" id="dbFileMsg"></span>
        </div>
    </div>
</div>