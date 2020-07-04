<div class="row">
    <div class="col-12 col-lg-10 col-xl-8 mx-auto">
        <div class="card">
            <div class="card-header">
                Lixeira
            </div>
            <div class="card-body" style="overflow-x:auto">
                <div class="alert alert-info small px-2 py-1">
                    <i class="fas fa-info-circle"></i> Clientes permanecerão na lixeira por 72h e depois serão excluídos automaticamente.
                </div>
                @if(empty($clientes))
                    <h6 class="text-center my-3 font-italic">Não há nada aqui...</h6>
                @else
                    <table class="table table-hover table-bordered table-sm">
                        <thead class="bg-dark text-white">
                            <tr>
                                <th>Cód.</th>
                                <th>Nome</th>
                                <th>Email</th>
                                <th>Cidade</th>
                                <th>Estado</th>
                                <th>Apagado em</th>
                                <th>Apagado por</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($clientes as $c)
                        @php
                            $apagado_em = new DateTime($c->deletado_em);
                        @endphp
                            <tr>
                                <td>{{$c->id}}</td>
                                <td><a href="javascript:void(0)" onclick="loadCliente({{$c->id}})">{{$c->nome}}</a></td>
                                <td class="d-none d-lg-table-cell">{{$c->email}}</td>
                                <td>{{$c->cidade}}</td>
                                <td>{{$c->estado}}</td>
                                <td>{{$apagado_em->format('d/m/Y H:i:s')}}</td>
                                <td>{{$c->usuario}}</td>
                                
                                <td>
                                    <button type="button" class="btn btn-transparent btn-rounded btn-sm dropdown-toggle no-caret" data-toggle="dropdown"> <i class="fas fa-ellipsis-v fa-fw"></i> </button>
                                    <div class="dropdown-menu">
                                        <button class="dropdown-item" onclick="loadCliente({{$c->id}})"><i class="far fa-eye fa-fw mr-1"></i> Ver</button>
                                        <button class="dropdown-item" onclick="restauraCliente({{$c->id}})"><i class="fas fa-redo-alt fa-fw mr-1"></i> Restaurar</button>
                                        <div class="dropdown-divider"></div>
                                        <button class="dropdown-item text-danger" onclick="deleteClienteLixeira({{$c->id}})"><i class="fas fa-trash fa-fw mr-1"></i> Apagar de vez</button>
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