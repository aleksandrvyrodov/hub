<?php

namespace MIT {
}

namespace MIT\CATALOG {

  /**
   * Sort property by field 'sort'
   *  @param int $sort
   *  @param int $group = PROP_MAIN_CARD
   *  @param int $quantity = PROP_MAX_COUNT_GROUP
   *
   *  @return bool
   */
  function property_filter(int $sort, int $group = PROP_MAIN_CARD,  int $quantity = PROP_MAX_COUNT_GROUP): bool
  {
    $alfa = $sort - $group;

    if ($alfa >= 0) {
      if ($quantity === PROP_INF_COUNT_GROUP)
        return true;
      elseif ($alfa < $quantity)
        return true;
      else
        return false;
    } else
      return false;
  }
}
