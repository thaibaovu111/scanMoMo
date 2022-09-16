<?php
require_once '../config.php';
if(empty($username)){
    header('Location: '. $site_url);
    die();
}
$site['title'] = 'Đăng ký ví MOMO - '. $site['site_name'];
require_once '../site/head.php';
?>
<div class="main-content">
    <div class="section__content section__content--p30">
        <div class="container-fluid">
            <div class="row g-0">
                <div class="col-md-12">
                    <div class="overview-wrap">
                        <h2 class="title-1">Đăng Ký Tài Khoản MOMO</h2>
                    </div>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-lg-12">
                    <div class="card mb-3">
                        <div class="card-header">
                            <div class="row align-items-div">
                                <div class="col mt-2 mb-2">
                                    <h5 class="mb-0">Đăng Ký MoMo</h5>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="col-lg-8 justify-content-xl-center mx-auto">
                                <div class="card-title text-center">
                                    <img width="50" src="<?php echo $site_public ?>/images/logo-momo.png" alt="Ví MoMo">
                                    <h5>Đăng Ký MoMo</h5>
                                </div>
                                <form action="javascript:void(0)" method="POST" id="form-register" >
                                    <div class="form-group">
                                        <label>Số Điện Thoại:</label>
                                        <input type="number" name="phone" class="form-control" placeholder="Nhập số điện thoại" >
                                    </div>
                                    <div class="form-group">
                                        <label>Mật Khẩu Mới:</label>
                                        <input type="number" name="pass" class="form-control" placeholder="Nhập mật khẩu">
                                    </div>
                                    <div class="form-group">
                                        <label>Nhập Họ Và Tên:</label>
                                        <input type="text" name="name" class="form-control" placeholder="Nhập họ tên">
                                    </div>
                                    <div class="form-group">
                                        <label>Chọn giới tính:</label>
                                        <select name="sex">
                                            <option value="1">Nam</option>
                                            <option value="2">Nữ</option>
                                        </select>
                                    </div>
                                    <input type="hidden" name="type" value="register">
                                    <div class="form-group" id="otphide">
                                        <label>Mã Xác Nhận OTP:</label>
                                        <input type="number" name="otp" class="form-control" placeholder="Mã OTP">
                                    </div>
                                    <div class="form-group text-center">
                                        <button class="btn btn-primary" id="submit-register" type="submit">Lấy Mã OTP</button>
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
<script>
    $('#otphide').hide();
    $('#submit-register').on('click', () => {
        $.ajax({
            url: '<?php echo $site_url ?>modun/momo/auth',
            data: $('#form-register').serialize(),
            dataType: 'JSON',
            type: 'POST',
            success: (result) => {
                if(result.status == 'success'){
                    Swal.fire({
                        icon: 'success',
                        title: 'Thành Công',
                        text: result.message
                    });
                    if($('input[name="otp"]').val()){
                        setTimeout(function(){
                            window.location.reload();
                        }, 3000)
                    }
                    $('#otphide').fadeIn();
                    $('#submit-register').html('Xác Nhận');
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