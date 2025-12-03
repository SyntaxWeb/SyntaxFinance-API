<?php

namespace App\Http\Controllers;

use App\Models\Cofrinho;
use Illuminate\Http\Request;

class CofrinhoController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->user()->id;

        return response()->json(Cofrinho::where('user_id', $userId)->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nome' => ['required', 'string', 'max:255'],
            'descricao' => ['nullable', 'string'],
            'saldo' => ['nullable', 'numeric'],
        ]);

        if (!isset($data['saldo'])) {
            $data['saldo'] = 0;
        }

        $data['user_id'] = $request->user()->id;

        $cofrinho = Cofrinho::create($data);

        return response()->json($cofrinho, 201);
    }

    public function show(Request $request, Cofrinho $cofrinho)
    {
        abort_if($cofrinho->user_id !== $request->user()->id, 403);

        return response()->json($cofrinho);
    }

    public function update(Request $request, Cofrinho $cofrinho)
    {
        abort_if($cofrinho->user_id !== $request->user()->id, 403);

        $data = $request->validate([
            'nome' => ['sometimes', 'string', 'max:255'],
            'descricao' => ['nullable', 'string'],
            'saldo' => ['sometimes', 'numeric'],
        ]);

        $cofrinho->update($data);

        return response()->json($cofrinho);
    }

    public function destroy(Request $request, Cofrinho $cofrinho)
    {
        abort_if($cofrinho->user_id !== $request->user()->id, 403);

        $cofrinho->delete();

        return response()->noContent();
    }
}
