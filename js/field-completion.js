
var vKeyword;
var vArchName;
var vLimitNumberOfResult;
var vKeywordSimilarity;
var vLatestVersionOnly = "true";
var vShowMaskedVersion = "false";
var vShowLivebuildVersion = "false";

function initValue ()
{
	vKeyword = "";
	vArchName = ""; //
	vLimitNumberOfResult = -1;
	vKeywordSimilarity = "exact";
	vLatestVersionOnly = "true";
	vShowMaskedVersion = "false";
	vShowLivebuildVersion = "false";
	document.q.k.focus();
}

function setShowLatestVersionOnly( )
{
	vLatestVersionOnly = document.q.latestonly.checked;
}

function setShowMaskedVersion ( )
{
	vShowMaskedVersion = document.q.showmasked.checked;
}

function setShowLivebuildVersion ( )
{
	vShowLivebuildVersion = document.q.livebuild.checked;
}

function checkLimitationNumberOfResult ( )
{
	vLimitNumberOfResult = document.q.limit.value;

	if ( vLimitNumberOfResult == "" )
		vLimitNumberOfResult = -1;


	if ( vLimitNumberOfResult < -1 || vLimitNumberOfResult == 0 )
	{
		alert("0 or less than -1 is not valid.");

		vLimitNumberOfResult = -1;
		document.q.limit.value = "";
		document.getElementById("lmt").focus();
		return -1;
	}
}

function setArchitectureName ( architectureName )
{
	vArchName = document.q.arch.value;
}

function checkKeywordSimilarity ( )
{
	vKeywordSimilarity = document.q.similarity.value;
}

function setKeyword ( ) 
{
	vKeyword = document.q.k.value;

	if ( typeof(vKeyword) == "undefined" || vKeyword == "" )
	{
		alert("검색 키워드를 입력해주셔야 합니다");
		document.getElementById("kwd").focus();
		return -1;
	}
}

function getURL()
{
	var baseURL = "http://darkcircle.myhome.tv/phportage/";

	// keyword
	if ( setKeyword ( ) != -1 )
		baseURL += "k=" + vKeyword;
	else
		return;

	// arch name
	setArchitectureName ();
	if ( vArchName != "" && typeof(vArchName) != "undefined" )
		baseURL += "&arch=" + vArchName;
	

	// keyword similarity
	checkKeywordSimilarity();
	baseURL += "&similarity=" + vKeywordSimilarity;

	// limitation number of result
	if ( checkLimitationNumberOfResult() != -1 )
	{
		if ( vLimitNumberOfResult != -1 ) 
			baseURL += "&limit=" + vLimitNumberOfResult;
	}
	else
		return;
		

	// checkboxes
	setShowLatestVersionOnly();
	setShowMaskedVersion();
	setShowLivebuildVersion();
	baseURL += "&latestonly=" + vLatestVersionOnly;
	baseURL += "&livebuild=" + vShowLivebuildVersion;
	baseURL += "&showmasked=" + vShowMaskedVersion;

	document.location.href= baseURL;
}

function helloWorld ()
{
	alert("hello world!");
}
