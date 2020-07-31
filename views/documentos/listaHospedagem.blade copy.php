<html>
    @php
        $data_ini = new DateTime($roteiro->data_ini);
        $data_fim = new DateTime($roteiro->data_fim);
        $conta1 = 0; $conta2 = 0;

        if(file_exists(__DIR__.'/../public_html/css/bootstrap-4.3.1.min.css')) {
            $handle = fopen(__DIR__.'/../public_html/css/bootstrap-4.3.1.min.css', 'r');
            $css = fread($handle, filesize(__DIR__.'/../public_html/css/bootstrap-4.3.1.min.css'));
            fclose($handle);
        } else {
            $css = '';
        }

        if($lista->dados != '') {
            $dLista = json_decode($lista->dados);
        } else {
            $dLista = [];
        }

        $config = json_decode($lista->instrucoes);
        
    @endphp
    <head>
        <style>
            {!!$css!!}
            @page {
                margin: 110px 15px 55px; 
                font-size: 14px;
            }
            
            body {
                margin:0;
                counter-reset: contapagina;
                font-family: Verdana, Arial, sans-serif;
            }
            
            @import url('https://fonts.googleapis.com/css2?family=Metrophobic:wght@400;700&display=swap');
            h1, h2, h3, h4, h5, h6 {
                font-family: 'Metrophobic', sans-serif;
            }
            

            .table-bordered.border-dark td, .table-bordered.border-dark th {
                border-color: #343a40!important;
            }
            .header {
                position:fixed;
                top: -95px;
                left:0;
                right:0;
                width:100%;
            }

            .footer {
                position:fixed;
                bottom: -10px;
                left:0;
                right:0;
                width:100%;
            }
            .footer .paginacontador::after {
                content: counter(contapagina);
                counter-increment: contapagina;
            }
        </style>
        <!--<link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet" type="text/css">-->
        <title>{{$lista->nome}} - {{$roteiro->nome}} ({{$data_ini->format('d/m/Y')}} a {{$data_fim->format('d/m/Y')}})</title>
    </head>
    <body>
    
        <table class="table table-bordered border-dark table-sm mb-1 header">
            <tr>
                <td style="width: 150px"><img src="https://tonaestradaviagens.com.br/media/images/logo-wide64.png" height="40"></td>
                <td class="text-center" style="vertical-align:middle">
                    <h3 class="mb-1 py-0"><strong class="text-uppercase">{{$lista->nome}}</strong></h3>
                    <span class="text-uppercase">{{$roteiro->nome}} ({{$data_ini->format('d/m/Y')}} a {{$data_fim->format('d/m/Y')}})</span>
                    
                </td>
            </tr>
        </table>

        <footer class="footer">
            <table class="table table-sm table-bordered border-dark text-muted">
                <tr>
                    <td><span class="small text-uppercase">{{$roteiro->nome}} ({{$data_ini->format('d/m/Y')}} a {{$data_fim->format('d/m/Y')}})</span></td>
                    <td class="small text-center font-italic">Gerado em: {{date('d/m/Y H:i:s')}}</td>
                    <td class="text-right paginacontador" style="width:30px"></td>
                </tr>
            </table>
        </footer>
        
        <div class="page-content">
            @if(empty($dLista))
                <h4 class="text-center">Esta lista não possui dados para renderizar.</h4>
            @else

            @foreach($dLista->quartos as $d)
            <table class="table table-bordered border-dark table-sm mb-2">
                <thead>
                    <tr>
                        <th style="vertical-align:middle">#</th>
                        <th>Nome</th>
                        <th style="width:150px;">Tipo de Quarto</th>
                        <!--<th style="vertical-align:middle"></th>-->
                    </tr>
                </thead>
                <tbody>
                @switch($d->total)
                    @case('1')
                        <tr>
                            <td class="small" style="width:20px">1</td>
                            <td>
                            @if(isset($d->pessoas[0]))
                                @if($d->pessoas[0]->tipo == 'cliente')
                                {{ array_search($d->pessoas[0]->id, array_column($clientes, 'id')) === false ? '--' : $clientes[array_search($d->pessoas[0]->id, array_column($clientes, 'id'))]['nome'] }}
                                @elseif($d->pessoas[0]->tipo == 'coord')
                                <span class="font-weight-bold text-danger">
                                {{ array_search($d->pessoas[0]->id, array_column($coord, 'id')) === false ? '--' : $coord[array_search($d->pessoas[0]->id, array_column($coord, 'id'))]['nome'] }}
                                </span>
                                @endif
                            @endif
                            </td>
                            <td class="font-weight-bold text-primary text-center" style="vertical-align:middle;">INDIVIDUAL</td>
                        </tr>
                    @break
                    @case('2')
                        @php
                        // Verifica se
                        @endphp
                        <tr>
                            <td class="small" style="width:20px">1</td>
                            <td>
                            @if(isset($d->pessoas[0]))
                                @if($d->pessoas[0]->tipo == 'cliente')
                                @php
                                    $cID = array_search($d->pessoas[0]->id, array_column($clientes, 'id'));

                                    if($cID === false) {
                                        echo '---';
                                    } else {
                                        echo $clientes[ $cID ]['nome'];
                                    }
                                @endphp
                                {{ array_search($d->pessoas[0]->id, array_column($clientes, 'id')) === false ? '--' : $clientes[array_search($d->pessoas[0]->id, array_column($clientes, 'id'))]['nome'] }}
                                @elseif($d->pessoas[0]->tipo == 'coord')
                                <span class="font-weight-bold text-danger">
                                {{ array_search($d->pessoas[0]->id, array_column($coord, 'id')) === false ? '--' : $coord[array_search($d->pessoas[0]->id, array_column($coord, 'id'))]['nome'] }}
                                </span>
                                @endif
                            @endif
                            </td>
                            <td class="font-weight-bold text-primary text-center" style="vertical-align:middle;" rowspan="2">DUPLO</td>
                        </tr>
                        <tr>
                            <td class="small" style="width:20px">2</td>
                            <td>
                            @if(isset($d->pessoas[1]))
                                @if($d->pessoas[1]->tipo == 'cliente')
                                {{ array_search($d->pessoas[1]->id, array_column($clientes, 'id')) === false ? '--' : $clientes[array_search($d->pessoas[1]->id, array_column($clientes, 'id'))]['nome'] }}
                                @elseif($d->pessoas[1]->tipo == 'coord')
                                <span class="font-weight-bold text-danger">
                                {{ array_search($d->pessoas[1]->id, array_column($coord, 'id')) === false ? '--' : $coord[array_search($d->pessoas[1]->id, array_column($coord, 'id'))]['nome'] }}
                                </span>
                                @endif
                            @endif
                            </td>
                        </tr>
                    @break
                    @case('3')
                        <tr>
                            <td class="small" style="width:20px">1</td>
                            <td>
                            @if(isset($d->pessoas[0]))
                                @if($d->pessoas[0]->tipo == 'cliente')
                                {{ array_search($d->pessoas[0]->id, array_column($clientes, 'id')) === false ? '--' : $clientes[array_search($d->pessoas[0]->id, array_column($clientes, 'id'))]['nome'] }}
                                @elseif($d->pessoas[0]->tipo == 'coord')
                                <span class="font-weight-bold text-danger">
                                {{ array_search($d->pessoas[0]->id, array_column($coord, 'id')) === false ? '--' : $coord[array_search($d->pessoas[0]->id, array_column($coord, 'id'))]['nome'] }}
                                </span>
                                @endif
                            @endif
                            </td>
                            <td class="font-weight-bold text-primary text-center" style="vertical-align:middle;" rowspan="3">TRIPLO</td>
                        </tr>
                        <tr>
                            <td class="small" style="width:20px">2</td>
                            <td>
                            @if(isset($d->pessoas[1]))
                                @if($d->pessoas[1]->tipo == 'cliente')
                                {{ array_search($d->pessoas[1]->id, array_column($clientes, 'id')) === false ? '--' : $clientes[array_search($d->pessoas[1]->id, array_column($clientes, 'id'))]['nome'] }}
                                @elseif($d->pessoas[1]->tipo == 'coord')
                                <span class="font-weight-bold text-danger">
                                {{ array_search($d->pessoas[1]->id, array_column($coord, 'id')) === false ? '--' : $coord[array_search($d->pessoas[1]->id, array_column($coord, 'id'))]['nome'] }}
                                </span>
                                @endif
                            @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="small" style="width:20px">3</td>
                            <td>
                            @if(isset($d->pessoas[2]))
                                @if($d->pessoas[2]->tipo == 'cliente')
                                {{ array_search($d->pessoas[2]->id, array_column($clientes, 'id')) === false ? '--' : $clientes[array_search($d->pessoas[2]->id, array_column($clientes, 'id'))]['nome'] }}
                                @elseif($d->pessoas[2]->tipo == 'coord')
                                <span class="font-weight-bold text-danger">
                                {{ array_search($d->pessoas[2]->id, array_column($coord, 'id')) === false ? '--' : $coord[array_search($d->pessoas[2]->id, array_column($coord, 'id'))]['nome'] }}
                                </span>
                                @endif
                            @endif
                            </td>
                        </tr>
                    @break
                    @case('4')
                        <tr>
                            <td class="small" style="width:20px">1</td>
                            <td>
                            @if(isset($d->pessoas[0]))
                                @if($d->pessoas[0]->tipo == 'cliente')
                                {{ array_search($d->pessoas[0]->id, array_column($clientes, 'id')) === false ? '--' : $clientes[array_search($d->pessoas[0]->id, array_column($clientes, 'id'))]['nome'] }}
                                @elseif($d->pessoas[0]->tipo == 'coord')
                                <span class="font-weight-bold text-danger">
                                {{ array_search($d->pessoas[0]->id, array_column($coord, 'id')) === false ? '--' : $coord[array_search($d->pessoas[0]->id, array_column($coord, 'id'))]['nome'] }}
                                </span>
                                @endif
                            @endif
                            </td>
                            <td class="font-weight-bold text-primary text-center" style="vertical-align:middle;" rowspan="4">QUÁDRUPLO</td>
                        </tr>
                        <tr>
                            <td class="small" style="width:20px">2</td>
                            <td>
                            @if(isset($d->pessoas[1]))
                                @if($d->pessoas[1]->tipo == 'cliente')
                                {{ array_search($d->pessoas[1]->id, array_column($clientes, 'id')) === false ? '--' : $clientes[array_search($d->pessoas[1]->id, array_column($clientes, 'id'))]['nome'] }}
                                @elseif($d->pessoas[1]->tipo == 'coord')
                                <span class="font-weight-bold text-danger">
                                {{ array_search($d->pessoas[1]->id, array_column($coord, 'id')) === false ? '--' : $coord[array_search($d->pessoas[1]->id, array_column($coord, 'id'))]['nome'] }}
                                </span>
                                @endif
                            @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="small" style="width:20px">3</td>
                            <td>
                            @if(isset($d->pessoas[2]))
                                @if($d->pessoas[2]->tipo == 'cliente')
                                {{ array_search($d->pessoas[2]->id, array_column($clientes, 'id')) === false ? '--' : $clientes[array_search($d->pessoas[2]->id, array_column($clientes, 'id'))]['nome'] }}
                                @elseif($d->pessoas[2]->tipo == 'coord')
                                <span class="font-weight-bold text-danger">
                                {{ array_search($d->pessoas[2]->id, array_column($coord, 'id')) === false ? '--' : $coord[array_search($d->pessoas[2]->id, array_column($coord, 'id'))]['nome'] }}
                                </span>
                                @endif
                            @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="small" style="width:20px">4</td>
                            <td>
                            @if(isset($d->pessoas[3]))
                                @if($d->pessoas[3]->tipo == 'cliente')
                                {{ array_search($d->pessoas[3]->id, array_column($clientes, 'id')) === false ? '--' : $clientes[array_search($d->pessoas[3]->id, array_column($clientes, 'id'))]['nome'] }}
                                @elseif($d->pessoas[3]->tipo == 'coord')
                                <span class="font-weight-bold text-danger">
                                {{ array_search($d->pessoas[3]->id, array_column($coord, 'id')) === false ? '--' : $coord[array_search($d->pessoas[3]->id, array_column($coord, 'id'))]['nome'] }}
                                </span>
                                @endif
                            @endif
                            </td>
                        </tr>
                    @break
                    @case('5')
                        <tr>
                            <td class="small" style="width:20px">1</td>
                            <td>
                            @if(isset($d->pessoas[0]))
                                @if($d->pessoas[0]->tipo == 'cliente')
                                {{ array_search($d->pessoas[0]->id, array_column($clientes, 'id')) === false ? '--' : $clientes[array_search($d->pessoas[0]->id, array_column($clientes, 'id'))]['nome'] }}
                                @elseif($d->pessoas[0]->tipo == 'coord')
                                <span class="font-weight-bold text-danger">
                                {{ array_search($d->pessoas[0]->id, array_column($coord, 'id')) === false ? '--' : $coord[array_search($d->pessoas[0]->id, array_column($coord, 'id'))]['nome'] }}
                                </span>
                                @endif
                            @endif
                            </td>
                            <td class="font-weight-bold text-primary text-center" style="vertical-align:middle;" rowspan="5">QUÍNTUPLO</td>
                        </tr>
                        <tr>
                            <td class="small" style="width:20px">2</td>
                            <td>
                            @if(isset($d->pessoas[1]))
                                @if($d->pessoas[1]->tipo == 'cliente')
                                {{ array_search($d->pessoas[1]->id, array_column($clientes, 'id')) === false ? '--' : $clientes[array_search($d->pessoas[1]->id, array_column($clientes, 'id'))]['nome'] }}
                                @elseif($d->pessoas[1]->tipo == 'coord')
                                <span class="font-weight-bold text-danger">
                                {{ array_search($d->pessoas[1]->id, array_column($coord, 'id')) === false ? '--' : $coord[array_search($d->pessoas[1]->id, array_column($coord, 'id'))]['nome'] }}
                                </span>
                                @endif
                            @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="small" style="width:20px">3</td>
                            <td>
                            @if(isset($d->pessoas[2]))
                                @if($d->pessoas[2]->tipo == 'cliente')
                                {{ array_search($d->pessoas[2]->id, array_column($clientes, 'id')) === false ? '--' : $clientes[array_search($d->pessoas[2]->id, array_column($clientes, 'id'))]['nome'] }}
                                @elseif($d->pessoas[2]->tipo == 'coord')
                                <span class="font-weight-bold text-danger">
                                {{ array_search($d->pessoas[2]->id, array_column($coord, 'id')) === false ? '--' : $coord[array_search($d->pessoas[2]->id, array_column($coord, 'id'))]['nome'] }}
                                </span>
                                @endif
                            @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="small" style="width:20px">4</td>
                            <td>
                            @if(isset($d->pessoas[3]))
                                @if($d->pessoas[3]->tipo == 'cliente')
                                {{ array_search($d->pessoas[3]->id, array_column($clientes, 'id')) === false ? '--' : $clientes[array_search($d->pessoas[3]->id, array_column($clientes, 'id'))]['nome'] }}
                                @elseif($d->pessoas[3]->tipo == 'coord')
                                <span class="font-weight-bold text-danger">
                                {{ array_search($d->pessoas[3]->id, array_column($coord, 'id')) === false ? '--' : $coord[array_search($d->pessoas[3]->id, array_column($coord, 'id'))]['nome'] }}
                                </span>
                                @endif
                            @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="small" style="width:20px">5</td>
                            <td>
                            @if(isset($d->pessoas[4]))
                                @if($d->pessoas[4]->tipo == 'cliente')
                                {{ array_search($d->pessoas[4]->id, array_column($clientes, 'id')) === false ? '--' : $clientes[array_search($d->pessoas[4]->id, array_column($clientes, 'id'))]['nome'] }}
                                @elseif($d->pessoas[4]->tipo == 'coord')
                                <span class="font-weight-bold text-danger">
                                {{ array_search($d->pessoas[4]->id, array_column($coord, 'id')) === false ? '--' : $coord[array_search($d->pessoas[4]->id, array_column($coord, 'id'))]['nome'] }}
                                </span>
                                @endif
                            @endif
                            </td>
                        </tr>
                    @break
                    @case('6')
                        <tr>
                            <td class="small" style="width:20px">1</td>
                            <td>
                            @if(isset($d->pessoas[0]))
                                @if($d->pessoas[0]->tipo == 'cliente')
                                {{ array_search($d->pessoas[0]->id, array_column($clientes, 'id')) === false ? '--' : $clientes[array_search($d->pessoas[0]->id, array_column($clientes, 'id'))]['nome'] }}
                                @elseif($d->pessoas[0]->tipo == 'coord')
                                <span class="font-weight-bold text-danger">
                                {{ array_search($d->pessoas[0]->id, array_column($coord, 'id')) === false ? '--' : $coord[array_search($d->pessoas[0]->id, array_column($coord, 'id'))]['nome'] }}
                                </span>
                                @endif
                            @endif
                            </td>
                            <td class="font-weight-bold text-primary text-center" style="vertical-align:middle;" rowspan="5">QUÍNTUPLO</td>
                        </tr>
                        <tr>
                            <td class="small" style="width:20px">2</td>
                            <td>
                            @if(isset($d->pessoas[1]))
                                @if($d->pessoas[1]->tipo == 'cliente')
                                {{ array_search($d->pessoas[1]->id, array_column($clientes, 'id')) === false ? '--' : $clientes[array_search($d->pessoas[1]->id, array_column($clientes, 'id'))]['nome'] }}
                                @elseif($d->pessoas[1]->tipo == 'coord')
                                <span class="font-weight-bold text-danger">
                                {{ array_search($d->pessoas[1]->id, array_column($coord, 'id')) === false ? '--' : $coord[array_search($d->pessoas[1]->id, array_column($coord, 'id'))]['nome'] }}
                                </span>
                                @endif
                            @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="small" style="width:20px">3</td>
                            <td>
                            @if(isset($d->pessoas[2]))
                                @if($d->pessoas[2]->tipo == 'cliente')
                                {{ array_search($d->pessoas[2]->id, array_column($clientes, 'id')) === false ? '--' : $clientes[array_search($d->pessoas[2]->id, array_column($clientes, 'id'))]['nome'] }}
                                @elseif($d->pessoas[2]->tipo == 'coord')
                                <span class="font-weight-bold text-danger">
                                {{ array_search($d->pessoas[2]->id, array_column($coord, 'id')) === false ? '--' : $coord[array_search($d->pessoas[2]->id, array_column($coord, 'id'))]['nome'] }}
                                </span>
                                @endif
                            @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="small" style="width:20px">4</td>
                            <td>
                            @if(isset($d->pessoas[3]))
                                @if($d->pessoas[3]->tipo == 'cliente')
                                {{ array_search($d->pessoas[3]->id, array_column($clientes, 'id')) === false ? '--' : $clientes[array_search($d->pessoas[3]->id, array_column($clientes, 'id'))]['nome'] }}
                                @elseif($d->pessoas[3]->tipo == 'coord')
                                <span class="font-weight-bold text-danger">
                                {{ array_search($d->pessoas[3]->id, array_column($coord, 'id')) === false ? '--' : $coord[array_search($d->pessoas[3]->id, array_column($coord, 'id'))]['nome'] }}
                                </span>
                                @endif
                            @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="small" style="width:20px">5</td>
                            <td>
                            @if(isset($d->pessoas[4]))
                                @if($d->pessoas[4]->tipo == 'cliente')
                                {{ array_search($d->pessoas[4]->id, array_column($clientes, 'id')) === false ? '--' : $clientes[array_search($d->pessoas[4]->id, array_column($clientes, 'id'))]['nome'] }}
                                @elseif($d->pessoas[4]->tipo == 'coord')
                                <span class="font-weight-bold text-danger">
                                {{ array_search($d->pessoas[4]->id, array_column($coord, 'id')) === false ? '--' : $coord[array_search($d->pessoas[4]->id, array_column($coord, 'id'))]['nome'] }}
                                </span>
                                @endif
                            @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="small" style="width:20px">6</td>
                            <td>
                            @if(isset($d->pessoas[5]))
                                @if($d->pessoas[5]->tipo == 'cliente')
                                {{ array_search($d->pessoas[5]->id, array_column($clientes, 'id')) === false ? '--' : $clientes[array_search($d->pessoas[5]->id, array_column($clientes, 'id'))]['nome'] }}
                                @elseif($d->pessoas[5]->tipo == 'coord')
                                <span class="font-weight-bold text-danger">
                                {{ array_search($d->pessoas[5]->id, array_column($coord, 'id')) === false ? '--' : $coord[array_search($d->pessoas[5]->id, array_column($coord, 'id'))]['nome'] }}
                                </span>
                                @endif
                            @endif
                            </td>
                        </tr>
                    @break
                @endswitch
                </tbody>
            </table>
            @endforeach
            @endif
        </div>
    </body>
</html>