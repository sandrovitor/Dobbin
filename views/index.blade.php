<div class="row mb-3">
    <div class="col-6 col-lg-3 mb-3">
        <a href="#vendas/novo" class="btn btn-block btn-info rounded-0 shadow-sm font-weight-bold py-2"><i class="fas fa-shopping-cart fa-fw"></i> Nova Venda</a>
    </div>
    <div class="col-6 col-lg-3 mb-3">
        <a href="#roteiros/novo" class="btn btn-block btn-info rounded-0 shadow-sm font-weight-bold py-2"><i class="fas fa-luggage-cart fa-fw"></i> Novo Roteiro</a>
    </div>
</div>

<div class="row">
    <div class="col-12 col-md-11 col-lg-10 col-xl-8 mx-auto">
        <div class="card">
            <div class="card-header">
                INFORMAÇÕES DO SISTEMA
            </div>
            <div class="card-body">
                Dados relevantes sobre o funcionamento da plataforma.
                @php
                    $consumo = $sistema['consumo'];
                    $limite = $sistema['limite'];
                    $perc = array();
                    $status = array();

                    foreach($consumo as $key => $val) {
                        $perc[$key] = ($val * 100) / $limite[$key];
                        if($perc[$key] < 50) {
                            $status[$key] = 'primary';
                        } else if($perc[$key] < 80) {
                            $status[$key] = 'success';
                        } else if($perc[$key] < 95) {
                            $status[$key] = 'warning';
                        } else {
                            $status[$key] = 'danger';
                        }
                    }
                @endphp
                <div class="row mt-3">
                    <div class="col-12 col-md-6 col-lg-4 mb-3">
                        <div class="border border-{{$status['clientes']}} shadow-sm p-2">
                            <strong class="text-primary">Clientes</strong><br>
                            <span>{{$consumo['clientes']}}</span> / <span>{{$limite['clientes']}}</span> <span class="ml-2">({{round($perc['clientes'], 2)}}%)</span>
                            <div class="progress">
                                <div class="progress-bar bg-{{$status['clientes']}}" style="width:{{round($perc['clientes'], 2)}}%"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-4 mb-3">
                        <div class="border border-{{$status['parceiros']}} shadow-sm p-2">
                            <strong class="text-primary">Parceiros</strong><br>
                            <span>{{$consumo['parceiros']}}</span> / <span>{{$limite['parceiros']}}</span> <span class="ml-2">({{round($perc['parceiros'], 2)}}%)</span>
                            <div class="progress">
                                <div class="progress-bar bg-{{$status['parceiros']}}" style="width:{{round($perc['parceiros'], 2)}}%"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-4 mb-3">
                        <div class="border border-{{$status['roteiros']}} shadow-sm p-2">
                            <strong class="text-primary">Roteiros</strong><br>
                            <span>{{$consumo['roteiros']}}</span> / <span>{{$limite['roteiros']}}</span> <span class="ml-2">({{round($perc['roteiros'], 2)}}%)</span>
                            <div class="progress">
                                <div class="progress-bar bg-{{$status['roteiros']}}" style="width:{{round($perc['roteiros'], 2)}}%"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-4 mb-3">
                        <div class="border border-{{$status['vendas']}} shadow-sm p-2">
                            <strong class="text-primary">Vendas</strong><br>
                            <span>{{$consumo['vendas']}}</span> / <span>{{$limite['vendas']}}</span> <span class="ml-2">({{round($perc['vendas'], 2)}}%)</span>
                            <div class="progress">
                                <div class="progress-bar bg-{{$status['vendas']}}" style="width:{{round($perc['vendas'], 2)}}%"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-4 mb-3">
                        <div class="border border-{{$status['usuarios']}} shadow-sm p-2">
                            <strong class="text-primary">Usuários</strong><br>
                            <span>{{$consumo['usuarios']}}</span> / <span>{{$limite['usuarios']}}</span> <span class="ml-2">({{round($perc['usuarios'], 2)}}%)</span>
                            <div class="progress">
                                <div class="progress-bar bg-{{$status['usuarios']}}" style="width:{{round($perc['usuarios'], 2)}}%"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-4 mb-3">
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>