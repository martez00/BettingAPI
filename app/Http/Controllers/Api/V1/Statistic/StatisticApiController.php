<?php

namespace App\Http\Controllers\Api\V1\Statistic;

use App\BalanceTransaction;
use App\Bet;
use App\Http\Controllers\Controller;
use App\Http\Requests\StatisticsByMonthRequest;
use App\User;
use Illuminate\Http\Request;

class StatisticApiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function byMonth(StatisticsByMonthRequest $request)
    {
        $validatedRequestData = $request->validated();

        $validatedRequestData['quantity']--;
        if ( ! isset($validatedRequestData['last_timestamp'])) {
            $now = date("Y-m-d H:i:s");
            $validatedRequestData['last_timestamp'] = $now;
        }

        $lastTimestampDate = date("Y-m-01", strtotime($validatedRequestData['last_timestamp']));
        $firstTimestampDate = date("Y-m-d",
            strtotime($lastTimestampDate . " - " . $validatedRequestData['quantity'] . " months"));

        switch ($validatedRequestData['table']) {
            case "bets":
                $valuesByMonth = Bet::groupBy('month')
                    ->selectRaw('DATE_FORMAT(bets.created_at, "%b %Y") AS month, count(*) as total')
                    ->where('bets.created_at', '>=', $firstTimestampDate)
                    ->where('bets.created_at', '<=', $validatedRequestData['last_timestamp'])
                    ->get();
                break;
            case "users":
                $valuesByMonth = User::groupBy('month')
                    ->selectRaw('DATE_FORMAT(users.created_at, "%b %Y") AS month, count(*) as total')
                    ->where('users.created_at', '>=', $firstTimestampDate)
                    ->where('users.created_at', '<=', $validatedRequestData['last_timestamp'])
                    ->get();
                break;
            case "balance_transactions":
                $valuesByMonth = BalanceTransaction::groupBy('month')
                    ->selectRaw('DATE_FORMAT(balance_transactions.date_time, "%b %Y") AS month, count(*) as total')
                    ->where('balance_transactions.date_time', '>=', $firstTimestampDate)
                    ->where('balance_transactions.date_time', '<=', $validatedRequestData['last_timestamp'])
                    ->get();
                break;
        }

        $total = 0;
        while ($validatedRequestData['quantity'] >= 0) {
            $monthValues = $valuesByMonth->where('month',
                date("M Y",
                    strtotime($lastTimestampDate . " - " . $validatedRequestData['quantity'] . " months")))->first();
            if ( ! $monthValues) {
                $valuesByMonth->push([
                    'month' => date("M Y",
                        strtotime($lastTimestampDate . " - " . $validatedRequestData['quantity'] . " months")),
                    'total' => 0
                ]);
            } else {
                $total += $monthValues['total'];
            }
            $validatedRequestData['quantity']--;
        }
        $sortedValuesByMonth = $valuesByMonth->sortBy(function ($obj, $key) {
            return date("Y-m", strtotime($obj['month']));
        })->values()->all();

        return response()->json([
            "data" => [
                'values_by_month' => $sortedValuesByMonth,
                'total' => $total
            ]
        ], 200);
    }
}
