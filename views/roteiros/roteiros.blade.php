<div class="row mb-3">
    <div class="col-6 col-lg-3 mb-3">
        <a href="#roteiros/novo" class="btn btn-block btn-light rounded-0 shadow-sm font-weight-bold py-2">Novo</a>
    </div>
    <div class="col-6 col-lg-3 mb-3">
        <a href="#roteiros/database" class="btn btn-block btn-light rounded-0 shadow-sm font-weight-bold py-2">Base de Dados</a>
    </div>
    <div class="col-12 col-lg-3 mb-3">
        <a href="#roteiros/simulacao" class="btn btn-block btn-light rounded-0 shadow-sm font-weight-bold py-2">Simulação</a>
    </div>
    <div class="col-12 col-lg-3 mb-3">
        <a href="#roteiros/lixeira" class="btn btn-block btn-danger rounded-0 shadow-sm font-weight-bold py-2">Lixeira</a>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                Últimos Roteiros cadastrados
            </div>
            <div class="card-body">
                @if($listaRoteiros['success'] == true)
                <table class="table table-bordered table-sm">
                    <thead class="bg-dark text-white">
                        <tr>
                            <th>Cód.</th>
                            <th>Nome (Partida e Retorno)</th>
                            <th>Total Passagens</th>
                            <th>Criado em</th>
                        </tr>
                    </thead>
                    <tbody style="font-size: .9rem;">
                    @if(empty($listaRoteiros['roteiros']))
                        <tr><td class="text-center" colspan="7">Nada encontrado</td></tr>
                    @else
                        @foreach($listaRoteiros['roteiros'] as $r)
                        @php
                            $criado = new DateTime($r->criado_em);
                            $partida = new DateTime($r->data_ini);
                            $retorno = new DateTime($r->data_fim);
                        @endphp
                            <tr>
                                <td>{{$r->id}}</td>
                                <td><a href="#roteiros/ver/{{$r->id}}">{{$r->nome}} ({{$partida->format('d/m/Y')}} a {{$retorno->format('d/m/Y')}})</a></td>
                                <td>{{$r->passagens + $r->qtd_coordenador}}</td>
                                <td>{{$criado->format('d/m/Y H:i:s')}}</td>
                            </tr>
                        @endforeach
                    @endif
                    </tbody>
                </table>
                @else
                <div class="alert alert-warning">Houve um erro interno. Consulte desenvolvedor. Mensagem: <i>{{$listaRoteiros['mensagem']}}</i></div>
                @endif
                
            </div>
        </div>
    </div>
</div>