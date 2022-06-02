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
        <h4 class="modal-title">Canlı Yayına Kullanıcı Gönder</h4>
    </div>
    <div class="modal-body">
        <div class="form-group">
            <label>Kullanıcı:</label>
            <img src="<?php echo str_replace("http:", "https:", $user["user"]["profile_pic_url"]); ?>" class="img-responsive"/>
        </div>
        <div class="form-group">
            <label>İzleyici Sayısı:</label>
            <input type="text" name="adet" class="form-control" placeholder="10" value="10">
        </div>
        <div class="form-group">
            <label>Kaç Dk:</label>
            <input type="text" name="dakika" class="form-control" placeholder="1" value="1">
        </div>
        <div class="form-group">
            <label>Cinsiyet:</label>
            <select name="gender" class="form-control">
                <option value="0">Karışık</option>
                <option value="1">Erkek</option>
                <option value="2">Bayan</option>
            </select>
        </div>
        <input type="hidden" name="userID" value="<?php echo $user["user"]["pk"]; ?>">
        <input type="hidden" name="userName" value="<?php echo $user["user"]["username"]; ?>">
    </div>
    <div class="modal-footer">
        <button type="button" id="formTakipSubmitButton" class="btn btn-success" onclick="sendTakip();">Gönderimi Başlat</button>
    </div>
    <div id="userList" class="modal-body"></div>
</form>
<script type="text/javascript">
    var countTakip, countTakipMax;

    function sendTakip() {
        countTakip    = 0;
        countTakipMax = parseInt($('#formTakip input[name=adet]').val());

        if(isNaN(countTakipMax) || countTakipMax <= 0) {
            alert('Görüntülenme adedi girin!');
            return false;
        }

        $('#formTakipSubmitButton').html('<i class="fa fa-spinner fa-spin fa-2x"></i> Gönderimi Başlat');
        $('#formTakip input').attr('readonly', 'readonly');
        $('#formTakip button').attr('disabled', 'disabled');
        $('#userList').html('');
        sendTakipRC();
    }

    function sendTakipRC() {
        $.ajax({type: 'POST', dataType: 'json', url: '<?php echo Wow::get("project/adminPrefix")?>/insta/canli-yayin/<?php echo $user["user"]["pk"]; ?>?formType=send', data: $('#formTakip').serialize()}).done(function(data) {

            if(data.status == 'error') {
                $('#userList').prepend('<p class="text-danger">' + data.message + '</p>');
                sendTakipComplete();
            } else {
                sendTakipComplete();
            }
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