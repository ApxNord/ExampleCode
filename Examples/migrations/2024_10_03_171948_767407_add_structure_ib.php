<?php

use Arrilot\BitrixMigrations\BaseMigrations\BitrixMigration;
use Arrilot\BitrixMigrations\Exceptions\MigrationException;
use Arrilot\BitrixMigrations\Constructors\IBlockProperty;

class AddStructureIb20241003171948767407 extends BitrixMigration
{
    const IBLOCK_TYPE = 'raiting';
    const IBLOCK_CODE = 'structure';

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
            ->setName('Рейтинг-структура')
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
            ->setName('Desktop')
            ->setCode('DESKTOP')
            ->setIsRequired(false)
            ->setDefaultValue('N')
            ->add();

        (new IBlockProperty())
            ->setIblockId($iblockId)
            ->setName('Mobile')
            ->setCode('MOBILE')
            ->setIsRequired(false)
            ->setDefaultValue('N')
            ->add();

        (new IBlockProperty())
            ->setIblockId($iblockId)
            ->setName('Баллы')
            ->setCode('WEIGHT')
            ->add();    
    } 
}
