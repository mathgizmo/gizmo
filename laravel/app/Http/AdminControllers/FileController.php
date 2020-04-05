<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FileController extends Controller
{
    public function uploadTopicIcon(Request $request)
    {
        $json = array();
        try {
            $time = time();
            $new_name = $time.".svg";
            $new_name_complete = $time."-gold.svg";
            $request->file('icon')->move('images/icons', $new_name);
            //$request->file('icon')->move('images/icons', $new_name_complete);
            $json['path'] = 'images/icons/'.$new_name;
        } catch (Exception $e) {
            $json['path'] = 'Caught exception: '.$e->getMessage();
        }
        return json_encode($json);
    }

    public function deleteTopicIcon(Request $request)
    {
        $json = array();
        try {
            if($request->icon) {
                if(strpos($request->icon, 'images/icons/') === 0) {
                   unlink($request->icon); 
                }
                /*$complete_icon = str_replace(".svg","-gold.svg",$request->icon);
                if(strpos($complete_icon, 'images/icons/') === 0) {
                    unlink($complete_icon);
                }*/
            } 
            $json['icon'] = $request->icon;
        } catch (Exception $e) {
            $json['icon'] = 'Caught exception: '.$e->getMessage();
        }
        return json_encode($json);
    }

}
