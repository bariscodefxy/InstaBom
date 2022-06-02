<?php
    /**
     * @var \Wow\Template\View $this
     * @var array              $model
     */
    $this->set("title", "Bakım");
?>
<div class="panel panel-default">
    <div class="panel-heading">
        Bakım
    </div>
    <div class="panel-body">
        <p>Çok fazla kayıt içeren, boyutu büyüyen database tablolarınız sql sorgularının yavaşlamasına, dolayısıyla sunucunuzun kaynak tüketiminin artmasına neden olur.</p>
        <p>Bu doğrultuda gereksiz dataları temizlemek ve tabloların boyutunu düşürmek sunucunuzun performansını arttıracaktır.</p>
        <p>
            <strong>Dikkat: </strong> Tabloları temizlemek, bayilerinizin 10 gün öncesine ait işlem geçmişi kayıtlarının silinmesi demektir!
        </p>
        <p><strong>İpucu: </strong> Ayrıca
            <a href="<?php echo Wow::get("project/adminPrefix"); ?>/islemler">Pasif Kullanıcıları Temizleme</a> sayfasında pasif kullanıcıları temizlemeniz de sql sorgularınızın hızlanması için yararlı olacaktır.
        </p>
        <table class="table table-bordered" style="width:auto !important;">
            <thead>
            <tr>
                <th>Tablo</th>
                <th>Durum</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>bayi_islem</td>
                <td><?php echo $model["bayi_islem"] > 0 ? $model["bayi_islem"] . " kayıt silinebilir." : "Tablo temizlenmiş."; ?></td>
            </tr>
            <tr>
                <td>uye_otobegenipaket_gonderi</td>
                <td><?php echo $model["uye_otobegenipaket_gonderi"] > 0 ? $model["uye_otobegenipaket_gonderi"] . " kayıt silinebilir." : "Tablo temizlenmiş."; ?></td>
            </tr>
            </tbody>
        </table>
        <h3>İşlem Yap</h3>
        <form id="workForm">
            <div class="form-group">
                <div class="checkbox">
                    <label><input type="checkbox" name="delete_rows" value="1" checked="checked"> Tablolardaki eski kayıtları temizle</label>
                </div>
            </div>
            <div class="form-group">
                <button type="button" class="btn btn-primary" id="btnDoWork" onclick="doWork();">BAŞLAT</button>
            </div>
        </form>
    </div>
</div>

<?php $this->section("section_scripts");
    $this->parent(); ?>
<script type="text/javascript">
    function doWork() {
        $('#btnDoWork').attr('disabled', 'disabled').html('<i class="fa fa-spinner fa-spin fa-3x"></i> TEMİZLENİYOR..');
        $.ajax({type: 'POST', dataType: 'json', url: '?', data: $('#workForm').serialize()}).done(function(data) {
            window.location.href = window.location.href;
        });
    }
</script>
<?php $this->endSection(); ?>
