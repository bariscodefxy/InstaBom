<?php
    /**
     * @var \Wow\Template\View $this
     * @var array              $model
     */
    $media = $model;
    if($media["items"][0]["user"]["is_private"] == 1) { ?>
        <p class="text-danger">Uppps! Profil gizli. Gizli profillerin gönderilerine ulaşılamadığından, yorum gönderilememektedir.</p>
    <?php } elseif(isset($media["items"][0]["comments_disabled"]) && $media["items"][0]["comments_disabled"] == 1) {
        ?>
        <p class="text-danger">Uppps! Bu gönderi yorumlara kapalı.</p>
    <?php } else { ?>
        <form id="formYorum" class="form">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Yorum Gönder</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Gönderi:</label>
                    <?php $item = $media["items"][0]; ?>
                    <img src="<?php echo $item["media_type"] == 8 ? str_replace("http:", "https:", $item["carousel_media"][0]["image_versions2"]["candidates"][0]["url"]) : str_replace("http:", "https:", $item["image_versions2"]["candidates"][0]["url"]); ?>" class="img-responsive" style="max-height: 80px;"/>
                </div>
                <div class="form-group">
                    <label>Cinsiyet:</label>
                    <select name="gender" class="form-control">
                        <option value="0">Karışık</option>
                        <option value="1">Erkek</option>
                        <option value="2">Bayan</option>
                    </select>
                </div>
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
                    <textarea class="form-control" name="yorum" style="height: 160px;"><?php
                            foreach($sampleComments as $comment) {
                                echo $comment . "\n";
                            }
                        ?></textarea>
                    <span class="help-block">Her satıra 1 yorum gelecek şekilde yorumları yazınız. Yazdığınız yorum adedi kadar gönderim yapılacaktır. Mükerrer yorum paylaşımı yapılmaz.</span>
                </div>
                <input type="hidden" name="mediaID" value="<?php echo $item["id"]; ?>">
                <input type="hidden" name="mediaCode" value="<?php echo $item["code"]; ?>">
            </div>
            <div class="modal-footer">
                <button type="button" id="formYorumSubmitButton" class="btn btn-success" onclick="sendYorum();">Gönderimi Başlat</button>
            </div>
            <div id="userList" class="modal-body"></div>
        </form>
    <?php } ?>
<script type="text/javascript">
    var countYorum, countYorumMax, clearCommentedIndex;

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
        countYorum = 0;
        $('#formYorumSubmitButton').html('<i class="fa fa-spinner fa-spin fa-2x"></i> Gönderimi Başlat');
        $('#formYorum input').attr('readonly', 'readonly');
        $('#formYorum button').attr('disabled', 'disabled');
        $('#userList').html('');
        clearCommentedIndex = 1;
        sendYorumRC();
    }

    function sendYorumRC() {
        $.ajax({type: 'POST', dataType: 'json', url: '<?php echo Wow::get("project/adminPrefix")?>/insta/send-comment/<?php echo $item["id"]; ?>?formType=send&clearCommentedIndex=' + clearCommentedIndex, data: $('#formYorum').serialize()}).done(function(data) {
            clearCommentedIndex = 0;
            if(data.status == 'error') {
                $('#userList').prepend('<p class="text-danger">' + data.message + '</p>');
                sendYorumComplete();
            }
            else {
                for(var i = 0; i < data.users.length; i++) {
                    var user = data.users[i];
                    if(user.status == 'success') {
                        $('#userList').prepend('<p><a href="<?php echo Wow::get("project/adminPrefix"); ?>/insta/user/' + user.instaID + '">' + user.userNick + '</a> kullanıcı denendi. Sonuç: <span class="label label-success">Başarılı</span></p>');
                        countYorum++;
                        $('#yorumKrediCount').html(data.yorumKredi);

                    }
                    else {
                        //$('#userList').prepend('<p><a href="<?php echo Wow::get("project/adminPrefix"); ?>/insta/user/' + user.instaID + '">' + user.userNick + '</a> kullanıcı denendi. Sonuç: <span class="label label-danger">Başarısız</span></p>');
                    }
                }
                if(countYorum < countYorumMax) {
                    sendYorumRC();
                }
                else {
                    sendYorumComplete();
                }
            }
        });
    }

    function sendYorumComplete() {
        $('#formYorumSubmitButton').html('Gönderimi Başlat');
        $('#formYorum input').removeAttr('readonly');
        $('#formYorum button').prop("disabled", false);
        $('#userList').prepend('<p class="text-success">Gönderilen toplam yorum adedi: ' + countYorum + '</p>');
    }
</script>
