<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Response;
use App\Models\blog;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $blogs = blog::get();

        foreach($blogs as $blog){
            $blog->tags = implode(', ', json_decode($blog->tags));
        }

        return view('home', compact('blogs'));
    }

    public function create(Request $request)
    {
        $this->validate($request, [
            'title' => 'required',
            'description' => 'required',
            'tags' => 'required',
            'image' => 'required|max:100',
        ]);

        $imageName = '';

        if($request->hasFile('image')){
            $image = $request->file('image');

            $path = public_path('blog_image');
            $ext = $image->getClientOriginalExtension();
            $imageName = time() . '.' . $ext;

            $image->move($path, $imageName);
        }
        
        $tags = explode(',', $request->tags);

        $blog = blog::create([
            'title' => $request->title,
            'description' => $request->description,
            'tags' => json_encode($tags),
            'image' => $imageName,
        ]);


        return Response::json(['status' => 'success', 'message' => 'Blog saved successfully.']);
    }

    public function getBlog($id)
    {
        $blog = blog::whereId($id)->first();
        $blog->tags = json_decode($blog->tags);

        return Response::json(['blog' => $blog]);
    }

    public function update(Request $request, $id)
    {
        $imageName = '';

        if($request->hasFile('image')){
            $image = $request->file('image');

            $path = public_path('blog_image');
            $ext = $image->getClientOriginalExtension();
            $imageName = time() . '.' . $ext;

            $image->move($path, $imageName);
        }else{
            $imageName = $request->old_image;
        }

        $blog = blog::whereId($id)->update([
            'title' => $request->title,
            'description' => $request->description,
            'tags' => json_encode($request->tags),
            'image' => $imageName,
        ]);


        return Response::json(['status' => 'success', 'message' => 'Blog updated successfully.']);
    }

    public function delete($id)
    {
        $blog = blog::whereId($id)->delete();

        return Response::json(['status' => 'success', 'message' => 'Blog deleted successfully.']);
    }
}
