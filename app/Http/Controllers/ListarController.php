<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;

class ListarController extends Controller
{
    public function listar(){
        $cuentas = Account::all();
        return json_encode($cuentas);
    }
}
