<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>ReportePDF</title>

    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }

        th, td {
            text-align: left;
            padding: 8px;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2
        }

        th {
            background-color: #4CAF50;
            color: white;
        }

        #logo {
            float: left;
            width: 48%;
            display: inline-block;
            margin-bottom: 20px;
        }

        #info {
            float: right;
            width: 50%;
            display: inline-block;
            margin-bottom: 20px;
            text-align: right;
        }
        .table{
            clear: both;
        }

        ul {
            list-style: none;
            margin: 0;
            padding: 0;
        }
    </style>

</head>
<body>
<div class="row">

    <div id="logo">
        <img src="{{url('images/logo_mini.png')}}" width="150px">
    </div>
    <div id="info">
        <ul>
            <li><label>Nombre: </label><strong>Consumo promedio por datafono</strong></li>
            <li><label>Fecha: </label><strong>{{\Carbon\Carbon::now()->format('d/m/Y')}}</strong></li>
            <li><label>Rango: </label><strong>{{$rango}}</strong></li>
        </ul>

    </div>
    <br>
    @if(sizeof($establecimientos)>0)
        @foreach($establecimientos as $establecimiento)
            <h5>Establecimiento: {{$establecimiento->razon_social}}</h5>
            <?php $haysucursal=0;
            $hayterminal=0;?>
            @if(sizeof($sucursales)>0)
                @foreach($sucursales as $sucursale)
                    @if($sucursale->establecimiento_id==$establecimiento->id)
                        <?php $haysucursal++; ?>
                        <h5>{{$sucursale->nombre}}</h5>
                        @foreach($terminales as $terminale)
                            <?php $cant=0; ?>
                            @if($sucursale->id==$terminale->sucursal_id)
                                <?php $hayterminal++; ?>
                                <h5>Código del datafono: {{$terminale->codigo}}</h5>
                                @if(sizeof($resultado)>0)
                                    <table id="datatable" class="table table-striped table-bordered" width="100%">
                                        <thead>
                                        <tr>
                                            <th>Fecha</th>
                                            <th>Total</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($resultado as $miresul)
                                            @if($miresul["establecimiento"]==$establecimiento->id && $miresul["sucursal"]==$sucursale->id && $miresul["terminal"]==$terminale->codigo)
                                                <?php $cant++;
                                                $venta_name = '$ '.number_format( $miresul["venta"], 2, ',', '.');?>
                                                <tr>
                                                    <td>{{$miresul["fecha"]}}</td>
                                                    <td>{{$venta_name}}</td>
                                                </tr>
                                            @endif
                                        @endforeach
                                        </tbody>
                                    </table>
                                    @if($cant==0)
                                        <p align="center">No hay registros</p>
                                    @endif
                                    @foreach ($resumen as $resum)
                                        @if($resum['terminal']== $terminale->codigo)
                                           <?php    $prom_name = '$ '.number_format( $resum["promedio"], 2, ',', '.');?>
                                            <table id="datatable" class="table table-striped table-bordered" width="100%">
                                                <tr><td>Estado actual: </td><td>{{$resum['estado']}}</td><td>Promedio diario: </td><td>{{$prom_name}}</td></tr>
                                            </table>
                                        @endif
                                    @endforeach
                                @endif
                            @endif
                        @endforeach
                        @if($hayterminal==0)
                            <p align="center">No existen datafonos</p>
                        @endif
                    @endif
                @endforeach
            @endif
            @if($haysucursal==0)
                <p align="center">No existen sucursales</p>
            @endif
            <br>
        @endforeach
    @endif


</div>
</body>
</html>