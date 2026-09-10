<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Subscription;

class CheckExpiredSubscriptions extends Command
{
    protected $signature = "subscriptions:check-expired";
    protected $description = "Deaktivira istekle pretplate i obnavlja auto-renewal";

    public function handle()
    {
        $expired = Subscription::where("endDate", "<", now())
            ->where("active", true)
            ->get();

        foreach ($expired as $subscription) {
            $subscription->update(["active" => false]);

            // Auto-renewal
            if ($subscription->autoRenew) {
                Subscription::create([
                    "userId" => $subscription->userId,
                    "startDate" => now(),
                    "endDate" => now()->addMonth(),
                    "price" => $subscription->price,
                    "active" => true,
                    "accountNumber" => $subscription->accountNumber,
                    "autoRenew" => true,
                ]);

                $this->info("Auto-renewal za userId {$subscription->userId}.");
            }

            $this->info("Pretplata {$subscription->subscriptionId} istekla.");
        }

        $this->info("Ukupno isteklih: {$expired->count()}");
    }
}
