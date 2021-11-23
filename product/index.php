<?php
    // Se inicia el metodo para encapsular todo el contenido de las paginas (bufering), para dar salida al HTML 
    ob_start();
?>

<div class="card">
    <div class="row g-0">
      <div class="col-md-6 border-end">
          <div class="d-flex flex-column justify-content-center">
              <div class="main_image"> <img src="#" id="main_product_image" class="img0" width="350"> </div>
              <div class="thumbnail_images">
                  <ul id="thumbnail">
                      <li class="d-none"><img onclick="changeImage(this)" src="#" width="70" class="img0"></li>
                      <li class="d-none"><img onclick="changeImage(this)" src="#" width="70" class="img1"></li>
                      <li class="d-none"><img onclick="changeImage(this)" src="#" width="70" class="img2"></li>
                      <li class="d-none"><img onclick="changeImage(this)" src="#" width="70" class="img3"></li>
                  </ul>
              </div>
          </div>
      </div>
      <div class="col-md-6">
          <div class="p-3 right-side">
              <div class="d-flex justify-content-between align-items-center">
                  <h3 class="lblName">Beautiful sofa</h3> <span class="heart"><i class='bx bx-heart'></i></span>
              </div>
              <div class="mt-2 pr-3 content">
                  <p class="lblDescription">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua</p>
              </div>

              <div class="row">
                    <div class="col-12 mb-3 dvSizes d-none">
                        <label class="form-label">Sizes</label><br>
                        <div class="form-check form-check-inline d-none chSizesP">
                            <input class="form-check-input chk" type="radio" name="chSizesP" value="0">
                            <label class="form-check-label lbl">Small</label>
                        </div>
                    </div>
                    <div class="col-12 mb-3 dvColors d-none">
                        <label class="form-label">Colors</label><br>
                        <div class="form-check form-check-inline d-none chColorsP">
                            <input class="form-check-input chk" type="radio"  name="chColorsP" value="0">
                            <label class="form-check-label lbl">Small</label>
                        </div>
                    </div>
                </div>

              <h3 class="lblPrice">$430.99</h3>
              <div class="buttons d-flex flex-row mt-5 gap-3"> <button class="btn btn-outline-dark btnAddtocart">Add To Cart</button></div>
        </div>
      </div>
    </div>
</div>

<script>
    $(document).ready(function(){
        // Recuperar el id del producto seleccionado
        let queryString = window.location.search,
            urlParams   = new URLSearchParams(queryString),
            productId   = urlParams.get('pid');

        productDetail(productId);

        $(".btnAddtocart").click(function(){
            let currentItem = $(this).data("item"),
                newItem     = {},
                currentCart = JSON.parse(localStorage.getItem("currentCart")),
                config      = JSON.parse(currentItem.dimensions),
                size        = null,
                color       = null,
                newItemId   = currentItem.id;

            if(!currentCart){
                localStorage.setItem("currentCart", "{}");
                currentCart = {};
            }

            if(config !=  "0"){
                if((config[0].sizes).length > 0){
                    size = $("input[type=radio][name=chSizesP]:checked").val();
                    newItemId += `|s-${size}`;
                    newItem.size = size;
                }

                if(config[1].colors[0] != ""){
                    color = $("input[type=radio][name=chColorsP]:checked").val();
                    newItemId += `|c-${color}`;
                    newItem.color = color;
                }
            }

            newItem.id                      = currentItem.id;
            newItem.name                    = currentItem.name;
            newItem.optional_name           = currentItem.optional_name;
            newItem.descriptions            = currentItem.descriptions;
            newItem.optional_description    = currentItem.optional_description;
            newItem.thumbnail               = currentItem.thumbnail;
            newItem.price                   = ((currentItem.sale_price).length > 0 && currentItem.sale_price > 0) ? currentItem.sale_price : currentItem.price;

            // if( (currentItem.sale_price).length > 0 && currentItem.sale_price > 0){
            //     newItem.price = currentItem.sale_price;
            // }else{
            //     newItem.price = currentItem.price;
            // }

            if(currentCart[newItemId]){
                currentCart[newItemId].qty = currentCart[newItemId].qty + 1;
            }else{
                newItem.qty = 1;
                currentCart[newItemId] = newItem;
            }

            localStorage.setItem("currentCart", JSON.stringify(currentCart));

            countCartItem();

            // Ejecutar para redirigir al checkout
            $(".btnCheckout").click();
        });
    });

    function productDetail(productId){
        let objData = {
            "_method":"getProductId",
            "productId": productId
        };

        $.post(`${base_url}/core/controllers/product.php`, objData, function(result){
            if(result.data){
                let info = result.data[0],
                    config = JSON.parse(info.dimensions);

                $(".lblName").html(info.name);
                $(".lblDescription").html(info.descriptions);

                if( (info.sale_price).length > 0 && info.sale_price > 0){
                    $(".lblPrice").html( formatter.format(info.sale_price) );
                }else{
                    $(".lblPrice").html( formatter.format(info.price) );
                }

                $(".btnAddtocart").data("item", info);

                let images = JSON.parse(info.images);
                if(images[0]){
                    $.each( images, function( index, item){
                        $(`.img${index}`)
                            .attr("src", `${base_url}/${item}`)
                            .parent().removeClass("d-none");
                    });
                }else{
                    $(`.img0`)
                        .attr("src", `${base_url}/assets/img/default.jpg`)
                        .parent().removeClass("d-none");
                }

                document.title = `${info.name} Pages`;

                if( config!="0" || (config[0].sizes).length != 0 || config[1].colors[0] != "" ){
                    $(".dvSizes").addClass("d-none");
                    if((config[0].sizes).length > 0){
                        $(".dvSizes").removeClass("d-none");
                        $(".toRemoves").remove();

                        $.each(config[0].sizes, function(index, item){
                            let dv = $(".chSizesP").clone();

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

                            dv.removeClass("d-none chSizesP");
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
                            let dv = $(".chColorsP").clone();

                            dv.find(".chk").val(item).attr("id", `rd${item}`);
                            dv.find(".lbl").html(item).attr("for", `rd${item}`);

                            if(index == 0)
                                dv.find(".chk").prop("checked", true);

                            dv.removeClass("d-none chColorsP");
                            dv.addClass("toRemovec");
                            $(dv).appendTo(".dvColors");

                        });
                    }
                }
            }
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