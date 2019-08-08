<?php

namespace App\Http\Controllers\Api\V1\Sport;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSportRequest;
use App\Http\Requests\UpdateSportRequest;
use App\Sport;
use Illuminate\Http\Request;

class SportApiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreSportRequest $request)
    {
        $validatedBetData = $request->validated();
        $sport = new Sport($validatedBetData);
        $sport->save();
        return response()->json([
            'message' => 'success',
            'data' => $sport
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $sport = Sport::findOrFail($id);
        return response()->json([
            'data' => $sport
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateSportRequest $request, $id)
    {
        $validatedBetData = $request->validated();
        $sport = Sport::findOrFail($id);
        $sport->fill($validatedBetData);
        $sport->save();
        return response()->json([
            'message' => 'success',
            'data' => $sport
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $sport = Sport::findOrFail($id);
        $sport->delete();
        return response()->json([
            'message' => 'success'
        ], 200);
    }
}
