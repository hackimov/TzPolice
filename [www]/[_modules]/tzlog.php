<h1>���������� ������� ����� ��������� v0.9</h1>



<?if(AuthStatus==1 && AuthUserName!="" && (AuthUserGroup == 100 || AuthUserClan=='Military Police' || AuthUserClan=='police' || AuthUserClan=='Police Academy' || AuthUserClan=='Financial Academy' || AuthUserClan=='Tribunal')) {?>



<script language="JavaScript">



var names = new Array();



var items = new Array();

items['Sharp knife'] = 1;

items['Jacket'] = 1;

items['Guardian'] = 1;

items['Boots'] = 1;

items['Carrot Berret'] = 1;

items['Boulder'] = 1;

items['BBS 6 mm'] = 1;

items['Vest'] = 1;

items['Bridges'] = 1;

items['Berret red'] = 1;

items['Jeans skirt'] = 1;

items['Waist'] = 1;

items['Sown-off rifle'] = 1;

items['ammo 7.0 mm'] = 1;

items['Fly knife'] = 1;

items['Selfmade Pistol'] = 1;

items['Nagant 1925'] = 1;

items['Sense diadem'] = 1;

items['Life crystal'] = 1;

items['Fire crystal'] = 1;

items['Reversi'] = 1;

items['Five-in-a-row'] = 1;

items['WCC'] = 1;

items['Ice SUMO'] = 1;



var zzz = new Array();



var start_time = 0;

var online = 0;

var login_error = '';



function ch_zzz(a) {

	zzz[a.name] = a.checked;

}



function check(a) {

	if (!(names[a])) {

		names[a] = new Array();

		names[a]['������'] = new Array();

		names[a]['������'] = new Array();

	}

}



function balance(a,b,c,d,e) {

	if (zzz[a] != false) {

		zzz[a] = true;

	}

	if (names[a][b][d]) {

		names[a][b][d] += Math.abs(e);

	} else {

		names[a][b][d] = Math.abs(e);

	}

	if (!(names[a][c][d])) {

		names[a][c][d] = 0;

	}

}



function parse(str) {

	var temp = new Array();

	var ss = str.indexOf(' �� \'');

	if (ss>0) {

		var name = str.substring(str.length-1,ss+5);

		check(name);

		temp = str.substring(ss,16).split(',');

		for (var i=0; i<temp.length; i++) {

			if (temp[i].indexOf('[')>=0) {

				var res_name = temp[i].substring(temp[i].indexOf('['),0);

				var count = temp[i].substring(temp[i].length-1,temp[i].indexOf('[')+1);

			} else {

				var res_name = temp[i];

				var count = 1;

			}

			balance(name,'������','������',res_name,count);

		}

	}

	temp = new Array();

	ss = str.indexOf(' � \'');

	if (ss>0) {

		var name = str.substring(str.length-1,ss+4);

		check(name);

		temp = str.substring(ss,15).split(',');

		for (var i=0; i<temp.length; i++) {

			if (temp[i].indexOf('[')>=0) {

				var res_name = temp[i].substring(temp[i].indexOf('['),0);

				var count = temp[i].substring(temp[i].length-1,temp[i].indexOf('[')+1);

			} else {

				var res_name = temp[i];

				var count = 1;

			}

			balance(name,'������','������',res_name,count);

		}

	}

	ss = str.indexOf(' � ������� �� ��������� \'');

	if (ss>0) {

		var name = str.substring(str.length-1,ss+25);

		check(name);

		var res_name = str.substring(ss,15);

		if (res_name.indexOf('[')>=0) {

			var count = res_name.substring(res_name.length-1,res_name.indexOf('[')+1);

			var res_name = res_name.substring(res_name.indexOf('['),0);

		} else {

			var count = 1;

		}

		balance(name,'������','������',res_name,count);

	}

	temp = new Array();

	ss = str.indexOf(' � ������ \'');

	if (ss>0) {

		var name = str.substring(str.length-1,ss+11);

		check(name);

		if (str.indexOf('��������')>=0) {

			temp = str.substring(ss,25).split(',');

			for (var i=0; i<temp.length; i++) {

				if (temp[i].indexOf('[')>=0) {

					var res_name = temp[i].substring(temp[i].indexOf('['),0);

					var count = temp[i].substring(temp[i].length-1,temp[i].indexOf('[')+1);

				} else {

					var res_name = temp[i];

					var count = 1;

				}

				balance(name,'������','������',res_name,count);

			}

		} else {

			temp = str.substring(ss,23).split(',');

			for (var i=0; i<temp.length; i++) {

				if (temp[i].indexOf('[')>=0) {

					var res_name = temp[i].substring(temp[i].indexOf('['),0);

					var count = temp[i].substring(temp[i].length-1,temp[i].indexOf('[')+1);

				} else {

					var res_name = temp[i];

					var count = 1;

				}

				balance(name,'������','������',res_name,count);

			}

		}

	}

	ss = str.indexOf('���������');

	if (ss<0) {

		ss = str.indexOf('���������');

	}

	if (ss>0) {

		check('��� ��������');

		if (str.indexOf('� ���')>=0) {

			var res_name = str.substring(str.indexOf(' � ���'),ss+11);

		} else {

			var res_name = str.substring(str.length,ss+11);

		}

		if (res_name.indexOf('[')>=0) {

			var count = res_name.substring(res_name.length-1,res_name.indexOf('[')+1);

			res_name = res_name.substring(res_name.indexOf('['),0);

		} else {

			var count = 1;

		}

		balance('��� ��������','������','������',res_name,count);

	}

	ss = str.indexOf('��������');

	if (ss>0) {

		check('��� ��������');

		var res_name = str.substring(str.indexOf(' � ���'),ss+10);

		if (res_name.indexOf('[')>=0) {

			var count = res_name.substring(res_name.length-1,res_name.indexOf('[')+1);

			res_name = res_name.substring(res_name.indexOf('['),0);

		} else {

			var count = 1;

		}

		balance('��� ��������','������','������',res_name,count);

	}

	ss = str.indexOf('��������');

	if (ss>0) {

		check('��� ��������');

		var res_name = str.substring(str.indexOf(' � ���'),ss+29);

		if (res_name.indexOf('[')>=0) {

			var count = res_name.substring(res_name.length-1,res_name.indexOf('[')+1);

			res_name = res_name.substring(res_name.indexOf('['),0);

		} else {

			var count = 1;

		}

		balance('��� ��������','������','������',res_name,count);

	}

	ss = str.indexOf('�������');

	if (ss>0) {

		if (str.indexOf(' � \'')>=0) {

			var name='��� ��������';

			var res_name = str.substring(str.indexOf(' � \''),ss+8);

		}

		if (str.indexOf(' �� �������� ������ � ��������� \'')>=0) {

			var name = str.substring(str.length-1,str.indexOf(' �� �������� ������ � ��������� \'')+33);

			var res_name = str.substring(str.indexOf(' �� �������� ������ � ��������� \''),ss+8);

		}

		check(name);

		var count = res_name.substring(res_name.length-1,res_name.indexOf('[')+1);

		res_name = res_name.substring(res_name.indexOf('['),0);

		balance(name,'������','������',res_name,count);

	}

	ss = str.indexOf('������� ���������� �������');

	if (ss>0) {

		check('�����');

		var res_name = str.substring(str.length,ss+27);

		if (res_name.indexOf('[')>=0) {

			var count = res_name.substring(res_name.length-1,res_name.indexOf('[')+1);

			res_name = res_name.substring(res_name.indexOf('['),0);

		} else {

			var count = 1;

		}

		balance('�����','������','������',res_name,count);

	}

	temp = new Array();

	ss = str.indexOf(' ���� ������������ ��� ������������');

	if (ss>0) {

		check('�����');

		temp = str.substring(ss,15).split(',');

		for (var i=0; i<temp.length; i++) {

			if (temp[i].indexOf('[')>=0) {

				var res_name = temp[i].substring(temp[i].indexOf('['),0);

				var count = temp[i].substring(temp[i].length-1,temp[i].indexOf('[')+1);

			} else {

				var res_name = temp[i];

				var count = 1;

			}

			balance('�����','������','������',res_name,count);

		}

	}

	ss = str.indexOf(' ������� �� ����� ');

	if (ss>0) {

		var name = str.substring(ss-1,16);

		check(name);

		var res_name = str.substring(str.length,ss+18);

		if (res_name.indexOf('[')>=0) {

			var count = res_name.substring(res_name.length-1,res_name.indexOf('[')+1);

			res_name = res_name.substring(res_name.indexOf('['),0);

		} else {

			var count = 1;

		}

		balance(name,'������','������',res_name,count);

	}

	ss = str.indexOf(' ������ �� ������ ');

	if (ss>0) {

		var name = str.substring(ss-1,16);

		check(name);

		var res_name = str.substring(str.length,ss+18);

		if (res_name.indexOf('[')>=0) {

			var count = res_name.substring(res_name.length-1,res_name.indexOf('[')+1);

			res_name = res_name.substring(res_name.indexOf('['),0);

		} else {

			var count = 1;

		}

		balance(name,'������','������',res_name,count);

	}

	ss = str.indexOf(' ������� � ������ ��������: ');

	if (ss>0) {

		var name = str.substring(ss-1,16);

		check(name);

		var res_name = str.substring(str.length,ss+28);

		if (res_name.indexOf('[')>=0) {

			var count = res_name.substring(res_name.length-1,res_name.indexOf('[')+1);

			res_name = res_name.substring(res_name.indexOf('['),0);

		} else {

			var count = 1;

		}

		balance(name,'������','������',res_name,count);

	}

	ss = str.indexOf(' ������ �� ������ ��������: ');

	if (ss>0) {

		var name = str.substring(ss-1,16);

		check(name);

		var res_name = str.substring(str.length,ss+28);

		if (res_name.indexOf('[')>=0) {

			var count = res_name.substring(res_name.length-1,res_name.indexOf('[')+1);

			res_name = res_name.substring(res_name.indexOf('['),0);

		} else {

			var count = 1;

		}

		balance(name,'������','������',res_name,count);

	}

	ss = str.indexOf(' ����� ��� ������� ');

	if (ss>0) {

		var name = str.substring(ss-1,16);

		check(name);

		names[name]['�������'] = new Array();

		var res_name = str.substring(str.indexOf(' � ������������ �������� '),ss+19);

		if (res_name.indexOf('[')>=0) {

			var count = res_name.substring(res_name.length-1,res_name.indexOf('[')+1);

			res_name = res_name.substring(res_name.indexOf('['),0);

		} else {

			var count = 1;

		}

		var mnt = str.substring(str.indexOf(' ���.'),str.indexOf('\' �� ')+5);

		balance(name,'�������','�������',res_name+' �� '+mnt+' ���',count);

	}

	ss = str.indexOf('���� � ����');

	if (ss>0) {

		start_time = Math.abs(str.substring(2,0))*60+Math.abs(str.substring(5,3));

	}

	ss = str.indexOf('����� �� ����');

	if (ss>0) {

		online += Math.abs(str.substring(2,0))*60+Math.abs(str.substring(5,3)) - start_time;

		start_time = 0;

	}

	ss = str.indexOf('��� ������ �������� ������');

	if (ss>0) {

		login_error += str+'<br>';

	}

}



function str(a) {

	while (a.indexOf(' ')>=0) {

		a = a.replace(' ','&nbsp;');

	}

	return a;

}



function show() {

	var s = '<b style="color: green">�������� ��� ������: '+online+' �����</b><br>';

	if (login_error.length>0) {

		s = s+'<b style="color: red">'+login_error+'</b>';

	}

	var result = new Array();

	result['������'] = new Array();

	result['������'] = new Array();

	for (var i in names) {

		var show = 0;

		for (var j in names[i]['������']) {

			if (!(items[j])) {

				if (names[i]['������'][j]-names[i]['������'][j]>0) {

					show = 1;

				}

			}

		}

		for (var j in names[i]['������']) {

			if (!(items[j])) {

				if (names[i]['������'][j]-names[i]['������'][j]>0) {

					show = 1;

				}

			}

		}

		if ((zzz[i] == true) && show) {

			s = s+'<table width=100%>';

			s = s+'<tr bgcolor=#F4ECD4><td colspan=2><p><img src=i/bullet-red-01a.gif width=18 height=11 hspace=5><strong><input name="'+i+'" type="checkbox" checked onclick="ch_zzz(this)"> '+i+'</strong> </p></td></tr>';

			s = s+'<tr><td background=i/bgr-grid-sand1.gif><b><font color=green>������&nbsp;</font></b></td><td width=99% background=i/bgr-grid-sand.gif>';

			for (var j in names[i]['������']) {

				if (!(items[j])) {

					if (!(result['������'][j])) {

						result['������'][j] = 0;

					}

					result['������'][j] += names[i]['������'][j];

					if (names[i]['������'][j]-names[i]['������'][j]>0) {

						s = s+str(j)+'&nbsp;[<b>'+(names[i]['������'][j]-names[i]['������'][j])+'</b>], ';

					}

				}

			}

			s = s+'</td></tr><tr><td background=i/bgr-grid-sand1.gif><b><font color=red>������</font></b></td><td background=i/bgr-grid-sand.gif>';

			for (var j in names[i]['������']) {

				if (!(items[j])) {

					if (!(result['������'][j])) {

						result['������'][j] = 0;

					}

					result['������'][j] += names[i]['������'][j];

					if (names[i]['������'][j]-names[i]['������'][j]>0) {

						s = s+str(j)+'&nbsp;[<b>'+(names[i]['������'][j]-names[i]['������'][j])+'</b>], ';

					}

				}

			}

			s = s+'</td></tr>';

			s = s+'</table><br>';

		}

	}

	s = s+'<table width=100%>';

	s = s+'<tr bgcolor=#F4ECD4><td colspan=2><p><img src=i/bullet-red-01a.gif width=18 height=11 hspace=5><strong>�����</strong> </p></td></tr>';

	s = s+'<tr><td background=i/bgr-grid-sand1.gif><b><font color=green>������&nbsp;</font></b></td><td width=99% background=i/bgr-grid-sand.gif>';

	for (var i in result['������']) {

		if (result['������'][i]-result['������'][i]>0) {

			s = s+str(i)+'&nbsp;[<b>'+(result['������'][i]-result['������'][i])+'</b>], ';

		}

	}

	s = s+'</td></tr><tr><td background=i/bgr-grid-sand1.gif><b><font color=red>������</font></b></td><td background=i/bgr-grid-sand.gif>';

	for (var i in result['������']) {

		if (result['������'][i]-result['������'][i]>0) {

			s = s+str(i)+'&nbsp;[<b>'+(result['������'][i]-result['������'][i])+'</b>], ';

		}

	}

	s = s+'</td></tr>';

	s = s+'</table><br>';

	var head_show = 1;

	for (var i in names) {

		var show = 0;

		for (var j in names[i]['�������']) {

			if (!(items[j])) {

				show = 1;

			}

		}

		if ((zzz[i] == true) && show) {

			if (head_show) {

				head_show = 0;

				s = s+'<table width=100%>';

				s = s+'<tr bgcolor=#F4ECD4><td colspan=2><p><img src=i/bullet-red-01a.gif width=18 height=11 hspace=5><strong>������� ����� �������</strong> </p></td></tr>';

			}

			s = s+'<tr><td background=i/bgr-grid-sand1.gif><b><font color=green>'+i+'&nbsp;</font></b></td><td width=99% background=i/bgr-grid-sand.gif>';

			for (var j in names[i]['�������']) {

				s = s+str(j)+'&nbsp;[<b>'+names[i]['�������'][j]+'</b>], ';

			}

			s = s+'</td></tr>';

		}

	}

	if (head_show == 0) {

		s = s+'</table><br>';

	}

	document.all("info").innerHTML = s;

}



function subm() {

	var max;

	var strings = new Array();

	var tmp = document.F1.text.value;

	tmp = '\015\012' + tmp;

	while(tmp.indexOf(' \015\012')>=0) tmp = tmp.replace(' \015\012','\015\012');

	while(tmp.indexOf('\015\012 ')>=0) tmp = tmp.replace('\015\012 ','\015\012');

	while(tmp.indexOf('\015\012\015\012')>=0) tmp = tmp.replace('\015\012\015\012','\015\012');

	tmp = tmp.replace('\015\012','');

	strings = tmp.split('\015\012');

	if (strings[strings.length-1] == '') max = strings.length - 1; else max = strings.length;

	names = new Array();

	start_time = 0;

	online = 0;

	login_error = '';

	for(var i=0; i<max; i++) {

		parse(strings[i]);

	}

	if (start_time>0) {

		if (confirm('���� �� ������� ����?')) {

			var time = new Date();

			online += time.getHours()*60+time.getMinutes()+180+time.getTimezoneOffset()-start_time;

		} else {

			online += 1440-start_time;

		}

	}

	show();

}



</script>



<table width='100%' border='0' cellspacing='3' cellpadding='2'><tr>



<td height='20' background='i/bgr-grid-sand.gif'><p><img src='i/bullet-red-01a.gif' width='18' height='11' hspace='5'><strong>������� �����:</strong> </p></td>



</tr><tr><td align=center>



<form method="POST" name=F1>



<textarea rows=6 name=text cols=110>

</textarea>

<br>

<input type="button" value="�������������" onclick="subm()">

<input type="button" value="��������" onclick="document.F1.text.value = ''; zzz = new Array();">

</form>



</td></tr><tr><td height='20' background='i/bgr-grid-sand.gif'><p><img src='i/bullet-red-01a.gif' width='18' height='11' hspace='5'><strong>���������:</strong> </p></td>



</tr><tr><td align=center>



<div id="info"></div>



</td></tr></table>



<?} else echo $mess['AccessDenied'];?>