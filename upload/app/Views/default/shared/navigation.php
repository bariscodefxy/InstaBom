<?php
    /**
     * @var \Wow\Template\View     $this
     * @var \App\Models\Navigation $model
     */
    return;
    if(count($model->get()) > 1) { ?>
        <div class="bg-breadcrumb">
            <div class="container">
                <ul class="breadcrumb">
                    <?php $intLoop = 0;
                        foreach($model->get() as $item) {
                            $intLoop++; ?>
                            <li<?php echo $intLoop == count($model->get()) ? ' class="active"' : ''; ?>>
                                <a href="<?php echo $item["link"]; ?>"><?php echo $item["text"]; ?></a></li>
                        <?php } ?>
                </ul>
            </div>
        </div>
    <?php } ?>