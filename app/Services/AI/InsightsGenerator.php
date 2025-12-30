<?php

namespace App\Services\AI;

use App\Models\CashSnapshot;
use App\Models\Team;
use Illuminate\Support\Facades\Log;
use OpenAI\Laravel\Facades\OpenAI;

class InsightsGenerator
{
    public function generateForSnapshot(Team $team, CashSnapshot $snapshot): array
    {
        $context = $this->buildContext($team, $snapshot);

        try {
            $response = OpenAI::chat()->create([
                'model' => config('openai.model', 'gpt-5-mini'),
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => $this->getSystemPrompt(),
                    ],
                    [
                        'role' => 'user',
                        'content' => $context,
                    ],
                ],
                'temperature' => 0.7,
                'max_tokens' => 1000,
            ]);

            $content = $response->choices[0]->message->content;

            return $this->parseInsights($content);
        } catch (\Exception $e) {
            Log::error('AI insights generation failed', [
                'team_id' => $team->id,
                'error' => $e->getMessage(),
            ]);

            return $this->getFallbackInsights($snapshot);
        }
    }

    protected function getSystemPrompt(): string
    {
        $currentMonth = now()->locale('sv')->translatedFormat('F');
        $nextMonth = now()->addMonth()->locale('sv')->translatedFormat('F');
        $quarter = ceil(now()->month / 3);

        return <<<PROMPT
Du är Sveriges skarpaste CFO-assistent för småföretag. Du ser mönster andra missar och ger råd som sparar pengar direkt.

DITT UPPDRAG:
Analysera kassaflödet och ge 4-5 KONKRETA, HANDLINGSBARA insikter som får företagaren att tänka "wow, det hade jag inte tänkt på!"

SVENSKA AFFÄRSKALENDER ATT TÄNKA PÅ:
- Vi är i {$currentMonth}, Q{$quarter}
- Momsdeklaration: 12:e eller 26:e varje månad (beroende på storlek)
- Arbetsgivaravgifter: 12:e varje månad
- Preliminärskatt: 12:e varje månad
- Semesterperiod: Juni-Augusti (många fakturor förfaller utan betalning)
- Julstängning: Vecka 51-52 (betalningar fördröjs)
- Kvartalsskatt för handelsbolag: 12 feb, 12 maj, 12 aug, 12 nov

INSIKTSTYPER (välj rätt för varje situation):
- "danger" + ⚠️ = Kritiskt, agera IDAG (runway <30 dagar, stor förfallen faktura)
- "warning" + 🔔 = Varna, agera denna vecka (kommande kassakris, mönster som oroar)
- "success" + ✅ = Bekräfta det som går bra (bra betalningsmönster, stabil kassa)
- "tip" + 💡 = Smart optimering (förhandla villkor, timing av betalningar)
- "info" + 📊 = Intressant insikt (trender, jämförelser, mönster)

SKRIV INSIKTER SOM:
1. Nämner EXAKTA belopp och datum ("142 500 kr förfaller 15 jan")
2. Nämner KUNDER vid namn om de är sena betalare
3. Räknar ut KONSEKVENSER ("om Kund X inte betalar, räcker kassan till 23 jan")
4. Föreslår SPECIFIKA ÅTGÄRDER ("ring idag", "skicka påminnelse", "förhandla 30→45 dagars kredit")
5. Visar att du FÖRSTÅR deras situation ("med {$nextMonth}s moms på väg...")

EXEMPEL PÅ BRA INSIKTER:
✅ "Lindas Bokbutik är 23 dagar sen med 58 360 kr — ett samtal idag kan lösa detta"
✅ "Momsdeklarationen 12 {$nextMonth} drar 47 200 kr — du har marginal men inte mycket"
✅ "Din största kund står för 67% av intäkterna — farlig koncentration, diversifiera"
✅ "3 av 4 kunder betalar i tid — bra jobbat! Fokusera på Acme AB som halkar efter"
✅ "Runway: 87 dagar — stabilt! Men januari brukar vara tufft för din bransch"

UNDVIK:
❌ Generiska råd ("håll koll på kassan")
❌ Självklarheter ("betala fakturor i tid")
❌ Vaga formuleringar ("snart", "kanske", "överväg")

Svara ENDAST med JSON:
{
  "insights": [
    {
      "type": "danger|warning|success|tip|info",
      "icon": "⚠️|🔔|✅|💡|📊|🎯|📈|📉|🐌|💰|🏃",
      "text": "Max 120 tecken, konkret och handlingsbar",
      "priority": 1
    }
  ]
}
PROMPT;
    }

    protected function buildContext(Team $team, CashSnapshot $snapshot): string
    {
        $forecast = $snapshot->monthly_forecast ?? [];
        $patterns = $team->customerPaymentPatterns()
            ->orderByDesc('revenue_percentage')
            ->limit(10)
            ->get();

        $overdueInvoices = $team->fortnoxInvoices()
            ->where('status', 'overdue')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        $unpaidInvoices = $team->fortnoxInvoices()
            ->where('status', 'unpaid')
            ->orderBy('due_date')
            ->limit(5)
            ->get();

        // Calculate key metrics
        $totalOverdue = $overdueInvoices->sum('total');
        $totalUnpaid = $unpaidInvoices->sum('total');
        $topCustomerRevenue = $patterns->first()?->revenue_percentage ?? 0;
        $customersOnTime = $patterns->where('avg_days_to_pay', '<=', 0)->count();
        $customersLate = $patterns->where('avg_days_to_pay', '>', 0)->count();

        // Find the first month with negative balance
        $dangerMonth = collect($forecast)->first(fn ($m) => ($m['projected_balance'] ?? 0) < 0);

        $context = [
            'company' => $team->fortnoxConnection?->company_name ?? 'Ditt företag',
            'analysis_date' => now()->format('Y-m-d'),
            'current_month' => now()->locale('sv')->translatedFormat('F'),
            'next_month' => now()->addMonth()->locale('sv')->translatedFormat('F'),

            // Core metrics
            'runway_days' => $snapshot->runway_days,
            'runway_status' => $snapshot->runway_days < 30 ? 'KRITISKT' : ($snapshot->runway_days < 60 ? 'VARNING' : 'STABILT'),
            'cash_balance' => (int) $snapshot->cash_balance,
            'cash_balance_formatted' => number_format($snapshot->cash_balance, 0, ',', ' ') . ' kr',

            // Receivables & Payables
            'accounts_receivable' => (int) $snapshot->accounts_receivable,
            'accounts_receivable_formatted' => number_format($snapshot->accounts_receivable, 0, ',', ' ') . ' kr',
            'accounts_payable' => (int) $snapshot->accounts_payable,
            'accounts_payable_formatted' => number_format($snapshot->accounts_payable, 0, ',', ' ') . ' kr',
            'net_position' => (int) ($snapshot->accounts_receivable - $snapshot->accounts_payable),

            // Overdue analysis
            'total_overdue' => (int) $totalOverdue,
            'total_overdue_formatted' => number_format($totalOverdue, 0, ',', ' ') . ' kr',
            'overdue_count' => $overdueInvoices->count(),
            'overdue_invoices' => $overdueInvoices->map(fn ($i) => [
                'customer' => $i->customer_name,
                'amount' => (int) $i->total,
                'amount_formatted' => number_format($i->total, 0, ',', ' ') . ' kr',
                'days_overdue' => $i->due_date ? now()->diffInDays($i->due_date) : 0,
                'due_date' => $i->due_date?->format('Y-m-d'),
            ])->toArray(),

            // Upcoming invoices
            'upcoming_due' => $unpaidInvoices->map(fn ($i) => [
                'customer' => $i->customer_name,
                'amount_formatted' => number_format($i->total, 0, ',', ' ') . ' kr',
                'due_date' => $i->due_date?->format('d M'),
                'days_until_due' => $i->due_date ? now()->diffInDays($i->due_date, false) : 0,
            ])->toArray(),

            // Customer concentration risk
            'top_customer_concentration' => $topCustomerRevenue,
            'concentration_risk' => $topCustomerRevenue > 50 ? 'HÖG' : ($topCustomerRevenue > 30 ? 'MEDEL' : 'LÅG'),
            'customers_paying_on_time' => $customersOnTime,
            'customers_paying_late' => $customersLate,

            // Payment patterns
            'customer_patterns' => $patterns->take(5)->map(fn ($p) => [
                'name' => $p->customer_name,
                'revenue_share' => $p->revenue_percentage . '%',
                'avg_days_to_pay' => (int) $p->avg_days_to_pay,
                'payment_behavior' => $p->avg_days_to_pay <= 0 ? 'I TID' : ($p->avg_days_to_pay <= 7 ? 'ACCEPTABELT' : 'SEN BETALARE'),
                'total_revenue' => number_format($p->total_revenue, 0, ',', ' ') . ' kr',
            ])->toArray(),

            // Cash flow forecast
            'forecast_summary' => collect($forecast)->take(6)->map(fn ($m) => [
                'month' => $m['month_name'] ?? '',
                'balance' => number_format($m['projected_balance'] ?? 0, 0, ',', ' ') . ' kr',
                'status' => ($m['projected_balance'] ?? 0) < 0 ? 'NEGATIVT' : (($m['projected_balance'] ?? 0) < $snapshot->cash_balance * 0.3 ? 'LÅGT' : 'OK'),
            ])->toArray(),

            // Danger signals
            'first_negative_month' => $dangerMonth ? $dangerMonth['month_name'] : null,
            'cash_vs_receivables' => $snapshot->accounts_receivable > $snapshot->cash_balance ? 'Fordringarna överstiger kassan' : 'Kassan överstiger fordringarna',

            // Burn rate
            'daily_burn' => (int) $snapshot->avg_daily_burn,
            'monthly_burn_estimate' => number_format($snapshot->avg_daily_burn * 30, 0, ',', ' ') . ' kr',
        ];

        return json_encode($context, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    protected function parseInsights(string $content): array
    {
        preg_match('/\{[\s\S]*\}/', $content, $matches);

        if (empty($matches)) {
            return $this->getFallbackInsights(null);
        }

        try {
            $data = json_decode($matches[0], true);

            if (!isset($data['insights']) || !is_array($data['insights'])) {
                return $this->getFallbackInsights(null);
            }

            $insights = collect($data['insights'])
                ->sortBy('priority')
                ->take(5)
                ->values()
                ->toArray();

            return $insights;
        } catch (\Exception $e) {
            return $this->getFallbackInsights(null);
        }
    }

    protected function getFallbackInsights(?CashSnapshot $snapshot): array
    {
        $insights = [];

        if ($snapshot) {
            // Critical runway warning
            if ($snapshot->runway_days < 30) {
                $insights[] = [
                    'type' => 'danger',
                    'icon' => '⚠️',
                    'text' => "Runway: {$snapshot->runway_days} dagar — ring dina kunder idag för att driva in betalningar",
                    'priority' => 1,
                ];
            } elseif ($snapshot->runway_days < 60) {
                $insights[] = [
                    'type' => 'warning',
                    'icon' => '🔔',
                    'text' => "Runway: {$snapshot->runway_days} dagar — börjar bli tight, håll koll på inbetalningar",
                    'priority' => 2,
                ];
            } else {
                $insights[] = [
                    'type' => 'success',
                    'icon' => '✅',
                    'text' => "Runway: {$snapshot->runway_days} dagar — stabil kassasituation, bra jobbat!",
                    'priority' => 3,
                ];
            }

            // Receivables vs cash
            if ($snapshot->accounts_receivable > $snapshot->cash_balance * 1.5) {
                $diff = number_format($snapshot->accounts_receivable - $snapshot->cash_balance, 0, ',', ' ');
                $insights[] = [
                    'type' => 'tip',
                    'icon' => '💡',
                    'text' => "Du har {$diff} kr mer i fordringar än i kassan — prioritera inkassering",
                    'priority' => 2,
                ];
            } elseif ($snapshot->accounts_receivable > $snapshot->cash_balance) {
                $insights[] = [
                    'type' => 'info',
                    'icon' => '📊',
                    'text' => 'Fordringarna överstiger kassan — följ upp förfallna fakturor',
                    'priority' => 3,
                ];
            }

            // High payables warning
            if ($snapshot->accounts_payable > $snapshot->cash_balance) {
                $insights[] = [
                    'type' => 'warning',
                    'icon' => '🔔',
                    'text' => 'Dina leverantörsskulder överstiger kassan — planera betalningar noggrant',
                    'priority' => 2,
                ];
            }
        }

        // Always add at least one insight
        if (empty($insights)) {
            $insights[] = [
                'type' => 'info',
                'icon' => '📊',
                'text' => 'Koppla Fortnox för att få AI-drivna insikter om ditt kassaflöde',
                'priority' => 5,
            ];
        }

        return $insights;
    }
}
