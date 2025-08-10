<?php

namespace App\Http\APIControllers;

use App\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{
    public function index(Request $request)
    {
        $tags = Tag::orderBy('order_no')->get(['id','name','order_no']);
        return $this->success($tags);
    }
}
