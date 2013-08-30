
var initPosPanoID, streetView;

      function initialize() {
        //In front of Ardenwood.
        // var initPos = new google.maps.LatLng(37.55631,-122.051153);  

        // Set StreetView provider.

        var streetViewOptions1 = {
          zoom: 1,
          pano: "test",
          panoProvider:  getCustomPanorama1,
          pov : {
            heading : 55,
            pitch : 0,
            zoom : 1
          }
        };
        
        // Create a StreetView object.
        var streetViewDiv = document.getElementById('map-canvas');
        streetViewDiv.style.fontSize = "15px";
        streetView1 = new google.maps.StreetViewPanorama(streetViewDiv, streetViewOptions1);

        // Add links when it happens "links_change" event.
        //google.maps.event.addListener(streetView, "links_changed", createCustomLink);








        var streetViewOptions = {
          zoom: 1,
          pano: "visitor_center",
          panoProvider:  getCustomPanorama,
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

        // Add links when it happens "links_change" event.
        google.maps.event.addListener(streetView, "links_changed", createCustomLink);




        // Create a StreetViewService object.
        // var streetviewService = new google.maps.StreetViewService();
        
        // // Get panorama ID of initPos
        // var radius = 50;
        // streetviewService.getPanoramaByLocation(initPos, radius, function(result, status) {
        //   if (status == google.maps.StreetViewStatus.OK) {
        //     initPosPanoID = result.location.pano;
        //     streetView.setPosition(result.location.latLng);
        //   }
        // });
        
      }

      function getCustomPanoramaTileUrl(panoID, zoom, tileX, tileY) {
        // Return a pano image given the panoID.
        return 'images/Pano2zu1.jpg';
      }

      function getCustomPanoramaTileUrl1(panoID, zoom, tileX, tileY) {
        // Return a pano image given the panoID.
        return 'images/bild.jpg';
      }

      function getCustomPanorama(panoID) {
        var streetViewPanoramaData = {
          links: [],
          copyright: 'Imagery (c) Masashi Katsumata',
          tiles: {
              tileSize: new google.maps.Size(256, 256),
              worldSize: new google.maps.Size(2048, 1024),
              centerHeading: 0,
              getTileUrl: getCustomPanoramaTileUrl
           }
        };

        var streetViewPanoramaData1 = {
          links: [],
          copyright: 'Imagery (c) Masashi Katsumata',
          tiles: {
              tileSize: new google.maps.Size(256, 256),
              worldSize: new google.maps.Size(2048, 1024),
              centerHeading: 0,
              getTileUrl: getCustomPanoramaTileUrl1
           }
        };

        switch(panoID) {
          case "visitor_center":
            streetViewPanoramaData["location"] = {
              pano: 'visitor_center',
              description: "Visitor center",
              latLng: new google.maps.LatLng(37.556429,-122.050745)
            };
            return streetViewPanoramaData;
          case "test":
            console.log("blub");
            streetViewPanoramaData1["location"] = {
              pano: "test",
              description: "Visitor center",
              latLng: new google.maps.LatLng(37.556429,-122.050745)
            };
            return streetViewPanoramaData1;
        }
      }

      function getCustomPanorama1(panoID) {
        var streetViewPanoramaData1 = {
          links: [],
          copyright: 'Imagery (c) Masashi Katsumata',
          tiles: {
              tileSize: new google.maps.Size(256, 256),
              worldSize: new google.maps.Size(2048, 1024),
              centerHeading: 0,
              getTileUrl: getCustomPanoramaTileUrl1
           }
        };
        switch(panoID) {
          case "test":
            streetViewPanoramaData1["location"] = {
              pano: "test",
              description: "Keksee",
              latLng: new google.maps.LatLng(37.556429,-122.050745)
            };
            return streetViewPanoramaData1;
        }
      }




      function createCustomLink() {
        /*
         * add links
         */
        var links = streetView.getLinks();
        var panoID = streetView.getPano();


        switch(panoID) {
          case "test":
            console.log("drin");
            links.push({
              description : "Welcome to Ardenwood",
              pano : "visitor_center",
              heading : 71
            });
            break;
            
          case "visitor_center":
            links.push({
              description : "Parking",
              pano : "test",
              heading : 248
            });
            break;
        } 
      }

      google.maps.event.addDomListener(window, 'load', initialize);