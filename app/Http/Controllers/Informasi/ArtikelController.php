<?php

/*
 * File ini bagian dari:
 *
 * OpenDK
 *
 * Aplikasi dan source code ini dirilis berdasarkan lisensi GPL V3
 *
 * Hak Cipta 2017 - 2024 Perkumpulan Desa Digital Terbuka (https://opendesa.id)
 *
 * Dengan ini diberikan izin, secara gratis, kepada siapa pun yang mendapatkan salinan
 * dari perangkat lunak ini dan file dokumentasi terkait ("Aplikasi Ini"), untuk diperlakukan
 * tanpa batasan, termasuk hak untuk menggunakan, menyalin, mengubah dan/atau mendistribusikan,
 * asal tunduk pada syarat berikut:
 *
 * Pemberitahuan hak cipta di atas dan pemberitahuan izin ini harus disertakan dalam
 * setiap salinan atau bagian penting Aplikasi Ini. Barang siapa yang menghapus atau menghilangkan
 * pemberitahuan ini melanggar ketentuan lisensi Aplikasi Ini.
 *
 * PERANGKAT LUNAK INI DISEDIAKAN "SEBAGAIMANA ADANYA", TANPA JAMINAN APA PUN, BAIK TERSURAT MAUPUN
 * TERSIRAT. PENULIS ATAU PEMEGANG HAK CIPTA SAMA SEKALI TIDAK BERTANGGUNG JAWAB ATAS KLAIM, KERUSAKAN ATAU
 * KEWAJIBAN APAPUN ATAS PENGGUNAAN ATAU LAINNYA TERKAIT APLIKASI INI.
 *
 * @package    OpenDK
 * @author     Tim Pengembang OpenDesa
 * @copyright  Hak Cipta 2017 - 2024 Perkumpulan Desa Digital Terbuka (https://opendesa.id)
 * @license    http://www.gnu.org/licenses/gpl.html    GPL V3
 * @link       https://github.com/OpenSID/opendk
 */

namespace App\Http\Controllers\Informasi;

use App\Models\Artikel;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Traits\HandlesFileUpload;
use App\Http\Controllers\Controller;
use App\Http\Requests\ArtikelRequest;
use Illuminate\Support\Facades\Storage;

class ArtikelController extends Controller
{
    use HandlesFileUpload;

    public function index()
    {
        $page_title = 'Artikel';
        $page_description = 'Daftar Artikel';

        return view('informasi.artikel.index', compact('page_title', 'page_description'));
    }

    public function getDataArtikel(Request $request)
    {
        if ($request->ajax()) {
            return DataTables::of(Artikel::all())
                ->addIndexColumn()
                ->addColumn('aksi', function ($row) {
                    $data['show_web'] = route('berita.detail', $row->slug);

                    if (! auth()->guest()) {
                        $data['edit_url'] = route('informasi.artikel.edit', $row->id);
                        $data['delete_url'] = route('informasi.artikel.destroy', $row->id);
                    }

                    return view('forms.aksi', $data);
                })
                ->editColumn('status', function ($row) {
                    if ($row->status == 0) {
                        return '<span class="label label-danger">Tidak Aktif</span>';
                    } else {
                        return '<span class="label label-success">Aktif</span>';
                    }
                })
                ->editColumn('dibuat', function ($row) {
                    return format_datetime($row->created_at);
                })
                ->rawColumns(['status'])
                ->make(true);
        }
    }

    public function create()
    {
        $page_title = 'Artikel';
        $page_description = 'Tambah Artikel';

        return view('informasi.artikel.create', compact('page_title', 'page_description'));
    }

    public function store(ArtikelRequest $request)
    {
        try {
            $input = $request->all();
            $this->handleFileUpload($request, $input, 'gambar', 'artikel');

            Artikel::create($input);
        } catch (\Exception $e) {
            report($e);

            return back()->withInput()->with('error', 'Simpan artikel gagal!');
        }

        return redirect()->route('informasi.artikel.index')->with('success', 'Artikel berhasil disimpan!');
    }

    public function edit(Artikel $artikel)
    {
        $page_title = 'Artikel';
        $page_description = 'Ubah Artikel';

        return view('informasi.artikel.edit', compact('artikel', 'page_title', 'page_description'));
    }

    public function update(ArtikelRequest $request, Artikel $artikel)
    {
        try {
            $input = $request->all();
            $this->handleFileUpload($request, $input, 'gambar', 'artikel');

            $artikel->update($input);
        } catch (\Exception $e) {
            report($e);

            return back()->withInput()->with('error', 'Artikel gagal dihapus!');
        }

        return redirect()->route('informasi.artikel.index')->with('success', 'Artikel berhasil diubah!');
    }

    public function destroy(Artikel $artikel)
    {
        try {
            $artikel->delete();
        } catch (\Exception $e) {
            report($e);

            return redirect()->route('informasi.artikel.index')->with('error', 'Artikel gagal dihapus!');
        }

        return redirect()->route('informasi.artikel.index')->with('success', 'Artikel sukses dihapus!');
    }
}
