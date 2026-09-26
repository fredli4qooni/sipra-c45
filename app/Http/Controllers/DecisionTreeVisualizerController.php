<?php

namespace App\Http\Controllers;

use App\Models\C45Model;
use App\Models\C45Rule;
use Illuminate\Http\Request;

class DecisionTreeVisualizerController extends Controller
{
    /**
     * Display Visual Interactive Decision Tree
     */
    public function showTree(Request $request, ?C45Model $c45 = null)
    {
        // If no model ID provided, use the active model or latest model
        if (!$c45 || !$c45->exists) {
            $c45 = C45Model::active()->first() ?? C45Model::latest()->first();
        }

        $allModels = C45Model::latest()->get();
        $treeArray = $c45 ? (json_decode($c45->tree_structure_json, true) ?? []) : [];

        return view('c45.tree', compact('c45', 'allModels', 'treeArray'));
    }

    /**
     * Display IF-THEN Rules Explorer
     */
    public function showRules(Request $request, ?C45Model $c45 = null)
    {
        if (!$c45 || !$c45->exists) {
            $c45 = C45Model::active()->first() ?? C45Model::latest()->first();
        }

        $allModels = C45Model::latest()->get();

        $rulesQuery = $c45 ? $c45->rules() : C45Rule::query();

        if ($decision = $request->input('decision')) {
            $rulesQuery->where('decision', $decision);
        }

        if ($search = $request->input('search')) {
            $rulesQuery->where('rule_text', 'like', "%{$search}%");
        }

        $rules = $rulesQuery->orderBy('id', 'asc')->paginate(15)->withQueryString();

        return view('c45.rules', compact('c45', 'allModels', 'rules'));
    }
}
