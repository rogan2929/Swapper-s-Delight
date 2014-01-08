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

        <link rel='stylesheet' href="//code.jquery.com/ui/1.10.3/themes/dark-hive/jquery-ui.css"
              <link rel="stylesheet" href="css/normalize.css">
        <link rel="stylesheet" href="css/main.css">
        <link rel="stylesheet" href="css/swd.css">
        <script src="js/vendor/modernizr-2.6.2.min.js"></script>
    </head>
    <body>
        <!--[if lt IE 7]>
            <p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->

        <!-- Add your site or application content here -->
        <div id='wrapper'>
            <div id='fb-root'></div>

            <div class='left col'>
                <div class='body row scroll-x scroll-y'>
                    <div class='tabs-main'>
                        <ul>
                            <li><a href='#tab-feed'>Group Feed</a></li>
                            <li><a href='#tab-buying'>Buying</a></li>
                            <li><a href='#tab-selling'>Selling</a></li>
                        </ul>
                        <div id='tab-feed'>
                            Group Feed
                        </div>
                        <div id='tab-buying'>
                            Buying Posts
                        </div>
                        <div id='tab-selling'>
                            Selling Posts
                        </div>
                    </div>
                </div>

                <div class='ad row ui-widget ui-widget-content'>Hey, I saw that! You're using something to block my ads, aren't you? Come on, admit it. Please do yourself a favor (and me) by purchasing the ad-free version of this app. It's a one-time upgrade and you'll never see an ad again! (At least not in this app; I truly wish I had the power to remove all ads, but... alas.)</div>
            </div>

            <div class='right col'>
                <div class='body row scroll-y ui-widget ui-widget-content'>
                    No post has been selected.
                </div>
            </div>

            <div class='header row'>
                <div class='button-menu-groups'>Select a Group</div>
                <div class='button-menu-main'>Menu</div>
            </div>
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
