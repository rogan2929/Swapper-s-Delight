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
    /***
     * Get posts in the group that are liked.
     * @param {type} gid
     * @param {type} callback
     */
    getLikedPosts: function(gid, callback) {
        // TODO: liked-posts.php
    },
    /***
     * Query database for groups that the user has marked as 'BST' (Buy, Sell, Trade)
     * @param {type} callback
     */
    getGroupInfo: function(callback) {
        $.ajax({
            type: 'GET',
            url: '/php/group-info.php',
            success: function(response) {
                callback.call(SwdModel, JSON.parse(response));
            },
            fail: function(response) {
                
            }
        });
    },
    /***
     * Get posts that are owned by the current user in the provided group. Go back 42 days.
     * @param {type} gid
     * @param {type} callback
     */
    getMyPosts: function(gid, callback) {
        $.ajax({
            type: 'GET',
            url: '/php/my-posts.php?gid=' + gid,
            success: function(response) {
                callback.call(SwdModel, JSON.parse(response.responseText));
            },
            fail: function(response) {
                
            }
        });
    },
    /***
     * AJAX call to FB group feed.
     * @param {type} gid Group id whose posts are to be retrieved.
     * @param {type} createdTime
     * @param {type} callback Completed callback function.
     */
    getNewestPosts: function(gid, createdTime, callback) {
        var url = '/php/new-posts.php?gid=' + gid;
        
        if (createdTime) {
            url += '&createdTime=' + createdTime;
        }
        
        $.ajax({
            type: 'GET',
            url: url,
            success: function(response) {
                callback.call(SwdModel, JSON.parse(response.responseText));
            },
            fail: function(response) {
                
            }
        });
    },
    /***
     * AJAX call to FB comment feed for given post.
     * @param {type} postId
     * @param {type} callback
     */
    getPostComments: function(postId, callback) {
        //SwdModel.facebookFQLQuery('SELECT fromid,text,text_tags,attachment FROM comment WHERE post_id="' + id + '"', callback);
    },
    /***
     * Get details for the given post.
     * @param {type} postId
     * @param {type} callback
     */
    getPostDetails: function(postId, callback) {
        //SwdModel.facebookFQLQuery('SELECT post_id,message,actor_id,permalink,like_info,share_info,comment_info,tagged_ids FROM stream WHERE post_id="' + id + '"', callback);
        // TODO: post-details.php
    },
    /***
     * Get data for the given user.
     * @param {type} uid
     * @param {type} callback
     */
    getUserData: function(uid, callback) {
        //SwdModel.facebookFQLQuery('SELECT last_name,first_name,pic_square,profile_url FROM user WHERE uid=' + id, callback);
        // TODO: user-data.php
    }
    /***
     * Establish a Facebook session on the server.
     * @param {type} callback
     */
    /*
    startSession: function(callback) {
        $.ajax({
            type: 'GET',
            url: '/php/session.php',
            complete: callback
        });
    }*/
};

/**
 * Presenter for the Swapper's Delight program.
 */
var SwdPresenter = {
    oldestPost: null,
    postType: PostType.group,
    selectedGroup: null,
    /**
     * Entry point of program.
     */
    init: function() {
        SwdView.initView();
		SwdPresenter.startApp();

        // Fetch the FB JS API
        /*
        $.getScript('//connect.facebook.net/en_US/all.js', function() {
            FB.init({
                appId: AppId,
                cookie: true
            });

            $('#loginbutton,#feedbutton').removeAttr('disabled');

            // Try to get a login session going if there isn't one already.
            FB.getLoginStatus(function(response) {
                if (response.status === 'connected') {
                    SwdModel.startSession(function() {
                        SwdPresenter.startApp();
                    });
                } else {
                    FB.login(function(response) {
                        if (response.status === 'connected') {
                            SwdModel.startSession(function() {
                                SwdPresenter.startApp();
                            });
                        }
                    }, {scope: 'user_groups,user_likes'});
                }
            });
        });*/
    },
    /***
     * Starts the application after init has finished.
     */
    startApp: function() {
        // Retrieve group info for logged in user.
        SwdModel.getGroupInfo(function(response) {
            var groups = response;
            
            alert(groups[0].name);

            if (groups) {
                SwdPresenter.setSelectedGroup(groups[0]);
                SwdView.addGroupsToMenu(groups);

                $('#popup-menu-groups').menu().position({
                    of: $('#button-menu-groups'),
                    my: 'left top',
                    at: 'left bottom'
                });

                // Install Event Handlers
                SwdView.installHandler('onClickButtonNew', SwdPresenter.onClickButtonNew, '#button-new', 'click');
                SwdView.installHandler('onClickHtml', SwdPresenter.onClickHtml, 'html', 'click');
                SwdView.installHandler('onClickMenuButton', SwdPresenter.onClickMenuButton, '.menu-button', 'click');
                SwdView.installHandler('onClickMenuItemGroup', SwdPresenter.onClickMenuItemGroup, '.menu-item-group', 'click');
                SwdView.installHandler('onClickMenuItemMain', SwdPresenter.onClickMenuItemMain, '.menu-item-main', 'click');
                SwdView.installHandler('onClickNavButton', SwdPresenter.onClickNavButton, '.button-nav', 'click');
                SwdView.installHandler('onClickPostButton', SwdPresenter.onClickPostButton, '.post-button', 'click');
                SwdView.installHandler('onClickPanelMessageUser', SwdPresenter.onClickPanelMessageUser, '#post-message-user', 'click');
                SwdView.installHandler('onClickPostTile', SwdPresenter.onClickPostTile, '.post-tile > *', 'click');
                SwdView.installHandler('onScrollGroupFeed', SwdPresenter.onScrollGroupFeed, '#group-feed', 'scroll');
                SwdView.installHandler('onWindowResize', SwdPresenter.onWindowResize, window, 'resize');

                SwdView.positionMenus();
            }

            else {
                // Have the view prompt the user to edit BST groups.
            }
        });
    },
    /***
     * Load posts liked by user.
     */
    loadLikedPosts: function() {

    },
    /***
     * Load posts owned by user.
     */
    loadMyPosts: function() {
        SwdModel.getMyPosts(SwdPresenter.currentUid, SwdPresenter.selectedGroup.gid, SwdPresenter.accessToken, function(response) {
            alert(response.responseText);
        });
    },
    /***
     * Load feed for the current group.
     * @param {type} loadNextPage
     */
    loadNewestPosts: function(loadNextPage) {
        var createdTime;
        var posts;
        var imageQuery;
        var i;
        var j;

        // TODO: configure options based on what tab the user is on.
        if (SwdPresenter.postType === PostType.search) {

        }

        if (loadNextPage) {
            createdTime = SwdPresenter.oldestPost.created_time;
        }
        else {
            createdTime = null;
        }

        // Get posts and then display them.
        SwdModel.getNewestPosts(SwdPresenter.selectedGroup.gid, createdTime, function(response) {
            if (!loadNextPage) {
                // Clear previous results, unless loading a new page.
                SwdView.clearPosts();
                SwdPresenter.oldestPost = null;
            }

            if (response.data && response.data[0].fql_result_set) {
                //SwdPresenter.oldestPost = response.data[response.data.length - 1];

                posts = response.data[0].fql_result_set;
                imageQuery = response.data[1].fql_result_set;

                SwdPresenter.oldestPost = posts[posts.length - 1];

                for (i = 0; i < posts.length; i++) {
                    posts[i]['image_url'] = null;

                    // For posts with an image, look for associate image data.
                    if (posts[i].attachment && posts[i].attachment.media
                            && posts[i].attachment.media[0] && posts[i].attachment.media[0].photo) {
                        for (j = 0; j < imageQuery.length; j++) {
                            // See if attachment media has a match for object_id.
                            if (posts[i].attachment.media[0].photo.fbid === imageQuery[j].object_id) {
                                posts[i]['image_url'] = imageQuery[j].images[4].source;
                                break;
                            }
                        }
                    }
                }

                SwdView.populatePosts(posts);
            }
            else if (!loadNextPage) {
                SwdPresenter.oldestPost = null;
            }
        });
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
     * Set currently selected group.
     * @param {type} group
     */
    setSelectedGroup: function(group) {
        SwdPresenter.selectedGroup = group;
        SwdPresenter.loadNewestPosts(false);
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
                SwdPresenter.loadNewestPosts(false);
                break;
            case 'button-nav-myposts':
                SwdPresenter.postType = PostType.myposts;
                SwdPresenter.loadMyPosts();
                break;
            case 'button-nav-liked':
                SwdPresenter.postType = PostType.pinned;
                SwdPresenter.loadLikedPosts();
                break;
            case 'button-nav-search':
                SwdPresenter.postType = PostType.search;
                break;
        }

        SwdView.setSelectedPostType(id);
    },
    onClickPanelMessageUser: function(e, args) {
        var profileUrl = $(e.currentTarget).data('src');
        window.open(profileUrl);
    },
    onClickPostButton: function(e, args) {
        SwdPresenter.sendFacebookMessage('', 'http://www.foxnews.com');
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

            // Try to retrieve image URL from object.
            post['image_url'] = $('#' + id).data('image_url');

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
        if (($(e.currentTarget).scrollTop() + $(e.currentTarget).innerHeight()) >= (e.currentTarget.scrollHeight)) {
            SwdPresenter.loadNewestPosts(true);
        }

        // TODO: Scroll up to refresh.
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
     * @param {type} groups
     */
    addGroupsToMenu: function(groups) {
        var i = 0;

        for (i = 0; i < group.length; i++) {
            $('#popup-menu-groups').append('<li id="' + groups[i].gid + '" class="menu-item-group"><a href="#"><span class="ui-icon" style="background-image: url(' + groups[i].icon + ')"></span><div style="display: inline-block; margin-left: 5px">' + groups[i].name + '</div></a></li>');
        }
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
    clearPosts: function() {
        $('#group-feed').empty();
    },
    /***
     * Closes all Jquery UI menus.
     */
    closeAllUiMenus: function() {
        $('.ui-menu').hide();
    },
    /***
     * Write posts to the page.
     * @param {type} posts
     */
    populatePosts: function(posts) {
        var i;
        var isEmpty;
        var url;
        var message;
        var post;
        var postTile;
        var primaryContent;
        var secondaryContent;

        // If there is a feed to display, then display it.
        if (posts) {
            for (i = 0; i < posts.length; i++) {
                isEmpty = false;

                post = posts[i];

                if (post.message) {
                    message = post.message;
                    //message = post.created_time;
                }
                else {
                    message = null;
                }

                if (post.image_url) {
                    url = post.image_url;
                }
                else {
                    url = null;
                }

                postTile = $('<div id="' + post.post_id + '" class="post-tile ui-corner-all ui-widget ui-widget-content ui-state-default"><div class="post-tile-primary-content"></div><div class="post-tile-secondary-content"></div></div>').data('image_url', url);
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
                    $(primaryContent).css('background-image', 'url("/img/no-image.jpg")');
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

            // Scroll up a tiny bit so the app is never at the bottom of the page after loading posts.
            $('#group-feed').scrollTop($('#group-feed').scrollTop() - 1);
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
        $('#' + id).addClass('selected-nav');
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
        var postImage;

        //if (post.attachment && post.attachment.media && post.attachment.media[0] && post.attachment.media[0].src) {
        if (post.image_url) {
            postImage = 'url("' + post.image_url + '")';

            // Hide the no-image container and display the post's attached image.
            $('#post-no-image').hide();
            $('#post-image').show();
            $('#post-image').css('background-image', postImage);
        }
        else {
            // Show the no-image notification.
            $('#post-permalink').attr('href', post.permalink).text(post.permalink);
            $('#post-image').hide();
            $('#post-no-image').show();
        }

        if (user.pic_square) {
            userImage = 'url("' + user.pic_square + '")';
        }
        else {
            userImage = '';
        }

        $('#post-message-pic').css('background-image', userImage);
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
