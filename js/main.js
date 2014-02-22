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
            fail: function(response) {
                callbacks.fail.call(SwdModel, response);
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
                        SwdView.installHandler('onClickNavButton', SwdPresenter.onClickNavButton, '.nav-button', 'click');
                        SwdView.installHandler('onClickPostButtonComment', SwdPresenter.onClickPostButtonComment, '#post-button-comment > div', 'click');
                        SwdView.installHandler('onClickPostButtonLike', SwdPresenter.onClickPostButtonLike, '#post-button-like', 'click');
                        SwdView.installHandler('onClickPostButtonPm', SwdPresenter.onClickPostButtonPm, '#post-button-pm', 'click');
                        //SwdView.installHandler('onClickPostTile', SwdPresenter.onClickPostTile, '.post-tile > *', 'click');
                        SwdView.installHandler('onClickPostBlock', SwdPresenter.onClickPostBlock, '.post-block', 'click');
                        SwdView.installHandler('onClickSelectGroup', SwdPresenter.onClickSelectGroup, '.selection-item.select-group', 'click');
                        SwdView.installHandler('onKeyUpCommentTextarea', SwdPresenter.onKeyUpCommentTextarea, '#post-comment-text > textarea', 'keyup')
                        SwdView.installHandler('onWindowResize', SwdPresenter.onWindowResize, window, 'resize');
                        SwdView.positionMenus();

                        // Set the main ajax overlay to be semi-transparent.
                        SwdView.setMainOverlayTransparency();
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

//                scrollPos = $('#app-content').height() - clientHeight;
//
//                // Detect scroll at bottom 10% of page.
//                if (scrollTop >= scrollPos * 0.9 && scrollPos >= 0) {
//                    //alert(scrollTop + ' ' + $('#app-content').height() + ' ' + clientHeight);
//                    SwdPresenter.loadPosts(true);
//                }

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
                updatedTime = SwdPresenter.oldestPost.updated_time;
            }
            else {
                updatedTime = null;
                SwdView.clearPosts();
                SwdPresenter.resetFbCanvasSize();
                //SwdView.toggleAjaxLoadingDiv('#post-feed', true);
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
            SwdView.populatePostBlocks(response);
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

            // Ensure a minimum height.
//            if ($('html').height() < SwdPresenter.clientHeight  * 1.5) {
//                $('html').height(SwdPresenter.clientHeight * 1.5);
//            }

            FB.Canvas.setSize({
                //height: Math.max($('html').height(), SwdPresenter.clientHeight)
                height: $('html').height()
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
    onClickHtml: function(e, args) {
        SwdView.closeAllUiMenus();
        SwdView.toggleFloatingPanel('.floating-panel', false);
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
    onClickPostButtonComment: function(e, args) {
        var id, comment;

        id = SwdPresenter.selectedPost.post_id;
        comment = $('#post-comment-text > textarea').val();

        // Show the ajax loading div.
        SwdView.toggleAjaxLoadingDiv('#post-comment-text', true);

        // Post the comment, if it's not empty.
        if (comment) {
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
    },
    onClickPostButtonLike: function(e, args) {
        var id, userLikes;

        id = SwdPresenter.selectedPost.post_id;
        userLikes = !SwdPresenter.selectedPost.like_info.user_likes;

        // Post the comment.
        SwdModel.likePost(id, userLikes, {
            success: function(response) {
                SwdView.setLikePost(response);
            },
            fail: function(response) {
                SwdView.showError(response);
            }
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

        // Assuming one of the child elements of post-block was clicked.
        id = $(e.currentTarget).parents('div.post-block').attr('id');

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
                //post['image_url'] = $('#' + id).data('image_url');

                if (post) {
                    SwdPresenter.selectedPost = post;
                    SwdView.showPostDetails(post);
                }
                else {
                    // TODO: Do a real error message.
                    SwdPresenter.selectedPost = null;
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

            id = SwdPresenter.selectedPost.post_id;
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
            $('#select-group-list').append('<div id="' + groups[i].gid + '" class="button selection-item select-group"><span class="button-icon" style="background-image: url(' + groups[i].icon + ')"></span><div style="display: inline-block; margin-left: 5px">' + groups[i].name + '</div></div>');
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

        //timeStamp = $.datepicker.formatDate('DD, mm/dd/yy at HH:MM', new Date(post.comments[i].time * 1000));
        timeStamp = new moment(new Date(comment.time * 1000));

//        commentDiv = $('<div class="post-comment"><div><div class="post-comment-user-image"></div><div class="post-comment-header"><p class="wrapper"><a class="post-comment-user-name" href="' + comment.user.profile_url + '" target="_blank">' + comment.user.first_name + ' ' + comment.user.last_name + '</a><span class="timestamp">' + timeStamp.calendar() + '</span></p></div></div><div>' + comment.text + '</div></div>');
//        $(commentDiv).find('.post-comment-user-image').css('background-image', userImage);
//        $(commentDiv).hide().prependTo('#post-comment-list').fadeIn();
        //$('#post-comment-list').append(commentDiv);
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
        $('#post-comment-text > textarea').val('');
        SwdView.toggleAjaxLoadingDiv('#post-comment-text', false);
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
     * Simulate the placing of fixed divs within the FB app canvas.
     * @param {type} offset
     */
    setFixedDivs: function(offset) {
        $('#left-rail').animate({
            top: Math.max(offset + 44, 0)
        }, 100);

        $('.toolbar, .floating-panel').animate({
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
        $('#button-groups').text(text);
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
     * Populate the main view with post blocks.
     * @param {type} posts
     */
    populatePostBlocks: function(posts) {
        var i, post, postBlock, message, color, colorArray;
        
        // Array of random colors to choose from.
        colorArray = [
            'cornsilk', 
            'azure', 
            'antiquewhite', 
            'cornflowerblue',
            'sandybrown',
            'mintcream'
        ];

        SwdView.toggleAjaxLoadingDiv('body', false);

        // If there is a feed to display, then display it.
        if (posts && posts.length > 0) {
            $('#post-feed-noposts').hide();
            
            // Remove any existing 'Load more...' tiles.
            $('.post-tile.load-more').remove();

            for (i = 0; i < posts.length; i++) {
                post = posts[i];

                postBlock = $('<div id="' + post.post_id + '" class="post-block ui-widget"></div>');
                
                message = '';
                
                if (post.image_url.length > 0) {
                    $(postBlock).addClass('post-block-image');
                    $(postBlock).css('background-image', 'url("' + post.image_url[0] + '")');
                    $(postBlock).appendTo('#post-feed');
                }
                else {
                    $(postBlock).addClass('post-block-text'); 
                    
                    color = colorArray[Math.floor(Math.random() * (colorArray.length))];
                    
                    message = '<div><p>' + post.message + '</p></div>';
                    
                    $(postBlock).css('background-color', color).html(message).appendTo('#post-feed');
                }
            }

            // Associate the click event handler for newly created posts.
            $('.post-block').click(SwdView.handlers['onClickPostBlock']);
            
            // Add the 'Load More...' post block.
            postBlock = $('<div class="post-block load-more ui-widget"><div class="load-more-text">Load more...</div></div>');
            
            $('#post-feed').append(postBlock);

            SwdPresenter.refreshFbCanvasSize();
        }
        else {
            $('#post-feed-noposts').show();
        }

        SwdPresenter.currentlyLoading = false;
    },
    /***
     * Sets the 'Like' or 'Unlike' button text.
     * @param {type} userLikes
     */
    setLikePost: function(userLikes) {
        if (userLikes) {
            $('#post-button-like').text('Unlike');
        }
        else {
            $('#post-button-like').text('Like');
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
        var userImage, postImage, i, timeStamp;

        // Display the post's image, or the no-image placeholder.
        if (post.image_url && post.image_url.length > 0) {
            postImage = 'url("' + post.image_url[0] + '")';

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

        timeStamp = new moment(new Date(post.created_time * 1000));

        $('#post-message-pic').css('background-image', userImage);
        $('#post-message-name').text(post.user.first_name + ' ' + post.user.last_name);
        $('#post-message-name').attr('href', post.user.profile_url);
        $('#post-message-header .timestamp').text(timeStamp.calendar());
        $('#post-message-text').html(post.message);
        $('#post-comment-list').empty();
        $('#post-nocomments').show();

        if (post.comments.length > 0) {
            for (i = 0; i < post.comments.length; i++) {
                SwdView.addPostComment(post.comments[i]);
            }
        }

        SwdView.setLikePost(post.like_info.user_likes);

        SwdView.clearPostCommentText();
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
            $(id).show();
        }
        else {
            $('#overlay').hide();
            $(id).hide();
        }
    }
};
$(document).ready(function() {
    $.ajaxSetup({
        cache: true
    });
    SwdPresenter.init();
});
