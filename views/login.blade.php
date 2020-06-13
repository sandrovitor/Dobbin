<!DOCTYPE HTML>
<html lang="pt-br">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta content="width=device-width, initial-scale=1, maximum-scale=1" name="viewport">
	<meta name="author" content="Sandro Mendonça">
    <meta name="robots" content="noindex,nofollow,noarchive">
	<meta name="description" content="{{$sistema->description}}">
	<title>Login do {{$sistema->name}} </title>
    <!--<link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet" type="text/css">-->
    <link href="/css/bootstrap-4.3.1.min.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css" integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous">
    <link rel="stylesheet" href="/css/geral.css">
    <link rel="stylesheet" href="/css/alert.css">
    <link rel="icon" href="/media/images/logo64i.png">
    
</head>

<body class="bodyLogin">
    <div class="container">
        <div class="row">
            <div class="col-12 col-md-5 col-lg-5 mx-auto pt-5">
                @if(isset($_SESSION['mensagem']) && $_SESSION['mensagem'] != '')
                <div class="alert alert-info mt-n4 px-2 py-1">
                    <small><i class="fas fa-exclamation-circle mr-2"></i> {!!$_SESSION['mensagem']!!}</small>
                </div>
                @php
                    unset($_SESSION['mensagem']);
                @endphp
                @endif
                <div class="card">
                    <div class="card-header py-4">
                        <h2 class="text-center font-weight-bold"><img src="/media/images/logo64.png" class="mx-3 my-2" alt="{{$sistema->name}} logo">
                        </h2>
                        <h4 class="text-center text-dark">Login</h4>
                    </div>
                    <div class="card-body">
                        <form action="/login" method="post">
                            <div class="form-group">
                                <label>E-mail ou usuário</label>
                                <input type="text" class="form-control" name="usuario">
                            </div>
                            <div class="form-group">
                                <label>Senha</label>
                                <input type="password" class="form-control " name="senha">
                            </div>
                            <div class="form-group text-right">
                                <input type="hidden" name="csrf" value="{{$csrf}}">
                                <button type="submit" class="btn btn-primary">Login</button>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer text-center">
                        <small>Esqueceu a senha? Informe a seu superior.</small>
                    </div>
                </div>
                <div class="text-center">
                    <small class="text-light">Copyright &copy; {{date('Y')}} Sandro Mendonça</small>
                </div>
            </div>
        </div>
    </div>

    <!-- SCRIPT OFFLINE -->
    <script src="/js/jquery-3.3.1.min.js"></script>
    <script src="/js/popper.min.js"></script>
    <script src="/js/bootstrap-4.3.1.min.js"></script>
    <!-- SCRIPT -->
    <!--
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>-->

    
    <script src="/js/alert.min.js"></script>
    <script>
    $(document).ready(function(){
        $(document).on('submit', 'form', function(){
            setTimeout(function(){location.reload();}, 10000);
        });
    });
    </script>
</body>
</html>