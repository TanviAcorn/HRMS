@extends('includes/header')

@section('pageTitle', 'Acorn Newsletters')

@section('content')
<main class="page-height bg-light-color">
    <div class="breadcrumb-wrapper d-flex">
        <div class="container-fluid">
            <h1 class="mb-0 header-title">Acorn Newsletters</h1>
        </div>
    </div>

    <section class="inner-wrapper-common-sections main-listing-section pt-4">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="newslettersTable" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Sr.No.</th>
                                    <th>Title</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Sample Row 1 -->
                                <tr>
                                    <td>1</td>
                                    <td>Quarterly Newsletter -July-August-September (2025)</td>
                                    <td>
                                        <a href="{{ url('docs/view/ACORN_NEWSLETTER_VOL2.pdf') }}" 
                                           class="btn btn-sm btn-primary" 
                                           target="_blank">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                                <!-- Sample Row 2 -->
                                <tr>
                                    <td>2</td>
                                    <td>Quarterly Newsletter -April-May-June (2025)</td>
                                    <td>
                                        <a href="{{ url('docs/view/ACORN_NEWSLETTER_VOL1.pdf') }}" 
                                           class="btn btn-sm btn-primary" 
                                           target="_blank">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
        <!-- /.col -->
    </div>
    <!-- /.row -->
</div>
<!-- /.container-fluid -->

@push('styles')
<!-- DataTables -->
<link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
@endpush

@push('scripts')
<!-- DataTables  & Plugins -->
<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>

<script>
    $(function () {
        $('#newslettersTable').DataTable({
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "responsive": true,
            "order": [[2, 'desc']] // Sort by date column (index 2) in descending order
        });
    });
</script>
@endpush

</section>
</main>
@endsection
