var map = null;
var markers = [];
var circles = [];
var restaurants = [];
var places = [];
var directionsDisplay;
var directionsService;
var currentIndex = 0;
var cebu = {
    lat: 10.3157,
    lng: 123.8854
};
var myCurrentLocation;

function initMap() {
    directionsDisplay = new google.maps.DirectionsRenderer;
    directionsService = new google.maps.DirectionsService;
    var options = {
        zoom: 14,
        center: cebu
    };
    map = new google.maps.Map(document.getElementById('map'), options);
    directionsDisplay.setMap(map);
    directionsDisplay.setPanel(document.getElementById('direction-panel'));
    getRestaurants();
    getPlaces();
}

function getRestaurants() {

    $.getJSON("./getrestaurants", function (e) {
        loadMarker(e);
        loadInfoToPanel(e);
        setRestaurants(e);
    });
}

function loadMarker(e) {
    markers = [];
    $.each(e.results, function (i, result) {
        addMarker(result, false, i);
    });
}

function setRestaurants(e) {
    restaurants = [];
    $.each(e.results, function (i, result) {
        restaurants.push(result);
    });
}

function addMarker(e, isOpen = false, index = 0) {
    let marker = new google.maps.Marker({
        position: e.geometry.location,
        map: map
    });

    let address_lat = e.geometry.location.lat;
    let address_lng = e.geometry.location.lng;

    let infoWindow = new google.maps.InfoWindow({
        content: '<div class="infoWindow"><b>' + e.name + '</b><br />' +
            e.formatted_address +
            '<br /><a href="#" onclick="calculateAndDisplayRoute(' + address_lat + ',' + address_lng + ',' + index + ')">Get Direction</a>' +
            '</div>'
    });

    marker.addListener('click', function () {
        infoWindow.open(map, marker);
    })

    markers.push(marker);

    if (isOpen == true) {
        infoWindow.open(map, marker);
    }
}

function clearMarkers() {
    setMapOnAll(null);
    markers = [];
}

function setMapOnAll(map) {
    for (var i = 0; i < markers.length; i++) {
        markers[i].setMap(map);
    }
}

// for menu
$(document).ready(function () {
    $('.dropdown-submenu a.test').on("click", function (e) {
        $(this).next('ul').toggle();
        e.stopPropagation();
        e.preventDefault();
    });
});

// load info to layer
function loadInfoToPanel(e) {

    let info = "";
    let index = 0;

    $('#panelContent').html("");
    $.each(e.results, function (i, result) {
        
        let address_lat = result.geometry.location.lat;
        let address_lng = result.geometry.location.lng;

        info = info + '<div id="panelContentDetail"><a href="#" onclick="setRestaurantMarker(' + index + ')"><b>' + result.name + "</b></a><br />" + result.formatted_address + 
        '<br /><a href="#" onclick="calculateAndDisplayRoute(' + address_lat + ',' + address_lng + ',' + index + ')"><small>Get Direction</small></a>' +
        '&nbsp;|&nbsp;<small><a href="#" onclick="showStats(' + index + ')">View Statistics</a></small>' +
        "</div>";
        index++;
    });

    $('#panelContent').html(info);
}

function setRestaurantMarker(index) {
    let result = restaurants[index];
    setMapOnAll(null);
    addMarker(result, true, index);
}

function calculateAndDisplayRoute(end_lat, end_lng, index) {
    currentIndex = index;
    showPanel("direction-panel");
    setRestaurantMarker(index);

    //Listen for click on map
    var listenerHandle = google.maps.event.addListener(map, 'click', function (event) {
        clearMarkers();
        setRestaurantMarker(index);
        myCurrentLocation = event.latLng;
        let marker = new google.maps.Marker({
            position: myCurrentLocation,
            map: map
        });
        markers.push(marker);

        // show selected latLong
        let msg = `Start Location<br><br><b>Latitude:${myCurrentLocation.lat()} <br />Longitude: ${myCurrentLocation.lng()}</b>`;
        msg = msg + `<br /><br /><a href='#' onclick='calculateAndDisplayRoute(${end_lat}, ${end_lng}, ${index})'>Show direction</a>`;
        geoMessage(msg);
        return false;
    });

    if (myCurrentLocation) {
        clearMarkers();
        //var start = { lat: 10.3457599, lng: 123.9132848};
        var start = myCurrentLocation;
        var end = {
            lat: end_lat,
            lng: end_lng
        };
        directionsDisplay.setMap(map);
        directionsService.route({
            origin: start,
            destination: end,
            travelMode: 'DRIVING'
        }, function (response, status) {
            if (status === 'OK') {
                directionsDisplay.setDirections(response);
                myCurrentLocation = null;
            } else {
                window.alert('Directions request failed due to ' + status);
            }
        });

        $html = "<b>" + restaurants[index].name + "</b><br />" + restaurants[index].formatted_address;
        $("#direction-restaurant").html($html);

        try {
            google.maps.event.removeListener(listenerHandle);
        } catch (e) {}
  
    } else {
        geoMessage('Please choose a start location. To select, just right click on the map.');
    }

}

function directionBack() {
    showPanel("panelContent");
    directionsDisplay.setMap(null);
    map.setZoom(14);
    map.setCenter(cebu);
    setRestaurantMarker(currentIndex);
}

function showPanel(name) {
    $("#direction-panel").hide();
    $("#panelContent").hide();
    $("#byRadiusContent").hide();
    $("#specificFoodContent").hide();
    if (name == 'direction-panel')
        $("#direction-panel").show();
    else if (name == 'panelContent')
        $("#panelContent").show();
    else if (name == 'byRadiusContent')
        $("#byRadiusContent").show();
    else if (name == 'specificFoodContent')
        $("#specificFoodContent").show();
}

function geoMessage(msg) {
    let html = `<div class="well" id="direction-restaurant">${msg}</div><a href="#" onclick="directionBack()">back</a>`;
    $('#direction-panel').html(html);
    showPanel("direction-panel");
}

function getPlaces() {
    $.getJSON("./getplaces", function (e) {
        $.each(e, function (i, result) {
            places.push(result);
        });
    });
}

function showRadiusPalnel() {
    showPanel('byRadiusContent');
    $('#placesRadius').empty();
    $('#radius').empty();
    $.each(places, function (i, item) {
        $('#placesRadius').append($('<option>', {
            value: item.id,
            text: item.formatted_address
        }));
    });
    for (let x = 1; x <= 5; x++) {
        $('#radius').append($('<option>', {
            value: x,
            text: `${x} Kilometers`
        }));
    }
}

function drawCircle() {

    let placeIndex = parseInt($('#placesRadius').children("option:selected").val()) -1;
    let radius = parseInt($('#radius').children("option:selected").val());

    let lat = parseFloat(places[placeIndex].lat);
    let lng = parseFloat(places[placeIndex].lng);
    let latLng = {
        lat: lat,
        lng: lng
    };

    radius = (radius / 6378.1) * 6378100; //compute kilometer
    clearMarkers();
    directionsDisplay.setMap(null);

    // clear circle
    for (var i = 0; i < circles.length; i++) {
        circles[i].setMap(null);
    }
    circles = [];

    circle = new google.maps.Circle({
        center: latLng,
        clickable: false,
        draggable: false,
        editable: false,
        fillColor: '#004de8',
        fillOpacity: 0.17,
        map: map,
        radius: radius,
        strokeColor: '#004de8',
        strokeOpacity: 0.62,
        strokeWeight: 1
    });

    circles.push(circle);
    map.setZoom(14);
    map.setCenter(latLng);

    searchByRadius(lat,lng,radius);
}

function searchByRadius(lat,lng,radius)
{
    $.getJSON(`./getbyradius/${lat}/${lng}/${radius}`, function (e) {
        loadMarker(e);
        loadInfoToPanel(e);
        setRestaurants(e);
    });
}

function searchBySpecificFood(me)
{
    let lat = cebu.lat;
    let lng = cebu.lng;
    let latLng = {
        lat: lat,
        lng: lng
    };

    map.setZoom(14);
    map.setCenter(latLng);

    let keyword = $('#searchFood').val();
    clearMarkers();
    clearCircles(null);
    directionsDisplay.setMap(null);

    if(keyword==null || keyword==""){
        alert('Please enter a value.');
    }else{
        me.disabled = true;
        $.getJSON(`./getbyspecific/${lat}/${lng}/?keyword=${keyword}`, function (e) {
            if(e==null || e==''|| e.results.length==0){
                alert('No record found.');
            }else{
                loadMarker(e);
                loadInfoToPanel(e);
                setRestaurants(e);
            } 
            me.disabled = false;
        });
    }
}

function searchByType(foodType)
{
    let lat = cebu.lat;
    let lng = cebu.lng;
    let latLng = {
        lat: lat,
        lng: lng
    };

    map.setZoom(14);
    map.setCenter(latLng);
    showPanel("panelContent");
    
    clearMarkers();
    clearCircles(null);
    directionsDisplay.setMap(null);
    $.getJSON(`./getbytype/${lat}/${lng}/${foodType}`, function (e) {
        loadMarker(e);
        loadInfoToPanel(e);
        setRestaurants(e);    
    });
}

function clearCircles(circle)
{
    // clear circle
    for (var i = 0; i < circles.length; i++) {
        circles[i].setMap(circle);
    }
    circles = [];
}

// stats
function showStats(index){

    $("#visit_date").val("");
    $("#stat_specific_food").val("");
    $("#statIndex").val(index);

    var today = new Date();
    var day=today.getDate()>9?today.getDate():"0"+today.getDate(); // format should be "DD" not "D" e.g 09
    var month=(today.getMonth()+1)>9?(today.getMonth()+1):"0"+(today.getMonth()+1);
    var year=today.getFullYear();

    $("#visit_date").attr('max', year + "-" + month + "-" + day);
    $(".ui-dialog-titlebar-close").text('x');

    let restaurant = restaurants[index];
    let name = restaurants[index].name;
    let address = restaurants[index].formatted_address;

    let restaurantInfo = `<b>${name}</b><br><small>${address}</small>`;

    $("#statRestaurantInfo").html(restaurantInfo);

    $( "#statsWindow" ).dialog({
        width: 800,
        height:500    
    });

    $( "#statRestaurantSurvey" ).show();
    $( "#realStatInfo" ).hide();
}

function saveRealStatInfo(){
    
    let index = $("#statIndex").val();
    let vdate = $("#visit_date").val();
    let food = $("#stat_specific_food").val();
    let validatorOK = true;

    if(vdate==null || vdate==""){
        validatorOK = false;
    }else if(food==null || food==""){
        validatorOK = false;
    }

    if(validatorOK == false){
        alert('Please enter a value to all the fields.');
    }else{
        $("#saveStatbutton").disabled = true;
        let lat = restaurants[index].geometry.location.lat;
        let lng = restaurants[index].geometry.location.lng;
        let name = restaurants[index].name;

        let urlSaveFood = `./stat/savefood/${lat}/${lng}?n=${name}&f=${food}`;
        $.get(urlSaveFood, function (e) {
            let urlSaveVisit = `./stat/savevisit/${lat}/${lng}?n=${name}&v=${vdate}`;
            $.get(urlSaveVisit, function (e) {
                $("#saveStatbutton").disabled = false;
                alert('Thank you. You can add more entry as you want or you may proceed or skip the survey.');
            });
        });
    }
}

//load chart
google.charts.load('current', {'packages':['corechart']});
google.charts.setOnLoadCallback(drawChart);

function showRealStatInfo(){

    const monthNames = ["January", "February", "March", "April", "May", "June",
                        "July", "August", "September", "October", "November", "December"
                        ];
    
    const d = new Date();
    let monthName = monthNames[d.getMonth()];

    let index = $("#statIndex").val();
    let lat = restaurants[index].geometry.location.lat;
    let lng = restaurants[index].geometry.location.lng;
    let name = restaurants[index].name;

    let urlGetFood = `./stat/foods/${lat}/${lng}?n=${name}`;
    let urlGetVisit = `./stat/vistors/${lat}/${lng}?n=${name}`;

    $( "#statRestaurantSurvey" ).hide();
    $( "#realStatInfo" ).show();
    $( "#ulFoodList" ).empty();

    $.getJSON(urlGetFood, function (foods) {
        if(foods.length){
            for(i=0; i<foods.length;i++){
                $( "#ulFoodList" ).append(`<li>${foods[i]}</li>`);
            }       
        }else{
            $( "#ulFoodList" ).append(`<li>No data yet.</li>`);
        }     
    });

    $.getJSON(urlGetVisit, function (chartsData) {
        let chartTitle = ["Days","Visitors"];
        let chartArray = Array();
        chartArray.push(chartTitle);
        $.each(chartsData, function (i, data) {
            let dayStr = `${monthName} ${i}`;
            let contentData = parseInt(data);
            let contentChart = [dayStr,contentData];
            chartArray.push(contentChart);
        });
        drawChart(chartArray,monthName);
    });
}

function drawChart(chartDetails,month) {
    var data = google.visualization.arrayToDataTable(chartDetails);
    var options = {
      title: 'Visitors for the Month of ' + month,
      curveType: 'function',
      legend: { position: 'top' }
    };
    var chart = new google.visualization.LineChart(document.getElementById('curve_chart'));
    chart.draw(data, options);
}