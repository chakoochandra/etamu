<?php
defined('BASEPATH') or exit('No direct script access allowed');

class mymenu
{
    protected $menus = [];

    public function __construct($menus)
    {
        $this->menus = $menus;
    }

    function getArrayMenu()
    {
        return $this->menus;
    }

    function printMenu($menus = [], $uri = null, $sessionUri = false)
    {
        $menus = $menus ?: $this->getArrayMenu();
        $uri = $uri ?: base_url(uri_string());

        $uriExistInMenu = $this->_check_uri_exist_in_menu($this->getArrayMenu(), base_url(uri_string()));
        $anyParentOpen = false;
        $anyParentActive = false;
        $html = '';
        foreach ($menus as $item) {
            if (!isset($item['visible']) || $item['visible'] === true) {
                $href = isset($item['href']) ? $item['href'] : '#';

                $hasChild = isset($item['child']) && is_array($item['child']) && !empty($item['child']);
                $liClass = !$anyParentOpen && ($anyParentOpen = $this->_check_parent_menu_active($item, $uri)) ? 'menu-open' : '';
                $aClass = !$anyParentActive && ($liClass || ($anyParentActive = $this->_uri_equal_item_menu($uri, $href))) ? 'active' : '';
                // $iClass = isset($item['iconClass']) ? $item['iconClass'] : (!$hasChild ? 'text-primary' : '');
                $iClass = isset($item['iconClass']) ? $item['iconClass'] : (!$hasChild ? '' : '');

                $hrefClass = isset($item['class']) ? $item['class'] : '';

                $html .= "<li class='nav-item {$liClass}'>";
                $html .= "  <a href='{$href}' class='nav-link {$aClass} {$hrefClass}'>";

                if (isset($item['iconImg'])) {
                    $html .= img([
                        'src' => $item['iconImg'],
                        'class' => 'nav-icon align-middle',
                        'style' => 'width: 22px; height: 22px; margin-right: 5px;',
                    ]);
                } else if (isset($item['icon'])) {
                    $html .= "<i class='nav-icon align-middle fa fa-{$item['icon']} {$iClass}'></i>";
                } else {
                    $html .= "<i class='nav-icon align-middle fa fa-circle-o {$iClass}'></i>";
                }

                if ($hasChild) {
                    $parentBadge = isset($item['parentBadge']) ? $item['parentBadge'] : '';
                    $html .= "<p class='text align-middle'>{$item['title']} <i class='right fa fa-angle-left'></i>{$parentBadge}</p>";
                } else {
                    $html .= "<p class='text align-middle'>{$item['title']}</p>";
                }
                $html .= '  </a>';

                if ($hasChild) {
                    $html .= '<ul class="nav nav-treeview" style="padding-left: 10px;">';
                    $html .= $this->printMenu($item['child'], $uri, $sessionUri);
                    $html .= '</ul>';
                }

                $html .= '</li>';
            }
        }

        if (!$anyParentOpen && !$anyParentActive && !$sessionUri && !$uriExistInMenu) {
            $CI = get_instance();
            $html = $this->printMenu($menus, $CI->session->flashdata('prev_active_uri'), true);
        }

        return $html;
    }

    private function _check_parent_menu_active($items, $uri)
    {
        if (isset($items['child']) && is_array($items['child'])) {
            $CI = get_instance();
            foreach ($items['child'] as $item) {
                if ($this->_uri_equal_item_menu($uri, isset($item['href']) ? $item['href'] : '#')) {
                    $CI->session->set_flashdata('prev_active_uri', $uri);
                    return true;
                }
            }
        }
        return false;
    }

    private function _check_uri_exist_in_menu($menus, $uri)
    {
        foreach ($menus as $items) {
            if (isset($items['visible']) && $items['visible'] === true && isset($items['child']) && is_array($items['child'])) {
                foreach ($items['child'] as $item) {
                    if ($this->_uri_equal_item_menu($uri, isset($item['href']) ? $item['href'] : '#')) {
                        return true;
                    }
                }
            } else if ($this->_uri_equal_item_menu($uri, isset($items['href']) ? $items['href'] : '#')) {
                return true;
            }
        }
        return false;
    }

    private function _uri_equal_item_menu($uri, $href)
    {
        return in_array($uri, [$href, "$href/"]);
    }
}
