<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>testMapapi</title>
    <script src="https://code.jquery.com/jquery-3.4.1.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    <style type="text/css">
        body{
            background-color: orange;
        }
        #map {
            width: 800px;
            height: 400px;
        }
        .title{
            font-family: Fantasy;
        }
        @media only screen and (max-width: 768px) {
           body{
               background-color: orange;
           }
            #map {
                width: 600px;
                height: 300px;
            }
            .title{
                font-family: Fantasy;
            }
        }

        @media only screen and (max-width: 500px) {
            body{
                background-color: orange;
            }
            #map {
                width: 300px;
                height: 200px;
            }
            .title{
                font-family: Fantasy;
            }
        }

    </style>
</head>


<body>
<div style="padding: 50px" class="container">
    <h2 class="title">Bang Sue Restaurants</h2>
    <div class="row">
        <div class="col-sm">
            <div id="map"></div>
            <div style="padding: 10px;text-align: end"><input id="search-input" type="text" placeholder="Search for a location"></div>
        </div>
        <div class="col-sm">
            <div id="listname">
            </div>
        </div>
    </div>
</div>

</body>


<script type="text/javascript">
    function initMap() {
        const myLatLng = {lat: 13.8283, lng: 100.5285};
        const map = new google.maps.Map(document.getElementById("map"), {
            zoom: 15,
            center: myLatLng,
            mapTypeId: 'hybrid'
        });

        var request = {
            location: myLatLng,
            radius: '100',
            type: ['restaurant']
        };

        var service = new google.maps.places.PlacesService(map);
        service.nearbySearch(request, function (results, status) {
            console.log(results[0].vicinity)
            if (status === google.maps.places.PlacesServiceStatus.OK) {
                for (var i = 0; i < results.length; i++) {
                    createMarker(results[i], map);
                    createList(results[i], i + 1);
                }
            }
        });

        function createMarker(place, map) {
            var marker =
                new google.maps.Marker({
                    map: map,
                    position: place.geometry.location,
                });
        }

        function createList(place, no) {
            $('#listname').append(
                '<div><h5>' + no + '.' + place.name + '</h5></div>'+
                '<div><h6>' + place.vicinity + '</h6></div>'
            )
        }

        var input = document.getElementById('search-input');
        var searchBox = new google.maps.places.SearchBox(input);
        // map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);
        //
        map.addListener('bounds_changed', function () {
            searchBox.setBounds(map.getBounds());
        });

        searchBox.addListener('places_changed', function () {
            var places = searchBox.getPlaces();

            if (places.length === 0) {
                return;
            }
            var bounds = new google.maps.LatLngBounds();
            places.forEach(function (place) {
                if (!place.geometry) {
                    console.log("Returned place contains no geometry");
                    return;
                }

                var icon = {
                    url: place.icon,
                    size: new google.maps.Size(71, 71),
                    origin: new google.maps.Point(0, 0),
                    anchor: new google.maps.Point(17, 34),
                    scaledSize: new google.maps.Size(25, 25)
                };

                var marker = new google.maps.Marker({
                    map: map,
                    icon: icon,
                    title: place.name,
                    position: place.geometry.location
                });

                if (place.geometry.viewport) {
                    bounds.union(place.geometry.viewport);
                } else {
                    bounds.extend(place.geometry.location);
                }
            });
            map.fitBounds(bounds);
        });
    }

    // window.initMap = initMap;
</script>
<script
    src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAP_KEY') }}&libraries=places&callback=initMap">
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe"
        crossorigin="anonymous"></script>
</html>
