@extends('layouts.dashboard_template')

@section('content')
    <section class="content-header">
        <h1>
            {{ $page_title ?? 'Page Title' }}
            <small>{{ $page_description ?? '' }}</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{ route('dashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li class="active">{{ $page_title }}</li>
        </ol>
    </section>

    <section class="content container-fluid">

        @include('partials.flash_message')

        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="control-group">
                    <a href="{{ route('data.imunisasi.import') }}">
                        <button type="button" class="btn btn-warning btn-sm" title="Import Data"><i class="fa fa-upload"></i>&ensp;Impor</button>
                    </a>
                </div>
            </div>
            <div class="box-body">
                @include('layouts.fragments.list-desa')
                <hr>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover dataTable" id="imunisasi-table">
                        <thead>
                            <tr>
                                <th style="max-width: 100px;">Aksi</th>
                                <th>Desa</th>
                                <th>Cakupan Imunisasi</th>
                                <th>Bulan</th>
                                <th>Tahun</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection

@include('partials.asset_select2')
@include('partials.asset_datatables')

@push('scripts')
    <script type="text/javascript">
        $(document).ready(function() {
            var data = $('#imunisasi-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{!! route('data.imunisasi.getdata') !!}",
                    data: function(d) {
                        d.desa = $('#list_desa').val();
                    }
                },
                columns: [{
                        data: 'aksi',
                        name: 'aksi',
                        class: 'text-center',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'desa.nama',
                        name: 'desa.nama'
                    },
                    {
                        data: 'cakupan_imunisasi',
                        name: 'cakupan_imunisasi'
                    },
                    {
                        data: 'bulan',
                        name: 'bulan'
                    },
                    {
                        data: 'tahun',
                        name: 'tahun'
                    },
                ],
                order: [
                    [1, 'asc']
                ]
            });

            $('#list_desa').on('select2:select', function(e) {
                data.ajax.reload();
            });
        });
    </script>
    @include('forms.datatable-vertical')
    @include('forms.delete-modal')
@endpush
