<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
            $time = time();
            $newName1 = $time.".svg";
            $newName2 = $time."-complete.svg";
            $request->file('icon1')->move('images/icons', $newName1);
            $request->file('icon2')->move('images/icons', $newName2);
            $json['path'] = 'images/icons/'.$newName1;
        } catch (Exception $e) {
            $json['path'] = 'Caught exception: '.$e->getMessage();
        }
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
            $completeIcon = str_replace(".svg","-complete.svg",$request->icon);
            if($request->icon && strpos($completeIcon, 'images/icons/') === 0) {
                unlink($completeIcon);
            }
            $json['icon'] = $request->icon;
        } catch (Exception $e) {
            $json['icon'] = 'Caught exception: '.$e->getMessage();
        }
        return json_encode($json);
    }

}
