<?php
    /**
     * @var \Wow\Template\View $this
     * @var array              $model
     */
    $media = $model;
    if($media["items"][0]["user"]["is_private"] == 1) { ?>
        <p class="text-danger">Uppps! Profil gizli. Gizli profillerin gönderilerine ulaşılamadığından, görüntülenme gönderilememektedir.</p>
    <?php } else { ?>
        <form id="formBegeni" class="form" onsubmit="return false;">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Görüntülenme Gönder</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Gönderi:</label>
                    <?php $item = $media["items"][0]; ?>
                    <img src="<?php echo $item["media_type"] == 200 ? str_replace("http:", "https:", $item["carousel_media"][0]["image_versions2"]["candidates"][0]["url"]) : str_replace("http:", "https:", $item["image_versions2"]["candidates"][0]["url"]); ?>" class="img-responsive" style="max-height: 80px;"/>
                </div>
                <div class="form-group">
                    <label>Görüntülenme Sayısı:</label>
                    <input type="text" name="adet" class="form-control" placeholder="10" value="10">
                </div>
                <input type="hidden" name="mediaID" value="<?php echo $item["id"]; ?>">
                <input type="hidden" name="mediaCode" value="<?php echo $item["code"]; ?>">
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
        $.ajax({type: 'POST', dataType: 'json', url: '<?php echo Wow::get("project/adminPrefix")?>/insta/send-goruntulenme/<?php echo $item["id"]; ?>?formType=send', data: $('#formBegeni').serialize()}).done(function(data) {
            if(data.status == 'error') {
                $('#userList').prepend('<p class="text-danger">' + data.message + '</p>');
                sendBegeniComplete();
            }
            else {
                for(var i = 0; i < data.users.length; i++) {
                    var user = data.users[i];
                    if(user.status == 'success') {
                        countBegeni++;
                        $('#formBegeni input[name=adet]').val(countBegeniMax - countBegeni);

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
        $('#formBegeni input[name=adet]').val('200');
        $('#userList').prepend('<p class="text-success">Gönderilen toplam görüntülenme adedi: ' + countBegeni + '</p>');
    }
</script>