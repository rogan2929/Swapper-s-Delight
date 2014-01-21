"use strict";

// "Enums"

// PostType Enum
var PostType = {group: 0, myposts: 1, pinned: 2, search: 3};

//var AppId = '1401018793479333'; // Prod
var AppId = '652991661414427'; // Test

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
     * Craft a FQL query to be consumed by FB.
     * @param {type} query
     * @param {type} callback
     */
    facebookFQLQuery: function(query, callback) {
        query = 'fql?q=' + encodeURIComponent(query);
        SwdModel.facebookApi(query, callback);
    },
    /***
     * Query FB group info.
     * @param {type} id
     * @param {type} callback
     */
    getGroupInfo: function(id, callback) {
        SwdModel.facebookFQLQuery('SELECT gid,name,icon FROM group WHERE gid=' + id, callback);
    },
    /***
     * AJAX call to FB group feed.
     * @param {type} gid Group id whose posts are to be retrieved.
     * @param {type} options
     * @param {type} callback Completed callback function.
     */
    getGroupPosts: function(gid, options, callback) {
        var query;

        // Base query
        query = 'SELECT post_id,message,attachment,comment_info FROM stream WHERE source_id=' + gid;

        // Constrain by current user.
        if (options.id) {
            query += ' AND actor_id=' + options.id;
        }

        // Constrain by whether or not the user likes the post.
        if (options.getLiked) {
            query += ' AND like_info.user_likes=1';
        }

        // Give 25 results, if possible.
        query += ' LIMIT 25';

        SwdModel.facebookFQLQuery(query, callback);
    },
    /***
     * Query database for groups that the user has marked as 'BST' (Buy, Sell, Trade)
     * @param {type} id
     * @param {type} callback
     */
    getMarkedGroups: function(id, callback) {
        // This is just some dummy data. Replace this with an actual ajax call.
        var response = new Array('120696471425768', '1447216838830981', '575530119133790');

        callback.call(SwdModel, response);
    },
    /***
     * AJAX call to FB comment feed for given post.
     * @param {type} id
     * @param {type} callback
     */
    getPostComments: function(id, callback) {
        //SwdModel.facebookApi(message + '?fields=comments', callback);
    },
    /***
     * Get details for the given post.
     * @param {type} id
     * @param {type} callback
     */
    getPostDetails: function(id, callback) {
        SwdModel.facebookFQLQuery('SELECT post_id,message,actor_id,attachment,permalink,like_info,share_info,comment_info,tagged_ids FROM stream WHERE post_id="' + id + '"', callback);
    },
    /***
     * Get data for the given user.
     * @param {type} id
     * @param {type} callback
     */
    getUserData: function(id, callback) {
        
    }
};

/**
 * Presenter for the Swapper's Delight program.
 */
var SwdPresenter = {
    nextPage: null,
    prevPage: null,
    postType: PostType.group,
    selectedGroup: null,
    selectedPost: null,
    /**
     * Entry point of program.
     */
    init: function() {
        SwdView.initView();

        // Fetch the FB JS API
        $.getScript('//connect.facebook.net/en_US/all.js', function() {
            FB.init({
                appId: AppId
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
            var markedGroupIds;
            var groups = [];
            var completed;
            var group;

            SwdModel.getMarkedGroups(response.id, function(response) {
                groupCount = response.length;
                markedGroupIds = response;
                completed = 0;

                if (response.length > 0) {
                    // Have the view write create groups vertical tab.
                    for (i = 0; i < response.length; i++) {
                        SwdModel.getGroupInfo(response[i], function(response) {
                            group = response.data[0];

                            // Add group to groups array.
                            groups[group.gid] = group;

                            SwdView.addGroupToMenu(group);

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
                                SwdPresenter.setSelectedGroup(groups[markedGroupIds[0]]);

                                // Install Event Handlers
                                SwdView.installHandler('onClickButtonClosePanel', SwdPresenter.onClickButtonClosePanel, '#button-close-panel', 'click');
                                SwdView.installHandler('onClickButtonNew', SwdPresenter.onClickButtonNew, '#button-new', 'click');
                                SwdView.installHandler('onClickHtml', SwdPresenter.onClickHtml, 'html', 'click');
                                SwdView.installHandler('onClickMenuButton', SwdPresenter.onClickMenuButton, '.menu-button', 'click');
                                SwdView.installHandler('onClickMenuItemGroup', SwdPresenter.onClickMenuItemGroup, '.menu-item-group', 'click');
                                SwdView.installHandler('onClickMenuItemMain', SwdPresenter.onClickMenuItemMain, '.menu-item-main', 'click');
                                SwdView.installHandler('onClickPanelButton', SwdPresenter.onClickPanelButton, '.panel-button', 'click');
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
    loadGroupPosts: function() {
        var options = {};

        // TODO: configure options based on what tab the user is on.
        switch (SwdPresenter.postType) {
            case PostType.group:
                options;
                break;
            case PostType.myposts:
                break;
            case PostType.pinned:
                break;
            case PostType.search:
                break;
        }

        // Get posts and then display them.
        SwdModel.getGroupPosts(SwdPresenter.selectedGroup.gid, options, function(response) {
            SwdView.displayGroupPosts(response.data, SwdPresenter.postType);
        });
    },
    /***
     * Load next page in group feed.
     */
    loadNextPage: function() {
        // TODO
        // Switch based on selectedPostType
    },
    /***
     * Brings up a send message window.
     */
    sendFacebookMessage: function(id, link) {
        FB.ui({
            to: id,
            method: 'send',
            link: link,
        });
    },
    /***
     * Set how old the oldest displayed post is to be.
     * @param {type} newerThan
     */
    setNewerThan: function(newerThan) {
        SwdPresenter.newerThan = newerThan;
        SwdPresenter.loadGroupPosts();
    },
    /***
     * Set currently selected group.
     * @param {type} group
     */
    setSelectedGroup: function(group) {
        SwdPresenter.selectedGroup = group;
        SwdPresenter.loadGroupPosts();
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
    onClickPanelButton: function(e, args) {
        SwdPresenter.sendFacebookMessage('', 'http://www.foxnews.com');
    },
    onClickPostTile: function(e, args) {
        var id;

        // Assuming one of the child elements of post-tile was clicked.
        id = $(e.currentTarget).parents('li.post-tile').attr('id');

        if (!id) {
            id = $(e.currentTarget).attr('id');
        }

        e.stopPropagation();

        SwdModel.getPostDetails(id, function(response) {
            SwdView.showRightPanel(response.data[0]);
        });
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
        $('#popup-menu-groups').append('<li id="' + group.gid + '" class="menu-item-group"><a href="#"><span class="ui-icon" style="background-image: url(' + group.icon + ')"></span><div style="display: inline-block; margin-left: 5px">' + group.name + '</div></a></li>');
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

        $('#button-menu-daysback').button({
            icons: {
                primary: 'ui-icon-calendar'
            }
        });

        $('#panel-button-comment').button({
            icons: {
                primary: 'ui-icon-comment'
            }
        });

        $('#panel-button-pm').button({
            icons: {
                primary: 'ui-icon-mail-closed'
            }
        });

        $('#panel-button-like').button({
            icons: {
                primary: 'ui-icon-pin-s'
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
     * @param {type} posts
     * @param {type} postType
     */
    displayGroupPosts: function(posts, postType) {
        var i;
        var url;
        var message;
        var feedContainer;
        var noImage;
        var noMessage;
        var post;

        switch (postType) {
            case PostType.myposts:
                feedContainer = '#feed-myposts';
                break;
            case PostType.pinned:
                feedContainer = '#feed-pinned';
                break;
            case PostType.search:
                feedContainer = '#feed-search';
                break;
            default:
                feedContainer = '#feed-group';
                break;
        }

        // Clear anything that is currently being displayed.
        $(feedContainer).empty();

        // Hide the right panel.
        SwdView.hideRightPanel();

        // If there is a feed to display, then display it.
        if (posts) {
            for (i = 0; i < posts.length; i++) {
                post = posts[i];
                noImage = false;
                noMessage = false;

                if (post.attachment && post.attachment.media && post.attachment.media[0] && post.attachment.media[0].src) {
                    url = post.attachment.media[0].src;
                }
                else {
                    url = '/img/no-image.jpg';
                    noImage = true;
                }

                if (post.message) {
                    message = post.message;
                }
                else {
                    message = '[No message attached to post.]';
                    noMessage = true;
                }

                if (!(noImage && noMessage)) {
                    $(feedContainer).append('<li id="' + post.post_id + '" class="post-tile ui-widget ui-widget-content ui-state-active"><div class="post-image"><img src="' + url + '"></div><div class="post-caption"><div><p>' + message + '</p></div></div></li>');
                }
            }

            // Associate the click event handler for newly created posts.
            $('.post-tile > *').click(SwdView.handlers['onClickPostTile']);
        }
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
        // Remove old image. Since we might be displaying a link or iframe instead.
        $('#panel-image .panel-post-image').remove();

        if (post.attachment && post.attachment.media && post.attachment.media[0] && post.attachment.media[0].src) {
            // Append new image;
            $('#panel-image div').hide();
            $('#panel-image').append('<img class="panel-post-image" src="' + post.attachment.media[0].src.replace('_s.jpg', '_n.jpg') + '">');
        }
        else {
            $('#panel-image div a').attr('href', post.permalink).text(post.permalink);
            $('#panel-image div').show();
        }

        //$('#panel-image img').attr('src', src);
        $('#panel-message').text(post.message);

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
