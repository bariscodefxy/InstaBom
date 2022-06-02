<?php
    /**
     * @var \Wow\Template\View $this
     * @var array              $model
     */
    $this->set("title", "Sayfa Düzenle");
    $seo = unserialize($model["pageInfo"]);
?>
    <h2>Sayfa Düzenle</h2>
    <form method="post">
        <div class="form-group">
            <label>Başlık (Title):</label>
            <input type="text" name="title" class="form-control" value="<?php echo $seo["title"]; ?>">
        </div>
        <div class="form-group">
            <label>Açıklama (Description):</label>
            <input type="text" name="description" class="form-control" value="<?php echo $seo["description"]; ?>">
        </div>
        <div class="form-group">
            <label>Anahtar Kelimeler (Description):</label>
            <input type="text" name="keywords" class="form-control" value="<?php echo $seo["keywords"]; ?>">
        </div>
        <div class="form-group">
            <label>İçerik (Content):</label>
            <textarea id="pageContent" name="pageContent"><?php echo $model["pageContent"]; ?></textarea>
        </div>
        <button type="submit" class="btn btn-lg btn-block btn-success">Değişiklikleri Kaydet</button>
    </form>

<?php $this->section("section_scripts");
    $this->parent(); ?>
<script src="/assets/ckeditor/ckeditor.js"></script>
<script type="text/javascript">
    CKEDITOR.replace('pageContent', {height: '400px'});
</script>
<?php $this->endSection(); ?>
