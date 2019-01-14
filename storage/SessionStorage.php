<?php

    namespace kenjebaev\compare\storage;

    use Yii;

    class SessionStorage implements StorageInterface
    {
        /**
         * @var array $params Custom configuration params
         */
        private $params;

        public function __construct(array $params)
        {
            $this->params = $params;
        }

        /**
         * @return \kenjebaev\compare\models\CompareItem[]
         */
        public function load()
        {
            return Yii::$app->session->get($this->params['key'], []);
        }

        /**
         * @param \kenjebaev\compare\models\CompareItem[] $items
         * @return void
         */
        public function save(array $items)
        {
            Yii::$app->session->set($this->params['key'], $items);
        }
    }
