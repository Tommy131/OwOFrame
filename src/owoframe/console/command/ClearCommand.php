<?php
/*
 *       _____   _          __  _____   _____   _       _____   _____
 *     /  _  \ | |        / / /  _  \ |  _  \ | |     /  _  \ /  ___|
 *     | | | | | |  __   / /  | | | | | |_| | | |     | | | | | |
 *     | | | | | | /  | / /   | | | | |  _  { | |     | | | | | |   _
 *     | |_| | | |/   |/ /    | |_| | | |_| | | |___  | |_| | | |_| |
 *     \_____/ |___/|___/     \_____/ |_____/ |_____| \_____/ \_____/
 *
 * Copyright (c) 2023 by OwOTeam-DGMT (OwOBlog).
 * @Author       : HanskiJay
 * @Date         : 2023-02-15 18:49:38
 * @LastEditors  : HanskiJay
 * @LastEditTime : 2023-02-19 05:33:14
 * @E-Mail       : support@owoblog.com
 * @Telegram     : https://t.me/HanskiJay
 * @GitHub       : https://github.com/Tommy131
 */
declare(strict_types=1);
namespace owoframe\console\command;



use FilesystemIterator as FI;

use owoframe\console\CommandBase;
use owoframe\utils\TextColorOutput as TCO;

class ClearCommand extends CommandBase
{
    public function execute(array $params) : bool
    {
        if(count($params) <= 0) {
            TCO::sendClear();
            echo TCO::background('[SUCCESS]', 42) . '  Screen cleared.' . PHP_EOL . PHP_EOL;
        } else {
            switch(strtolower(array_shift($params))) {
                default:
                    $this->execute([]);
                break;

                case 'log':
                    $param = array_shift($params) ?? null;
                    if($param) {
                        $param = strtolower($param) . $param . '.log';
                        if(is_file(\owo\log_path($param))) {
                            unlink(\owo\log_path($param));
                            $param = TCO::GREEN . 'Removed log file ' . TCO::GOLD . $param . TCO::GREEN . ' successfully.';
                        } else {
                            $param = TCO::LIGHT_RED . 'Cannot find log file ' . TCO::GOLD . $param . TCO::LIGHT_RED . '!';
                        }
                        $this->getLogger()->info($param);
                    } else {
                        $files = iterator_to_array(new FI(\owo\log_path(), FI::CURRENT_AS_PATHNAME | FI::SKIP_DOTS), false);
                        if(count($files) === 1) {
                            $this->getLogger()->info('No files to delete.');
                            return true;
                        }

                        foreach($files as $file) {
                            $baseName = basename($file);
                            $ext = @end(explode('.', $baseName));
                            if(strtolower($ext) === 'log') {
                                unlink($file);
                                $this->getLogger()->success(TCO::GREEN . 'Removed log file ' . TCO::GOLD . $baseName . TCO::GREEN . ' successfully.');
                            }
                        }
                    }
                break;

                case 'cache':
                    $path = array_shift($params) ?? '';
                    $path = \owo\cache_path(strtolower($path));
                    if(\owo\remove_dir($path)) {
                        mkdir($path);
                        \owo\add_gitignore($path);
                        $this->getLogger()->success(TCO::GREEN . 'Removed Cache path ' . TCO::GOLD . $path . TCO::GREEN . ' successfully.');
                    } else {
                        $this->getLogger()->error('Delete Cache path failed!');
                    }
                break;
            }
        }
        return true;
    }

    public static function getAliases() : array
    {
        return ['c', '-c'];
    }

    public static function getName() : string
    {
        return 'clear';
    }

    public static function getDescription() : string
    {
        return "Command for clear the screen or empty a log file. Usage: owo clear log|cache";
    }
}
?>