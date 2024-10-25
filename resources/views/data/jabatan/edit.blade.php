@extends('layouts.dashboard_template')

@section('content')
<section class="content-header">
    <h1>
        {{ $page_title ?? "Page Title" }}
        <small>{{ $page_description ?? '' }}</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('dashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li><a href="{{ route('data.jabatan.index') }}">Daftar Jabatan</a></li>
        <li class="active">{{ $page_description }}</li>
    </ol>
</section>

<section class="content container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">

                    <!-- form start -->
                    {!!  Form::model($jabatan, [ 'route' => ['data.jabatan.update', $jabatan->id], 'method' => 'post','id' => 'form-jabatan', 'class' => 'form-horizontal form-label-left' ] ) !!}
                    @include('layouts.fragments.error_message')

                    <div class="box-body">

                        {{ method_field('PUT') }}
                        @include( 'flash::message' )
                        @include('data.jabatan.form')

                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer">
                        <div class="form-group">
                            <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                                <a href="{{ route('data.jabatan.index') }}">
                                    <button type="button" class="btn btn-default btn-sm"><i class="fa fa-refresh"></i>&nbsp; Batal</button>
                                </a>
                                <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-save"></i>&nbsp; Simpan</button>
                            </div>
                        </div>
                    </div>
                    {!! Form::close() !!}
            </div>
        </div>
    </div>
</section>
@endsection