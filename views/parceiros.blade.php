<div class="row mb-3">
    <div class="col-6 col-lg-4 mb-3">
        <a href="#parceiros/novo" class="btn btn-block btn-light rounded-0 shadow-sm font-weight-bold py-2">Novo</a>
    </div>
    <!--
    <div class="col-6 col-lg-4 mb-3">
        <a href="#parceiros/buscar" class="btn btn-block btn-light rounded-0 shadow-sm font-weight-bold py-2">Buscar</a>
    </div>
    -->
    <div class="col-12 col-lg-4 mb-3">
        <a href="#parceiros/database" class="btn btn-block btn-light rounded-0 shadow-sm font-weight-bold py-2">Base de dados</a>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                Últimos parceiros cadastrados
            </div>
            <div class="card-body" style="overflow-x:auto;">
                
                @if($parceiros['success'] == true)
                <table class="table table-bordered table-sm">
                    <thead class="bg-dark text-white">
                        <tr>
                            <th>Cód.</th>
                            <th>Parceiro</th>
                            <th>Cidade</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody style="font-size: .9rem;">
                    @if(empty($parceiros['parceiros']))
                        <tr><td class="text-center" colspan="7">Nada encontrado</td></tr>
                    @else
                        @foreach($parceiros['parceiros'] as $p)
                        @php
                            $criado = new DateTime($p->criado_em);
                        @endphp
                            <tr>
                                <td>{{$p->id}}</td>
                                <td>
                                @if($p->nome_fantasia != '')
                                    <a href="#parceiros/ver/{{$p->id}}">
                                        <span class="font-weight-bold">{{$p->nome_fantasia}}</span><br>
                                        <span class="text-uppercase small font-italic">{{$p->razao_social}}</span>
                                    </a>
                                @else
                                    <a href="#parceiros/ver/{{$p->id}}">
                                        <span class="text-uppercase font-weight-bold">{{$p->razao_social}}</span>
                                    </a>
                                @endif
                                </td>
                                <td>{{$p->cidade}}</td>
                                <td>{{$p->estado}}</td>
                            </tr>
                        @endforeach
                    @endif
                @else
                <div class="alert alert-warning">Houve um erro interno. Consulte desenvolvedor. Mensagem: <i>{{$clientes['mensagem']}}</i></div>
                @endif
            </div>
        </div>
    </div>
</div>