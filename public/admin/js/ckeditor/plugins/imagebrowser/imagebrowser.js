// <![CDATA[
var current_li = 'c_li';    // id of current accessed li-menu
var current_span = 'c_span';    // id of current accessed span in li-menu
var title_dir = document.getElementById(current_li).querySelector('span').title;    // title with dir-path of current span-li
var li_name = document.getElementById(current_li).querySelector('span').innerHTML;     // name of current accessed menu-list
var imgs = document.getElementById('imgs');    // element with images

// To get value of imgroot and CKEditorFuncNum from URL
var url = location.href;    // current page address
var imgroot = url.match(/imgroot=([^&]*)/) ? url.match(/imgroot=([^&]*)/)[1] : null;
var CKEditorFuncNum = url.match(/CKEditorFuncNum=([0-9]+)/) ? url.match(/CKEditorFuncNum=([0-9]+)/)[1] : null;

// Ajax, receives the url of file to access, data to send, and a callback function (called when the response is received)
function ajaxSend(datasend, callback) {
  imgs.innerHTML = '<h1>Loading ...: '+ li_name +'</h1>';    // message till ajax-response

  var request =  (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject("Microsoft.XMLHTTP");      // sets the XMLHttpRequest instance
  datasend += '&isajax=1';    // to know in php it is ajax request
  if(imgroot != null) datasend += '&imgroot='+ imgroot;

  request.open("POST", 'imagebrowser.php');			// define the request

  // adds  a header to tell the PHP script to recognize the data as is sent via POST, and send data
  request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  request.send(datasend);

  // Check request status,  when the response is completely received pass it to callback function
  request.onreadystatechange = function() {
    if (request.readyState == 4) {
      callback(request.responseText);
    }
  }
}

// callback from Ajax
function ajaxCallback(response) {
  var content = JSON.parse(response);

  if(response.match(/ERROR from PHP:/))  imgs.innerHTML = '<h2>'+ content +'</h2>';
  else {
    // add new menu in current clicked list
    if(document.getElementById(current_li)) document.getElementById(current_li).innerHTML = '<span title="'+ title_dir +'" id="'+ current_span +'">'+ li_name +'</span>'+ content.menu;
    imgs.innerHTML = content.imgs;
    regEv();
  }
}

// to register events
function regEv() {
  if(document.getElementById('menu')) {
    // get menu LIs
    var lists = document.getElementById('menu').querySelectorAll('li span');
    var nr_lists = lists.length;

    // register click to eack span-li
    if(nr_lists > 0) {
      for(var i=0; i<nr_lists; i++) {
        lists[i].addEventListener('click', function(e){
          if(e.target.id == current_span) return false;
          else {
            // removes and sets id for current element
            if(document.getElementById(current_li)) document.getElementById(current_li).removeAttribute('id');
            if(document.getElementById(current_span)) document.getElementById(current_span).removeAttribute('id');
            e.target.parentNode.setAttribute('id', current_li);
            li_name = e.target.childNodes[0].nodeValue;
            title_dir = e.target.title;

            ajaxSend('imgdr='+ title_dir, ajaxCallback);    // get data from php
          }
        }, false);
      }
    }

    // get images and register click to eack img, to acces window.parent.CKEDITOR.tools.callFunction()
    var img_all = imgs.querySelectorAll('img');
    var nr_img_all = img_all.length;

    // register click to eack span-li
    if(nr_img_all > 0) {
      for(var i=0; i<nr_img_all; i++) {
        img_all[i].addEventListener('click', function(e){
          if(CKEditorFuncNum !== null) window.opener.CKEDITOR.tools.callFunction(CKEditorFuncNum, e.target.src);
          window.close();
        }, false);
      }
    }
  }
}

ajaxSend('', ajaxCallback);    // get data from php
// ]]>