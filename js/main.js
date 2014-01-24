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
        query = 'SELECT post_id,created_time,message,attachment,comment_info FROM stream WHERE source_id=' + gid;

        // Constrain by current user.
        if (options.id) {
            query += ' AND actor_id=' + options.id;
        }

        // Constrain by whether or not the user likes the post.
        if (options.getLiked) {
            query += ' AND like_info.user_likes=1';
        }

        // For FQL pagination (query by posts with created_time less than created_time of last query's oldest post.)
        if (options.createdTime) {
            query += ' AND created_time < ' + options.createdTime;
        }

        // Fetch 30 results, and sorted by creation time.
        query += ' ORDER BY created_time DESC LIMIT 30';

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
        SwdModel.facebookFQLQuery('SELECT fromid,text,text_tags,attachment FROM comment WHERE post_id="' + id + '"', callback);
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
        SwdModel.facebookFQLQuery('SELECT last_name,first_name,pic_square,profile_url FROM user WHERE uid=' + id, callback);
    }
};

/**
 * Presenter for the Swapper's Delight program.
 */
var SwdPresenter = {
    currentUid: null,
    oldestPost: null,
    postType: PostType.group,
    selectedGroup: null,
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

            SwdPresenter.currentUid = response.id;

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
                                SwdView.installHandler('onClickButtonNew', SwdPresenter.onClickButtonNew, '#button-new', 'click');
                                SwdView.installHandler('onClickHtml', SwdPresenter.onClickHtml, 'html', 'click');
                                SwdView.installHandler('onClickMenuButton', SwdPresenter.onClickMenuButton, '.menu-button', 'click');
                                SwdView.installHandler('onClickMenuItemGroup', SwdPresenter.onClickMenuItemGroup, '.menu-item-group', 'click');
                                SwdView.installHandler('onClickMenuItemMain', SwdPresenter.onClickMenuItemMain, '.menu-item-main', 'click');
                                SwdView.installHandler('onClickNavButton', SwdPresenter.onClickNavButton, '.button-nav', 'click');
                                SwdView.installHandler('onClickPanelButton', SwdPresenter.onClickPanelButton, '.panel-button', 'click');
                                SwdView.installHandler('onClickPanelMessageUser', SwdPresenter.onClickPanelMessageUser, '#panel-message-user', 'click');
                                SwdView.installHandler('onClickPostTile', SwdPresenter.onClickPostTile, '.post-tile > *', 'click');
                                SwdView.installHandler('onScrollGroupFeed', SwdPresenter.onScrollGroupFeed, '#group-feed', 'scroll');
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
                break;
            case PostType.myposts:
                options = {id: SwdPresenter.currentUid}
                break;
            case PostType.pinned:
                break;
            case PostType.search:
                break;
        }

        // Get posts and then display them.
        SwdModel.getGroupPosts(SwdPresenter.selectedGroup.gid, options, function(response) {
            if (response.data) {
                SwdPresenter.oldestPost = response.data[response.data.length - 1];
            }
            else {
                SwdPresenter.oldestPost = null;
            }

            SwdView.displayGroupPosts(response.data);
        });
    },
    /***
     * Load next page in group feed.
     */
    loadNextGroupPosts: function() {
        var options = {};

        if (SwdPresenter.oldestPost) {

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

            // Get more posts.
            SwdModel.getGroupPosts(SwdPresenter.selectedGroup.gid, {createdTime: SwdPresenter.oldestPost.created_time}, function(response) {
                if (response.data) {
                    SwdPresenter.oldestPost = response.data[response.data.length - 1];
                    SwdView.displayNextGroupPosts(response.data);
                }
            });
        }
    },
    /***
     * Brings up a send message window.
     * @param {type} id
     * @param {type} link
     */
    sendFacebookMessage: function(id, link) {
        FB.ui({
            to: id,
            method: 'send',
            link: link
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
    onClickButtonNew: function(e, args) {
        SwdView.showNewPostDialog();
    },
    onClickHtml: function(e, args) {
        SwdView.closeAllUiMenus();
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
                SwdPresenter.setSelectedGroup(response.data[0]);
            });
        }
    },
    onClickMenuItemMain: function(e, args) {
        var id = $(e.currentTarget).attr('id');
    },
    onClickNavButton: function(e, args) {
        var id = $(e.currentTarget).attr('id');

        switch (id) {
            case 'button-nav-group':
                SwdPresenter.postType = PostType.group;
                break;
            case 'button-nav-myposts':
                SwdPresenter.postType = PostType.myposts;
                break;
            case 'button-nav-liked':
                SwdPresenter.postType = PostType.pinned;
                break;
            case 'button-nav-search':
                SwdPresenter.postType = PostType.search;
                break;
        }

        SwdPresenter.loadGroupPosts();
        SwdView.setSelectedPostType(id);
    },
    onClickPanelButton: function(e, args) {
        SwdPresenter.sendFacebookMessage('', 'http://www.foxnews.com');
    },
    onClickPanelMessageUser: function(e, args) {
        var profileUrl = $(e.currentTarget).data('src');
        window.open(profileUrl);
    },
    onClickPostTile: function(e, args) {
        var id;
        var post;

        // Assuming one of the child elements of post-tile was clicked.
        id = $(e.currentTarget).parents('div.post-tile').attr('id');

        if (!id) {
            id = $(e.currentTarget).attr('id');
        }

        e.stopPropagation();

        SwdModel.getPostDetails(id, function(response) {
            post = response.data[0];

            if (post) {
                SwdModel.getUserData(post.actor_id, function(response) {
                    SwdView.showPostDetails(post, response.data[0]);
                });
            }
            else {
                // TODO: Do a real error message.
                alert('Unable to display post. It was most likely deleted.');
            }
        });
    },
    onScrollGroupFeed: function(e, args) {
        // Check to see if the user has scrolled all the way to the bottom.
        if ($(e.currentTarget).scrollTop() + $(e.currentTarget).innerHeight() >= e.currentTarget.scrollHeight) {
            SwdPresenter.loadNextGroupPosts();
        }
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
//        $('#tabs-main').tabs({
//            heightStyle: 'fill'
//        });
//        }).addClass("ui-tabs-vertical ui-helper-clearfix");

//        $("#tabs li").removeClass("ui-corner-top").addClass("ui-corner-left");

        //$('.tabs-bottom .ui-tabs-nav, .tabs-bottom .ui-tabs-nav > *').removeClass('ui-corner-all ui-corner-top').addClass('ui-corner-bottom');

        // move the nav to the bottom
//        $('.tabs-bottom .ui-tabs-nav').appendTo('.tabs-bottom');

        // Set up buttons
        $('.button-nav').button();

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

        $('#post-button-comment').button({
            icons: {
                primary: 'ui-icon-comment'
            }
        });

        $('#post-button-pm').button({
            icons: {
                primary: 'ui-icon-mail-closed'
            }
        });

        $('#post-button-like').button({
            icons: {
                primary: 'ui-icon-pin-s'
            }
        });

        $('#post-message-user').hover(function() {
            $(this).removeClass('ui-state-default').addClass('ui-state-hover');
        }, function() {
            $(this).removeClass('ui-state-hover').addClass('ui-state-default');
        });

        $('#right-panel').click(function(e) {
            e.stopPropagation();
        });

        $('#post-message-user').button();

        // Init menus.
        $('#popup-menu-main').menu();

        // Fade out the div we are using to hide non-initted elements.
        $('#overlay-app-loading').fadeOut('fast');
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
     */
    displayGroupPosts: function(posts) {
        // Populate the DOM, clearing any previous posts.
        SwdView.populatePosts(posts, true);
    },
    /***
     * After a query calling for the next batch of results, display them.
     * @param {type} posts
     */
    displayNextGroupPosts: function(posts) {
        // Populate the DOM, not clearing any previous posts.
        SwdView.populatePosts(posts, false);
    },
    /***
     * Write posts to the page.
     * @param {type} posts
     * @param {type} empty
     */
    populatePosts: function(posts, empty) {
        var i;
        var isEmpty;
        var url;
        var message;
        var post;
        var postTile;
        var primaryContent;
        var secondaryContent;
        var hasMessage;
        var hasImage;

        if (empty === true) {
            // Clear anything that is currently being displayed.
            $('#group-feed').empty();
        }

        // If there is a feed to display, then display it.
        if (posts) {
            for (i = 0; i < posts.length; i++) {
                isEmpty = false;

                post = posts[i];

                if (post.message) {
                    message = post.message;
                }
                else {
                    message = null;
                }

                if (post.attachment && post.attachment.media && post.attachment.media[0] && post.attachment.media[0].src) {
                    url = post.attachment.media[0].src;
                }
                else {
                    url = null;
                }

                postTile = $('<div id="' + post.post_id + '" class="post-tile ui-widget ui-widget-content ui-state-default"><div class="post-tile-primary-content"></div><div class="post-tile-secondary-content"></div></div>')
                primaryContent = $(postTile).children('.post-tile-primary-content');
                secondaryContent = $(postTile).children('.post-tile-secondary-content');

                if (message && url) {
                    $(postTile).addClass('post-tile-multi');
                    $(primaryContent).css('background-image', 'url("' + url + '")');
                    $(secondaryContent).text(message);
                }
                else if (message && !url)
                {
                    $(postTile).addClass('post-tile-multi');
                    $(primaryContent).css('background-image', 'url("/img/no-image.jpg"');
                    $(secondaryContent).text(message);
                }
                else if (!message && url)
                {
                    $(postTile).addClass('post-tile-image');
                    $(primaryContent).css('background-image', 'url("' + url + '")');
                }
                else {
                    isEmpty = true;
                }

                if (!isEmpty) {
                    $(postTile).hide().appendTo('#group-feed');
                }
            }

            // Sleekly fade in the post tile elements.
            // From: http://www.paulirish.com/2008/sequentially-chain-your-callbacks-in-jquery-two-ways/
            (function shownext(jq) {
                jq.eq(0).fadeIn(120, function() {
                    (jq = jq.slice(1)).length && shownext(jq);
                });
            })($('div.post-tile'));

            // Associate the click event handler for newly created posts.
            $('.post-tile > *').click(SwdView.handlers['onClickPostTile']);
            $('.post-tile').hover(function() {
                $(this).removeClass('ui-state-default').addClass('ui-state-hover');
            }, function() {
                $(this).removeClass('ui-state-hover').addClass('ui-state-default');
            });

//            $('#group-feed').selectable();

            // Scroll up a tiny bit so the app is never at the bottom of the page after loading posts.
            $('.ui-tabs-panel').scrollTop($('.ui-tabs-panel').scrollTop() - 1);
        }
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
    setSelectedPostType: function(id) {
        $('.button-nav').removeClass('selected-nav');
        $('#' + id).removeClass('ui-state-default').addClass('selected-nav');
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
     * Shows the post details for the selected post.
     * @param {type} post Post to load into right column.
     * @param {type} user User data
     */
    showPostDetails: function(post, user) {
        var userImage;

        // Remove old image. Since we might be displaying a link or iframe instead.
        //$('#panel-image .panel-post-image').remove();

        if (post.attachment && post.attachment.media && post.attachment.media[0] && post.attachment.media[0].src) {
            // Hide the no-image container and display the post's attached image.
            $('#post-no-image').hide();
            $('#post-image').css('background-image', 'url("' + post.attachment.media[0].src.replace('_s.jpg', '_n.jpg') + '")');
            // Append new image;
            //$('#panel-image div').hide();
            //$('#panel-image').append('<img class="panel-post-image" src="' + post.attachment.media[0].src.replace('_s.jpg', '_n.jpg') + '">');
        }
        else {
            //$('#panel-image div a').attr('href', post.permalink).text(post.permalink);
            //$('#panel-image div').show();
            // Show the no-image notification.
            $('#post-permalink').text(post.permalink);
            $('#post-image').hide();
            $('#post-no-image').show();
        }

        if (user.pic_square) {
            userImage = user.pic_square;
        }
        else {
            userImage = '';
        }

        //$('#panel-image img').attr('src', src);
        $('#post-message-pic').css('background-image', 'url("' + userImage + '")');
        $('#post-message-name').text(user.first_name + ' ' + user.last_name);
        $('#post-message-user').data('src', user.profile_url);
        $('#post-message-text').text(post.message);

        $('#right-panel-empty').fadeOut();
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
