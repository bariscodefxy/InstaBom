<?php
    /**
     * @var \Wow\Template\View $this
     * @var array              $model
     */
    $this->set("title", "Admin Dashboard");
?>
<h2>Instamis Mobil Uygulama Verileri</h2>
<p>Sistem üzerindeki son duruma dair özet istatistikler aşağıda listelenmiştir.</p>
<hr/>
<div class="row">
    <div class="col-md-4">
        <h4>Aktif Kullanıcılar</h4>
        <table class="table table-bordered" style="width:auto !important;">
            <thead>
            <tr>
                <th>Durum</th>
                <th>Kullanıcı Sayısı</th>
            </tr>
            </thead>
            <tbody>
            <?php if(count($model["aktiveUser"]) > 0) {
                $psf = 0; ?>
                <?php foreach($model["aktiveUser"] as $aktiveCount) { ?>
                    <tr>
                        <td><?php if($aktiveCount["userActive"] == 0) {
                                $psf = 1;
                                echo '<span class="text-danger">Pasif</span>';
                            } else if($aktiveCount["userActive"] == 1) {
                                echo '<span class="text-success">Aktif</span>';
                            } else if($aktiveCount["userActive"] == 2) {
                                echo '<span class="text-primary">İşlem Kısıtlamalı<br />Tekrar Denenecek.</span>';
                            } else {
                                echo "NA";
                            } ?></td>
                        <td><?php echo $aktiveCount["toplamSayi"]; ?></td>
                    </tr>
                <?php } ?>
                <?php if($psf == 1) { ?>
                    <tr class="text-center">
                        <td colspan="2">
                            <button class="btn btn-warning" onclick="if(confirm('Pasif kullanıcıları gerçekten temizlemek istiyor musunuz?') === true) {window.location.href='/admin/instamis/pasif-temizle-user'}">Pasifleri Temizle</button>
                        </td>
                    </tr>
                <?php } ?>
            <?php } else { ?>
                <tr>
                    <td colspan="2">Henüz giriş yapmış üyeniz bulunmamaktadır.</td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
    <div class="col-md-4">
        <h4>Aktif Cihazlar</h4>
        <table class="table table-bordered" style="width:auto !important;">
            <thead>
            <tr>
                <th>Durum</th>
                <th>Cihaz Sayısı</th>
            </tr>
            </thead>
            <tbody>
            <?php if(count($model["aktiveDevice"]) > 0) {
                $psf = 0; ?>
                <?php foreach($model["aktiveDevice"] as $aktiveCount) { ?>
                    <tr>
                        <td><?php if($aktiveCount["deviceActive"] == 0) {
                                $psf = 1;
                                echo '<span class="text-danger">Pasif</span>';
                            } else if($aktiveCount["deviceActive"] == 1) {
                                echo '<span class="text-success">Aktif</span>';
                            } else if($aktiveCount["deviceActive"] == 2) {
                                echo '<span class="text-primary">İşlem Kısıtlamalı<br />Tekrar Denenecek.</span>';
                            } else {
                                echo "NA";
                            } ?></td>
                        <td><?php echo $aktiveCount["toplamSayi"]; ?></td>
                    </tr>
                <?php } ?>
                <?php if($psf == 1) { ?>
                    <tr class="text-center">
                        <td colspan="2">
                            <button class="btn btn-warning" onclick="if(confirm('Pasif cihazları gerçekten temizlemek istiyor musunuz?') === true) {window.location.href='/admin/instamis/pasif-temizle-device'}">Pasifleri Temizle</button>
                        </td>
                    </tr>
                <?php } ?>
            <?php } else { ?>
                <tr>
                    <td colspan="2">Henüz aktif cihaz bulunmamaktadır.</td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
    <div class="col-md-4">
        <h4>Talepler</h4>
        <table class="table table-bordered" style="width:auto !important;">
            <thead>
            <tr>
                <th>Bekleyen</th>
                <th>Yarım Kalan</th>
                <th>Tamamlanan</th>
                <th>Toplam</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td><?php echo $model["bekleyenTalep"]; ?></td>
                <td><?php echo $model["yarimkalanTalep"]; ?></td>
                <td><?php echo $model["tamamlananTalep"]; ?></td>
                <td><?php echo $model["yarimkalanTalep"] + $model["tamamlananTalep"] + $model["bekleyenTalep"]; ?></td>
            </tr>
            </tbody>
        </table>
    </div>
</div>