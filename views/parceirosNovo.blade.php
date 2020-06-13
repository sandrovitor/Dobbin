<div class="row">
    <div class="col-12 col-md-8 col-lg-8 mx-auto">
        <div class="card">
            <div class="card-header">
                Nova parceria (empresa ou pessoa física)
            </div>
            <div class="card-body">
                <form action="parceiros/novo" method="post" data-manual="true" onsubmit="return novoParceiro($(this));">
                    <h6 class="font-weight-bold mb-3">DADOS GERAIS</h6>
                    <div class="form-group row">
                        <div class="col-12 col-lg-6 mb-2">
                            <label class="small">Razão Social (empresa) / Nome completo (pessoa física)</label>
                            <input type="text" name="razao_social" class="form-control form-control-solid form-control-sm" maxlength="70" onblur="if($(this).val() != '') { $('[name=\'favorecido\']').val($(this).val());}">
                        </div>
                        <div class="col-12 col-lg-6 mb-2">
                            <label class="small">Nome Fantasia (empresa)</label>
                            <input type="text" name="fantasia" class="form-control form-control-solid form-control-sm" maxlength="70">
                        
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-4 mb-2">
                            <label class="small">Tipo</label>
                            <select name="doc_tipo" class="form-control form-control-solid form-control-sm">
                                <option value="CNPJ">CNPJ</option>
                                <option value="CPF">CPF</option>
                            </select>
                        </div>
                        <div class="col-8 mb-2">
                            <label class="small">Número do documento</label>
                            <input type="text" name="doc_numero" class="form-control form-control-solid form-control-sm" placeholder="Somente números..." pattern="[0-9]{0,}" maxlength="14">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-12 col-lg-6 mb-2">
                            <label class="small">Responsável (empresa)</label>
                            <input type="text" name="responsavel" class="form-control form-control-solid form-control-sm" maxlength="70">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-12 col-lg-6 mb-2">
                            <label class="small">Endereço completo</label>
                            <input type="text" name="endereco" class="form-control form-control-solid form-control-sm" maxlength="200">
                        </div>
                        <div class="col-8 col-lg-4 mb-2">
                            <label class="small">Cidade</label>
                            <input type="text" name="cidade" class="form-control form-control-solid form-control-sm" maxlength="30">
                        </div>
                        <div class="col-4 col-lg-2 mb-2">
                            <label class="small">Estado</label>
                            <select name="estado" class="form-control form-control-solid form-control-sm">
                                <option value="">Escolha:</option>
                                <option value="AC">Acre</option>
                                <option value="AL">Alagoas</option>
                                <option value="AP">Amapá</option>
                                <option value="AM">Amazonas</option>
                                <option value="BA" selected>Bahia</option>
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
                    <hr>
                    <h6 class="font-weight-bold mb-3">DADOS FINANCEIROS</h6>
                    <div class="form-group row">
                        <div class="col-12 col-lg-6 mb-2">
                            <label class="small">Banco</label>
                            <select name="banco" class="form-control form-control-solid form-control-sm">
                                <option value="">Escolha</option>
                                @foreach($bancos as $b)
                                <option value="{{$b->codigo}}">{{$b->codigo}} - {{$b->banco}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-lg-6 mb-2">
                            <label class="small">Favorecido</label>
                            <input type="text" name="favorecido" class="form-control form-control-solid form-control-sm" maxlength="70">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-8 col-lg-4 mb-2">
                            <label class="small">Agência</label>
                            <input type="text" name="agencia" class="form-control form-control-solid form-control-sm" pattern="[0-9]{0,}" maxlength="10">
                        </div>
                        <div class="col-4 col-lg-2 mb-2">
                            <label class="small">DV</label>
                            <input type="text" name="agencia_dv" class="form-control form-control-solid form-control-sm" pattern="[0-9]{0,1}" maxlength="1">
                        </div>
                        <div class="col-8 col-lg-4 mb-2">
                            <label class="small">Conta</label>
                            <input type="text" name="conta" class="form-control form-control-solid form-control-sm" pattern="[0-9]{0,}" maxlength="10">
                        </div>
                        <div class="col-4 col-lg-2 mb-2">
                            <label class="small">DV</label>
                            <input type="text" name="conta_dv" class="form-control form-control-solid form-control-sm" pattern="[0-9]{0,1}" maxlength="1">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-12 col-lg-6 mb-2">
                            <label class="small">Tipo de conta</label>
                            <select name="tipo_conta" class="form-control form-control-solid form-control-sm">
                                <option value="CORRENTE">Conta Corrente</option>
                                <option value="POUPANÇA">Conta Poupança</option>
                                <option value="SALÁRIO">Conta-Salário</option>
                                <option value="DIGITAL">Conta Digital</option>
                                <option value="PAGAMENTOS">Conta de Pagamentos</option>
                            </select>
                        </div>
                        <div class="col-12 col-lg-6 mb-2">
                            <label class="small">OBSERVAÇÕES FINANCEIRAS</label>
                            <textarea rows="3" cols="" name="obs_financeiro" class="form-control form-control-solid form-control-sm" maxlength="300"></textarea>
                        </div>
                    </div>
                    <div class="form-group text-right">
                        <button type="submit" class="btn btn-success btn-sm">Salvar</button>
                        <a href="#parceiros" class="btn btn-light btn-sm">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>