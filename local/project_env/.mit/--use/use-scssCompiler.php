<?php

(include(\MIT\PATH_STATIC . '/style_compiler.php'))(
  from_path: SITE_TEMPLATE_PATH . '/assets/styles/src',
  to_path: SITE_TEMPLATE_PATH . '/assets/styles/dist',
  list_scss_files: ['main.scss'],
  silent: false
);