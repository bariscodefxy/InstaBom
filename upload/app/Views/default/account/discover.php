<?php
    /**
     * @var \Wow\Template\View $this
     * @var array              $model
     */
 ?>
<div class="cl10"></div>
<div class="container">
    <div class="tab-content">
        <div class="tab-pane fade active in">
            <?php
                $this->renderView("shared/list-media", $model);
            ?>
        </div>
    </div>
</div>