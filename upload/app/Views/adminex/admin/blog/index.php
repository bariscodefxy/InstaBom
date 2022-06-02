<?php
    /**
     * @var \Wow\Template\View $this
     * @var array              $model
     */
    $this->set("title", "Blog İçerikleri");
?>
        <h2>Blog
            <a class="pull-right btn btn-success" href="#modalNewBlog" data-toggle="modal"><i class="fa fa-plus"></i> İçerik Ekle</a>
        </h2>
        <div class="clearfix"></div>
        <?php if(!empty($model)) { ?>
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Başlık</th>
                        <th>Düzenle</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach($model as $blog) { ?>
                        <tr>
                            <td><?php echo $blog["blogID"]; ?></td>
                            <td><?php echo $blog["baslik"]; ?></td>
                            <td>
                                <a class="btn btn-sm btn-primary" href="<?php echo Wow::get("project/adminPrefix"); ?>/blog/blog-duzenle/<?php echo $blog["blogID"]; ?>"><i class="fa fa-edit"></i> Düzenle</a>
                                <a class="btn btn-sm btn-danger" href="?deleteBlogID=<?php echo $blog["blogID"]; ?>"><i class="fa fa-remove"></i> Sil</a>
                            </td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
        <?php } else { ?>
            <p>Henüz eklenmiş bir blog yok!</p>
        <?php } ?>

<?php $this->section("section_modals");
    $this->parent(); ?>
    <div class="modal fade" id="modalNewBlog">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="post">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title">Blog Konusu Ekle</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Başlık:</label>
                            <input type="text" name="baslik" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Vazgeç</button>
                        <button type="submit" class="btn btn-primary">Ekle</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php $this->endSection(); ?>