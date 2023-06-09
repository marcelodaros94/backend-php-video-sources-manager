<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Link;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class VideoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $perPage=$request->input('perPage',3);
        $page=$request->input('page')-1; // Sino nunca tomará los primeros 3
        $searchTerm = $request->input('search');
        $rating=$request->input('rating',0);

        //this query works with or without search param
        $query = Video::with('links')
        ->where('rating', '>=', $rating)
        ->where(function ($query) use ($searchTerm) {
            $query->where('title', 'like', '%' . $searchTerm .'%')
                //it searches in the brand table fields too
                ->orWhereHas('brand', function ($query) use ($searchTerm) {
                    $query->where('name', 'like', '%'. $searchTerm. '%');
                });
        });
        
        $totalPages = ceil($query->count() / $perPage);
        
        //this applies the pagination
        $videos = $query
            ->skip($page * $perPage)
            ->take($perPage)
            ->get();
        
        return response()->json([
            'videos' => $videos,
            'currentPage' => $request->input('page'),
            'totalPages' => $totalPages,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try{
            DB::beginTransaction();

            $path=$request->file('image')->store('public/videos');
            $video=new Video();
            $video->title=$request->input('title');
            $video->description=$request->input('description');
            $video->type=$request->input('type');
            $video->image=Storage::url($path);
            $video->rating=$request->input('rating');
            $video->brand_id=$request->input('brand');
            $video->save();

            $links=json_decode($request->input('links'),true);
            foreach ($links as $item) {
                $link=new Link();
                $link->url=$item['url'];
                $link->video_id=$video->id;
                $link->save();
            }
            DB::commit();

            return response()->json([
                "id" => $video->id,
                "message" => "Registro creado exitosamente"
            ],201);
        }
        catch(Exception $e) {
            DB::rollback();
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
