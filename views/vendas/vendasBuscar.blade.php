<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                Buscar Vendas
            </div>
            <div class="card-body">
                <form action="" method="post" data-manual id="buscarVendas">
                    <div class="form-group">
                        <label class="font-weight-bold">Buscar</label>
                        <input type="text" class="form-control form-control-solid" name="busca" placeholder="Deixe em branco para retornar todos"> 
                        <div class="alert alert-info small py-1 px-2">
                            - Serão retornados 400 resultados no máximo;<br>
                            - Itens que serão pesquisados: NOME DO CLIENTE, ROTEIRO, DATA_RESERVA, DATA_VENDA, DATA_PAGAMENTO, DATA_CANCELADO, DATA_ESTORNO e STATUS/SITUAÇÃO;<br>
                            - Somente a data da reserva será exibida em primeira instância, para as demais datas consultar os detalhes da venda;<br>
                            - Para buscar vendas pela situação, use somente palavras como <a href="javascript:void(0)" onclick="$(this).parents('form').find('[name=\'busca\']').val('Aguardando')">Aguardando</a>,
                            <a href="javascript:void(0)" onclick="$(this).parents('form').find('[name=\'busca\']').val('Reserva')">Reserva</a>,
                            <a href="javascript:void(0)" onclick="$(this).parents('form').find('[name=\'busca\']').val('Pagando')">Paga</a>,
                            <a href="javascript:void(0)" onclick="$(this).parents('form').find('[name=\'busca\']').val('Paga')">Paga</a>,
                            <a href="javascript:void(0)" onclick="$(this).parents('form').find('[name=\'busca\']').val('Cancelada')">Cancelada</a>,
                            <a href="javascript:void(0)" onclick="$(this).parents('form').find('[name=\'busca\']').val('Devolvida')">Devolvida</a>.
                        </div>
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
