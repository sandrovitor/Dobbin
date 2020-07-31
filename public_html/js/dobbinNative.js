const Dobbin = {
    // Verifica se o valor é dinheiro.
    isMoney: function (valor = '') {
        if(typeof valor != 'string') {
            console.log('Era esperado uma string.');
            return false;
        }
        if(valor == '' || typeof valor != 'string') {
            return false;
        }

        if(valor.length > 0) {
            // Procura pontos.
            if(valor.search(/\./gi) >= 0) {
                // Remove os pontos
                valor = valor.replace(/\./gi, '');
            }

            let patt = /(^[0-9]{1,}[,]{1}[0-9]{2}$|^([0-9]{1,})$)/gi;
            if(patt.test(valor) == false) {
                return false;
            } else {
                return true;
            }
        } else {
            return false;
        }
    },

    // Calcula diferença de dias.
    diffDays: function(data1 = null, data2 = null) {
        if(data1 == null || data2 == null) {
            return false;
        }
    
        if(data1 instanceof Date == false || data2 instanceof Date == false) {
            console.log('Essa função nativa espera que os campos sejam do tipo Date.');
            return false;
        }
    
        let diffTime = Math.abs(data2 - data1);
        let diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        
        return diffDays;
    },

    // Oculta CPF.
    ocultaCPF: function(cpf) {
        return cpf.substring(0,3)+'***.***-**';
    },

    // Oculta CNPJ.
    ocultaCNPJ: function(cnpj) {
        return cnpj.substring(0,2)+'.'+cnpj.substring(2,3)+'**.***/****-'+cnpj.substr(cnpj.length - 2);
    },

    // Formata Data e Hora: --/--/---- --:--:--
    formataDataHora: function(dateObj, anulaTimezone = false) {
        if(dateObj == 'Invalid Date') {
            return '--/--/---- --:--:--';
        }
        var dia, mes, ano, hora, minuto, segundo;
        
        if(anulaTimezone == true) {
            // Anula o timezone local.
            let timezone = dateObj.getTimezoneOffset();
            timezone = timezone/60;
            dateObj.setHours(dateObj.getHours() + (timezone));
        }
    
    
        dia = dateObj.getDate();
        mes = dateObj.getMonth();
        ano = dateObj.getFullYear();
        hora = dateObj.getHours();
        minuto = dateObj.getMinutes();
        segundo = dateObj.getSeconds();
    
        var data = '';
    
        if(dia < 10) {
            data +='0';
        }
        data += dia+'/';
    
        if(mes+1 < 10) {
            data += '0';
        }
        data += (mes+1)+'/'+ano+' ';
    
        if(hora < 10) {
            data += '0';
        }
        data += hora+':';
    
        if(minuto < 10) {
            data += '0';
        }
        data += minuto+':';
    
        if(segundo < 10) {
            data += '0';
        }
        data += segundo;
        
        return data;
    
    },

    // Formata Data: --/--/----
    formataData: function(dateObj, anulaTimezone = false) {
        if(dateObj == 'Invalid Date') {
            return '--/--/----';
        }
        var dia, mes, ano, hora, minuto, segundo;
        
        if(anulaTimezone == true) {
            // Anula o timezone local.
            let timezone = dateObj.getTimezoneOffset();
            timezone = timezone/60;
            dateObj.setHours(dateObj.getHours() + (timezone));
        }
    
        dia = dateObj.getDate();
        mes = dateObj.getMonth();
        ano = dateObj.getFullYear();
    
        var data = '';
    
        if(dia < 10) {
            data +='0';
        }
        data += dia+'/';
    
        if(mes+1 < 10) {
            data += '0';
        }
        data += (mes+1)+'/'+ano;
        
        return data;
    
    },

    // Formata Data para: MÊS/AAAA
    formataMesAno: function(dateObj, anulaTimezone = false) {
        if(dateObj == 'Invalid Date') {
            return '--/----';
        }
        var dia, mes, ano, hora, minuto, segundo;
        
        if(anulaTimezone == true) {
            // Anula o timezone local.
            let timezone = dateObj.getTimezoneOffset();
            timezone = timezone/60;
            dateObj.setHours(dateObj.getHours() + (timezone));
        }
    
        dia = dateObj.getDate();
        mes = dateObj.getMonth();
        ano = dateObj.getFullYear();
    
        var data = '';
        switch(mes) {
            case 0: data = 'Janeiro/'+ano; break;
            case 1: data = 'Fevereiro/'+ano; break;
            case 2: data = 'Março/'+ano; break;
            case 3: data = 'Abril/'+ano; break;
            case 4: data = 'Maio/'+ano; break;
            case 5: data = 'Junho/'+ano; break;
            case 6: data = 'Julho/'+ano; break;
            case 7: data = 'Agosto/'+ano; break;
            case 8: data = 'Setembro/'+ano; break;
            case 9: data = 'Outubro/'+ano; break;
            case 10: data = 'Novembro/'+ano; break;
            case 11: data = 'Dezembro/'+ano; break;
        }
        
        return data;
    
    },

    // Conversão: Centavo para Real
    converteCentavoEmReal: function(centavos = 0) {
        centavos = parseInt(centavos);
        let real, cents, invert = false;
    
        // Trato sinal (se negativo)
        if(centavos < 0) {
            invert = true;
            centavos = centavos*(-1);
        }
    
        // Trato REAL
        if(centavos >= 100) {
            // Mais de 1 real.
            real = Math.floor(centavos/100);
            cents = centavos%100;
        } else {
            // Menos de 1 real.
            real = 0;
            cents = centavos;
        }
    
        // Trato CENTAVOS
        if(cents < 10) {
            cents = '0'+cents;
        }

        // Acrescenta divisor de milhar.
        let reais = '';
        if(real.length > 3) {
            let i = real.length;
            while(i >= 1){
                if(i-3 > 0 && reais == '') {
                    reais = real.slice(i-3, i);
                } else if(i-3 > 0) {
                    reais = real.slice(i-3, i)+'.'+reais;
                } else {
                    reais = real.slice(0, i)+'.'+reais;
                }
                
                i = i-3;
            }
        } else {
            reais = real;
        }
        real = reais;
    
        if(invert === false) {
            return real+','+cents;
        } else {
            return '-'+real+','+cents;
        }
    },
    
    // Conversão: Real para Centavo
    converteRealEmCentavo: function(valor = '0,00') {
        if(Dobbin.isMoney(valor) == false) {
            return false;
        }
        let negativo = false;

        // Remove sinal de menos ou mais no inicio
        if(valor.charAt(0) == '-') {
            negativo = true;
            valor = valor.replace('-', '');
        }


        // Remove ponto do separador de milhar.
        
        while(valor.indexOf('.') >= 0) {
            valor = valor.replace('.', '');
        }
    
        if(valor.search(',') >= 0) {
            // Tem vírgula. Remove virgula.
            valor = valor.replace(',', '');
            valor = parseInt(valor);
        } else {
            // Não tem virgula. Multiplica por 100.
            valor = parseInt(valor)*100;
            return valor;
        }

        if(negativo == true) {
            return valor * (-1);
        } else {
            return valor;
        }
    },

    validaCamposRequired:function(formulario) {
        if(formulario == '' || formulario == null || formulario == undefined) {
            return false;
        } else {
            let form = $(formulario);
            for(let i = 0; i < form.find('[required]').length; i++) {
                if(form.find('[required]').eq(i).val() == '') {
                    form.find('[required]').eq(i).focus();
                    alerta('Preencha todos os campos requeridos.','Faltou um...', 'info');
                    return false;
                }
            }

            return true;
        }
    }
};

/**
 * 
 * VALIDAÇÃO AUTOMATICA ATRIBUTOS DOBBIN
 * 
 */

$(document).ready(function(){

    $(document).on('change keyup', '[dobbin-validate-valor]', function(ev){ // VALOR|DINHEIRO
        let alvo = ev.currentTarget;
        setTimeout(function(){
            resetValidaOnChange(alvo);
            validaValorDinheiroOnChange(alvo);
        }, 100);
    });

    $(document).on('blur', '[dobbin-validate-valor]', function(ev){ // VALOR|DINHEIRO
        resetValidaOnChange(ev.currentTarget);
        if(validaValorDinheiroOnChange(ev.currentTarget) == true) {
            let valor = $(ev.currentTarget).val();
            if(valor.length > 0 && valor.search(',') == -1) {
                $(ev.currentTarget).val(valor+',00');
            }
        }
    });
    
    $(document).on('blur', '[dobbin-validate-password]', function(ev){
        validaSenhaOnChange(ev.currentTarget);
    });

    $(document).on('change', '[dobbin-validate-password]', function(ev){
        resetValidaOnChange(ev.currentTarget);
    });

    $(document).on('change keyup', '[dobbin-mask-money]', function(ev){
        let valor = $(ev.currentTarget).val();
        let decimal;
        let reais = '';
        valor = valor.replace(/(\W|\D)/g,'');

        // Remove o 0 do inicio.
        while(valor.charAt(0) == '0') {
            valor = valor.slice(1, valor.length);
        }
        
        //console.log(valor);
        if(valor.length < 3) {
            // Preenche com zeros à esquerda.
            while(valor.length < 3) {valor = '0'+valor;}

        }


        let x = [];
        x[0] = valor.slice(0,valor.length-2);
        x[1] = valor.slice(valor.length-2, valor.length); // Casas decimais
        decimal = x[1];

        
        // Verifica se foi definido um valor máximo.
        if($(ev.currentTarget).attr('max') != undefined && $(ev.currentTarget).attr('max') != '') {
            let maxValor = parseInt($(ev.currentTarget).attr('max'));
            // Se valor for maior que o máximo, abaixa para o limite
            if(parseInt(x.join('')) > maxValor) {
                if(maxValor > 100) {
                    x[0] = maxValor.toString().substr(0, maxValor.toString().length-2);
                    x[1] = maxValor.toString().substr(maxValor.toString().length-2, 2);
                } else if(maxValor > 10) {
                    x[0] = "0";
                    x[1] = maxValor.toString();
                } else {
                    x[0] = "0";
                    x[1] = "0"+maxValor.toString();
                }
            }
        }
        
        // Acrescenta divisor de milhar.
        if(x[0].length > 3) {
            let i = x[0].length;
            while(i >= 1){
                if(i-3 > 0 && reais == '') {
                    reais = x[0].slice(i-3, i);
                } else if(i-3 > 0) {
                    reais = x[0].slice(i-3, i)+'.'+reais;
                } else {
                    reais = x[0].slice(0, i)+'.'+reais;
                }
                
                i = i-3;
            }
        } else {
            reais = x[0];
        }

        //console.log(x);
        

        

        $(ev.currentTarget).val(reais+','+decimal);
        
    });

    $(document).on('change keyup', '[dobbin-mask-number]', function(ev){
        let alvo = $(ev.currentTarget);

        if(alvo.attr('min') != undefined) {
            if(parseInt(alvo.val()) < parseInt(alvo.attr('min'))) {
                alvo.val(parseInt(alvo.attr('min')));
            }
        }

        if(alvo.attr('max') != undefined) {
            if(parseInt(alvo.val()) > parseInt(alvo.attr('max'))) {
                alvo.val(parseInt(alvo.attr('max')));
            }
        }
    });

    $(document).on('dblclick', '[dobbin-campo-edita]', function(ev){
        let alvo = $(ev.currentTarget);
        //console.log(alvo);
        let tipo;
        if(alvo.attr('dobbin-campo-tipo') == undefined || alvo.attr('dobbin-campo-tipo') == '') {
            tipo = 'text';
        } else {
            tipo = alvo.attr('dobbin-campo-tipo');
        }

        switch(tipo) {
            case 'textarea':
                alvo.after('<textarea class="form-control form-control-sm" rows="2" dobbin-editor-campo>'+alvo.text().trim()+'</textarea>');
                alvo.slideUp('fast');
                break;

            case 'text':
            case 'number':
                alvo.after('<input type="'+tipo+'" class="form-control form-control-sm" dobbin-editor-campo value="'+alvo.text().trim()+'">')
                alvo.slideUp('fast');
                break;

            default:
                return false;
                break;
        }

        //console.log(tipo);
    });

    $(document).on('keyup', '[dobbin-editor-campo]', function(ev){
        let alvo = $(ev.currentTarget);
        let valor;

        if(ev.which == 13) { // Enter
            // Salva novo valor
            let dest = alvo.prev();

            if(dest.attr('dobbin-url-form') != undefined && dest.attr('dobbin-url-form') != '') {
                // Foi definido um formulário para salvar o valor no BD.
                let obj = {};
                if(dest.attr('dobbin-campo-nome') != undefined && dest.attr('dobbin-campo-nome') != '') {
                    // Foi definido um nome do campo para envio do formulário.
                    obj[dest.attr('dobbin-campo-nome')] = alvo.val();
                } else {
                    // Não foi definido nome do campo. Usa o padrão "valor".
                    obj['valor'] = alvo.val();
                }

                // Envia a variável para o formulário via POST (utiliza o PREFIX POST).
                $.post(PREFIX_POST+dest.attr('dobbin-url-form'), obj, function(res){
                    if(res.success) {
                        alvo.prev().text(alvo.val());
                        alvo.prev().fadeIn('fast');
                        alvo.remove();
                    } else {
                        alerta(res.mensagem+'<br>Aperte ESC para encerrar a edição.', 'Não foi possível salvar.', 'warning');
                        alvo.focus();
                    }
                },'json').
                fail(function(evento){nativePOSTFail(evento);});
            } else {
                // Não precisa enviar dados para formulário.
                alvo.prev().text(alvo.val());
                alvo.prev().fadeIn('fast');
                alvo.remove();
            }
            
        } else if(ev.which == 27) { // ESC ou Escape
            // Retorna valor default
            alvo.prev().text( alvo[0].defaultValue );
            alvo.prev().fadeIn('fast');
            alvo.remove();

        }
    });

});