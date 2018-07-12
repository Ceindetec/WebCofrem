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
            <li><label>Nombre: </label><strong>Ventas por establecimiento</strong></li>
            <li><label>Fecha: </label><strong>{{\Carbon\Carbon::now()->format('d/m/Y')}}</strong></li>
            <li><label>Rango: </label><strong>{{$rango}}</strong></li>
        </ul>
    </div>
    <br>
    @if(sizeof($establecimientos)>0)
        <?php $canttotal = 0;
        $ventatotal = 0; ?>
        <table id="datatable" class="table table-striped table-bordered" width="100%">
            <thead>
            <tr>
                <th>Establecimiento</th>
                <th>Nit</th>
                <th>Cantidad de transacciones</th>
                <th>Monto</th>
            </tr>
            </thead>
            <tbody>
            @foreach($establecimientos as $establecimiento)
                <?php $cant=0; ?>
                @if(sizeof($resultado)>0)
                    <?php $subtotal=0; ?>
                    @foreach($resultado as $miresul)
                        @if($miresul["establecimiento"]==$establecimiento->id)
                            <?php $cant++;
                            $venta_name = '$ '.number_format( $miresul["venta"], 2, ',', '.'); ?>
                            <tr>
                                <td>{{$establecimiento->razon_social}}</td>
                                <td>{{$establecimiento->nit}}</td>
                                <td>{{$miresul["cantidad"]}}</td>
                                <td>{{$venta_name}}</td>
                                <?php $canttotal += $miresul["cantidad"];
                                $ventatotal += $miresul["venta"]; ?>
                            </tr>
                        @endif
                    @endforeach
                @endif
                @if($cant==0)
                    <tr><td colspan="4" align="center">No hay registros</td></tr>
                @endif

            @endforeach
            </tbody>
        </table>
        <?php $ventatotal_name = '$ '.number_format( $ventatotal, 2, ',', '.'); ?>
        <h5>Resumen de los resultados</h5>
        <table id="datatable" class="table table-striped table-bordered" width="100%">
            <tr><th>Total establecimientos</th><th>Cantidad de transacciones</th><th>Monto</th></tr>
            <tr><td align="center">{{sizeof($establecimientos)}}</td><td align="center"> {{$canttotal}}</td><td align="center"> {{$ventatotal_name}}</td>
            </tr></table>
    @endif

</div>
</body>
</html>