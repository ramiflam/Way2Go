<!DOCTYPE html>
<html>
  <head>
   <link rel="stylesheet" type="text/css" href="settingsPage.css">

  </head>
  <body>
    <div id="map" class="mapSnippet"></div> 
    <script>
      function initMap() {
        var myLatLng = {lat: 39.9981673, lng: -75.5741956};
        var mapDiv = document.getElementById('map');
        var map = new google.maps.Map(mapDiv, {
          center: myLatLng,
          zoom: 12
        });
        
        var marker = new google.maps.Marker({
    	  position: myLatLng,
    	  map: map,
    	  title: 'Boot Road'
        });
       }
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAw5gl1LJqMre1o3JztvMM7jK_qDbB5pBk&callback=initMap"
        async defer></script>
  </body>
</html>