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
     * @param {type} days Days before today. (0 = today)
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
    /***
     * Query database for groups that the user has marked as 'BST' (Buy, Sell, Trade)
     * @param {type} id
     * @param {type} callback
     */
    queryBSTGroups: function(id, callback) {
        // This is just some dummy data. Replace this with an actual ajax call.
        var response = new Array('120696471425768', '1447216838830981', '575530119133790');

        callback.call(SwdModel, response);
    }
};

/**
 * Presenter for the Swapper's Delight program.
 */
var SwdPresenter = {
    currentPage: 1,
    days: 0,
    selectedGroup: null,
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
    /***
     * Starts the application after init has finished.
     */
    startApp: function() {
        // Retrieve group info for logged in user.
        SwdModel.facebookApi('me', function(response) {
            var i;
            var groupCount;
            var groups;
            var completed;

            // Save the FB user object for later consumption.
            SwdPresenter.userObject = response;

            SwdModel.queryBSTGroups(SwdPresenter.userObject.id, function(response) {
                groupCount = response.length;
                groups = response;
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

                                // Select first group and load posts.
                                SwdPresenter.selectedGroup = groups[0];
                                SwdPresenter.loadGroupFeed();

                                // Install Event Handlers
                                SwdView.installHandler('onClickButtonNew', SwdPresenter.onClickButtonNew, '#button-new', 'click');
                                SwdView.installHandler('onClickHtml', SwdPresenter.onClickHtml, 'html', 'click');
                                SwdView.installHandler('onClickMenuButton', SwdPresenter.onClickMenuButton, '.menu-button', 'click');
                                SwdView.installHandler('onClickUiMenuItem', SwdPresenter.onClickUiMenuItem, 'li.ui-menu-item', 'click');
                            }
                        });
                    }
                }
                else {
                    // Have the view prompt the user to edit BST groups.
                }
            });
        });
    },
    
    /***
     * 
     * @param {type} group Facebook group ID.
     * @param {type} postType Integer representing the type of post to retrieve. 0 = Group Feed, 1 = Buying, 2 = Selling, 3 = Pinned.
     */
    loadGroupFeed: function() {
        SwdModel.getGroupFeed(this.selectedGroup, this.days, this.currentPage, function(response) {
            alert(response.feed.data[0].message);
            
            SwdView.displayFeed(response.feed.data);
        });
    },
    
    // Event Handlers (onX(e, args))
    onClickButtonNew: function(e, args) {
        SwdView.showNewPostDialog();
    },
    onClickHtml: function(e, args) {
        SwdView.closeAllUiMenus();
    },
    onClickMenuButton: function(e, args) {
        SwdView.showUiMenu(e);
    },
    onClickUiMenuItem: function(e, args) {
        // Switch logic based on calling element.
        var id = $(e.currentTarget).attr('id');

        switch (id) {
            default:
                break;
        }
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

        $('#button-pin').button({
            icons: {
                primary: 'ui-icon-pin-s'
            }
        });

        $('#button-delete').button({
            icons: {
                primary: 'ui-icon-trash'
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
    /***
     * Closes all Jquery UI menus.
     * @param {type} e
     * @param {type} args
     */
    closeAllUiMenus: function() {
        $('.ui-menu').hide();
    },
    
    displayFeed: function(feed) {
        alert(feed[0].message);
    },
    /***
     * Displays new post dialog box.
     * @param {type} e
     * @param {type} args
     */
    showNewPostDialog: function() {
        $('#dialog-new-post').dialog();
    },
    /***
     * Shows a Jquery UI menu.
     * @param {type} e
     * @param {type} args
     * @param {type} menu
     */
    showUiMenu: function(e) {
        var menu;
        e.stopPropagation();
        menu = $(e.currentTarget).find('a').attr('href');
        $(menu).show();
    }
};

$(document).ready(function() {
    $.ajaxSetup({cache: true});
    SwdPresenter.init();
});
