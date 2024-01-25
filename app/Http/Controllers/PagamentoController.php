<?php

namespace App\Http\Controllers;

use App\Http\Requests\PagamentoFormRequest;
use App\Models\Pagamento;
use Illuminate\Http\Request;

class PagamentoController extends Controller
{
    public function pagamento(PagamentoFormRequest $request){
        $pagamento = Pagamento::create([
            'nome' => $request->nome,
            'taxa' => $request->taxa,
            'condicao' => $request->condicao,
        ]);
        return response()->json([
            "status" => true,
            "message" => "Pagamento registrado",
            "data"=> $pagamento
        ]);
    }
}
