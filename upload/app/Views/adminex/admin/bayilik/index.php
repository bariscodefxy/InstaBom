<?php
    /**
     * @var \Wow\Template\View $this
     * @var array              $model
     */
    $this->set("title", "Bayilik");
    $pagination = $this->get("pagination");
?>
<h2>Bayiler</h2>
<div class="panel panel-default">
    <div class="panel-heading">Filtrele</div>
    <div class="panel-body">
        <form method="get">
            <div class="row">
                <div class="col-md-3">
                    <label>Ara</label>
                    <input type="text" name="q" value="<?php echo $this->e($this->request->query->q); ?>" class="form-control" placeholder="Nick veya Ad">
                </div>
                <div class="col-md-3">
                    <label>Aktiflik</label>
                    <select class="form-control" name="isActive">
                        <option value="">Tümü</option>
                        <option value="1"<?php echo $this->request->query->isActive === "1" ? ' selected="selected"' : ''; ?>>Aktif Bayiler</option>
                        <option value="0"<?php echo $this->request->query->isActive === "0" ? ' selected="selected"' : ''; ?>>Pasif Bayiler</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label>&nbsp;</label>
                    <button type="submit" class="btn btn-success form-control">Filtrele</button>
                </div>
                <div class="col-md-3">
                    <label>&nbsp;</label>
                    <a href="<?php echo Wow::get("project/adminPrefix"); ?>/bayilik/bayi-ekle" class="btn btn-warning form-control">Yeni Bayi Ekle</a>
                </div>
            </div>
        </form>
    </div>
</div>
<?php if(!empty($model)) { ?>
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Bitiş Tarihi</th>
                <th>Notlar</th>
                <th>Bakiye</th>
                <th>Son IP Adresi</th>
                <th>İşlem</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach($model as $bayi) { ?>
                <tr>
                    <td><?php echo $bayi["bayiID"]; ?></td>
                    <td><?php echo $bayi["username"]; ?><br/>
                        <?php if($bayi["isActive"] == 1) { ?>
                            <label class="label label-success">Aktif</label>
                        <?php } else { ?>
                            <label class="label label-danger">Pasif</label>
                        <?php } ?></td>
                    <td><?php echo date("d.m.Y H:i", strtotime($bayi["sonaErmeTarihi"])); ?></td>
                    <td><?php echo $bayi["notlar"]; ?></td>
                    <td><?php echo $bayi["bakiye"]; ?> ₺</td>
                    <td><?php echo $bayi["ipAdresi"]; ?></td>
                    <td>
                        <a class="btn btn-primary btn-xs" href="<?php echo Wow::get("project/adminPrefix"); ?>/bayilik/bayi-detay/<?php echo $bayi["bayiID"]; ?>">Detaylar</a>
                        <?php if(strtotime($bayi["sonaErmeTarihi"]) > strtotime("now") && $bayi["isActive"] == 1) { ?>
                            <a class="btn btn-warning btn-xs" href="<?php echo Wow::get("project/adminPrefix"); ?>/login/bayi/<?php echo $bayi["bayiID"]; ?>" target="_blank">Login</a>
                        <?php } ?>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
    <?php $this->renderView("shared/pagination", $this->get("pagination")); ?>
<?php } else { ?>
    <p>Henüz bayi yok!</p>
<?php } ?>
