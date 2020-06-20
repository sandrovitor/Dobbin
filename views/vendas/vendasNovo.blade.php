<div class="row">
    <div class="col-12 col-lg-10 col-xl-8 mx-auto">
        <div class="card">
            <form>
                <div class="card-body font-monospace">
                    <div class="row">
                        <div class="col-12 mb-2">
                            <label class="font-weight-bold">Cliente</label>
                            <div class="input-group input-group-sm">
                                <input type="text" class="form-control-solid form-control form-control-sm" name="cliente">
                                <div class="input-group-append">
                                    <button type="button" class="btn-secondary btn btn-sm px-2" data-toggle="tooltip" title="Localizar cliente" onclick=""><i class="fas fa-search fa-fw"></i></button>
                                    <button type="button" class="btn-primary btn btn-sm px-2" data-toggle="tooltip" title="Criar novo cliente" onclick="location.hash = '#clientes/novo'; loadLanding(location.hash)"><i class="fas fa-plus fa-fw"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-12 col-lg-7 mb-2">
                            <label class="font-weight-bold">Roteiro</label>
                            <div class="input-group input-group-sm">
                                <input type="text" class="form-control-solid form-control form-control-sm" name="roteiro">
                                <div class="input-group-append">
                                    <button type="button" class="btn-secondary btn btn-sm px-2" onclick=""><i class="fas fa-search fa-fw"></i></button>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-lg-2 mb-2">
                            <label class="font-weight-bold">Disponível</label>
                            <input type="number" class="form-control form-control-sm form-control-solid" name="estoque">
                        </div>
                        <div class="col-12 col-lg-3 mb-2">
                            <label class="font-weight-bold">Tarifas</label>
                            <select class="form-control form-control-sm form-control-solid" name="tarifa">
                            
                            </select>
                        </div>
                    </div>

                    
                    <div class="d-flex flex-column flex-md-row align-items-end mb-2">
                        <div class="mr-2">
                            <label class="font-weight-bold">Quantidade</label>
                            <input type="number" class="form-control-solid form-control form-control-sm" min="1" value="1" max="100" name="qtd">
                        </div>
                        <div class="mr-2">
                            <label class="font-weight-bold">Valor Unitário</label>
                            <div class="input-group input-group-sm">
                                <div class="input-group-prepend">
                                    <div class="input-group-text form-control-solid">R$</div>
                                </div>
                                <input type="text" class="form-control-solid form-control form-control-sm" name="valor_unitario">
                            </div>
                        </div>
                        <div class="mr-2">
                            <label class="font-weight-bold">Desconto Unitário</label>
                            <div class="input-group input-group-sm">
                                <div class="input-group-prepend">
                                    <div class="input-group-text form-control-solid">R$</div>
                                </div>
                                <input type="text" class="form-control-solid form-control form-control-sm" name="valor_unitario">
                            </div>
                        </div>
                        <div class="mr-2">
                            <label class="font-weight-bold">Subtotal</label>
                            <div class="input-group input-group-sm">
                                <div class="input-group-prepend">
                                    <div class="input-group-text form-control-solid">R$</div>
                                </div>
                                <input type="text" class="form-control-solid form-control form-control-sm" name="valor_unitario">
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-primary">Adicionar</button>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-12 mb-2">
                            <div class="border rounded-0">
                                <table class="table table-sm table-bordered">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>Cód.</th>
                                            <th>Roteiro</th>
                                            <th>Qtd</th>
                                            <th>Tarifa</th>
                                            <th>Valor</th>
                                            <th>Subtotal</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>