<?php

namespace MIT {
  const PATH_ROOT   = __DIR__;
  const PATH_ENV    = __DIR__ . '/..';
  const PATH_SITE   = PATH_ENV;
  const PATH_MODEL  = PATH_ROOT . '/model';
  const PATH_APP    = PATH_ROOT . '/app';
  const PATH_FN     = PATH_ROOT . '/function';
  const PATH_VENDOR = PATH_ROOT . '/vendor';
  const PATH_STATIC = PATH_ROOT . '/static';

  const PATH_APP_CORE = PATH_APP . '/core';
}

namespace MIT\Bitrix {
  const PATH_SETTINGS   = \MIT\PATH_ENV . '/bitrix/.settings.php';
}

namespace MIT\HL {
  const USER_INCLUDE_ID   = 5;
}

namespace MIT\User {
  const MANAGER_IBLOCK_ID   = 16;
  const MANAGER_UF_NAME   = 'UF_MANAGER';
}

namespace MIT\Catalog {
  const SECTION_ALERT_IBLOCK_ID   = 39;
  const NAME_PROP_ASSOCIATED = 'ANALOG';

  const MAIN_CATALOG_ID = 26;
  const SKU_CATALOG_ID = 91;
  const SKU_CATALOG_TYPE = 'aspro_max_catalog';
}

namespace MIT\Order {
  const PAY_BAN   = true;
  const PAY_BAN_STATUS   = ['N'];
}
