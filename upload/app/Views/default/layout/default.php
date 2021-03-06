<?php
    /**
     * Wow Master Template
     *
     * @var \Wow\Template\View      $this
     * @var \App\Models\LogonPerson $logonPerson
     */
    $logonPerson = $this->get("logonPerson");
    $uyelik      = $logonPerson->member;
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <?php $this->section("section_head"); ?>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="/assets/bootstrap/css/bootstrap-paper.min.css">
    <link type="text/css" rel="stylesheet" href="/assets/lightgallery/dist/css/lightgallery.min.css"/>
    <link rel="stylesheet" href="/assets/scripts/fancybox/source/jquery.fancybox.css?v=2.1.5" type="text/css" media="screen"/>
    <link rel="stylesheet" href="/assets/style/font-awesome.min.css" type="text/css" media="screen"/>
    <link rel="stylesheet" href="/assets/style/paper.css?v=v3.1.0">
    <link rel="stylesheet" href="/assets/nprogress/nprogress.css">
    <link rel="shortcut icon" href="/assets/images/favicon.png" type="image/x-icon"/>
    <title><?php if($this->has('title')) {
            echo $this->get('title') . " | ";
        }
            echo Wow::get("ayar/site_title"); ?></title>
    <?php if($this->has('description')) { ?>
        <meta name="description" content="<?php echo $this->get('description'); ?>"><?php } ?>
    <?php if($this->has('keywords')) { ?>
        <meta name="keywords" content="<?php echo $this->get('keywords'); ?>"><?php } ?>
    <?php $this->show(); ?>
</head>
<body <?php if(isset($_SESSION["instamisToken"])) { ?>style="padding-top:0 !important;"<?php } ?>>
<?php if(!isset($_SESSION["instamisToken"])) { ?>
    <header>
        <nav class="navbar navbar-default navbar-fixed-top">
            <div class="container">
                <div class="navbar-header pull-left">
                    <a class="navbar-brand" href="<?php echo $logonPerson->isLoggedIn() ? '/tools' : '/'; ?>"><img alt="instagram takip" src="/assets/images/logo.png"/></a>
                </div>
                <div class="navbar-header pull-right">
                    <ul class="nav navbar-nav pull-left">
                        <?php if(!$logonPerson->isLoggedIn()) { ?>
                            <li><p class="navbar-btn">
                                    <a id="loginAsUser" class="btn btn-primary" href="<?php echo Wow::get("project/memberLoginPrefix"); ?>"><i class="fa fa-sign-in"></i> G??R????</a>
                                </p></li>
                        <?php } else { ?>
                            <li class="dropdown pull-right">
                                <a href="" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false" style="text-transform: none;">
                                    <img src="<?php echo str_replace("http:", "https:", $uyelik["profilFoto"]); ?>" alt="<?php $uyelik["kullaniciAdi"]; ?>" style="max-height:30px;"> <?php echo (strlen($uyelik["fullName"]) > 10) ? substr($uyelik["fullName"], 0, 5) . ".." : $uyelik["fullName"]; ?>
                                    <span class="caret"></span></a>
                                <ul role="menu" class="dropdown-menu dropdown-light fadeInUpShort">
                                    <?php if($logonPerson->member->isBayi == 1) { ?>
                                        <li><a href="/bayi-tx" class="menu-toggler"> Bayi Paneli </a></li>
                                        <li class="divider"></li>
                                    <?php } ?>
                                    <li>
                                        <a href="/user/<?php echo $logonPerson->member->instaID; ?>" class="menu-toggler"> Profilim </a>
                                    </li>
                                    <li><a href="/account/settings" class="menu-toggler"> Hesap Ayarlar?? </a></li>
                                    <li class="divider"></li>
                                    <li><a href="/account/logout" class="menu-toggler"> ????k???? Yap </a></li>
                                </ul>
                            </li>
                            <li class="pull-right">
                                <a href="/messages" title="Mesaj Kutum" style="text-transform: none;">
                                    <img src="/assets/images/direct_icon.png" style="max-height:30px;">
                                    <span class="badge<?php echo isset($_SESSION["NonReadThreadCount"]) && intval($_SESSION["NonReadThreadCount"]) > 0 ? '' : ' hidden'; ?>" id="nonReadThreadCount"><?php echo isset($_SESSION["NonReadThreadCount"]) ? $_SESSION["NonReadThreadCount"] : 0; ?></span>
                                </a>
                            </li>
                        <?php } ?>
                    </ul>


                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".navbar-collapse" aria-expanded="false">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                </div>
                <div class="navbar-collapse collapse navbar-left">
                    <ul class="nav navbar-nav">
                        <li<?php echo $this->route->params["controller"] == "Tools" ? ' class="active"' : ''; ?>>
                            <a href="/tools"><?php echo $this->translate("instagram/menu/tools"); ?></a></li>
                        <li<?php echo $this->route->params["controller"] == "Packages" ? ' class="active"' : ''; ?>>
                            <a href="/packages"><?php echo $this->translate("instagram/menu/packages"); ?></a></li>
                        <?php if(!$logonPerson->isLoggedIn()) { ?>
                        <li<?php echo $this->route->params["controller"] == "Blog" ? ' class="active"' : ''; ?>>
                                <a href="/blog">Blog</a></li><?php } ?>
                    </ul>
                </div>
                <?php if($logonPerson->isLoggedIn()) { ?>
                    <div class="navbar-collapse collapse navbar-right">
                        <?php if($logonPerson->isLoggedIn()) { ?>
                            <div class="pull-left">
                                <form class="navbar-form" role="search" action="/account/search">
                                    <input type="hidden" name="tab" value="<?php echo $this->route->params["controller"] == "Account" && ($this->route->params["action"] == "Search" || $this->route->params["action"] == "Tag" || $this->route->params["action"] == "Location") ? $this->get("tab") : ""; ?>">
                                    <div class="input-group">
                                        <input type="text" class="form-control" placeholder="Ara" name="q" value="<?php echo $this->route->params["controller"] == "Account" && ($this->route->params["action"] == "Search" || $this->route->params["action"] == "Tag" || $this->route->params["action"] == "Location") ? $this->get("q") : ""; ?>" required>
                                        <div class="input-group-btn">
                                            <button class="btn btn-default" type="submit">
                                                <i class="glyphicon glyphicon-search"></i></button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        <?php } ?>
                    </div>
                <?php } ?>
            </div>
        </nav>
    </header>
<?php } ?>
<?php
    if($logonPerson->isLoggedIn()) {
        $this->renderView("shared/account-bar");
    }
    if($this->has("navigation")) {
        $this->renderView("shared/navigation", $this->get("navigation"));
    }
    if(count($this->get('notifications')) > 0) {
        $this->renderView("shared/notifications", $this->get('notifications'));
    }
    $this->renderBody();
?>
<footer class="bg-dark footer-one">
    <div class="container">
        <div class="row">
            <div class="col-md-3">
                <center><img alt="site logo" class="img-responsive" src="/assets/images/logo.png"/>
                <p>
                    <a href="<?php echo (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http') . "://" . $_SERVER['SERVER_NAME']; ?>">Instagram Takip??i ve Be??eni Hilesi</a>
                </p>
                <p>Copyright &copy; <?php echo date("Y"); ?>
                    <a href="<?php echo (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http') . "://" . $_SERVER['SERVER_NAME']; ?>"><?php echo $_SERVER['SERVER_NAME']; ?></a></center><br>
                </p>
            </div>
            <div class="col-md-3">
                <h5>Nas??l ??al??????r</h5>
                <p>Kredileriniz ile diledi??iniz payla????m??n??za be??eni ve profilinize takip??i g??nderebilirsiniz.
                    <a href="/packages">Paketler</a> b??l??m??nden uygun fiyatlar ile bir paket sat??n alabilirsiniz.</p>
            </div>
            <div class="col-md-3">
                <h5>Kimler Kullanabilir</h5>
                <p>Instagram ??yeli??i olan herkes sistemi kullanabilir. Instagram hesab??n??zla giri?? yap??n ve hemen kullanmaya ba??lay??n. Kullan??m ??cretsizdir. Kredi sat??n almad??k??a hi??bir ??cret ??demezsiniz.</p>
            </div>
            <div class="col-md-3">
                <h5>Bize Ula????n</h5>
<?php if(Wow::get("ayar/contact_whatsapp") != "") { ?>
                <class="font-secondary font-15"><a style="color: #00ff00;"><i class="fa fa-whatsapp" aria-hidden="true"></i> Whatsapp:</a> <?php echo Wow::get("ayar/contact_whatsapp"); ?><br>
<?php } ?>
<?php if(Wow::get("ayar/contact_instagram") != "") { ?>
                        <face="tahoma""><a style="color: #dd2a7b;"><i class="fa fa-instagram" aria-hidden="true"></i> Instagram:</a> <?php echo Wow::get("ayar/contact_instagram"); ?><br>
<?php } ?>
<?php if(Wow::get("ayar/contact_mail") != "") { ?>
                <class="font-secondary font-15"><a style="color: #ffbe00;"><i class="fa fa-envelope-o" aria-hidden="true"></i> E-Posta:</a> <?php echo Wow::get("ayar/contact_mail"); ?><br>
<?php } ?>
<?php if(Wow::get("ayar/contact_skype") != "") { ?>
                        <face="tahoma""><a style="color: #00aff0;"><i class="fa fa-skype" aria-hidden="true"></i> Skype:</a> <?php echo Wow::get("ayar/contact_skype"); ?><br>
<?php } ?>
<a href="https://instaturk.org/" title="smm panel">smm panel</a>
<a href="https://ig.web.tr/" title="smm panel">instagram takip??i hilesi</a>
            </div>
        </div>
    </div>
</footer>
<?php $this->section("section_modals");
    if($logonPerson->isLoggedIn()) { ?>
        <div class="modal" id="modalEditMedia" style="z-index: 1051;">
            <div class="modal-dialog">
                <div class="modal-content" id="modalEditMediaInner">
                </div>
            </div>
        </div>
        <div class="modal" id="modalChangeProfilePhoto" style="z-index: 1051;">
            <div class="modal-dialog">
                <div class="modal-content" id="modalChangeProfilePhotoInner">
                </div>
            </div>
        </div>
        <div class="modal fade" id="addPicture">
            <div class="modal-dialog">
                <div class="modal-content">
                    <ul class="nav nav-tabs nav-justified nav-tabs-justified">
                        <li class="active"><a href="#addByUploadImage" data-toggle="tab">Resim Y??kle</a></li>
                        <li><a href="#addByUrl" data-toggle="tab">Url'den Ekle</a></li>
                    </ul>
                    <div id="myTabContent" class="tab-content">
                        <div class="tab-pane fade active in" id="addByUploadImage">
                            <form method="post" action="/account/upload-image" enctype="multipart/form-data" onsubmit="$('#btnUploadImage').html('Y??KLEN??YOR BEKLEY??N..').removeClass('btn-primary').addClass('btn-success').attr('disabled','disabled');">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                    <h4 class="modal-title">Resim Y??kle</h4>
                                </div>
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label>A????klama</label>
                                        <input class="form-control" type="text" name="aciklama" placeholder="Ne d??????n??yorsun..">
                                    </div>
                                    <div class="form-group">
                                        <label>Resim</label>
                                        <input type="file" name="file" required>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Vazge??</button>
                                    <button type="submit" id="btnUploadImage" class="btn btn-primary">Kaydet</button>
                                </div>
                            </form>
                        </div>
                        <div class="tab-pane fade" id="addByUrl">
                            <form method="post" action="/account/upload-image-from-url" onsubmit="$('#btnAddImage').html('Y??KLEN??YOR BEKLEY??N..').removeClass('btn-primary').addClass('btn-success').attr('disabled','disabled');">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                    <h4 class="modal-title">Url ??le Resim Ekle</h4>
                                </div>
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label>A????klama</label>
                                        <input class="form-control" type="text" name="aciklama" placeholder="Ne d??????n??yorsun..">
                                    </div>
                                    <div class="form-group">
                                        <label>Resim Url</label>
                                        <input type="text" name="url" class="form-control" placeholder="??rn: http://site.com/image.jpg" required>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Vazge??</button>
                                    <button type="submit" class="btn btn-primary" id="btnAddImage">Kaydet</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="modalNewMessage">
            <div class="modal-dialog">
                <div class="modal-content" id="modalNewMessageInner">
                </div>
            </div>
        </div>
    <?php } ?>
<div class="modal fade" id="modalContact" style="z-index: 1051;">
    <div class="modal-dialog">
        <div class="modal-content" id="infoModal">
            <div class="modal-body">
                <h3>??leti??im Bilgileri</h3>
                <p>Kredi sat??n almak i??in a??a????da bulunan ileti??im kanallar??ndan bize ula??abilirsiniz.</p>
                <?php if(Wow::get("ayar/contact_whatsapp") != "") { ?>
                    <p><span style="color:#43d854"><i class="fa fa-whatsapp" aria-hidden="true"></i> Whatsapp </span>:
                        <b><?php echo Wow::get("ayar/contact_whatsapp"); ?></b></p>
                <?php } ?>
                <?php if(Wow::has("ayar/contact_skype") != "") { ?>
                    <p><span style="color:#00aff0"><i class="fa fa-skype" aria-hidden="true"></i> Skype </span> :
                        <b><?php echo Wow::get("ayar/contact_skype"); ?></b></p><br>
                        <a href="https://instaturk.org/" title="smm panel">smm panel</a>
                        <a href="https://ig.web.tr/" title="smm panel">instagram takip??i hilesi</a>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modalSorumluluk" style="z-index: 1051;">
<div class="modal-dialog">
<div class="modal-content" id="modalSorumlulukInner">
<div class="modal-header"><button aria-hidden="true" class="close" data-dismiss="modal" type="button">??</button>

<h4 class="modal-title"><?php echo Wow::get("ayar/site_title"); ?> Sorumluluk Kurallar??</h4>
</div>

<div class="modal-body">
<div class="well">
<p>Her birey kendisinden sorumludur site ??zerinden yap??lan hi??bir i??lem web site sahibi ile alakas?? olmamaktad??r. Bunun yan?? s??ra mahkemede tespit edilip i??lemi yapan kullan??c?? bilgileri mahkemeye sunularak taraf??m??z sorumlulu??unu getirecektir.</p>

<p>Kullan??c?? Ad??, ??ifre veya e-posta de??i??imlerinden hi??bir t??rl?? web sitemiz sorumlu de??ildir. Kullan??c?? Sisteme giri?? yapt??????nda tamamen kendi sorumlulu??u alt??ndad??r.</p>

<p>Web site ??zerinde yap??lan spam i??lemler mahkemeye sunulacakt??r.</p>

<p>Web Sitemiz Kullan??c?? Havuzu y??ntemiyle ??al??????r. Sizler i??lem yaparken ba??ka ki??iler hesab??n??zdan i??lem talep ediyor olabilir.</p>

<p>Web siteye yap??lan k??t??lemeler ve iftiralar sonucu bu k??t??lemeleri ve iftiralar?? yapan ki??i mahkemeye verilecektir.</p>

<p>Instagram ??ifreleri otomatik s??f??rlanmaktad??r. Bu i??lem ger??ekle??ti??inde web sitemiz sorumlu de??ildir.</p>

<p>Instagram hesab??n??z??n kapat??lmas?? spam olmas?? veya ask??ya al??nmas?? sitemiz taraf??ndan sorumlu tutulmamaktad??r.</p>

<p><?php echo Wow::get("ayar/site_title"); ?> instagram ile giri?? yapan kullan??c??lar??n herhangi bir bilgileri sitemizde bar??nd??rmamak ??zere 24 saatte bir silinmektedir.</p>

<p>Hi??bir kullan??c?? ge??mi?? i??lemler i??in kay??t listesine ula??amayacakt??r.</p>

<p>De??i??en kullan??c?? ad??, ??ifre ve e-postalardan sitemiz sorumlu olmamakla beraber kullan??c?? hi??bir hak talebinde bulunamayacakt??r.</p>

<p>Web sitemizi gereksiz yere mahkeme ve benzeri i??lemler ile rahats??z edenler ki??ilere tazminat davas?? a????lacakt??r.</p>

<p>Sisteme girmi?? t??m kullan??c??lar bu maddeleri okumu?? ve kabul etmi?? say??lmaktad??r.</p>
</div>
</div>
</div>
</div>
</div>

<?php $this->show(); ?>
<?php $this->section('section_scripts'); ?>
<script src="/assets/jquery/2.2.4/jquery.min.js"></script>
<script src="/assets/bootstrap/js/bootstrap.min.js"></script>
<script src="/assets/scripts/fancybox/source/jquery.fancybox.pack.js"></script>
<script src="/assets/lightgallery/dist/js/lightgallery.min.js"></script>
<script src="/assets/lightgallery/dist/js/lg-video.min.js"></script>
<script src="/assets/lazyload/jquery.lazyload.min.js"></script>
<script src="/assets/nprogress/nprogress.js"></script>
<script src="/assets/core/core.js?v=3.1.10"></script>
<?php $this->show(); ?>
<script type="text/javascript">
    <?php if(Wow::has("ayar/googleanalyticscode") != "") { ?>
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
    <?php } ?>
    initProject();
</script>

<style>
    /* The Modal (background) */
    .modal {
        display          : none; /* Hidden by default */
        position         : fixed; /* Stay in place */
        z-index          : 999999; /* Sit on top */
        padding-top      : 100px; /* Location of the box */
        left             : 0;
        top              : 0;
        width            : 100%; /* Full width */
        height           : 100%; /* Full height */
        overflow         : auto; /* Enable scroll if needed */
        background-color : rgb(0, 0, 0); /* Fallback color */
        background-color : rgba(0, 0, 0, 0.4); /* Black w/ opacity */
    }

    /* Modal Content */
    .modal-content {
        background-color : #fefefe;
        margin           : auto;
        padding          : 20px;
        border           : 1px solid #888;
        width            : 80%;
    }

    /* The Close Button */
    .close {
        color       : #aaaaaa;
        float       : right;
        font-size   : 28px;
        font-weight : bold;
    }

    .close:hover,
    .close:focus {
        color           : #000;
        text-decoration : none;
        cursor          : pointer;
    }
</style>

<!-- The Modal -->
<!--<div id="myModal" class="modal">-->
<!---->
<!--    <!-- Modal content -->
<!--    <div class="modal-content">-->
<!--        <span class="close">&times;</span>-->
<!--        <p style="text-align:center;">-->
<!--            <img style="width:100px;" src="http://insta.web.tr/assets/icon.png"/><br/>-->
<!--        <div style="font-size:22px;text-align:center;">Instamis uygulamas??n?? indirerek her g??n-->
<!--            <b>1000</b> takip??i, be??eni, yorum ve story g??r??nt??lenmesi kazanabilirsiniz.<br/>-->
<!--            <a target="_blank" href="https://play.google.com/store/apps/details?id=org.instamoda">https://play.google.com/store/apps/details?id=org.instamoda</a><br/>-->
<!--        </div>-->
<!--        </p>-->
<!--    </div>-->
<!--</div>-->

<?php $this->section('section_scripts');
    $this->parent(); ?>
<script>
    // // Get the modal
    // var modal = document.getElementById('myModal');
    //
    // // Get the <span> element that closes the modal
    // var span = document.getElementsByClassName("close")[0];
    //
    // // When the user clicks on <span> (x), close the modal
    // span.onclick = function() {
    //     modal.style.display = "none";
    // }
    //
    // // When the user clicks anywhere outside of the modal, close it
    // window.onclick = function(event) {
    //     if(event.target == modal) {
    //         modal.style.display = "none";
    //     }
    // }
    //
    // document.addEventListener('DOMContentLoaded', function() {
    //     modal.style.display = "block";
    // }, false);
</script>
<?php $this->show(); ?>
</body>
</html>