<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

if (!function_exists('switch_input')) {
    function switch_input($type, $field)
    {
        switch ($type) {
            case 'form_separator':
                return '<hr>';
            case 'form_info':
                $title = isset($field['title']) ? "<h6 class='font-weight-bold'><i class='fa fa-" . (isset($field['icon']) ? $field['icon'] : 'warning') . "' aria-hidden='true'></i> &nbsp; {$field['title']}</h6>" : '';
                $message = isset($field['info']) ? $field['info'] : '';
                $class = isset($field['textClass']) ? $field['textClass'] : 'small';
                return warning_message($title . $message, '', $class);
            case 'form_checkbox':
                $html = '';
                if (isset($field['data']) && is_array($field['data'])) {
                    $html .= '<div class="my-1">';
                    foreach ($field['data'] as $key => $data) {
                        $html .= '<div class="form-check">';
                        if (is_array($data)) {
                            $html .= form_checkbox($field['name'] . '[]', $data['id'], isset($field['selected']) ? in_array($data, $field['selected']) : false);
                            $html .= '<label class="form-check-label">' . htmlspecialchars($data['name'], ENT_QUOTES, 'UTF-8') . '</label>';
                        } else {
                            $html .= form_checkbox($field['name'] . '[]', $key, isset($field['selected']) ? in_array($key, $field['selected']) : false);
                            $html .= '<label class="form-check-label">' . htmlspecialchars($data, ENT_QUOTES, 'UTF-8') . '</label>';
                        }
                        $html .= '</div>';
                    }
                    $html .= '</div>';
                }
                return $html;
            case 'form_dropdown':
                return get_instance()->load->view('widgets/select2picker', ['field' => $field], true);
            case 'form_textarea':
                return form_textarea($field);
            case 'form_summernote':
                return get_instance()->load->view('widgets/summernote', ['field' => $field], true);
            case 'form_upload':
                return get_instance()->load->view('widgets/uploadpicker', ['field' => $field], true);
            case 'form_datetimepicker':
                return get_instance()->load->view('widgets/datetimepicker', ['field' => $field], true);
            case 'form_datepicker':
                return get_instance()->load->view('widgets/datepicker', ['field' => $field], true);
            case 'form_timepicker':
                return get_instance()->load->view('widgets/timepicker', ['data' => $field], true);
            case 'form_hidden':
                return form_hidden($field);
            case 'form_toggler':
                return form_toggler($field);
            case 'form_switcher':
                return form_switcher($field);
            case 'form_number':
                $field['type'] = 'number';
                return form_input($field);
            case 'camera':
                if ($field['value'] && file_exists($field['value'])) {
                    $field['value'] = 'data:' . mime_content_type($field['value']) . ';base64,' . base64_encode(file_get_contents($field['value']));
                }
                return get_instance()->load->view('widgets/camera', ['name' => $field['name'], 'value' => $field['value'], 'class' => $field['class'] ?: 'el'], true);
            default:
                $html = '';
                if (isset($field['icon'])) {
                    $html .= '<div class="input-group-append">';
                    $html .= '  <div class="input-group-text">';
                    $html .= '      <span class="fa fa-' . $field['icon'] . '"></span>';
                    $html .= '  </div>';
                    $html .= '</div>';
                }
                return form_input($field) . $html;
        }
    }
}

if (!function_exists('filter_form')) {
    function filter_form($data, $action = '', $attributes = [])
    {
        $attributes['id'] = isset($attributes['id']) ? $attributes['id'] : 'my-form-filter';
        $attributes['formClass'] = isset($attributes['formClass']) ? $attributes['formClass'] : 'mb-3';

        $html = '<form method="get" id="' . $attributes['id'] . '" class="d-flex justify-content-start bd-highlight ' . $attributes['formClass'] . '" action="' . base_url($action) . '">';

        foreach ($data as $field) {
            if (!isset($field['id']) && isset($field['name'])) {
                $field['id'] = $field['name'];
            }
            if (!isset($field['class'])) {
                $field['class'] = 'form-control';
            }

            $html .= isset($field['type']) ? switch_input($field['type'], $field) : form_input($field);
        }

        if (!isset($attributes['hideSubmitButton']) || !$attributes['hideSubmitButton']) {
            $html .= '      <button type="submit" class="btn btn-outline-primary" title="Cari"><i class="fa fa-search"></i></button>';
        }
        $html .= '      <a href="' . base_url($action) . '" class="btn btn-outline-danger ml-1" title="Refresh Pencarian"><i class="fa fa-refresh"></i></a>';

        $html .= '</form>';

        return $html;
    }
}

if (!function_exists('my_form')) {
    function my_form($data, $options = '', $attributes = [])
    {
        $html = '<div class="card m-0">';

        if (isset($options['title'])) {
            $html .= '<div class="card-header">';
            $html .= '<h3 class="card-title">' . $options['title'] . '</h3>';
            $html .= '<div class="card-tools"></div>';
            $html .= '</div>';
        }

        $html .= ' <div class="card-body pb-2">';
        if (!isset($attributes['class'])) {
            $attributes['class'] = '';
        }

        if (!isset($options['ajax']) || $options['ajax']) {
            $attributes['class'] .= ' form-ajax';
        }

        $html .= form_open_multipart(isset($options['action']) && $options['action'] ? $options['action'] : uri_string(), $attributes);

        if (isset($options['message']) && $options['message']) {
            $html .= alert($options['message'], 'Kelengkapan Data', ['class' => 'small alert-danger']);
            // $html .= warning_message("<h6 class='font-weight-bold'><i class='fa fa-warning' aria-hidden='true'></i> PERHATIAN!</h6>" . $options['message'], '', 'small');
        }

        foreach ($data as $field) {
            if (!isset($field['id']) && isset($field['name'])) {
                $field['id'] = $field['name'];
            }
            if (!isset($field['class'])) {
                $field['class'] = 'form-control';
            }

            $collapse = !isset($field['visible']) || $field['visible'] ? '' : 'collapse';
            $divClass = isset($field['divClass']) ? $field['divClass'] : '';

            $label = isset($field['label']) ? $field['label'] : (isset($field['placeholder']) ? $field['placeholder'] : '');
            $html .= $label ? '<label for="' . $field['name'] . '" class="' . $divClass . ' ' . $collapse . '">' . $label . '</label>' : '';
            $html .= isset($field['type']) && (in_array($field['type'], ['form_hidden', 'form_checkbox'])) ? '' : '<div class="input-group mb-3 align-items-center' . ($divClass ?: 'justify-content-center') . ' ' . $collapse . '">';
            $html .= isset($field['type']) ? switch_input($field['type'], $field) : form_input($field);
            $html .= isset($field['type']) && (in_array($field['type'], ['form_hidden', 'form_checkbox'])) ? '' : '</div>';
        }

        $isConfirmation = isset($options['isConfirmation']) ? $options['isConfirmation'] : false;
        $showBtnBack = isset($options['backUrl']) && $options['backUrl'];
        $showBtnCloseModal = isset($options['showBtnCloseModal']) ? $options['showBtnCloseModal'] : false;
        $submitLabel = isset($options['submitLabel']) && $options['submitLabel'] ? $options['submitLabel'] : ($isConfirmation ? 'Ya' : 'Simpan');
        $submitIcon = isset($options['submitIcon']) && $options['submitIcon'] ? $options['submitIcon'] : 'save';
        $submitClass = isset($options['submitClass']) ? $options['submitClass'] : '';

        $html .= '  <div class="d-flex justify-content-end mb-3">';
        $html .= $showBtnBack ? anchor($options['backUrl'], '<span class="fa fa-chevron-left" aria-hidden="true"></span> ' . ($isConfirmation ? 'Tidak' : 'Kembali'), ['class' => 'btn btn-outline-warning btn-back m-1', 'style' => 'width: 100px;;']) : '';
        $html .= !$showBtnBack && $showBtnCloseModal ? '<button type="button" class="btn btn-outline-warning m-1" data-dismiss="modal" style="min-width: 100px;"><i class="fa fa-times"></i> ' . ($isConfirmation ? 'Tidak' : 'Batal') . '</button>' : '';
        $html .= '      <button type="submit" class="btn btn-outline-primary m-1 ' . $submitClass . '" style="min-width: 100px;"><i class="fa fa-' . $submitIcon . '"></i> ' . $submitLabel . '</button>';
        $html .= '  </div>';

        $html .= form_close();

        $html .= ' </div>';
        $html .= ' <div class="card-footer p-0">';
        $html .= ' </div>';
        $html .= '</div>';

        return $html;
    }
}

if (!function_exists('form_captcha')) {
    /**
     * Captcha Field
     *
     * @param	mixed
     * @param	string
     * @param	mixed
     * @return	string
     */
    function form_captcha($data = '', $value = '', $extra = '')
    {
        $ci = &get_instance();

        // set captcha
        $ci->load->helper('captcha');
        $cap = create_captcha([
            'img_path'      => './captcha/',
            'img_url'       => base_url('captcha'),
            'word_length'    => 4,
            'font_size'    => 24,
            'img_height'    => 38,
            'pool'        => 'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
            // 'pool'        => '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
        ]);

        $query = $ci->db->insert_string('captcha', [
            'captcha_time'  => $cap['time'],
            'ip_address'    => $ci->input->ip_address(),
            'word'          => $cap['word']
        ]);
        $ci->db->query($query);
        // end of set captcha

        $defaults = array(
            'type' => 'text',
            'name' => is_array($data) ? '' : $data,
            'value' => $value,
        );

        return $cap['image'] . '<input ' . _parse_form_attributes($data, $defaults) . _attributes_to_string($extra) . " />\n";
    }
}

if (!function_exists('form_upload')) {
    /**
     * Form Upload Field
     *
     * @param	mixed
     * @param	string
     * @param	mixed
     * @return	string
     */
    function form_upload($data = '', $value = '', $extra = '')
    {
        $defaults = array(
            'type' => 'text',
            'name' => is_array($data) ? '' : $data,
            'value' => $value,
        );

        return '<div class="input-group">
                    <div class="custom-file">
                        <input type="file" class="form-control custom-file-input" ' . _parse_form_attributes($data, $defaults) . _attributes_to_string($extra) . ' />
                        <label class="custom-file-label" for="' . $data['name'] . '">' . $data['placeholder'] . '</label>
                    </div>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fa fa fa-folder-open"></span>
                        </div>
                    </div>
                </div>';
    }
}

if (!function_exists('form_datepicker')) {
    /**
     * Datepicker Field
     *
     * @param	mixed
     * @param	string
     * @param	mixed
     * @return	string
     */
    function form_datepicker($data = '', $value = '', $extra = '')
    {
        $defaults = array(
            'type' => 'text',
            'name' => is_array($data) ? '' : $data,
            'value' => $value
        );

        $html = '<input ' . _parse_form_attributes($data, $defaults) . _attributes_to_string($extra) . " autocomplete=\"off\" readonly />\n";
        $html .= '<div class="input-group-append">';
        $html .= '  <div class="input-group-text">';
        $html .= '      <span class="fa fa-calendar"></span>';
        $html .= '  </div>';
        $html .= '</div>';

        return $html;
    }
}


if (!function_exists('form_toggler')) {
    /**
     * Toggler Field
     *
     * @param	mixed
     * @return	string
     */
    function form_toggler($data)
    {
        if (!isset($data['value'])) {
            $data['value'] = null;
        }
        if (!isset($data['options'])) {
            $data['options'] = [1 => 'Ya', 0 => 'Tidak'];
        }

        $onclick0 = isset($data['onclick']) ? 'onclick="' . $data['onclick'][0] . '"' : '';
        $onclick1 = isset($data['onclick']) ? 'onclick="' . $data['onclick'][1] . '"' : '';

        $values = array_keys($data['options']);
        $labels = array_values($data['options']);

        return '<div class="btn-group btn-group-toggle" data-toggle="buttons">
                    <label class="btn btn-outline-success ' . (!is_null($data['value']) && $data['value'] == $values[0] ? 'active' : '') . '" style="min-width: ' . (isset($data['btnWidth'][0]) ? $data['btnWidth'][0] : 68) . 'px;">
                        <input type="radio" name="' . $data['name'] . '" id="' . $data['name'] . '_option0" ' . $onclick0 . ' value="' . $values[0] . '" autocomplete="off" ' . (!is_null($data['value']) && $data['value'] == $values[0] ? 'checked=""' : '') . '> ' . $labels[0] . '
                    </label>
                    <label class="btn btn-outline-success ' . (!is_null($data['value']) && $data['value'] == $values[1] ? 'active' : '') . '" style="min-width: ' . (isset($data['btnWidth'][1]) ? $data['btnWidth'][1] : 68) . 'px;">
                        <input type="radio" name="' . $data['name'] . '" id="' . $data['name'] . '_option1" ' . $onclick1 . ' value="' . $values[1] . '" autocomplete="off" ' . (!is_null($data['value']) && $data['value'] == $values[1] ? 'checked=""' : '') . '> ' . $labels[1] . '
                    </label>
                </div>';
    }
}

if (!function_exists('form_switcher')) {
    /**
     * Switcher Field
     *
     * @param	mixed
     * @return	string
     */
    function form_switcher($data)
    {
        if (!isset($data['value'])) {
            $data['value'] = true;
        }

        return '<div class="form-check form-switch ms-5">
                    <input name="' . $data['name'] . '" class="form-check-input" type="checkbox" role="switch" style="cursor: pointer;" ' . ($data['value'] ? 'checked' : '') . ' />
                    ' . (isset($data['sublabel']) ? '<label class="form-check-label text-muted" for="switch-theme">' . $data['sublabel'] . '</label>' : '') . '
                </div>';
    }
}

if (!function_exists('anchor_confirm')) {
    /**
     * Anchor Link
     *
     * Creates an anchor based on the local URL.
     *
     * @param	string	the URL
     * @param	string	the link title
     * @param	mixed	any attributes
     * @return	string
     */
    function anchor_confirm($uri = '', $title = '', $attributes = '')
    {
        $title = (string) $title;

        $site_url = is_array($uri)
            ? site_url($uri)
            : (preg_match('#^(\w+:)?//#i', $uri) ? $uri : site_url($uri));

        if ($title === '') {
            $title = $site_url;
        }

        // if (is_array($attributes)) {
        //     $CI = get_instance();
        //     $attributes = array_merge($attributes, [
        //         'data-csrf-token-name' => $CI->security->get_csrf_token_name(),
        //         'data-csrf-hash' => $CI->security->get_csrf_hash(),
        //     ]);
        // }

        if (!isset($attributes['class'])) {
            $attributes['class'] = '';
        }
        $attributes['class'] .= ' btn-confirm';

        if ($attributes !== '') {
            $attributes = _stringify_attributes($attributes);
        }

        return '<a href="' . $site_url . '"' . $attributes . '>' . $title . '</a>';
    }
}
