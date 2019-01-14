# Yii2 compare

This extension adds compare for Yii framework 2.0

## Installation

The preferred way to install this extension is through [Composer](https://getcomposer.org/download/)

Either run

```
php composer.phar require kenjebaev/yii2-compare"*"
```

or add

```
kenjebaev/yii2-compare: "*"
```

to the `require` section of your `composer.json` file.

## Configuration

Configure the `compare` component (default values are shown):

```php
return [
    //...
    'components' => [
        //...
        'compare' => [
            'class' => 'kenjeaev\compare\Compare',
            'storageClass' => 'kenjebaev\compare\storage\SessionStorage',
            'params' => [
                'key' => 'compare',
                'expire' => 604800,
                'productClass' => 'app\model\Product',
                'productFieldId' => 'id',
            ],
        ],
    ]
    //...
];
```

In addition to `kenjebaev\compare\storage\SessionStorage`, there is also `kenjebaev\compare\storage\CookieStorage` and `kenjebaev\compare\storage\DbSessionStorage`. It is possible to create your own storage, you need to implement the interface `kenjebaev\compare\storage\StorageInterface`.

`DbSessionStorage` uses `SessionStorage` for unauthorized users and database for authorized.

> If you use the `kenjebaev\compare\storage\DbSessionStorage` as `storageClass` then you need to apply the following migration:

```php
php yii migrate --migrationPath=@vendor/kenjebaev/yii2-compare/migrations
```

Setting up the `params` array: 

* `key` - For Session and Cookie.

* `expire` - Cookie life time.

* `productClass` - Product class is an ActiveRecord model.

* `productFieldId` - Name of the product model `id` field.

## Usage

You can get the compare component anywhere in the app using `Yii::$app->compare`.

Using compare:

```php
// Product is an AR model
$product = Product::findOne(1);

// Get component of the compare
$compare = \Yii::$app->compare;

// Add an item to the compare
$compare->add($product);

// Removes an items from the compare
$compare->remove($product->id);

// Removes all items from the compare
$compare->clear();

// Get all items from the compare
$compare->getItems();

// Get an item from the compare
$compare->getItem($product->id);

// Get ids array all items from the compare
$compare->getItemIds();
```

Using compare items:

```php
// Product is an AR model
$product = Product::findOne(1);

// Get component of the compare
$compare = \Yii::$app->compare;

// Get an item from the compare
$item = $compare->getItem($product->id);

// Get the id of the item
$item->getId();

// Get the product, AR model
$item->getProduct();
```

> By using method `getProduct()`, you have access to all the properties and methods of the product.

```php
$product = $item->getProduct();

echo $product->name;
```