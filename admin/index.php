<?php
    @session_start();
    if (!isset($_SESSION['isLoged']))
        header("Location: login.html");
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <!-- Bootstrap icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.6.1/font/bootstrap-icons.css">
    <!-- Admin css -->
    <link href="../assets/css/admin.css" rel="stylesheet">
    <!-- Datatables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/dataTables.bootstrap5.min.css">
    <!-- Jquery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>

    <title>Store Admin</title>

    <style>
        .bd-placeholder-img {
            font-size: 1.125rem;
            text-anchor: middle;
            -webkit-user-select: none;
            -moz-user-select: none;
            user-select: none;
        }

        @media (min-width: 768px) {
            .bd-placeholder-img-lg {
                font-size: 3.5rem;
            }
        }

        hr {
            margin-top: 1rem;
            margin-bottom: 1rem;
            border: 0;
            border-top: 1px solid rgba(0, 0, 0, 0.1);
        }

        .offcanvas-end {
            width: 750px;
        }

        /*Forzar a que se muestre el fondo blanco en el modal del cropperJS*/
        .cropper-modal{
            background-color: #fff !important;
            opacity: .7 !important;
        }
    </style>
</head>
<body>
    <header class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0 shadow">
        <a class="navbar-brand col-md-3 col-lg-2 me-0 px-3" href="javascript:void(0);">La Chapinita</a>
        <button class="navbar-toggler position-absolute d-md-none collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <!-- <input class="form-control form-control-dark w-100 lblInputSearch" type="text" placeholder="Search" aria-label="Search"> -->
        <div class="dropdown w-100">
            <input type="text" class="form-control dropdown-toggle form-control-dark w-100 lblInputSearch" id="inputSearch" placeholder="Search..." autocomplete="off" >
            <ul class="dropdown-menu" aria-labelledby="inputSearch"></ul>
        </div>
        <div class="navbar-nav">
            <div class="nav-item text-nowrap">
                <a class="nav-link px-3" href="javascript:void(0);" id="btnLogout">Sign out</a>
            </div>
        </div>
    </header>

    <div class="container-fluid">
        <div class="row">    
            <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
                <div class="position-sticky pt-3">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active changeLang" aria-current="page" href="javascript:void(0);">
                                <i class="bi bi-globe2"></i> Español
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link linkCategories" href="categories.php">
                                <i class="bi bi-tags"></i> Categories
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link linkProduct" href="product.php">
                                <i class="bi bi-bag"></i> Products
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link linkCoupons" href="coupons.php">
                                <i class="bi bi-award"></i> Coupons
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link linkOrders" href="orders.php">
                                <i class="bi bi-basket"></i> Orders
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="linkSetting" href="settings.php">
                                <i class="bi bi-sliders"></i> Settings
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <?php
                    if(isset($content)){
                        echo $content;
                    } else {
                        echo '<script>window.location.href = "orders.php";</script>';
                    }
                ?>
            </main>
        </div>
    </div>

    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    <!-- Datatables -->
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.3/js/dataTables.bootstrap5.min.js"></script>

    <script type="text/javascript">
        var isNew = <?php echo $_SESSION['authData']->isDefault; ?>,
            currentPage = "",
            lang = "en",
            searchRequest = null;

        $(document).ready(function(){
            if( localStorage.getItem("currentLag") ){
                lang = localStorage.getItem("currentLag");
            }else{
                localStorage.setItem("currentLag", lang);
            }

            switchLanguage(lang);

            if(isNew == 1 && currentPage != "Settings")
                document.getElementById("linkSetting").click();

            $("#btnLogout").on("click", function(){
                if (confirm(`do you really want to log out?`)){
                    localStorage.removeItem("logged");
                    window.location.replace("../core/controllers/logout.php");
                }
            });

            $(".changeLang").click( function(){
                if (localStorage.getItem("currentLag") == "es") {
                    localStorage.setItem("currentLag", "en");
                    lang = "en";
                }else{
                    localStorage.setItem("currentLag", "es");
                    lang = "es";
                }
                switchLanguage(lang);
            });

            // Metodo para buscar lo escrito en el inputsearch, si ya existe una busqueda previa que no se termina, se cancela y se reinicia
            $('#inputSearch').keyup(function(){
                if(searchRequest)
                    searchRequest.abort();

                searchRequest = $.ajax({
                    url:`../core/controllers/product.php`,
                    method:"POST",
                    data:{
                        _method:'search',
                        strQuery: $('#inputSearch').val()
                    },
                    success:function(data){
                        let items = '';
                        $.each(data.data, function(index, prod){
                            let img = (prod.thumbnail != "" &&  prod.thumbnail != "0") ? `../${prod.thumbnail}` : "../assets/img/default.jpg";

                            items += `
                                <li>
                                    <a class="dropdown-item" href="../product/index.php?pid=${prod.id}">
                                        <img src="${img}" alt="twbs" height="32" class="rounded flex-shrink-0 me-2">
                                        ${prod.name}
                                    </a>
                                </li>
                            `;
                        });

                        $(".dropdown-menu")
                            .html(items)
                            .addClass("show");
                    }
                });
            });

            // Metodo para ocultar el resultado de la busqueda si se hace click en cualquier parte
            $('body').click(function() {
                if(searchRequest)
                    searchRequest.abort();

                $(".dropdown-menu")
                    .html("")
                    .removeClass("show");
            });
        });

        function pad (str, max) {
            str = str.toString();
            return str.length < max ? pad("0" + str, max) : str;
        }

        function getData(obj, dtable){
            let tr   = obj.parents("tr");
            return dtable.row( tr ).data();
        }

        function switchLanguage(lang){
            $.post("../assets/langAdmin.json", {}, function(data) {
                $(".changeLang").html('<i class="bi bi-globe2"></i> ' + data[lang]["buttonText"]);

                let myLang = data[lang]["main"];

                $(".lblInputSearch").attr("placeholder", myLang.inputSearch);
                $("#btnLogout").html(myLang.linkLogout);
                $(".linkCategories").html(`<i class="bi bi-tags"></i> ${myLang.linkCategories}`);
                $(".linkProduct").html(`<i class="bi bi-bag"></i> ${myLang.linkProduct}`);
                $(".linkCoupons").html(`<i class="bi bi-award"></i> ${myLang.linkCoupons}`);
                $(".linkOrders").html(`<i class="bi bi-basket"></i> ${myLang.linkOrders}`);
                $("#linkSetting").html(`<i class="bi bi-sliders"></i> ${myLang.linkSetting}`);

                changePageLang(data[lang][currentPage]);
            });
        }
    </script>
</body>
</html>
