"use strict";

// http://stackoverflow.com/questions/1102215/mvp-pattern-with-javascript-framework

/**
 * Model for the Swapper's Delight program.
 */
var SwdModel = {
    /**
     * Wrapper function for FB.api
     * @param {type} api
     * @param {type} callback
     */
    facebookApi: function(api, callback) {
            FB.api('/' + api, callback);
    },
    queryBSTGroups: function(id, callback) {
        // This is just some dummy data. Replace this with an actual ajax call.
        var response = new Array('1447216838830981', '575530119133790');

        callback.call(SwdModel, response);
    }
};

/**
 * Presenter for the Swapper's Delight program.
 */
var SwdPresenter = {
    /**
     * Entry point of program.
     */
    init: function() {
        SwdView.init();

        $.getScript('//connect.facebook.net/en_US/all.js', function() {
            FB.init({
                //appId: '1401018793479333',      // Swapper's Delight PROD
                appId: '652991661414427', // Swapper's Delight TEST
            });

            $('#loginbutton,#feedbutton').removeAttr('disabled');

            // Try to get a login session going if there isn't one already.
            FB.getLoginStatus(function(response) {
                if (response.status === 'connected') {
                    SwdPresenter.startApp();
                } else {
                    FB.login(function(response) {
                        if (response.status === 'connected') {
                            SwdPresenter.startApp();
                        }
                    }, {scope: 'user_groups,user_likes'});
                }
            });
        });
    },
    
    startApp: function() {
        // Retrieve group info for logged in user.
        SwdModel.facebookApi('me', function(response) {
            SwdModel.queryBSTGroups(response.id, function(response) {
                if (response.length > 0) {
                    // Have the view write create groups vertical tab.
                    alert(response.name);
                }
                else {
                    // Have the view prompt the user to edit BST groups.
                }
            });
        });
        
        // Install Handlers
    }

    // Event Handlers (onX(e, args))
};

/**
 * View for the Swapper's Delight program.
 */
var SwdView = {
    handlers: {},
    /**
     * Init function for SwdView.
     */
    init: function() {
        // Init header row buttons.
        $('div.header-button').button();
        $('div.tabs-main').tabs(
                {heightStyle: "fill"}
        );
        $('div.button-menu-main').button();
        $('ul.menu-main').menu();
        $('div.button-menu-groups').button();
    },
    /**
     * Installs an event handler and connects it to the presenter.
     * @param {type} name
     * @param {type} handler
     * @param {type} selector
     * @param {type} event
     */
    installHandler: function(name, handler, selector, event) {
        this.handlers[name] = handler;

        $(selector).bind(event, function(e, args) {
            SwdView.handlers[name].call(SwdPresenter, e, args);
        });
    }
};

$(document).ready(function() {
    $.ajaxSetup({cache: true});
    SwdPresenter.init();
});
