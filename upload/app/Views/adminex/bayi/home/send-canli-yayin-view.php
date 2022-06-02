<?php
    /**
     * @var \Wow\Template\View      $this
     * @var array                   $media
     * @var \App\Models\LogonPerson $logonPerson
     */
    $logonPerson = $this->get("logonPerson");
    $user        = NULL;
    if($this->has("user")) {
        $user = $this->get("user");
    }
?>
    <h4>Canlı Yayına İzleyici Gönderme Aracı</h4>
<?php if(is_null($user)) { ?>
    <p>Canlı yayına izleyici gönderme aracı ile, dilediğiniz kullanıcıya, kendi belirlediğiniz adette izleyici gönderebilirsiniz. Gönderilen izleyicilerin tamamı gerçek kullanıcılardır.</p>
    <div class="panel panel-default">
        <div class="panel-heading">
            Canlı Yayına İzleyici Gönder
        </div>
        <div class="panel-body">
            <form method="post" action="?formType=findUserID" class="form">
                <div class="form-group">
                    <label>Kullanıcı Adı:</label>
                    <input type="text" name="username" class="form-control" placeholder="instabayisipanel" required>
                </div>
                <button type="submit" class="btn btn-success">Kullanıcıyı Bul</button>
            </form>
        </div>
    </div>
<?php } else { ?>
    <p><strong>Kalan İşlem Hakkınız:</strong> <?php echo $logonPerson->member->gunlukCanliYayinLimitLeft; ?>
    </p>
    <p>
        <strong>İşlem Başına Gönderebileceğiniz Max İzleyici:</strong> <?php echo $logonPerson->member->canliYayinMaxKredi; ?>
    </p>
    <div class="panel panel-default">
        <div class="panel-heading">
            Canlı Yayına İzleyici Gönder
        </div>
        <div class="panel-body">
            <form id="formTakip" class="form">
                <div class="form-group">
                    <label><?php echo "@" . $user["user"]["username"]; ?></label>
                    <img src="<?php echo str_replace("http:", "https:", $user["user"]["profile_pic_url"]); ?>" class="img-responsive" style="max-height: 200px;"/>
                </div>
                <div class="form-group">
                    <label>İzleyici Sayısı:</label>
                    <input type="text" name="adet" class="form-control" placeholder="10" value="10">
                    <span class="help-block">Max <?php echo $logonPerson->member->canliYayinMaxKredi; ?> izleyici gönderebilirsiniz.</span>
                </div>
                <div class="form-group">
                    <label>Kaç Dk:</label>
                    <input type="text" name="dakika" class="form-control" placeholder="Kaç dk boyunca gönderilecek?" value="1">
                    <span class="help-block">Dakika girmezseniz 1dk olarak işlem yapacaktır.</span>
                </div>
                <?php if($logonPerson->member->canliYayinGender === 1) { ?>
                    <div class="form-group">
                        <label>Cinsiyet:</label>
                        <select name="gender" class="form-control">
                            <option value="0">Karışık</option>
                            <option value="1">Erkek</option>
                            <option value="2">Bayan</option>
                        </select>
                    </div>
                <?php } ?>
                <input type="hidden" name="userID" value="<?php echo $user["user"]["pk"]; ?>">
                <input type="hidden" name="userName" value="<?php echo $user["user"]["username"]; ?>">
                <input type="hidden" name="imageUrl" value="<?php echo str_replace("http:", "https:", $user["user"]["profile_pic_url"]); ?>">
                <input type="hidden" name="_method" value="POST">
                <button type="button" id="formTakipSubmitButton" class="btn btn-success" onclick="sendTakip();">Gönderimi Başlat</button>
            </form>
            <div class="cl10"></div>
            <div id="userList"></div>
        </div>
    </div>
<?php } ?>

<?php $this->section("section_scripts");
    $this->parent();
    if(!is_null($user)) { ?>
        <script type="text/javascript">
            var countTakip, countTakipMax, bayiIslemIDLast;

            function sendTakip() {
                countTakip    = 0;
                countTakipMax = parseInt($('#formTakip input[name=adet]').val());

                if(isNaN(countTakipMax) || countTakipMax <= 0) {
                    alert('İzleyici adedi girin!');
                    return false;
                }

                if(countTakipMax > <?php echo $logonPerson->member->canliYayinMaxKredi; ?>) {
                    alert('İzleyici adedi max <?php echo $logonPerson->member->canliYayinMaxKredi; ?> olabilir!');
                    return false;
                }

                $('#formTakipSubmitButton').html('<i class="fa fa-spinner fa-spin fa-2x"></i> Gönderimi Başlat');
                $('#formTakip input').attr('readonly', 'readonly');
                $('#formTakip button').attr('disabled', 'disabled');
                $('#userList').html('');
                sendTakipRC();
            }

            function sendTakipRC() {
                url = '?formType=send';
                $.ajax({type: 'POST', dataType: 'json', url: url, data: $('#formTakip').serialize()}).done(function(data) {

                    if(data.status == 'error') {
                        $('#userList').prepend('<p class="text-danger">' + data.message + '</p>');
                        sendTakipComplete();
                    }
                    else {
                        sendTakipComplete();
                    }
                }).fail(function() {
                    setTimeout(function() {
                        sendTakipRC(bayiIslemIDLast);
                    }, 3000);
                });
            }

            function sendTakipComplete() {
                $('#formTakipSubmitButton').html('Gönderimi Başlat');
                $('#formTakip input').removeAttr('readonly');
                $('#formTakip button').prop("disabled", false);
                $('#formTakip input[name=adet]').val('10');
                $('#userList').prepend('<p class="text-success">Canlı Yayına Giren Toplam Kullanıcı Adedi: ' + countTakip + '</p>');
            }
        </script>
    <?php }
    $this->endSection(); ?>