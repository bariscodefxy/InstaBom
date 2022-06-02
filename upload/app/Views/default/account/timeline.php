<?php
    /**
     * @var \Wow\Template\View $this
     */
?>
<?php $this->renderView("account/header"); ?>
<div class="container">
    <div class="tab-content">
        <div class="tab-pane fade active in">
            <div id="entry-list-container">
                <div class="row">
                    <?php
                        $this->renderView("account/_timeline", $model);
                    ?>
                </div>
                <button onclick="loadMore();" class="btn btn-block btn-success" type="button">
                    <i class="fa fa-plus"></i> Daha Fazla Yükle
                </button>
            </div>
        </div>
    </div>
</div>
