<!DOCTYPE HTML>
<html lang="pt-br">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta content="width=device-width, initial-scale=1, maximum-scale=1" name="viewport">
	<meta name="author" content="Sandro Mendonça">
    <meta name="robots" content="noindex,nofollow,noarchive">
	<meta name="description" content="{{$sistema->description}}">
	<title>{{$sistema->name}}</title>
    <!--<link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet" type="text/css">-->
    <link href="/css/bootstrap-4.3.1.min.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css" integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous">
    <link rel="stylesheet" href="/css/geral.css?v{{$sistema->version}}">
    <link rel="stylesheet" href="/css/alert.css?v{{$sistema->version}}">
    <link rel="icon" href="/media/images/logo64i.png">
    
</head>

<body>
    <nav class="ds-navbar ">
        <div class="ds-navbar-brand">
            <strong><img src="/media/images/logo64.png" height="38" class="mx-3 my-2" alt="{{$sistema->name}} logo"></strong>
        </div>
        <button class="btn btn-transparent btn-rounded" data-sidebar-toggle><i class="fas fa-bars"></i></button>
        <div class="flex-grow-1">
        <ul class="nav justify-content-end">
            <li class="nav-item">
                <a href="javascript:void(0)" id="avatarMe" onclick="$(this).next('.nav-dropdown').fadeToggle(100);">
                    <img src="/media/images/av/{{$_SESSION['auth']['avatar']}}" height="38" style="border-radius: 50%;">
                </a>
                <div class="nav-dropdown" style="display:none;">
                    <div class="d-flex my-3 mx-3">
                        <div class="mr-2">
                            <img src="/media/images/av/{{$_SESSION['auth']['avatar']}}" height="55" style="border-radius: 50%;">
                        </div>
                        <div class="ml-2" style="line-height:1.2;">
                            <strong>{{$_SESSION['auth']['nome']}} {{$_SESSION['auth']['sobrenome']}}</strong><br>
                            <small>{{$_SESSION['auth']['email']}}</small><br>
                            <span class="badge badge-info">Nível {{$_SESSION['auth']['nivel']}}</span>
                        </div>
                    </div>
                    <hr>
                    <div class="my-3 mx-3">
                        <ul class="nav flex-column">
                            <li class="nav-item my-1"><a href="javascript:void(0)" onclick="loadMinhaConta($(this));">Minha conta</a></li>
                            <li class="nav-item my-1"><a href="/sair">Sair</a></li>
                        </ul>
                    </div>
                    
                </div>
            </li>
        </ul>
            
        </div>
    </nav>
    <div id="main">
        <section id="sidebar">
            <div class="sidebar-content">
                <div id="sidebarFixedToggle" class="alwaysShow" title="Alterne entre sempre exibir e sempre ocultar menu lateral em telas grandes." data-toggle="tooltip"><i class="fas fa-toggle-on"></i></div>
                <h6 class="title">GERAL</h6>
                <ul class="nav flex-column">
                    <li class="nav-item"><a class="nav-link" href="#inicio"><i class="fas fa-home fa-fw"></i> Início</a></li>
                    <li class="nav-item">
                        <a class="nav-link link-dropdown" href="#clientes"><i class="fas fa-users fa-fw"></i> Clientes</a>
                        <button class="btn btn-dropdown btn-sm"><i class="fas fa-angle-right fa-fw"></i></button>
                        <ul style="display:none;">
                            <li><a class="nav-link" href="#clientes/novo">Novo</a></li>
                            <li><a class="nav-link" href="#clientes/buscar">Buscar</a></li>
                            <li><a class="nav-link" href="#clientes/database">Base de dados</a></li>
                            <li><a class="nav-link text-danger" href="#clientes/lixeira">Lixeira</a></li>
                        </ul>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link link-dropdown" href="#coordenadores"><i class="fas fa-user-tie fa-fw"></i> Coordenadores</a>
                        <button class="btn btn-dropdown btn-sm"><i class="fas fa-angle-right fa-fw"></i></button>
                        <ul style="display:none;">
                            <li><a class="nav-link" href="#coordenadores/novo">Novo</a></li>
                            <li><a class="nav-link" href="#coordenadores/buscar">Buscar</a></li>
                            <li><a class="nav-link" href="#coordenadores/database">Base de dados</a></li>
                            <li><a class="nav-link text-danger" href="#coordenadores/lixeira">Lixeira</a></li>
                        </ul>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link" href="#parceiros"><i class="far fa-handshake fa-fw"></i> Parceiros</a>
                        <button class="btn btn-dropdown btn-sm"><i class="fas fa-angle-right fa-fw"></i></button>
                        <ul style="display:none;">
                            <li><a class="nav-link" href="#parceiros/novo">Novo</a></li>
                            <li><a class="nav-link" href="#parceiros/database">Base de dados</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#roteiros"><i class="fas fa-luggage-cart fa-fw"></i> Roteiros</a>
                        <button class="btn btn-dropdown btn-sm"><i class="fas fa-angle-right fa-fw"></i></button>
                        <ul style="display:none;">
                            <li><a class="nav-link" href="#roteiros/novo">Novo</a></li>
                            <li><a class="nav-link" href="#roteiros/database">Base de dados</a></li>
                            <li><a class="nav-link" href="#roteiros/simulacao">Simulação</a></li>
                            <li><a class="nav-link text-danger" href="#roteiros/lixeira">Lixeira</a></li>
                        </ul>
                    </li>
                    

                    <li class="nav-item">
                        <a class="nav-link" href="#vendas"><i class="fas fa-shopping-cart fa-fw"></i> Vendas</a>
                        <button class="btn btn-dropdown btn-sm"><i class="fas fa-angle-right fa-fw"></i></button>
                        <ul style="display:none;">
                            <li><a class="nav-link" href="#vendas/novo">Novo</a></li>
                            <li><a class="nav-link" href="#vendas/buscar">Buscar</a></li>
                            <li><a class="nav-link" href="#vendas/database">Base de Dados</a></li>
                            <li><a class="nav-link" href="#vendas/canceladas">Vendas Canceladas</a></li>
                        </ul>
                    </li>


                    <!--
                    <li class="nav-item"><a class="nav-link" href="#roteiros"><i class="fas fa-luggage-cart fa-fw"></i> Roteiros</a></li>
                    <li class="nav-item"><a class="nav-link" href="#vendas"><i class="fas fa-shopping-cart fa-fw"></i> Vendas</a></li>
                    <li class="nav-item"><a class="nav-link" href="#parceiros"><i class="far fa-handshake fa-fw"></i> Parceiros</a></li>
                    -->
                </ul>
                <hr>
                <h6 class="title">PLATAFORMA</h6>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link link-dropdown" href="#usuarios"><i class="fas fa-user-shield fa-fw"></i> Usuários</a>
                        <button class="btn btn-dropdown btn-sm"><i class="fas fa-angle-right fa-fw"></i></button>
                        <ul style="display:none;">
                            <li><a class="nav-link" href="#usuarios/novo">Novo</a></li>
                            <li><a class="nav-link" href="#usuarios/buscar">Buscar</a></li>
                            <li><a class="nav-link" href="#usuarios/database">Base de Dados</a></li>
                        </ul>
                    </li>
                    <li class="nav-item"><a class="nav-link" href="#log"><i class="fas fa-info-circle fa-fw"></i> LOG</a></li>
                    <!--
                    <li class="nav-item"><a class="nav-link" href="#offline"><i class="fas fa-cloud-download-alt fa-fw"></i> Offline</a></li>
                    -->
                </ul>
            </div>
            <div class="sidebar-footer">
                <small>Logado como:</small><br>
                <strong>{{$_SESSION['auth']['nome']}} {{$_SESSION['auth']['sobrenome']}}</strong>
            </div>
        </section>
        <section id="content" >
            <div class="page-header">
                <div class="container-fluid">
                    <div class="page-header-content">
                        <h1 class="page-header-title">Carregando...</h1>
                        <h5 class="page-header-description"></h5>
                    </div>
                </div>
            </div>
            <div class="container-fluid pos-page-header" id="bodyContent">
                
            </div>
            <div class="container-fluid">
                <div class="row mt-5 mb-3 pt-2 ">
                    <div class="col-12 col-md-4 col-lg-6 text-muted text-center text-md-left">
                        <small>{{$sistema->name}} <span data-systemversion>{{$sistema->version}}</span></small>
                    </div>
                    <div class="col-12 col-md-8 col-lg-6 text-muted text-center text-md-right">
                        <small>Copyright &copy; {{date('Y')}} Sandro Mendonça</small>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- MODAIS-->
<div class="modal fade" id="modalClienteDetalhes">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header py-2 px-3 font-weight-bold">
                Detalhes do cliente
                <button type="button" class="btn btn-sm btn-danger fechar" data-dismiss="modal"><strong>&times;</strong></button>
            </div>
            <div class="modal-body">
                <ul class="breadcrumb small">
                </ul>
                <h3 class="">
                    <strong class="text-primary" data-detalhes-nome></strong> <small>[Cód.: <span data-detalhes-id></span>]</small>
                    <span class="ml-3" data-detalhes-deletado></span> <span class="ml-3 tipoCliente" style="cursor:pointer;"></span>
                    <button class="btn btn-sm btn-primary" data-detalhes-id onclick="$(this).parents('.modal').modal('hide'); editaCliente($(this).data('id'))"><i class="fas fa-pen"></i></button>
                </h3>
                <div class="mt-n1 d-flex">
                    <small class="badge badge-info mr-2 mb-1">Criado em: &nbsp; <span data-detalhes-criadoem></span></small>
                    <span class="badge badge-info mr-2 mb-1">Atualizado em: &nbsp; <span data-detalhes-atualizadoem></span></span>
                </div>
                <div class="listaDependentes pt-3" style="display:none">
                    
                </div>
                <hr class="mb-5">
                <h5 class="mt-3 mb-2 font-weight-bold">INFORMAÇÕES PESSOAIS</h5>
                <div class="d-flex flex-wrap">
                    <div class="py-2 px-3 hover border shadow-sm mr-2 mb-2">
                        <strong>Data de Nascimento:</strong><br><span data-detalhes-nascimento></span>
                    </div>
                    <div class="py-2 px-3 hover border shadow-sm mr-2 mb-2">
                        <strong>RG:</strong><br><span data-detalhes-rg></span>
                    </div>
                    <div class="py-2 px-3 hover border shadow-sm mr-2 mb-2">
                        <strong>CPF:</strong><br><span data-detalhes-cpf></span>
                    </div>
                    <div class="py-2 px-3 hover border shadow-sm mr-2 mb-2">
                        <strong>Estado Civil:</strong><br><span data-detalhes-civil></span>
                    </div>
                    <div class="py-2 px-3 hover border shadow-sm mr-2 mb-2">
                        <strong>Email:</strong><br><span data-detalhes-email></span>
                    </div>
                    <div class="py-2 px-3 hover border shadow-sm mr-2 mb-2">
                        <strong>Telefone:</strong><br><span data-detalhes-tel></span>
                    </div>
                    <div class="py-2 px-3 hover border shadow-sm mr-2 mb-2">
                        <strong>Faixa Etária:</strong><br><span data-detalhes-faixaetaria></span>
                    </div>
                </div>
                <hr>

                <h5 class="mt-3 mb-2 font-weight-bold">ENDEREÇO</h5>
                <div class="d-flex flex-wrap">
                    <div class="py-2 px-3 hover border shadow-sm mr-2 mb-2">
                        <strong>Endereço:</strong><br><span data-detalhes-endereco></span>
                    </div>
                    <div class="py-2 px-3 hover border shadow-sm mr-2 mb-2">
                        <strong>Complemento:</strong><br><span data-detalhes-complemento></span>
                    </div>
                    <div class="py-2 px-3 hover border shadow-sm mr-2 mb-2">
                        <strong>Ponto Referência:</strong><br><span data-detalhes-pontoref></span>
                    </div>
                    <div class="py-2 px-3 hover border shadow-sm mr-2 mb-2">
                        <strong>CEP:</strong><br><span data-detalhes-cep></span>
                    </div>
                    <div class="py-2 px-3 hover border shadow-sm mr-2 mb-2">
                        <strong>Bairro:</strong><br><span data-detalhes-bairro></span>
                    </div>
                    <div class="py-2 px-3 hover border shadow-sm mr-2 mb-2">
                        <strong>Cidade:</strong><br><span data-detalhes-cidade></span>
                    </div>
                    <div class="py-2 px-3 hover border shadow-sm mr-2 mb-2">
                        <strong>Estado:</strong><br><span data-detalhes-estado></span>
                    </div>
                </div>
                <hr>

                <h5 class="mt-3 mb-2 font-weight-bold">SAÚDE E EMERGÊNCIA</h5>
                <div class="d-flex flex-wrap">
                    <div class="py-2 px-3 hover border shadow-sm mr-2 mb-2">
                        <strong>Tipo Sanguíneo:</strong><br><span data-detalhes-sangue></span>
                    </div>
                    <div class="py-2 px-3 hover border shadow-sm mr-2 mb-2">
                        <strong>Alergias:</strong><br><span data-detalhes-alergia></span>
                    </div>
                    <div class="py-2 px-3 hover border shadow-sm mr-2 mb-2">
                        <strong>Contato de emergência:</strong><br><span data-detalhes-emergencianome></span>
                    </div>
                    <div class="py-2 px-3 hover border shadow-sm mr-2 mb-2">
                        <strong>Telefone de emergência:</strong><br><span data-detalhes-emergenciatel></span>
                    </div>
                </div>
                <hr>

                <h5 class="mt-3 mb-2 font-weight-bold">OUTRAS INFORMAÇÕES</h5>
                <div class="d-flex flex-wrap">
                    <div class="py-2 px-3 hover border shadow-sm mr-2 mb-2">
                        <strong>Taxa Extra Casal:</strong><br><span data-detalhes-taxaextracasal></span>
                    </div>
                    <div class="py-2 px-3 hover border shadow-sm mr-2 mb-2">
                        <strong>Criado em:</strong><br><span data-detalhes-criadoem></span>
                    </div>
                    <div class="py-2 px-3 hover border shadow-sm mr-2 mb-2">
                        <strong>Atualizado em:</strong><br><span data-detalhes-atualizadoem></span>
                    </div>
                </div>
                <hr>

                <h5 class="mt-3 mb-2 font-weight-bold">HISTÓRICO DE COMPRAS</h5>
                <div data-historico-compras>
                    <div class="border bloco-acord">
                        <div class="acord-header bg-light p-2 d-flex justify-content-between" style="cursor:pointer;">
                            <h6 class="font-weight-bold text-uppercase my-1 text-primary">
                                <small class="ml-2 text-dark"></small>
                            </h6>
                            <button class="btn btn-transparent btn-sm text-dark"><i class="fas fa-angle-down"></i></button>
                        </div>
                        <div class="acord-body p-2 py-3 pt-0 border border-secondary border-bottom-0 border-left-0 border-right-0" style="display:none"></div>
                    </div>
                </div>


            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light btn-sm" data-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalClienteEditar">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header py-2 px-3 font-weight-bold">
                Editar dados do cliente
                <button type="button" class="btn btn-sm btn-danger fechar" data-dismiss="modal"><strong>&times;</strong></button>
            </div>
            <div class="modal-body">
                <h3 class="">
                    <strong class="text-primary" data-detalhes-nome></strong> <small>[Cód.: <span data-detalhes-id></span>]</small>
                </h3>
                <hr>
                <form action="clientes/salvar" method="post">
                    <div class="form-group row">
                        <div class="col-12 col-xl-6 mb-3">
                            <label>Nome:</label>
                            <input type="text" class="form-control form-control-solid" name="nome" maxlength="30" data-detalhes-nome>
                        </div>
                        <div class="col-12 col-xl-6">
                            <label>Data de Nascimento:</label>
                            <input type="date" class="form-control form-control-solid" name="nascimento" maxlength="30" data-detalhes-nascimento>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-12 col-xl-6 mb-3">
                            <label>RG:</label>
                            <input type="text" class="form-control form-control-solid" name="rg" placeholder="Insira somente os números." pattern="[0-9]{0,10}" data-detalhes-rg data-validate-rg maxlength="10">
                        </div>
                        <div class="col-12 col-xl-6">
                            <label>CPF:</label>
                            <input type="text" class="form-control form-control-solid" name="cpf" placeholder="Insira somente os números." pattern="[0-9]{0,11}" data-detalhes-cpf data-validate-cpf maxlength="11">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-12 col-xl-6 mb-3">
                            <label>Email:</label>
                            <input type="email" class="form-control form-control-solid" name="email" placeholder="" maxlength="60" data-detalhes-email>
                        </div>
                        <div class="col-12 col-xl-6">
                            <label>Telefone(s):</label>
                            <input type="text" class="form-control form-control-solid" name="telefone" maxlength="60" data-detalhes-tel>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-12 col-xl-6 mb-3">
                            <label>Endereço:</label>
                            <input type="text" class="form-control form-control-solid" name="endereco" placeholder="Rua, setor, número..." maxlength="120" data-detalhes-endereco>
                        </div>
                        <div class="col-12 col-xl-6">
                            <label>Complemento:</label>
                            <input type="text" class="form-control form-control-solid" name="complemento" placeholder="Complemento do endereço..."  maxlength="120" data-detalhes-complemento>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-12 col-xl-6 mb-3">
                            <label>Ponto de Referência:</label>
                            <input type="text" class="form-control form-control-solid" name="ponto_referencia" placeholder="Próximo a..." maxlength="120" data-detalhes-pontoref>
                        </div>
                        <div class="col-12 col-xl-6">
                            <label>Bairro:</label>
                            <input type="text" class="form-control form-control-solid" name="bairro" placeholder=""  maxlength="30" data-detalhes-bairro>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-12 col-xl-6 mb-3">
                            <label>Cidade</label>
                            <input type="text" class="form-control form-control-solid" name="cidade" maxlength="30" data-detalhes-cidade>
                        </div>
                        <div class="col-12 col-xl-6">
                            <label>Estado</label>
                            <select class="form-control form-control-solid" name="estado" data-detalhes-estado>
                                <option value="">Escolha:</option>
                                <option value="AC">Acre</option>
                                <option value="AL">Alagoas</option>
                                <option value="AP">Amapá</option>
                                <option value="AM">Amazonas</option>
                                <option value="BA">Bahia</option>
                                <option value="CE">Ceará</option>
                                <option value="DF">Distrito Federal</option>
                                <option value="ES">Espírito Santo</option>
                                <option value="GO">Goiás</option>
                                <option value="MA">Maranhão</option>
                                <option value="MT">Mato Grosso</option>
                                <option value="MS">Mato Grosso do Sul</option>
                                <option value="MG">Minas Gerais</option>
                                <option value="PA">Pará</option>
                                <option value="PB">Paraíba</option>
                                <option value="PR">Paraná</option>
                                <option value="PE">Pernambuco</option>
                                <option value="PI">Piauí</option>
                                <option value="RJ">Rio de Janeiro</option>
                                <option value="RN">Rio Grande do Norte</option>
                                <option value="RS">Rio Grande do Sul</option>
                                <option value="RO">Rondônia</option>
                                <option value="RR">Roraima</option>
                                <option value="SC">Santa Catarina</option>
                                <option value="SP">São Paulo</option>
                                <option value="SE">Sergipe</option>
                                <option value="TO">Tocantins</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-12 col-xl-6 mb-3">
                            <label>CEP:</label>
                            <input type="text" class="form-control form-control-solid" name="cep" placeholder="Somente números" pattern="[0-9]{0,8}" maxlength="8" data-detalhes-cep>
                        </div>
                        <div class="col-12 col-xl-6">
                            <label>Estado Civil:</label>
                            <select class="form-control form-control-solid" name="estado_civil" data-detalhes-civil>
                                <option value="">Escolha:</option>
                                <option value="solteiro">Solteiro(a)</option>
                                <option value="casado">Casado(a)</option>
                                <option value="separado">Separado(a)</option>
                                <option value="divorciado">Divorciado(a)</option>
                                <option value="viuvo">Viúvo(a)</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Alergia(s):</label>
                        <textarea rows="3" cols="1" class="form-control form-control-solid" name="alergia" placeholder="Lista de alérgenos..." maxlength="255" data-detalhes-alergia></textarea>
                    </div>
                    <div class="form-group row">
                        <div class="col-12 col-xl-6 mb-3">
                            <label>Nome para contato de emergência</label>
                            <input type="text" class="form-control form-control-solid" name="emergencia_nome" placeholder="Nome para contato" maxlength="60" data-detalhes-emergencianome>
                        </div>
                        <div class="col-12 col-xl-6">
                            <label>Telefone para contato de emergência:</label>
                            <input type="text" class="form-control form-control-solid" name="emergencia_tel" placeholder="Telefone para contato"  maxlength="30" data-detalhes-emergenciatel>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-12 col-xl-6 mb-3">
                            <label>Taxa extra casal (R$):</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <div class="input-group-text form-control-solid">R$</div>
                                </div>
                                <input type="text" class="form-control form-control-solid" name="taxa_extra_casal" placeholder="" data-detalhes-taxaextracasal dobbin-validate-valor pattern="[0-9,]{0,10}">
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-12 col-xl-6 mb-3">
                            <label>Tipo sanguíneo:</label>
                            <select class="form-control form-control-solid" name="sangue" data-detalhes-sangue>
                                <option value="">Escolha:</option>
                                <option value="A+">A+</option>
                                <option value="A-">A-</option>
                                <option value="B+">B+</option>
                                <option value="B-">B-</option>
                                <option value="AB+">AB+</option>
                                <option value="AB-">AB-</option>
                                <option value="O+">O+</option>
                                <option value="O-">O-</option>

                            </select>
                        </div>
                        
                        <div class="col-12 col-xl-6 ">
                            <label>Dependente (?):</label>
                            <div class="d-flex">
                                <input type="text" class="form-control form-control-solid flex-grow-1" name="pseudo-dependente" disabled placeholder="" pattern="[0-9]{0,}" maxlength="30" data-detalhes-titular>
                                <button type="button" class="btn btn-sm form-control-solid ml-1" onclick="janClienteSelect($(this).siblings('[name|=\'pseudo\']'))"><i class="fas fa-search fa-fw"></i></button>
                                <button type="button" class="btn btn-sm form-control-solid ml-1" onclick="$(this).siblings('[name|=\'pseudo\']').val('').trigger('change')"><i class="fas fa-times fa-fw"></i></button>
                                <input type="hidden" name="dependente" data-detalhes-titular>
                            </div>
                            
                            <small class="text-muted">
                                Se esse for um usuário <strong>TITULAR</strong>, deixe em branco; se esse for um usuário <strong>DEPENDENTE</strong>, insira o código
                                do cliente TITULAR.
                            </small>
                        </div>

                        <!--
                        <div class="col-12 col-xl-6">
                            <label>Dependente (?):</label>
                            <input type="text" class="form-control form-control-solid" name="dependente" placeholder="" pattern="[0-9]{0,}" maxlength="30" data-detalhes-titular>
                            <small class="text-muted">
                                Se esse for um usuário <strong>TITULAR</strong>, deixe em branco; se esse for um usuário <strong>DEPENDENTE</strong>, insira o código
                                do cliente TITULAR.
                            </small>
                        </div>
                        -->
                    </div>


                    
                    
                    <div class="form-group">
                        <input type="hidden" name="id" data-detalhes-id value="">
                        <button type="submit" class="btn btn-success">Salvar</button>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light btn-sm" data-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalMinhaConta">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header py-2 px-3 font-weight-bold">
                Minha conta
                <button type="button" class="btn btn-sm btn-danger fechar" data-dismiss="modal"><strong>&times;</strong></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12 mb-2 d-flex flex-column flex-md-row">
                        <div class="mx-auto mx-md-0 mr-md-3 mb-2 mb-md-0 text-center">
                            <img src="media/images/av/user00.png"  height="70" data-minhaconta-avatar><br>
                            <a href="javascript:void(0)" data-minhaconta-id onclick="loadMinhaContaFotoAlterar()"><small>Alterar foto</small></a><br>
                            <a href="javascript:void(0)" onclick="delMinhaContaFoto()"><small>Excluir foto</small></a>
                        </div>
                        <h3 class="font-weight-bold mb-0">
                            <span data-minhaconta-nome></span>
                            <button type="button" class="btn btn-sm btn-success" data-minhaconta-id onclick="editaMinhaConta();"><i class="fas fa-pen"></i></button><br>
                            <small class="small font-weight-normal font-italic" data-minhaconta-usuario>@sandro_vitor</small><br>
                            <div class="d-flex flex-wrap mt-3">
                                <button type="button" class="btn btn-sm btn-secondary" onclick="alterarMinhaSenha()">Alterar senha</button>
                            </div>
                        </h3>
                        
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-12">
                        <strong class="mr-1">Email:</strong> <span data-minhaconta-email></span><br>
                        <strong class="mr-1">Usuário:</strong> <span data-minhaconta-usuario></span><br>
                        <strong class="mr-1">Nível de acesso:</strong> <span data-minhaconta-nivel></span>
                        <br><br>
                        <strong class="mr-1">Criado em:</strong> <span data-minhaconta-criado></span><br>
                        <strong class="mr-1">Atualizado em:</strong> <span data-minhaconta-atualizado></span><br>
                        <strong class="mr-1">Último login:</strong> <span data-minhaconta-logadoem></span>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-12">
                        <div class="alert alert-info">
                            <small><strong>A alteração não aparece?</strong> Atualize a página, ou <a href="/sair">faça login novamente</a>.</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light btn-sm" data-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalMinhaContaEditar">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header py-2 px-3 font-weight-bold">
                Editar minha conta
                <button type="button" class="btn btn-sm btn-danger fechar" data-dismiss="modal"><strong>&times;</strong></button>
            </div>
            <div class="modal-body">
                <form action="minhaconta/salvar" method="post" >
                    <div class="form-group row">
                        <div class="col-12 col-xl-6 mb-3">
                            <label>Nome</label>
                            <input type="text" class="form-control form-control-solid" name="nome" data-minhaconta-nome maxlength="30">
                        </div>
                        <div class="col-12 col-xl-6">
                            <label>Sobrenome</label>
                            <input type="text" class="form-control form-control-solid" name="sobrenome" data-minhaconta-sobrenome maxlength="30">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-12 col-xl-6 mb-3">
                            <label>Email:</label>
                            <input type="email" class="form-control form-control-solid" name="email" data-minhaconta-email placeholder="" maxlength="60">
                        </div>
                        <div class="col-12 col-xl-6">
                            <label>Nome de usuário:</label>
                            <input type="text" class="form-control form-control-solid" name="usuario" data-minhaconta-usuario maxlength="30">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-12 col-xl-6 mb-3">
                            <label>Nível:</label>
                            <select class="form-control form-control-solid" name="nivel" data-minhaconta-nivel>
                                <option value="1">Nível 1</option>
                                <option value="2">Nível 2</option>
                                <option value="3">Nível 3</option>
                                <option value="4">Nível 4</option>
                                <option value="5">Nível 5</option>
                                <option value="10">Nível 10</option>
                            </select>
                        </div>
                        <div class="col-12 col-xl-6">
                            
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" class="btn btn-success">Salvar</button>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light btn-sm" data-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalMinhaContaFotoAlterar">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header py-2 px-3 font-weight-bold">
                Alterar foto
                <button type="button" class="btn btn-sm btn-danger fechar" data-dismiss="modal"><strong>&times;</strong></button>
            </div>
            <div class="modal-body">
                <ul>
                    <li>Envie uma foto somente do rosto. Imagens de corpo não servirão para lhe identificar.</li>
                    <li>As imagens terão o tamanho 80x80. Envie foto de qualquer tamanho igual ou acima disso.</li>
                    
                </ul>
                <div class="row">
                    <div class="col-12 col-md-4 text-center mb-3 mb-md-0">
                        <strong>Foto atual:</strong><br><br>
                        <img src="" data-minhaconta-avatar height="80">

                    </div>
                    <div class="col-12 col-md-8  text-left">
                        <form action="minhaconta/avatar" method="post" data-manual onsubmit="return setMinhaContaFoto(this);" enctype="multipart/form-data">
                            <div class="form-group">
                                <label class="font-weight-bold">Escolha uma foto (JPG, PNG, BMP):</label>
                                <input type="file" name="avatar" class="form-control form-control-solid" accept="image/jpeg,image/bmp,image/png" max-size="2048" required>
                                <small class="text-muted">Tamanho máximo do arquivo deve ser de 2MB.</small>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">Salvar</button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <div class="retorno"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light btn-sm" data-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalUsuarioEditar">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header py-2 px-3 font-weight-bold">
                Editar dados do usuário
                <button type="button" class="btn btn-sm btn-danger fechar" data-dismiss="modal"><strong>&times;</strong></button>
            </div>
            <div class="modal-body">
                <form action="usuarios/salvar" method="post" >
                    <div class="form-group text-center">
                        <img src="" class="mb-2" height="70" data-usuario-avatar style="border-radius: 50%;">
                    </div>
                    <hr>
                    <div class="form-group row">
                        <div class="col-12 col-xl-6 mb-3">
                            <label>Nome</label>
                            <input type="text" class="form-control form-control-solid" name="nome" data-usuario-nome maxlength="30">
                        </div>
                        <div class="col-12 col-xl-6">
                            <label>Sobrenome</label>
                            <input type="text" class="form-control form-control-solid" name="sobrenome" data-usuario-sobrenome maxlength="30">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-12 col-xl-6 mb-3">
                            <label>Email:</label>
                            <input type="email" class="form-control form-control-solid" name="email" data-usuario-email placeholder="" maxlength="60">
                        </div>
                        <div class="col-12 col-xl-6">
                            <label>Nome de usuário:</label>
                            <input type="text" class="form-control form-control-solid" name="usuario" data-usuario-usuario maxlength="30">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-12 col-xl-6 mb-3">
                            <label>Nível:</label>
                            <select class="form-control form-control-solid" name="nivel" data-usuario-nivel>
                                <option value="1">Nível 1</option>
                                <option value="2">Nível 2</option>
                                <option value="3">Nível 3</option>
                                <option value="4">Nível 4</option>
                                <option value="5">Nível 5</option>
                                <option value="10">Nível 10</option>
                            </select>
                        </div>
                        <div class="col-12 col-xl-6">
                            
                        </div>
                    </div>


                    
                    
                    <div class="form-group">
                        <input type="hidden" name="id" value="" data-usuario-id>
                        <button type="submit" class="btn btn-success">Salvar</button>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light btn-sm" data-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalAlterarMinhaSenha">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header py-2 px-3 font-weight-bold">
                Trocar minha senha
                <button type="button" class="btn btn-sm btn-danger fechar" data-dismiss="modal"><strong>&times;</strong></button>
            </div>
            <div class="modal-body">
                <form action="minhaconta/alterarsenha">
                    <div class="form-group">
                        <label>Senha atual:</label>
                        <input type="password" name="senha_atual" class="form-control form-control-solid" autocomplete="off">
                    </div>
                    <hr>
                    <div class="form-group">
                        <label>Nova senha:</label>
                        <input type="password" name="senha1" dobbin-validate-password class="form-control form-control-solid" autocomplete="off">
                    </div>
                    <div class="alert alert-secondary">
                        <ul class="ml-2 pl-1">
                            <li>Sua senha deve possuir mínimo 8 e máximo de 16 caracteres;</li>
                            <li>Sua senha deve possuir pelo menos 1 número, 1 letra, 1 caractere especial;</li>
                            <li>Caracteres especiais permitidos: ($ # ! ? @ % & * - _ + = . , : ; [espaço] ).</li>
                        </ul>
                    </div>
                    <div class="form-group">
                        <label>Repita nova senha:</label>
                        <input type="password" name="senha2" dobbin-validate-password class="form-control form-control-solid" autocomplete="off">
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-success">Salvar</button>
                    </div>
                </form>
                <hr>
                <div class="alert alert-info">
                    <small>Sua senha valerá a partir do próximo login.</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light btn-sm" data-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalUsuarioDetalhes">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header py-2 px-3 font-weight-bold">
                Detalhes do usuário
                <button type="button" class="btn btn-sm btn-danger fechar" data-dismiss="modal"><strong>&times;</strong></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12 mb-2 d-flex flex-column flex-md-row">
                        <div class="mx-auto mx-md-0 mr-md-3 mb-2 mb-md-0 text-center">
                            <img src="media/images/av/user00.png"  height="70" style="border-radius:50%;" data-usuario-avatar>
                        </div>
                        <h3 class="font-weight-bold mb-0">
                            <span data-usuario-nome></span><br>
                            <small class="small font-weight-normal font-italic" data-usuario-usuario>@sandro_vitor</small><br>
                        </h3>
                        
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-12">
                        <strong class="mr-1">Email:</strong> <span data-usuario-email></span><br>
                        <strong class="mr-1">Usuário:</strong> <span data-usuario-usuario></span><br>
                        <strong class="mr-1">Nível de acesso:</strong> <span data-usuario-nivel></span>
                        <br><br>
                        <hr>
                        <div class="mb-1 px-2 py-1 border-secondary border bg-light">
                            <small><strong>Criado em:</strong></small><br>
                            <span data-usuario-criado></span>
                        </div>
                        <div class="mb-1 px-2 py-1 border-secondary border bg-light">
                            <small><strong>Atualizado em:</strong></small><br>
                            <span data-usuario-atualizado></span>
                        </div>
                        <div class="mb-1 px-2 py-1 border-secondary border bg-light">
                            <small><strong>Último login:</strong></small><br>
                            <span data-usuario-logadoem></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light btn-sm" data-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalCoordenadorDetalhes">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header py-2 px-3 font-weight-bold">
                Detalhes do coordenador
                <button type="button" class="btn btn-sm btn-danger fechar" data-dismiss="modal"><strong>&times;</strong></button>
            </div>
            <div class="modal-body">
                <h3 class="">
                    <i class="fas fa-user-tie"></i> 
                    <strong class="text-primary" data-detalhes-nome></strong> <small>[Cód.: <span data-detalhes-id></span>]</small>
                    <span class="ml-3" data-detalhes-deletado></span> <span class="ml-3 tipoCliente" style="cursor:pointer;"></span>
                </h3>
                <div class="mt-n1 d-flex">
                    <small class="badge badge-info mr-2 mb-1">Criado em: &nbsp; <span data-detalhes-criadoem></span></small>
                    <span class="badge badge-info mr-2 mb-1">Atualizado em: &nbsp; <span data-detalhes-atualizadoem></span></span>
                </div>
                <div class="listaDependentes pt-3" style="display:none">
                    
                </div>
                <hr class="mb-5">
                <h5 class="mt-3 mb-2 font-weight-bold">INFORMAÇÕES PESSOAIS</h5>
                <div class="d-flex flex-wrap">
                    <div class="py-2 px-3 hover border shadow-sm mr-2 mb-2">
                        <strong>Data de Nascimento:</strong><br><span data-detalhes-nascimento></span>
                    </div>
                    <div class="py-2 px-3 hover border shadow-sm mr-2 mb-2">
                        <strong>RG:</strong><br><span data-detalhes-rg></span>
                    </div>
                    <div class="py-2 px-3 hover border shadow-sm mr-2 mb-2">
                        <strong>CPF:</strong><br><span data-detalhes-cpf></span>
                    </div>
                    <div class="py-2 px-3 hover border shadow-sm mr-2 mb-2">
                        <strong>Estado Civil:</strong><br><span data-detalhes-civil></span>
                    </div>
                    <div class="py-2 px-3 hover border shadow-sm mr-2 mb-2">
                        <strong>Email:</strong><br><span data-detalhes-email></span>
                    </div>
                    <div class="py-2 px-3 hover border shadow-sm mr-2 mb-2">
                        <strong>Telefone:</strong><br><span data-detalhes-tel></span>
                    </div>
                </div>
                <hr>

                <h5 class="mt-3 mb-2 font-weight-bold">ENDEREÇO</h5>
                <div class="d-flex flex-wrap">
                    <div class="py-2 px-3 hover border shadow-sm mr-2 mb-2">
                        <strong>Endereço:</strong><br><span data-detalhes-endereco></span>
                    </div>
                    <div class="py-2 px-3 hover border shadow-sm mr-2 mb-2">
                        <strong>Complemento:</strong><br><span data-detalhes-complemento></span>
                    </div>
                    <div class="py-2 px-3 hover border shadow-sm mr-2 mb-2">
                        <strong>Ponto Referência:</strong><br><span data-detalhes-pontoref></span>
                    </div>
                    <div class="py-2 px-3 hover border shadow-sm mr-2 mb-2">
                        <strong>CEP:</strong><br><span data-detalhes-cep></span>
                    </div>
                    <div class="py-2 px-3 hover border shadow-sm mr-2 mb-2">
                        <strong>Bairro:</strong><br><span data-detalhes-bairro></span>
                    </div>
                    <div class="py-2 px-3 hover border shadow-sm mr-2 mb-2">
                        <strong>Cidade:</strong><br><span data-detalhes-cidade></span>
                    </div>
                    <div class="py-2 px-3 hover border shadow-sm mr-2 mb-2">
                        <strong>Estado:</strong><br><span data-detalhes-estado></span>
                    </div>
                </div>
                <hr>

                <h5 class="mt-3 mb-2 font-weight-bold">SAÚDE E EMERGÊNCIA</h5>
                <div class="d-flex flex-wrap">
                    <div class="py-2 px-3 hover border shadow-sm mr-2 mb-2">
                        <strong>Tipo Sanguíneo:</strong><br><span data-detalhes-sangue></span>
                    </div>
                    <div class="py-2 px-3 hover border shadow-sm mr-2 mb-2">
                        <strong>Alergias:</strong><br><span data-detalhes-alergia></span>
                    </div>
                    <div class="py-2 px-3 hover border shadow-sm mr-2 mb-2">
                        <strong>Contato de emergência:</strong><br><span data-detalhes-emergencianome></span>
                    </div>
                    <div class="py-2 px-3 hover border shadow-sm mr-2 mb-2">
                        <strong>Telefone de emergência:</strong><br><span data-detalhes-emergenciatel></span>
                    </div>
                </div>
                <hr>

                <h5 class="mt-3 mb-2 font-weight-bold">OUTRAS INFORMAÇÕES</h5>
                <div class="d-flex flex-wrap">
                    <div class="py-2 px-3 hover border shadow-sm mr-2 mb-2">
                        <strong>Criado em:</strong><br><span data-detalhes-criadoem></span>
                    </div>
                    <div class="py-2 px-3 hover border shadow-sm mr-2 mb-2">
                        <strong>Atualizado em:</strong><br><span data-detalhes-atualizadoem></span>
                    </div>
                </div>


            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light btn-sm" data-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalCoordenadorEditar">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header py-2 px-3 font-weight-bold">
                Editar dados do coordenador
                <button type="button" class="btn btn-sm btn-danger fechar" data-dismiss="modal"><strong>&times;</strong></button>
            </div>
            <div class="modal-body">
                <h3 class="">
                    <strong class="text-primary" data-detalhes-nome></strong> <small>[Cód.: <span data-detalhes-id></span>]</small>
                </h3>
                <hr>
                <form action="coordenadores/salvar" method="post">
                    <div class="form-group row">
                        <div class="col-12 col-xl-6 mb-3">
                            <label>Nome:</label>
                            <input type="text" class="form-control form-control-solid" name="nome" maxlength="30" data-detalhes-nome>
                        </div>
                        <div class="col-12 col-xl-6">
                            <label>Data de Nascimento:</label>
                            <input type="date" class="form-control form-control-solid" name="nascimento" maxlength="30" data-detalhes-nascimento>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-12 col-xl-6 mb-3">
                            <label>RG:</label>
                            <input type="text" class="form-control form-control-solid" name="rg" placeholder="Insira somente os números." pattern="[0-9]{0,10}" data-detalhes-rg data-validate-rg maxlength="10">
                        </div>
                        <div class="col-12 col-xl-6">
                            <label>CPF:</label>
                            <input type="text" class="form-control form-control-solid" name="cpf" placeholder="Insira somente os números." pattern="[0-9]{0,11}" data-detalhes-cpf data-validate-cpf maxlength="11">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-12 col-xl-6 mb-3">
                            <label>Email:</label>
                            <input type="email" class="form-control form-control-solid" name="email" placeholder="" maxlength="60" data-detalhes-email>
                        </div>
                        <div class="col-12 col-xl-6">
                            <label>Telefone(s):</label>
                            <input type="text" class="form-control form-control-solid" name="telefone" maxlength="60" data-detalhes-tel>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-12 col-xl-6 mb-3">
                            <label>Endereço:</label>
                            <input type="text" class="form-control form-control-solid" name="endereco" placeholder="Rua, setor, número..." maxlength="120" data-detalhes-endereco>
                        </div>
                        <div class="col-12 col-xl-6">
                            <label>Complemento:</label>
                            <input type="text" class="form-control form-control-solid" name="complemento" placeholder="Complemento do endereço..."  maxlength="120" data-detalhes-complemento>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-12 col-xl-6 mb-3">
                            <label>Ponto de Referência:</label>
                            <input type="text" class="form-control form-control-solid" name="ponto_referencia" placeholder="Próximo a..." maxlength="120" data-detalhes-pontoref>
                        </div>
                        <div class="col-12 col-xl-6">
                            <label>Bairro:</label>
                            <input type="text" class="form-control form-control-solid" name="bairro" placeholder=""  maxlength="30" data-detalhes-bairro>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-12 col-xl-6 mb-3">
                            <label>Cidade</label>
                            <input type="text" class="form-control form-control-solid" name="cidade" maxlength="30" data-detalhes-cidade>
                        </div>
                        <div class="col-12 col-xl-6">
                            <label>Estado</label>
                            <select class="form-control form-control-solid" name="estado" data-detalhes-estado>
                                <option value="">Escolha:</option>
                                <option value="AC">Acre</option>
                                <option value="AL">Alagoas</option>
                                <option value="AP">Amapá</option>
                                <option value="AM">Amazonas</option>
                                <option value="BA">Bahia</option>
                                <option value="CE">Ceará</option>
                                <option value="DF">Distrito Federal</option>
                                <option value="ES">Espírito Santo</option>
                                <option value="GO">Goiás</option>
                                <option value="MA">Maranhão</option>
                                <option value="MT">Mato Grosso</option>
                                <option value="MS">Mato Grosso do Sul</option>
                                <option value="MG">Minas Gerais</option>
                                <option value="PA">Pará</option>
                                <option value="PB">Paraíba</option>
                                <option value="PR">Paraná</option>
                                <option value="PE">Pernambuco</option>
                                <option value="PI">Piauí</option>
                                <option value="RJ">Rio de Janeiro</option>
                                <option value="RN">Rio Grande do Norte</option>
                                <option value="RS">Rio Grande do Sul</option>
                                <option value="RO">Rondônia</option>
                                <option value="RR">Roraima</option>
                                <option value="SC">Santa Catarina</option>
                                <option value="SP">São Paulo</option>
                                <option value="SE">Sergipe</option>
                                <option value="TO">Tocantins</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-12 col-xl-6 mb-3">
                            <label>CEP:</label>
                            <input type="text" class="form-control form-control-solid" name="cep" placeholder="Somente números" pattern="[0-9]{0,8}" maxlength="8" data-detalhes-cep>
                        </div>
                        <div class="col-12 col-xl-6">
                            <label>Estado Civil:</label>
                            <select class="form-control form-control-solid" name="estado_civil" data-detalhes-civil>
                                <option value="">Escolha:</option>
                                <option value="solteiro">Solteiro(a)</option>
                                <option value="casado">Casado(a)</option>
                                <option value="separado">Separado(a)</option>
                                <option value="divorciado">Divorciado(a)</option>
                                <option value="viuvo">Viúvo(a)</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Alergia(s):</label>
                        <textarea rows="3" cols="1" class="form-control form-control-solid" name="alergia" placeholder="Lista de alérgenos..." maxlength="255" data-detalhes-alergia></textarea>
                    </div>
                    <div class="form-group row">
                        <div class="col-12 col-xl-6 mb-3">
                            <label>Nome para contato de emergência</label>
                            <input type="text" class="form-control form-control-solid" name="emergencia_nome" placeholder="Nome para contato" maxlength="60" data-detalhes-emergencianome>
                        </div>
                        <div class="col-12 col-xl-6">
                            <label>Telefone para contato de emergência:</label>
                            <input type="text" class="form-control form-control-solid" name="emergencia_tel" placeholder="Telefone para contato"  maxlength="30" data-detalhes-emergenciatel>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-12 col-xl-6 mb-3">
                            <label>Tipo sanguíneo:</label>
                            <select class="form-control form-control-solid" name="sangue" data-detalhes-sangue>
                                <option value="">Escolha:</option>
                                <option value="A+">A+</option>
                                <option value="A-">A-</option>
                                <option value="B+">B+</option>
                                <option value="B-">B-</option>
                                <option value="AB+">AB+</option>
                                <option value="AB-">AB-</option>
                                <option value="O+">O+</option>
                                <option value="O-">O-</option>

                            </select>
                        </div>

                        <!--
                        <div class="col-12 col-xl-6">
                            <label>Dependente (?):</label>
                            <input type="text" class="form-control form-control-solid" name="dependente" placeholder="" pattern="[0-9]{0,}" maxlength="30" data-detalhes-titular>
                            <small class="text-muted">
                                Se esse for um usuário <strong>TITULAR</strong>, deixe em branco; se esse for um usuário <strong>DEPENDENTE</strong>, insira o código
                                do cliente TITULAR.
                            </small>
                        </div>
                        -->
                    </div>


                    
                    
                    <div class="form-group">
                        <input type="hidden" name="id" data-detalhes-id value="">
                        <button type="submit" class="btn btn-success">Salvar</button>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light btn-sm" data-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEstornarVenda">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header py-2 px-3 font-weight-bold">
                Estornar Venda
                <button type="button" class="btn btn-sm btn-danger fechar" data-dismiss="modal"><strong>&times;</strong></button>
            </div>
            <div class="modal-body">
                <h6></h6>
                <form>
                    <div class="form-group">
                        <label>Valor a ser devolvido</label>
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend">
                                <div class="input-group-text ">R$</div>
                            </div>
                            <input type="text" class="form-control form-control-sm" name="valor_devolvido" dobbin-mask-money max="">
                            <input type="hidden" name="id" value="">
                        </div>
                    </div>
                    <div class="form-group text-right">
                        <button type="button" class="btn btn-sm btn-primary" onclick="vendaConfirmarEstorno(this)" disabled>Confirmar estorno</button>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light btn-sm" data-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header py-2 px-3 font-weight-bold">
                Titulo do modal
                <button type="button" class="btn btn-sm btn-danger fechar" data-dismiss="modal"><strong>&times;</strong></button>
            </div>
            <div class="modal-body">
            
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light btn-sm" data-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header py-2 px-3 font-weight-bold">
                Titulo do modal
                <button type="button" class="btn btn-sm btn-danger fechar" data-dismiss="modal"><strong>&times;</strong></button>
            </div>
            <div class="modal-body">
            
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light btn-sm" data-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

    <!-- ./MODAIS-->

    <!-- JANELA SUSPENSA -->
<div class="modal fade" id="janClienteSelect">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header py-2 px-3 font-weight-bold bg-dark text-white">
                Escolha o cliente
                <button type="button" class="btn btn-sm btn-danger fechar" data-dismiss="modal"><strong>&times;</strong></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="form-group">
                        <input type="text" class="form-control form-control-sm form-control-solid" name="busca" placeholder="Digite para buscar...">
                        <div class="text-muted small mt-2"> </div>
                    </div>
                    <hr>

                    <div class="text-right">
                        <button type="button" class="btn btn-sm btn-primary" data-dismiss="modal" data-selecionar disabled onclick="$($(this).data('target')).val($('#janClienteSelect table.table-selectable ').find('tr.selecionado').data('id')).trigger('change');">Selecionar</button>
                        <button type="button" class="btn btn-sm btn-dark" data-dismiss="modal">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="janParceirosSelect">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header py-2 px-3 font-weight-bold bg-dark text-white">
                Escolha o parceiro
                <button type="button" class="btn btn-sm btn-danger fechar" data-dismiss="modal"><strong>&times;</strong></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="form-group">
                        <input type="text" class="form-control form-control-sm form-control-solid" name="busca" placeholder="Digite para buscar...">
                        <div class="text-muted small mt-2"> </div>
                    </div>
                    <hr>

                    <div class="text-right">
                        <button type="button" class="btn btn-sm btn-primary" data-selecionar disabled onclick="roteiroAddParceiroNovoRoteiro($('#janParceirosSelect table.table-selectable ').find('tr.selecionado'));">Selecionar</button>
                        <button type="button" class="btn btn-sm btn-dark" data-dismiss="modal">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="janCoordenadorSelect">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header py-2 px-3 font-weight-bold bg-dark text-white">
                Escolha o coordenador
                <button type="button" class="btn btn-sm btn-danger fechar" data-dismiss="modal"><strong>&times;</strong></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="form-group">
                        <input type="text" class="form-control form-control-sm form-control-solid" name="busca" placeholder="Digite para buscar...">
                        <div class="text-muted small mt-2"> </div>
                    </div>
                    <hr>

                    <div class="text-right">
                        <input type="hidden" name="rid" value="">
                        <button type="button" class="btn btn-sm btn-primary" data-selecionar disabled onclick="roteiroAddCoordenador(this)">Selecionar</button>
                        <button type="button" class="btn btn-sm btn-dark" data-dismiss="modal">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="janRoteirosSelect">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header py-2 px-3 font-weight-bold bg-dark text-white">
                Escolha o roteiro
                <button type="button" class="btn btn-sm btn-danger fechar" data-dismiss="modal"><strong>&times;</strong></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="form-group">
                        <input type="text" class="form-control form-control-sm form-control-solid" name="busca" placeholder="Digite para buscar...">
                        <div class="text-muted small mt-2"> </div>
                        <div class="alert alert-info small">
                            <strong>DICA:</strong> Pesquise DATA ou NOME (nunca os dois juntos);<br>
                            <strong>DICA:</strong>Para pesquisar data, use o formato AAAA-MM-DD. Exemplo: 2020-06-01 (01/06/2020).
                        </div>
                    </div>
                    <hr>

                    <div class="text-right">
                        <button type="button" class="btn btn-sm btn-primary" data-dismiss="modal" data-selecionar data-target="" disabled onclick="$($(this).data('target')).val($('#janRoteirosSelect table.table-selectable ').find('tr.selecionado').data('id')).trigger('change');">Selecionar</button>
                        <button type="button" class="btn btn-sm btn-dark" data-dismiss="modal">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header py-2 px-3 font-weight-bold">
                Titulo do modal
                <button type="button" class="btn btn-sm btn-danger fechar" data-dismiss="modal"><strong>&times;</strong></button>
            </div>
            <div class="modal-body">
            
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light btn-sm" data-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header py-2 px-3 font-weight-bold">
                Titulo do modal
                <button type="button" class="btn btn-sm btn-danger fechar" data-dismiss="modal"><strong>&times;</strong></button>
            </div>
            <div class="modal-body">
            
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light btn-sm" data-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>
    <!-- ./JANELA SUSPENSA -->

    <!-- JANELA SUSPENSA DINÂMICA -->
<div class="modal fade" id="janDinamica">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header py-2 px-3 font-weight-bold">
                <span class="tituloModal">Janela suspensa dinâmica</span>
                <button type="button" class="btn btn-sm btn-danger fechar" data-dismiss="modal"><strong>&times;</strong></button>
            </div>
            <div class="modal-body">
                TESTE DE JANELA DINÂMICA
            </div>
        </div>
    </div>
</div>
    <!-- ./JANELA SUSPENSA DINÂMICA -->


    <!-- SPLASH SCREEN -->
<div id="splash-screen" style="display:none;">
    <div class="text-center">
        <img class="mx-5 my-2" src="/media/images/logo128ib.png"><br>
        Conversando com o servidor...
    </div>
    
</div>
    <!-- ./SPLASH SCREEN -->

    <!-- SCRIPT -->
    <!--
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>-->

    <!-- SCRIPT OFFLINE -->
    <script src="/js/jquery-3.3.1.min.js"></script>
    <script src="/js/popper.min.js"></script>
    <script src="/js/bootstrap-4.3.1.min.js"></script>
    <script src="/js/alert.min.js?v{{$sistema->version}}"></script>
    <script src="/js/scripts.min.js?v{{$sistema->version}}"></script>
    <script src="/js/dobbinNative.min.js?v{{$sistema->version}}"></script>
</body>