<?php

namespace App\Http\APIControllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TopicController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return array
     */
    public function index()
    {
        $mode = DB::connection()->getFetchMode();
        DB::connection()->setFetchMode(\PDO::FETCH_ASSOC);
        $response =[];
        $levels = [];
        $units = [];
        foreach (DB::select('select * from level order by id asc') as $level) {
            $level['units'] = [];
            $levels[$level['id']] = count($response);
            $response[] = $level;
        }
        foreach (DB::select('select * from unit order by id asc') as $unit) {
            $unit['topics'] = [];
            $l_element_id = $levels[$unit['level_id']];
            $units[$unit['id']] = array(count($response[$l_element_id]['units']), $l_element_id);
            $response[$l_element_id]['units'][] = $unit;
        }
        foreach (DB::select('select * from topic order by id asc') as $topic) {
            list($u_element_id, $l_element_id) = $units[$topic['unit_id']];
            $topic['order_id'] = floor(count($response[$l_element_id]['units'][$u_element_id]['topics'])/2);
            $response[$l_element_id]['units'][$u_element_id]['topics'][] = $topic;
        }
        DB::connection()->setFetchMode($mode);
        return $this->success($response);
    }
}
