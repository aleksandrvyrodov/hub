<?php

namespace MIT {
  const PATH_ROOT  = __DIR__;
  const PATH_ENV   = __DIR__ . '/..';
  const PATH_SITE  = PATH_ENV;
  const PATH_MODEL = PATH_ROOT . '/model';
  const PATH_APP   = PATH_ROOT . '/app';
  const PATH_FN    = PATH_ROOT . '/function';

  const PATH_APP_CORE = PATH_APP . '/.core';
}

namespace MIT\CATALOG {
  const PROP_MIN_COUNT_GROUP = 10;
  const PROP_MAX_COUNT_GROUP = 50;

  const PROP_INF_COUNT_GROUP = 0;

  const PROP_MAIN_CARD = 100;
  const PROP_INNERCARD_1 = 150;
  const PROP_INNERCARD_2 = 200;
  const PROP_INNERCARD_CHARACTER = 1000;
}
