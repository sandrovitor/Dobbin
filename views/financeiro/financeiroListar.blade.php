<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body px-3 py-3">
                <h3 class="mb-4 font-weight-bold">Folhas de contabilidade (balanço)</h3>
                <div class="mt-2 d-flex flex-wrap">
                @if(empty($folhas))
                    Sem folhas.
                @else
                @php
                    $anoAtual = '';
                    $mesAtual = '';
                @endphp
                @foreach($folhas as $f)
                    @php
                    // Se for fechado mudar a cor.
                    if($f->fechada == '1') {
                        $fechadaCor = 'dark';
                    } else {
                        $fechadaCor = 'info';
                    }
                    @endphp
                    <div data-href="#{{substr($router->generate('financeiroVer', ['ano' => $f->ano, 'mes'=>$f->mes, 'name' => substr($f->nomearq, 0, -5)]), 1)}}"
                        class="cursor-pointer p-2 border border-{{$fechadaCor}} mr-2 mb-2" data-toggle="tooltip" title="{{$f->fechada == '1' ? 'Balanço fechado.' : ''}}"
                        onclick="location.href = $(this).data('href');" style="width: 300px;"> 
                        <h5>Balanço {{$f->mes}}/{{$f->ano}}</h5>
                        
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="badge badge-{{$fechadaCor}}">{{$f->fechada == '1' ? 'FECHADO' : 'ABERTO'}}</span>

                            @if($f->fechada == '0')
                            <small class="text-right">Criado em:<br>{{$f->criado_data_str}}</small>
                            @else
                            <small class="text-right">Fechado em:<br>{{$f->criado_data_str}}</small>
                            @endif
                        </div>
                </div>
                    <!--
                    <a href="#{{substr($router->generate('financeiroVer', ['ano' => $f->ano, 'mes'=>$f->mes, 'name' => substr($f->nomearq, 0, -5)]), 1)}}" 
                        class="btn {{$fechadaCor}} px-4 py-2 mr-2"
                    data-toggle="tooltip" title="{{$f->fechada == '1' ? 'Balanço fechado.' : ''}}">
                        <b>Folha {{$f->mes}}/{{$f->ano}}</b>
                        <br>
                        <small>{{$f->criado_data_str}}</small>
                    </a>
                -->
                @endforeach
                @endif
                </div>
            </div>
        </div>
    </div>
</div>