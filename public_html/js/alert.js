function alerta(texto, titulo = '', tipo = 'darkdark', duration = 5000) {
	criaAlerta({
		tipo: tipo,
		titulo: titulo,
		mensagem: texto
	}, duration, 'topCenter');
}

function criaAlerta(arr, tempoDuracao = 5000, position = 'bottomRight') {
	position = position.trim();
	var container;

	if(position == 'bottomRight' || position.indexOf(' ') >= 0) {
		if($('.container-popup-alerts').length == 0) {
			// Cria o container.
			$('body').append('<div class="container-popup-alerts"></div>');
		}

		container = $('.container-popup-alerts');
	} else {
		if($('.container-popup-alerts-'+position.toLowerCase()).length == 0) {
			// Cria o container.
			$('body').append('<div class="container-popup-alerts-'+ position.toLowerCase() +'"></div>');
		}
		
		container = $('.container-popup-alerts-'+ position.toLowerCase());
	}
	

	var html = '';
	switch(arr['tipo']) {
		case 'primary':
			html = '<div class="alert alert-primary" style="display:none">';
			break;

		case 'success':
			html = '<div class="alert alert-success" style="display:none">';
			break;

		case 'warning':
			html = '<div class="alert alert-warning" style="display:none">';
			break;

		case 'danger':
			html = '<div class="alert alert-danger" style="display:none">';
			break;

		case 'secondary':
			html = '<div class="alert alert-secondary" style="display:none">';
			break;

		case 'dark':
		default:
			html = '<div class="alert alert-dark" style="display:none">';
			break;

		case 'info':
			html = '<div class="alert alert-info" style="display:none">';
			break;

		case 'darkdark':
			html = '<div class="alert alert-darkdark" style="display:none">';
			break;
	}

	html += '<button type="button" class="close" data-dismiss="alert">&times;</button>';
	html += '<strong>'+arr['titulo']+'</strong> ' + arr['mensagem'] + '</div>';

	$(container).append(html);
	var alerta = $(container).children().last();

	alerta.fadeIn('slow', function(){
		fechaAlerta(alerta, tempoDuracao);
	});
	//fechaAlertPopup($('.container-popup-alerts div').last());
	return true;
}

function fechaAlerta(obj, tempoDuracao = 5000) {
	if(tempoDuracao <= 1000) {
		// desativa o fechamento automático

	} else {
		setTimeout(function(){
			$(obj).fadeOut('slow', function(){
				$(this).remove();
	
			});
		}, tempoDuracao);
	}
	
}
