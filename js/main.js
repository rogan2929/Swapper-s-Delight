"use strict";

// "Enums"

// PostType Enum
var PostType = {group: 0, buying: 1, selling: 2, pinned: 3};

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
     * Get group object from FB API.
     * @param {type} id
     * @param {type} callback
     */
    getGroupInfo: function(id, callback) {
        SwdModel.facebookApi(id, callback);
    },
    /***
     * AJAX call to retrieve marked posts for the given group.
     * @param {type} postType
     * @param {type} callback
     */
    getMarkedPosts: function(postType, callback) {

    },
    /***
     * AJAX call to FB group feed.
     * @param {type} group Group whose feed is to be retrieved.
     * @param {type} callback Completed callback function.
     */
    getGroupFeed: function(group, callback) {
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
    currentPage: 0,
    daysBack: 1,
    postType: PostType.group,
    selectedGroup: null,
    selectedPost: null,
    userObject: null,
    /**
     * Entry point of program.
     */
    init: function() {
        SwdView.initView();

        // Fetch the FB JS API
        $.getScript('//connect.facebook.net/en_US/all.js', function() {
            FB.init({
                //appId: '1401018793479333',      // Swapper's Delight PROD
                appId: '652991661414427' // Swapper's Delight TEST
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
            var bstGroupIds;
            var groups = [];
            var completed;

            // Save the FB user object for later consumption.
            SwdPresenter.userObject = response;

            SwdModel.queryBSTGroups(SwdPresenter.userObject.id, function(response) {
                groupCount = response.length;
                bstGroupIds = response;
                completed = 0;

                if (response.length > 0) {
                    // Have the view write create groups vertical tab.
                    for (i = 0; i < response.length; i++) {
                        SwdModel.facebookApi(response[i], function(response) {
                            // Add group to groups array.
                            groups[response.id] = response;

                            SwdView.addGroupToMenu(response);

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
                                SwdPresenter.setSelectedGroup(groups[bstGroupIds[0]]);

                                // Install Event Handlers
                                SwdView.installHandler('onClickButtonClosePanel', SwdPresenter.onClickButtonClosePanel, '#button-close-panel', 'click');
                                SwdView.installHandler('onClickButtonNew', SwdPresenter.onClickButtonNew, '#button-new', 'click');
                                SwdView.installHandler('onClickHtml', SwdPresenter.onClickHtml, 'html', 'click');
                                SwdView.installHandler('onClickMenuButton', SwdPresenter.onClickMenuButton, '.menu-button', 'click');
                                SwdView.installHandler('onclickMenuItemDate', SwdPresenter.onClickMenuItemDate, '.menu-item-date', 'click');
                                SwdView.installHandler('onclickMenuItemGroup', SwdPresenter.onClickMenuItemGroup, '.menu-item-group', 'click');
                                SwdView.installHandler('onclickMenuItemMain', SwdPresenter.onClickMenuItemMain, '.menu-item-main', 'click');
                                SwdView.installHandler('onClickPostTile', SwdPresenter.onClickPostTile, '.post-tile > *', 'click');
                                SwdView.installHandler('onWindowResize', SwdPresenter.onWindowResize, window, 'resize');

                                // Position our menus.
                                SwdView.positionMenus();

                                // TODO: Set auto refresh with setInterval
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
     * Load feed for the current group.
     */
    loadGroupFeed: function() {
        SwdModel.getGroupFeed(this.selectedGroup.id, function(response) {
            var i;
            var post;
            var creationTime;
            var currentTime = new Date();
            var ONE_DAY = 1000 * 60 * 60 * 24;
            var maxAge = SwdPresenter.daysBack * ONE_DAY;
            var feed = [];

            // If looking for marked posts, then make an additional API call to determine what these are.
            if (SwdPresenter.postType !== PostType.group) {
                SwdModel.getMarkedPosts(SwdPresenter.postType, function(response) {
                    switch (SwdPresenter.postType) {
                        case PostType.buying:
                            break;
                        case PostType.selling:
                            break;
                        case PostType.pinned:
                            break;
                    }
                });
            }

            if (response.feed && response.feed.data) {
                alert(response.paging.next);
                
                // Remove posts that are not in the selected date range.
                for (i = 0; i < response.feed.data.length; i++) {
                    post = response.feed.data[i];
                    
                    creationTime = Date.parse(post.created_time);
                    
                    if (currentTime - creationTime <= maxAge) {
                        feed.push(post);
                    }
                }
            }

            SwdView.displayGroupFeed(feed, SwdPresenter.postType);
        });
    },
    /***
     * Set how old the oldest displayed post is to be.
     * @param {type} daysBack
     */
    setDaysBack: function(daysBack) {
        SwdPresenter.daysBack = daysBack;
        SwdPresenter.loadGroupFeed();
    },
    /***
     * Set currently selected group.
     * @param {type} group
     */
    setSelectedGroup: function(group) {
        SwdPresenter.selectedGroup = group;
        SwdPresenter.loadGroupFeed();
        SwdView.setGroupButtonText(group.name);
    },
    // Event Handlers (onX(e, args))
    onClickButtonClosePanel: function(e, args) {
        SwdView.hideRightPanel();
    },
    onClickButtonNew: function(e, args) {
        SwdView.showNewPostDialog();
    },
    onClickHtml: function(e, args) {
        SwdView.closeAllUiMenus();
        SwdView.hideRightPanel();
    },
    onClickMenuButton: function(e, args) {
        SwdView.showUiMenu(e);
    },
    onClickMenuItemDate: function(e, args) {
        var daysBack;
        var id = $(e.currentTarget).attr('id');

        switch (id) {
            case 'menu-item-3days':
                daysBack = 3;
                break;
            case 'menu-item-week':
                daysBack = 7;
                break;
            case 'menu-item-30days':
                daysBack = 30;
                break;
            case 'menu-item-all':
                daysBack = 365;
                break;
            default:
                daysBack = 1;
                break;
        }

        SwdPresenter.setDaysBack(daysBack);
    },
    onClickMenuItemGroup: function(e, args) {
        var id = $(e.currentTarget).attr('id');

        if (id === 'menu-item-choose-groups') {
            // TODO: Display group chooser dialog.
        }
        else {
            // Retreive group information for selected ID
            SwdModel.getGroupInfo(id, function(response) {
                // Set selected group and load its feed.
                SwdPresenter.setSelectedGroup(response);
            });
        }
    },
    onClickMenuItemMain: function(e, args) {
        var id = $(e.currentTarget).attr('id');
    },
    onClickPostTile: function(e, args) {
        var post = $(e.currentTarget).attr('id');
        e.stopPropagation();
        SwdView.showRightPanel(post);
    },
    onWindowResize: function(e, args) {
        SwdView.positionMenus();
    }
};

/**
 * View for the Swapper's Delight program.
 */
var SwdView = {
    handlers: {},
    /***
     * Add group to Group Select Menu.
     * @param {type} group
     */
    addGroupToMenu: function(group) {
        $('#popup-menu-groups').append('<li id="' + group.id + '" class="menu-item-group"><a href="#"><span class="ui-icon" style="background-image: url(' + group.icon + ')"></span><div style="display: inline-block; margin-left: 5px">' + group.name + '</div></a></li>');
    },
    /**
     * Init function for SwdView.
     */
    initView: function() {
        // Init header row buttons.
        $('#tabs-main').tabs({
            heightStyle: 'fill'
        });

        $('.tabs-bottom .ui-tabs-nav, .tabs-bottom .ui-tabs-nav > *').removeClass('ui-corner-all ui-corner-top').addClass('ui-corner-bottom');

        // move the nav to the bottom
        $('.tabs-bottom .ui-tabs-nav').appendTo('.tabs-bottom');

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

//        $('#button-pin').button({
//            icons: {
//                primary: 'ui-icon-pin-s'
//            }
//        }).toggle();

        $('#button-menu-date').button({
            icons: {
                primary: 'ui-icon-calendar'
            }
        });

        $('#button-close-panel').hover(function() {
            $(this).addClass('ui-state-hover');
        }, function() {
            $(this).removeClass('ui-state-hover');
        });

        $('#right-panel').click(function(e) {
            e.stopPropagation();
        });

        // Init menus.
        $('#popup-menu-main').menu();
        $('#popup-menu-date').menu();
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
     */
    closeAllUiMenus: function() {
        $('.ui-menu').hide();
    },
    /***
     * Displays the returned feed in the main window.
     * @param {type} feed
     * @param {type} postType
     */
    displayGroupFeed: function(feed, postType) {
        var i;
        var url;
        var message;
        var feedContainer;

        switch (postType) {
            case PostType.buying:
                feedContainer = '#feed-buying';
                break;
            case PostType.selling:
                feedContainer = '#feed-selling';
                break;
            case PostType.pinned:
                feedContainer = '#feed-pinned';
                break;
            default:
                feedContainer = '#feed-group';
                break;
        }

        // Clear anything that is currently being displayed.
        $(feedContainer).empty();
        
        // Hide the right panel.
        SwdView.hideRightPanel();

        for (i = 0; i < feed.length; i++) {
            if (feed[i].picture) {
                url = feed[i].picture;
            }
            else {
                url = '/img/no-image.jpg';
            }

            if (feed[i].message) {
                message = feed[i].message;
            }
            else {
                message = '[No caption for image.]'
            }

            $(feedContainer).append('<li id="' + feed[i].id + '" class="post-tile"><div class="post-image"><img src="' + url + '"></div><div class="post-caption">' + message + '</div></li>');
        }

//        $('#feed-group').selectable({
//            filter: " > li"
//        });

        // Associate the click event handler for newly created posts.
        $('.post-tile > *').click(SwdView.handlers['onClickPostTile']);
    },
    /***
     * Hides the right column.
     */
    hideRightPanel: function() {
        $('#right-panel').hide('slide', {
            direction: 'right',
            duration: 300,
            easing: 'easeInOutQuint'
        });
    },
    /***
     * Sets menu positions.
     */
    positionMenus: function() {
        $('.menu-button').each(function() {
            var menu = $(this).find('a').attr('href');

            $(menu).css({top: 0, left: 0, position: 'absolute'});

            $(menu).position({
                of: $(this),
                my: 'left top',
                at: 'left bottom'
            });
        });
    },
    /***
     * Changes the text shown in the "Select a Group" button.
     * @param {type} text Text to display inside the button.
     */
    setGroupButtonText: function(text) {
        $('#button-menu-groups span a').text(text);
    },
    /***
     * Displays new post dialog box.
     */
    showNewPostDialog: function() {
        $('#dialog-new-post').dialog({
            modal: true
        });
    },
    /***
     * Shows the right column.
     * @param {type} post Post to load into right column.
     */
    showRightPanel: function(post) {
        $('#right-panel').show('slide', {
            direction: 'right',
            duration: 300,
            easing: 'easeInOutQuint'
        });
    },
    /***
     * Shows a Jquery UI menu.
     * @param {type} e
     */
    showUiMenu: function(e) {
        var menu;

        e.stopPropagation();
        menu = $(e.currentTarget).find('a').attr('href');

        // Display the menu.
        $(menu).show('slide', {
            direction: 'up',
            duration: 300,
            easing: 'easeInOutQuint'
        });
    }
};

$(document).ready(function() {
    $.ajaxSetup({cache: true});
    SwdPresenter.init();
});
