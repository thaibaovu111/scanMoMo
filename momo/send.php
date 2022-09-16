<?php
session_start();
require_once '../config.php';
if(empty($username)){
    header('Location: '. $site_url);
    die();
}
$phone = Request::Clean_GET('phone');
if(empty($phone)){
    header('Location: '. $site_url);
    die();
}
$creat = $conn->query("SELECT * FROM `table_momo` WHERE `phone` = '$phone' ");
if(empty($creat->num_rows)){
    header('Location: '. $site_url);
    die();
}
if(empty($_SESSION[$phone])){
    $result = $momo->LoadData($phone)->CheckHis(100);

    $_SESSION[$phone] = $result['status'] == 'error' ? array() : $result['TranList'];

}

$ListHis =  $_SESSION[$phone];

$site['title'] = 'Thông tin ví momo '.$phone. ' - '. $site['site_name'];
require_once '../site/head.php';
?>
<div class="main-content">
    <div class="section__content section__content--p30">
        <div class="content-fluid">
            <div class="row mt-3">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="row align-items-div">
                                <div class="col mt-2 mb-2">
                                    <h5 class="mb-0">Thông tin ví MoMo</h5>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="col-lg-12">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="card-title text-center">
                                            <h4 class="title">Chuyển tiền </h4>
                                        </div>
                                        <form action="javascript:void(0)" id="form-send" method="post">
                                            <div class="form-group">
                                                <label>Số điện thoại nhận</label>
                                                <div class="input-group">
                                                    <input type="number" name="receiver" class="form-control" placeholder="Số điện thoại nhận tiền">
                                                    <div class="input-group-append">
                                                        <button type="button" class="btn btn-dark" id="get-name-1">KIỂM TRA</button>
                                                    </div>
                                                </div>
                                            </div>
                                            <input type="hidden" name="type" value="send">
                                            <input type="hidden" name="phone" value="<?= trim($_GET['phone']) ?>">
                                            <div class="form-group">
                                                <label>Số tiền chuyển: </label>
                                                <input type="number" class="form-control" name="amount" placeholder="Số tiền VNĐ">
                                            </div>
                                            <div class="form-group">
                                                <label>Lời Nhắn</label>
                                                <input type="text" class="form-control" name="comment" placeholder="Nhập lời nhắn">
                                            </div>
                                            <div class="form-group text-center">
                                                <button class="btn btn-primary" type="submit" id="submit-send">Xác Nhận</button>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="card-title text-center">
                                            <h4 class="title">Yêu cầu chuyển tiền</h4>
                                        </div>
                                        <form action="javascript:void(0)" id="form-request" method="post">
                                            <div class="form-group">
                                                <label>Số điện thoại nhận</label>
                                                <div class="input-group">
                                                    <input type="number" name="receiver" class="form-control" placeholder="Số điện thoại nhận tiền">
                                                    <div class="input-group-append">
                                                        <button type="button" class="btn btn-dark" id="get-name-2">KIỂM TRA</button>
                                                    </div>
                                                </div>
                                            </div>
                                            <input type="hidden" name="type" value="request">
                                            <input type="hidden" name="phone" value="<?= trim($_GET['phone']) ?>">
                                            <div class="form-group">
                                                <label>Số tiền: </label>
                                                <input type="number" name="amount" class="form-control" placeholder="Số tiền yêu cầu VNĐ" value="100" >
                                            </div>
                                            <div class="form-group">
                                                <label>Lời nhắn: </label>
                                                <input type="text" class="form-control" name="comment" placeholder="Lời nhắn">
                                            </div>
                                            <div class="form-group text-center">
                                                <button class="btn btn-primary" type="submit" id="submit-request">Xác Nhận</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card mt-4">
                        <div class="card-header">
                            <div class="row align-items-div">
                                <div class="col mt-2 mb-2">
                                    <h5 class="mb-0">Lịch sử ví momo</h5>
                                </div>
                                <div class="col-md-auto">
                                    <button class="btn btn-primary btn-sm" id="submit-update-proxy"> Cập Nhật Lịch Sử</button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="col-lg-12">
                                <div class="table-responsive table-responsive-data2">
                                    <table id="example" class="table table-data2 table-striped" style="border: none;">
                                        <thead class="text-center">
                                            <tr>
                                                <th style="width: 20px">#</th>
                                                <th>Số Điện Thoại</th>
                                                <th>Số Tiền</th>
                                                <th>Chuyển / Nhận</th>
                                                <th>Comment</th>
                                                <th>Trạng Thái</th>
                                                <th>Thời Gian</th>
                                            </tr>
                                        </thead>
                                        <tbody class="text-center">
                                            <?php
                                            $i = 1;
                                            foreach ($ListHis as $rows){
                                                if($rows['status'] == '999') {
                                                    $desc = '<span class="badge badge-success">'.$rows['desc'].'</span>';
                                                }
                                                else {
                                                    $desc = '<span class="badge badge-danger">'.$rows['desc'].'</span>';
                                                }
                                            ?>
                                            
                                            <tr class="tr-shadow">
                                                <td style="width: 20px"><?= $i ?></td>
                                                <td><span class="block-email"><?= $rows['partnerId'] ?></span></td>
                                                <td><?= $rows['amount'] ?></td>
                                                <td>
                                                    <?php echo $rows['io'] == '1' ? 'Nhận' : 'Chuyển'; ?>
                                                </td>
                                                <td><?php echo $rows['comment'] ?></td>
                                                <td><?php echo $desc ?></td>
                                                <td><?php echo date('d-m-Y H:i:s', round($rows['millisecond'] / 1000)) ?></td>
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
</div>
<script>
    $('#get-name-1').click(()=>{
        var formSend = $('#form-send').serializeArray();
        var recevicer = formSend[0]['value'];
        $.ajax({
            url: '<?php echo $site_url ?>modun/momo/send',
            dataType: 'JSON',
            type: 'POST',
            data: {
                type: 'get-name',
                receiver: recevicer,
                phone: '<?= trim($_GET['phone']) ?>'
            },
            success: (result) =>{
                if(result.status == 'success'){
                    $('#get-name-1').html(result.NAME).prop('disabled', true);
                }
                else {
                    Swal.fire({
                        icon:'error',
                        title: 'Thất Bại',
                        text: result.message
                    });
                }
            } 
        });
    });
    $('#get-name-2').click(()=>{
        var formSend = $('#form-request').serializeArray();
        var recevicer = formSend[0]['value'];
        $.ajax({
            url: '<?php echo $site_url ?>modun/momo/send',
            dataType: 'JSON',
            type: 'POST',
            data: {
                type: 'get-name',
                receiver: recevicer,
                phone: '<?= trim($_GET['phone']) ?>'
            },
            success: (result) =>{
                if(result.status == 'success'){
                    $('#get-name-2').html(result.NAME).prop('disabled', true);
                }
                else {
                    Swal.fire({
                        icon:'error',
                        title: 'Thất Bại',
                        text: result.message
                    });
                }
 
            } 
        });
    });
    $('#submit-send').click(()=> {
        $.ajax({
            url: '<?php echo $site_url ?>modun/momo/send',
            dataType: 'JSON',
            type: 'POST',
            data: $('#form-send').serialize(),
            success: (result) =>{
                if(result.status == 'success'){
                   Swal.fire('Thành Công', result.message, 'success');
                }
                else {
                    Swal.fire({
                        icon:'error',
                        title: 'Thất Bại',
                        text: result.message
                    });
                }
 
            } 
        });
    });
    $('#submit-request').click(()=> {
        $.ajax({
            url: '<?php echo $site_url ?>modun/momo/send',
            dataType: 'JSON',
            type: 'POST',
            data: $('#form-request').serialize(),
            success: (result) =>{
                if(result.status == 'success'){
                   Swal.fire('Thành Công', result.message, 'success');
                }
                else {
                    Swal.fire({
                        icon:'error',
                        title: 'Thất Bại',
                        text: result.message
                    });
                }
 
            } 
        });
    });
</script>
<script>
    $(document).ready(()=>{
        $('#example').DataTable();
    });
</script>
<?php 
require_once '../site/foot.php';
?>