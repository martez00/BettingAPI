<?php

namespace App\Http\Controllers\Api\V1\Bet;

use App\BalanceTransaction;
use App\Bet;
use App\BetSelections;
use App\Http\Controllers\Controller;
use App\Http\Requests\BetsRequest;
use App\Http\Requests\StoreBetRequest;
use App\User;
use Illuminate\Http\Request;

class BetApiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(BetsRequest $request)
    {
        $query = Bet::select('*');

        $query->when(request()->has('order_by'), function ($q) {
            if(request()->has('order_by_keyword')) $orderByKeyword = request()->get('order_by_keyword');
            else $orderByKeyword = "ASC";
            $q->orderBy(request()->get('order_by'), $orderByKeyword);
        });

        $query->when(request()->has('limit'), function ($q) {
            $q->take(request()->get('limit'));
        });

        $query->when(request()->has('user_info'), function ($q) {
            if(request()->get('user_info') == "true"){
                $q->with('user');
            }
        });

        $query->when(request()->has('selection_info'), function ($q) {
            if(request()->get('selection_info') == "true"){
                $q->with('betSelections')->with('betSelections.selection', 'betSelections');
            }
        });

        $bets = $query->get();

        return response()->json(["data" => $bets], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreBetRequest $request)
    {
        $validatedBetData = $request->validated();
        sleep(1);

        $user = User::find($validatedBetData['user_id']);
        if ( ! $user) { //documentation says that if there are no user - then create it
            $user = factory(User::class)->create([
                'id' => $validatedBetData['user_id']
            ]);
        }

        $amountBefore = $user->balance;
        $user->balance = $user->balance - $validatedBetData['stake_amount'];

        $bet = new Bet();
        $bet->user_id = $user->id;
        $bet->stake_amount = $validatedBetData['stake_amount'];
        $bet->created_at = date("Y-m-d H:i:s");

        $user->save();
        $bet->save();

        foreach ($validatedBetData['selections'] as $selection) {
            $betSelection = new BetSelections();
            $betSelection->bet_id = $bet->id;
            $betSelection->selection_id = $selection['id'];
            $betSelection->odds = $selection['odds'];
            $betSelection->save();
        }

        $balanceTransactions = new BalanceTransaction();
        $balanceTransactions->amount = $user->balance;
        $balanceTransactions->amount_before = $amountBefore;
        $balanceTransactions->date_time = date("Y-m-d H:i:s");
        $balanceTransactions->user_id = $user->id;
        $balanceTransactions->save();

        session()->forget(['bet_requested']);
        session()->save();

        return response()->json(null, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $bet = Bet::findOrFail($id);
        return response()->json(["data" => $bet], 200);
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
