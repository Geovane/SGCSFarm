$(document).ready( function() {
	$("#contactform").validate({
		// Define as regras
		rules:{
			name:{
				// campoNome será obrigatório (required) e terá tamanho mínimo (minLength)
				required: true, minlength: 3
			},
			email:{
				// campoEmail será obrigatório (required) e precisará ser um e-mail válido (email)
				required: true, email: true
			},
			assunto:{
				required: true
			},

                        message:{
				// campoMensagem será obrigatório (required) e terá tamanho mínimo (minLength)
				required: true, minlength: 5
			}
		},
		// Define as mensagens de erro para cada regra
		messages:{
			name:{
				required: "Digite o seu nome",
				minLength: "O seu nome deve conter, no mínimo, 3 caracteres"
			},
			email:{
				required: "Digite o seu e-mail para contato",
				email: "Digite um e-mail válido"
			},
			assunto:{
				required: "Digite o assunto da sua mensagem"
			},
			message:{
				required: "Digite a sua mensagem",
				minLength: "A sua mensagem deve conter, no mínimo, 5 caracteres"
			}
		}
	});
});

/* EXEMPLO DE MASCARA
 
$(document).ready(function(){
    $("#name").mask("99/99/9999");
});
 
 
 **/