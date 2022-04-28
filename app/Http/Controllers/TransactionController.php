<?php

namespace App\Http\Controllers;

use App\{Transaction, Account};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\TransactionPostRequest;
use Carbon\Carbon;

class TransactionController extends Controller
{
    public function responseHandler($msg, $status, $data=[]){
        return response()->json([
            'msg' => $msg,
            'data'=> $data
        ], $status);
    }

    public function deposit($account, $request)
    {
        $account->balance = $account->balance + $request->amount;
        $account->save();
        $trans = Transaction::create([
            'card_number'=> $request->card_number,
            'type'=> $request->type,
            'status'=> 'exitosa',
            'amount'=> $request->amount
        ]);
        return $this->responseHandler('Deposito Exitoso', 200, $trans );
    }

    public function checkTransactions($request){
        $now = Carbon::now();
        
        $transactions = Transaction::whereDate('created_at', $now)
                                    ->where('type', $request->type)
                                    ->where('card_number', $request->card_number)
                                    ->get();
        $total = 0;
        $noFounds = 0;
        foreach ($transactions as $key => $t) {
            $total = $t->amount + $total;
            if($t->observation === 'Saldo insuficiente'){
                $noFounds++;
            }
        }

        if($noFounds >= 3){
            return array('msg'=>'Demasiados Intentos sin Saldo', 'error'=>true);
        }
        
        if($total > 10000){
            return array('msg'=>'Ah superado el limite por hoy', 'error'=>true);
        }

        
        return array('msg'=>'', 'error'=>false);
    }

    public function retire($account, $request)
    {
        if($request->amount > $account->balance) {
                
            $trans = Transaction::create([
                'card_number'=> $request->card_number,
                'type'=> $request->type,
                'status'=> 'fallida',
                'amount'=> $request->amount,
                'observation'=> 'Saldo insuficiente',
            ]);
            return $this->responseHandler('Saldo insuficiente', 200, $trans );
        }else{
         
            $account->balance = $account->balance - $request->amount;
            $account->save();

            $trans = Transaction::create([
                'card_number'=> $request->card_number,
                'type'=> $request->type,
                'status'=> 'exitosa',
                'amount'=> $request->amount
            ]);

            return $this->responseHandler('Retiro Exitoso', 200, $trans );
        }
    }

    public function store(TransactionPostRequest $request)
    {
        $account = Account::where('card_number',$request->card_number)->first();
       
        if(!$account){
            return $this->responseHandler('Cuenta No Encontrada', 404 );
        }

        $checkTransaction = $this->checkTransactions($request);
        
        if($checkTransaction['error']){
            return $this->responseHandler($checkTransaction['msg'], 200 );
        }

        switch ($request->type) {
            case 'deposito':
                return $this->deposit($account, $request);
                break;
            case 'retiro':
                return $this->retire($account, $request);
                break;
        }

    }

    
}
