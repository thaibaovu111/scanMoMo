<?php 
require_once 'config.php';
if(empty($user_info['username'])){
    header('Location: '. $site_url);
    die();
}
$site['title'] = 'Trang chủ - '. $site['site_name'];
?>
<?php
require_once 'site/head.php';
?>
<div class="main-content">
    <div class="section__content section__content--p30">
        <div class="container-fluid">
            <div class="row g-0">
                <div class="col-md-12">
                    <div class="overview-wrap">
                        <h2 class="title-1">Trang chủ</h2>
                        <button class="au-btn au-btn-icon au-btn--blue">
                            <i class="zmdi zmdi-plus"></i>Cài Đặt Hệ Thống</button>
                    </div>
                </div>
            </div>
            <div class="row g-0 mt-3">
                <div class="col-lg-12">
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <div class="row align-items-div">
                                <div class="col mt-2 mb-2">
                                    <h5 class="mb-0">Danh sách tài khoản momo</h5>
                                </div>
                            </div>
                        </div>
                        <div class="card-body col-lg-12">
                            <div class="table-data__tool">
                                <div class="table-data__tool-left">
                                    <div class="rs-select2--light rs-select2--md">
                                        <select class="js-select2" name="property">
                                            <option selected="selected">Chọn tất cả</option>
                                            <option value="">Option 1</option>
                                            <option value="">Option 2</option>
                                        </select>
                                        <div class="dropDownSelect2"></div>
                                    </div>
                                    <div class="rs-select2--light rs-select2--sm">
                                        <select class="js-select2" name="time">
                                            <option selected="selected">Today</option>
                                            <option value="">3 Days</option>
                                            <option value="">1 Week</option>
                                        </select>
                                        <div class="dropDownSelect2"></div>
                                    </div>
                                    <button class="au-btn-filter">
                                        <i class="zmdi zmdi-filter-list"></i>filters</button>
                                </div>
                                <div class="table-data__tool-right">
                                    <a href="<?php echo $site_url ?>momo/register" class="au-btn au-btn-icon au-btn--green au-btn--small">
                                        <i class="zmdi zmdi-plus"></i>Thêm Tài Khoản</a>
                                    <div class="rs-select2--dark rs-select2--sm rs-select2--dark2">
                                        <select class="js-select2" name="type">
                                            <option selected="selected">Xuất file</option>
                                            <option value="">Xuất file exel</option>
                                            <option value="">Xuất file SQL</option>
                                        </select>
                                        <div class="dropDownSelect2"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive table-responsive-data2">
                                <table class="table table-data2 table-striped" id="example">
                                    <thead>
                                        <tr>
                                            <th>
                                                <label class="au-checkbox">
                                                    <input type="checkbox">
                                                    <span class="au-checkmark"></span>
                                                </label>
                                            </th>
                                            <th>#</th>
                                            <th>Số Điện Thoại</th>
                                            <th>Họ Và Tên</th>
                                            <th>Số Tiền</th>
                                            <th>Trạng Thái</th>
                                            <th>Ngày Đăng Nhập</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $table_momo = mysqli_query($conn, "SELECT * FROM `table_momo` WHERE `success` = 'true' ");
                                        $i = 0;
                                        while($rows = mysqli_fetch_assoc($table_momo)){
                                        ?>
                                        <tr class="tr-shadow mb-1" id="<?=  $rows['phone'] ?>">
                                            <td>
                                                <label class="au-checkbox">
                                                    <input type="checkbox">
                                                    <span class="au-checkmark"></span>
                                                </label>
                                            </td>
                                            <td><?= $i ?></td>
                                            <td>
                                                <span class="block-email"><?= $rows['phone'] ?></span>
                                            </td>
                                            <td class="desc"><?= $rows['Name'] ?></td>
                                            <td><?= $rows['BALANCE'] ?></td>
                                            <td>
                                                <span id="span_<?= $rows['phone'] ?>" class="text-white badge bg-<?php echo ($rows['TimeLogin'] + 600) > time()  ? 'success' : 'danger'; ?>"><?php echo ($rows['TimeLogin'] + 600) > time() ? 'Đã Đăng Nhập' : 'Hết Thời Gian'; ?></span>
                                            </td>
                                            <td><?php echo date('d-m-Y H:i:s', $rows['TimeLogin']); ?></td>
                                            <td>
                                                <div class="table-data-feature">
                                                    <button class="item" data-toggle="tooltip" onclick="window.location.href = '<?= $site_url ?>momo/bank?phone=<?= $rows['phone'] ?>';" data-placement="top" title="Ngân Hàng" ><i class="fas fa-university"></i></button>
                                                    <button class="item" data-toggle="tooltip" onclick="delete_phone('<?php echo $rows['phone'] ?>')" data-placement="top" title="Xóa">
                                                        <i class="zmdi zmdi-delete"></i>
                                                    </button>
                                                    <button  class="item" data-toggle="tooltip" onclick="window.location.href = '<?= $site_url ?>momo/chat?phone=<?= $rows['phone'] ?>';" data-placement="top" title="Gửi Tin Nhắn" ><i class="fas fa-comments"></i></button>
                                                    <button class="item" data-toggle="tooltip" onclick="login_momo('<?php echo $rows['phone'] ?>')" data-placement="top" title="Đăng Nhập">
                                                        <i class="zmdi fas fa-sign-in-alt"></i>
                                                    </button>
                                                    <button class="item" data-toggle="tooltip" onclick="window.location.href = '<?= $site_url ?>momo/send?phone=<?= $rows['phone'] ?>'" data-placement="top" title="Chuyển Tiền">
                                                        <i class="fas fa-money-bill-wave"></i></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <!-- <tr class="spacer"></tr> -->
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
<script>
    $(document).ready(() => {
        $('#example').DataTable();
    });
    function delete_phone(phone) {
        if(!phone) {
            Swal({
                icon: 'error',
                title: 'Oops..',
                text: "Đã xảy ra lỗi vui lòng thử lại"
            });
            return false;
        }

        Swal.fire({
            title: 'Bạn sẽ xóa',
            text: `Bạn chắc sẽ xóa só điện thoại ${phone} ra khỏi hệ thống`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Chấp nhận xóa',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '<?php echo $site_url ?>modun/momo/setting',
                        dataType: 'JSON',
                        type: 'POST',
                        data: {
                            type: 'delete',
                            phone: phone
                        },
                        success: (data) => {
                            if(data.error) {
                                Swal.fire({icon: 'error', title: 'Lỗi', text: data.message});
                                return false;
                            }
                            else if(data.success) {
                                Swal.fire({icon: 'success', title: 'Thành Công', text: data.message});
                                $('#' + phone).fadeOut();
                            }

                        }
                    });
                }
        });
    }

    function login_momo (phone) {
        $.ajax({
            url: '<?php echo $site_url ?>modun/momo/setting',
            dataType: 'JSON',
            type: 'POST',
            data: {
                type: 'login',
                phone: phone
            },
            success: (data) => {
                if(data.error) {
                    $.toast({
                        heading: 'Thất Bại',
                        text: data.message,
                        icon: 'error'
                    });
                    return false;
                }
                else if(data.success) {
                    $.toast({
                        heading: 'Thành Công',
                        text: data.message,
                        icon: 'success'
                    });
                    $('#span_' + phone).html('Đã Đăng Nhập').removeClass('bg-danger').toggleClass('bg-success');
                }

            }
        })
    }
</script>
<?php  
require_once 'site/foot.php';
?>