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
$site['title'] = 'Quản Lý Ngân Hàng  '.$phone. ' - '. $site['site_name'];

$result_momo = $momo->LoadData($phone)->LoginTimeSetup();
if($result_momo['status'] == 'error'){
    header('Location: '. $site_url);
    die();
}
$result = $momo->LoadData($phone)->service();
$napasBanks = $result['napasBanks'];

if(empty($_SESSION[$phone])){
    $result = $momo->LoadData($phone)->CheckHis(100);

    $_SESSION[$phone] = $result['status'] == 'error' ? array() : $result['TranList'];

}

$ListHis =  $_SESSION[$phone];

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
                                    <h5 class="mb-0">Quản Lý Ngân Hàng Tài Khoản MoMo</h5>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="col-lg-12">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="card-title text-center">
                                            <h4 class="title">Chuyển tiền Ngân Hàng</h4>
                                        </div>
                                        <form action="javascript:void(0)" id="form-send" method="post">
                                            <div class="form-group">
                                                <label>Số Tài Khoản: </label>
                                                <div class="input-group">
                                                    <input type="number" name="accid" class="form-control" placeholder="Số tài khoản nhận tiền">
                                                    <div class="input-group-append">
                                                        <button type="button" class="btn btn-dark" id="get-name-1">KIỂM TRA</button>
                                                    </div>
                                                </div>
                                            </div>
                                            <input type="hidden" name="type" value="send-bank">
                                            <input type="hidden" name="phone" value="<?= trim($_GET['phone']) ?>">
                                            <div class="form-group">
                                                <label>Ngân Hàng: </label>
                                                <select name="bankName" data-show-subtext="true" data-live-search="true" class="selectpicker form-control">
                                                    <?php
                                                    foreach ($napasBanks as $item){
                                                    ?>
                                                    <option value="<?= $item['shortBankName'] ?>"><?= $item['bankName'] ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
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
                                            <h4 class="title">Liên Kết Thẻ Napas</h4>
                                        </div>
                                        <form action="javascript:void(0)" id="form-request" method="post">
                                            <div class="form-group">
                                                <label>Số Thẻ</label>
                                                <div class="input-group">
                                                    <input type="number" name="cardNumber" class="form-control" placeholder="Nhập số thẻ">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label>Tên chủ thẻ: </label>
                                                <input type="text" name="name" class="form-control" placeholder="Nhập tên chủ thẻ">
                                            </div>
                                            <input type="hidden" name="type" value="napas">
                                            <input type="hidden" name="phone" value="<?= trim($_GET['phone']) ?>">
                                            <div class="form-group">
                                                <label>Ngày Hết Hạn </label>
                                                <input type="text" name="date" class="form-control" placeholder="AA/DD" value="" >
                                            </div>
                                            <div class="form-group" id="otp-hidden">
                                                <label>Mã OTP Xác Nhận: </label>
                                                <input type="number" class="form-control" name="otp" placeholder="Nhập Mã Xác Nhận OTP">
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
        var bankName  = formSend[3]['value'];
        $.ajax({
            url: '<?php echo $site_url ?>modun/momo/bank',
            dataType: 'JSON',
            type: 'POST',
            data: {
                type: 'check-name',
                bankName: bankName,
                accid: recevicer,
                phone: '<?= trim($_GET['phone']) ?>'
            },
            success: (result) =>{
                if(result.status == 'success'){
                    $('#get-name-1').html(result.name).prop('disabled', true);
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
            url: '<?php echo $site_url ?>modun/momo/bank',
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
    $('#otp-hidden').hide();
    $('#submit-request').click(()=> {
        var otp = $('input[name="otp"]').val();
        if(otp) {
            window.open('<?= $site_url ?>modun/momo/confirm?code=' + otp + '&phone=<?= $phone ?>');
        }
        $.ajax({
            url: '<?php echo $site_url ?>modun/momo/bank',
            dataType: 'JSON',
            type: 'POST',
            data: $('#form-request').serialize(),
            success: (result) =>{
                if(result.status == 'success'){
                   Swal.fire('Thành Công', result.message, 'success');
                   $('#otp-hidden').show();
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