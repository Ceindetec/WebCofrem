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
    </style>

</head>
<body>
<div class="row">

    <div id="logo">
        <img src="{{url('images/logo_mini.png')}}" width="150px">
    </div>
    <div id="info">
        <ul>
            <li><label>Nombre: </label><strong>Tarjetas usadas por primera vez</strong></li>
            <li><label>Fecha: </label><strong>{{\Carbon\Carbon::now()->format('d/m/Y')}}</strong></li>
            <li><label>Rango: </label><strong>{{$rango}}</strong></li>
        </ul>

    </div>
    <br>
    <div class="table">
        <table id="datatable" class="table table-striped table-bordered" width="100%">
            <thead>
            <tr>
                <th>Numero tarjeta</th>
                <th>Transacción</th>
                <th>Valor</th>
                <th>Sucursal</th>
                <th>Terminal</th>
                <th>Fecha</th>
            </tr>
            </thead>
            <tbody>
            @foreach($transacciones as $transacione)
                <tr>
                    <td>{{$transacione->numero_tarjeta}}</td>
                    <td>{{$transacione->numero_transaccion}}</td>
                    <td>{{$transacione->valorTransacion[0]->total}}</td>
                    <td>{{$transacione->getSucursal->nombre}}</td>
                    <td>{{$transacione->codigo_terminal}}</td>
                    <td>{{$transacione->fecha}}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
</body>
</html>



