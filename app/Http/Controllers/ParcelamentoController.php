<?php

namespace App\Http\Controllers;

use App\Models\Parcelamento;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;

class ParcelamentoController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->user()->id;

        $query = Parcelamento::where('user_id', $userId);

        if ($request->filled('cartao_id')) {
            $query->where('cartao_id', $request->integer('cartao_id'));
        }

        return response()->json($query->get());
    }

    public function store(Request $request)
    {
        $userId = $request->user()->id;
        $data = $request->validate([
            'cartao_id' => [
                'required',
                'integer',
                Rule::exists('cartoes', 'id')->where('user_id', $userId),
            ],
            'descricao' => ['required', 'string', 'max:255'],
            'valor_total' => ['required', 'numeric'],
            'numero_parcelas' => ['required', 'integer', 'min:1'],
            'parcela_atual' => ['nullable', 'integer', 'min:1'],
            'mes_inicio' => ['required', 'string', 'size:7'],
        ]);

        if (!isset($data['parcela_atual'])) {
            $data['parcela_atual'] = 1;
        }

        $data['user_id'] = $userId;

        $parcelamento = Parcelamento::create($data);

        return response()->json($parcelamento, 201);
    }

    public function show(Request $request, Parcelamento $parcelamento)
    {
        abort_if($parcelamento->user_id !== $request->user()->id, 403);

        return response()->json($parcelamento);
    }

    public function update(Request $request, Parcelamento $parcelamento)
    {
        abort_if($parcelamento->user_id !== $request->user()->id, 403);

        $userId = $request->user()->id;
        $data = $request->validate([
            'cartao_id' => [
                'sometimes',
                'integer',
                Rule::exists('cartoes', 'id')->where('user_id', $userId),
            ],
            'descricao' => ['sometimes', 'string', 'max:255'],
            'valor_total' => ['sometimes', 'numeric'],
            'numero_parcelas' => ['sometimes', 'integer', 'min:1'],
            'parcela_atual' => ['sometimes', 'integer', 'min:1'],
            'mes_inicio' => ['sometimes', 'string', 'size:7'],
        ]);

        $parcelamento->update($data);

        return response()->json($parcelamento);
    }

    public function destroy(Request $request, Parcelamento $parcelamento)
    {
        abort_if($parcelamento->user_id !== $request->user()->id, 403);

        $parcelamento->delete();

        return response()->noContent();
    }
}
