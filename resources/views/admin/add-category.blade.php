
@extends('includes/header')

@section('pageTitle', $pageTitle )

@section('content')
<main class="page-height bg-light-color">
<div class="breadcrumb-wrapper d-lg-flex p-3 border-bottom">
    <h1 class="h3 mb-lg-0 mr-3 header-title"  id="pageTitle">{{ trans("messages.add-category") }}</h1>
    <nav aria-label="breadcrumb" class="d-flex mr-3">
        <ol class="breadcrumb bg-transparent p-0 mb-0 align-self-end">
            <li class="breadcrumb-item"><a href="{{ url('category-list') }}" class="category-add-link">{{ trans("messages.all-categories") }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ trans("messages.add-category") }}</li>
        </ol>
    </nav>
</div>

    <section class="inner-wrapper-common-sections main-listing-section py-4">
        <div class="container-fluid">
        <div class="card card-body mb-3 shadow-sm">
        <form method="post">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label class="control-label" for="category_name">{{ trans("messages.category-name") }}:</label>
                        <input type="text" class="form-control" id="category_name" name="category_name" placeholder="{{ trans("messages.category-name") }}" autofocus="">
                    </div>
                </div>
                <div class="col-md-12">
                    <button type="submit" class="btn btn bg-theme text-white btn-wide" title="{{ trans("messages.submit") }}">{{ trans("messages.submit") }}</button>
                    <a href="{{ url('category-list') }}" class="btn btn-outline-secondary shadow-sm btn-wide" title="{{ trans("messages.cancel") }}">{{ trans("messages.cancel") }}</a>
                </div>
            </div>
        </form>    
    </div>
        </div>
    </section>
</main>
<script>
$("form").validate({
    errorClass: "invalid-input",
    rules: {
        category_name: { required: true },
    },
    messages: {
        category_name: { required: "Category name is required." },
    },
    submitHandler: function(form) {
        showLoader()
        form.submit();
    }
});
</script>
@endsection
