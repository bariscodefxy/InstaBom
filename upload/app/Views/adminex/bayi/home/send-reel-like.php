<?php
    /**
     * @var \Wow\Template\View      $this
     * @var array                   $media
     * @var \App\Models\LogonPerson $logonPerson
     */
    $logonPerson = $this->get("logonPerson");
    $media       = NULL;
    if($this->has("media")) {
        $media = $this->get("media");
    }
?>
    <h4>Beğeni Gönderme Aracı</h4>
<?php if(is_null($media)) { ?>
    <p>Reel Beğeni gönderme aracı ile, dilediğiniz gönderiye, kendi belirlediğiniz adette beğeniyi gönderebilirsiniz. Gönderilen beğenilerin tamamı gerçek kullanıcılardır.</p>
    <p>Reel Beğeni göndereceğiniz profil gizli olmamalıdır! Gizli profillerin gönderilerine ulaşılamadığından, beğeni de gönderilememektedir.</p>
    <div class="panel panel-default">
        <div class="panel-heading">
            Reel Beğeni Gönder
        </div>
        <div class="panel-body">
            <form method="post" action="?formType=findMediaID" class="form">
                <div class="form-group">
                    <label>Gönderi Url'si:</label>
                    <input type="text" name="mediaUrl" class="form-control" placeholder="https://www.instagram.com/reel/" required>
                </div>
                <button type="submit" class="btn btn-success">Gönderiyi Bul</button>
            </form>
        </div>
    </div>
<?php } elseif($media["items"][0]["user"]["is_private"] == 1) { ?>
    <hr/>
    <p class="text-danger">Uppps! Bu gönderiyi paylaşan profil gizli. Gizli profillerin gönderilerine ulaşılamadığından, beğeni de gönderilememektedir.</p>
<?php } else { ?>
    <p><strong>Kalan İşlem Hakkınız:</strong> <?php echo $logonPerson->member->gunlukBegeniLimitLeft; ?>
    </p>
    <p>
        <strong>İşlem Başına Gönderebileceğiniz Max Beğeni:</strong> <?php echo $logonPerson->member->begeniMaxKredi; ?>
    </p>
    <div class="panel panel-default">
        <div class="panel-heading">
            Beğeni Gönder
        </div>
        <div class="panel-body">
            <form id="formBegeni" class="form">
                <div class="form-group">
                    <label>Gönderi:</label>
                    <?php $item = $media["items"][0]; ?>
                    <img src="<?php echo $item["media_type"] == 100 ? str_replace("http:", "https:", $item["carousel_media"][0]["image_versions2"]["candidates"][0]["url"]) : str_replace("http:", "https:", $item["image_versions2"]["candidates"][0]["url"]); ?>" class="img-responsive" style="max-height: 200px;"/>
                </div>
                <div class="form-group">
                    <label>Beğeni Sayısı:</label>
                    <input type="text" name="adet" class="form-control" placeholder="10" value="10">
                    <span class="help-block">Max <?php echo $logonPerson->member->begeniMaxKredi; ?> beğeni gönderebilirsiniz.</span>
                </div>
                <?php if($logonPerson->member->begeniGender === 1) { ?>
                    <div class="form-group">
                        <label>Cinsiyet:</label>
                        <select name="gender" class="form-control">
                            <option value="0">Karışık</option>
                            <option value="1">Erkek</option>
                            <option value="2">Bayan</option>
                        </select>
                    </div>
                <?php } ?>
                <input type="hidden" name="mediaID" value="<?php echo $item["id"]; ?>">
                <input type="hidden" name="mediaCode" value="<?php echo $item["code"]; ?>">
                <input type="hidden" name="userID" value="<?php echo $item["user"]["pk"]; ?>">
                <input type="hidden" name="userName" value="<?php echo $item["user"]["username"]; ?>">
                <input type="hidden" name="imageUrl" value="<?php echo $item["media_type"] == 100 ? str_replace("http:", "https:", $item["carousel_media"][0]["image_versions2"]["candidates"][0]["url"]) : str_replace("http:", "https:", $item["image_versions2"]["candidates"][0]["url"]); ?>">
                <input type="hidden" name="_method" value="POST">
                <button type="button" id="formBegeniSubmitButton" class="btn btn-success" onclick="sendBegeni();">Gönderimi Başlat</button>
            </form>
            <div class="cl10"></div>
            <div id="userList"></div>
        </div>
    </div>
<?php } ?>

<?php $this->section("section_scripts");
    $this->parent();
    if(!is_null($media) && $media["items"][0]["user"]["is_private"] != 1) { ?>
        <script type="text/javascript">
            var countBegeni, countBegeniMax, bayiIslemIDLast;

            function sendBegeni() {
                countBegeni    = 0;
                countBegeniMax = parseInt($('#formBegeni input[name=adet]').val());
                if(isNaN(countBegeniMax) || countBegeniMax <= 0) {
                    alert('Beğeni adedi girin!');
                    return false;
                }
                if(countBegeniMax > <?php echo $logonPerson->member->begeniMaxKredi; ?>) {
                    alert('Beğeni adedi max <?php echo $logonPerson->member->begeniMaxKredi; ?> olabilir!');
                    return false;
                }
                $('#formBegeniSubmitButton').html('<i class="fa fa-spinner fa-spin fa-2x"></i> Gönderimi Başlat');
                $('#formBegeni input').attr('readonly', 'readonly');
                $('#formBegeni button').attr('disabled', 'disabled');
                $('#userList').html('');
                sendBegeniRC();
            }

            function sendBegeniRC(bayiIslemID) {
                url = '?formType=send';
                if(bayiIslemID) {
                    url += '&bayiIslemID=' + bayiIslemID;
                    bayiIslemIDLast = bayiIslemID;
                }
                $.ajax({type: 'POST', dataType: 'json', url: url, data: $('#formBegeni').serialize()}).done(function(data) {
                    if(data.status == 'error') {
                        $('#userList').prepend('<p class="text-danger">' + data.message + '</p>');
                        sendBegeniComplete();
                    }
                    else {
                        for(var i = 0; i < data.users.length; i++) {
                            var user = data.users[i];
                            if(user.status == 'success') {
                                $('#userList').prepend('<p><a href="/user/' + user.instaID + '">' + user.userNick + '</a> kullanıcı denendi. Sonuç: <span class="label label-success">Başarılı</span></p>');
                                countBegeni++;
                                $('#formBegeni input[name=adet]').val(countBegeniMax - countBegeni);
                                $('#begeniKrediCount').html(data.begeniKredi);

                            }
                            else {
                                //$('#userList').prepend('<p><a href="/user/' + user.instaID + '">' + user.userNick + '</a> kullanıcı denendi. Sonuç: <span class="label label-danger">Başarısız</span></p>');
                            }
                        }
                        if(countBegeni < countBegeniMax) {
                            sendBegeniRC(data.bayiIslemID);
                        }
                        else {
                            sendBegeniComplete();
                        }
                    }
                }).fail(function() {
                    setTimeout(function() {
                        sendBegeniRC(bayiIslemIDLast);
                    }, 3000);
                });
            }

            function sendBegeniComplete() {
                $('#formBegeniSubmitButton').html('Gönderimi Başlat');
                $('#formBegeni input').removeAttr('readonly');
                $('#formBegeni button').prop("disabled", false);
                $('#formBegeni input[name=adet]').val('100');
                $('#userList').prepend('<p class="text-success">Gönderilen toplam beğeni adedi: ' + countBegeni + '</p>');
            }
        </script>
    <?php }
    $this->endSection(); ?>