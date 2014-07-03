
var map;
var marker;
var markersArray 	= [];
var distance		= 5;
var zoomLvl	  		= 7;
var infowindow 		= new google.maps.InfoWindow();
var contentString 	= new Array();
var geocoder = new google.maps.Geocoder();
var itemLocality='';
var itemCountry='';
var default_lat = 61.976;
var default_lng = 26.289;
var isProfile = false;

/**
 * Initialize map
 */
function initialize() {
    var mapOptions = {
        center: new google.maps.LatLng( default_lat , default_lng),
        zoom: zoomLvl,
        mapTypeId: google.maps.MapTypeId.TERRAIN
    };

    map = new google.maps.Map(document.getElementById('map_canvas'),
        mapOptions);

    if(isProfile){
        var markPos = new google.maps.LatLng(default_lat,default_lng);
        marker = new google.maps.Marker({
            position: markPos,
            map: map,
            icon: '/bundles/tdomview/images/Icon_gamepiece_purple.png'
        });
    }

    executeForProfile();
}

/**
 * executeForProfile custom method for interactive map with user profile
 */
function executeForProfile() {
    var triangleCoords = [
        new google.maps.LatLng(25.774252, -80.190262),
        new google.maps.LatLng(18.466465, -66.118292),
        new google.maps.LatLng(32.321384, -64.75737)
    ];

    var bermudaTriangle = new google.maps.Polygon({
        paths: triangleCoords
    });

    google.maps.event.addListener(map, 'click', function(e) {

        if(marker)
            marker.setMap(null);

        //Set location while click on map
        setLocation(e.latLng);

        geocoder.geocode( { 'latLng': e.latLng}, function(results, status) {
            if (status == google.maps.GeocoderStatus.OK) {

                if(marker)
                    marker.setMap(null);

                map.setCenter(results[0].geometry.location);

                $.each(results, function(i, item) {

                    var arrAddress= item.address_components;

                    $.each(arrAddress, function (j, address_component) {

                        if (address_component.types[j] == "political"){
                            console.log("town:"+address_component.long_name);
                            if(!itemLocality)
                            itemLocality = address_component.long_name;
                        }

                        if (address_component.types[j] == "country"){
                            console.log("country:"+address_component.long_name);
                            itemCountry = address_component.short_name;
                        }

                        if(itemCountry && itemLocality)
                        return false;
                    });

                });


                //Set location based on map selected place
                setLocation(results[0].geometry.location);

                marker = new google.maps.Marker({
                    position: results[0].geometry.location,
                    map: map,
                    icon: '/bundles/tdomview/images/Icon_gamepiece_purple.png'
                });
            } else {
                alert("Invalid country or city for following reason : " + status);
            }
        });
    });
}

/**
 * Map will be relocated based on address param
 * @param string address
 * */
function codeAddress(address) {
    geocoder.geocode( { 'address': address}, function(results, status) {
        if (status == google.maps.GeocoderStatus.OK) {
            if(marker)
                marker.setMap(null);
            map.setCenter(results[0].geometry.location);

            itemLocality = "";

            //Set location based on map selected place
            setLocation(results[0].geometry.location);

            marker = new google.maps.Marker({
                position: results[0].geometry.location,
                map: map,
                icon: '/bundles/tdomview/images/Icon_gamepiece_purple.png'
            });
        } else {
            alert("Invalid country or city for following reason : " + status);
        }
    });
}

/**
 * Set Location based on map marker
 *
 * @param Object latLng
 */
function setLocation(latLng) {
    console.log(latLng);
    var location = "lat = "+ parseFloat(latLng.k).toFixed(3)+ " , lng = "+parseFloat(latLng.B).toFixed(3);
    $(document).find(".map_loc").html(location);
    $(document).find(".form_location").val(parseFloat(latLng.k)+","+parseFloat(latLng.B));

    if(itemCountry)
        $(document).find(".form_country").val(itemCountry);

    if(itemLocality) {
        $(document).find(".form_city").val(itemLocality);
        itemLocality = "";
    }
}

$(document).ready(function() {

    var location = $(document).find(".form_location").val();
    if (location) {
        isProfile = true;
        var locArr = location.split(',');
        default_lat = locArr[0];
        default_lng = locArr[1];
        var latLng = {'k' : default_lat, "A": default_lng };
        setLocation(latLng);
    }

    //Map initialize
    initialize();

    //Set home into the map with marker and selected location based on register country and city entered
    $("#sethome").on("click", function(e){
        e.preventDefault();
        var country = $(".form_country").val();
        var city = $(".form_city").val();


        if(country && city)
            codeAddress(country+ "," +city)
        else
            console.log(" Was not found country and city!");

    });

    //Enable commentout code if need map update based on country and city entered
   /* $(".form_country, .form_city").on("click", function(e){
        e.preventDefault();
        var country = $(".form_country").val();
        var city = $(".form_city").val();

        if(country && city)
            codeAddress(country+ "," +city)
        else
            console.log(" Was not found country and city!");
    });*/
});

/**
 * Thumbnail plugin
 *
 * Show thumbnail image on the fly while uploaded is done from client end
 * */
jQuery(function($) {
    var fileDiv = document.getElementById("upload");

    var fileInput = document.getElementById("fos_user_registration_form_file");

    if(!fileInput)
        fileInput = document.getElementById("fos_user_profile_form_file");

    fileInput.addEventListener("change",function(e){
        var files = this.files
        showThumbnail(files)
    },false)

    fileDiv.addEventListener("click",function(e){
        $(fileInput).show().focus().click().hide();
        e.preventDefault();
    },false)

    fileDiv.addEventListener("dragenter",function(e){
        e.stopPropagation();
        e.preventDefault();
    },false);

    fileDiv.addEventListener("dragover",function(e){
        e.stopPropagation();
        e.preventDefault();
    },false);

    fileDiv.addEventListener("drop",function(e){
        e.stopPropagation();
        e.preventDefault();
        var dt = e.dataTransfer;
        var files = dt.files;

        showThumbnail(files)
    },false);

    function showThumbnail(files){
        for(var i=0;i<files.length;i++){
            var file = files[i]
            var imageType = /image.*/
            if(!file.type.match(imageType)){
                console.log("Not an Image");
                continue;
            }

            var image = document.createElement("img");
            // image.classList.add("")
            var thumbnail = $("#thumbnail");
            image.file = file;

            var reader = new FileReader()
            reader.onload = (function(aImg){
                return function(e){
                    aImg.src = e.target.result;
                };
            }(image))
            var ret = reader.readAsDataURL(file);
            var canvas = document.createElement("canvas");
            ctx = canvas.getContext("2d");
            image.onload= function(){
                ctx.drawImage(image,100,100);
                thumbnail.html(image)
            }
        }
    }
});
