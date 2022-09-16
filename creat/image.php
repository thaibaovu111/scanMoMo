<?php
require_once '../config.php';
if(empty($username)){
    header('Location: '. $site_url);
    die();
}
$site['title'] = 'Tạo Đường Dẫn Ảnh Gửi Mess MoMo - '. $site['site_name'];
require_once '../site/head.php';
?>
<div class="main-content">
    <div class="section__content section__content--p30">
        <div class="container-fluid">
            <div class="row g-0">
                <div class="col-md-12">
                    <div class="overview-wrap">
                        <h2 class="title-1">Tạo Link Ảnh MoMo</h2>
                    </div>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-lg-12">
                    <div class="card mb-3">
                        <div class="card-header">
                            <div class="row align-items-div">
                                <div class="col mt-2 mb-2">
                                    <h5 class="mb-0">Tạo Link Ảnh MoMo</h5>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="col-lg-8 justify-content-xl-center mx-auto">
                                <div class="card-title text-center">
                                    <img width="50" src="<?php echo $site_public ?>/images/logo-momo.png" alt="Ví MoMo">
                                    <h5>Tạo Link Ảnh MoMo</h5>
                                </div>
                                <form action="javascript:void(0)" method="POST" id="form-login" enctype="multipart/form-data">
                                    <div class="form-group">
                                        <label>Tiêu Đề</label>
                                        <input type="text" name="title" class="form-control" placeholder="Nhập tiêu đề ảnh" >
                                    </div>
                                    <div class="form-group">
                                        <label>Ảnh:</label>
                                        <input type="file" name="image" class="form-control" placeholder="Nhập mật khẩu">
                                    </div>
                                    <div class="form-group text-center">
                                        <button class="btn btn-primary" id="submit-login" type="submit">Tạo Link</button>
                                    </div>
                                </form>
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
                                                <th>Tiêu Đề</th>
                                                <th>Link Ảnh</th>
                                                <th>Thời Gian</th>
                                            </tr>
                                        </thead>
                                        <tbody class="text-center">
                                            <?php
                                            $select = mysqli_query($conn, "SELECT * FROM `table_images` LIMIT 200 ");
                                            $i = 1;
                                            while($rows = mysqli_fetch_assoc($select)){
                                            ?>
                                            <tr class="tr-shadow">
                                                <td style="width: 20px"><?= $i ?></td>
                                                <td><span class="block-email"><?= $rows['title'] ?></span></td>
                                                <td><a class="nav-link" href="<?= $rows['image_url'] ?>"><?= $rows['image_url'] ?></a></td>
                                                <td><?= $rows['creat_date'] ?></td>
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
    $('#submit-login').on('click', () => {
        fd = new FormData();
        var title = $('input[name="title"]').val();
                if(!title) {
                    Swal.fire('Lỗi', "Vui lòng nhập tiêu đề file", 'error');
                    return;
        }
        var input = new FormData();
        var file  = $('input[name="image"]')[0].files[0];
        input.append('image', file);
        input.append('title', title);
        $.ajax({
            url: '<?php echo $site_url ?>modun/creat/image',
            data: input,
            dataType: 'JSON',
            type: 'POST',
            contentType: false,
            processData: false,
            success: (result) => {
                if(result.status == 'success'){
                    Swal.fire({
                        icon: 'success',
                        title: 'Thành Công',
                        text: result.message
                    }).then(()=>{
                        window.location.reload();
                    });

                }
                else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Thất Bại',
                        text: result.message
                    });
                }

            },
            error: () => {
                Swal.fire({
                        icon: 'error',
                        title: 'Oops..',
                        text: 'Đã xảy ra lỗi với máy chủ vui lòng báo với admin'
                    });
            }
        });
    })
</script>
<?php
require_once '../site/foot.php';
?>