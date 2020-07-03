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
            <div class="card-body" style="overflow-x:auto;">
            @php
            $hoje = new DateTime();
            @endphp
                <div class="mb-3">
                    <div class="alert alert-info small py-1 px-2 mb-2">
                        <strong>OBS:</strong> As últimas 200 vendas serão exibidas. Para ver vendas mais antigas, consulte informações de um <a href="#clientes">cliente</a> ou de um <a href="#roteiros">roteiro</a>.<br>
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
                    <tbody>
                    @if(empty($vendas))
                        <tr>
                            <td colspan="6" class="text-center font-italic">Nenhuma venda ainda...</td>
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

<script>
function getReservas()
{
    $.post('/vendas/database/get/reservas', function(res){
        if(res.success) {
            //console.log(res.vendas);
            //console.log(res.vendas.length);
            if(res.vendas.length == 0) {
                if($('#reservasDiv > div').length == 0) {
                    $('#reservasDiv').append('<div class="text-center mx-2"></div>');
                }
                $('#reservasDiv > div').html('<i>Nada encontrado...</i>');
                $('#reservasDiv').find('table').remove();
            } else {
                $('#reservasDiv').find('table').remove();
                $('#reservasDiv').append('<table class="table table-sm table-bordered table-hover mb-0" style="display:none;">'+
                    '<thead class="thead-dark"> <tr>'+
                    '<th>Cód</th> <th>(Cód) Roteiro</th> <th>(Cód) Cliente</th> <th>Status</th> <th>Data</th> <th>Valor Total</th>'+
                    '</tr> </thead> <tbody> </tbody> </table>');
                res.vendas.forEach(function(v){
                    let dataReserva = Dobbin.formataDataHora( new Date(v.data_reserva), true);
                    $('#reservasDiv tbody').append(
                        '<tr class="small cursor-pointer" onclick="getVenda('+v.id+')"> <td>'+v.id+'</td> <td>( '+v.roteiro_id+' ) '+v.roteiro_nome+'</td> '+
                        '<td>( '+v.cliente_id+' ) '+v.cliente_nome+'</td> <td>'+v.status+'</td> '+
                        '<td>'+dataReserva+'</td> <td>R$ '+Dobbin.converteCentavoEmReal(v.valor_total)+'</td></tr>'
                    );
                });

                $('#reservasDiv > div').fadeOut('fast', function(){
                    $(this).remove();
                    $('#reservasDiv table').slideDown();
                });
            }
            
        } else {
            alerta(res.mensagem, 'Falha ao obter lista de reservas.', 'warning');
            if(debugEnabled === true) {
                console.log(res);
            }
        }
    }, 'json').
    fail(function(ev){nativePOSTFail(ev);});
}

function getAguardandoPagamento()
{
    $.post('/vendas/database/get/aguardando', function(res){
        if(res.success) {
            //console.log(res.vendas);
            //console.log(res.vendas.length);
            if(res.vendas.length == 0) {
                if($('#aguardPagDiv > div').length == 0) {
                    $('#aguardPagDiv').append('<div class="text-center mx-2"></div>');
                }
                $('#aguardPagDiv > div').html('<i>Nada encontrado...</i>');
                $('#aguardPagDiv').find('table').remove();
            } else {
                $('#aguardPagDiv').find('table').remove();
                $('#aguardPagDiv').append('<table class="table table-sm table-bordered table-hover mb-0" style="display:none;">'+
                    '<thead class="thead-dark"> <tr>'+
                    '<th>Cód</th> <th>Roteiro</th> <th>Cliente</th> <th>Status</th> <th>Data</th> <th>Valor Total</th>'+
                    '</tr> </thead> <tbody> </tbody> </table>');
                res.vendas.forEach(function(v){
                    let dataReserva = Dobbin.formataDataHora( new Date(v.data_reserva), true);
                    $('#aguardPagDiv tbody').append(
                        '<tr class="small cursor-pointer" onclick="getVenda('+v.id+')"> <td>'+v.id+'</td> <td>( '+v.roteiro_id+' ) '+v.roteiro_nome+'</td> '+
                        '<td>( '+v.cliente_id+' ) '+v.cliente_nome+'</td> <td>'+v.status+'</td> '+
                        '<td>'+dataReserva+'</td> <td>R$ '+Dobbin.converteCentavoEmReal(v.valor_total)+'</td></tr>'
                    );
                });

                $('#aguardPagDiv > div').fadeOut('fast', function(){
                    $(this).remove();
                    $('#aguardPagDiv table').slideDown();
                });
            }
            
        } else {
            alerta(res.mensagem, 'Falha ao obter lista de vendas Aguardando Pagamento.', 'warning');
            if(debugEnabled === true) {
                console.log(res);
            }
        }
    }, 'json').
    fail(function(ev){nativePOSTFail(ev);});
}

$(document).ready(function(){
    // Atraso de 1 segundo no carregamento de outras seções
    setTimeout(function(){
        getReservas();
        getAguardandoPagamento();
    }, 1000);
    
});
</script>