<?php
    /**
     * @var \Wow\Template\View $this
     * @var array              $model
     */
    $this->set("title", "Oto Beğeni Paketleri");
    $pagination = $this->get("pagination");
?>
<h2>INSTAMIS - Oto Beğeni Paketleri</h2>
<div class="panel panel-default">
    <div class="panel-heading">Filtrele</div>
    <div class="panel-body">
        <form method="get">
            <div class="row">
                <div class="col-md-3">
                    <label>Ara</label>
                    <input type="text" name="q" value="<?php echo $this->e($this->request->query->q); ?>" class="form-control" placeholder="Username">
                </div>
                <div class="col-md-3">
                    <label>Aktiflik</label>
                    <select class="form-control" name="isActive">
                        <option value="">Tümü</option>
                        <option value="1"<?php echo $this->request->query->isActive === "1" ? ' selected="selected"' : ''; ?>>Aktifler</option>
                        <option value="0"<?php echo $this->request->query->isActive === "0" ? ' selected="selected"' : ''; ?>>Pasifler</option>
                        <option value="2"<?php echo $this->request->query->isActive === "2" ? ' selected="selected"' : ''; ?>>Süresi Bitenler</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label>&nbsp;</label>
                    <button type="submit" class="btn btn-success form-control">Filtrele</button>
                </div>
            </div>
        </form>
    </div>
</div>
<?php if(!empty($model)) { ?>
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
            <tr>
                <th>ID</th>
                <th colspan="2">Insta Hesabı</th>
                <th>Durum</th>
                <th>Başlama - Bitiş</th>
                <th>Detaylar</th>
                <th>İşlem</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach($model as $paket) { ?>
                <tr>
                    <td><?php echo $paket["id"]; ?></td>
                    <td><img style="max-width: 50px;" src="<?php echo $paket["imageUrl"]; ?>"/></td>
                    <td>
                        <a href="<?php echo Wow::get("project/adminPrefix"); ?>/instamis/user/<?php echo $paket["instaID"]; ?>">@<?php echo $paket["userName"]; ?></a>
                    </td>
                    <td><?php switch($paket["isActive"]) {
                            case 0:
                                echo '<label class="label label-danger">Pasif</label>';
                                break;
                            case 2:
                                echo '<label class="label label-warning">Süresi Bitti</label>';
                                break;
                            case 1:
                                echo '<label class="label label-success">Aktif</label>';
                                break;
                            default:
                                echo '<label class="label label-primary">NA</label>';
                        } ?></td>
                    <td><?php echo date("d.m.Y", strtotime($paket["startDate"])) . " - " . date("d.m.Y", strtotime($paket["endDate"])); ?></td>
                    <td>Gönderi Başına Beğeni: <?php echo $paket["likeCountMin"]; ?> - <?php echo $paket["likeCountMax"]; ?></td>
                    <td>
                        <a href="#modalPackageDetails" data-toggle="modal" class="btn btn-primary btn-sm" onclick="getLikePackageDetails(<?php echo $paket["id"]; ?>);">Düzenle</a>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
    <?php $this->renderView("shared/pagination", $this->get("pagination")); ?>
<?php } else { ?>
    <p>Henüz oto beğeni paketi yok!</p>
<?php } ?>

<?php $this->section("section_scripts");
    $this->parent(); ?>
<script type="text/javascript">
    function getLikePackageDetails(id) {
        $('#modalPackageDetailsInner').html('<div class="modal-body"><h2>Bekleyin..</h2></div>');
        $.ajax({url: '<?php echo Wow::get("project/adminPrefix"); ?>/instamis/edit-auto-like-package/' + id, type: 'GET'}).done(function(data) {
            $('#modalPackageDetailsInner').html(data);
        });
    }
</script>
<?php $this->endSection(); ?>

<?php $this->section("section_modals");
    $this->parent(); ?>
<div class="modal fade" id="modalPackageDetails" style="z-index: 1051;">
    <div class="modal-dialog">
        <div class="modal-content" id="modalPackageDetailsInner"></div>
    </div>
</div>
<?php $this->endSection(); ?>
