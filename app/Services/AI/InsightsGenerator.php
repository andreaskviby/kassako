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
                'model' => 'gpt-5-mini',
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
        return <<<'PROMPT'
Du är en finansiell rådgivare för svenska småföretag. Din uppgift är att analysera kassaflödesdata och ge korta, handlingsbara insikter på svenska.

Regler:
1. Ge exakt 3-5 insikter
2. Varje insikt ska vara max 100 tecken
3. Var konkret och specifik (nämn månader, kunders namn, procentsatser)
4. Prioritera varningar först, sedan tips
5. Undvik generella råd - var specifik för datan
6. Tänk på svenska skatteregler (moms, arbetsgivaravgifter)

Svara i JSON-format:
{
  "insights": [
    {
      "type": "warning|info|tip",
      "icon": "⚠️|💡|📊|🐌|🎯|📈|📉",
      "text": "Insiktstexten här",
      "priority": 1-5
    }
  ]
}
PROMPT;
    }

    protected function buildContext(Team $team, CashSnapshot $snapshot): string
    {
        $forecast = $snapshot->monthly_forecast;
        $patterns = $team->customerPaymentPatterns()
            ->orderByDesc('revenue_percentage')
            ->limit(10)
            ->get();

        $overdueInvoices = $team->invoices()
            ->where('status', 'overdue')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        $context = [
            'company' => $team->fortnoxConnection?->company_name ?? 'Okänt företag',
            'runway_days' => $snapshot->runway_days,
            'cash_balance' => number_format($snapshot->cash_balance, 0, ',', ' ') . ' SEK',
            'accounts_receivable' => number_format($snapshot->accounts_receivable, 0, ',', ' ') . ' SEK',
            'accounts_payable' => number_format($snapshot->accounts_payable, 0, ',', ' ') . ' SEK',
            'avg_daily_income' => number_format($snapshot->avg_daily_income, 0, ',', ' ') . ' SEK',
            'avg_daily_burn' => number_format($snapshot->avg_daily_burn, 0, ',', ' ') . ' SEK',
            'forecast' => collect($forecast)->map(fn ($m) => [
                'month' => $m['month_name'],
                'balance' => number_format($m['projected_balance'], 0, ',', ' '),
                'warning' => $m['warning'],
                'has_tax' => $m['has_tax_payment'],
            ])->toArray(),
            'top_customers' => $patterns->map(fn ($p) => [
                'name' => $p->customer_name,
                'revenue_pct' => $p->revenue_percentage . '%',
                'avg_days_late' => $p->avg_days_to_pay . ' dagar',
                'reliability' => $p->payment_reliability,
            ])->toArray(),
            'overdue_invoices' => $overdueInvoices->map(fn ($i) => [
                'customer' => $i->customer_name,
                'amount' => number_format($i->total, 0, ',', ' ') . ' SEK',
                'days_overdue' => $i->days_overdue,
            ])->toArray(),
            'current_month' => now()->locale('sv')->translatedFormat('F'),
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
            if ($snapshot->runway_days < 30) {
                $insights[] = [
                    'type' => 'warning',
                    'icon' => '⚠️',
                    'text' => 'Kassan räcker mindre än 30 dagar — agera nu',
                    'priority' => 1,
                ];
            }

            if ($snapshot->accounts_receivable > $snapshot->cash_balance) {
                $insights[] = [
                    'type' => 'tip',
                    'icon' => '💡',
                    'text' => 'Dina utestående fakturor överstiger kassan — följ upp betalningar',
                    'priority' => 2,
                ];
            }
        }

        $insights[] = [
            'type' => 'info',
            'icon' => '📊',
            'text' => 'Data analyseras — fler insikter kommer snart',
            'priority' => 5,
        ];

        return $insights;
    }
}
