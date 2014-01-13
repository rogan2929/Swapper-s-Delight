"use strict";

// http://stackoverflow.com/questions/1102215/mvp-pattern-with-javascript-framework

/**
 * Model for the Swapper's Delight program.
 */
var SwdModel = {
    /**
     * Wrapper function for FB.api
     * @param {type} api Facebook API to call.
     * @param {type} callback Callback function.
     */
    facebookApi: function(api, callback) {
        FB.api('/' + api, callback);
    },
    /***
     * AJAX call to FB group feed.
     * @param {type} group Group whose feed is to be retrieved.
     * @param {type} days Days before today.
     * @param {type} page Page number in results. (Will be multiplied by 25 for exact post count).
     * @param {type} callback Completed callback function.
     */
    getGroupFeed: function(group, days, page, callback) {
        SwdModel.facebookApi(group + '?fields=feed', callback);
    },
    /***
     * AJAX call to FB comment feed for given post.
     * @param {type} message
     * @param {type} callback
     */
    getMessageComments: function(message, callback) {
        SwdModel.facebookApi(message + '?fields=comments', callback);
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
    selectedPost: null,
    userObject: null,
    /**
     * Entry point of program.
     */
    init: function() {
        SwdView.init();

        // Fetch the FB JS API
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
            var i;
            var groupCount;
            var completed;

            // Save the FB user object for later consumption.
            SwdPresenter.userObject = response;

            SwdModel.queryBSTGroups(SwdPresenter.userObject.id, function(response) {
                groupCount = response.length;
                completed = 0;

                if (response.length > 0) {
                    // Have the view write create groups vertical tab.
                    for (i = 0; i < response.length; i++) {
                        SwdModel.facebookApi(response[i], function(response) {
                            //$('<li style="display: block;"><a href="#"><img style="display: inline-block;" src="' + response.icon + '" /><div style="display: inline-block; margin-left: 5px">' + response.name + '</div></a></li>').appendTo('#popup-menu-groups');
                            //alert('<li><a href="#">' + response.name + '</a></li>');
                            $('#popup-menu-groups').append('<li id="menu-item-' + response.id + '"><a href="#"><span class="ui-icon" style="background-image: url(' + response.icon + ')"></span><div style="display: inline-block; margin-left: 5px">' + response.name + '</div></a></li>');

                            // Keep track of how many groups have been downloaded.
                            completed++;

                            // On last api call, convert create the menu.
                            if (completed === groupCount) {
                                $('#popup-menu-groups').menu().position({
                                    of: $('#button-menu-groups'),
                                    my: 'left top',
                                    at: 'left bottom'
                                });

                                // TODO: Select first group.
                            }
                        });
                    }
                }
                else {
                    // Have the view prompt the user to edit BST groups.
                }
            });
        });

        // Install Handlers
        SwdView.installHandler('onClickButtonNew', this.onClickButtonNew, '#button-new', 'click');
        SwdView.installHandler('onClickUiMenuItem', this.onClickUiMenuItem, 'li.ui-menu-item', 'click');
    },
    // Event Handlers (onX(e, args))
    onClickButtonNew: function(e, args) {
        SwdView.showNewPostDialog(e, args);
    },
    onClickUiMenuItem: function(e, args) {
        // TODO: Switch logic based on calling element.
    }
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
        $('#tabs-main').tabs({
            heightStyle: "fill"
        });

        // Set up tab pages
        $('#feed-posts').selectable();
        $('#buying-posts').selectable();
        $('#selling-posts').selectable();
        $('#pinned-posts').selectable();

        // Set up buttons
        $('#button-menu-main').button({
            icons: {
                primary: 'ui-icon-gear'
            }
        });

        $('#button-menu-groups').button({
            icons: {
                primary: 'ui-icon-contact'
            }
        });

        $('#button-new').button({
            icons: {
                primary: 'ui-icon-comment'
            }
        });

        $('#button-delete').button({
            icons: {
                primary: 'ui-icon-trash'
            }
        });

        $('#button-bump').button({
            icons: {
                primary: 'ui-icon-circle-plus'
            }
        });

        $('#button-menu-date').button({
            icons: {
                primary: 'ui-icon-calendar'
            }
        });

        $('#popup-menu-main').menu().position({
            of: $('#button-menu-main'),
            my: 'left top',
            at: 'left bottom'
        });

        $('#popup-menu-date').menu().position({
            of: $('#button-menu-date'),
            my: 'left top',
            at: 'left bottom'
        });
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
    },
    showNewPostDialog: function(e, args) {
        $('#dialog-new-post').dialog();
    }
};

$(document).ready(function() {
    $.ajaxSetup({cache: true});
    SwdPresenter.init();
});
