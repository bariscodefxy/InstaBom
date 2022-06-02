<?php
    /**
     * @var \Wow\Template\View $this
     * @var array              $model
     */
?>

<div class="container">
    <ul class="nav nav-tabs nav-justified nav-tabs-justified" style="margin-bottom: 15px;">
        <li<?php if(!isset($this->route->params["id"]) || $this->route->params["id"] != "me") { ?> class="active"<?php } ?>>
            <a href="/account/history">TAKİP ETTİKLERİN</a>
        </li>
        <li<?php if(isset($this->route->params["id"]) && $this->route->params["id"] == "me") { ?> class="active"<?php } ?>>
            <a href="/account/history/me">SEN</a>
        </li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane fade active in">
            <?php $this->renderView("shared/list-activity", $model); ?>
        </div>
    </div>
</div>