<?php

    use yii\db\Migration;

    /**
     * Handles the creation of table `compare_items`.
     */
    class m180604_202836_create_compare_items_table extends Migration
    {
        public function safeUp()
        {
            $tableOptions = null;
            if ($this->db->driverName === 'mysql' || $this->db->driverName === 'mariadb') {
                $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
            }

            $this->createTable('{{%compare_items}}', [
                'user_id'    => $this->integer()->unsigned()->notNull(),
                'product_id' => $this->integer()->unsigned()->notNull(),
            ], $tableOptions);

            $this->addPrimaryKey('{{%pk-compare_items}}', '{{%compare_items}}', ['user_id', 'product_id']);

            $this->createIndex('{{%idx-compare_items-user_id}}', '{{%compare_items}}', 'user_id');
            $this->createIndex('{{%idx-compare_items-product_id}}', '{{%compare_items}}', 'product_id');
        }

        public function safeDown()
        {
            $this->dropTable('{{%compare_items}}');
        }
    }
