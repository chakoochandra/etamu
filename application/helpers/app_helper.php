<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

if (!function_exists('get_or_set_menu')) {
    function get_or_set_menu($fromSession = true)
    {
        $CI = get_instance();

        $menus = iterateMenu(getMenu());

        $CI->session->set_userdata("app_menu", $menus);

        return $menus;
    }
}

if (!function_exists('get_client_ip')) {
    function get_client_ip()
    {
        $ipaddress = '';
        if (isset($_SERVER['HTTP_CLIENT_IP']))
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        else if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if (isset($_SERVER['HTTP_X_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        else if (isset($_SERVER['HTTP_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        else if (isset($_SERVER['HTTP_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        else if (isset($_SERVER['REMOTE_ADDR']))
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
    }
}

if (!function_exists('is_development')) {
    function is_development()
    {
        return ENVIRONMENT != 'production';
    }
}

if (!function_exists('add_days_to_date')) {
    function add_days_to_date($date, $days)
    {
        return date('Y-m-d', strtotime($date . (intval($days) >= 0 ? ' + ' : ' - ') . abs($days) . ' days'));
    }
}

if (!function_exists('get_notif_range')) {
    function get_notif_range($filter = '')
    {
        $range = [
            'antrian' => [1, 1],
            'sidang' => [2, 6],
            'calendar' => !is_development() ? 6 : 365,
            'jurnal' => !is_development() ? -30 : -365,
            'ac' => !is_development() ? -30 : -365,
        ];
        return isset($range[$filter]) ? $range[$filter] : $range['antrian'];
    }
}

if (!function_exists('get_notif_criteria')) {
    function get_notif_criteria($filter = '')
    {
        $range = get_notif_range($filter);
        switch ($filter) {
            case 'antrian':
            case 'sidang':
                return "tipe LIKE 'sidang%' AND tanggal_sidang = tanggal_antrian AND tanggal_antrian BETWEEN CURDATE() + INTERVAL {$range[0]} DAY AND CURDATE() + INTERVAL {$range[1]} DAY";
                // case 'sidang':
                //     return "tanggal_sidang BETWEEN CURDATE() + INTERVAL {$range[0]} DAY AND CURDATE() + INTERVAL {$range[1]} DAY";
            case 'calendar':
                return "((rencana_tanggal BETWEEN CURDATE() + INTERVAL 1 DAY AND CURDATE() + INTERVAL {$range} DAY) OR (rencana_tanggal = CURRENT_DATE() AND rencana_jam > CURRENT_TIME())) AND rencana_agenda REGEXP 'perbaikan|jawaban|replik|duplik|kesimpulan'";
            case 'jurnal':
                // jenis perkara 346 = cerai talak
                // status putusan 62 = Dikabulkan
                return "(tahapan_id = 10 AND tanggal_putusan > CURRENT_DATE() + INTERVAL {$range} DAY AND (pemasukan - pengeluaran) > 0 AND (CASE WHEN (perkara.jenis_perkara_id = 346 AND status_putusan_id = 62) THEN (tgl_ikrar_talak IS NOT NULL) ELSE (1=1) END))";
            case 'ac':
                return "tgl_akta_cerai IS NOT NULL AND (tgl_penyerahan_akta_cerai IS NOT NULL AND tgl_penyerahan_akta_cerai_pihak2 IS NULL) AND tgl_akta_cerai > CURRENT_DATE() + INTERVAL {$range} DAY";
        }
    }
}

if (!function_exists('add_currency_symbol')) {
    function add_currency_symbol($amount, $currencySymbol = 'Rp')
    {
        return $currencySymbol . ' ' . number_format($amount, 0, ',', '.');
    }
}

if (!function_exists('cleansePhoneNumbers')) {
    function cleansePhoneNumbers($number, $includeInvalid = false)
    {
        $cleanedNumbers = [];

        // Temukan semua potongan angka dalam string (baik dengan atau tanpa tanda +).
        preg_match_all('/(\+?\d+)/', str_replace(['-', ' '], '', $number), $matches);

        foreach ($matches[0] as $match) {
            // Hapus karakter yang tidak valid.
            $cleanedPiece = preg_replace('/[^0-9+]/', '', $match);

            // Periksa apakah nomor telepon dimulai dengan kode negara atau kode area, jika tidak, tambahkan kode negara Indonesia (+62).
            if (!in_array(substr($cleanedPiece, 0, 2), ['62', '60']) && substr($cleanedPiece, 0, 1) != '+') {
                $cleanedPiece = '+62' . ltrim($cleanedPiece, '0'); // Tambahkan +62 dan hapus angka 0 di depan.
            }

            if ($includeInvalid || strlen($cleanedPiece) >= 10) {
                // Hapus karakter tambahan selain angka.
                $cleanedNumbers[] = preg_replace('/[^0-9]/', '', $cleanedPiece);
            }
        }

        return $cleanedNumbers;
    }
}

if (!function_exists('merge_html_class')) {
    function merge_html_class($class1, $class2 = '')
    {
        // Extract the class names from the strings
        $class1 = str_replace('class="', '', $class1);
        $class2 = str_replace('class="', '', $class2);
        $class1 = rtrim($class1, '"');
        $class2 = rtrim($class2, '"');

        // Merge the class names into a single string
        $mergedString = 'class="' . $class1 . ' ' . $class2 . '"';

        return $mergedString;
    }
}

if (!function_exists('generate_excel')) {
    function generate_excel($options)
    {
        include APPPATH . 'third_party/PHPExcel/Classes/PHPExcel.php';

        $CI = get_instance();

        $columns = isset($options['columns']) ? $options['columns'] : [];
        $isLandscape = count($columns) > 7;
        $startRow = isset($options['startRow']) ? $options['startRow'] : 1;
        $startCol = isset($options['startColumn']) ? $options['startColumn'] : 'A';
        $startOrd = ord($startCol);
        $endCol = chr($startOrd + count($columns));

        $excel = new PHPExcel();
        $excel->getProperties()
            ->setCreator($CI->user->nama_lengkap)
            ->setLastModifiedBy($CI->user->nama_lengkap);

        if (isset($options['properties']['title'])) {
            $excel->getProperties()->setTitle($options['properties']['title']);
        }
        if (isset($options['properties']['subject'])) {
            $excel->getProperties()->setSubject($options['properties']['subject']);
        }
        if (isset($options['properties']['description'])) {
            $excel->getProperties()->setDescription($options['properties']['description']);
        }
        if (isset($options['properties']['keywords'])) {
            $excel->getProperties()->setKeywords($options['properties']['keywords']);
        }

        $activeSheet = $excel->getActiveSheet();

        $tabColors = [
            1 => PHPExcel_Style_Color::COLOR_GREEN,
            2 => PHPExcel_Style_Color::COLOR_DARKGREEN,
        ];

        if (isset($options['data']['allData'])) {
            $keys = array_keys($options['data']['allData']);
            $lastArrayKey = array_pop($keys);
            $tab = 1;
            foreach ($options['data']['allData'] as $key => $data) {
                $activeSheet->getPageSetup()->setOrientation($isLandscape ? PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE : PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT);
                $activeSheet->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
                $activeSheet->getPageSetup()->setFitToPage(true);
                $activeSheet->getPageSetup()->setFitToWidth(1);
                $activeSheet->getPageSetup()->setFitToHeight(0);
                $activeSheet->getPageSetup()->setHorizontalCentered(true);

                $activeSheet->getPageMargins()->setTop(0.5);
                $activeSheet->getPageMargins()->setBottom(0.8);

                $activeSheet->getTabColor()->setARGB($tabColors[$tab]);

                if (isset($options['data']['allSheets'][$key])) {
                    $activeSheet->setTitle($options['data']['allSheets'][$key]);
                }

                $curRow = $startRow;
                if (isset($options['data']['allTitles'][$key])) {
                    $activeSheet->mergeCells("{$startCol}{$curRow}:{$endCol}{$curRow}");
                    $activeSheet->getRowDimension($curRow)->setRowHeight(50);
                    $activeSheet->getStyle("{$startCol}{$curRow}")->applyFromArray(
                        [
                            'alignment' => [
                                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                            ],
                            'font'  => [
                                'bold'  => true,
                                'size'  => 30,
                                'name'  => 'Verdana'
                            ]
                        ]
                    );

                    $activeSheet->setCellValue("{$startCol}{$curRow}", $options['data']['allTitles'][$key]);
                    $curRow += 1;
                }

                $activeSheet->mergeCells("{$startCol}{$curRow}:{$endCol}{$curRow}");
                $activeSheet->getRowDimension($curRow)->setRowHeight(20);
                $activeSheet->getStyle("{$startCol}{$curRow}")->applyFromArray(
                    [
                        'alignment' => [
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                        ],
                        'font'  => [
                            'bold'  => true,
                            'size'  => 12,
                            'name'  => 'Verdana'
                        ]
                    ]
                );

                if (isset($options['subTitle'])) {
                    $activeSheet->setCellValue("{$startCol}{$curRow}", $options['subTitle']);
                    $curRow += 2;
                }

                $startRowTable = $curRow;

                $activeSheet->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd($curRow, $curRow);
                $activeSheet->getColumnDimension($startCol)->setWidth(3.5);
                $activeSheet->setCellValue("{$startCol}{$curRow}", "No.");

                $curOrd = $startOrd + 1;
                foreach ($columns as $field => $header) {
                    $activeSheet->getColumnDimension(chr($curOrd))->setWidth($options['colWidth'][$field]);
                    $activeSheet->setCellValue(chr($curOrd) . $curRow, $header);
                    $curOrd++;
                }

                $activeSheet->getRowDimension($curRow)->setRowHeight(30);
                $activeSheet->getStyle("{$startCol}{$curRow}:{$endCol}{$curRow}")->applyFromArray(
                    [
                        'fill' => [
                            'type' => PHPExcel_Style_Fill::FILL_SOLID,
                            'color' => ['rgb' => '008000']
                        ],
                        'font'  => [
                            'bold'  => true,
                            'color' => ['rgb' => 'FFFFFF'],
                            'size'  => 8,
                            'name'  => 'Verdana'
                        ],
                        'alignment' => [
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                        ]
                    ]
                );

                $style = [
                    'alignment' => [
                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    ]
                ];

                $no = 1;
                $curRow += 1;
                foreach ($data as $row) {
                    $activeSheet->getRowDimension($curRow)->setRowHeight(60);
                    $activeSheet->setCellValue("{$startCol}{$curRow}", $no);
                    $activeSheet->getStyle("{$startCol}{$curRow}")->applyFromArray($style);

                    $curOrd = $startOrd + 1;
                    foreach ($columns as $field => $header) {
                        if (isset($options['colStyles'][$field])) {
                            $activeSheet->getStyle(chr($curOrd) . $curRow)->applyFromArray($options['colStyles'][$field]);
                        }

                        if ($field == 'jam') {
                            if ($row->no_antrian) {
                                $startHour = date('H:i', strtotime($row->jam_sidang));
                                $activeSheet->setCellValue(chr($curOrd) . $curRow, $startHour);
                                $startHour = date('H:i', strtotime("+{$row->$field} minutes", strtotime($startHour)));
                            }
                        } else if ($field == 'nama_ruang') {
                            $activeSheet->setCellValue(chr($curOrd) . $curRow, $row->no_antrian);
                            // $activeSheet->setCellValue(chr($curOrd) . $curRow, $row->no_antrian ?: $row->nama_ruang);
                        } else {
                            $activeSheet->setCellValue(chr($curOrd) . $curRow, str_replace(['</br>', '<br />'], "\n", $row->$field));
                        }
                        $curOrd++;
                    }

                    $no++;
                    $curRow++;
                }

                $activeSheet->getStyle("{$startCol}{$startRowTable}:{$endCol}" . ($curRow - 1))->applyFromArray([
                    'borders' => [
                        'allborders' => [
                            'style' => PHPExcel_Style_Border::BORDER_THIN,
                        ]
                    ]
                ]);

                $activeSheet->getStyle("{$startCol}{$startRow}:" . chr($curOrd) . $curRow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $activeSheet->getStyle("{$startCol}{$startRow}:" . chr($curOrd) . $curRow)->getAlignment()->setWrapText(true);

                if (isset($options['footer'])) {
                    $activeSheet->getHeaderFooter()->setOddFooter($options['footer']);
                }
                if (isset($options['header'])) {
                    $activeSheet->getHeaderFooter()->setOddHeader($options['header']);
                }

                if ($key != $lastArrayKey) {
                    $tab++;
                    $activeSheet = $excel->createSheet($tab);
                }
            }
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . (isset($options['filename']) ? $options['filename'] : time()) . '.xlsx"');
        header('Cache-Control: max-age=0');
        $write = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
        $write->save('php://output');
    }
}

if (!function_exists('hit_api')) {
    function hit_api($endpoint, $type = 'GET', $data = null, $token = null)
    {
        $CI = get_instance();
        $CI->load->library('Guzzle');

        $client = new GuzzleHttp\Client([
            'verify' => false
        ]);
        $request = new GuzzleHttp\Psr7\Request(strtoupper($type), $endpoint);

        try {
            return $client->sendAsync($request, [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $token,
                ],
                'json' => $data,
            ])->then(function ($response) {
                return [
                    'status' => true,
                    'code' => $response->getStatusCode(),
                    'response' => $response->getBody()->getContents(),
                ];
            })->wait();
        } catch (GuzzleHttp\Exception\RequestException $exception) {
            return [
                'status' => false,
                'code' => $exception->hasResponse() ? $exception->getResponse()->getStatusCode() : $exception->getCode(),
                'response' => $exception->hasResponse() ? $exception->getResponse()->getBody()->getContents() : $exception->getMessage(),
            ];
        } catch (\Exception $exception) {
            return [
                'status' => false,
                'code' => null,
                'response' => $exception->getMessage(),
            ];
        }
    }

    function hit_api_async($endpoint, $type = 'GET', $data = null, $token = null)
    {
        $ch = curl_init($endpoint);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 1); // Set timeout to 1 second
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($type));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $token,
        ]);
        if ($type == 'POST' || $type == 'PUT' || $type == 'PATCH') {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        return [
            'status' => $error ? false : true,
            'code' => $http_code,
            'response' => $response ?: $error,
        ];
    }
}

if (!function_exists('send_wa')) {
    function send_wa($target, $text, $sleep = 60, $filePath = null)
    {
        $data = [
            'session' => DIALOGWA_SESSION,
            'target' => $target,
            'message' => $text,
        ];

        if ($filePath && ($imageData = file_get_contents($filePath))) {
            $data['file'] = 'data:' . mime_content_type($filePath) . ';base64,' . base64_encode($imageData);
        }

        $result = hit_api(
            DIALOGWA_API_URL . ($filePath ? '/send-media' : '/send-text'),
            'post',
            $data,
            DIALOGWA_TOKEN
        );

        if ($result['status'] === false) {
            return ['status' => $result['status'], 'message' => isset($result['response']) ? $result['response'] : (isset($result['message']) ? $result['message'] : 'Terjadi Kesalahan!')];
        }

        sleep((is_development() ? 1 : $sleep));

        $result = json_decode($result['response'], 1);

        if (!isset($result['data'])) {
            return $result;
        }

        $data = ['status' => $result['status'], 'sent_time' => date('Y-m-d H:i:s')];
        foreach ($result['data'] as $item) {
            $data[$item['status'] == 200 ? $item['target'] : $target] = $item;
        }
        return $data;
    }
}

if (!function_exists('curl_reset')) {
    function curl_reset(&$ch)
    {
        curl_close($ch);
        $ch = curl_init();
    }
}

if (!function_exists('write_custom_log')) {
    function write_custom_log($message, $level = 'info', $file = 'custom_log')
    {
        $CI = &get_instance();
        $CI->load->helper('file');

        $filepath = APPPATH . 'logs/' . $file . '-' . date('Y-m-d') . '.php';
        $message  = strtoupper($level) . ' ' . date('Y-m-d H:i:s') . ' --> ' . $message . "\n";

        if (!write_file($filepath, $message, 'a')) {
            log_message('error', 'Unable to write to custom log file: ' . $filepath);
        }
    }
}

if (!function_exists('is_owner')) {
    function is_owner($id_pegawai)
    {
        return get_instance()->user->id == $id_pegawai;
    }
}

if (!function_exists('arraySearchKeyIndex')) {
    function arraySearchKeyIndex($key, $value, $array)
    {
        foreach ($array as $k => $val) {
            if ($val[$key] === $value) {
                return $k;
            }
        }
        return null;
    }
}

if (!function_exists('get_dates')) {
    function get_dates($month, $year)
    {
        $num = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $dates_month = array();

        for ($i = 1; $i <= $num; $i++) {
            $mktime = mktime(0, 0, 0, $month, $i, $year);
            $date = date("Y-m-d", $mktime);
            $dates_month[$i] = $date;
        }

        return $dates_month;
    }
}

if (!function_exists('formatDate')) {
    function formatDate($date, $format = "%d %B %Y")
    {
        if (!$date || $date == '0000-00-00') {
            return null;
        }
        return getLocaleTime(strftime($format, strtotime($date)));
    }
}

if (!function_exists('getLocaleTime')) {
    function getLocaleTime($time)
    {
        $days = [
            'Sunday' => 'Minggu',
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
        ];
        $months = [
            'January' => 'Januari',
            'February' => 'Februari',
            'March' => 'Maret',
            'April' => 'April',
            'May' => 'Mei',
            'June' => 'Juni',
            'July' => 'Juli',
            'August' => 'Agustus',
            'September' => 'September',
            'October' => 'Oktober',
            'November' => 'November',
            'December' => 'Desember',
        ];

        $time = str_replace(array_keys($days), array_values($days), $time);
        $time = str_replace(array_keys($months), array_values($months), $time);
        return $time;
    }
}

if (!function_exists('getSemesterFromDate')) {
    function getSemesterFromDate($dateString)
    {
        $date = new DateTime($dateString);
        $month = (int)$date->format('n');
        return floor($month / 6) + 1;
    }
}

if (!function_exists('do_upload')) {
    function do_upload($field, $folder, $filename = null, $types = 'pdf', $max_size = 10240)
    {
        $CI = get_instance();

        $config['upload_path'] = FOLDER_ROOT_UPLOAD . $folder;
        $config['allowed_types'] = $types; //'gif|jpg|png|jpeg'
        $config['max_size']  = $max_size;
        $config['file_name'] = $filename;
        $config['encrypt_name'] = false;
        // $config['max_width'] = 1024;
        // $config['max_height'] = 1024;

        $CI->load->library('upload', $config, $folder);
        $CI->$folder->initialize($config);
        if (!$CI->$folder->do_upload($field)) {
            return ['success' => false, 'message' => $CI->$folder->display_errors()];
        }

        return ['success' => true, 'filename' => pathinfo($CI->$folder->data()['full_path'], PATHINFO_BASENAME)];
    }
}

if (!function_exists('rename_file')) {
    function rename_file($folder, $oldFilename, $newFilename)
    {
        if (file_exists(($oldPath = FOLDER_ROOT_UPLOAD . $folder . '/' . $oldFilename))) {
            $newFilename .= (($pathInfo = pathinfo($oldPath)) && isset($pathInfo['extension']) ? ".{$pathInfo['extension']}" : '');
            if (rename($oldPath, FOLDER_ROOT_UPLOAD . $folder . '/' . $newFilename)) {
                return $newFilename;
            }
        }
        return false;
    }
}

if (!function_exists('delete_file')) {
    function delete_file($folder, $filename = null)
    {
        return $filename && file_exists(($path = FOLDER_ROOT_UPLOAD . $folder . '/' . $filename)) ? unlink($path) : true;
    }
}

if (!function_exists('file_path')) {
    function file_path($folder, $filename = null)
    {
        return $filename && file_exists(($path = FOLDER_ROOT_UPLOAD . $folder . '/' . $filename)) ? $path : null;
    }
}

if (!function_exists('file_url')) {
    function file_url($folder, $filename = null)
    {
        return $filename && file_exists(FOLDER_ROOT_UPLOAD  . $folder . '/' . $filename) ? base_url(FOLDER_ROOT_UPLOAD . $folder . '/' . $filename) : null;
    }
}

if (!function_exists('warning_message')) {
    function warning_message($message, $textClass = 'warning', $class = '')
    {
        $html = '<div class="warning-message text-' . $textClass . ' mb-2 ' . $class . '">';
        $html .= $message;
        $html .= '</div>';
        return $html;
    }
}

if (!function_exists('alert')) {
    function alert($message, $title = 'Perhatian', $options = [])
    {
        $html = '<div class="alert ' . (isset($options['class']) ? $options['class'] : 'alert-warning') . ' alert-dismissible">';
        $html .= '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>';
        $html .= '<h6 class="font-weight-bold"><i class="icon fa fa-' . (isset($options['icon']) ? $options['icon'] : 'warning') . '"></i> ' . strtoupper($title) . '</h6>';
        $html .= $message;
        $html .= '</div>';
        return $html;
    }
}

if (!function_exists('is_holiday')) {
    function is_holiday($date, $holidays)
    {
        if (!($date instanceof DateTime)) {
            $date = new DateTime($date);
        }
        return in_array($date->format("l"), ['Saturday', 'Sunday']) || in_array($date->format("Y-m-d"), $holidays);
    }
}

if (!function_exists('is_honorer')) {
    function is_honorer($id_jabatan)
    {
        return empty($id_jabatan) || in_array($id_jabatan, get_or_set_jabatan_honorer());
    }
}

if (!function_exists('jabatan_class')) {
    function jabatan_class($id_jabatan)
    {
        $class = [
            JABATAN_HONORER => 'honorer',
            JABATAN_HONORER_NON_DIPA => 'honorer-non-dipa',
        ];
        return isset($class[$id_jabatan]) ? $class[$id_jabatan] : '';
    }
}

if (!function_exists('get_user_config')) {
    function get_user_config($id_user)
    {
        $CI = get_instance();
        $CI->load->model('UserConfig_Model', 'userconfig');
        return $CI->userconfig->findOne($id_user, true);
    }
}

if (!function_exists('get_layout_classes')) {
    function get_layout_classes($layout)
    {
        $classes = [
            'navbar' => 'main-header navbar navbar-expand',
            'sidebar' => 'main-sidebar elevation-4',
            'mode-layout-plain' => 'hold-transition layout-footer-fixed',
            'mode-layout' => 'sidebar-mini layout-footer-fixed layout-navbar-fixed layout-fixed',
            // 'mode-layout' => 'sidebar-mini sidebar-collapse layout-footer-fixed layout-navbar-fixed layout-fixed',
        ];
        return isset($classes[$layout]) ? $classes[$layout] : '';
    }
}

if (!function_exists('my_validation_errors')) {
    function my_validation_errors()
    {
        $CI = get_instance();
        if (validation_errors()) {
            return validation_errors();
        } else if ($CI->ion_auth->errors()) {
            return $CI->ion_auth->errors();
        }
        return 'Terjadi kesalahan pada saat mengirim data';
    }
}

if (!function_exists('spell_number')) {
    function spell_number($number)
    {
        return (new NumberFormatter("id", NumberFormatter::SPELLOUT))->format($number);
    }
}

if (!function_exists('spell_number')) {
    function spell_number($number)
    {
        return (new NumberFormatter("id", NumberFormatter::SPELLOUT))->format($number);
    }
}

if (!function_exists('random_date_between')) {
    function random_date_between($start, $end)
    {
        $start = DateTime::createFromFormat('Y-m-d', $start);
        $end = DateTime::createFromFormat('Y-m-d', $end);

        $randomTimestamp = mt_rand($start->getTimestamp(), $end->getTimestamp());
        $randomDate = new DateTime();
        $randomDate->setTimestamp($randomTimestamp);
        return $randomDate->format('Y-m-d');
    }
}

if (!function_exists('random_int_length')) {
    function random_int_length($length)
    {
        return join('', array_map(function ($value) {
            return $value == 1 ? mt_rand(1, 9) : mt_rand(0, 9);
        }, range(1, $length)));
    }
}

if (!function_exists('number_to_day')) {
    function number_to_day($string_to_replace)
    {
        return str_replace(
            [0, 1, 2, 3, 4, 5],
            ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'],
            $string_to_replace
        );
    }
}

//yes it is dumb
if (!function_exists('var_dumb')) {
    function var_dumb($data, $exit = false)
    {
        echo '<pre>';
        var_dump($data);
        echo '</pre>';

        if ($exit) {
            exit;
        }
    }
}

if (!function_exists('getYearsRange')) {
    function getYearsRange($start_year, $end_year, $sorting = 'desc')
    {
        $years = array(); // inisialisasi array kosong
        if ($sorting == 'asc') { // jika sorting adalah ascending
            for ($i = $start_year; $i <= $end_year; $i++) {
                array_push($years, $i); // menambahkan tahun ke array
            }
        } else { // jika sorting adalah descending
            for ($i = $end_year; $i >= $start_year; $i--) {
                array_push($years, $i); // menambahkan tahun ke array
            }
        }
        return $years; // mengembalikan array
    }
}

if (!function_exists('arrayToAssoc')) {
    function arrayToAssoc($arr)
    {
        $new_arr = array(); // inisialisasi array kosong
        foreach ($arr as $value) {
            $new_arr[$value] = $value; // menambahkan key dan value ke array baru
        }
        return $new_arr; // mengembalikan array baru
    }
}

if (!function_exists('removeSpecialChars')) {
    function removeSpecialChars($str)
    {
        // menghapus karakter selain huruf, angka, dan spasi
        $str = preg_replace('/[^A-Za-z0-9\s]/', '', $str);
        // menghapus spasi di awal dan akhir string
        $str = trim($str);
        return $str;
    }
}

if (!function_exists('arrayRemoveDuplicate')) {
    function arrayRemoveDuplicate($array1, $array2)
    {
        return array_diff($array1, $array2);
    }
}
