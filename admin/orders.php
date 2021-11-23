<?php
    @session_start();
    ob_start();
?>

<style type="text/css">
    .dropdown-menu {
        width: 20rem !important;
    }
</style>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2 lblNamePage">Orders</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="input-group mb-3 dvFilter"></div>
    </div>
</div>

<div class="table-responsive">
    <table class="table" id="orderList">
        <thead class="table-light">
            <th class="colA">#</th>
            <th class="colB">Order date</th>
            <th class="colC">Amount</th>
            <th class="colD">Ship price</th>
            <th class="colE">Ship to</th>
            <th class="colF">Receive</th>
            <th class="colG">Paypal reference</th>
            <th class="colH">Status</th>
            <th class="colI"></th>
        </thead>
    </table>
</div>

<!-- Panel lateral para ver detalles del envio -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasOrder" aria-labelledby="offcanvasWithBackdropLabel"  >
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="offcanvasWithBackdropLabel">Order detail</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col" class="pnlColA">Thumbnail</th>
                    <th scope="col" class="pnlColB">Name</th>
                    <th scope="col" class="pnlColC">Quantity</th>
                    <th scope="col" class="pnlColD">Price</th>
                    <th scope="col" class="pnlColE">Amount</th>
                    <th scope="col" class="pnlColF">Options</th>
                </tr>
            </thead>
            <tbody id="tblDetalle"></tbody>
            <tr class="rowClone d-none">
                <th class="lblId" scope="row"></th>
                <td class="lblImg"></td>
                <td class="lblName"></td>
                <td class="lblQty"></td>
                <td class="lblPrice"></td>
                <td class="lblAmount"></td>
                <td class="lblOption"></td>
            </tr>
        </table>
        <p class="lblCoupon lead d-none"></p>
        <p class="lblShipDate lead d-none"></p>
        <p class="lblShipTrak lead d-none"></p>
    </div>
</div>

<script type="text/javascript">
    var dataTableOrder = null,
        formatter = new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: 'USD',
            minimumFractionDigits: 2
        });

    $(document).ready(function(){
        currentPage = "Orders";
        var myOffcanvas = document.getElementById('offcanvasOrder');
        myOffcanvas.addEventListener('hidden.bs.offcanvas', function () {
        });

        getOrders();
    });

    function getOrders(){
        let objData = {
            "_method":"GetOrders"
        };

        if (dataTableOrder != null)
            dataTableOrder.destroy();

        $.post("../core/controllers/checkout.php", objData, function(result) {
            let mypaging =  false;

            if( (result.data).length > 20 )
                mypaging = true;

            dataTableOrder = $("#orderList").DataTable({
                data: result.data.order,
                order: [[ 0, "desc" ]],
                columns:
                [
                    {
                        data: 'id',
                        width: "20px",
                        render: function(data, type, row) {
                            return pad(data, 5);
                        }
                    },
                    {
                        data: 'order_date'
                    },
                    {
                        data: 'amount',
                        render: function(data, type, row) {
                            return formatter.format(data);
                        }
                    },
                    {
                        data: 'ship_price',
                        render: function(data, type, row) {
                            return formatter.format(data);
                        }
                    },
                    {
                        data: 'shipping_address',
                        render: function(data, type, row) {
                            let objAddress = JSON.parse(data),
                                strAddress = "";

                            $.each(objAddress, function(index, item){
                                strAddress += `${item}, `
                            });

                            return strAddress;
                        }
                    },
                    {
                        data: 'payment_data',
                        render: function(data, type, row) {
                            let paypalorder = JSON.parse(data),
                                str = `${paypalorder.purchase_units[0].shipping.name.full_name}`

                            return str;
                        }
                    },
                    {
                        data: 'payment_data',
                        render: function(data, type, row) {
                            let paypalorder = JSON.parse(data),
                                str = `${paypalorder.purchase_units[0].payments.captures[0].id}`
                            return str;
                        }
                    },
                    {
                        data: 'status',
                        render: function(data, type, row) {
                            if(data == 0)
                                return "Canceled";

                            if(data == 1)
                                return "New order";

                            if(data == 2)
                                return "Order sent";

                            if(data == 3)
                                return "Order completed";
                        }
                    },
                    {
                        data: null,
                        orderable: false,
                        class: "text-center",
                        width: "180px",
                        render: function ( data, type, row ) {
                            let btn = `<a href="javascript:void(0);" class="btn btn-outline-secondary btnDetailOrder me-2" title="View details" data-bs-toggle="offcanvas" data-bs-target="#offcanvasOrder"><i class="bi bi-card-checklist"></i></a>`,
                                btnFinih = `<button type="button" class="btn btn-danger btnFinalize ms-2">Finalize</button>`;

                            if(row.status == 1 || row.status == 2){
                                return btn + `
                                    
                                    <a href="javascript:void(0);" class="btn btn-outline-danger btnDeleteOrder me-2" title="Cancel order"><i class="bi bi-dash-circle"></i></a>
                                    <div class="btn-group" role="group">
                                        <button id="btnGroupDrop1" type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="bi bi-geo-alt"></i>
                                        </button>
                                        <form class="dropdown-menu p-4 dropdown-menu-lg-end">
                                            <div class="mb-3">
                                                <label for="inputOrderId${row.id}" class="form-label">Enter the tracking guide and package name</label>
                                                <input type="text" class="form-control" id="inputOrderId${row.id}" placeholder="#0000000, package name" autocomplete="off">
                                            </div>
                                            <button type="button" class="btn btn-success tmpSetTracking">Ok</button>
                                            ${ (row.shipper_tracking) ? btnFinih : '' }
                                        </form>
                                    </div>
                                `;
                            }else{
                                return btn;
                            }
                        }
                    }
                ],
                "fnDrawCallback":function(oSettings){
                    $(".btnDetailOrder").unbind().click(function(){
                        let data = getData($(this), dataTableOrder),
                            buton = $(this);

                        buton.attr("disabled","disabled");
                        buton.html('<i class="bi bi-clock-history"></i>');

                        let objData = {
                            "_method":"getDetailOrder",
                            "orderId": data.id
                        };

                        $.post("../core/controllers/checkout.php", objData, function(result) {
                            buton.removeAttr("disabled");
                            buton.html('<i class="bi bi-card-checklist"></i>');

                            $("#tblDetalle").html("");
                            $.each(result.data, function(index, item){
                                let row = $(".rowClone").clone();

                                row.find(".lblId").html(index + 1);
                                row.find(".lblImg").html(`<img src="../${item.thumbnail}" alt="twbs" height="32" class="rounded flex-shrink-0">`);
                                row.find(".lblName").html(item.name);
                                row.find(".lblQty").html(item.quantity);
                                row.find(".lblPrice").html(formatter.format(item.price));
                                row.find(".lblAmount").html(formatter.format(item.amount));

                                let options = JSON.parse(item.selected_options),
                                    strOptions = '';

                                if(options.size)
                                    strOptions += `Size: ${options.size}`;

                                if(options.color)
                                    strOptions += ` Color: ${options.color}`;

                                row.find(".lblOption").html(strOptions);

                                row.removeClass("d-none rowClone");
                                $(row).appendTo("#tblDetalle");

                                if(data.coupon && data.coupon != ""){
                                    $(".lblCoupon")
                                                    .removeClass("d-none")
                                                    .html(`Coupon used: ${data.coupon.toUpperCase()}`);
                                }else{
                                    $(".lblCoupon").addClass("d-none");
                                }

                                if(data.ship_date){
                                    $(".lblShipDate")
                                        .removeClass("d-none")
                                        .html(`Order shipped from ${data.ship_date}`);

                                    let track = (data.shipper_tracking).split(","),
                                        strTrack = "";

                                    if(track[0])
                                        strTrack += `Follow-up guide: ${track[0]}`

                                    if(track[1])
                                        strTrack += ` Company: ${track[1]}`

                                    $(".lblShipTrak")
                                        .removeClass("d-none")
                                        .html(strTrack);

                                }else{
                                    $(".lblShipDate, .lblShipTrak").addClass("d-none");
                                }

                            });
                        });

                    });

                    $(".btnDeleteOrder").unbind().click(function(){
                        let data = getData($(this), dataTableOrder),
                            buton = $(this);

                        if (confirm(`Are you sure to cancel this order?`)){
                            buton.attr("disabled","disabled");
                            buton.html('<i class="bi bi-clock-history"></i>');

                            let objData = {
                                "_method":"cancelOrder",
                                "orderId": data.id
                            };

                            $.post("../core/controllers/checkout.php", objData, function(result) {
                                buton.removeAttr("disabled");
                                buton.html('<i class="bi bi-dash-circle"></i>');

                                getOrders();
                            });

                        }
                    });

                    $(".tmpSetTracking").unbind().click(function(){
                        let data = getData($(this), dataTableOrder),
                            inputText = $(`#inputOrderId${data.id}`).val();

                        let objData = {
                            "_method":"setTracking",
                            "orderId": data.id,
                            "tracking": inputText
                        };

                        $.post("../core/controllers/checkout.php", objData, function(result) {
                            getOrders();
                        });
                    });

                    $(".btnFinalize").unbind().click(function(){
                        let data = getData($(this), dataTableOrder);

                        if (confirm(`Do you want to finish this order?`)){

                            let objData = {
                                "_method":"finalizeOrder",
                                "orderId": data.id
                            };

                            $.post("../core/controllers/checkout.php", objData, function(result) {
                                getOrders();
                            });
                        }
                    });
                },
                searching: true,
                pageLength: 20,
                info: false,
                lengthChange: false,
                paging: mypaging,
                initComplete: function(){
                    this.api().columns([7]).every( function(){
                        $(".dvFilter").html(`<label class="input-group-text labelFilter" for="cboFilter">Filter</label>`);
                        let column = this,
                            select = $(`<select class="form-select" id="cboFilter"><option value="">All</option></select>`)
                                .appendTo(".dvFilter")
                                .on('change', function(){
                                    let val = $.fn.dataTable.util.escapeRegex($(this).val());

                                    column
                                    .search( val ? '^'+val+'$' : '', true, false)
                                    .draw();
                                });

                        column.cells('', column[0]).render('display').sort().unique().each(function( d, j )
                        {
                            select.append(`<option value="${d}">${d}</option>`);
                        });
                    });
                }
            });
            
            $(".dataTables_filter").parent().addClass("d-none");

        });
    }

    function changePageLang(myLang){        
        $(".lblNamePage").html(myLang.namePage);
        $("#offcanvasWithBackdropLabel").html(myLang.panelTitle);
        $(".pnlColA").html(myLang.pnlColA);
        $(".pnlColB").html(myLang.pnlColB);
        $(".pnlColC").html(myLang.pnlColC);
        $(".pnlColD").html(myLang.pnlColD);
        $(".pnlColE").html(myLang.pnlColE);
        $(".pnlColF").html(myLang.pnlColF);
        $(".colB").html(myLang.colB);
        $(".colC").html(myLang.colC);
        $(".colD").html(myLang.colD);
        $(".colE").html(myLang.colE);
        $(".colF").html(myLang.colF);
        $(".colG").html(myLang.colG);
        $(".colH").html(myLang.colH);

        $(".labelFilter").html(myLang.labelFilter);
    }
</script>

<?php
    $content = ob_get_contents();
    ob_end_clean();

    include("index.php");
?>