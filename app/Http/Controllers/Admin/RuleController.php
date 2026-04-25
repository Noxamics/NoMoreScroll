<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

/** @noinspection PhpUndefinedClassInspection */
class RuleController extends Controller
{
    /**
     * Convert string ID to MongoDB ObjectId
     * @param string $id
     * @return object
     */
    private function toObjectId($id)
    {
        // Suppress Intelephense warning - MongoDB BSON classes are available at runtime
        $objectIdClass = '\MongoDB\BSON\ObjectId';
        return new $objectIdClass($id);
    }

    public function index()
    {
        // Ambil langsung dari collection MongoDB dan convert ke object
        $rulesArray = DB::connection('mongodb')
            ->collection('recommendation_rules')
            ->get();

        // Convert array hasil MongoDB menjadi object dengan default values
        $rules = collect($rulesArray)->map(function ($item) {
            return (object) [
                '_id' => $item['_id'] ?? $item->_id ?? null,
                'name' => $item['name'] ?? $item->name ?? 'Untitled',
                'variable' => $item['variable'] ?? $item->variable ?? '',
                'operator' => $item['operator'] ?? $item->operator ?? '>',
                'value' => $item['value'] ?? $item->value ?? 0,
                'recommendation' => $item['recommendation'] ?? $item->recommendation ?? '',
                'priority' => $item['priority'] ?? $item->priority ?? 'medium',
                'is_active' => $item['is_active'] ?? $item->is_active ?? false,
                'applied_count' => $item['applied_count'] ?? $item->applied_count ?? 0,
                'created_at' => $item['created_at'] ?? $item->created_at ?? null,
            ];
        });

        return view('admin.rules', compact('rules'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'variable' => 'required|string',
            'operator' => 'required|in:<,>,<=,>=',
            'value' => 'required|numeric',
            'recommendation' => 'required|string',
            'priority' => 'required|in:high,medium,low',
        ]);

        $validated['is_active'] = true;
        $validated['applied_count'] = 0;
        $validated['created_at'] = now();

        DB::connection('mongodb')
            ->collection('recommendation_rules')
            ->insert($validated);

        return redirect()->route('admin.rules')->with('success', 'Rule berhasil ditambahkan');
    }

    public function toggle(Request $request, $id)
    {
        $objectId = $this->toObjectId($id);
        
        $rule = DB::connection('mongodb')
            ->collection('recommendation_rules')
            ->where('_id', $objectId)
            ->first();

        if ($rule) {
            DB::connection('mongodb')
                ->collection('recommendation_rules')
                ->where('_id', $objectId)
                ->update(['is_active' => !$rule->is_active]);
        }

        return back();
    }

    public function destroy($id)
    {
        $objectId = $this->toObjectId($id);
        
        DB::connection('mongodb')
            ->collection('recommendation_rules')
            ->where('_id', $objectId)
            ->delete();

        return redirect()->route('admin.rules')->with('success', 'Rule berhasil dihapus');
    }
}