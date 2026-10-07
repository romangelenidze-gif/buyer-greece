<?php

namespace App\Console\Commands;

use App\Actions\Quotes\ExpireQuoteAction;
use App\Enums\QuoteStatus;
use App\Models\Quote;
use Illuminate\Console\Command;

class ExpireQuotesCommand extends Command
{
    protected $signature = 'quotes:expire';

    protected $description = 'Истечение срока действия просроченных коммерческих расчётов';

    public function handle(ExpireQuoteAction $expireQuoteAction): int
    {
        $expiredQuotes = Quote::query()
            ->whereIn('status', [QuoteStatus::DRAFT, QuoteStatus::SENT])
            ->where('valid_until', '<=', now())
            ->get();

        $count = 0;
        foreach ($expiredQuotes as $quote) {
            $expireQuoteAction->execute($quote);
            $count++;
        }

        $this->info("Обработано истёкших квот: {$count}");

        return Command::SUCCESS;
    }
}