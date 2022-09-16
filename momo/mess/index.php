<?php
require_once '../../config.php';
if(empty($username)){
    header('Location: '. $site_url);
    die();
}
if(empty($_GET['id'])) {
    header('Location: '.$site_url.'momo/auto-chat');
    die();
}
$id = Request::Clean_GET('id');
$creat = $conn->query("SELECT * FROM `table_excel` WHERE `id` = '$id' ");
if(empty($creat->num_rows)) {

    header('Location: '.$site_url.'momo/auto-chat');
    die();
}
$site['title'] = 'Auto chat momo - '. $site['site_name'];
require_once '../../site/head.php';
?>
<div class="main-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="overview-wrap">
                    <h2 class="title-1">Thống Kê</h2>
                </div>
            </div>
        </div>
        <div class="row g-0">
            <div class="col-lg-12">
                <div class="row g-0 mt-3">
                    <div class="col-sm-6 col-lg-3">
                        <div class="overview-item overview-item--c1">
                            <div class="overview__inner">
                                <div class="overview-box clearfix">
                                    <div class="icon">
                                        <i class="fab fa-facebook-messenger"></i>
                                    </div>
                                    <div class="text">
                                        <?php $total = ($conn->query("SELECT COUNT(*) FROM `table_message` WHERE `file_id` = '$id' ")->fetch_assoc()['COUNT(*)']) ?? 0; ?>
                                        <h2 id="total"><?= $total ?></h2>
                                        <span>Số Tin Đã Gửi</span>
                                    </div>
                                </div>
                                <div class="overview-chart">
                                    <canvas id="widgetChart1"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div class="overview-item overview-item--c2">
                            <div class="overview__inner">
                                <div class="overview-box clearfix">
                                    <div class="icon">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                    <div class="text">
                                        <?php
                                        $success = $conn->query("SELECT COUNT(*) FROM `table_message` WHERE `status` = 'success' AND `file_id` = '$id' ")->fetch_assoc()['COUNT(*)'] ?? 0;
                                        ?>
                                        <h2 id="success"><?= $success ?></h2>
                                        <span>Thành Công</span>
                                    </div>
                                </div>
                                <div class="overview-chart">
                                    <canvas id="widgetChart2"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div class="overview-item overview-item--c3">
                            <div class="overview__inner">
                                <div class="overview-box clearfix">
                                    <div class="icon">
                                        <i class="fas fa-times"></i>
                                    </div>
                                    <div class="text">
                                        <?php
                                        $error = $conn->query("SELECT COUNT(*) FROM `table_message` WHERE `status` = 'error' AND `file_id` = '$id' ")->fetch_assoc()['COUNT(*)'] ?? 0;
                                        ?>
                                        <h2 id="error"><?= $error ?></h2>
                                        <span>Thất Bại</span>
                                    </div>
                                </div>
                                <div class="overview-chart">
                                    <canvas id="widgetChart3"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div class="overview-item overview-item--c4">
                            <div class="overview__inner">
                                <div class="overview-box clearfix">
                                    <div class="icon">
                                        <i class="fas fa-percent"></i>
                                    </div>
                                    <div class="text">
                                        <h2><?php 
                                        if($total == 0) echo '0'; else echo round(($success / $total) * 100);
                                        
                                        ?></h2>
                                        <span>Tỷ Lệ</span>
                                    </div>
                                </div>
                                <div class="overview-chart">
                                    <canvas id="widgetChart4"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="card mb-3">
                            <div class="card-header">
                                <div class="row align-items-div">
                                    <div class="col mt-2 mb-2">
                                        <h5 class="mb-0">Danh sách tin nhắn đã được gửi</h5>
                                    </div>
                                    <button class="btn btn-primary mr-1" id="re-submit-chat">Gửi Lại Tin Lỗi </button>
                                    <button class="btn btn-success mr-5" id="submit-chat">Bắt Đầu</button>
                                </div>
                            </div>
                            <input type="hidden" id="switch" value="OFF">
                            <input type="hidden" id="re-chat" value="OFF">
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
                                            $select = mysqli_query($conn, "SELECT * FROM `table_message` WHERE `file_id` = '$id' ORDER BY `id` ");
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
    $(document).ready(function() {
        $('#example').DataTable();
    });
    $('#submit-chat').on('click', () => {
        var switchs   = $('#switch').val();
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

    $('#re-submit-chat').click(()=>{
            var submit = $('#re-chat').val();
            if(submit == 'ON'){
                $('#re-submit-chat').html('Bắt Đầu Lại').removeClass('btn-danger').toggleClass('btn-primary');
                $('#re-chat').val('OFF');
                return false;
            }
            else if(submit == 'OFF'){
                $('#re-submit-chat').html('Dừng').removeClass('btn-primary').toggleClass('btn-danger');
                $('#re-chat').val('ON');
                setTimeout(ReChatMess,10);
            }


    });

    function ReChatMess(){
            var submit = $('#re-chat').val();
            if(submit == 'OFF'){
                return false;
            }
            $.ajax({
                url: '<?php echo $site_url ?>modun/momo/mess/',
                data: {
                    file: '<?= $id ?>'
                },
                dataType: 'JSON',
                type: 'POST',
                success: (data) => {
                    $('#example').append();
                    if(data.success) {
                        $('#success').html(Number($('#success').html()) + 1 );
                        $('#error').html(Number($('#error').html()) - 1 );
                        $.toast({
                            heading: 'Thành Công',
                            text: data.message,
                            icon: 'success'
                        });
                    }
                    else {
                        $.toast({
                            heading: 'Thất Bại',
                            text: data.message,
                            icon: 'error'
                        });
                    }
                }
            }).always(()=>{
                setTimeout(ReChatMess, 2000);
            });
        }

    function ChatMess() {
        var switchs = $('#switch').val();
        if(switchs == 'OFF'){
            return false;
        }
        $.ajax({
            url: '<?php echo $site_url ?>modun/momo/mess/muilty-mess',
            data: {
                file_id: '<?= $id ?>'
            },
            dataType: 'JSON',
            type: 'POST',
            success: (data) => {
                $('#total').html(Number($('#total').html()) + 1 );
                $('#example').append();
                $('#success').html(Number($('#success').html()) + data.length );
                $.toast({
                    heading: 'Thành Công',
                    text: `Gửi thành công tổng ${data.length} tin nhắn / 20`,
                    icon: 'success'
                });
            }
        }).always(()=>{
            setTimeout(ChatMess, 2000);
        });

    }
</script>
<?php 
require_once '../../site/foot.php';
?>