<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

if (!function_exists('get_template')) {
    function get_template($guest)
    {
        $template = '🏛️ *INFORMASI ' . strtoupper(APP_SHORT_NAME) . ' ' . strtoupper(SATKER_NAME) . '*

Informasi tamu untuk *%s* pada %s.

*Identitas Tamu*
*%s* %s
%s
%s

*Jumlah Tamu*
%s

*Keperluan*
%s

*Pesan*
_%s_

ℹ️ _*Pesan ini dikirim oleh sistem secara otomatis. Balas OK untuk informasi lebih lanjut*_';

        return sprintf($template, $guest->person, formatDate($guest->visit_date, "%A, %d/%m/%y %H:%M"), ($guest->gender == 0 ? 'Bpk. ' : 'Ibu ') . $guest->name, ($guest->organization ? "($guest->organization)" : ''), $guest->phone_number . ($guest->phone_number && $guest->email ? ' / ' : '') . $guest->email, $guest->address, "$guest->guest_count orang", $guest->purpose ?: '-', $guest->message ?: '-');
    }
}
