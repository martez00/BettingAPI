<?php

namespace Tests\Feature\Bet;

use App\Selection;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class SaveBetTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @test
     * @group save_bet
     *
     * @return void
     */
    public function empty_bet_request_returns_validation_errors_test()
    {
        $response = $this->startJsonRequest()
            ->json('POST', 'api/V1/bet', [
            ]);
        $response->assertStatus(400);
        $errors = collect($response->json('errors'));

        $userError = $errors->where('message', 'User ID field is required!')->first();
        $this->assertNotNull($userError);
        $this->assertEquals('0', $userError['code']);

        $amountError = $errors->where('message', 'Stake amount field is required!')->first();
        $this->assertNotNull($amountError);
        $this->assertEquals('0', $amountError['code']);

        $betslipError = $errors->where('message', 'Minimum number of selections is 1!')->first();
        $this->assertNotNull($betslipError);
        $this->assertEquals('4', $betslipError['code']);
    }

    /**
     * @test
     * @group save_bet
     *
     * @return void
     */
    public function maximum_stake_amount_validation_errors_test()
    {
        $selections = factory(Selection::class, 2)->create([
            'odds' => 1.85
        ]);

        $response = $this->startJsonRequest()
            ->json('POST', 'api/V1/bet', [
                "user_id" => 1,
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
    public function minimum_stake_amount_validation_errors_test()
    {
        $selections = factory(Selection::class, 2)->create([
            'odds' => 1.85
        ]);

        $response = $this->startJsonRequest()
            ->json('POST', 'api/V1/bet', [
                "user_id" => 1,
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
    public function maximum_selections_validation_errors_test()
    {
        $selections = factory(Selection::class, 20)->create([
            'odds' => 1.1
        ]);

        $response = $this->startJsonRequest()
            ->json('POST', 'api/V1/bet', [
                "user_id" => 1,
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
    public function minimum_selections_validation_errors_test()
    {
        $response = $this->startJsonRequest()
            ->json('POST', 'api/V1/bet', [
                "user_id" => 1,
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
    public function bet_on_same_selection_validation_errors_test()
    {
        $selection = factory(Selection::class)->create([
            'odds' => 1.1
        ]);

        $response = $this->startJsonRequest()
            ->json('POST', 'api/V1/bet', [
                "user_id" => 1,
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
    public function minimum_odds_validation_errors_test()
    {
        $selection = factory(Selection::class)->create([
            'odds' => 0.9
        ]);

        $response = $this->startJsonRequest()
            ->json('POST', 'api/V1/bet', [
                "user_id" => 1,
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
    public function maximum_odds_validation_errors_test()
    {
        $selection = factory(Selection::class)->create([
            'odds' => 10001
        ]);

        $response = $this->startJsonRequest()
            ->json('POST', 'api/V1/bet', [
                "user_id" => 1,
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
    public function maximum_win_amount_validation_error_test()
    {
        $selection = factory(Selection::class)->create([
            'odds' => 5000
        ]);

        $response = $this->startJsonRequest()
            ->json('POST', 'api/V1/bet', [
                "user_id" => 1,
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
}
