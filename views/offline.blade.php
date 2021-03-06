<div class="row">
    <div class="col-12 col-md-12 col-lg-10 col-xl-8 mx-auto">
        <div class="card">
            <div class="card-header">Acesso offline</div>
            <div class="card-body">
                O acesso offline permite que você use a plataforma de forma limitada em um computador.
                <br><br>
                <div class="row">
                    <div class="col-12 ">

                        Faça o download do arquivo HTML para acessar a plataforma offline.<br>
                        <a href="/offlineclient" class="btn btn-primary btn-block">Baixar arquivo offline </a>
                        <small class="text-muted">[O tamanho do arquivo varia com a quantidade de clientes. Se você tiver cerca de 1000 clientes, o arquivo deve ter um tamanho aproximado de <b>1,3MB</b>.]</small>
                        <br><br>
                        <hr>
                        <h6 class="font-weight-bold">OBSERVAÇÕES</h6>
                        <ol>
                            <li>Todos os dados necessários estarão disponíveis no arquivo;</li>
                            <li><strong>Tenha cuidado com ele!</strong> Assim que ele não for mais necessário, exclua-o;</li>
                            <li>Depois que concluir o download, abra o arquivo HTML com o navegador de sua preferência (recomendamos o Google Chrome, e desaconselhamos o Internet Explorer);</li>
                            <li>Nenhuma mudança pode ser realizada offline. Se for necessário corrigir qualquer informação, SOMENTE será realizada online, através dessa plataforma;</li>
                        </ol>
                         
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function gerarNovoToken()
    {
        $.post(PREFIX_POST+'genauthcode', function(res){
            console.log(res);
            if(res.success == false) {
                alerta(res.mensagem, 'Erro: ', 'warning');
                return false;
            } else {
                $('#tokenValue').text(res.token.substr(0,15)+'-'+res.token.substr(16,47)+'-'+res.token.substr(48));
            }
        }, 'json')
        .fail(function(ev){
            alerta('Falha ao recuperar o token. Informe o desenvolvedor...', 'Falha!', 'danger');
            console.log('Falha ao recuperar o token. Informe o desenvolvedor...');
            console.log(ev);
        });
    }
</script>