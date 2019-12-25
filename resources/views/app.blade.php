<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="css/app.css" rel="stylesheet" type="text/css"/>

    <link rel="icon" type="image/png" href="/favicon.png">

    <link href="https://fonts.googleapis.com/css?family=Lato:300,300i,400,400i,700,700i,900" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Josefin+Sans" rel="stylesheet">

    <title>Roast</title>

    <script type='text/javascript'>
        window.Laravel = <?php echo json_encode([
            'csrfToken' => csrf_token(),
        ]); ?>
    </script>
</head>
<body>
    <script>
        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
                (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
            m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
        })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

        ga('create', 'UA-142209270-1', 'auto');
        ga('send', 'pageview');
    </script>

    <div id="app">
        <router-view></router-view>
    </div>

    <script type="text/javascript" src="js/app.js"></script>
    <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyD5t7G0K8ghJDAsiMUkts1mDC3h5XlNtr8&libraries=places"></script>

</body>
</html>
