var directionsDisplay;
var directionsService = new google.maps.DirectionsService();
var map;

function initialize(home_base) {
  // Find latitude and longitude based on user address
  geocoder = new google.maps.Geocoder();
  geocoder.geocode({address: home_base}, function(results, status) {
    if (status == google.maps.GeocoderStatus.OK) 
    {
      // Start map centred on user home base address
      directionsDisplay = new google.maps.DirectionsRenderer();
      var home_base_latitude = results[0].geometry.location.lat();
      var home_base_longitude = results[0].geometry.location.lng();
      var home_base_start = new google.maps.LatLng(home_base_latitude, home_base_longitude);
      var mapOptions = {
        zoom: 15,
        center: home_base_start
      }
      map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);

      directionsDisplay.setMap(map);
    }
    else
    {
      alert("Could not pinpoint starting address - " + status);
    }
  });
}

function calcRoute(home_base, location_stops) {
  var waypoints = [];
  
  for (i = 0; i < location_stops.length; i++)
  {
    waypoints.push({location:location_stops[i], stopover:true});
  }

  // home_base will be start and end to make a route loop  
  var request = {
    origin: home_base,
    destination: home_base,
    waypoints: waypoints,
    optimizeWaypoints: true,
    travelMode: google.maps.TravelMode.DRIVING
  };

  directionsService.route(request, function(response, status) {
    if (status == google.maps.DirectionsStatus.OK) {
      directionsDisplay.setDirections(response);
    }
  });
}