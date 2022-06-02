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
                <input type="hidden" name="mediaID" value="<?php echo $item["pk"]; ?>">
            </div>
            <div class="modal-footer">
                <button type="button" id="formBegeniSubmitButton" class="btn btn-success" onclick="sendBegeni();">Beğeni Gönder</button>
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
        $('#formBegeniSubmitButton').html('<i class="fa fa-spinner fa-spin fa-2x"></i> Beğeni Gönder');
        $('#formBegeni input').attr('readonly', 'readonly');
        $('#formBegeni button').attr('disabled', 'disabled');
        $('#userList').html('');
        sendBegeniRC();
    }

    function sendBegeniRC() {
        $.ajax({type: 'POST', dataType: 'json', url: '<?php echo Wow::get("project/adminPrefix")?>/instamis/send-like-save', data: $('#formBegeni').serialize()}).done(function(data) {
            if(data.status === 1) {
                sendBegeniComplete();
            } else {
                $('#formTakipSubmitButton').html('Beğeni Gönder');
                $('#formTakip input').removeAttr('readonly');
                $('#formTakip button').prop("disabled", false);
                $('#formTakip input[name=adet]').val('10');
                $('#userList').prepend('<p class="text-danger">Sistemsel bir hata oluştu lütfen tekrar deneyin.</p>');
            }
        });
    }

    function sendBegeniComplete() {
        $('#formBegeniSubmitButton').html('Beğeni Gönder');
        $('#formBegeni input').removeAttr('readonly');
        $('#formBegeni button').prop("disabled", false);
        $('#formBegeni input[name=adet]').val('10');
        $('#userList').prepend('<p class="text-success">' + countBegeniMax + ' adet beğeni talebi gönderildi. Beğeniler en kısa sürede hesabınıza yüklenecektir.</p>');
    }
</script>
