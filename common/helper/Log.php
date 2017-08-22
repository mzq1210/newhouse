<?php

/*
 * 日志
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace common\helper;

class Log {

    /**
     * 日志写入接口
     * @access public
     * @param string $log 日志信息
     * @param string $destination  写入目标
     * @return void
     */
    public static function write($log, $msgMode) {
        $fileSize = LOG_FILE_SIZE * 1024 * 1024;
        $now = date('Y-m-d H:i:s');
        $destination = LOG_SAVE_PATH . '/' . $msgMode . '_' . date('y_m_d') . '.log';

        if (!is_dir(LOG_SAVE_PATH)) {
            mkdir(LOG_SAVE_PATH, 0755, true);
        }
        //检测日志文件大小，超过配置大小则备份日志文件重新生成
        if (is_file($destination) && floor($fileSize) <= filesize($destination)) {
            rename($destination, dirname($destination) . '/' . time() . '-' . basename($destination));
        }

        if (!is_array($log)) {
            error_log("[{$now}] " . $_SERVER['REMOTE_ADDR'] . $_SERVER['REQUEST_URI'] . ' ' . "{$log}\r\n", 3, $destination);
        } else {
            error_log("[{$now}] " . $_SERVER['REMOTE_ADDR'] . $_SERVER['REQUEST_URI'] . ' ' . implode($log, " ") . "\r\n", 3, $destination);
        }
    }

    /**
     * @param string $message - 发送的消息
     * @param string $msgMode - 消息模式 错误信息或提示信息
     * @return bool
     */
    public static function send($message, $msgMode) {
        $data['msg'] = $message;
        $url = LOG_HOST;
        if (!LOG_HOST)
            return true;

        if(is_array($message)){
            $data['msg'] = implode(' ', $message);
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data)); //如果不用json速度比较慢
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 6);
        $headers = [
            "application/json",
//            'Content-Length: ' . strlen(json_encode$message)),
            'Referer: ' . $_SERVER['REQUEST_URI'],
            "ip:" . $_SERVER['REMOTE_ADDR'],
            "msgMode" . $msgMode,
        ];

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); //设置header
        $handles = curl_exec($ch);
        curl_close($ch);
        return $handles;
    }

    public static function record($message, $msgMode) {
        if (!LOG_RECORD) {
            return true;
        }
        switch (LOG_SAVE_MODE) {
            case 1:
                self::send($message, $msgMode);
                break;
            case 0:
                self::write($message, $msgMode);
                break;
            default :
                self::write($message . $msgMode);
        }
    }

    public static function info($message = '') {
        self::record($message, 'info');
    }

    public static function error($message = '') {
        self::record($message, 'error');
    }

}
