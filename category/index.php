<?php
    // Se inicia el metodo para encapsular todo el contenido de las paginas (bufering), para dar salida al HTML 
    ob_start();
?>
 
<!-- Products -->
<h2 class="pb-2 text-center display-6">Product list</h2>
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

<script type="text/javascript">
    var categoryId = 0;

    $(document).ready(function(){
        // Recuperar el id de la categoria seleccionada
        let queryString = window.location.search,
            urlParams = new URLSearchParams(queryString);
        categoryId = urlParams.get('cid');

        catProducts();
    });

    // Metodo para listar los productos de la categoria seleccionada
    function catProducts() {
        let objData = {
            "_method":"getProductCat",
            "categoryId": categoryId
        };

        $.post(`${base_url}/core/controllers/product.php`, objData, function(result) {
            $("#ListProduct").html("");
            $.each( result.data, function( index, item){
                let productCard = $(".itemClone").clone();

                if(lang == "en"){
                    productCard.find(".card-title").html(item.name);
                    productCard.find(".card-text").html(item.descriptions);
                }else{
                    productCard.find(".card-title").html(item.optional_name);
                    productCard.find(".card-text").html(item.optional_description);
                }                    

                if( (item.sale_price).length > 0 && item.sale_price > 0){
                    productCard.find(".lblOldPrice").html( formatter.format(item.price)).removeClass("d-none");
                    productCard.find(".lblPrice").html(formatter.format(item.sale_price));
                    productCard.find(".brandPrice").removeClass("d-none");
                }else{
                    productCard.find(".lblPrice").html( formatter.format(item.price) );
                }

                let img = (item.thumbnail != "" &&  item.thumbnail != "0") ? `${base_url}/${item.thumbnail}` : `${base_url}/assets/img/default.jpg`;

                productCard.find(".card-img-top").attr("src", `${img}`);

                productCard.find(".card-img-top").parent().attr("href", `${base_url}/product/index.php?pid=${item.id}`);

                productCard.find(".btnAddtocart").data("item", item);

                productCard.removeClass("d-none itemClone");
                $(productCard).appendTo("#ListProduct");
            });

            $(".btnAddtocart").unbind().click(function(){
                let currentItem = $(this).data("item"),
                    newItem = {},
                    currentCart = JSON.parse(localStorage.getItem("currentCart")),
                    config = JSON.parse(currentItem.dimensions);

                if( config=="0" || ((config[0].sizes).length == 0 && config[1].colors[0] == "") ){
                    if(!currentCart){
                        localStorage.setItem("currentCart", "{}");
                        currentCart = {};
                    }                    

                    newItem.id = currentItem.id;
                    newItem.name = currentItem.name;
                    newItem.optional_name = currentItem.optional_name;
                    newItem.descriptions = currentItem.descriptions;
                    newItem.optional_description = currentItem.optional_description;
                    newItem.thumbnail = currentItem.thumbnail;

                    if( (currentItem.sale_price).length > 0 && currentItem.sale_price > 0){
                        newItem.price = currentItem.sale_price;
                    }else{
                        newItem.price = currentItem.price;
                    }

                    if(currentCart[currentItem.id]){
                        currentCart[currentItem.id].qty = currentCart[currentItem.id].qty + 1;
                    }else{
                        newItem.qty = 1;
                        currentCart[currentItem.id] = newItem;
                    }

                    localStorage.setItem("currentCart", JSON.stringify(currentCart));
                    countCartItem();

                    // Ejecutar para redirigir al checkout
                    $(".btnCheckout").click();
                }else{
                    if(lang == "en"){
                        $(".lblMdlName").html(currentItem.name);
                        $(".lblDescription").html(currentItem.descriptions);
                    }else{
                        $(".lblMdlName").html(currentItem.optional_name);
                        $(".lblDescription").html(currentItem.optional_description);
                    }                        

                    if( (currentItem.sale_price).length > 0 && currentItem.sale_price > 0){
                        $(".lblMdlPrice").html( formatter.format(currentItem.sale_price) );
                    }else{
                        $(".lblMdlPrice").html( formatter.format(currentItem.price) );
                    }

                    $("#mdlAddtoCart").data("item", currentItem);

                    let images = JSON.parse(currentItem.images);
                    $.each( images, function( index, item){
                        $(`.img${index}`)
                            .attr("src", `../${item}`)
                            .parent().removeClass("d-none");
                    });

                    $(".dvSizes").addClass("d-none");
                    if((config[0].sizes).length > 0){
                        $(".dvSizes").removeClass("d-none");
                        $(".toRemoves").remove();

                        $.each(config[0].sizes, function(index, item){
                            let dv = $(".chSizes").clone();

                            dv.find(".chk").val(item).attr("id", `ch${item}`);

                            if(item == "sm")
                                dv.find(".lbl").html("Small");

                            if(item == "m")
                                dv.find(".lbl").html("Medium");

                            if(item == "l")
                                dv.find(".lbl").html("Large");

                            if(item == "xl")
                                dv.find(".lbl").html("Extra large");

                            dv.find(".lbl").attr("for", `ch${item}`);
                            
                            if(index == 0)
                                dv.find(".chk").prop("checked", true);

                            dv.removeClass("d-none chSizes");
                            dv.addClass("toRemoves");
                            $(dv).appendTo(".dvSizes");
                        });
                    }

                    $(".dvColors").addClass("d-none");
                    if(config[1].colors[0] != ""){
                        $(".dvColors").removeClass("d-none");
                        $(".toRemovec").remove();

                        let items = (config[1].colors[0]).split(",");
                        $.each(items, function(index, item){
                            let dv = $(".chColors").clone();

                            dv.find(".chk").val(item).attr("id", `rd${item}`);
                            dv.find(".lbl").html(item).attr("for", `rd${item}`);

                            if(index == 0)
                                dv.find(".chk").prop("checked", true);

                            dv.removeClass("d-none chColors");
                            dv.addClass("toRemovec");
                            $(dv).appendTo(".dvColors");

                        });
                    }

                    $("#mdlProDetalle").modal("show");
                }
            });

            countCartItem();
        });

        $("#mdlCheckCart").unbind().on('shown.bs.modal', function(){
            printList();
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