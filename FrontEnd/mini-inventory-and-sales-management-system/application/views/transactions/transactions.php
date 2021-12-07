<?php
defined('BASEPATH') OR exit('');

$current_items = [];

if(isset($items) && !empty($items)){    
    foreach($items as $get){
        $current_items[$get->code] = $get->name;
    }
}
?>

<style href="<?=base_url('public/ext/datetimepicker/bootstrap-datepicker.min.css')?>" rel="stylesheet"></style>

<script>
    var currentItems = <?=json_encode($current_items)?>;
</script>

<div class="pwell hidden-print">   
    <div class="row">
        <div class="col-sm-12">
            <!--- Row to create new reservation-->
            <div class="row">
                <div class="col-sm-3">
                    <span class="pointer text-primary">
                        <button class='btn btn-primary btn-sm' id='showResForm'><i class="fa fa-plus"></i> Nueva Reservación</button>
                    </span>
                </div>
                <div class="col-sm-3">
                    <span class="pointer text-primary">
                        <button class='btn btn-primary btn-sm' data-toggle='modal' data-target='#reportModal'>
                            <i class="fa fa-newspaper-o"></i> Generar Reporte
                        </button>
                    </span>
                </div>
            </div>
            <br>
            <!--- End of row to create new reservation-->
            <!---form to create new reservations--->
            <div class="row collapse" id="newResDiv">
                <!---div to display reservation form--->
                <div class="col-sm-12" id="salesResFormDiv">
                    <div class="well">
                        <form name="salesResForm" id="salesResForm" role="form">
                            <div class="text-center errMsg" id='newResErrMsg'></div>
                            <br>

                            <div class="row">
                                <div class="col-sm-12">
                                    <!--Cloned div comes here--->
                                    <div id="appendClonedDivHere"></div>
                                    <!--End of cloned div here--->
                                    
                                    <!--- Text to click to add another item to reservation-->
                                    <div class="row">
                                        <div class="col-sm-2 text-primary pointer">
                                            <button class="btn btn-primary btn-sm" id="clickToClone"><i class="fa fa-plus"></i> Adicionar Elemento</button>
                                        </div>
                                        
                                        <br class="visible-xs">
                                        
                                        <div class="col-sm-2 form-group-sm">
                                            <input type="text" id="barcodeText" class="form-control" placeholder="Código ID" autofocus>
                                            <span class="help-block errMsg" id="itemCodeNotFoundMsg"></span>
                                        </div>
                                    </div>
                                    <!-- End of text to click to add another item to reservation-->
                                    <br>
                                    
                                    <div class="row">
                                        <div class="col-sm-3 form-group-sm">
                                            <label for="vat">Cantidad a Comprar</label>
                                            <input type="number" min="0" id="vat" class="form-control" value="0">
                                        </div>
                                        
                                        <div class="col-sm-3 form-group-sm">
                                            <label for="discount">Descuento(%)</label>
                                            <input type="number" min="0" id="discount" class="form-control" value="0">
                                        </div>
                                        
                                        <div class="col-sm-3 form-group-sm">
                                            <label for="discount">Descuento(valor)</label>
                                            <input type="number" min="0" id="discountValue" class="form-control" value="0">
                                        </div>
                                        
                                        <div class="col-sm-3 form-group-sm">
                                            <label for="modeOfPayment">Modo de Pago</label>
                                            <select class="form-control checkField" id="modeOfPayment">
                                                <option value="">---</option>
                                                <option value="Cash">Efectivo</option>
                                                <option value="POS">Tarjeta</option>
                                                <option value="Cash and POS">Efectivo y Tarjeta</option>
                                            </select>
                                            <span class="help-block errMsg" id="modeOfPaymentErr"></span>
                                        </div>
                                    </div>
                                        
                                    <div class="row">
                                        <div class="col-sm-4 form-group-sm">
                                            <label for="cumAmount">Monto Total</label>
                                            <span id="cumAmount" class="form-control">0.00</span>
                                        </div>
                                        
                                        <div class="col-sm-4 form-group-sm">
                                            <div class="cashAndPos hidden">
                                                <label for="cashAmount">Efectivo</label>
                                                <input type="text" class="form-control" id="cashAmount">
                                                <span class="help-block errMsg"></span>
                                            </div>

                                            <div class="cashAndPos hidden">
                                                <label for="posAmount">Terminal de Venta</label>
                                                <input type="text" class="form-control" id="posAmount">
                                                <span class="help-block errMsg"></span>
                                            </div>

                                            <div id="amountTenderedDiv">
                                                <label for="amountTendered" id="amountTenderedLabel">Importe recibido</label>
                                                <input type="text" class="form-control" id="amountTendered">
                                                <span class="help-block errMsg" id="amountTenderedErr"></span>
                                            </div>
                                        </div>
                                        
                                        <div class="col-sm-4 form-group-sm">
                                            <label for="changeDue">Saldo</label>
                                            <span class="form-control" id="changeDue"></span>
                                        </div>
                                    </div>
                                        
                                    <div class="row">
                                        <div class="col-sm-4 form-group-sm">
                                            <label for="custName">Nombre del Cliente</label>
                                            <input type="text" id="custName" class="form-control" placeholder="Nombre">
                                        </div>
                                        
                                        <div class="col-sm-4 form-group-sm">
                                            <label for="custPhone">Teléfono del Cliente</label>
                                            <input type="tel" id="custPhone" class="form-control" placeholder="Número de Teléfono">
                                        </div>
                                        
                                        <div class="col-sm-4 form-group-sm">
                                            <label for="custEmail">Correo Electrónico Cliente</label>
                                            <input type="email" id="custEmail" class="form-control" placeholder="Dirección Correo Electrónico">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <br>
                            <div class="row">
                                <!--<div class="col-sm-2 form-group-sm">
                                    <button class="btn btn-primary btn-sm" id='useScanner'>Usar Escaner de Código de Barras</button>
                                </div>-->
                                <br class="visible-xs">
                                <div class="col-sm-6"></div>
                                <br class="visible-xs">
                                <div class="col-sm-4 form-group-sm">
                                    <button type="button" class="btn btn-primary btn-sm" id="confirmSaleOrder">Confirmar</button>
                                    <button type="button" class="btn btn-danger btn-sm" id="cancelSaleOrder">Limpiar</button>
                                    <button type="button" class="btn btn-danger btn-sm" id="hideResForm">Cerrar</button>
                                </div>
                            </div>
                        </form><!-- end of form-->
                    </div>
                </div>
                <!-- end of div to display reservation form-->
            </div>
            <!--end of form-->
    
            <br><br>
            <!-- sort and co row-->
            <div class="row">
                <div class="col-sm-12">
                    <div class="col-sm-3 form-inline form-group-sm">
                        <label for="resListPerPage">Por Página</label>
                        <select id="resListPerPage" class="form-control">
                            <option value="1">1</option>
                            <option value="5">5</option>
                            <option value="10" selected>10</option>
                            <option value="15">15</option>
                            <option value="20">20</option>
                            <option value="20">20</option>
                            <option value="30">30</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </div>

                    <div class="col-sm-5 form-group-sm form-inline">
                        <label for="resListSortBy">Ordenar por</label>
                        <select id="resListSortBy" class="form-control">
                            <option value="resId-DESC">Fecha(Más Reciente Primero)</option>
                            <option value="restId-ASC">Fecha(Más Antiguo Primero)</option>
                            <option value="quantity-DESC">Cantidad (Más Alto Primero)</option>
                            <option value="quantity-ASC">Cantidad (Más Bajo Primero)</option>
                            <option value="totalPrice-DESC">Precio Total (Más Alto Primero)</option>
                            <option value="totalPrice-ASC">Precio Total (Más Bajo Primero)</option>
                            <option value="totalMoneySpent-DESC">Total de Dinero Invertido (Más Alto Primero)</option>
                            <option value="totalMoneySpent-ASC">Total de Dinero Invertido (Más Bajo Primero)</option>
                        </select>
                    </div>

                    <div class="col-sm-4 form-inline form-group-sm">
                        <label for='resSearch'><i class="fa fa-search"></i></label>
                        <input type="search" id="resSearch" class="form-control" placeholder="Buscar Reservas">
                    </div>
                </div>
            </div>
            <!-- end of sort and co div-->
        </div>
    </div>
    
    <hr>
    
    <!-- reservation list table-->
    <div class="row">
        <!-- reservation list div-->
        <div class="col-sm-12" id="resListTable"></div>
        <!-- End of reservations div-->
    </div>
    <!-- End of reservations list table-->
</div>


<div class="row hidden" id="divToClone">
    <div class="col-sm-4 form-group-sm">
        <label>Elemento</label>
        <select class="form-control selectedItemDefault" onchange="selectedItem(this)"></select>
    </div>

    <div class="col-sm-2 form-group-sm itemAvailQtyDiv">
        <label>Cantidad Disponible</label>
        <span class="form-control itemAvailQty">0</span>
    </div>

    <div class="col-sm-2 form-group-sm">
        <label>Precio Unitario</label>
        <span class="form-control itemUnitPrice">0.00</span>
    </div>

    <div class="col-sm-1 form-group-sm itemResQtyDiv">
        <label>Cantidad</label>
        <input type="number" min="0" class="form-control itemResQty" value="0">
        <span class="help-block itemResQtyErr errMsg"></span>
    </div>

    <div class="col-sm-2 form-group-sm">
        <label>Precio Total</label>
        <span class="form-control itemTotalPrice">0.00</span>
    </div>
    
    <br class="visible-xs">
    
    <div class="col-sm-1">
        <button class="close retrit">&times;</button>
    </div>
    
    <br class="visible-xs">
</div>


<div class="modal fade" id='reportModal' data-backdrop='static' role='dialog'>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <div class="close" data-dismiss='modal'>&times;</div>
                <h4 class="text-center">Generar Reporte</h4>
            </div>
            
            <div class="modal-body">
                <div class="row" id="datePair">
                    <div class="col-sm-6 form-group-sm">
                        <label class="control-label">Desde</label>
                        <div class="input-group">
                            <div class="input-group-addon">
                                <span><i class="fa fa-calendar"></i></span>
                            </div>
                            <input type="text" id='resFrom' class="form-control date start" placeholder="YYYY-MM-DD">
                        </div>
                        <span class="help-block errMsg" id='resFromErr'></span>
                    </div>

                    <div class="col-sm-6 form-group-sm">
                        <label class="control-label">Antes de</label>
                        <div class="input-group">
                            <div class="input-group-addon">
                                <span><i class="fa fa-calendar"></i></span>
                            </div>
                            <input type="text" id='resTo' class="form-control date end" placeholder="YYYY-MM-DD">
                        </div>
                        <span class="help-block errMsg" id='resToErr'></span>
                    </div>
                </div>
            </div>
            
            <div class="modal-footer">
                <button class="btn btn-success" id='clickToGen'>Generar</button>
                <button class="btn btn-danger" data-dismiss='modal'>Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!---End of copy of div to clone when adding more items to sales reservation---->
<script src="<?=base_url()?>public/js/reservations.js"></script>
<script src="<?=base_url('public/ext/datetimepicker/bootstrap-datepicker.min.js')?>"></script>
<script src="<?=base_url('public/ext/datetimepicker/jquery.timepicker.min.js')?>"></script>
<script src="<?=base_url()?>public/ext/datetimepicker/datepair.min.js"></script>
<script src="<?=base_url()?>public/ext/datetimepicker/jquery.datepair.min.js"></script>