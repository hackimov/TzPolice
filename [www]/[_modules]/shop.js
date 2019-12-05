function $(id) { return document.getElementById(id); };

function debug(data) {
    if (document.body) {
        var oItemId = 'js-debug',
            oItem = $(oItemId);
        if (!oItem) {
            oItem = document.createElement('DIV');
            oItem.setAttribute('id', oItemId);
            oItem.style.left = '0';
            oItem.style.top = '90%';
            oItem.style.backgroundColor = 'yellow';
            oItem.style.color = '#000';
            oItem.style.position = 'absolute';
            oItem.style.zIndex = '100';
            document.body.insertBefore(oItem, document.body.firstChild);
        }
        oItem.innerHTML = '<pre style="margin:0;">' + var_dump(data) + '</pre>';
        oItem = null;
    } else {
        alert(var_dump(data));
    }
}

function var_dump(oElem) {
	var sStr = '';
	if (typeof(oElem) == 'string' || typeof(oElem) == 'number') 	{
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

function xmlhttpPost(strURL) {
    var xmlHttpReq = false;
    var self = this;
    // Mozilla/Safari
    if (window.XMLHttpRequest) {
        self.xmlHttpReq = new XMLHttpRequest();
    }
    // IE
    else if (window.ActiveXObject) {
        self.xmlHttpReq = new ActiveXObject("Microsoft.XMLHTTP");
    }
    self.xmlHttpReq.open('POST', strURL, true);
    self.xmlHttpReq.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    self.xmlHttpReq.onreadystatechange = function() {
        if (self.xmlHttpReq.readyState == 4) {
            updateShopList(self.xmlHttpReq.responseText);
        }
    }
    self.xmlHttpReq.send("");
}

function updateShopList(jsCode) {
    sh = jsCode.split("|");
    shops = [];
    for (var i in sh) {
        shop = sh[i];
        shops.push(eval("(" + shop + ")"));
    }
    sortShops();
    printShops();
}

// sort functions
function sortByCityAsc(shop1, shop2) { return shop1.city > shop2.city; }
function sortByCityDesc(shop1, shop2) { return shop1.city < shop2.city; }
function sortByNameAsc(shop1, shop2) { return shop1.name > shop2.name; }
function sortByNameDesc(shop1, shop2) { return shop1.name < shop2.name; }
function sortByXYAsc(shop1, shop2) { return shop1.xy > shop2.xy; }
function sortByXYDesc(shop1, shop2) { return shop1.xy < shop2.xy; }
function sortByMetalsAsc(shop1, shop2) { return shop1.resources[0] - shop2.resources[0]; }
function sortByMetalsDesc(shop1, shop2) { return shop2.resources[0] - shop1.resources[0]; }
function sortByGoldAsc(shop1, shop2) { return shop1.resources[1] - shop2.resources[1]; }
function sortByGoldDesc(shop1, shop2) { return shop2.resources[1] - shop1.resources[1]; }
function sortByPolymersAsc(shop1, shop2) { return shop1.resources[2] - shop2.resources[2]; }
function sortByPolymersDesc(shop1, shop2) { return shop2.resources[2] - shop1.resources[2]; }
function sortByOrganicAsc(shop1, shop2) { return shop1.resources[3] - shop2.resources[3]; }
function sortByOrganicDesc(shop1, shop2) { return shop2.resources[3] - shop1.resources[3]; }
function sortBySiliconAsc(shop1, shop2) { return shop1.resources[4] - shop2.resources[4]; }
function sortBySiliconDesc(shop1, shop2) { return shop2.resources[4] - shop1.resources[4]; }
function sortByRadioactiveAsc(shop1, shop2) { return shop1.resources[5] - shop2.resources[5]; }
function sortByRadioactiveDesc(shop1, shop2) { return shop2.resources[5] - shop1.resources[5]; }
function sortByGemsAsc(shop1, shop2) { return shop1.resources[6] - shop2.resources[6]; }
function sortByGemsDesc(shop1, shop2) { return shop2.resources[6] - shop1.resources[6]; }
function sortByVenomAsc(shop1, shop2) { return shop1.resources[7] - shop2.resources[7]; }
function sortByVenomDesc(shop1, shop2) { return shop2.resources[7] - shop1.resources[7]; }


var shops = [];
var sortedBy = {'field':'city','method':'asc'};

sortFunctions = function(field, method) {
    switch (field) {
        case 'city':
            return (method == "asc") ? sortByCityAsc : sortByCityDesc;
        break;
        case 'name':
            return (method == "asc") ? sortByNameAsc : sortByNameDesc;
        break;
        case 'xy':
            return (method == "asc") ? sortByXYAsc : sortByXYDesc;
        break;
        case 'metals': return (method == "asc") ? sortByMetalsAsc : sortByMetalsDesc;  break;
        case 'gold': return (method == "asc") ? sortByGoldAsc : sortByGoldDesc;  break;
        case 'polymers': return (method == "asc") ? sortByPolymersAsc : sortByPolymersDesc;  break;
        case 'organic': return (method == "asc") ? sortByOrganicAsc : sortByOrganicDesc;  break;
        case 'silicon': return (method == "asc") ? sortBySiliconAsc : sortBySiliconDesc;  break;
        case 'radioactive': return (method == "asc") ? sortByRadioactiveAsc : sortByRadioactiveDesc;  break;
        case 'gems': return (method == "asc") ? sortByGemsAsc : sortByGemsDesc;  break;
        case 'venom': return (method == "asc") ? sortByVenomAsc : sortByVenomDesc;  break;
    }
}

function sortShops(field, method) {
    if ((field == undefined) || (method == undefined)) {
        field = sortedBy.field;
        method = sortedBy.method;
    } else {
        sortedBy.field = field;
        sortedBy.method = method;
    }
    shops.sort(sortFunctions(field, method));
    method = (method == "asc") ? "desc" : "asc";
    printShops(method);
}

function shopListRequest(method) {
    var url = "_modules/resParser.php?rand=" + Math.random(9999);
    xmlhttpPost(url);
}

function getMaxResPrice(id){
    var max = shops[0].resources[id];
    var maxId = 0;
    for (i = 1; i < shops.length; i++)  {
       if (shops[i].resources[id] > max) {
           max = shops[i].resources[id];
           maxPrice = shops[i].resources[id];
       }
    }
    return maxPrice;
}

var filterCity = '';
function setFilter(value) {
    filterCity = value;
    printShops();
}

function checkFilter(shop) {
    if (filterCity != '') {
        if (shop.city == filterCity) {
            return true;
        } else {
            return false;
        }
    } else {
        return true;
    }
}


function printShops(method) {
    var method = (method == undefined) ? "asc" : method;

    var container = $("shopsPrices");
    var htmlCode = "<table width=100% cellspacing=1 border=0 bgcolor=#7d7d7d>";
    htmlCode    += "<tr bgcolor=#e1d5ad><td align='center' onClick=\"sortShops('name', '" + method + "');\" style='cursor: pointer;' onMouseOver=\"this.style.background = '#d9cfbb'\" onMouseOut=\"this.style.background = '#e1d5ad'\"><b>Магазин</b></td><td align='center' onClick=\"sortShops('city', '" + method + "');\" style='cursor: pointer;' onMouseOver=\"this.style.background = '#d9cfbb'\" onMouseOut=\"this.style.background = '#e1d5ad'\"><b>Город</b></td><td align='center' onClick=\"sortShops('xy', '" + method + "');\" style='cursor: pointer;' onMouseOver=\"this.style.background = '#d9cfbb'\" onMouseOut=\"this.style.background = '#e1d5ad'\"><b>Локация</b></td><td align='center' onClick=\"sortShops('metals', '" + method + "');\" style='cursor: pointer;' onMouseOver=\"this.style.background = '#d9cfbb'\" onMouseOut=\"this.style.background = '#e1d5ad'\"><img src='_imgs/tz/resourse/metals.gif' border='0'></td><td align='center' onClick=\"sortShops('gold', '" + method + "');\" style='cursor: pointer;' onMouseOver=\"this.style.background = '#d9cfbb'\" onMouseOut=\"this.style.background = '#e1d5ad'\"><img src='_imgs/tz/resourse/gold.gif' border='0'></td><td align='center' onClick=\"sortShops('polymers', '" + method + "');\" style='cursor: pointer;'onMouseOver=\"this.style.background = '#d9cfbb'\" onMouseOut=\"this.style.background = '#e1d5ad'\"><img src='_imgs/tz/resourse/polymers.gif' border='0'></td><td align='center' onClick=\"sortShops('organic', '" + method + "');\" style='cursor: pointer;' onMouseOver=\"this.style.background = '#d9cfbb'\" onMouseOut=\"this.style.background = '#e1d5ad'\"><img src='_imgs/tz/resourse/organic.gif' border='0'></td><td align='center' onClick=\"sortShops('silicon', '" + method + "');\" style='cursor: pointer;' onMouseOver=\"this.style.background = '#d9cfbb'\" onMouseOut=\"this.style.background = '#e1d5ad'\"><img src='_imgs/tz/resourse/silicon.gif' border='0'></td><td align='center' onClick=\"sortShops('radioactive', '" + method + "');\" style='cursor: pointer;' onMouseOver=\"this.style.background = '#d9cfbb'\" onMouseOut=\"this.style.background = '#e1d5ad'\"><img src='_imgs/tz/resourse/radic.gif' border='0'></td><td align='center' onClick=\"sortShops('gems', '" + method + "');\" style='cursor: pointer;' onMouseOver=\"this.style.background = '#d9cfbb'\" onMouseOut=\"this.style.background = '#e1d5ad'\"><img src='_imgs/tz/resourse/gems.gif' border='0'></td><td align='center' onClick=\"sortShops('venom', '" + method + "');\" style='cursor: pointer;' onMouseOver=\"this.style.background = '#d9cfbb'\" onMouseOut=\"this.style.background = '#e1d5ad'\"><img src='_imgs/tz/resourse/venom.gif' border='0'></td></tr>";

    var bestPrices = [];
    for (i = 0; i < 8; i++) {
        bestPrices[i] = getMaxResPrice(i);
    }

    for (var i in shops) {
        shop = shops[i];
        if (checkFilter(shop)) {
          htmlCode += "<tr bgcolor=#ffffff>";
          htmlCode += "<td>" + shop.name +"</td>";
          htmlCode += "<td onClick=\"setFilter('" + shop.city +"')\" style=\"cursor: pointer;\" onMouseOver=\"this.style.background = '#fffa73'\" onMouseOut=\"this.style.background = '#ffffff'\">" + shop.city +"</td>";
          htmlCode += "<td>" + shop.xy +"</td>";
          for (var j in shop.resources)  {
              res = shop.resources[j];
              if (bestPrices[j] == shop.resources[j]) {
                  htmlCode += "<td bgcolor=#ccff66>" + res +"</td>";
              } else {
                  htmlCode += "<td>" + res +"</td>";
              }
          }
          htmlCode += "</tr>";
        }
    }
    container.innerHTML = htmlCode;
}
