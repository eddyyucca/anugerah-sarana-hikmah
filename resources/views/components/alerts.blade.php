@if(session('success'))
<<<<<<< HEAD
<div class="alert alert-success alert-dismissible fade show alert-rounded" role="alert">
=======
<div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius:14px;">
>>>>>>> a456df66c536f85e5f8af9e06880d7e6a6f56a1c
    <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if(session('error'))
<<<<<<< HEAD
<div class="alert alert-danger alert-dismissible fade show alert-rounded" role="alert">
=======
<div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-radius:14px;">
>>>>>>> a456df66c536f85e5f8af9e06880d7e6a6f56a1c
    <i class="bi bi-exclamation-triangle me-2"></i> {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if($errors->any())
<<<<<<< HEAD
<div class="alert alert-danger alert-dismissible fade show alert-rounded" role="alert">
=======
<div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-radius:14px;">
>>>>>>> a456df66c536f85e5f8af9e06880d7e6a6f56a1c
    <i class="bi bi-exclamation-triangle me-2"></i>
    <ul class="mb-0">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif
