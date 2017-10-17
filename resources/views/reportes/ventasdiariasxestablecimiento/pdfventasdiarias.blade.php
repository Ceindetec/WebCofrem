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
            <li><label>Nombre: </label><strong>Ventas diarias</strong></li>
            <li><label>Fecha: </label><strong>{{\Carbon\Carbon::now()->format('d/m/Y')}}</strong></li>
            <li><label>Rango: </label><strong>{{$rango}}</strong></li>
        </ul>

    </div>
    <br>
    @if(sizeof($establecimientos)>0)
        @foreach($establecimientos as $establecimiento)
            <h5>Establecimiento: {{$establecimiento->razon_social}}</h5>
            <?php $haysucursal=0; ?>
            @foreach($sucursales as $sucursale)
                <?php $cant=0; ?>
                @if($sucursale->establecimiento_id==$establecimiento->id)
                    <?php $haysucursal++; ?>
                    <h5>{{$sucursale->nombre}}</h5>
                    @if(sizeof($resultado)>0)
                            <div class="table">
                                <table id="datatable" class="table table-striped table-bordered" width="100%">
                                    <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Venta</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                            <?php $subtotal=0; ?>
                            @foreach($resultado as $miresul)
                                @if($miresul["establecimiento"]==$establecimiento->id && $miresul["sucursal"]==$sucursale->id)
                                    <?php $cant++; ?>
                                    <tr>
                                        <td>{{$miresul["fecha"]}}</td>
                                        <td>{{$miresul["venta"]}}</td>
                                        <?php $subtotal+=$miresul["venta"]; ?>
                                    </tr>
                                @endif
                            @endforeach
                            <tr><td align="center"><b>Total: </b></td><td align="center"> {{$subtotal}}</td></tr>
                            </tbody>
                        </table>
                    @endif
                    @if($cant==0)
                        <p align="center">No hay registros</p>
                    @endif
                @endif
            @endforeach
            @if($haysucursal==0)
                <p align="center">No existen sucursales</p>
            @endif
                            </div>
            <br>
        @endforeach
    @endif

</div>
</body>
</html>