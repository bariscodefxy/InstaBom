<?php
    /**
     * @var \Wow\Template\View $this
     * @var array              $model
     */

    $api = $model["api"];
?>

<?php if(isset($api["status"]) && $api["status"] == 1) { ?>
    <div class="panel panel-default">
        <div class="panel-heading">
            InstaWeb SMM
        </div>
        <div class="panel-body">
            <h2><?php echo $api["user"]["username"]; ?>
                <a href="<?php echo Wow::get("project/adminPrefix"); ?>/api-smm/cikis-yap" style="font-size:12px;">(SMM Çıkış Yap)</a></h2>
            <h3>Bakiye : <label class="label label-success"><?php echo $api["user"]["bakiye"]; ?> ₺</label></h3>
            <hr/>
            <h3>Video Görüntüleme : <?php echo $api["user"]["videoGoruntulenme"] == "aktif" ? '<label class="label label-success">Aktif</label>' : '<label class="label label-danger">Pasif</label>'; ?></h3>
            <h3>Oto Proxy : <?php echo $api["user"]["otoProxy"] == "aktif" ? '<label class="label label-success">Aktif</label>' : '<label class="label label-danger">Pasif</label>'; ?></h3>
            <h3>Resim Kaydetme : <?php echo $api["user"]["resimKaydetme"] == "aktif" ? '<label class="label label-success">Aktif</label>' : '<label class="label label-danger">Pasif</label>'; ?></h3>
            <h3>Api Servisi : <?php echo $api["user"]["apiServisi"] == "aktif" ? '<label class="label label-success">Aktif</label>' : '<label class="label label-danger">Pasif</label>'; ?></h3>
            <hr/>
            <p>Bakiye yüklemek için skype : <b>ckn.89</b> <span style="font-size:11px;">(Min. bakiye ödemesi <u>100TL</u>)</span></p>
            <?php if(count($api["duyurular"]) > 0) { ?>
                <br/><hr/>
                <h3>Duyurular</h3></hr>
                <?php foreach($api["duyurular"] AS $duyuru) { ?>
                    <div class="alert alert-<?php echo $duyuru["type"]; ?>">
                        <?php echo $duyuru["duyuru"]; ?>
                        <br/><i style="font-size:11px;"><?php echo date("d-m-Y H:i:s", strtotime($duyuru["registerDate"])); ?></i>
                    </div>
                <?php } ?>
            <?php } ?>
        </div>
    </div>

    <?php $this->section("section_scripts");
    $this->parent(); ?>
    <script type="text/javascript">

    </script>
    <?php $this->endSection(); ?>
<?php } else { ?>
    <div class="panel panel-default">
        <div class="panel-heading">
            InstaWeb SMM Login
        </div>
        <div class="panel-body">
            <p>InstaWeb SMM Paneli sizlere mevcut kabiliyetleriniz yetmediğinde destek sağlayan bir paneldir. Kayıt işlemini tamamlayarak sistemi kullanmaya başlayabilirsiniz. Daha önceden kaydınız varsa giriş yaparak üyeliğinizi devam ettirebilirsiniz.</p>
            <p class="text-warning">
                <b>Not!</b> Video Görüntülenme ve Oto proxy sistemini kullanabilmeniz için INSTAWEB SMM sistemine kayıt olmanız gerekmektedir.
            </p>
            <p class="text-danger">
                <b>Dikkat!</b> Lisanslı bir kullanıcıysanız ve lisans hatası alıyorsanız lütfen skype üzerinden
                <b>ckn.89</b> adresine bu bilgiyi iletiniz.</p>
            <div class="col-sm-6">
                <section class="panel">
                    <header class="panel-heading">
                        InstaWeb SMM Kayıt Ol
                    </header>
                    <div class="panel-body">
                        <form role="form" id="kayitform" onsubmit="return false;">
                            <div class="form-group">
                                <label for="kayitkullaniciadi">Kullanıcı Adı</label>
                                <input type="text" class="form-control" id="kayitkullaniciadi" placeholder="Kullanıcı Adı">
                            </div>
                            <div class="form-group">
                                <label for="kayitsifre">Şifre</label>
                                <input type="password" class="form-control" id="kayitsifre" placeholder="Şifre">
                            </div>
                            <div class="form-group">
                                <label for="kayitsifretekrar">Şifre Tekrar</label>
                                <input type="password" class="form-control" id="kayitsifretekrar" placeholder="Şifre Tekrar">
                            </div>
                            <button type="submit" class="kayitbtn btn btn-primary">Kayıt Ol</button>
                            <br/>
                            <hr/>
                            <br/>
                            <span class="alert alert-block alert-danger"><b>Dikkat!</b> Her siteden sadece 1 üyelik alınabilmektedir. Kayıt olurken bilgilerinizin doğruluğundan emin olun.</span>
                        </form>

                    </div>
                </section>
            </div>
            <div class="col-sm-6">
                <section class="panel">
                    <header class="panel-heading">
                        InstaWeb SMM Giriş Yap
                    </header>
                    <div class="panel-body">
                        <form role="form" id="loginform" onsubmit="return false;">
                            <div class="form-group">
                                <label for="giriskullaniciadi">Kullanıcı Adı</label>
                                <input type="text" class="form-control" id="giriskullaniciadi" placeholder="Kullanıcı Adı">
                            </div>
                            <div class="form-group">
                                <label for="girissifre">Şifre</label>
                                <input type="password" class="form-control" id="girissifre" placeholder="Şifre">
                            </div>
                            <button type="submit" class="girisbtn btn btn-primary">Giriş Yap</button>
                        </form>
                    </div>
                </section>
            </div>
        </div>
    </div>

    <?php $this->section("section_scripts");
    $this->parent(); ?>
    <script type="text/javascript">
        $('.kayitbtn').click(function() {
            var kullaniciAdi = $('#kayitkullaniciadi').val();
            var sifre        = $('#kayitsifre').val();
            var sifretekrar  = $('#kayitsifretekrar').val();

            if(kullaniciAdi.length < 5 || sifre.length < 6 || sifre !== sifretekrar) {
                alert("Kullanıcı Adınız en 5 karakter olmalı, Şifreniz 6 yada daha uzun karakter ve tekrar yazdığınız şifreniz ile eşleşmelidir.");
                return false;
            }
            $('.kayitbtn').html('<i class="fa fa-circle-o-notch fa-spin"></i>').attr('disabled', "disabled");
            ;
            $.ajax({
                       type   : "POST",
                       url    : "<?php echo Wow::get("project/adminPrefix"); ?>/api-smm/post-data",
                       data   : {kullaniciAdi: kullaniciAdi, sifre: sifre, sifretekrar: sifretekrar, type: "register"},
                       success: function(data) {
                           if(data.status === 1) {
                               $.ajax({
                                          type   : "POST",
                                          url    : "<?php echo Wow::get("project/adminPrefix"); ?>/api-smm/post-data",
                                          data   : {auth: data.Auth, type: "authregister"},
                                          success: function(data) {
                                              console.log(data);
                                              if(data.status === 1) {
                                                  alert("Üyeliğiniz başarılı bir şekilde oluşturulmuştur.");
                                                  location.reload();
                                              } else {
                                                  alert(data.error);
                                              }
                                          }
                                      });
                           } else {
                               alert(data.error);
                           }
                           $('.kayitbtn').html('Kayıt Ol').removeAttr('disabled');
                       }
                   });
        });

        $('.girisbtn').click(function() {

            var kullaniciAdi = $('#giriskullaniciadi').val();
            var sifre        = $('#girissifre').val();

            if(kullaniciAdi.length < 5 || sifre.length < 6) {
                alert("Kullanıcı adınız en az 5, şifreniz de en az 6 karakter olmalıdır.");
                return false;
            }

            $('.girisbtn').html('<i class="fa fa-circle-o-notch fa-spin"></i>').attr('disabled', "disabled");

            $.ajax({
                       type   : "POST",
                       url    : "<?php echo Wow::get("project/adminPrefix"); ?>/api-smm/post-data",
                       data   : {kullaniciAdi: kullaniciAdi, sifre: sifre, type: "login"},
                       success: function(data) {
                           if(data.status === 1) {
                               $.ajax({
                                          type   : "POST",
                                          url    : "<?php echo Wow::get("project/adminPrefix"); ?>/api-smm/post-data",
                                          data   : {auth: data.Auth, type: "authregister"},
                                          success: function(data) {
                                              if(data.status === 1) {
                                                  location.reload();
                                              } else {
                                                  alert(data.error);
                                              }
                                          }
                                      });
                           } else {
                               alert(data.error);
                           }
                           $('.girisbtn').html('Giriş Yap').removeAttr('disabled');
                       }
                   });

        });
    </script>
    <?php $this->endSection(); ?>
<?php } ?>