<?php

namespace App\Http\Controllers;

use App\Models\Renda;
use Illuminate\Http\Request;

class RendaController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->user()->id;

        $query = Renda::where('user_id', $userId);

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
            'origem' => ['required', 'string', 'max:255'],
            'data' => ['required', 'date'],
        ]);

        $data['user_id'] = $request->user()->id;

        $renda = Renda::create($data);

        return response()->json($renda, 201);
    }

    public function show(Request $request, Renda $renda)
    {
        abort_if($renda->user_id !== $request->user()->id, 403);

        return response()->json($renda);
    }

    public function update(Request $request, Renda $renda)
    {
        abort_if($renda->user_id !== $request->user()->id, 403);

        $data = $request->validate([
            'mes' => ['sometimes', 'string', 'size:7'],
            'valor' => ['sometimes', 'numeric'],
            'origem' => ['sometimes', 'string', 'max:255'],
            'data' => ['sometimes', 'date'],
        ]);

        $renda->update($data);

        return response()->json($renda);
    }

    public function destroy(Request $request, Renda $renda)
    {
        abort_if($renda->user_id !== $request->user()->id, 403);

        $renda->delete();

        return response()->noContent();
    }
}
