<?php
require_once '../config.php';
if(empty($username)){
    header('Location: '. $site_url);
    die();
}
else if($user['level'] != 'admin') {
    header('Location: '. $site_url);
    die();
}

$site['title'] = 'Cấu hình trang web - '. $site['site_name'];
$slect = $conn->query("SELECT * FROM `table_site` LIMIT 1 ")->fetch_assoc();

if(!empty($_POST)){
    unset($_POST['site_id']);
    $db->update('table_site', $_POST, ['site_id' => $slect['site_id']]);
    header('Location: '. $site_url. 'administrator/config');
    die();
}
require_once '../site/head.php';
?>
<div class="main-content">
    <div class="section__content section__content--p30">
        <div class="container-fluid">
            <div class="row g-0">
                <div class="col-md-12">
                    <div class="overview-wrap">
                        <h2 class="title-1">Trang chủ</h2>
                        <button class="au-btn au-btn-icon au-btn--blue">
                            <i class="zmdi zmdi-plus"></i>Cài Đặt Hệ Thống</button>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="row align-items-div">
                                <div class="col mt-2 mb-2">
                                    <h5 class="mb-0">Cấu Hình Web SITE</h5>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="col-lg-12">
                                <form action="<?php echo $site_url ?>administrator/config" method="POST" >
                                    <?php
                                    foreach ($slect as $keys => $item){
                                    ?>
                                    <div class="form-group">
                                        <label><?= $keys ?></label>
                                        <input type="text" class="form-control" name="<?= $keys ?>" value="<?= $item ?>" placeholder="">
                                    </div>
                                    <?php } ?>
                                    <div class="form-group text-center">
                                        <button class="btn btn-primary" type="submit">Xác Nhận</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php 
require_once '../site/foot.php';
?>