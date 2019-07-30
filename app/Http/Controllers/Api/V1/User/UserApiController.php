<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Requests\UsersByMonthRequest;
use App\Http\Requests\UsersRequest;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserApiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(UsersRequest $request)
    {
        $query = User::select('*');

        $query->when(request()->has('order_by'), function ($q) {
            if(request()->has('order_by_keyword')) $orderByKeyword = request()->get('order_by_keyword');
            else $orderByKeyword = "ASC";
            $q->orderBy(request()->get('order_by'), $orderByKeyword);
        });

        $query->when(request()->has('limit'), function ($q) {
            $q->take(request()->get('limit'));
        });

        $users = $query->get();

        return response()->json(["data" => $users], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
