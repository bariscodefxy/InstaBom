<?php
    /**
     * @var \Wow\Template\View $this
     * @var array              $model
     */
    $this->set("title", "Cinsiyet Tespiti");
    $toplamTespitBekleyenUye = 0;
?>
    <div class="panel panel-default">
        <div class="panel-heading">
            Cinsiyet Tespit Aracı
        </div>
        <div class="panel-body">
            <p>Bu araç, cinsiyetini belirtmemiş veya cinsiyet alanı tanımsız (NA) olan kullanıcıların cinsiyetlerini isim arşivinden sorgulayarak tespit etmeye yarar.</p>
            <p>Şu anda kullanıcılarınızın cinsiyet dağılımı aşağıdaki gibidir.</p>
            <table class="table table-bordered" style="width:auto !important;">
                <thead>
                <tr>
                    <th>Cinsiyet</th>
                    <th>Kullanıcı Sayısı</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach($model as $genderCount) { ?>
                    <tr>
                        <td><?php
                                if($genderCount["gender"] == 3) {
                                    echo "Belirtilmemiş.";
                                    $toplamTespitBekleyenUye = $toplamTespitBekleyenUye + $genderCount["toplamSayi"];
                                } elseif($genderCount["gender"] == 1) {
                                    echo "Erkek";
                                } elseif($genderCount["gender"] == 2) {
                                    echo "Bayan";
                                } else {
                                    echo "NA";
                                    $toplamTespitBekleyenUye = $toplamTespitBekleyenUye + $genderCount["toplamSayi"];
                                } ?></td>
                        <td><?php echo $genderCount["toplamSayi"]; ?></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
            <p>
                <a href="javascript:void(0);" id="btnDoWork" onclick="doWorkRecursive(0);" class="btn btn-primary">BAŞLAT</a>
            </p>
        </div>
    </div>

<?php $this->section("section_scripts");
    $this->parent(); ?>
    <script type="text/javascript">
        function doWorkRecursive(lastUserID) {
            $('#btnDoWork').attr('disabled', 'disabled').html('<i class="fa fa-spinner fa-spin fa-3x"></i> DEVAM EDİYOR..');
            $.ajax({type: 'POST', dataType: 'json', url: '?', data: 'lastUserID=' + lastUserID}).done(function(data) {
                if(data.isCompleted === 1) {
                    window.location.href = window.location.href;
                }
                else {
                    doWorkRecursive(data.lastUserID);
                }
            });
        }
    </script>
<?php $this->endSection(); ?>