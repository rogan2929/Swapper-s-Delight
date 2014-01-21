<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title></title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- Place favicon.ico and apple-touch-icon.png in the root directory -->

        <link rel='stylesheet' href="//code.jquery.com/ui/1.10.3/themes/ui-lightness/jquery-ui.css">
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
        <div id='wrapper'>
            <div id='fb-root'></div>

            <div id='left-panel' class='left col'>
                <div class='body row'>
                    <div id='tabs-main' class='tabs-bottom'>
                        <ul>
                            <li><a href='#tab-feed'>Group Feed</a></li>
                            <li><a href='#tab-myposts'>My Posts</a></li>
                            <li><a href='#tab-liked'>Liked</a></li>
                            <li><a href='#tab-search'>Search</a></li>
                        </ul>
                        <div id='tab-feed'>
                            <ol id='feed-group'>
                            </ol>
                        </div>
                        <div id='tab-myposts'>
                            <ol id='feed-myposts'>
                            </ol>
                        </div>
                        <div id='tab-liked'>
                            <ol id='feed-pinned'>
                            </ol>
                        </div>
                        <div id='tab-search'>
                            <ol id='feed-search'>
                            </ol>
                        </div>
                    </div>
                </div>

                <div class='ad row ui-widget ui-widget-content ui-corner-all hidden'>Hey, I saw that! You're using something to block my ads, aren't you? Come on, admit it. Please do yourself a favor (and me) by purchasing the ad-free version of this app. It's a one-time upgrade and you'll never see an ad again! (At least not in this app; I truly wish I had the power to remove all ads, but... alas.)</div>
            </div>

            <div id='right-panel' class='right col'>
                <div class='body row ui-widget ui-widget-content scroll-y'>
                    <div id='panel-post'>
                        <div id='panel-image'>
                            <div class='hidden'>
                                <img src='/img/no-image.jpg'>
                                <span>This post does not have a photo, or the owner of this post has chosen not to give permission for applications like Swapper's Delight to view it. Click the link below to see the original Facebook posting.</span>
                                <a id='panel-post-permalink' class='wrappable-link' target='_blank'></a></div>
                        </div>
                        <div id='panel-message'></div>
                        <div id='panel-button-bar'>
                            <div id='panel-button-comment' class='panel-button'>Comment</div>
                            <div id='panel-button-pm' class='panel-button'>Message</div>
                            <div id='panel-button-like' class='panel-button'>Like</div>
                        </div>
                        <div id='panel-comments'>
                            <div id='panel-nocomments'>No comments yet.</div>
                            <ul id='panel-comment-list'>

                            </ul>
                        </div>
                    </div>
                    <button id='button-close-panel' class="ui-button ui-widget ui-state-default ui-corner-all ui-button-icon-only ui-dialog-titlebar-close" role="button" aria-disabled="false" title="close"><span class="ui-button-icon-primary ui-icon ui-icon-closethick"></span>
                        <span class="ui-button-text">close</span>
                    </button>
                </div>
            </div>

            <div class='header row ui-widget ui-widget-header'>
                <div class='float-left left-margin'>
                    <div id='button-menu-groups' class='menu-button'><a href='#popup-menu-groups'>Select a Group</a></div>
                    <div id='button-new' class='left-margin'>New</div>
                </div>
                <div class='float-right right-margin'>
                    <div id='button-menu-main' class='menu-button'><a href='#popup-menu-main'>Menu</a></div>
                </div>
                <div style='clear: both;'></div>
            </div>

            <!--Menus-->

            <ul id='popup-menu-groups' class='menu'>
                <li id='menu-item-choose-groups' class='menu-item-group'><a href='#'><span class='ui-icon ui-icon-check'></span>Edit Groups...</a></li>
            </ul>

            <ul id='popup-menu-main' class='menu'>
                <li id='menu-item-logout' class='menu-item-main'><a href='#'><span class='ui-icon ui-icon-circle-close'></span>Logout</a></li>
            </ul>

            <!--Dialogs-->
            <div id='dialog-new-post' class='hidden' title='Create New Post'>TEST</div>
        </div>

        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
        <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
        <script src="js/plugins.js"></script>
        <script src="js/main.js"></script>

        <!-- Google Analytics: change UA-XXXXX-X to be your site's ID. -->
        <script>
            (function(b, o, i, l, e, r) {
                b.GoogleAnalyticsObject = l;
                b[l] || (b[l] =
                        function() {
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
