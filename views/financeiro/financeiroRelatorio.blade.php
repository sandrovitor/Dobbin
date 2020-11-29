<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body px-3 py-3">
                <form class="d-flex border p-2 bg-light">
                    <div class="mr-3">
                        <div class="form-group mr-3">
                            <label class="font-weight-bold">Inicio</label>
                            <input type="month" name="inicio" class="form-control form-control-sm">
                        </div>
                        <div class="form-group mr-3">
                            <label class="font-weight-bold">Fim</label>
                            <input type="month" name="fim" class="form-control form-control-sm">
                        </div>
                    </div>
                    <div class="form-group mr-3">
                        <label class="font-weight-bold">Visões</label>
                        <div class="">
                            <div class="form-check mr-2">
                                <label class="form-check-label">
                                    <input type="checkbox" class="form-check-input" data-visao value="1001" checked> Dados do período
                                </label>
                            </div>
                            <div class="form-check mr-2">
                                <label class="form-check-label">
                                    <input type="checkbox" class="form-check-input" data-visao value="1002" checked> Progressão Financeira
                                </label>
                            </div>
                            <div class="form-check mr-2">
                                <label class="form-check-label">
                                    <input type="checkbox" class="form-check-input" data-visao value="1004" checked> Despesa p/ categoria
                                </label>
                            </div>
                            <div class="form-check mr-2">
                                <label class="form-check-label">
                                    <input type="checkbox" class="form-check-input" data-visao value="1008" checked> Vendas e Reservas
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group mr-3 align-self-center">
                        <button type="button" class="btn btn-primary" onclick="gerarRelatorio()">Gerar relatório</button>
                    </div>
                </form>
                <small>
                    * O relatório é gerado com base nas folhas/balanço dos meses. Se não houver o balanço de um mês específico no sistema, aquele mês aparecerá com alguns dados zerados.<br>
                    * Limite de meses no relatório: {{DOBBIN_LIM_QTD_MES_BALANCO_TOTAL}} meses. O máximo é {{DOBBIN_LIM_QTD_MES_BALANCO_TOTAL_MAX}} meses (consulte disponibilidade para aumento).
                </small>
                <hr>
                <div id="relatorioResultado"></div>
            </div>
        </div>
    </div>
</div>