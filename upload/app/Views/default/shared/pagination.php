<?php
    /**
     * @var \Wow\Template\View $this
     * @var  array             $model
     */
    $pageLink        = "?" . preg_replace("/&?page=[^&]+/", '', $_SERVER['QUERY_STRING']);
    $pageLink        = $pageLink == "?" ? $pageLink . "page=" : $pageLink . "&page=";
    $numaraAraligi   = 3;
    $numaraBaslangic = $model["activePage"] - $numaraAraligi;
    if($numaraBaslangic < 1) {
        $numaraBaslangic = 1;
    }
    $numaraBitis = $model["activePage"] + $numaraAraligi;
    if($numaraBitis > $model["pageCount"]) {
        $numaraBitis = $model["pageCount"];
    }
?>
<p>Toplam <?php echo $model["recordCount"]; ?> kayıttan, <?php echo $model["startIndex"]; ?> - <?php echo $model["endIndex"]; ?> arası listeleniyor.</p>
<ul class="pagination">
    <li<?php echo $model["activePage"] == 1 ? ' class="disabled"' : ''; ?>>
        <a href="<?php echo $model["activePage"] == 1 ? 'javascript:;' : $pageLink . "1"; ?>"><i class="fa fa-angle-double-left"></i></a>
    </li>
    <li<?php echo empty($model["previousPage"]) ? ' class="disabled"' : ''; ?>>
        <a href="<?php echo empty($model["previousPage"]) ? 'javascript:;' : $pageLink . $model["previousPage"]; ?>"><i class="fa fa-angle-left"></i></a>
    </li>
    <?php for($i = $numaraBaslangic; $i <= $numaraBitis; $i++) { ?>
        <li<?php echo $model["activePage"] == $i ? ' class="active"' : ''; ?>>
            <a href="<?php echo $pageLink . $i; ?>"><?php echo $i; ?></a></li>
    <?php } ?>
    <li<?php echo empty($model["nextPage"]) ? ' class="disabled"' : ''; ?>>
        <a href="<?php echo empty($model["nextPage"]) ? 'javascript:;' : $pageLink . $model["nextPage"]; ?>"><i class="fa fa-angle-right"></i></a>
    </li>
    <li<?php echo $model["activePage"] == $model["pageCount"] ? ' class="disabled"' : ''; ?>>
        <a href="<?php echo $model["activePage"] == $model["pageCount"] ? 'javascript:;' : $pageLink . $model["pageCount"]; ?>"><i class="fa fa-angle-double-right"></i></a>
    </li>
</ul>