<div class="row">
    <div class="col-12 col-lg-10 col-xl-8 mx-auto">
        <div class="card">
            <div class="card-header">
                Lixeira
            </div>
            <div class="card-body" style="overflow-x:auto">
                <div class="alert alert-info small px-2 py-1">
                    <i class="fas fa-info-circle"></i> Roteiro permanecerão na lixeira por 7 dias e depois serão excluídos automaticamente.
                </div>
                @if(empty($roteiros['roteiros']))
                    <h6 class="text-center my-3 font-italic">Não há nada aqui...</h6>
                @else
                    <table class="table table-hover table-bordered table-sm">
                        <thead class="bg-dark text-white">
                            <tr>
                                <th>Cód.</th>
                                <th>Nome</th>
                                <th>Apagado em</th>
                                <th>Apagado por</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($roteiros['roteiros'] as $c)
                        @php
                            $data_ini = new DateTime($c->data_ini);
                            $data_fim = new DateTime($c->data_fim);
                            $apagado_em = new DateTime($c->deletado_em);
                        @endphp
                            <tr>
                                <td>{{$c->id}}</td>
                                <td><a href="#roteiros/lixeira/ver/{{$c->id}}">{{$c->nome}} ({{$data_ini->format('d/m/Y')}} a {{$data_fim->format('d/m/Y')}})</a></td>
                                <td>{{$apagado_em->format('d/m/Y H:i:s')}}</td>
                                <td>{{$c->usuario}}</td>
                                
                                <td>
                                    <button type="button" class="btn btn-transparent btn-rounded btn-sm dropdown-toggle no-caret" data-toggle="dropdown"> <i class="fas fa-ellipsis-v fa-fw"></i> </button>
                                    <div class="dropdown-menu">
                                        <a href="#roteiros/lixeira/ver/{{$c->id}}" class="dropdown-item"><i class="far fa-eye fa-fw mr-1"></i> Ver</a>
                                        <button class="dropdown-item" onclick="roteiroRestaurar({{$c->id}})"><i class="fas fa-redo-alt fa-fw mr-1"></i> Restaurar</button>
                                        <div class="dropdown-divider"></div>
                                        <button class="dropdown-item text-danger" onclick="roteiroApagarLixeira({{$c->id}})"><i class="fas fa-trash fa-fw mr-1"></i> Apagar de vez</button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>
</div>