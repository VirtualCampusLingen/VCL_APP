
var initPosPanoID, streetView;

function initialize() {
  //In front of Ardenwood.
  // var initPos = new google.maps.LatLng(37.55631,-122.051153);  

  // Set StreetView provider.

  var streetViewOptions = {
    zoom: 1,
    panoProvider:  getCustomPanorama,
    pano: "Pano01",
    pov : {
      heading : 55,
      pitch : 0,
      zoom : 0
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
  //return "images/ba2_1_4096.jpg"
}

function getCustomPanorama(panoID) {
  var streetViewPanoramaData = {
    links: [],
    copyright: 'Imagery (c) Masashi Katsumata',
    tiles: {
        tileSize: new google.maps.Size(2048, 1024),
        worldSize: new google.maps.Size(2048, 1024),
        centerHeading: 140,
        getTileUrl: getCustomPanoramaTileUrl
     }
  };

  switch(panoID) {
    case "Pano01":
      streetViewPanoramaData["location"] = {
        pano: 'Pano01',
        description: "BA 2 FotoNr: 1",
      };
      return streetViewPanoramaData;
    
    case "ba2_2":
      streetViewPanoramaData["location"] = {
        pano: "ba2_2",
        description: "BA 2 FotoNr: 2"
      };
      return streetViewPanoramaData;
    
    case "ba2_3":
      streetViewPanoramaData["location"] = {
        pano: "ba2_3",
        description: "BA 2 FotoNr: 3",
      };
      return streetViewPanoramaData;
    
    case "ba2_4":
      streetViewPanoramaData["location"] = {
        pano: "ba2_4",
        description: "BA 2 FotoNr: 4",
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
    case "Pano01":
      links.push({
        description : "Zum Eingang",
        pano : "ba2_2",
        heading : 71
      },{
        description: "Zur Treppe",
        pano: "ba2_3",
        heading: 350
      },{
        description: "S1",
        pano: "ba2_4",
        heading: 320
      });
      break;
      
    case "ba2_2":
      links.push({
        description : "Zur Treppe",
        pano : "ba2_3",
        heading : 150
      });
      break;

  case "ba2_3":
      links.push({
        description : "S1",
        pano : "ba2_4",
        heading : 320
      });
      break;

  case "ba2_4":
      links.push({
        description : "Zum Sekretariat",
        pano : "ba2_1",
        heading : 150
      });
      break;
  } 
}

google.maps.event.addDomListener(window, 'load', initialize);