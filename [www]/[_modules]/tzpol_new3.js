function mu(l,h,n,c,d,e){
		var txt="";
        if (l == 1){
            if (d == 1){txt+="</div>";}
            if (c == 'n'){txt+='<P class=menu1th><IMG height=11 hspace=0 src="i/bullet-menu01.gif" width=15 align=absMiddle> <A class=d-menulink href="'+h+'">'+n+'</A></P>';}
           	else
            	{
                    if (e == 1)
                    	{
		                    txt+='<P class=menu1th><IMG height=11 hspace=0 src="i/bullet-menu01.gif" width=15 align=absMiddle> <A class=d-menulink href="javascript: #; return false;" onClick="sh(\'mn'+c+'\');\">'+n+'</A></P>';
                        	txt += '<div id="mn'+c+'" style="display: none">';
                        }
                    else
                    	{
        		            txt+='<P class=menu1th><IMG height=11 hspace=0 src="i/bullet-menu01.gif" width=15 align=absMiddle> <A class=d-menulink href="javascript: #; return false;" onClick="sh(\'mn'+c+'\');\">'+n+'</A></P>';
                            txt += '<div id="mn'+c+'">';
                        }
				}
        	}

        else{txt+='<P class=menu2th><A class=d-menulink2th href="'+h+'"><IMG height=7 src="i/bullet-menu02.gif" width=10 border=0>'+n+'</A></P>';}
 		return (txt);
}