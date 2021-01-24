<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EventController extends Controller
{
    public function store(Request $request){

        if($request->input('type') === 'deposit'){
            return $this->deposit(
                $request->input('destination'),
                $request->input('amount')
            );
        }else if($request->input('type') === 'withdraw'){
            return $this->withdraw(
                $request->input('origin'),
                $request->input('amount')
            );
        }else if($request->input('type') === 'transfer'){
            return $this->transfer(
                $request->input('origin'),
                $request->input('destination'),
                $request->input('amount'),
            );
        }
        //return $request->input('type');
    }

    private function deposit($destination,$amount){
        $account = Account::firstOrCreate([
            'id' => $destination
        ]);

        $account->balance += $amount;
        $account->save();

        return response()->json([
            'destination' => [
                'id' => $account->id,
                'balance' => $account->balance
            ]
            ],201);
    }

    private function withdraw($origin,$amount){
        $account = Account::findOrFail($origin);

        $account->balance -= $amount;
        $account->save();

        return response()->json([
            'origin' => [
                'id' => $account->id,
                'balance' => $account->balance
            ]
            ],201);
    }

    private function transfer($origin,$destination,$amount){
        
        $account_origen = Account::findOrFail($origin);
        $account_destino = Account::firstOrCreate([
            'id' => $destination
        ]);

        DB::transaction(function () use ($account_origen,$account_destino,$amount) {

            $account_origen->balance -= $amount;
            $account_destino->balance += $amount;

            $account_origen->save();
            $account_destino->save();
        });

        return response()->json([
            'origin' => [
                'id' => $account_origen->id,
                'balance' => $account_origen->balance
            ],
            'destination' => [
                'id' => $account_destino->id,
                'balance' => $account_destino->balance
            ]
            ],201);
        
    }


}
