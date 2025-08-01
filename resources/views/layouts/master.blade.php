<!DOCTYPE html>
<html>
    <head>
        <!-- Global site tag (gtag.js) - Google Analytics -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=G-0M43YF0CSW"></script>
        <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'G-0M43YF0CSW');
        </script>

        <title>@yield("title", "Portal Atos Documentos IFAL - Uma plataforma de buscas sobre portarias, resoluções e decretos")</title>

        <meta name="robots" content="index, follow">
        
        <meta name="description" 
            content="@yield("description", 'No portal você pode realizar buscas sobre portarias, resoluções, decretos e outros atos produzidos na instituição')" />
        
        <meta name="keywords" content="@yield("keywords", "unidades educação, normas, edital, resolução, ata, ementa, normativas, normativos")"  />

        <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
        <meta property="og:image" content="/img/social.png">

        <link href="/css/bootstrap.min.css" rel="stylesheet">
        <link href="/css/star-rating.min.css" media="all" rel="stylesheet" type="text/css"/>
        <link href="/css/theme.min.css" media="all" rel="stylesheet" type="text/css"/>
        <link href="/css/app-search.css" media="all" rel="stylesheet" type="text/css"/>
        <link href="/vendor/tagsinput/bootstrap-tagsinput.css" rel="stylesheet">

        <link href="/img/favicon.ico" rel="shortcut icon">

        @stack('estilos-caio')
    </head>
    <style>
    a{
        text-decoration: none !important;
        color: inherit !important;
    }

    .card-primary:not(.card-outline)>.card-header{
        background-color: #45a050;
    }
    .custom-switch {
    position: relative;
    display: inline-flex;
    align-items: center;
    width: 3.5em;
    height: 1.8em;
    margin-right: 10px;
}

.custom-switch input {
    opacity: 0;
    width: 0;
    height: 0;
    margin-left: 20px;

}

.slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc;
    transition: 0.4s;
    border-radius: 2em;
}

.slider::before {
    position: absolute;
    content: "";
    height: 1.3em;
    width: 1.3em;
    left: 0.25em;
    bottom: 0.25em;
    background-color: white;
    transition: 0.4s;
    border-radius: 50%;
}

input:checked + .slider {
    background-color: #28a745;
}

input:checked + .slider:before {
    transform: translateX(1.7em);
}

.switch-label {
    margin-left: 0.5em;
    font-size: 1em;
    font-weight: 500;
    white-space: nowrap;
}


    </style>

     <body>
        <div id="barra-brasil" style="background:#7F7F7F; height: 20px; padding:0 0 0 10px;display:block;">
            <ul id="menu-barra-temp" style="list-style:none;">
                <li style="display:inline; float:left;padding-right:10px; margin-right:10px; border-right:1px solid #EDEDED">
                    <a href="http://brasil.gov.br" style="font-family:sans,sans-serif; text-decoration:none; color:white;">Portal do Governo Brasileiro</a>
                </li>
                <li>
                <a style="font-family:sans,sans-serif; text-decoration:none; color:white;" href="http://epwg.governoeletronico.gov.br/barra/atualize.html">Atualize sua Barra de Governo</a>
                </li>
            </ul>
        </div>
        <div id="content">
            @yield('content')
        </div>

        <!-- app  scripts -->
        <script src="/js/jquery.min.js" type="text/javascript"></script>
        <script src="/js/jquery-migrate.min.js" type="text/javascript"></script>
        <script src="/js/bootstrap.min.js"></script>
        <script src="/js/star-rating.min.js" type="text/javascript"></script>
        <script src="/js/theme.min.js"></script>
        <script src="/js/normativas-search.js" type="text/javascript"></script>

        @stack('scripts-caio')
        <script>
            $(document).on('ready', function () {

                $('.kv-fa').rating({
                    theme: 'krajee-fa',
                    filledStar: '<i class="fa fa-star"></i>',
                    emptyStar: '<i class="fa fa-star-o"></i>'
                });
            });
        </script>
        <script defer="defer" src="//barra.brasil.gov.br/barra.js" type="text/javascript"></script>
        <!-- fim app  scripts -->

        <!--<footer style="background-color: #45a050">

            <div class="container" style="height: 100%;">
                <div class="col-lg-12">
              
                </div>
            </div>
        </footer>-->


    </body>
</html>
