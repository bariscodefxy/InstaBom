<?php
    /**
     * @var \Wow\Template\View $this
     * @var array              $model
     */
    $paket = $model;
    $this->set("title", "Oto Beğeni Paketleri");
?>
<div class="container">
    <div class="cl10"></div>
    <div class="row">
        <div class="col-sm-8 col-md-9">
            <h4 style="margin-top: 0;">Oto Beğeni Paketleri</h4>
            <p>Oto Beğeni paketleri, belirli bir süre için geçerli olan, bu süre zarfında paylaştığınız her gönderiye daha önce belirlenmiş bir adette otomatik olarak beğeni gönderen sistemin adıdır. Örneğin, Haftalık 100 Oto Beğeni Paketi dendiğinde anlamanız gereken: 1 hafta boyunca, paylaştığınız her gönderiye, sistemimiz tarafından 100 adet beğeni gönderileceğidir. Süre ve adet satın aldığınız paket çeşidine göre değişebilir.</p>
            <p>Yeni bir tane oto beğeni paketi almak için
                <a class="btn btn-success btn-xs" href="/packages">Paketler</a> sayfamızı inceleyin.</p>
            <hr/>
            <h4>Benim Oto Beğeni Paketlerim</h4>

            <?php if(empty($paket)) { ?>
                <p class="text-danger">Daha önce hiç oto beğeni paketi almamışsınız!</p>
            <?php } else { ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            <th>No</th>
                            <th>Durum</th>
                            <th>Başlama</th>
                            <th>Bitiş</th>
                            <th>Gönderi Başına Beğeni</th>
                            <th>Detay</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach($paket as $p) { ?>
                            <tr>
                                <td><?php echo $p["id"]; ?></td>
                                <td><?php switch($p["isActive"]) {
                                        case 0:
                                            echo '<label class="label label-danger">Pasif</label>';
                                            break;
                                        case 1:
                                            echo '<label class="label label-success">Aktif</label>';
                                            break;
                                        case 2:
                                            echo '<label class="label label-warning">Süre Bitti</label>';
                                            break;
                                        default:
                                            echo '<label class="label label-primary">NA</label>';
                                    } ?></td>
                                <td><?php echo date("d.m.Y H:i:s", strtotime($p["startDate"])); ?></td>
                                <td><?php echo date("d.m.Y H:i:s", strtotime($p["endDate"])); ?></td>
                                <td><?php echo $p["likeCount"]; ?></td>
                                <td>
                                    <a href="#modalPackageDetails" data-toggle="modal" class="btn btn-primary btn-sm" onclick="getLikePackageDetails(<?php echo $p["id"]; ?>);">Detay</a>
                                </td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            <?php } ?>
        </div>
        <div class="col-sm-4 col-md-3">
            <?php $this->renderView("tools/sidebar"); ?>
        </div>
    </div>
</div>

<?php $this->section("section_scripts");
    $this->parent(); ?>
<script type="text/javascript">
    function getLikePackageDetails(id) {
        $('#modalPackageDetailsInner').html('<div class="modal-body"><h2>Bekleyin..</h2></div>');
        $.ajax({url: '/tools/auto-like-packages?formType=packageDetails&paketID=' + id, type: 'POST'}).done(function(data) {
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
