<?php
    /**
     * @var \Wow\Template\View $this
     * @var array              $model
     */
    $tag = $this->get("tag");
?>
<div class="container">
    <h2>Tag #<?php echo $tag; ?></h2>
    <?php $this->renderView("shared/list-media", $model); ?>
</div>