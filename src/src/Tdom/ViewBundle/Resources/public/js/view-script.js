var escapeAjaxPreloading = false;
(function($) {
    $(document).ready(function() {
        $("#add_new_game").click(function() {
            var form = $("#adding_game_form");
            var game = new Game(form);
            game.addGame();
        });

        $("#add_game_n_close").click(function() {
            var form = $("#adding_game_form");
            var game = new Game(form);
            game.addGameNClose();
        });

        //Gaming operation script like add and remove
        var Game = function(form) {
            var gameForm = form;
            var isReset = false;
            this.addGame = function() {
                console.log("Adding acton");
                this.save();
            };
            this.addGameNClose = function() {
                isReset = true;
                this.save();
            };
            this.save = function() {
                $.post(gameForm.attr('action'),gameForm.serialize(),function(data) {
                    $(document).find("#popup_form").html(data.form);
                    if(data.error) {
                        return;
                    } else {
                        gameForm.find("input[type=text]").val("");

                        var cat = "#cat_"+data.cat+"_id";
                        $(document).find(cat).append(data.item);
                        if (isReset) {
                            $("#add_game").modal("hide");
                        }
                    }
                })
            };
            this.render = function(data) {
            }
            this.remove = function(id) {
                console.log("Removed is called!!");
            }
        };

        //Game search auto complete
        $("#game_search").asearch({url:settings.gameSearchAjaxUrl});


        //Remove game action from list
        $(document).on('click','.remove-action', function() {
            var currentElement = $(this).parent('li');
            var action = $(this).data('action');
            $.post(action, function(data){
                if(data.success) {
                    currentElement.remove();
                }
            });
        });

        //Adding game action
        $(document).on("click", '.asearch .list-action', function(e){
            e.stopPropagation();
            var url = $(this).data('action');
            var element = $(this).parent('li');
            $.post(url, function(data) {
                if(data.success) {
                    var item = data.item;
                    var cat = "#cat_"+data.cat+"_id";
                    $(document).find(cat).append(item);
                    element.remove();
                }
            });
        });


        //In general ajax preloading implement
        $(document).ajaxStart(function() {
            if(!escapeAjaxPreloading)
            $.blockUI({ message: settings.ajaxLoadingImg, css: {background:'none', border:0} });
        }).ajaxStop(function() {
            $.unblockUI();
        });

        //New game action
        $(document).on("click", "#new_games_action", function(e){
            e.preventDefault();
            $("#tdom_user_game_name").val($("#game_search").val());
            $("#add_game").modal('show');
            $("#game_search").val("");
            $(document).find('.mygame-seach-bar ul.asearch').hide().html("");
        });

        var hContent = $("body").height(); // get the height of your content
        var hWindow = $(window).height();  // get the height of the visitor's browser window

        // if the height of your content is smaller than the height of the
        // browser window, sticky footer would be applied
        if(hContent < hWindow) {
            $(document).find("#tdom_footer").addClass('sticky-footer');
        }

    });
})(jQuery);


(function($) {
    $.fn.asearch = function(options) {
        var currentElement = $(this);
        var ajaxGameSearchReq = null;
        var isShown = false;
        var settings = $.extend({
            item : 10,
            top    : 39,
            left   : 0,
            type   : 'json',
            menu   : '<ul class="asearch dropdown-menu"></ul>',
            url    : "/"
        }, options);

        $(this).after(settings.menu);

        $(this).keyup(function() {
            currentElement.next('ul').html("<li> <span style='padding-left:10px; color:#F88800'> Loading... </span> </li>");
            currentElement.next('ul').show();
            escapeAjaxPreloading = true;
            if(currentElement.val().length > 0) {
                if (ajaxGameSearchReq != null) ajaxGameSearchReq.abort();
                ajaxGameSearchReq = $.ajax({
                    url: settings.url+"/"+currentElement.val(),
                    data: {'item' : settings.item, q : currentElement.val()},
                    method: 'post',
                    dataType: settings.type,
                    success: function (data) {
                        escapeAjaxPreloading = false;
                        if(data.data) {
                            currentElement.next('ul').html(data.data);
                            currentElement.next('ul').show();
                            isShown = true;
                        } else {
                            currentElement.next('ul').hide();
                            isShown = false;
                        }
                    }
                });
            }
            else {

                currentElement.next('ul').hide();
                currentElement.next('ul').html("");
            }
        });

        currentElement.on('mouseenter',function() {
            if(isShown) {
                currentElement.next('ul').show();
                isShown = true;
            }
        });

        currentElement.next('ul').on('mouseenter',function(){
            isShown = true;
        });

        currentElement.next('ul').on('mouseleave',function(){
            if (isShown) {
                $(this).hide();
            }
        });
    }
})(jQuery);


