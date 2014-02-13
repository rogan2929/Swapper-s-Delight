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
    currentlyLoading: false,
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
                    SwdView.toggleAjaxLoadingDiv('body', false);

                    SwdPresenter.groups = response;

                    selectedGroups = [];

                    // Call the polling function again after 100ms.
                    SwdPresenter.facebookPageInfoPoll();

                    // Find groups that have been marked as 'BST'
                    for (i = 0; i < SwdPresenter.groups.length; i++) {
                        if (SwdPresenter.groups[i].marked) {
                            selectedGroups.push(SwdPresenter.groups[i]);
                        }
                    }

                    if (SwdPresenter.groups) {
                        SwdPresenter.setSelectedGroup(selectedGroups[0]);
                        SwdView.addGroupsToSelectPanel(selectedGroups);

                        // Install Event Handlers
                        SwdView.installHandler('onClickButtonGroups', SwdPresenter.onClickButtonGroups, '#button-groups', 'click');
                        SwdView.installHandler('onClickButtonNew', SwdPresenter.onClickButtonNew, '#button-new', 'click');
                        SwdView.installHandler('onClickButtonRefresh', SwdPresenter.onClickButtonRefresh, '#button-refresh', 'click');
                        SwdView.installHandler('onClickHtml', SwdPresenter.onClickHtml, 'html', 'click');
                        SwdView.installHandler('onClickMenuButton', SwdPresenter.onClickMenuButton, '.menu-button', 'click');
                        SwdView.installHandler('onClickMenuItemMain', SwdPresenter.onClickMenuItemMain, '.menu-item-main', 'click');
                        SwdView.installHandler('onClickNavButton', SwdPresenter.onClickNavButton, '.button-nav', 'click');
                        SwdView.installHandler('onClickPostButtonComment', SwdPresenter.onClickPostButtonComment, '#post-button-comment', 'click');
                        SwdView.installHandler('onClickPostButtonLike', SwdPresenter.onClickPostButtonLike, '#post-button-like', 'click');
                        SwdView.installHandler('onClickPostButtonPm', SwdPresenter.onClickPostButtonPm, '#post-button-pm', 'click');
                        SwdView.installHandler('onClickPanelMessageUser', SwdPresenter.onClickPanelMessageUser, '#post-message-user', 'click');
                        SwdView.installHandler('onClickPostTile', SwdPresenter.onClickPostTile, '.post-tile > *', 'click');
                        SwdView.installHandler('onClickSelectGroup', SwdPresenter.onClickSelectGroup, '.selection-item.select-group', 'click');
                        SwdView.installHandler('onKeyUpCommentTextarea', SwdPresenter.onKeyUpCommentTextarea, '#post-comment-text > textarea', 'keyup')
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
     */
    facebookPageInfoPoll: function() {
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

                // Update floating panel height
                if (scrollTop > offsetTop) {
                    height = clientHeight - 10;
                }
                else {
                    height = clientHeight - offsetTop - 10;
                }

                SwdView.setFloatingPanelHeight(height);

                scrollPos = $('#app-content').height() - clientHeight;

                // Detect scroll at bottom 10% of page.
                if (scrollTop >= scrollPos * 0.9 && scrollPos >= 0) {
                    //alert(scrollTop + ' ' + $('#app-content').height() + ' ' + clientHeight);
                    SwdPresenter.loadPosts(true);
                }

                FB.Canvas.setSize({
                    height: Math.max($('html').height(), clientHeight)
                });
            }

            setTimeout(SwdPresenter.facebookPageInfoPoll, 100);
        });
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

        if (!SwdPresenter.currentlyLoading) {
            SwdPresenter.currentlyLoading = true;

            if (loadNextPage && SwdPresenter.oldestPost) {
                console.log('Loading next page.');
                updatedTime = SwdPresenter.oldestPost.updated_time;
            }
            else {
                console.log('Loading new content.');
                updatedTime = null;
                SwdView.clearPosts();
                SwdPresenter.resetFbCanvasSize();
                SwdView.toggleAjaxLoadingDiv('#post-feed', true);
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
        }
    },
    /***
     * Function to wrap up any kind of post loading.
     * @param {type} loadNextPage
     * @param {type} response
     */
    loadPostsComplete: function(loadNextPage, response) {
        if (!loadNextPage) {
            // Clear previous results, unless loading a new page.
            SwdPresenter.oldestPost = null;
        }

        if (response) {
            // If a response came through, then display the posts.
            SwdPresenter.oldestPost = response[response.length - 1];
            SwdView.populatePosts(response);
        }
        else
        if (!loadNextPage) {
            // Otherwise, clear the previous oldest post.
            SwdPresenter.oldestPost = null;
        }
    },
    /***
     * Refresh FB canvas size.
     */
    refreshFbCanvasSize: function() {
        FB.Canvas.getPageInfo(function(pageInfo) {
            SwdPresenter.clientHeight = parseInt(pageInfo.clientHeight);

            FB.Canvas.setSize({
                height: Math.max($('html').height(), SwdPresenter.clientHeight)
            });
        });
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
    onClickButtonGroups: function(e, args) {
        // Prevent the event from bubbling up the DOM and closing the floating panel.
        e.stopPropagation();

        SwdView.toggleFloatingPanel('#select-group-panel', true);
    },
    // Event Handlers (onX(e, args))
    onClickButtonNew: function(e, args) {
        // Prevent the event from bubbling up the DOM and closing the floating panel.
        e.stopPropagation();

        SwdView.toggleFloatingPanel('#new-post-panel', true);
    },
    onClickButtonRefresh: function(e, args) {
        SwdPresenter.loadPosts(false);
    },
    onClickHtml: function(e, args) {
        SwdView.closeAllUiMenus();
        SwdView.toggleFloatingPanel('.floating-panel', false);
    },
    onClickMenuButton: function(e, args) {
        SwdView.showUiMenu(e);
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

        // Show the ajax loading div.
        SwdView.toggleAjaxLoadingDiv('#post-comment-text', true);

        // Post the comment.
        SwdModel.postComment(id, comment, {
            success: function(response) {
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

        // Prevent the event from bubbling up the DOM and immediately causing the displayed panel to close.
        e.stopPropagation();

        SwdView.toggleAjaxLoadingDiv('#post-details-panel', true);
        SwdView.toggleFloatingPanel('#post-details-panel', true);
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
    onClickSelectGroup: function(e, args) {
        var i, id, group;

        id = $(e.currentTarget).attr('id');

        if (id === 'select-group-choose') {
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

            SwdView.toggleFloatingPanel('#select-group-panel', false);
        }
    },
    onKeyUpCommentTextarea: function(e, args) {
        var id, comment;

        if (e.which === 13 && !e.shiftKey) {
            e.preventDefault();

            id = $('#panel-post').data('id');
            comment = $('#post-comment-text > textarea').val();

            // Show the ajax loading div.
            SwdView.toggleAjaxLoadingDiv('#post-comment-text', true);

            // Post the comment.
            SwdModel.postComment(id, comment, {
                success: function(response) {
                    SwdView.addPostComment(response);
                    SwdView.clearPostCommentText();
                },
                fail: function(response) {
                    SwdView.showError(response);
                }
            });
        }

        return true;
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
    addGroupsToSelectPanel: function(groups) {
        var i = 0;

        $('#select-group-no-groups').hide();

        for (i = 0; i < groups.length; i++) {
            $('#select-group-list').append('<li id="' + groups[i].gid + '" class="selection-item select-group"><span class="ui-icon" style="background-image: url(' + groups[i].icon + ')"></span><div style="display: inline-block; margin-left: 5px">' + groups[i].name + '</div></li>');
        }

        $('.selection-item.select-group').button();
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

        commentDiv = $('<div class="post-comment ui-corner-all ui-widget ui-widget-content"><div class="ui-state-default"><div class="post-comment-user-image"></div><div class="post-comment-header"><p class="wrapper"><a class="post-comment-user-name" href="' + comment.user.profile_url + '" target="_blank">' + comment.user.first_name + ' ' + comment.user.last_name + '</a><span class="timestamp">' + timeStamp.calendar() + '</span></p></div></div><div class="ui-widget ui-widget-content">' + comment.text + '</div></div>');
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

        $('#button-groups').button({
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

        $('.floating-panel').click(function(e) {
            // Prevent floating panels from closing whenever they are clicked on.
            e.stopPropagation();
        });

        $('#post-message-user').button();

        // Init menus.
        $('#popup-menu-main').menu();

        // Fade out the div we are using to hide non-initted elements.
        //$('#overlay-app-loading').fadeOut('fast');
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
        SwdView.toggleAjaxLoadingDiv('#post-comment-text', false);
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
        var i, isEmpty, imageUrl, message, post, postTile, primaryContent, secondaryContent;

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
                    imageUrl = post.image_url[0];
                }
                else {
                    imageUrl = null;
                }

                postTile = $('<div id="' + post.post_id + '" class="post-tile ui-corner-all ui-widget ui-widget-content ui-state-default"><div class="post-tile-primary-content"></div><div class="post-tile-secondary-content"></div></div>').data('image_url', imageUrl);
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

            SwdView.toggleAjaxLoadingDiv('#post-feed', false);

            // Sleekly fade in the post tile elements.
            // From: http://www.paulirish.com/2008/sequentially-chain-your-callbacks-in-jquery-two-ways/
//            (function shownext(jq) {
//                jq.eq(0).fadeIn(60, function() {
//                    FB.Canvas.setSize({
//                        height: Math.max($('html').height(), SwdPresenter.clientHeight)
//                                //height: Math.max(clientHeight, 810)
//                    });
//                    (jq = jq.slice(1)).length && shownext(jq);
//                });
//            })($('div.post-tile'));
            $('div.post-tile').fadeIn(200, function() {
                // Refresh FB canvas when fade in completes.
                SwdPresenter.refreshFbCanvasSize();
            });

            // Associate the click event handler for newly created posts.
            $('.post-tile > *').click(SwdView.handlers['onClickPostTile']);
            $('.post-tile').hover(function() {
                $(this).removeClass('ui-state-default').addClass('ui-state-hover');
            }, function() {
                $(this).removeClass('ui-state-hover').addClass('ui-state-default');
            });

            SwdPresenter.currentlyLoading = false;
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
        $('#main-toolbar, .floating-panel').animate({
            top: Math.max(offset, 0)
        }, 100);
    },
    /***
     * Dynamically calculate the height of all floating panels, based on how large the FB canvas is.
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
        $('#button-groups span').text(text);
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
        SwdView.toggleAjaxLoadingDiv('#post-details-panel', false);
    },
    /***
     * Shows a Jquery UI menu.
     * @param {type} e
     */
    showUiMenu: function(e) {
        var menu;
        e.stopPropagation();
        menu = $(e.currentTarget).find('a').attr('href');

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
    },
    /***
     * Show or hide an ajax loading div element.
     * @param {type} parent
     * @param {type} show
     */
    toggleAjaxLoadingDiv: function(parent, show) {
        if (show) {
            $(parent + ' .ajax-loading-div').fadeIn(100);
        }
        else {
            $(parent + ' .ajax-loading-div').fadeOut(100);
        }
    },
    /***
     * Shows or hides a 'floating panel'
     * @param {type} id
     * @param {type} show
     */
    toggleFloatingPanel: function(id, show) {
        if (show) {
            // Make the panel modal by summoning a ui-widget-overlay.
            //$('<div class="ui-widget-overlay ui-widget-front"></div>').hide().appendTo('body').fadeIn();
            //$(id).fadeIn(150);
            $('<div class="ui-widget-overlay ui-widget-front"></div>').hide().appendTo('body').show();
            $(id).show();
//            $(id).show('slide', {
//                easing: 'easeInOutQuint',
//                direction: 'down'
//            }, 300);
        }
        else {
            $(id).hide();
            $('div.ui-widget-overlay').remove();
//            $(id).hide('slide', {
//                easing: 'easeInOutQuint',
//                direction: 'down'
//            }, 300, function() {
//                $('div.ui-widget-overlay').remove();
//            });
        }
    }
};
$(document).ready(function() {
    $.ajaxSetup({
        cache: true
    });
    SwdPresenter.init();
});
