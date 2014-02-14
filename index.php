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

        <link rel="stylesheet" href="css/flick-red/jquery-ui-1.10.4.custom.min.css">
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
                <div id='left-rail' class='ui-widget ui-widget-content ui-corner-all scroll-y'>
                    <div id='left-rail-nav'>
                        <div id='button-nav-group' class='button-nav selected-nav'>
                            Newest
                        </div>
                        <div id='button-nav-myposts' class='button-nav'>
                            My Posts
                        </div>
                        <div id='button-nav-liked' class='button-nav'>
                            Liked
                        </div>
                        <div id='button-nav-search' class='button-nav'>
                            Search
                        </div>
                    </div>
                    <div id='ad-space'>
                        Hey, I saw that! You're using something to block my ads, aren't you? Come on, admit it. Please do yourself a favor (and me) by by turning what you're using off for this site. Ads are what makes this program viable. Thank you!
                    </div>
                </div>
                <div id='post-feed' class='scroll-y'>
                    <div class='ajax-loading-div hidden'></div>
                    <div id='post-feed-noposts'>No posts were found.</div>
                </div>
                <div id='post-details-panel' class='floating-panel hidden'>
                    <div id='panel-post' class='floating-panel-content ui-widget-content'>
                        <div id='panel-post-left' class='panel-post-column scroll-y'>
                            <div id='post-message-user' class='ui-state-default'>
                                <div id='post-message-pic'></div>
                                <div id='post-message-header'>
                                    <p class='wrapper'>
                                        <a id='post-message-name'></a>
                                        <span class='timestamp'>Today</span>
                                    </p>
                                </div>
                            </div>
                            <div id='post-image-container' class='ui-widget ui-widget-content ui-corner-all'>
                                <div id='post-no-image' class='hidden'>
                                    <!--                                <div></div>
                                                                    <span>This post does not have a photo, or the owner of this post has chosen not to give permission for applications like Swapper's Delight to view it. Click the link below to see the original Facebook posting.</span>-->
                                </div>
                                <div id='post-image'></div>
                            </div>
                            <div id='post-permalink' class='ui-widget ui-widget-content ui-corner-all'>
                                <a class='wrappable-link' target='_blank'></a>
                            </div>
                            <div id='post-message' class='ui-widget ui-widget-content ui-corner-all'>
                                <div id='post-message-text'></div>
                            </div>
                            <div id='post-button-bar'>
                                <div>
                                    <div id='post-button-pm' class='post-button'>
                                        Message
                                    </div>
                                    <div id='post-button-like' class='post-button'>
                                        Like
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id='panel-post-right' class='panel-post-column scroll-y'>
                            <div id='post-comment-text' class='ui-corner-all ui-widget ui-widget-content'>
                                <textarea placeholder='Add a comment...'></textarea>
                                <div class='ajax-loading-div hidden'></div>
                            </div>
                            <div id='post-button-comment'>
                                <div class='post-button'>
                                    Comment
                                </div>
                            </div>
                            <span id='post-comment-heading'>Viewing all comments, newest first.</span>
                            <div id='post-comments'>
                                <div id='post-nocomments'>
                                    No comments yet.
                                </div>
                                <div id='post-comment-list'>
                                </div>
                            </div>
                        </div>
                        <div class='ajax-loading-div hidden'></div>
                    </div>
                </div>
                <div id='new-post-panel' class='floating-panel hidden'>
                    <div class='floating-panel-content ui-widget-content scroll-y'></div>
                </div>
                <div id='select-group-panel' class='floating-panel narrow-floating-panel hidden'>
                    <div class='floating-panel-content ui-widget-content scroll-y'>
                        <ul id='select-group-list' class='selection-list'>
                            <li id='select-group-no-groups' class='selection-item select-group'>
                                <span class='ui-icon ui-icon-check'></span>You're not a member of any Facebook groups.
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div id='main-toolbar' class='ui-widget ui-widget-header'>
                <div class='float-left left-margin'>
                    <div id='button-groups' class='left-margin'>
                        Select a Group
                    </div>
                    <div id='button-new' class='left-margin'>
                        New
                    </div>
                    <div id='button-refresh' class='left-margin'>
                        Refresh
                    </div>
                </div>
                <div class='float-right right-margin'>
                    <div id='button-menu-main' class='menu-button'>
                        <a href='#popup-menu-main'>Menu</a>
                    </div>
                </div>
                <div style='clear: both;'></div>
            </div>

            <!--Menus-->

            <ul id='popup-menu-main' class='menu'>
                <li id='menu-item-logout' class='menu-item-main'>
                    <a href='#'><span class='ui-icon ui-icon-circle-close'></span>Logout</a>
                </li>
            </ul>
        </div>

        <div id='overlay-app-loading' class='ajax-loading-div'></div>

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
