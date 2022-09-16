<?php
require_once '../config.php';
if(empty($username)){
    header('Location: '. $site_url);
    die();
}
$site['title'] = 'Check Lịch Sử Ví MoMo - '. $site['site_name'];
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
                                    <h5 class="mb-0">Check Lịch Sử MoMo</h5>
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
                                    <input type="hidden" name="phone" id="phone">
                                    <div class="form-group">
                                        <label>Nhập List Điện Thoại:</label>
                                        <textarea name="list-phone" class="form-control" id="list-phone" cols="5" rows="10" placeholder=
"
Example: 
0987654321
0123456789"></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label>Số Lần Request: <span class="badge badge-danger">Tối đa 99 lần</span></label>
                                        <input type="number" class="form-control" value="10" id="days" placeholder="Nhập số ngày">
                                    </div>
                                    <div class="form-group text-center">
                                        <button class="btn btn-primary" id="submit-get-his" type="submit">Bắt Đầu</button>
                                    </div>
                                </form>
                            </div>
                            <input type="hidden" id="phone" value="" >
                            <div class="col-lg-8 justify-content-xl-center mx-auto">
                                <form action="<?= $site_url ?>creat/excel" method="post">
                                    <div class="form-group">
                                        <label>Thông Tin Lịch Sử: <span class="ml-3 badge badge-danger" id="counts">0</span></label>
                                        <textarea  require="require" id="info-his" cols="5" rows="10" class="form-control" disabled></textarea>
                                        <input type="hidden" name="datahis" id="hishidden">
                                    </div>
                                    <div class="row mx-auto justify-content-xl-center">
                                        <div class="form-group">
                                            <button class="btn btn-outline-success mr-4" id="submit-download" type="submit">Tải Xuống</button>
                                        </div>
                                        <div class="form-group">
                                            <button class="btn btn-outline-danger" id="copy" onclick="copyToClipboard('#hishidden')" type="button">Sao Chép</button>
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
$(document).ready(function () {
    var data = [];
    var phone = '';
    $('#submit-get-his').click(()=> {
        var phone = $('input[name="rervicer"]').val();
        if(phone == ''){
            Swal.fire('Thất Bại', 'Vui lòng nhập số điện thoại cần lấy', 'error');
            return false;
        }
        data = $('#list-phone').val().trim().split("\n");
        $('#info-his').val('');
        $('#submit-get-his').html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Đang Quét Tài Khoản!').prop('disabled', true);
        run(0);

    });

    function run(index){
        if (index < data.length) {
            GetHis(index);
        } else {
            $("#submit-get-his").html('Bắt Đầu').prop('disabled', false);
            $('#counts').html($('#info-his').val().split("\n").length);
        }
    }

    function GetHis(index){
        phone = data[index].trim();
        $.ajax({
            url: '<?php echo $site_url ?>modun/momo/get-his',
            type: 'POST',
            dataType: 'JSON',
            data: {
                phone: phone,
                days: $('#days').val()
            },
            error: (jqXHR, textStatus) =>{
                $.toast({
                    icon: 'error',
                    text: textStatus,
                    postison: 'top-right'
                });
            },
            success: (result)=>{
                if(result.status == 'success'){
                    var datas = result.TranList;
                    for(let i = 0; i < datas.length; i++) {
                        var history = datas[i];
                        var token   = history['tranId'] + '|' + history['patnerID'] + '|' + history['partnerName'] + '|' + history['comment'] + '|' + history['amount'] + '|' + history['millisecond'];
                        var current = $('#info-his').val();
                        if (token != undefined && token != "" && token.trim() != "") {
                            $('#info-his').val(current + token.trim() + "\n");
                            $('#hishidden').val(current + token.trim() + "\n");
                        }

                    }
                }
                else {
                    $.toast({
                        icon: 'error',
                        text: `Không thể check số điện thoại ${phone} `,
                        postison: 'top-right'
                    });
                }
            }
        }).always(function (){
            run(index + 1);
        })
    }
});

function copyToClipboard(element) {
    var $temp = $("<input>");
    $("body").append($temp);
    $temp.val($(element).val()).select();
    document.execCommand("copy");
    $temp.remove();
}
</script>
<?php
require_once '../site/foot.php';
?>