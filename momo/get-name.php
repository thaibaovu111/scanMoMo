<?php
require_once '../config.php';
if(empty($username)){
    header('Location: '. $site_url);
    die();
}
$site['title'] = 'Check Tên Ví MoMo - '. $site['site_name'];
require_once '../site/head.php';
?>
<div class="main-content">
    <div class="section__content section__content--p30">
        <div class="container-fluid">
            <div class="row g-0">
                <div class="col-md-12">
                    <div class="overview-wrap">
                        <h2 class="title-1">Check Tài Khoản MoMo</h2>
                    </div>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-lg-12">
                    <div class="card mb-3">
                        <div class="card-header">
                            <div class="row align-items-div">
                                <div class="col mt-2 mb-2">
                                    <h5 class="mb-0">Check Ví MoMo</h5>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="col-lg-8 justify-content-xl-center mx-auto">
                                <div class="card-title text-center">
                                    <img width="50" src="<?php echo $site_public ?>/images/logo-momo.png" alt="Ví MoMo">
                                    <h5>Check Ví MoMo</h5>
                                </div>
                                <form action="javascript:void(0)" method="POST" id="form-login" >
                                    <div class="form-group">
                                        <label>Nhập List Số Điện Thoại:</label>
                                        <textarea name="number" class="form-control" id="number" cols="5" rows="10" placeholder=
                                        "Example: 
                                        0987654321
                                        0123456789">
                                       </textarea>
                                    </div>
                                    <div class="form-group text-center">
                                        <button class="btn btn-primary" id="submit-get-name" type="submit">Bắt Đầu</button>
                                    </div>
                                </form>
                            </div>
                            <input type="hidden" id="phone" value="" >
                            <div class="col-lg-8 justify-content-xl-center mx-auto">
                                <form action="<?= $site_url ?>creat/excel" method="post">
                                    <div class="form-group">
                                        <label>Thông Tin: </label>
                                        <textarea name="data" id="info" cols="5" rows="10" class="form-control"></textarea>
                                    </div>
                                    <div class="form-group text-center">
                                        <button class="btn btn-success" id="submit-download" type="submit">Tải Xuống</button>
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
$(document).ready(function () {
        var separator, data = [];

        $("#submit-get-name").click(function () {
            data = $('#number').val().trim().split("\n");
            $("#submit-get-name").html('Đang Chạy');
            run(0);
        });

        function run(index) {
            if (index < data.length) {
                GetName(index);
            } else {
                $("#submit-get-name").html('Bắt Đầu');
            }
        }

        function GetName(index) {
            var phone = data[index].trim();
            $('#phone').val(phone);
            $.post('<?= $site_url ?>modun/momo/send', {receiver: phone, type: 'get-name'}, function (data) {
                if (data.status == 'success') {

                    var token = data.NAME;
                    var current = $('#info').val();
                    if (token != undefined && token != "" && token.trim() != "") {
                        $('#info').val(current + $('#phone').val() + '|' + token.trim() + "\n");
                        
                    }
                }
                else {
                    $.toast({
                        icon: 'error',
                        text: data.message
                    })
                }

            },'JSON').always(function () {
                run(index + 1);
            });
        }
    });

</script>
<?php
require_once '../site/foot.php';
?>