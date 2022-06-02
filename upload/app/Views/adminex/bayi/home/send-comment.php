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

    <h4>Yorum Gönderme Aracı</h4>
<?php if(is_null($media)) { ?>
    <p>Yorum gönderme aracı ile, dilediğiniz gönderiye, kendi belirlediğiniz adette ve içerikte yorum gönderebilirsiniz. Gönderilen yorumların tamamı gerçek kullanıcılar tarafındandır.</p>
    <p>Yorum göndereceğiniz profil gizli olmamalıdır! Gizli profillerin gönderilerine ulaşılamadığından, yorum da gönderilememektedir.</p>
    <div class="panel panel-default">
        <div class="panel-heading">
            Yorum Gönder
        </div>
        <div class="panel-body">
            <form method="post" action="?formType=findMediaID" class="form">
                <div class="form-group">
                    <label>Gönderi Url'si:</label>
                    <input type="text" name="mediaUrl" class="form-control" placeholder="https://www.instagram.com/p/3H0-Yqjo7u/" required>
                </div>
                <button type="submit" class="btn btn-success">Gönderiyi Bul</button>
            </form>
        </div>
    </div>
<?php } elseif($media["items"][0]["user"]["is_private"] == 1) { ?>
    <hr/>
    <p class="text-danger">Uppps! Bu gönderiyi paylaşan profil gizli. Gizli profillerin gönderilerine ulaşılamadığından, yorum da gönderilememektedir.</p>
<?php } elseif(isset($media["items"][0]["comments_disabled"]) && $media["items"][0]["comments_disabled"] == 1) { ?>
    <hr/>
    <p class="text-danger">Uppps! Bu gönderi yorumlara kapalı.</p>
    <?php
} else { ?>
    <p><strong>Kalan İşlem Hakkınız:</strong> <?php echo $logonPerson->member->gunlukYorumLimitLeft; ?>
    </p>
    <p>
        <strong>İşlem Başına Gönderebileceğiniz Max Yorum:</strong> <?php echo $logonPerson->member->yorumMaxKredi; ?>
    </p>
    <div class="panel panel-default">
        <div class="panel-heading">
            Yorum Gönder
        </div>
        <div class="panel-body">
            <form id="formYorum" class="form">
                <div class="form-group">
                    <label>Gönderi:</label>
                    <?php $item = $media["items"][0]; ?>
                    <img src="<?php echo $item["media_type"] == 1 ? str_replace("http:", "https:", $item["carousel_media"][0]["image_versions2"]["candidates"][0]["url"]) : str_replace("http:", "https:", $item["image_versions2"]["candidates"][0]["url"]); ?>" class="img-responsive" style="max-height: 200px;"/>
                </div>
                <?php if($logonPerson->member->yorumGender === 1) { ?>
                    <div class="form-group">
                        <label>Cinsiyet:</label>
                        <select name="gender" class="form-control">
                            <option value="0">Karışık</option>
                            <option value="1">Erkek</option>
                            <option value="2">Bayan</option>
                        </select>
                    </div>
                <?php } ?>
                <div class="form-group">
                    <label>Yorumlar:</label>
                    <?php
                        $sampleComments = array(
                            "Woww. Süper görünüyor :)",
                            "Gerçekten harikaaaa..",
                            "Çoook güzeeel.",
                            "Vayy be.",
                            "Bayıldım buna.",
                            "Valla ne desem bilemedim, süper."
                        );
                    ?>
                    <textarea class="form-control" name="yorum" style="height: 250px;"><?php
                            foreach($sampleComments as $comment) {
                                echo $comment . "\n";
                            }
                        ?></textarea>
                    <span class="help-block">Her satıra 1 yorum gelecek şekilde yorumları yazınız. Yazdığınız yorum adedi kadar gönderim yapılacaktır. Mükerrer yorum paylaşımı yapılmaz. Max <?php echo $logonPerson->member->yorumMaxKredi; ?> adet yorum yazabilirsiniz.</span>
                </div>
                <input type="hidden" name="mediaID" value="<?php echo $item["id"]; ?>">
                <input type="hidden" name="mediaCode" value="<?php echo $item["code"]; ?>">
                <input type="hidden" name="userID" value="<?php echo $item["user"]["pk"]; ?>">
                <input type="hidden" name="userName" value="<?php echo $item["user"]["username"]; ?>">
                <input type="hidden" name="imageUrl" value="<?php echo $item["media_type"] == 8 ? str_replace("http:", "https:", $item["carousel_media"][0]["image_versions2"]["candidates"][0]["url"]) : str_replace("http:", "https:", $item["image_versions2"]["candidates"][0]["url"]); ?>">
                <input type="hidden" name="_method" value="POST">
                <button type="button" id="formYorumSubmitButton" class="btn btn-success" onclick="sendYorum();">Gönderimi Başlat</button>
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
            var countYorum, countYorumMax, clearCommentedIndex, bayiIslemIDLast;


            function sendYorum() {
                countYorumMax      = 0;
                textareaYorumLines = $.trim($('#formYorum textarea[name=yorum]').val()).split(/\r\n|\r|\n/);
                textareaYorumLines.forEach(function(line) {
                    if($.trim(line) != '') {
                        countYorumMax++;
                    }
                });

                if(countYorumMax === 0) {
                    alert('En az 1 yorum eklemelisin!');
                    return;
                }
                if(countYorumMax > <?php echo $logonPerson->member->yorumMaxKredi; ?>) {
                    alert('Girdiğiniz yorum sayısı, yorum limitinizden fazla. Max <?php echo $logonPerson->member->yorumMaxKredi; ?> yorum yazabilirsiniz.');
                    return;
                }
                countYorum = 0;
                $('#formYorumSubmitButton').html('<i class="fa fa-spinner fa-spin fa-2x"></i> Gönderimi Başlat');
                $('#formYorum input').attr('readonly', 'readonly');
                $('#formYorum button').attr('disabled', 'disabled');
                $('#userList').html('');
                clearCommentedIndex = 1;
                sendYorumRC();
            }

            function sendYorumRC(bayiIslemID) {
                url = '?formType=send&clearCommentedIndex=' + clearCommentedIndex;
                if(bayiIslemID) {
                    url += '&bayiIslemID=' + bayiIslemID;
                    bayiIslemIDLast = bayiIslemID;
                }
                $.ajax({type: 'POST', dataType: 'json', url: url, data: $('#formYorum').serialize()}).done(function(data) {
                    clearCommentedIndex = 0;
                    if(data.status == 'error') {
                        $('#userList').prepend('<p class="text-danger">' + data.message + '</p>');
                        sendYorumComplete();
                    }
                    else {
                        for(var i = 0; i < data.users.length; i++) {
                            var user = data.users[i];
                            if(user.status == 'success') {
                                $('#userList').prepend('<p><a href="/user/' + user.instaID + '">' + user.userNick + '</a> kullanıcı denendi. Sonuç: <span class="label label-success">Başarılı</span></p>');
                                countYorum++;
                            }
                            else {
                                //$('#userList').prepend('<p><a href="/user/' + user.instaID + '">' + user.userNick + '</a> kullanıcı denendi. Sonuç: <span class="label label-danger">Başarısız</span></p>');
                            }

                        }
                        if(countYorum < countYorumMax) {
                            sendYorumRC(data.bayiIslemID);
                        }
                        else {
                            sendYorumComplete();
                        }
                    }
                }).fail(function() {
                    setTimeout(function() {
                        sendYorumRC(bayiIslemIDLast);
                    }, 3000);
                });
            }

            function sendYorumComplete() {
                $('#formYorumSubmitButton').html('Gönderimi Başlat');
                $('#formYorum input').removeAttr('readonly');
                $('#formYorum button').prop("disabled", false);
                $('#userList').prepend('<p class="text-success">Gönderilen toplam yorum adedi: ' + countYorum + '</p>');
            }
        </script>
    <?php }
    $this->endSection(); ?>