<form method="post" action="<?php echo $url?>" role="form" class="form-horizontal" id="menu-confirm" width="80%">
<div id="modal-menu" class="modal-dialog modal-md" role="document">
	<div class="modal-content">
		<div class="modal-header">
			<h5 class="modal-title" id="exampleModalLabel"><?php echo $title?></h5>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<div class="modal-body p-0">
            <div class="mb-0 form-message text-center"></div>
            <div class="alert alert-warning mb-0">
                Apakah anda yakin menghapus data berikut:
                <section class="landing">
                    <div class="container">
                        <dl class="row mb-0">
                            <?php foreach($info as $k => $v):?>
                                <dt class="col-sm-3 text-right"><strong><?php echo $k?>:</strong></dt><dd class="col-sm-9 mb-0"><?php echo $v ?></dd>
                            <?php endforeach;?>
                        </dl>
                    </div>
                </section>
            </div>
        </div>
		<div class="modal-footer">
			<button type="button" data-dismiss="modal" class="btn btn-warning">Batal</button>
			<button type="submit" class="btn btn-primary">Ya, Hapus</button>
		</div>
	</div>
</div>
<?php echo form_close() ?>

<script>
	$(document).ready(function(){
 //unblockUI();

		$("#menu-confirm").submit(function(){
            $('.form-message').html('');
            let blc = '#modal-menu';
            blockUI(blc);
            $(this).ajaxSubmit({
                dataType:  'json',
                data: {<?php echo $page->tokenName ?> : $('meta[name=<?php echo $page->tokenName ?>]').attr("content"), method: "DELETE"},
                success: function(data){
                    refreshToken(data);
                    unblockUI(blc);
					setFormMessage('.form-message', data);
                    if(data.stat){
                        dataTable.draw();
                    }
                    closeModal($modal, data);
                }
            });
            return false;
		});
	});
</script>