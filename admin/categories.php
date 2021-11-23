<?php
    @session_start();
    ob_start();
?>
<!-- cropperCSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.9/cropper.min.css" integrity="sha512-w+u2vZqMNUVngx+0GVZYM21Qm093kAexjueWOv9e9nIeYJb1iEfiHC7Y+VvmP/tviQyA5IR32mwN/5hTEJx6Ng==" crossorigin="anonymous" referrerpolicy="no-referrer" />

<!-- cropperJS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.9/cropper.min.js" integrity="sha512-9pGiHYK23sqK5Zm0oF45sNBAX/JqbZEP7bSDHyt+nT3GddF+VFIcYNqREt0GDpmFVZI3LZ17Zu9nMMc9iktkCw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2 lblNamePage">Categories</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <button class="btn btn-outline-secondary btnPanel" type="button" data-bs-toggle="modal" data-bs-target="#modalCategoria"><i class="bi bi-plus-lg"></i> Add Category</button>
    </div>
</div>

<table class="table" id="categoryList">
    <thead class="table-light">
        <th class="colA">#</th>
        <th class="colB">Thumbnails</th>
        <th class="colC">Name</th>
        <th class="colD">Name spanish</th>
        <th class="colE"></th>
    </thead>
</table>

<!-- Modal para administrar categorias -->
<div class="modal fade" id="modalCategoria" tabindex="-1" role="dialog" aria-labelledby="modalLabelNew" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalLabelNew">Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="frmCategorie" class="needs-validation" novalidate>
                    <input type="hidden" name="categoryId" id="categoryId" value="0">
                    <div class="mb-3">
                        <label for="inputName" class="form-label labelNombreCat">Name</label>
                        <input type="text" class="form-control" id="inputName" name="inputName" placeholder="Category name" autocomplete="off" required>
                    </div>
                    <div class="mb-3">
                        <label for="inputNameSp" class="form-label labelNombreCatSp">Name spanish</label>
                        <input type="text" class="form-control" id="inputNameSp" name="inputNameSp" placeholder="Category name in spanish" autocomplete="off" required>
                    </div>
                    <center>
                        <figure class="figure d-none" id="imgPreview">
                            <img src="#" class="figure-img img-fluid rounded imgPreview">
                            <figcaption class="figure-caption text-end labelImg">Preview</figcaption>
                        </figure>
                    </center>
                    <div class="input-group mb-3">
                        <label class="input-group-text" for="inputPhoto"><i class="bi bi-camera"></i></label>
                        <input type="file" class="form-control" id="inputPhoto">
                    </div>
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="chkVisible">
                            <label class="form-check-label labelCheck" for="chkVisible">Visible on main page</label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btnmdlC" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="btnAddCategory">Apply</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para editar las imagenes -->
<div class="modal fade" id="modalCrop" tabindex="-1" role="dialog" aria-labelledby="modalLabelP" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalLabelP">Edit / Crop the photo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="img-container mb-3" style="max-height: 500px">
                    <img id="previewCrop" src="#">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btnmdlC" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="cropImage">Apply</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    let dataTableCategory = null,
        maxCroppedWidth = 300,
        maxCroppedHeight = 300,
        catPhoto = null,
        mesages = {//Control de mensajes para los ALERTS
            "ctrImage1":"Your selected file is larger than 5MB",
            "ctrImage2":"files not allowed, only images",
            "ctrtoRemove":"do you want to delete this category"
        };

    $(document).ready(function(){
        currentPage = "Categories";

        $("#btnAddCategory").click( fnRegisterCategory);

        // listar categorias
        fnGetCategories();

        // Iniciar componente de imagen
        initComponent();

        var myModalEl = document.getElementById('modalCategoria')
        myModalEl.addEventListener('hidden.bs.modal', function (event) {
            $("#inputPhoto").val("");
            $("#inputName").val("");
            $("#inputNameSp").val("");
            $("#chkVisible").prop("checked", false);
            $("#categoryId").val(0);
            $("#frmCategorie").removeClass("was-validated");
            $("#imgPreview").addClass("d-none");
        })
    });

    function fnRegisterCategory(){
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

        $("#btnAddCategory").attr("disabled","disabled");
        $("#btnAddCategory").html('<i class="bi bi-clock-history"></i> Registering');

        let form = $("#frmCategorie")[0],
            formData = new FormData(form);

        formData.append("_method", "POST");

        if(catPhoto){
            let namePhoto = ($("#inputName").val()).replace(" ", "_");
            formData.append("imageCat", catPhoto, `${namePhoto}.jpg`);
        }

        let visible = ($("#chkVisible").is(':checked')) ? 1 : 0;
        formData.append("chkVisible", visible);

        $.ajax({
            url: '../core/controllers/category.php',
            data: formData,
            type: 'POST',
            dataType: 'json',
            success: function(response){
                $("#btnAddCategory").removeAttr("disabled");
                $("#btnAddCategory").html('<i class="bi bi-plus-lg"></i> Add Category');

                $("#inputName").val("");
                $("#inputNameSp").val("");
                $("#chkVisible").prop("checked", false);
                $("#frmCategorie").removeClass("was-validated");
                $("#categoryId").val(0);
                $("#imgPreview").addClass("d-none");
                
                fnGetCategories();
                $("#modalCategoria").modal("hide");
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.log(textStatus);
            },
            cache: false,
            contentType: false,
            processData: false
        });
    }

    function fnGetCategories(){
        let objData = {
            "_method":"Get"
        };

        if (dataTableCategory != null)
            dataTableCategory.destroy();

        $.post("../core/controllers/category.php", objData, function(result) {
            let mypaging =  false;

            if( (result.data).length > 20 )
                mypaging = true;

            dataTableCategory = $("#categoryList").DataTable({
                data: result.data,
                order: [[ 0, "desc" ]],
                columns:
                [
                    {
                        data: 'id',
                        width: "20px",
                        render: function(data, type, row){
                            return pad(data, 5);
                        }
                    },
                    {
                        data: 'thumbnail',
                        orderable: false,
                        class: "text-center",
                        width: "100px",
                        render: function ( data, type, row ) {
                            let img = (data) ? `../${data}` : "../assets/img/defaultCat.jpg";

                            return `
                                <img src="${img}" class="rounded" alt="Image cat" width="100">
                            `;
                        }
                    },
                    {
                        data: 'name',
                    },
                    {
                        data: 'nameSp',
                    },
                    {
                        data: null,
                        orderable: false,
                        class: "text-center",
                        width: "200px",
                        render: function ( data, type, row )
                        {
                            let status = (row.parent && row.parent > 0) ? 'checked' : '';
                            return `
                                <div class="row">
                                    <div class="col">
                                        <a href="javascript:void(0);" class="btn btn-outline-danger btnDeleteCategory" title="Delete"><i class="bi bi-trash"></i></a>
                                        <a href="javascript:void(0);" class="btn btn-outline-warning btnModifyCategory" title="Modify"><i class="bi bi-pencil"></i></a>
                                    </div>
                                    <div class="col mt-2">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input chVisible" type="checkbox" ${status} id="chvis${row.id}">
                                            <label class="form-check-label" for="chvis${row.id}">Visible</label>
                                        </div>
                                    </div>
                                </div>
                            `;
                        }
                    }
                ],
                "fnDrawCallback":function(oSettings){
                    $(".btnDeleteCategory").unbind().click(function(){
                        let data = getData($(this), dataTableCategory),
                            buton = $(this);

                        if (confirm(`${mesages.ctrtoRemove} (${data.name})?`)){
                            buton.attr("disabled","disabled");
                            buton.html('<i class="bi bi-clock-history"></i>');

                            let objData = {
                                "_method":"Delete",
                                "categoryId": data.id
                            };

                            $.post("../core/controllers/category.php", objData, function(result) {
                                buton.removeAttr("disabled");
                                buton.html('<i class="bi bi-trash"></i>');

                                fnGetCategories();
                            });

                        }
                    });

                    $(".chVisible").unbind().change(function(){
                        let data = getData($(this), dataTableCategory),
                            buton = $(this),
                            visible = ($(this).is(':checked')) ? 1 : 0;

                        let objData = {
                            "_method":"unVisivility",
                            "categoryId": data.id,
                            "visible": visible
                        };

                        $.post("../core/controllers/category.php", objData);
                    });

                    $(".btnModifyCategory").unbind().click(function(){
                        let data = getData($(this), dataTableCategory),
                            visible = (data.parent) ? (data.parent == 1) ? true : false : false;

                        catPhoto = null;

                        $("#categoryId").val(data.id);
                        $("#inputName").val(data.name);
                        $("#inputNameSp").val(data.nameSp);
                        $("#chkVisible").prop("checked", visible);

                        let img = (data.thumbnail) ? `../${data.thumbnail}` : "../assets/img/defaultCat.jpg";
                        $(".imgPreview").attr("src", img).parent().removeClass("d-none");

                        $("#modalCategoria").modal("show");
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
        let picture = $(".imgPreview"),
            image       = $("#previewCrop")[0],
            inputFile1   = $("#inputPhoto")[0],
            $modal      = $('#modalCrop'),
            cropper     = null;

        inputFile1.addEventListener("change", function(e){
            let files = e.target.files,
                done  = function (url){
                    inputFile1.value = "";
                    image.src = url;
                    $modal.modal('show');
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
            let canvas;

            $modal.modal("hide");

            if(cropper){
                canvas = cropper.getCroppedCanvas({
                    width: maxCroppedWidth,
                    height: maxCroppedHeight,
                });

                picture
                    .attr("src", canvas.toDataURL())
                    .parent().removeClass('d-none');

                canvas.toBlob(function (blob){
                    catPhoto = blob;
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

        $("#modalLabelNew").html(myLang.labelModal1);
        $(".labelNombreCat").html(myLang.labelNombreCat);
        $(".labelNombreCatSp").html(myLang.labelNombreCatSp);
        $(".labelImg").html(myLang.labelImg);
        $(".labelCheck").html(myLang.labelCheck);
        $("#inputName").attr("placeholder", myLang.inputName);
        $("#inputNameSp").attr("placeholder", myLang.inputNameSp);

        $("#modalLabelP").html(myLang.modalLabelP);
        $(".btnmdlC").html(myLang.btnmdlC);
        $("#cropImage, #btnAddCategory").html(myLang.cropImage);

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