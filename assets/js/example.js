
var initPosPanoID, streetView;

function initialize() {
  //In front of Ardenwood.
  // var initPos = new google.maps.LatLng(37.55631,-122.051153);  

  // Set StreetView provider.

  var streetViewOptions = {
    zoom: 1,
    panoProvider:  getCustomPanorama,
    pano: "campus01",
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
  return "images/2048x1024/"+panoID+".jpg"
  //return "images/ba2_1_4096.jpg"
}

function getCustomPanorama(panoID) {
  var streetViewPanoramaData = {
    links: [],
    copyright: 'Imagery (c) VCL',
    tiles: {
        tileSize: new google.maps.Size(2048, 1024),
        worldSize: new google.maps.Size(2048, 1024),
        centerHeading: 140,
        getTileUrl: getCustomPanoramaTileUrl
     }
  };

  switch(panoID) {
    case "campus01":
      streetViewPanoramaData["location"] = {
        pano: 'campus01',
        description: "Campus 01",
      };
      return streetViewPanoramaData;
    
    case "campus02":
      streetViewPanoramaData["location"] = {
        pano: "campus02",
        description: "Campus 02"
      };
      return streetViewPanoramaData;
    
    case "campus03":
      streetViewPanoramaData["location"] = {
        pano: "campus03",
        description: "Campus 03",
      };
      return streetViewPanoramaData;
    
    case "campus04":
      streetViewPanoramaData["location"] = {
        pano: "campus04",
        description: "Campus 04",
      };
      return streetViewPanoramaData;

    case "campus04":
      streetViewPanoramaData["location"] = {
        pano: "campus04",
        description: "Campus 04",
      };
      return streetViewPanoramaData;

    case "campus05":
      streetViewPanoramaData["location"] = {
        pano: "campus05",
        description: "Campus 05",
      };
      return streetViewPanoramaData;

    case "ba201":
      streetViewPanoramaData["location"] = {
        pano: "ba201",
        description: "BA2 01",
      };
      return streetViewPanoramaData;

    case "ba202":
      streetViewPanoramaData["location"] = {
        pano: "ba202",
        description: "BA2 02",
      };
      return streetViewPanoramaData;

    case "ba203":
      streetViewPanoramaData["location"] = {
        pano: "ba203",
        description: "BA2 03",
      };
      return streetViewPanoramaData;

    case "ba204":
      streetViewPanoramaData["location"] = {
        pano: "ba204",
        description: "BA2 04",
      };
      return streetViewPanoramaData;

    case "ba205":
      streetViewPanoramaData["location"] = {
        pano: "ba205",
        description: "BA2 05",
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
    case "campus01":
      links.push({
        description : "",
        pano : "campus02",
        heading : 80
      });
      break;
      
    case "campus02":
      links.push({
        description : "",
        pano : "campus03",
        heading : 170
      },{
        description: "",
        pano: "campus04",
        heading: 80
      },{
        description: "",
        pano: "campus05",
        heading: 260
      },{
        description: "",
        pano: "campus01",
        heading: 350
      });
      break;

  case "campus03":
      links.push({
        description : "",
        pano : "campus04",
        heading : 20
      },{
        description: "",
        pano: "campus02",
        heading: 320
      });
      break;

  case "campus04":
      links.push({
        description : "",
        pano : "campus02",
        heading : 250
      });
      break;
  case "campus05":
      links.push({
        description : "Foyer BA2",
        pano : "ba201",
        heading : 220
      });
      break;

  case "ba201":
      links.push({
        description : "",
        pano : "ba202",
        heading : 160
      });
      break;

  case "ba202":
      links.push({
        description : "",
        pano : "ba203",
        heading : 150
      });
      break;

  case "ba203":
      links.push({
        description : "",
        pano : "ba204",
        heading : 80
      });
      break;

  case "ba204":
      links.push({
        description : "Eingang Campus",
        pano : "campus01",
        heading : 60
      });
      break;
  }
}

//google.maps.event.addDomListener(window, 'load', initialize);
