<?php
    /**
     * @var \Wow\Template\View $this
     * @var array              $model
     */
?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title">Yeni Mesaj</h4>
</div>
<div class="modal-body">
    <div class="form-group">
        <label>Alıcı Ekle</label>
        <div class="input-group">
            <input type="text" class="form-control" placeholder="Kullanıcı Ara" id="searchuserstext">
            <div class="input-group-btn">
                <button class="btn btn-default" onclick="searchRecipients($('#searchuserstext').val());">
                    <i class="glyphicon glyphicon-search"></i></button>
            </div>
        </div>
        <div class="list-group" id="foundRecipients">
        </div>
    </div>
    <div class="form-group">
        <label>Alıcı(lar)</label>
        <div class="list-group" id="addedRecipients">
            <?php foreach($model["recipients"] as $rec) {
                $userInfo = $rec["user"]; ?>
                <a class="list-group-item recipient" data-recipientid="<?php echo $userInfo["pk"]; ?>" href="javascript:void(0);" onclick="$(this).remove();"><img class="img-circle" style="max-width:30px;" src="<?php echo str_replace("http:","https:",$userInfo["profile_pic_url"]); ?>"/> <?php echo $userInfo["username"]; ?>
                    <span class="badge"><i class="fa fa-remove"></i></span> </a>
            <?php } ?>
        </div>
    </div>
    <hr/>
    <div class="form-group">
        <label>Media</label>
        <?php if(empty($model["media"])) { ?>
            <p><input type="file" id="fileNewMessage"></p>
        <?php } else { ?>
            <p><img src="<?php echo str_replace("http:","https:",$model["media"]["items"][0]["image_versions2"]["candidates"][0]["url"]); ?>" style="max-height: 150px;" />
            <input type="hidden" id="mediaIDNewMessage" value="<?php echo $model["media"]["items"][0]["pk"]; ?>"></p>
        <?php } ?>
    </div>
    <hr/>
    <div class="form-group">
        <label>Mesajınız</label>
        <input class="form-control" type="text" id="strNewMessage" placeholder="Mesajınızı yazın.">
    </div>
    <hr/>
    <div class="form-group">
        <label>Gönderim Tipi</label>
        <select class="form-control" id="selectNewMessageType">
            <option value="">Grup mesajı</option>
            <option value="onetoone">Her alıcıya ayrı ayrı</option>
        </select>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Vazgeç</button>
    <button type="button" class="btn btn-primary" onclick="sendNewMessage();">Gönder</button>
</div>