<?php

namespace App\Console;

class Kernal 
{
    const COMMON_LIST = [
        ['App/Services/MemberService', 'test', '25 */2 * * *'],
    ];

    public function run()
    {
        if (!empty($_SERVER['argv'][1])) {
            call_user_func_array([make($_SERVER['argv'][1]), $_SERVER['argv'][2]], []);
            exit();
        }
        if (empty(self::COMMON_LIST)) return false;
        foreach (self::COMMON_LIST as $value) {
            if ($this->matchTime($value[2] ?? '')) {
                $this->execCommand('php '.ROOT_PATH.'command '.$value[0].' '.$value[1]);
            }
        }
        return true;
    }

    private function execCommand($cmd)
    {
        if (isWin()) {
            exec($cmd);
        } else {
            pclose(popen('start /B '.$cmd, 'r')); 
        }
    }

    private function matchTime($time)
    {
        if (empty($time)) return true;
        $time = explode(' ', preg_replace('/\s(?=\s)/', '\\1', trim($time)));
        $checkStatus = true;
        $dateArr = [
            date('i'),
            date('G'),
            date('j'),
            date('n'),
            date('w'),
        ];
        foreach ($time as $key => $value) {
            if (!$checkStatus) {
                return false;
            }
            if ($value == '*') {
                $checkStatus = true;
            } else {
                $tempArr = explode(',', $value);
                $checkStatus = false;
                foreach ($tempArr as $tk => $tv) {
                    if (is_numeric($tv)) {
                        if ($dateArr[$key] == $tv) {
                            $checkStatus = true;
                        }
                    } else {
                        if ($tv == '*') {
                            $checkStatus = true;
                        } else {
                            $tv = explode('/', $tv)[1];
                            if ($dateArr[$key] % $tv == 0) {
                                $checkStatus = true;
                            }
                        }
                    }
                }
            }
        }
        return $checkStatus;
    }
}
