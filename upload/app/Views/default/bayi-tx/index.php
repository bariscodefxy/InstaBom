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
            <h4 style="margin-top: 0;">Bayi Paneli (Light)</h4>
            <p>Kullanmak üzere bir araç seçin.</p>
            <p class="text-info">Bu light bayilik versiyonumuzdur. Burada sadece kredilerinizle işlem yapabilirsiniz. 15k Kullanıcılı, Sınırsız İşlemli Full bayilik için bizden fiyat alın.</p>
        </div>
        <div class="col-sm-4 col-md-3">
            <?php $this->renderView("bayi-tx/sidebar"); ?>
        </div>
    </div>
</div>