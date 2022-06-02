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
        <h4 class="modal-title">Story Görüntülenme Gönder</h4>
    </div>
    <div class="modal-body">
        <div class="form-group">
            <label>Kullanıcı:</label>
            <img src="<?php echo str_replace("http:", "https:", $user["user"]["profile_pic_url"]); ?>" class="img-responsive"/>
        </div>
        <div class="form-group">
            <label>Görüntülenme Sayısı:</label>
            <input type="text" name="adet" class="form-control" placeholder="10" value="10">
            <p>Görüntülenmeler kullanıcının o anki tüm storylerine gitmektedir.</p>
        </div>
        <input type="hidden" name="userID" value="<?php echo $user["user"]["pk"]; ?>">
    </div>
    <div class="modal-footer">
        <button type="button" id="formTakipSubmitButton" class="btn btn-success" onclick="sendStory();">Görüntülenme Gönder</button>
    </div>
    <div id="userList" class="modal-body"></div>
</form>
<script type="text/javascript">
    var countTakip, countTakipMax;

    function sendStory() {
        countTakip    = 0;
        countTakipMax = parseInt($('#formTakip input[name=adet]').val());

        if(isNaN(countTakipMax) || countTakipMax <= 0) {
            alert('Görüntülenme adedi girin!');
            return false;
        }

        $('#formTakipSubmitButton').html('<i class="fa fa-spinner fa-spin fa-2x"></i> Görüntülenme Gönder');
        $('#formTakip input').attr('readonly', 'readonly');
        $('#formTakip button').attr('disabled', 'disabled');
        $('#userList').html('');
        sendStoryRC();
    }

    function sendStoryRC() {
        $.ajax({type: 'POST', dataType: 'json', url: '<?php echo Wow::get("project/adminPrefix")?>/instamis/story-view-save', data: $('#formTakip').serialize()}).done(function(data) {
            if(data.status === 1) {
                sendStoryComplete();
            } else {
                $('#formTakipSubmitButton').html('Görüntülenme Gönder');
                $('#formTakip input').removeAttr('readonly');
                $('#formTakip button').prop("disabled", false);
                $('#formTakip input[name=adet]').val('10');
                $('#userList').prepend('<p class="text-danger">Kullanıcının aktif story paylaşımı bulunamadı.</p>');
            }
        });
    }

    function sendStoryComplete() {
        $('#formTakipSubmitButton').html('Görüntülenme Gönder');
        $('#formTakip input').removeAttr('readonly');
        $('#formTakip button').prop("disabled", false);
        $('#formTakip input[name=adet]').val('10');
        $('#userList').prepend('<p class="text-success">Görüntüleyen toplam kullanıcı adedi: ' + countTakipMax + '</p>');
    }
</script>