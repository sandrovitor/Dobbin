<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                Buscar Coordenadores
            </div>
            <div class="card-body">
                <form action="" method="post" data-manual id="buscarCoordenadores">
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
                Últimos coordenadores cadastrados
            </div>
            <div class="card-body" style="overflow-x:auto;">
                
                
                @if($coordenadores['success'] == true)
                <table class="table table-bordered table-sm">
                    <thead class="bg-dark text-white">
                        <tr>
                            <th>Cód.</th>
                            <th>Nome</th>
                            <th class="d-none d-lg-table-cell">Email</th>
                            <th>Cidade</th>
                            <th>Estado</th>
                            <th class="d-none d-lg-table-cell">Criado em</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody style="font-size: .9rem;">
                    @if(empty($coordenadores['coordenadores']))
                        <tr><td class="text-center" colspan="7">Nada encontrado</td></tr>
                    @else
                        @foreach($coordenadores['coordenadores'] as $c)
                        @php
                            $criado = new DateTime($c->criado_em);
                        @endphp
                            <tr>
                                <td>{{$c->id}}</td>
                                <td>{{$c->nome}}</td>
                                <td class="d-none d-lg-table-cell">{{$c->email}}</td>
                                <td>{{$c->cidade}}</td>
                                <td>{{$c->estado}}</td>
                                <td class="d-none d-lg-table-cell">{{$criado->format('d/m/Y H:i')}}</td>
                                <td>
                                    <button type="button" class="btn btn-transparent btn-rounded btn-sm dropdown-toggle no-caret" data-toggle="dropdown"> <i class="fas fa-ellipsis-v fa-fw"></i> </button>
                                    <div class="dropdown-menu">
                                        <button class="dropdown-item" onclick="loadCoordenador({{$c->id}})"><i class="far fa-eye fa-fw mr-1"></i> Ver</button>
                                        <button class="dropdown-item" onclick="editaCoordenador({{$c->id}})"><i class="fas fa-pencil-alt fa-fw mr-1"></i> Editar</button>
                                        <div class="dropdown-divider"></div>
                                        <button class="dropdown-item text-danger" onclick="deleteCoordenador({{$c->id}})"><i class="fas fa-trash fa-fw mr-1"></i> Apagar</button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                    </tbody>
                </table>

                <br>
                Total de coordenadores registrados: <span class="badge badge-info">{{$totalCoordenadores}}</span>
                @else
                    <div class="alert alert-warning">Houve um erro interno. Consulte desenvolvedor. Mensagem: <i>{{$coordenadores->mensagem}}</i></div>
                @endif
            </div>
        </div>
    </div>
</div>