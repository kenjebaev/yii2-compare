<?php

    namespace kenjebaev\compare\storage;

    use kenjebaev\compare\CompareItem;
    use Yii;
    use yii\db\Query;

    class DbSessionStorage implements StorageInterface
    {
        /**
         * @var string $string Table name
         */
        private $table = '{{%compare_items}}';

        /**
         * @var array $params Custom configuration params
         */
        private $params;

        /**
         * @var \yii\db\Connection $db
         */
        private $db;

        /**
         * @var integer $userId
         */
        private $userId;

        /**
         * @var SessionStorage $sessionStorage
         */
        private $sessionStorage;

        public function __construct(array $params)
        {
            $this->params = $params;
            $this->db = Yii::$app->db;
            $this->userId = Yii::$app->user->id;
            $this->sessionStorage = new SessionStorage($this->params);
        }

        /**
         * @return CompareItem[]
         */
        public function load()
        {
            if (Yii::$app->user->isGuest) {
                return $this->sessionStorage->load();
            }
            $this->moveItems();
            return $this->loadDb();
        }

        /**
         * @param CompareItem[] $items
         * @return void
         */
        public function save(array $items)
        {
            if (Yii::$app->user->isGuest) {
                $this->sessionStorage->save($items);
            } else {
                $this->moveItems();
                $this->saveDb($items);
            }
        }

        /**
         *  Moves all items from session storage to database storage
         * @return void
         */
        private function moveItems()
        {
            if ($sessionItems = $this->sessionStorage->load()) {
                $items = array_merge($this->loadDb(), $sessionItems);
                $this->saveDb($items);
                $this->sessionStorage->save([]);
            }
        }

        /**
         * Load all items from the database
         * @return CompareItem[]
         */
        private function loadDb()
        {
            $rows = (new Query())
                ->select('*')
                ->from($this->table)
                ->where(['user_id' => $this->userId])
                ->all();

            $items = [];
            foreach ($rows as $row) {
                $product = $this->params['productClass']::find()
                    ->where([$this->params['productFieldId'] => $row['product_id']])
                    ->limit(1)
                    ->one();
                if ($product) {
                    $items[$product->{$this->params['productFieldId']}] = new CompareItem($product, $this->params);
                }
            }
            return $items;
        }

        /**
         * Save all items to the database
         * @param CompareItem[] $items
         * @return void
         */
        private function saveDb(array $items)
        {
            $this->db->createCommand()->delete($this->table, ['user_id' => $this->userId])->execute();

            $this->db->createCommand()->batchInsert(
                $this->table,
                ['user_id', 'product_id'],
                array_map(function (CompareItem $item) {
                    return [
                        'user_id'    => $this->userId,
                        'product_id' => $item->getId(),
                    ];
                }, $items)
            )->execute();
        }
    }