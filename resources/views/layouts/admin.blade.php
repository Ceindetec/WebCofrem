<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8"/>
    <title>{{ config('app.name', 'Laravel') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
    <meta content="A fully featured admin theme which can be used to build CRM, CMS, etc." name="description"/>
    <meta content="Coderthemes" name="author"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="shortcut icon" href="{{asset('images/logo_sm.png')}}">


    <link href="{{asset('plugins/bootstrap-tagsinput/css/bootstrap-tagsinput.css')}}" rel="stylesheet" />

    <!-- Bootstrap core CSS -->
{{Html::Style('css/bootstrap.min.css')}}
<!-- MetisMenu CSS -->
{{Html::Style('css/metisMenu.min.css')}}
<!-- Icons CSS -->
    {{Html::Style('css/icons.css')}}

    <link href="{{asset('plugins/bootstrap-sweetalert/sweetalert.css')}}" rel="stylesheet" type="text/css">
    <link href="{{asset('plugins/select2/css/select2.min.css')}}" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" href="{{asset('plugins/switchery/switchery.min.css')}}">

<!-- Custom styles for this template -->
    {{Html::Style('css/style.css')}}

    <style>

        #contecarga{
            display: none;
            position: absolute;
            top:0;
            width: 98%;
            height: 100%;
            z-index: 5000;
        }

        #fondo{
            position: fixed;
            top:0;
            width: 100%;
            height: 100%;
            display: block;
            background-color: #fff;
            opacity: .5;
        }

        #imacarga{
            position: fixed;
            top:0;
            opacity: 1;
        }

        td.details-control {
            /*background: url('../images/details_open.png') no-repeat center center;*/
            color: green;
            text-align: center;
            cursor: pointer;
            width: 25px;
        }
        tr.shown td.details-control {
            color:red;
           /* background: url('../images/details_close.png') no-repeat center center;*/
        }

    </style>

    @yield("styles")

</head>


<body>

<div id="page-wrapper">

    <!-- Top Bar Start -->
    <div class="topbar">

        <!-- LOGO -->
        <div class="topbar-left">
            <div class="">
                <a href="{{route('login')}}" class="logo">
                    <img src="{{url('images/logo.png')}}" alt="logo" class="logo-lg" style="height: 40px"/>
                    <img src="{{url('images/logo_sm.png')}}" alt="logo" class="logo-sm hidden"/>
                </a>
            </div>
        </div>

        <!-- Top navbar -->
        <div class="navbar navbar-default" role="navigation">
            <div class="container">
                <div class="">

                    <!-- Mobile menu button -->
                    <div class="pull-left">
                        <button type="button" class="button-menu-mobile visible-xs visible-sm">
                            <i class="fa fa-bars"></i>
                        </button>
                        <span class="clearfix"></span>
                    </div>

                    <!-- Top nav left menu -->
                    <!--<ul class="nav navbar-nav hidden-sm  hidden-xs top-navbar-items">
                        <li><a href="#">About</a></li>
                        <li><a href="#">Help</a></li>
                        <li><a href="#">Contact</a></li>
                    </ul>-->

                    <!-- Top nav Right menu -->
                    <ul class="nav navbar-nav navbar-right top-navbar-items-right pull-right">
                        <!--<li class="dropdown top-menu-item-xs">
                            <a href="#" data-target="#" class="dropdown-toggle menu-right-item" data-toggle="dropdown"
                               aria-expanded="true">
                                <i class="mdi mdi-bell"></i> <span class="label label-danger">3</span>
                            </a>
                            <ul class="dropdown-menu p-0 dropdown-menu-lg">
                                <li class="list-group notification-list" style="height: 267px;">
                                    <div class="slimscroll">
                                        <a href="javascript:void(0);" class="list-group-item">
                                            <div class="media">
                                                <div class="media-left p-r-10">
                                                    <em class="fa fa-diamond bg-primary"></em>
                                                </div>
                                                <div class="media-body">
                                                    <h5 class="media-heading">A new order has been placed A new order
                                                        has been placed</h5>
                                                    <p class="m-0">
                                                        <small>There are new settings available</small>
                                                    </p>
                                                </div>
                                            </div>
                                        </a>

                                        <a href="javascript:void(0);" class="list-group-item">
                                            <div class="media">
                                                <div class="media-left p-r-10">
                                                    <em class="fa fa-cog bg-warning"></em>
                                                </div>
                                                <div class="media-body">
                                                    <h5 class="media-heading">New settings</h5>
                                                    <p class="m-0">
                                                        <small>There are new settings available</small>
                                                    </p>
                                                </div>
                                            </div>
                                        </a>


                                        <a href="javascript:void(0);" class="list-group-item">
                                            <div class="media">
                                                <div class="media-left p-r-10">
                                                    <em class="fa fa-bell-o bg-custom"></em>
                                                </div>
                                                <div class="media-body">
                                                    <h5 class="media-heading">Updates</h5>
                                                    <p class="m-0">
                                                        <small>There are <span class="text-primary font-600">2</span>
                                                            new updates available
                                                        </small>
                                                    </p>
                                                </div>
                                            </div>
                                        </a>


                                        <a href="javascript:void(0);" class="list-group-item">
                                            <div class="media">
                                                <div class="media-left p-r-10">
                                                    <em class="fa fa-user-plus bg-danger"></em>
                                                </div>
                                                <div class="media-body">
                                                    <h5 class="media-heading">New user registered</h5>
                                                    <p class="m-0">
                                                        <small>You have 10 unread messages</small>
                                                    </p>
                                                </div>
                                            </div>
                                        </a>


                                        <a href="javascript:void(0);" class="list-group-item">
                                            <div class="media">
                                                <div class="media-left p-r-10">
                                                    <em class="fa fa-diamond bg-primary"></em>
                                                </div>
                                                <div class="media-body">
                                                    <h5 class="media-heading">A new order has been placed A new order
                                                        has been placed</h5>
                                                    <p class="m-0">
                                                        <small>There are new settings available</small>
                                                    </p>
                                                </div>
                                            </div>
                                        </a>

                                        <a href="javascript:void(0);" class="list-group-item">
                                            <div class="media">
                                                <div class="media-left p-r-10">
                                                    <em class="fa fa-cog bg-warning"></em>
                                                </div>
                                                <div class="media-body">
                                                    <h5 class="media-heading">New settings</h5>
                                                    <p class="m-0">
                                                        <small>There are new settings available</small>
                                                    </p>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                </li>
                            </ul>
                        </li>-->

                        <li class="dropdown top-menu-item-xs">
                            <a href="" class="dropdown-toggle menu-right-item profile" data-toggle="dropdown"
                               aria-expanded="true"><img src="{{url('images/avatar2.png')}}" alt="user-img"
                                                         class="img-circle"> </a>
                            <ul class="dropdown-menu">
                                <!--<li><a href="javascript:void(0)"><i class="ti-user m-r-10"></i> Profile</a></li>
                                <li><a href="javascript:void(0)"><i class="ti-settings m-r-10"></i> Settings</a></li>
                                <li><a href="javascript:void(0)"><i class="ti-lock m-r-10"></i> Lock screen</a></li>
                                <li class="divider"></li>-->
                                <li><a href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();"><i
                                                class="ti-power-off m-r-10"></i> Logout</a> <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                        {{ csrf_field() }}
                                    </form></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div> <!-- end container -->
        </div> <!-- end navbar -->
    </div>
    <!-- Top Bar End -->


    <!-- Page content start -->
    <div class="page-contentbar">

        <!--left navigation start-->
        <aside class="sidebar-navigation">
            <div class="scrollbar-wrapper">
                <div>
                    <button type="button" class="button-menu-mobile btn-mobile-view visible-xs visible-sm">
                        <i class="mdi mdi-close"></i>
                    </button>
                    <!-- User Detail box -->
                    <div class="user-details">
                        <div class="pull-left">
                            <img src="{{url('images/avatar2.png')}}" alt="" class="thumb-md img-circle">
                        </div>
                        <div class="user-info">
                            <a href="#">{{Auth::User()->name}}</a>
                            <p class="text-muted m-0">{{Auth::User()->getRoles()[0]->name}}</p>
                        </div>
                    </div>
                    <!--- End User Detail box -->


                    @include('layouts.menulateral')

                </div>
            </div><!--Scrollbar wrapper-->
        </aside>
        <!--left navigation end-->

        <!-- START PAGE CONTENT -->
        <div id="page-right-content">


            @yield('contenido')

            <div class="footer">
                <!--<div class="pull-right hidden-xs">
                    Project Completed <strong class="text-custom">39%</strong>.
                </div>-->
                <div>
                    <strong>Ceindetec Llanos</strong> - Copyright &copy; 2017
                </div>
            </div> <!-- end footer -->

        </div>
        <!-- End #page-right-content -->

        <div class="clearfix"></div>

    </div>
    <!-- end .page-contentbar -->
</div>
<!-- End #page-wrapper -->

<!-- Modal Bootstrap-->
<div id='modalBs' class='modal fade' tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
        </div>
    </div>
</div>

<!--efecto cargando para formulario-->
<div id="contecarga">
    <div id="fondo"></div>
    <img src="{{asset('images/62157.gif')}}" id="imacarga">
</div>

<!-- js placed at the end of the document so the pages load faster -->
{{Html::Script("js/jquery-2.1.4.min.js")}}
{{Html::Script("js/bootstrap.min.js")}}
{{Html::Script("js/metisMenu.min.js")}}
{{Html::Script("js/jquery.slimscroll.min.js")}}

<script src="{{asset('plugins/bootstrap-tagsinput/js/bootstrap-tagsinput.min.js')}}"></script>
<script src="{{asset('plugins/bootstrap-filestyle/js/bootstrap-filestyle.min.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/bootstrap-sweetalert/sweetalert.min.js')}}"></script>

<script src="{{asset('plugins/select2/js/select2.min.js')}}" type="text/javascript"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/js/i18n/es.js"></script>

<script src="{{asset('plugins/parsleyjs/parsley.min.js')}}"></script>
<script src="{{asset('plugins/parsleyjs/idioma/es.js')}}"></script>

<script src="{{asset('plugins/switchery/switchery.min.js')}}"></script>
<!-- App Js -->
{{Html::Script("js/jquery.app.js")}}

{{Html::Script("js/inicio.js")}}

<script>



function cargando() {
    $('#contecarga').show();
    alto = window.innerHeight;
    ancho = window.innerWidth;
    $('#imacarga').css({
        'top':(alto/2)-53,
        'left':(ancho/2)-53
    })
}

function fincarga() {
    $('#contecarga').hide();
}

</script>

@yield("scripts")

</body>
</html>