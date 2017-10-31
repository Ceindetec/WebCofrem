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
            <li><label>Nombre: </label><strong>Relación de Datafonos por establecimiento</strong></li>
            <li><label>Fecha: </label><strong>{{\Carbon\Carbon::now()->format('d/m/Y')}}</strong></li>
        </ul>

    </div>
    <br>
    @if(sizeof($establecimientos)>0)
        @foreach($establecimientos as $establecimiento)

            <h5>Establecimiento: {{$establecimiento->razon_social}}</h5>
            @foreach ($resumen as $resum)
                @if($resum['establecimiento']== $establecimiento->id)
                    <table id="datatable" class="table table-striped table-bordered" width="100%">
                        <tr><td>Terminales Activas: </td><td>{{$resum['tactivas']}}</td><td> Terminales Inactivas: </td><td>{{$resum['tinactivas']}}</td></tr>
                    </table>
                @endif
            @endforeach
            <?php $haysucursal=0; ?>
            @foreach($sucursales as $sucursale)
                <?php $cant=0; ?>
                @if($sucursale->establecimiento_id==$establecimiento->id)
                    <?php $haysucursal++; ?>
                    <h5>{{$sucursale->nombre}}</h5>
                    @if(sizeof($resultado)>0)
                        <table id="datatable" class="table table-striped table-bordered" width="100%">
                            <thead>
                            <tr>
                                <th>Código</th>
                                <th>Activo</th>
                                <th>Estado</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($resultado as $miresul)
                                @if($miresul["establecimiento"]==$establecimiento->id && $miresul["sucursal"]==$sucursale->id)
                                    <?php $cant++; ?>
                                    <tr>
                                        <td>{{$miresul["codigo"]}}</td>
                                        <td>{{$miresul["numero_activo"]}}</td>
                                        <td>{{$miresul["estado"]}}</td>
                                    </tr>
                                @endif
                            @endforeach

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
            <br>
</div>
@endforeach
@endif

</div>
</body>
</html>