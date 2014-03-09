<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js">
    <!--<![endif]-->
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title></title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- Place favicon.ico and apple-touch-icon.png in the root directory -->

        <!--        <link rel="stylesheet" href="css/flick-red/jquery-ui-1.10.4.custom.min.css">-->
        <link rel="stylesheet" href="css/normalize.css">
        <link rel="stylesheet" href="css/main.css">
        <script src="js/vendor/modernizr-2.6.2.min.js"></script>
    </head>
    <body>
        <!--[if lt IE 7]>
        <p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->

        <!-- Add your site or application content here -->
        <div id='app-content'>
            <div id='fb-root'></div>

            <div id='main-panel'>
                <div id='left-rail' class='ui-widget scroll-y'>
                    <div id='left-rail-nav'>
                        <div id='button-nav-group' class='button nav-button selected-nav'>
                            <span>Newest Posts</span>
                        </div>
                        <div id='button-nav-myposts' class='button nav-button'>
                            <span>My Posts</span>
                        </div>
                        <div id='button-nav-liked' class='button nav-button'>
                            <span>Liked Posts</span>
                        </div>
                        <!--                        <div id='button-nav-search' class='button nav-button'>
                                                    <span>Search</span>
                                                </div>-->
                    </div>
                    <div id='ad-space'>
                        Hey, I saw that! You're using something to block my ads, aren't you? Come on, admit it. Please do yourself a favor (and me) by by turning what you're using off for this site. Ads are what makes this program viable. Thank you!
                    </div>
                </div>
                <div id='post-feed' class='scroll-y'>
                    <div id='post-feed-noposts' class='hidden'>No posts were found.</div>
                </div>

                <!--Post Details Panel-->

                <div id='post-details-panel' class='floating-panel hidden'>
                    <div class='floating-panel-content ui-widget'>
                        <div class='close-button'></div>
                        <div id='details-left-column' class='panel-post-column scroll-y'>
                            <div id='post-details-user-data' class='facebook-user-header floating-panel-section'>
                                <div class='facebook-user-photo'></div>
                                <div class='facebook-poststamp'>
                                    <a class='facebook-user-name' target='_blank'></a>
                                    <span>-</span>
                                    <span class='timestamp'></span>
                                    <a target='_blank' class='post-permalink float-right wrappable-link'>Permalink</a>
                                </div>
                            </div>
                            <div id='post-image' class='floating-panel-section'></div>
                            <div id='post-message' class='floating-panel-section'>
                                <div id='post-message-text'></div>
                            </div>
                            <div id='post-message-linkdata' class='floating-panel-section'>
                                <div>
                                    <a id='linkdata-href' target='_blank' class='link-title'></a>
                                    <span id='linkdata-caption'></span>
                                </div>
                                <img id='linkdata-img'>
                                <span id='linkdata-desc'></span>
                            </div>
                            <div id='post-no-image-desc' class='floating-panel-section hidden'>
                                <span class='hint'>Hint: Sometimes posts have photos, but the owner of the post hasn't allowed apps like Swapper's Delight access to them. Click <a target='_blank' class='post-permalink wrappable-link'>here</a> to see the post directly in Facebook.</span>
                            </div>
                        </div>
                        <div id='details-right-column' class='panel-post-column scroll-y'>
                            <div id='post-comments' class='floating-panel-section'>
                                <div id='post-nocomments' class='italic'>
                                    No comments yet.
                                </div>
                                <div id='post-comment-list'></div>
                            </div>
                        </div>
                        <div class='ajax-loading-div hidden'></div>
                    </div>
                </div>

                <!--New Post Panel-->

                <div id='new-post-panel' class='floating-panel hidden'>
                    <div class='floating-panel-content ui-widget scroll-y'>
                        <div class='close-button'></div>
                    </div>
                </div>

                <!--Select Group panel-->

                <div id='select-group-panel' class='floating-panel hidden'>
                    <div class='floating-panel-content ui-widget scroll-y'>
                        <div class='close-button'></div>
                        <span class='heading'>Choose a Group to View</span>
                        <span class='hint'>Hint: Hide any groups that you don't want to see here. Don't worry, though. You can just click <a id='restore-group-selection-items' href='#'>here</a> to show them again.</span>
                        <div id='select-group-list' class='selection-list'>
                            <div id='select-group-no-groups' class='selection-item select-group'>
                                You're not a member of any Facebook groups.
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id='main-toolbar' class='ui-widget toolbar'>
                <div class='toolbar-group float-left'>
                    <div id='button-new' class='button toolbar-button'>
                        <span class='button-icon icon-new'></span>
                        <span>New Post</span>
                    </div>
                    <div id='button-refresh' class='button toolbar-button'>
                        <span class='button-icon icon-refresh'></span>
                        <span>Refresh</span>
                    </div>
                    <div id='button-menu-main' class='button menu-button'>
                        <span class='button-icon icon-menu'></span>
                        <a href='#popup-menu-main'></a>
                    </div>
                </div>
                <div class='toolbar-group float-right'>
                    <div id='button-groups' class='button toolbar-button'>
                        <span>Select a Group</span>
                    </div>
                </div>
                <div style='clear: both;'></div>
            </div>

            <div id='post-details-toolbar' class='ui-widget toolbar floating-panel-toolbar hidden'>
                <div class='toolbar-group float-left'>
                    <div id='post-button-comment' class='button menu-button'>
                        <span class='button-icon icon-comment'></span>
                        <span>Comment</span>
                        <a href='#popup-comment'></a>
                    </div>
                    <div id='post-button-like' class='button toolbar-button'>
                        <span class='button-icon icon-like'></span>
                        <span>Like</span>
                    </div>
                    <div id='post-button-pm' class='button toolbar-button'>
                        <span class='button-icon icon-pm'></span>
                        <span>Private Message</span>
                    </div>
                </div>
            </div>

            <!--Menus and Popups-->

            <div id='popup-menu-main' class='menu'>
                <div id='menu-item-logout' class='menu-item'>
                    <span>Logout</span>
                </div>
            </div>

            <div id='popup-comment' class='menu floating-panel-menu ui-widget'>
                <textarea id='popup-comment-text' placeholder='Type a comment and press [Enter] to post it.'></textarea>
                <div class='ajax-loading-div hidden'></div>
            </div>

            <!--Image Viewer Panel-->
            <div id='image-viewer' class='hidden'>

            </div>

        </div>

        <div id='overlay-app-loading' class='ajax-loading-div'></div>
        <div id='overlay' class='hidden'></div>
        <div id='post-block-mask' class='post-block ui-widget hidden'></div>

        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
        <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
        <script src="js/plugins.js"></script>
        <script src="js/main.js"></script>

        <!-- Google Analytics: change UA-XXXXX-X to be your site's ID. -->
        <script>
            (function(b, o, i, l, e, r) {
                b.GoogleAnalyticsObject = l;
                b[l] || (b[l] = function() {
                    (b[l].q = b[l].q || []).push(arguments)
                });
                b[l].l = +new Date;
                e = o.createElement(i);
                r = o.getElementsByTagName(i)[0];
                e.src = '//www.google-analytics.com/analytics.js';
                r.parentNode.insertBefore(e, r)
            }(window, document, 'script', 'ga'));
            ga('create', 'UA-XXXXX-X');
            ga('send', 'pageview');
        </script>
    </body>
</html>
