<?php

namespace App\Http\Controllers;

use HTMLPurifier;
use HTMLPurifier_Config;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Storage;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Ramsey\Uuid\Uuid;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    private   $action           = ''; // c, r, u, d
    private   $actionResponse   = ''; // default, json, modal
    protected $userMenu    = '';
    protected $userAccess  = [];
    protected $periode     = null;
    protected $menuCode    = null;
    protected $menuUrl     = '';
    protected $menuTitle   = '';
    protected $viewPath    = '';
    protected $prefixAdmin = 'admin';
    protected $prefixDose  = 'dosen';
    protected $prefixMahasiswa = 'mahasiswa';
    protected $pathFile      = 'public/';
    protected $ruleFileValidation = 'required|file|mimetypes:application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/pdf,application/vnd.ms-powerpoint,application/vnd.openxmlformats-officedocument.presentationml.presentation,text/plain,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.oasis.opendocument.presentation,application/vnd.oasis.opendocument.spreadsheet,application/vnd.oasis.opendocument.text,application/zip,application/octet-stream,application/x-zip-compressed,multipart/x-zip|max:8192'; // maksimal 5MB

    protected function getMessage($index = null)
    {
        $message = [
            'insert.success'    => 'Data berhasil disimpan.',
            'insert.failed'     => 'Data gagal disimpan.',
            'update.success'    => 'Data berhasil diganti.',
            'update.failed'     => 'Data gagal diganti.',
            'delete.success'    => 'Data berhasil dihapus.',
            'delete.failed'     => 'Data gagal dihapus.',
            'delete.prevent'    => 'Data tidak dapat dihapus karena memiliki relasi dengan data lain.',
            'data.found'        => 'Data ditemukan.',
            'data.notfound'     => 'Data yang dicari tidak ditemukan.',
            'validate.success'   => 'Validasi berhasil dilakukan. Data Check In disimpan.',
            'validate.failed'    => 'Validasi gagal dilakukan.',
            'password.success'  => 'Password yang dimasukkan berhasil diganti.',
            'password.failed'   => 'Password yang dimasukkan salah.',
            '403'               => 'Anda tidak memiliki hak akses untuk halaman ini.',
            '404'               => 'Halaman yang diakses tidak tersedia.',
            'upload.success'    => 'Data berhasil diunggah.',
            'upload.failed'     => 'Data gagal diunggah.',

            'payment.success'     => 'Pembayaran berhasil disimpan.',
            'payment.failed'     => 'Pembayaran gagal disimpan.',
        ];
        return ($message[$index]) ? $message[$index] : 'Pesan belum didefinisikan.';
    }

    protected function populateValidationErrors($errors)
    {
        $errors = $errors->all();
        $error = '';
        if (count($errors) > 1) {
            $error = '<ul>';
            foreach ($errors as $value) {
                $error .= '<li>' . $value . '</li>';
            }
            $error .= '</ul>';
        } else {
            $error = $errors[0];
        }
        return $error;
    }

    protected function showModalError($subject = 'Kesalahan', $title = 'Terjadi Kesalahan!!!', $message = 'Data yang dicari tidak ditermukan.')
    {
        return view('layouts.modal_error')->with('subject', $subject)->with('title', $title)->with('message', $message);
    }

    protected function showModalConfirm($url, $info = ['keterangan' => '~ tidak ada ~'], $title = 'Konfirmasi Hapus Data', $desc = 'Apakah anda yakin menghapus data berikut:', $btnAction = 'Ya, Hapus', $action = 'DELETE')
    {
        return view('layouts.modal_confirm')->with('url', url($url))->with('title', $title)->with('desc', $desc)->with('info', $info)->with('btnAction', $btnAction)->with('action', $action);
    }

    protected function showModalReject($url, $info = ['keterangan' => '~ tidak ada ~'], $title = 'Konfirmasi Hapus Data', $desc = 'Apakah anda yakin menghapus data berikut:', $btnAction = 'Ya, Hapus', $action = 'DELETE')
    {
        return view('layouts.modal_reject')->with('url', url($url))->with('title', $title)->with('desc', $desc)->with('info', $info)->with('btnAction', $btnAction)->with('action', $action);
    }

    protected function showModalConfirmCustom($layout, $url, $info = ['keterangan' => '~ tidak ada ~'], $title = 'Konfirmasi Hapus Data', $desc = 'Apakah anda yakin menghapus data berikut:', $btnAction = 'Ya, Hapus', $action = 'DELETE', $otherData = [])
    {
        return view($layout)->with('url', url($url))->with('title', $title)->with('desc', $desc)->with('info', $info)->with('btnAction', $btnAction)->with('action', $action)->with('otherData', $otherData);
    }

    protected function showPageNotFound($subject = 'Kesalahan', $title = 'Terjadi Kesalahan!!!', $message = 'Data yang dicari tidak ditermukan.', $breadcrumb = [], $active_menu = [])
    {
        $breadcrumb = empty($breadcrumb) ? [
            'title' => 'Halaman tidak ditemukan',
            'list'  => ['Error 404']
        ] : $breadcrumb;
        $active_menu = empty($active_menu) ? [
            'l1' => 'error',
            'l2' => 'error-404',
            'l3' => null
        ] : $active_menu;
        return view('layouts.index_nodata')
            ->with('subject', $subject)
            ->with('title', $title)
            ->with('message', $message)
            ->with('breadcrumb', (object) $breadcrumb)
            ->with('activeMenu', (object) $active_menu);
    }

    public function filterHTML($html_string, $path_file = '', $name = 'content')
    {
        $html = $this->DOMProcessing($html_string, $path_file, 'img_' . $name);
        //$html = htmlspecialchars($html, ENT_QUOTES, "UTF-8");
        $h = $this->HTMLPurify($html);
        return trim($h);
    }

    private function DOMProcessing($html_string, $path_file = '', $img_class = 'img_content')
    {
        $path_file  = !empty($path_file) ? $path_file . '/' : '';

        $dom = new \DomDocument('1.0', 'UTF-8');

        @$dom->loadHtml($html_string, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

        $images = $dom->getElementsByTagName('img');
        foreach ($images as $k => $img) {
            $data = $img->getAttribute('src');
            if (strpos($data, 'base64') !== false) {
                list($type, $data) = explode(';', $data);
                list(, $data) = explode(',', $data);

                $image_name = $this->storeUploadedFile($path_file, base64_decode($data), 'img_', 'png');

                $img->removeAttribute('src');
                $img->setAttribute('src', asset($image_name->file_url));
                $img->setAttribute('class', $img_class);
            }
        }

        return $dom->saveHTML();
    }

    protected function storeUploadedFile($file_path, $file_data, $prefix_name = '', $file_extension = 'png')
    {
        $file_name = $prefix_name . Uuid::uuid4()->getHex() . '.' . strtolower($file_extension);
        $the_file  = $this->pathFile . $file_path . '/' . $file_name;
        Storage::disk('local')->put($the_file, $file_data);
        return (object) [
            'file_name' => $file_name,
            'file_url' => Storage::url($the_file),
            'file_dir' => $the_file,
            'file_ext' => strtolower($file_extension)
        ];
    }

    private function HTMLPurify($html)
    {
        $config = HTMLPurifier_Config::createDefault();
        $config->set('Filter.YouTube', true);
        //$config->set('CSS.AllowedProperties', 'margin-left');
        $config->set('HTML.AllowedElements', array(
            'iframe', 'img', 'a', 'h1', 'h2', 'h3', 'h4', 'h5', 'code', 'blockquote',
            'b', 'i', 'u', 'em', 'p', 'ul', 'ol', 'li', 'sup', 'sub', 'div', 'pre',
            'span', 'font', 'table', 'tbody', 'thead', 'tr', 'th', 'td'
        )); // <-- IMPORTANT
        $config->set('HTML.AllowedAttributes', array(
            'iframe@src',
            'iframe@class',
            'iframe@width',
            'iframe@height',
            'iframe@frameborder',
            'img@src',
            'a@href',
            'font@color',
            'table@class',
            'img@style'
        ));

        $config->set('HTML.DefinitionID', '1');
        $config->set('HTML.FlashAllowFullScreen', 'true');
        $config->set('HTML.SafeIframe', true);
        $config->set('HTML.SafeObject', 'true');
        $config->set('HTML.Trusted', true);
        $config->set('Output.FlashCompat', 'true');
        $config->set('URI.SafeIframeRegexp', '%^(https?:)?//(www\.youtube(?:-nocookie)?\.com/embed/|player\.vimeo\.com/video/)%'); //allow YouTube and Vimeo

        $purifier = new HTMLPurifier($config);
        return $purifier->purify($html);
    }


    /**
     * @description Cek status akses C-R-U-D user
     * @param string $action
     * @param string $action_response
     * @return void
     */
    protected function authAction(string $action = 'read', string $action_response = 'default'): void
    {
        $crud   = ['create' => 'c', 'read' => 'r', 'update' => 'u', 'delete' => 'd'];
        switch (strtolower($action)) {
            case 'create':
                $this->action = 'c';
                break;
            case 'update':
                $this->action = 'u';
                break;
            case 'delete':
                $this->action = 'd';
                break;
            case 'read':
            default:
                $this->action = 'r';
                break;
        };

        $this->actionResponse = $action_response;
    }

    /*
	 * Cek status akses C-R-U-D user
	 * Bernilai -> 	true: memiliki akses
	 * 				false: tidak memiliki akses
     * @param $action = c|r|u|d
     * @param $response_type = default|json|modal
     * @return bool|JsonResponse|never|string
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function authCheckDetailAccess()
    {
        $userAccess = session()->get('userAccess');
        if (!isset($userAccess[strtoupper($this->menuCode)][$this->action]) or $userAccess[strtoupper($this->menuCode)][$this->action] != 1) {
            /*return match (strtolower($this->actionResponse)) {
                'json' => response()->json(['stat' => 'error', 'mc' => false, 'msg' => 'Halaman yang diakses tidak tersedia.'], 404),
                'modal' => $this->showModalError('Error Akses', 'Terjadi Kesalahan', 'Halaman yang diakses tidak tersedia.')->render(),
                default => abort(401),
            };*/

            switch (strtolower($this->actionResponse)) {
                case 'json':
                    return response()->json(['stat' => 'error', 'mc' => false, 'msg' => 'Halaman yang diakses tidak tersedia.'], 404);
                    break;
                case 'modal':
                    return $this->showModalError('Error Akses', 'Terjadi Kesalahan', 'Halaman yang diakses tidak tersedia.')->render();
                    break;
                default:
                    return abort(401);
                    break;
            }
        }
        return true;
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function authAccessKey()
    {
        $access = (isset(session()->get('userAccess')[strtoupper($this->menuCode)]) ? session()->get('userAccess')[strtoupper($this->menuCode)] : ['c' => 0, 'r' => 0, 'u' => 0, 'd' => 0]);
        $crud   = ['c' => 'create', 'r' => 'read', 'u' => 'update', 'd' => 'delete'];
        $res    = [];
        foreach ($access as $k => $v) {
            $res[$crud[$k]] = (bool) $v;
        }
        return (object) $res;
    }
}
