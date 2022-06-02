<?php
    /**
     * @var \Wow\Template\View $this
     * @var array              $model
     */
    $accountInfo    = $this->get("accountInfo");
    $userFriendship = $this->get("userFriendship");
    $this->set("title", $accountInfo["user"]["full_name"]." Etiketlendiği Gönderiler");
    $this->renderView("shared/user-header", $accountInfo);
?>
<div class="container">
    <ul class="nav nav-tabs nav-justified nav-tabs-justified" style="margin-bottom: 15px;">
        <li<?php if($this->route->params["action"] == "Index") { ?> class="active"<?php } ?>>
            <a href="/user/<?php echo $accountInfo["user"]["pk"]; ?>">Paylaştığı Gönderiler</a>
        </li>
        <li<?php if($this->route->params["action"] == "Geo") { ?> class="active"<?php } ?>>
            <a href="/user/<?php echo $accountInfo["user"]["pk"]; ?>/geo">Haritalı Gönderileri</a>
        </li>
        <li<?php if($this->route->params["action"] == "Tagged") { ?> class="active"<?php } ?>>
            <a href="/user/<?php echo $accountInfo["user"]["pk"]; ?>/tagged">Etiketlendiği Gönderiler</a>
        </li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane fade active in" id="entry-list-container">
            <?php $this->renderView("shared/list-media", $model); ?>
        </div>
    </div>
</div>