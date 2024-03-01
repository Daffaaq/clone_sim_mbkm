@extends('layouts.template')

@php
    $role = $user->role;
    $page->url_image = $user->avatar_url ? asset($user->avatar_url) : asset('assets/dist/user/user.png');
@endphp

@section('content')
<div class="row">
   <div class="col-md-4">
      <!-- Profile Image -->
      <div class="card card-{{ $theme->card_outline }} card-outline">
         <div class="card-body box-profile">
            <div class="text-center">
               <img class="profile-user-img img-fluid img-rounded" src="{{ $page->url_image }}" alt="User profile picture">
            </div>
            <h4 class="profile-username text-center">{{ $role->group_name }}</h4>
            <form method="post" action="{{ $page->url."/avatar" }}" role="form" class="form-horizontal" id="form-avatar">
               @csrf
               @method('PUT')
               <div class="form-message-image text-center"></div>
                <div class="form-group row mb-1">
                   <div class="input-group">
                      <div class="custom-file">
                         <input type="file" class="custom-file-input" id="image" name="image" onchange="showDir('#image', '#image-label')" accept="image/*">
                         <label class="custom-file-label" id="image-label" for="image"></label>
                     </div>
                   </div>
               </div>
                <div class="form-group row float-right mb-0">
                  <button type="submit" class="btn btn-{{ $theme->button }}">Ganti Foto Profil</button>
               </div>
            </form>
         </div>
        <!-- /.card-body -->
      </div>
      <!-- /.card -->
   </div>

   <div class="col-md-8">
      <div class="card card-{{ $theme->card_outline }} card-outline card-tabs">
         <div class="card-header">
             <h3 class="card-title mt-1">
                 <i class="fas fa-angle-double-right text-md text-{{ $theme->card_outline }} mr-1"></i>
                 Ganti Password
             </h3>
         </div>
         <div class="card-body">
              <form method="post" action="{{ $page->url.'/password'}}" role="form" class="form-horizontal" id="form-password">
                 <div class="form-message-password text-center"></div>
                 @csrf
                 @method('PUT')
                  <div class="form-group row mb-1">
                      <label class="col-sm-3 col-form-label">Username Login</label>
                      <div class="col-sm-9">
                          <input type="text" disabled class="form-control form-control-sm" id="username" value="{{ $user->username }}">
                          <small class="form-text text-muted">Username yang digunakan untuk login ke sistem.</small>
                      </div>
                  </div>
                  <hr>
                 <div class="form-group row mb-1">
                    <label for="password_old" class="col-sm-3 col-form-label">Password Lama</label>
                    <div class="col-sm-9">
                       <input type="password" class="form-control form-control-sm" id="password_old" name="password_old" placeholder="Masukan Password Lama">
                    </div>
                 </div>
                 <hr>
                 <div class="form-group row mb-1">
                    <label for="password" class="col-sm-3 col-form-label">Password Baru</label>
                    <div class="col-sm-9">
                       <input type="password" class="form-control form-control-sm" id="password" name="password" placeholder="Masukan Password Baru">
                    </div>
                 </div>
                 <div class="form-group row mb-1">
                    <label for="password_confirmation" class="col-sm-3 col-form-label">Ulangi Password Baru</label>
                    <div class="col-sm-9">
                       <input type="password" class="form-control form-control-sm" id="password_confirmation" name="password_confirmation" placeholder="Ulangi Password Baru">
                    </div>
                 </div>
                 <div class="row">
                    <label class="col-sm-3"></label>
                    <div class="col-sm-9">
                       <button type="submit" class="btn btn-{{ $theme->button }}">Submit</button>
                    </div>
                 </div>
              </form>
         </div>
         <!-- /.card -->
      </div>
   </div>
</div>
@endsection

@push('content-js')
<script>
   $(document).ready(function() {
      $("#form-avatar").validate({
         rules: {
            image: {
               required: true,
               accept: "image/jpeg,image/png,image/jpg",
                filesize: 0.125 // 125 kb
            },
         },
         submitHandler: function(form) {
            $('.form-message-image').html('');
            $(form).ajaxSubmit({
               dataType: 'json',
               success: function(data) {
                  setFormMessage('.form-message-image', data);
                  if (data.stat) {
                      setTimeout(function(){
                          location.reload();
                      }, 2000);
                  }
               }
            });
         },
         validClass: "valid-feedback",
         errorElement: "div",
         errorClass: 'invalid-feedback',
         errorPlacement: erp,
         highlight: hl,
         unhighlight: uhl,
         success: sc
      });

      $("#form-password").validate({
         rules: {
            password_old: {
               required: true,
               minlength: 5,
               maxlength: 20
            },
            password: {
               required: true,
               minlength: 5,
               maxlength: 20
            },
            password_confirmation: {
               required: true,
               minlength: 5,
               maxlength: 20,
               equalTo: '#password'
            }
         },
         submitHandler: function(form) {
            $('.form-message-password').html('');
            $(form).ajaxSubmit({
               dataType: 'json',
               success: function(data) {
                  setFormMessage('.form-message-password', data);
               }
            });
         },
         validClass: "valid-feedback",
         errorElement: "div",
         errorClass: 'invalid-feedback',
         errorPlacement: erp,
         highlight: hl,
         unhighlight: uhl,
         success: sc
      });
  });
</script>
@endpush
