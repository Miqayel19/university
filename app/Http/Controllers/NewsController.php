<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\News;
use App\User;
class NewsController extends Controller
{

    public function index()
    {
        $news = News::with('user')->orderBy('id', 'DESC')->get();
        return view('admin.news.index',compact('news'));
    }

    public function create()
    {
        return view('admin.news.create');
    }

    public function store(Request $request)
    {
        $data = [
            'header' => $request->get('header'),
            'description' => $request->get('description'),
            'summary' => $request->get('summary'),
            'image' => $request->file('image')
        ];
        $rules = [
            'header' => 'required|min:5',
            'description' => 'required',
            'summary' => 'required',
            'image' => 'required|mimes:jpeg,png,jpg,gif,svg'
        ];

        $validator = Validator::make($data,$rules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }
        if($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time().$file->getClientOriginalName();
            $data['image'] = Image::make($request->file('image')->getRealPath());
            $data['image']->crop($request->get('w'), $request->get('h'), $request->get('x1'), $request->get('y1'));
            $path = ('images/'.$filename);
            $data['image']->save($path);
            $data['image']=$filename;
        }

        $news = News::create($data)->with('user')->get();
//        Auth::loginUsingId($user->id);
        return redirect()->to('/admin/user/news',compact('news'));

    }

    public function show($id)
    {
        $new = News::where('id', $id)->first();
        return view('admin.news.show',compact('new'));

    }




}
