<?php
    @session_start();
    ob_start();
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2 lblNamePage">Settings</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <button type="button" class="btn btn-outline-secondary" id="btnUpdateData"><i class="bi bi-check2"></i> Save changes</button>
        </div>
    </div>
</div>
<form id="configForm" class="needs-validation" novalidate>
    <div class="row g-3">
        <div class="col-3">
            <label for="inputshipingCost" class="form-label lblShipCost">Shipping Cost</label>
            <input type="text" class="form-control" placeholder="Shipping Cost" aria-label="Shipping Cost" id="inputshipingCost" required>
        </div>
        <div class="col-3">
            <label for="inputshipingFree" class="form-label lblShipFree">Free Shipping</label>
            <input type="text" class="form-control" placeholder="Free Shipping" aria-label="Free Shipping" id="inputshipingFree" required>
        </div>
        <div class="col-3">
            <label for="inputtax" class="form-label lblTax">Tax</label>
            <input type="text" class="form-control" placeholder="Tax" aria-label="Tax" id="inputtax" required>
        </div>
    </div>

    <hr>

    <div class="row g-3">
        <div class="col-3">
            <label for="inputUname" class="form-label lblUname">User name</label>
            <input type="text" class="form-control" placeholder="User name" aria-label="User name" id="inputUname" readonly value="<?php echo $_SESSION['authData']->owner; ?>" required>
        </div>
        <div class="col-4">
            <label for="inputMail" class="form-label lblEmail">Email</label>
            <input type="mail" class="form-control" placeholder="Enter a email" aria-label="Enter a email" id="inputMail" value="<?php echo $_SESSION['authData']->email; ?>" required>
        </div>
        <div class="col-3">
            <label for="inputPass" class="form-label lblPassword">Change Password</label>
            <input type="password" class="form-control" placeholder="New Password" aria-label="New Password" id="inputPass">
        </div>
    </div>

    <hr>

    <div class="row g-3">
        <div class="col-6">
            <label for="inputpaypalid" class="form-label lblApiKey">Paypal cliente ID</label>
            <input type="text" class="form-control" placeholder="Client ID" id="inputpaypalid" value="" required>
        </div>
    </div>
</form>

<script type="text/javascript">
    var confButonText = "",
        strMesage = "";
    $(document).ready(function(){
        currentPage = "Settings";

        $("#btnUpdateData").click( fnUpdateData);

        //listar Valores de configuracion
        fnGetconfig();
    });

    function fnGetconfig(){
        let objData = {
            "_method":"Get"
        };
        $.post("../core/controllers/setting.php", objData, function(result) {
             $.each( result.data, function( index, item){
                $(`#input${item.parameter}`).val(item.value);
             });
        });
    }

    function fnUpdateData(){
        let forms = document.querySelectorAll('.needs-validation'),
            continuar = true;

        Array.prototype.slice.call(forms).forEach(function (formv){ 
            if (!formv.checkValidity()) {
                    continuar = false;
            }

            formv.classList.add('was-validated');
        });

        if(!continuar)
            return false;
        
        $("#btnUpdateData").attr("disabled","disabled");
        $("#btnUpdateData").html('<i class="bi bi-clock-history"></i> Updating');

        let objData = {
            "_method":"updateData",
            "shipingCost": $("#inputshipingCost").val(),
            "shipingFree": $("#inputshipingFree").val(),
            "owner": $("#inputUname").val(),
            "email": $("#inputMail").val(),
            "password": $("#inputPass").val(),
            "tax": $("#inputtax").val(),
            "paypalid": $("#inputpaypalid").val()
        };

        $.post("../core/controllers/setting.php", objData, function(result) {
            alert(strMesage);
            isNew = <?php echo $_SESSION['authData']->isDefault; ?>;

            $("#btnUpdateData").removeAttr("disabled");
            $("#btnUpdateData").html('<i class="bi bi-check2"></i> ' + confButonText);
        });
    }

    function changePageLang(myLang){
        $(".lblNamePage").html(myLang.namePage);
        $("#btnUpdateData").html(`<i class="bi bi-check2"></i> ${myLang.butonText}`);
        confButonText = myLang.butonText;
        $(".lblShipCost").html(myLang.labelShipCost);
        $(".lblShipFree").html(myLang.labelShipFree);
        $(".lblTax").html(myLang.labelTax);
        $(".lblUname").html(myLang.labelUname);
        $(".lblEmail").html(myLang.labelEmail);
        $(".lblPassword").html(myLang.labelPassword);
        $(".lblApiKey").html(myLang.lblApiKey);
        $("#inputpaypalid").attr("placeholder", myLang.inputApiKey);

        $("#inputshipingCost").attr("placeholder", myLang.labelShipCost);
        $("#inputshipingFree").attr("placeholder", myLang.labelShipFree);
        $("#inputtax").attr("placeholder", myLang.labelTax);
        $("#inputMail").attr("placeholder", myLang.labelEmail);
        $("#inputPass").attr("placeholder", myLang.labelPassword);

        strMesage = myLang.ctrMessage;
    }
</script>

<?php
    $content = ob_get_contents();
    ob_end_clean();

    include("index.php");
?>