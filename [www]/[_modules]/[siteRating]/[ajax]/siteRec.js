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
  var request =  new XMLHttpRequest();
  request.open("POST", url, true);
  request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  request.setRequestHeader("Content-length", vars.length);
  request.setRequestHeader("Connection", "close");
  request.onreadystatechange = function() {
    if (request.readyState == 4 && request.status == 200) {
      if (request.responseText) {
          callbackFunction(request.responseText);
      }
    }
  };
  request.send(vars);
}

var PHPAJAXURL = "siteRecAJAX.php";

function updateSiteList(responseData) {
    if (responseData != "0") {
        var sites = responseData.split("[end]").slice(0, -1);
        var list = [];
        for (i = 0; i < sites.length; i++) {
            var site = sites[i];
            list.push(eval("(" + site + ")"));
        }
        printSiteList(list);
    } else {
        $("error").innerHTML = "No results";
        $("sites").innerHTML = "";
    }
}

function getSiteDomain(url) {
    return url.substring(url.search("//") + 2, url.length);

}

function getStatusImage(status) {
    switch (status) {
        case 0: return "<img src=\"img/statusUnchecked.gif\" border=0 alt=\"не проверено\">"; break;
        case 1: return "<img src=\"img/statusApproved.gif\" border=0 alt=\"одобрено\">"; break;
        case 2: return "<img src=\"img/statusError.gif\" border=0 alt=\"заблокировано\">"; break;
    }
}
var clickX = clickY = 0;

function showEditForm(responseData) {
    var site = eval("(" + responseData + ")");

    var html = "<table cellspacing=1 class=\"editTable\"><tr><td>url:</td><td><input id='editUrl' value='"+site.url+"' class=\"searchInput\"></tr><tr><td>name:</td><td><input id='editName' value='" + site.name + "' class=\"searchInput\"></tr>";
    html += "<tr><td>status:</td><td><select id='editStatus' class=\"searchInput\">";
    html += "<option value=0>не проверено</option><option value=1>одобрено</option><option value=2>заблокировано</option></select></td></tr>";
    html += "<tr><td colspan=2><button onClick='editSave(" + site.id + "); return false;' class=\"editBtn\">Save</button><button onClick=\"$('editForm').style.visibility = 'hidden'; return false;\" class=\"editBtn\">cancel</button></td></tr>";

    formId = "editForm";
    editform = $(formId);
    editform.innerHTML = html;
    editform.style.backgroundColor = '#c3d9ff';
    editform.style.color = '#000';
    editform.style.position = 'absolute';
    editform.style.zIndex = '100';
    editform.style.top = clickY + 16;
    editform.style.left = clickX + 16;
    editform.style.visibility = "visible";

    $('editStatus').selectedIndex = site.status;
}

function closeEditForm(responseData){
    editform = $("editForm");
    editform.style.visibility = "hidden";
    getAll();
}

function editSite(id, event) {
    clickX = event.clientX;
    clickY = event.clientY;
    ajax(PHPAJAXURL + "?r=" + Math.random(9999) , "mode=edit&id=" + id,  showEditForm);
}

function editSave(id) {
    var request = "";
    request += "&url=" + $('editUrl').value;
    request += "&name=" + escape($('editName').value);
    request += "&inspector=" + escape($('editInspectorNick').value);
    request += "&status=" + $('editStatus').value;
    ajax(PHPAJAXURL + "?r=" + Math.random(9999) , "mode=save&id=" + id + request,  closeEditForm);
}

function addSiteForm(event) {

    clickX = event.clientX;
    clickY = event.clientY;

    var html = "<table cellspacing=1 class=\"editTable\"><tr><td>url:</td><td><input id='addUrl' class=\"searchInput\"></tr><tr><td>name:</td><td><input id='addName' class=\"searchInput\"></tr>";
    html += "<tr><td colspan=2><button onClick='addSite(); return false;' class=\"editBtn\">add</button> <button onClick=\"$('addForm').style.visibility = 'hidden'; return false;\" class=\"editBtn\">cancel</button></td></tr>";

    formId = "addForm";
    addform = $(formId);
    addform.innerHTML = html;
    addform.style.backgroundColor = '#c3d9ff';
    addform.style.color = '#000';
    addform.style.position = 'absolute';
    addform.style.zIndex = '100';
    addform.style.top = clickY + 16;
    addform.style.left = clickX + 16;
    addform.style.visibility = "visible";
}

function closeAddForm() {
    addform = $("addForm");
    addform.style.visibility = "hidden";
    getAll();
}

function addSite() {
    var request = "";
    request += "&url=" + $('addUrl').value;
    request += "&name=" + escape($('addName').value);
    ajax(PHPAJAXURL + "?r=" + Math.random(9999) , "mode=add" + request,  closeAddForm);
}

function deleteSite(id) {
    var r = confirm("Do you really want to delete this site?");
    if (r) {
        ajax(PHPAJAXURL + "?r=" + Math.random(9999) , "mode=delete&id=" + id,  function (){getAll();});
    }
}

function attachSite(id) {
    var r = confirm("Закрепить данный сайт за вами?");
    if (r) {
        ajax(PHPAJAXURL + "?r=" + Math.random(9999) , "mode=attach&id="+id,  function (){getMy();});
    }
}

function printSiteList(list) {
    var container = $("sites");
    var html = "<table cellspacing=1 class=\"tblSites\">";
    if (access == "admin") {
        html += "<tr id=\"tblSitesHeader\"><td>domain</td><td>name</td><td>last check</td><td>status</td><td>inspector</td><td>options</td></tr>";
    } else {
        html += "<tr id=\"tblSitesHeader\"><td>domain</td><td>name</td><td>last check</td><td>status</td><td>inspector</td><td>options</td></tr>";
    }
    for (i in list)  {
        var site = list[i];
        if (access == "moderator") {
           html += "<tr onMouseOver=\"this.style.background = '#ffeb86';\" onMouseOut=\"this.style.background = '#ffffff';\">";
           html += "<td><a href=" + site.url + " target=_blank>" + getSiteDomain(site.url) + "</a></td>";
           html += "<td>" + site.name + "</td>";
           html += "<td>" + site.lastCheck + "</td>";
           html += "<td>" + getStatusImage(site.status) + "</td>";
           html += "<td>" + site.inspector + "</td>";
           html += "<td><button class=\"searchBtn\" onClick=\"attachSite(" + site.id + "); return false;\">Закрепить</button></td></tr>";
        } else if (access == "admin") {
           html += "<tr onMouseOver=\"this.style.background = '#ffeb86';\" onMouseOut=\"this.style.background = '#ffffff';\">";
           html += "<td onClick=\"editSite(" +  site.id + ", event);\"><a href=" + site.url + " target=_blank>" + getSiteDomain(site.url) + "</a></td>";
           html += "<td onClick=\"editSite(" +  site.id + ", event);\">" + site.name + "</td>";
           html += "<td onClick=\"editSite(" +  site.id + ", event);\">" + site.lastCheck + "</td>";
           html += "<td onClick=\"editSite(" +  site.id + ", event);\">" + getStatusImage(site.status) + "</td>";
           html += "<td onClick=\"editSite(" +  site.id + ", event);\">" + site.inspector + "</td>";
           html += "<td><button class=\"searchBtn\" onClick=\"deleteSite(" + site.id + "); return false\">X</button></td>";
           html += "<td><button class=\"searchBtn\" onClick=\"attachSite(" + site.id + "); return false;\">Закрепить</button></td></tr>";
        } else {
            html += "<tr onMouseOver=\"this.style.background = '#ffeb86';\" onMouseOut=\"this.style.background = '#ffffff';\"><td><a href=" + site.url + " target=_blank>" + getSiteDomain(site.url) + "</a></td><td>" + site.name + "</td><td>" + site.lastCheck + "</td><td>" + getStatusImage(site.status) + "</td></tr>";
        }
    }
    html += "</table>";

    container.innerHTML = html;
    $("error").innerHTML = "";
}

function getFree() {
    var params = "act=free";
    ajax(PHPAJAXURL + "?r=" + Math.random(9999), params,  updateSiteList);
}

function getMy() {
    var params = "act=my";
    ajax(PHPAJAXURL + "?r=" + Math.random(9999), params,  updateSiteList);
}

function searchSite() {
    var site = $("site").value;
    if (site == "") {
        $("error").innerHTML = "enter site";
    } else {
        var params = "act=search&site=" + site;
        ajax(PHPAJAXURL + "?r=" + Math.random(9999), params,  updateSiteList);
    }

}

