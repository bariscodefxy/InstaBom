<?php
    /**
     * @var \Wow\Template\View $this
     * @var array              $model
     */
    $this->set("title", "Blog Düzenle");
?>
    <h2>Blog Düzenle</h2>
    <form method="post">
        <div class="form-group">
            <label>Başlık:</label>
            <input type="text" name="baslik" class="form-control" value="<?php echo $model["baslik"]; ?>">
        </div>
        <div class="form-group">
            <label>Resim:</label>
            <input type="text" name="anaResim" class="form-control" value="<?php echo $model["anaResim"]; ?>">
        </div>
        <div class="form-group">
            <label>Unique Link:</label>
            <input type="text" name="seoLink" class="form-control" value="<?php echo $model["seoLink"]; ?>">
        </div>
        <div class="checkbox">
            <label>
                <input type="checkbox" name="isActive" value="1"<?php echo $model["isActive"]==1 ? ' checked="checked"':''; ?>> Yayınla
            </label>
        </div>
        <div class="form-group">
            <label>İçerik:</label>
            <textarea id="icerik" name="icerik"><?php echo $model["icerik"]; ?></textarea>
        </div>
        <button type="submit" class="btn btn-lg btn-block btn-success">Değişiklikleri Kaydet</button>
    </form>

<?php $this->section("section_scripts");
    $this->parent(); ?>
<script src="/assets/ckeditor/ckeditor.js"></script>
<script type="text/javascript">
    CKEDITOR.replace('icerik', {height: '400px'});
</script>
<?php $this->endSection(); ?>
