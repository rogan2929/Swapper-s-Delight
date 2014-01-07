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
    
    queryBSTGroups: function(callback) {
        // This is just some dummy data. Replace this with an actual ajax call.
        var response = ('1447216838830981', '575530119133790');
        
        call(SwdModel, callback, response);
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
        SwdModel.queryBSTGroups(function(response) {
            alert(response[0]);
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
    $.getScript('//connect.facebook.net/en_US/all.js', function() {
        FB.init({
            //appId: '1401018793479333',      // Swapper's Delight PROD
            appId: '652991661414427',       // Swapper's Delight TEST
        });

        $('#loginbutton,#feedbutton').removeAttr('disabled');

        // Try to get a login session going if there isn't one already.
        FB.getLoginStatus(function(response) {
            if (response.status === 'connected') {
                SwdPresenter.init();
            } else {
                FB.login(function(response) {
                    if (response.status === 'connected') {
                        SwdPresenter.init();
                    }
                }, {scope: 'user_groups,user_likes'});
            }
        });
    });
});
