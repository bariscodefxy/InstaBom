<?php
    /**
     * @var \Wow\Template\View $this
     * @var array              $model
     */
    $this->set("title", "Export Wizard");
?>
    <div class="panel panel-default">
        <div class="panel-heading">
            EXPORT WIZARD
        </div>
        <div class="panel-body">
            <p>Bu araç, talep ettiğiniz sayı ve cinsiyette kullanıcı datasını tek bir dosya olarak dışa aktarmanızı sağlar.</p>
            <p>Dışa aktarılan dosyalar, herhangi bir sitede import bölümünden içe aktarılabilir.</p>
            <p class="text-danger">Datalarınızı başkalarına satmak, kullanıcılarınızın işlem hacmini büyüteceğinden daha çabuk patlamasına sebep olur. Ayrıca data sattığınız kişinin aynı dataları kaç farklı kişiye satacağını da bilemezsiniz. Lütfen bu bölümü bilinçli kullanın ve datalarınızı yabancı kişilere vermeyin!</p>
            <p>Daha fazla datanın cinsiyetini tespit etmek için
                <a href="<?php echo Wow::get("project/adminPrefix"); ?>/islemler/cinsiyet-tespit">Cinsiyet Tespit Aracı</a> kullanabilirsiniz.
            </p>
            <p>Data export'ta sadece aktif ve havuzda olmayan kullanıcılar referans alınır.</p>
            <hr/>
            <form method="post" id="formExportWizard">
                <div class="form-group">
                    <label>Cinsiyet Seçimi</label>
                    <select name="gender" class="form-control" onchange="doWork()">
                        <option value="0">Tümü</option>
                        <option value="1">Sadece Erkekler</option>
                        <option value="2">Sadece Bayanlar</option>
                        <option value="3">Sadece Cinsiyeti Bilinmeyenler</option>
                    </select>
                </div>
                <hr/>
                <div id="AdetContainerDiv"></div>
                <hr/>
                <div class="form-group">
                    <label>Export Edilecek Data Adedi</label>
                    <input type="number" value="" name="adet" class="form-control">
                    <span class="help-block">Tüm tespit edilenler için boş bırakabilirsiniz veya kaç tane aktarmak istediğinizi yazabilirsiniz.</span>
                </div>
                <p>
                    <button type="submit" class="btn btn-primary">EXPORT DATA</button>
                </p>
            </form>
        </div>
    </div>

<?php $this->section("section_scripts");
    $this->parent(); ?>
    <script type="text/javascript">
        function doWork() {
            $('#AdetContainerDiv').html('<p><i class="fa fa-spinner fa-spin"></i> HESAPLIYOR..</p>');
            $.ajax({type: 'POST', dataType: 'json', url: '?formType=sorgu', data: $('#formExportWizard').serialize()}).done(function(data) {
                $('#AdetContainerDiv').html('<p><strong>Tesipit edilen, kriterlere uygun data adedi:</strong> ' + data.adet + '</p>');
            });
        }
        doWork();
    </script>
<?php $this->endSection(); ?>