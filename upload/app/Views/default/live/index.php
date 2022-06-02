<?php
    /**
     * @var \Wow\Template\View $this
     * @var array              $model
     */
?>
<div class="container">
    <div class="cl10"></div>
    <div class="row">
        <div class="col-sm-8 col-md-9">
            <h4 style="margin-top: 0;">Canlı Yayın (İzleyici,beğeni,yorum)</h4>
            <p>Kullanmak üzere bir araç seçin.</p>
        </div>
        <div class="col-sm-4 col-md-3">
            <?php $this->renderView("live/sidebar"); ?>
        </div>
    </div>
</div>