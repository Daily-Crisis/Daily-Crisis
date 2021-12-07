<?php defined('BASEPATH') OR exit('') ?>

<!--An process's transactions history--->
<div class="col-sm-4">
    <div class="row">
        <div class="col-sm-12 form-group-sm form-inline">
            <div class="col-sm-4">
                Mostrar
                <select id="processPerPage" class="form-control">
                    <option value="5">5</option>
                    <option value="10" selected>10</option>
                    <option value="15">15</option>
                    <option value="20">20</option>
                </select>
            </div>
            <div class="col-sm-4">
                <select id="sortProcesss" class="form-control">
                    <option value="">Ordenar por</option>
                    <option value="code-asc">Codigo del elemento</option>
                </select>
            </div>
            <div class="col-sm-4">
                <input type="search" id="processSearch" class="form-control" placeholder="Search Processes">
            </div>
        </div>
    </div>
    <br>

    <!--Row of process's transactions -->
    <div class="row">
        <div class="col-sm-12" id='processTransHistoryTable'>

        </div>
    </div>
</div>
<!--End of an process's transactions history--->