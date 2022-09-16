<?php
require_once '../config.php';
if(empty($username) or $user_info['level'] != 'admin'){
    header('Location: '. $site_url);
    die();
}

$site['title'] = 'Quản lý Tinsoft Proxy - '. $site['site_name'];
require_once '../site/head.php';
?>
<div class="main-content">
    <div class="section__content section__content--p30">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="row align-items-div">
                                <div class="col mt-2 mb-2">
                                    <h5 class="mb-0">Quản Lý TinSoft Proxy</h5>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="card-title text-center">
                                        <div class="title">
                                            <h5 class="h6">Cập Nhật Key Người Dùng</h5>
                                        </div>
                                    </div>
                                    <form action="javascript:void(0)" id="form-user-key" method="post">
                                        <div class="form-group">
                                            <label>Nhập User Keys: </label>
                                            <input type="text" class="form-control" name="site_tinsoft_partner_key" placeholder="Nhập User Key Tinsoft" value="<?= $site['site_tinsoft_partner_key'] ?>">
                                        </div>
                                        <input type="hidden" name="type" value="update">
                                        <div class="form-group text-center">
                                            <button class="btn btn-primary" type="submit" id="submit-user-key">Thay Đổi</button>
                                        </div>
                                    </form>
                                </div>
                                <div class="col-lg-6">
                                    <div class="card-title text-center">
                                        <div class="title">
                                            <h5 class="h6">Mua Thêm Key Proxy</h5>
                                        </div>
                                    </div>
                                    <form action="javascript:void(0)" id="form-order-key" method="post">
                                        <input type="hidden" name="type" value="order">
                                        <div class="form-group">
                                            <label>Số Ngày: </label>
                                            <input type="number" class="form-control" name="days" placeholder="Nhập thời gian keys proxy" name="">
                                        </div>
                                        <div class="form-group">
                                            <label>Số Lượng: </label>
                                            <input type="number" class="form-control" name="quantity" placeholder="Nhập số lượng key muốn mua">
                                        </div>
                                        <div class="form-group">
                                            <label>Chọn Loại: </label>
                                            <select class="form-control" name="vip" >
                                                <option value="0">Loại Thường</option>
                                                <option value="1">Loại Vip</option>
                                                <option value="2">Loại Dùng Nhanh</option>
                                            </select>
                                        </div>
                                        <div class="form-group text-center">
                                            <button class="btn btn-primary" type="submit" id="submit-order-key">Mua Key</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card mt-3">
                        <div class="card-header">
                            <div class="row align-items-div">
                                <div class="col mt-2 mb-2">
                                    <h5 class="mb-0">Danh Sách Key Proxy</h5>
                                </div>
                                <div class="col-md-auto">
                                        <button class="btn btn-primary btn-sm" id="submit-update-proxy"> Cập Nhật </button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive table-responsive-data2">
                                <table class="table table-data2 table-striped" id="table-key">
                                    <thead class="text-center">
                                        <tr>
                                            <th>#</th>
                                            <th>Mã Key</th>
                                            <th>Loại</th>
                                            <th>Hạn Dùng</th>
                                            <th>Trạng Thái</th>
                                            <th>Thao Tác</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-center text-white">
                                        <?php 
                                        $select = mysqli_query($conn, "SELECT * FROM `table_tinsoft` WHERE `user_key` = '".$site['site_tinsoft_partner_key']."' ORDER BY `id` ");
                                        $i = 0;
                                        while($rows = mysqli_fetch_assoc($select)){
                                            if($rows['vip'] == 0){
                                                $vip = 'Thường';
                                            }
                                            else if($rows['vip'] == 1){
                                                $vip = 'Vip';
                                            }
                                            else if($rows['vip'] == 2){
                                                $vip = 'Dùng Nhanh';
                                            }
                                            if(strtotime($rows['date_expired']) > time()){
                                                $status = '<span class="badge text-white bg-success">Đang sử dụng</span>';
                                                $disabled = 'disabled';
                                                $btn      = 'danger';
                                            }
                                            else {
                                                $status = '<span class="badge text-white bg-danger">Hết Hạn</span>';
                                                $disabled = '';
                                                $btn      = 'success';
                                            }
                                        ?>
                                        <tr>
                                            <td><?= $i ?></td>
                                            <td><span class="block-email"><?= $rows['proxy_key'] ?></span></td>
                                            <td><?= $vip ?></td>
                                            <td><?= $rows['date_expired'] ?></td>
                                            <td><?= $status ?></td>
                                            <td>
                                                <div class="table-data-feature">
                                                    <button class="btn btn-sm btn-<?php echo $btn ?>" onclick="extend('<?= $rows['proxy_key'] ?>')" data-toggle="tooltip" data-placement="top" title="Gia Hạn" <?php echo $disabled ?>>
                                                        Gia Hạn
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php $i++; } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
$('#submit-user-key').click(() => {
    var DataPost = $('#form-user-key').serialize();
    Ajax(DataPost);
});

$('#submit-order-key').click(() => {
    var DataPost = $('#form-order-key').serialize();
    Ajax(DataPost);
});

$('#submit-update-proxy').click(() => {
    var DataPost = {
        type: 'update-proxy'
    }
    Ajax(DataPost);
});

function extend(keys) {
    var DataPost = {
        key: keys,
        type: 'extend'
    };
    Ajax(DataPost);
}

function Ajax(Data) {
    $.ajax({
        url: '<?php echo $site_url ?>modun/tinsoft/',
        type: 'POST',
        dataType: 'JSON',
        data: Data,
        success: (result) => {
            if(result.success) {
                Swal.fire('Thành Công', result.message, 'success').then(()=>{
                    window.location.reload();
                });
            }
            else {
                Swal.fire('Thất Bại', result.message, 'error')
            }
        },
        error: () => {
            Swal.fire('Lỗi', "Lỗi máy chủ vui lòng báo lại cho admin", 'error');
        }
    });
}
</script>
<?php
require_once '../site/foot.php';
?>