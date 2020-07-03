<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                BASE DE DADOS - Vendas Canceladas
            </div>
            <div class="card-body">
                <div class="alert alert-info small mb-2 py-1 px-2">
                    <strong>Observação:</strong> Somente vendas canceladas <strong>(não inclui devolvidas/estornadas)</strong> são exibidas aqui. Se uma venda ainda não aparece, experimente acessar a página de novo.<br>
                    Quanto mais vendas na lista, mais a página pode ficar lenta para carregar (isso é normal devido ao volume de informação para processar).
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
                    <tbody>
                        
                    @if(empty($vendas))
                        <tr>
                            <td colspan="6" class="text-center font-italic">Nenhuma venda cancelada ainda...</td>
                        </tr>
                    @else
                    @foreach($vendas as $v)
                        @php
                        $data_ini = new DateTime($v->roteiro_data_ini);
                        $data_fim = new DateTime($v->roteiro_data_fim);
                        $data_reserva = new DateTime($v->data_reserva);
                        @endphp
                        <tr class="small cursor-pointer" onclick="getVenda({{$v->id}})">
                            <td>{{$v->id}}</td>
                            <td>( {{$v->roteiro_id}} ) {{$v->roteiro_nome}} ({{$data_ini->format('d/m/Y')}} a {{$data_fim->format('d/m/Y')}})</td>
                            <td>( {{$v->cliente_id}} ) {{$v->cliente_nome}}</td>
                            <td>{{$v->status}}</td>
                            <td>{{$data_reserva->format('d/m/Y H:i:s')}}</td>
                            <td>R$ {{$sgc->converteCentavoParaReal($v->valor_total)}}</td>
                        </tr>
                    @endforeach
                    @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div>

</div>