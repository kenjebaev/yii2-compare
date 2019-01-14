<?php

    namespace kenjebaev\compare;

    class CompareItem
    {
        /**
         * @var object $product
         */
        private $product;

        /**
         * @var array $params Custom configuration params
         */
        private $params;

        public function __construct($product, array $params)
        {
            $this->product = $product;
            $this->params = $params;
        }

        /**
         * Returns the id of the item
         * @return integer
         */
        public function getId()
        {
            return $this->product->{$this->params['productFieldId']};
        }

        /**
         * Returns the product, AR model
         * @return object
         */
        public function getProduct()
        {
            return $this->product;
        }
    }
