<?php
    /**
     * @var \Wow\Template\View $this
     * @var array              $model
     */
    $this->renderView("shared/user-header", $this->get("accountInfo"));
    $this->renderView("account/header"); ?>
<div class="container">
    <div class="tab-content">
        <div class="tab-pane fade active in" id="entry-list-container">
            <?php $this->renderView("shared/list-geo-media", $model); ?>
        </div>
    </div>
</div>