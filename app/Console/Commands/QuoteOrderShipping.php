<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\OrderService;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

class QuoteOrderShipping extends Command
{
    protected $signature = 'orders:quote {number : Numéro de commande (ex. JP-261015-K3M9Q)} {amount : Frais de livraison (ex. 12.50)}';

    protected $description = 'Fixe les frais de livraison d\'une commande en attente et la confirme';

    public function handle(OrderService $orders): int
    {
        if (! is_numeric($this->argument('amount')) || (float) $this->argument('amount') < 0) {
            $this->error('Le montant doit être un nombre positif ou nul.');

            return self::FAILURE;
        }

        try {
            $order = $orders->quoteShipping($this->argument('number'), (int) round((float) $this->argument('amount') * 100));
        } catch (ModelNotFoundException) {
            $this->error('Commande introuvable.');

            return self::FAILURE;
        } catch (ValidationException $e) {
            $this->error($e->validator->errors()->first());

            return self::FAILURE;
        }

        $this->info(sprintf('%s confirmée : livraison %.2f, total %.2f.', $order->number, $order->shipping_cents / 100, $order->total_cents / 100));

        return self::SUCCESS;
    }
}
