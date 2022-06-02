<?php
    /**
     * @var \Wow\Template\View $this
     * @var array              $model
     */
    $accountInfo = $this->get("accountInfo");
    $userFriendship = $this->get("userFriendship");
    $this->set("title",$accountInfo["user"]["full_name"]." Takip Ettikleri");
    $this->renderView("shared/user-header", $accountInfo);
?>
<div class="cl10"></div>
<div class="container">
    <div class="tab-content">
        <div class="tab-pane fade active in">
            <?php $this->renderView("shared/list-user", $model); ?>
        </div>
    </div>
</div>