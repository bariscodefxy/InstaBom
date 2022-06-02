<?php
    /**
     * @var \Wow\Template\View $this
     * @var array              $model
     */
    $location = $this->get("location");
?>
<div class="container">
    <h2>Lokasyon #<?php echo $location; ?></h2>
    <?php $this->renderView("shared/list-media", $model); ?>
</div>