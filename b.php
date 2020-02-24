<?php
$dir    = 'C:///xampp/htdocs/webapp/adminApp/js/modules/';
$folders = scandir($dir);
$result = [];
for($i=2; $i<count($folders); $i++){
	if(file_exists($dir.$folders[$i]."/views/")){
		$temp = scandir($dir.$folders[$i]."/views/");
		$temp = array_slice($temp,2,count($temp)-2);
		array_push($result,array($folders[$i]."/views/"=>$temp));
	}
}
//echo json_encode($result);

?>

<html lang="en">
<head>
  <script src="https://code.jquery.com/jquery-3.4.1.js"></script>

<script> 
var $log;
var fileText;
	var words = [];
var files = $.parseJSON('<?php echo json_encode($result);?>');
$(function(){
$log = $("#log");
	for(var i = 0 ; i < files.length; i++){
		var path = Object.keys(files[i])[0];
		var filesInPath = files[i][path];
		for(var j=0; j < filesInPath.length; j++){
			var fullPath = 'webapp/adminApp/js/modules/'+path+filesInPath[j]
			readTextFile(fullPath);
		}
	}
	for(var i = 0 ; i < words.length; i++){
		$log.append(generateKey(words[i])+':"' + words[i]+'", <br>');
	}

});
function getWordsInGlobalVariable(param){
	var text = param;	
	var  html = $.parseHTML( text ),nodeNames = [];
	$.each( html, function( i, el ) {
		if(el.nodeName!=='#text'){
			var texts = el.innerText.split("\n");
			for(var i = 0 ; i < texts.length; i++){
				if(isAlphaNumeric(texts[i])){
					var temp = texts[i].trim();
					if(!temp.includes('{{')&&!words.includes(temp)){
						var items = temp.split('/');
						for(var j=0; j<items.length;j++){
							if(!words.includes(generateKey(items[j])))
								words.push(items[j]);
						}
					}
				}
			}
		}
	});
}
function getTraslateText(param){
		fileText = param;
	for(var i = 0 ; i < words.length; i++){
		var replaceText = generateHtml(words[i]);
		var indexs = getIndicesOf(words[i],fileText,1);
		while(indexs.length>0){
			fileText = fileText.substr(0,indexs[0])+
					replaceText+
					fileText.substr(indexs[0]+words[i].length,fileText.length-words[i].length-indexs[0]);
			indexs = getIndicesOf(words[i],fileText,1);
		}
	}
	return fileText;
}

function readTextFile(file)
{
    var rawFile = new XMLHttpRequest();
    rawFile.open("GET", file, false);
    rawFile.onreadystatechange = function ()
    {
        if(rawFile.readyState === 4)
        {
            if(rawFile.status === 200 || rawFile.status == 0)
            {
                var allText = rawFile.responseText;
                getWordsInGlobalVariable(allText);
				var translateText = getTraslateText(allText);
				$.ajax({
					url:'c.php',
					type:"POST",
					data:{
						path:file,
						text:translateText
					},
					success:function(data){
						console.log(data);
					}
				});
            }
        }
    }
    rawFile.send(null);
}

function isAlphaNumeric(str) {
  var code, i, len;
	if(str.length<1) return false;
  for (i = 0, len = str.length; i < len; i++) {
    code = str.charCodeAt(i);
    if ((code > 47 && code < 58) || // numeric (0-9)
        (code > 64 && code < 91) || // upper alpha (A-Z)
        (code > 96 && code < 123)) { // lower alpha (a-z)
      return true;
    }
  }
  return false;
};
function download(filename, text) {
    var pom = document.createElement('a');
    pom.setAttribute('href', 'data:text/plain;charset=utf-8,' + encodeURIComponent(text));
    pom.setAttribute('download', filename);

    if (document.createEvent) {
        var event = document.createEvent('MouseEvents');
        event.initEvent('click', true, true);
        pom.dispatchEvent(event);
    }
    else {
        pom.click();
    }
}
function generateKey(param){
	param = param.replace(/[&\/\\#,+()$~%.'":*?<>{}]/g,'_');

	result = param.replace(/ /g,'_');
	result = result.replace(/-/g,'_');
	
	result = result.split('/').join('_');
	return result;
}
function generateHtml(param){
	result = "{{ '"+generateKey(param)+"' || translate }}";
	return result;
}
function getIndicesOf(searchStr, str, caseSensitive) {
    var searchStrLen = searchStr.length;
    if (searchStrLen == 0) {
        return [];
    }
    var startIndex = 0, index, indices = [];
    if (!caseSensitive) {
        str = str.toLowerCase();
        searchStr = searchStr.toLowerCase();
    }
    while ((index = str.indexOf(searchStr, startIndex)) > -1) {
		if(!isAlphaNumeric(str.substr(index-1,1)) && !isAlphaNumeric(str.substr(index+searchStr.length,1) ) && str.substr(index-1,1)!='"' && str.substr(index-1,1)!="'")
			if(!(str.substr(index-1,1)=="'" && str.substr(index+searchStr.length,14)=="' || translate"))
				indices.push(index);
        startIndex = index + searchStrLen;
    }
    return indices;
}

</script>
</head>
<body>
    <input type='file' accept='*' onchange='openFile(event)'><br>
<div id="log">
  <h3>Content:</h3>
</div>

</body>
</html>
https://codelabs.developers.google.com/codelabs/cloud-translation-intro/index.html#0
