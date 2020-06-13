<div class="row mb-3">
    <div class="col-6 col-lg-4 mb-3">
        <a href="#usuarios/novo" class="btn btn-block btn-light rounded-0 shadow-sm font-weight-bold py-2">Novo</a>
    </div>
    <div class="col-6 col-lg-4 mb-3">
        <a href="#usuarios/buscar" class="btn btn-block btn-light rounded-0 shadow-sm font-weight-bold py-2">Buscar</a>
    </div>
    <div class="col-12 col-lg-4 mb-3">
        <a href="#usuarios/database" class="btn btn-block btn-light rounded-0 shadow-sm font-weight-bold py-2">Base de dados</a>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                Últimos usuários cadastrados
            </div>
            <div class="card-body" style="overflow-x:auto;">
                
                @if($usuarios['success'] == true)
                <table class="table table-bordered table-sm">
                    <thead class="bg-dark text-white">
                        <tr>
                            <th>Usuário</th>
                            <th>Criado em</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody style="font-size: .9rem;">
                    @if(empty($usuarios['usuarios']))
                        <tr><td class="text-center" colspan="7">Nada encontrado</td></tr>
                    @else
                        @foreach($usuarios['usuarios'] as $u)
                        @php
                            $criado = new DateTime($u->criado_em);
                        @endphp
                            <tr>
                                <td>
                                    <div class="d-flex" style="font-size: .9rem;">
                                        <div class="mr-2"><img src="/media/images/av/{{$u->avatar}}" height="50" style="border-radius: 50%;"></div>
                                        <div class="px-2">
                                            <strong class="text-primary">{{$u->nome}} {{$u->sobrenome}}</strong><br>
                                            <small><span class="font-italic ">{{'@'.$u->usuario}}</span><br>
                                            <span>Nível {{$u->nivel}}</span></small>
                                        </div>
                                    </div>
                                </td>
                                <td>{{$criado->format('d/m/Y H:i')}}</td>
                                <td>
                                    <button type="button" class="btn btn-transparent btn-rounded btn-sm dropdown-toggle no-caret" data-toggle="dropdown"> <i class="fas fa-ellipsis-v fa-fw"></i> </button>
                                    <div class="dropdown-menu">
                                        <button class="dropdown-item" onclick="loadUsuario({{$u->id}})"><i class="far fa-eye fa-fw mr-1"></i> Ver</button>
                                        <button class="dropdown-item" onclick="editaUsuario({{$u->id}})"><i class="fas fa-pencil-alt fa-fw mr-1"></i> Editar</button>
                                        <div class="dropdown-divider"></div>
                                        <button class="dropdown-item text-danger" onclick="deleteUsuario({{$u->id}})"><i class="fas fa-trash fa-fw mr-1"></i> Apagar</button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                    </tbody>
                </table>
                @else
                    <div class="alert alert-warning">Houve um erro interno. Consulte desenvolvedor. Mensagem: <i>{{$usuarios['mensagem']}}</i></div>
                @endif
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                Nível de acesso
            </div>
            <div class="card-body">
                O acesso à plataforma é regulada por níveis de acesso. Veja quais níveis foram definidos e ajuste os usuários de acordo com essas atividades:<br><br>
                <table class="table table-sm table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Nível</th>
                            <th>Seções</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th>Nível 1</th>
                            <td>Início; Clientes (Buscar, Base de Dados)</td>
                        </tr>
                        <tr>
                            <th>Nível 2</th>
                            <td><small class="text-muted">(Mesmas atividades do Nível 1.)</small><br> Clientes (Novo, Lixeira); Roteiros</td>
                        </tr>
                        <tr>
                            <th>Nível 3</th>
                            <td><small class="text-muted">(Mesmas atividades do Nível 2.)</small><br> Vendas; Offline</td>
                        </tr>
                        <tr>
                            <th>Nível 4</th>
                            <td><small class="text-muted">(Mesmas atividades do Nível 3.)</small><br> Parceiros</td>
                        </tr>
                        <tr>
                            <th>Nível 5</th>
                            <td><small class="text-muted">(Mesmas atividades do Nível 4.)</small><br> Financeiro</td>
                        </tr>
                        <tr>
                            <th>Nível 10</th>
                            <td>TODAS seções disponíveis</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>