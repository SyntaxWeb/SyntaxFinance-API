<?php

namespace App\Http\Controllers;

use App\Models\Divida;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

class DividaController extends Controller
{
    public function storeRecorrentes(Request $request)
    {
        $userId = $request->user()->id;
        $data = $request->validate([
            'mes_inicio' => ['required', 'string', 'size:7'],
            'numero_meses' => ['required', 'integer', 'min:1'],
            'valor' => ['required', 'numeric'],
            'motivo' => ['required', 'string', 'max:255'],
            'data' => ['required', 'date'],
            'status' => ['nullable', 'in:paga,aberta'],
            'cartao_id' => ['prohibited'],
            'parcelamento_id' => ['prohibited'],
        ]);

        $startMonth = Carbon::createFromFormat('Y-m', $data['mes_inicio'])->startOfMonth();
        $baseDay = Carbon::parse($data['data'])->day;
        $numeroMeses = (int) $data['numero_meses'];
        $status = $data['status'] ?? 'aberta';

        $dividas = [];
        for ($i = 0; $i < $numeroMeses; $i++) {
            $date = $startMonth->copy()->addMonthsNoOverflow($i);
            $day = min($baseDay, $date->daysInMonth);
            $date = $date->day($day);

            $dividas[] = Divida::create([
                'user_id' => $userId,
                'mes' => $date->format('Y-m'),
                'valor' => $data['valor'],
                'motivo' => $data['motivo'],
                'categoria' => 'fixa',
                'data' => $date->toDateString(),
                'status' => $status,
            ]);
        }

        return response()->json($dividas, 201);
    }

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
        $userId = $request->user()->id;
        $data = $request->validate([
            'mes' => ['required', 'string', 'size:7'],
            'valor' => ['required', 'numeric'],
            'motivo' => ['required', 'string', 'max:255'],
            'categoria' => ['required', 'in:cartao,fixa,variavel,outro'],
            'data' => ['required', 'date'],
            'status' => ['required', 'in:paga,aberta'],
            'cartao_id' => [
                'nullable',
                'integer',
                Rule::exists('cartoes', 'id')->where('user_id', $userId),
            ],
            'parcelamento_id' => [
                'nullable',
                'integer',
                Rule::exists('parcelamentos', 'id')->where('user_id', $userId),
            ],
        ]);

        $data['user_id'] = $userId;

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

        $userId = $request->user()->id;
        $data = $request->validate([
            'mes' => ['sometimes', 'string', 'size:7'],
            'valor' => ['sometimes', 'numeric'],
            'motivo' => ['sometimes', 'string', 'max:255'],
            'categoria' => ['sometimes', 'in:cartao,fixa,variavel,outro'],
            'data' => ['sometimes', 'date'],
            'status' => ['sometimes', 'in:paga,aberta'],
            'cartao_id' => [
                'nullable',
                'integer',
                Rule::exists('cartoes', 'id')->where('user_id', $userId),
            ],
            'parcelamento_id' => [
                'nullable',
                'integer',
                Rule::exists('parcelamentos', 'id')->where('user_id', $userId),
            ],
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
