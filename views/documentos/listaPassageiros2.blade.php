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
        <title>LISTA - {{$roteiro->nome}} ({{$data_ini->format('d/m/Y')}} a {{$data_fim->format('d/m/Y')}})</title>
    </head>
    <body>
    
        <table class="table table-bordered border-dark table-sm mb-1 header">
            <tr>
                <td style="width: 150px"><img src="https://tonaestradaviagens.com.br/media/images/logo-wide64.png" height="40"></td>
                <td class="text-center" style="vertical-align:middle">
                    <h3 class="mb-1 py-0"><strong>LISTA DE PASSAGEIROS</strong></h3>
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
            <table class="table table-bordered border-dark table-sm mb-1">
                <thead>
                    <tr>
                        <th colspan="6" class="text-center">CLIENTES</th>
                    </tr>
                    <tr>
                        <th style="vertical-align:middle">#</th>
                        <th style="vertical-align:middle">Cód.</th>
                        <th style="vertical-align:middle">Nome</th>
                        <th style="vertical-align:middle">Faixa Etária</th>
                        <th style="vertical-align:middle">RG</th>
                        <th style="vertical-align:middle">CPF</th>
                        <!--<th style="vertical-align:middle"></th>-->
                    </tr>
                </thead>
                <tbody>
                @foreach($clientes as $c)
                    @php
                    if($c['colo'] == false) { $conta1++; }
                    @endphp
                    <tr>
                        <td class="font-weight-bold">{{$c['colo'] ? '': $conta1}}</td>
                        <td class="small">{{$c['id']}}</td>
                        <td>{{$c['nome']}} {!!$c['colo'] ? '<span class="text-danger small">( COLO )</i>' : ''!!}</td>
                        <td>{{$c['faixa_etaria']}} ({{$c['idade']}} anos)</td>
                        <td class="small">{{$c['rg']}}</td>
                        <td class="small">{{$c['cpf']}}</td>
                        <!--<td class="small">{!!$c['colo'] ? '<i class="badge badge-danger py-1 px-2">COLO</i>' : '-'!!}</td>-->
                    </tr>
                    
                @endforeach
                </tbody>
            </table>
            <br>
            <table class="table table-bordered border-dark table-sm mb-1">
                <thead>
                    <tr>
                        <th colspan="4" class="text-center">COORDENADORES</th>
                    </tr>
                    <tr>
                        <th style="vertical-align:middle">#</th>
                        <th style="vertical-align:middle">Nome</th>
                        <th style="vertical-align:middle">RG</th>
                        <th style="vertical-align:middle">CPF</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($coord as $c)
                    @php
                    $conta2++
                    @endphp
                    <tr>
                        <td>{{$conta2}}</td>
                        <td>{{$c['nome']}}</td>
                        <td>{{$c['rg']}}</td>
                        <td>{{$c['cpf']}}</td>
                    </tr>
                    
                @endforeach
                </tbody>
            </table>
        </div>
    </body>
</html>