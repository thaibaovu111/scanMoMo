<?php
require_once '../config.php';
if(empty($username)){
    header('Location: '. $site_url);
    die();
}
$site['title'] = 'Auto Đăng Ký Tài Khoản MoMo - '.$site['site_name'];
require_once '../site/head.php';
?>
<div class="main-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="overview-wrap">
                    <h2 class="title-1">Auto Đăng Ký MoMo</h2>
                    <button class="au-btn au-btn-icon au-btn--blue"><i class="zmdi zmdi-plus"></i>Trang Chủ</button>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="card mt-4">
                    <div class="card-header">
                        <div class="row align-items-div">
                            <div class="col mt-2 mb-2">
                                <h5 class="mb-0">Đăng Ký Tài Khoản MoMo</h5>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-6 justify-content-xl-center mx-auto">
                                <div class="card-title text-center">
                                    <div class="title">
                                        <img src="<?= $site_public ?>/images/logo-momo.png" width="50" alt="MoMo">
                                        <h5 class="title">Đăng Ký MoMo</h5>
                                    </div>
                                </div>
                                <form action="javascript:void(0)" id="form-register" method="post">
                                    <div class="form-group">
                                        <label>Thời Gian: </label>
                                        <input type="number" id="times" class="form-control" placeholder="Thời gian sau Reg 1 tài khoản">
                                    </div>
                                    <input type="hidden" name="switch"  id="switch" value="OFF">
                                    <div class="form-group">
                                        <label>Mật Khẩu Chung: </label>
                                        <input type="number" id="pass-setting" class="form-control" placeholder="Mật Khẩu">
                                    </div>
                                    <div class="form-group text-center">
                                        <button class="btn btn-primary" id="submit-register" type="submit">Bắt Đầu</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card mt-4">
                    <div class="card-header">
                        <div class="row align-items-div">
                            <div class="col mt-2 mb-2">
                                <h5 class="mb-0">Hệ Thống Reg Acc Tự Động</h5>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-6 justify-content-xl-center mx-auto">
                                <div class="card-title text-center">
                                    <div class="title">
                                        <img src="<?= $site_public ?>/images/logo-momo.png" width="50" alt="MoMo">
                                        <h5 class="title">Đăng Ký MoMo</h5>
                                    </div>
                                </div>
                                <form action="javascript:void(0)" id="form-register-auto" method="post">
                                    <div class="form-group">
                                        <label>Số Điện Thoại: </label>
                                        <input type="number" id="phone" name="phone" class="form-control" placeholder="Số điện thoại get từ API" disabled>
                                    </div>
                                    <div class="form-group">
                                        <label>Họ Và Tên: </label>
                                        <input type="text" id="name" name="name" class="form-control" placeholder="Họ và tên lấy random" disabled>
                                    </div>
                                    <div class="form-group">
                                        <label>Giới Tính: </label>
                                        <input type="number" name="sex" id="sex" class="form-control" placeholder="Giới tính" disabled>
                                    </div>
                                    <input type="hidden" name="id_sms" id="id-sms">
                                    <input type="hidden" id="number" value="0" >
                                    <div class="form-group">
                                        <label>Mật Khẩu: </label>
                                        <input type="number" id="pass" name="pass" class="form-control" placeholder="Mật Khẩu Cài Đặt" disabled>
                                    </div>
                                    <div class="form-group">
                                        <label>Mã OTP: </label>
                                        <input type="number" id="otp" name="otp" class="form-control" placeholder="Mã OTP Hệ Thống Tự Get" disabled>
                                    </div>
                                    <div class="form-group text-center">
                                        <div id="sufee-alert" class="sufee-alert alert with-close alert-success alert-dismissible fade show">
											<span id="span-alert" class="badge badge-pill badge-success"></span>
											<span id="message">Thông báo:</span>
											<button type="button" class="close" data-dismiss="alert" aria-label="Close">
												<span aria-hidden="true">×</span>
											</button>
										</div>
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
    $('#submit-register').click(() =>{
       var times = $('#times').val();
       var pass_setting = document.getElementById('pass-setting').value;
       var switchs   = $('#switch').val();
       if(!times) {
           Swal.fire('Thất Bại', 'Vui lòng nhập thời gian ', 'error');
           return false;
       }
       else if(!pass_setting) {
            Swal.fire('Thất Bại', 'Vui lòng nhập mật khẩu chung', 'error');
            return false;
       }
       else if(pass_setting.length != 6){
            Swal.fire('Thất Bại', 'Mật khẩu chứa 6 ký tự số', 'error');
            return false;
       }
       if(switchs == 'ON'){
            $('#submit-register').html('Bắt Đầu Lại').removeClass('btn-danger').toggleClass('btn-primary');
            $('#switch').val('OFF');
            return false;
        }
        else if(switchs == 'OFF'){
            $('#submit-register').html('Dừng').removeClass('btn-primary').toggleClass('btn-danger');
            $('#switch').val('ON');
            setTimeout(GetInfo, 200);  
        }
       $('#pass').val(pass_setting);

    });
    function GetInfo(){
        var switchs = $('#switch').val();
        if(switchs == 'OFF'){
            return false;
        }
        $.ajax({
            url: '<?= $site_url ?>modun/momo/get-info-register',
            dataType: 'JSON',
            success: (result) => {
                ThongBao(result);
                if(result.success) {
                    $('#phone').val(result.phone); $('#sex').val(result.sex);
                    $('#name').val(result.name);$('#id-sms').val(result.id);
                    $('#otp').val('');
                    setTimeout(SendInfo, 2000);
                }
                else {
                    if(result.code == -5){
                        return false;
                    }
                    else {
                        setTimeout(GetInfo, 2000);
                    }
                }
            }
        });
    }

    function SendInfo(){
        $.ajax({
            url: '<?php echo $site_url ?>modun/momo/auth',
            data: {
                phone: $('#phone').val(),
                name : $('#name').val(),
                sex: $('#sex').val(),
                type: 'register',
                pass: $('#pass').val(),
                otp: $('#otp').val()
            },
            dataType: 'JSON',
            type: 'POST',
            success: (result) => {
                ThongBao(result);
                if(result.status == 'success'){
                    if(!$('#otp').val()){
                        setTimeout(GetOTP, 3000);
                        return false;
                    }
                    else {
                        setTimeout(GetInfo, $('#times').val() * 1000);
                    }
                }
                else {
                    setTimeout(GetInfo, $('#times').val() * 1000);
                }

            },
            error: () => {
                setTimeout(GetInfo, $('#times').val() * 1000);
                $.toast({
                    icon:'error',
                    text: 'Đã xảy ra lỗi máy chủ xin vui lòng thử lại'
                });
            }
        });
    }

    function run(){
        var code = $('#otp').val();
        if($('#number').val() >= 8){
            $('#number').val(0);
            Delete($('#phone').val());
            setTimeout(GetInfo, 2000);
            return false;
        }
        if(code != ''){
            setTimeout(SendInfo,2000);
        }
        else {
            setTimeout(GetOTP, 6000);
        }
    }

    function GetOTP(){
        var phone = $('#phone').val();
        var id    = $('#id-sms').val();
        $.ajax({
            url: '<?= $site_url ?>modun/momo/get-otp',
            type: 'POST',
            dataType: 'JSON',
            data: {
                id: id,
                phone: phone,                
            },
            success: (result) => {
                ThongBao(result);
                if(result.success){
                    $('#otp').val(result.code);
                    $('#number').val(0);
                }
                else {
                    $('#number').val(parseInt($('#number').val()) + 1);

                }
            }
        }).always(()=>{
            run();
        });
    }

    function Delete(phone) {
        $.ajax({
            url: '<?php echo $site_url ?>modun/momo/setting',
            dataType: 'JSON',
            type: 'POST',
            data: {
                type: 'delete',
                phone: phone
            },
            success: (data) => {
                ThongBao(data);
            }
        });
    }

    function ThongBao(result) {
        $.toast({
            text: result.message,
            icon: result.status,
            position: 'top-right'
        });
        if(result.status == 'success'){
            $('#span-alert').toggleClass('badge-danger badge-success');
            $('#sufee-alert').toggleClass('alert-danger alert-success');
        }
        else {
            $('#span-alert').toggleClass('badge-success badge-danger');
            $('#sufee-alert').toggleClass('alert-success alert-danger');
        }
        $('#span-alert').html(result.status ?? 'success');
        $('#message').html(result.message);
    }
</script>
<?php
require_once '../site/foot.php';
?>