<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Articles;

class ArticleController extends Controller
{
    public function index()
    {
        $articles = Articles::all();
        
        $articles = $articles->map(function ($article) {
            $article->urlToImage = url($article->urlToImage);
            
            return $article;
        });
        if ($articles->count() > 0) {
            $data = [
                'status' => 200,
                'articles' => $articles
            ];
            return response()->json($data, 200);
        } else {
            $data = [
                'status' => 404,
                'message' => 'Article not found'
            ];
            return response()->json($data, 404);
        }

    }

    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'category' => 'required|string|max:255',
                'author' => 'nullable|string|max:255',
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'url' => 'nullable|string',
                'urlToImage' => 'nullable|mimes:jpg,png,jpeg,gif',
                'publishedAt' => 'nullable|date|max:255',
                'content' => 'nullable|string'
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->messages()
            ], 422);
        } else {

            $imageName = null;
            if ($request->hasFile('urlToImage')) {
                $img = $request->file('urlToImage');
                $ext = $img->getClientOriginalExtension();
                $imageName = time() . '.' . $ext;
                $img->move(public_path('uploads'), $imageName);
                $imageName = 'uploads/' . $imageName;
            }
            $articles = Articles::create([
                'category' => $request->category,
                'author' => $request->author,
                'title' => $request->title,
                'description' => $request->description,
                'url' => $request->url,
                'urlToImage' => $imageName,
                'publishedAt' => $request->publishedAt,
                'content' => $request->content,
            ]);

            if ($articles) {
                return response()->json([
                    'status' => 200,
                    'message' => 'Article Added Successfully'
                ], 200);
            } else {
                return response()->json([
                    'status' => 500,
                    'message' => 'Something went wrong'
                ], 500);
            }
        }


    }

    public function show($id)
    {
        $articles = Articles::find($id);
        $articles->urlToImage = url($articles->urlToImage);
        if ($articles) {
            return response()->json([
                "status" => 200,
                "articles" => $articles
            ], 200);
        } else {
            return response()->json([
                "status" => 404,
                "message" => "Article Not Found"
            ], 404);
        }

    }

    public function edit($id)
    {
        $articles = Articles::find($id);
        $articles->urlToImage = url($articles->urlToImage);
        if ($articles) {
            return response()->json([
                "status" => 200,
                "articles" => $articles
            ], 200);
        } else {
            return response()->json([
                "status" => 404,
                "message" => "Article Not Found"
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'category' => 'required|string|max:255',
                'author' => 'nullable|string|max:255',
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'url' => 'nullable|string',
                'urlToImage' => 'nullable|mimes:jpg,png,jpeg,gif',
                'publishedAt' => 'nullable|date|max:255',
                'content' => 'nullable|string'
            ]

        );
        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->messages()
            ], 422);
        } else {
            $articles = Articles::find($id);
            if ($articles) {
                $imageName = $articles->urlToImage;
                if ($request->hasFile('urlToImage')) {
                    // Delete old image if exists
                    if ($imageName && file_exists(public_path('uploads/' . $imageName))) {
                        unlink(public_path('uploads/' . $imageName));
                    }
                    $img = $request->file('urlToImage');
                    $ext = $img->getClientOriginalExtension();
                    $imageName = time() . '.' . $ext;
                    $img->move(public_path('uploads/'), $imageName);
                    $imageName = url('uploads/' . $imageName);

                }

                $articles->update([
                    'category' => $request->category,
                    'author' => $request->author,
                    'title' => $request->title,
                    'description' => $request->description,
                    'url' => $request->url,
                    'urlToImage' => $imageName,
                    'publishedAt' => $request->publishedAt,
                    'content' => $request->content,
                ]);

                return response()->json([
                    'status' => 200,
                    'message' => 'Article Update Successfully'
                ], 200);
            } else {
                return response()->json([
                    'status' => 404,
                    'message' => 'Article Not Found'
                ], 404);
            }
        }
    }

    public function delete($id)
    {
        $articles = Articles::find($id);
        $articles->urlToImage = url($articles->urlToImage);
        if ($articles) {
            if ($articles->urlToImage && file_exists(public_path('uploads/' . $articles->urlToImage))) {
                unlink(public_path('uploads/' . $articles->urlToImage));
            }
            $articles->delete();
            return response()->json([
                "status" => 200,
                "message" => "Article Deleted Successfully"
            ], 200);
        } else {
            return response()->json([
                "status" => 404,
                "message" => "Article Not Found"
            ], 404);
        }
    }
}
