<?php

namespace Tests\Feature\Bet;

use App\Bet;
use App\BetSelections;
use App\Selection;
use App\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class SaveBetTest extends TestCase
{
    use DatabaseTransactions;

    private $user;

    private function createNewUser($balance = 1000)
    {
        $user = factory(User::class)->create(
            ["balance" => $balance]
        );
        $this->user = $user;
    }
    /**
     * @test
     * @group save_bet
     *
     * @return void
     */
    public function can_not_save_bet_with_empty_data()
    {
        $response = $this->startJsonRequest()
            ->json('POST', 'api/V1/bet', [
            ]);
        $response->assertStatus(400);
        $errors = collect($response->json('errors'));

        $userError = $errors->where('message', 'User ID field is required')->first();
        $this->assertNotNull($userError);
        $this->assertEquals('0', $userError['code']);

        $amountError = $errors->where('message', 'Stake amount field is required')->first();
        $this->assertNotNull($amountError);
        $this->assertEquals('0', $amountError['code']);

        $betslipError = $errors->where('message', 'Minimum number of selections is 1')->first();
        $this->assertNotNull($betslipError);
        $this->assertEquals('4', $betslipError['code']);
    }

    /**
     * @test
     * @group save_bet
     *
     * @return void
     */
    public function can_not_save_bet_when_amount_is_more_than_max()
    {
        $this->createNewUser();

        $selections = factory(Selection::class, 2)->create([
            'odds' => 1.85
        ]);

        $response = $this->startJsonRequest()
            ->json('POST', 'api/V1/bet', [
                "user_id" => $this->user->id,
                "stake_amount" => 20000,
                "selections" => $selections->toArray()
            ]);
        $response->assertStatus(400);

        $maximumAmountError = collect($response->json('errors'))->where('code', '3')->first();
        $this->assertNotNull($maximumAmountError);

    }

    /**
     * @test
     * @group save_bet
     *
     * @return void
     */
    public function can_not_save_bet_when_amount_is_less_than_min()
    {
        $this->createNewUser();

        $selections = factory(Selection::class, 2)->create([
            'odds' => 1.85
        ]);

        $response = $this->startJsonRequest()
            ->json('POST', 'api/V1/bet', [
                "user_id" => $this->user->id,
                "stake_amount" => 0.2,
                "selections" => $selections->toArray()
            ]);
        $response->assertStatus(400);

        $minimumAmountError = collect($response->json('errors'))->where('code', '2')->first();
        $this->assertNotNull($minimumAmountError);

    }

    /**
     * @test
     * @group save_bet
     *
     * @return void
     */
    public function can_not_save_bet_when_selections_amount_is_more_than_max()
    {
        $this->createNewUser();

        $selections = factory(Selection::class, 20)->create([
            'odds' => 1.1
        ]);

        $response = $this->startJsonRequest()
            ->json('POST', 'api/V1/bet', [
                "user_id" => $this->user->id,
                "stake_amount" => 0.3,
                "selections" => $selections->toArray()
            ]);
        $response->assertStatus(201);

        $maximumSelectionsError = collect($response->json('errors'))->where('code', '5')->first();
        $this->assertNull($maximumSelectionsError);

        $selections = factory(Selection::class, 21)->create([
            'odds' => 1.1
        ]);

        $response = $this->startJsonRequest()
            ->json('POST', 'api/V1/bet', [
                "user_id" => 1,
                "stake_amount" => 0.3,
                "selections" => $selections->toArray()
            ]);
        $response->assertStatus(400);

        $maximumSelectionsError = collect($response->json('errors'))->where('code', '5')->first();
        $this->assertNotNull($maximumSelectionsError);
    }

    /**
     * @test
     * @group save_bet
     *
     * @return void
     */
    public function can_not_save_bet_when_selections_amount_is_less_than_min()
    {
        $this->createNewUser();

        $response = $this->startJsonRequest()
            ->json('POST', 'api/V1/bet', [
                "user_id" => $this->user->id,
                "stake_amount" => 0.3,
                "selections" => []
            ]);
        $response->assertStatus(400);

        $minimumSelectionsError = collect($response->json('errors'))->where('code', '4')->first();
        $this->assertNotNull($minimumSelectionsError);

        $selections = factory(Selection::class, 1)->create([
            'odds' => 1.1
        ]);

        $response = $this->startJsonRequest()
            ->json('POST', 'api/V1/bet', [
                "user_id" => 1,
                "stake_amount" => 0.3,
                "selections" => $selections->toArray()
            ]);
        $response->assertStatus(201);

        $minimumSelectionsError = collect($response->json('errors'))->where('code', '4')->first();
        $this->assertNull($minimumSelectionsError);
    }

    /**
     * @test
     * @group save_bet
     *
     * @return void
     */
    public function can_not_save_bet_when_there_are_same_selections()
    {
        $this->createNewUser();

        $selection = factory(Selection::class)->create([
            'odds' => 1.1
        ]);

        $response = $this->startJsonRequest()
            ->json('POST', 'api/V1/bet', [
                "user_id" => $this->user->id,
                "stake_amount" => 0.3,
                "selections" => [
                    $selection,
                    $selection
                ]
            ]);
        $response->assertStatus(400);

        $selectionsFromResponse = collect($response->json('selections'));
        $selectionsFromResponse->each(function ($selection) {
            $dublicateError = collect($selection['errors'])->where('code', 8)->first();
            $this->assertNotNull($dublicateError);
            $this->assertEquals('Duplicate selection found', $dublicateError['message']);
        });
    }

    /**
     * @test
     * @group save_bet
     *
     * @return void
     */
    public function can_not_save_bet_when_there_are_selection_with_odd_less_than_min()
    {
        $this->createNewUser();

        $selection = factory(Selection::class)->create([
            'odds' => 0.9
        ]);

        $response = $this->startJsonRequest()
            ->json('POST', 'api/V1/bet', [
                "user_id" => $this->user->id,
                "stake_amount" => 0.3,
                "selections" => [
                    $selection,
                ]
            ]);
        $response->assertStatus(400);

        $selectionsFromResponse = collect($response->json('selections'))->where('id', $selection->id)->first();
        $minimumOddsError = collect($selectionsFromResponse['errors'])->where('code', 6)->first();
        $this->assertNotNull($minimumOddsError);
        $this->assertEquals('Minimum odds are 1', $minimumOddsError['message']);

        $selection = factory(Selection::class)->create([
            'odds' => 1.1
        ]);

        $response = $this->startJsonRequest()
            ->json('POST', 'api/V1/bet', [
                "user_id" => 1,
                "stake_amount" => 0.3,
                "selections" => [
                    $selection,
                ]
            ]);
        $response->assertStatus(201);

        $selectionsFromResponse = collect($response->json('selections'))->where('id', $selection->id)->first();
        $minimumOddsError = collect($selectionsFromResponse['errors'])->where('code', 6)->first();
        $this->assertNull($minimumOddsError);
    }

    /**
     * @test
     * @group save_bet
     *
     * @return void
     */
    public function can_not_save_bet_when_there_are_selection_with_odd_more_than_max()
    {
        $this->createNewUser();

        $selection = factory(Selection::class)->create([
            'odds' => 10001
        ]);

        $response = $this->startJsonRequest()
            ->json('POST', 'api/V1/bet', [
                "user_id" => $this->user->id,
                "stake_amount" => 0.3,
                "selections" => [
                    $selection,
                ]
            ]);
        $response->assertStatus(400);

        $selectionsFromResponse = collect($response->json('selections'))->where('id', $selection->id)->first();
        $maximumOddsError = collect($selectionsFromResponse['errors'])->where('code', 7)->first();
        $this->assertNotNull($maximumOddsError);
        $this->assertEquals('Maximum odds are 10000', $maximumOddsError['message']);

        $selection = factory(Selection::class)->create([
            'odds' => 10000
        ]);

        $response = $this->startJsonRequest()
            ->json('POST', 'api/V1/bet', [
                "user_id" => 1,
                "stake_amount" => 0.3,
                "selections" => [
                    $selection,
                ]
            ]);
        $response->assertStatus(201);

        $selectionsFromResponse = collect($response->json('selections'))->where('id', $selection->id)->first();
        $maximumOddsError = collect($selectionsFromResponse['errors'])->where('code', 7)->first();
        $this->assertNull($maximumOddsError);
    }

    /**
     * @test
     * @group save_bet
     *
     * @return void
     */
    public function can_not_save_bet_when_win_amount_is_more_than_max()
    {
        $this->createNewUser();

        $selection = factory(Selection::class)->create([
            'odds' => 5000
        ]);

        $response = $this->startJsonRequest()
            ->json('POST', 'api/V1/bet', [
                "user_id" => $this->user->id,
                "stake_amount" => 3,
                "selections" => [
                    $selection,
                ]
            ]);
        $response->assertStatus(400);

        $maxAmountError = collect($response->json('errors'))->where('code', 9)->first();
        $this->assertNotNull($maxAmountError);
        $this->assertEquals('Maximum win amount is 10000', $maxAmountError['message']);
    }

    /**
     * @test
     * @group save_bet
     *
     * @return void
     */
    public function can_not_save_bet_when_balance_is_insufficient()
    {
        $this->createNewUser(1100);

        $selection = factory(Selection::class)->create([
            'odds' => 1.85
        ]);

        $response = $this->startJsonRequest()
            ->json('POST', 'api/V1/bet', [
                "user_id" => $this->user->id,
                "stake_amount" => 1200,
                "selections" => [
                    $selection,
                ]
            ]);
        $response->assertStatus(400);

        $errors = collect($response->json('errors'));
        $this->assertNotNull($errors);

        $balanceError = $errors->where('code', 11)->first();
        $this->assertNotNull($balanceError);
    }

    /**
     * @test
     * @group save_bet
     *
     * @return void
     */
    public function user_is_created_when_was_not_during_bet_save()
    {
        $lastUser = User::orderBy('id', 'desc')->first();
        $notTakenID = $lastUser->id + 1;

        $user = User::find($notTakenID);
        $this->assertNull($user);

        $selections = factory(Selection::class, 2)->create([
            'odds' => 1.89
        ]);

        $response = $this->startJsonRequest()
            ->json('POST', 'api/V1/bet', [
                "user_id" => $notTakenID,
                "stake_amount" => 5,
                "selections" => $selections->toArray()
            ]);
        $response->assertStatus(201);

        $user = User::find($notTakenID);
        $this->assertNotNull($user);
        $this->assertEquals($notTakenID, $user->id);
    }

    /**
     * @test
     * @group save_bet
     *
     * @return void
     */
    public function can_save_bet()
    {
        $this->createNewUser();

        $selections = factory(Selection::class, 2)->create([
            'odds' => 1.89
        ]);

        $response = $this->startJsonRequest()
            ->json('POST', 'api/V1/bet', [
                "user_id" => $this->user->id,
                "stake_amount" => 5,
                "selections" => $selections->toArray()
            ]);
        $response->assertStatus(201);

        $createdBet = Bet::where('user_id', $this->user->id)->where('stake_amount', 5)->orderBy('created_at', 'desc')->first();
        $this->assertNotNull($createdBet);
    }

    /**
     * @test
     * @group save_bet
     *
     * @return void
     */
    public function can_save_bet_and_bet_selections()
    {
        $this->createNewUser();

        $selections = factory(Selection::class, 2)->create([
            'odds' => 1.89
        ]);

        $response = $this->startJsonRequest()
            ->json('POST', 'api/V1/bet', [
                "user_id" => $this->user->id,
                "stake_amount" => 5,
                "selections" => $selections->toArray()
            ]);
        $response->assertStatus(201);

        $createdBet = Bet::where('user_id', $this->user->id)->where('stake_amount', 5)->orderBy('created_at', 'desc')->first();
        $this->assertNotNull($createdBet);

        $betSelections = BetSelections::where('bet_id', $createdBet->id)->get();
        $this->assertNotNull($betSelections);

        $selections->each(function ($selection) use ($betSelections) {
            $selectionExistInBetSelections = $betSelections->where('selection_id', $selection->id)->first();
            $this->assertNotNull($selectionExistInBetSelections);
        });
    }

    /**
     * @test
     * @group save_bet
     *
     * @return void
     */
    public function user_balance_is_changed_after_succesfully_bet()
    {
        $this->createNewUser();
        $balanceBeforeBet = $this->user->balance;

        $selections = factory(Selection::class, 2)->create([
            'odds' => 1.89
        ]);
        $stakeAmount = 20;

        $response = $this->startJsonRequest()
            ->json('POST', 'api/V1/bet', [
                "user_id" => $this->user->id,
                "stake_amount" => $stakeAmount,
                "selections" => $selections->toArray()
            ]);
        $response->assertStatus(201);

        $user = User::find($this->user->id);
        $balanceAfterBet = $user->balance;

        $this->assertNotEquals($balanceAfterBet, $balanceBeforeBet);
        $this->assertEquals($balanceBeforeBet, $balanceAfterBet + $stakeAmount);
    }

    /**
     * @test
     * @group save_bet
     *
     * @return void
     */
    public function balance_transactions_is_saved_after_succesfully_bet()
    {
        $this->createNewUser();
        $balanceBeforeBet = $this->user->balance;

        $selections = factory(Selection::class, 2)->create([
            'odds' => 1.89
        ]);
        $stakeAmount = 20;

        $response = $this->startJsonRequest()
            ->json('POST', 'api/V1/bet', [
                "user_id" => $this->user->id,
                "stake_amount" => $stakeAmount,
                "selections" => $selections->toArray()
            ]);
        $response->assertStatus(201);

        $user = User::find($this->user->id);
        $balanceAfterBet = $user->balance;

        $this->assertNotEquals($balanceAfterBet, $balanceBeforeBet);
        $this->assertEquals($balanceBeforeBet, $balanceAfterBet + $stakeAmount);

        $blanaceTransactions = DB::table("balance_transactions")
            ->select("*")
            ->where('amount', $balanceAfterBet)
            ->where('amount_before', $balanceBeforeBet)
            ->where(DB::raw('DATE_FORMAT(date_time, "%Y-%m-%d %H")'), date("Y-m-d H"))
            ->get()
            ->first();
        $this->assertNotNull($blanaceTransactions);
    }
}
