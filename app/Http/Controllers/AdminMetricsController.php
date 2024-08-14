<?php
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Signature;
use Illuminate\Http\Request;

class AdminMetricsController extends Controller
{
    public function getMetrics(Request $request)
    {
        // Ensure the user is an admin (optional, if you have roles)
        if ($request->user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Calculate total users and total signatures
        $totalUsers = User::count();
        $totalSignatures = Signature::count();

        // Optionally, calculate signatures per month (for chart)
        $signaturesPerMonth = Signature::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month');

        return response()->json([
            'totalUsers' => $totalUsers,
            'totalSignatures' => $totalSignatures,
            'signaturesPerMonth' => $signaturesPerMonth,
        ]);
    }
}
