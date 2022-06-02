<?php
    /**
     * @var \Wow\Template\View $this
     * @var array              $model
     */
    $this->set("title","Takipçilerim");
    $this->renderView("shared/user-header", $this->get("accountInfo"));
    $this->renderView("account/header"); ?>
<div class="container">
    <div class="tab-content">
        <div class="tab-pane fade active in">
            <?php
                $this->renderView("shared/list-user", $model);
            ?>
        </div>
    </div>
</div>
