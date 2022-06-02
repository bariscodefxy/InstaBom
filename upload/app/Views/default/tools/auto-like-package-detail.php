<?php
    /**
     * @var \Wow\Template\View $this
     * @var array              $model
     */
    $paket      = $model["paket"];
    $paketdetay = $model["paketdetay"];
?>
<ul class="nav nav-tabs nav-justified nav-tabs-justified">
    <li class="active"><a href="#tabLikePackageDetails" data-toggle="tab">Paket Detayları</a></li>
    <li><a href="#tabLikePackageHistory" data-toggle="tab">Gönderim Geçmişi</a></li>
</ul>
<div id="myTabContent" class="tab-content">
    <div class="tab-pane fade active in" id="tabLikePackageDetails">
        <div class="modal-body">
            <div class="form-group">
                <label>Başlama Tarihi</label>
                <p><?php echo date("d.m.Y H:i:s", strtotime($paket["startDate"])); ?></p>
            </div>
            <div class="form-group">
                <label>Sona Erme Tarihi</label>
                <p><?php echo date("d.m.Y H:i:s", strtotime($paket["endDate"])); ?></p>
            </div>
            <div class="form-group">
                <label>Gönderi Başına Beğeni Adedi</label>
                <p><?php echo $paket["likeCount"]; ?></p>
            </div>
            <div class="form-group">
                <label>Durum</label>
                <p><?php switch($paket["isActive"]) {
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
                    } ?></p>
            </div>
        </div>
    </div>
    <div class="tab-pane fade" id="tabLikePackageHistory">
        <div class="modal-body">
            <?php if(empty($paketdetay)) { ?>
                <p class="text-primary">Henüz veri yok!</p>
            <?php } else { ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            <th>Medya</th>
                            <th>Gönderilen</th>
                            <th>Kalan</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach($paketdetay as $item) { ?>
                            <tr>
                                <td>
                                    <img style="max-height: 100px;" src="<?php echo str_replace("http:", "https:", $item["imageUrl"]); ?>"/>
                                </td>
                                <td>
                                    <label class="label label-success"><?php echo intval($item["likeCountTotal"]) - intval($item["likeCountLeft"]); ?></label>
                                </td>
                                <td>
                                    <label class="label label-warning"><?php echo intval($item["likeCountLeft"]); ?></label>
                                </td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            <?php } ?>
        </div>
    </div>
</div>