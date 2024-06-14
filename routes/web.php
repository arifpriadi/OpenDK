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

use App\Http\Controllers\BackEnd\ThemesController;
use App\Http\Controllers\Counter\CounterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Informasi\EventController;
use App\Http\Controllers\LogViewerController;
use App\Http\Controllers\Role\RoleController;
use App\Http\Controllers\Setting\AplikasiController;
use App\Http\Controllers\Setting\COAController;
use App\Http\Controllers\Setting\JenisPenyakitController;
use App\Http\Controllers\Setting\KategoriKomplainController;
use App\Http\Controllers\Setting\SlideController;
use App\Http\Controllers\Setting\TipePotensiController;
use App\Http\Controllers\Setting\TipeRegulasiController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\User\UserController;
use App\Models\DataDesa;
use App\Models\Penduduk;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Redirect if apps not installed
Route::group(['middleware' => ['installed', 'xss_sanitization']], function () {
    Auth::routes([
        'register' => false,
    ]);

    Route::group(['prefix' => 'filemanager', 'middleware' => ['auth:web', 'role:administrator-website|super-admin|admin-kecamatan']], function () {
        \UniSharp\LaravelFilemanager\Lfm::routes();
    });

    Route::group(['middleware' => 'maintenance'], function () {
        /**
         * Group Routing for Halaman Website
         */
        Route::namespace('\App\Http\Controllers\FrontEnd')->group(function () {
            Route::get('/', 'PageController@index')->name('beranda');
            Route::get('berita-desa', 'PageController@beritaDesa')->name('berita-desa');
            Route::get('filter-berita-desa', 'PageController@filterFeeds')->name('filter-berita-desa');

            Route::group(['prefix' => 'berita'], function () {
                Route::permanentRedirect('/', '/');
                Route::get('{slug}', 'PageController@detailBerita')->name('berita.detail');
            });

            Route::group(['prefix' => 'profil'], function () {
                Route::get('letak-geografis', 'ProfilController@LetakGeografis')->name('profil.letak-geografis');
                Route::get('struktur-pemerintahan', 'ProfilController@StrukturPemerintahan')->name('profil.struktur-pemerintahan');
                Route::get('visi-dan-misi', 'ProfilController@VisiMisi')->name('profil.visi-misi');
                Route::get('sejarah', 'ProfilController@sejarah')->name('profil.sejarah');
            });

            Route::group(['prefix' => 'event'], function () {
                Route::permanentRedirect('/', '/');
                Route::get('{slug}', 'PageController@eventDetail')->name('event.detail');
            });

            Route::group(['prefix' => 'desa'], function () {
                Route::permanentRedirect('/', '/');
                Route::get('desa-{slug}', 'PageController@DesaShow')->name('desa.show');
            });

            Route::group(['prefix' => 'potensi'], function () {
                Route::permanentRedirect('/', '/');
                Route::get('{slug}', 'PageController@PotensiByKategory')->name('potensi.kategori');
                Route::get('{kategori}/{slug}', 'PageController@PotensiShow')->name('potensi.kategori.show');
            });

            Route::any('refresh-captcha', 'PageController@refresh_captcha')->name('refresh-captcha');

            Route::group(['prefix' => 'statistik'], function () {
                Route::get('kependudukan', 'KependudukanController@showKependudukan')->name('statistik.kependudukan');
                Route::get('show-kependudukan', 'KependudukanController@showKependudukanPartial')->name('statistik.show-kependudukan');
                Route::get('chart-kependudukan', 'KependudukanController@getChartPenduduk')->name('statistik.chart-kependudukan');
                Route::get('chart-kependudukan-usia', 'KependudukanController@getChartPendudukUsia')->name('statistik.chart-kependudukan-usia');
                Route::get('chart-kependudukan-pendidikan', 'KependudukanController@getChartPendudukPendidikan')->name('statistik.chart-kependudukan-pendidikan');
                Route::get('chart-kependudukan-goldarah', 'KependudukanController@getChartPendudukGolDarah')->name('statistik.chart-kependudukan-goldarah');
                Route::get('chart-kependudukan-kawin', 'KependudukanController@getChartPendudukKawin')->name('statistik.chart-kependudukan-kawin');
                Route::get('chart-kependudukan-agama', 'KependudukanController@getChartPendudukAgama')->name('statistik.chart-kependudukan-agama');
                Route::get('chart-kependudukan-kelamin', 'KependudukanController@getChartPendudukKelamin')->name('statistik.chart-kependudukan-kelamin');
                Route::get('data-penduduk', 'KependudukanController@getDataPenduduk')->name('statistik.data-penduduk');

                Route::get('pendidikan', 'PendidikanController@showPendidikan')->name('statistik.pendidikan');
                Route::get('chart-tingkat-pendidikan', 'PendidikanController@getChartTingkatPendidikan')->name('statistik.pendidikan.chart-tingkat-pendidikan');
                Route::get('chart-putus-sekolah', 'PendidikanController@getChartPutusSekolah')->name('statistik.pendidikan.chart-putus-sekolah');
                Route::get('chart-fasilitas-paud', 'PendidikanController@getChartFasilitasPAUD')->name('statistik.pendidikan.chart-fasilitas-paud');

                Route::get('program-dan-bantuan', 'ProgramBantuanController@showProgramBantuan')->name('statistik.program-bantuan');
                Route::get('chart-penduduk', 'ProgramBantuanController@getChartBantuanPenduduk')->name('statistik.program-bantuan.chart-penduduk');
                Route::get('chart-keluarga', 'ProgramBantuanController@getChartBantuanKeluarga')->name('statistik.program-bantuan.chart-keluarga');

                Route::get('anggaran-dan-realisasi', 'AnggaranRealisasiController@showAnggaranDanRealisasi')->name('statistik.anggaran-dan-realisasi');
                Route::get('chart-anggaran-realisasi', 'AnggaranRealisasiController@getChartAnggaranRealisasi')->name('statistik.chart-anggaran-realisasi');

                Route::get('anggaran-desa', 'AnggaranDesaController@showAnggaranDesa')->name('statistik.anggaran-desa');
                Route::get('chart-anggaran-desa', 'AnggaranDesaController@getChartAnggaranDesa')->name('statistik.chart-anggaran-desa');

                Route::get('kesehatan', 'KesehatanController@showKesehatan')->name('statistik.kesehatan');
                Route::get('chart-akiakb', 'KesehatanController@getChartAKIAKB')->name('statistik.kesehatan.chart-akiakb');
                Route::get('chart-imunisasi', 'KesehatanController@getChartImunisasi')->name('statistik.kesehatan.chart-imunisasi');
                Route::get('chart-penyakit', 'KesehatanController@getChartEpidemiPenyakit')->name('statistik.kesehatan.chart-penyakit');
                Route::get('chart-sanitasi', 'KesehatanController@getChartToiletSanitasi')->name('statistik.kesehatan.chart-sanitasi');
            });

            Route::group(['prefix' => 'unduhan'], function () {
                Route::permanentRedirect('/', '/');

                Route::group(['prefix' => 'prosedur'], function () {
                    Route::permanentRedirect('/', '/');
                    Route::get('/', 'DownloadController@indexProsedur')->name('unduhan.prosedur');
                    Route::get('getdata', 'DownloadController@getDataProsedur')->name('unduhan.prosedur.getdata');
                    Route::get('{nama_prosedur}', 'DownloadController@showProsedur')->name('unduhan.prosedur.show');
                    Route::get('{file}/download', 'DownloadController@downloadProsedur')->name('unduhan.prosedur.download');
                });

                Route::group(['prefix' => 'regulasi'], function () {
                    Route::permanentRedirect('/', '/');
                    Route::get('/', 'DownloadController@indexRegulasi')->name('unduhan.regulasi');
                    Route::get('{nama_regulasi}', 'DownloadController@showRegulasi')->name('unduhan.regulasi.show');
                    Route::get('{file}/download', 'DownloadController@downloadRegulasi')->name('unduhan.regulasi.download');
                });

                Route::group(['prefix' => 'form-dokumen'], function () {
                    Route::permanentRedirect('/', '/');
                    Route::get('/', 'DownloadController@indexFormDokumen')->name('unduhan.form-dokumen');
                    Route::get('getdata', 'DownloadController@getDataDokumen')->name('unduhan.form-dokumen.getdata');
                });
            });

            Route::get('faq', 'WebFaqController@index')->name('faq');
        });
        Route::get('agenda-kegiatan/{slug}', [EventController::class, 'show'])->name('event.show');

        Route::namespace('\App\Http\Controllers\SistemKomplain')->group(function () {
            Route::group(['prefix' => 'sistem-komplain'], function () {
                Route::get('/', ['as' => 'sistem-komplain.index', 'uses' => 'SistemKomplainController@index']);
                Route::get('kirim', ['as' => 'sistem-komplain.kirim', 'uses' => 'SistemKomplainController@kirim']);
                Route::post('store', ['as' => 'sistem-komplain.store', 'uses' => 'SistemKomplainController@store']);
                Route::get('komplain/{slug}', ['as' => 'sistem-komplain.komplain', 'uses' => 'SistemKomplainController@show']);
                Route::get('komplain/kategori/{slug}', ['as' => 'sistem-komplain.kategori', 'uses' => 'SistemKomplainController@indexKategori']);
                Route::get('komplain-sukses', ['as' => 'sistem-komplain.komplain-sukses', 'uses' => 'SistemKomplainController@indexSukses']);
                Route::post('tracking', ['as' => 'sistem-komplain.tracking', 'uses' => 'SistemKomplainController@tracking']);
                Route::post('reply/{id}', ['as' => 'sistem-komplain.reply', 'uses' => 'SistemKomplainController@reply']);
                Route::get('jawabans', ['as' => 'sistem-komplain.jawabans', 'uses' => 'SistemKomplainController@getJawabans']);
            });
        });
    });

    /**
     * Group Routing for Halaman Dahsboard
     */
    Route::group(['middleware' => ['auth:web', 'complete_profile']], function () {
        // Route::get('logout', ['as' => 'logout', 'uses' => 'Auth\AuthController@logout']);

        Route::get('/dashboard', DashboardController::class)->name('dashboard');

        Route::namespace('\App\Http\Controllers\Auth')->group(function () {
            Route::group(['prefix' => 'changedefault', 'middleware' => ['role:administrator-website|super-admin|admin-kecamatan|kontributor-artikel']], function () {
                Route::get('/', 'ChangeDefaultController@index')->name('change-default');
                Route::post('store', ['as' => 'changedefault.store', 'uses' => 'ChangeDefaultController@store']);
            });
        });

        /**
         * Group Routing for Informasi
         */
        Route::namespace('\App\Http\Controllers\Informasi')->group(function () {
            Route::group(['prefix' => 'informasi', 'middleware' => ['role:administrator-website|super-admin|admin-kecamatan|kontributor-artikel']], function () {
                // Prosedur
                Route::group(['prefix' => 'prosedur'], function () {
                    Route::get('/', ['as' => 'informasi.prosedur.index', 'uses' => 'ProsedurController@index']);
                    Route::get('show/{prosedur}', ['as' => 'informasi.prosedur.show', 'uses' => 'ProsedurController@show']);
                    Route::get('getdata', ['as' => 'informasi.prosedur.getdata', 'uses' => 'ProsedurController@getDataProsedur']);
                    Route::get('create', ['as' => 'informasi.prosedur.create', 'uses' => 'ProsedurController@create']);
                    Route::post('store', ['as' => 'informasi.prosedur.store', 'uses' => 'ProsedurController@store']);
                    Route::get('edit/{prosedur}', ['as' => 'informasi.prosedur.edit', 'uses' => 'ProsedurController@edit']);
                    Route::put('update/{prosedur}', ['as' => 'informasi.prosedur.update', 'uses' => 'ProsedurController@update']);
                    Route::delete('destroy/{prosedur}', ['as' => 'informasi.prosedur.destroy', 'uses' => 'ProsedurController@destroy']);
                    Route::get('download/{prosedur}', ['as' => 'informasi.prosedur.download', 'uses' => 'ProsedurController@download']);
                });

                // Regulasi
                Route::group(['prefix' => 'regulasi'], function () {
                    Route::get('/', ['as' => 'informasi.regulasi.index', 'uses' => 'RegulasiController@index']);
                    Route::get('show/{regulasi}', ['as' => 'informasi.regulasi.show', 'uses' => 'RegulasiController@show']);
                    Route::get('getdata', ['as' => 'informasi.regulasi.getdata', 'uses' => 'RegulasiController@getDataRegulasi']);
                    Route::get('create', ['as' => 'informasi.regulasi.create', 'uses' => 'RegulasiController@create']);
                    Route::post('store', ['as' => 'informasi.regulasi.store', 'uses' => 'RegulasiController@store']);
                    Route::get('edit/{regulasi}', ['as' => 'informasi.regulasi.edit', 'uses' => 'RegulasiController@edit']);
                    Route::put('update/{regulasi}', ['as' => 'informasi.regulasi.update', 'uses' => 'RegulasiController@update']);
                    Route::delete('destroy/{regulasi}', ['as' => 'informasi.regulasi.destroy', 'uses' => 'RegulasiController@destroy']);
                    Route::get('download/{regulasi}', ['as' => 'informasi.regulasi.download', 'uses' => 'RegulasiController@download']);
                });

                // FAQ
                Route::group(['prefix' => 'faq', 'excluded_middleware' => 'xss_sanitization'], function () {
                    Route::get('/', ['as' => 'informasi.faq.index', 'uses' => 'FaqController@index']);
                    Route::get('getdata', ['as' => 'informasi.faq.getdata', 'uses' => 'FaqController@getDataFaq']);
                    Route::get('show/{id}', ['as' => 'informasi.faq.show', 'uses' => 'FaqController@show']);
                    Route::get('create', ['as' => 'informasi.faq.create', 'uses' => 'FaqController@create']);
                    Route::post('store', ['as' => 'informasi.faq.store', 'uses' => 'FaqController@store']);
                    Route::get('edit/{id}', ['as' => 'informasi.faq.edit', 'uses' => 'FaqController@edit']);
                    Route::post('update/{id}', ['as' => 'informasi.faq.update', 'uses' => 'FaqController@update']);
                    Route::delete('destroy/{id}', ['as' => 'informasi.faq.destroy', 'uses' => 'FaqController@destroy']);
                });

                // Events
                Route::group(['prefix' => 'event', 'excluded_middleware' => 'xss_sanitization'], function () {
                    Route::get('/', ['as' => 'informasi.event.index', 'uses' => 'EventController@index']);
                    Route::get('show/{event}', ['as' => 'informasi.event.show', 'uses' => 'EventController@show']);
                    Route::get('create', ['as' => 'informasi.event.create', 'uses' => 'EventController@create']);
                    Route::post('store', ['as' => 'informasi.event.store', 'uses' => 'EventController@store']);
                    Route::get('edit/{event}', ['as' => 'informasi.event.edit', 'uses' => 'EventController@edit']);
                    Route::post('update/{event}', ['as' => 'informasi.event.update', 'uses' => 'EventController@update']);
                    Route::delete('destroy/{event}', ['as' => 'informasi.event.destroy', 'uses' => 'EventController@destroy']);
                });

                // Artikel
                Route::group(['prefix' => 'artikel', 'excluded_middleware' => 'xss_sanitization'], function () {
                    Route::get('/', ['as' => 'informasi.artikel.index', 'uses' => 'ArtikelController@index']);
                    Route::get('create', ['as' => 'informasi.artikel.create', 'uses' => 'ArtikelController@create']);
                    Route::post('store', ['as' => 'informasi.artikel.store', 'uses' => 'ArtikelController@store']);
                    Route::get('edit/{artikel}', ['as' => 'informasi.artikel.edit', 'uses' => 'ArtikelController@edit']);
                    Route::post('update/{artikel}', ['as' => 'informasi.artikel.update', 'uses' => 'ArtikelController@update']);
                    Route::delete('destroy/{artikel}', ['as' => 'informasi.artikel.destroy', 'uses' => 'ArtikelController@destroy']);
                    Route::get('getdata', ['as' => 'informasi.artikel.getdata', 'uses' => 'ArtikelController@getDataArtikel']);
                });

                // Form Dokumen
                Route::group(['prefix' => 'form-dokumen'], function () {
                    Route::get('/', ['as' => 'informasi.form-dokumen.index', 'uses' => 'FormDokumenController@index']);
                    Route::get('show/{dokumen}', ['as' => 'informasi.form-dokumen.show', 'uses' => 'FormDokumenController@show']);
                    Route::get('create', ['as' => 'informasi.form-dokumen.create', 'uses' => 'FormDokumenController@create']);
                    Route::get('getdata', ['as' => 'informasi.form-dokumen.getdata', 'uses' => 'FormDokumenController@getDataDokumen']);
                    Route::post('store', ['as' => 'informasi.form-dokumen.store', 'uses' => 'FormDokumenController@store']);
                    Route::get('edit/{dokumen}', ['as' => 'informasi.form-dokumen.edit', 'uses' => 'FormDokumenController@edit']);
                    Route::put('update/{dokumen}', ['as' => 'informasi.form-dokumen.update', 'uses' => 'FormDokumenController@update']);
                    Route::delete('destroy/{dokumen}', ['as' => 'informasi.form-dokumen.destroy', 'uses' => 'FormDokumenController@destroy']);
                    Route::get('download/{dokumen}', ['as' => 'informasi.form-dokumen.download', 'uses' => 'FormDokumenController@download']);
                });

                // Potensi
                Route::group(['prefix' => 'potensi'], function () {
                    Route::get('/', ['as' => 'informasi.potensi.index', 'uses' => 'PotensiController@index']);
                    Route::get('show/{potensi}', ['as' => 'informasi.potensi.show', 'uses' => 'PotensiController@show']);
                    Route::get('getdata', ['as' => 'informasi.potensi.getdata', 'uses' => 'PotensiController@getDataPotensi']);
                    Route::get('create', ['as' => 'informasi.potensi.create', 'uses' => 'PotensiController@create']);
                    Route::post('store', ['as' => 'informasi.potensi.store', 'uses' => 'PotensiController@store']);
                    Route::get('edit/{potensi}', ['as' => 'informasi.potensi.edit', 'uses' => 'PotensiController@edit']);
                    Route::put('update/{potensi}', ['as' => 'informasi.potensi.update', 'uses' => 'PotensiController@update']);
                    Route::delete('destroy/{potensi}', ['as' => 'informasi.potensi.destroy', 'uses' => 'PotensiController@destroy']);
                    Route::get('getdata', ['as' => 'informasi.potensi.getdata', 'uses' => 'PotensiController@getDataPotensi']);
                    Route::get('kategori', ['as' => 'informasi.potensi.kategori', 'uses' => 'PotensiController@kategori']);
                });

                // Media Sosial
                Route::group(['prefix' => 'media-sosial'], function () {
                    Route::get('/', ['as' => 'informasi.media-sosial.index', 'uses' => 'MediaSosialController@index']);
                    Route::get('getdata', ['as' => 'informasi.media-sosial.getdata', 'uses' => 'MediaSosialController@getDataMediaSosial']);
                    Route::get('show/{id}', ['as' => 'informasi.media-sosial.show', 'uses' => 'MediaSosialController@show']);
                    Route::get('create', ['as' => 'informasi.media-sosial.create', 'uses' => 'MediaSosialController@create']);
                    Route::post('store', ['as' => 'informasi.media-sosial.store', 'uses' => 'MediaSosialController@store']);
                    Route::get('edit/{id}', ['as' => 'informasi.media-sosial.edit', 'uses' => 'MediaSosialController@edit']);
                    Route::put('update/{id}', ['as' => 'informasi.media-sosial.update', 'uses' => 'MediaSosialController@update']);
                    Route::delete('destroy/{id}', ['as' => 'informasi.media-sosial.destroy', 'uses' => 'MediaSosialController@destroy']);
                });

                // Sinergi Program
                Route::group(['prefix' => 'sinergi-program'], function () {
                    Route::get('/', ['as' => 'informasi.sinergi-program.index', 'uses' => 'SinergiProgramController@index']);
                    Route::get('getdata', ['as' => 'informasi.sinergi-program.getdata', 'uses' => 'SinergiProgramController@getDataSinergiProgram']);
                    Route::get('show/{id}', ['as' => 'informasi.sinergi-program.show', 'uses' => 'SinergiProgramController@show']);
                    Route::get('create', ['as' => 'informasi.sinergi-program.create', 'uses' => 'SinergiProgramController@create']);
                    Route::post('store', ['as' => 'informasi.sinergi-program.store', 'uses' => 'SinergiProgramController@store']);
                    Route::get('edit/{id}', ['as' => 'informasi.sinergi-program.edit', 'uses' => 'SinergiProgramController@edit']);
                    Route::put('update/{id}', ['as' => 'informasi.sinergi-program.update', 'uses' => 'SinergiProgramController@update']);
                    Route::delete('destroy/{id}', ['as' => 'informasi.sinergi-program.destroy', 'uses' => 'SinergiProgramController@destroy']);
                    Route::get('urut/{id}/{arah}', ['as' => 'informasi.sinergi-program.urut', 'uses' => 'SinergiProgramController@urut']);
                });
            });
        });

        /**
         * Group Routing for Data
         */
        Route::namespace('\App\Http\Controllers\Data')->group(function () {
            Route::group(['prefix' => 'data'], function () {
                // Profil
                Route::group(['prefix' => 'profil', 'excluded_middleware' => ['complete_profile', 'xss_sanitization']], function () {
                    Route::get('/', ['as' => 'data.profil.index', 'uses' => 'ProfilController@index']);
                    Route::put('update/{id}', ['as' => 'data.profil.update', 'uses' => 'ProfilController@update']);
                    Route::get('success/{id}', ['as' => 'data.profil.success', 'uses' => 'ProfilController@success']);
                });

                // Data Umum
                Route::group(['prefix' => 'data-umum', 'excluded_middleware' => 'xss_sanitization', 'middleware' => ['role:super-admin|data-kecamatan']], function () {
                    Route::get('/', ['as' => 'data.data-umum.index', 'uses' => 'DataUmumController@index']);
                    Route::get('getdataajax', ['as' => 'data.data-umum.getdataajax', 'uses' => 'DataUmumController@getDataUmumAjax']);
                    Route::put('update/{id}', ['as' => 'data.data-umum.update', 'uses' => 'DataUmumController@update']);
                    Route::get('resetpeta/{id}', ['as' => 'data.data-umum.resetpeta', 'uses' => 'DataUmumController@resetPeta']);
                });

                // Data Desa
                Route::group(['prefix' => 'data-desa', 'middleware' => ['role:super-admin|admin-kecamatan']], function () {
                    Route::get('/', ['as' => 'data.data-desa.index', 'uses' => 'DataDesaController@index']);
                    Route::get('getdata', ['as' => 'data.data-desa.getdata', 'uses' => 'DataDesaController@getDataDesa']);
                    Route::get('getdata/ajax', ['as' => 'data.data-desa.getdataajax', 'uses' => 'DataDesaController@getDataDesaAjax']);
                    Route::post('getdesa', ['as' => 'data.data-desa.getdesa', 'uses' => 'DataDesaController@getDesaKecamatan']);
                    Route::get('peta/{id}', ['as' => 'data.data-desa.peta', 'uses' => 'DataDesaController@peta']);
                    Route::get('create', ['as' => 'data.data-desa.create', 'uses' => 'DataDesaController@create']);
                    Route::post('store', ['as' => 'data.data-desa.store', 'uses' => 'DataDesaController@store']);
                    Route::get('edit/{id}', ['as' => 'data.data-desa.edit', 'uses' => 'DataDesaController@edit']);
                    Route::put('update/{id}', ['as' => 'data.data-desa.update', 'uses' => 'DataDesaController@update']);
                    Route::delete('destroy/{id}', ['as' => 'data.data-desa.destroy', 'uses' => 'DataDesaController@destroy']);
                });

                // Jabatan
                Route::resource('jabatan', 'JabatanController', ['as' => 'data'])->middleware(['role:super-admin|admin-kecamatan'])->except(['show']);

                //Pengurus
                Route::post('pengurus/lock/{id}/{status}', ['as' => 'data.pengurus.lock', 'uses' => 'PengurusController@lock'])->middleware(['role:super-admin|admin-kecamatan']);
                Route::resource('pengurus', 'PengurusController', ['as' => 'data'])->middleware(['role:super-admin|admin-kecamatan'])->except(['show']);

                // Penduduk
                Route::group(['prefix' => 'penduduk', 'middleware' => ['role:super-admin|admin-desa']], function () {
                    Route::get('/', ['as' => 'data.penduduk.index', 'uses' => 'PendudukController@index']);
                    Route::get('getdata', ['as' => 'data.penduduk.getdata', 'uses' => 'PendudukController@getPenduduk']);
                    Route::get('show/{id}', ['as' => 'data.penduduk.show', 'uses' => 'PendudukController@show']);
                    Route::get('import', ['as' => 'data.penduduk.import', 'uses' => 'PendudukController@import']);
                    Route::post('import-excel', ['as' => 'data.penduduk.import-excel', 'uses' => 'PendudukController@importExcel']);
                });

                // Keluarga
                Route::group(['prefix' => 'keluarga', 'middleware' => ['role:super-admin|admin-desa']], function () {
                    Route::get('/', ['as' => 'data.keluarga.index', 'uses' => 'KeluargaController@index']);
                    Route::get('getdata', ['as' => 'data.keluarga.getdata', 'uses' => 'KeluargaController@getKeluarga']);
                    Route::get('show/{id}', ['as' => 'data.keluarga.show', 'uses' => 'KeluargaController@show']);
                });

                // Data Suplemen
                Route::group(['prefix' => 'data-suplemen', 'middleware' => ['role:super-admin|admin-desa']], function () {
                    Route::get('/', ['as' => 'data.data-suplemen.index', 'uses' => 'SuplemenController@index']);
                    Route::get('getdata', ['as' => 'data.data-suplemen.getdata', 'uses' => 'SuplemenController@getDataSuplemen']);
                    Route::get('getsuplementerdata', ['as' => 'data.data-suplemen.getsuplementerdata', 'uses' => 'SuplemenController@getDataSuplemenTerdata']);
                    Route::get('show/{id}', ['as' => 'data.data-suplemen.show', 'uses' => 'SuplemenController@show']);
                    Route::get('create', ['as' => 'data.data-suplemen.create', 'uses' => 'SuplemenController@create']);
                    Route::post('store', ['as' => 'data.data-suplemen.store', 'uses' => 'SuplemenController@store']);
                    Route::get('edit/{id}', ['as' => 'data.data-suplemen.edit', 'uses' => 'SuplemenController@edit']);
                    Route::put('update/{id}', ['as' => 'data.data-suplemen.update', 'uses' => 'SuplemenController@update']);
                    Route::delete('destroy/{id}', ['as' => 'data.data-suplemen.destroy', 'uses' => 'SuplemenController@destroy']);
                    Route::get('getsuplementerdata/{id_suplemen}', ['as' => 'data.data-suplemen.getsuplementerdata', 'uses' => 'SuplemenController@getDataSuplemenTerdata']);
                    Route::get('createdetail/{id_suplemen}', ['as' => 'data.data-suplemen.createdetail', 'uses' => 'SuplemenController@createDetail']);
                    Route::get('getpenduduk/{id_desa}/{id_suplemen}', ['as' => 'data.data-suplemen.getpenduduk', 'uses' => 'SuplemenController@getPenduduk']);
                    Route::post('storedetail', ['as' => 'data.data-suplemen.storedetail', 'uses' => 'SuplemenController@storeDetail']);
                    Route::get('editdetail/{id}/{id_suplemen}', ['as' => 'data.data-suplemen.editdetail', 'uses' => 'SuplemenController@editDetail']);
                    Route::put('updatedetail/{id}', ['as' => 'data.data-suplemen.updatedetail', 'uses' => 'SuplemenController@updateDetail']);
                    Route::delete('destroydetail/{id}/{id_suplemen}', ['as' => 'data.data-suplemen.destroydetail', 'uses' => 'SuplemenController@destroyDetail']);
                });

                // Laporan Penduduk
                Route::group(['prefix' => 'laporan-penduduk', 'middleware' => ['role:super-admin|admin-desa']], function () {
                    Route::get('/', ['as' => 'data.laporan-penduduk.index', 'uses' => 'LaporanPendudukController@index']);
                    Route::get('getdata', ['as' => 'data.laporan-penduduk.getdata', 'uses' => 'LaporanPendudukController@getData']);
                    Route::delete('destroy/{id}', ['as' => 'data.laporan-penduduk.destroy', 'uses' => 'LaporanPendudukController@destroy']);
                    Route::get('download{id}', ['as' => 'data.laporan-penduduk.download', 'uses' => 'LaporanPendudukController@download']);
                    Route::get('import', ['as' => 'data.laporan-penduduk.import', 'uses' => 'LaporanPendudukController@import']);
                    Route::post('do_import', ['as' => 'data.laporan-penduduk.do_import', 'uses' => 'LaporanPendudukController@do_import']);
                });

                // AKI & AKB
                Route::group(['prefix' => 'aki-akb', 'middleware' => ['role:super-admin|admin-puskesmas']], function () {
                    Route::get('/', ['as' => 'data.aki-akb.index', 'uses' => 'AKIAKBController@index']);
                    Route::get('getdata', ['as' => 'data.aki-akb.getdata', 'uses' => 'AKIAKBController@getDataAKIAKB']);
                    Route::get('edit/{id}', ['as' => 'data.aki-akb.edit', 'uses' => 'AKIAKBController@edit']);
                    Route::put('update/{id}', ['as' => 'data.aki-akb.update', 'uses' => 'AKIAKBController@update']);
                    Route::delete('destroy/{id}', ['as' => 'data.aki-akb.destroy', 'uses' => 'AKIAKBController@destroy']);
                    Route::get('import', ['as' => 'data.aki-akb.import', 'uses' => 'AKIAKBController@import']);
                    Route::post('do_import', ['as' => 'data.aki-akb.do_import', 'uses' => 'AKIAKBController@do_import']);
                });

                // AKI & AKB
                Route::group(['prefix' => 'imunisasi', 'middleware' => ['role:super-admin|admin-puskesmas']], function () {
                    Route::get('/', ['as' => 'data.imunisasi.index', 'uses' => 'ImunisasiController@index']);
                    Route::get('getdata', ['as' => 'data.imunisasi.getdata', 'uses' => 'ImunisasiController@getDataAKIAKB']);
                    Route::get('edit/{id}', ['as' => 'data.imunisasi.edit', 'uses' => 'ImunisasiController@edit']);
                    Route::put('update/{id}', ['as' => 'data.imunisasi.update', 'uses' => 'ImunisasiController@update']);
                    Route::delete('destroy/{id}', ['as' => 'data.imunisasi.destroy', 'uses' => 'ImunisasiController@destroy']);
                    Route::get('import', ['as' => 'data.imunisasi.import', 'uses' => 'ImunisasiController@import']);
                    Route::post('do_import', ['as' => 'data.imunisasi.do_import', 'uses' => 'ImunisasiController@do_import']);
                });

                // Epidemi Penyakit
                Route::group(['prefix' => 'epidemi-penyakit', 'middleware' => ['role:super-admin|admin-puskesmas']], function () {
                    Route::get('/', ['as' => 'data.epidemi-penyakit.index', 'uses' => 'EpidemiPenyakitController@index']);
                    Route::get('getdata', ['as' => 'data.epidemi-penyakit.getdata', 'uses' => 'EpidemiPenyakitController@getDataAKIAKB']);
                    Route::get('edit/{id}', ['as' => 'data.epidemi-penyakit.edit', 'uses' => 'EpidemiPenyakitController@edit']);
                    Route::put('update/{id}', ['as' => 'data.epidemi-penyakit.update', 'uses' => 'EpidemiPenyakitController@update']);
                    Route::delete('destroy/{id}', ['as' => 'data.epidemi-penyakit.destroy', 'uses' => 'EpidemiPenyakitController@destroy']);
                    Route::get('import', ['as' => 'data.epidemi-penyakit.import', 'uses' => 'EpidemiPenyakitController@import']);
                    Route::post('do_import', ['as' => 'data.epidemi-penyakit.do_import', 'uses' => 'EpidemiPenyakitController@do_import']);
                });

                // Toilet Sanitasi
                Route::group(['prefix' => 'toilet-sanitasi', 'middleware' => ['role:super-admin|admin-puskesmas']], function () {
                    Route::get('/', ['as' => 'data.toilet-sanitasi.index', 'uses' => 'ToiletSanitasiController@index']);
                    Route::get('getdata', ['as' => 'data.toilet-sanitasi.getdata', 'uses' => 'ToiletSanitasiController@getDataAKIAKB']);
                    Route::get('edit/{id}', ['as' => 'data.toilet-sanitasi.edit', 'uses' => 'ToiletSanitasiController@edit']);
                    Route::put('update/{id}', ['as' => 'data.toilet-sanitasi.update', 'uses' => 'ToiletSanitasiController@update']);
                    Route::delete('destroy/{id}', ['as' => 'data.toilet-sanitasi.destroy', 'uses' => 'ToiletSanitasiController@destroy']);
                    Route::get('import', ['as' => 'data.toilet-sanitasi.import', 'uses' => 'ToiletSanitasiController@import']);
                    Route::post('do_import', ['as' => 'data.toilet-sanitasi.do_import', 'uses' => 'ToiletSanitasiController@do_import']);
                });

                // Tingkaat Pendidikan
                Route::group(['prefix' => 'tingkat-pendidikan', 'middleware' => ['role:super-admin|admin-pendidikan|administrator-website']], function () {
                    Route::get('/', ['as' => 'data.tingkat-pendidikan.index', 'uses' => 'TingkatPendidikanController@index']);
                    Route::get('getdata', ['as' => 'data.tingkat-pendidikan.getdata', 'uses' => 'TingkatPendidikanController@getData']);
                    Route::delete('destroy/{id}', ['as' => 'data.tingkat-pendidikan.destroy', 'uses' => 'TingkatPendidikanController@destroy']);
                    Route::get('import', ['as' => 'data.tingkat-pendidikan.import', 'uses' => 'TingkatPendidikanController@import']);
                    Route::post('do_import', ['as' => 'data.tingkat-pendidikan.do_import', 'uses' => 'TingkatPendidikanController@do_import']);
                });

                // Putus Sekolah
                Route::group(['prefix' => 'putus-sekolah', 'middleware' => ['role:super-admin|admin-pendidikan|administrator-website']], function () {
                    Route::get('/', ['as' => 'data.putus-sekolah.index', 'uses' => 'PutusSekolahController@index']);
                    Route::get('getdata', ['as' => 'data.putus-sekolah.getdata', 'uses' => 'PutusSekolahController@getDataPutusSekolah']);
                    Route::get('edit/{id}', ['as' => 'data.putus-sekolah.edit', 'uses' => 'PutusSekolahController@edit']);
                    Route::put('update/{id}', ['as' => 'data.putus-sekolah.update', 'uses' => 'PutusSekolahController@update']);
                    Route::delete('destroy/{id}', ['as' => 'data.putus-sekolah.destroy', 'uses' => 'PutusSekolahController@destroy']);
                    Route::get('import', ['as' => 'data.putus-sekolah.import', 'uses' => 'PutusSekolahController@import']);
                    Route::post('do_import', ['as' => 'data.putus-sekolah.do_import', 'uses' => 'PutusSekolahController@do_import']);
                });

                // Fasilitas PAUD
                Route::group(['prefix' => 'fasilitas-paud', 'middleware' => ['role:super-admin|admin-pendidikan|administrator-website']], function () {
                    Route::get('/', ['as' => 'data.fasilitas-paud.index', 'uses' => 'FasilitasPaudController@index']);
                    Route::get('getdata', ['as' => 'data.fasilitas-paud.getdata', 'uses' => 'FasilitasPaudController@getDataFasilitasPAUD']);
                    Route::get('edit/{id}', ['as' => 'data.fasilitas-paud.edit', 'uses' => 'FasilitasPaudController@edit']);
                    Route::put('update/{id}', ['as' => 'data.fasilitas-paud.update', 'uses' => 'FasilitasPaudController@update']);
                    Route::delete('destroy/{id}', ['as' => 'data.fasilitas-paud.destroy', 'uses' => 'FasilitasPaudController@destroy']);
                    Route::get('import', ['as' => 'data.fasilitas-paud.import', 'uses' => 'FasilitasPaudController@import']);
                    Route::post('do_import', ['as' => 'data.fasilitas-paud.do_import', 'uses' => 'FasilitasPaudController@do_import']);
                });

                // Program Bantuan
                Route::group(['prefix' => 'program-bantuan', 'middleware' => ['role:super-admin|administrator-website|admin-desa']], function () {
                    Route::get('/', ['as' => 'data.program-bantuan.index', 'uses' => 'ProgramBantuanController@index']);
                    Route::get('getdata', ['as' => 'data.program-bantuan.getdata', 'uses' => 'ProgramBantuanController@getaProgramBantuan']);
                    Route::get('show/{id}/{id_desa}', ['as' => 'data.program-bantuan.show', 'uses' => 'ProgramBantuanController@show']);
                    Route::get('import', ['as' => 'data.program-bantuan.import', 'uses' => 'ProgramBantuanController@import']);
                    Route::post('do_import', ['as' => 'data.program-bantuan.do_import', 'uses' => 'ProgramBantuanController@do_import']);
                });

                // Anggaran Realisasi
                Route::group(['prefix' => 'anggaran-realisasi', 'middleware' => ['role:super-admin|administrator-website|admin-kecamatan']], function () {
                    Route::get('/', ['as' => 'data.anggaran-realisasi.index', 'uses' => 'AnggaranRealisasiController@index']);
                    Route::get('getdata', ['as' => 'data.anggaran-realisasi.getdata', 'uses' => 'AnggaranRealisasiController@getDataAnggaran']);
                    Route::get('edit/{id}', ['as' => 'data.anggaran-realisasi.edit', 'uses' => 'AnggaranRealisasiController@edit']);
                    Route::put('update/{id}', ['as' => 'data.anggaran-realisasi.update', 'uses' => 'AnggaranRealisasiController@update']);
                    Route::delete('destroy/{id}', ['as' => 'data.anggaran-realisasi.destroy', 'uses' => 'AnggaranRealisasiController@destroy']);
                    Route::get('import', ['as' => 'data.anggaran-realisasi.import', 'uses' => 'AnggaranRealisasiController@import']);
                    Route::post('do_import', ['as' => 'data.anggaran-realisasi.do_import', 'uses' => 'AnggaranRealisasiController@do_import']);
                });

                // Anggaran Desa
                Route::group(['prefix' => 'anggaran-desa', 'middleware' => ['role:super-admin|administrator-website|admin-desa']], function () {
                    Route::get('/', ['as' => 'data.anggaran-desa.index', 'uses' => 'AnggaranDesaController@index']);
                    Route::get('getdata', ['as' => 'data.anggaran-desa.getdata', 'uses' => 'AnggaranDesaController@getDataAnggaran']);
                    Route::delete('destroy/{id}', ['as' => 'data.anggaran-desa.destroy', 'uses' => 'AnggaranDesaController@destroy']);
                    Route::get('import', ['as' => 'data.anggaran-desa.import', 'uses' => 'AnggaranDesaController@import']);
                    Route::post('do_import', ['as' => 'data.anggaran-desa.do_import', 'uses' => 'AnggaranDesaController@do_import']);
                });

                // Laporan Apbdes
                Route::group(['prefix' => 'laporan-apbdes', 'middleware' => ['role:super-admin|administrator-website|admin-desa']], function () {
                    Route::get('/', ['as' => 'data.laporan-apbdes.index', 'uses' => 'LaporanApbdesController@index']);
                    Route::get('getdata', ['as' => 'data.laporan-apbdes.getdata', 'uses' => 'LaporanApbdesController@getApbdes']);
                    Route::delete('destroy/{id}', ['as' => 'data.laporan-apbdes.destroy', 'uses' => 'LaporanApbdesController@destroy']);
                    Route::get('download{id}', ['as' => 'data.laporan-apbdes.download', 'uses' => 'LaporanApbdesController@download']);
                    Route::get('import', ['as' => 'data.laporan-apbdes.import', 'uses' => 'LaporanApbdesController@import']);
                    Route::post('do_import', ['as' => 'data.laporan-apbdes.do_import', 'uses' => 'LaporanApbdesController@do_import']);
                    Route::get('download/{id}', ['as' => 'data.laporan-apbdes.download', 'uses' => 'LaporanApbdesController@download']);
                });

                // Pembangunan
                Route::group(['prefix' => 'pembangunan', 'middleware' => ['role:super-admin|administrator-website|admin-desa']], function () {
                    Route::get('/', ['as' => 'data.pembangunan.index', 'uses' => 'DataPembangunanController@index']);
                    Route::get('getdata', ['as' => 'data.pembangunan.getdata', 'uses' => 'DataPembangunanController@getPembangunan']);
                    Route::get('rincian/{id}/{desa_id}', ['as' => 'data.pembangunan.rincian', 'uses' => 'DataPembangunanController@rincian']);
                    Route::get('getrinciandata/{id}/{desa_id}', ['as' => 'data.pembangunan.getrinciandata', 'uses' => 'DataPembangunanController@getrinciandata']);
                });
            });

            // Admin SIKEMA
            Route::group(['prefix' => 'admin-komplain', 'middleware' => ['role:administrator-website|admin-komplain|super-admin|kontributor-artikel']], function () {
                Route::get('/', ['as' => 'admin-komplain.index', 'uses' => 'AdminKomplainController@index']);
                Route::get('getdata', ['as' => 'admin-komplain.getdata', 'uses' => 'AdminKomplainController@getDataKomplain']);
                Route::get('edit/{id}', ['as' => 'admin-komplain.edit', 'uses' => 'AdminKomplainController@edit']);
                Route::put('update/{id}', ['as' => 'admin-komplain.update', 'uses' => 'AdminKomplainController@update']);
                Route::delete('destroy/{id}', ['as' => 'admin-komplain.destroy', 'uses' => 'AdminKomplainController@destroy']);
                Route::put('setuju/{id}', ['as' => 'admin-komplain.setuju', 'uses' => 'AdminKomplainController@disetujui']);
                Route::get('statistik', ['as' => 'admin-komplain.statistik', 'uses' => 'AdminKomplainController@statistik']);
                Route::get('show/{id}', ['as' => 'admin-komplain.show', 'uses' => 'AdminKomplainController@show']);
                Route::delete('deletekomentar/{id}', ['as' => 'admin-komplain.deletekomentar', 'uses' => 'AdminKomplainController@deletekomentar']);
                Route::get('getkomentar/{id}', ['as' => 'admin-komplain.getkomentar', 'uses' => 'AdminKomplainController@getKomentar']);
                Route::put('updatekomentar/{id}', ['as' => 'admin-komplain.updatekomentar', 'uses' => 'AdminKomplainController@updateKomentar']);
            });
        });

        /**
         * Group Routing for Pesan
         */
        Route::namespace('\App\Http\Controllers\Pesan')->group(function () {
            //Routes Resource Pesan
            Route::group(['prefix' => 'pesan'], function () {
                Route::get('/', ['as' => 'pesan.index', 'uses' => 'PesanController@index']);
                Route::get('/keluar', ['as' => 'pesan.keluar', 'uses' => 'PesanController@loadPesanKeluar']);
                Route::get('/arsip', ['as' => 'pesan.arsip', 'uses' => 'PesanController@loadPesanArsip']);
                Route::post('/arsip', ['as' => 'pesan.arsip.post', 'uses' => 'PesanController@setArsipPesan']);
                Route::get('/compose', ['as' => 'pesan.compose', 'uses' => 'PesanController@composePesan']);
                Route::post('/compose/post', ['as' => 'pesan.compose.post', 'uses' => 'PesanController@storeComposePesan']);
                Route::post('/read/multiple', ['as' => 'pesan.read.multiple', 'uses' => 'PesanController@setMultipleReadPesanStatus']);
                Route::post('/arsip/multiple', ['as' => 'pesan.arsip.multiple', 'uses' => 'PesanController@setMultipleArsipPesanStatus']);
                Route::post('/reply', ['as' => 'pesan.reply.post', 'uses' => 'PesanController@replyPesan']);
                Route::get('/{id_pesan}', ['as' => 'pesan.read', 'uses' => 'PesanController@readPesan']);
            });
        });

        /**
         * Group Routing for Pesan
         */
        Route::namespace('\App\Http\Controllers\Surat')->group(function () {
            Route::group(['prefix' => 'surat', 'middleware' => ['role:super-admin|admin-kecamatan']], function () {
                //permohonan
                Route::group(['prefix' => 'permohonan'], function () {
                    Route::get('/', ['as' => 'surat.permohonan', 'uses' => 'PermohonanController@index']);
                    Route::get('getdata', ['as' => 'surat.permohonan.getdata', 'uses' => 'PermohonanController@getData']);
                    Route::get('show/{surat}', ['as' => 'surat.permohonan.show', 'uses' => 'PermohonanController@show']);
                    Route::get('download/{surat}', ['as' => 'surat.permohonan.download', 'uses' => 'PermohonanController@download']);
                    Route::get('setujui/{surat}', ['as' => 'surat.permohonan.setujui', 'uses' => 'PermohonanController@setujui']);
                    Route::post('tolak/{surat}', ['as' => 'surat.permohonan.tolak', 'uses' => 'PermohonanController@tolak']);
                    Route::get('ditolak', ['as' => 'surat.permohonan.ditolak', 'uses' => 'PermohonanController@ditolak']);
                    Route::get('getdataditolak', ['as' => 'surat.permohonan.getdataditolak', 'uses' => 'PermohonanController@getDataDitolak']);
                    Route::post('passphrase/{surat}', ['as' => 'surat.permohonan.passphrase', 'uses' => 'PermohonanController@passphrase']);
                });

                //arsip
                Route::get('/arsip', ['as' => 'surat.arsip', 'uses' => 'SuratController@arsip']);
                Route::get('/arsip/getdata', ['as' => 'surat.arsip.getdata', 'uses' => 'SuratController@getData']);
                Route::get('/arsip/qrcode/{surat}', ['as' => 'surat.arsip.qrcode', 'uses' => 'SuratController@qrcode']);
                Route::get('/arsip/download/{surat}', ['as' => 'surat.arsip.download', 'uses' => 'SuratController@download']);

                //pengaturan
                Route::get('/pengaturan', ['as' => 'surat.pengaturan', 'uses' => 'SuratController@pengaturan']);
                Route::put('/pengaturan/update', ['as' => 'surat.pengaturan.update', 'uses' => 'SuratController@pengaturan_update']);
            });
        });

        /**
         * Group Routing for Setting
         */
        Route::group(['prefix' => 'setting'], function () {
            // User Management
            Route::group(['prefix' => 'user', 'controller' => UserController::class, 'middleware' => ['role:super-admin|administrator-website']], function () {
                Route::get('/', 'index')->name('setting.user.index');
                Route::get('getdata', 'getDataUser')->name('setting.user.getdata');
                Route::get('create', 'create')->name('setting.user.create');
                Route::post('store', 'store')->name('setting.user.store');
                Route::get('edit/{id}', 'edit')->name('setting.user.edit');
                Route::put('update/{id}', 'update')->name('setting.user.update');
                Route::put('updatePassword/{id}', 'updatePassword')->name('setting.user.updatePassword');
                Route::put('password/{id}', 'password')->name('setting.user.password');
                Route::post('destroy/{id}', 'destroy')->name('setting.user.destroy');
                Route::post('active/{id}', 'active')->name('setting.user.active');
                Route::get('photo-profil/{id}', 'photo')->name('setting.user.photo');
                Route::put('update-photo/{id}', 'updatePhoto')->name('setting.user.uphoto');
            });

            // Role Management
            Route::group(['prefix' => 'role', 'controller' => RoleController::class, 'middleware' => ['role:super-admin|administrator-website']], function () {
                Route::get('/', 'index')->name('setting.role.index');
                Route::get('getdata', 'getData')->name('setting.role.getdata');
                Route::get('create', 'create')->name('setting.role.create');
                Route::post('store', 'store')->name('setting.role.store');
                Route::get('edit/{id}', 'edit')->name('setting.role.edit');
                Route::put('update/{id}', 'update')->name('setting.role.update');
                Route::delete('destroy/{id}', 'destroy')->name('setting.role.destroy');
            });

            // Komplain Kategori
            Route::group(['prefix' => 'komplain-kategori', 'controller' => KategoriKomplainController::class, 'middleware' => ['role:super-admin|admin-komplain|administrator-website']], function () {
                Route::get('/', 'index')->name('setting.komplain-kategori.index');
                Route::get('getdata', 'getData')->name('setting.komplain-kategori.getdata');
                Route::get('create', 'create')->name('setting.komplain-kategori.create');
                Route::post('store', 'store')->name('setting.komplain-kategori.store');
                Route::get('edit/{id}', 'edit')->name('setting.komplain-kategori.edit');
                Route::put('update/{id}', 'update')->name('setting.komplain-kategori.update');
                Route::delete('destroy/{id}', 'destroy')->name('setting.komplain-kategori.destroy');
            });

            // Tipe Regulasi
            Route::group(['prefix' => 'tipe-regulasi', 'controller' => TipeRegulasiController::class, 'middleware' => ['role:super-admin|administrator-website']], function () {
                Route::get('/', 'index')->name('setting.tipe-regulasi.index');
                Route::get('getdata', 'getData')->name('setting.tipe-regulasi.getdata');
                Route::get('create', 'create')->name('setting.tipe-regulasi.create');
                Route::post('store', 'store')->name('setting.tipe-regulasi.store');
                Route::get('edit/{id}', 'edit')->name('setting.tipe-regulasi.edit');
                Route::put('update/{id}', 'update')->name('setting.tipe-regulasi.update');
                Route::delete('destroy/{id}', 'destroy')->name('setting.tipe-regulasi.destroy');
            });

            // Jenis Penyakit
            Route::group(['prefix' => 'jenis-penyakit', 'controller' => JenisPenyakitController::class, 'middleware' => ['role:super-admin|admin-puskesmas|administrator-website']], function () {
                Route::get('/', 'index')->name('setting.jenis-penyakit.index');
                Route::get('getdata', 'getData')->name('setting.jenis-penyakit.getdata');
                Route::get('create', 'create')->name('setting.jenis-penyakit.create');
                Route::post('store', 'store')->name('setting.jenis-penyakit.store');
                Route::get('edit/{id}', 'edit')->name('setting.jenis-penyakit.edit');
                Route::put('update/{id}', 'update')->name('setting.jenis-penyakit.update');
                Route::delete('destroy/{id}', 'destroy')->name('setting.jenis-penyakit.destroy');
            });

            // Tipe Potensi
            Route::group(['prefix' => 'tipe-potensi', 'controller' => TipePotensiController::class, 'middleware' => ['role:super-admin|administrator-website']], function () {
                Route::get('/', 'index')->name('setting.tipe-potensi.index');
                Route::get('getdata', 'getData')->name('setting.tipe-potensi.getdata');
                Route::get('create', 'create')->name('setting.tipe-potensi.create');
                Route::post('store', 'store')->name('setting.tipe-potensi.store');
                Route::get('edit/{id}', 'edit')->name('setting.tipe-potensi.edit');
                Route::put('update/{id}', 'update')->name('setting.tipe-potensi.update');
                Route::delete('destroy/{id}', 'destroy')->name('setting.tipe-potensi.destroy');
            });

            // Slide
            Route::group(['prefix' => 'slide', 'controller' => SlideController::class, 'middleware' => ['role:super-admin|administrator-website']], function () {
                Route::get('/', 'index')->name('setting.slide.index');
                Route::get('getdata', 'getData')->name('setting.slide.getdata');
                Route::get('create', 'create')->name('setting.slide.create');
                Route::post('store', 'store')->name('setting.slide.store');
                Route::get('edit/{slide}', 'edit')->name('setting.slide.edit');
                Route::get('show/{slide}', 'show')->name('setting.slide.show');
                Route::put('update/{slide}', 'update')->name('setting.slide.update');
                Route::delete('destroy/{slide}', 'destroy')->name('setting.slide.destroy');
            });

            // COA
            Route::group(['prefix' => 'coa', 'controller' => COAController::class, 'middleware' => ['role:super-admin|administrator-website']], function () {
                Route::get('/', 'index')->name('setting.coa.index');
                Route::get('create', 'create')->name('setting.coa.create');
                Route::post('store', 'store')->name('setting.coa.store');
                Route::get('sub_coa/{type_id}', 'get_sub_coa')->name('setting.coa.sub_coa');
                Route::get('sub_sub_coa/{type_id}/{sub_id}', 'get_sub_sub_coa')->name('setting.coa.sub_sub_coa');
                Route::get('generate_id/{type_id}/{sub_id}/{sub_sub_id}', 'generate_id')->name('setting.coa.generate_id');
            });

            // Themes
            Route::group(['prefix' => 'themes', 'controller' => ThemesController::class, 'middleware' => ['role:super-admin|administrator-website']], function () {
                Route::get('/', 'index')->name('setting.themes.index');
                Route::get('activate/{themes}', 'activate')->name('setting.themes.activate');
                Route::get('rescan', 'rescan')->name('setting.themes.rescan');
                // post to-upload
                Route::post('upload', 'upload')->name('setting.themes.upload');
                Route::delete('destroy/{themes}', 'destroy')->name('setting.themes.destroy');
            });

            Route::group(['prefix' => 'aplikasi', 'controller' => AplikasiController::class, 'middleware' => ['role:super-admin|administrator-website']], function () {
                Route::get('/', 'index')->name('setting.aplikasi.index');
                Route::get('/edit/{aplikasi}', 'edit')->name('setting.aplikasi.edit');
                Route::put('/update/{aplikasi}', 'update')->name('setting.aplikasi.update');
            });

            Route::group(['prefix' => 'info-sistem', 'controller' => LogViewerController::class, 'middleware' => ['role:super-admin|administrator-website']], function () {
                Route::get('/', 'index')->name('setting.info-sistem');
                Route::get('/linkstorage', 'linkStorage')->name('setting.info-sistem.linkstorage');
                Route::get('/queuelisten', 'queueListen')->name('setting.info-sistem.queuelisten');
                Route::get('/migrasi', 'migrasi')->name('setting.info-sistem.migrasi');
            });
        });

        /**
         * Group Routing for Counter
         */
        Route::group(['prefix' => 'counter'], function () {
            Route::get('/', [CounterController::class, 'index'])->name('counter.index');
        });
    });

    Route::group(['middleware' => ['web']], function () {
        if (Cookie::get(env('COUNTER_COOKIE', 'kd-counter')) == false) {
            Cookie::queue(env('COUNTER_COOKIE', 'kd-counter'), str_random(80), 2628000); // Forever aka 5 years
        }
    });

    Route::group(['controller' => SitemapController::class], function () {
        Route::get('/sitemap.xml', 'index')->name('sitemap');
        Route::get('/sitemap', function () {
            return redirect()->route('sitemap');
        });
        Route::get('/sitemap/prosedur', 'prosedur');
    });

    // Semua Desa
    Route::get('/api/desa', function () {
        return DataDesa::paginate(10)->name('api.desa');
    });

    Route::get('/api/list-penduduk', function () {
        return Penduduk::selectRaw('nik as id, nama as text, nik, nama, alamat, rt, rw, tempat_lahir, tanggal_lahir')
            ->whereRaw('lower(nama) LIKE \'%'.strtolower(request('q')).'%\' or lower(nik) LIKE \'%'.strtolower(request('q')).'%\'')
            ->paginate(10);
    });

    // TODO : Peserta KK gunakan das_keluarga
    Route::get('/api/list-kk', function () {
        return Penduduk::selectRaw('no_kk as id, nama as text, nik, nama, alamat, rt, rw, tempat_lahir, tanggal_lahir')
            ->whereRaw('lower(nama) LIKE \'%'.strtolower(request('q')).'%\' or lower(no_kk) LIKE \'%'.strtolower(request('q')).'%\'')
            ->where('kk_level', 1)
            ->paginate(10);
    });
});
