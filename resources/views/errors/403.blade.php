<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>SimpleAdmin - Responsive Admin Dashboard Template</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <meta content="A fully featured admin theme which can be used to build CRM, CMS, etc." name="description" />
    <meta content="Coderthemes" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <link rel="shortcut icon" href="{{asset('/images/favicon.ico')}}">

    <!-- Bootstrap core CSS -->
    <link href="{{asset('css/bootstrap.min.css')}}" rel="stylesheet">
    <!-- MetisMenu CSS -->
    <link href="{{asset('css/metisMenu.min.css')}}" rel="stylesheet">
    <!-- Icons CSS -->
    <link href="{{asset('css/icons.css')}}" rel="stylesheet">
    <!-- Custom styles for this template -->
    <link href="{{asset('css/style.css')}}" rel="stylesheet">

</head>


<body>

<!-- HOME -->
<section>
    <div class="container">
        <div class="row">
            <div class="col-sm-12">

                <div class="wrapper-page">

                    <div class="m-t-40 card-box">
                        <div class="text-center">
                            <h2 class="text-uppercase m-t-0 m-b-30">
                                <a href="index.html" class="text-success">
                                    <span><img src="{{url('images/logo.png')}}" alt="" height="60"></span>
                                </a>
                            </h2>
                            <!--<h4 class="text-uppercase font-bold m-b-0">Sign In</h4>-->
                        </div>
                        <div class="account-content">
                            <div class="text-center m-b-20">
                                <img src="{{url('images/cancel.svg')}}" title="invite.svg" height="80" class="m-t-10">
                                <h3 class="expired-title">Sin suficientes privilegios</h3>
                                <p class="text-muted m-t-30 line-h-24"> Necesita tener el rol, o los permisos necesarios para ingresar a esta área del sistema </p>
                            </div>

                            <div class="row m-t-30">
                                <div class="col-xs-12">
                                    <a href="{{route('login')}}" class="btn btn-lg btn-custom btn-block" type="submit">Volver al Inicio</a>
                                </div>
                            </div>

                            <div class="clearfix"></div>

                        </div>
                    </div>
                    <!-- end card-box-->

                </div>
                <!-- end wrapper -->

            </div>
        </div>
    </div>
</section>
<!-- END HOME -->



<!-- js placed at the end of the document so the pages load faster -->
<script src="{{asset('js/jquery-2.1.4.min.js')}}"></script>
<script src="{{asset('js/bootstrap.min.js')}}"></script>
<script src="{{asset('js/metisMenu.min.js')}}"></script>
<script src="{{asset('js/jquery.slimscroll.min.js')}}"></script>

<!-- App Js -->
<script src="{{asset('js/jquery.app.js')}}"></script>

</body>
</html>