<?php

use think\migration\Migrator;
use think\migration\db\Column;

class SuAppName extends Migrator
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        $this->table('app_name', array('engine' => 'MyISAM', 'comment' => '应用名称'))
            ->addColumn('project_id', 'string', array('comment' => '关联project_name表project_id'))
            ->addColumn('app_one', 'string', array('default' => 'null'))
            ->addColumn('value_one', 'string', array('default' => 'null'))
            ->addColumn('one_iv', 'string', array('default' => 'null'))
            ->addColumn('app_two', 'string', array('default' => 'null'))
            ->addColumn('value_two', 'string', array('default' => 'null'))
            ->addColumn('two_iv', 'string', array('default' => 'null'))
            ->addColumn('app_three', 'string', array('default' => 'null'))
            ->addColumn('value_three', 'string', array('default' => 'null'))
            ->addColumn('three_iv', 'string', array('default' => 'null'))
            ->create();
    }
}
