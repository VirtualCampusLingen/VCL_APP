
var initPosPanoID, streetView;

function initialize() {
  //In front of Ardenwood.
  // var initPos = new google.maps.LatLng(37.55631,-122.051153);  

  // Set StreetView provider.

  var streetViewOptions = {
    zoom: 1,
    panoProvider:  getCustomPanorama,
    pano: "ba2_1",
    pov : {
      heading : 55,
      pitch : 0,
      zoom : 1
    }
  };
  
  // Create a StreetView object.
  var streetViewDiv = document.getElementById('map-canvas');
  streetViewDiv.style.fontSize = "15px";
  streetView = new google.maps.StreetViewPanorama(streetViewDiv, streetViewOptions);
  
  google.maps.event.addListener(streetView, "links_changed", createCustomLink);
}

function getCustomPanoramaTileUrl(panoID, zoom, tileX, tileY) {
  // Return a pano image given the panoID.
  //return "images/PanoTest/"+tileX+"-"+tileY+".jpg";
  return "images/"+panoID+".jpg"
}

function getCustomPanorama(panoID) {
  var streetViewPanoramaData = {
    links: [],
    copyright: 'Imagery (c) Masashi Katsumata',
    tiles: {
        tileSize: new google.maps.Size(4096, 2048),
        worldSize: new google.maps.Size(4096, 2048),
        centerHeading: 140,
        getTileUrl: getCustomPanoramaTileUrl
     }
  };

  switch(panoID) {
    case "ba2_1":
      streetViewPanoramaData["location"] = {
        pano: 'ba2_1',
        description: "BA 2 FotoNr: 1",
      };
      return streetViewPanoramaData;
    
    case "pano_test":
      streetViewPanoramaData["location"] = {
        pano: "pano_test",
        description: "Test Foto",
      };
      return streetViewPanoramaData;
  }
}



function createCustomLink() {
  /*
   * add links
   */
  var links = streetView.getLinks();
  var panoID = streetView.getPano();


  switch(panoID) {
    case "ba2_1":
      links.push({
        description : "Zum Test Foto",
        pano : "pano_test",
        heading : 71
      });
      break;
      
    case "pano_test":
      links.push({
        description : "Zur BA2",
        pano : "ba2_1",
        heading : 248
      });
      break;
  } 
}

google.maps.event.addDomListener(window, 'load', initialize);