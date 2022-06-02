<?php
    /**
     * @var \Wow\Template\View $this
     * @var array              $model
     */
?>
<form id="formEditMedia">
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title">Medya Düzenle</h4>
</div>
<div class="modal-body">
    <div class="form-group">
        <label>Açıklama</label>
        <input class="form-control" type="text" name="aciklama" value="<?php echo isset($model["items"][0]["caption"]["text"]) ? $model["items"][0]["caption"]["text"] : ""; ?>" placeholder="Ne düşünüyorsun..">
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Vazgeç</button>
    <button type="button" class="btn btn-primary" onclick="updateMedia('<?php echo $model["items"][0]["id"]; ?>')">Kaydet</button>
</div>
</form>