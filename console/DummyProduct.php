<?php namespace Octommerce\Octommerce\Console;

use File;
use Faker\Factory as Faker;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class DummyProduct extends Command
{
    /**
     * @var string The console command name.
     */
    protected $name = 'octommerce:dummy-product';

    /**
     * @var string The console command description.
     */
    protected $description = 'Generate dummy products using faker';

    /**
     * Execute the console command.
     * @return void
     */
    public function fire()
    {
        $this->generateProductFactory();
        $amount = (integer) $this->option('amount');

        factory(\Octommerce\Octommerce\Models\Product::class, $amount)->create();
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
        return [
            ['amount', null, InputOption::VALUE_OPTIONAL, 'Amount of dummy product', 1]
        ];
    }

    private function generateProductFactory()
    {
        $factoryTarget = base_path() . '/database/factories/ProductFactory.php';

        if (File::exists($factoryTarget)) return;

        File::copyDirectory(
            plugins_path() . '/octommerce/octommerce/classes/factories',
            base_path() . '/database/factories'
        );
    }
}
