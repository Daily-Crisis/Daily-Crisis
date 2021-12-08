'use strict';

$(document).ready(function(){
    checkDocumentVisibility(checkLogin);//check document visibility in order to confirm user's log in status
	
	
    //load all client once the page is ready
    //function header: lacl_(url)
    lacl_();
    
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    
    //reload the list of client when fields are changed
    $("#clientListSortBy, #clientListPerPage").change(function(){
        displayFlashMsg("Please wait...", spinnerClass, "", "");
        lacl_();
    });
    
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    
    //load and show page when pagination link is clicked
    $("#allClient").on('click', '.lnp', function(e){
        e.preventDefault();
		
        displayFlashMsg("Please wait...", spinnerClass, "", "");

        lacl_($(this).attr('href'));

        return false;
    });
    
    
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    
    //Check to ensure the password and retype password fields are the same
    $("#passwordDup").on('keyup change focusout focus focusin', function(){
        var orig = $("#passwordOrig").val();
        var dup = $("#passwordDup").val();
        
        if(dup !== orig){
            //show error
            $("#passwordDupErr").addClass('fa');
            $("#passwordDupErr").addClass('fa-times');
            $("#passwordDupErr").removeClass('fa-check');
            $("#passwordDupErr").css('color', 'red');
            $("#passwordDupErr").html("");
        }
        
        else{
            //show success
            $("#passwordDupErr").addClass('fa');
            $("#passwordDupErr").addClass('fa-check');
            $("#passwordDupErr").removeClass('fa-times');
            $("#passwordDupErr").css('color', 'green');
            $("#passwordDupErr").html("");
        }
    });
    
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    
    
    //handles the addition of new client details .i.e. when "add client" button is clicked
    $("#addClientSubmit").click(function(e){
        e.preventDefault();
        
        //reset all error msgs in case they are set
        changeInnerHTML(['firstNameErr', 'lastNameErr', 'emailErr', 'roleErr', 'mobile1Err', 'mobile2Err', 'passwordOrigErr', 'passwordDupErr'],
        "");
        
        var firstName = $("#firstName").val();
        var lastName = $("#lastName").val();
        var email = $("#email").val();
        var role = $("#role").val();
        var mobile1 = $("#mobile1").val();
        var mobile2 = $("#mobile2").val();
        var passwordOrig = $("#passwordOrig").val();
        var passwordDup = $("#passwordDup").val();
        
        //ensure all required fields are filled
        if(!firstName || !lastName || !email || !role || !mobile1 || !passwordOrig || !passwordDup){
            !firstName ? changeInnerHTML('firstNameErr', "required") : "";
            !lastName ? changeInnerHTML('lastNameErr', "required") : "";
            !email ? changeInnerHTML('emailErr', "required") : "";
            !role ? changeInnerHTML('roleErr', "required") : "";
            !mobile1 ? changeInnerHTML('mobile1Err', "required") : "";
            !passwordOrig ? changeInnerHTML('passwordOrigErr', "required") : "";
            !passwordDup ? changeInnerHTML('passwordDupErr', 'required') : "";
            
            return;
        }
        
        //display message telling user action is being processed
        $("#fMsgIcon").attr('class', spinnerClass);
        $("#fMsg").text(" Processing...");
        
        //make ajax request if all is well
        $.ajax({
            method: "POST",
            url: appRoot+"clients/add",
            data: {firstName:firstName, lastName:lastName, email:email, role:role, mobile1:mobile1, mobile2:mobile2,
                passwordOrig:passwordOrig, passwordDup:passwordDup}
        }).done(function(returnedData){
            $("#fMsgIcon").removeClass();//remove spinner
                
            if(returnedData.status === 1){
                $("#fMsg").css('color', 'green').text(returnedData.msg);

                //reset the form
                document.getElementById("addNewClientForm").reset();

                //close the modal
                setTimeout(function(){
                    $("#fMsg").text("");
                    $("#addNewClientModal").modal('hide');
                }, 1000);

                //reset all error msgs in case they are set
                changeInnerHTML(['firstNameErr', 'lastNameErr', 'emailErr', 'roleErr', 'mobile1Err', 'mobile2Err', 'passwordOrigErr', 'passwordDupErr'],
                "");

                //refresh client list table
                lacl_();

            }

            else{
                //display error message returned
                $("#fMsg").css('color', 'red').html(returnedData.msg);

                //display individual error messages if applied
                $("#firstNameErr").text(returnedData.firstName);
                $("#lastNameErr").text(returnedData.lastName);
                $("#emailErr").text(returnedData.email);
                $("#role").text(returnedData.role);
                $("#mobile1Err").text(returnedData.mobile1);
                $("#mobile2Err").text(returnedData.mobile2);
                $("#passwordOrigErr").text(returnedData.passwordOrig);
                $("#passwordDupErr").text(returnedData.passwordDup);
            }
        }).fail(function(){
            if(!navigator.onLine){
                $("#fMsg").css('color', 'red').text("Network error! Pls check your network connection");
            }
        });
    });
    
    
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    
    
    //handles the updating of client details
    $("#editClientSubmit").click(function(e){
        e.preventDefault();
        
        if(formChanges("editClientForm")){
            //reset all error msgs in case they are set
            changeInnerHTML(['firstNameEditErr', 'lastNameEditErr', 'emailEditErr', 'roleEditErr', 'mobile1Err', 'mobile2Err'], "");

            var firstName = $("#firstNameEdit").val();
            var lastName = $("#lastNameEdit").val();
            var email = $("#emailEdit").val();
            var mobile1 = $("#mobile1Edit").val();
            var mobile2 = $("#mobile2Edit").val();
            var role = $("#roleEdit").val();
            var clientId = $("#clientId").val();

            //ensure all required fields are filled
            if(!firstName || !lastName || !email || !role || !mobile1){
                !firstName ? changeInnerHTML('firstNameEditErr', "required") : "";
                !lastName ? changeInnerHTML('lastNameEditErr', "required") : "";
                !email ? changeInnerHTML('emailEditErr', "required") : "";
                !mobile1 ? changeInnerHTML('mobile1EditErr', "required") : "";
                !role ? changeInnerHTML('roleEditErr', "required") : "";

                return;
            }

            if(!clientId){
                $("#fMsgEdit").text("An unexpected error occured while trying to update client's details");
                return;
            }

            //display message telling user action is being processed
            $("#fMsgEditIcon").attr('class', spinnerClass);
            $("#fMsgEdit").text(" Updating details...");

            //make ajax request if all is well
            $.ajax({
                method: "POST",
                url: appRoot+"clients/update",
                data: {firstName:firstName, lastName:lastName, email:email, role:role, mobile1:mobile1, mobile2:mobile2, clientId:clientId}
            }).done(function(returnedData){
                $("#fMsgEditIcon").removeClass();//remove spinner

                if(returnedData.status === 1){
                    $("#fMsgEdit").css('color', 'green').text(returnedData.msg);

                    //reset the form and close the modal
                    setTimeout(function(){
                        $("#fMsgEdit").text("");
                        $("#editClientModal").modal('hide');
                    }, 1000);

                    //reset all error msgs in case they are set
                    changeInnerHTML(['firstNameEditErr', 'lastNameEditErr', 'emailEditErr', 'roleEditErr', 'mobile1Err', 'mobile2Err'], "");

                    //refresh client list table
                    lacl_();

                }

                else{
                    //display error message returned
                    $("#fMsgEdit").css('color', 'red').html(returnedData.msg);

                    //display individual error messages if applied
                    $("#firstNameEditErr").html(returnedData.firstName);
                    $("#lastNameEditErr").html(returnedData.lastName);
                    $("#emailEditErr").html(returnedData.email);
                    $("#mobile1EditErr").html(returnedData.mobile1);
                    $("#mobile2EditErr").html(returnedData.mobile2);
                    $("#roleEditErr").html(returnedData.role);
                }
            }).fail(function(){
                    if(!navigator.onLine){
                        $("#fMsgEdit").css('color', 'red').html("Network error! Pls check your network connection");
                    }
                });
        }
        
        else{
            $("#fMsgEdit").html("No changes were made");
        }
    });
    
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    
    
    //handles client search
    $("#clientSearch").on('keyup change', function(){
        var value = $(this).val();
        
        if(value){//search only if there is at least one char in input
            $.ajax({
                type: "get",
                url: appRoot+"search/clientsearch",
                data: {v:value},
                success: function(returnedData){
                    $("#allClient").html(returnedData.clientTable);
                }
            });
        }
        
        else{
            lacl_();
        }
    });
    
    
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    
    //When the toggle on/off button is clicked to change the account status of an client (i.e. suspend or lift suspension)
    $("#allClient").on('click', '.suspendClient', function(){
        var ElemId = $(this).attr('id');
        
        var clientId = ElemId.split("-")[1];//get the clientId
        
        //show spinner
        $("#"+ElemId).html("<i class='"+spinnerClass+"'</i>");
        
        if(clientId){
            $.ajax({
                url: appRoot+"clients/suspend",
                method: "POST",
                data: {_aId:clientId}
            }).done(function(returnedData){
                if(returnedData.status === 1){
                    //change the icon to "on" if it's "off" before the change and vice-versa
                    var newIconClass = returnedData._ns === 1 ? "fa fa-toggle-on pointer" : "fa fa-toggle-off pointer";
                    
                    //change the icon
                    $("#sus-"+returnedData._aId).html("<i class='"+ newIconClass +"'></i>");
                    
                }
                
                else{
                    console.log('err');
                }
            });
        }
    });
    
    
    /*
    ******************************************************************************************************************************
    ******************************************************************************************************************************
    ******************************************************************************************************************************
    ******************************************************************************************************************************
    ******************************************************************************************************************************
    */
    
    
    //When the trash icon in front of an client account is clicked on the client list table (i.e. to delete the account)
    $("#allClient").on('click', '.deleteClient', function(){
        var confirm = window.confirm("Proceed?");
        
        if(confirm){
            var ElemId = $(this).attr('id');

            var clientId = ElemId.split("-")[1];//get the clientId

            //show spinner
            $("#"+ElemId).html("<i class='"+spinnerClass+"'</i>");

            if(clientId){
                $.ajax({
                    url: appRoot+"clients/delete",
                    method: "POST",
                    data: {_aId:clientId}
                }).done(function(returnedData){
                    if(returnedData.status === 1){
                       
                        //change the icon to "undo delete" if it's "active" before the change and vice-versa
                        var newHTML = returnedData._nv === 1 ? "<a class='pointer'>Undo Delete</a>" : "<i class='fa fa-trash pointer'></i>";

                        //change the icon
                        $("#del-"+returnedData._aId).html(newHTML);

                    }

                    else{
                        alert(returnedData.status);
                    }
                });
            }
        }
    });
    
    
    /*
    ******************************************************************************************************************************
    ******************************************************************************************************************************
    ******************************************************************************************************************************
    ******************************************************************************************************************************
    ******************************************************************************************************************************
    */
    
    
    //to launch the modal to allow for the editing of client info
    $("#allClient").on('click', '.editClient', function(){
        
        var clientId = $(this).attr('id').split("-")[1];
        
        $("#clientId").val(clientId);
        
        //get info of client with clientId and prefill the form with it
        //alert($(this).siblings(".clientEmail").children('a').html());
        var firstName = $(this).siblings(".firstName").html();
        var lastName = $(this).siblings(".lastName").html();
        var role = $(this).siblings(".clientRole").html();
        var email = $(this).siblings(".clientEmail").children('a').html();
        var mobile1 = $(this).siblings(".clientMobile1").html();
        var mobile2 = $(this).siblings(".clientMobile2").html();
        
        //prefill the form fields
        $("#firstNameEdit").val(firstName);
        $("#lastNameEdit").val(lastName);
        $("#emailEdit").val(email);
        $("#mobile1Edit").val(mobile1);
        $("#mobile2Edit").val(mobile2);
        $("#role").val(role);
        
        $("#editClientModal").modal('show');
    });
    
});



/*
***************************************************************************************************************************************
***************************************************************************************************************************************
***************************************************************************************************************************************
***************************************************************************************************************************************
***************************************************************************************************************************************
*/

/**
 * lacl_ = "Load all clients"
 * @returns {undefined}
 */
function lacl_(url){
    var orderBy = $("#clientListSortBy").val().split("-")[0];
    var orderFormat = $("#clientListSortBy").val().split("-")[1];
    var limit = $("#clientListPerPage").val();
    
    $.ajax({
        type:'get',
        url: url ? url : appRoot+"clients/lacl_/",
        data: {orderBy:orderBy, orderFormat:orderFormat, limit:limit},
     }).done(function(returnedData){
            hideFlashMsg();
			
            $("#allClient").html(returnedData.clientTable);
        });
}



///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////



///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////



///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////




///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////



///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////




///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////





///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////