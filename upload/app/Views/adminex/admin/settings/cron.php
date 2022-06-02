<?php
    /**
     * @var \Wow\Template\View $this
     * @var array              $model
     */
    $this->set("title", "Ayarlar");
    $baseUrl = Wow::get("app/base_url");
?>
    <h2>Cron Listesi</h2>
    <div class="alert alert-info">
        <p>Cron url lerinizin çalıştırılabilir olması isim bir zamanlanmış görev tanımlamış olmanız gerekmektedir.<br /Zamanlanmış görevler Cpanel'de CronJob, Plesk'te ise Görev Zamanlayıcı (Scheduled Tasks) olarak anılır.<br/>Tanımladığınız görev dakida bir çalışmak üzere işaretlenmelidir. Plesk kullanıcıları Cron Stili seçeneğini seçerek
            <strong>* * * * *</strong> yazabilirler. Cpanel kullanıcıları listeden dakikada bir seçebilir.</p>
        <p>Görev tanımlarken komut satırına eklemeniz gereken kod şu şekildedir:<br/><strong>curl -s -m 180 -H "CronJobToken: <?php echo Wow::get("project/cronJobToken"); ?>" http<?php echo Wow::get("project/onlyHttps") ? 's' : ''; ?>://<?php echo $_SERVER['SERVER_NAME'];
                    echo empty($baseUrl) ? '' : $baseUrl; ?>/cron-job > /dev/null</strong>
        </p>
    </div>

    <hr/>

<?php foreach($model AS $cron) { ?>
    <div class="row">
        <div class="col-sm-9"><strong><?php echo $cron["baslik"]; ?></strong><br/><?php echo $cron["url"]; ?>
            <br/><?php echo $cron["calismaSikligi"] ?> saniyede bir çalışır<br/></div>
        <div class="col-sm-3">
            <button id="cronBtn-<?php echo $cron["cronID"]; ?>" class="btn <?php echo $cron["isActive"] == 1 ? "btn-success" : "btn-danger"; ?>" onclick="cronDuzenle(<?php echo $cron["cronID"]; ?>)" style="width: 88.48px;">DÜZENLE</button>
        </div>
    </div>
    <hr/>
<?php } ?>
    <p>
        <button type="button" class="btn btn-primary" style="float:left;" onclick="cronDuzenle()">Yeni Cron Ekle</button>
    </p>

<?php $this->section("section_scripts");
    $this->parent(); ?>
    <script type="text/javascript">
        function showCronModal() {
            $('#myCrons').modal("show");
        }

        function cronDuzenle(cronID) {
            if(cronID == "") {
                $('#myCronEditSave').modal("show");
            } else {
                $('#cronBtn-' + cronID).html('<i class="fa fa-cog fa-spin fa-1x fa-fw"></i>');
                $.ajax({url: '<?php echo Wow::get("project/adminPrefix"); ?>/settings/one-cron/' + cronID, type: 'POST', dataType: 'json'}).done(function(data) {
                    $('input[name="cronID"]').val(data.cronID);
                    $('input[name="cronBaslik"]').val(data.baslik);
                    $('input[name="cronUrl"]').val(data.url);
                    $('input[name="cronSaniye"]').val(data.calismaSikligi);
                    $('select[name="cronDurum"]').val(data.isActive);
                    $('#cronBtn-' + cronID).html('DÜZENLE');
                    $('#myCronEditSave').modal("show");
                });
            }
        }

        $('#cronKaydetUpdate').click(function() {
            $('#requireAlert').hide();
            var cronBaslik = $('input[name="cronBaslik"]').val();
            var cronUrl    = $('input[name="cronUrl"]').val();
            var cronSaniye = $('input[name="cronSaniye"]').val();
            var cronDurum  = $('select[name="cronDurum"]').val();
            if(cronBaslik != '' && cronUrl != '' && cronSaniye != '' && cronDurum != '') {
                $('#cronUpdateForm').submit();
            } else {
                $('#requireAlert').fadeIn(500);
                setTimeout(function() {
                    $('#requireAlert').fadeOut(500);
                }, 2000);
            }
        });
    </script>
<?php $this->endSection(); ?>
<?php $this->section("section_modals");
    $this->parent(); ?>

    <!-- Modal -->
    <div class="modal fade" id="myCronEditSave" tabindex="-1" role="dialog" aria-labelledby="myCronEditSaveLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Cron Ayarları</h4>
                </div>
                <div class="modal-body">
                    <div id="requireAlert" class="alert alert-warning" role="alert" style="display:none;">
                        <strong>Dikkat!</strong> Lütfen tüm alanları doldurunuz.
                    </div>
                    <form method="POST" action="<?php echo Wow::get("project/adminPrefix"); ?>/settings/save-update-cron" id="cronUpdateForm">
                        <div class="row">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Cron Başlığı</label>
                                <div class="col-sm-8">
                                    <input type="hidden" class="form-control" name="cronID" value="" required>
                                    <input type="text" class="form-control" name="cronBaslik" value="" placeholder="İşlemin Başlığı" required>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <br/>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">İşlem URL</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" name="cronUrl" value="" placeholder="İşlemin Url Adresi" required>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <br/>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Çalışma Aralığı (saniye)</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" name="cronSaniye" value="" placeholder="Çalışma aralığını saniye cinsinden yazınız" required>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <br/>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Durum</label>
                                <div class="col-sm-8">
                                    <select class="form-control" name="cronDurum" required>
                                        <option value="">Durum Seçiniz</option>
                                        <option value="1">Aktif</option>
                                        <option value="0">Pasif</option>
                                    </select>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <br/>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Kapat</button>
                    <button type="button" class="btn btn-success" id="cronKaydetUpdate">Kaydet</button>
                </div>
            </div>
        </div>
    </div>
<?php $this->endSection(); ?>