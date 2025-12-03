<?php

namespace App\Http\Controllers;

use App\Models\Divida;
use Illuminate\Http\Request;

class DividaController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->user()->id;

        $query = Divida::where('user_id', $userId);

        if ($request->filled('mes')) {
            $query->where('mes', $request->string('mes'));
        }

        return response()->json($query->orderByDesc('data')->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'mes' => ['required', 'string', 'size:7'],
            'valor' => ['required', 'numeric'],
            'motivo' => ['required', 'string', 'max:255'],
            'categoria' => ['required', 'in:cartao,fixa,variavel,outro'],
            'data' => ['required', 'date'],
            'status' => ['required', 'in:paga,aberta'],
            'cartao_id' => ['nullable', 'integer', 'exists:cartoes,id'],
            'parcelamento_id' => ['nullable', 'integer', 'exists:parcelamentos,id'],
        ]);

        $data['user_id'] = $request->user()->id;

        $divida = Divida::create($data);

        return response()->json($divida, 201);
    }

    public function show(Request $request, Divida $divida)
    {
        abort_if($divida->user_id !== $request->user()->id, 403);

        return response()->json($divida);
    }

    public function update(Request $request, Divida $divida)
    {
        abort_if($divida->user_id !== $request->user()->id, 403);

        $data = $request->validate([
            'mes' => ['sometimes', 'string', 'size:7'],
            'valor' => ['sometimes', 'numeric'],
            'motivo' => ['sometimes', 'string', 'max:255'],
            'categoria' => ['sometimes', 'in:cartao,fixa,variavel,outro'],
            'data' => ['sometimes', 'date'],
            'status' => ['sometimes', 'in:paga,aberta'],
            'cartao_id' => ['nullable', 'integer', 'exists:cartoes,id'],
            'parcelamento_id' => ['nullable', 'integer', 'exists:parcelamentos,id'],
        ]);

        $divida->update($data);

        return response()->json($divida);
    }

    public function destroy(Request $request, Divida $divida)
    {
        abort_if($divida->user_id !== $request->user()->id, 403);

        $divida->delete();

        return response()->noContent();
    }
}
