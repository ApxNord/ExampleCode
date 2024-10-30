<?php

use Arrilot\BitrixMigrations\BaseMigrations\BitrixMigration;
use Arrilot\BitrixMigrations\Exceptions\MigrationException;

class AddRatingIbType20241003171630916109 extends BitrixMigration
{
    const IBLOCK_TYPE = 'raiting';

    /**
     * Run the migration.
     *
     * @return mixed
     */
    public function up()
    {
        $this->addIblockType();
    }

    /**
     * Reverse the migration.
     *
     * @return mixed
     */
    public function down()
    {
        $this->deleteIblockType();
    }
     private function addIblockType()
     {
        $arFields = [
            'ID' => self::IBLOCK_TYPE,
            'SECTIONS' => 'Y',
            'SORT' => 500,
            'IN_RSS' => 'N',
            'LANG' => [
                'ru' => [
                    'NAME' => 'Рейтинг',
                    'SECTION_NAME' => 'Разделы',
                    'ELEMENT_NAME' => 'Элементы',
                ],
            ],
        ];

        $this->db->startTransaction();
        $iblockType = new CIBlockType;
        $result = $iblockType->Add($arFields);
        if (!$result) {
            $this->db->rollbackTransaction();
            throw new MigrationException('Ошибка при создании типа инфоблока: ' . self::IBLOCK_TYPE . "\n" . $iblockType->LAST_ERROR);
        }

        $this->db->commitTransaction();
     }

    private function deleteIblockType()
    {
        $this->db->startTransaction();
        if (!CIBlockType::Delete(self::IBLOCK_TYPE)) {
            $this->db->rollbackTransaction();
            throw new MigrationException('Ошибка при удалении типа инфоблока: ' . self::IBLOCK_TYPE);
        }

        $this->db->commitTransaction();
    }
}
