<?php

namespace Database\Seeders;

use App\Models\CashSnapshot;
use App\Models\FortnoxConnection;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Check if user already exists
        $user = User::where('email', 'test@example.com')->first();

        if (!$user) {
            // Create test user with team
            $user = User::factory()->withPersonalTeam()->create([
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);
        }

        $team = $user->currentTeam;

        // Update team name to a company name
        $team->update([
            'name' => 'Demo Företag AB',
        ]);

        // Create mock Fortnox connection (so dashboard shows data)
        FortnoxConnection::updateOrCreate(
            ['team_id' => $team->id],
            [
                'company_name' => 'Demo Företag AB',
                'organization_number' => '556123-4567',
                'access_token' => 'demo_access_token',
                'refresh_token' => 'demo_refresh_token',
                'token_expires_at' => now()->addDays(30),
                'is_active' => true,
                'last_synced_at' => now(),
                'sync_status' => 'completed',
            ]
        );

        // Create sample cash snapshot with realistic data
        $monthlyForecast = $this->generateMonthlyForecast();

        CashSnapshot::updateOrCreate(
            ['team_id' => $team->id, 'snapshot_date' => now()->toDateString()],
            [
                'cash_balance' => 847320,
                'accounts_receivable' => 234500,
                'accounts_payable' => 156200,
                'runway_days' => 87,
                'avg_daily_burn' => 9738,
                'avg_daily_income' => 12450,
                'monthly_forecast' => $monthlyForecast,
                'insights' => [
                    [
                        'type' => 'success',
                        'icon' => '✓',
                        'text' => 'Stabilt kassaflöde. Din runway har ökat med 12 dagar senaste månaden.',
                    ],
                    [
                        'type' => 'warning',
                        'icon' => '⚠️',
                        'text' => '2 förfallna fakturor på totalt 45 000 kr. Överväg påminnelse.',
                    ],
                    [
                        'type' => 'info',
                        'icon' => 'ℹ️',
                        'text' => 'Kund "Byggteamet AB" betalar i snitt 8 dagar sent. Fakturera tidigare?',
                    ],
                ],
            ]
        );
    }

    private function generateMonthlyForecast(): array
    {
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'Maj', 'Jun', 'Jul', 'Aug', 'Sep', 'Okt', 'Nov', 'Dec'];
        $startMonth = now()->month - 1;
        $forecast = [];

        $projectedBalances = [
            620000, 680000, 590000, 720000, 780000, 847320,
            890000, 920000, 880000, 950000, 1020000, 1080000,
        ];

        for ($i = 0; $i < 12; $i++) {
            $monthIndex = ($startMonth + $i) % 12;
            $forecast[] = [
                'month_name' => $months[$monthIndex],
                'projected_balance' => $projectedBalances[$i],
                'warning' => $projectedBalances[$i] < 700000,
            ];
        }

        return $forecast;
    }
}
