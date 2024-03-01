<div class="modal-header">
	 <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	<h4 class="modal-title text-center">{{ $title }}</h4>
</div>
<div id="pdf_preview" class="modal-body modal-loading" style="height:700px;">
	<object data="{{ $url }}" type="application/pdf" width="100%" height="680">
		<p>This browser does not support inline PDFs. Please download the PDF to view it: <a href="{{ $url }}">Download PDF</a></p>
		<div class="progress progress-sm active">
			<div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100" style="width: 100%">
			  <span class="sr-only">Loading</span>
			</div>
		  </div>
	</object>
</div>
<div class="modal-footer">
	<button type="button" data-dismiss="modal" id="closeModal" class="btn btn-warning">Close</button>
</div>
<!--script src="{{ asset('plugins/PDFObject/pdfobject.min.js') }}"></script>
<script>
	var mb = $('.modal-loading');
    jQuery(document).ready(function(){
		blockTab(mb);
		window.setTimeout(function () {
			unblockTab(mb);
			
			PDFObject.embed("{{ $url }}", "#pdf_preview", {
			   fallbackLink: '<p>This browser does not support inline PDFs. Please Contact Your Support.</p>'
			});
        }, 2000);
     });
</script-->