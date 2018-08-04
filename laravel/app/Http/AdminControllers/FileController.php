<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    /**
     * Upload topic icon 
     *
     * @param  Request  $request
     * @return Response
     */
    public function uploadTopicIcon(Request $request)
    {
        $json = array();
        try {
            $request->file('icon');
            $newName = time().".svg";
            $request->file('icon')->move('images/icons', $newName);
            $json['path'] = 'images/icons/'.$newName;
        } catch (Exception $e) {
            $json['path'] = 'Caught exception: '.$e->getMessage();
        }
        //$json['link'] = url('/').'/'.$json['path'];
        return json_encode($json);
    }

    /**
     * Delete topic icon 
     *
     * @param  Request  $request
     * @return Response
     */
    public function deleteTopicIcon(Request $request)
    {
        $json = array();
        try {
            if($request->icon && strpos($request->icon, 'images/icons/') === 0) {
                unlink($request->icon);
            }
            $json['icon'] = $request->icon;
        } catch (Exception $e) {
            $json['icon'] = 'Caught exception: '.$e->getMessage();
        }
        return json_encode($json);
    }

}
