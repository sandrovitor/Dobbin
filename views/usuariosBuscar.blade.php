<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                Buscar Usuários
            </div>
            <div class="card-body">
                <form action="" method="post" data-manual id="buscarUsuarios">
                    <div class="form-group">
                        <label class="font-weight-bold">Buscar</label>
                        <input type="text" class="form-control form-control-solid" name="busca" placeholder="Deixe em branco para retornar todos"> 
                    </div>
                    <div class="form-group text-right">
                        <button type="submit" class="btn btn-success">Buscar</button>
                    </div>
                </form>
                <hr>
                <div id="retornoBusca" style="overflow-x:auto;">
                
                </div>
            </div>
        </div>
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
                            <th>Cód.</th>
                            <th>Perfil</th>
                            <th>Usuário</th>
                            <th>Nível</th>
                            <th class="d-none d-lg-table-cell">Criado em</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody style="font-size: .9rem;">
                    @if(empty($usuarios['usuarios']))
                        <tr><td class="text-center" colspan="6">Nada encontrado</td></tr>
                    @else
                        @foreach($usuarios['usuarios'] as $c)
                        @php
                            $criado = new DateTime($c->criado_em);
                        @endphp
                            <tr>
                                <td>{{$c->id}}</td>
                                <td><img src="/media/images/av/{{$c->avatar}}" height="50" style="border-radius: 50%;"></td>
                                <td>{{$c->nome}} {{$c->sobrenome}}<br><small class="font-italic">{{'@'.$c->usuario}}</small></td>
                                <td>{{$c->nivel}}</td>
                                <td class="d-none d-lg-table-cell">{{$criado->format('d/m/Y H:i')}}</td>
                                <td>
                                    <button type="button" class="btn btn-transparent btn-rounded btn-sm dropdown-toggle no-caret" data-toggle="dropdown"> <i class="fas fa-ellipsis-v fa-fw"></i> </button>
                                    <div class="dropdown-menu">
                                        <button class="dropdown-item" onclick="loadUsuario({{$c->id}})"><i class="far fa-eye fa-fw mr-1"></i> Ver</button>
                                        <button class="dropdown-item" onclick="editaUsuario({{$c->id}})"><i class="fas fa-pencil-alt fa-fw mr-1"></i> Editar</button>
                                        <div class="dropdown-divider"></div>
                                        <button class="dropdown-item text-danger" onclick="deleteUsuario({{$c->id}})"><i class="fas fa-trash fa-fw mr-1"></i> Apagar</button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                    </tbody>
                </table>

                <br>
                Total de clientes registrados: <span class="badge badge-info">{{$totalUsuarios}}</span>
                @else
                    <div class="alert alert-warning">Houve um erro interno. Consulte desenvolvedor. Mensagem: <i>{{$usuarios->mensagem}}</i></div>
                @endif
            </div>
        </div>
    </div>
</div>