<?php

    namespace kenjebaev\compare;

    use yii\base\BaseObject;
    use yii\base\InvalidConfigException;

    class Compare extends BaseObject
    {
        /**
         * @var string $storageClass
         */
        public $storageClass = 'kenjebaev\compare\storage\SessionStorage';

        /**
         * @var array $params Custom configuration params
         */
        public $params = [];

        /**
         * @var array $defaultParams
         */
        private $defaultParams = [
            'key'            => 'compare',
            'expire'         => 604800,
            'productClass'   => 'app\model\Product',
            'productFieldId' => 'id',
        ];

        /**
         * @var CompareItem[]
         */
        private $items;

        /**
         * @var \kenjebaev\compare\storage\StorageInterface
         */
        private $storage;

        /**
         * @inheritdoc
         */
        public function init()
        {
            parent::init();

            $this->params = array_merge($this->defaultParams, $this->params);

            if (!class_exists($this->params['productClass'])) {
                throw new InvalidConfigException('productClass `' . $this->params['productClass'] . '` not found');
            }
            if (!class_exists($this->storageClass)) {
                throw new InvalidConfigException('storageClass `' . $this->storageClass . '` not found');
            }

            $this->storage = new $this->storageClass($this->params);
        }

        /**
         * Add an item to the compare
         * @param object $product
         * @return void
         */
        public function add($product)
        {
            $this->remove($product->{$this->params['productFieldId']});
            $this->loadItems();
            $this->items[$product->{$this->params['productFieldId']}] = new CompareItem($product, $this->params);
            ksort($this->items, SORT_NUMERIC);
            $this->saveItems();
        }

        /**
         * Removes an items from the compare
         * @param integer $id
         * @return void
         */
        public function remove($id)
        {
            $this->loadItems();
            if (array_key_exists($id, $this->items)) {
                unset($this->items[$id]);
            }
            $this->saveItems();
        }

        /**
         * Removes all items from the compare
         * @return void
         */
        public function clear()
        {
            $this->items = [];
            $this->saveItems();
        }

        /**
         * Returns all items from the compare
         * @return CompareItem[]
         */
        public function getItems()
        {
            $this->loadItems();
            return $this->items;
        }

        /**
         * Returns an item from the compare
         * @param integer $id
         * @return CompareItem
         */
        public function getItem($id)
        {
            $this->loadItems();
            return isset($this->items[$id]) ? $this->items[$id] : null;
        }

        /**
         * Returns ids array all items from the compare
         * @return array
         */
        public function getItemIds()
        {
            $this->loadItems();
            $items = [];
            foreach ($this->items as $item) {
                $items[] = $item->getId();
            }
            return $items;
        }

        /**
         * Load all items from the compare
         * @return void
         */
        private function loadItems()
        {
            if ($this->items === null) {
                $this->items = $this->storage->load();
            }
        }

        /**
         * Save all items to the compare
         * @return void
         */
        private function saveItems()
        {
            $this->storage->save($this->items);
        }
    }
