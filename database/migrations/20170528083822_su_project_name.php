<?php

use think\migration\Migrator;
use think\migration\db\Column;

class SuProjectName extends Migrator
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
        //创建项目总表
        $this->table('project_name', array('id' => 'project_id', 'engine' => 'MyISAM', 'comment' => '项目名称'))
            ->addColumn('project_name', 'string')
            ->addColumn('create_time', 'timestamp', array('default' => 'CURRENT_TIMESTAMP'))
            ->addColumn('update_time', 'timestamp', array('default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'))
            ->create();
    }
}
