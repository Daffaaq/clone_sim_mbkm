$.widget.bridge('uibutton', $.ui.button);
var enableBlockUI = true;
$.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});

function formatDosen (dt) {
    if(dt.hasOwnProperty('id')){
        if(dt.id.length == 0) return dt.text;
        return $('<div class="row"><div class="col-8 border-right">'+dt.text+'</div><div class="col-2 border-right overflow-hidden">Quota : <span class="badge badge-'+((dt.quota > 0)? 'info' : 'warning')+'">'+dt.quota+'</span></div><div class="col-2">Jml. Bimbingan : <span class="badge badge-'+((dt.jumlah_bimbingan < dt.quota)? 'success' : 'warning')+'">'+dt.jumlah_bimbingan+'</div></div>');
    }
    if(dt.hasOwnProperty('children') && dt.children.length > 0) return dt.text;
}

function formatDosenSelection (dt) {
    if(!dt.quota){dt.quota = (dt.element && dt.element.dataset.quota)? dt.element.dataset.quota : 0;}
    if(!dt.jumlah_bimbingan){dt.jumlah_bimbingan = (dt.element && dt.element.dataset.bimbingan)? dt.element.dataset.bimbingan : 0;}
    return (dt.id)? $('<div class="row ml-2"><div class="col-8 border-right">'+dt.text+'</div><div class="col-2 border-right overflow-hidden">Quota : <span class="badge badge-'+((dt.quota > 0)? 'info' : 'warning')+'">'+dt.quota+'</span></div><div class="col-2">Jml. Bimbingan : <span class="badge badge-'+((dt.jumlah_bimbingan < dt.quota)? 'success' : 'warning')+'">'+dt.jumlah_bimbingan+'</div></div>') : dt.text;
}

function formatDosenPembahas (dt) {
    if(dt.hasOwnProperty('id')){
        if(dt.id.length == 0) return dt.text;
        return $('<div class="row"><div class="col-8 border-right">'+dt.text+'</div><div class="col-2 border-right overflow-hidden">Jml Pembahas-1 : <span class="badge badge-'+((dt.total_utama > 0)? 'info' : 'warning')+'">'+dt.total_utama+'</span></div><div class="col-2">Jml Pembahas-2 : <span class="badge badge-'+((dt.total_pendamping < dt.total_utama)? 'success' : 'warning')+'">'+dt.total_pendamping+'</div></div>');
    }
    if(dt.hasOwnProperty('children') && dt.children.length > 0) return dt.text;
}

function formatDosenPembahasSelection (dt) {
    if(!dt.total_utama){dt.total_utama = (dt.element && dt.element.dataset.total_utama)? dt.element.dataset.total_utama : 0;}
    if(!dt.total_pendamping){dt.total_pendamping = (dt.element && dt.element.dataset.total_pendamping)? dt.element.dataset.total_pendamping : 0;}
    return (dt.id)? $('<div class="row ml-2"><div class="col-8 border-right">'+dt.text+'</div><div class="col-2 border-right overflow-hidden">Jml Pembahas-1 : <span class="badge badge-'+((dt.total_utama > 0)? 'info' : 'warning')+'">'+dt.total_utama+'</span></div><div class="col-2">Jml Pembahas-2 : <span class="badge badge-'+((dt.total_pendamping < dt.total_utama)? 'success' : 'warning')+'">'+dt.total_pendamping+'</div></div>') : dt.text;
}

function getProposalUsulanStatus(c = ''){
    let ss =  {'available': '<span class="badge badge-success">available</span>', 'selected' : '<span class="badge badge-warning">selected</span>', 'claimed' : '<span class="badge badge-danger">claimed</span>'};
    return ss[c.toLowerCase()];
}

function getDosenStatus(c = '', with_style = false){
    let s  = {'AK': 'Aktif', 'IB' : 'Izin Belajar', 'TB' : 'Tugas Belajar', 'CT' : 'Cuti', 'NA' : 'Lainnya'};
    let ss =  {'AK': '<span class="badge badge-success">Aktif</span>', 'IB' : '<span class="badge badge-primary">Izin Belajar</span>', 'TB' : '<span class="badge badge-info">Tugas Belajar</span>', 'CT' : '<span class="badge badge-warning">Cuti</span>', 'NA' : '<span class="badge badge-danger">Lainnya</span>'};
    return (with_style)? ss[c] : s[c];
}

function getDosenJenis(c = 'A', with_style = false){
    let s  = {'P': 'PNS', 'T' : 'Tetap Non-PNS', 'K' : 'Kontrak', 'L' : 'Luar Biasa', 'X' : 'Lainnya'};
    let ss =  {'P': '<span class="badge badge-success">PNS</span>', 'T' : '<span class="badge badge-primary">Tetap Non-PNS</span>', 'K' : '<span class="badge badge-info">Kontrak</span>', 'L' : '<span class="badge badge-warning">Luar Biasa</span>', 'X' : '<span class="badge badge-danger">Lainnya</span>'};
    return (with_style)? ss[c] : s[c];
}

function getLicenseLevel(l = 1, with_status = false){
    let s  = {1: 'Level 1', 2 : 'Level 2', 3 : 'Level 3', 4 : 'Temporary'};
    let ss =  {1: '<span class="badge badge-primary">Level 1</span>', 2 : '<span class="badge badge-primary">Level 2</span>', 3 : '<span class="badge badge-success">Level 3</span>', 4 : '<span class="badge badge-warning">Temporary</span>'};
    return (with_status)? ss[l] : s[l];
}

function setFormMessage(el, dt, to = 8000){
    if(dt.hasOwnProperty('stat') && dt.hasOwnProperty('msg')){
        $(el).fadeIn(200, function(){ $(el).html(((dt.stat)? '<div class="alert alert-success rounded-0 mb-1">'+dt.msg+'</div>':'<div class="alert alert-danger rounded-0 mb-1">'+dt.msg+'</div>'));});
		setTimeout(function(){$(el).fadeOut(to - 1200, function(){$(el).html('');});}, to);
    }
	if(dt.hasOwnProperty('csrf')){
        $('meta[name='+dt.csrf.name+']').attr("content", dt.csrf.token);
    }
    if(dt.hasOwnProperty('msgField')){
        $.each(dt.msgField, function(i,v){
            if(!$('#'+i).hasClass('is-invalid')) $('#'+i).addClass('is-invalid');
            if(!$('#'+i).next('div').hasClass('is-invalid'))$('#'+i).after('<div id="'+i+'-error" class="invalid-feedback">'+v+'</div>');
        });
    }
    if(dt.hasOwnProperty('rowDel')){
        $(dt.rowDel).remove();
    }
}

function getStatus(id = 0, s = '-'){
    let stat = {
        0 : 'badge badge-danger',
        1 : 'badge badge-warning',
        2 : 'badge badge-info',
        3 : 'badge badge-primary',
        4 : 'badge badge-success',
    };
    return '<span class="'+stat[id]+'">'+ s +'</span>';
}

function refreshToken(dt){
    if(dt.hasOwnProperty('csrf')){
        $('meta[name='+dt.csrf.name+']').attr("content", dt.csrf.token);
    }
}

function setActiveMenu(m, sm1='', sm2=''){
	m = m.trim(); sm1 = sm1.trim(); sm2 = sm2.trim();
    $( "li a.nav-link" ).each(function(){
        if(! $(this).parent().parent().hasClass('nav-tabs')){$(this).removeClass('active');}
        if($(this).parent().hasClass('has-treeview')){$(this).parent().removeClass('menu-open');}
    });
    if(m.length > 0){
		if($('.'+m).parent().hasClass('has-treeview')){$('.'+m).parent().addClass('menu-open');}
		$('.'+m).addClass('active');
	}
    if(sm1.length > 0){
		if($('.'+sm1).parent().hasClass('has-treeview')){
			$('.'+sm1).parent().addClass('menu-open');
		}else{
			$('.'+sm1).addClass('active');
		}
	}
    if(sm2.length > 0){
        $('.'+sm2).addClass('active').closest('.nav-treeview').show();
    }
}

function blockUI(blc, t = 1, s){
	if(enableBlockUI){
		let msg = '<div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div>';
		if (t == 'progress') {
			msg = '<h4 class="progress-tittle">Silahkan tunggu...</h4><div class="progress progress-status"><div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 1%" aria-valuenow="10" aria-valuemin="0" aria-valuemax="100">1%</div></div>';
		}
		blc = (typeof blc == 'undefined')? 'body' : blc;
		$(blc).block({
			onOverlayClick: $.unblockUI,
			overlayCSS: {
				backgroundColor: '#8EA0D7',
				cursor: 'default'
			},
			message: msg,
			css: {
				border: 'none',
				color: '#4D79FF',
				background: 'none',
				cursor: 'default',
			},
			baseZ: 1050
		});

		if(typeof s != 'undefined'){
			progressUnblockUI(blc, s, 0, 1);
		}
	}
}

function unblockUI(blc){
    blc = (typeof blc == 'undefined')? 'body' : blc;
    $(blc).unblock();
}

// s: second, ps: progress second, ac: auto close
function progressUnblockUI(blc, s = 1, ps = 0, ac = 1){
    let p = Math.round(100/s);
    if(ps != s){
        ps++;
        setTimeout(function(){
            let x = p * ps;
            x = (x > 100)? '100%' : x+'%';
            $('.progress-bar').css('width', x).text(x);
            progressUnblockUI(blc, s, ps, ac);
        }, 1000);
    } else {
        if(ac) setTimeout(function(){unblockUI(blc);}, 1000);
    }
}

function closeModal(mdl, dt = {}){
    if(dt.hasOwnProperty('mcc')){
        if(dt.mcc) {
            setTimeout(function(){
                $modalConfirm.modal('hide');
            }, 1000);
        }
    } else if(dt.hasOwnProperty('mc')){
        if(dt.mc) {
            setTimeout(function(){
                $modal.modal('hide');
            }, 1000);
        }
    }else {
        if (mdl) {
            setTimeout(function () {
                $(mdl).modal('toggle');
            }, 1000);
        }
    }
}

function resetForm(el, exc){
    exc = (typeof exc != 'undefined')? exc : '';
    $('.select2, .selectbox', el).not(exc).val("").trigger("change");
    $(':input', el).not(':button, :submit, :reset, :radio'+((exc.length > 0)? ','+exc : '')).val('').prop('selected', false);
    $('label.custom-file-label').text('');
}


function ajaxChained(v, t, url, tname = '', adts = ''){
    var opt = '<option value="">- pilih -</option>';
    var c = 0;
    if(v.length > 0) {
        $.ajax({
            type: 'POST',
            url: url,
            dataType: 'json',
            data: {[tname]: $('meta[name=' + tname + ']').attr("content"), id: v}
        }).done(function (data){
            refreshToken(data);
            let adt = adts.split(',');
            $.each(data.data, function (i, item) {
                let l = adt.length;
                opt += '<option value="' + item.id + '" ';
                for (let i = 0; i < l; i++) {
                    opt += 'data-' + adt[i] + '="' + item[adt[i]] + '" ';
                }
                opt += '>' + item.text + '</option>';
            });
            c = data.data.length;
            $(t).html(opt);
            $(t).select2();

        });
    }else{
        $(t).html(opt);
        $(t).select2({placeholder: c+' results'});
    }
}

function getDayName(i){
    i = parseInt(i);
    let h = ['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'];
    return (i>0 && i<8)? h[i-1] : '';
}

function formatMoney(a){
    return new Intl.NumberFormat('id-ID').format(a);
}

function customFiles(){
	if (window.FileReader && window.FileList && window.Blob && window.File){
		$('.preview-images-zone').find('tr.img-client').remove();
		var files = event.target.files; //FileList object
		for (let i = 0; i < files.length; i++) {
			var file = files[i];
			if (!file.type.match('image')) continue;

			var picReader = new FileReader();
			let cdImg 	= 'img'+getSandStr(3,'-',3);
			let imgSize = (file.size/1000).toFixed(2);
			let imgType = file.type;

			picReader.addEventListener('load', function (event) {
				var picFile = event.target;
				var html =  '<tr class="img-client"><td class="align-top">' +
					'<div class="preview-image preview-show-' + i + '">' +
					'<div class="image-zone"><img id="pro-img-' + i + '" src="' + picFile.result + '"></div>' +
					'<input type="hidden" name="cdImg['+i+']" value="'+cdImg+'"/></div></td>' +
					'<td class="align-top">' +
					'<div><strong>Tipe : </strong>'+imgType+'</div>'+
					'<div><strong>Ukuran : </strong>'+imgSize+' Kb</div>'+
					'</td><td class="align-top">' +
					'<input type="text" class="form-control form-control-sm title-image-'+i+' '+cdImg+'" placeholder="Judul Gambar" name="gambarJudul['+i+']" value=""/>'+
					'</td><td class="text-center align-top"">' +
					'<div class="icheck-primary d-inline mr-2">' +
					'<input type="radio" id="gambarUtama'+i+'" name="gambarUtama" value="'+i+'"><label for="gambarUtama'+i+'"></label>' +
					'</div></td><td class="text-center align-top""><div class="icheck-success d-inline"><input type="checkbox" name="gambarSelect[]" value="'+i+'" id="gambar'+i+'" checked><label for="gambar'+i+'"></label></div></td></tr>';
				$(html).appendTo(".preview-images-zone");
			});

			//$( ".preview-images-zone" ).sortable();
			picReader.readAsDataURL(file);
		}
		$("#pro-image").val('');
	} else {
		alert('Browser not support');
	}
}

function getDataModal(m,u){
    let b = 'body';
    blockUI(b);
    setTimeout(function(){
        m.load(u, function(){m.modal('show');});
        unblockUI(b);
    }, 100);
}

function setStore(n, dt){
    store.set(n, dt);// 8jam
}
function getStore(n){
    return store.get(n);
}

function showDir(fuid, label){
    let file = $(fuid)[0].files[0].name;
    $(label).text(file);
}

function select2AllowRenderHtml(m){ // untuk membuat custom html option di render sama select2
    return m;
}
function select2ShowOfficialFlag(state){
    if (!state.id) {
        return state.text;
    }
    let ofc = {0: '', 1:'&nbsp;<label class="label label-inline label-success">official</label>'};
    return state.text + ofc[$(state.element).data('official')];
}

function formatBytes(a,b=2,k=1024){with(Math){let d=floor(log(a)/log(k));return 0==a?"0 Bytes":(parseFloat((a/pow(k,d)).toFixed(max(0,b)))+" "+["Bytes","KB","MB","GB","TB","PB","EB","ZB","YB"][d])}}

$('.date_filter').daterangepicker({
	startDate: moment().startOf('month'),
	endDate: moment(),
	locale:{format: 'DD-MM-YYYY', separator: ' ~ '},
	ranges: {
	   'Sekarang': [moment(), moment()],
	   'Kemarin': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
	   'Tgl. 1 - Sekarang': [moment().startOf('month'), moment()],
	   '1 Minggu lalu': [moment().subtract(6, 'days'), moment()],
	   '30 Hari lalu': [moment().subtract(29, 'days'), moment()],
	   'Bulan ini': [moment().startOf('month'), moment().endOf('month')]
	}
});

$('.date_filter').on('cancel.daterangepicker', function(ev, picker) {
  $(this).val('');
});

$('.date_filter').on('apply.daterangepicker', function(ev, picker) {
	if(typeof dataTable != 'undefined'){
		blockUI('.card');
		dataTable.draw();
		setTimeout(function(){unblockUI('.card')}, 500);
	}
});

var $modal = $('#ajax-modal');
var $modalConfirm = $('#ajax-modal-confirm');
$('body').on('click', '.ajax_modal', function(ev) {
    ev.preventDefault();
    let u = $(this).data('url');
    let b = $(this).data('block');
    blockUI(b);
    setTimeout(function(){
        $modal.load(u, function(){
            $modal.modal('show');
            unblockUI(b);
        });
    }, 10);
});
$('body').on('click', '.ajax_modal_confirm', function(ev) {
    ev.preventDefault();
    let u = $(this).data('url');
    let b = $(this).data('block');
    blockUI(b);
    setTimeout(function(){
        $modalConfirm.load(u, function(){
            $modalConfirm.modal('show');
            unblockUI(b);
        });
    }, 10);
});

var hl= function (element, errorClass, validClass) {
    $(element).removeClass('is-valid').addClass('is-invalid');
    let elem = $(element);
    if (elem.hasClass("select2-offscreen")) {
        $("#s2id_" + elem.attr("id") + " ul").addClass(errorClass);
    }
};
var uhl= function (element, errorClass, validClass) {
    $(element).removeClass('is-invalid');
    let elem = $(element);
    if (elem.hasClass("select2-offscreen")) {
        $("#s2id_" + elem.attr("id") + " ul").removeClass(errorClass);
    }
};
var sc= function (label, element) {
    label.remove();
    $(element).removeClass('is-invalid');
};
var erp= function (error, element) { // render error placement for each input type
    if (element.is("select") && (element.hasClass("select2") || element.hasClass("select2-hidden-accessible"))) {
        $(element).parent().append(error);
    } else if (element.is("select") || element.attr("type") == "radio" || element.attr("type") == "checkbox" || element.attr("type") == "textarea") { // for chosen elements, need to insert the error after the chosen container
    //} else if (element.is("select") || element.attr("type") == "radio" || element.attr("type") == "checkbox" || element.attr("type") == "file" || element.attr("type") == "textarea") { // for chosen elements, need to insert the error after the chosen container
        error.insertAfter($(element).closest('.form-group').children('div').children().last());
    } else if (element.is("select")) {
        error.appendTo($(element).parent());
    } else if (element.hasClass("ckeditor")) {
        error.appendTo($(element).closest('.form-group'));
    } else if ($(element).parent().hasClass('input-group')) {
        $(element).parent().append(error);
    }else if($(element).parent().hasClass('custom-file')){
        $(element).parent().parent().append(error);
    }else {
        error.insertAfter(element);
    }
};

var currencyMask = {prefix: "",autoUnmask: true, radixPoint: ",", groupSeparator: ".", digits: 2, autoGroup: true, allowMinus: false, min: 0, rightAlign: true, positionCaretOnClick: true,  digitsOptional: true, oncleared: function (self) { $(this).val('0'); }};
var discountMask = {prefix: "",autoUnmask: true, radixPoint: ",", groupSeparator: ".", digits: 2, autoGroup: true, allowMinus: false, min: 0, max: 99, rightAlign: true, positionCaretOnClick: true, digitsOptional: true, oncleared: function (self) { $(this).val('0'); }};
var debtMask = {prefix: "",autoUnmask: true, radixPoint: ",", groupSeparator: ".", digits: 0, autoGroup: true, allowMinus: true, prefix: '', rightAlign: true, positionCaretOnClick: true, digitsOptional: true, oncleared: function (self) { $(this).val('0'); }};
var datepickModal = {/*drops:"up",*/ parentEl: ".modal-body",singleDatePicker: true, timePicker: true,timePicker24Hour: true, timePickerIncrement: 1, autoUpdateInput: true, locale:{format: 'YYYY-MM-DD HH:mm'}/*, startDate: moment().format('YYYY-MM-DD HH:mm:00')*/};
var datepickMonYear = {singleDatePicker: true, showDropdowns: true,  locale:{format: 'MM-YYYY'}};
var daterangepickModal = {startDate: moment().startOf('month'),endDate: moment(),locale:{format: 'DD-MM-YYYY', separator: ' ~ '},ranges: {'Sekarang': [moment(), moment()],'Kemarin': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],'Tgl. 1 - Sekarang': [moment().startOf('month'), moment()],'1 Minggu lalu': [moment().subtract(6, 'days'), moment()],'Bulan ini': [moment().startOf('month'), moment().endOf('month')],'Bulan Kemarin': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]}};
var chartOption = {responsive: true, maintainAspectRatio: false, datasetFill: false, legend: {display: false}, scaleStartValue: 0, scales: {yAxes: [{display: true, ticks: {suggestedMin: 0, beginAtZero: true}}]}, tooltips: {
                   callbacks: {label: function(tooltipItem, data) {return "Total: " +parseInt(tooltipItem.yLabel).toLocaleString('id-ID')+" "+data.datasets[tooltipItem.datasetIndex].label;}}}};
if(typeof $.ui != 'undefined'){$.ui.dialog.prototype._allowInteraction = function(e) {return !!$(e.target).closest('.ui-dialog, .ui-datepicker, .select2-drop').length}}
var chartOnHover = function(e) {let p = this.getElementAtEvent(e); if(p.length){e.target.style.cursor = 'pointer';}else{e.target.style.cursor = 'default';}};
