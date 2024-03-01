<div class="content-header">
    <div class="container-fluid px-3">
        <div class="row mb-2 content-header-title">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark"><?php echo $breadcrumb->title?></h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <?php
                        if(!empty($breadcrumb->list)){
                            $last = count($breadcrumb->list);
                            for($i = 0; $i < $last; $i++){
                                echo ($i == ($last - 1))? '<li class="breadcrumb-item">'.$breadcrumb->list[$i].'</li>' : '<li class="breadcrumb-item active">'.$breadcrumb->list[$i].'</li>';
                            }
                        }
                    ?>

                </ol>
            </div>
        </div>
    </div>
</div>
