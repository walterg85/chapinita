<?php
    // Se requiere el modelo de setting para recuperar el id de paypal    
    require_once '../core/models/setting.php';

    // Se instancia la clase
    $settingsModel  = new Settingsmodel();

    // Se recuperan los parametros de configuracion
    $configs = $settingsModel->get();
    $data['existeId'] = FALSE;

    // Estructura de control para validar que exista el id de paypal
    foreach ($configs as $key => $value) {
        if($value['parameter'] == 'paypalid'){
            $data['existeId'] = TRUE;
            $data['paypalId'] = $value['value'];
            break;
        }
    }

    // Si no existe el id de paypal, se redirige al index de la tienda
    if(!$data['existeId']){
        header('Location: ../index.php');
    }
?>
<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap, CSS & Icons-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-uWxY/CJNBR+1zjPWmfnSnVxwRheevXITnMqoEIeG1LJrdI0GlVs/9cVSyPYXdcSF" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <title>Checkout | La Chapinita</title>
</head>
<body> 
    <!-- Include the PayPal JavaScript SDK; replace "test" with your own sandbox Business account app client ID -->
    <script src="https://www.paypal.com/sdk/js?client-id=<?php echo $data['paypalId']; ?>&currency=USD&disable-funding=credit"></script>
    <div class="container">
        <main>
            <div class="py-5 text-center">
                <a href="/"><img class="d-block mx-auto mb-4" src="../assets/img/logo.png?v=1" alt="logo" height="125"></a>
                <h2 class="labelSeccion">Checkout</h2>
                <p class="lead lblLetrero">You have 14 days to return your product if not satisfied.</p>
            </div>
            <div class="row g-5">
                <div class="col-md-7 col-lg-8">
                    <h4 class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-primary labelCart">Your cart</span>
                        <span class="badge bg-primary rounded-pill qtyCart">2</span>
                    </h4>
                    <ul class="list-group mb-3" id="cartListItem"></ul>
                    <li class="list-group-item d-flex justify-content-between lh-sm cartItemClone d-none">
                        <img src="#" alt="twbs" height="32" width="32" class="rounded flex-shrink-0 prdImg">

                        <div class="input-group input-group-sm contBtn ms-2" style="width:90px; height: fit-content;">
                            <button class="btn btn-outline-secondary btnDown" type="button">-</button>
                            <input type="text" class="form-control intQty text-center" disabled>
                            <button class="btn btn-outline-secondary btnUp" type="button">+</button>
                        </div>

                        <div class="w-50 ms-2">
                            <h6 class="my-0 prodName">Women's glove</h6>
                        </div>
                        <span class="text-muted prodPrice">$45.50</span>
                        <a href="javascript:void(0);" class="text-danger btnRemove ms-2">X</a>
                    </li>

                    <div class="input-group mb-2 w-50 float-end">
                        <input type="text" class="form-control" placeholder="Coupon Code" id="inputCode" autocomplete="off">
                        <button class="btn btn-outline-secondary" type="button" id="applyCoupon"><i class="bi bi-check2"></i> <texto class="tdLabelCoupon">Apply coupon</texto></button>
                    </div><br><br>
                    
                    <table class="table w-50 float-end">
                        <tbody>
                            <tr>
                                <td class="text-end lalelShip">Shipping Cost</td>
                                <td class="lblShipCost text-end"></td>
                            </tr>
                            <tr class="rowCoupon d-none">
                                <td class="text-end labelCoupon">Coupon</td>
                                <td class="lblCoupon text-end">0</td>
                            </tr>
                            <tr>
                                <td class="text-end">Subtotal</td>
                                <td class="lblsubtotal text-end">0</td>
                            </tr>
                            <tr class="rowTax d-none">
                                <td class="text-end labelTax">Tax</td>
                                <td class="lblTax text-end">0</td>
                            </tr>
                            <tr>
                                <td class="text-end">Total</td>
                                <td class="lblTotal text-end">0</td>
                            </tr>
                        </tbody>
                    </table>

                </div>
                <div class="col-md-5 col-lg-4">
                    <div class="mt-md-5" id="paypal-button-container"></div>          
                </div>
            </div>
        </main>

        <footer class="my-5 pt-5 text-muted text-center text-small">
          <p class="mb-1">&copy; <script>document.write(new Date().getFullYear())</script> La Chapinita</p>
        </footer>
    </div> 

<!-- Option 1: Bootstrap Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-kQtW33rZJAHjgefvhyyzcGF3C5TFyBQBA13V1RKPf4uH+bwyzQxZ6CmMZHmNBEfJ" crossorigin="anonymous"></script>
<!-- Jquery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>

<script type="text/javascript">
    var formatter = new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: 'USD',
            minimumFractionDigits: 2
        }),
        subTotal = 0,
        grantotal = 0,
        ship_price = 0,
        orderDetails = [],
        descuento = 0,
        lang = "en",
        strCupon = "",
        strTax = "";

    $(document).ready(function(){
        countCartItem();

        $("#applyCoupon").click( function(){
            let objData = {
                "_method":"GetCoupon",
                "code": $("#inputCode").val()
            };

            $.post("../core/controllers/checkout.php", objData, function(result) {
                if(result.data){
                    let codigo = result.data;
                    if(codigo.tipo == 1){
                        descuento = (subTotal * parseFloat( codigo.valor / 100)).toString().match(/^-?\d+(?:\.\d{0,2})?/)[0];
                        $(".labelCoupon").html(`${strCupon} ${codigo.valor}%`);
                        $(".lblCoupon").html(`$${descuento}`);
                        $(".lblCoupon").parent().removeClass("d-none");
                    }else{
                        descuento = parseFloat( codigo.valor );
                        $(".labelCoupon").html(strCupon);
                        $(".lblCoupon").html(`${formatter.format(descuento)}`);
                        $(".lblCoupon").parent().removeClass("d-none");
                    }                    
                }else{
                    descuento = 0;
                    alert("This coupon does not exist.");
                }

                resumen();
            });
        });

        if( localStorage.getItem("currentLag") ){
            lang = localStorage.getItem("currentLag");
        }else{
            localStorage.setItem("currentLag", lang);
        }

        switchLanguage(lang);
        printList();
    });

    function printList() {
        let currentCart = JSON.parse(localStorage.getItem("currentCart"));
        $("#cartListItem").html("");

        $.each( currentCart, function( index, item){
            let listItem = $(".cartItemClone").clone(),
                size = '',
                color = '';

            if(item.size){
                if(item.size == "sm")
                    size = "Small";

                if(item.size == "m")
                    size = "Medium";

                if(item.size == "l")
                    size = "Large";

                if(item.size == "xl")
                    size = "Extra large";
            }

            if(item.color)
                color = `${item.color}`;

            let img = (item.thumbnail != "" &&  item.thumbnail != "0") ? `../${item.thumbnail}` : "../assets/img/default.jpg";

            listItem.find(".prdImg").attr("src", img);

            if(lang == "en"){
                listItem.find(".prodName").html(`${item.name} ${color} ${size}`);
            }else{
                listItem.find(".prodName").html(`${item.optional_name} ${color} ${size}`);
            }

            listItem.find(".prodPrice").html(formatter.format(item.price));
            listItem.find(".intQty").val(item.qty);

            listItem.find(".contBtn").data("item", item);
            listItem.data("item", item);

            listItem.removeClass("d-none cartItemClone");
            $(listItem).appendTo("#cartListItem");
        });

        $(".btnDown").unbind().click( function () {
            let currentCart = JSON.parse(localStorage.getItem("currentCart")),
                data = $(this).parent().data("item"),
                actualQty = 0,
                itemId = data.id;

            if(data.size)
                itemId += `|s-${data.size}`;

            if(data.color)
                itemId += `|c-${data.color}`;

            actualQty = currentCart[itemId].qty;

            if(actualQty > 1){
                currentCart[itemId].qty = actualQty - 1;
                localStorage.setItem("currentCart", JSON.stringify(currentCart));
                printList();
            }
        });

        $(".btnUp").unbind().click( function () {
            let currentCart = JSON.parse(localStorage.getItem("currentCart")),
                data = $(this).parent().data("item"),
                actualQty = 0,
                itemId = data.id;

            if(data.size)
                itemId += `|s-${data.size}`;

            if(data.color)
                itemId += `|c-${data.color}`;

            actualQty = currentCart[itemId].qty;

            if(actualQty > 0){
                currentCart[itemId].qty = actualQty + 1;
                localStorage.setItem("currentCart", JSON.stringify(currentCart));
                printList();
            }
        });

        $(".btnRemove").unbind().click( function(){
            let currentCart = JSON.parse(localStorage.getItem("currentCart")),
                data = $(this).parent().data("item"),
                itemId = data.id;


            if(data.size)
                itemId += `|s-${data.size}`;

            if(data.color)
                itemId += `|c-${data.color}`;

            delete currentCart[itemId];
            localStorage.setItem("currentCart", JSON.stringify(currentCart));
            printList();
            countCartItem();
        });

        resumen();
    }

    function countCartItem(){
        let currentCart = JSON.parse(localStorage.getItem("currentCart"));
        if(currentCart)
            $(".qtyCart").html(Object.keys(currentCart).length);
    }

    function resumen(){
        grantotal = 0;
        ship_price = 0;
        orderDetails = [];
        subTotal = 0;

        let currentCart = JSON.parse(localStorage.getItem("currentCart"));

        $.each(currentCart, function(index, item){
            grantotal += parseFloat(parseFloat(item.price) * parseFloat(item.qty));

            let row = {
                "product_id": item.id,
                "price": item.price,
                "quantity": item.qty,
                "amount" : parseFloat(parseFloat(item.price) * parseFloat(item.qty))
            };

            let config = {};

            if(item.size)
                config.size = item.size;

            if(item.color)
                config.color = item.color;

            row.config = JSON.stringify(config);

            orderDetails.push(row);
        });

        let objData = {
            "_method":"Get"
        };

        $.post("../core/controllers/setting.php", objData, function(result) {
            let data = result.data,
                shipingCost = parseFloat(data[0].value),
                shipingFree = parseFloat(data[1].value),
                tax = parseFloat(data[2].value);

            $(".lblShipCost").html("$0.00");

            if(grantotal < shipingFree){
                $(".lblShipCost").html(formatter.format(shipingCost));
                grantotal += shipingCost;
                ship_price = shipingCost;
            }

            if(parseFloat(descuento) > 0){
                grantotal = grantotal - parseFloat(descuento);
            }

            subTotal = grantotal;

            $(".lblsubtotal").html(formatter.format(grantotal));

            if(tax || tax != "" || tax > 0){
                let impuesto = grantotal * (parseFloat(tax) / 100);
                grantotal = grantotal + impuesto;

                $(".labelTax").html(`${strTax} ${tax}%`);
                $(".lblTax").html(`${formatter.format(impuesto)}`);
                $(".lblTax").parent().removeClass("d-none");
            }else{
                $(".lblTax").parent().addClass("d-none");
            }

            $(".lblTotal").html(`<strong class="text-danger">${formatter.format(grantotal)}</strong>`);
        });
    }

    function switchLanguage(lang){
        $.post(`../assets/lang.json`, {}, function(data) {
            let myLang = data[lang]["checkout"];

            $(".labelSeccion").html(myLang.labelSeccion);
            $(".lblLetrero").html(myLang.lblLetrero);
            $(".labelCart").html(myLang.labelCart);
            $(".tdLabelCoupon").html(myLang.labelCoupon);
            strCupon = myLang.strCupon;

            $("#inputCode").attr("placeholder", myLang.inputCode);
            $(".lalelShip").html(myLang.lalelShip);
            strTax = myLang.strTax;
        });
    }
</script>

<script>
    paypal.Buttons({
        // Sets up the transaction when a payment button is clicked
        createOrder: function(data, actions) {
            return actions.order.create({
                purchase_units: [{
                    amount: {
                        value: parseFloat(grantotal).toFixed(2)
                    }
                }]
            });
        },
        // Finalize the transaction after payer approval
        onApprove: function(data, actions) {
            return actions.order.capture().then(function(orderData) {
                if(orderData.status == "COMPLETED"){
                    let objData = {
                        "_method":"_POST",
                        "amount": grantotal,
                        "ship_price": ship_price,
                        "shipping_address": JSON.stringify(orderData.purchase_units[0].shipping.address),
                        "payment_data": JSON.stringify(orderData),
                        "order": JSON.stringify(orderDetails),
                        "coupon": $("#inputCode").val()
                    };

                    $.post("../core/controllers/checkout.php", objData, function(result) {
                        localStorage.removeItem("currentCart");
                        window.location.replace(`../order/index.php?orderId=${result.id}`);
                    });
                }else{
                    alert("Payment was not processed correctly, please try again.");
                }
            });
        }
    }).render('#paypal-button-container');
</script>
  </body>
</html>