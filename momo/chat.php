<?php
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
$site['title'] = 'Gửi Tin Nhắn Ví MoMo '.$phone. ' - '. $site['site_name'];
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
                                    <h5 class="mb-0">Gửi Tin Nhắn MoMo</h5>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="col-lg-12">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="card-title text-center">
                                            <h4 class="title">Gửi Tin Nhắn </h4>
                                        </div>
                                        <form action="javascript:void(0)" id="form-send" method="post" enctype="multipart/form-data">
                                            <div class="form-group">
                                                <label>Số điện thoại nhận</label>
                                                <div class="input-group">
                                                    <input type="number" name="receiver" class="form-control" placeholder="Số điện thoại nhận tin">
                                                    <div class="input-group-append">
                                                        <button type="button" class="btn btn-dark" id="get-name-1">KIỂM TRA</button>
                                                    </div>
                                                </div>
                                            </div>
                                            <input type="hidden" name="type" value="send-mess">
                                            <input type="hidden" name="phone" value="<?= trim($_GET['phone']) ?>">
                                            <div class="form-group">
                                                <label>Tin Nhắn: </label>
                                                <input type="text" class="form-control" name="message" placeholder="Nhập lời nhắn">
                                            </div>
                                            <div class="form-group">
                                                <label>Ảnh: </label>
                                                <input type="file" name="image" id="image" class="form-control">
                                            </div>
                                            <div class="form-group text-center">
                                                <button class="btn btn-primary" type="submit" id="submit-send">Xác Nhận</button>
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
                                                <th>SĐT Nhận</th>
                                                <th>Comment</th>
                                                <th>Trạng Thái</th>
                                                <th>Thời Gian</th>
                                            </tr>
                                        </thead>
                                        <tbody class="text-center">
                                            <?php
                                            $select = mysqli_query($conn, "SELECT * FROM `table_message` WHERE `sender` = '".$phone."' LIMIT 200 ");
                                            $i = 1;
                                            while($rows = mysqli_fetch_assoc($select)){
                                                if($rows['status'] == 'success'){
                                                    $_span = '<span class="badge text-white bg-success">Thành Công</span>';
                                                }
                                                else {
                                                    $_span = '<span class="badge text-white bg-danger">Thất Bại</span>';
                                                }
                                            ?>
                                            <tr class="tr-shadow">
                                                <td style="width: 20px"><?= $i ?></td>
                                                <td><span class="block-email"><?= $rows['sender'] ?></span></td>
                                                <td><?= $rows['receiver'] ?></td>
                                                <td>
                                                    <p><?php echo substr($rows['message'], 0, 20); ?></p>
                                                </td>
                                                <td><?= $_span ?></td>
                                                <td><?= $rows['time'] ?></td>
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
    $('#submit-send').click(()=> {
        fd = new FormData();
        var image = $('#image')[0].files[0];
        var Data = $('#form-send').serializeArray();
        fd.append('image', image);
        Data.forEach(element => fd.append(element["name"], element["value"]));
        $.ajax({
            url: '<?php echo $site_url ?>modun/momo/send',
            dataType: 'JSON',
            type: 'POST',
            contentType: false,
            processData: false,
            data: fd,
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