<div class="row">
    <div class="col-12 col-md-12 col-lg-8 col-xl-6 mx-auto">
        <div class="card">
            <div class="card-header">
                Novo Usuário
            </div>
            <div class="card-body">
                <form action="usuarios/novo" method="post" >
                    <div class="form-group row">
                        <div class="col-12 col-xl-6 mb-3">
                            <label>Nome</label>
                            <input type="text" class="form-control form-control-solid" name="nome" maxlength="30">
                        </div>
                        <div class="col-12 col-xl-6">
                            <label>Sobrenome</label>
                            <input type="text" class="form-control form-control-solid" name="sobrenome" maxlength="30">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-12 col-xl-6 mb-3">
                            <label>Email:</label>
                            <input type="email" class="form-control form-control-solid" name="email" placeholder="" maxlength="60">
                        </div>
                        <div class="col-12 col-xl-6">
                            <label>Nome de usuário:</label>
                            <input type="text" class="form-control form-control-solid" name="usuario" maxlength="30">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-12 col-xl-6 mb-3">
                            <label>Senha:</label>
                            <input type="password" class="form-control form-control-solid" name="senha1" maxlength="32">
                        </div>
                        <div class="col-12 col-xl-6">
                            <label>Repita a senha:</label>
                            <input type="password" class="form-control form-control-solid" name="senha2" maxlength="32">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-12 col-xl-6 mb-3">
                            <label>Nível:</label>
                            <select class="form-control form-control-solid" name="nivel">
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
                <div id="retornoMSG"></div>
            </div>
        </div>
    </div>
</div>