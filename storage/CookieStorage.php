<?php

    namespace kenjebaev\compare\storage;

    use kenjebaev\compare\CompareItem;
    use Yii;
    use yii\helpers\Json;
    use yii\web\Cookie;

    class CookieStorage implements StorageInterface
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
         * @return CompareItem[]
         */
        public function load()
        {
            if ($cookie = Yii::$app->request->cookies->get($this->params['key'])) {
                return array_filter(array_map(function (array $row) {
                    if (isset($row['id']) && $product = $this->findProduct($row['id'])) {
                        return new CompareItem($product, $this->params);
                    }
                    return false;
                }, Json::decode($cookie->value)));
            }
            return [];
        }

        /**
         * @param CompareItem[] $items
         * @return void
         */
        public function save(array $items)
        {
            Yii::$app->response->cookies->add(new Cookie([
                'name'   => $this->params['key'],
                'value'  => Json::encode(array_map(function (CompareItem $item) {
                    return [
                        'id' => $item->getId(),
                    ];
                }, $items)),
                'expire' => time() + $this->params['expire'],
            ]));
        }

        /**
         * @param integer $productId
         * @return object|null
         */
        private function findProduct($productId)
        {
            return $this->params['productClass']::find()
                ->where([$this->params['productFieldId'] => $productId])
                ->limit(1)
                ->one();
        }
    }
