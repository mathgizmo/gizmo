<?php

namespace App\Http\AdminControllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Tag;

class TagController extends Controller
{
    public function __construct()
    {
        // $this->authorizeResource(Faq::class); // not working!
    }

    public function index(Request $request)
    {
        $this->checkAccess(auth()->user()->isSuperAdmin() || auth()->user()->isAdmin());
        $query = Tag::query();
        if ($request['id']) {
            $query->where('id', $request['id']);
        }
        if ($request['order_no']) {
            $query->where('order_no', $request['order_no']);
        }
        if ($request['name']) {
            $query->where('name', 'LIKE', '%'.$request['name'].'%');
        }
        if ($request['sort'] && $request['order']) {
            $query->orderBy($request['sort'], $request['order']);
        } else {
            $query->orderBy('order_no', 'ASC');
        }
        return view('tag.index', [
            'tags' => $query->get()
        ]);
    }

    public function create()
    {
        $this->checkAccess(auth()->user()->isSuperAdmin() || auth()->user()->isAdmin());
        $tags = Tag::all();
        return view('tag.create', array(
            'tag' => $tags,
            'total_tags' => $tags->count()
        ));
    }

    public function store(Request $request)
    {
        $this->checkAccess(auth()->user()->isSuperAdmin() || auth()->user()->isAdmin());
        $this->validate($request, [
            'name'=> 'required',
         ]);
         DB::table('tag')->insert([
             'name' => $request['name'],
             'order_no' => $request['order_no']
        ]);
        return redirect('/tags')->with(array('message'=> 'Created successfully'));
    }

    public function show()
    {
        return "Under Construction";
    }

    public function edit($id)
    {
        $this->checkAccess(auth()->user()->isSuperAdmin() || auth()->user()->isAdmin());
        return view('tag.edit', [
            'tag' => Tag::find($id),
            'total_tags'=> Tag::all()->count(),
        ]);
    }

    public function update(Request $request, $id)
    {
        $this->checkAccess(auth()->user()->isSuperAdmin() || auth()->user()->isAdmin());
        $this->validate($request, [
            'name'  => 'required',
        ]);
        DB::table('tag')->where('id', $id)->update([
            'name' => $request['name'],
            'order_no' => $request['order_no']
        ]);
        return redirect('/tags')->with(array('message'=> 'Updated successfully'));
    }

    public function destroy($id)
    {
        $this->checkAccess(auth()->user()->isSuperAdmin() || auth()->user()->isAdmin());
        $tag = Tag::findOrFail($id);
        if ($tag->units()->count() > 0) {
            return redirect('/tags')->withErrors(['message' => 'Cannot delete tag: it is assigned to one or more units.']);
        }
        $tag->delete();
        return redirect('/tags')->with(['message'=> 'Deleted successfully']);
    }
}
