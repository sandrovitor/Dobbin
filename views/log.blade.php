<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                Registro de Atividades - LOG
            </div>
            <div class="card-body">
                <div class="bg-secondary shadow border border-dark text-white rounded py-2 px-3 mb-4">
                    <form class="form-inline d-flex justify-content-between" id="controlesLog">
                        <div class="form-group">
                            <label class="mr-1">Quantidade </label>
                            <select class="form-control form-control-sm" name="qtd">
                                <option value="25" {{$qtd == 25 ? 'selected' : ''}}>25</option>
                                <option value="50" {{$qtd == 50 ? 'selected' : ''}}>50</option>
                                <option value="75" {{$qtd == 75 ? 'selected' : ''}}>75</option>
                                <option value="100" {{$qtd == 100 ? 'selected' : ''}}>100</option>
                                <option value="150" {{$qtd == 150 ? 'selected' : ''}}>150</option>
                                <option value="200" {{$qtd == 200 ? 'selected' : ''}}>200</option>
                            </select>
                        </div>
                        <div>
                            Página {{$pagina}}
                        </div>
                        <div class="input-group ml-4">
                            <div class="input-group-prepend">
                                <button type="button" class="btn btn-sm btn-outline-light" data-controle-prev {{$pagina <= 1 ? 'disabled' : ''}}>Anterior</button>
                            </div>
                            <select class="form-control form-control-sm" name="pagina">
                                @php
                                    $pages = ceil($total / $qtd);
                                @endphp
                                @for($x = 1; $x <= $pages; $x++)
                                <option value="{{$x}}" {{$x == $pagina ? 'selected': ''}}>Página {{$x}}</option>
                                @endfor
                                @if($pagina > $pages)
                                <option value="{{$pagina}}" selected>Página {{$pagina}}</option>
                                @endif
                            </select>
                            <div class="input-group-append">
                                <button type="button" class="btn btn-sm btn-outline-light" data-controle-next {{$pagina >= $pages ? 'disabled' : ''}}>Próximo</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="py-2 " style="overflow-x:auto;">
                    <table class="table table-bordered table-striped table-sm" id="logAtividades">
                        <thead>
                            <tr class="bg-white">
                                <th class="small font-weight-bold" style="max-width:5rem;">ID</th>
                                <th class="small font-weight-bold" style="width:4rem; max-width:6rem;">Grau</th>
                                <th>Data</th>
                                <th>Usuário</th>
                                <th>Mensagem</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(empty($logs))
                            <tr>
                                <td colspan="5" class="text-center font-italic py-2">Nada para mostrar nessa página...</td>
                            </tr>
                            @else
                            @foreach($logs as $l)
                            @php
                                $data = new DateTime($l->datahora);
                            @endphp
                            @if($l->usuid == '0')
                            <!-- DOBBIN ROBOT -->
                            <tr class="table-info">
                                <td class="small">{{$l->id}}</td>
                                <td class="text-center">
                                    @switch($l->grau)
                                        @case('1')
                                        <span class="badge badge-pill badge-info" title="Grau: baixo ou informação." data-toggle="tooltip"><i class="fas fa-info"></i></span>
                                        @break;

                                        @case('2')
                                        <span class="badge badge-pill badge-success" title="Grau: normal." data-toggle="tooltip"><i class="fas fa-info"></i></span>
                                        @break;

                                        @case('3')
                                        <span class="badge badge-pill badge-warning" title="Grau: atenção." data-toggle="tooltip"><i class="fas fa-exclamation"></i></span>
                                        @break;

                                        @case('4')
                                        <span class="badge badge-pill badge-danger" title="Grau: perigoso ou arriscado." data-toggle="tooltip"><i class="fas fa-exclamation-triangle"></i></span>
                                        @break;
                                    @endswitch
                                </td>
                                <td>{{$data->format('d/m/Y H:i:s')}}</td>
                                <td>
                                    <div class="d-flex" style="font-size: .9rem;">
                                        <div class="mr-2"><img src="/media/images/logo64i.png" height="50" style="border-radius: 50%;"></div>
                                        <div class="px-2">
                                            <strong class="text-primary">Dobbin Robot</strong><br>
                                            <small><span class="font-italic ">@robot</span><br>
                                            <span>Nível MAX</span></small>
                                        </div>
                                    </div>
                                </td>
                                <td>{!!$l->evento!!}</td>
                            </tr>
                            <!-- DOBBIN ROBOT -->
                            @else
                            <tr>
                                <td class="small">{{$l->id}}</td>
                                <td class="text-center">
                                    @switch($l->grau)
                                        @case('1')
                                        <span class="badge badge-pill badge-info" title="Grau: baixo ou informação." data-toggle="tooltip"><i class="fas fa-info"></i></span>
                                        @break;

                                        @case('2')
                                        <span class="badge badge-pill badge-success" title="Grau: normal." data-toggle="tooltip"><i class="fas fa-info"></i></span>
                                        @break;

                                        @case('3')
                                        <span class="badge badge-pill badge-warning" title="Grau: atenção." data-toggle="tooltip"><i class="fas fa-exclamation"></i></span>
                                        @break;

                                        @case('4')
                                        <span class="badge badge-pill badge-danger" title="Grau: perigoso ou arriscado." data-toggle="tooltip"><i class="fas fa-exclamation-triangle"></i></span>
                                        @break;
                                    @endswitch
                                </td>
                                <td>{{$data->format('d/m/Y H:i:s')}}</td>
                                <td>
                                    <div class="d-flex" style="font-size: .9rem;">
                                        <div class="mr-2"><img src="/media/images/av/{{$l->avatar}}" height="50" style="border-radius: 50%;"></div>
                                        <div class="px-2">
                                            <strong class="text-primary">{{$l->nome}}</strong><br>
                                            <small><span class="font-italic ">{{'@'.$l->usuario}}</span><br>
                                            <span>Nível {{$l->nivel}}</span></small>
                                        </div>
                                    </div>
                                </td>
                                <td>{!!$l->evento!!}</td>
                            </tr>
                            @endif
                            

                            @endforeach
                            @endif
                        
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
    </div>
</div>