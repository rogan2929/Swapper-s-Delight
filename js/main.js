"use strict";
// "Enums"

// PostType Enum
var PostType = {
    group: 0,
    myposts: 1,
    liked: 2,
    search: 3
};
// Prod AppId
//var AppId = '1401018793479333';

// Test AppId
var AppId = '652991661414427';
// http://stackoverflow.com/questions/1102215/mvp-pattern-with-javascript-framework

/**
 * Model for the Swapper's Delight program.
 */
var SwdModel = {
    /**
     * Create a new post on the selected group's or groups' wall(s).
     */
    createNewPost: function(callback) {

    },
    /***
     * Get posts in the group that are liked.
     * @param {type} gid
     * @param {type} callback
     */
    getLikedPosts: function(gid, callback) {
        $.ajax({
            type: 'GET',
            url: '/php/liked-posts.php?gid=' + gid,
            success: function(response) {
                callback.call(SwdModel, JSON.parse(response.responseText));
            },
            fail: function(response) {

            }
        });
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
     * @param {type} updatedTime
     * @param {type} callback Completed callback function.
     */
    getNewestPosts: function(gid, updatedTime, callback) {
        var url = '/php/new-posts.php?gid=' + gid;
        if (updatedTime) {
            url += '&updatedTime=' + updatedTime;
        }

        $.ajax({
            type: 'GET',
            url: url,
            success: function(response) {
                callback.call(SwdModel, JSON.parse(response));
            },
            fail: function(response) {

            }
        });
    },
    /***
     * Get details for the given post.
     * @param {type} postId
     * @param {type} callback
     */
    getPostDetails: function(postId, callback) {
        $.ajax({
            type: 'GET',
            url: '/php/post-details.php?postId=' + postId,
            success: function(response) {
                callback.call(SwdModel, JSON.parse(response));
            },
            fail: function(response) {

            }
        });
    },
    /***
     * Like a post. 
     */
    likePost: function() {

    },
    postComment: function() {

    },
    shortenUrl: function(url, callback) {
        $.ajax({
            type: 'POST',
            data: JSON.stringify({longUrl: url}),
            url: 'https://www.googleapis.com/urlshortener/v1/url',
            contentType: 'application/json',
            success: function(response) {
                callback.call(SwdModel, response);
            },
            fail: function(response) {

            }
        });
    },
    /***
     * Sync the server with the current session.
     */
    startSession: function(callback) {
        $.ajax({
            type: 'GET',
            url: '/php/session.php',
            success: function(response) {
                callback.call(SwdModel, response);
            },
            fail: function(response) {

            }
        });
    },
    /***
     * Unlike a post. 
     */
    unlikePost: function() {

    }
};
/**
 * Presenter for the Swapper's Delight program.
 */
var SwdPresenter = {
    oldestPost: null,
    postType: PostType.group,
    selectedGroup: null,
    groups: null,
    prevOffset: null,
    /**
     * Entry point of program.
     */
    init: function() {
        SwdView.initView();
        $.ajaxSetup({
            cache: true
        });
        // Fetch the FB JS API
        $.getScript('//connect.facebook.net/en_US/all.js', function() {
            FB.init({
                appId: AppId,
                cookie: true,
                status: true
            });
            $('#loginbutton,#feedbutton').removeAttr('disabled');
            // Try to get a session going if there isn't one already.
            FB.getLoginStatus(function(response) {
                if (response.status === 'connected') {
                    SwdModel.startSession(function() {
                        SwdPresenter.startApp();
                    });
                }
                else {
                    FB.login(function(response) {
                        if (response.status === 'connected') {
                            SwdModel.startSession(function() {
                                SwdPresenter.startApp();
                            });
                        }
                    }, {
                        scope: 'user_groups,user_likes'
                    });
                }
            });
        });
    },
    /***
     * Starts the application after init has finished.
     */
    startApp: function() {
        // Retrieve group info for logged in user.
        SwdModel.getGroupInfo(function(response) {
            SwdPresenter.groups = response;
            if (SwdPresenter.groups) {
                SwdPresenter.setSelectedGroup(SwdPresenter.groups[0]);
                SwdView.addGroupsToMenu(SwdPresenter.groups);
                $('#popup-menu-groups').menu();
                // Install Event Handlers
                SwdView.installHandler('onClickButtonNew', SwdPresenter.onClickButtonNew, '#button-new', 'click');
                SwdView.installHandler('onClickButtonRefresh', SwdPresenter.onClickButtonRefresh, '#button-refresh', 'click');
                SwdView.installHandler('onClickHtml', SwdPresenter.onClickHtml, 'html', 'click');
                SwdView.installHandler('onClickMenuButton', SwdPresenter.onClickMenuButton, '.menu-button', 'click');
                SwdView.installHandler('onClickMenuItemGroup', SwdPresenter.onClickMenuItemGroup, '.menu-item-group', 'click');
                SwdView.installHandler('onClickMenuItemMain', SwdPresenter.onClickMenuItemMain, '.menu-item-main', 'click');
                SwdView.installHandler('onClickNavButton', SwdPresenter.onClickNavButton, '.button-nav', 'click');
                SwdView.installHandler('onClickPostButtonComment', SwdPresenter.onClickPostButtonComment, '#post-button-comment', 'click');
                SwdView.installHandler('onClickPostButtonLike', SwdPresenter.onClickPostButtonLike, '#post-button-like', 'click');
                SwdView.installHandler('onClickPostButtonPm', SwdPresenter.onClickPostButtonPm, '#post-button-pm', 'click');
                SwdView.installHandler('onClickPanelMessageUser', SwdPresenter.onClickPanelMessageUser, '#post-message-user', 'click');
                SwdView.installHandler('onClickPostTile', SwdPresenter.onClickPostTile, '.post-tile > *', 'click');
                SwdView.installHandler('onWindowResize', SwdPresenter.onWindowResize, window, 'resize');
                SwdView.positionMenus();
            }
            else {
                // Have the view prompt the user to edit BST groups.
            }
        });
    },
    /***
     * Periodically call FB.Canvas.getPageInfo in order to dynmically update the UI within the canvas
     * iframe.
     */
    facebookPageInfoPoll: function() {
        FB.Canvas.getPageInfo(function(pageInfo) {
            var scrollTop;
            var offsetTop;
            var windowHeight;
            var clientHeight;
            var offset;
            var height;
            scrollTop = parseInt(pageInfo.scrollTop);
            offsetTop = parseInt(pageInfo.offsetTop);
            clientHeight = parseInt(pageInfo.clientHeight);
            // Calculate how far to offset things.
            offset = Math.max(scrollTop - offsetTop, 0);
            // Check to see if the offset has been updated.
            if (offset != SwdPresenter.prevOffset) {
                SwdPresenter.prevOffset = offset;
                // Update fixed divs
                SwdView.setFixedDivs(offset);
                // Update post-details-panel height
                if (scrollTop > offsetTop) {
                    height = clientHeight - 10;
                }
                else {
                    height = clientHeight - offsetTop - 10;
                }

                SwdView.setFloatingPanelHeight(height);
                // Detect scroll at bottom
                if (scrollTop >= $('#app-content').height() - clientHeight) {
                    SwdPresenter.loadNewestPosts(true);
                }
                else {
                    // Only do another call if we are not loading more posts.
                    setTimeout(SwdPresenter.facebookPageInfoPoll, 100);
                }
            }
            else {
                setTimeout(SwdPresenter.facebookPageInfoPoll, 100);
            }
        });
    },
    /***
     * Load posts liked by user.
     */
    loadLikedPosts: function() {
        SwdPresenter.resetFbCanvasSize();
        SwdView.showFeedLoadingAjaxDiv();
        SwdModel.getLikedPosts(SwdPresenter.selectedGroup.gid, function(response) {
            alert('Not yet implemented.');
        });
    },
    /***
     * Load posts owned by user.
     */
    loadMyPosts: function() {
        SwdPresenter.resetFbCanvasSize();
        SwdView.showFeedLoadingAjaxDiv();
        SwdModel.getMyPosts(SwdPresenter.selectedGroup.gid, function(response) {
            alert('Not yet implemented.');
        });
    },
    /***
     * Load feed for the current group.
     * @param {type} loadNextPage
     */
    loadNewestPosts: function(loadNextPage) {
        var updatedTime;
        if (loadNextPage) {
            updatedTime = SwdPresenter.oldestPost.updated_time;
        }
        else {
            updatedTime = null;
            SwdView.clearPosts();
            SwdPresenter.resetFbCanvasSize();
            SwdView.showFeedLoadingAjaxDiv();
        }

        // Get posts and then display them.
        SwdModel.getNewestPosts(SwdPresenter.selectedGroup.gid, updatedTime, function(response) {
            if (!loadNextPage) {
                // Clear previous results, unless loading a new page.
                SwdPresenter.oldestPost = null;
            }

            if (response) {
                SwdPresenter.oldestPost = response[response.length - 1];
                SwdView.populatePosts(response);
            }
            else
            if (!loadNextPage) {
                SwdPresenter.oldestPost = null;
            }
        });
    },
    /***
     * Reset Facebook Canvas Size to default value of 800
     */
    resetFbCanvasSize: function() {
        FB.Canvas.setSize({
            height: 800
        });
    },
    /***
     * Brings up a send message window.
     * @param {type} id
     * @param {type} link
     */
    sendFacebookMessage: function(id, link) {
        FB.ui({
            app_id: AppId,
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
        SwdView.showFloatingPanel('#new-post-panel');
    },
    onClickButtonRefresh: function(e, args) {
        switch (SwdPresenter.postType) {
            case PostType.group:
                SwdPresenter.loadNewestPosts(false);
                break;
            case PostType.myposts:
                SwdPresenter.loadMyPosts();
                break;
            case PostType.liked:
                SwdPresenter.loadLikedPosts();
                break;
            case PostType.search:
                break;
        }
    },
    onClickHtml: function(e, args) {
        SwdView.closeAllUiMenus();
        SwdView.hideFloatingPanel('.floating-panel');
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
            // Set selected group and load its feed.
            SwdPresenter.setSelectedGroup(id);
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
                SwdPresenter.postType = PostType.liked;
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
    onClickPostButtonComment: function(e, args) {
        alert($(e.currentTarget).attr('id'));
    },
    onClickPostButtonLike: function(e, args) {
        alert($(e.currentTarget).attr('id'));
    },
    onClickPostButtonPm: function(e, args) {
        var id;
        var permalink;

        id = $('#panel-post').data('actor_id');
        permalink = $('#panel-post').data('permalink');

        SwdModel.shortenUrl(permalink, function(response) {
            SwdPresenter.sendFacebookMessage(id, response.id);
        });
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
        $('#post-details-panel .ajax-loading-div').show();
        SwdView.showFloatingPanel('#post-details-panel');
        SwdModel.getPostDetails(id, function(response) {
            post = response;
            // Try to retrieve image URL from object.
            post['image_url'] = $('#' + id).data('image_url');
            if (post) {
                SwdView.showPostDetails(post);
            }
            else {
                // TODO: Do a real error message.
                alert('Unable to display post. It was most likely deleted.');
            }
        });
    },
    onWindowResize: function(e, args) {
        SwdView.positionMenus();
    },
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
        for (i = 0; i < groups.length; i++) {
            $('#popup-menu-groups').append('<li id="' + groups[i].gid + '" class="menu-item-group"><a href="#"><span class="ui-icon" style="background-image: url(' + groups[i].icon + ')"></span><div style="display: inline-block; margin-left: 5px">' + groups[i].name + '</div></a></li>');
        }
    },
    /**
     * Init function for SwdView.
     */
    initView: function() {
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
        $('#button-refresh').button({
            icons: {
                primary: 'ui-icon-refresh'
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
        $('#post-details-panel').click(function(e) {
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
        $('#post-feed .post-tile').remove();
    },
    /***
     * Closes all Jquery UI menus.
     */
    closeAllUiMenus: function() {
        $('.ui-menu').hide();
    },
    hideFeedLoadingAjaxDiv: function() {
        $('#feed-ajax-loading-div').fadeOut();
    },
    /***
     * Write posts to the page.
     * @param {type} posts
     */
    populatePosts: function(posts) {
        var i;
        var isEmpty;
        var imageUrl;
        var imageUrlBig;
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
                }
                else {
                    message = null;
                }

                if (post.image_url) {
                    imageUrl = post.image_url[1];
                    imageUrlBig = post.image_url[0];
                }
                else {
                    imageUrl = null;
                    imageUrlBig = null;
                }

                postTile = $('<div id="' + post.post_id + '" class="post-tile ui-corner-all ui-widget ui-widget-content ui-state-default"><div class="post-tile-primary-content"></div><div class="post-tile-secondary-content"></div></div>').data('image_url', imageUrlBig);
                primaryContent = $(postTile).children('.post-tile-primary-content');
                secondaryContent = $(postTile).children('.post-tile-secondary-content');
                if (message && imageUrl) {
                    $(postTile).addClass('post-tile-multi');
                    $(primaryContent).css('background-image', 'url("' + imageUrl + '")');
                    $(secondaryContent).text(message);
                }
                else
                if (message && !imageUrl) {
                    $(postTile).addClass('post-tile-multi');
                    $(primaryContent).css('background-image', 'url("/img/no-image.png")');
                    $(secondaryContent).text(message);
                }
                else
                if (!message && imageUrl) {
                    $(postTile).addClass('post-tile-image');
                    $(primaryContent).css('background-image', 'url("' + imageUrl + '")');
                }
                else {
                    isEmpty = true;
                }

                if (!isEmpty) {
                    $(postTile).hide().appendTo('#post-feed');
                }
            }

            SwdView.hideFeedLoadingAjaxDiv();
            // Sleekly fade in the post tile elements.
            // From: http://www.paulirish.com/2008/sequentially-chain-your-callbacks-in-jquery-two-ways/
            (function shownext(jq) {
                jq.eq(0).fadeIn(60, function() {
                    FB.Canvas.setSize({
                        height: Math.max($('html').height(), 800)
                    });
                    (jq = jq.slice(1)).length && shownext(jq);
                });
            })($('div.post-tile'));
            /*
             $('div.post-tile').fadeIn(100, function() {
             FB.Canvas.setSize({
             height: $('#app-content').height()
             });
             });*/

            // Associate the click event handler for newly created posts.
            $('.post-tile > *').click(SwdView.handlers['onClickPostTile']);
            $('.post-tile').hover(function() {
                $(this).removeClass('ui-state-default').addClass('ui-state-hover');
            }, function() {
                $(this).removeClass('ui-state-hover').addClass('ui-state-default');
            });
            // Scroll up a tiny bit so the app is never at the bottom of the page after loading posts.
            $('#post-feed').scrollTop($('#post-feed').scrollTop() - 1);
            // Start polling page info again.
            SwdPresenter.facebookPageInfoPoll();
        }
    },
    /***
     * Sets menu positions.
     */
    positionMenus: function() {
        $('.menu-button').each(function() {
            var menu = $(this).find('a').attr('href');
            $(menu).css({
                top: 0,
                left: 0
            });
            $(menu).position({
                of: $(this),
                my: 'left top',
                at: 'left bottom'
            });
        });
    },
    /***
     * Simulate the placing of fixed divs within the FB app canvas.
     * @param {type} offset
     */
    setFixedDivs: function(offset) {
        $('#left-rail').animate({
            top: Math.max(offset + 60, 0)
        }, 100);
        $('#main-toolbar, #post-details-panel').animate({
            top: Math.max(offset, 0)
        }, 100);
    },
    /***
     * Dynamically calculate the height of #post-details-panel, based on how large the FB canvas is.
     * @param {type} height
     */
    setFloatingPanelHeight: function(height) {
        $('.floating-panel').height(height);
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
    showFeedLoadingAjaxDiv: function() {
        $('#feed-ajax-loading-div').fadeIn();
    },
    showFloatingPanel: function(id) {
        // Make the panel modal by summoning a ui-widget-overlay.
        $('<div class="ui-widget-overlay ui-widget-front"></div>').hide().appendTo('body').fadeIn();
        $(id).show('slide', {
            easing: 'easeInOutQuint',
            direction: 'down'
        }, 300);
    },
    hideFloatingPanel: function(id) {
        $(id).hide('slide', {
            easing: 'easeInOutQuint',
            direction: 'up'
        }, 300, function() {
            $('div.ui-widget-overlay').remove();
        });
    },
    /***
     * Shows the post details for the selected post.
     * @param {type} post Post to load into floating post details panel.
     */
    showPostDetails: function(post) {
        var userImage;
        var postImage;
        var i;
        var comment;
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

        if (post.user.pic_square) {
            userImage = 'url("' + post.user.pic_square + '")';
        }
        else {
            userImage = '';
        }

        $('#post-message-pic').css('background-image', userImage);
        $('#post-message-name').text(post.user.first_name + ' ' + post.user.last_name);
        $('#post-message-user').data('src', post.user.profile_url);
        $('#post-message-text').text(post.message);
        $('#post-comment-list').empty();
        $('#post-nocomments').show();
        if (post.comments.length > 0) {
            $('#post-nocomments').hide();
            for (i = 0; i < post.comments.length; i++) {
                // Set user image
                if (post.comments[i].user.pic_square) {
                    userImage = 'url("' + post.comments[i].user.pic_square + '")';
                }
                else {
                    userImage = '';
                }

                comment = $('<div class="post-comment ui-corner-all ui-widget ui-widget-content"><div class="ui-state-default"><div class="post-comment-user-image"></div><a class="post-comment-user-name" href="' + post.comments[i].user.profile_url + '" target="_blank">' + post.comments[i].user.first_name + ' ' + post.comments[i].user.last_name + '</a></div>' + post.comments[i].text + '</div>');
                $(comment).find('.post-comment-user-image').css('background-image', userImage);
                $('#post-comment-list').append(comment);
            }
        }

        // Clear comment box.
        $('#post-comment-text > textarea').val('');
        // Save for later consumption.
        $('#panel-post').data('actor_id', post.actor_id).data('permalink', post.permalink).data('comment', post.action_links[0]).data('like', post.action_links[1]);
        $('#post-details-panel .ajax-loading-div').fadeOut();
    },
    /***
     * Shows a Jquery UI menu.
     * @param {type} e
     */
    showUiMenu: function(e) {
        var menu;
        e.stopPropagation();
        menu = $(e.currentTarget).find('a').attr('href');
        //SwdView.positionMenus(menu);
        $(menu).css({
            top: 0,
            left: 0
        });
        $(menu).position({
            of: $(e.currentTarget),
            my: 'left top',
            at: 'left bottom'
        });
        // Display the menu.
        $(menu).show('slide', {
            direction: 'up',
            duration: 300,
            easing: 'easeInOutQuint'
        });
    }
};
$(document).ready(function() {
    $.ajaxSetup({
        cache: true
    });
    SwdPresenter.init();
});
