'use strict';

$(document).ready(function(){
    checkDocumentVisibility(checkLogin);//check document visibility in order to confirm user's log in status

    //load all processes once the page is ready
    lapr();



    //WHEN USE BARCODE SCANNER IS CLICKED
    $("#useBarcodeScanner").click(function(e){
        e.preventDefault();

        $("#processCode").focus();
    });


    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Toggle the form to add a new process
     */
    $("#createProcess").click(function(){
        $("#processesListDiv").toggleClass("col-sm-8", "col-sm-12");
        $("#createNewProcessDiv").toggleClass('hidden');
        $("#processName").focus();
    });


    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


    $(".cancelAddProcess").click(function(){
        //reset and hide the form
        document.getElementById("addNewProcessForm").reset();//reset the form
        $("#createNewProcessDiv").addClass('hidden');//hide the form
        $("#processesListDiv").attr('class', "col-sm-12");//make the table span the whole div
    });


    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    //execute when 'auto-generate' checkbox is clicked while trying to add a new process
    $("#gen4me").click(function(){
        //if checked, generate a unique process code for user. Else, clear field
        if($("#gen4me").prop("checked")){
            var codeExist = false;

            do{
                //generate random string, reduce the length to 10 and convert to uppercase
                var rand = Math.random().toString(36).slice(2).substring(0, 10).toUpperCase();
                $("#processCode").val(rand);//paste the code in input
                $("#processCodeErr").text('');//remove the error message being displayed (if any)

                //check whether code exist for another process
                $.ajax({
                    type: 'get',
                    url: appRoot+"processes/gettablecol/id/code/"+rand,
                    success: function(returnedData){
                        codeExist = returnedData.status;//returnedData.status could be either 1 or 0
                    }
                });
            }

            while(codeExist);

        }

        else{
            $("#processCode").val("");
        }
    });

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    //handles the submission of adding new process
    $("#addNewProcess").click(function(e){
        e.preventDefault();

        changeInnerHTML(['processNameErr', 'processQuantityErr', 'processPriceErr', 'processCodeErr', 'addCustErrMsg'], "");

        var processName = $("#processName").val();
        var processQuantity = $("#processQuantity").val();
        var processPrice = $("#processPrice").val().replace(",", "");
        var processCode = $("#processCode").val();
        var processDescription = $("#processDescription").val();

        if(!processName || !processQuantity || !processPrice || !processCode){
            !processName ? $("#processNameErr").text("required") : "";
            !processQuantity ? $("#processQuantityErr").text("required") : "";
            !processPrice ? $("#processPriceErr").text("required") : "";
            !processCode ? $("#processCodeErr").text("required") : "";

            $("#addCustErrMsg").text("One or more required fields are empty");

            return;
        }

        displayFlashMsg("Adding Process '"+processName+"'", "fa fa-spinner faa-spin animated", '', '');

        $.ajax({
            type: "post",
            url: appRoot+"processes/add",
            data:{processName:processName, processQuantity:processQuantity, processPrice:processPrice, processDescription:processDescription, processCode:processCode},

            success: function(returnedData){
                if(returnedData.status === 1){
                    changeFlashMsgContent(returnedData.msg, "text-success", '', 1500);
                    document.getElementById("addNewProcessForm").reset();

                    //refresh the processes list table
                    lapr();

                    //return focus to process code input to allow adding process with barcode scanner
                    $("#processCode").focus();
                }

                else{
                    hideFlashMsg();

                    //display all errors
                    $("#processNameErr").text(returnedData.processName);
                    $("#processPriceErr").text(returnedData.processPrice);
                    $("#processCodeErr").text(returnedData.processCode);
                    $("#processQuantityErr").text(returnedData.processQuantity);
                    $("#addCustErrMsg").text(returnedData.msg);
                }
            },

            error: function(){
                if(!navigator.onLine){
                    changeFlashMsgContent("You appear to be offline. Please reconnect to the internet and try again", "", "red", "");
                }

                else{
                    changeFlashMsgContent("Unable to p_rocess your request at this time. Pls try again later!", "", "red", "");
                }
            }
        });
    });


    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    //reload processes list table when events occur
    $("#processesListPerPage, #processesListSortBy").change(function(){
        displayFlashMsg("Please wait...", spinnerClass, "", "");
        lapr();
    });

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    $("#processSearch").keyup(function(){
        var value = $(this).val();

        if(value){
            $.ajax({
                url: appRoot+"search/processsearch",
                type: "get",
                data: {v:value},
                success: function(returnedData){
                    $("#processesListTable").html(returnedData.processesListTable);
                }
            });
        }

        else{
            //reload the table if all text in search box has been cleared
            displayFlashMsg("Loading page...", spinnerClass, "", "");
            lapr();
        }
    });

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    //triggers when an process's "edit" icon is clicked
    $("#processesListTable").on('click', ".editProcess", function(e){
        e.preventDefault();

        //get process info
        var processId = $(this).attr('id').split("-")[1];
        var processDesc = $("#processDesc-"+processId).attr('title');
        var processName = $("#processName-"+processId).html();
        var processPrice = $("#processPrice-"+processId).html().split(".")[0].replace(",", "");
        var processCode = $("#processCode-"+processId).html();

        //prefill form with info
        $("#processIdEdit").val(processId);
        $("#processNameEdit").val(processName);
        $("#processCodeEdit").val(processCode);
        $("#processPriceEdit").val(processPrice);
        $("#processDescriptionEdit").val(processDesc);

        //remove all error messages that might exist
        $("#editProcessFMsg").html("");
        $("#processNameEditErr").html("");
        $("#processCodeEditErr").html("");
        $("#processPriceEditErr").html("");

        //launch modal
        $("#editProcessModal").modal('show');
    });

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    $("#editProcessSubmit").click(function(){
        var processName = $("#processNameEdit").val();
        var processPrice = $("#processPriceEdit").val();
        var processDesc = $("#processDescriptionEdit").val();
        var processId = $("#processIdEdit").val();
        var processCode = $("#processCodeEdit").val();

        if(!processName || !processPrice || !processId){
            !processName ? $("#processNameEditErr").html("Process name cannot be empty") : "";
            !processPrice ? $("#processPriceEditErr").html("Process price cannot be empty") : "";
            !processId ? $("#editProcessFMsg").html("Unknown process") : "";
            return;
        }

        $("#editProcessFMsg").css('color', 'black').html("<i class='"+spinnerClass+"'></i> P_rocessing your request....");

        $.ajax({
            method: "POST",
            url: appRoot+"processes/edit",
            data: {processName:processName, processPrice:processPrice, processDesc:processDesc, _iId:processId, processCode:processCode}
        }).done(function(returnedData){
            if(returnedData.status === 1){
                $("#editProcessFMsg").css('color', 'green').html("Process successfully updated");

                setTimeout(function(){
                    $("#editProcessModal").modal('hide');
                }, 1000);

                lapr();
            }

            else{
                $("#editProcessFMsg").css('color', 'red').html("One or more required fields are empty or not properly filled");

                $("#processNameEditErr").html(returnedData.processName);
                $("#processCodeEditErr").html(returnedData.processCode);
                $("#processPriceEditErr").html(returnedData.processPrice);
            }
        }).fail(function(){
            $("#editProcessFMsg").css('color', 'red').html("Unable to p_rocess your request at this time. Please check your internet connection and try again");
        });
    });


    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    //trigers the modal to update stock
    $("#processesListTable").on('click', '.updateStock', function(){
        //get process info and fill the form with them
        var processId = $(this).attr('id').split("-")[1];
        var processName = $("#processName-"+processId).html();
        var processCurQuantity = $("#processQuantity-"+processId).html();
        var processCode = $("#processCode-"+processId).html();

        $("#stockUpdateProcessId").val(processId);
        $("#stockUpdateProcessName").val(processName);
        $("#stockUpdateProcessCode").val(processCode);
        $("#stockUpdateProcessQInStock").val(processCurQuantity);

        $("#updateStockModal").modal('show');
    });


    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    //runs when the update type is changed while trying to update stock
    //sets a default description if update type is "newStock"
    $("#stockUpdateType").on('change', function(){
        var updateType = $("#stockUpdateType").val();

        if(updateType && (updateType === 'newStock')){
            $("#stockUpdateDescription").val("New processes were purchased");
        }

        else{
            $("#stockUpdateDescription").val("");
        }
    });

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    //handles the updating of process's quantity in stock
    $("#stockUpdateSubmit").click(function(){
        var updateType = $("#stockUpdateType").val();
        var stockUpdateQuantity = $("#stockUpdateQuantity").val();
        var stockUpdateDescription = $("#stockUpdateDescription").val();
        var processId = $("#stockUpdateProcessId").val();

        if(!updateType || !stockUpdateQuantity || !stockUpdateDescription || !processId){
            !updateType ? $("#stockUpdateTypeErr").html("required") : "";
            !stockUpdateQuantity ? $("#stockUpdateQuantityErr").html("required") : "";
            !stockUpdateDescription ? $("#stockUpdateDescriptionErr").html("required") : "";
            !processId ? $("#stockUpdateProcessIdErr").html("required") : "";

            return;
        }

        $("#stockUpdateFMsg").html("<i class='"+spinnerClass+"'></i> Updating Stock.....");

        $.ajax({
            method: "POST",
            url: appRoot+"processes/updatestock",
            data: {_iId:processId, _upType:updateType, qty:stockUpdateQuantity, desc:stockUpdateDescription}
        }).done(function(returnedData){
            if(returnedData.status === 1){
                $("#stockUpdateFMsg").html(returnedData.msg);

                //refresh processes' list
                lapr();

                //reset form
                document.getElementById("updateStockForm").reset();

                //dismiss modal after some secs
                setTimeout(function(){
                    $("#updateStockModal").modal('hide');//hide modal
                    $("#stockUpdateFMsg").html("");//remove msg
                }, 1000);
            }

            else{
                $("#stockUpdateFMsg").html(returnedData.msg);

                $("#stockUpdateTypeErr").html(returnedData._upType);
                $("#stockUpdateQuantityErr").html(returnedData.qty);
                $("#stockUpdateDescriptionErr").html(returnedData.desc);
            }
        }).fail(function(){
            $("#stockUpdateFMsg").html("Unable to p_rocess your request at this time. Please check your internet connection and try again");
        });
    });

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    //PREVENT AUTO-SUBMISSION BY THE BARCODE SCANNER
    $("#processCode").keypress(function(e){
        if(e.which === 13){
            e.preventDefault();

            //change to next input by triggering the tab keyboard
            $("#processName").focus();
        }
    });



    //TO DELETE AN process (The process will be marked as "deleted" instead of removing it totally from the db)
    $("#processesListTable").on('click', '.delProcess', function(e){
        e.preventDefault();

        //get the process id
        var processId = $(this).parents('tr').find('.curProcessId').val();
        var processRow = $(this).closest('tr');//to be used in removing the currently deleted row

        if(processId){
            var confirm = window.confirm("Are you sure you want to delete process? This cannot be undone.");

            if(confirm){
                displayFlashMsg('Please wait...', spinnerClass, 'black');

                $.ajax({
                    url: appRoot+"processes/delete",
                    method: "POST",
                    data: {i:processId}
                }).done(function(rd){
                    if(rd.status === 1){
                        //remove process from list, update processes' SN, display success msg
                        $(processRow).remove();

                        //update the SN
                        resetProcessSN();

                        //display success message
                        changeFlashMsgContent('Process deleted', '', 'green', 1000);
                    }

                    else{

                    }
                }).fail(function(){
                    console.log('Req Failed');
                });
            }
        }
    });
});



/**
 * "lapr" = "load Processes List Table"
 * @param {type} url
 * @returns {undefined}
 */
function lapr(url){
    var orderBy = $("#processesListSortBy").val().split("-")[0];
    var orderFormat = $("#processesListSortBy").val().split("-")[1];
    var limit = $("#processesListPerPage").val();


    $.ajax({
        type:'get',
        url: url ? url : appRoot+"processes/lapr/",
        data: {orderBy:orderBy, orderFormat:orderFormat, limit:limit},

        success: function(returnedData){
            hideFlashMsg();
            $("#processesListTable").html(returnedData.processesListTable);
        },

        error: function(){

        }
    });

    return false;
}


/**
 * "vittrhist" = "View process's transaction history"
 * @param {type} processId
 * @returns {Boolean}
 */
function vittrhist(processId){
    if(processId){

    }

    return false;
}



function resetProcessSN(){
    $(".processSN").each(function(i){
        $(this).html(parseInt(i)+1);
    });
}