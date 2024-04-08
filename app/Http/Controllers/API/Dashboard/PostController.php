<?php

namespace App\Http\Controllers\API\Dashboard;

use App\Models\Post;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $posts = Post::all();

        return new PostResource(true, 'List data post', $posts);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title'     => 'required',
            'image'     => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'text_1'   => 'required',
            'text_2'   => 'nullable',
            'text_3'   => 'nullable',
        ]);

        //check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //upload image
        $image = $request->file('image');
        $image->storeAs('public/posts', $image->hashName());

        //create post
        $post = Post::create([
            'image'     => $image->hashName(),
            'title'     => $request->title,
            'text_1'   => $request->text_1,
            'text_2'   => $request->text_2,
            'text_3'   => $request->text_3,
        ]);

        //return response
        return new PostResource(true, 'Data post berhasil ditambahkan!', $post);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $post = Post::find($id);

        return new PostResource(true, 'Detail Data Post!', $post);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'title'     => 'required',
            'text_1'   => 'required',
            'text_2'   => 'nullable',
            'text_3'   => 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $post = Post::find($id);

        if ($request->hasFile('image')) {

            $image = $request->file('image');
            $image->storeAs('public/posts', $image->hashName());

            Storage::delete('public/posts/'.basename($post->image));

            $post->update([
                'image'     => $image->hashName(),
                'title'     => $request->title,
                'text_1'   => $request->text_1,
                'text_2'   => $request->text_2,
                'text_3'   => $request->text_3,
            ]);

        } else {

            $post->update([
                'title'     => $request->title,
                'text_1'   => $request->text_1,
                'text_2'   => $request->text_2,
                'text_3'   => $request->text_3,
            ]);
        }

        return new PostResource(true, 'Data Post Berhasil Diubah!', $post);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $post = Post::find($id);

        Storage::delete('public/posts/'.basename($post->image));

        $post->delete();

        return new PostResource(true, 'Data Post Berhasil Dihapus!', null);
    }
}
