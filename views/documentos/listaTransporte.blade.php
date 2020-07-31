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

            @foreach($dLista as $key => $d)
            <table class="table table-bordered border-dark table-sm mb-2">
                <thead>
                    <tr class="bg-dark text-white">
                        <th class="text-center" colspan="2"> VIAGEM #{{$key+1}}</th>
                    </tr>
                    <tr>
                        <th style="vertical-align:middle; width: 40px;">#</th>
                        <th>Nome</th>
                        <!--<th style="vertical-align:middle"></th>-->
                    </tr>
                </thead>
                <tbody>
                @php
                $contaPessoa = 1;
                @endphp

                @foreach($d as $p)
                @php
                    // ID do cliente
                    $cID = array_search($p->id, array_column($clientes, 'id'));

                    // Se o cliente for uma criança de colo e a configuração é que as crianças de colo
                    // não são individuais, pula a linha.
                    if( 
                        $p->tipo == 'cliente' && $clientes[ array_search($p->id, array_column($clientes, 'id')) ]['colo'] == true
                        &&
                        (!isset($config->criancaColoIndividual) || $config->criancaColoIndividual == false)) {
                        continue;
                    }
                @endphp
                    <tr>
                        <td class="small">{{$contaPessoa}}</td>
                        <td>

                        @if(isset($config->criancaColoIndividual) && $config->criancaColoIndividual == true)

                            @if($p->tipo == 'coord')
                                @if(array_search($p->id, array_column($coord, 'id')) === false)
                                --
                                @else
                                <span class="text-danger font-weight-bold">{{$coord[array_search($p->id, array_column($coord, 'id'))]['nome']}}</span>
                                @endif
                            @elseif($p->tipo == 'cliente')
                                @if(array_search($p->id, array_column($clientes, 'id')) === false)
                                --
                                @else
                                @php
                                    
                                @endphp

                                {{$clientes[$cID]['nome']}}
                                @endif
                            @endif

                        @else

                            @if($p->tipo == 'coord')
                                @if(array_search($p->id, array_column($coord, 'id')) === false)
                                --
                                @else
                                <span class="text-danger font-weight-bold">{{$coord[array_search($p->id, array_column($coord, 'id'))]['nome']}}</span>
                                @endif
                            @elseif($p->tipo == 'cliente')
                                @if(array_search($p->id, array_column($clientes, 'id')) === false)
                                --
                                @else
                                @php
                                    $cID = array_search($p->id, array_column($clientes, 'id'));
                                    $proxCliente = $clientes[$cID+1];
                                @endphp
                                {{$clientes[$cID]['nome']}}
                                {!!$proxCliente['colo'] == true ? '<br> &nbsp; &nbsp; &nbsp; '.$proxCliente['nome'].' (COLO)' : ''!!}
                                @endif
                            @endif

                        @endif
                        </td>
                    </tr>
                @php
                $contaPessoa++;
                @endphp
                @endforeach
                </tbody>
            </table>
            @endforeach
            @endif
        </div>
    </body>
</html>