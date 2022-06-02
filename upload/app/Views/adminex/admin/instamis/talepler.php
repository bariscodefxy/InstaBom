<?php
    /**
     * @var \Wow\Template\View $this
     * @var array              $model
     */
    $this->set("title", "Instamis Talepleri"); ?>
<div class="panel panel-default">
    <div class="panel-heading">
        INSTAMIS TALEPLER (Son 50 Talep)
    </div>
    <div class="panel-body">
        <section id="unseen" style="overflow: auto;">
            <table class="table table-bordered table-striped table-condensed">
                <thead>
                <tr>
                    <th>TalepID</th>
                    <th>Talep Eden</th>
                    <th>Tip</th>
                    <th>Gönderi</th>
                    <th>Yorum Metni</th>
                    <th>Talep Tarihi</th>
                    <th class="numeric">Gönderilen/Hedef</th>
                    <th>Durum</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach($model["talepler"] AS $talep) { ?>
                    <tr>
                        <td><?php echo $talep["talepID"]; ?></td>
                        <td><?php echo $talep["kullaniciAdi"]; ?></td>
                        <td><?php echo $talep["talepTip"]; ?></td>
                        <td class="text-center">
                            <a href="#modalPopup" data-toggle="modal" onclick="sendPopup('<?php echo $talep["talepID"]; ?>')" class="btn btn-primary btn-xs">Gönderiyi İncele</a>
                        </td>
                        <td><?php echo $talep["yorumText"]; ?></td>
                        <td><?php echo date("d-m-Y H:i:s", strtotime($talep["talepTarih"])); ?></td>
                        <td class="numeric text-center">
                            <span style="font-weight:bold;" class="<?php echo $talep["adetMax"] <= $talep["gonderilenAdet"] ? "text-success" : "text-danger"; ?>"><?php echo $talep["gonderilenAdet"]; ?>/<?php echo $talep["adetMax"]; ?></span>
                        </td>
                        <td class="text-center">
                            <?php echo $talep["adetMax"] <= $talep["gonderilenAdet"] || $talep["durum"] == 1 ? "<button class='btn btn-success btn-xs'>Tamamlandı</button>" : ($talep["durum"] == 0 ? "<button class='btn btn-info btn-xs'>Devam Ediyor</button>" : "<button class='btn btn-warning btn-xs'>Yarım Kaldı</button>") ?>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </section>
    </div>
</div>

<?php $this->section("section_modals");
    $this->parent(); ?>
<div class="modal fade" id="modalPopup" style="z-index: 1051;">
    <div class="modal-dialog">
        <div class="modal-content" id="modalPopupInner"></div>
    </div>
</div>
<?php $this->endSection(); ?>

<?php $this->section("section_scripts");
    $this->parent(); ?>
<script type="text/javascript">
    function sendPopup(talepID) {
        $('#modalPopupInner').html('<div class="modal-body"><h2>Bekleyin..</h2></div>');
        $.ajax({url: '<?php echo Wow::get("project/adminPrefix"); ?>/instamis/talepler/'+talepID, type: 'GET'}).done(function(data) {
            $('#modalPopupInner').html(data);
        });
    }
</script>
<?php $this->endSection(); ?>
