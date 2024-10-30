<?php

use Arrilot\BitrixMigrations\BaseMigrations\BitrixMigration;
use Arrilot\BitrixMigrations\Exceptions\MigrationException;
use Arrilot\BitrixMigrations\Constructors\IBlockProperty;

class AddProjectIb20241003172159590241 extends BitrixMigration
{
    const IBLOCK_TYPE = 'raiting';
    const IBLOCK_CODE = 'project';
    const LINK_IBLOCK_ID = 'structure';

    /**
     * Run the migration.
     *
     * @return mixed
     * @throws MigrationException
     */
    public function up()
    {
        $iblockId = $this->addIblock();
        $this->addIblockProperty($iblockId);     
    }

    /**
     * Reverse the migration.
     *
     * @return mixed
     */
    public function down()
    {
        $this->deleteIblockByCode(self::IBLOCK_CODE);
    }

    /**
     * добавить инфоблок
     * @throws \Arrilot\BitrixMigrations\Exceptions\MigrationException
     * @return mixed
     */
    private function addIblock()
    {
        $iblockId = (new \Arrilot\BitrixMigrations\Constructors\IBlock())
            ->setVersion(2)
            ->setIblockTypeId(self::IBLOCK_TYPE)
            ->setName('Рейтинг-проекты')
            ->setCode(self::IBLOCK_CODE)
            ->setSort(500)
            ->setGroupId([
                '2' => 'R',             
            ])
            ->setWorkflow(false)
            ->add();

        if (!$iblockId) {
            throw new MigrationException('Ошибка при добавлении инфоблока ');
        }

        return $iblockId;
    }

    /**
     * свойства
     * @param mixed $iblockId
     * @return void
     */
    private function addIblockProperty($iblockId)
    {      
        (new IBlockProperty())
            ->setIblockId($iblockId)
            ->setName('Название')
            ->setCode('NAME')
            ->add();

        (new IBlockProperty())
            ->setIblockId($iblockId)
            ->setName('Пункт рейтинга')
            ->setCode('RAITING_POINT')
            ->setPropertyTypeIblock('E', self::LINK_IBLOCK_ID)
            ->add();  
    }
}
