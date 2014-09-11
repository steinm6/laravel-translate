<?php

namespace Steinm6\LaravelTranslate\Commands;

use Illuminate\Console\Command;
use Config;

class TranslateCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'translate:extract';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Merge all blade templates so gettext can parse them.';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        $fileinfos = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(app_path() . "/views")
        );

        $compiled = '<?php ';

        foreach ($fileinfos as $pathname => $fileinfo) {
            if (!$fileinfo->isFile()) continue;
            $text = str_replace("\t", "", str_replace("\n", " ", file_get_contents($pathname)));

            preg_match_all("/Translate::_(n)?\\([\"|']{1}((?!\\)).)*[\"|']{1}\\)/", $text, $found);

            foreach ($found[0] as $value)
                $compiled .= $value . ';';
        }

        $filename = Config::get('translate.extractPath');
        if (!$filename) {
            throw new \Exception('ExtractPath (translate.extractPath) not set.');
        }
        file_put_contents("", $compiled);
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return array();
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return array();
    }

}
