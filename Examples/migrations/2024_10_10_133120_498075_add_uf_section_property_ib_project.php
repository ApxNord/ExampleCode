<?php

use Arrilot\BitrixMigrations\BaseMigrations\BitrixMigration;
use Arrilot\BitrixMigrations\Exceptions\MigrationException;

class AddUfSectionPropertyIbProject20241010133120498075 extends BitrixMigration
{
    const IBLOCK_CODE = 'project';

    /**
     * Run the migration.
     *
     * @return mixed
     * @throws MigrationException
     */
    public function up()
    {
        $iblockId = $this->getIblockIdByCode(self::IBLOCK_CODE);

        $propId = $this->addUF([
            /*
            * Идентификатор сущности, к которой будет привязано свойство.
            * Для секция формат следующий - IBLOCK_{IBLOCK_ID}_SECTION
            */
            'ENTITY_ID' => 'IBLOCK_'.$iblockId.'_SECTION',
            /* Код поля. Всегда должно начинаться с UF_ */
            'FIELD_NAME' => 'UF_URL_PROJECT',
            /* Указываем, что тип нового пользовательского свойства строка */
            'USER_TYPE_ID' => 'string',
            /*
            * XML_ID пользовательского свойства.
            * Используется при выгрузке в качестве названия поля
            */
            'XML_ID' => '',
            /* Сортировка */
            'SORT' => 500,
            /* Является поле множественным или нет */
            'MULTIPLE' => 'N',
            /* Обязательное или нет свойство */
            'MANDATORY' => 'N',
            /*
            * Показывать в фильтре списка. Возможные значения:
            * не показывать = N, точное совпадение = I,
            * поиск по маске = E, поиск по подстроке = S
            */
            'SHOW_FILTER' => 'N',
            /*
            * Не показывать в списке. Если передать какое-либо значение,
            * то будет считаться, что флаг выставлен (недоработка разработчиков битрикс).
            */
            'SHOW_IN_LIST' => '',
            /*
            * Не разрешать редактирование пользователем.
            * Если передать какое-либо значение, то будет считаться,
            * что флаг выставлен (недоработка разработчиков битрикс).
            */
            'EDIT_IN_LIST' => '',
            /* Значения поля участвуют в поиске */
            'IS_SEARCHABLE' => 'N',
            /*
            * Дополнительные настройки поля (зависят от типа).
            * В нашем случае для типа string
            */
            'SETTINGS' => array(
                /* Значение по умолчанию */
                'DEFAULT_VALUE' => '',
                /* Размер поля ввода для отображения */
                'SIZE' => '20',
                /* Количество строчек поля ввода */
                'ROWS' => '1',
                /* Минимальная длина строки (0 - не проверять) */
                'MIN_LENGTH' => '0',
                /* Максимальная длина строки (0 - не проверять) */
                'MAX_LENGTH' => '0',
                /* Регулярное выражение для проверки */
                'REGEXP' => '',
            ),
            /* Подпись в форме редактирования */
            'EDIT_FORM_LABEL' => array(
                'ru' => 'URL проекта',
                'en' => 'URL project',
            ),
            /* Заголовок в списке */
            'LIST_COLUMN_LABEL' => array(
                'ru' => 'URL проекта',
                'en' => 'User field',
            ),
            /* Подпись фильтра в списке */
            'LIST_FILTER_LABEL' => array(
                'ru' => 'URL проекта',
                'en' => 'URL project',
            ),
            /* Сообщение об ошибке (не обязательное) */
            'ERROR_MESSAGE' => array(
                'ru' => 'Ошибка при заполнении URL проекта',
                'en' => 'An error in completing the URL project',
            ),
            /* Помощь */
            'HELP_MESSAGE' => [
                'ru' => '',
                'en' => '',
            ],
        ]);
    }

    /**
     * Reverse the migration.
     *
     * @return mixed
     * @throws MigrationException
     */
    public function down()
    {
        $code = 'UF_URL_PROJECT';

        $id = $this->getUFIdByCode('USER', $code);
        if (!$id) {
            throw new MigrationException('Не найдено URL проекта для удаления');
        }

        (new CUserTypeEntity())->delete($id);
    }
}
