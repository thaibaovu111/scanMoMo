<?php
require_once '../config.php';

$site['title'] = 'Tải Dữ Liệu File';
require_once '../site/head.php';
?>
<div class="main-content">
    <div class="section__content section__content--p30">
        <div class="container-fluid">
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
                                                <th>Tên File</th>
                                                <th>Đã Gửi</th>
                                                <th>Hành Động</th>
                                            </tr>
                                        </thead>
                                        <tbody class="text-center">
                                            <?php 
                                            $scaned_director = array_diff(scandir('../tool/output'), array('..', '.'));
                                            foreach ($scaned_director as $item){
                                            ?>
                                            <tr class="tr-shadow">
                                                <td><?php echo $item ?></td>
                                                <td><?php echo count(explode("\n",file_get_contents('../tool/output/'.$item, FILE_USE_INCLUDE_PATH))); ?></td>
                                                <td>
                                                    <div class="dropdown font-sans-serif position-static">
                                                        <button class="btn btn-link text-600 btn-sm dropdown-toggle btn-reveal"
                                                            type="button" id="dropdownMenuButton" data-toggle="dropdown" data-boundary="window"
                                                            aria-haspopup="true" aria-expanded="false">
                                                            <span class="fas fa-ellipsis-h fs--1"></span>
                                                        </button>
                                                        <div class="dropdown-menu dropdown-menu-end border py-0" >
                                                            <div class="bg-white py-2">
                                                                <a class="dropdown-item" href="<?php echo $site_url ?>creat/excel?input=<?= $item ?>">Tải Xuống</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
        </div>
    </div>
</div>
<?php 
require_once '../site/foot.php';
?>
