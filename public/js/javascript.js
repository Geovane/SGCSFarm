// MUDA IMAGEM DO MENU
function menu(it)	{
//	alert(document.getElementById(it).style.display);
        var tam = 5;
        if(document.getElementById('6')!=null){tam = tam + 1};
        if(document.getElementById('7')!=null){tam = tam + 1};
        //alert(tam);
	if(it>10){
		for(i = 1; i<= tam ; i ++)	{
			if( i == tam && document.getElementById('6')==null && document.getElementById('7')!=null){
                            i = i + 1;
                        }
                        document.getElementById(i).style.display = "none";
			document.getElementById(i+10).style.display = "inherit";			
                        
		}
		document.getElementById(it).style.display = "none";
		document.getElementById(it-10).style.display = "inherit";
	}
        
}
