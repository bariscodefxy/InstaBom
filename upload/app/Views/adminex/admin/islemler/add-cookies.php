<?php
    /**
     * @var \Wow\Template\View $this
     * @var array              $model
     */
    $this->set("title", "İşlemler");
?>
<div class="panel panel-default">
    <div class="panel-heading">
        Cookie Aktarma
    </div>
    <div class="panel-body">
        <p class="text-primary">
            <i class="fa fa-info"></i> username.selco ve username.dat + username.cnf şeklindeki cookieler desteklenmektedir. <span class="text-danger">Farklı sistemlere ait olan bu datalara karşı desteğimizi sonlandırma hakkımız saklıdır! </span>
        </p>
        <p>Ayrıca bu bölümde <strong>.iwb uzantılı dosyalar desteklenmez.</strong> iwb aktarımı için İmport / Export araçlarını kullanabilirsiniz.</p>
        <p>
        <span class="btn btn-success fileinput-button">
                                    <i class="fa fa-upload"></i>
                                    <span>Cookie Yükle</span>
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
            <?php if($model["countSourceCookies"] > 0) { ?>
                <p>
                    <strong><?php echo $model["countSourceCookies"]; ?></strong> cookie aktarılmayı bekliyor. Bu cookie'ler sistem tarafından arka planda otomatik olarak aktarılmaktadır.
                </p>
            <?php } else { ?>
                <p class="text-danger"><?php echo Wow::get("project/cookiePath"); ?>source/ klasöründe aktarılmayı bekleyen hiç cookie yok. Elinizdeki cookie'leri yüklemek için cookie yükle butonunu kullanın.</p>
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
    function setCookieUpload() {
        $('#fileupload').fileupload({
                                        url            : '?formType=uploadCookies',
                                        acceptFileTypes: /(\.|\/)(selco|dat|cnf)$/i,
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
        setCookieUpload();
    });
</script>
<?php $this->endSection(); ?>
