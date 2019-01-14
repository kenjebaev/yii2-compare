<?php

    namespace kenjebaev\compare\storage;

    interface StorageInterface
    {
        /**
         * @param array $params (configuration params)
         */
        public function __construct(array $params);

        /**
         * @return \kenjebaev\compare\models\CompareItem[]
         */
        public function load();

        /**
         * @param \kenjebaev\compare\models\CompareItem[] $items
         */
        public function save(array $items);
    }
