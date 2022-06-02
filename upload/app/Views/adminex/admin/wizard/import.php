<?php
    /**
     * @var \Wow\Template\View $this
     * @var array              $model
     */
    $this->set("title", "Import Wizard");
?>
<div class="panel panel-default">
    <div class="panel-heading">
        IMPORT WIZARD
    </div>
    <div class="panel-body">
        <p class="text-primary">
            <i class="fa fa-info"></i> Yalnızca {PAKETADI}.iwp dosyaları desteklenmektedir. Bu dosyalar Export wizard aracılığı ile oluşturulabilir.
        </p>
        <p class="text-danger">Import işlemi esnasında sitenizde zaten mevcut olan kullanıcılar atlanacaktır. Bu kapsamda daha fazla kullanıcıyı içe aktarabilmek için Import işleminden önce sisteminizdeki Pasif kullanıcıları silmenizi öneririz! </p>
        <p>Aktarım gerçekleştikten sonra yüklediğiniz iwp paketi sunucudan silinir.</p>
        <p>
        <p>Yalnızca güvenilir kaynaklardan aldığınız iwp dosyalarını yükleyin. Güvenilir kaynaklardan temin edilmeyen paketler sunucunuza zarar verebilir!</p>
        <p>Sunucu yapılandırmanızdan kaynaklı, büyük iwp paketlerini yüklemede sorun yaşıyorsanız,  yükleme işlemini farklı bir platform (FTP, CPanel Dosya Yöneticisi vb.) üzerinden
            <strong>~app/Cookies/import</strong> dizinine yapabilirsiniz.</p>
        <span class="btn btn-success fileinput-button">
                                    <i class="fa fa-upload"></i>
                                    <span>Paket Yükle</span>
                                    <input id="fileupload" type="file" name="files[]" multiple>
                                </span>
        </p>
        <div id="UploadProgressContainer" style="display: none;">
            <div id="progress" class="progress">
                <div class="progress-bar progress-bar-success progress-bar-striped active"></div>
            </div>
        </div>
        <hr/>
        <div class="clearfix"></div>
        <div id="sourceCookies">
            <?php if(count($model["iwpPackages"]) > 0) { ?>
                <form method="post">
                    <div class="form-group">
                        <label>İçe Aktarılacak Paket</label>
                        <select name="packageName" class="form-control">
                            <?php foreach($model["iwpPackages"] as $p) { ?>
                                <option value="<?php echo $p; ?>"><?php echo $p; ?>.iwp</option>
                            <?php } ?>
                        </select>
                        <span class="help-block"><strong><?php echo count($model["iwpPackages"]); ?></strong> paket aktarılmayı bekliyor. İçe aktarmak istediğiniz paketi seçin.</span>
                    </div>
                    <p>
                        <button type="submit" class="btn btn-primary" onclick="$(this).html('BEKLEYİN..');">İÇE AKTAR</button>
                    </p>
                </form>
            <?php } else { ?>
                <p class="text-danger"><?php echo Wow::get("project/cookiePath"); ?>import/ klasöründe aktarılmayı bekleyen hiç paket yok. Elinizdeki paketleri yüklemek için paket yükle butonunu kullanın.</p>
            <?php } ?>
        </div>
    </div>
</div>

<?php $this->section("section_head");
    $this->parent(); ?>
<link rel="stylesheet" href="/assets/jquery-file-upload/css/jquery.fileupload.css"/>
<?php $this->endSection(); ?>

<?php $this->section("section_scripts");
    $this->parent(); ?>
<script src="/assets/load-image/load-image.all.min.js"></script>
<script src="/assets/canvas-to-blob/canvas-to-blob.min.js"></script>
<script src="/assets/jquery-file-upload/js/vendor/jquery.ui.widget.js"></script>
<script src="/assets/jquery-file-upload/js/jquery.iframe-transport.js"></script>
<script src="/assets/jquery-file-upload/js/jquery.fileupload.js"></script>
<script src="/assets/jquery-file-upload/js/jquery.fileupload-process.js"></script>
<script type="text/javascript">
    function setIwpUpload() {
        $('#fileupload').fileupload({
                                        url            : '?formType=uploadIwp',
                                        acceptFileTypes: /(\.|\/)(iwp)$/i,
                                        stop           : function() {
                                            $('#UploadProgressContainer').css('display', 'none');
                                            window.location.href = window.location.href;
                                        },
                                        progressall    : function(e, data) {
                                            if($('#UploadProgressContainer').css('display') == 'none') {
                                                $('#UploadProgressContainer').css('display', 'block');
                                            }
                                            var progress = parseInt(data.loaded / data.total * 100, 10);
                                            $('#progress .progress-bar').css(
                                                'width',
                                                progress + '%'
                                            );
                                        }
                                    }).prop('disabled', !$.support.fileInput).parent().addClass($.support.fileInput ? undefined : 'disabled');
    }
    $(document).ready(function() {
        setIwpUpload();
    });
</script>
<?php $this->endSection(); ?>
