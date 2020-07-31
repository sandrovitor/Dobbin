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
                        <th style="vertical-align:middle; width: 1rem;">#</th>
                        <th>Nome</th>
                        <th style="width:150px;">Tipo de Quarto</th>
                        <!--<th style="vertical-align:middle"></th>-->
                    </tr>
                </thead>
                <tbody>
                <!-- 
                    Varre cada quarto e checa os ocupantes.
                    1) Verifica a configuração: se as crianças de colo é 
                -->
                @php
                    $contaPessoa = 0;
                    switch((int)$d->total) {
                        case 1: $quartoNome = 'INDIVIDUAL'; break;
                        case 2: $quartoNome = 'DUPLO'; break;
                        case 3: $quartoNome = 'TRIPLO'; break;
                        case 4: $quartoNome = 'QUÁDRUPLO'; break;
                        case 5: $quartoNome = 'QUÍNTUPLO'; break;
                        case 6: $quartoNome = 'SÉXTUPLO'; break;
                        default: $quartoNome = $d->total.' CAMAS'; break;
                        
                    }
                @endphp
                @for($t = 0; $t < (int)$d->total; $t++)
                    @if(!isset($d->pessoas[$contaPessoa]))
                        <tr>
                            <td class="small">{{$t+1}}</td>
                            <td></td>
                            {!!$t == 0 ? '<td rowspan="'.$d->total.'" style="vertical-align:middle" class="text-primary font-weight-bold text-center">'.$quartoNome.'</td>' : ''!!}
                        </tr>
                    @elseif($d->pessoas[$contaPessoa]->tipo == 'coord')
                        <tr>
                            <td class="small">{{$t+1}}</td>
                            <td>
                                <span class="text-danger font-weight-bold">
                                {{$coord[ array_search($d->pessoas[$contaPessoa]->id, array_column($coord, 'id')) ]['nome']}}
                                </span>
                            </td>
                            {!!$t == 0 ? '<td rowspan="'.$d->total.'" style="vertical-align:middle" class="text-primary font-weight-bold text-center">'.$quartoNome.'</td>' : ''!!}
                        </tr>
                    @elseif($d->pessoas[$contaPessoa]->tipo == 'cliente')
                        @php
                            // Verifica se o cliente é criança de colo.
                            // Verifica a configuração/instrução, se permite criança de colo individual.
                            $cID = array_search($d->pessoas[$contaPessoa]->id, array_column($clientes, 'id'));

                            /**
                            1) É criança de colo e a configuração NÃO permite indiv.: NÃO ESCREVE CRIANÇA/PULA LINHA.
                            2) É criança de colo e a configuração permite indiv.: ESCREVE CRIANÇA.
                            3) É adulto e NÃO tem proximo cliente: ESCREVE ADULTO.
                            4) É adulto e TEM próximo cliente:
                                4.1) Proximo cliente NÃO é criança de colo: ESCREVE ADULTO.
                                4.2) Proximo cliente é criança de colo: 
                                    4.2.1) Configuração permite indiv.: ESCREVE ADULTO.
                                    4.2.2) Configuração NÃO permite indiv.: ESCREVE ADULTO E CRIANÇA DE COLO.

                            */
                        @endphp
                        @if($clientes[$cID]['colo'] == true && $config->criancaColoIndividual == false) 
                            @php
                            // Não escreve nada.
                            // Decrementa o laço para não perder a vaga do quarto.
                            $t--;
                            @endphp

                        @elseif($clientes[$cID]['colo'] == true && $config->criancaColoIndividual == true)
                        <!-- DEC 2 -->
                        <tr>
                            <td class="small">{{$t+1}}</td>
                            <td>
                                {{$clientes[ $cID ]['nome']}}
                            </td>
                            {!!$t == 0 ? '<td rowspan="'.$d->total.'" style="vertical-align:middle" class="text-primary font-weight-bold text-center">'.$quartoNome.'</td>' : ''!!}
                        </tr>
                        @elseif($clientes[$cID]['colo'] == false && !isset( $clientes[($cID+1)] ))
                        <!-- DEC 3 -->
                        <tr>
                            <td class="small">{{$t+1}}</td>
                            <td>
                                {{$clientes[ $cID ]['nome']}}
                            </td>
                            {!!$t == 0 ? '<td rowspan="'.$d->total.'" style="vertical-align:middle" class="text-primary font-weight-bold text-center">'.$quartoNome.'</td>' : ''!!}
                        </tr>
                        @elseif($clientes[$cID]['colo'] == false && isset( $clientes[($cID+1)] ))
                        <!-- DEC 4 -->
                            @if($clientes[($cID+1)]['colo'] == false)
                            <!-- DEC 4.1 -->
                            <tr>
                                <td class="small">{{$t+1}}</td>
                                <td>
                                    {{$clientes[ $cID ]['nome']}}
                                </td>
                                {!!$t == 0 ? '<td rowspan="'.$d->total.'" style="vertical-align:middle" class="text-primary font-weight-bold text-center">'.$quartoNome.'</td>' : ''!!}
                            </tr>
                            @elseif($clientes[($cID+1)]['colo'] == true)
                            <!-- DEC 4.2 -->
                                @if($config->criancaColoIndividual == true)
                                <!-- DEC 4.2.1 -->
                                <tr>
                                    <td class="small">{{$t+1}}</td>
                                    <td>
                                        {{$clientes[ $cID ]['nome']}}
                                    </td>
                                    {!!$t == 0 ? '<td rowspan="'.$d->total.'" style="vertical-align:middle" class="text-primary font-weight-bold text-center">'.$quartoNome.'</td>' : ''!!}
                                </tr>

                                @else
                                <!-- DEC 4.2.2 -->
                                <tr>
                                    <td class="small">{{$t+1}}</td>
                                    <td>
                                        {{$clientes[ $cID ]['nome']}}<br>
                                        &nbsp; &nbsp; &nbsp; {{$clientes[($cID+1)]['nome']}} (COLO)
                                    </td>
                                    {!!$t == 0 ? '<td rowspan="'.$d->total.'" style="vertical-align:middle" class="text-primary font-weight-bold text-center">'.$quartoNome.'</td>' : ''!!}
                                </tr>
                                @endif
                            @endif
                        @endif
                    @endif

                    @php
                    // Incrementa contador da pessoa.
                    $contaPessoa++;
                    @endphp
                @endfor
                </tbody>
            </table>
            @endforeach
            @endif
        </div>
    </body>
</html>