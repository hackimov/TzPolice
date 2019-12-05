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
  $('checkbtn').innerHTML = 'загрузка...';
  
  var request =  new XMLHttpRequest();
  request.open("POST", url, true);
  request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  request.setRequestHeader("Content-length", vars.length);
  request.setRequestHeader("Connection", "close");
  request.onreadystatechange = function() {
    if (request.readyState == 4 && request.status == 200) {
      if (request.responseText) {
          callbackFunction(request.responseText);
          $('checkbtn').innerHTML = 'Проверить';
      }
    }
  };
  request.send(vars);
}

var PHPAJAXURL = "http://www.tzpolice.ru/_modules/siteRating/siteRatingAJAX.php";


function checkSite() {
    var request = "action=check";
    var site = $('site').value;
    if (site.length !=0) {
        request += "&site=" + escape(site);
        ajax(PHPAJAXURL + "?r=" + Math.random(9999) , request,  function (response) { $('response').innerHTML =  response;});
    } else {
        $('site').value = 'введите параметры поиска'
    }
}
















