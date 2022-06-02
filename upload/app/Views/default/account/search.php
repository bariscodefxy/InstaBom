<?php
    /**
     * @var \Wow\Template\View $this
     * @var  array             $model
     */
?>
<div class="container">
    <h2>Arama Sonuçları</h2>
    <ul class="nav nav-tabs">
        <li<?php if($this->get("tab") == "user") { ?> class="active"<?php } ?>>
            <a href="?q=<?php echo $this->get("q"); ?>&tab=user">Kişiler</a></li>
        <li<?php if($this->get("tab") == "tag") { ?> class="active"<?php } ?>>
            <a href="?q=<?php echo $this->get("q"); ?>&tab=tag">Taglar</a></li>
        <li<?php if($this->get("tab") == "location") { ?> class="active"<?php } ?>>
            <a href="?q=<?php echo $this->get("q"); ?>&tab=location">Lokasyonlar</a></li>
    </ul>
    <div id="myTabContent" class="tab-content">
        <div class="tab-pane fade active in" style="margin-top:20px;">
            <?php switch($this->get("tab")) {
                case "user":
                    $this->renderView("shared/list-user", $model);
                    break;
                case "tag": ?>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                            <th>Tag</th>
                            <th>Gönderi Sayısı</th>
                            </thead>
                            <?php foreach($model["results"] as $result) { ?>
                                <tr>
                                    <td>
                                        <a href="/account/tag/<?php echo $result["name"]; ?>"><?php echo $result["name"]; ?></a>
                                    </td>
                                    <td><?php echo $result["media_count"]; ?></td>
                                </tr>
                            <?php } ?>
                        </table>
                    </div>
                    <?php break;
                case "location": ?>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                            <th>Lokasyon</th>
                            <th>Detaylar</th>
                            </thead>
                            <?php foreach($model["items"] as $item) { ?>
                                <tr>
                                    <td>
                                        <a href="/account/location/<?php echo $item["location"]["facebook_places_id"]; ?>?location=<?php echo strip_tags($item["location"]["name"]); ?>"><?php echo $item["location"]["name"]; ?></a>
                                    </td>
                                    <td>
                                        <p><?php echo $item["location"]["name"] . " " . $item["location"]["city"]; ?></p><?php echo empty($item["location"]["address"]) ? '' : '<p>' . $item["location"]["address"] . '</p>'; ?>
                                    </td>
                                </tr>
                            <?php } ?>
                        </table>
                    </div>
                    <?php break;
            } ?>
        </div>
    </div>
</div>