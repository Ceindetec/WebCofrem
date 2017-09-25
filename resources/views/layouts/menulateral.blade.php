<!-- Left Menu Start -->

<ul class="metisMenu nav" id="side-menu">
    @role('admin')
    <li>
        <a href="javascript: void(0);" aria-expanded="true"><i class="ti-settings"></i> Administración
            <span class="fa arrow"></span></a>
        <ul class="nav-second-level nav" aria-expanded="true">
            <li><a href="{{route('usuarios')}}">Usuarios</a></li>
            <li><a href="{{route('roles')}}">Roles</a></li>
            <li><a href="{{route('permisos')}}">Permisos</a></li>
        </ul>
    </li>
    <li>
        <a href="javascript: void(0);"  aria-expanded="true"><i class="ti-layout"></i> Establecimientos <span class="fa arrow"></span></a>
        <ul class="nav-second-level nav collapse" aria-expanded="true" style="">
            <li><a href="{{route('establecimientos')}}">Crear establecimeintos</a></li>
            <li><a href="{{route('traladoterminal')}}">Trasladar terminal</a></li>
        </ul>
    </li>
    {{--<li>--}}
    <li><a href="{{route('empresas')}}"><i class="fa fa-university" aria-hidden="true"></i> Empresas </a></li>
            {{--<span class="fa arrow"></span></a>--}}
        {{--<ul class="nav-second-level nav" aria-expanded="true">--}}
            {{--<li><a href="{{route('usuarios')}}">Usuarios</a></li>--}}
            {{--<li><a href="{{route('roles')}}">Roles</a></li>--}}
            {{--<li><a href="{{route('permisos')}}">Permisos</a></li>--}}
        {{--</ul>--}}
        {{--<a href="{{route('tarjetas')}}"><i class="ti-credit-card"></i> Inventario </a>--}}
    {{--</li>--}}

    <li>
        <a href="javascript: void(0);" aria-expanded="true"><i class="ti-credit-card"></i> Servicios <span class="fa arrow"></span></a>
        <ul class="nav-second-level nav collapse in" aria-expanded="true" style="">
            <li><a href="{{route('tarjetas.parametros')}}">Parametrizacion</a></li>
            <li><a href="{{route('tarjetas')}}">Inventario</a></li>
            <li><a href="{{route('tarjetas.duplicar')}}">Duplicado</a></li>
            <li><a href="javascript: void(0);" aria-expanded="true">Regalo <span class="fa arrow"></span></a>
                <ul class="nav-third-level nav collapse" aria-expanded="false">
                    <li><a href="{{route('crearTarjetaRegalo')}}">Individualmente</a></li>
                    <li><a href="{{route('crearTarjetaBloque')}}">En Bloque</a></li>
                    <li><a href="{{route('consultaregalo')}}">Consulta</a></li>
                </ul>
            </li>
            <li><a href="javascript: void(0);" aria-expanded="true">Bono Empresarial <span class="fa arrow"></span></a>
                <ul class="nav-third-level nav collapse" aria-expanded="false">
                    <li><a href="{{route('creartarjetasBono')}}">Individualmente</a></li>
                    <li><a href="javascript: void(0);">En Bloque</a></li>
                </ul>
            </li>
        </ul>
    </li>
   <!-- <li><a href="{ {route('tarjetasregalo')}}"><i class="ti-layout"></i> Tarjetas Regalo</a></li> -->
@endrole

<!--<li><a href="index.html"><i class="ti-home"></i> Dashboard </a></li>

    <li><a href="ui-elements.html"><span class="label label-custom pull-right">11</span> <i
                    class="ti-paint-bucket"></i> UI Elements </a></li>

    <li>
        <a href="javascript: void(0);" aria-expanded="true"><i class="ti-light-bulb"></i> Components
            <span class="fa arrow"></span></a>
        <ul class="nav-second-level nav" aria-expanded="true">
            <li><a href="components-range-slider.html">Range Slider</a></li>
            <li><a href="components-alerts.html">Alerts</a></li>
            <li><a href="components-icons.html">Icons</a></li>
            <li><a href="components-widgets.html">Widgets</a></li>
        </ul>
    </li>

    <li><a href="typography.html"><i class="ti-spray"></i> Typography </a></li>

    <li>
        <a href="javascript: void(0);" aria-expanded="true"><i class="ti-pencil-alt"></i> Forms
            <span class="fa arrow"></span></a>
        <ul class="nav-second-level nav" aria-expanded="true">
            <li><a href="forms-general.html">General Elements</a></li>
            <li><a href="forms-advanced.html">Advanced Form</a></li>
        </ul>
    </li>

    <li>
        <a href="javascript: void(0);" aria-expanded="true"><i class="ti-menu-alt"></i> Tables <span
                    class="fa arrow"></span></a>
        <ul class="nav-second-level nav" aria-expanded="true">
            <li><a href="tables-basic.html">Basic tables</a></li>
            <li><a href="tables-advanced.html">Advanced tables</a></li>
        </ul>
    </li>

    <li><a href="charts.html"><span class="label label-custom pull-right">5</span> <i
                    class="ti-pie-chart"></i> Charts </a></li>

    <li><a href="maps.html"><i class="ti-location-pin"></i> Maps </a></li>

    <li>
        <a href="javascript: void(0);" aria-expanded="true"><i class="ti-files"></i> Pages <span
                    class="fa arrow"></span></a>
        <ul class="nav-second-level nav" aria-expanded="true">
            <li><a href="pages-login.html">Login</a></li>
            <li><a href="pages-register.html">Register</a></li>
            <li><a href="pages-forget-password.html">Forget Password</a></li>
            <li><a href="pages-lock-screen.html">Lock-screen</a></li>
            <li><a href="pages-blank.html">Blank page</a></li>
            <li><a href="pages-404.html">Error 404</a></li>
            <li><a href="pages-confirm-mail.html">Confirm Mail</a></li>
            <li><a href="pages-session-expired.html">Session Expired</a></li>
        </ul>
    </li>

    <li>
        <a href="javascript: void(0);" aria-expanded="true"><i class="ti-widget"></i> Extra Pages
            <span class="fa arrow"></span></a>
        <ul class="nav-second-level nav" aria-expanded="true">
            <li><a href="extras-timeline.html">Timeline</a></li>
            <li><a href="extras-invoice.html">Invoice</a></li>
            <li><a href="extras-profile.html">Profile</a></li>
            <li><a href="extras-calendar.html">Calendar</a></li>
            <li><a href="extras-faqs.html">FAQs</a></li>
            <li><a href="extras-pricing.html">Pricing</a></li>
            <li><a href="extras-contacts.html">Contacts</a></li>
        </ul>
    </li>

    <li>
        <a href="javascript: void(0);" aria-expanded="true"><i class="ti-share"></i> Multi Level
            <span class="fa arrow"></span></a>
        <ul class="nav-second-level nav" aria-expanded="true">
            <li><a href="javascript: void(0);">Level 1.1</a></li>
            <li><a href="javascript: void(0);" aria-expanded="true">Level 1.2 <span
                            class="fa arrow"></span></a>
                <ul class="nav-third-level nav" aria-expanded="true">
                    <li><a href="javascript: void(0);">Level 2.1</a></li>
                    <li><a href="javascript: void(0);">Level 2.2</a></li>
                </ul>
            </li>
        </ul>
    </li>-->
</ul>