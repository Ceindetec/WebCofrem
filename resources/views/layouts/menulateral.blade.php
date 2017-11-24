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
    @endrole
    <li>
        <a href="javascript: void(0);" aria-expanded="true"><i class="ti-layout"></i> Establecimientos <span
                    class="fa arrow"></span></a>
        <ul class="nav-second-level nav collapse" aria-expanded="true" style="">
            <li><a href="{{route('establecimientos')}}">Establecimientos</a></li>
            @can('trasladar.terminal')
                <li><a href="{{route('traladoterminal')}}">Trasladar terminal</a></li>
            @endcan
        </ul>
    </li>

    <li>
        <a href="javascript: void(0);"  aria-expanded="true"><i class="fa fa-university" aria-hidden="true"></i> Empresas <span class="fa arrow"></span></a>
         <ul class="nav-second-level nav collapse" aria-expanded="true" style="">
             <li><a href="{{route('empresas')}}">Empresas </a></li>
             <li><a href="{{route('contratos')}}">Contratos </a></li>

        </ul>
    </li>

    <li>
        <a href="javascript: void(0);" aria-expanded="true"><i class="ti-credit-card"></i> Servicios <span
                    class="fa arrow"></span></a>
        <ul class="nav-second-level nav collapse in" aria-expanded="true" style="">
            @can('parametrizar.producto')
                <li><a href="{{route('tarjetas.parametros')}}">Parametrización</a></li>
            @endcan
            <li><a href="{{route('tarjetas')}}">Inventario</a></li>
            @can('duplicar.tarjeta')
                <li><a href="{{route('tarjetas.duplicar')}}">Duplicado</a></li>
            @endcan
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
                    <li><a href="{{route('creartarjetasBonoBloque')}}">En Bloque</a></li>
                    <li><a href="{{route('consultabono')}}">Consulta</a></li>
                    <li><a href="{{route('bono.consultaxcontrato')}}">Consulta inteligente</a></li>
                </ul>
            </li>
        </ul>
    </li>
    <li>
        <a href="javascript: void(0);"  aria-expanded="true"><i class="fa fa-line-chart" aria-hidden="true"></i> Reportes <span class="fa arrow"></span></a>
        <ul class="nav-second-level nav collapse" aria-expanded="true" style="">
            <li><a href="{{route('rprimeravez')}}"> Tarjetas usadas por primera vez </a></li>
            <li><a href="{{route('reportes.saldosvencidos')}}"> Saldos vencidos </a></li>
            <li><a href="{{route('reportes.montostarjetas')}}"> Activación por montos </a></li>
            <li><a href="{{route('reportes.ventasdiarias')}}"> Ventas diarias </a></li>
            <li><a href="{{route('reportes.datafonosxestablecimientos')}}"> Relación de datafonos </a></li>
            <li><a href="{{route('reportes.saldotarjeta')}}"> Saldos de tarjeta </a></li>
            <li><a href="{{route('reportes.transaccionesxdatafono')}}"> Transacciones por datafono </a></li>
            <li><a href="{{route('reportes.promedioxdatafono')}}"> Consumo promedio por datafono</a></li>
            <li><a href="{{route('reportes.montosusados')}}"> Montos usados</a></li>
            <li><a href="{{route('reportes.ventasxsucursal')}}"> Ventas por sucursal</a></li>
            <li><a href="{{route('reportes.ventasxestablecimiento')}}"> Ventas por establecimiento</a></li>
        </ul>
    </li>
</ul>