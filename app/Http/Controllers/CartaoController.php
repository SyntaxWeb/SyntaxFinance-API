<?php

namespace App\Http\Controllers;

use App\Models\Cartao;
use Illuminate\Http\Request;

class CartaoController extends Controller
{
    public function index()
    {
        $userId = request()->user()->id;

        return response()->json(Cartao::where('user_id', $userId)->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nome' => ['required', 'string', 'max:255'],
            'bandeira' => ['required', 'string', 'max:255'],
            'limite' => ['required', 'numeric'],
            'dia_fechamento' => ['required', 'integer', 'between:1,31'],
            'dia_vencimento' => ['required', 'integer', 'between:1,31'],
        ]);

        $data['user_id'] = $request->user()->id;

        $cartao = Cartao::create($data);

        return response()->json($cartao, 201);
    }

    public function show(Request $request, Cartao $cartao)
    {
        abort_if($cartao->user_id !== $request->user()->id, 403);

        return response()->json($cartao);
    }

    public function update(Request $request, Cartao $cartao)
    {
        abort_if($cartao->user_id !== $request->user()->id, 403);

        $data = $request->validate([
            'nome' => ['sometimes', 'string', 'max:255'],
            'bandeira' => ['sometimes', 'string', 'max:255'],
            'limite' => ['sometimes', 'numeric'],
            'dia_fechamento' => ['sometimes', 'integer', 'between:1,31'],
            'dia_vencimento' => ['sometimes', 'integer', 'between:1,31'],
        ]);

        $cartao->update($data);

        return response()->json($cartao);
    }

    public function destroy(Request $request, Cartao $cartao)
    {
        abort_if($cartao->user_id !== $request->user()->id, 403);

        $cartao->delete();

        return response()->noContent();
    }
}
