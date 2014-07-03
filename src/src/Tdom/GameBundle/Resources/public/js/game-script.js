var map;
var marker;
var markersArray 	= [];
var distance		= 5;
var zoomLvl	  		= 7;
var infowindow 		= new google.maps.InfoWindow();
var contentString 	= new Array();
var urlString = new Array();
var LatLngList = [];
var selectedUser = {};
var users = [];

function initialize() {
    var mapOptions = {
        center: new google.maps.LatLng( 61.976 , 26.289),
        zoom: zoomLvl,
        mapTypeId: google.maps.MapTypeId.TERRAIN
    };

    map = new google.maps.Map(document.getElementById('map_canvas'),
        mapOptions);

    //Update google map
    updateMap($("#tdom_filter_game_category"));

}

jQuery(document).ready(function(){
    initialize();
    loadMultiSelect();
    var windowHeight = $(window).height();
    jQuery(document).find('#map_canvas').css('height', windowHeight-200+"px");

    //Change category action
    jQuery(document).on("change", "#tdom_filter_game_category", function() {
       // $(document).find('.multiselect').text('Loading...').attr('title', 'Loading..');
        updateMap($(this));
    });

   jQuery(".form_reset").click(function(e){
       e.preventDefault();
       location.reload(true);
   });

    //My games quick link
   jQuery(document).on('click',"#quick_my_games_link", function(e){
       e.preventDefault();
       window.location = settings.myGamesLink;
   });

});

jQuery(document).on("click", "#showonmap", function(e){
    e.preventDefault();
    jQuery("#user_modal").modal('hide');
    map.setCenter(new google.maps.LatLng(selectedUser.lat, selectedUser.lng));
    zoomTo();      // This will trigger a zoom_changed on the map
});

/**
 * Add or remove  to contacts event
 */
jQuery(document).on("click", "#addcontacts", function(e) {
    e.preventDefault();
    var checkLogin = $(this).data('login');
    if (checkLogin == "")  {
        jQuery("#confirm-modal").modal('show');
        return;
    }
    var button = $(this);
    jQuery.post($(this).attr("href")+"/1", function(data) {
        if(data.button) {
            button.attr("href",data.url);
            button.text(data.button);
        }
    });
});

/**
 * Sending message
 */
jQuery(document).on('click', "#send_message", function(e){
    var checkLogin = $(this).data('login');
    if (checkLogin == "") {
        e.preventDefault();
        jQuery("#confirm-modal").modal('show');
        return;
    }
});

//Map zooming
var zoomFluid = 0;
function zoomTo(){
    //console.log(zoomFluid);
    if(zoomFluid==13) { zoomFluid = 0;
        return 0;
    }
    else {
        zoomFluid ++;
        map.setZoom(zoomFluid);
        setTimeout(zoomTo, 10);
    }
}

//Load multi select box
var loadMultiSelect = function() {
    //$("#tdom_filter_game_games").select2();
    $('.selectpicker').selectpicker();

    $("#tdom_filter_game_games").multiselect({
        onChange: function(element, checked) {
           var selectedItems = $('#tdom_filter_game_games :selected');
           var left = $('.game-option-group .list-left');
           var right = $('.game-option-group .list-right');
           right.find('li').remove();

           if (selectedItems.length > 0) {
               left.css({'width':50+"%", 'border-right':'1px solid #ccc'});

               $.each(selectedItems, function(){
                   var li = $('<li><a href="javascript:void(0)" data-game="" class="selected-game"><span class="selected-game"></span><span class="fa fa-times-circle remove-checked"> </span></a></a></li>');
                   var text = $(this).text();
                   var val = $(this).val();
                   if (text.length > 22)
                       li.find('span.selected-game').text(text.substring(0, 22)+"..." );
                   else
                       li.find('span.selected-game').text(text);

                   li.find('a.selected-game').attr( "data-game", val );
                   li.find('a.selected-game').attr( "title", text );
                   right.append(li);
               });

               $('.multiselect-container').animate({
                    width: 180+"%"
                });
           }
           else {
               left.css({'width':94+"%", border:"0"});
               $('.multiselect-container').animate({
                   width: 94+"%"
               });
           }
           var form = $(document).find('#filter_form_map');
           $.post(form.attr('action'), form.serialize(), function(data) {
                if(data.success) {
                    settings.mapData = data.result;
                    executeForFind();
                }
            });
        },
        enableFiltering : true
    });
    //Type filter option disabled
    $(document).find('#tdom_filter_game_type').next().find('a').addClass('menu-disabled');
};

jQuery(document).on('click', 'a.selected-game', function(e) {
    e.stopPropagation();
    var curVal = $(this).data('game');
    var checkboxes = jQuery(".list-left li input:checked");
    jQuery.each(checkboxes, function() {
        if (curVal == $(this).val()) {
            $(this).removeAttr('checked');
            $(this).change();
        }
    });
});

//Update google map
var updateMap = function(Obj) {
    var url = settings.changeCategoryAjaxUrl;
    $.post((Obj.val())? url+"/"+Obj.val(): url, function(data){
        if (data.success) {
            $(document).find('#seaching_form').html(data.form);
            loadMultiSelect();
            settings.mapData = data.users;
            executeForFind();
        }
    });
};

//Execute google map
function executeForFind() {

    if(markersArray) {
        for (var i = 0; i < markersArray.length; i++) {
            marker = markersArray[i];
            marker.setMap(null);
        }

        LatLngList = [];
    }

    if(!settings.mapData) return;

    //Scripting for map
    $.each(settings.mapData, function(key, val) {

        var markPos = new google.maps.LatLng(val.lat,val.lng);

        // Set marker image
        var image = '/bundles/tdomview/images/Icon_gamepiece_purple.png';

        var marker = new google.maps.Marker({
            position: markPos,
            map: map,
            title: val.nickname,
            clickable: true,
            animation: google.maps.Animation.DROP,
            icon: image
        });

        markersArray.push(marker);
        LatLngList.push(markPos);

        // Setup content for info bubble
        contentString[marker.__gm_id] = '<div class="info_window" style="min-width: 180px; overflow: hidden; padding: 10px">';
        contentString[marker.__gm_id] = '<img src="'+val.avatar+'" class="profile_picture" style="float:left; margin-right:5px" />';
        contentString[marker.__gm_id] += '<ul class="marker_user_info" style="float:left"> <li><a class="map_profile" class="" href="#" >'+val.nickname+'</a></li>'
        contentString[marker.__gm_id] += '<li>'+val.birthday+'</li>'
        contentString[marker.__gm_id] += '</ul></div>';

        urlString[marker.__gm_id] = val.path;
        users[marker.__gm_id] = val;

        //On mouse over event
        google.maps.event.addListener(marker, 'mouseover', function() {
            infowindow.close();
            infowindow.setContent(contentString[marker.__gm_id]);
            infowindow.open(map, marker);
        });

        //On mouseout event
        google.maps.event.addListener(marker, 'mouseout', function() {
            infowindow.close();
        });

        //Click event
        google.maps.event.addListener(marker, 'click', function() {
            openPopup(urlString[marker.__gm_id], users[marker.__gm_id]);
        });

        //  Create a new viewpoint bound
        var bounds = new google.maps.LatLngBounds ();
        //  Go through each...
        for (var i = 0, LtLgLen = LatLngList.length; i < LtLgLen; i++) {
            //  And increase the bounds to take this point
            bounds.extend (LatLngList[i]);
        }
        //  Fit these bounds to the map
        map.fitBounds (bounds);
    });

    google.maps.event.addListener(map, 'idle', function() {
        $.unblockUI();
    });
}

//Dialog popup with user information while click on google map icon
function openPopup(url, user) {
    selectedUser = user;
    jQuery.post(url, {'json':true}, function(data){

        if(data.success) {
            $(document).find("h4.modal-title").text(data.nickname);
            //jQuery("#user_modal").find('.modal-lg').width($("#map_canvas").width()-25);
            jQuery(document).find('.modal-body').html(data.content);
            jQuery("#user_modal").modal('show');
        }
    });
}
