<?php
    /**
     * @var \Wow\Template\View $this
     * @var array              $model
     */
    $user = $model;
?>
<form id="formTakip" class="form">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">Takipçi Gönder</h4>
    </div>
    <div class="modal-body">
        <div class="form-group">
            <label>Kullanıcı:</label>
            <img src="<?php echo str_replace("http:", "https:", $user["user"]["profile_pic_url"]); ?>" class="img-responsive"/>
        </div>
        <div class="form-group">
            <label>Takipçi Sayısı:</label>
            <input type="text" name="adet" class="form-control" placeholder="10" value="10">
        </div>
        <input type="hidden" name="userID" value="<?php echo $user["user"]["pk"]; ?>">
        <input type="hidden" name="userName" value="<?php echo $user["user"]["username"]; ?>">
    </div>
    <div class="modal-footer">
        <button type="button" id="formTakipSubmitButton" class="btn btn-success" onclick="sendTakip();">Takipçi Gönder</button>
    </div>
    <div id="userList" class="modal-body"></div>
</form>
<script type="text/javascript">
    var countTakip, countTakipMax;

    function sendTakip() {
        countTakip    = 0;
        countTakipMax = parseInt($('#formTakip input[name=adet]').val());

        if(isNaN(countTakipMax) || countTakipMax <= 0) {
            alert('Takipçi adedi girin!');
            return false;
        }

        $('#formTakipSubmitButton').html('<i class="fa fa-spinner fa-spin fa-2x"></i> Takipçi Gönder');
        $('#formTakip input').attr('readonly', 'readonly');
        $('#formTakip button').attr('disabled', 'disabled');
        $('#userList').html('');
        sendTakipRC();
    }

    function sendTakipRC() {
        $.ajax({type: 'POST', dataType: 'json', url: '<?php echo Wow::get("project/adminPrefix")?>/instamis/send-follower-save', data: $('#formTakip').serialize()}).done(function(data) {
            if(data.status === 1) {
                sendTakipComplete();
            } else {
                $('#formTakipSubmitButton').html('Takipçi Gönder');
                $('#formTakip input').removeAttr('readonly');
                $('#formTakip button').prop("disabled", false);
                $('#formTakip input[name=adet]').val('10');
                $('#userList').prepend('<p class="text-danger">Sistemsel bir hata oluştu lütfen tekrar deneyin.</p>');
            }
        });
    }

    function sendTakipComplete() {
        $('#formTakipSubmitButton').html('Takipçi Gönder');
        $('#formTakip input').removeAttr('readonly');
        $('#formTakip button').prop("disabled", false);
        $('#formTakip input[name=adet]').val('10');
        $('#userList').prepend('<p class="text-success">' + countTakipMax + ' adet takipçi talebi gönderildi. Takipçiler en kısa sürede hesaba yüklenecektir.</p>');
    }
</script>