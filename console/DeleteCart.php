<?php namespace Octommerce\Octommerce\Console;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Octommerce\Octommerce\Models\Cart;
use Octommerce\Octommerce\Models\Settings;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;

class DeleteCart extends Command
{
    /**
     * @var string The console command name.
     */
    protected $name = 'octommerce:delete-cart';

    /**
     * @var string The console command description.
     */
    protected $description = 'Delete unused cart in a few days';

    /**
     * Execute the console command.
     * @return void
     */
    public function fire()
    {
        // Default value is 30 days
        $inDays = Settings::get('delete_cart_in_days') ?: 30;
        $maxDate = Carbon::now()->subDays($inDays);

        $cartBuilder = Cart::with('products')->whereDate('updated_at', '<', $maxDate);

        $total = $cartBuilder->count();
        $perPage = 1000;
        $totalPage = ceil($total / $perPage);

        $this->info('Deleting old cart...');

        $progressBar = new ProgressBar($this->output, $total);
        $progressBar->setFormat('debug');
        $progressBar->start();

        for ($page = 1; $page <= $totalPage; $page++) {
            $cartBuilder->paginate($page, 1000)
                ->each(function($cart) use ($progressBar) {
                    $cart->products()->detach();
                    $cart->delete();

                    $progressBar->advance();
                });
        }

        $this->line('Finish');
    }

    /**
     * Get the console command arguments.
     * @return array
     */
    protected function getArguments()
    {
        return [];
    }

    /**
     * Get the console command options.
     * @return array
     */
    protected function getOptions()
    {
        return [];
    }
}
