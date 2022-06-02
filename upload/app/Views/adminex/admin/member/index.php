<?php
    /**
     * @var \Wow\Template\View $this
     */
    $this->setLayout(NULL);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <title>Admin Girişi | <?php echo Wow::get("ayar/site_title"); ?></title>
    <link rel="stylesheet" href="/assets/style/font-awesome.min.css" type="text/css" media="screen"/>
    <link href="/assets/themes/adminex/css/style.css" rel="stylesheet">
    <link href="/assets/themes/adminex/css/style-responsive.css" rel="stylesheet">
    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="/assets/themes/adminex/js/html5shiv.js"></script>
    <script src="/assets/themes/adminex/js/respond.min.js"></script>
    <![endif]-->
</head>

<body class="login-body">

<div class="container">
    <form method="post" class="form-signin">
        <div class="form-signin-heading text-center">
            <h1 class="sign-title">Admin Girişi</h1>
            <img src="/assets/images/logo.png" alt=""/>
        </div>
        <div class="login-wrap">
            <?php
                if(count($this->get('notifications')) > 0) {
                    $this->renderView("shared/notifications", $this->get('notifications'));
                }
            ?>

            <input type="text" name="username" class="form-control" placeholder="Kullanıcı Adı" autofocus>
            <input type="password" name="password" class="form-control" placeholder="Şifre">
            <input type="hidden" name="antiForgeryToken" value="<?php echo $_SESSION["AntiForgeryToken"]; ?>">
            <?php if(!empty(Wow::get("ayar/GoogleCaptchaSiteKey")) && !empty(Wow::get("ayar/GoogleCaptchaSecretKey"))) { ?>
                <div class="g-recaptcha" data-sitekey="<?php echo Wow::get("ayar/GoogleCaptchaSiteKey"); ?>"></div>
            <?php } ?>
            <button class="btn btn-lg btn-login btn-block" type="submit">
                <i class="fa fa-check"></i>
            </button>
        </div>
    </form>

</div>

<script src="/assets/themes/adminex/js/jquery-1.10.2.min.js"></script>
<script src="/assets/themes/adminex/js/bootstrap.min.js"></script>
<script src="/assets/themes/adminex/js/modernizr.min.js"></script>
<script src='https://www.google.com/recaptcha/api.js'></script>
<?php if(Wow::has("ayar/googleanalyticscode") != "") { ?>
    <script>
        (function(i, s, o, g, r, a, m) {
            i['GoogleAnalyticsObject'] = r;
            i[r] = i[r] || function() {
                (i[r].q = i[r].q || []).push(arguments)
            }, i[r].l = 1 * new Date();
            a = s.createElement(o),
                m = s.getElementsByTagName(o)[0];
            a.async = 1;
            a.src   = g;
            m.parentNode.insertBefore(a, m)
        })(window, document, 'script', 'https://www.google-analytics.com/analytics.js', 'ga');
        ga('create', '<?php echo Wow::get("ayar/googleanalyticscode"); ?>', 'auto');
        ga('send', 'pageview');
    </script>
<?php } ?>
</body>
</html>