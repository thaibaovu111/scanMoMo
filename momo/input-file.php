<?php
require_once '../config.php';
if(empty($username)){
    header('Location: '. $site_url);
    die();
}
$site['title'] = 'Tải dữ liệu file lên hệ thống - '. $site['site_name'];
require_once '../site/head.php';
?>
<div class="main-content">
    <div class="section__content section__content--p30">
        <div class="container-fluid">
            <div class="row g-0">
                <div class="col-md-12">
                    <div class="overview-wrap">
                        <h2 class="title-1">Tải file tin nhắn</h2>
                    </div>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-lg-12">
                    <div class="card mb-3">
                        <div class="card-header">
                            <div class="row align-items-div">
                                <div class="col mt-2 mb-2">
                                    <h5 class="mb-0">Tải file tin nhắn</h5>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-6 justify-content-xl-center mx-auto">
                                    <div class="card-title text-center">
                                        <img width="80" src="<?php echo $site_public ?>/images/excel.png" alt="excel">
                                        <h5></h5>
                                    </div>
                                    <form action="javascript:void(0)" method="POST" id="form-input" enctype="multipart/form-data">
                                        <div class="form-group">
                                            <label>Tiêu Để File:</label>
                                            <input type="text" name="title" class="form-control" placeholder="Nhập tiêu đề" >
                                        </div>
                                        <div class="form-group">
                                            <label>File Excel</label>
                                            <input type="file" name="file" class="form-control" placeholder="File tin nhắn">
                                        </div>
                                        <div class="form-group text-center">
                                            <button class="btn btn-primary" id="submit-input" type="submit">Xác Nhận</button>
                                        </div>
                                    </form>
                                </div>
                                <div class="col-lg-6 mx-auto">
                                    <div class="card-title text-center">
                                        <h5>Nhập file dạng TXT</h5>
                                    </div>
                                    <form action="javascript:void(0)" id="form-txt" method="post" enctype="multipart/form-data">
                                        <div class="form-group">
                                            <label>Tiêu Đề File: </label>
                                            <input class="form-control" type="text" name="title-txt"  placeholder="Nhập tiêu đề file">
                                        </div>
                                        <div class="form-group">
                                            <label>Chữ Cố Định: <span class="text-dark">Vị Trí Mã Code Thay Thế Dấu {}</span></label>
                                            <input class="form-control" type="text" name="text" placeholder="Nhập dòng chữ cố định ví dụ: Mã code {} ">
                                        </div>
                                        <div class="form-group">
                                            <label>Số Ký Tự Random: </label>
                                            <input class="form-control" type="number" name="number_rand" placeholder="Nhập số ký tự random">
                                        </div>
                                        <div class="form-group">
                                            <label>File Số Điện Thoại: </label>
                                            <input class="form-control" type="file" name="file-txt">
                                        </div>
                                        <div class="form-group text-center">
                                            <button class="btn btn-primary" id="submit-input-txt" type="submit">Xác Nhận</button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="card mt-4">
                        <div class="card-header">
                            <div class="row">
                                <div class="col mt-2 mb-2">
                                    <h5 class="mb-0">Thông Tin Các File</h5>
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
                                                <th>Tiêu Đề File</th>
                                                <th>Tổng Tin Nhắn</th>
                                                <th>Đã Gửi</th>
                                                <th>Thành Công</th>
                                                <th>Thất Bại</th>
                                                <th>Ngày Tạo Đơn</th>
                                                <th>Hành Động</th>
                                            </tr>
                                        </thead>
                                        <tbody class="text-center">
                                            <?php 
                                            $select = mysqli_query($conn, "SELECT * FROM `table_excel` ORDER BY `id`  ");
                                            $i = 1;
                                            while ($rows = mysqli_fetch_assoc($select)){
                                            ?>
                                            <tr class="tr-shadow">
                                                <td style="width: 20px"><?= $i ?></td>
                                                <td><span class="block-email"><?= strtoupper($rows['title']) ?></span></td>
                                                <td>
                                                    <?= $rows['total'] ?>
                                                </td>
                                                <td>
                                                    <span class="badge bg-primary text-white">
                                                        <?php echo $total = $conn->query("SELECT COUNT(*) FROM `table_message` WHERE `file_id` = '".$rows['id']."' ")->fetch_assoc()['COUNT(*)']; ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-success text-white">
                                                        <?php echo $sucess = $conn->query("SELECT COUNT(*) FROM `table_message` WHERE `file_id` = '".$rows['id']."' AND `status` = 'success' ")->fetch_assoc()['COUNT(*)']; ?>
                                                    </span>
                                                    
                                                </td>
                                                <td>
                                                    <span class="badge bg-danger text-white">
                                                    <?php echo $total - $sucess ?>
                                                    </span>
                                                </td>
                                                <td><?= $rows['creat_times'] ?></td>
                                                <td>
                                                    <div class="dropdown font-sans-serif position-static">
                                                        <button class="btn btn-link text-600 btn-sm dropdown-toggle btn-reveal"
                                                            type="button" id="dropdownMenuButton" data-toggle="dropdown" data-boundary="window"
                                                            aria-haspopup="true" aria-expanded="false">
                                                            <span class="fas fa-ellipsis-h fs--1"></span>
                                                        </button>
                                                        <div class="dropdown-menu dropdown-menu-end border py-0" >
                                                            <div class="bg-white py-2">
                                                                <a class="dropdown-item" href="<?php echo $site_url ?>creat/excel?file=<?= $rows['id'] ?>">Tải Xuống</a>
                                                                <a class="dropdown-item text-success" href="<?php echo $site_url ?>momo/mess/?id=<?= $rows['id'] ?>">Auto Chat</a>
                                                                <button class="dropdown-item text-danger" onclick="delete_file('<?= $rows['id'] ?>')" >Xóa</button>
                                                            </div>
                                                        </div>
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
</div>
<script>
    $(document).ready(function() {
        $('#submit-input-txt').click(()=>{
                var title = $('input[name="title-txt"]').val();
                if(!title) {
                    Swal.fire('Lỗi', "Vui lòng nhập tiêu đề file", 'error');
                    return;
                }
                var input = new FormData();
                var file  = $('input[name="file-txt"]')[0].files[0];
                var text  = $('input[name="text"]').val();
                var number_rand = $('input[name="number_rand"]').val();
                input.append('file', file);
                input.append('text', text);
                input.append('number_rand', number_rand);
                input.append('title', title);
                input.append('type', 'save-txt');
                Swal.fire({
                    icon: 'info',
                    title: 'Xác nhận?',
                    text: `Xác nhận tải file dữ liệu gửi tin lên hệ thống`,
                    showDenyButton: true,
                    confirmButtonText: 'OK!',
                    denyButtonText: `Cancel`
                }).then((result)=> {
                    if(result.isConfirmed){
                        $.ajax({
                            url : '<?php echo $site_url ?>modun/momo/input-file',
                            data: input,
                            dataType: 'JSON',
                            contentType: false,
                            processData: false,
                            type: 'POST',
                            success: (data) => {
                                if(data.status == 'success') {
                                    Swal.fire('Thành công',data.message, 'success');
                                    setTimeout(() => {
                                        window.location.href = '<?php echo $site_url ?>momo/input-file'
                                    }, 3000);
                                }
                                else {
                                    Swal.fire('Thất Bại',data.message, 'error');
                                }
                            }
                        });
                    }
                });
        });



        $('#submit-input').on('click',() => {
                var title = $('input[name="title"]').val();
                if(!title) {
                    Swal.fire('Lỗi', "Vui lòng nhập tiêu đề file", 'error');
                    return;
                }
                var input = new FormData();
                var file  = $('input[name="file"]')[0].files[0];
                input.append('file', file);
                input.append('title', title);
                input.append('type', 'save');
                Swal.fire({
                    icon: 'info',
                    title: 'Xác nhận?',
                    text: `Xác nhận tải file dữ liệu gửi tin lên hệ thống`,
                    showDenyButton: true,
                    confirmButtonText: 'OK!',
                    denyButtonText: `Cancel`
                }).then((result)=> {
                    if(result.isConfirmed){
                        $.ajax({
                            url : '<?php echo $site_url ?>modun/momo/input-file',
                            data: input,
                            dataType: 'JSON',
                            contentType: false,
                            processData: false,
                            type: 'POST',
                            success: (data) => {
                                if(data.status == 'success') {
                                    Swal.fire('Thành công',data.message, 'success');
                                    setTimeout(() => {
                                        window.location.href = '<?php echo $site_url ?>momo/input-file'
                                    }, 3000);
                                }
                                else {
                                    Swal.fire('Thất Bại',data.message, 'error');
                                }
                            }
                        });
                    }
                }); 
        })
    });
    function delete_file(file) {
        if(!file) {
            Swal({
                icon: 'error',
                title: 'Oops..',
                text: "Đã xảy ra lỗi vui lòng thử lại"
            });
            return false;
        }

        Swal.fire({
            title: 'Bạn sẽ xóa',
            text: `Bạn chắc sẽ xóa file này ra khỏi hệ thống`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Chấp nhận xóa',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '<?php echo $site_url ?>modun/momo/input-file',
                        dataType: 'JSON',
                        type: 'POST',
                        data: {
                            type: 'delete',
                            file: file
                        },
                        success: (data) => {
                            if(data.error) {
                                Swal.fire({icon: 'error', title: 'Lỗi', text: data.message});
                                return false;
                            }
                            else if(data.success) {
                                Swal.fire({icon: 'success', title: 'Thành Công', text: data.message}).then(()=> {
                                    window.location.reload();
                                });
                            }

                        }
                    });
                }
        });
    }
</script>
<?php
require_once '../site/foot.php';
?>