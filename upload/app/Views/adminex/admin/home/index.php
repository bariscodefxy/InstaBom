<?php
    /**
     * @var \Wow\Template\View $this
     * @var array              $model
     */
    $this->set("title", "Admin Dashboard");
?>

<?php echo $model["SüperYenile"] ==4 ; ?>
<div id="duyurulist"></div>
<h2>Hoşgeldiniz</h2>
<p><strong>Yazılım Versiyonu: InstaBoom 4.2 Sorunsuz Script</strong> </p>
<p>Sistem üzerindeki son duruma dair özet istatistikler aşağıda listelenmiştir.</p>
<hr/>
<div class="row">
    <div class="col-md-4">
        <h4>Cinsiyete Göre</h4>
        <table class="table table-bordered" style="width:auto !important;">
            <thead>
            <tr>
                <th>Cinsiyet</th>
                <th>Kullanıcı Sayısı</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach($model["genderUser"] as $genderCount) { ?>
                <tr>
                    <td><?php if($genderCount["gender"] == 3) {
                            echo "Belirtilmemiş.";
                        } elseif($genderCount["gender"] == 1) {
                            echo "Erkek";
                        } elseif($genderCount["gender"] == 2) {
                            echo "Bayan";
                        } else {
                            echo "NA";
                        } ?></td>
                    <td><?php echo $genderCount["toplamSayi"]; ?></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
    <div class="col-md-4">
        <h4>Duruma Göre</h4>
        <table class="table table-bordered" style="width:auto !important;">
            <thead>
            <tr>
                <th>Durum</th>
                <th>Kullanıcı Sayısı</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach($model["aktiveUser"] as $aktiveCount) { ?>
                <tr>
                    <td><?php if($aktiveCount["isActive"] == 0) {
                            echo '<span class="text-danger">Pasif</span>';
                        } else if($aktiveCount["isActive"] == 1) {
                            echo '<span class="text-success">Aktif</span>';
                        } else if($aktiveCount["isActive"] == 2) {
                            echo '<span class="text-primary">Cookie Geçersiz!<br />Login Denenecek.</span>';
                        } else if($aktiveCount["isActive"] == 4) {
                            echo '<span class="text-primary">İwp Eski Yenilenmesi Lazım!<br />İwp Bölünden Yenileyin</span>';
                        } else {
                            echo "NA";
                        } ?></td>
                    <td><?php echo $aktiveCount["toplamSayi"]; ?></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
    <div class="col-md-4">
        <h4>Kullanıcı Tipine Göre</h4>
        <table class="table table-bordered" style="width:auto !important;">
            <thead>
            <tr>
                <th>Tip</th>
                <th>Kullanıcı Sayısı</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach($model["typeUser"] as $typeCount) { ?>
                <tr>
                    <td><?php if($typeCount["isWebCookie"] == 0) {
                            echo "Kullanıcı Adı & Şifre";
                        } else if($typeCount["isWebCookie"] == 1) {
                            echo "Sadece Cookie";
                        } else {
                            echo "NA";
                        } ?></td>
                    <td><?php echo $typeCount["toplamSayi"]; ?></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>
<hr/>
<div class="row">
    <div class="col-md-6">
        <h4>Aktif Kullanıcı Kabiliyetleri</h4>
        <div class="table-responsive">
            <table class="table table-bordered" style="width:auto !important;">
                <thead>
                <tr>
                    <th>Yetenek</th>
                    <th>Toplam</th>
                    <th>Erkek</th>
                    <th>Bayan</th>
                    <th>Belirtilmemiş</th>
                    <th>NA</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach($model["aktiveUserAbility"] as $k => $v) {
                    $cinsT = array(
                        "0"   => 0,
                        "1"   => 0,
                        "2"   => 0,
                        "3"   => 0,
                        "all" => 0
                    );
                    foreach($v as $genderCount) {
                        if($genderCount["gender"] == 3) {
                            $cinsT["3"] += $genderCount["toplamSayi"];
                        } elseif($genderCount["gender"] == 1) {
                            $cinsT["1"] += $genderCount["toplamSayi"];
                        } elseif($genderCount["gender"] == 2) {
                            $cinsT["2"] += $genderCount["toplamSayi"];
                        } else {
                            $cinsT["0"] += $genderCount["toplamSayi"];
                        }
                    }
                    $cinsT["all"] += $cinsT["0"] + $cinsT["1"] + $cinsT["2"] + $cinsT["3"];
                    ?>
                    <tr>
                        <td class="text-primary"><?php if($k == "follow") {
                                echo "Takip";
                            } elseif($k == "like") {
                                echo "Beğeni";
                            } elseif($k == "comment") {
                                echo "Yorum";
                            } else {
                                echo "Story";
                            } ?></td>
                        <td class="text-success"><?php echo $cinsT["all"]; ?></td>
                        <td><?php echo $cinsT["1"]; ?></td>
                        <td><?php echo $cinsT["2"]; ?></td>
                        <td><?php echo $cinsT["3"]; ?></td>
                        <td><?php echo $cinsT["0"]; ?></td>

                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="col-md-6">
        <h4 class="text-danger">Dikkat!</h4>
        <p>Max gönderim yapabileceğiniz takipçi, beğeni ve yorum sayıları için
            <strong>Aktif Kullanıcı Kabiliyetleri</strong> kısmına bakın. Duruma göre Aktif kullanıcı sayısı sizi yanıltabilir.
        </p>
        <p>Aktif kullanıcı kabiliyetleri kısmında Kullanımda olmayan ve ilgili işlem için geçici blokta olan hesaplar toplam adetten düşülmüştür. Max gönderim kapasiteniz buradaki rakamlardır!</p>
    </div>
</div>
<?php $this->section("section_scripts");
    $this->parent(); ?>
<?php $this->endSection(); ?>