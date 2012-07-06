// JavaScript Document

//Funções de VALIDAÇÃO
function validaLogin(){
	var user = document.login.login
	var senha = document.login.senha

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
	
	
// MUDA IMAGEM DO MENU
function menu(it)	{
//	alert(document.getElementById(it).style.display);
        var tam = 6;
        if(document.getElementById('6')!=null){tam = tam + 1};
        if(document.getElementById('7')!=null){tam = tam + 1};
	if(it>10){
		for(i = 1; i< tam ; i ++)	{
			document.getElementById(i).style.display = "none";
			document.getElementById(i+10).style.display = "inherit";			
		}
		document.getElementById(it).style.display = "none";
		document.getElementById(it-10).style.display = "inherit";
	}
        
}
