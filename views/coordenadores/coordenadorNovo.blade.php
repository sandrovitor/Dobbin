<div class="row">
    <div class="col-12 col-md-12 col-lg-8 col-xl-6 mx-auto">
        <div class="card">
            <div class="card-header">
                Novo Coordenador
            </div>
            <div class="card-body">
                <form action="coordenadores/novo" method="post" aftersubmit="">
                    <div class="form-group row">
                        <div class="col-12 col-xl-6 mb-3">
                            <label>Nome:</label>
                            <input type="text" class="form-control form-control-solid" name="nome" maxlength="30">
                        </div>
                        <div class="col-12 col-xl-6">
                            <label>Data de Nascimento:</label>
                            <input type="date" class="form-control form-control-solid" name="nascimento" maxlength="30">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-12 col-xl-6 mb-3">
                            <label>RG:</label>
                            <input type="text" class="form-control form-control-solid" name="rg" placeholder="Insira somente os números." pattern="[0-9]{0,10}" data-validate-rg maxlength="10">
                        </div>
                        <div class="col-12 col-xl-6">
                            <label>CPF:</label>
                            <input type="text" class="form-control form-control-solid" name="cpf" placeholder="Insira somente os números." pattern="[0-9]{0,11}" data-validate-cpf maxlength="11">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-12 col-xl-6 mb-3">
                            <label>Email:</label>
                            <input type="email" class="form-control form-control-solid" name="email" placeholder="" maxlength="60">
                        </div>
                        <div class="col-12 col-xl-6">
                            <label>Telefone(s):</label>
                            <input type="text" class="form-control form-control-solid" name="telefone" maxlength="60">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-12 col-xl-6 mb-3">
                            <label>Endereço:</label>
                            <input type="text" class="form-control form-control-solid" name="endereco" placeholder="Rua, setor, número..." maxlength="120">
                        </div>
                        <div class="col-12 col-xl-6">
                            <label>Complemento:</label>
                            <input type="text" class="form-control form-control-solid" name="complemento" placeholder="Complemento do endereço..."  maxlength="120">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-12 col-xl-6 mb-3">
                            <label>Ponto de Referência:</label>
                            <input type="text" class="form-control form-control-solid" name="ponto_referencia" placeholder="Próximo a..." maxlength="120">
                        </div>
                        <div class="col-12 col-xl-6">
                            <label>Bairro:</label>
                            <input type="text" class="form-control form-control-solid" name="bairro" placeholder=""  maxlength="30">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-12 col-xl-6 mb-3">
                            <label>Cidade</label>
                            <input type="text" class="form-control form-control-solid" name="cidade" maxlength="30">
                        </div>
                        <div class="col-12 col-xl-6">
                            <label>Estado</label>
                            <select class="form-control form-control-solid" name="estado">
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
                    <div class="form-group row">
                        <div class="col-12 col-xl-6 mb-3">
                            <label>CEP:</label>
                            <input type="text" class="form-control form-control-solid" name="cep" placeholder="Somente números" pattern="[0-9]{0,8}" maxlength="8">
                        </div>
                        <div class="col-12 col-xl-6">
                            <label>Estado Civil:</label>
                            <select class="form-control form-control-solid" name="estado_civil">
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
                        <textarea rows="3" cols="1" class="form-control form-control-solid" name="alergia" placeholder="Lista de alérgenos..." maxlength="255"></textarea>
                    </div>
                    <div class="form-group row">
                        <div class="col-12 col-xl-6 mb-3">
                            <label>Nome para contato de emergência</label>
                            <input type="text" class="form-control form-control-solid" name="emergencia_nome" placeholder="Nome para contato" maxlength="60">
                        </div>
                        <div class="col-12 col-xl-6">
                            <label>Telefone para contato de emergência:</label>
                            <input type="text" class="form-control form-control-solid" name="emergencia_tel" placeholder="Telefone para contato"  maxlength="30">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-12 col-xl-6 mb-3">
                            <label>Tipo sanguíneo:</label>
                            <select class="form-control form-control-solid" name="sangue">
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
                    </div>


                    
                    
                    <div class="form-group text-right">
                        <button type="submit" class="btn btn-success">Salvar</button>
                        <a href="#clientes" class="btn btn-light">Cancelar</a>
                    </div>
                </form>
                <div id="retornoMSG"></div>
            </div>
        </div>
    </div>
</div>