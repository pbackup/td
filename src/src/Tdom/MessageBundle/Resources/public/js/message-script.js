(function($){
    $(document).ready(function() {
        $("#send_message").click(function(e) {
            e.preventDefault();
            if ($("#tdom_message_body").val().length >1)
            sendMessage();
        });

        //Send message to all
        $("#send_message_all").click(function(e) {
            e.preventDefault();
            var messageText = $("#tdom_message_body").val();
            if (messageText.length <= 1)
            return;
            var activeUser = $(document).find("#user_connect_list a.active").attr('id');
            var message_form = $("#message_form");
            $.post($(this).attr('href'), message_form.serialize(), function(data) {
                if (data.success) {
                    $(document).find('#message_list').html(data.messages);
                    $(document).find("#user_connect_list").html(data.users);
                    scrollToDown(document.getElementById('message_list'));
                    $(document).find("#tdom_message_body").val("");
                    if (activeUser) {
                        $("#"+activeUser).addClass('active');
                        $("#"+activeUser).find('.badge').hide();
                    }
                }
            });
        });

        //Sending message by enter key press
        $("#tdom_message_body").keypress(function(e) {
            if(e.which == 13 && $("#tdom_message_body").val().length >1 ) {
                sendMessage();
            }
        });

        //Search contact
        $("#contact_search").asearch({url : settings.searchContactsUrl, menu   : '<ul class="asearch user-search-result dropdown-menu"></ul>' });
    });

    //Send Message function
    function sendMessage() {
        var activeUser = $(document).find("#user_connect_list a.active").attr('id');
        var message_form = $("#message_form");
        $.post(message_form.attr('action'), message_form.serialize(), function(data) {
            if (data.success) {
                $(document).find('#message_list').html(data.messages);
                $(document).find("#user_connect_list").html(data.users);
                scrollToDown(document.getElementById('message_list'));
                $(document).find("#tdom_message_body").val("");
                if (activeUser) {
                    $("#"+activeUser).addClass('active');
                    $("#"+activeUser).find('.badge').hide();
                }
            }
        });
    }

    //User Messge function
    var userMessages = function() {
        escapeAjaxPreloading = true;
        var activeUser = $(document).find("#user_connect_list a.active").attr('id');
        var url = $(document).find('#refresh_button').attr('href');
        $.post(url, function(data) {
            if (data.success) {
                $(document).find('#message_list').html(data.messages);
                $(document).find("#user_connect_list").html(data.users);
                scrollToDown(document.getElementById('message_list'));
                if (activeUser) {
                    $("#"+activeUser).addClass('active');
                    $("#"+activeUser).find('.badge').hide();
                }
                escapeAjaxPreloading = false;
            }
        });
    }

    //Custom refresh click event action
    $(document).on("click", "#refresh_button", function(e) {
        e.preventDefault();
        userMessages();
    });

    //Message would be loaded based on Interval input. e. 10 sec = 10000
    window.setInterval(userMessages, 30000);

})(jQuery);

function scrollToDown(eleObj) {
    eleObj.scrollTop = eleObj.scrollHeight;
}
