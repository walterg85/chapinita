<!doctype html>
<html lang="en">
<?php
    // Se inicia el metodo para encapsular todo el contenido de las paginas (bufering), para dar salida al HTML 
    ob_start();
?>
      
<div class="container">

    <div class="container col-xxl-8 px-4">
        <div class="text-center">
          <h1 class="text-info">Order Details</h1>
        </div>
        
        <div class="row flex-lg-row-reverse align-items-center g-5 py-5">
            <div class="col-10 col-sm-8 col-lg-6" id="contenedorItem">
                <h4 class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-primary">Total Items</span>
                    <span class="badge bg-primary rounded-pill lblItems"></span>
                </h4>
            </div>
            <a href="javascript:void(0);" class="list-group-item list-group-item-action d-flex gap-3 py-3 d-none prodsItemClone" aria-current="true">
                    <img src="#" alt="twbs" height="32" class="rounded-circle flex-shrink-0 imagep">
                    <div class="d-flex gap-2 w-100 justify-content-between">
                        <div>
                            <h6 class="mb-0 lblNameItem"></h6>
                        </div>
                        <small class="opacity-50 text-nowrap lblPriceProd"></small>
                    </div>
                </a>         
            <div class="col-lg-6">
                <h1 class="display-5 fw-bold lh-1 mb-3">Order: <text class="lblOrderId"></text></h1>
                <p class="lead">Order status: <text class="lblStatus"></text></p>
                <p class="lead">PayPal transaction: <text class="lblPaypalId"></text> </p>
                <p class="lead">Shipping: <text class="lblShipingCost"></text> </p>
                <p class="lead">Total: <text class="lblTotal"></text></p>
            </div>
        </div>
    </div>

    <hr class="mb-5">

    <!-- Products -->
    <div class="text-center col-lg-3 col-md-4 col-6 my-3 itemClone d-none">
        <div class="card shadow">
            <div class="badge bg-warning text-white position-absolute d-none brandPrice" style="top: 0.5rem; right: 0.5rem">
                Sale
            </div>
            <div>
                <a href="javascript:void(0);">
                    <img src="#" alt="image" class="img-fluid card-img-top">
                </a>
            </div>
            <div class="card-body">
                <h5 class="card-title"></h5>
                <p class="card-text"></p>
                <h5>
                    <span class="text-muted text-decoration-line-through lblOldPrice d-none"></span>
                    <span class="price lblPrice"></span>
                </h5>
                <input type="hidden">
                <button type="button" class="btn btn-success my-3 btnAddtocart">Add To Cart</button>                                                   
            </div>
        </div>
    </div>
    <div class="row" id="ListProduct"></div>
</div>

<script type="text/javascript">
    var currentOrderId = 0;

    $(document).ready(function(){
        productLimite = 4;

        let queryString = window.location.search,
            urlParams   = new URLSearchParams(queryString);
        currentOrderId  = urlParams.get('orderId');

        getInfo();
    });

    function getInfo() {
        let objData = {
            "_method":"_GET",
            "currentOrderId": currentOrderId
        };

        $.post(`${base_url}/core/controllers/checkout.php`, objData, function(result) {
            $(".lblOrderId").html( pad(result.data.order.id, 5) );

            if(result.data.order.status == 0)
                $(".lblStatus").html("Canceled");

            if(result.data.order.status == 1)
                $(".lblStatus").html("New order");

            if(result.data.order.status == 2)
                $(".lblStatus").html("Sent to your address");

            if(result.data.order.status == 3)
                $(".lblStatus").html("Order completed");
            
            let paypalorder = JSON.parse(result.data.order.payment_data);

            $(".lblPaypalId").html( paypalorder.purchase_units[0].payments.captures[0].id );

            $(".lblShipingCost").html( (result.data.order.ship_price == 0) ? 'Free' : formatter.format(result.data.order.ship_price) );

            $(".lblTotal").html( formatter.format(result.data.order.amount) );

            $.each(result.data.detail, function(index, prod){
                let item = $(".prodsItemClone").clone(),
                    config = JSON.parse(prod.selected_options),
                    size = "",
                    color = "";

                if(config.size){
                    if(config.size == "sm")
                        size = "Small";

                    if(config.size == "m")
                        size = "Medium";

                    if(config.size == "l")
                        size = "Large";

                    if(config.size == "xl")
                        size = "Extra large";
                }

                if(config.color)
                    color = `${config.color}`;

                let img = (prod.thumbnail != "" &&  prod.thumbnail != "0") ? `${base_url}/${prod.thumbnail}` : `${base_url}/assets/img/default.jpg`;

                item.find(".imagep").attr("src", img);
                item.find(".lblNameItem").html(`${prod.quantity} - ${prod.name}  ${size}  ${color}`);
                item.find(".lblPriceProd").html( formatter.format(prod.price) );

                item.removeClass("d-none prodsItemClone");
                $(item).appendTo("#contenedorItem");
            });

            $(".lblItems").html( (result.data.detail).length );
        });
    }
</script>

<?php
    // Se obtiene el contenido del bufer
    $content = ob_get_contents();

    // Limpiar el bufer para liberar
    ob_end_clean();

    // Se carga la pagina maestra para imprimir la pagina global
    include("../masterPage.php");
?>