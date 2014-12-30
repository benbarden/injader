
/*
  Injader
  Copyright (c) 2005-2015 Ben Barden


  This program is free software: you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation, either version 3 of the License, or
  (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

    /* Handy stuff */
    window.$ = function(id) {
        return document.getElementById(id);
    }

    /* Check/uncheck */
  
  function ToggleCheckboxes(blnWhich) {
    var fields = document.getElementsByTagName('input');
    for (i=0; i<fields.length; i++) {
      if (fields[i].type == 'checkbox') {
        fields[i].checked = blnWhich;
      }
    }
  }

  function TogglePermissionCheckboxes(strName, blnWhich) {
    j = 0;
    var arrFields = new Array();
    var labels = document.getElementsByTagName('label');
    for (i=0; i<labels.length; i++) {
      if (labels[i].innerHTML == strName) {
        strFieldID = labels[i].id;
        arrFields[j] = strFieldID.replace('lbl', '');
        j++;
      }
    }
    var fields = document.getElementsByTagName('input');
    for (i=0; i<fields.length; i++) {
      if (fields[i].type == 'checkbox') {
        for (j=0; j<arrFields.length; j++) {
          if (arrFields[j] == fields[i].value) {
            fields[i].checked = blnWhich;
          }
        }
      }
    }
  }
  
/* Insert at current cursor position */

  function insertAtCursor(strFieldName, strValue) {
    elem = document.getElementById(strFieldName);
    if (document.selection) { // IE
      elem.focus();
      sel = document.selection.createRange();
      sel.text = strValue;
      elem.focus();
    } else if (elem.selectionStart || elem.selectionStart == '0') { // Firefox/Mozilla/Netscape
      var startPos = elem.selectionStart;
      var endPos = elem.selectionEnd;
      elem.value = elem.value.substring(0, startPos)
      + strValue
      + elem.value.substring(endPos, elem.value.length);
      elem.focus();
      elem.selectionStart = startPos + strValue.length;
      elem.selectionEnd = startPos + strValue.length;
    } else {
      elem.value += strValue;
      elem.focus();
    }
  }

  function selectFieldData(elem) {
    elem.focus();
    elem.select();
  }

/* Template variables */

  function showVarDesc(strListName, intListCount) {
    elem = document.getElementById(strListName);
    if (elem.options.selectedIndex > -1) {
      intSel = elem.options.selectedIndex;
      strVar = elem.options[intSel].value;
      for (i=0; i<intListCount; i++) {
        document.getElementById(strListName + 'Desc' + i).style.display = 'none';
      }
      document.getElementById(strListName + 'Desc' + intSel).style.display = 'block';
    }
  }
  
  function addVariable(strListName, strFieldName) {
    elem = document.getElementById(strListName);
    if (elem.options.selectedIndex > -1) {
      strVar = elem.options[elem.options.selectedIndex].value;
      insertAtCursor(strFieldName, strVar);
      elem.focus(); // doesn't solve the scrolling problem...
    }
  }

/* Post buttons */

  function doSimpleCMSCode(control, desc, tag) {
    elem = document.getElementById(control);
    strText = window.prompt("Enter the " + desc + " text", desc + " text");
    strValue = "[" + tag + "]" + strText + "[/" + tag + "]";
    insertAtCursor(control, strValue);
  }

  function doLinkCMSCode(control) {
    elem = document.getElementById(control);
    strLinkAddress = window.prompt("Enter the address of the link (omit http:// if this is a relative link)", "http://");
    strLinkTextDefault = strLinkAddress.replace("http://", "");
    strLinkText = window.prompt("Enter the clickable text", strLinkTextDefault);
    strValue = "[link=" + strLinkAddress + "]" + strLinkText + "[/link]";
    insertAtCursor(control, strValue);
  }
  
  function doImageCMSCode(control) {
    elem = document.getElementById(control);
    strImageAddress = window.prompt("Enter the address of the image (omit http:// if this is a relative link)", "http://");
    strImageDescDefault = strImageAddress.replace("http://", "");
    strImageDesc = window.prompt("Enter a description of the image", strImageDescDefault);
    strValue = "[img src=" + strImageAddress + "]" + strImageDesc + "[/img]";
    insertAtCursor(control, strValue);
  }

/* Random images */

  function Mixer(intLimit) {
    var i = Math.round(intLimit*Math.random());
    if (i == 0) {
      i = Mixer(intLimit);
    }
    return i;
  }

  function MixField(varField, intLimit) {
    var el = document.getElementById(varField);
    var i = Mixer(intLimit);
    if (el.src == (arrImages[i-1].src)) {
      i = Mixer(intLimit);
      while (el.src == (arrImages[i-1].src)) {
        i = Mixer(intLimit);
      }
    }
    el.src = arrImages[i-1].src;
  }
  
  function preloadImages() {
    var arrImages = new Array();
    for (i=0; i<preloadImages.arguments.length; i++) {
      arrImages[i] = new Image();
      arrImages[i].src = preloadImages.arguments[i];
    }
    return arrImages;
  }

/* Cookies */

  function createCookie(name,value,days) {
  	if (days) {
  		var date = new Date();
  		date.setTime(date.getTime()+(days*24*60*60*1000));
  		var expires = "; expires="+date.toGMTString();
  	} else {
      var expires = "";
    }
    value = escape(value);
  	document.cookie = name+"="+value+expires+"; path=/";
  }

  function readCookie(name) {
  	var nameEQ = name + "=";
  	var ca = document.cookie.split(';');
  	for(var i=0;i < ca.length;i++) {
  		var c = ca[i];
  		while (c.charAt(0)==' ') c = c.substring(1,c.length);
  		if (c.indexOf(nameEQ) == 0) {
        value = c.substring(nameEQ.length,c.length);
        value = unescape(value);
        return value;
      }
  	}
  	return null;
  }

  function eraseCookie(name) {
  	createCookie(name,"",-1);
  }

/* Miscellaneous */

  function reloadPage() {
    window.location.reload();
  }
  
  function EnlargeTextarea(strName) {
    elem = document.getElementById(strName);
    intRows = parseInt(elem.getAttribute('rows'));
    if (intRows >= 30) {
      window.alert("You cannot make this field any bigger.");
    } else {
      intRows = intRows + 3;
      elem.setAttribute('rows', intRows);
    }
  }

  function ShrinkTextarea(strName) {
    elem = document.getElementById(strName);
    intRows = parseInt(elem.getAttribute('rows'));
    if (intRows <= 6) {
      window.alert("You cannot make this field any smaller.");
    } else {
      intRows = intRows - 3;
      elem.setAttribute('rows', intRows);
    }
  }

  function ClearDropDown(sel) {
    var opts = sel.options;
    try {
      opts.length = 0;
    }
    catch (e) {
      window.alert(e);
    }
    var len;
    while ((len = opts.length)) opts[len - 1] = null;
    opts[0] = new Option('(None)', '0');
    /*
    for (var i = (objSelect.options.length-1); i >= 0; i--) {
      options[i] = null;
    }
    options[0] = new Option('(None)', '0');
    */
  }
