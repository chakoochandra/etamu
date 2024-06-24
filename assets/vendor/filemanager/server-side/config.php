<?php

if (isset($_GET['options'])) {
  $options = json_decode($_GET['options'], true);
}

if (!isset($options['projects_path'])) {
  echo 'projects_path belum diset';
  exit;

} else {
  $config = array(
    'token_secret' => 'ppL0jSLQzdpNdhtFhkzv56GlYSuoWIw5zb9S5HIqjdHRbMWk2LTS8H8usjiUUf',
    'projects_path' => $options['projects_path'],
    'projects_url' => isset($options['projects_url']) ? $options['projects_url'] : '',
    'dot_folders' => false,
    'file_exts' => isset($options['file_exts']) ? $options['file_exts'] : '.txt',
    // 'file_exts' => '.jpg, .jpeg, .png, .gif, .svg, .pdf, .rtf, .doc, .docx',
    'allow_empty_ext' => false,
    'new_file_ext' => '.txt',
    'upload_limit' => '20MB',
    'recycling' => true,
    'tabbed' => true,
    'password' => false,
  );
}
