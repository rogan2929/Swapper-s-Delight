<?php
// IE header to allow third party cookies.
header('P3P:CP="IDC DSP COR ADM DEVi TAIi PSA PSD IVAi IVDi CONi HIS OUR IND CNT"');

// Force browser to keep page updated.
header('Expires: ' . gmdate('D, d M Y H:i:s', time()) . ' GMT');
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
header('Cache-Control: no-cache, must-revalidate');
header('Pragma: no-cache');

error_reporting(E_ALL);
?>

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
        <meta name="og:description" content="Swapper's Delight Buy, Sell, and Trade Assistant">
        <meta name='og:image' content='https://fbcdn-photos-f-a.akamaihd.net/hphotos-ak-prn1/t39.2081-0/851559_1428631484051397_43248421_n.png'>

        <!-- Place favicon.ico and apple-touch-icon.png in the root directory -->

        <link rel="stylesheet" href="css/normalize.css">
        <link rel="stylesheet" href="css/main.css">
        <script src="js/vendor/modernizr-2.6.2.min.js"></script>

        <!--LifesiteMedia Script-->
        <script src="//ads.lfstmedia.com/getad?site=231280" type="text/javascript"></script>
    </head>
    <body>
        <!--[if lt IE 7]>
        <p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->

        <!-- Add your site or application content here -->
        <div id='app-content'>
            <div id='fb-root'></div>

            <div id='main-panel'>
                <div id='left-rail' class='block scroll-y'>
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
                    </div>
                    <input type='text' id='main-search' placeholder='Search this group'>
                    <div class='separator'></div>
                    <div>
                        <a href='https://www.facebook.com/SwappersDelight' target='_blank'>Our Facebook Page</a>
                    </div>
                    <div id='like-button-wrapper'>
                        <iframe src="//www.facebook.com/plugins/like.php?href=https%3A%2F%2Fapps.facebook.com%2F1401018793479333&amp;width=200&amp;layout=button_count&amp;action=like&amp;show_faces=false&amp;share=true&amp;height=21&amp;appId=1401018793479333" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:200px; height:21px;" allowTransparency="true"></iframe>
                    </div>
                </div>

                <div id='post-feed' class='scroll-y'></div>

                <!--Post Details Panel-->

                <div id='post-details-panel' class='floating-panel fullsize hidden'>
                    <div class='floating-panel-content ui-widget'>
                        <div id='details-prev' class='navigation prev'></div>
                        <div id='details-next' class='navigation next'></div>
                        <div class='close-button'></div>
                        <div id='details-left-column' class='column left'>
                            <div id='post-content' class='floating-panel-section'>
                                <div class='post-activity-section block original-content'>
                                    <div id='post-details-user-data'>
                                        <div class='facebook-user-photo'></div>
                                        <a class='facebook-user-name' target='_blank'></a>
                                        <div class='timestamp'></div>
                                    </div>
                                    <div id='post-message-text' class='activity-text'></div>
                                    <div id='post-message-linkdata' class='linkdata'>
                                        <div>
                                            <a id='linkdata-href' target='_blank' class='link-title'></a>
                                            <span class='linkdata-caption'></span>
                                        </div>
                                        <img class='linkdata-img'>
                                        <span class='linkdata-desc'></span>
                                    </div>
                                    <div id='post-comment-wrapper'>
                                        <textarea id='post-comment-text' placeholder='Type a comment and press [Enter] to post it.'></textarea>
                                        <div class='ajax-loading-div hidden'></div>
                                    </div>
                                </div>
                            </div>
                            <div id='post-comments' class='floating-panel-section'>
                                <div id='button-show-new-comments' class='button hidden'></div>
                                <div id='post-comment-list'></div>
                            </div>
                        </div>
                        <div id='details-right-column' class='column block right'>
                            <div id='post-image-container'></div>
                            <div id='post-no-image-desc' class='hidden'>
                                <span class='hint'>Hint: Sometimes posts have photos, but the owner of the post hasn't allowed apps like Swapper's Delight access to them. Click <a target='_blank' class='post-permalink wrappable-link'>here</a> to see the post directly in Facebook. You'll be able to see any hidden photos there.</span>
<!--                                <span><a href='#' class='privacy-settings-about'>What's this all about?</a></span>-->
                            </div>
                        </div>
                        <div class='ajax-loading-div hidden'></div>
                    </div>
                </div>

                <!--New Post Panel-->

                <div id='new-post-panel' class='floating-panel collapsed hidden'>
                    <div class='floating-panel-content ui-widget scroll-y'>
                        <div class='close-button'></div>
                        <div class='heading'>Create a new group post.</div>
                    </div>
                </div>

                <!--Select Group panel-->

                <div id='select-group-panel' class='floating-panel hidden'>
                    <div class='floating-panel-content ui-widget scroll-y'>
                        <div class='close-button'></div>
                        <span class='heading'>Choose a group to start.</span>
                        <span class='hint'>Hint: You can also hide any groups that you don't want to see here. Don't worry; click <a id='restore-group-selection-items' href='#'>here</a> to bring them back.</span>
                        <div id='select-group-list' class='selection-list'>
                            <div id='select-group-no-groups' class='selection-item select-group'>
                                You're not a member of any Facebook groups.
                            </div>
                        </div>
                    </div>
                </div>

                <!--Message Boxes-->

                <div id='message-box-panel' class='floating-panel modal hidden'>
                    <div id='popup-error-message' class='ui-widget floating-panel-content dialog-box hidden'>
                        <div class='message'>
                            <div class='message-icon'></div>
                            <span class='message-text'></span>
                        </div>
                        <div class='dialog-group'>
                            <div class='button dialog-button button-ok'>OK</div>
                        </div>
                    </div>
                    <div id='popup-info-message' class='ui-widget floating-panel-content dialog-box hidden'>
                        <div class='message'>
                            <div class='message-icon'></div>
                            <span class='message-text'></span>
                        </div>
                        <div class='dialog-group'>
                            <div class='button dialog-button button-ok'>OK</div>
                        </div>
                    </div>
                    <div id='popup-confirm-message' class='ui-widget floating-panel-content dialog-box hidden'>
                        <div class='message'>
                            <div class='message-icon'></div>
                            <span class='message-text'></span>
                        </div>
                        <div class='dialog-group'>
                            <div class='button dialog-button button-yes'>Yes</div>
                            <div class='button dialog-button button-no'>No</div>
                        </div>
                    </div>
                </div>
            </div>

            <div id='main-toolbar' class='block toolbar'>
                <div class='toolbar-group float-left'>
                    <!--                    <div id='button-new' class='button toolbar-button'>
                                            <span class='button-icon icon-new'></span>
                                            <span>New Post</span>
                                        </div>-->
                    <div id='button-view-group' class='button toolbar-button'>
                        <span class='button-icon icon-facebook'></span>
                        <span>View in Facebook</span>
                    </div>
                    <div id='button-refresh' class='button toolbar-button'>
                        <span class='button-icon icon-refresh'></span>
                        <span>Refresh</span>
                    </div>
                    <div id='button-menu-main' class='button toolbar-button menu-button icon-only'>
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

            <div id='post-details-toolbar' class='block toolbar floating-panel-toolbar hidden'>
                <div class='toolbar-group float-left'>
                    <div id='post-button-permalink' class='button toolbar-button'>
                        <span class='button-icon icon-facebook'></span>
                        <span>View in Facebook</span>
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
                <div class='toolbar-group float-right'>
                    <div id='post-button-delete' class='button toolbar-button personal-button'>
                        <span class='button-icon icon-delete'></span>
                        <span>Delete</span>
                    </div>
                </div>
            </div>

            <!--Menus and Popups-->

            <div id='popup-menu-main' class='menu'>
                <div id='menu-item-logout' class='menu-item'>
                    <span>Logout</span>
                </div>
            </div>

            <!--Ad Tiles that will be dynamically placed.-->

            <div id='ad-tile-1' class='post-block ui-widget ad-div post-block-text'>
                <div class='wrapper'>
                    <p class='content'>
                        Hey, I saw that! You're using something to block my ads, aren't you? Come on, admit it. Please do yourself a favor (and me) by turning what you're using off for this site. Ads are what makes this program viable. Thank you!
                    </p>
                </div>
            </div>
            <div id='ad-tile-2' class='post-block ui-widget ad-div post-block-text'>
                <div class='wrapper'>
                    <p class='content'>
                        Hey, I saw that! You're using something to block my ads, aren't you? Come on, admit it. Please do yourself a favor (and me) by turning what you're using off for this site. Ads are what makes this program viable. Thank you!
                    </p>
                </div>
            </div>
            <div id='ad-tile-3' class='post-block ui-widget ad-div post-block-text'>
                <div class='wrapper'>
                    <p class='content'>
                        Hey, I saw that! You're using something to block my ads, aren't you? Come on, admit it. Please do yourself a favor (and me) by turning what you're using off for this site. Ads are what makes this program viable. Thank you!
                    </p>
                </div>
            </div>
            <div id='ad-tile-4' class='post-block ui-widget ad-div post-block-text'>
                <div class='wrapper'>
                    <p class='content'>
                        Hey, I saw that! You're using something to block my ads, aren't you? Come on, admit it. Please do yourself a favor (and me) by turning what you're using off for this site. Ads are what makes this program viable. Thank you!
                    </p>
                </div>
            </div>

            <div id='overlay-loading-posts' class='floating-overlay'>
                <div class='ajax-loading-div semi-transparent'></div>
            </div>
            <div id='overlay' class='hidden'></div>
            <div id='message-overlay' class='hidden'></div>
            <div id='post-block-mask' class='post-block ui-widget hidden'></div>
            <div id='selection-item-mask' class='button selection-item ui-widget hidden'></div>

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
