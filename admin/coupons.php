<?php
    @session_start();
    ob_start();
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2 lblNamePage">Coupons</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <form id="addCouponForm" class="needs-validation" novalidate>
            <div class="input-group me-2">
                <input type="text" class="form-control" placeholder="Coupon Code" id="inputCode" autocomplete="off" style="width: 40%;" required>
                <input type="text" class="form-control" placeholder="Value" id="inputValue" autocomplete="off" required>
                <select class="form-select" id="cboTipo">
                    <option value="1">%</option>
                    <option value="2">$</option>
                </select>
                <button class="btn btn-outline-secondary btn-sm" type="button" id="btnAddCoupon"><i class="bi bi-plus-lg"></i> Add coupon</button>
            </div>
        </form>
    </div>
</div>

<table class="table" id="couponList">
    <thead class="table-light">
        <th class="colA">#</th>
        <th class="colB">Code</th>
        <th class="colC">Discount</th>
        <th class="colD"></th>
    </thead>
</table>

<script type="text/javascript">
    let dataTableCoupon = null,
        formatter = new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: 'USD',
            minimumFractionDigits: 2
        }),
        strMesage = "do you want to delete this coupon";

    $(document).ready(function(){
        currentPage = "Coupon";

        $("#btnAddCoupon").click( fnRegisterCoupon);

        //listar cupones
        fnGetCoupons();
    });

    function fnRegisterCoupon(){
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

        $("#btnAddCoupon").attr("disabled","disabled");
        $("#btnAddCoupon").html('<i class="bi bi-clock-history"></i> Registering');

        let objData = {
            "_method":"_POST",
            "code": $("#inputCode").val(),
            "value": $("#inputValue").val(),
            "tipo": $("#cboTipo").val()
        };

        $.post("../core/controllers/coupon.php", objData, function(result) {
            $("#btnAddCoupon").removeAttr("disabled");
            $("#btnAddCoupon").html('<i class="bi bi-plus-lg"></i> Add Category');

            $("#inputCode").val("");
            $("#inputValue").val("");
            $("#cboTipo").val(1);

            fnGetCoupons();
        });
    }

    function fnGetCoupons(){
        let objData = {
            "_method":"Get"
        };

        if (dataTableCoupon != null)
            dataTableCoupon.destroy();

        $.post("../core/controllers/coupon.php", objData, function(result) {
            let mypaging =  false;

            if( (result.data).length > 20 )
                mypaging = true;

            dataTableCoupon = $("#couponList").DataTable({
                data: result.data,
                order: [[ 0, "desc" ]],
                columns:
                [
                    {
                        data: 'id',
                        width: "20px"
                    },
                    {
                        data: 'codigo',
                    },
                    {
                        data: null,
                        render: function(data, type, row){
                            if(row.tipo == 1){
                                return `%${row.valor}`;
                            }else{
                                return `${formatter.format(row.valor)}`;
                            }
                        }
                    },
                    {
                        title: '',
                        data: null,
                        orderable: false,
                        class: "text-center",
                        render: function ( data, type, row )
                        {
                            return `
                                <a href="javascript:void(0);" class="btn btn-outline-danger btnDeleteCoupon" title="Delete"><i class="bi bi-trash"></i></a>
                            `;
                        }
                    }
                ],
                "fnDrawCallback":function(oSettings){
                    $(".btnDeleteCoupon").unbind().click(function(){
                        let data = getData($(this), dataTableCoupon),
                            buton = $(this);

                        if (confirm(`${strMesage} (${data.codigo})?`)){
                            buton.attr("disabled","disabled");
                            buton.html('<i class="bi bi-clock-history"></i>');

                            let objData = {
                                "_method":"Delete",
                                "couponId": data.id
                            };

                            $.post("../core/controllers/coupon.php", objData, function(result) {
                                buton.removeAttr("disabled");
                                buton.html('<i class="bi bi-trash"></i>');

                                fnGetCoupons();
                            });

                        }
                    });
                },
                searching: false,
                pageLength: 20,
                info: false,
                lengthChange: false,
                paging: mypaging
            });
        });
    }

    function changePageLang(myLang){        
        $(".lblNamePage").html(myLang.namePage);
        $("#inputCode").attr("placeholder", myLang.inputCode);
        $("#inputValue").attr("placeholder", myLang.inputValue);
        $("#btnAddCoupon").html(`<i class="bi bi-plus-lg"></i> ${myLang.inputBtn}`);
        $(".colB").html(myLang.colB);
        $(".colC").html(myLang.colC);

        strMesage = myLang.ctrtoRemove;
    }
</script>



<?php
    $content = ob_get_contents();
    ob_end_clean();

    include("index.php");
?>