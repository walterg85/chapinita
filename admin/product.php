<?php
    @session_start();
    ob_start();
?>

<!-- cropperCSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.9/cropper.min.css" integrity="sha512-w+u2vZqMNUVngx+0GVZYM21Qm093kAexjueWOv9e9nIeYJb1iEfiHC7Y+VvmP/tviQyA5IR32mwN/5hTEJx6Ng==" crossorigin="anonymous" referrerpolicy="no-referrer" />

<!-- cropperJS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.9/cropper.min.js" integrity="sha512-9pGiHYK23sqK5Zm0oF45sNBAX/JqbZEP7bSDHyt+nT3GddF+VFIcYNqREt0GDpmFVZI3LZ17Zu9nMMc9iktkCw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2 lblNamePage">Products</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <button type="button" class="btn btn-outline-secondary btnPanel" data-bs-toggle="offcanvas" data-bs-target="#offcanvasProduct"><i class="bi bi-plus-lg"></i> Add Product</button>
        </div>
    </div>
</div>

<table class="table" id="productList">
    <thead class="table-light">
        <th class="colA">#</th>
        <th class="colB">Thumbnails</th>
        <th class="colC">Name</th>
        <th class="colD">Descriptions</th>
        <th class="colE">Category</th>
        <th class="colF">Price</th>
        <th class="colG">Sale price</th>
        <th class="colH"></th>
    </thead>
</table>

<!-- Modal para editar las imagenes -->
<div class="modal fade" id="modalCrop" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalLabel">Edit / Crop the photo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="img-container mb-3" style="max-height: 500px">
                    <img id="previewCrop" src="#">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="cropImage">Apply</button>
            </div>
        </div>
    </div>
</div>

<!-- Panel lateral para agregar nuevo producto -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasProduct" aria-labelledby="offcanvasWithBackdropLabel"  >
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="offcanvasWithBackdropLabel">Add a new product</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <form id="addProductForm" class="needs-validation" novalidate>
            <input type="hidden" name="productId" id="productId" value="0">
            <div class="row">
                <div class="col mb-3">
                    <label for="inputName" class="form-label labelName">Name</label>
                    <input type="text" id="inputName" name="inputName" class="form-control" autocomplete="off" required>
                </div>
                <div class="col mb-3">
                    <label for="inputNameSp" class="form-label labelNameSp">Name Spanish</label>
                    <input type="text" id="inputNameSp" name="inputNameSp" class="form-control" autocomplete="off" required>
                </div>
            </div>
            <div class="mb-3">
                <label for="inputDescription" class="form-label labelDescription">Description</label>
                <input type="text" id="inputDescription" name="inputDescription" class="form-control" autocomplete="off" required>
            </div>
            <div class="mb-3">
                <label for="inputDescriptionSp" class="form-label labelDescriptionSp">Description Spanish</label>
                <input type="text" id="inputDescriptionSp" name="inputDescriptionSp" class="form-control" autocomplete="off" required>
            </div>
            <div class="row">
                <div class="col-3 mb-3">
                    <label for="inputPrice" class="form-label labelPrice">Price</label>
                    <input type="text" id="inputPrice" name="inputPrice" class="form-control" autocomplete="off" required>
                </div>
                <div class="col-3 mb-3">
                    <label for="inputSalePrice" class="form-label labelSalePrice">Sale Price</label>
                    <input type="text" id="inputSalePrice" name="inputSalePrice" class="form-control" autocomplete="off">
                </div>
                <div class="col-6 mb-3">
                    <label for="inputCategory" class="form-label labelCategory">Category</label>
                    <select class="form-select" id="inputCategory" name="inputCategory" required></select>
                </div>
            </div>
            <div class="row">
                <div class="col-12 mb-3">
                    <label class="form-label labelSizes">Sizes</label><br>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" id="chsm" name="chsizes" value="sm">
                        <label class="form-check-label labelSm" for="chsm">Small</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" id="chm" name="chsizes" value="m">
                        <label class="form-check-label labelMed" for="chm">Medium</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" id="chl" name="chsizes" value="l">
                        <label class="form-check-label labelLarge" for="chl">Large</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" id="chxl" name="chsizes" value="xl">
                        <label class="form-check-label labelXLarge" for="chxl">Extra large</label>
                    </div>
                </div>
            </div>
            <div class="mb-3">
                <label for="inputColors" class="form-label labelColors">Colors (separated by commas)</label>
                <input type="text" id="inputColors" name="inputColors" class="form-control" autocomplete="off" placeholder="Red, Orange, Gray">
            </div>

            <div class="row mb-3">
                <div class="col-3 d-none img1">
                    <img src="#" class="img-thumbnail" alt="Product image" id="img1">
                    <span class="top-0 start-100 badge rounded-pill bg-danger removePhoto" data-control="image1" data-pic="1">
                        <i class="bi bi-x-lg"></i>
                    </span>
                </div>
                <div class="col-3 d-none img2">
                    <img src="#" class="img-thumbnail" alt="Product image" id="img2">
                    <span class="top-0 start-100 badge rounded-pill bg-danger removePhoto" data-control="image2" data-pic="2">
                        <i class="bi bi-x-lg"></i>
                    </span>
                </div>
                <div class="col-3 d-none img3">
                    <img src="#" class="img-thumbnail" alt="Product image" id="img3">
                    <span class="top-0 start-100 badge rounded-pill bg-danger removePhoto" data-control="image3" data-pic="3">
                        <i class="bi bi-x-lg"></i>
                    </span>
                </div>
                <div class="col-3 d-none img4">
                    <img src="#" class="img-thumbnail" alt="Product image" id="img4">
                    <span class="top-0 start-100 badge rounded-pill bg-danger removePhoto" data-control="image4" data-pic="4">
                        <i class="bi bi-x-lg"></i>
                    </span>
                </div>
            </div>

            <div class="mb-3">
                <label for="image1" class="form-label labelImage">Add Image</label>
                <input class="form-control" type="file" id="image1">
            </div>

            <div class="mb-3">
                <label for="image2" class="form-label labelImage">Add Image</label>
                <input class="form-control" type="file" id="image2">
            </div>

            <div class="mb-3">
                <label for="image3" class="form-label labelImage">Add Image</label>
                <input class="form-control" type="file" id="image3">
            </div>

            <div class="mb-3">
                <label for="image4" class="form-label labelImage">Add Image</label>
                <input class="form-control" type="file" id="image4">
            </div>

            <div class="d-grid gap-2 my-5">
                <button class="btn btn-success btn-lg" type="button" id="addProduct">
                    <i class="bi bi-check2"></i> Save
                </button>
            </div>
        </form>
    </div>
</div>

<script type="text/javascript">
    var productPhotos   = {},
        maxCroppedWidth = 420,
        maxCroppedHeight = 300,
        dataTableProduct = null,
        formatter = new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: 'USD',
            minimumFractionDigits: 2
        }),
        deletesImages = [],
        mesages = {//Control de mensajes para los ALERTS
            "ctrImage1":"Your selected file is larger than 5MB",
            "ctrImage2":"files not allowed, only images",
            "ctrtoRemove":"do you want to delete this product"
        };

    $(document).ready(function(){
        currentPage = "Products";

        $(".removePhoto").click( function(){
            let datas = $(this).data();
            $(`#${datas.control}`).parent().removeClass('d-none');
            $(`.img${datas.pic}`).addClass('d-none');

            productPhotos[datas.control] = null;

            if( $("#productId").val() != 0 )
                deletesImages.push(`${datas.control}.jpg`);
        });

        var myOffcanvas = document.getElementById('offcanvasProduct');
        myOffcanvas.addEventListener('hidden.bs.offcanvas', function () {
            $("#productId").val("0");
            $("#addProductForm")[0].reset();

            productPhotos = {};
            deletesImages = [];

            $(`#image1, #image2, #image3, #image4`).parent().removeClass('d-none');
            $(`.img1, .img2, .img3, .img4`).addClass('d-none');
            $(`#img1, #img2, #img3, #img4`).attr("src", "#");

            $("#addProductForm").removeClass("was-validated");
        });

        $("#addProduct").click( registerProduct);

        getProducts();
        loadCategories();
        initComponent();
    });

    function loadCategories(){
        let objData = {
            "_method":"Get"
        };

        $.post("../core/controllers/category.php", objData, function(result) {
            let optList = '<option value="" selected>Select Category</option>';
            $.each( result.data, function( index, item){
                optList += `<option value="${item.id}">${item.name}</option>`;
            });
            $(optList).appendTo("#inputCategory");
        });
    }

    function registerProduct(){
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

        $("#addProduct").attr("disabled","disabled");
        $("#addProduct").html('<i class="bi bi-clock-history"></i> Registering');

        let mySizes = document.getElementsByName('chsizes'),
            pSizes = [],
            pColors = [$("#inputColors").val()],
            pConfig = [];

        for (var sizes of mySizes) {
            if (sizes.checked)
                pSizes.push(sizes.value);
        }

        pConfig.push({"sizes":pSizes});
        pConfig.push({"colors":pColors});

        let form = $("#addProductForm")[0],
            formData = new FormData(form);

        formData.append("_method", "POST");
        formData.append("pConfig", JSON.stringify(pConfig));

        $.each(productPhotos, function( index, value ) {
            if(value)
                formData.append("imagesproduct[]", value, `${index}.jpg`);
        });

        formData.append("deletesImages", JSON.stringify(deletesImages));

        $.ajax({
            url: '../core/controllers/product.php',
            data: formData,
            type: 'POST',
            dataType: 'json',
            success: function(response){
                $("#addProduct").removeAttr("disabled");
                $("#addProduct").html('<i class="bi bi-check2"></i> Save');

                $(".btnPanel").click();
                getProducts();
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.log(textStatus);
            },
            cache: false,
            contentType: false,
            processData: false
        });
    }

    function getProducts(){
        let objData = {
            "_method":"GET",
            "limite": 0
        };

        if (dataTableProduct != null)
            dataTableProduct.destroy();

        $.post("../core/controllers/product.php", objData, function(result) {
            let mypaging =  false;

            if( (result.data).length > 20 )
                mypaging = true;

            dataTableProduct = $("#productList").DataTable({
                data: result.data,
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
                        data: 'thumbnail',
                        orderable: false,
                        class: "text-center",
                        width: "100px",
                        render: function ( data, type, row ) {
                            let img = (data != "" &&  data != "0") ? `../${data}` : "../assets/img/default.jpg";

                            return `
                                <img src="${img}" class="rounded" alt="Image product" width="100">
                            `;
                        }
                    },
                    {
                        data: 'name'
                    },
                    {
                        data: 'descriptions'
                    },
                    {
                        data: 'categoria',
                        render: function ( data, type, row ) {
                            return data.name;
                        }
                    },
                    {
                        data: 'price',
                        render: function (data, type, row) {
                            return formatter.format(data);
                        }
                    },
                    {
                        data: 'sale_price',
                        render: function (data, type, row) {
                            return formatter.format(data);
                        }
                    },
                    {
                        data: null,
                        orderable: false,
                        class: "text-center",
                        render: function ( data, type, row ) {
                            return `
                                <a href="javascript:void(0);" class="btn btn-outline-secondary btnEditProduct me-2" title="Edit" data-bs-toggle="offcanvas" data-bs-target="#offcanvasProduct"><i class="bi bi-eye"></i></a>
                                <a href="javascript:void(0);" class="btn btn-outline-danger btnDeleteProduct" title="Delete"><i class="bi bi-trash"></i></a>
                            `;
                        }
                    }
                ],
                "fnDrawCallback":function(oSettings){
                    $(".btnDeleteProduct").unbind().click(function(){
                        let data = getData($(this), dataTableProduct),
                            buton = $(this);

                        if (confirm(`${mesages.ctrtoRemove} (${data.name})?`)){
                            buton.attr("disabled","disabled");
                            buton.html('<i class="bi bi-clock-history"></i>');

                            let objData = {
                                "_method":"Delete",
                                "productId": data.id
                            };

                            $.post("../core/controllers/product.php", objData, function(result) {
                                buton.removeAttr("disabled");
                                buton.html('<i class="bi bi-trash"></i>');

                                getProducts();
                            });

                        }
                    });

                    $(".btnEditProduct").unbind().click(function(){
                        let data = getData($(this), dataTableProduct),
                            buton = $(this),
                            currentImages = JSON.parse(data.images);

                        $("#productId").val(data.id);
                        $("#inputName").val(data.name);
                        $("#inputNameSp").val(data.optional_name);
                        $("#inputDescription").val(data.descriptions);
                        $("#inputDescriptionSp").val(data.optional_description);
                        $("#inputPrice").val(data.price);
                        $("#inputSalePrice").val(data.sale_price);
                        $("#inputCategory").val(data.categoria.id);

                        $.each( currentImages, function( index, item){
                            let control = (item.split('/').pop()).substring(0,6),
                                pic = control.substring(5,6);

                            $(`.img${pic}`).removeClass('d-none');
                            $(`#img${pic}`).attr("src", `../${item}`);
                            $(`#${control}`).parent().addClass('d-none');
                        });

                        if(data.dimensions && data.dimensions != "0"){
                            let pConfig = JSON.parse(data.dimensions);

                            $.each(pConfig, function(index, item){
                                if(item.colors)
                                    $("#inputColors").val(item.colors);

                                if(item.sizes){
                                    $.each(item.sizes, function(idx, itm){
                                        $(`#ch${itm}`).prop("checked", true);
                                    });
                                }
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

    function initComponent() {
        // Controlar tipo de objeto que intentan subir
        $('input[type="file"]').unbind().change( function(){
            let ext = $( this ).val().split('.').pop();

            if ($( this ).val() != ''){
                if($.inArray(ext, ["jpg", "jpeg", "png", "bmp", "raw", "tiff"]) != -1){
                    if($(this)[0].files[0].size > 5242880){
                        $( this ).val('');
                        alert(mesages.ctrImage1);
                    }
                }else{
                    $( this ).val('');
                    alert(`${ext} ${mesages.ctrImage2}`);
                }
            }
        });

        // Image Cropper
        let picture = null,
            image       = $("#previewCrop")[0],
            inputFile1   = $("#image1")[0],
            inputFile2   = $("#image2")[0],
            inputFile3   = $("#image3")[0],
            inputFile4   = $("#image4")[0],
            $modal      = $('#modalCrop'),
            cropper     = null,
            curentInput = null; 

        inputFile1.addEventListener("change", function(e){
            picture   = $("#img1");
            let files = e.target.files,
                done  = function (url){
                    inputFile1.value = "";
                    image.src = url;
                    $modal.modal('show');
                    curentInput = inputFile1;
                },
                reader,
                file,
                url;

            if (files && files.length > 0){
                file = files[0];

                if (URL){
                    done(URL.createObjectURL(file));
                }
                else if (FileReader){
                    reader = new FileReader();
                    reader.onload = function(e){
                        done(reader.result);
                    };
                    reader.readAsDataURL(file);
                }
            }
        });

        inputFile2.addEventListener("change", function(e){
            picture   = $("#img2");
            let files = e.target.files,
                done  = function (url){
                    inputFile2.value = "";
                    image.src = url;
                    $modal.modal('show');
                    curentInput = inputFile2;
                },
                reader,
                file,
                url;

            if (files && files.length > 0){
                file = files[0];

                if (URL){
                    done(URL.createObjectURL(file));
                }
                else if (FileReader){
                    reader = new FileReader();
                    reader.onload = function(e){
                        done(reader.result);
                    };
                    reader.readAsDataURL(file);
                }
            }
        });

        inputFile3.addEventListener("change", function(e){
            picture   = $("#img3");
            let files = e.target.files,
                done  = function (url){
                    inputFile3.value = "";
                    image.src = url;
                    $modal.modal('show');
                    curentInput = inputFile3;
                },
                reader,
                file,
                url;

            if (files && files.length > 0){
                file = files[0];

                if (URL){
                    done(URL.createObjectURL(file));
                }
                else if (FileReader){
                    reader = new FileReader();
                    reader.onload = function(e){
                        done(reader.result);
                    };
                    reader.readAsDataURL(file);
                }
            }
        });

        inputFile4.addEventListener("change", function(e){
            picture   = $("#img4");
            let files = e.target.files,
                done  = function (url){
                    inputFile4.value = "";
                    image.src = url;
                    $modal.modal('show');
                    curentInput = inputFile4;
                },
                reader,
                file,
                url;

            if (files && files.length > 0){
                file = files[0];

                if (URL){
                    done(URL.createObjectURL(file));
                }
                else if (FileReader){
                    reader = new FileReader();
                    reader.onload = function(e){
                        done(reader.result);
                    };
                    reader.readAsDataURL(file);
                }
            }
        });

        $modal.unbind().on('shown.bs.modal', function(){
            let URL         = window.URL || window.webkitURL,
                container   = document.querySelector('.img-container'),
                download    = document.getElementById('download'),
                actions     = document.getElementById('cropper-buttons'),
                options     = {
                    viewMode: 1,
                    aspectRatio: maxCroppedWidth / maxCroppedHeight,
                    background: false
                };

            cropper = new Cropper(image, options);
        }).on('hidden.bs.modal', function(){
            cropper.destroy();
            cropper = null;
        });

        $("#cropImage").unbind().click( function(){
            curentInput.parentNode.classList.add("d-none");

            let initialPhotoURL,
                canvas;

            $modal.modal("hide");

            if(cropper){
                canvas = cropper.getCroppedCanvas({
                    width: maxCroppedWidth,
                    height: maxCroppedHeight,
                });

                initialPhotoURL = picture.attr("src");
                picture
                    .attr("src", canvas.toDataURL())
                    .removeClass("d-none")
                    .parent().removeClass('d-none');

                canvas.toBlob(function (blob){
                    productPhotos[curentInput.id] = blob;
                });
            }
        });
    }

    function changePageLang(myLang){
        $(".lblNamePage").html(myLang.namePage);
        $(".btnPanel").html(`<i class="bi bi-plus-lg"></i> ${myLang.inputBtn}`);
        $(".colB").html(myLang.colB);
        $(".colC").html(myLang.colC);
        $(".colD").html(myLang.colD);
        $(".colE").html(myLang.colE);
        $(".colF").html(myLang.colF);
        $(".colG").html(myLang.colG);
        $("#offcanvasWithBackdropLabel").html(myLang.panelTitle);
        $(".labelName").html(myLang.labelName);
        $(".labelNameSp").html(myLang.labelNameSp);
        $(".labelDescription").html(myLang.labelDescription);
        $(".labelDescriptionSp").html(myLang.labelDescriptionSp);
        $(".labelPrice").html(myLang.labelPrice);
        $(".labelSalePrice").html(myLang.labelSalePrice);
        $(".labelCategory").html(myLang.labelCategory);
        $(".labelSizes").html(myLang.labelSizes);
        $(".labelColors").html(myLang.labelColors);
        $(".labelImage").html(myLang.labelImage);
        $("#addProduct").html(`<i class="bi bi-check2"></i> ${myLang.labelBtnSave}`);
        $(".labelSm").html(myLang.labelSm);
        $(".labelMed").html(myLang.labelMed);
        $(".labelLarge").html(myLang.labelLarge);
        $(".labelXLarge").html(myLang.labelXLarge);
        $("#inputColors").attr("placeholder", myLang.inputColors);

        mesages.ctrImage1 = myLang.ctrImage1;
        mesages.ctrImage2 = myLang.ctrImage2;
        mesages.ctrtoRemove = myLang.ctrtoRemove;
    }
</script>

<?php
    $content = ob_get_contents();
    ob_end_clean();

    include("index.php");
?>