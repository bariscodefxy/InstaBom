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
                <input type="hidden" name="mediaID" value="<?php echo $item["pk"]; ?>">
            </div>
            <div class="modal-footer">
                <button type="button" id="formYorumSubmitButton" class="btn btn-success" onclick="sendYorum();">Yorum Gönder</button>
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
        $('#formYorumSubmitButton').html('<i class="fa fa-spinner fa-spin fa-2x"></i> Yorum Gönder');
        $('#formYorum input').attr('readonly', 'readonly');
        $('#formYorum button').attr('disabled', 'disabled');
        $('#userList').html('');
        clearCommentedIndex = 1;
        sendYorumRC();
    }

    function sendYorumRC() {
        $.ajax({type: 'POST', dataType: 'json', url: '<?php echo Wow::get("project/adminPrefix")?>/instamis/send-comment-save', data: $('#formYorum').serialize()}).done(function(data) {
            if(data.status === 1) {
                sendYorumComplete();
            } else {
                $('#formTakipSubmitButton').html('Yorum Gönder');
                $('#formTakip input').removeAttr('readonly');
                $('#formTakip button').prop("disabled", false);
                $('#formTakip input[name=adet]').val('10');
                $('#userList').prepend('<p class="text-danger">Sistemsel bir hata oluştu lütfen tekrar deneyin.</p>');
            }
        });
    }

    function sendYorumComplete() {
        $('#formYorumSubmitButton').html('Yorum Gönder');
        $('#formYorum input').removeAttr('readonly');
        $('#formYorum button').prop("disabled", false);
        $('#userList').prepend('<p class="text-success">Gönderilen toplam yorum adedi: ' + countYorumMax + '</p>');
    }
</script>
