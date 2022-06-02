<?php
    /**
     * @var \Wow\Template\View $this
     * @var array              $model
     */
?>
<ul class="nav nav-tabs nav-justified nav-tabs-justified">
    <li class="active"><a href="#addByUploadImageProfilePhoto" data-toggle="tab">Resim Yükle</a></li>
    <li><a href="#addByUrlProfilePhoto" data-toggle="tab">Url'den Ekle</a></li>
</ul>
<div id="myTabContent" class="tab-content">
    <div class="tab-pane fade active in" id="addByUploadImageProfilePhoto">
        <form method="post" action="/account/change-profile-photo" enctype="multipart/form-data" onsubmit="$('#btnUploadImageProfilePhoto').html('YÜKLENİYOR BEKLEYİN..').removeClass('btn-primary').addClass('btn-success').attr('disabled','disabled');">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Resim Yükle</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Resim</label>
                    <input type="file" name="file" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Vazgeç</button>
                <button type="submit" id="btnUploadImageProfilePhoto" class="btn btn-primary">Kaydet</button>
            </div>
        </form>
    </div>
    <div class="tab-pane fade" id="addByUrlProfilePhoto">
        <form method="post" action="/account/change-profile-photo" onsubmit="$('#btnAddImageProfilePhoto').html('YÜKLENİYOR BEKLEYİN..').removeClass('btn-primary').addClass('btn-success').attr('disabled','disabled');">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Url İle Profile Resmi Değiştir</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Resim Url</label>
                    <input type="text" name="url" class="form-control" placeholder="Örn: http://site.com/image.jpg" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Vazgeç</button>
                <button type="submit" class="btn btn-primary" id="btnAddImageProfilePhoto">Kaydet</button>
            </div>
        </form>
    </div>
</div>