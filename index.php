<?php

include("Include.inc");

$header = 
"<html>
	<script language=\"javascript\" type=\"text/javascript\" src=\"js/field-completion.js\"></script>
	<style type=\"text/css\">
		a:link { text-decoration:none };
		a:hover { text-decoration:none };
	</style>
	<head>	
		<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">
		<title>PHPortage :: The REST API for the fastest gentoo package search engine in the web</title>
	</head>
	<body onload=\"initValue()\">
		<div align=center>
			<table border=0>
				<tr><td rowspan=4> <img src=\"image/php.gif\" border=0>               </td><td>  &nbsp; </td></tr>
				<tr><td valign=top>  <font size=7><b><i>ortage</i></b></font> </td></tr>
				<tr><td>             &nbsp;                                     </td></tr>
				<tr><td>             &nbsp;                                     </td></tr>
			</table>
		</div>
		<p align=\"center\"><i>The REST API for the fastest gentoo package search engine in the web</i></p>
		<hr>";
$intro_1="		<div align=center>
			<table border=0 cellpadding=0 cellspacing=0>
				<tr><td>
					<form method=\"GET\" name=\"q\">
						<p><input name=\"k\" id=\"kwd\" type=\"text\" size=60 tabindex=1 onchange=\"setKeyword()\"></input><a href=\"javascript:getURL()\"><button value=\"button\" type=\"button\" tabindex=8 ><b>Search</b></button></a> </p>
						<p>&nbsp;&nbsp;<b>Architecture</b> <select name=\"arch\" size=\"1\" tabindex=2 onchange=\"javascript:setArchitectureName(this.value);\">
							<option value=\"\" selected>(none)</option>\n";
$intro_2="						</select>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Similarity</b> <select name=\"similarity\" size=1 tabindex=3>
							<option value=\"exact\" selected>exact</option>
							<option value=\"similar\">similar</option>
						</select>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Limit</b> <input type=\"text\" id=\"lmt\" name=\"limit\"size=4 tabindex=4></input></p>
						<p>
						<input type=\"checkbox\" name=\"latestonly\" tabindex=5 checked=\"true\"><b>Show latest version only</b></input><br>
						<input type=\"checkbox\" name=\"livebuild\" tabindex=6><b>Show live build version</b></input><br>

						<input type=\"checkbox\" name=\"showmasked\" tabindex=7><b>Show masked packages</b></input>
						</p>
					</form>
					<br/><center>REST API 사용법을 보시려면 [<a href=\"help.html\" target=\"_parent\">여기</a>]를 눌러주세요 :)</center><br/>
				</td></tr>
				</table>";
$footer="		<hr>
		<table border=0 width=100%>
			<tr><td align=left><i>Powered by PHP</i></td><td align=right>Copyright &copy; 2012 by Darkcircle</td></tr>
		</table>
	</body>
</html>";


print($header);

print($intro_1);
$arch = new ArchList();
$archlist = $arch->getArchList();
foreach ( $archlist as &$archstr)
	print("\t\t\t\t\t\t\t<option value=\"{$archstr}\">{$archstr}</option>\n");
print($intro_2);

print($footer);
