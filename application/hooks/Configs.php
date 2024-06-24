<?php

class Configs
{
    function get_configs()
    {
        $CI = &get_instance();
        $CI->load->model('Configs_Model', 'configs');

        foreach ($CI->configs->get_all() as $row) {
            defined($row->key) or define($row->key, $row->value);
        }
    }
}
