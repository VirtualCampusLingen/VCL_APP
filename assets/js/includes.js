if(navigator.onLine) {
  include('css', 'https://developers.google.com/maps/documentation/javascript/examples/default.css');
  include('js', 'http://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&callback=initialize');
}else {
  include('css', 'assets/css/external/default.css');
  include('js', 'assets/js/external/apiv3.js');
  include('js', 'assets/js/external/main.js');
}

function include(type, source, callback) {
  if (type == 'js'){
    var ele = document.createElement("script");
    ele.type = "text/javascript";
    ele.src = source;
  }
  else if (type == 'css'){
    var ele = document.createElement("link");
    ele.rel = 'stylesheet';
    ele.href= source;
  }
  document.body.appendChild(ele);
}