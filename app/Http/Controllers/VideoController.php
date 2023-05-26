<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Link;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VideoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $videos=Video::all();
        return $videos;
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
            $video->image=$path;
            $video->rating=$request->input('rating');
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
