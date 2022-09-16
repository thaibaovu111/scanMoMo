<?php
require_once '../config.php';
if(empty($username)) {
    header('Location: '. $site_url);
    die();
}
$site['title'] = 'Tự động gửi tin nhắn - '. $site['site_name'];
require_once '../site/head.php';
?>
<div class="main-content">
    <div class="section__content section__content--p30">
        <div class="container-fluid">
            <div class="row g-0">
                <div class="col-lg-12">
                    <div class="col-lg-12">
                        <div class="overview-wrap">
                            <h2 class="title-1">Auto Chat</h2>
                            <button class="au-btn au-btn-icon au-btn--blue">
                                <i class="zmdi zmdi-plus"></i>Thêm File Excel</button>
                        </div>
                    </div>
                    <div class="row g-0 mt-3">
                        <div class="col-lg-12">
                            <div class="card mb-3">
                                <div class="card-header">
                                    <div class="row align-items-div">
                                        <div class="col mt-2 mb-2">
                                            <h5 class="mb-0">Danh sách tin nhắn đã được gửi</h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive table-responsive-data2">
                                        <table id="example" class="table table-data2 table-striped" style="border: none;">
                                            <thead class="text-center">
                                                <tr>
                                                    <th style="width: 20px">#</th>
                                                    <th>SĐT Gửi</th>
                                                    <th>SĐT Nhận</th>
                                                    <th>Tin Nhắn</th>
                                                    <th>Trạng Thái</th>
                                                    <th>Thời Gian</th>
                                                </tr>
                                            </thead>
                                            <tbody class="text-center">
                                                <?php
                                                $select = mysqli_query($conn, "SELECT * FROM `table_message` ORDER BY `id` ");
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
                    <div class="row mt-3">
                        <div class="col-lg-12">
                            <div class="card mt-2 mb-3">
                                <div class="card-header">
                                    <div class="row">
                                        <div class="col mt-2 mb-2">
                                            <h5 class="mb-0">Chạy Auto Chat MoMo</h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="col-lg-8 justify-content-xl-center mx-auto">
                                        <div class="card-title text-center">
                                            <img src="<?php echo $site_public ?>/images/message.jpg" width="50" alt="chat">
                                        </div>
                                        <form action="javascript:void(0)" id="form-chat" method="post">
                                            <div class="form-group">
                                                <label>Thời gian giữa mỗi tin: (Giây)</label>
                                                <input type="number" id="times" class="form-control" placeholder="Nhập thời gian" >
                                            </div>
                                            <div class="form-group">
                                                <label>File Data Mess: </label>
                                                <select class="form-control" id="file_id" name="file_id">
                                                    <?php 
                                                    $file_select = mysqli_query($conn, "SELECT * FROM `table_excel` ");
                                                    while($row = mysqli_fetch_assoc($file_select)){
                                                    ?>
                                                    <option value="<?php echo $row['id'] ?>"><?php echo strtoupper($row['title']) ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                            <input type="hidden" id="switch" value="OFF">
                                            <div class="form-group text-center">
                                                <button class="btn btn-primary" id="submit-chat" type="submit">Bắt Đầu Chạy</button>
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
    </div>
</div>
<script>
    $(document).ready(function() {
        $('#example').DataTable();
    } );
</script>
<script>
    $('#submit-chat').on('click', () => {
            var timedelay = $('#times').val();
            var file_id   = $('#file_id').val();
            var switchs   = $('#switch').val();
            if(!timedelay) {
                Swal.fire('Warning', 'Vui lòng nhập thời gian để chạy', 'warning');
                return false;
            }
            else if(!file_id) {
                Swal.fire('Warning', 'Vui lòng chọn file dữ liệu để chạy', 'warning');
                return false;
            }
            if(switchs == 'ON'){
                $('#submit-chat').html('Bắt Đầu Lại').removeClass('btn-danger').toggleClass('btn-primary');
                $('#switch').val('OFF');
                return false;
            }
            else if(switchs == 'OFF'){
                $('#submit-chat').html('Dừng').removeClass('btn-primary').toggleClass('btn-danger');
                $('#switch').val('ON');
                setTimeout(ChatMess,10);
            }
    });

    function ChatMess() {
        var switchs = $('#switch').val();
        if(switchs == 'OFF'){
            return false;
        }
        $.ajax({
            url: '<?php echo $site_url ?>modun/momo/mess/muilty-mess',
            data: {file_id: $('#file_id').val()},
            dataType: 'JSON',
            type: 'POST',
            success: (data) => {
                $.toast({
                        heading: 'Thành Công',
                        text: `Gửi thành công tổng ${data.length} tin nhắn / 20`,
                        icon: 'success'
                    });
            }
        }).always(()=>{
            setTimeout(ChatMess, $('#times').val() * 1000);
        });

    }
</script>
<?php
require_once '../site/foot.php';
?>