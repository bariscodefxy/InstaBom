<?php
    /**
     * @var \Wow\Template\View $this
     * @var array              $model
     */
    $this->set("title", "Oto Yorum");
    $pagination = $this->get("pagination");
?>
<h2>Oto Yorum
    <a class="pull-right btn btn-success" href="<?php echo Wow::get("project/adminPrefix") . "/plugins/auto-comment/add"; ?>"><i class="fa fa-plus"></i> Ekle</a>
</h2>
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
                        <option value="1"<?php echo $this->request->query->isActive === "1" ? ' selected="selected"' : ''; ?>>Aktif Kayıtlar</option>
                        <option value="0"<?php echo $this->request->query->isActive === "0" ? ' selected="selected"' : ''; ?>>Tamamlananlar</option>
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
                <th colspan="2">Gönderi</th>
                <th>Durum</th>
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
                        <a href="<?php echo Wow::get("project/adminPrefix"); ?>/insta/user/<?php echo $paket["userID"]; ?>">@<?php echo $paket["userName"]; ?></a>
                    </td>
                    <td><?php
                            if($paket["commentCountLeft"] > 0) {
                                echo '<label class="label label-warning">Kalan ' . $paket["commentCountLeft"] . ' / ' . $paket["commentCountTotal"] . '</label>';
                            } else {
                                echo '<label class="label label-success">Tamamlandı / ' . $paket["commentCountTotal"] . '</label>';
                            }
                        ?></td>
                    <td>Limitler: <?php echo $paket["krediDelay"]; ?> yorum / <?php echo $paket["minuteDelay"]; ?> dakika<br/>
                        Cinsiyet: <?php switch($paket["gender"]) {
                            case 1:
                                echo '<label class="label" style="background-color: pink;">Kadın</label>';
                                break;
                            case 2:
                                echo '<label class="label" style="background-color: blue;">Erkek</label>';
                                break;
                            default:
                                echo "Karışık";
                        } ?></td>
                    <td>
                        <a href="#modalPackageDetails" data-toggle="modal" class="btn btn-primary btn-sm" onclick="getPackageDetails(<?php echo $paket["id"]; ?>);">Düzenle</a>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
    <?php $this->renderView("shared/pagination", $this->get("pagination")); ?>
<?php } else { ?>
    <p>Henüz kayıt yok!</p>
<?php } ?>


<?php $this->section("section_scripts");
    $this->parent(); ?>
<script type="text/javascript">
    function getPackageDetails(id) {
        $('#modalPackageDetailsInner').html('<div class="modal-body"><h2>Bekleyin..</h2></div>');
        $.ajax({url: '<?php echo Wow::get("project/adminPrefix"); ?>/plugins/auto-comment/edit/' + id, type: 'GET'}).done(function(data) {
            $('#modalPackageDetailsInner').html(data);
        });
    }
</script>
<?php $this->endSection(); ?>

<?php $this->section("section_modals");
    $this->parent(); ?>
<div class="modal fade" id="modalPackageDetails" style="z-index: 1051;">
    <div class="modal-dialog">
        <div class="modal-content" id="modalPackageDetailsInner">
        </div>
    </div>
</div>
<?php $this->endSection(); ?>
