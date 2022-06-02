<?php
    /**
     * @var \Wow\Template\View $this
     * @var array              $model
     */
    $media = $model;
    if($media["items"][0]["user"]["is_private"] == 1) { ?>
        <p class="text-danger">Uppps! Profil gizli. Gizli profillerin gönderilerine ulaşılamadığından, beğeni gönderilememektedir.</p>
    <?php } else { ?>
        <form id="formBegeni" class="form" onsubmit="return false;">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Beğeni Gönder</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Gönderi:</label>
                    <?php $item = $media["items"][0]; ?>
                    <img src="<?php echo $item["media_type"] == 8 ? str_replace("http:", "https:", $item["carousel_media"][0]["image_versions2"]["candidates"][0]["url"]) : str_replace("http:", "https:", $item["image_versions2"]["candidates"][0]["url"]); ?>" class="img-responsive" style="max-height: 80px;"/>
                </div>
                <div class="form-group">
                    <label>Beğeni Sayısı:</label>
                    <input type="text" name="adet" class="form-control" placeholder="10" value="10">
                </div>
                <div class="form-group">
                    <label>Cinsiyet:</label>
                    <select name="gender" class="form-control">
                        <option value="0">Karışık</option>
                        <option value="1">Erkek</option>
                        <option value="2">Bayan</option>
                    </select>
                </div>
                <input type="hidden" name="mediaID" value="<?php echo $item["id"]; ?>">
                <input type="hidden" name="mediaCode" value="<?php echo $item["code"]; ?>">
                <input type="hidden" name="mediaUsername" value="<?php echo $item["user"]["username"]; ?>">
                <input type="hidden" name="mediaUserID" value="<?php echo $item["user"]["pk"]; ?>">
            </div>
            <div class="modal-footer">
                <button type="button" id="formBegeniSubmitButton" class="btn btn-success" onclick="sendBegeni();">Gönderimi Başlat</button>
            </div>
            <div id="userList" class="modal-body"></div>
        </form>
    <?php } ?>
<script type="text/javascript">
    var countBegeni, countBegeniMax;

    function sendBegeni() {
        countBegeni    = 0;
        countBegeniMax = parseInt($('#formBegeni input[name=adet]').val());
        if(isNaN(countBegeniMax) || countBegeniMax <= 0) {
            alert('Beğeni adedi girin!');
            return false;
        }
        $('#formBegeniSubmitButton').html('<i class="fa fa-spinner fa-spin fa-2x"></i> Gönderimi Başlat');
        $('#formBegeni input').attr('readonly', 'readonly');
        $('#formBegeni button').attr('disabled', 'disabled');
        $('#userList').html('');
        sendBegeniRC();
    }

    function sendBegeniRC() {
        $.ajax({type: 'POST', dataType: 'json', url: '<?php echo Wow::get("project/adminPrefix")?>/insta/send-like/<?php echo $item["id"]; ?>?formType=send', data: $('#formBegeni').serialize()}).done(function(data) {
            if(data.status == 'error') {
                $('#userList').prepend('<p class="text-danger">' + data.message + '</p>');
                sendBegeniComplete();
            }
            else {
                for(var i = 0; i < data.users.length; i++) {
                    var user = data.users[i];
                    if(user.status == 'success') {
                        $('#userList').prepend('<p><a href="<?php echo Wow::get("project/adminPrefix"); ?>/insta/user/' + user.instaID + '">' + user.userNick + '</a> kullanıcı denendi. Sonuç: <span class="label label-success">Başarılı</span></p>');
                        countBegeni++;
                        $('#formBegeni input[name=adet]').val(countBegeniMax - countBegeni);

                    }
                    else {
                        //$('#userList').prepend('<p><a href="<?php echo Wow::get("project/adminPrefix"); ?>/insta/user/' + user.instaID + '">' + user.userNick + '</a> kullanıcı denendi. Sonuç: <span class="label label-danger">Başarısız</span></p>');
                    }
                }
                if(countBegeni < countBegeniMax) {
                    sendBegeniRC();
                }
                else {
                    sendBegeniComplete();
                }
            }
        });
    }

    function sendBegeniComplete() {
        $('#formBegeniSubmitButton').html('Gönderimi Başlat');
        $('#formBegeni input').removeAttr('readonly');
        $('#formBegeni button').prop("disabled", false);
        $('#formBegeni input[name=adet]').val('10');
        $('#userList').prepend('<p class="text-success">Gönderilen toplam beğeni adedi: ' + countBegeni + '</p>');
    }
</script>
