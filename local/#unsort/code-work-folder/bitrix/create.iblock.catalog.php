<?php
   function create_catalog_iblock()
   {
      $ib = new CIBlock;

      $IBLOCK_TYPE = "s1"; // Тип инфоблока
      $SITE_ID = "s1"; // ID сайта
      
      /*
      // Айдишники групп, которым будем давать доступ на инфоблок
      $contentGroupId = $this->GetGroupIdByCode("CONTENT");
      $editorGroupId = $this->GetGroupIdByCode("EDITOR");
      $ownerGroupId = $this->GetGroupIdByCode("OWNER");
      */

      //===================================//
      // Создаем инфоблок каталога товаров //
      //===================================//

      // Настройка доступа
      $arAccess = array(
         "2" => "R", // Все пользователи
         );
         if ($contentGroupId) $arAccess[$contentGroupId] = "X"; // Полный доступ
         if ($editorGroupId) $arAccess[$editorGroupId] = "W"; // Запись
         if ($ownerGroupId) $arAccess[$ownerGroupId] = "X"; // Полный доступ

      $arFields = Array(
         "ACTIVE" => "Y",
         "NAME" => "Каталог товаров",
         "CODE" => "catalog",
         "IBLOCK_TYPE_ID" => $IBLOCK_TYPE,
         "SITE_ID" => $SITE_ID,
         "SORT" => "5",
         "GROUP_ID" => $arAccess, // Права доступа
         "FIELDS" => array(
            "DETAIL_PICTURE" => array(
               "IS_REQUIRED" => "N", // не обязательное
               "DEFAULT_VALUE" => array(
                  "SCALE" => "Y", // возможные значения: Y|N. Если равно "Y", то изображение будет отмасштабировано. 
                  "WIDTH" => "600", // целое число. Размер картинки будет изменен таким образом, что ее ширина не будет превышать значения этого поля. 
                  "HEIGHT" => "600", // целое число. Размер картинки будет изменен таким образом, что ее высота не будет превышать значения этого поля.
                  "IGNORE_ERRORS" => "Y", // возможные значения: Y|N. Если во время изменения размера картинки были ошибки, то при значении "N" будет сгенерирована ошибка. 
                  "METHOD" => "resample", // возможные значения: resample или пусто. Значение поля равное "resample" приведет к использованию функции масштабирования imagecopyresampled, а не imagecopyresized. Это более качественный метод, но требует больше серверных ресурсов. 
                  "COMPRESSION" => "95", // целое от 0 до 100. Если значение больше 0, то для изображений jpeg оно будет использовано как параметр компрессии. 100 соответствует наилучшему качеству при большем размере файла.
               ),
            ),
            "PREVIEW_PICTURE" => array(
               "IS_REQUIRED" => "N", // не обязательное
               "DEFAULT_VALUE" => array(
                  "SCALE" => "Y", // возможные значения: Y|N. Если равно "Y", то изображение будет отмасштабировано. 
                  "WIDTH" => "140", // целое число. Размер картинки будет изменен таким образом, что ее ширина не будет превышать значения этого поля. 
                  "HEIGHT" => "140", // целое число. Размер картинки будет изменен таким образом, что ее высота не будет превышать значения этого поля.
                  "IGNORE_ERRORS" => "Y", // возможные значения: Y|N. Если во время изменения размера картинки были ошибки, то при значении "N" будет сгенерирована ошибка. 
                  "METHOD" => "resample", // возможные значения: resample или пусто. Значение поля равное "resample" приведет к использованию функции масштабирования imagecopyresampled, а не imagecopyresized. Это более качественный метод, но требует больше серверных ресурсов. 
                  "COMPRESSION" => "95", // целое от 0 до 100. Если значение больше 0, то для изображений jpeg оно будет использовано как параметр компрессии. 100 соответствует наилучшему качеству при большем размере файла.
                  "FROM_DETAIL" => "Y", // возможные значения: Y|N. Указывает на необходимость генерации картинки предварительного просмотра из детальной. 
                  "DELETE_WITH_DETAIL" => "Y", // возможные значения: Y|N. Указывает на необходимость удаления картинки предварительного просмотра при удалении детальной.
                  "UPDATE_WITH_DETAIL" => "Y", // возможные значения: Y|N. Указывает на необходимость обновления картинки предварительного просмотра при изменении детальной.
               ),
            ),
            "SECTION_PICTURE" => array(
               "IS_REQUIRED" => "N", // не обязательное
               "DEFAULT_VALUE" => array(
                  "SCALE" => "Y", // возможные значения: Y|N. Если равно "Y", то изображение будет отмасштабировано. 
                  "WIDTH" => "235", // целое число. Размер картинки будет изменен таким образом, что ее ширина не будет превышать значения этого поля. 
                  "HEIGHT" => "235", // целое число. Размер картинки будет изменен таким образом, что ее высота не будет превышать значения этого поля.
                  "IGNORE_ERRORS" => "Y", // возможные значения: Y|N. Если во время изменения размера картинки были ошибки, то при значении "N" будет сгенерирована ошибка. 
                  "METHOD" => "resample", // возможные значения: resample или пусто. Значение поля равное "resample" приведет к использованию функции масштабирования imagecopyresampled, а не imagecopyresized. Это более качественный метод, но требует больше серверных ресурсов. 
                  "COMPRESSION" => "95", // целое от 0 до 100. Если значение больше 0, то для изображений jpeg оно будет использовано как параметр компрессии. 100 соответствует наилучшему качеству при большем размере файла.
                  "FROM_DETAIL" => "Y", // возможные значения: Y|N. Указывает на необходимость генерации картинки предварительного просмотра из детальной. 
                  "DELETE_WITH_DETAIL" => "Y", // возможные значения: Y|N. Указывает на необходимость удаления картинки предварительного просмотра при удалении детальной.
                  "UPDATE_WITH_DETAIL" => "Y", // возможные значения: Y|N. Указывает на необходимость обновления картинки предварительного просмотра при изменении детальной.
               ),
            ),
            // Символьный код элементов
            "CODE" => array(
               "IS_REQUIRED" => "Y", // Обязательное
               "DEFAULT_VALUE" => array(
                  "UNIQUE" => "Y", // Проверять на уникальность
                  "TRANSLITERATION" => "Y", // Транслитерировать
                  "TRANS_LEN" => "30", // Максмальная длина транслитерации
                  "TRANS_CASE" => "L", // Приводить к нижнему регистру
                  "TRANS_SPACE" => "-", // Символы для замены
                  "TRANS_OTHER" => "-",
                  "TRANS_EAT" => "Y",
                  "USE_GOOGLE" => "N",
                  ),
               ),
            // Символьный код разделов
            "SECTION_CODE" => array(
               "IS_REQUIRED" => "Y",
               "DEFAULT_VALUE" => array(
                  "UNIQUE" => "Y",
                  "TRANSLITERATION" => "Y",
                  "TRANS_LEN" => "30",
                  "TRANS_CASE" => "L",
                  "TRANS_SPACE" => "-",
                  "TRANS_OTHER" => "-",
                  "TRANS_EAT" => "Y",
                  "USE_GOOGLE" => "N",
                  ),
               ),
            "DETAIL_TEXT_TYPE" => array(      // Тип детального описания
               "DEFAULT_VALUE" => "html",
               ),
            "SECTION_DESCRIPTION_TYPE" => array(
               "DEFAULT_VALUE" => "html",
               ),
            "IBLOCK_SECTION" => array(         // Привязка к разделам обязательноа
               "IS_REQUIRED" => "Y",
               ),            
            "LOG_SECTION_ADD" => array("IS_REQUIRED" => "Y"), // Журналирование
            "LOG_SECTION_EDIT" => array("IS_REQUIRED" => "Y"),
            "LOG_SECTION_DELETE" => array("IS_REQUIRED" => "Y"),
            "LOG_ELEMENT_ADD" => array("IS_REQUIRED" => "Y"),
            "LOG_ELEMENT_EDIT" => array("IS_REQUIRED" => "Y"),
            "LOG_ELEMENT_DELETE" => array("IS_REQUIRED" => "Y"),
         ),
         
         // Шаблоны страниц
         "LIST_PAGE_URL" => "#SITE_DIR#/catalog/",
         "SECTION_PAGE_URL" => "#SITE_DIR#/catalog/#SECTION_CODE#/",
         "DETAIL_PAGE_URL" => "#SITE_DIR#/catalog/#SECTION_CODE#/#ELEMENT_CODE#/",         

         "INDEX_SECTION" => "Y", // Индексировать разделы для модуля поиска
         "INDEX_ELEMENT" => "Y", // Индексировать элементы для модуля поиска

         "VERSION" => 1, // Хранение элементов в общей таблице

         "ELEMENT_NAME" => "Товар",
         "ELEMENTS_NAME" => "Товары",
         "ELEMENT_ADD" => "Добавить товар",
         "ELEMENT_EDIT" => "Изменить товар",
         "ELEMENT_DELETE" => "Удалить товар",
         "SECTION_NAME" => "Категории",
         "SECTIONS_NAME" => "Категория",
         "SECTION_ADD" => "Добавить категорию",
         "SECTION_EDIT" => "Изменить категорию",
         "SECTION_DELETE" => "Удалить категорию",

         "SECTION_PROPERTY" => "Y", // Разделы каталога имеют свои свойства (нужно для модуля интернет-магазина)
      );

      $ID = $ib->Add($arFields);
      if ($ID > 0)
      {
         echo "&mdash; инфоблок \"Каталог товаров\" успешно создан<br />";
      }
      else
      {
         echo "&mdash; ошибка создания инфоблока \"Каталог товаров\"<br />";
         return false;
      }

      
      //=======================================//
      // Добавляем свойства к каталогу товаров //
      //=======================================//

      // Определяем, есть ли у инфоблока свойства
      $dbProperties = CIBlockProperty::GetList(array(), array("IBLOCK_ID"=>$ID));
      if ($dbProperties->SelectedRowsCount() <= 0)
      {
         $ibp = new CIBlockProperty;

         $arFields = Array(
            "NAME" => "[служебное] Спецпредложение",
            "ACTIVE" => "Y",
            "SORT" => -777, // Сортировка
            "CODE" => "SPECIALOFFER",
            "PROPERTY_TYPE" => "L", // Список
            "LIST_TYPE" => "C", // Тип списка - "флажки"
            "FILTRABLE" => "Y", // Выводить на странице списка элементов поле для фильтрации по этому свойству
            "VALUES" => array(
               "VALUE" => "да",
            ),
            "IBLOCK_ID" => $ID
           );
         $propId = $ibp->Add($arFields);
         if ($propId > 0)
         {
            $arFields["ID"] = $propId;
            $arCommonProps[$arFields["CODE"]] = $arFields;
            echo "&mdash; Добавлено свойство ".$arFields["NAME"]."<br />";
         }
         else
            echo "&mdash; Ошибка добавления свойства ".$arFields["NAME"]."<br />";


         $arFields = Array(
            "NAME" => "[служебное] Дополнительные фотографии",
            "ACTIVE" => "Y",
            "MULTIPLE" => "Y",
            "SORT" => -777,
            "CODE" => "MORE_PHOTO",
            "PROPERTY_TYPE" => "F", // Файл
            "FILE_TYPE" => "jpg, gif, bmp, png, jpeg",   
            "IBLOCK_ID" => $ID,
            "HINT" => "Допускается произвольное число дополнительных фотографий. Добавьте одну, и появится поле для добавленя следующей.",
           );
         $propId = $ibp->Add($arFields);
         if ($propId > 0)
         {
            $arFields["ID"] = $propId;
            $arCommonProps[$arFields["CODE"]] = $arFields;
            echo "&mdash; Добавлено свойство ".$arFields["NAME"]."<br />";
         }
         else
            echo "&mdash; Ошибка добавления свойства ".$arFields["NAME"]."<br />";


         $arFields = Array(
            "NAME" => "[служебное] Рекомендуемые товары",
            "ACTIVE" => "Y",
            "MULTIPLE" => "Y",
            "SORT" => -777,
            "CODE" => "RECOMMEND",
            "MULTIPLE_CNT" => 2, // Количество свойств, предлагаемых по умолчанию
            "PROPERTY_TYPE" => "E", // Привязка к элементам инфоблока
            "IBLOCK_ID" => $ID,
            "LINK_IBLOCK_ID" => $ID,
            "HINT" => "Данные товары будут показываться дла этого товара как рекомендуемые на странице просмотра товара",
           );
         $propId = $ibp->Add($arFields);
         if ($propId > 0)
         {
            $arFields["ID"] = $propId;
            $arCommonProps[$arFields["CODE"]] = $arFields;
            echo "&mdash; Добавлено свойство ".$arFields["NAME"]."<br />";
         }
         else
            echo "&mdash; Ошибка добавления свойства ".$arFields["NAME"]."<br />";

         
         /* сео-свойства */
         $arFields = Array(
            "NAME" => "[служебное] Сео-заголовок (title)",
            "ACTIVE" => "Y",
            "SORT" => -777,
            "CODE" => "SEO_TITLE",
            "PROPERTY_TYPE" => "S", // Строка
            "ROW_COUNT" => 1, // Количество строк
            "COL_COUNT" => 60, // Количество столбцов
            "IBLOCK_ID" => $ID,
            "HINT" => "Если задан - то заголовок для товара будет подставляться из этой строчки",
           );
         $propId = $ibp->Add($arFields);
         if ($propId > 0)
         {
            $arFields["ID"] = $propId;
            $arCommonProps[$arFields["CODE"]] = $arFields;
            echo "&mdash; Добавлено свойство ".$arFields["NAME"]."<br />";
         }
         else
            echo "&mdash; Ошибка добавления свойства ".$arFields["NAME"]."<br />";

         $arFields = Array(
            "NAME" => "[служебное] Ключевые слова (keywords)",
            "ACTIVE" => "Y",
            "SORT" => -777,
            "CODE" => "SEO_KEYWORDS",
            "PROPERTY_TYPE" => "S", // Строка
            "ROW_COUNT" => 3, // Количество строк
            "COL_COUNT" => 70, // Количество столбцов
            "IBLOCK_ID" => $ID,
            "HINT" => "Ключевые слова (keywords) для страницы товара",
           );
         $propId = $ibp->Add($arFields);
         if ($propId > 0)
         {
            $arFields["ID"] = $propId;
            $arCommonProps[$arFields["CODE"]] = $arFields;
            echo "&mdash; Добавлено свойство ".$arFields["NAME"]."<br />";
         }
         else
            echo "&mdash; Ошибка добавления свойства ".$arFields["NAME"]."<br />";

         $arFields = Array(
            "NAME" => "[служебное] Описание (description)",
            "ACTIVE" => "Y",
            "SORT" => -777,
            "CODE" => "SEO_DESCRIPTION",
            "PROPERTY_TYPE" => "S", // Строка
            "ROW_COUNT" => 3, // Количество строк
            "COL_COUNT" => 70, // Количество столбцов
            "IBLOCK_ID" => $ID,
            "HINT" => "Сео-описание (description) для страницы товара",
           );
         $propId = $ibp->Add($arFields);
         if ($propId > 0)
         {
            $arFields["ID"] = $propId;
            $arCommonProps[$arFields["CODE"]] = $arFields;
            echo "&mdash; Добавлено свойство ".$arFields["NAME"]."<br />";
         }
         else
            echo "&mdash; Ошибка добавления свойства ".$arFields["NAME"]."<br />";
      }
      else
         echo "&mdash; Для данного инфоблока уже существуют свойства<br />";      

      
      //===================//
      // Свойства разделов //
      //===================//

      $obUserType = new CUserTypeEntity();

      $fieldTitle = "Сео-заголовок (title)";
      $arPropFields = array(
         "ENTITY_ID" => "IBLOCK_".$ID."_SECTION",
         "FIELD_NAME" => "UF_SEO_TITLE",
         "USER_TYPE_ID" => "string",
         "XML_ID" => "",
         "SORT" => -777,
         "MULTIPLE" => "N", // Множественное
         "MANDATORY" => "N", // Обязательное 
         "SHOW_FILTER" => "S",
         "SHOW_IN_LIST" => "Y",
         "EDIT_IN_LIST" => "Y",
         "IS_SEARCHABLE" => "N",
         "SETTINGS" => array(
               "SIZE" => "70", // длина поля ввода
               "ROWS" => "1" // высота поля ввода
            ),
         "EDIT_FORM_LABEL" => array("ru" => $fieldTitle, "en" => ""),
         "LIST_COLUMN_LABEL" => array("ru" => $fieldTitle, "en" => ""),
         "LIST_FILTER_LABEL" => array("ru" => $fieldTitle, "en" => ""),
      );
      $FIELD_ID = $obUserType->Add($arPropFields);

      $fieldTitle = "Ключевые слова (keywords)";
      $arPropFields = array(
         "ENTITY_ID" => "IBLOCK_".$ID."_SECTION",
         "FIELD_NAME" => "UF_SEO_KEYWORDS",
         "USER_TYPE_ID" => "string",
         "XML_ID" => "",
         "SORT" => -777,
         "MULTIPLE" => "N", // Множественное
         "MANDATORY" => "N", // Обязательное 
         "SHOW_FILTER" => "S",
         "SHOW_IN_LIST" => "Y",
         "EDIT_IN_LIST" => "Y",
         "IS_SEARCHABLE" => "N",
         "SETTINGS" => array(
               "SIZE" => "70", // длина поля ввода
               "ROWS" => "3" // высота поля ввода
            ),
         "EDIT_FORM_LABEL" => array("ru" => $fieldTitle, "en" => ""),
         "LIST_COLUMN_LABEL" => array("ru" => $fieldTitle, "en" => ""),
         "LIST_FILTER_LABEL" => array("ru" => $fieldTitle, "en" => ""),
      );
      $FIELD_ID = $obUserType->Add($arPropFields);

      $fieldTitle = "Описание (description)";
      $arPropFields = array(
         "ENTITY_ID" => "IBLOCK_".$ID."_SECTION",
         "FIELD_NAME" => "UF_SEO_DESCRIPTION",
         "USER_TYPE_ID" => "string",
         "XML_ID" => "",
         "SORT" => -777,
         "MULTIPLE" => "N", // Множественное
         "MANDATORY" => "N", // Обязательное 
         "SHOW_FILTER" => "S",
         "SHOW_IN_LIST" => "Y",
         "EDIT_IN_LIST" => "Y",
         "IS_SEARCHABLE" => "N",
         "SETTINGS" => array(
               "SIZE" => "70", // длина поля ввода
               "ROWS" => "3" // высота поля ввода
            ),
         "EDIT_FORM_LABEL" => array("ru" => $fieldTitle, "en" => ""),
         "LIST_COLUMN_LABEL" => array("ru" => $fieldTitle, "en" => ""),
         "LIST_FILTER_LABEL" => array("ru" => $fieldTitle, "en" => ""),
      );
      $FIELD_ID = $obUserType->Add($arPropFields);

      
      // Настройка формы редактирования товаров
      // см. в блоге: http://dev.1c-bitrix.ru/community/webdev/user/16182/blog/8524/

      
      // Подключаем инфоблок к модулю торгового каталога
      if (!CCatalog::GetByID($ID))
      {
         $arFields = array(
            "IBLOCK_ID" => $ID,   // код (ID) инфоблока товаров
         //   "YANDEX_EXPORT" => "Y",      // экспортировать в Яндекс.Товары с помощью агента
         );
         $boolResult = CCatalog::Add($arFields);
         if ($boolResult == false)
         {
            if ($ex = $APPLICATION->GetException())
            {
               $strError = $ex->GetString();
               ShowError($strError);
            }
         }
         else
         {
            echo "&mdash; Инфоблок №".$ID." успешно подключен к модулю торгового каталога<br />";
         }
      }   
      else
      {
         echo "&mdash; Инфоблок №".$ID." является торговым каталогом<br />";
      }

      // Возвращаем номер добавленного инфоблока
      return $ID;
   }
?>