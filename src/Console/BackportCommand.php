<?php

namespace Wiledia\Backport\Console;

use Wiledia\Backport\Backport;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

class BackportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backport';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all backport commands';

    /**
     * @var string
     */
    public static $logo = <<<LOGO
██████╗░░█████╗░░█████╗░██╗░░██╗██████╗░░█████╗░██████╗░████████╗
██╔══██╗██╔══██╗██╔══██╗██║░██╔╝██╔══██╗██╔══██╗██╔══██╗╚══██╔══╝
██████╦╝███████║██║░░╚═╝█████═╝░██████╔╝██║░░██║██████╔╝░░░██║░░░
██╔══██╗██╔══██║██║░░██╗██╔═██╗░██╔═══╝░██║░░██║██╔══██╗░░░██║░░░
██████╦╝██║░░██║╚█████╔╝██║░╚██╗██║░░░░░╚█████╔╝██║░░██║░░░██║░░░
╚═════╝░╚═╝░░╚═╝░╚════╝░╚═╝░░╚═╝╚═╝░░░░░░╚════╝░╚═╝░░╚═╝░░░╚═╝░░░
LOGO;

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->line(static::$logo);
        $this->line(Backport::getLongVersion());

        $this->comment('');
        $this->comment('Available commands:');

        $this->listBackportCommands();
    }

    /**
     * List all admin commands.
     *
     * @return void
     */
    protected function listBackportCommands()
    {
        $commands = collect(Artisan::all())->mapWithKeys(function ($command, $key) {
            if (Str::startsWith($key, 'backport:')) {
                return [$key => $command];
            }

            return [];
        })->toArray();

        $width = $this->getColumnWidth($commands);

        /** @var Command $command */
        foreach ($commands as $command) {
            $this->line(sprintf(" %-{$width}s %s", $command->getName(), $command->getDescription()));
        }
    }

    /**
     * @param (Command|string)[] $commands
     *
     * @return int
     */
    private function getColumnWidth(array $commands)
    {
        $widths = [];

        foreach ($commands as $command) {
            $widths[] = static::strlen($command->getName());
            foreach ($command->getAliases() as $alias) {
                $widths[] = static::strlen($alias);
            }
        }

        return $widths ? max($widths) + 2 : 0;
    }

    /**
     * Returns the length of a string, using mb_strwidth if it is available.
     *
     * @param string $string The string to check its length
     *
     * @return int The length of the string
     */
    public static function strlen($string)
    {
        if (false === $encoding = mb_detect_encoding($string, null, true)) {
            return strlen($string);
        }

        return mb_strwidth($string, $encoding);
    }
}
