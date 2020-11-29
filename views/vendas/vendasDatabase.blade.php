<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-12 col-xl-6">
                        <div class="card">
                            <div class="card-header bg-info text-white">
                                RESERVAS
                            </div>
                            <div class="card-body px-2" id="reservasDiv">
                                <div class="text-center mx-2">
                                    Carregando...<br>
                                    <div class="spinner-grow text-primary"></div><br>
                                    <small class="text-muted">[Se essa seção demorar a carregar, ela pode estar indisponível ou em desenvolvimento.]</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-xl-6">
                        <div class="card">
                            <div class="card-header bg-primary text-light">
                                Aguardando Pagamento
                            </div>
                            <div class="card-body px-2" id="aguardPagDiv">
                                <div class="text-center mx-2">
                                    Carregando...<br>
                                    <div class="spinner-grow text-primary"></div><br>
                                    <small class="text-muted">[Se essa seção demorar a carregar, ela pode estar indisponível ou em desenvolvimento.]</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                BASE DE DADOS - Vendas
            </div>
            <div class="card-body database" style="overflow-x:auto;">
                @php
                $hoje = new DateTime();

                @endphp
                <div class="mb-3">
                    <div class="alert alert-info small py-1 px-2 mb-2">
                        <strong>OBS:</strong> As últimas 1000 vendas serão exibidas (50 por página). Para ver vendas mais antigas, consulte informações de um <a href="#clientes">cliente</a> ou de um <a href="#roteiros">roteiro</a>.<br>
                    </div>
                    <small>Atualizado em: <i>{{$hoje->format('d/m/Y H:i:s')}}</i>.</small>
                    <a href="#vendas/database" class="btn btn-sm btn-info"><i class="fas fa-sync"></i></a>
                </div>
                <table class="table table-sm table-bordered table-hover table-responsive-sm">
                    <thead class="bg-secondary text-white">
                        <tr>
                            <th>Cód</th>
                            <th>(Cód) Roteiro</th>
                            <th>(Cód) Cliente</th>
                            <th>Status</th>
                            <th>Data</th>
                            <th>Valor Total</th>
                        </tr>
                    </thead>
                    
                    @if(empty($vendas))
                    <tbody>
                        <tr>
                            <td colspan="6" class="text-center font-italic">Nenhuma venda ainda...</td>
                        </tr>
                    </tbody>
                    @else
                    
                    @php
                        $pagina = 1;
                        $itensPagina = 50;
                    @endphp
                    <tbody data-page="{{$pagina}}" class="show">
                    @foreach($vendas as $v)
                        @if($itensPagina <= 0)
                    </tbody>
                    <tbody data-page="{{$pagina+1}}">
                        @php
                        $pagina++;
                        $itensPagina = 50;
                        @endphp
                        @endif


                        @php

                        $itensPagina--;
                        $data_ini = new DateTime($v->roteiro_data_ini);
                        $data_fim = new DateTime($v->roteiro_data_fim);
                        $data_reserva = new DateTime($v->data_reserva);
                        @endphp
                        <tr class="small cursor-pointer" onclick="getVenda({{$v->id}})">
                            <td>{{$v->id}}</td>
                            <td>( {{$v->roteiro_id}} ) {{$v->roteiro_nome}} ({{$data_ini->format('d/m/Y')}} a {{$data_fim->format('d/m/Y')}})</td>
                            <td>( {{$v->cliente_id}} ) {{$v->cliente_nome}}</td>
                            <td>{!!$v->status_html!!}</td>
                            <td>{{$data_reserva->format('d/m/Y H:i:s')}}</td>
                            <td>R$ {{$sgc->converteCentavoParaReal($v->valor_total)}}</td>
                        </tr>
                    @endforeach
                    </tbody>
                    @endif
                </table>
            
                @if(!empty($vendas))
                <div>
                    <ul class="pagination pagination-sm justify-content-end">
                        <li class="page-item disabled"><a class="page-link" data-go-prev href="javascript:void(0)">Anterior</a></li>
                        @for($i = 1; $i <= $pagina; $i++)
                        <li class="page-item {{$i == 1 ? 'active' : ''}}"><a class="page-link" data-goto="{{$i}}" href="javascript:void(0)">{{$i}}</a></li>
                        @endfor
                        <li class="page-item {{$pagina == 1 ? 'disabled' : ''}}"><a class="page-link" data-go-next href="javascript:void(0)">Próximo</a></li>
                    </ul>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>

$(document).ready(function(){
    // Atraso de 1 segundo no carregamento de outras seções
    
    
});
</script>