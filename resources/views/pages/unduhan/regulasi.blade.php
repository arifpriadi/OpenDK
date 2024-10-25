@extends('layouts.app')

@section('content')
<div class="col-md-8">
    <div class="box box-primary">
        <div class="box-header with-border">
            <h4 class="box-title">Data Regulasi</h4>
        </div>
        <!-- /.box-header -->
        @if(isset($regulasi))
            <div class="box-body no-padding">

                <table class="table table-striped">
                    <tr>
                        <th>Judul Regulasi</th>
                        <th style="width: 150px">Aksi</th>
                    </tr>
                    @foreach($regulasi as $item)
                    <tr>
                        <td><a href="{{ route('unduhan.regulasi.show', ['nama_regulasi' => str_slug($item->judul)] ) }}">{{ $item->judul }}</a></td>
                        <td>
                            <a href="{{ route('unduhan.regulasi.show', ['nama_regulasi' => str_slug($item->judul)]) }}" title="Lihat">
                                <button type="button" class="btn btn-warning btn-sm" style="width: 40px;"><i class="fa fa-eye fa-fw"></i></button>
                            </a>
                            <a href="{{ route('unduhan.regulasi.download', ['file'=> str_slug($item->judul)]) }}" title="Unduh">
                                <button type="button" class="btn btn-info btn-sm" style="width: 40px;"><i class="fa fa-download"></i></button>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </table>
            </div>


            <!-- /.box-body -->
            <div class="box-footer clearfix">
                {!! $regulasi->links() !!}
            </div>
        @else
            <div class="box-body">
                <h3>Data not found.</h3>
                Sorry no data available right now!
            </div>
            <!-- /.box-body -->
            <div class="box-footer clearfix">
                <div class="pull-right">

                </div>
            </div>
            @endif
            <!-- /.box-footer -->
    </div>
</div>
@endsection


