function $(id) { return document.getElementById(id); }

function debug(data) {
    if (console) {
        console.debug(var_dump(data));
    } else {
        alert(var_dump(data));
    }
    
}

function var_dump(oElem) {
    var sStr = '';
    if (typeof(oElem) == 'string' || typeof(oElem) == 'number')     {
        sStr = oElem;
    } else {
        var sValue = '';
        for (var oItem in oElem) {
            sValue = oElem[oItem];
            if (typeof(oElem) == 'innerHTML' || typeof(oElem) == 'outerHTML') {
                sValue = sValue.replace(/</g, '&lt;').replace(/>/g, '&gt;');
            }
            sStr += 'obj.' + oItem + ' = ' + sValue + '\n';
        }
    }
    return sStr;
}

function ajax(url, vars, callbackFunction) {
    $("loading").style.visibility = "visible";
    var request =  new XMLHttpRequest();
    request.open("POST", url, true);
    request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    request.setRequestHeader("Content-length", vars.length);
    request.setRequestHeader("Connection", "close");
    request.onreadystatechange = function() {
    if (request.readyState == 4 && request.status == 200) {
      if (request.responseText) {
          callbackFunction(request.responseText);
          $("loading").style.visibility = "hidden";
      }
    }
    };
    request.send(vars);
}


function updateIssueSelectList(response) {
    var list = response.split("\n").slice(0, -1);
    var oldIssues = $("issue");
    var w = oldIssues.options
    
    for (i = 0; i < oldIssues.length; i++) {
        l = eval( "(" + list[i] + ")" )
        x = document.createElement('option')
        x.text = l.title
        x.value = l.id
        w[i] = x
    }
    
    while (i < list.length) {
        l = eval( "(" + list[i] + ")" )
        x = document.createElement('option')
        x.text = l.title
        x.value = l.id
        try {
            oldIssues.add(x, null)
        } catch(ex) {
            oldIssues.add(x)
        }
        i++;
    }
    $("issue").selectedIndex = i - 1;
}

function getEvalCode(str) {
    return str.match(/\[evalcode\](.+)\[\/evalcode\]/g);
}

function updateIssueList(response) {
    var r = response;
    var evalCode = getEvalCode(response);
    var evalStr = "";
    for (var i = 0; i < evalCode.length; i++) {
        r = r.replace(evalCode[i], "");
        evalStr += evalCode[i].replace(/\[\/?evalcode\]/g, "") + ";\n";
    }
    $("body").innerHTML = r;
    eval(evalStr);
}

function createMenu(e, id) {
    f = $(id);
    f.style.position = 'absolute';
    f.style.zIndex = '100';
    f.style.top = e.clientY - 16;
    f.style.left = e.clientX + 16;
    f.style.visibility = "visible";
}

function addNewIssue() {
    var title = $("newIssueTitle").value;
    $("newIssueMenu").style.visibility = "hidden";
    var postData = "title=" + encodeURI(title);
    if (document.location.href.match("newArticle")) {
        ajax("adminAJAX.php?action=addNewIssue&r=" + Math.random(9999), postData, function() {loadIssues("selectIssues", updateIssueSelectList)});
    } else {
        ajax("adminAJAX.php?action=addNewIssue&r=" + Math.random(9999) , postData, function() {loadIssues("allIssues", updateIssueList)});   
    }
}

function loadIssues(act, callbackFunction) {
    ajax("adminAJAX.php?action=" + act + "&r=" + Math.random(9999) , "", callbackFunction);
}

function newIssue(e) {
    createMenu(e, "newIssueMenu");
}

function editIssue(id, title, e){
    createMenu(e, "updateIssueMenu");
    $("updateIssueTitle").value = title;
    $("updateIssueId").value = id;
}

function saveIssue(){
    $("updateIssueMenu").style.visibility = "hidden";
    ajax("adminAJAX.php?action=updateIssue&id=" + $("updateIssueId").value + "&r=" + Math.random(9999), "title=" + encodeURI($("updateIssueTitle").value), function () {loadIssues("allIssues", updateIssueList)}); 
}

function deleteIssue(id){
    var r = confirm("¬ы уверены что хотите удалить этот выпуск?");
    if (r) {
        ajax("adminAJAX.php?action=deleteIssue&id=" + id + "&r=" + Math.random(9999) , "0",  function() {loadIssues("allIssues", updateIssueList)});
    }
}

function deleteArticle(id) {
    var r = confirm("¬ы уверены что хотите удалить эту статью?");
    if (r) {
        ajax("adminAJAX.php?action=deleteArticle&id=" + id + "&r=" + Math.random(9999) , "0",  function() {loadIssues("allIssues", updateIssueList)});
    }
}

function setArticlePos(id, pos) {
    ajax("adminAJAX.php?action=setArticlePos&id=" + id + "&pos=" + pos + "&r=" + Math.random(9999) , "0",  function() {loadIssues("allIssues", updateIssueList)});
}

function highlightArticlePos(id, pos) {
    //$("article_"+id+"_pos_"+pos).style.color = "#ca1313"; 
    $("article_"+id+"_pos_"+pos).className = "articleSelected";
}

function changeStatus(e, id) {
    createMenu(e, "chStatusMenu");
    $("issueID").value = id;
}

function setStatus(status) {
    $("chStatusMenu").style.visibility = "hidden";
    ajax("adminAJAX.php?action=setIssueStatus&id=" + $("issueID").value + "&status=" + status + "&r=" + Math.random(9999) , "0",  function() {loadIssues("allIssues", updateIssueList)});
}



function main() {
    if (!document.location.href.match("allIssues") && !document.location.href.match("newArticle") && !document.location.href.match("editArticle")) {
        loadIssues("allIssues", updateIssueList);
    } 
}












