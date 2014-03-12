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
//var AppUrl = 'http://bit.ly/1kq93Xb';
//var AppUrlFull = 'https://apps.facebook.com/1401018793479333'

// Test AppId
var AppId = '652991661414427';
var AppUrl = 'http://bit.ly/1aXsWl3';
var AppUrlFull = 'https://apps.facebook.com/652991661414427';

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
     * Delete a post from a group's feed.
     * @param {type} postId
     * @param {type} callbacks
     */
    deletePost: function(postId, callbacks) {
        $.ajax({
            type: 'GET',
            url: '/php/delete-post.php?postId=' + postId,
            success: function(response) {
                callbacks.success.call(SwdModel, JSON.parse(response));
            },
            error: function(response) {
                callbacks.error.call(SwdModel, response);
            }
        });
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
            error: function(response) {
                callbacks.error.call(SwdModel, response);
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
            error: function(response) {
                callbacks.error.call(SwdModel, response);
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
            error: function(response) {
                callbacks.error.call(SwdModel, response);
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
            error: function(response) {
                callbacks.error.call(SwdModel, response);
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
            error: function(response) {
                callbacks.error.call(SwdModel, response);
            }
        });
    },
    /***
     * Like a post.
     * @param {type} postId
     * @param {type} userLikes
     * @param {type} callbacks
     */
    likePost: function(postId, userLikes, callbacks) {
        $.ajax({
            type: 'POST',
            url: '/php/like-post.php',
            dataType: 'json',
            data: {
                'postId': postId,
                'userLikes': userLikes
            },
            success: function(response) {
                callbacks.success.call(SwdModel, response);
            },
            error: function(response) {
                callbacks.error.call(SwdModel, response);
            }
        });
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
            error: function(response) {
                callbacks.fail.call(SwdModel, response);
            }
        });
    },
    /***
     * Remove a group from the selected groups list.
     * @param {type} gid
     */
    removeGroup: function(gid) {
        
    },
    /***
     * Restores all groups to the selected groups list.
     */
    restoreAllGroups: function() {
        
    },
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
    selectedPost: null,
    /***
     * Confirm Facebook Login Status.
     * @param {type} callback
     */
    checkFBLoginStatus: function(callback) {
        FB.getLoginStatus(function(response) {
            // Check connection status, posting a login prompt if the user has disconnected.
            // TODO: Replace with something better.
            if (response.status !== 'connected') {
                SwdView.showMessage('Sorry, but your session has expired - automatically taking you back to the main page.');

                // Send the user to the app's main url.
                window.location = window.location.href;
                window.location.reload();

//                FB.login(function(response) {
//                    if (response.status === 'connected') {
//                        callback.call(SwdPresenter);
//                    }
//                }, {
//                    scope: 'user_groups,user_likes,publish_stream,read_stream'
//                });
            }
            else {
                callback.call(SwdPresenter);
            }
        });
    },
    /***
     * Top-level error handler function.
     */
    handleError: function(error) {
        switch (error.status) {
            case 401:
                // Access denied, most likely from an expired access token.
                // Get a new access token.
                // For now, simply refresh the page.
                SwdView.showMessage('Sorry, but your session has expired - automatically taking you back to the main page.');

                // Send the user to the app's main url.
                window.location = AppUrl;
                break;
            default:
                SwdView.showError(error.responseText);
        }
    },
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
                        scope: 'user_groups,user_likes,publish_stream,read_stream'
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

                    // Call the polling function again after 100ms.
                    SwdPresenter.facebookPageInfoPoll();

                    // Find groups that have been marked as 'BST'
                    for (i = 0; i < SwdPresenter.groups.length; i++) {
                        if (SwdPresenter.groups[i].marked) {
                            selectedGroups.push(SwdPresenter.groups[i]);
                        }
                    }

                    if (SwdPresenter.groups) {
                        //SwdPresenter.setSelectedGroup(selectedGroups[0]);
                        SwdView.addGroupsToSelectPanel(selectedGroups);

                        // Install Event Handlers
                        SwdView.installHandler('onClickButtonGroups', SwdPresenter.onClickButtonGroups, '#button-groups', 'click');
                        SwdView.installHandler('onClickButtonNew', SwdPresenter.onClickButtonNew, '#button-new', 'click');
                        SwdView.installHandler('onClickButtonRefresh', SwdPresenter.onClickButtonRefresh, '#button-refresh', 'click');
                        SwdView.installHandler('onClickFloatingPanelCloseButton', SwdPresenter.onClickFloatingPanelCloseButton, '.floating-panel-content > .close-button', 'click');
                        SwdView.installHandler('onClickFloatingPanelContent', SwdPresenter.onClickFloatingPanelContent, '.floating-panel-content', 'click');
                        SwdView.installHandler('onClickHtml', SwdPresenter.onClickHtml, 'html', 'click');
                        SwdView.installHandler('onClickMenuButton', SwdPresenter.onClickMenuButton, '.menu-button', 'click');
                        SwdView.installHandler('onClickNavButton', SwdPresenter.onClickNavButton, '.nav-button', 'click');
                        SwdView.installHandler('onClickPopupComment', SwdPresenter.onClickPopupComment, '#popup-comment', 'click');
                        SwdView.installHandler('onClickPostButtonDelete', SwdPresenter.onClickPostButtonDelete, '#post-button-delete', 'click');
                        SwdView.installHandler('onClickPostButtonLike', SwdPresenter.onClickPostButtonLike, '#post-button-like', 'click');
                        SwdView.installHandler('onClickPostButtonPm', SwdPresenter.onClickPostButtonPm, '#post-button-pm', 'click');
                        SwdView.installHandler('onClickPostBlock', SwdPresenter.onClickPostBlock, '.post-block', 'click');
                        SwdView.installHandler('onClickPostBlockLoadMore', SwdPresenter.onClickPostBlockLoadMore, '.post-block.load-more', 'click');
                        SwdView.installHandler('onClickPostImage', SwdPresenter.onClickPostImage, '#post-image', 'click');
                        SwdView.installHandler('onClickSelectGroup', SwdPresenter.onClickSelectGroup, '.selection-item.select-group', 'click');
                        SwdView.installHandler('onClickGroupClose', SwdPresenter.onClickGroupClose, '.group-selection-item > .close-button', 'click');
                        SwdView.installHandler('onClickRestoreGroupSelectionItems', SwdPresenter.onClickRestoreGroupSelectionItems, '#restore-group-selection-items', 'click');
                        SwdView.installHandler('onClickToolbar', SwdPresenter.onClickToolbar, '.toolbar', 'click');
                        SwdView.installHandler('onKeyUpCommentTextarea', SwdPresenter.onKeyUpCommentTextarea, '#popup-comment-text', 'keyup')
                        SwdView.installHandler('onWindowResize', SwdPresenter.onWindowResize, window, 'resize');
                        SwdView.positionMenus();

                        // Sleep for 1 second, allowing facebookPageInfoPoll() to complete for the first time.
                        setTimeout(function() {
                            SwdView.toggleAjaxLoadingDiv('body', false);

                            // Set the main ajax overlay to be semi-transparent.
                            SwdView.setMainOverlayTransparency();

                            // Start with displaying the group selection panel.
                            SwdView.toggleFloatingPanel('#select-group-panel', true);
                        }, 1000);
                    }
                    else {
                        // Have the view prompt the user to edit BST groups.
                    }
                },
                error: SwdPresenter.handleError
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

                // Update floating panel height, allowing for height of toolbar.
                if (scrollTop > offsetTop) {
                    height = clientHeight - 10 - 42;
                }
                else {
                    height = clientHeight - offsetTop - 10 - 42;
                }

                SwdView.setFloatingPanelHeight(height);

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
            error: SwdPresenter.handleError
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
            error: SwdPresenter.handleError
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
            error: SwdPresenter.handleError
        });
    },
    /***
     * High level post loading function.
     * @param {type} loadNextPage
     */
    loadPosts: function(loadNextPage) {
        var updatedTime;

        // Before calling anything, confirm login status.
        SwdPresenter.checkFBLoginStatus(function() {
            if (!SwdPresenter.currentlyLoading) {
                SwdPresenter.currentlyLoading = true;

                if (loadNextPage && SwdPresenter.oldestPost) {
                    updatedTime = SwdPresenter.oldestPost.updated_time;
                }
                else {
                    updatedTime = null;
                    SwdView.clearPosts();
                    SwdPresenter.resetFbCanvasSize();
                    SwdView.toggleAjaxLoadingDiv('body', true);
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
        });
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
            SwdView.populatePostBlocks(response, SwdPresenter.postType);
        }
        else
        if (!loadNextPage) {
            // Otherwise, clear the previous oldest post.
            SwdPresenter.oldestPost = null;
        }
    },
    /***
     * Reset Facebook Canvas Size to default value of 800
     */
    resetFbCanvasSize: function() {
//        FB.Canvas.setSize({
//            height: 810
//        });
        FB.Canvas.getPageInfo(function(pageInfo) {
            SwdPresenter.clientHeight = parseInt(pageInfo.clientHeight);

            FB.Canvas.setSize({
                height: Math.max($('html').height(), SwdPresenter.clientHeight)
            });
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
        SwdPresenter.postType = PostType.group;
        SwdPresenter.loadPosts(false);
        SwdView.setGroupButtonText(group.name);
        SwdView.setSelectedPostType('button-nav-group');
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
    onClickFloatingPanelCloseButton: function(e, args) {
        SwdView.toggleFloatingPanel('.floating-panel', false);
        SwdView.toggleToolbar('', false);
    },
    onClickFloatingPanelContent: function(e, args) {
        SwdView.closeAllUiMenus();
    },
    onClickHtml: function(e, args) {
        SwdView.closeAllUiMenus();
        SwdView.toggleFloatingPanel('.floating-panel', false);
        SwdView.toggleToolbar('', false);
    },
    onClickLogout: function(e, args) {
        // User selected 'logout' from the settings menu.
        // Take them back to their main Facebook page.
        window.location = "www.facebook.com";
    },
    onClickMenuButton: function(e, args) {
        SwdView.showUiMenu(e);
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
        }

        SwdPresenter.loadPosts(false);
        SwdView.setSelectedPostType(id);
    },
    onClickPopupComment: function(e, args) {
        e.stopPropagation();
    },
    onClickPostButtonDelete: function(e, args) {
        if (SwdView.showConfirmation('Are you sure you want to delete this post? You won\'t be able to get it back.')) {
            SwdView.toggleAjaxLoadingDiv('#post-details-panel', true);

            // Delete the post and then remove it from the feed.
            SwdModel.deletePost(SwdPresenter.selectedPost.post_id, {
                success: function(response) {
                    SwdView.toggleAjaxLoadingDiv('#post-details-panel', false);
                    SwdView.toggleFloatingPanel('#post-details-panel', false);

                },
                fail: SwdPresenter.handleError
            });
        }
    },
    onClickPostButtonLike: function(e, args) {
        var id, userLikes;

        id = SwdPresenter.selectedPost.post_id;
        userLikes = !SwdPresenter.selectedPost.like_info.user_likes;

        SwdView.setLikePost(userLikes);

        // Before calling anything, confirm login status.
        SwdPresenter.checkFBLoginStatus(function() {
            // Post the comment.
            SwdModel.likePost(id, userLikes, {
                success: function(response) {
                    //SwdView.setLikePost(response);
                },
                error: SwdPresenter.handleError
            });
        });
    },
    onClickPostButtonPm: function(e, args) {
        var id;

        id = SwdPresenter.selectedPost.actor_id;

        // Yes, shamelessly plug the app.
        SwdPresenter.sendFacebookMessage(id, AppUrl);
    },
    onClickPostBlock: function(e, args) {
        var id;
        var post;

        // Close any open menus.
        SwdView.closeAllUiMenus();

        // Assuming one of the child elements of post-block was clicked.
        id = $(e.currentTarget).parents('div.post-block').attr('id');

        if (!id) {
            id = $(e.currentTarget).attr('id');
        }

        // Prevent the event from bubbling up the DOM and immediately causing the displayed panel to close.
        e.stopPropagation();

        // Before calling anything, confirm login status.
        SwdPresenter.checkFBLoginStatus(function() {
            SwdView.toggleAjaxLoadingDiv('#post-details-panel', true);
            SwdView.toggleFloatingPanel('#post-details-panel', true);
            SwdView.toggleToolbar('#post-details-toolbar', true);

            SwdModel.getPostDetails(id, {
                success: function(response) {
                    post = response;

                    // Try to retrieve image URL from object.
                    //post['image_url'] = $('#' + id).data('image_url');

                    if (post) {
                        SwdPresenter.selectedPost = post;
                        SwdView.setLikePost(false);
                        SwdView.showPostDetails(post);
                    }
                    else {
                        // TODO: Do a real error message.
                        SwdPresenter.selectedPost = null;
                        alert('Unable to display post. It was most likely deleted.');
                    }
                },
                error: SwdPresenter.handleError
            });
        });
    },
    onClickPostBlockLoadMore: function(e, args) {
        SwdView.toggleAjaxLoadingDiv('.post-block.load-more', true);
        SwdPresenter.loadPosts(true);
    },
    onClickPostImage: function(e, args) {
        SwdView.showImageViewer(SwdPresenter.selectedPost);
    },
    onClickSelectGroup: function(e, args) {
        var i, id, group;

        id = $(e.currentTarget).attr('id');

        for (i = 0; i < SwdPresenter.groups.length; i++) {
            if (id === SwdPresenter.groups[i].gid) {
                group = SwdPresenter.groups[i];
                break;
            }
        }

        // Set selected group and load its feed.
        SwdPresenter.setSelectedGroup(group);

        SwdView.toggleFloatingPanel('#select-group-panel', false);
    },
    onClickGroupClose: function(e, args) {
        var groupTile, target, gid;

        e.stopPropagation();

        target = $(e.currentTarget);

        groupTile = $(target).parent('.group-selection-item');
        
        gid = $(groupTile).attr('id');
        
        // Remove the item from the back end.
        SwdModel.removeGroup(gid);

        // Remove the item from view.
        SwdView.hideGroupFromSelectPanel(groupTile);
    },
    onClickRestoreGroupSelectionItems: function(e, args) {
        // Restore all group selection items.
        SwdModel.restoreAllGroups();
        SwdView.showAllGroupSelectionItems();
    },
    onClickToolbar: function(e, args) {
        e.stopPropagation();

        SwdView.closeAllUiMenus();
    },
    onKeyUpCommentTextarea: function(e, args) {
        var id, comment;

        if (e.which === 13 && !e.shiftKey) {
            e.preventDefault();

            id = SwdPresenter.selectedPost.post_id;
            comment = $('#popup-comment-text').val();

            // Before calling anything, confirm login status.
            SwdPresenter.checkFBLoginStatus(function() {
                // Show the ajax loading div.
                SwdView.toggleAjaxLoadingDiv('#popup-comment', true);

                // Post the comment.
                SwdModel.postComment(id, comment, {
                    success: function(response) {
                        SwdView.addPostComment(response);
                        SwdView.clearPostCommentText();
                    },
                    error: SwdPresenter.handleError
                });
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
            $('#select-group-list').append('<div id="' + groups[i].gid + '" class="button group-selection-item selection-item select-group"><div class="close-button"></div><div class="selection-item-content"><span class="button-icon" style="background-image: url(' + groups[i].icon + ')"></span><div>' + groups[i].name + '</div></div></div>');
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

        // Hide the 'no comments' div.
        if ($('#post-nocomments').is(':visible')) {
            $('#post-nocomments').hide();
        }

        // Get a human-readable version of the comment's timestamp value.
        timeStamp = new moment(new Date(comment.time * 1000));

        commentDiv = $('<div class="post-comment"><div><a href="' + comment.user.profile_url + '" target="_blank">' + comment.user.first_name + ' ' + comment.user.last_name + '</a><div class="timestamp">' + timeStamp.calendar() + '</div></div><div class="post-comment-text">' + comment.text + '</div></div>');
        $(commentDiv).hide().prependTo('#post-comment-list').fadeIn();      // .prependTo to place newest on top.
    },
    /**
     * Init function for SwdView.
     */
    initView: function() {
        // TODO: Install an event handler to decouple this from the view.
        $('.floating-panel-content').click(function(e) {
            // Prevent floating panels from closing whenever they are clicked on.
            e.stopPropagation();
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
     * Clear all posts from the view.
     */
    clearPosts: function() {
        //$('#post-feed .post-tile').remove();
        $('#post-feed .post-block').remove();
    },
    /***
     * Clear comment box.
     */
    clearPostCommentText: function() {
        $('#popup-comment-text').val('');
        $('#popup-comment').hide();
        SwdView.toggleAjaxLoadingDiv('#popup-comment', false);
    },
    /***
     * Closes all Jquery UI menus.
     */
    closeAllUiMenus: function() {
        $('.menu').hide();
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
     * Remove a post block from the view.
     * @param {type} id
     */
    removePost: function(id) {
        $(id).fadeOut(function() {
            $(this).remove();
        });
    },
    /***
     * Remove a group from the group selection panel.
     * @param {type} id
     */
    hideGroupFromSelectPanel: function(id) {
        $(id).fadeOut();
    },
    /***
     * Show all group selection items.
     */
    showAllGroupSelectionItems: function() {
        $('.group-selection-item').fadeIn();
    },
    /***
     * Simulate the placing of fixed divs within the FB app canvas.
     * @param {type} offset
     */
    setFixedDivs: function(offset) {
        $('#left-rail').animate({
            top: Math.max(offset + 43, 0)
        }, 100);

        $('.toolbar').animate({
            top: Math.max(offset, 0)
        }, 100);

        $('.floating-panel').animate({
            top: Math.max(offset + 47, 47)
        }, 100);
    },
    /***
     * Calculate the height of all floating panels, based on how large the FB canvas is.
     * @param {type} height
     */
    setFloatingPanelHeight: function(height) {
        $('.floating-panel').height(height);
    },
    /***
     * Changes the text shown in the "Select a Group" button.
     * @param {type} text Text to display inside the button.
     */
    setGroupButtonText: function(text, postCount) {
        if (postCount) {
            $('#button-groups').text(text + ' (' + postCount + ')');
        }
        else {
            $('#button-groups').text(text);
        }

    },
    /***
     * Set selected post type.
     * @param {type} id
     */
    setSelectedPostType: function(id) {
        $('.nav-button').removeClass('selected-nav');
        $('#' + id).addClass('selected-nav');
    },
    /***
     * Create and display an image type post block.
     * @param {type} post
     */
    createImagePostBlock: function(post) {
        var postBlock;

        postBlock = $('<div id="' + post.post_id + '" class="post-block ui-widget"></div>');
        $(postBlock).addClass('post-block-image');
        $(postBlock).css('background-image', 'url("' + post.image_url[0] + '")');
        $(postBlock).appendTo('#post-feed');
    },
    /***
     * Create and display a text type post block.
     * @param {type} post
     */
    createTextPostBlock: function(post) {
        var postBlock, message;

        postBlock = $('<div id="' + post.post_id + '" class="post-block ui-widget"></div>');

        $(postBlock).addClass('post-block-text');

        message = '<div><p>' + post.message + '</p></div>';

        $(postBlock).html(message).appendTo('#post-feed');
    },
    /***
     * Create and display a link type post block.
     * @param {type} post
     */
    createLinkPostBlock: function(post) {
        var postBlock, description;

        postBlock = $('<div id="' + post.post_id + '" class="post-block ui-widget"></div>');
        $(postBlock).addClass('post-block-link');

        description = '<div><p><span class="link-title">' + post.link_data.name + '</span><br/>' + post.link_data.description + '</p></div>';

        $(postBlock).html(description).appendTo('#post-feed');
    },
    /***
     * Create and display a textlink type post block.
     * @param {type} post
     */
    createTextLinkPostBlock: function(post) {
        var postBlock, message;

        postBlock = $('<div id="' + post.post_id + '" class="post-block ui-widget"></div>');
        $(postBlock).addClass('post-block-textlink');

        message = '<div><p>' + post.message + '</p></div>';

        $(postBlock).html(message).appendTo('#post-feed');
    },
    /***
     * Populate the main view with post blocks.
     * @param {type} posts
     */
    populatePostBlocks: function(posts, postType) {
        var i, post, groupText;

        SwdView.toggleAjaxLoadingDiv('body', false);
        SwdView.toggleAjaxLoadingDiv('.post-block.load-more', false);

        // If there is a feed to display, then display it.
        if (posts && posts.length > 0) {
            $('#post-feed-noposts').hide();
            SwdView.setGroupButtonText(SwdPresenter.selectedGroup.name, posts.length);

            // Remove any existing 'Load more...' tiles.
            $('.post-block.load-more').remove();

            for (i = 0; i < posts.length; i++) {
                post = posts[i];

                // Switch based on post_type
                switch (post.post_type) {
                    case 'image':
                        SwdView.createImagePostBlock(post);
                        break;
                    case 'text':
                        SwdView.createTextPostBlock(post);
                        break;
                    case 'link':
                        SwdView.createLinkPostBlock(post);
                        break;
                    case 'textlink':
                        SwdView.createTextLinkPostBlock(post);
                        break;
                }
            }

            // Associate the click event handler for newly created posts.
            $('.post-block').click(SwdView.handlers['onClickPostBlock']);

            // Show the "Load More..." block if the group's main feed is being displayed.
            if (postType === PostType.group) {
                // Add the 'Load More...' post block.
                $('<div class="button post-block load-more ui-widget"><div class="ajax-loading-div hidden"></div><div class="load-more-text">Load more...</div></div>').appendTo('#post-feed');

                // Add an event handler for when it is clicked on.
                $('.post-block.load-more').click(SwdView.handlers['onClickPostBlockLoadMore']);
            }

            // Additionally, set up some styling for when an image type post block is moused over.
            $('.post-block.post-block-image').hover(function() {
                $('#post-block-mask').show().position({
                    my: 'left top',
                    at: 'left top',
                    of: $(this)
                });
            }, function() {
                $('#post-block-mask').hide();
            });

            SwdPresenter.resetFbCanvasSize();
        }
        else {
//            $('#post-feed-noposts').show();
        }

        SwdPresenter.currentlyLoading = false;
    },
    /***
     * Sets the 'Like' or 'Unlike' button text.
     * @param {type} userLikes
     */
    setLikePost: function(userLikes) {
        if (userLikes) {
            $('#post-button-like span:nth-child(2)').text('Unlike');
        }
        else {
            $('#post-button-like span:nth-child(2)').text('Like');
        }
    },
    /***
     * Sets the main overlay to a semi-transparent state.
     */
    setMainOverlayTransparency: function() {
        // Set the main ajax overlay to be semi-transparent.
        $('#overlay-app-loading').addClass('semi-transparent');
    },
    /***
     * Displays a confirmation dialog (Yes/No) to the user.
     * Returns true for Yes, false for No.
     * @param {type} message
     * @returns {bool}
     */
    showConfirmation: function(message) {
        return confirm(message);
    },
    /***
     * Shows an image viewer for the given post, allowing the user to see photos in greater detail.
     * @param {type} post
     */
    showImageViewer: function(post) {
        alert(post.post_id);
    },
    /***
     * Displays an error message to the user.
     * @param {type} message
     */
    showError: function(message) {
        // TODO: Replace with a nice dialog box.
        alert(message);
    },
    /***
     * Displays an information message to the user.
     * @param {type} message
     */
    showMessage: function(message) {
        // TODO: Replace with a nice dialog box.
        alert(message);
    },
    /***
     * Shows the post details for the selected post.
     * @param {type} post Post to load into floating post details panel.
     */
    showPostDetails: function(post) {
        var userImage, postImage, i, timeStamp, linkData;

        // Display the 'Delete' button for owned posts. Otherwise, hide it.
        if (post.actor_id === SwdPresenter.uid) {
            $('.personal-button').fadeIn();
        }
        else {
            $('.personal-button').hide();
        }

        // Display user's data.
        if (post.user.pic_square) {
            userImage = 'url("' + post.user.pic_square + '")';
        }
        else {
            userImage = '';
        }

        // Get a nice, human readable version of the post's created_time timestamp.
        timeStamp = new moment(new Date(post.created_time * 1000));

        $('#post-details-user-data .facebook-user-photo').css('background-image', userImage);
        $('#post-details-user-data .facebook-user-name').text(post.user.first_name + ' ' + post.user.last_name).attr('href', post.user.profile_url);
        $('#post-details-user-data .timestamp').text(timeStamp.calendar());

        // Display the post's image, or the no-image placeholder.
        if (post.image_url && post.image_url.length > 0) {
            postImage = 'url("' + post.image_url[0] + '")';

            // Hide the no-image container and display the post's attached image.
            $('#post-image').show();
            $('#post-no-image-desc').hide();
            $('#post-image').css('background-image', postImage);
        }
        else {
            // Hide the image container.
            $('#post-image').hide();
            $('#post-no-image-desc').show();
        }

        // Display permalink
        $('.post-permalink').attr('href', post.permalink);

        // Display message content, or hide it if empty.
        if (post.message !== '') {
            $('#post-message').show();
            $('#post-message-text').html(post.message);
        } else {
            $('#post-message').hide();
        }

        // Set link data and display it.
        if (post.post_type === 'link' || post.post_type === 'textlink') {
            //linkData = '<div><p><a href="' + post.link_data.href + '" target="_blank" class="link-title">' + post.link_data.name + '</a></br>' + post.link_data.description + '</p></div>';
            //$('#post-message-linkdata').html(linkData).show();

            $('#linkdata-href').attr('href', post.link_data.href).text(post.link_data.name);
            $('#linkdata-caption').text(post.link_data.caption);

            if (post.link_data.media && post.link_data.media[0].src) {
                $('#linkdata-img').attr('src', post.link_data.media[0].src);
            }

            $('#linkdata-desc').html(post.link_data.description);
            $('#post-message-linkdata').show();
        }
        else {
            $('#post-message-linkdata').hide();
        }

        // Populate the comments section.
        $('#post-comment-list').empty();
        $('#post-nocomments').show();

        if (post.comments.length > 0) {
            for (i = 0; i < post.comments.length; i++) {
                SwdView.addPostComment(post.comments[i]);
            }
        }

        // Wrap stuff up.
        SwdView.setLikePost(post.like_info.user_likes);
        SwdView.clearPostCommentText();
        SwdView.toggleAjaxLoadingDiv('#post-details-panel', false);
    },
    /***
     * Toggles the display of a context sensitive one specified by 'id'.
     * @param {type} id
     * @param {type} show
     */
    toggleToolbar: function(id, show) {
        if (show) {
            $('#main-toolbar').hide();
            $(id).show();
        }
        else {
            $('.toolbar').hide();
            $('#main-toolbar').show();
        }

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
            //$(parent + ' .ajax-loading-div').fadeIn(100);
            $(parent + ' .ajax-loading-div').show();
        }
        else {
            //$(parent + ' .ajax-loading-div').fadeOut(100);
            $(parent + ' .ajax-loading-div').hide();
        }
    },
    /***
     * Shows or hides a 'floating panel'
     * @param {type} id
     * @param {type} show
     */
    toggleFloatingPanel: function(id, show) {
        if (show) {
            // Make the panel modal by summoning an overlay.
            $('#overlay').show();
            $(id).show("drop");
        }
        else {
            $('#overlay').hide();
            $(id).hide("drop");
        }
    }
};

$(document).ready(function() {
    $.ajaxSetup({
        cache: true
    });

    SwdPresenter.init();
});
