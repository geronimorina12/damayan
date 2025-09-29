<?php

namespace App\Http\Controllers;

use App\Models\memberModel;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MemberControllerForCollector extends Controller
{
    public function index()
    {
        $members = memberModel::paginate(10);
        return Inertia::render('collector/dashboard/RegisteredMember', [
            'members' => $members,
        ]);
    }
}
