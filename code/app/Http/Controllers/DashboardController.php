<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Models\Test;
use App\Models\Answer;
use App\Models\TestAttempt;
use App\Models\InterestField;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PHPUnit\TextUI\XmlConfiguration\IntroduceCacheDirectoryAttribute;

class DashboardController extends Controller
{
    /**
     * Show the appropriate dashboard based on user role.
     */
    public function index()
    {
        $user = Auth::user();

        $totalUsers = User::count();
        $totalTests = Test::count();
        $interestFields = InterestField::all();
        $mostChosenIntrestFields = DB::table('answer')
            ->join('test_attempt', 'answer.test_attempt_id', '=', 'test_attempt.test_attempt_id')
            ->join('question', 'answer.question_id', "=", "question.question_id")
            ->join('interest_field', 'question.interest_field_id', "=", "interest_field.interest_field_id")
            ->select(
                'interest_field.name as interest_field_name',
                DB::raw('COUNT(answer.answer_id) as total_chosen'))
            ->groupBy('interest_field.interest_field_id', 'interest_field.name')
            ->orderByDesc('total_chosen')
            ->get();

        if ($user->isSuperAdmin()) {
            return view('roles.superadmin.dashboard');
        } elseif ($user->isAdmin()) {
            return view('roles.admin.dashboard');
        } elseif ($user->isMentor()) {
            return view('roles.mentor.dashboard');
        } elseif ($user->isClient()) {
            return view('roles.client.dashboard');
        } elseif($user->isResearcher()){
            return view('roles.researcher.dashboard', compact('totalUsers', 'totalTests', 'interestFields', 'mostChosenIntrestFields'));
        }

        // Fallback for users without roles
        // NONE
    }
}
