<?php

namespace App\Http\Controllers;

use App\Models\AssistanceDistribution;
use App\Models\ContributionModel;
use App\Models\memberModel;
use App\Models\OfficialModel;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(){
      if (!Auth::check()) {
        return redirect()->route('login');
      }

      // Carbon = library san laravel para sa date and time manipulation
      $month = Carbon::now()->month;
      $year = Carbon::now()->year;

      //total san collected
      $totalCollected = ContributionModel::whereMonth('payment_date', $month)
    ->whereYear('payment_date', $year)
    ->sum('amount');

    // Total Disbursed This Month
    $totalDisbursed = ContributionModel::whereMonth('payment_date', $month)
    ->whereYear('payment_date', $year)
    ->sum('amount');

    // Balance (mag kakaiba ang table pero same cra member san damayan)
    // why? may conflict kc sa data san members pag abot sa database
    $membersCount = memberModel::count(); //Get the count of all members
    $usersCount = User::count(); //Get the count of all users
    $officialsCount = OfficialModel::count(); //Get the count of all officials

    $monthBalance = $membersCount + $usersCount + $officialsCount; // Eh plus lang intiro na bilang
    $monthBalance = $monthBalance * 100; //Tsaka eh times sa 100 kay tig 100 ang contribution san members

    $currentMonthData = [
      'totalCollected' => $totalCollected,
      'totalDisbursed' => $totalDisbursed,
      'balance' => $monthBalance - $totalCollected, //Lastly eh minus yung Month balance sa total collected.
    ];

    ////////////////////////////
    // Total for entire year
    $currentYear = Carbon::now()->year;

    $yearCollected = ContributionModel::whereYear('payment_date', $currentYear)
        ->sum('amount');

    $yearDisbursed = ContributionModel::whereYear('payment_date', $currentYear)
        ->sum('amount');

        // Same logic sa month balance
        $membersCount = memberModel::count();
      $usersCount = User::count(); 
      $officialsCount = OfficialModel::count(); 

      $yearBalance = $membersCount + $usersCount + $officialsCount; 
      $yearBalance = $yearBalance * 100; 

      $yearData = [
      'yearCollected' => $yearCollected,
      'yearDisbursed' => $yearDisbursed,
      'yearBalance' => $yearBalance - $yearCollected
    ];
    ///////////////////////////

    /////////////////////////
    //Para sa monthly overview
      $monthlyData = ContributionModel::selectRaw('MONTH(payment_date) as month, SUM(amount) as total_collected')
      ->whereYear('payment_date', $currentYear)
      ->groupByRaw('MONTH(payment_date)')
      ->get()
      ->keyBy('month'); //total contributions collected for each month in the current year

      $monthlyDisbursement = AssistanceDistribution::selectRaw('MONTH(distribution_date) as month, SUM(total_amount) as total_disbursed')
          ->whereYear('distribution_date', $currentYear)
          ->groupByRaw('MONTH(distribution_date)')
          ->get()
          ->keyBy('month');
      
        $monthlyOverview = [
        'monthlyData' => $monthlyData,
        'monthlyDisbursement' => $monthlyDisbursement,
      ];
    ////////////////////////

    // redirect sa specific na dashboard depende sa role 
      if(Auth::user()->role === 'admin') {
        return Inertia::render('admin/dashboard/Home', [
          'currentMonthData' => $currentMonthData,
          'yearData' => $yearData,
          'monthlyOverview' => $monthlyOverview,
        ]);
      }else if(Auth::user()->role === 'collector') {
        return Inertia::render('collector/dashboard/Home', [
          'currentMonthData' => $currentMonthData,
          'yearData' => $yearData,
          'monthlyOverview' => $monthlyOverview,
        ]);
      }
      
    }
    public function registeredMember(){
     $members = memberModel::orderBy('first_name', 'asc')->get();
      return Inertia::render('admin/dashboard/RegisteredMember', [
        'members' => $members
      ]);
    }
}
