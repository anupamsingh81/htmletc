function showhide(id){
  if (document.getElementById){
	obj = document.getElementById(id);
	if (obj.style.display == "none"){
		obj.style.display = "";
	} else {
		obj.style.display = "none";
	}
  }
}

function openWindow(page_name,width,height,window_name) {
	if (window_name=='') window_name = 'floater';
	winStats='toolbar=no,location=no,directories=no,menubar=no,'
	winStats+='scrollbars=yes,resizable=yes,status=yes,width='+width+',height='+height;
	if (navigator.appName.indexOf("Microsoft")>=0) {
		winStats+=',left=15,top=20'
	}else{
		winStats+=',screenX=15,screenY=20'
	}
	floater=window.open(page_name,window_name,winStats);
}


function confirmDelete(id) {
  if( !confirm( "Are you sure you want to delete "+id+"?" ) ) {
    return false;
  } else {
    return true;
  }
}

var aeOL = [];
function addEvent(o, n, f, l)
{
 var a = 'addEventListener', h = 'on'+n, b = '', s = '';
 if (o[a] && !l) return o[a](n, f, false);
 o._c |= 0;
 if (o[h])
 {
  b = '_f' + o._c++;
  o[b] = o[h];
 }
 s = '_f' + o._c++;
 o[s] = f;
 o[h] = function(e)
 {
  e = e || window.event;
  var r = true;
  if (b) r = o[b](e) != false && r;
  r = o[s](e) != false && r;
  return r;
 };
 aeOL[aeOL.length] = { o: o, h: h };
};
addEvent(window, 'unload', function() {
 for (var i = 0; i < aeOL.length; i++) with (aeOL[i])
 {
  o[h] = null;
  for (var c = 0; o['_f' + c]; c++) o['_f' + c] = null;
 }
});


function hidediv(id) {
	//safe function to hide an element with a specified id
	if (document.getElementById) { // DOM3 = IE5, NS6
		document.getElementById(id).style.display = 'none';
	}
	else {
		if (document.layers) { // Netscape 4
			document.id.display = 'none';
		}
		else { // IE 4
			document.all.id.style.display = 'none';
		}
	}
}

function showdiv(id) {
	//safe function to show an element with a specified id
		  
	if (document.getElementById) { // DOM3 = IE5, NS6
		document.getElementById(id).style.display = 'block';
	}
	else {
		if (document.layers) { // Netscape 4
			document.id.display = 'block';
		}
		else { // IE 4
			document.all.id.style.display = 'block';
		}
	}
}

function getElementsByClass(searchClass,node,tag) {
  var classElements = new Array();
  if (node == null)
    node = document;
  if (tag == null)
    tag = '*';
  var els = node.getElementsByTagName(tag);
  var elsLen = els.length;
  var pattern = new RegExp("(^|\\s)"+searchClass+"(\\s|$)");
  for (i = 0, j = 0; i < elsLen; i++) {
    if (pattern.test(els[i].className) ) {
      classElements[j] = els[i];
      j++;
    }
  }
  return classElements;
}

// ---------------------------
// --- load image
// ---------------------------
function loadImage(idname,filedir) {
	var sel = document.getElementById(idname);
	var newImg = filedir + sel.value;
	var previewid = idname+"-preview";

	var previewImg = document.getElementById(previewid);
	previewImg.src=newImg;

	var aNewImg = new Image();
	aNewImg.src = newImg;
	var newWidth = aNewImg.width;
	var newHeight = aNewImg.height;
	if (newWidth>0 && newWidth<100) {
		previewImg.style.width = newWidth+"px";
	} else {
		previewImg.style.width ="50px";
	}
	if (newHeight>0 && newHeight<100) {
		previewImg.style.height = newHeight+"px";
	} else {
		previewImg.style.height = "50px";
	}
	previewImg.style.border = "0";
	previewImg.onclick = function() { 
		var width=aNewImg.width;
		var height=aNewImg.height;
		winStats='toolbar=no,location=no,directories=no,menubar=no,'
		winStats+='scrollbars=yes,resizable=yes,status=yes,width='+width+',height='+height;
		floater=window.open(previewImg.src,"floater",winStats);
	};

	// create a new Image Object. This object will be responsible for loading the image
	loadedImage = new Image();

	// provide a callback for the onload event.
	loadedImage.onload = function() {
		previewImg.src = imageSrc;
	}

	// This causes the loading of the image.
	loadedImage.src = imageSrc;
}

// -------------------------------------------------------------------------------------
// --- changesubjects
// --- used to change the subject dropdown in advanced search based on type selected...
// --- NOTE: I wrote this for my own form need and you probably won't need it but it 
// --- might serve as a useful example of mixing in JavaScript with your forms...
// -------------------------------------------------------------------------------------
function changesubjects_addedit (array) {
	var sel1 = document.getElementById('content-type');
	var sel2 = document.getElementById('content2subject-subject-id');
	total = sel2.length;
	// --- reset the first Choose option
	//alert(sel2.options[0].defaultSelected);
	sel2.options[0] = new Option("","",false,false);
	for (i=1; i<=total; i++) {
		//sel2.options[i] = new Option(subjectarray[i],subjectarray[i]);
		if (sel1.value=="career" || sel1.value=="quote" || sel1.value=="prospective") {
			if (sel2.options[i].className==sel1.value) {
				sel2.options[i].style.display = "";
			} else {
				sel2.options[i].style.display = "none";
			}
		} else {
			if (sel2.options[i].className=="other") {
				sel2.options[i].style.display = "";
			} else {
				sel2.options[i].style.display = "none";
			}
		}
		if (sel2.options[i].className=="select") sel2.options[i].style.display = "";
	}
}

// -------------------------------------------------------------------------------------
// --- AJAX functions
// -------------------------------------------------------------------------------------

// ---------------------------
// --- Browser Support Code
// ---------------------------
function GetXmlHttpObject() {
  var xmlHttp=null;
  try
    {
    // Firefox, Opera 8.0+, Safari
    xmlHttp=new XMLHttpRequest();
    }
  catch (e)
    {
    // Internet Explorer
    try
      {
      xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
      }
    catch (e)
      {
      xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
      }
    }
  return xmlHttp;
}

/* ---------------------------------------------------------------- */
/* nl2br - emulate equiv. PHP function
/* ref: http://kevin.vanzonneveld.net/techblog/article/javascript_equivalent_for_phps_nl2br/
/* ---------------------------------------------------------------- */
function nl2br (str, is_xhtml) {
    // http://kevin.vanzonneveld.net
    // +   original by: Kevin van Zonneveld 
    // +   improved by: Philip Peterson
    // +   improved by: Onno Marsman
    // +   improved by: Atli Þór
    // +   bugfixed by: Onno Marsman
 
    breakTag = '<br />';
    if (typeof is_xhtml != 'undefined' && !is_xhtml) {
        breakTag = '<br>';
    }
 
    return (str + '').replace(/([^>]?)\n/g, '$1'+ breakTag +'\n');
}

/* ---------------------------------------------------------------- */
/* This script Created by: Steve Chipman 
/* http://slayeroffice.com/
/* ---------------------------------------------------------------- */

// --- constants to define the title of the alert and button text.
var ALERT_TITLE = "Warning Messages:";
var ALERT_BUTTON_TEXT = "Close";

// --- over-ride the alert method only if this a newer browser.
// --- Older browser will see standard alerts
//if(document.getElementById) {
//  window.alert = function(txt) {
//    createCustomAlert(txt);
//  }
//}

function createCustomAlert(txt) {
  // --- shortcut reference to the document object
  d = document;

  // --- if the modalContainer object already exists in the DOM, bail out.
  if(d.getElementById("modalContainer")) return;

  // --- create the modalContainer div as a child of the BODY element
  mObj = d.getElementsByTagName("body")[0].appendChild(d.createElement("div"));
  mObj.id = "modalContainer";
   // --- make sure its as tall as it needs to be to overlay all the content on the page
  mObj.style.height = document.documentElement.scrollHeight + "px";

  // --- create the DIV that will be the alert 
  alertObj = mObj.appendChild(d.createElement("div"));
  alertObj.id = "alertBox";
  // --- MSIE doesnt treat position:fixed correctly, so this compensates for positioning the alert
  if(d.all && !window.opera) alertObj.style.top = document.documentElement.scrollTop + "px";
  // --- center the alert box
  alertObj.style.left = (d.documentElement.scrollWidth - alertObj.offsetWidth)/2 + "px";

  // --- create an H1 element as the title bar
  h1 = alertObj.appendChild(d.createElement("h1"));
  h1.appendChild(d.createTextNode(ALERT_TITLE));

  // --- create a paragraph element to contain the txt argument
  msg = alertObj.appendChild(d.createElement("p"));
  var newtxt = txt;
  newtxt = newtxt.replace(/</g,"&lt;");
  newtxt = newtxt.replace(/>/g,"&gt;");
  newtxt = newtxt.replace(/(\[([a-z]+)\])/g,'<$2>');
  newtxt = newtxt.replace(/(\[\/([a-z]+)\])/g,'</$2>');
  newtxt = "<div class='warning'>"+newtxt+"</div>";
  msg.innerHTML = nl2br(newtxt);
  
  // --- create an anchor element to use as the confirmation button.
  btn = alertObj.appendChild(d.createElement("a"));
  btn.id = "closeBtn";
  btn.appendChild(d.createTextNode(ALERT_BUTTON_TEXT));
  btn.href = "#";
  // --- set up the onclick event to remove the alert when the anchor is clicked
  btn.onclick = function() { removeCustomAlert();return false; }
}

// --- removes the custom alert from the DOM
function removeCustomAlert() {
  document.getElementsByTagName("body")[0].removeChild(document.getElementById("modalContainer"));
}
