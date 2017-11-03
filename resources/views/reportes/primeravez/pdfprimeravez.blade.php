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
        @if(count($transacciones["regalo"]["detproducto"])>0)
            <h5>Regalo</h5>
            <table id="datatable" class="table table-striped table-bordered datatable"
                   width="100%">
                <thead>
                <tr>
                    <th>Numero tarjeta</th>
                    <th>Monto inicial</th>
                    <th>Factura</th>
                    <th>No. transaccion</th>
                    <th>Codigo terminal</th>
                    <th>Sucursal</th>
                    <th>Valor</th>
                    <th>Fecha</th>
                </tr>
                </thead>
                <tbody>
                @for($i=0; $i < count($transacciones["regalo"]["detproducto"]); $i++)
                    <tr>
                        <td>{{$transacciones["regalo"]["detproducto"][$i]->numero_tarjeta}}</td>
                        <td>{{$transacciones["regalo"]["detproducto"][$i]->monto_inicial}}</td>
                        <td>{{$transacciones["regalo"]["detproducto"][$i]->factura}}</td>
                        <td>{{$transacciones["regalo"]["dettransa"][$i]->numero_transaccion}}</td>
                        <td>{{$transacciones["regalo"]["dettransa"][$i]->codigo_terminal}}</td>
                        <td>{{$transacciones["regalo"]["dettransa"][$i]->nombre}}</td>
                        <td>{{$transacciones["regalo"]["dettransa"][$i]->valor}}</td>
                        <td>{{$transacciones["regalo"]["dettransa"][$i]->fecha}}</td>
                    </tr>
                @endfor
                </tbody>
            </table>
            <br>
            <br>
        @endif
        @if(count($transacciones["bono"]["detproducto"])>0)
            <h5>Bonos empresariales</h5>
            <table id="datatable" class="table table-striped table-bordered datatable"
                   width="100%">
                <thead>
                <tr>
                    <th>Numero tarjeta</th>
                    <th>Monto inicial</th>
                    <th>No. Contrato</th>
                    <th>No. transaccion</th>
                    <th>Codigo terminal</th>
                    <th>Sucursal</th>
                    <th>Valor</th>
                    <th>Fecha</th>
                </tr>
                </thead>
                <tbody>
                @for($i=0; $i < count($transacciones["bono"]["detproducto"]); $i++)
                    <tr>
                        <td>{{$transacciones["bono"]["detproducto"][$i]->numero_tarjeta}}</td>
                        <td>{{$transacciones["bono"]["detproducto"][$i]->monto_inicial}}</td>
                        <td>{{$transacciones["bono"]["detproducto"][$i]->getContrato->n_contrato}}</td>
                        <td>{{$transacciones["bono"]["dettransa"][$i]->numero_transaccion}}</td>
                        <td>{{$transacciones["bono"]["dettransa"][$i]->codigo_terminal}}</td>
                        <td>{{$transacciones["bono"]["dettransa"][$i]->nombre}}</td>
                        <td>{{$transacciones["bono"]["dettransa"][$i]->valor}}</td>
                        <td>{{$transacciones["bono"]["dettransa"][$i]->fecha}}</td>
                    </tr>
                @endfor
                </tbody>
            </table>
            <br>
            <br>
        @endif
    </div>
</div>
</body>
</html>



