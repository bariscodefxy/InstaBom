<?php
    /**
     * @var array $model
     */
    if(empty($model)) {
        return;
    }
?>
    <div class="cl10"></div>
        <?php
            foreach($model as $notification) {
                /**
                 * @var \App\Models\Notification $notification
                 */
                ?>
                <div class="alert alert-block alert-<?php echo $notification->type; ?> fade in">
                    <?php if($notification->closable) { ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    <?php } ?>
                    <?php if(!empty($notification->title)) {
                        ?><h4 class="alert-heading margin-bottom-10"><i class="fa <?php switch($notification->type) {
                            case $notification::PARAM_TYPE_DANGER:
                                echo "fa-close";
                                break;
                            case $notification::PARAM_TYPE_INFO:
                                echo "fa-info";
                                break;
                            case $notification::PARAM_TYPE_WARNING:
                                echo "fa-warning";
                                break;
                            case $notification::PARAM_TYPE_SUCCESS:
                                echo "fa-check";
                                break;
                            default:
                                echo "";
                        } ?>"></i> <?php echo $notification->title; ?></h4><?php } ?>
                    <?php if(!empty($notification->messages)) { ?>
                        <p class="margin-bottom-10">
                            <?php $intLoop = 0;
                                foreach($notification->messages as $message) {
                                    $intLoop++;
                                    if($intLoop > 1) {
                                        echo "<br />";
                                    }
                                    echo $message;
                                }
                            ?>
                        </p>
                    <?php } ?>
                    <?php if(!empty($notification->buttons)) { ?>
                        <p>
                            <?php foreach($notification->buttons as $button) {
                                echo $button . " ";
                            } ?>
                        </p>
                    <?php } ?>
                </div>
                <?php
            }
        ?>