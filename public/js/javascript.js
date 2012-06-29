// JavaScript Document

//Funções de VALIDAÇÃO
function validaLogin(){
	var user = document.login.usuario
	var senha = document.login.pwd

	if(user.value =="")
		{
		alert("Campo \'login\' deve ser preenchido!");
		user.focus();
		}else
		if(senha.value == "")
			{
			alert("Uma senha precisa ser informada!");
			senha.focus();
			}else
				{
				document.login.submit();
				}
			
		
		
	
	}