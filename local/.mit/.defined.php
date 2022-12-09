<?php

namespace MIT {
  const PATH_ROOT   = __DIR__;
  const PATH_ENV    = __DIR__ . '/..';
  const PATH_SITE   = PATH_ENV;
  const PATH_MODEL  = PATH_ROOT . '/model';
  const PATH_APP    = PATH_ROOT . '/app';
  const PATH_FN     = PATH_ROOT . '/function';
  const PATH_VENDOR = PATH_ROOT . '/vendor';

  const PATH_APP_CORE = PATH_APP . '/core';
}

namespace MIT\Bitrix {
  const PATH_SETTINGS   = \MIT\PATH_ENV . '/bitrix/.settings.php';
}
