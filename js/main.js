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
//var AppUrl = 'TODO: '

// Test AppId
var AppId = '652991661414427';
var AppUrl = 'http://bit.ly/1aXsWl3';

// http://stackoverflow.com/questions/1102215/mvp-pattern-with-javascript-framework

/**
 * Model for the Swapper's Delight program.
 */
var SwdModel = {
    /**
     * Create a new post on the selected group's or groups' wall(s).
     * @param {type} callbacks
     */
    createNewPost: function(callbacks) {

    },
    /***
     * Get posts in the group that are liked.
     * @param {type} uid
     * @param {type} gid
     * @param {type} callbacks
     */
    getLikedPosts: function(uid, gid, callbacks) {
        var url = '/php/liked-posts.php?gid=' + gid + '&uid=' + uid;

        $.ajax({
            type: 'GET',
            url: url,
            success: function(response) {
                callbacks.success.call(SwdModel, JSON.parse(response));
            },
            fail: function(response) {
                callbacks.fail.call(SwdModel, JSON.parse(response));
            }
        });
    },
    /***
     * Query database for groups that the user is a member of.
     * @param {type} callbacks
     */
    getGroupInfo: function(callbacks) {
        $.ajax({
            type: 'GET',
            url: '/php/group-info.php',
            success: function(response) {
                callbacks.success.call(SwdModel, JSON.parse(response));
            },
            fail: function(response) {
                callbacks.fail.call(SwdModel, JSON.parse(response));
            }
        });
    },
    /***
     * Get posts that are owned by the current user in the provided group. Go back 42 days.
     * @param {type} uid
     * @param {type} gid
     * @param {type} callbacks
     */
    getMyPosts: function(uid, gid, callbacks) {
        var url = '/php/my-posts.php?gid=' + gid + '&uid=' + uid;

        $.ajax({
            type: 'GET',
            url: url,
            success: function(response) {
                callbacks.success.call(SwdModel, JSON.parse(response));
            },
            fail: function(response) {
                callbacks.fail.call(SwdModel, JSON.parse(response));
            }
        });
    },
    /***
     * AJAX call to FB group feed.
     * @param {type} gid Group id whose posts are to be retrieved.
     * @param {type} updatedTime
     * @param {type} callbacks Completed callback function.
     */
    getNewestPosts: function(gid, updatedTime, callbacks) {
        var url = '/php/new-posts.php?gid=' + gid;

        if (updatedTime) {
            url += '&updatedTime=' + updatedTime;
        }

        $.ajax({
            type: 'GET',
            url: url,
            success: function(response) {
                callbacks.success.call(SwdModel, JSON.parse(response));
            },
            fail: function(response) {
                callbacks.fail.call(SwdModel, JSON.parse(response));
            }
        });
    },
    /***
     * Get details for the given post.
     * @param {type} postId
     * @param {type} callbacks
     */
    getPostDetails: function(postId, callbacks) {
        $.ajax({
            type: 'GET',
            url: '/php/post-details.php?postId=' + postId,
            success: function(response) {
                callbacks.success.call(SwdModel, JSON.parse(response));
            },
            fail: function(response) {
                callbacks.fail.call(SwdModel, JSON.parse(response));
            }
        });
    },
    /***
     * Like a post.
     * @param {type} id
     * @param {type} callbacks
     */
    likePost: function(id, callbacks) {
        alert(id);
    },
    /***
     * Post a comment on a post.
     * @param {type} postId
     * @param {type} comment
     * @param {type} callbacks
     */
    postComment: function(postId, comment, callbacks) {
        $.ajax({
            type: 'POST',
            url: '/php/post-comment.php',
            dataType: 'json',
            data: {
                'postId': postId,
                'comment': comment
            },
            success: function(response) {
                callbacks.success.call(SwdModel, response);
            },
            fail: function(response) {
                callbacks.fail.call(SwdModel, response);
            }
        });
    },
    /***
     * Sync the server with the current session.
     * @param {type} callbacks
     */
//    startSession: function(callbacks) {
//        $.ajax({
//            type: 'GET',
//            url: '/php/session.php',
//            success: function(response) {
//                callbacks.success.call(SwdModel);
//            },
//            fail: function(response) {
//                callbacks.fail.call(SwdModel, JSON.parse(response));
//            }
//        });
//    },
    /***
     * Unlike a post. 
     * @param {type} postId
     * @param {type} callbacks
     */
    unlikePost: function(postId, callbacks) {

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
    clientHeight: null,
    uid: null,
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

//            FB.Event.subscribe('auth.authResponseChange', function(response) {
//                //SwdPresenter.uid = response.authResponse.userID;
//                //SwdPresenter.startApp();
//            });

            // Try to get a session going if there isn't one already.
            FB.getLoginStatus(function(response) {
                if (response.status === 'connected') {
                    SwdPresenter.uid = response.authResponse.userID;
                    SwdPresenter.startApp();
                }
                else {
                    FB.login(function(response) {
                        if (response.status === 'connected') {
                            SwdPresenter.uid = response.authResponse.userID;
                            SwdPresenter.startApp();
                        }
                    }, {
                        scope: 'user_groups,user_likes,publish_stream'
                    });
                }
            });
        });
    },
    /***
     * Starts the application after init has finished.
     */
    startApp: function() {
        var i, selectedGroups;

        if (!SwdPresenter.groups) {
            // Retrieve group info for logged in user.
            SwdModel.getGroupInfo({
                success: function(response) {
                    SwdPresenter.groups = response;

                    selectedGroups = [];

                    // Find groups that have been marked as 'BST'
                    for (i = 0; i < SwdPresenter.groups.length; i++) {
                        if (SwdPresenter.groups[i].marked) {
                            selectedGroups.push(SwdPresenter.groups[i]);
                        }
                    }

                    if (SwdPresenter.groups) {
                        SwdPresenter.setSelectedGroup(selectedGroups[0]);
                        SwdView.addGroupsToMenu(selectedGroups);
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
                },
                fail: function(response) {
                    SwdView.showError(response);
                }
            });
        }
    },
    /***
     * Periodically call FB.Canvas.getPageInfo in order to dynmically update the UI within the canvas
     * iframe.
     * @param {type} stop
     */
    facebookPageInfoPoll: function(stop) {
        if (!stop) {
            FB.Canvas.getPageInfo(function(pageInfo) {
                var scrollTop, offsetTop, clientHeight, offset, height, scrollPos;

                scrollTop = parseInt(pageInfo.scrollTop);
                offsetTop = parseInt(pageInfo.offsetTop);
                clientHeight = parseInt(pageInfo.clientHeight);

                SwdPresenter.clientHeight = clientHeight;

                // Calculate how far to offset things.
                offset = Math.max(scrollTop - offsetTop, 0);

                // Check to see if the offset has been updated.
                if (offset !== SwdPresenter.prevOffset) {
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

                    scrollPos = $('#app-content').height() - clientHeight;

                    // Detect scroll at bottom 10% of page.
                    if (scrollTop >= scrollPos && scrollPos >= 0) {
                        //alert(scrollTop + ' ' + $('#app-content').height() + ' ' + clientHeight);
                        SwdPresenter.loadPosts(true);
                    }

                    FB.Canvas.setSize({
                        height: Math.max($('html').height(), clientHeight)
                                //height: Math.max(clientHeight, 810)
                    });

                    // Call the polling function again after 100ms.
                    setTimeout(SwdPresenter.facebookPageInfoPoll, 100);
                }
            });
        }
    },
    /***
     * Load posts liked by user.
     */
    loadLikedPosts: function() {
        SwdModel.getLikedPosts(SwdPresenter.uid, SwdPresenter.selectedGroup.gid, {
            success: function(response) {
                SwdPresenter.loadPostsComplete(null, response);
            },
            fail: function(response) {
                SwdView.showError(response);
            }
        });
    },
    /***
     * Load posts owned by user.
     */
    loadMyPosts: function() {
        SwdModel.getMyPosts(SwdPresenter.uid, SwdPresenter.selectedGroup.gid, {
            success: function(response) {
                SwdPresenter.loadPostsComplete(null, response);
            },
            fail: function(response) {
                SwdView.showError(response);
            }
        });
    },
    /***
     * Load feed for the current group.
     * @param {type} loadNextPage
     */
    loadNewestPosts: function(loadNextPage, updatedTime) {
        // Get posts and then display them.
        SwdModel.getNewestPosts(SwdPresenter.selectedGroup.gid, updatedTime, {
            success: function(response) {
                SwdPresenter.loadPostsComplete(loadNextPage, response);
            },
            fail: function(response) {
                SwdView.showError(response);
            }
        });
    },
    /***
     * High level post loading function.
     * @param {type} loadNextPage
     */
    loadPosts: function(loadNextPage) {
        var updatedTime;

        // Pause window data polling.
        SwdPresenter.facebookPageInfoPoll(true);

        if (loadNextPage && SwdPresenter.oldestPost) {
            console.log('Loading Next Page');
            updatedTime = SwdPresenter.oldestPost.updated_time;
//            if (SwdPresenter.oldestPost) {
//                updatedTime = SwdPresenter.oldestPost.updated_time;
//            }
//            else {
//                updatedTime = null;
//            }
        }
        else {
            console.log('Loading new content.');
            updatedTime = null;
            SwdView.clearPosts();
            SwdPresenter.resetFbCanvasSize();
            SwdView.showFeedLoadingAjaxDiv();
        }

        switch (SwdPresenter.postType) {
            case PostType.group:
                SwdPresenter.loadNewestPosts(loadNextPage, updatedTime);
                break;
            case PostType.myposts:
                if (!loadNextPage) {
                    // This request is so intensive, that it's best to return everything at once, rather than implement paging.
                    SwdPresenter.loadMyPosts();
                }
                break;
            case PostType.liked:
                if (!loadNextPage) {
                    // This request is so intensive, that it's best to return everything at once, rather than implement paging.
                    SwdPresenter.loadLikedPosts();
                }
                break;
            case PostType.search:
                break;
        }
    },
    loadPostsComplete: function(loadNextPage, response) {
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
    },
    /***
     * Reset Facebook Canvas Size to default value of 800
     */
    resetFbCanvasSize: function() {
        FB.Canvas.setSize({
            height: 810
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
        SwdPresenter.loadPosts(false);
        SwdView.setGroupButtonText(group.name);
    },
    // Event Handlers (onX(e, args))
//    onFBauthResponseChange: function(url, html_element) {
//        alert('TEST');
//    },
    onClickButtonNew: function(e, args) {
        SwdView.showFloatingPanel('#new-post-panel');
    },
    onClickButtonRefresh: function(e, args) {
        SwdPresenter.loadPosts(false);
    },
    onClickHtml: function(e, args) {
        SwdView.closeAllUiMenus();
        SwdView.hideFloatingPanel('.floating-panel');
    },
    onClickMenuButton: function(e, args) {
        SwdView.showUiMenu(e);
    },
    onClickMenuItemGroup: function(e, args) {
        var i, id, group;

        id = $(e.currentTarget).attr('id');

        if (id === 'menu-item-choose-groups') {
            // TODO: Display group chooser dialog.
        }
        else {
            for (i = 0; i < SwdPresenter.groups.length; i++) {
                if (id === SwdPresenter.groups[i].gid) {
                    group = SwdPresenter.groups[i];
                    break;
                }
            }

            // Set selected group and load its feed.
            SwdPresenter.setSelectedGroup(group);
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
                SwdPresenter.postType = PostType.liked;
                break;
            case 'button-nav-search':
                SwdPresenter.postType = PostType.search;
                break;
        }

        SwdPresenter.loadPosts(false);
        SwdView.setSelectedPostType(id);
    },
    onClickPanelMessageUser: function(e, args) {
        var profileUrl = $(e.currentTarget).data('src');
        window.open(profileUrl);
    },
    onClickPostButtonComment: function(e, args) {
        var id, comment;

        id = $('#panel-post').data('id');
        comment = $('#post-comment-text > textarea').val();

        // Post the comment.
        SwdModel.postComment(id, comment, {
            success: function(response) {
                //alert(response.postId + ' ' + response.comment);
                SwdView.addPostComment(response);
                SwdView.clearPostCommentText();
            },
            fail: function(response) {
                SwdView.showError(response);
            }
        });
    },
    onClickPostButtonLike: function(e, args) {
        var id;

        id = $('#panel-post').data('id');

        // Post the comment.
        SwdModel.likePost(id, {
            success: function(response) {
                // TODO: Update View
            },
            fail: function(response) {
                SwdView.showError(response);
            }
        });
    },
    onClickPostButtonPm: function(e, args) {
        var id;

        id = $('#panel-post').data('actor_id');

        // Yes, shamelessly plug the app.
        SwdPresenter.sendFacebookMessage(id, AppUrl);
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
        SwdModel.getPostDetails(id, {
            success: function(response) {
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
            },
            fail: function(response) {
                SwdView.showError(response);
            }
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
     * @param {type} groups
     */
    addGroupsToMenu: function(groups) {
        var i = 0;

        $('#menu-item-no-groups').hide();

        for (i = 0; i < groups.length; i++) {
            $('#popup-menu-groups').append('<li id="' + groups[i].gid + '" class="menu-item-group"><a href="#"><span class="ui-icon" style="background-image: url(' + groups[i].icon + ')"></span><div style="display: inline-block; margin-left: 5px">' + groups[i].name + '</div></a></li>');
        }
    },
    addPostComment: function(comment) {
        var commentDiv, timeStamp, userImage;

        // Set user image
        if (comment.user.pic_square) {
            userImage = 'url("' + comment.user.pic_square + '")';
        }
        else {
            userImage = '';
        }

        //timeStamp = $.datepicker.formatDate('DD, mm/dd/yy at HH:MM', new Date(post.comments[i].time * 1000));
        timeStamp = new moment(new Date(comment.time * 1000));

        commentDiv = $('<div class="post-comment ui-corner-all ui-widget ui-widget-content"><div class="ui-state-default"><div class="post-comment-user-image"></div><div class="post-comment-header"><p class="wrapper"><a class="post-comment-user-name" href="' + comment.user.profile_url + '" target="_blank">' + comment.user.first_name + ' ' + comment.user.last_name + '</a><span class="timestamp">' + timeStamp.calendar() + '</span></p></div></div>' + comment.text + '</div>');
        $(commentDiv).find('.post-comment-user-image').css('background-image', userImage);
        $(commentDiv).hide().appendTo('#post-comment-list').fadeIn();
        //$('#post-comment-list').append(commentDiv);
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
    /***
     * Clear all posts from the view.
     */
    clearPosts: function() {
        $('#post-feed .post-tile').remove();
    },
    /***
     * Clear comment box.
     */
    clearPostCommentText: function() {
        $('#post-comment-text > textarea').val('');
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
        var i, isEmpty, imageUrl, imageUrlBig, message, post, postTile, primaryContent, secondaryContent;

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

            i = 0;

            // Sleekly fade in the post tile elements.
            // From: http://www.paulirish.com/2008/sequentially-chain-your-callbacks-in-jquery-two-ways/
            (function shownext(jq) {
                jq.eq(0).fadeIn(60, function() {
                    FB.Canvas.setSize({
                        height: Math.max($('html').height(), SwdPresenter.clientHeight)
                                //height: Math.max(clientHeight, 810)
                    });
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
    /***
     * Displays a lovely error message. Something which the user loves.
     * @param {type} message
     */
    showError: function(message) {

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
        var userImage, postImage, i;

        if (post.image_url) {
            postImage = 'url("' + post.image_url + '")';
            // Hide the no-image container and display the post's attached image.
            $('#post-no-image').hide();
            $('#post-image').show();
            $('#post-image').css('background-image', postImage);
        }
        else {
            // Show the no-image notification.
            $('#post-image').hide();
            $('#post-no-image').show();
        }

        $('#post-permalink > a').attr('href', post.permalink).text(post.permalink);

        if (post.user.pic_square) {
            userImage = 'url("' + post.user.pic_square + '")';
        }
        else {
            userImage = '';
        }

        $('#post-message-pic').css('background-image', userImage);
        $('#post-message-name').text(post.user.first_name + ' ' + post.user.last_name);
        $('#post-message-user').data('src', post.user.profile_url);
        $('#post-message-text').html(post.message);
        $('#post-comment-list').empty();
        $('#post-nocomments').show();

        if (post.comments.length > 0) {
            $('#post-nocomments').hide();

            for (i = 0; i < post.comments.length; i++) {
                SwdView.addPostComment(post.comments[i]);
            }
        }

        SwdView.clearPostCommentText();

        // Save some data for later consumption.
        $('#panel-post').data('actor_id', post.actor_id).data('permalink', post.permalink).data('id', post.post_id);
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
