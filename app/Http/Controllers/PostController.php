<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{    
    /**
     * index
     *
     * @return void
     */
    public function index()
    {
        $posts = User::latest()->when(request()->search, function($posts) {
            $posts = $posts->where('name', 'like', '%'. request()->search . '%');
        })->paginate(5);

        return view('post.index', compact('posts'));
    }
    
    /**
     * create
     *
     * @return void
     */
    public function create()
    {
        return view('post.create');
    }
    
    /**
     * store
     *
     * @param  mixed $request
     * @return void
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'image'     => 'required|image|mimes:png,jpg,jpeg',
            'name'     => 'required',
            'nim'     => 'required',
            'no_hp'     => 'required',
            'content'   => 'required'
        ]);

        //upload image
        $image = $request->file('image');
        $image->storeAs('public/posts', $image->hashName());

        $post = User::create([
            'image'     => $image->hashName(),
            'name'   => $request->name,
            'nim'   => $request->nim,
            'no_hp'   => $request->no_hp,
            'content'   => $request->content
        ]);

        

        if($post){
            //redirect dengan pesan sukses
            return redirect()->route('post.index')->with(['success' => 'Data Berhasil Disimpan!']);
        }else{
            //redirect dengan pesan error
            return redirect()->route('post.index')->with(['error' => 'Data Gagal Disimpan!']);
        }

    }
    
    /**
     * edit
     *
     * @param  mixed $post
     * @return void
     */
    public function edit(User $post)
    {
        return view('post.edit', compact('post'));
    }
    
    /**
     * update
     *
     * @param  mixed $request
     * @param  mixed $post
     * @return void
     */
    public function update(Request $request, User $post)
    {
        $this->validate($request, [
            'image'     => 'required|image|mimes:png,jpg,jpeg',
            'name'     => 'required',
            'nim'     => 'required',
            'no_hp'     => 'required',
            'content'   => 'required'
        ]);

    
        //get data post by ID
        $post = User::findOrFail($post->id);
    
        if($request->file('image') == "") {
    
            $post->update([
            'name'   => $request->name,
            'nim'   => $request->nim,
            'no_hp'   => $request->no_hp,
            'content'   => $request->content
            ]);
    
        } else {
    
            //hapus old image
            Storage::disk('local')->delete('public/posts/'.$post->image);
    
            //upload new image
            $image = $request->file('image');
            $image->storeAs('public/posts', $image->hashName());
    
            $post->update([
                'image'     => $image->hashName(),
                'name'   => $request->name,
                'nim'   => $request->nim,
                'no_hp'   => $request->no_hp,
                'content'   => $request->content
            ]);
    
        }
    
        if($post){
            //redirect dengan pesan sukses
            return redirect()->route('post.index')->with(['success' => 'Data Berhasil Diupdate!']);
        }else{
            //redirect dengan pesan error
            return redirect()->route('post.index')->with(['error' => 'Data Gagal Diupdate!']);
        }
    }
    
    /**
     * destroy
     *
     * @param  mixed $id
     * @return void
     */
    public function destroy($id)
    {
        $post = User::findOrFail($id);
        Storage::disk('local')->delete('public/posts/'.$post->image);
        $post->delete();

        if($post){
            //redirect dengan pesan sukses
            return redirect()->route('post.index')->with(['success' => 'Data Berhasil Dihapus!']);
        }else{
            //redirect dengan pesan error
            return redirect()->route('post.index')->with(['error' => 'Data Gagal Dihapus!']);
        }
    }
}
