
var initPosPanoID, streetView;

function initialize() {
  var streetViewOptions = {
    zoom: 1,
    panoProvider:  getCustomPanorama,
    pano:  "62",
    pov : {
      heading : 270,
      pitch : 0,
      zoom : 0
    }
  };
  // Create a StreetView object.
  var streetViewDiv = document.getElementById('map-canvas');
  streetViewDiv.style.fontSize = "15px";
  streetView = new google.maps.StreetViewPanorama(streetViewDiv, streetViewOptions);

  google.maps.event.addListener(streetView, "links_changed", createCustomLink);
  google.maps.event.addListener(streetView, "pano_changed", preLoadImg);
}

function preLoadImg(){
  var panoID = streetView.getPano();
  var panoJson = getPanoJson(panoID);
  var neighbours = panoJson.neighbours;
  for(key in neighbours){
    if (neighbours.hasOwnProperty(key)){
      var obj = neighbours[key];
      $.ajax({
        url: 'admin/'+obj.path,
        success: function(){
          $('<img/>')[0].src = 'admin/'+obj.path
        }
      })
    }
  }
}

function getCustomPanoramaTileUrl(panoID, zoom, tileX, tileY) {
  // Return a pano image given the panoID.
  return pano.path;
}

function getCustomPanorama(panoID) {
  var panoJson = getPanoJson(panoID);
  var info_texts = panoJson.info_texts;
  $("#info").empty()
  for(key in info_texts){
    if (info_texts.hasOwnProperty(key)){
      // console.log(key)
      // console.log(info_texts)
      // console.log(info_texts[key])
      addInfoIcon();
    }
  }

  var streetViewPanoramaData = {
    links: [],
    copyright: 'Imagery (c) VCL',
    location: {
      pano: panoJson.id,
      description: panoJson.description
    },
    tiles: {
        tileSize: new google.maps.Size(2048, 1024),
        worldSize: new google.maps.Size(2048, 1024),
        getTileUrl: getCustomPanoramaTileUrl
     }
  };

  return streetViewPanoramaData;
}

function createCustomLink() {
  /*
   * add links
   */
  var links = streetView.getLinks();
  var panoID = streetView.getPano();
  //get panoJson
  var panoJson = getPanoJson(panoID);
  //detect neighbors
  var neighbours = panoJson.neighbours;
  for(key in neighbours){
    if (neighbours.hasOwnProperty(key)){
      var obj = neighbours[key];
      links.push({
        description: obj.description,
        pano: obj.neighbour_id,
        heading: obj.heading
      });
    }
  }
}

function getPanoJson(panoID){
  if (window.XMLHttpRequest){// code for IE7+, Firefox, Chrome, Opera, Safari
    var xhr = new XMLHttpRequest();
  }else{// code for IE6, IE5
    var xhr = new ActiveXObject("Microsoft.XMLHTTP");
  }

  xhr.open("GET", "/admin/test_new.php?id="+panoID, false);
  xhr.send();
  var response = xhr.responseText;
  var json = JSON.parse(response);
  pano = json["Panoid"];
  return pano;
}

function addInfoIcon(){
  var text = "Lorem Ipsum Foo Bar Lorem <b>Ipsum</b> Foo Bar Lorem Ipsum Foo Bar Lorem Ipsum Foo Bar Lorem Ipsum Foo Bar"
  var id = Date.now()
  $("#info").appendPartial('info_modal', function(){
    $("#temp").attr("id", id);
    $("button[data-target='#temp']").attr("data-target", "#"+id);
    $("#"+id+" .modal-body").html(text);
  })
}

jQuery.fn.extend({
  appendPartial: function(name, callback){
    var element = this
    $.get("_"+name+".html", function(data){
      $(element).append(data)
      if(typeof callback != 'undefined') callback();
    })
  }
});

google.maps.event.addDomListener(window, 'load', initialize);