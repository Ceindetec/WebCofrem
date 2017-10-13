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
            <li><label>Nombre: </label><strong>Tarjetas con saldo vencido</strong></li>
            <li><label>Fecha: </label><strong>{{\Carbon\Carbon::now()->format('d/m/Y')}}</strong></li>
            <li><label>Rango: </label><strong>{{$rango}}</strong></li>
        </ul>

    </div>
    <br>
    @if($tiposervicio!="R")
        <h5>Tarjetas Bono</h5>
        @if(sizeof($resultadob)>0)
            <div class="table">
                <table id="datatable" class="table table-striped table-bordered" width="100%">
                    <thead>
                    <tr>
                        <th>Número tarjeta</th>
                        <th>Monto inicial</th>
                        <th>Sobrante</th>
                        <th>Fecha activación</th>
                        <th>Fecha vencimiento</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($resultadob as $miresul)
                        <tr>
                            <td>{{$miresul["numero_tarjeta"]}}</td>
                            <td>{{$miresul["monto_inicial"]}}</td>
                            <td>{{$miresul["sobrante"]}}</td>
                            <td> {{$miresul["fecha_activacion"]}}</td>
                            <td>{{$miresul["fecha_vencimiento"]}}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif
        @if(sizeof($resultadob)==0)
            <p align="center">No hay registros</p>
        @endif
    @endif
    @if($tiposervicio!="B")
        <h5>Tarjetas Regalo</h5>
        @if(sizeof($resultador)>0)
            <div class="table">
                <table id="datatable" class="table table-striped table-bordered" width="100%">
                    <thead>
                    <tr>
                        <th>Numero tarjeta</th>
                        <th>Monto incial</th>
                        <th>Sobrante</th>
                        <th>Fecha activacion</th>
                        <th>Fecha vencimiento</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($resultador as $miresul)
                        <tr>
                            <td>{{$miresul["numero_tarjeta"]}}</td>
                            <td>{{$miresul["monto_inicial"]}}</td>
                            <td>{{$miresul["sobrante"]}}</td>
                            <td> {{$miresul["fecha_activacion"]}}</td>
                            <td>{{$miresul["fecha_vencimiento"]}}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif
        @if(sizeof($resultador)==0)
            <p align="center">No hay registros</p>
        @endif
    @endif
    <br>
</div>
</body>
</html>