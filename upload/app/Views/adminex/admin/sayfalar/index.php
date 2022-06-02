<?php
    /**
     * @var \Wow\Template\View $this
     * @var array              $model
     */
    $this->set("title", "Sayfalar");
?>
    <h2>Sayfalar</h2>
    <?php if(!empty($model)) { ?>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Sayfa Kodu</th>
                    <th>Düzenle</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach($model as $page) { ?>
                    <tr>
                        <td><?php echo $page["id"]; ?></td>
                        <td><?php echo $page["page"]; ?></td>
                        <td>
                            <a class="btn btn-sm btn-primary" href="<?php echo Wow::get("project/adminPrefix"); ?>/sayfalar/sayfa-duzenle/<?php echo $page["id"]; ?>"><i class="fa fa-edit"></i> Düzenle</a>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    <?php } else { ?>
        <p>Henüz eklenmiş bir sayfa yok!</p>
    <?php } ?>