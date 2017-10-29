<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>

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
        .izquierda, .derecha{
            display: inline-block;
            width: 50%;
        }
        .izquierda{
            text-align: left;
        }
        .derecha{
            text-align: right;
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
            <li><label>Nombre: </label><strong>Tarjetas activadas discriminadas por monto</strong></li>
            <li><label>Fecha: </label><strong>{{\Carbon\Carbon::now()->format('d/m/Y')}}</strong></li>
            <li><label>Rango: </label><strong>{{$rango}}</strong></li>
        </ul>

    </div>
    <br>

    <div class="table-responsive m-b-20">
        @if(isset($regalo) && count($regalo)>0)
            <h2>Regalo</h2>
            @foreach($regalo['detalles'] as $detalles)
                @foreach($detalles as $montos)
                    <div class="izquierda">
                        <b>Fecha: </b><span>{{\Carbon\Carbon::createFromFormat('Y-m-d H:i:s',$montos[0]->fecha_activacion)->toDateString()}}</span></div>
                    <div class="derecha"><b>Monto
                            Inicial: </b><span>{{$montos[0]->monto_inicial}}</span></div>

                    <table id="datatable" class="table table-striped table-bordered datatable"
                           width="100%">
                        <thead>
                        <tr>
                            <th>Numero tarjeta</th>
                            <th>Monto inicial</th>
                            <th>factura</th>
                            <th>Usuario</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($montos as $monto)
                            <tr>
                                <td>{{$monto->numero_tarjeta}}</td>
                                <td>{{$monto->monto_inicial}}</td>
                                <td>{{$monto->factura}}</td>
                                <td>{{$monto->getUser->name}}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <br>
                    <br>
                @endforeach
            @endforeach
        @endif
        @if(isset($bono) && count($bono)>0)
            <h5>Bonos empresariales</h5>
            @foreach($bono['detalles'] as $detalles)
                @foreach($detalles as $montos)
                    <div class="izquierda">
                        <b>Fecha: </b><span>{{\Carbon\Carbon::createFromFormat('Y-m-d H:i:s',$montos[0]->fecha_activacion)->toDateString()}}</span></div>
                    <div class="derecha"><b>Monto
                            Inicial: </b><span>{{$montos[0]->monto_inicial}}</span></div>
                    <br>
                    <br>
                    <table id="datatable" class="table table-striped table-bordered datatable"
                           width="100%">
                        <thead>
                        <tr>
                            <th>Numero tarjeta</th>
                            <th>Monto inicial</th>
                            <th>Factura</th>
                            <th>Usuario</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($montos as $monto)
                            <tr>
                                <td>{{$monto->numero_tarjeta}}</td>
                                <td>{{$monto->monto_inicial}}</td>
                                <td>{{$monto->getContrato->n_contrato}}</td>
                                <td>{{$monto->getUser->name}}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <br>
                    <br>
                @endforeach
            @endforeach
        @endif
    </div>
</div>
</body>
</html>



