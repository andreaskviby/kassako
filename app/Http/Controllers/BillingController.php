<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BillingController extends Controller
{
    public function index()
    {
        $team = Auth::user()->currentTeam;

        return view('billing.index', [
            'team' => $team,
            'subscription' => $team->subscription('default'),
            'onTrial' => $team->onTrial(),
            'subscribed' => $team->subscribed('default'),
            'invoices' => $team->invoices(),
        ]);
    }

    public function subscribe(Request $request)
    {
        $team = Auth::user()->currentTeam;

        if ($team->onTrial()) {
            return $team->newSubscription('default', config('stripe.price_id'))
                ->checkout([
                    'success_url' => route('billing.success'),
                    'cancel_url' => route('billing'),
                ]);
        }

        return $team->newSubscription('default', config('stripe.price_id'))
            ->trialDays(14)
            ->checkout([
                'success_url' => route('billing.success'),
                'cancel_url' => route('billing'),
            ]);
    }

    public function success()
    {
        return redirect()->route('dashboard')
            ->with('success', 'Tack! Din prenumeration är aktiverad.');
    }

    public function portal(Request $request)
    {
        $team = Auth::user()->currentTeam;

        return $team->redirectToBillingPortal(route('billing'));
    }

    public function cancel(Request $request)
    {
        $team = Auth::user()->currentTeam;

        $team->subscription('default')->cancel();

        return redirect()->route('billing')
            ->with('success', 'Din prenumeration avslutas vid periodens slut.');
    }

    public function resume(Request $request)
    {
        $team = Auth::user()->currentTeam;

        $team->subscription('default')->resume();

        return redirect()->route('billing')
            ->with('success', 'Din prenumeration är återaktiverad.');
    }
}
