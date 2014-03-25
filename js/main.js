"use strict";
// "Enums"

// SelectedView Enum
var SelectedView = {
    group: 0,
    myposts: 1,
    liked: 2,
    search: 3
};

// Prod AppId
var AppId = '1401018793479333';

// Test AppId
//var AppId = '652991661414427';

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
                callbacks.success.call(SwdModel, response);
            },
            error: function(response) {
                callbacks.error.call(SwdModel, response);
            }
        });
    },
    /***
     * Get posts in the group that are liked.
     * 
     * @param {type} offset
     * @param {type} callbacks
     */
    getLikedPosts: function(offset, callbacks) {
        var url = '/php/liked-posts.php?offset=' + offset + '&limit=50';

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
    getHiddenGroups: function(callbacks) {
        $.ajax({
            type: 'GET',
            url: '/php/hidden-groups.php',
            success: function(response) {
                callbacks.success.call(SwdModel, response);
            },
            error: function(response) {
                callbacks.error.call(SwdModel, response);
            }
        });
    },
    /***
     * Get posts that are owned by the current user in the provided group. Go back 42 days.
     * 
     * @param {type} offset
     * @param {type} callbacks
     */
    getMyPosts: function(offset, callbacks) {
        var url = '/php/my-posts.php?offset=' + offset + '&limit=50';

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
     * @param {type} refresh
     * @param {type} offset
     * @param {type} callbacks Completed callback function.
     */
    getNewestPosts: function(gid, refresh, offset, callbacks) {
        var url = '/php/new-posts.php?gid=' + gid + '&refresh=' + (refresh | 0) + '&offset=' + offset + '&limit=25';

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
     * Remove a group from the selected groups list by saving it to the HiddenGroups DB table.
     * @param {type} uid
     * @param {type} gid
     * @param {type} callbacks
     */
    hideGroup: function(uid, gid, callbacks) {
        var url = '/php/hide-group.php?uid=' + uid + '&gid=' + gid;

        $.ajax({
            type: 'GET',
            url: url,
            success: function(response) {
                callbacks.success.call(SwdModel, response);
            },
            error: function(response) {
                callbacks.error.call(SwdModel, response);
            }
        });
    },
    /***
     * Refresh the cached FQL stream on the server.
     * @param {type} gid
     * @param {type} callbacks
     */
    refreshStream: function(gid, callbacks) {
        var url = '/php/refresh-stream.php?gid=' + gid;

        $.ajax({
            type: 'GET',
            url: url,
            success: function(response) {
                callbacks.success.call(SwdModel, response);
            },
            error: function(response) {
                callbacks.error.call(SwdModel, response);
            }
        });
    },
    /***
     * Restores all groups to the selected groups list.
     * @param {type} callbacks
     */
    restoreAllGroups: function(callbacks) {
        $.ajax({
            type: 'GET',
            url: '/php/restore-groups.php',
            success: function(response) {
                callbacks.success.call(SwdModel, response);
            },
            error: function(response) {
                callbacks.error.call(SwdModel, response);
            }
        });
    },
    /***
     * Searches within a group's post.
     * @param {type} search
     * @param {type} offset
     * @param {type} callbacks
     */
    searchPosts: function(search, offset, callbacks) {
        var url = '/php/search-posts.php?search=' + encodeURIComponent(search) + '&offset=' + offset + '&limit=25';

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
    }
};
/**
 * Presenter for the Swapper's Delight program.
 */
var SwdPresenter = {
    // Object variables and properties.
    selectedView: SelectedView.group,
    selectedGroup: null,
    groups: null,
    prevOffset: null,
    clientHeight: null,
    uid: null,
    currentlyLoading: false,
    selectedPost: null,
    search: null,
    refreshStream: null,
    postOffset: 0,
    /***
     * Confirm Facebook Login Status.
     * @param {type} callback
     */
    checkFBLoginStatus: function(callback) {
        FB.getLoginStatus(function(response) {
            // Check connection status, posting a login prompt if the user has disconnected.
            // TODO: This is stub function that doesn't appear to be able to do much.
            if (response.status !== 'connected') {
//                SwdView.showMessage('Sorry, but your session has expired - automatically taking you back to the main page.');
//
//                // Send the user to the app's main url.
//                window.location = window.location.href;
//                window.location.reload();

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
     * @param {type} error
     */
    handleError: function(error) {
        var message = JSON.parse(error.responseText);

        switch (error.status) {
            case 401:
                // Access denied, most likely from an expired access token.
                // Get a new access token by simply refreshing the page.
                SwdView.showMessage(message);

                // Send the user to the app's main url.
                window.location = window.location.href;
                break;
            default:
                SwdView.showError(message);
        }
    },
    /**
     * Entry point of program.
     */
    init: function() {
        // Initialize the view.
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
        if (!SwdPresenter.groups) {
            // Retrieve group info for logged in user.
            SwdModel.getGroupInfo({
                success: function(response) {
                    SwdPresenter.groups = response;

                    // Call the polling function again after 100ms.
                    SwdPresenter.facebookPageInfoPoll();

                    if (SwdPresenter.groups) {

                        // Retrieve the user's group preferences.
                        SwdModel.getHiddenGroups({
                            success: function(response) {
                                SwdView.addGroupsToSelectPanel(SwdPresenter.groups, response);

                                // Install Event Handlers
                                SwdView.installHandler('onClickButtonGroups', SwdPresenter.onClickButtonGroups, '#button-groups', 'click');
                                SwdView.installHandler('onClickButtonNew', SwdPresenter.onClickButtonNew, '#button-new', 'click');
                                SwdView.installHandler('onClickButtonRefresh', SwdPresenter.onClickButtonRefresh, '#button-refresh', 'click');
                                SwdView.installHandler('onClickFloatingPanelCloseButton', SwdPresenter.onClickFloatingPanelCloseButton, '.floating-panel-content > .close-button', 'click');
                                SwdView.installHandler('onClickFloatingPanelContent', SwdPresenter.onClickFloatingPanelContent, '.floating-panel-content', 'click');
                                SwdView.installHandler('onClickHtml', SwdPresenter.onClickHtml, 'html', 'click');
                                SwdView.installHandler('onClickLogout', SwdPresenter.onClickLogout, '#menu-item-logout', 'click');
                                SwdView.installHandler('onClickMenuButton', SwdPresenter.onClickMenuButton, '.menu-button', 'click');
                                SwdView.installHandler('onClickPermalink', SwdPresenter.onClickPermalink, '#post-button-permalink', 'click');
                                SwdView.installHandler('onClickNavButton', SwdPresenter.onClickNavButton, '.nav-button', 'click');
                                SwdView.installHandler('onClickPostButtonDelete', SwdPresenter.onClickPostButtonDelete, '#post-button-delete', 'click');
                                SwdView.installHandler('onClickPostButtonLike', SwdPresenter.onClickPostButtonLike, '#post-button-like', 'click');
                                SwdView.installHandler('onClickPostButtonPm', SwdPresenter.onClickPostButtonPm, '#post-button-pm', 'click');
                                SwdView.installHandler('onClickPostBlock', SwdPresenter.onClickPostBlock, '.post-block.ui-widget.unique', 'click');
                                SwdView.installHandler('onClickPostBlockLoadMore', SwdPresenter.onClickPostBlockLoadMore, '.post-block.load-more', 'click');
                                SwdView.installHandler('onClickPostImageTile', SwdPresenter.onClickPostImageTile, '.post-image-tile', 'click');
                                SwdView.installHandler('onClickSelectGroup', SwdPresenter.onClickSelectGroup, '.selection-item.select-group', 'click');
                                SwdView.installHandler('onClickGroupClose', SwdPresenter.onClickGroupClose, '.group-selection-item > .close-button', 'click');
                                SwdView.installHandler('onClickRestoreGroupSelectionItems', SwdPresenter.onClickRestoreGroupSelectionItems, '#restore-group-selection-items', 'click');
                                SwdView.installHandler('onClickToolbar', SwdPresenter.onClickToolbar, '.toolbar', 'click');
                                SwdView.installHandler('onKeyUpCommentTextarea', SwdPresenter.onKeyUpCommentTextarea, '#post-comment-text', 'keyup');
                                SwdView.installHandler('onKeyUpSearch', SwdPresenter.onKeyUpSearch, '#main-search', 'keyup');
                                SwdView.installHandler('onWindowResize', SwdPresenter.onWindowResize, window, 'resize');
                                SwdView.positionMenus();

                                // Sleep for 1 second, allowing facebookPageInfoPoll() to complete for the first time.
                                setTimeout(function() {
                                    SwdView.toggleAjaxLoadingDiv('#overlay-loading-posts', false);

                                    // Start with displaying the group selection panel.
                                    SwdView.toggleFloatingPanel('#select-group-panel', true, 'drop');
                                }, 1000);
                            },
                            error: SwdPresenter.handleError
                        });
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
            var scrollTop, offsetTop, clientHeight, offset, height;

            scrollTop = parseInt(pageInfo.scrollTop);
            offsetTop = parseInt(pageInfo.offsetTop);
            clientHeight = parseInt(pageInfo.clientHeight);

            SwdPresenter.clientHeight = clientHeight;

            // Calculate how far to offset things. (In other words, only really do anything if the user has scrolled.
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
                SwdView.setFloatingOverlayHeight(clientHeight);

                // Change FB canvas size.
                FB.Canvas.setSize({
                    height: Math.max($('html').height(), clientHeight)
                });
            }

            // Poll again in another 100 ms.
            setTimeout(SwdPresenter.facebookPageInfoPoll, 100);
        });
    },
    /***
     * Load posts liked by user.
     * 
     * @param {type} offset
     */
    loadLikedPosts: function(offset) {
        SwdModel.getLikedPosts(offset, {
            success: function(response) {
                SwdPresenter.loadPostsComplete(response);
            },
            error: SwdPresenter.handleError
        });
    },
    /***
     * Load posts owned by user.
     * 
     * @param {type} offset
     */
    loadMyPosts: function(offset) {
        SwdModel.getMyPosts(offset, {
            success: function(response) {
                SwdPresenter.loadPostsComplete(response);
            },
            error: SwdPresenter.handleError
        });
    },
    /***
     * Load feed for the current group.
     * @param {type} refresh
     */
    loadNewestPosts: function(refresh, offset) {
        // If there is already a timer function running, then clear it.
        clearInterval(SwdPresenter.refreshStream);

        // Get posts and then display them.
        SwdModel.getNewestPosts(SwdPresenter.selectedGroup.gid, refresh, offset, {
            success: function(response) {
                // Set a timer function to periodically refresh the server-side FQL stream.
                SwdPresenter.refreshStream = setInterval(function() {
                    SwdModel.refreshStream(SwdPresenter.selectedGroup.gid, {
                        success: function(response) {
                        },
                        error: SwdPresenter.handleError
                    });
                }, 600000);

                SwdPresenter.loadPostsComplete(response);
            },
            error: SwdPresenter.handleError
        });
    },
    /***
     * Load posts that match the given search term.
     * 
     * @param {type} offset
     */
    loadSearchPosts: function(offset) {
        SwdView.clearSelectedNav();
        SwdView.blurControl('#main-search');

        // Get posts and then display them.
        SwdModel.searchPosts(SwdPresenter.search, offset, {
            success: function(response) {
                SwdPresenter.loadPostsComplete(response);
            },
            error: SwdPresenter.handleError
        });
    },
    /***
     * High level post loading function.
     * @param {type} refresh
     * @param {type} viewChanged
     */
    loadPosts: function(refresh, viewChanged) {
        // Before calling anything, confirm login status.
        SwdPresenter.checkFBLoginStatus(function() {
            if (!SwdPresenter.currentlyLoading) {
                SwdPresenter.currentlyLoading = true;

                if (refresh || viewChanged) {
                    SwdView.clearPosts();
                    SwdPresenter.refreshFbCanvasSize();

                    // If the view changed or the page has refreshed, reset the post offset to 0.
                    SwdPresenter.postOffset = 0;
                }

                SwdView.toggleElement('#overlay-loading-posts', true);
                SwdView.toggleAjaxLoadingDiv('#overlay-loading-posts', true);
                
                switch (SwdPresenter.selectedView) {
                    case SelectedView.group:
                        SwdPresenter.loadNewestPosts(refresh, SwdPresenter.postOffset);
                        break;
                    case SelectedView.myposts:
                        SwdPresenter.loadMyPosts(SwdPresenter.postOffset);
                        break;
                    case SelectedView.liked:
                        SwdPresenter.loadLikedPosts(SwdPresenter.postOffset);
                        break;
                    case SelectedView.search:
                        SwdPresenter.loadSearchPosts(SwdPresenter.postOffset);
                        break;
                }
            }
        });
    },
    /***
     * Function to wrap up any kind of post loading.
     * @param {type} response
     */
    loadPostsComplete: function(response) {
        if (response) {
            // Update post offset.
            SwdPresenter.postOffset += response.length;

            // If a response came through, then display the posts.
            SwdView.populatePostBlocks(response);
        }
    },
    /***
     * Update Facebook Canvas Size to match the canvas's clientHeight or html height, whichever is greater.
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
     * Set currently selected group.
     * @param {type} group
     */
    setSelectedGroup: function(group) {
        SwdPresenter.selectedGroup = group;
        SwdPresenter.selectedView = SelectedView.group;
        SwdPresenter.loadPosts(true);
        SwdView.setGroupButtonText(group.name);
        SwdView.setSelectedView('button-nav-group');
    },
    onClickButtonGroups: function(e, args) {
        // Prevent the event from bubbling up the DOM and closing the floating panel.
        e.stopPropagation();

        SwdView.toggleFloatingPanel('#select-group-panel', true, 'drop');
    },
    // Event Handlers (onX(e, args))
    onClickButtonNew: function(e, args) {
        // Prevent the event from bubbling up the DOM and closing the floating panel.
        e.stopPropagation();

        SwdView.toggleFloatingPanel('#new-post-panel', true);
    },
    onClickButtonRefresh: function(e, args) {
        SwdPresenter.loadPosts(true);
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
    onClickPermalink: function(e, args) {
        window.open(SwdPresenter.selectedPost.permalink, '_blank');
    },
    onClickNavButton: function(e, args) {
        var id, prevView;

        id = $(e.currentTarget).attr('id');

        prevView = SwdPresenter.selectedView;

        switch (id) {
            case 'button-nav-group':
                SwdPresenter.selectedView = SelectedView.group;
                break;
            case 'button-nav-myposts':
                SwdPresenter.selectedView = SelectedView.myposts;
                break;
            case 'button-nav-liked':
                SwdPresenter.selectedView = SelectedView.liked;
                break;
        }

        // Signal for posts to be loaded.
        SwdPresenter.loadPosts(false, true);
        SwdView.setSelectedView(id);
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
                    SwdView.toggleToolbar('', false);
                    SwdView.removePost('#' + SwdPresenter.selectedPost.post_id);
                },
                fail: SwdPresenter.handleError
            });
        }
    },
    onClickPostButtonLike: function(e, args) {
        var id, userLikes;

        id = SwdPresenter.selectedPost.post_id;
        userLikes = Number(!SwdPresenter.selectedPost.like_info.user_likes);

        SwdView.setLikePost(userLikes);

        // Before calling anything, confirm login status.
        SwdPresenter.checkFBLoginStatus(function() {
            // Post the comment.
            SwdModel.likePost(id, userLikes, {
                success: function(response) {
                    SwdPresenter.selectedPost.like_info.user_likes = userLikes;
                },
                error: SwdPresenter.handleError
            });
        });
    },
    onClickPostButtonPm: function(e, args) {
        window.open('https://www.facebook.com/messages/' + SwdPresenter.selectedPost.actor_id);

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
        SwdPresenter.loadPosts(false);
    },
    onClickPostImageTile: function(e, args) {
        SwdView.toggleSelectedImage($(e.currentTarget))
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

        SwdView.toggleFloatingPanel('#select-group-panel', false, 'drop');
    },
    onClickGroupClose: function(e, args) {
        var groupTile, target, gid;

        e.stopPropagation();

        target = $(e.currentTarget);

        groupTile = $(target).parent('.group-selection-item');

        gid = $(groupTile).attr('id');

        // Remove the item from the back end.
        SwdModel.hideGroup(SwdPresenter.uid, gid, {
            success: function() {
                // Remove the item from view.
                SwdView.hideGroupFromSelectPanel(groupTile);
            },
            fail: SwdPresenter.handleError
        });
    },
    onClickRestoreGroupSelectionItems: function(e, args) {
        // Restore all group selection items.
        SwdModel.restoreAllGroups({
            success: function() {
                SwdView.showAllGroupSelectionItems();
            },
            error: SwdPresenter.handleError
        });
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
            comment = $('#post-comment-text').val();

            // Before calling anything, confirm login status.
            SwdPresenter.checkFBLoginStatus(function() {
                // Show the ajax loading div.
                SwdView.toggleAjaxLoadingDiv('#post-comment-wrapper', true);

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
    onKeyUpSearch: function(e, args) {
        if (e.which === 13) {
            e.preventDefault();

            SwdPresenter.selectedView = SelectedView.search;

            SwdPresenter.search = $('#main-search').val();
            SwdPresenter.loadPosts(false, true);
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
     * @param {type} groups
     * @param {type} hiddenGroups
     */
    addGroupsToSelectPanel: function(groups, hiddenGroups) {
        var i, groupItem;

        $('#select-group-no-groups').hide();

        for (i = 0; i < groups.length; i++) {
            groupItem = $('<div id="' + groups[i].gid + '" class="button group-selection-item selection-item select-group"><div class="close-button"></div><div class="selection-item-content"><span class="button-icon" style="background-image: url(' + groups[i].icon + ')"></span><div>' + groups[i].name + '</div></div></div>');

            // Look through the string containing hiddenGroup IDs. If there is a match, hide the group.
            if (hiddenGroups && hiddenGroups.indexOf(groups[i].gid) !== -1) {
                $(groupItem).hide();
            }

            $('#select-group-list').append(groupItem);
        }

        $('.selection-item').hover(function() {
            $(this).addClass('hover', 100);
        }, function() {
            $(this).removeClass('hover', 100);
        });
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

        $('.button, .button.menu-item, .button.toolbar-button, .selection-item').hover(function() {
            $(this).addClass('hover', 100);
        }, function() {
            $(this).removeClass('hover', 100);
        });

        $('.post-block.ad-div').hide();
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
    blurControl: function(id) {
        $(id).blur();
    },
    /***
     * Clear all posts from the view.
     */
    clearPosts: function() {
        $('.post-block.ad-div').hide();
        $('#post-feed .post-block').not('.post-block.ad-div').remove();
    },
    /***
     * Clear comment box.
     */
    clearPostCommentText: function() {
        $('#post-comment-text').val('');
        SwdView.toggleAjaxLoadingDiv('#post-comment-wrapper', false);
    },
    clearSelectedNav: function() {
        $('.nav-button').removeClass('selected-nav');
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
            SwdView.setGroupButtonText(SwdPresenter.selectedGroup.name, SwdView.getPostBlockCount());
        });
    },
    /***
     * Determine the number of visible tiles.
     */
    getPostBlockCount: function() {
        return $('.post-block.unique').length;
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
            top: Math.max(offset + 55, 0)
        }, 100);

        $('.toolbar, .floating-overlay').animate({
            top: Math.max(offset, 0)
        }, 100);

        $('.floating-panel').animate({
            top: Math.max(offset + 47, 47)
        }, 100);
    },
    /***
     * Calculate the height of all floating panels.
     * @param {type} height
     */
    setFloatingPanelHeight: function(height) {
        $('.floating-panel').height(height);
    },
    /***
     * Calculate the height of all floating overlays.
     * @param {type} height
     */
    setFloatingOverlayHeight: function(height) {
        $('.floating-overlay').height(height);
    },
    /***
     * Changes the text shown in the "Select a Group" button.
     * @param {type} text Text to display inside the button.
     * @param {type} postCount
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
    setSelectedView: function(id) {
        $('.nav-button').removeClass('selected-nav');
        $('#' + id).addClass('selected-nav');
    },
    /***
     * Create and display an image type post block.
     * @param {type} post
     */
    createImagePostBlock: function(post) {
        var postBlock, userImage, tileImage, timeStamp, message;

        userImage = 'url(' + post.user.pic + ')';
        tileImage = 'url(' + post.image_url[0] + ')';

        // Create the visible block.
        postBlock = $('<div id="' + post.post_id + '" class="post-block ui-widget unique"><div class="visible-content" style="background-image: ' + tileImage + '"></div></div>');
        //$(postBlock).addClass('post-block-image').css('background-image', 'url("' + post.image_url[0] + '")');

        $(postBlock).addClass('post-block-image');

        // Create the text block that resides below the visible post block.
        userImage = 'url(' + post.user.pic + ')';

        timeStamp = new moment(new Date(post.created_time * 1000));

        message = '<div class="wrapper hidden-content"><p class="content"><span class="user-image" style="background-image: ' + userImage + '"></span><span class="user-name">' + post.user.first_name + ' ' + post.user.last_name + '</span><span class="timestamp">' + timeStamp.calendar() + '</span>' + post.message + '</p></div>';

        $(postBlock).append('<div class="post-block post-block-text hidden-block">' + message + '</div>');

        $(postBlock).appendTo('#post-feed');
    },
    /***
     * Expands a selected post details image to fill its entire parent container.
     * @param {type} image
     */
    toggleSelectedImage: function(image) {
        if (!$(image).hasClass('expanded') && $('#post-image-container').children('.post-image-tile').length > 1) {
            $(image).addClass('expanded');

            // Hide all the other images.
            $('#post-image-container .post-image-tile').not(image).hide();
        }
        else {
            $(image).removeClass('expanded');

            // Show all the other images.
            $('#post-image-container .post-image-tile').not(image).show();
        }
    },
    /***
     * Create and display a text type post block.
     * @param {type} post
     */
    createTextPostBlock: function(post) {
        var postBlock, message, userImage, timeStamp;

        postBlock = $('<div id="' + post.post_id + '" class="post-block ui-widget unique"></div>');

        userImage = 'url(' + post.user.pic + ')';

        timeStamp = new moment(new Date(post.created_time * 1000));

        message = '<div class="visible-content wrapper"><p class="content"><span class="user-image" style="background-image: ' + userImage + '"></span><span class="user-name">' + post.user.first_name + ' ' + post.user.last_name + '</span><span class="timestamp">' + timeStamp.calendar() + '</span>' + post.message + '</p></div>';

        $(postBlock).addClass('post-block-text').html(message).appendTo('#post-feed');
    },
    /***
     * Fill the post-image-container with post-image-tiles.
     * @param {type} post
     */
    fillPostImageContainer: function(post) {
        var i, imageTile, imageUrl, tileWidth, tileHeight, colCount;

        switch (post.image_url.length) {
            case 1:
                colCount = 1;
                break;
            case 2:
                colCount = 2;
                break;
            default:
                colCount = 3;
                break;
        }

        // Get image tile width & height, assuming a max of 375 for height.
        // Try for a square first.
        // Subtract 6 * colCount - 1 from total width.
        tileWidth = ($('#post-image-container').width() - (10 * (colCount))) / post.image_url.length;
        tileHeight = Math.min(tileWidth, $('#post-image-container').height()) - 14;

        // Create at tile for each image.
        for (i = 1; i <= post.image_url.length; i++) {
            imageUrl = 'url(' + post.image_url[i - 1] + ')';
            imageTile = $('<div class="post-image-tile"></div>');

            $(imageTile).height(tileHeight).width(tileWidth).css('background-image', imageUrl).appendTo('#post-image-container');
        }

        // For only 1 image, set background-size to contain, rather than cover.
        if (post.image_url.length === 1) {
            $(imageTile).addClass('single');
        }

        // Set up mouse over effects and connect to event handlers.
        $('#post-image-container .post-image-tile').hoverIntent(function() {
            $(this).addClass('hover', 100);
        }, function() {
            $(this).removeClass('hover', 100);
        }).click(SwdView.handlers['onClickPostImageTile']);
    },
    /***
     * Create and display a link type post block.
     * @param {type} post
     */
    createLinkPostBlock: function(post) {
        var postBlock, description, userImage, timeStamp, message, linkImage;

        linkImage = 'url(' + post.link_data.media[0].src + ')';

        postBlock = $('<div id="' + post.post_id + '" class="post-block ui-widget unique"></div>');
        description = '<div class="visible-content wrapper"><p class="content"><span class="link-image" style="background-image: ' + linkImage + '"></span><span class="link-title">' + post.link_data.name + '</span><br/>' + post.link_data.description + '</p></div>';

        $(postBlock).addClass('post-block-link').html(description);

        // Create the text block that resides below the visible post block.
        userImage = 'url(' + post.user.pic + ')';

        timeStamp = new moment(new Date(post.created_time * 1000));

        message = '<div class="hidden-content wrapper"><p class="content"><span class="user-image" style="background-image: ' + userImage + '"></span><span class="user-name">' + post.user.first_name + ' ' + post.user.last_name + '</span><span class="timestamp">' + timeStamp.calendar() + '</span>' + post.message + '</p></div>';

        $(postBlock).append('<div class="post-block post-block-text hidden-block">' + message + '</div>');

        $(postBlock).appendTo('#post-feed');
    },
    /***
     * Create and display a textlink type post block.
     * @param {type} post
     */
    createTextLinkPostBlock: function(post) {
        var postBlock, message, userImage, timeStamp, description, linkImage;

        postBlock = $('<div id="' + post.post_id + '" class="post-block ui-widget unique"></div>');

        userImage = 'url(' + post.user.pic + ')';

        timeStamp = new moment(new Date(post.created_time * 1000));

        message = '<div class="visible-content wrapper"><p class="content"><span class="user-image" style="background-image: ' + userImage + '"></span><span class="user-name">' + post.user.first_name + ' ' + post.user.last_name + '</span><span class="timestamp">' + timeStamp.calendar() + '</span>' + post.message + '</p></div>';

        $(postBlock).addClass('post-block-textlink').html(message);

        linkImage = 'url(' + post.link_data.media[0].src + ')';

        description = '<span class="link-image" style="background-image: ' + linkImage + '"></span><span class="link-title">' + post.link_data.name + '</span><br/>' + post.link_data.description;

        // Create the link text block that resides below the visible block.
        $(postBlock).append('<div class="post-block post-block-link hidden-block"><div class="hidden-content wrapper"><p class="content">' + description + '</p></div></div>');

        $(postBlock).appendTo('#post-feed');
    },
    /***
     * Populate the main view with post blocks.
     * @param {type} posts
     */
    populatePostBlocks: function(posts) {
        var i, post, adSpread, terminatorReached;

        SwdView.toggleAjaxLoadingDiv('#overlay-loading-posts', false);
        SwdView.toggleElement('#overlay-loading-posts', false);
        SwdView.toggleAjaxLoadingDiv('.post-block.load-more', false);

        // If there is a feed to display, then display it.
        if (posts && posts.length > 0) {
            // Remove any existing 'Load more...' tiles.
            $('.post-block.load-more').remove();

            for (i = 0; i < posts.length; i++) {
                post = posts[i];

                if (post.post_id !== 'terminator') {
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
                else {
                    terminatorReached = true;
                }
            }

            // Associate the click event handler for newly created posts.
            $('.post-block').not('.post-block.ad-div').click(SwdView.handlers['onClickPostBlock']);

            // Show the "Load More..." block if the group's main feed is being displayed.
            // Add the 'Load More...' post block.
            if (!terminatorReached) {
                $('<div class="button post-block load-more ui-widget"><div class="ajax-loading-div hidden"></div><div class="load-more-text">Load more...</div></div>').appendTo('#post-feed');

                // Add an event handler for when it is clicked on.
                $('.post-block.load-more').click(SwdView.handlers['onClickPostBlockLoadMore']);
            }

            // Determine how far apart each ad-tile will be.
            adSpread = Math.max(Math.floor(SwdView.getPostBlockCount() / 4), 7);

            // Insert add tiles evenly throughout all the posts.
            for (i = 1; i <= 4; i++) {
                $('#ad-tile-' + i).insertAfter('#post-feed .post-block.unique:nth-child(' + i * adSpread + ')').show();
            }

            // After a delay, show the hidden content for any moused over image post blocks.
            // Use the hoverIntent plugin.
            $('.post-block').not('.post-block.post-block-text').hoverIntent({
                over: function() {
                    $(this).children('.visible-content').hide('blind', 300);
                },
                out: function() {
                    $(this).children('.visible-content').show('blind', 300);
                },
                timeout: 400
            });

            $('.post-block.unique.post-block-text, .post-block.load-more').hoverIntent(function() {
                $(this).addClass('hover', 100);
            }, function() {
                $(this).removeClass('hover', 100);
            });

            SwdPresenter.refreshFbCanvasSize();
        }
        else {
//            $('#post-feed-noposts').show();
        }

        // Display the official count.
        SwdView.setGroupButtonText(SwdPresenter.selectedGroup.name, SwdView.getPostBlockCount());

        SwdPresenter.currentlyLoading = false;
    },
    /***
     * Sets the 'Like' or 'Unlike' button text.
     * @param {type} userLikes
     */
    setLikePost: function(userLikes) {
        if (userLikes == true) {
            $('#post-button-like span:nth-child(2)').text('Unlike');
        }
        else {
            $('#post-button-like span:nth-child(2)').text('Like');
        }
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
        var userImage, postImage, i, timeStamp;

        // Display the 'Delete' button for owned posts. Otherwise, hide it.
        if (post.actor_id === SwdPresenter.uid) {
            $('.personal-button').fadeIn();
        }
        else {
            $('.personal-button').hide();
        }

        // Display user's data.
        if (post.user.pic) {
            userImage = 'url("' + post.user.pic + '")';
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
            $('#post-image-container').show().empty();
            $('#post-no-image-desc').hide();

            // File the image container with post-image-tiles.
            SwdView.fillPostImageContainer(post);
        }
        else {
            // Hide the image container.
            $('#post-image-container').hide();
            $('#post-no-image-desc').show();
        }

        // Display message content, or hide it if empty.
        if (post.message !== '') {
            $('#post-message').show();
            $('#post-message-text').html(post.message);
        } else {
            $('#post-message').hide();
        }

        // Set link data and display it.
        if (post.post_type === 'link' || post.post_type === 'textlink') {
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

        $('.post-permalink').attr('href', post.permalink);

        // Populate the comments section.
        $('#post-comment-list').empty();

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

        $('.personal-button').hide();
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
            // Before showing a loading-div, make sure no others are visible.
            if ($('.ajax-loading-div').filter(':visible').length === 0) {
                $(parent + ' .ajax-loading-div').show();
            }
        }
        else {
            $(parent + ' .ajax-loading-div').hide();
        }
    },
    /***
     * Shows or hides an element with the given selector.
     * @param {type} element
     * @param {type} show
     */
    toggleElement: function(element, show) {
        if (show) {
            $(element).show();
        }
        else {
            $(element).hide();
        }
    },
    /***
     * Shows or hides a 'floating panel'
     * @param {type} id
     * @param {type} show
     * @param {type} effect
     * @param {type} options
     */
    toggleFloatingPanel: function(id, show, effect, options) {
        if (!effect) {
            effect = 'drop';
        }

        if (!options) {
            options = {};
        }

        if (show) {
            // Make the panel modal by summoning an overlay.
            $('#overlay').show();
            $(id).show(effect, options, 400);
        }
        else {
            $('#overlay').hide();
            $(id).hide(effect, options, 400);
        }
    }
};

$(document).ready(function() {
    $.ajaxSetup({
        cache: true
    });

    SwdPresenter.init();
});
