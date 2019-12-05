<?php
$votes_no=7;//ЧИСЛО ГОЛОСОВ
$numdivs=100;//Число участников голосования - т.к. это шаблон, то оно устанавливается тут...
print <<<_HEADER
<HTML><HEAD>
<TITLE>Test</TITLE>
<SCRIPT LANGUAGE="JavaScript"> 
<!--
var checkArr=[
_HEADER;
print "false";
for ($i=2;$i<=$votes_no;$i++)
{
	print ",false";
}
print "];\n";
print "var totaldivs=".$numdivs.";\n";
print <<<_HDREND
var defColor;
function CloseVote(vid,vr)
{
		for (i=1;i<=totaldivs;i++)
		{
			o=document.getElementById('voter'+i+'cell'+vr);
			if (i != vid)
				o.style.visibility = checkArr[vr]?'hidden':'visible';
		}
}
function Colorize(cellover,cellid,vid,optype)
{
	if ((checkArr[cellid] == true)&&(optype!='click'))
		return;
	if (optype == "over")
	{
		defColor=cellover.style.backgroundColor;
		cellover.style.backgroundColor = '#FFCC99';
	}
	if (optype == "out")
		cellover.style.backgroundColor = defColor;
	if (optype == "click")
	{
		for (i=1;i<=$votes_no;i++)
		{
			
			o=document.getElementById('voter'+vid+'cell'+i);
			if ((checkArr[i]) && (o.style.visibility!='hidden'))
				if (i!=cellid)
				{
					checkArr[i]=false;
					CloseVote(vid,i);
					o.style.backgroundColor = defColor;
				}
		}
		checkArr[cellid]=checkArr[cellid]?false:true;
		if (checkArr[cellid] == true)
			cellover.style.backgroundColor = '#CCFFCC';
		else
			cellover.style.backgroundColor = '#FFCC99';
		CloseVote(vid,cellid);
	}

}
//-->
</SCRIPT>
_HDREND;
//здесь - ограничивать голоса, отданные в режиме просмотра полной картинки.
//Т.е. вешать в onload несколько таких процедур подряд:
//Colorize(document.getElementById('voter".$voter_id."cell".$voter_ratio."'),".$voter_ratio.",".$voter_id.",'click');
//Где: $voter_id - идентификатор за кого голосуем
//     $voter_ratio - оценка
print "<body onload=\"Colorize(document.getElementById('voter1cell1'),1,1,'click');\"";
print "<table width=100% cellspacing=1 border=1>";
for ($i=0;$i<$numdivs;$i++)
{
	if ($i==0)
		print "<tr>";
	if (($i % 4) ==0){
		print "</tr><tr>";
	}
	print "<td>";
	print "<div id=\"voterphoto".($i+1)."\" align=\"center\" valign=\"top\">";
	print "<img src=\"subm.gif\"></div>";//Картинка
	print "<div align=\"center\" valign=\"bottom\">";
	print "<table cellpadding=\"10\"><tr>";
	for ($a=$votes_no;$a>=0;$a--)
	{
		print "<td id=\"voter".($i+1)."cell".$a."\" onmouseover=\"Colorize(this,".$a.",".($i+1).",'over');\" onmouseout=\"Colorize(this,".$a.",".($i+1).",'out');\" onclick=\"Colorize(this,".$a.",".($i+1).",'click');\">".$a."</td>";
	}
	print "</tr></table></td>";
}
print "</tr></table></body>";
?>